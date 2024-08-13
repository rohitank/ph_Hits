<?php
include("includes/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "swapQueueItems") {
    if (isset($_POST["queueItemA"]) && isset($_POST["queueItemB"])) {
        $queueItemA = $_POST["queueItemA"];
        $queueItemB = $_POST["queueItemB"];

     
        $sqlFetchInfoA = "SELECT schedTime FROM queue WHERE queueID = ?";
        $stmtFetchInfoA = $db->prepare($sqlFetchInfoA);

        if (!$stmtFetchInfoA) {
            die("Error preparing fetch statement A: " . $db->error);
        }

        $stmtFetchInfoA->bind_param("i", $queueItemA);

        if (!$stmtFetchInfoA->execute()) {
            die("Error executing fetch statement A: " . $stmtFetchInfoA->error);
        }

        $stmtFetchInfoA->bind_result($currentSchedTimeA);
        $stmtFetchInfoA->fetch();
        $stmtFetchInfoA->close();

        $sqlFetchInfoB = "SELECT schedTime FROM queue WHERE queueID = ?";
        $stmtFetchInfoB = $db->prepare($sqlFetchInfoB);

        if (!$stmtFetchInfoB) {
            die("Error preparing fetch statement B: " . $db->error);
        }

        $stmtFetchInfoB->bind_param("i", $queueItemB);

        if (!$stmtFetchInfoB->execute()) {
            die("Error executing fetch statement B: " . $stmtFetchInfoB->error);
        }

        $stmtFetchInfoB->bind_result($currentSchedTimeB);
        $stmtFetchInfoB->fetch();
        $stmtFetchInfoB->close();

        // Swap the schedTime for both items
        $sqlUpdateA = "UPDATE queue SET schedTime = ? WHERE queueID = ?";
        $stmtUpdateA = $db->prepare($sqlUpdateA);

        if (!$stmtUpdateA) {
            die("Error preparing update statement A: " . $db->error);
        }

        $stmtUpdateA->bind_param("si", $currentSchedTimeB, $queueItemA);

        if (!$stmtUpdateA->execute()) {
            die("Error executing update statement A: " . $stmtUpdateA->error);
        }

        $stmtUpdateA->close();

        $sqlUpdateB = "UPDATE queue SET schedTime = ? WHERE queueID = ?";
        $stmtUpdateB = $db->prepare($sqlUpdateB);

        if (!$stmtUpdateB) {
            die("Error preparing update statement B: " . $db->error);
        }

        $stmtUpdateB->bind_param("si", $currentSchedTimeA, $queueItemB);

        if (!$stmtUpdateB->execute()) {
            die("Error executing update statement B: " . $stmtUpdateB->error);
        }

        $stmtUpdateB->close();

        echo "Success";
    } else {
        echo "Invalid parameters";
    }
} else {
    echo "Invalid request";
}
?>