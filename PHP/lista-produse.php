<?php
session_start();

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "solarquery";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$subcategorie = $_GET['subcategorie'];
$sql = "SELECT * FROM tbl_produse WHERE subcategorie = ?";

$stmt = $conn->prepare($sql);

// leaga parametrii
$stmt->bind_param("s", $subcategorie);


$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error executing query: " . $conn->error);
}

?>

<?php
$products = [];

while ($row = $result->fetch_assoc()) {
    // incarca imaginea produsului din baza de date
    $image_sql = "SELECT nume_imagine FROM tbl_imagini WHERE id_produs = " . $row['id'];
    $image_result = $conn->query($image_sql);
    
    if ($image_result->num_rows > 0) {
        $image_data = $image_result->fetch_assoc();
        $row['nume_imagine'] = $image_data['nume_imagine']; // adauga numele imaginii la array
    } else {
        $row['nume_imagine'] = 'default.png'; // imagine default.
    }

    $products[] = $row; // stocheaza produsele in array
}

?>


<script>
// let products =  echo json_encode($result->fetch_all(MYSQLI_ASSOC)); 

let products = <?php echo json_encode($products); ?>;




</script>




<script>
let filteredProducts = [];
let currentPage = 1;
const productsPerPage = 10;

function filterAndSortProducts() {
    let pretMin = parseFloat(document.getElementById('pret_min').value) || 0; // Default to 0 if not specified
    let pretMax = parseFloat(document.getElementById('pret_max').value) || Infinity; // Default to Infinity if not specified

    filteredProducts = products.filter(product => {
        return product.pret_produs >= pretMin && product.pret_produs <= pretMax;
    });

    // continuare filtrare
    displayProducts();
}


function displayProducts() {
    let displayStart = (currentPage - 1) * productsPerPage;
    let displayEnd = displayStart + productsPerPage;

    let productsToDisplay = filteredProducts.slice(displayStart, displayEnd);
    
    let productsDiv = document.querySelector('.lista-produse');
    productsDiv.innerHTML = '';

    productsToDisplay.forEach(product => {
        productsDiv.innerHTML += `
            <div class='produs'>
            
                <h3><a href='produs.php?id=${product.id}'>${product.nume_produs}</a></h3>
                <p>Pret: ${product.pret_produs} lei</p>
                 
                <img src='../IMAGES/products/${product.nume_imagine}' alt='Imagine produs' style='width: 300px; height: 300px;'>
            </div>
        `;
    });
}

function nextPage() {
    if (currentPage < Math.ceil(filteredProducts.length / productsPerPage)) {
        currentPage++;
        displayProducts();
    }
}

function prevPage() {
    if (currentPage > 1) {
        currentPage--;
        displayProducts();
    }
}
</script>


<!DOCTYPE html>
<html>
<head>
    <title>Lista produse</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../CSS/lista_produse_style.css">
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

<!-- script search box -->
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


<button onclick="goBack()" style="margin-top: 5px;">Mergi inapoi</button st>
        

        <!-- filtrare -->
     

<label for="pret_min">Pret minim:</label>
<input type="number" id="pret_min" name="pret_min" placeholder="Min" min="0">

<label for="pret_max">Pret maxim:</label>
<input type="number" id="pret_max" name="pret_max" placeholder="Max" min="1">

<button type="button" onclick="filterAndSortProducts()">Filtrare</button>



<!-- ordonare -->
<label for="ordine">Ordonează după:</label>
<select name="ordine" id="ordine">
    <option value="pret_asc">Preț: Ascendent</option>
    <option value="pret_desc">Preț: Descendent</option>
</select>
<button type="button" onclick="sortProducts()">Ordonează</button>


<div class="lista-produse"></div>
      







<div style="text-align: center;">
    <button onclick="prevPage()">Pagina anterioara</button>
    <button onclick="nextPage()">Urmatoarea pagina</button>
</div>
           
        </div>
    </div>
    <?php $conn->close(); ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    filterAndSortProducts();
});

</script>



<script>
    function goBack() {
        window.history.back();
    }




    function sortProducts() {
    let order = document.getElementById('ordine').value;

    if (order === 'pret_asc') {
        filteredProducts.sort((a, b) => a.pret_produs - b.pret_produs);
    } else if (order === 'pret_desc') {
        filteredProducts.sort((a, b) => b.pret_produs - a.pret_produs);
    }

    displayProducts();
}


</script>

</body>
</html>
