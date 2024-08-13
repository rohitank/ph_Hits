<?php
// logout.php

include 'includes/db.php';
session_start();

header('Content-Type: application/json'); // Set content type to JSON

if (isset($_SESSION['user'])) {
    global $db;

    $userID = $_SESSION['user']['id'];

    // Update login status to OFFLINE for the session user
    $db->query("UPDATE logInStatus SET status = 'OFFLINE' WHERE userID = '$userID'");

    // Clear the session data
    $_SESSION = array();
    session_destroy();

    echo json_encode(['message' => 'Logout successful', 'redirect' => '/login.html']);
} else {
    echo json_encode(['message' => 'User not logged in']);
}
