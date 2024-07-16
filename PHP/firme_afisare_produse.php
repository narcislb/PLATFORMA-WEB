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



    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
    $order = isset($_GET['order']) && in_array($_GET['order'], ['asc', 'desc']) ? $_GET['order'] : 'asc';
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

<body>
  

  <div class="content">

  <form action="firme_afisare_produse.php" method="GET">
    <input type="text" id="searchProduct" name="search" placeholder="Cauta produs"oninput="fetchProducts(this.value)">
    <div id="suggestions" style="border:1px solid #ddd; max-height:200px; overflow-y:auto;"></div>
    <input type="hidden" name="sort" value="<?= $sort ?>">
    <input type="hidden" name="order" value="<?= $order ?>">
    <input type="submit" value="Search">
</form>



<?php


  if (isset($_GET['query'])) {
    $query = $_GET['query'];

    $sql = "SELECT id, nume_produs FROM tbl_produse WHERE nume_produs LIKE ? AND id_firma = ? LIMIT 5";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $query . "%";
    $stmt->bind_param("si", $searchTerm, $id_firma);
    $stmt->execute();

    $result = $stmt->get_result();
    $suggestions = [];
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row;
    }

    echo json_encode($suggestions);
    exit;  // termină execuția scriptului
}



?>



<script>
 function fetchProducts(query) {
    let firmaId = <?php echo $id_firma; ?>;

    if(query.length < 3) {  // nu se face cautarea daca inputul este mai mic de 3 caractere
        document.getElementById('suggestions').innerHTML = ''; // resetează lista de sugestii
        return;
    }

    fetch(`fetch_products.php?query=${query}`)

    
    .then(response => response.json())
    .then(data => {
        let productsHTML = '';
        data.forEach(product => {
            productsHTML += `<div onclick="selectProduct('${product.nume_produs}', ${product.id})">${product.nume_produs}</div>`;
        });
        document.getElementById('suggestions').innerHTML = productsHTML;
    });
}





function selectProduct(productName, productId) {
    document.getElementById('searchProduct').value = productName;
    document.getElementById('suggestions').innerHTML = '';  // resetează lista de sugestii
    // 
}


</script>

    <?php
    
 
   // $conn->close();
    ?>
  </div>







<?php
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$order = isset($_GET['order']) && in_array($_GET['order'], ['asc', 'desc']) ? $_GET['order'] : 'asc';

  $items_per_page = 20;
$page_number = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page_number - 1) * $items_per_page;

$sql = "SELECT id, nume_produs, descriere_produs, pret_produs, cantitate,categorie FROM tbl_produse WHERE id_firma = ? ORDER BY $sort $order LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $id_firma, $items_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);


$search_term = isset($_GET['search']) ? $_GET['search'] : '';

// pregătește o instrucțiune SELECT care va fi executată mai târziu
$sql = "SELECT id, nume_produs, descriere_produs, pret_produs, cantitate ,categorie
        FROM tbl_produse 
        WHERE id_firma = ?";

//
if ($search_term) {
    $sql .= " AND nume_produs LIKE ?";
}

$sql .= " ORDER BY $sort $order LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);

// Adaugă un parametru suplimentar dacă este furnizat un termen de căutare
if ($search_term) {
    $search_term = '%' . $search_term . '%';  // Surround the search term with % for the LIKE clause
    $stmt->bind_param("ssii", $id_firma, $search_term, $items_per_page, $offset);
} else {
    $stmt->bind_param("iii", $id_firma, $items_per_page, $offset);
}

$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
















?>




   
<table style="border: 1px solid; margin-left: 250px;">


<thead>
    <tr>
        <th><a href="?sort=nume_produs&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>">Nume Produs</a></th>
        <th><a href="?sort=nume_produs&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>">Categorie</a></th>

        <th><a href="?sort=descriere_produs&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>">Descriere</a></th>
        <th><a href="?sort=pret_produs&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>">Pret</a></th>
        <th><a href="?sort=cantitate&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>">Cantitate</a></th>
        <th>Actiune</th>
    </tr>
</thead>
<tbody>
        <?php foreach ($products as $product): ?>
        <tr>
            <td contenteditable="true" data-id="<?= $product['id'] ?>" data-column="nume_produs"><?= $product['nume_produs'] ?></td>
            <td contenteditable="true" data-id="<?= $product['id'] ?>" data-column="categorie"><?= $product['categorie'] ?></td>

            <td contenteditable="true" data-id="<?= $product['id'] ?>" data-column="descriere_produs"><?= $product['descriere_produs'] ?></td>
            <td contenteditable="true" data-id="<?= $product['id'] ?>" data-column="pret_produs"><?= $product['pret_produs'] ?></td>
            <td contenteditable="true" data-id="<?= $product['id'] ?>" data-column="cantitate"><?= $product['cantitate'] ?></td>
            <td><button onclick="updateProduct(<?= $product['id'] ?>)">Update</button></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>


<?php
$total_items = $conn->query("SELECT COUNT(*) as total FROM tbl_produse WHERE id_firma = $id_firma")->fetch_assoc()['total'];
$total_pages = ceil($total_items / $items_per_page);

echo "<div class='pagination'>";
for ($i = 1; $i <= $total_pages; $i++) {
    if ($i == $page_number) {
        echo "<span class='active'>$i</span>";
    } else {
        echo "<a href='?page=$i&sort=$sort&order=$order'>$i</a> ";
    }
}
echo "</div>";

?>

<!-- actualizare produs -->
<script>

function updateProduct(productId) {
    let rows = document.querySelectorAll('table tbody tr');
    let productRow;
    rows.forEach(row => {
        if (row.querySelector('button').getAttribute('onclick') === `updateProduct(${productId})`) {
            productRow = row;
        }
    });

    let updatedData = {
        id: productId,
        nume_produs: productRow.querySelector('[data-column="nume_produs"]').innerText,
        descriere_produs: productRow.querySelector('[data-column="descriere_produs"]').innerText,
        pret_produs: productRow.querySelector('[data-column="pret_produs"]').innerText,
        cantitate: productRow.querySelector('[data-column="cantitate"]').innerText
    };

    fetch('update_product.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(updatedData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Product updated successfully');
        } else {
            alert('Error updating product');
        }
    });
}

</script>


</body>
</html>