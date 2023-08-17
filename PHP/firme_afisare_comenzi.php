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

    <!-- afiseaza comenzile           -->
  

    <h2>Comenzile primite:</h2>

    
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

   $sql = "SELECT * FROM tbl_comenzi WHERE id_firma = '$firma_id'";

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
            <th><a href="?sort=id_adresa_livrare&direction=<?php echo ($currentSort === 'id_adresa_livrare' && $sortDirection === 'asc') ? 'desc' : 'asc'; ?>">Id adresa livrare</a></th>
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
           echo "<td>" . $row["id_adresa_livrare"] . "</td>";
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
    SELECT tbl_produse_comanda.*, tbl_produse.nume_produs
    FROM tbl_produse_comanda 
    JOIN tbl_produse ON tbl_produse_comanda.id_produs = tbl_produse.id 
    WHERE tbl_produse_comanda.id_comanda = ?";

    $stmt = $conn->prepare($productsSql);
    $stmt->bind_param("i", $orderId);  // daca este string s, daca este int i
    $stmt->execute();
    $productsResult = $stmt->get_result();
}



   

   if (isset($productsResult) && $productsResult->num_rows > 0) {
    
    echo "<h3>Products for Order ID: " . $orderId . "</h3>";
    echo "<table style='margin-left: 300px; margin-top: 95px;'>";
                
    echo "<thead>";
    echo "<tr><th>Id_produs</th><th>Nume produs</th><th>cantitate</th>...</tr>";  // Adjust columns to fit your database schema
    echo "</thead>";
    echo "<tbody>";
    while($product = $productsResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $product["id_produs"] . "</td>";
        echo "<td>" . htmlspecialchars($product["nume_produs"], ENT_QUOTES, 'UTF-8') . "</td>";  // Display the product name
        echo "<td>" . $product["cantitate"] . "</td>";
        // ... alte coloane din tabelul tbl_produse_comanda
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id_input']) && isset($_POST['new_status'])) {
    $order_id_input = $_POST['order_id_input'];
    $new_status = $_POST['new_status'];

    $updateSql = "UPDATE tbl_comenzi SET status_comanda = ? WHERE id_comanda = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("si", $new_status, $order_id_input); 
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $feedback = "Status updated successfully!";
    } else {
        $feedback = "Error updating status or no changes made.";
    }
    $stmt->close();
}

echo "</table>";

// formular pentru modificare status comanda
echo "<h3 style='margin-left: 300px; margin-top: 95px;'>Modificare Status Comanda </h3>";
echo "<form method='post'  style='margin-left: 300px; margin-top: 95px;'>";
echo "<label for='order_id_input'>ID Comanda:</label>";
echo "<input type='text' id='order_id_input' name='order_id_input' required>";
echo "<br><br>";

echo "<label for='new_status'>Status Nou:</label>";
echo "<select name='new_status' id='new_status' required>";
echo "<option value=''>Selectați un status</option>";
echo "<option value='procesata'>procesata</option>";
echo "<option value='In progres'>In progres</option>";
echo "<option value='Expediata'>Expediata</option>";
echo "<option value='Livrata'>Livrata</option>";
echo "<option value='Anulata'>Anulata</option>";
// adaugati aici si celelalte statusuri
echo "</select>";
echo "<br><br>";

echo "<input type='submit' value='Modifica Status'>";
echo "</form>";



if (isset($feedback)) {
    echo "<p>" . $feedback . "</p>";
}
$conn->close();

    ?>



<!DOCTYPE html>
<html>
<head>
  <title>My Page</title>
  <link rel="stylesheet" type="text/css" href="../CSS/clienti_comenzi.css">
</head>
