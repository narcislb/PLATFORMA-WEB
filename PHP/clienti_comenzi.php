<?php

session_start(); // pornește o sesiune

// Check if user is logged in as client
if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'client') {
  header('Location: clienti-login-form.php');
  exit();
}


// preia datele despre utilizatorul curent
$username = $_SESSION['username'];
$user_id= $_SESSION['user_id'];


// conectare la baza de date
$host = 'localhost';
$dbname = 'solarquery';
$user = 'root';
$db_password = '';

$db = new PDO("mysql:host=$host;dbname=$dbname", $user, $db_password);
?>


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
       <li><a href="logout.php">Logout</a></li>
    </ul>
  </div>

<!-- Display orders for client -->
  

<h2>Comenzile mele</h2>
<table style="margin-left: 300px;margin-top: 95px;">

  <thead>
    <tr>
      <th>ID comanda</th>
      <th>Data comanda</th>
      <th>Valoare comanda</th>
      <th>Status comanda</th>
    </tr>
  </thead>
  <tbody>
    <?php
    // Retrieve orders for client from database
    $stmt = $db->prepare('SELECT * FROM tbl_comenzi WHERE id_client = :user_id');
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Loop through orders and display details
    foreach ($orders as $order) {
      echo '<tr>';
      echo '<td>' . $order['id_comanda'] . '</td>';
      echo '<td>' . $order['data_comanda'] . '</td>';
      echo '<td>' . $order['total_de_plata'] . '</td>';
      echo '<td>' . $order['status_comanda'] . '</td>';
      echo '</tr>';
    }
    ?>
  </tbody>
</table>


<!DOCTYPE html>
<html>
<head>
  <title>My Page</title>
  <link rel="stylesheet" type="text/css" href="../CSS/clienti_comenzi.css">
</head>
<body>
