<?php
session_start();
?>

<div class="hdr-left_Container">
    <div>
        <h4 id="userName"><?php echo $_SESSION['user']['firstName'] . ' ' . $_SESSION['user']['lastName']; ?></h4>
        <p id="datetime"></p>
    </div>
    <a href="#" id="sign-out-button">
        <img src="./assets/img/logout.png" alt="SignOut Button" id="signOutImage">
    </a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const signOutButton = document.getElementById('sign-out-button');

        signOutButton.addEventListener('click', function(event) {
            event.preventDefault();

            fetch('/logout.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Logged out successfully:', data);
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
        });
    });
</script>