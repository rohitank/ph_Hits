document.addEventListener('DOMContentLoaded', function(){
            
    /*--------------------------------------------------------------
    # Admin Page Functions
    --------------------------------------------------------------*/
    
    /**
     * for fetching users data to reflect on content manager list
     */
    
    $(document).ready(function() {
        $('#sample_data').DataTable({
            ajax: {
                url: '/fetchData',
                type: 'GET'
            },
            columns: [
                { data: 'ID' },
                { data: 'First Name' },
                { data: 'Last Name' },
                { data: 'Username' },
                { data: 'Status' },
                {
                    data: null,
                    render: function(data, type, row) {
                        return '<button class="edit-button">Edit</button>&nbsp;<button class="delete-button">Delete</button>&nbsp;<button class="enable-disable-button">Change Status</button>';
                    }
                }
            ]
        });
        
        /**
         * Event handling for edit, delete, and disable buttons
         *  */ 
    
        // For edit account credentials for existing content managers
        $('#sample_data').on('click', '.edit-button', function() {
    
            // Retrieves the cselected tupple
            let selectedRow = $(this).closest('tr');
            let rowData = $('#sample_data').DataTable().row(selectedRow).data();
    
            // Retrieve the userID from the row data and stores only for updating data
            const userID = rowData['ID']
    
            // Populates the edit popup with the row data
            document.getElementById('edit-admin-firstname').value = rowData['First Name'];
            document.getElementById('edit-admin-lastname').value = rowData['Last Name'];
            document.getElementById('edit-admin-username').value = rowData['Username'];
            document.getElementById('edit-admin-password').value = rowData['Password'];
    
            // Displays the edit-popup 
            document.querySelector('.edit-popup').style.display = 'flex';
            document.getElementById('overlay').style.display = 'flex';
    
            // When saving changes, includes the userID in the update request
            document.getElementById('save-changes').addEventListener('click', function(event) {
                event.preventDefault(); // Prevent default form submission
    
                // Retrieve the existing data
                const editedFirstName = document.getElementById('edit-admin-firstname').value;
                const editedLastName = document.getElementById('edit-admin-lastname').value;
                const editedUsername = document.getElementById('edit-admin-username').value;
                const editedPassword = document.getElementById('edit-admin-password').value;
    
                // Construct the updated data object with userID
                const updatedData = {
                    userID: userID,
                    firstName: editedFirstName,
                    lastName: editedLastName,
                    username: editedUsername,
                    password: editedPassword
                };
    
                // Send the updated data to the server for processing
                fetch('/update-account', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(updatedData),
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Client-side: Account updated successfully:', data);
    
                    // Close the edit popup and overlay
                    document.querySelector('.edit-popup').style.display = 'none';
                    document.getElementById('overlay').style.display = 'none';
    
                    // Refresh the table with the updated data
                    $('#sample_data').DataTable().ajax.reload();
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
            });
        });

        // Close button for edit popup
        document.getElementById('edit-close-popup').addEventListener('click', function() {
            document.querySelector('.edit-popup').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        });

        // Close button for delete popup
        document.getElementById('delete-close-popup').addEventListener('click', function() {
            document.querySelector('.delete-popup').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        });

        // Close buttons create popup
        document.getElementById('create-account-close-popup').addEventListener('click', function() {
            document.getElementById('create-success-popup').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        });

        // Close buttons username exist popup
        document.getElementById('username-exist-close-popup').addEventListener('click', function() {
            document.getElementById('username-exist-popup').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        });
        
        // For deleting account credentials for existing content managers
        $('#sample_data').on('click', '.delete-button', function() {
    
            let selectedRow = $(this).closest('tr');
            let rowData = $('#sample_data').DataTable().row(selectedRow).data();
            const userID = rowData['ID'];
    
            document.querySelector('.delete-popup').style.display = 'flex';
            document.getElementById('overlay').style.display = 'flex';
    
            document.getElementById('confirm-button').addEventListener('click', function () {
                document.querySelector('.delete-popup').style.display = 'none';
                document.getElementById('overlay').style.display = 'none';
        
                // fetch request to delete the user by userID
                fetch(`/delete-account/${userID}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(error => Promise.reject(error));
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('User deleted successfully:', data);
                    // Only remove the row if the deletion was successful
                    if (data && data.message && data.message === 'Cannot delete user. User has content scheduled.') {
                        alert('Cannot delete user. User has content scheduled.');
                    } else if (data && data.message && data.message === 'Account deleted successfully') {
                        selectedRow.remove();
                    } 
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                    // Display an alert if there's an error during deletion
                    alert('Error deleting user. Please try again later.');
                });
            });

            // for no button; cancels deletion
            document.getElementById('cancel-button').addEventListener('click', function () {
                document.querySelector('.delete-popup').style.display = 'none';
                document.getElementById('overlay').style.display = 'none';
            });
        });


        /**
         * For restricting (DISABLED) and unrestricting (ACTIVE) accounts
         */
        $('#sample_data').on('click', '.enable-disable-button', function() {
            let selectedRow = $(this).closest('tr');
            let rowData = $('#sample_data').DataTable().row(selectedRow).data();
            const userID = rowData['ID'];
            const status = rowData['Status'];
    
            const updatedStatus = status === 'ACTIVE' ? 'DISABLED' : 'ACTIVE';
    
            const updatedData = {
                userID: userID,
                status: updatedStatus
            };
    
            fetch(`/update-status/${userID}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(updatedData),
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('User status updated successfully:', data);
    
                // Refresh the table with the updated data
                $('#sample_data').DataTable().ajax.reload();
            })
            .catch(error => {
                console.error('There was a problem with the fetch operation:', error);
            });
        });
    });

     // for logout button (icon)
    const signOutButton = document.getElementById('sign-out-button');

    signOutButton.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent the default action of the anchor tag

        fetch('/logout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Logged out successfully:', data);
            if (data.redirect) {
                window.location.href = data.redirect; // Redirects the user back to the login page
            }
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
    });
    
    document.getElementById('create-account-form').addEventListener('submit', function(event) {
        event.preventDefault();

        const firstName = document.getElementById('admin-first-name').value;
        const lastName = document.getElementById('admin-last-name').value;
        const username = document.getElementById('admin-username').value;
        const password = document.getElementById('admin-password').value;

        const formData = {
            firstName: firstName,
            lastName: lastName,
            username: username,
            password: password,
        };

        fetch('/create-account', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData),
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    showErrorNotification(data.message);
                    throw new Error(data.message); // Throwing an error to trigger the catch block
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.message === 'Username already exists') {
                clearFormFields();
                document.getElementById('username-exist-popup').style.display = 'flex';
                document.getElementById('overlay').style.display = 'flex';
            } else {
                console.log('Account created successfully:', data);
                clearFormFields();
                document.getElementById('create-success-popup').style.display = 'flex';
                document.getElementById('overlay').style.display = 'flex';

                $('#sample_data').DataTable().ajax.reload();
            }
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
            alert('Error creating account. Please try again later.');
        });
    });


    function clearFormFields() {
        document.getElementById('admin-first-name').value = '';
        document.getElementById('admin-last-name').value = '';
        document.getElementById('admin-username').value = '';
        document.getElementById('admin-password').value = '';
    }
    
    function showErrorNotification(message) {
        alert(`Error: ${message}`);
    }
    
});