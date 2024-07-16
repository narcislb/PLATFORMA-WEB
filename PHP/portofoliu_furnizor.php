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
<a href="firme-dashboard.php?user_id=<?php echo $firma_id; ?>" >
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



  <div class="content">

  <div style="text-align: center;">
  <form  method="post" enctype="multipart/form-data">
  <div>
        <label for="titlu">Titlu:</label>
        <textarea name="titlu" id="titlu" rows="5" required></textarea>
    </div>
    <div>
        <label for="descriere">Descriere pentru lucrarea efectuata:</label>
        <textarea name="descriere" id="descriere" rows="5" required></textarea>
    </div>
    
    <div>
    <label for="images">Alege imagini pentru a le incarca:</label>
    <input type="file" name="images[]" id="images" accept="image/*" multiple required>
</div>


    <input type="submit" value="Adauga lucrare" name="submit">
</form>


<?php


if (isset($_POST["submit"])) {
    $descriere = $_POST["descriere"];
    $titlu = $_POST["titlu"];
    $stmt = $conn->prepare("INSERT INTO tbl_portofoliu (titlu, descriere, id_firma) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi",$titlu, $descriere, $firma_id);
    
    // 
   // $id_descriere = $stmt->insert_id; // setam id-ul descrierii pentru a-l folosi la inserarea imaginilor
   
    if ($stmt->execute()) {
        $id_descriere = $stmt->insert_id;
        // icnarcam imaginile
        $image_count = count($_FILES['images']['name']);
        for ($i = 0; $i < $image_count; $i++) {
            $image_name = uniqid() . "_" . $_FILES['images']['name'][$i];
            $image_tmp = $_FILES['images']['tmp_name'][$i];
            $image_size = $_FILES['images']['size'][$i];
    
             if (!empty($image_name) && $image_size > 0) { 
                 $image_data = file_get_contents($image_tmp);

                 // salveaza imaginea in folderul portofoliu
                 $image_path = "../IMAGES/portofoliu/" . $image_name;
                 file_put_contents($image_path, $image_data);

                 // inseram imaginea in baza de date
                 $insert_image_stmt = $conn->prepare("INSERT INTO tbl_imagini_portofoliu (nume_imagine,id_descriere) VALUES (?, ?)");
                 if ($insert_image_stmt === false) {
                     die("Error preparing SQL statement: " . $conn->error);
                 }
                 $insert_image_stmt->bind_param("si", $image_name, $id_descriere);
                 $insert_image_stmt->execute();
                 $insert_image_stmt->close();
    }
}

} else {
    echo "Eroare la adăugarea : " . $conn->error;
}
}

?>

<?php


$offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;  // folosim un offset pentru a incarca portofoliul curent

// incarca portofoliul curent (offset)
$query = "SELECT * FROM tbl_portofoliu WHERE id_firma = ? LIMIT 1 OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $firma_id, $offset);
$stmt->execute();
$current_portofoliu = $stmt->get_result()->fetch_assoc();


if (!$current_portofoliu) {
    echo "<h2>Nu există niciun portofoliu înregistrat.</h2>";
} else 
{
// incarca imaginile
$query = "SELECT * FROM tbl_imagini_portofoliu WHERE id_descriere = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $current_portofoliu['id']);
$stmt->execute();
$images = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// verificam daca exista un portofoliu urmator
$query = "SELECT * FROM tbl_portofoliu WHERE id_firma = ? LIMIT 1 OFFSET ?";
$stmt = $conn->prepare($query);
$offset_plus_one = $offset + 1;
$stmt->bind_param("ii", $firma_id, $offset_plus_one);
$stmt->execute();
$next_portofoliu = $stmt->get_result()->fetch_assoc();

$stmt->close();

?>

<!-- HTML part -->
<h2>Portofoliu cu lucrari : </h2>
<h2><?php echo $current_portofoliu['titlu']; ?></h2>
<h2><?php echo $current_portofoliu['descriere']; ?></h2>

<!-- Display images -->
<?php foreach ($images as $image): ?>
    <img src="../IMAGES/portofoliu/<?php echo $image['nume_imagine']; ?>" alt="" width="300" height="300" style="object-fit: contain;">
<?php endforeach; ?>

<!-- Previous button -->
<?php if ($offset > 0): ?>
    <a href="portofoliu_furnizor.php?offset=<?php echo $offset - 1; ?>">Previous</a>
<?php endif; ?>


<!-- Next button -->
<?php if ($next_portofoliu): ?>
    <a href="portofoliu_furnizor.php?offset=<?php echo $offset + 1; ?>">Next</a>
<?php endif; ?>


<?php

$conn->close();
}
?>