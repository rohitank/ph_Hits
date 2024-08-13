<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'includes/db.php';
require_once 'assets/getID3-master/getid3/getid3.php';

if (isset($_POST['upload'])) {
    $uploadDirectory = 'uploads/';

    $currentDate = $_POST['currentDate'];
    $title = $_POST['Title'];
    $description = $_POST['Description'];

    $fileName = basename($_FILES['ContentFile']['name']);
    $filePath = $uploadDirectory . $fileName;
    $fileType = pathinfo($filePath, PATHINFO_EXTENSION);
    $getID3 = new getID3();
    $fileInfo = $getID3->analyze($filePath);
    $duration = $fileInfo['playtime_seconds'];

    $allowedFormats = ['mp4', 'mp3', 'png', 'jpg', 'jpeg', 'HEIC'];
    if (!in_array(strtolower($fileType), $allowedFormats)) {
        echo "Invalid file format. Please upload a valid file.";
        exit;
    }

    if (file_exists($filePath)) {
        echo "File already exists. Please choose a different file.";
        exit;
    }

    if (move_uploaded_file($_FILES['ContentFile']['tmp_name'], $filePath)) {
        $sql = "INSERT INTO content (contentTitle, contentDesc, schedDate, userID, filePath, duration) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);

        // Replace 'your_user_id' with the actual user ID of the uploader
        $userID = $user['userID'];

        $stmt->bind_param('ssssss', $title, $description, $currentDate, $userID, $filePath, $duration);

        if ($stmt->execute()) {
            echo "File uploaded successfully.";
        } else {
            echo "Error uploading file. Please try again.";
        }

        $stmt->close();
    } else {
        echo "Error uploading file. Please try again.";
    }

    $db->close();
} else {
    echo "Invalid request.";
}

#chmod 777 /path/to/destination/directory (uploads directory)
#chown webserveruser:webserveruser /path/to/destination/directory (uploads directory)
#Replace webserveruser with the actual user running the web server