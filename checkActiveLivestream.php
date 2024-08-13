<?php
session_start();

include 'includes/db.php';

checkActiveLivestream();

function checkActiveLivestream()
{
    global $db;

    $livestreamer = $_SESSION['user']['username'];
    $isStreaming = 1;

    $checkQuery = "SELECT * FROM streams WHERE livestreamer = ? AND isStreaming = ?";
    $checkStatement = $db->prepare($checkQuery);
    $checkStatement->bind_param('si', $livestreamer, $isStreaming);
    $checkStatement->execute();
    $checkResult = $checkStatement->get_result();

    if ($checkResult->num_rows > 0) {
        echo json_encode(['activeLivestream' => true]);
    } else {
        echo json_encode(['activeLivestream' => false]);
    }
}

$db->close();
