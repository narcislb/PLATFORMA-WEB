






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
    $firma_id = $_SESSION['user_id'];

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
  <h1>Adăugare produse si servicii</h1>

  
  <form method="post" enctype="multipart/form-data">
    <label for="nume">Nume:</label>
    <input type="text" name="nume" id="nume" required style="width: 300px;">
    <br><br>

    <label for="pret">Preț:</label>
    <input type="number" name="pret" id="pret" required>
    <br><br>

    <label for="categorie">Categorie:</label>
    <select name="categorie" id="categorie" required>
      <option value="">Selectați o categorie</option>
      <option value="Invertoare">Invertoare</option>
      <option value="Baterii-Acumulatori">Baterii-Acumulatori</option>
      <option value="Sisteme">Sisteme</option>
      <option value="Regulatoare solare">Regulatoare solare</option>
      <option value="Accesorii">Accesorii</option>
      <option value="Panouri">Panouri</option>
      <option disabled>SERVICII: </option>
      <option value="Servicii consultanta">Servicii consultanta</option>
      <option value="servicii de montaj">servicii de montaj</option>
      <option value="servicii de mentenanta">servicii de mentenanta</option>
      


      <!-- Adaug alte optiuni pentru categorii aici -->
    </select>
    <br><br>

    <label for="subcategorie">Subcategorie:</label>
    <select name="subcategorie" id="subcategorie" required>
      <!-- subcategoria va fi populata pe baza categoriei alese -->
    </select>
    <br><br>

    <script>
    document.getElementById("categorie").addEventListener("change", function() {
    let subcategorie = document.getElementById("subcategorie");
    
    // șterge opțiunile existente din subcategorie
    subcategorie.innerHTML = '<option value="">Selectați o subcategorie</option>';

    // pe bază de categorie, adaugă opțiuni pentru subcategorie
    switch(this.value) {
        case 'Invertoare':
            subcategorie.innerHTML += '<option value="monofazate">monofazate</option>';
            subcategorie.innerHTML += '<option value="trifazate">trifazate</option>';
            subcategorie.innerHTML += '<option value="dc-dc">dc-dc</option>';
            subcategorie.innerHTML += '<option value="Invertoare_Altele">altele</option>';
            break;

        case 'Baterii-Acumulatori':
            subcategorie.innerHTML += '<option value="Acumulator cu acid">Acumulator cu acid</option>';
            subcategorie.innerHTML += '<option value="Baterie Lithium">Baterie Lithium</option>';
            subcategorie.innerHTML += '<option value="Baterie Gel">Baterie Gel</option>';
            subcategorie.innerHTML += '<option value="Baterie_Altele">Altele</option>';

            break;

        case 'Sisteme':
            subcategorie.innerHTML += '<option value="sistem fotovoltaic off grid">sistem fotovoltaic off grid</option>';
            subcategorie.innerHTML += '<option value="sistem fotovoltaic on grid hibrid">sistem fotovoltaic on grid hibrid</option>';
            subcategorie.innerHTML += '<option value="sistem fotovoltaic on grid">sistem fotovoltaic on grid</option>';
            subcategorie.innerHTML += '<option value="Sisteme_Altele">Altele</option>';

            break;

         case 'Regulatoare solare':
            subcategorie.innerHTML += '<option value="Controller MPPT">Controller MPPT</option>';
            subcategorie.innerHTML += '<option value="Controller_Altele">Altele</option>';
            
            break;

        case 'Accesorii':
            subcategorie.innerHTML += '<option value="Accesorii montaj panouri">Accesorii montaj panouri</option>';
            subcategorie.innerHTML += '<option value="Cabluri">Cabluri</option>';
            subcategorie.innerHTML += '<option value="Accesorii_Altele">Altele</option>';
            
            break;
        case 'Panouri':
            subcategorie.innerHTML += '<option value="Monocristalin">Monocristalin</option>';
            subcategorie.innerHTML += '<option value="Policristalin">Policristalin</option>';
            subcategorie.innerHTML += '<option value="Panouri_Altele">Altele</option>';
            
            break;  
         
        case 'Servicii consultanta':
            subcategorie.innerHTML += '<option value="servicii de consultanță și proiectare">servicii de consultanță și proiectare</option>';
            subcategorie.innerHTML += '<option value="documentatie prosumator">documentatie prosumator</option>';
            subcategorie.innerHTML += '<option value="Consultanta_Altele">Altele</option>';
            
            break;

        case 'servicii de montaj':
            subcategorie.innerHTML += '<option value="instalare sisteme">instalare sisteme</option>';
            subcategorie.innerHTML += '<option value="modificare sisteme">modificare sisteme</option>';
            subcategorie.innerHTML += '<option value="Montaj_Altele">Altele</option>';
            
            break;

        case 'servicii de mentenanta':
            subcategorie.innerHTML += '<option value="verificare sisteme">verificare sisteme</option>';
            subcategorie.innerHTML += '<option value="Mentenanta_Altele">Altele</option>';
            
            break;

        // Add more cases for additional categories

        default:
            break;
    }
});

 </script>


    <label for="descriere">Descriere:</label>
    <textarea name="descriere" id="descriere" rows="4" cols="50" required></textarea>
    <br><br>
   
    <label for="cantitate">Cantitate:</label>
    <input type="number" name="cantitate" id="cantitate" required>
    <br><br>

    <label for="image">Imagini:</label>
  <input type="file" name="images[]" id="images" accept="image/*" multiple>
  <br><br>

    <input type="submit" name="submit" value="Adauga in baza de date">
  </form>
    </div>

</body>
</html>

<div class="content">
<?php
// Verifică dacă formularul a fost trimis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Preia datele din formular
    $nume = $_POST['nume'];
    $pret = $_POST['pret'];
    $categorie = $_POST['categorie'];
    $subcategorie = $_POST['subcategorie'];
    $descriere = $_POST['descriere'];
    $cantitate = $_POST['cantitate'];


    // Conectarea la baza de date
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $dbname = "solarquery";

    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    // Verifică conexiunea
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepară instrucțiunea SQL pentru inserarea produsului în baza de date folosind parametrii marcați cu semnul întrebării (?) 
    // Parametrii vor fi înlocuiți cu valorile reale folosind metoda bind_param()
    // Tipul parametrilor: s = string, i = integer, d = double, b = blob
    // Tipul și ordinea parametrilor trebuie să corespundă cu tipul și ordinea coloanelor din tabelul bazei de date
    
    $stmt = $conn->prepare("INSERT INTO tbl_produse (nume_produs,descriere_produs, pret_produs, categorie, subcategorie, id_firma, cantitate) VALUES (?, ?, ?, ?,?, ?, ?)");
    if ($stmt === false) {
        die("Error preparing SQL statement: " . $conn->error);
    }
    $stmt->bind_param("ssissis", $nume, $descriere , $pret, $categorie, $subcategorie, $firma_id, $cantitate);

   
         // Execută instrucțiunea SQL
    if ($stmt->execute()) {
        $product_id = $stmt->insert_id;
        
         // Încarcă imaginile
         $image_count = count($_FILES['images']['name']);
         for ($i = 0; $i < $image_count; $i++) {
             $image_name = uniqid() . "_" . $_FILES['images']['name'][$i]; // Generează un nume unic pentru imagine
             $image_tmp = $_FILES['images']['tmp_name'][$i]; // Nume temporar al imaginii
             $image_size = $_FILES['images']['size'][$i]; // Dimensiunea imaginii
             
             // Verifică dacă există o imagine
             if (!empty($image_name) && $image_size > 0) { 
                 $image_data = file_get_contents($image_tmp);
                 
                 // Salvează imaginea în directorul corespunzător
                 $image_path = "../IMAGES/products/" . $image_name;
                 file_put_contents($image_path, $image_data);
                 
                 // Inserează în baza de date numele și calea imaginii
                 $insert_image_stmt = $conn->prepare("INSERT INTO tbl_imagini (nume_imagine,id_produs) VALUES (?, ?)");
                 if ($insert_image_stmt === false) {
                     die("Error preparing SQL statement: " . $conn->error);
                 }
                $insert_image_stmt->bind_param("si", $image_name,$product_id);
                $insert_image_stmt->execute();
                $insert_image_stmt->close();
            }
        }
        echo "Produsul a fost adăugat cu succes.";
    } else {
        echo "Eroare la adăugarea produsului: " . $conn->error;
    }

    // Închide conexiunea la baza de date
    $stmt->close();
    $conn->close();
}
?>
</div>

</body>
</html>


