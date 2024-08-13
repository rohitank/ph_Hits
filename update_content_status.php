<?php

include 'includes/db.php';


function updateContentStatus($contentID, $status)
{
    global $db;
    $sql = "UPDATE queue SET status = '$status' WHERE contentID = $contentID";
    $result = $db->query($sql);


    if ($result) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error']);
    }
}


if (isset($_POST['contentID']) && isset($_POST['status'])) {

    $contentID = $_POST['contentID'];
    $status = $_POST['status'];

    updateContentStatus($contentID, $status);
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'missing_parameters']);
}
