<!DOCTYPE html>
<html>
<head>
  <title>Adăugare produs</title>
  <meta charset="utf-8">
</head>

<body>
<body>

  <div class="sidebar">
    <h1>Dashboard</h1>
    <ul>
      <li><a href="../firme-comenzi.php">Comenzi</a></li>
      <li><a href="#">Produse</a></li>
      <li><a href="firme-adaugare-produs.php">Adaugare produse</a></li>
    </ul>
  </div>
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
      <option value="categoria1">Categoria 1</option>
      <option value="categoria2">Categoria 2</option>
      <!-- Adaug opțiunile pentru categorii aici -->
    </select>
    <br><br>

    <label for="subcategorie">Subcategorie:</label>
    <select name="subcategorie" id="subcategorie" required>
      <option value="">Selectați o subcategorie</option>
      <option value="subcategorie1">Subcategoria 1</option>
      <option value="subcategorie2">Subcategoria 2</option>
      <!-- Adaug opțiunile pentru subcategorii aici -->
    </select>
    <br><br>

    <label for="descriere">Descriere:</label>
    <textarea name="descriere" id="descriere" rows="4" cols="50" required></textarea>
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
    
    $stmt = $conn->prepare("INSERT INTO tbl_produse (nume_produs,descriere_produs, pret_produs, categorie, subcategorie) VALUES (?, ?, ?, ?,?)");
    if ($stmt === false) {
        die("Error preparing SQL statement: " . $conn->error);
    }
    $stmt->bind_param("ssiss", $nume, $descriere , $pret, $categorie, $subcategorie);

   
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



<style>
    .content {
  margin-right: 200px;
  margin-left: 220px;
  padding: 20px;
  margin-top: -30px; 
}
.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: 180px;
  height: 100%;
  background-color: #f1f1f1;
  padding: 20px;
}

.sidebar h1 {
  margin-top: 0;
}

.sidebar ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

.sidebar ul li {
  margin-bottom: 10px;
}

.sidebar ul li a {
  display: block;
  padding: 10px;
  background-color: #ddd;
  color: #333;
  text-decoration: none;
}

.sidebar ul li a:hover {
  background-color: #ccc;
}

.form-group {
  margin-bottom: 20px;
}

label {
  display: inline-block;
  width: 100px;
  text-align: right;
  margin-right: 20px;
}

input[type="text"],
input[type="number"],
select,
textarea {
  display: inline-block;
  width: 200px;
  padding: 5px;
  border: 1px solid #ccc;
  border-radius: 3px;
  box-sizing: border-box;
}

textarea {
  height: 100px;
  vertical-align: top;
}
</style>
