<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// The user is logged in, show the protected content
?>

<!DOCTYPE html>
<html>
<head>
    <title>Protected page</title>
</head>
<body>
    <h1>Welcome to the protected page</h1>
    <p>Only logged-in users can see this content.</p>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>