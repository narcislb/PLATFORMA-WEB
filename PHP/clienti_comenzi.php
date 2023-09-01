<?php

session_start(); // pornește o sesiune

// Check if user is logged in as client
if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'client') {
  header('Location: clienti-login-form.php');
  exit();
}


// preia datele despre utilizatorul curent
$username = $_SESSION['username'];
$user_id= $_SESSION['user_id'];


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
  <title>My Page</title>
  <link rel="stylesheet" type="text/css" href="../CSS/clienti_comenzi.css">
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
                
                <div class="cart-icon-container">
                        <a href="cos-cumparaturi.php">
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

<!-- Sidebar -->
<div class="sidebar">
<a href="profil_client.php" >
    <h1>Dashboard</h1>
  </a>
    <ul>
      <li><a href="clienti_comenzi.php">Comenzi</a></li>
       <li><a href="adaugare_adresa_client.php">Adrese si date personale</a></li>
       <li><a href="../CHAT/messenger.php">Messenger</a></li>
       <li><a href="logout.php">Logout</a></li>
       
    </ul>
  </div>





    <!-- afiseaza comenzile           -->
  

    <h2 style='margin-left: 300px; margin-top: 95px;'>Comenzile mele:</h2>

    
      <?php
   // Default sort order
   $sortDirection = 'asc';
   $currentSort = '';

   // If a sort direction and column are specified, use them. Otherwise, use defaults.
   if (isset($_GET['direction']) && in_array($_GET['direction'], ['asc', 'desc'])) {
       $sortDirection = $_GET['direction'];
   }

   if (isset($_GET['sort'])) {
       $currentSort = $_GET['sort'];
   }

   $sql = "SELECT * FROM tbl_comenzi WHERE id_client = '$user_id'";

   if ($currentSort) {
       $sql .= " ORDER BY $currentSort $sortDirection";
   }

   $result = $conn->query($sql);
?>

<table style="margin-left: 300px;margin-top: 95px;">
    <thead>
        <tr>
            <th><a href="?sort=id_comanda&direction=<?php echo ($currentSort === 'id_comanda' && $sortDirection === 'asc') ? 'desc' : 'asc'; ?>">ID comanda</a></th>
            <th><a href="?sort=data_comanda&direction=<?php echo ($currentSort === 'data_comanda' && $sortDirection === 'asc') ? 'desc' : 'asc'; ?>">Data comanda</a></th>
            <th><a href="?sort=total_de_plata&direction=<?php echo ($currentSort === 'total_de_plata' && $sortDirection === 'asc') ? 'desc' : 'asc'; ?>">Total de plata (LEI)</a></th>
            <th><a href="?sort=status_comanda&direction=<?php echo ($currentSort === 'status_comanda' && $sortDirection === 'asc') ? 'desc' : 'asc'; ?>">Status comanda</a></th>
        </tr>
    </thead>
    <tbody>
<?php
   
   if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td><a href='?order_id=" . $row["id_comanda"] . "'>" . $row["id_comanda"] . "</a></td>";

        echo "<td>" . $row["data_comanda"] . "</td>";
        echo "<td>" . $row["total_de_plata"] . "</td>";
        echo "<td>" . $row["status_comanda"] . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>Nu există comenzi.</td></tr>";
}

?>
   </tbody>
</table>

<?php

   if (isset($_GET['order_id'])) {
    $orderId = $_GET['order_id'];
// Această interogare preia detaliile comenzii și numele produselor asociate pentru un ID de comandă specific.
// Se realizează o legătură între tabelul produselor din comandă și tabelul principal al produselor pe baza ID-ului produsului.
$productsSql = "
    SELECT tbl_produse_comanda.*, tbl_produse.nume_produs, tbl_firme.nume_firma
    FROM tbl_produse_comanda 
    JOIN tbl_produse ON tbl_produse_comanda.id_produs = tbl_produse.id 
    JOIN tbl_firme ON tbl_produse.id_firma = tbl_firme.id
    WHERE tbl_produse_comanda.id_comanda = ?";


    $stmt = $conn->prepare($productsSql);
    $stmt->bind_param("i", $orderId);  // daca este string s, daca este int i
    $stmt->execute();
    $productsResult = $stmt->get_result();
}



   

   if (isset($productsResult) && $productsResult->num_rows > 0) {
    
    echo "<h3 style='margin-left: 300px; margin-top: 95px;'>Produsele pentru comanda cu id ul: " . $orderId . "</h3>";
    echo "<table style='margin-left: 300px; margin-top: 95px;'>";
                
    echo "<thead>";
    echo "<tr><th>Id_produs</th><th>Nume furnizor</th><th>Nume produs</th><th>cantitate</th>...</tr>";  // Adjust columns to fit your database schema
    echo "</thead>";
    echo "<tbody>";
    while($product = $productsResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $product["id_produs"] . "</td>";
        echo "<td>" . htmlspecialchars($product["nume_firma"], ENT_QUOTES, 'UTF-8') . "</td>";  // afiseaza numele furnizorului
        echo "<td>" . htmlspecialchars($product["nume_produs"], ENT_QUOTES, 'UTF-8') . "</td>";  // Display the product name
        echo "<td>" . $product["cantitate"] . "</td>";
        // ... alte coloane din tabelul tbl_produse_comanda
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
}







?>


  </body>
  </html>
