<?php
session_start(); // Start the session
//verifică dacă utilizatorul este deja autentificat si daca este firma
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'firma') {
    //  utilizatorul nu este autentificat, redirecționează către pagina de autentificare
    header('Location: firme-login-form.php');
    exit;
}
    // Preluarea ID-ului firmei din URL
    $id_firma = $_SESSION['user_id'];
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



    $recenzii_per_pagina = 10; // sau oricât dorești
$pagina_curenta = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
$start = ($pagina_curenta - 1) * $recenzii_per_pagina;

$stmt = $conn->prepare("SELECT * FROM tbl_recenzii WHERE id_firma = ? LIMIT ?, ?");
$stmt->bind_param("iii", $id_firma, $start, $recenzii_per_pagina);
$stmt->execute();
$result = $stmt->get_result();


  if (isset($_GET['flagged']) && $_GET['flagged'] == 'true') {
    echo '<script>alert("Recenzie raportata cu succes.");</script>';
    unset($_GET['flagged']);
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
<body>
<header>
    <h1><a href="../index.php">SolarQuery</a></h1>
        <nav>
            <ul>
                <li><a href="../index.php">Acasă</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Shop</a>
                    <div class="dropdown-content">
                        <a href="produse.php">Produse</a>
                        <a href="servicii.php">Servicii</a>
                    </div>
                </li>
                <li><a href="../despre_noi.php">Despre noi</a></li>
                <li><a href="../contact.php">Contact</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Clienti</a>
                    <div class="dropdown-content">
                        <a href="clienti-register-form.php">Înregistrare</a>
                        <a href="clienti-login-form.php">Logare</a>
                        
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Furnizori</a>
                    <div class="dropdown-content">
                        <a href="firme-register-form.php">Înregistrare</a>
                        <a href="firme-login-form.php">Logare</a>
                        
                    </div>
                </li>
                <li class="button"><a href="../ADD_RECENZIE/adaugare_recenzie.php">Lasa o recenzie</a></li>
              
            </ul>
           
            <div>                 
<?php if(isset($_SESSION['user_id'])): ?>
  <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'client'): ?>
    <a href="../PHP/profil_client.php" class="account-button">My Account</a>
    <a href="../PHP/logout.php" class="logout-button">Logout</a>
  <?php elseif (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'firma'): ?>  
    <a href="../PHP/firme-dashboard.php" class="account-button">My Account</a>
    <a href="../PHP/logout.php" class="logout-button">Logout</a>
  <?php endif; ?>
<?php endif; ?>
</div>      
              

        </nav>

    </header>
<!-- script afisare firme -->
    <script>
    function fetchResults() {
        let query = document.getElementById('search-box').value;
     // verificam daca inputul este gol
     if (query.trim() === '') {
        document.getElementById('search-results-container').innerHTML = ''; // eliminam continutul din container
        return; // iesim din functie
    }
        // Efectuăm un request AJAX către scriptul PHP
        fetch('../ADD_RECENZIE/cauta_firma.php?query=' + query)
        .then(response => response.json())
        .then(data => {
            let resultsContainer = document.getElementById('search-results-container');
            resultsContainer.innerHTML = ''; // Resetează containerul
    
            if (data.length > 0) {
                data.forEach(firma => {
                    let firmaDiv = document.createElement('div');
                    let firmaLink = document.createElement('a');
                    firmaLink.href = '../PHP/profil_firma_copy.php?id=' + firma.id;
                    firmaLink.textContent = firma.nume_firma;
    
                    firmaDiv.appendChild(firmaLink);
                    resultsContainer.appendChild(firmaDiv);
                });
            } else {
                resultsContainer.innerHTML = 'Niciun rezultat găsit.';
            }
        })
        .catch(error => console.error('Error:', error));
    }
    

    </script>



<!-- Sidebar -->
<div class="sidebar">
<a href="firme-dashboard.php" >
    <h1>Dashboard</h1>
  </a>
    <ul>
      <li><a href="firme_afisare_comenzi.php">Comenzi</a></li>
       <li><a href="adaugare_date_furnizor.php">Informatii despre firma</a></li>
       <li><a href="firme_adaugare_produs.php">Adaugare produse</a></li>
       <li><a href="firme_afiasare_produse.php">Produse si servicii</a></li>
       <li><a href="portofoliu_furnizor.php">Portofoliu furnizor</a></li>
         <li><a href="firme_afisare_recenzii.php">Recenzii</a></li>
        <li><a href="../CHAT/messenger.php">Messenger</a></li>
       <li><a href="logout.php">Logout</a></li>
       
    </ul>
  </div>



  <div class="content">

  <h2>Recenzii</h2>
  <?php


while($row = $result->fetch_assoc()) {
    echo "<a href='../ADD_RECENZIE/flag_review.php?id=" . $row['id'] . "'>Raporteaza aceasta recenzie  </a>";

    echo "Nume client: " . $row['nume_client'] . "<br>";
     
     if ($row['flagged'] == 1) {
        echo "<strong>RAPORTAT</strong><br>";
    }

    echo "Descriere: " . $row['descriere'] . "<hr>";
    

    echo "<hr>";

}
if ($pagina_curenta > 1) {
    echo "<a href='firme_afisare_recenzii.php?pagina=" . ($pagina_curenta - 1) . "'>Prev</a> ";
}
echo "<a href='firme_afisare_recenzii.php?pagina=" . ($pagina_curenta + 1) . "'>Next</a>";


?>