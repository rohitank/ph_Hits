const express = require('express');
const mysql = require('mysql');
const path = require('path');
const bodyParser = require('body-parser');
const session = require('express-session'); // for session handling
const cors = require('cors'); // Import CORS
const http = require('http');
const WebSocket = require('ws');

/**
 * If error occurs, run npm install express-session in terminal
 */

const app = express();
app.use(express.json()); // mandatory for edit and delete
app.use(cors());

app.use(express.urlencoded({ extended: true }));

// Configuration for express-session
app.use(session({
    secret: 'amen', 
    resave: false,
    saveUninitialized: true
}));

const iceServers = [{ urls: 'stun:stun.stunprotocol.org' }];
const port = process.env.PORT || 5000;

const connection = mysql.createConnection({
    host               : 'localhost',
    user               : 'root',
    password           : '',
    database           : 'ph_hits',
});

connection.connect(err => {
    if (err) {
        console.error('Error connecting to MySQL:', err);
        process.exit(1);
    }
    console.log('Connected to MySQL database!');
});

app.use('/assets', express.static(path.join(__dirname, 'assets')));

app.get('/', (req, res) => {
    res.sendFile( __dirname +'/login.html');
});

app.use(express.static('overtoaster-final'));
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Middleware to check if the user is logged in
const isLoggedIn = (req, res, next) => {
    if (req.session && req.session.user) {
        return next();
    }
    res.redirect('/login.html');
};

app.get('/admin.html', isLoggedIn, (req, res) => {
    if (req.session.user.role === 'Admin') {
        res.sendFile(path.join(__dirname, 'admin.html'));
    } else {
        res.redirect('/login.html');
    }
});

/**
 * Route for user login (for both Content Manager and Admin)
 */
app.post('/login', (req, res) => {
    const { username, password } = req.body;


    if (req.session.user) {
        if (req.session.user.username !== username) {
            return res.status(401).json({ message: 'Another user is already logged in on this browser' });
        } else {
            return res.status(401).json({ message: 'User already logged in the current browser in use.' });
        }
    } else {
        connection.query('SELECT * FROM users WHERE username = ?', [username], (err, results) => {
            if (err) {
                console.error('Error querying the database:', err);
                return res.status(500).json({ message: 'Database error' });
            }


            if (results.length > 0) {
                const user = results[0];


                if (user.password === password) {
                    // Check if the account status is "ACTIVE"
                    if (user.status === 'ACTIVE') {
                        req.session.user = {
                            id: user.userID,
                            username: user.userName,
                            firstName: user.fName,  
                            lastName: user.lName,
                            role: user.role,
                        };


                        // redirects user based on role
                        if (user.role === 'Content Manager') {
                            res.json({ redirect: '/dashboard.html' });
                        } else if (user.role === 'Admin') {
                            res.json({ redirect: '/admin.html' });
                        } else {
                            res.status(401).json({ message: 'Unauthorized role' });
                        }
                    } else {
                        // for restricted accounts
                        return res.status(401).json({ message: 'Account is restricted. Contact administrator.' });
                    }
                } else {
                    res.status(401).json({ message: 'Incorrect username or password' });
                }
            } else {
                res.status(401).json({ message: 'User not found' });
            }
        });
    }
});


// route to display username in UI
app.get('/getUserName', (req, res) => {
    if (req.session.user) {
        res.json({
            userID: req.session.user.id, 
            firstName: req.session.user.firstName, 
            lastName: req.session.user.lastName 
        });
    } else {
        res.status(401).json({ message: 'User not logged in' });
    }
});

// Route for logout
app.post('/logout', (req, res) => {
    if (req.session) {
        req.session.destroy(err => {
            if (err) {
                console.error('Error destroying session:', err);
                return res.status(500).json({ message: 'Error logging out' });
            }
            res.clearCookie('connect.sid'); // Clear the session cookie
            res.status(200).json({ redirect: '/login.html' }); // Send a JSON response with the redirect URL
        });
    } else {
        res.status(400).json({ message: 'No active session to log out from' });
    }
});

// Route for login whenever the user logouts
app.get('/login.html', (req, res) => {
    res.sendFile(path.join(__dirname, '/login.html'));
});

/**
 * For creating an account
 */
app.post('/create-account', (req, res) => {
    const { firstName, lastName, username, password } = req.body;
    console.log('Incoming request body:', req.body);

    if (!firstName || !lastName || !username || !password) {
        return res.status(400).json({ message: 'Please provide all required fields' });
    }

    // Allow letters and dash only to prevent HTML injection
    const regexName = /^[a-zA-Z-]*$/; 
    if (!regexName.test(firstName) || !regexName.test(lastName)) {
        return res.status(400).json({ message: 'First Name and Last Name should contain only letters or dash' });
    }

    // Data validation to check if the username already exists
    const checkUsernameSql = 'SELECT * FROM users WHERE userName = ?';
    connection.query(checkUsernameSql, [username], (err, results) => {
        if (err) {
            console.error('Error querying the database:', err);
            return res.status(500).send('Error checking username availability');
        }

        if (results.length > 0) {
            // If username already exists
            return res.status(200).json({ message: 'Username already exists' });
        }

        // If username does not exist, create a new account
        const insertUserSql = 'INSERT INTO users (fName, lName, userName, password, status, role) VALUES (?, ?, ?, ?, "ACTIVE", "Content Manager")';
        connection.query(insertUserSql, [firstName, lastName, username, password], (insertErr, result) => {
            if (insertErr) {
                console.error('Error inserting data:', insertErr);
                return res.status(500).json({ message: 'Error creating account' });
            }
            console.log('Account created successfully!');

            // Retrieve newly created account's userID 
            const newUserID = result.insertId;

            // Add the new user's status to the login status table; set to default OFFLINE
            const insertLoginStatusSql = 'INSERT INTO loginstatus (userID, status) VALUES (?, "OFFLINE")';
            connection.query(insertLoginStatusSql, [newUserID], (loginStatusErr, loginStatusResult) => {
                if (loginStatusErr) {
                    console.error('Error inserting login status:', loginStatusErr);
                    return res.status(500).json({ message: 'Error adding user to login status table' });
                }
                console.log('Added to login status table successfully!');
                res.status(200).json({ message: 'A Content Manager was created and added to login status table successfully' });
            });
        });
    });
});


/**
 * For fetching data to display the existing content manager accounts
 */
app.get('/fetchData', (request, response) => {
    const query = 'SELECT userID AS `ID`, fName AS `First Name`, lName AS `Last Name`, userName AS `Username`, password AS `Password`, status AS `Status` FROM users WHERE role="Content Manager"'; 

    connection.query(query, (dataError, dataResult) => {
        if (dataError) {
            console.error('Error fetching data:', dataError);
            return response.status(500).json({ error: 'Error fetching data' });
        }

        response.json({
            draw: request.query.draw,
            data: dataResult
        });
    });
});

/**
 * For updating account details via edit button
 */
app.post('/update-account', (req, res) => {
    const { userID, firstName, lastName, username, password } = req.body;

    // Allow letters and dash only to prevent HTML injection
    const regexName = /^[a-zA-Z-]*$/; 
    if (!regexName.test(firstName) || !regexName.test(lastName)) {
        return res.status(400).json({ message: 'First Name and Last Name should contain only letters or dash' });
    }

    // Creates an object to hold the updated data
    const updatedData = {
        fName: firstName,
        lName: lastName,
        userName: username,
        password: password
    };

    let updateFields = [];
    let fieldValues = [];

    // Iterates through the updated data and construct the update query dynamically
    for (const [key, value] of Object.entries(updatedData)) {
        if (value) {
            updateFields.push(`${key} = ?`);
            fieldValues.push(value);
        }
    }

    if (updateFields.length === 0) {
        return res.status(400).json({ message: 'No fields to update' });
    }

    // Adds the userID at the end for updating a specific user 
    fieldValues.push(userID);

    // Construct the SQL update query based on the fields received and the userID
    let updateQuery = `UPDATE users SET ${updateFields.join(', ')} WHERE userID = ?`;

    connection.query(updateQuery, fieldValues, (err, result) => {
        if (err) {
            console.error('Error updating account:', err);
            return res.status(500).json({ message: 'Error updating account' });
        }

        // Nagpapakita twice, d ko pa maayos pero working na si edit 
        console.log('Account updated successfully!');
        res.status(200).json({ message: 'Account credentials updated successfully' });
    });
});


/**
 * For deleting an account via delete button
 */
app.delete('/delete-account/:userID', async (req, res) => {
    const userID = req.params.userID;

    // Check if the user's userID exists in the content table
    const checkQuery = 'SELECT * FROM content WHERE userID = ?';
    connection.query(checkQuery, [userID], async (checkErr, checkResult) => {
        if (checkErr) {
            console.error('Error checking user:', checkErr);
            return res.status(500).json({ message: 'Error checking user' });
        }

        // If the user's ID exists in content, prevent deletion
        if (checkResult.length > 0) {
            // Log the failed deletion attempt
            console.error(`Cannot delete user ${userID}. User has content scheduled.`);
            
            // Send a specific error response
            return res.status(400).json({ message: 'Cannot delete user. User has content scheduled.' });
        }

        // If the user's ID does not exist in content table, proceed with deletion
        const deleteQuery = 'DELETE FROM users WHERE userID = ?';
        connection.query(deleteQuery, [userID], (err, result) => {
            if (err) {
                console.error('Error deleting user:', err);
                return res.status(500).json({ message: 'Error deleting user' });
            }

            console.log('Account deleted successfully');
            res.status(200).json({ message: 'Account deleted successfully' });
        });
    });
});


/**
 * For updating account status - disable and disable button
 */
app.put('/update-status/:userID', (req, res) => {
    const userID = req.params.userID;
    const { status } = req.body;

    const updateQuery = 'UPDATE users SET status = ? WHERE userID = ?';

    connection.query(updateQuery, [status, userID], (err, result) => {
        if (err) {
            console.error('Error updating user status:', err);
            return res.status(500).json({ message: 'Error updating user status' });
        }

        console.log('User status updated successfully');
        res.status(200).json({ message: 'User status updated successfully' });
    });
});


// Listen on environment port or 5000
app.listen(port, () => console.log(`Listening on port ${port}`))