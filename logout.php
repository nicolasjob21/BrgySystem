<?php
// Start the session
session_start();

// Destroy all session variables
session_destroy();

// Redirect the user to the login page
header("Location: http://localhost/BarangaySystem/Updated/login.php");

// Exit the script to prevent further execution
exit();
?>