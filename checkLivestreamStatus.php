<?php

$statusFilePath = 'livestream_status.txt';

$livestreamStatus = file_exists($statusFilePath) ? trim(file_get_contents($statusFilePath)) : 'offline';

header('Content-Type: application/json');
echo json_encode(['livestreamStatus' => $livestreamStatus]);
?>
