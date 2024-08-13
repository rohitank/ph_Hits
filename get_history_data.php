<?php
include "includes/db.php";
include "includes/fetch_history.php";

$date = $_GET['date'];
$dates = getHistoryData($date);

header('Content-Type: application/json');
echo json_encode($dates);
