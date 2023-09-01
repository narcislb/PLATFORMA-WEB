<?php
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "solarquery";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query pentru a selecta produsele din baza de date în funcție de subcategorie și filtrul selectat
if (isset($_GET['subcategorie'])) {
    $subcategorie = $_GET['subcategorie'];
    if (isset($_GET['pret'])) { // Verificare dacă a fost selectat un filtru de preț
        $pret = $_GET['pret'];
        if ($pret == '0-50') {
            $sql = "SELECT * FROM tbl_produse WHERE subcategorie = '$subcategorie' AND pret_produs >= 0 AND pret_produs <= 50";
        } else if ($pret == '51-100') {
            $sql = "SELECT * FROM tbl_produse WHERE subcategorie = '$subcategorie' AND pret_produs >= 51 AND pret_produs <= 100";
        } else {
            $sql = "SELECT * FROM tbl_produse WHERE subcategorie = '$subcategorie'";
        }
    } else {
        $sql = "SELECT * FROM tbl_produse WHERE subcategorie = '$subcategorie'"; // Query pentru a selecta produsele din baza de date în funcție de subcategorie
    }
} else {
    $sql = "SELECT * FROM tbl_produse";
}

$result = $conn->query($sql);    // Executarea interogării SQL

// Verificare dacă există erori în execuția interogării
if (!$result) {
    die("Error executing query: " . $conn->error);
}


?>



<!DOCTYPE html>
<html>
<head>
    <title>Lista produse</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../CSS/lista_produse_style.css">
</head>

<body>
<header>
        <h1><a href="../index.php">SolarQuery Home</a></h1>
        <nav>
            <ul>
                <li><a href="#">Acasă</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Shop</a>
                    <div class="dropdown-content">
                        <a href="HTML/produse.html">Produse</a>
                        <a href="HTML/servicii.html">Servicii</a>
                    </div>
                </li>
                <li><a href="#">Despre noi</a></li>
                <li><a href="#">Contact</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Clienti</a>
                    <div class="dropdown-content">
                        <a href="HTML/clienti-register.html">Înregistrare</a>
                        <a href="PHP/clienti-login-form.php">Logare</a>
                        
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Furnizori</a>
                    <div class="dropdown-content">
                        <a href="HTML/firme-register.html">Înregistrare</a>
                        <a href="PHP/firme-login-form.php">Logare</a>
                        
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
  <?php if($_SESSION['user_type'] == 'client'): ?>
    <a href="../PHP/profil_client.php" class="account-button">My Account</a>
    <a href="../PHP/logout.php" class="logout-button">Logout</a>
  <?php elseif ($_SESSION['user_type'] == 'firma'): ?>  
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


<button onclick="goBack()">Mergi inapoi</button>
   
<h2>Produse</h2>

    <form method="get" action="lista-produse.php">
        <!-- Selectarea subcategoriei,se obtine din URL folosind GET  --> 
    <input type="hidden" name="subcategorie" value="<?php echo $_GET['subcategorie']; ?>">
        <label for="pret">Filtrare după preț:</label>
        <select name="pret" id="pret">
            <option value="">Toate</option>
            <option value="0-50">0 - 50</option>
            <option value="51-100">51 - 100</option>
            <!-- Alte opțiuni de preț -->
        </select>
        <button type="submit">Filtrare</button>
    </form>

    <div class="lista-produse">
        <?php
        // Verificare dacă există produse în baza de date
        if ($result->num_rows > 0) {
            // Afișarea produselor într-un container 
            while ($row = $result->fetch_assoc()) {
                echo "<div class='produs'>";
                // duce la pagina produsului, unde se afiseaza detaliile produsului
                echo "<h3><a href='produs.php?id=" . $row['id'] . "'>" . $row['nume_produs'] . "</a></h3>";
                //echo "<h3>" . $row['nume_produs'] . "</h3>";
                echo "<p>Pret: " . $row['pret_produs'] . " lei</p>";
                echo "<p>Descriere: " . $row['descriere_produs'] . "</p>";
                // Afișarea imaginii produsului
                $image_sql = "SELECT nume_imagine FROM tbl_imagini WHERE id_produs = " . $row['id']; // Query pentru a selecta imaginea produsului din baza de date 
                $image_result = $conn->query($image_sql);
                if ($image_result->num_rows > 0) {
                    $image_row = $image_result->fetch_assoc();
                    $image_path = "../IMAGES/products/" . $image_row['nume_imagine'];
                    // Afișarea imaginii produsului în funcție de calea imaginii 
                    echo "<img src='" . $image_path . "' alt='Imagine produs' style='width: 300px; height: 300px;'>"; 

                } else {
                    echo "<p>Imagine indisponibilă</p>";
                }
                echo "</div>";
            }
        } else {
            echo "Nu există produse în baza de date.";
        }
        ?>
    </div>

 

</body>
</html>

<script>
    function goBack() {
        window.history.back();
    }
</script>

<?php

$conn->close();
?>
