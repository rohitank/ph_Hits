<?php
include("includes/db.php");


function isWithinAllowedTime()
{
    $currentManilaTime = new DateTime('now', new DateTimeZone('Asia/Manila'));
    $currentHour = (int)$currentManilaTime->format('H');
    return ($currentHour >= 9 && $currentHour < 24);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isWithinAllowedTime()) {
        echo '<script>alert("The website is closed! Come back tomorrow!")</script>';
        exit();
    }

    $bufferTimeInSeconds = 1;
    $adID = 57;

    $durationSql = "SELECT duration FROM content WHERE contentID = ?";
    $durationStmt = $db->prepare($durationSql);

    if ($durationStmt) {
        $durationStmt->bind_param("i", $adID);
        $durationStmt->execute();
        $durationStmt->bind_result($videoDuration);
        $durationStmt->fetch();
        $durationStmt->close();

        // Calculate startTime
        $currentManilaTime = new DateTime('now', new DateTimeZone('Asia/Manila'));
        $startTimeInSeconds = strtotime($currentManilaTime->format('H:i:s')) - strtotime($videoDuration);

        $endTimeInSeconds = $startTimeInSeconds + strtotime($videoDuration) - strtotime('00:00:00');
        $endTime = date('H:i:s', $endTimeInSeconds);

        $insertSql = "INSERT INTO queue (contentID, dateOfAiring, schedTime, status)
                                  VALUES (?, CURDATE(), ?, 'queued')";
        $insertStmt = $db->prepare($insertSql);

        if ($insertStmt) {
            $insertStmt->bind_param("is", $adID, $endTime);
            $insertStmt->execute();
            $insertStmt->close();

            $latestSchedTime = $endTime; // Update latestSchedTime for the next iteration
        } else {
            echo '<script>alert("Error in adding to queue")</script>' . $db->error;
            exit();
        }
    }
    // }

    echo '<script>
                    alert("Successfully added to queue!");
                    window.location.href = "schedule-content.php";
                </script>';
} else {
    echo '<script>alert("Invalid request")</script>';
}