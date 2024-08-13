<?php
$db = new mysqli("localhost", "root", "", "ph_hits");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
