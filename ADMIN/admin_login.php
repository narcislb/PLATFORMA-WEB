<?php
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "solarquery";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<?php
session_start();


$error = '';
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password']; // plain text password

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['logged_in'] = true;
        $_SESSION['user_role'] = 'admin';
        header('Location: admin_page.php');
        exit;
    } else {
        $error = "Invalid credentials!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" type="text/css" href="../CSS/admin_page.css">
</head>
<body>
<header>
    <div class="header-content">
        <div class="logo">SolarQuery Admin</div>
        <nav>
            <ul>
                
                <li><a href="#">Administrare useri</a></li>
                <li><a href="#">Vizualizare rapoarte</a></li>
                <li><a href="../PHP/logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<div style="display: flex; justify-content: center; align-items: center; height: 200px;">
  <form action="" method="post">
    <h2 style="text-align: center;">Admin Login</h2>
    <div style="text-align: center;">
      <label for="username">Username:</label>
      <input type="text" name="username" required><br><br>
      <label for="password">Password:</label>
      <input type="password" name="password" required><br><br>
      <input type="submit" name="submit" value="Login">
    </div>
</form>

<?php if ($error) { echo "<p style='color:red;'>$error</p>"; } ?>

</body>
</html>
