
<?php
include("db.php");

function updateHistoryTable()
{
    global $db;
    date_default_timezone_set('Asia/Manila');
    $currentHour = date('H');
    if ($currentHour == 18) {
        $updateQuery = "INSERT INTO history (streamDate)
                        SELECT DISTINCT dateOfAiring
                        FROM queue
                        WHERE dateOfAiring NOT IN (SELECT streamDate FROM history)";
        $db->query($updateQuery);
    }

    $db->close();
}

function getHistoryData($date)
{
    global $db;

    $query = "SELECT streamDate
              FROM history
              ORDER BY streamDate DESC";
    $result = $db->query($query);

    $dates = [];
    while ($row = $result->fetch_assoc()) {
        $dates[] = $row['streamDate'];
    }

    return $dates;
}

function getContentForDate($date)
{
    global $db;

    $query = "SELECT c.filePath FROM content c
              JOIN queue q ON c.contentID = q.contentID
              WHERE q.dateOfAiring='$date'";
    $result = $db->query($query);

    $files = [];
    while ($row = $result->fetch_assoc()) {
        $files[] = $row['filePath'];
    }

    return $files;
}
?>

