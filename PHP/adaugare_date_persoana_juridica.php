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
  <title>Date persoana juridica</title>
  <link rel="stylesheet" type="text/css" href="../CSS/adaugare_date_juridica.css">
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
     // Check if the query is empty or not
     if (query.trim() === '') {
        document.getElementById('search-results-container').innerHTML = ''; // Clear any previous results
        return; // Exit the function early
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
  <h2>Date persoana juridica</h2>
  
  <?php
  // Initialization
  $nume_companie= $CUI= $NumarRegCom= $SediuSocial= "";



  // Fetch data
  $stmt = $conn->prepare('SELECT persoana_juridica_id FROM tbl_clienti WHERE id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$clientData = $stmt->get_result();
$clientDataArray = $clientData->fetch_assoc();

$companyExists = false;
$companyDataArray = null;

if ($clientDataArray['persoana_juridica_id']) {
    $stmt = $conn->prepare('SELECT * FROM tbl_date_persoana_juridica WHERE id = ?');
    $stmt->bind_param('i', $clientDataArray['persoana_juridica_id']);
    $stmt->execute();
    $companyData = $stmt->get_result();
    $companyDataArray = $companyData->fetch_assoc();
    if ($companyDataArray) {
        extract($companyDataArray);
        $companyExists = true;
    }
}




  //interogari precompilate

  // Form submission logic
  if (isset($_POST['submit'])) {
    // ... Capture data from form ...
    $nume_companie = $_POST['nume_companie'];
    $CUI = $_POST['CUI'];
    $NumarRegCom = $_POST['NumarRegCom'];
    $SediuSocial = $_POST['SediuSocial'];
   
    if ($companyExists) {
        // Update the existing data in tbl_date_persoana_juridica
        $stmt = $conn->prepare('UPDATE tbl_date_persoana_juridica 
                        SET nume_companie = ?, CUI = ?, NumarRegCom = ?, SediuSocial = ? 
                        WHERE id = (SELECT persoana_juridica_id FROM tbl_clienti WHERE id = ?)');
        $stmt->bind_param('ssssi', $nume_companie, $CUI, $NumarRegCom, $SediuSocial, $user_id);
        $stmt->execute();
        echo '<p>Datele au fost actualizate cu succes.</p>';
    } else {
        // Insert new data in tbl_date_persoana_juridica
        $stmt = $conn->prepare('INSERT INTO tbl_date_persoana_juridica ( nume_companie, CUI, NumarRegCom, SediuSocial) VALUES ( ?, ?, ?, ?)');
        $stmt->bind_param('siss', $nume_companie, $CUI, $NumarRegCom, $SediuSocial);
        $stmt->execute();
        
        $last_id = $conn->insert_id;
    
        // Update the tbl_clienti with the $last_id
        $updateStmt = $conn->prepare('UPDATE tbl_clienti SET persoana_juridica_id = ? WHERE id = ?');
        $updateStmt->bind_param('ii', $last_id, $user_id);
        if (!$updateStmt->execute()) {
            die("Execution failed: " . $updateStmt->error);
        }
        echo '<p>Datele au fost adaugate cu succes.</p>';

        // set persoana_juridica to true in tbl_clienti
        $stmt = $conn->prepare('UPDATE tbl_clienti SET persoana_juridica = 1 WHERE id = ?');
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
    }
}


  
 

  ?>
  

    
<form method="post">
  <div>
      <label for="nume_companie">Nume companie:</label>
      <input type="text" name="nume_companie" id="nume_companie" value="<?php echo htmlspecialchars($nume_companie); ?>" required>
    </div>

    <div>
      <label for="CUI">CUI:</label>
      <input type="text" name="CUI" id="CUI" value="<?php echo htmlspecialchars($CUI); ?>"    required>
    </div>

    <div>
      <label for="NumarRegCom">Numărul de ordine din registrul comerţului</label>
      <input type="text" name="NumarRegCom" id="NumarRegCom" value="<?php echo htmlspecialchars($NumarRegCom); ?>" required>
    </div>

    <div>
      <label for="adresa">Adresa sediu social:</label>
      <input type="text" name="SediuSocial" id="SediuSocial" value="<?php echo htmlspecialchars($SediuSocial); ?>" required>
    </div>

    <div>
    <input type="submit" name="submit" value="<?= $companyExists ? 'Actualizeaza datele' : 'Adauga datele' ?>">
 
    </div>



    </form>
</div>

   

</body>
</html>

