<?php
// Start session and retrieve user data
session_start();
$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];


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

// Verifica daca user ul este autentificat
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'client') {
  // Redirectionare catre pagina de logare daca nu este client sau nu este logat
  header('Location: clienti-login-form.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));

   exit;
}
?>

<!-- HTML structure -->



<!DOCTYPE html>
<html>
<head>
  <title>Adresa si date personale</title>
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
     // verificam daca inputul de cautare este gol
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
  <h2>Adresa si date personale</h2>
  
  <?php
  // Initialization
  $tara = $judet = $localitate = $adresa = $cod_postal = $telefon = $nume = "";



  // Fetch data
  $stmt = $conn->prepare('SELECT * FROM tbl_adrese WHERE id_client = ?');
  $stmt->bind_param('i', $user_id);
  $stmt->execute();
  $userData = $stmt->get_result();

  $addressExists = false;

  $userDataArray = $userData->fetch_assoc();

  if ($userDataArray) {
      extract($userDataArray);
      $addressExists = true;
  }

  // Form submission logic
  if (isset($_POST['submit'])) {
    // Capture data from form
    $tara = $_POST['tara'];
    $judet = $_POST['judet'];
    $localitate = $_POST['localitate'];
    $adresa = $_POST['adresa'];
    $cod_postal = $_POST['cod_postal'];
    $telefon = $_POST['telefon'];
    $nume = $_POST['nume'];
  
    if ($addressExists) {
      // Update the database
      $stmt = $conn->prepare('UPDATE tbl_adrese SET tara = ?, judet = ?, localitate = ?, adresa = ?, cod_postal = ?, telefon = ?, nume = ? WHERE id_adresa = ?');
      $stmt->bind_param('ssssissi', $tara, $judet, $localitate, $adresa, $cod_postal, $telefon, $nume, $id_adresa);
      $stmt->execute();
      echo '<p>Datele au fost actualizate cu succes.</p>';
  } else {
      // Insert new address
      $stmt = $conn->prepare('INSERT INTO tbl_adrese (id_client, tara, judet, localitate, adresa, cod_postal, telefon, nume) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
      $stmt->bind_param('issssiss', $user_id, $tara, $judet, $localitate, $adresa, $cod_postal, $telefon, $nume);
      $stmt->execute();
      echo '<p>Adresa a fost adaugata cu succes.</p>';
  }
}
  ?>

  <!--  -->
  <form method="post">
  <div>
      <label for="tara">Tara:</label>
      <input type="text" name="tara" id="tara" value="<?php echo htmlspecialchars($tara); ?>" required>
    </div>
    <div>
      <label for="judet">Judet:</label>
      <input type="text" name="judet" id="judet" value="<?php echo htmlspecialchars($judet); ?>"    required>
    </div>
    <div>
      <label for="localitate">Localitate:</label>
      <input type="text" name="localitate" id="localitate" value="<?php echo htmlspecialchars($localitate); ?>" required>
    </div>
    <div>
      <label for="adresa">Adresa:</label>
      <input type="text" name="adresa" id="adresa" value="<?php echo htmlspecialchars($adresa); ?>" required>
    </div>
    <div>
      <label for="cod_postal">Cod postal:</label>
      <input type="text" name="cod_postal" id="cod_postal" value="<?php echo htmlspecialchars($cod_postal); ?>" required>
    </div>
    <div>
      <label for="telefon">Telefon:</label>
      <input type="text" name="telefon" id="telefon" value="<?php echo htmlspecialchars($telefon); ?>" required>
    </div>
    <div>
      <label for="nume">Nume:</label>
      <input type="text" name="nume" id="nume" value="<?php echo htmlspecialchars($nume); ?>" required>
    </div>
    <input type="submit" name="submit" value="<?= $addressExists ? 'Actualizeaza datele' : 'Adauga datele' ?>">
  </form>
</div>

</body>
</html>

