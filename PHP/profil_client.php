<?php
session_start(); // pornește o sesiune

// preia datele despre utilizatorul curent
$username = $_GET['username'];

// conectarea la baza de date
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "solarquery";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Verifică dacă s-a realizat conexiunea la baza de date
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



//verifică dacă utilizatorul este deja autentificat si daca este client
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'client') {
  //  utilizatorul nu este  autentificat sau nu este client, redirecționează către pagina de logare
  //header('Location: clienti-login-form.php');
  //exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>My Page</title>
  <link rel="stylesheet" type="text/css" href="../CSS/adresa_styles.css">
</head>
<body>

<!-- Navbar -->
<div class="navbar">
  <h1>My Page</h1>
</div>

<!-- Sidebar -->
<div class="sidebar">
<a href="profil_client.php?username=' . $email . ' ])" >
    <h1>Dashboard</h1>
  </a>
    <ul>
      <li><a href="clienti_comenzi.php">Comenzi</a></li>
       <li><a href="adaugare_adresa_client.php">Adrese si date personale</a></li>
       <li><a href="../CHAT/messenger.php">Messenger</a></li>
       <li><a href="logout.php">Logout</a></li>
       
    </ul>
  </div>

<!-- Content box -->
<div class="content">
  <h2>Menu 1</h2>
  <p>Meniul pentru utilizatorul cu emailul <?php echo $_SESSION['username']; ?>.</p>
</div>

</body>
</html>