<?php
// Initialize the session
session_start();

// Unset all of the session variables to clear the data
$_SESSION = array();

// Destroy the session completely
session_destroy();

// Redirect the user back to the login page
// Note: If your login page has a different filename (like index.php), change it here
header("Location: dashboard.php");
exit();
?>