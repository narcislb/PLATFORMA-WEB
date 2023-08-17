<?php
session_start(); // Start the session

// Unset all of the session variables
$_SESSION = array();

// Finally, destroy the session
session_destroy();


// Redirect to the appropriate login page
if ($_SESSION['user_type'] == 'client') {
    header("Location: client-login-form.php");
} else if ($_SESSION['user_type'] == 'firma') {
    header("Location: firme-login-form.php");
} else {
    header("Location: ../index.html");
}
exit;
?>