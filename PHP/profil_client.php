<?php
session_start(); // pornește o sesiune

// preia datele despre utilizatorul curent
$username = $_SESSION['username'];

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
  header('Location: clienti-login-form.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
exit;
}

// obtine data inregistrarii clientului
$query = "SELECT date FROM tbl_clienti WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username); // unde username este emailul clientului
$stmt->execute();
$stmt->bind_result($registration_date);
$stmt->fetch();
$stmt->close();


$now = new DateTime();  // current date
$registered_date = new DateTime($registration_date);
$interval = $now->diff($registered_date);
$days_since_registration = $interval->days;

  

?>

<!DOCTYPE html>
<html>
<head>
  <title>Profil client</title>
  <link rel="stylesheet" type="text/css" href="../CSS/profil_client.css">
</head>
<body>
<header>
<h1><a href="../index.php">SolarQuery</a></h1>
        <nav>
            <ul>
                <li><a href="#">Acasă</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Shop</a>
                    <div class="dropdown-content">
                        <a href="../php/produse.php">Produse</a>
                        <a href="../php/servicii.php">Servicii</a>
                    </div>
                </li>
                <li><a href="../despre_noi.php">Despre noi</a></li>
                <li><a href="../contact.php">Contact</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Clienti</a>
                    <div class="dropdown-content">
                        <a href="../PHP/clienti-register-form.php">Înregistrare</a>
                        <a href="../PHP/clienti-login-form.php">Logare</a>
                        
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Furnizori</a>
                    <div class="dropdown-content">
                        <a href="../PHP/firme-register-form.php">Înregistrare</a>
                        <a href="../PHP/firme-login-form.php">Logare</a>
                        
                    </div>
                </li>
                <li class="button"><a href="../ADD_RECENZIE/adaugare_recenzie.php">Lasa o recenzie</a></li>
                <li class="search-container">
                    <!-- Formularul de căutare -->
                    <form method="GET">
                        <input type="text" class="search-box" id="search-box" name="termen_cautare" placeholder="Caută o firmă..." onkeyup="fetchResults()">
                        <div id="search-results-container"></div>
                    </form>
                </li>
                

                <div class="cart-icon-container">
                        <a href="../PHP/cos-cumparaturi.php">
                             <i class="fas fa-shopping-cart"></i> 
                                    <span class="cart-item-count">
                            <?php 
                        if(isset($_SESSION['cos-cumparaturi']) && is_array($_SESSION['cos-cumparaturi'])) {
                            echo array_sum($_SESSION['cos-cumparaturi']); 
                        } else {
                            echo 0;
                        }
                            ?>
                            </span>
                        </a>
                    </div>




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

    <script>
    function fetchResults() {
        let query = document.getElementById('search-box').value;
     // verifică dacă query-ul nu este gol
     if (query.trim() === '') {
        document.getElementById('search-results-container').innerHTML = ''; // elimină rezultatele anterioare
        return; // iese din funcție
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
<a href="profil_client.php" >
    <h1>Dashboard</h1>
  </a>
    <ul>
      <li><a href="clienti_comenzi.php">Comenzi</a></li>
       <li><a href="adaugare_adresa_client.php">Adrese si date personale</a></li>
       <li><a href="adaugare_date_persoana_juridica.php">Date persoana juridica</a></li>
         <li><a href="../CHAT/messenger.php">Messenger</a></li>
       <li><a href="logout.php">Logout</a></li>
       
    </ul>
  </div>

<!-- Content box -->
<div class="content">
  
  <h1>Contul meu</h1>
  <p>Bun venit </p>
  <p>Iti multumim ca esti client aici de <?= $days_since_registration; ?> zile.</p>
</div>

</body>
</html>