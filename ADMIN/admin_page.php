<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: admin_login.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../CSS/admin_page.css">
</head>
<body>

<!-- Header -->
<header>
    <div class="header-content">
       
        <div class="logo"><a href="admin_page.php">SolarQuery Admin</a></div>
        <nav>
            <ul>
                
                <li><a href="#">Administrare useri</a></li>
                <li><a href="#">Vizualizare rapoarte</a></li>
                <li><a href="../PHP/logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<!-- Sidebar -->
<aside class="sidebar">
    <ul>
        <li><a href="admin_flagged_review.php">Recenzii raportate</a></li>
        <li><a href="#">Coming soon</a></li>
        <li><a href="#">Coming soon</a></li>
    </ul>
</aside>

<!-- Main Content -->
<main>
    <h2>Bun venit</h2>
    <!-- Other content goes here -->
</main>

</body>
</html>

