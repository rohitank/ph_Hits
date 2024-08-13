 <?php
    include 'includes/db.php';
    session_start();


    if (!isset($_SESSION['user'])) {

        header('Location: loginCM.html');
        exit();
    }

    $user = $_SESSION['user'];

    if (isset($_GET['signOut'])) {

        $userID = $_SESSION['user']['id'];

        $db->query("UPDATE logInStatus SET status = 'OFFLINE' WHERE userID = '$userID'");

        // Clear the session data
        $_SESSION = array();

        session_destroy();

        header('Location: loginCM.html');
        exit();
    }

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include 'includes/db.php';
    require_once 'assets/getID3-master/getid3/getid3.php';

    // Initialize upload status variable
    $uploadStatus = '';

    if (isset($_POST['upload'])) {
        $uploadDirectory = 'uploads/';

        $currentDate = $_POST['currentDate'];
        $title = $_POST['Title'];
        $description = $_POST['Description'];

        $fileName = basename($_FILES['ContentFile']['name']);
        $filePath = $uploadDirectory . $fileName;
        $fileType = pathinfo($filePath, PATHINFO_EXTENSION);

        // Analyze file only if it is successfully uploaded
        if (move_uploaded_file($_FILES['ContentFile']['tmp_name'], $filePath)) {
            $getID3 = new getID3();
            $fileInfo = $getID3->analyze($filePath);
            $imageFormats = ['png', 'jpg', 'jpeg', 'HEIC'];

            $duration = isset($fileInfo['playtime_seconds']) ? $fileInfo['playtime_seconds'] : null;
            $formattedDuration = ($duration !== null) ? gmdate("H:i:s", (int)$duration) : "00:00:30";

            // Set a default duration of 30 seconds if it's not an image or the playtime_seconds is not available
            if (!$formattedDuration) {
                $formattedDuration = "00:00:30";
            }

            $allowedFormats = ['mp4', 'mp3', 'png', 'jpg', 'jpeg', 'HEIC'];

            if (!in_array(strtolower($fileType), $allowedFormats)) {
                $uploadStatus = 'Invalid file format. Please upload a valid file.';
            } else {
                $sql = "INSERT INTO content (contentTitle, contentDesc, schedDate, userID, filePath, duration) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($sql);

                // Replace with ID of logged-in user
                $userID = $user['id'];

                $stmt->bind_param('ssssss', $title, $description, $currentDate, $userID, $filePath, $formattedDuration);

                if ($stmt->execute()) {
                    $uploadStatus = 'File successfully inserted!';
                } else {
                    $uploadStatus = 'Failed to insert file.';
                }

                $stmt->close();
            }
        } else {
            $uploadStatus = 'Failed to upload file.' . $_FILES['ContentFile']['error'] . ' pls';
        }
    }
    ?>


 <!DOCTYPE html>
 <html lang="en">

 <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <!-- Include Bootstrap CSS -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link rel="stylesheet" href="assets/css/schedule-content.css">
     <title>Schedule Content</title>

     <!-- Boxicons CSS -->
     <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
     <link rel="stylesheet" href="assets/css/side-bar.css" />
     <link rel="stylesheet" href="assets/css/header.css" />
 </head>

 <body>
     <div id="mySidebar" class="sidebar close">
         <div class="logo_items flex">
             <span class="nav_image">
                 <img src="assets/img/logo.png" alt="logo_img" />
             </span>
             <span class="logo_name">Cheers</span>
         </div>
         <div class="menu_container">
             <div class="menu_items">
                 <ul class="menu_item">
                     <div class="menu_title flex">
                         <span class="title">Dashboard</span>
                         <span class="line"></span>
                     </div>
                     <li class="item">
                         <a href="dashboard.php" class="link flex">
                             <i class="bx bx-home-alt"></i>
                             <span>Home</span>
                             <i class="bi bi-chevron-down ms-auto"></i>
                         </a>
                     </li>

                     <ul class="menu_item">
                         <div class="menu_title flex">
                             <span class="title">Content</span>
                             <span class="line"></span>
                         </div>
                         <li class="item">
                             <a href="schedule-content.php" class="link flex">
                                 <i class="bx bx-cloud-upload"></i>
                                 <span>Manage Content</span>
                                 <i class="bi bi-chevron-down ms-auto"></i>
                             </a>
                         </li>
                     </ul>

                     <ul class="menu_item">
                         <div class="menu_title flex">
                             <span class="title">Livestream</span>
                             <span class="line"></span>
                         </div>
                         <li class="item">
                             <a href="livestream.php" class="link flex">
                                 <i class="bx bxs-video-recording"></i>
                                 <span>Livestream</span>
                                 <i class="bi bi-chevron-down ms-auto"></i>
                             </a>
                         </li>
                     </ul>
                     <ul class="menu_item">
                         <div class="menu_title flex">
                             <span class="title">History</span>
                             <span class="line"></span>
                         </div>
                         <li class="item">
                             <a id="historyLink" href="history.php" class="link flex">
                                 <i class="bx bx-history"></i>
                                 <span>Recorded Streams</span>
                                 <i class="bi bi-chevron-down ms-auto"></i>
                             </a>
                         </li>
                     </ul>
             </div>
         </div>
     </div>
     <div id="main">
         <div id="content">
             <!-- header -->
             <nav class="header">
                 <a href="dashboard.php" class="logo">
                     <img src="assets/img/logo.png" alt="logo_img" />
                 </a>

                 <div class="hdr-left_Container">
                     <div>
                         <h4 id="userName"><?php echo $user['username'] ?></h4>
                         <p id="datetime"></p>
                     </div>
                     <a href="?signOut" id="sign-out-button">
                         <img src="./assets/img/logout.png" alt="SignOut Button" id="signOutImage">
                     </a>
                 </div>
             </nav>
             <div class="page-title">
                 <h1>Schedule Content</h1>
                 <p>Upload a file and choose a specific stream type.</p>
             </div>
             <!-- queueing -->

             <?php
                include("includes/db.php");

                // Fetch distinct schedTimes for the current date
                $sqlDistinctSchedTimes = "SELECT DISTINCT schedTime FROM queue WHERE dateOfAiring = CURRENT_DATE";
                $stmtDistinctSchedTimes = $db->prepare($sqlDistinctSchedTimes);

                if ($stmtDistinctSchedTimes->execute()) {
                    $stmtDistinctSchedTimes->bind_result($distinctSchedTime);
                    $distinctSchedTimes = [];

                    while ($stmtDistinctSchedTimes->fetch()) {
                        $distinctSchedTimes[] = $distinctSchedTime;
                    }

                    $stmtDistinctSchedTimes->close();
                } else {
                    echo "Error fetching distinct schedTimes: " . $stmtDistinctSchedTimes->error;
                }
                ?>

             <?php
                $sql = "SELECT queue.*, content.contentTitle, content.schedDate, users.fName, users.lName FROM queue 
                JOIN content ON queue.contentID = content.contentID 
                JOIN users ON content.userID = users.userID
                WHERE queue.dateOfAiring = CURRENT_DATE AND (queue.status = 'queued' OR queue.status = 'playing')";
                $stmt = $db->prepare($sql);

                if ($stmt) {
                    $stmt->execute();
                    $stmt->bind_result($queueID, $contentID, $dateOfAiring, $schedTime, $status, $contentTitle, $schedDate, $fName, $lName);
                    $distinctQueueIDs = [];

                    while ($stmt->fetch()) {
                        $distinctQueueIDs[$queueID] = $schedTime;
                    }

                    $stmt->close();

                    // HTML output
                    echo '<div class="content-managers-container">
                                <h1>Streaming Schedule</h1>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Title</th>
                                            <th>Status</th>
                                            <th>Uploader</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablebody">';

                    // Re-run the query to fetch the data again because it displays an error if not
                    $stmt = $db->prepare($sql);
                    $stmt->execute();
                    $stmt->bind_result($queueID, $contentID, $dateOfAiring, $schedTime, $status, $contentTitle, $schedDate, $fName, $lName);

                    while ($stmt->fetch()) {
                        echo '<tr>
                                    <td>' . date('d/m/Y', strtotime($dateOfAiring)) . '</td>
                                    <td>' . date('H:i:s', strtotime($schedTime)) . '</td>
                                    <td>' . htmlspecialchars($contentTitle) . '</td>';
                        echo '<td>
                                    <div class="status-indicator queued"></div>
                                    <span class="status-text">' . ($status) . '</span>
                                </td>
                                <td>' . $fName . ' ' . $lName . '</td>';

                        echo '<td>
                                <select class="form-control" id="schedTimeDropdown' . $queueID . '">';
                        foreach ($distinctSchedTimes as $time) {
                            echo '<option value="' . $time . '">' . date('H:i:s', strtotime($time)) . '</option>';
                        }
                        echo '</select>
                                <button class="btn btn-info" onclick="swapQueueItems(' . $queueID . ')">Swap</button>
                                <button class="btn btn-danger" onclick="deleteFromQueue(' . $queueID . ')">Delete</button>
                            </td>
                        </tr>';
                    }
                    echo '</tbody>
                                </table>
                            </div>';
                    $stmt->close();
                } else {
                    echo "Error preparing statement: " . $db->error;
                }
                ?>

             <?php
                include("includes/db.php");

                $sql = "SELECT content.*, users.fName, users.lName FROM content JOIN users ON content.userID = users.userID";
                $stmt = $db->prepare($sql);

                if ($stmt) {
                    $stmt->execute();
                    $stmt->bind_result($contentID, $contentTitle, $contentDesc, $schedDate, $userID, $filePath, $duration, $fName, $lName);

                    // HTML output
                    echo   '<div class="content-managers-container">
                                <h1>Uploaded Videos</h1>
                                <form id="submissionForm" method="post" action="add_to_queue.php">
                                    <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Title</th>
                                            <th>Type</th>
                                            <th>Duration</th>
                                            <th>Uploader</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablebody">';

                    while ($stmt->fetch()) {
                        echo '<tr>
                                        <td>' . $schedDate . '</td>
                                        <td>' . $contentTitle . '</td>';
                        $fileType = pathinfo($filePath, PATHINFO_EXTENSION);
                        echo '<td>' . $fileType . '</td>';
                        echo '<td>' . $duration . '</td>';
                        echo '<td>' . $fName . ' ' . $lName . '</td>
                                        <td>
                                            <input type="checkbox" name="selectedContent[]" id="row' . $contentID . '" class="checkbox" value="' . $contentID . '">
                                            <label for="row' . $contentID . '"></label>
                                        </td>
                                    </tr>';
                    }
                    echo '</tbody>
                                    </table>
                                    <button type="submit" id="submitBtn">Submit</button>
                                    </form>
                                </div>';
                    $stmt->close();
                } else {
                    echo "Error preparing statement: " . $db->error;
                }
                ?>

             <div class="schedule-stream-container">
                 <h1>Schedule Stream</h1>
                 <form id="scheduleForm" action="schedule-content.php" method="post" enctype="multipart/form-data">
                     <div class="form-group">
                         <label for="currentDate">Current Date:</label>
                         <input type="date" id="currentDate" name="currentDate">
                     </div>

                     <div class="form-group">
                         <label for="ContentFile">Upload File:</label>
                         <input type="file" id="ContentFile" name="ContentFile" accept=".mp3, .mp4, .jpg, .png, .jpeg, .HEIC" onchange="handleFileSelect()">
                         <p id="DurationDisplay">Duration: Not available</p>
                     </div>

                     <div class="form-group">
                         <label for="Title">Title (Max 60 characters):</label>
                         <input type="Text" id="Title" name="Title" maxlength="60">
                         <p id="TitleCount">0 characters</p>
                     </div>

                     <div class="form-group">
                         <label for="Description">Short Description (Max 5000 characters):</label>
                         <textarea id="Description" name="Description" maxlength="5000"></textarea>
                         <p id="DescriptionCount">0 characters</p>
                     </div>

                     <button type="submit" name="upload">Upload</button>
                 </form>
             </div>

             <?php
                // Display upload status message if set
                if (isset($uploadStatus)) {
                    $messageClass = ($uploadStatus === 'File successfully inserted!') ? 'success-message' : 'error-message';
                    echo "<p class='$messageClass'>$uploadStatus</p>";
                }
                ?>

             <!-- Include Bootstrap JS and Popper.js -->
             <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
             <!-- Include SortableJS -->
             <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
             <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
             <script src="assets/js/schedule-content.js"></script>
             <script src="assets/js/side-bar.js" defer></script>
         </div>
     </div>
 </body>

 </html>