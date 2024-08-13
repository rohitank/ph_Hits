<?php
include("includes/db.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $queueID = $_POST['queueID']; 

    $durationSql = "SELECT c.duration
                    FROM queue q
                    INNER JOIN content c ON q.contentID = c.contentID
                    WHERE q.queueID = ?";
    $durationStmt = $db->prepare($durationSql);

    if ($durationStmt) {
        $durationStmt->bind_param('i', $queueID);
        if ($durationStmt->execute()) {
            $durationStmt->bind_result($videoDuration);

            if ($durationStmt->fetch()) {
                $durationStmt->close(); // Close the result set

                $fetchSql = "SELECT SEC_TO_TIME(TIME_TO_SEC(schedTime) % 3600) AS deletedSchedTime FROM queue WHERE queueID = ?";
                $fetchStmt = $db->prepare($fetchSql);

                if ($fetchStmt) {
                    $fetchStmt->bind_param('i', $queueID);
                    if ($fetchStmt->execute()) {
                        $fetchStmt->bind_result($deletedSchedTime);

                        if ($fetchStmt->fetch()) {
                            $fetchStmt->close(); // Close the result set

                            $updateSql = "UPDATE queue SET schedTime = SEC_TO_TIME(TIME_TO_SEC(schedTime) - TIME_TO_SEC(?)) WHERE queueID > ?";
                            $updateStmt = $db->prepare($updateSql);

                            if ($updateStmt) {
                                $updateStmt->bind_param('si', $videoDuration, $queueID);
                                if ($updateStmt->execute()) {
                                    $updateStmt->close(); 

                                    $deleteSql = "DELETE FROM queue WHERE queueID = ?";
                                    $deleteStmt = $db->prepare($deleteSql);

                                    if ($deleteStmt) {
                                        $deleteStmt->bind_param('i', $queueID);
                                        if ($deleteStmt->execute()) {
                                            echo 'success';
                                        } else {
                                            // Handle error executing delete statement
                                            echo 'error executing delete statement: ' . $deleteStmt->error;
                                        }
                                        $deleteStmt->close(); // Close the delete statement
                                    } else {
                                        // Handle error preparing delete statement
                                        echo 'error preparing delete statement: ' . $db->error;
                                    }
                                } else {
                                    // Handle error updating schedTime
                                    echo 'error updating: ' . $updateStmt->error;
                                }
                            } else {
                                // Handle error preparing update statement
                                echo 'error preparing update statement: ' . $db->error;
                            }
                        } else {
                            // Handle error fetching the deleted video's schedTime
                            echo 'error fetching schedTime: ' . $fetchStmt->error;
                        }
                    } else {
                        // Handle error executing fetch statement
                        echo 'error executing fetch statement: ' . $fetchStmt->error;
                    }
                } else {
                    // Handle error preparing fetch statement
                    echo 'error preparing fetch statement: ' . $db->error;
                }
            } else {
                // Handle error fetching the video's duration
                echo 'error fetching duration: ' . $durationStmt->error;
            }
        } else {
            // Handle error executing duration statement
            echo 'error executing duration statement: ' . $durationStmt->error;
        }
    } else {
        // Handle error preparing duration statement
        echo 'error preparing duration statement: ' . $db->error;
    }

    $db->close();
} else {
    echo 'error';
}
?>