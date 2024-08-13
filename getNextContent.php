<?php
include 'includes/db.php';

function getNextContentInfo()
{
    global $db;
    $nextContentQuery = "SELECT c.filePath, u.userName
                         FROM queue q
                         JOIN content c ON q.contentID = c.contentID
                         JOIN users u ON c.userID = u.userID
                         WHERE q.dateOfAiring = CURDATE() AND q.schedTime >= CURTIME()
                         ORDER BY q.dateOfAiring ASC, q.schedTime ASC
                         LIMIT 1";

    $nextContentResult = $db->query($nextContentQuery);

    if ($nextContentResult && $nextContentResult->num_rows > 0) {
        return $nextContentResult->fetch_assoc();
    } else {
        return false;
    }
}

$nextContentInfo = getNextContentInfo($db);

$response = array();
$response['success'] = false;

if ($nextContentInfo) {
    $response['success'] = true;
    $response['nextContent'] = array(
        'filePath' => $nextContentInfo['filePath'],
        'uploaderName' => $nextContentInfo['userName']
    );
}

header('Content-Type: application/json');
echo json_encode($response);
