<?php


/*
    config.php
    ----------
    This file connects our PHP project to the MySQL database.
    Every other page includes this file using: require_once 'config.php';
*/

// ---- Database settings (change these if your setup is different) ----
$db_host = "localhost";
$db_user = "root";
$db_pass = "password@123";          // XAMPP/WAMP default password is empty
$db_name = "crm_db";

// ---- Create connection using mysqli ----
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// ---- Check connection ----
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// ---- Start session on every page (needed for login system) ----
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
