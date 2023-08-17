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

 ?>
<?php
if (isset($_GET['query'])) {
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $query = $_GET['query'];


    $sql = "SELECT id, nume_produs FROM tbl_produse WHERE nume_produs LIKE ? AND id_firma = ? LIMIT 5";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $query . "%";
    $stmt->bind_param("si", $searchTerm,$id_firma);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $suggestions = [];
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row;
    }
    
    echo json_encode($suggestions);
    exit;  // Important: Exit after sending the JSON data
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
<a href="firme-dashboard.php?user_id=<?php echo $id_firma; ?>" >
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

  <input type="text" id="searchProduct" placeholder="Cauta produs">
<div id="suggestions"></div>
<input type="number" id="cantitate" min="0" value="0">
<button onclick="actualizareCantitate()">Actualizeaza cantitatea</button>


<script>
  document.getElementById('searchProduct').addEventListener('input', function() {
    let query = this.value;
    let firmaId = <?php echo $id_firma; ?>;  

    if (query.length > 2) {
        fetch(`firme_afisare_produse.php?query=${query}&id_firma=${firmaId}`)
        .then(response => response.json())
        .then(data => {
            let suggestionHTML = '';
            data.forEach(product => {
                suggestionHTML += `<div onclick="selectProduct('${product.nume_produs}', ${product.id})">${product.nume_produs}</div>`;
            });
            document.getElementById('suggestions').innerHTML = suggestionHTML;
        });
    }
});

function selectProduct(nume_produs, id) {
    document.getElementById('searchProduct').value = nume_produs;
    document.getElementById('suggestions').innerHTML = '';
    // Store the selected product ID for later usage
    document.getElementById('searchProduct').dataset.selectedId = id;

     // preia cantitatea produsului din baza de date 
     fetch(`extrage_cantitatea.php?productId=${id}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
    document.getElementById('cantitate').value = data.quantity;
} else {
            alert('Eroare la preluarea cantitatii');
        }
    });

}


function actualizareCantitate() {
    let id = document.getElementById('searchProduct').dataset.selectedId;
    let quantity = document.getElementById('cantitate').value;

    fetch('actualizare_cantitate.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            productId: id,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Cantitate actualizata cu succes');
        } else {
            alert('Eroare la actualizarea cantitatii  ');
        }
    });
}

</script>

    <?php
    
 
   // $conn->close();
    ?>
  </div>
</body>
</html>