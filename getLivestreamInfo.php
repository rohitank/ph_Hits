<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('includes/db.php');


$sql = "SELECT title, description FROM streams WHERE isStreaming = 1 LIMIT 1";
$result = $db->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $livestreamInfo = array(
        'title' => $row['title'],
        'description' => $row['description'],
    );
    echo json_encode($livestreamInfo);
} else {
    echo json_encode(array('error' => 'No active livestream'));
}

$db->close();
