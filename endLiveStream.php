<?php

$statusFilePath = 'livestream_status.txt';


$status = 'offline';  


file_put_contents($statusFilePath, $status);

echo json_encode(['success' => true]);
?>
