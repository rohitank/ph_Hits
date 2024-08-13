<?php
include 'includes/db.php';
date_default_timezone_set('Asia/Manila');


function getCurrentContentInfo()
{
    global $db;
    $query = "SELECT c.contentID, c.contentTitle, c.filePath, c.duration, q.dateOfAiring, q.schedTime, u.userName
              FROM queue q
              JOIN content c ON q.contentID = c.contentID
              JOIN users u ON c.userID = u.userID
              WHERE q.dateOfAiring = CURDATE() AND q.schedTime <= CURTIME() 
              AND NOT q.schedTime + c.duration < CURTIME()
              ORDER BY q.dateOfAiring DESC, q.schedTime DESC
              LIMIT 1";

    $result = $db->query($query);

    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
}


$currentContentInfo = getCurrentContentInfo();

$response = array();
$response['success'] = false;

if ($currentContentInfo) {

    $currentTimeZone = new DateTimeZone('Asia/Manila');
    $currentTime = new DateTime('now', $currentTimeZone);


    $scheduleTimeZone = new DateTimeZone('Asia/Manila');
    $scheduleTime = new DateTime($currentContentInfo['dateOfAiring'] . ' ' . $currentContentInfo['schedTime'], $scheduleTimeZone);

    $timeDifference = $scheduleTime->getTimestamp() - $currentTime->getTimestamp();

    $response['success'] = true;
    $response['content'] = array(
        'contentTitle' => $currentContentInfo['contentTitle'],
        'filePath' => $currentContentInfo['filePath'],
        'timeDifference' => $timeDifference * 1000,
        'uploaderName' => $currentContentInfo['userName']
    );
}

header('Content-Type: application/json');
echo json_encode($response);