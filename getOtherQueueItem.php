<?php
include("includes/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "getOtherQueueItem") {
    if (isset($_POST["queueItemA"]) && isset($_POST["selectedSchedTime"])) {
        $queueItemA = $_POST["queueItemA"];
        $selectedSchedTime = $_POST["selectedSchedTime"];

        
        $sqlGetOtherQueueItem = "SELECT queueID FROM queue WHERE schedTime = ? AND queueID != ?";
        $stmtGetOtherQueueItem = $db->prepare($sqlGetOtherQueueItem);

        if (!$stmtGetOtherQueueItem) {
            die("Error preparing statement to get other queue item: " . $db->error);
        }

        $stmtGetOtherQueueItem->bind_param("si", $selectedSchedTime, $queueItemA);

        if (!$stmtGetOtherQueueItem->execute()) {
            die("Error executing statement to get other queue item: " . $stmtGetOtherQueueItem->error);
        }

        $stmtGetOtherQueueItem->bind_result($otherQueueItem);
        $stmtGetOtherQueueItem->fetch();
        $stmtGetOtherQueueItem->close();

        $response = array("queueItemB" => $otherQueueItem);
        echo json_encode($response);
    } else {
        echo "Invalid parameters";
    }
} else {
    echo "Invalid request";
}
?>