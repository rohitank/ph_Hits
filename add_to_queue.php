<?php
include("includes/db.php");


function isWithinAllowedTime()
{
    $currentManilaTime = new DateTime('now', new DateTimeZone('Asia/Manila'));
    $currentHour = (int)$currentManilaTime->format('H');
    return ($currentHour >= 9 && $currentHour < 24);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selectedContent'])) {
    if (!isWithinAllowedTime()) {
        echo '<script>alert("Queue can only be added between 9 AM and 6 PM Manila time")</script>';
        exit();
    }

    $selectedContent = $_POST['selectedContent'];

    $latestSchedTimeSql = "SELECT MAX(schedTime) AS latestSchedTime
                           FROM queue
                           WHERE dateOfAiring = CURDATE()";
    $latestSchedTimeResult = $db->query($latestSchedTimeSql);

    if ($latestSchedTimeResult) {
        $latestSchedTime = $latestSchedTimeResult->fetch_assoc()['latestSchedTime'];
        error_log('Latest Time: ' . $latestSchedTime);
        $latestSchedTimeResult->close();

        $bufferTimeInSeconds = 1;

        if (count($selectedContent) > 0) {
            foreach ($selectedContent as $key => $contentID) {
                $durationSql = "SELECT c.duration FROM queue q JOIN content c ON q.contentID = c.contentID WHERE q.dateOfAiring = CURDATE() ORDER BY q.schedTime DESC LIMIT ?";
                $durationStmt = $db->prepare($durationSql);

                if ($durationStmt) {
                    $durationStmt->bind_param("i", $bufferTimeInSeconds);
                    $durationStmt->execute();
                    $durationStmt->bind_result($videoDuration);
                    $durationStmt->fetch();
                    $durationStmt->close();

                    $currentManilaTime = new DateTime('now', new DateTimeZone('Asia/Manila'));
                    if ($key === 0 && !$latestSchedTime) {
                        $startTimeInSeconds = strtotime($currentManilaTime->format('H:i:s')) - strtotime($videoDuration);
                    } else {
                        $startTimeInSeconds = (!$latestSchedTime) ? strtotime($currentManilaTime->format('H:i:s')) : strtotime($latestSchedTime);
                    }

                    $endTimeInSeconds = $startTimeInSeconds + strtotime($videoDuration) - strtotime('00:00:00');
                    $endTime = date('H:i:s', $endTimeInSeconds);

                    $insertSql = "INSERT INTO queue (contentID, dateOfAiring, schedTime, status)
                                  VALUES (?, CURDATE(), ?, 'queued')";
                    $insertStmt = $db->prepare($insertSql);

                    if ($insertStmt) {
                        $insertStmt->bind_param("is", $contentID, $endTime);
                        $insertStmt->execute();
                        $insertStmt->close();

                        $latestSchedTime = $endTime;
                    } else {
                        echo '<script>alert("Error in adding to queue")</script>' . $db->error;
                        exit();
                    }
                }
            }

            echo '<script>
                    alert("Successfully added to queue!");
                    window.location.href = "schedule-content.php";
                </script>';
        } else {
            echo '<script>alert("Nothing was selected...")</script>';
        }
    } else {
        echo '<script>alert("Error in fetching latest scheduled time")</script>';
    }
} else {
    echo '<script>alert("Invalid request")</script>';
}