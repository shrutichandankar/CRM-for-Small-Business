<?php
/*
    auth_check.php
    ---------------
    Include this file at the top of any page that should be
    accessible ONLY to logged-in users.
    It checks the session and redirects to login if not found.
*/
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
