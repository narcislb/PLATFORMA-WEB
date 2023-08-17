

<?php
    // Inițializarea sesiunii
session_start(); 
    //verifică dacă utilizatorul este deja autentificat si daca este firma
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'firma') {
  //  utilizatorul nu este autentificat, redirecționează către pagina de autentificare
  header('Location: firme-login-form.php');
  exit;
}
    // Preluarea ID-ului firmei din URL
    $firma_id = $_GET['user_id'];

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
<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
  <meta charset="utf-8">
  <link rel="stylesheet" type="text/css" href="../CSS/adresa_styles.css">
</head>

<!-- Navbar -->
<div class="navbar">
  <h1><a href="../index.html">SolarQuery</a></h1>
</div>

<!-- Sidebar -->
<div class="sidebar">
<a href="firme-dashboard.php?user_id=<?php echo $firma_id; ?>" >
    <h1>Dashboard</h1>
  </a>
    <ul>
      <li><a href="firme_afisare_comenzi.php">Comenzi</a></li>
       <li><a href="adaugare_date_firma.php">Informatii firma</a></li>
       <li><a href="firme_adaugare_produs.php">Adaugare produse</a></li>
       <li><a href="firme_afiasare_produse.php">Produse si servicii</a></li>
       <li><a href="../CHAT/messenger.php">Messenger</a></li>
       <li><a href="logout.php">Logout</a></li>
       
    </ul>
  </div>

  <div class="content">
    <?php
    

    // Interogarea bazei de date pentru a obține comenzile firmei curente (în funcție de ID) 
    $sql = "SELECT * FROM comenzi WHERE firma_id = '$firma_id'";   
    $result = $conn->query($sql);

    if ($result->num_rows > 0) { 
        // Afișarea comenzilor într-o listă 
        echo "<h2>Comenzi:</h2>";
        echo "<ul>";
        while($row = $result->fetch_assoc()) {
            echo "<li>" . $row["nume_client"] . " - " . $row["descriere"] . " (ID: " . $row["id"] . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "Nu există comenzi.";
    }

    $conn->close();
    ?>
  </div>
</body>
</html>


























































