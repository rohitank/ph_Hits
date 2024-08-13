<?php
include 'includes/db.php';
session_start();

header('Content-Type: application/json'); // Set content type to JSON

// Debugging statements to log and send POST data
error_log(print_r($_POST, true)); // Log POST data to PHP error log

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $db;

    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Updated SQL query to check login status as well
    $sql = "SELECT users.*, logInStatus.status AS loginStatus
            FROM users
            LEFT JOIN logInStatus ON users.userID = logInStatus.userID
            WHERE users.userName = '$username' AND users.password = '$password'";

    $result = $db->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($user['loginStatus'] === 'ONLINE') {
            echo json_encode(['message' => 'User is already online.']);
        } elseif ($user['status'] === 'ACTIVE') {

            $_SESSION['user'] = [
                'id' => $user['userID'],
                'username' => $user['userName'],
                'firstName' => $user['fName'],
                'lastName' => $user['lName'],
                'role' => $user['role'],
            ];

            if ($user['role'] === 'Content Manager') {
                $db->query("UPDATE logInStatus SET status = 'ONLINE' WHERE userID = '{$user['userID']}'");
                echo json_encode(['redirect' => 'dashboard.php']);
            } else {
                echo json_encode(['message' => 'Unauthorized role']);
            }
        } else {
            echo json_encode(['message' => 'Account is restricted. Contact administrator.']);
        }
    } else {
        echo json_encode(['message' => 'Incorrect username or password']);
    }

    $db->close();
} else {
    echo json_encode(['message' => 'Invalid request method']);
}
