<?php

$statusFilePath = 'livestream_status.txt';

$status = 'online';  

file_put_contents($statusFilePath, $status);

echo json_encode(['success' => true]);
?>
