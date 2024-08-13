<?php
include("includes/db.php");

$sql = "SELECT queue.*, content.filePath, content.contentTitle, content.contentDesc FROM queue 
        JOIN content ON queue.contentID = content.contentID
        WHERE queue.dateOfAiring = CURRENT_DATE AND queue.status = 'queued' OR queue.status = 'playing' ORDER BY queue.schedTime ASC LIMIT 1";

$stmt = $db->prepare($sql);

if ($stmt) {
    $stmt->execute();
    $stmt->bind_result($queueID, $contentID, $dateOfAiring, $schedTime, $status, $filePath, $contentTitle, $contentDesc);

    if ($stmt->fetch()) {
        $data = [
            'contentID' => $contentID,
            'filePath' => $filePath,
            'contentTitle' => $contentTitle,
            'contentDesc' => $contentDesc,
        ];

        echo json_encode($data);
    } else {
        echo json_encode(null);
    }

    $stmt->close();
} else {
    echo json_encode(null);
}
