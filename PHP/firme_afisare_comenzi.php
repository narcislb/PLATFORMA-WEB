<?php
session_start(); // Start the session
//verifică dacă utilizatorul este deja autentificat si daca este tip firma
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
  <link rel="stylesheet" type="text/css" href="../CSS/afisare_comenzi.css">
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
     // Dacă nu există niciun termen de căutare, nu efectuați nicio solicitare AJAX
     if (query.trim() === '') {
        document.getElementById('search-results-container').innerHTML = ''; // Golește containerul de rezultate
        return; // Terminați execuția funcției
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
       <li><a href="firma_afisare_recenzii.php">Recenzii</a></li>
       <li><a href="../CHAT/messenger.php">Messenger</a></li>
       <li><a href="logout.php">Logout</a></li>
       
    </ul>
  </div>


    <!-- afiseaza comenzile           -->
  

    <h2 style='margin-left: 300px; margin-top: 95px;'>Comenzile primite:</h2>

    
      <?php
   // ordine de sortare default
   $sortDirection = 'asc';
   $currentSort = '';

   // daca o directie de sortare este activa se va folosi iar daca nu 
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
   $idclient = null; // Initialize the variable outside the loop
?>



<!-- TABEL PRINCIPAL AFISARE COMENZI -->

<table style="margin-left: 300px;margin-top: 95px;">
    <thead>
        <tr>
            <th ><a href="?sort=id_comanda&direction=<?php echo ($currentSort === 'id_comanda' && $sortDirection === 'asc') ? 'desc' : 'asc'; ?>">ID comanda</a></th>
            <th ><a href="?sort=data_comanda&direction=<?php echo ($currentSort === 'data_comanda' && $sortDirection === 'asc') ? 'desc' : 'asc'; ?>">Data comanda</a></th>
            <th ><a href="?sort=id_adresa_livrare&direction=<?php echo ($currentSort === 'id_adresa_livrare' && $sortDirection === 'asc') ? 'desc' : 'asc'; ?>">Id adresa livrare</a></th>

            <th ><a href="?sort=tip_facturare&direction=<?php echo ($currentSort === 'tip_facturare' && $sortDirection === 'asc') ? 'desc' : 'asc'; ?>">Tip facturare</a></th>
            <th ><a href="?sort=status_comanda&direction=<?php echo ($currentSort === 'status_comanda' && $sortDirection === 'asc') ? 'desc' : 'asc'; ?>">Status comanda</a></th>
        </tr>
    </thead>
    <tbody>
   




    <?php
    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id_input']) && isset($_POST['new_status'])) {
    $order_id_input = $_POST['order_id_input'];
    $new_status = $_POST['new_status'];

    $updateSql = "UPDATE tbl_comenzi SET status_comanda = ? WHERE id_comanda = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("si", $new_status, $order_id_input); 
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $feedback = "Status actualizat cu succes!";
    } else {
        $feedback = "Eroare la actualizare.";
    }
    $stmt->close();


    echo '<script type="text/javascript">
           window.location = "firme_afisare_comenzi.php"
      </script>';
    exit;
}

?>
   
    
<?php





//acest script afiseaza comenzile 

   if ($result->num_rows > 0) {
       while($row = $result->fetch_assoc()) {
           echo "<tr>";
           
           echo "<td><a href='?order_id=" . $row["id_comanda"] . "'>" . $row["id_comanda"] . "</a></td>";
           

           echo "<td>" . $row["data_comanda"] . "</td>";
        

           echo "<td><a href='?show_address=" . $row["id_adresa_livrare"] . "'>" . $row["id_adresa_livrare"] . "</a></td>";

            // determina tipul de facturare si afiseaza link catre adresa corespunzatoare
    if($row["tip_facturare"] == "persoana_fizica") {
        echo "<td><a href='?show_address=" . $row["id_adresa_livrare"] . "'>persoana_fizica</a></td>";

    } else if($row["tip_facturare"] == "persoana_juridica") {
        echo "<td><a href='?show_juridica_address=" . $row["id_adresa_livrare"] . "'>persoana_juridica</a></td>";
        $idclient=$row["id_client"];
    } 
           
        echo "<td>";
        echo "<form method='post' class='status-form'>";
        echo "<input type='hidden' name='order_id_input' value='" . $row["id_comanda"] . "'>";
        echo "<select name='new_status'>";
        echo "<option value='procesata' " . ($row["status_comanda"] === "procesata" ? "selected" : "") . ">procesata</option>";
        echo "<option value='In progres' " . ($row["status_comanda"] === "In progres" ? "selected" : "") . ">In progres</option>";
        // ... more status options ...
        echo "</select>";
        echo "<input type='submit' value='Modifica Status'>";
        echo "</form>";
        echo "</td>";
    

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






  // 
// tabel cu produsele comenzii
// Dacă există produse pentru comanda selectată, afisare într-un tabel

   if (isset($productsResult) && $productsResult->num_rows > 0) {
    
    echo "<h3>Products for Order ID: " . $orderId . "</h3>";
    echo "<table style='margin-left: 300px; margin-top: 95px;'>";
                
    echo "<thead>";
    echo "<tr><th>Id_produs</th><th>Nume produs</th><th>cantitate</th>...</tr>";  
    echo "</thead>";
    echo "<tbody>";
    while($product = $productsResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $product["id_produs"] . "</td>";
        echo "<td>" . htmlspecialchars($product["nume_produs"], ENT_QUOTES, 'UTF-8') . "</td>";  // afiseaza numele produsului
        echo "<td>" . $product["cantitate"] . "</td>";
        // ... alte coloane din tabelul tbl_produse_comanda
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
}









// echo "</table>";

// // formular pentru modificare status comanda
// echo "<h3 style='margin-left: 300px; margin-top: 95px;'>Modificare Status Comanda </h3>";
// echo "<form method='post'  style='margin-left: 300px; margin-top: 95px;'>";
// echo "<label for='order_id_input'>ID Comanda:</label>";
// echo "<input type='text' id='order_id_input' name='order_id_input' required>";
// echo "<br><br>";

// echo "<label for='new_status'>Status Nou:</label>";
// echo "<select name='new_status' id='new_status' required>";
// echo "<option value=''>Selectați un status</option>";
// echo "<option value='procesata'>procesata</option>";
// echo "<option value='In progres'>In progres</option>";
// echo "<option value='Expediata'>Expediata</option>";
// echo "<option value='Livrata'>Livrata</option>";
// echo "<option value='Anulata'>Anulata</option>";
// // adaugati aici si celelalte statusuri
// echo "</select>";
// echo "<br><br>";

// echo "<input type='submit' value='Modifica Status'>";
// echo "</form>";





if(isset($_GET['show_address'])) {
    $address_id = $_GET['show_address'];

    // Fetch address details
    $addressSql = "SELECT * FROM tbl_adrese WHERE id_adresa = ?";
    $stmt = $conn->prepare($addressSql);
    $stmt->bind_param("i", $address_id);
    $stmt->execute();
    $addressResult = $stmt->get_result();
    
    if ($address = $addressResult->fetch_assoc()) {
        echo "<h3>Detalii Adresa:</h3>";
        echo "<table style='margin-left: 300px; margin-top: 95px;'>";
        echo "<tr><th>Tara</th><th>Judet</th><th>Localitate</th><th>Adresa</th><th>Cod Postal</th><th>Telefon</th><th>Nume</th></tr>";
        echo "<tr>";
        echo "<td>" . $address["tara"] . "</td>";
        echo "<td>" . $address["judet"] . "</td>";
        echo "<td>" . $address["localitate"] . "</td>";
        echo "<td>" . $address["adresa"] . "</td>";
        echo "<td>" . $address["cod_postal"] . "</td>";
        echo "<td>" . $address["telefon"] . "</td>";
        echo "<td>" . $address["nume"] . "</td>";
        echo "</tr>";
        echo "</table>";
    } else {
        echo "<p style='margin-left: 300px;'>Nu există detalii pentru adresa selectată.</p>";
    }
}


if(isset($_GET['show_juridica_address'])) {
    
    
    // // First, retrieve the ID for the client based on the order ID
    // $orderSql = "SELECT id_client FROM tbl_comenzi WHERE id_comanda = ?";
    // $stmt = $conn->prepare($orderSql);
    // $stmt->bind_param("i", $order_id);
    // $stmt->execute();
    // $result = $stmt->get_result();
    // $order = $result->fetch_assoc();
    // $id_client = $order['id_client'];
    // $stmt->close();

    // acum avem id_client, trebuie sa aflam id-ul persoanei juridice
    $clientSql = "SELECT persoana_juridica_id FROM tbl_clienti WHERE id = ?";
    $stmt = $conn->prepare($clientSql);
    $stmt->bind_param("i", $idclient);
    $stmt->execute();
    $result = $stmt->get_result();
    $client = $result->fetch_assoc();
    $persoana_juridica_id = $client['persoana_juridica_id'];
    $stmt->close();

   //se afiseaza datele persoanei juridice

    $detailsSql = "SELECT nume_companie, CUI, NumarRegCom, SediuSocial
                   FROM tbl_date_persoana_juridica 
                   WHERE id = ?";
    $stmt = $conn->prepare($detailsSql);
    $stmt->bind_param("i", $persoana_juridica_id);
    $stmt->execute();
    $detailsResult = $stmt->get_result();
    
    if ($details = $detailsResult->fetch_assoc()) {
        echo "<h3> Detalii Persoana Juridica:</h3>";
        echo "<table style='margin-left: 300px; margin-top: 95px;'>";
        echo "<tr><th>Nume Companie</th><th>CUI</th><th>Numar Inregistrare</th><th>Sediu Social</th></tr>";
        echo "<tr>";
        echo "<td>" . $details["nume_companie"] . "</td>";
        echo "<td>" . $details["CUI"] . "</td>";
        echo "<td>" . $details["NumarRegCom"] . "</td>";
        echo "<td>" . $details["SediuSocial"] . "</td>";
        echo "</tr>";
        echo "</table>";
    } else {
        echo "<p>Nu există detalii pentru persoana juridica selectată.</p>";
    }
}









if (isset($feedback)) {
    echo "<p>" . $feedback . "</p>";
}
$conn->close();

    ?>
