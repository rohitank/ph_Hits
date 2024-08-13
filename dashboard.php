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

  ?>

 <!DOCTYPE html>
 <html lang="en">

 <head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Document</title>
   <link rel="stylesheet" href="./assets/css/dashboard.css">
   <link href="assets/css/header.css" rel="stylesheet">
   <!-- Boxicons CSS -->
   <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
   <link rel="stylesheet" href="assets/css/side-bar.css" />
   <script src="assets/js/side-bar.js" defer></script>
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
       <!-- Dashboard Section -->
       <section class="dashboard">
         <div class="currentStream">
           <h2>Now Streaming</h2>
           <div id="stream-container">
             <video autoplay muted id="strm-vid"></video>
             <div>
               <h3 id="prerecorded-title"></h3>
               <h3 id="prerecorded-cm"></h3>
             </div>
           </div>
         </div>
       </section>

       <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
       <script src="assets/js/dashboard.js"></script>
     </div>
   </div>

 </body>

 </html>