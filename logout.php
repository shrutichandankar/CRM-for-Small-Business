<?php
/*
    logout.php
    ----------
    Destroys the session and sends the user back to the login page.
    (No output is printed before header(), otherwise the redirect fails.)
*/

require_once __DIR__ . '/config.php';

session_unset();      // remove all session variables
session_destroy();    // destroy the session

// index.php shows "Logged out successfully" when ?logout=success is present
header("Location: index.php?logout=success");
exit();
?>
