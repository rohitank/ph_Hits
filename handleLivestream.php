<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'includes/db.php';

if (isset($_POST['insertLivestream'])) {
    insertLivestream();
} elseif (isset($_POST['updateLivestream'])) {
    updateLivestream();
} elseif (isset($_POST['endLivestream'])) {
    endLivestream();
}

function insertLivestream()
{
    global $db;

    if (empty($_POST['titleStream']) || empty($_POST['descStream'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid title or description.']);
        return;
    }

    $title = $_POST['titleStream'];
    $description = $_POST['descStream'];
    $livestreamer = $_SESSION['user']['username'];
    $isStreaming = 1;

    $checkQuery = "SELECT * FROM streams WHERE title = ? AND isStreaming = 1";
    $checkStatement = $db->prepare($checkQuery);
    $checkStatement->bind_param('s', $title);
    $checkStatement->execute();
    $checkResult = $checkStatement->get_result();

    if ($checkResult->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'Livestream with the same title is already active.']);
        return;
    }

    $insertQuery = "INSERT INTO streams (title, description, livestreamer, isStreaming) VALUES (?, ?, ?, ?)";
    $insertStatement = $db->prepare($insertQuery);
    $insertStatement->bind_param('sssi', $title, $description, $livestreamer, $isStreaming);

    if ($insertStatement->execute()) {
        // Store the ID of the newly inserted livestream
        $livestreamId = $insertStatement->insert_id;
        echo json_encode(['success' => true, 'livestreamId' => $livestreamId]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to insert livestream into the database.']);
    }
}

function updateLivestream()
{
    global $db;

    if (empty($_POST['titleStream']) || empty($_POST['descStream'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid title or description.']);
        return;
    }

    $livestreamId = $_POST['livestreamId'];
    $title = $_POST['titleStream'];
    $description = $_POST['descStream'];

    $updateQuery = "UPDATE streams SET title = ?, description = ? WHERE id = ?";
    $updateStatement = $db->prepare($updateQuery);
    $updateStatement->bind_param('ssi', $title, $description, $livestreamId);

    if ($updateStatement->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update livestream in the database.']);
    }
}

function endLivestream()
{
    global $db;

    $livestreamId = $_POST['livestreamId'];
    $isStreaming = 0;

    $updateQuery = "UPDATE streams SET isStreaming = ? WHERE id = ?";
    $updateStatement = $db->prepare($updateQuery);
    $updateStatement->bind_param('ii', $isStreaming, $livestreamId);

    if ($updateStatement->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to end livestream in the database.']);
    }
}

$db->close();
