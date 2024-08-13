<?php
require_once('includes/db.php');

$sql = "SELECT * FROM streams WHERE isStreaming = 1";
$result = $db->query($sql);

$response = ['activeLivestream' => $result->num_rows > 0];
echo json_encode($response);

$db->close();
