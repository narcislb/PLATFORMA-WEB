
<?php
session_start(); // Start the session
//verifică dacă utilizatorul este deja autentificat si daca este firma
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'firma') {
    //  utilizatorul nu este autentificat, redirecționează către pagina de autentificare
    header('Location: firme-login-form.php');
    exit;
}
    // Preluarea ID-ului firmei din URL
    $firma_id = $_SESSION['user_id'];
    $numa_firma['nume_firma'] =$_SESSION['nume_firma'];
    // Conectarea la baza de date
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $dbname = "solarquery";

    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    // Verificarea conexiunii
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    ?>

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
       <li><a href="logout.php">Logout</a></li>
    </ul>
  </div>

    <!-- afiseaza comenzile           -->
  

    <h2>Comenzile primite:</h2>

    


    

      <?php
   // Default sort order
   $sortDirection = 'asc';
   $currentSort = '';

   // If a sort direction and column are specified, use them. Otherwise, use defaults.
   if (isset($_GET['direction']) && in_array($_GET['direction'], ['asc', 'desc'])) {
       $sortDirection = $_GET['direction'];
   }

   if (isset($_GET['sort'])) {
       $currentSort = $_GET['sort'];
   }

   $sql = "SELECT * FROM tbl_comenzi WHERE id_firma = '$firma_id'";

   if ($currentSort) {
       $sql .= " ORDER BY $currentSort $sortDirection";
   }

   $result = $conn->query($sql);
?>

<table style="margin-left: 300px;margin-top: 95px;">
    <thead>
        <tr>
            <th><a href="?sort=id_comanda&direction=<?php echo ($currentSort === 'id_comanda' && $sortDirection === 'asc') ? 'desc' : 'asc'; ?>">ID comanda</a></th>
            <th><a href="?sort=data_comanda&direction=<?php echo ($currentSort === 'data_comanda' && $sortDirection === 'asc') ? 'desc' : 'asc'; ?>">Data comanda</a></th>
            <th><a href="?sort=id_adresa_livrare&direction=<?php echo ($currentSort === 'id_adresa_livrare' && $sortDirection === 'asc') ? 'desc' : 'asc'; ?>">Id adresa livrare</a></th>
            <th><a href="?sort=status_comanda&direction=<?php echo ($currentSort === 'status_comanda' && $sortDirection === 'asc') ? 'desc' : 'asc'; ?>">Status comanda</a></th>
        </tr>
    </thead>
    <tbody>
<?php
   if ($result->num_rows > 0) {
       while($row = $result->fetch_assoc()) {
           echo "<tr>";
           echo "<td>" . $row["id_comanda"] . "</td>";
           echo "<td>" . $row["data_comanda"] . "</td>";
           echo "<td>" . $row["id_adresa_livrare"] . "</td>";
           echo "<td>" . $row["status_comanda"] . "</td>";
           echo "</tr>";
       }
   } else {
       echo "<tr><td colspan='4'>Nu există comenzi.</td></tr>";
   }

   $conn->close();
?>
    </tbody>
</table>

<!DOCTYPE html>
<html>
<head>
  <title>My Page</title>
  <link rel="stylesheet" type="text/css" href="../CSS/clienti_comenzi.css">
</head>