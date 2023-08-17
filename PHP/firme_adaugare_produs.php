



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

<!-- Navbar -->
<div class="navbar">
  <h1>My Page</h1>
</div>

<!-- Sidebar -->
<div class="sidebar">
<a href="firme-dashboard.php?user_id=<?php echo $firma_id; ?>" >
    <h1>Dashboard</h1>
  </a>
    <ul>
      <li><a href="firme_afisare_comenzi.php">Comenzi</a></li>
       <li><a href="adaugare_adresa_client.php">Adrese si date personale</a></li>
       <li><a href="firme_adaugare_produs.php">Adaugare produse</a></li>
       <li><a href="firme-produse.php">Produse si servicii</a></li>
       <li><a href="logout.php">Logout</a></li>
       
    </ul>
  </div>
<body>
  



<div class="content">
  <h1>Adăugare produs</h1>

  
  <form method="post" enctype="multipart/form-data">
    <label for="nume">Nume:</label>
    <input type="text" name="nume" id="nume" required>
    <br><br>

    <label for="pret">Preț:</label>
    <input type="number" name="pret" id="pret" required>
    <br><br>

    <label for="categorie">Categorie:</label>
    <select name="categorie" id="categorie" required>
      <option value="">Selectați o categorie</option>
      <option value="categoria1">Invertoare</option>
      <option value="categoria2">Categoria 2</option>
      <option value="categoria3">Categoria 2</option>
      <!-- Adaug opțiunile pentru categorii aici -->
    </select>
    <br><br>

    <label for="subcategorie">Subcategorie:</label>
    <select name="subcategorie" id="subcategorie" required>
      <!-- The subcategories will be populated based on the category chosen -->
    </select>
    <br><br>

    <script>
    document.getElementById("categorie").addEventListener("change", function() {
    let subcategorie = document.getElementById("subcategorie");
    
    // First, clear the previous subcategories
    subcategorie.innerHTML = '<option value="">Selectați o subcategorie</option>';

    // Based on the category, populate the subcategories
    switch(this.value) {
        case 'categoria1':
            subcategorie.innerHTML += '<option value="monofazate">monofazate</option>';
            break;

        case 'categoria2':
            subcategorie.innerHTML += '<option value="subcategoria3">Subcategoria 3</option>';
            subcategorie.innerHTML += '<option value="subcategoria4">Subcategoria 4</option>';
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

    <input type="submit" name="submit" value="Adăugare produs">
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


