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
    $id_firma = $_SESSION['user_id'];
    




    // Conectarea la baza de date
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $dbname = "solarquery";
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

?>

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
                <li class="search-container">
                    <!-- Formularul de căutare -->
                    <form method="GET">
                        <input type="text" class="search-box" id="search-box" name="termen_cautare" placeholder="Caută o firmă..." onkeyup="fetchResults()">
                        <div id="search-results-container"></div>
                    </form>
                </li>
                
                


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
        document.getElementById('search-results-container').innerHTML = ''; // curață containerul de rezultate
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
<a href="firme-dashboard.php" >
    <h1>Dashboard</h1>
  </a>
    <ul>
      <li><a href="firme_afisare_comenzi.php">Comenzi</a></li>
       <li><a href="adaugare_date_furnizor.php">Informatii despre firma</a></li>
       <li><a href="firme_adaugare_produs.php">Adaugare produse</a></li>
       <li><a href="firme_afisare_produse.php">Produse si servicii</a></li>
       <li><a href="portofoliu_furnizor.php">Portofoliu furnizor</a></li>
       <li><a href="firme_afisare_recenzii.php">Recenzii</a></li>
       <li><a href="../CHAT/messenger.php">Messenger</a></li>
       <li><a href="logout.php">Logout</a></li>
       
    </ul>
  </div>





<?php
$sql = "SELECT nume_firma, persoana_de_contact, telefon, services, zona FROM tbl_firme WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_firma);
$stmt->execute();
$result = $stmt->get_result();
$firma = $result->fetch_assoc();
?>

<form method="post" >
    Nume Firma: <input type="text" name="nume_firma" value="<?php echo $firma['nume_firma']; ?>"><br>
    Persoana de Contact: <input type="text" name="persoana_de_contact" value="<?php echo $firma['persoana_de_contact']; ?>"><br>
    Telefon: <input type="text" name="telefon" value="<?php echo $firma['telefon']; ?>"><br>
    Servicii: <input type="text" name="services" value="<?php echo $firma['services']; ?>"><br>
    Zona: <input type="text" name="zona" value="<?php echo $firma['zona']; ?>"><br>
    <input type="submit" value="Actualizeaza">
</form>


<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nume_firma = $_POST['nume_firma'];
    $persoana_de_contact = $_POST['persoana_de_contact'];
    $telefon = $_POST['telefon'];
    $services = $_POST['services'];
    $zona = $_POST['zona'];

    $sql = "UPDATE tbl_firme SET nume_firma=?, persoana_de_contact=?, telefon=?, services=?, zona=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nume_firma, $persoana_de_contact, $telefon, $services, $zona, $id_firma);
    
    if ($stmt->execute()) {
        
        echo '<div style="text-align: center;">Actualizat cu succes,Dati refresh la pagina!</div>';
    } else {
        echo "Error: " . $stmt->error;
    }
    
}
?>