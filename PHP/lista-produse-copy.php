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

// Bind the parameter to the prepared statement
$stmt->bind_param("s", $subcategorie);


$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error executing query: " . $conn->error);
}

?>


<script>
let products = <?php echo json_encode($result->fetch_all(MYSQLI_ASSOC)); ?>;



</script>




<script>
let filteredProducts = [];
let currentPage = 1;
const productsPerPage = 10;

function filterAndSortProducts() {
    let priceFilter = document.getElementById('pret').value;
    let subcategory = "<?php echo $_GET['subcategorie']; ?>"; // Get this from your PHP.

    filteredProducts = products.filter(product => {
        if (priceFilter === '0-50') {
            return product.pret_produs >= 0 && product.pret_produs <= 50;
        } else if (priceFilter === '51-100') {
            return product.pret_produs >= 51 && product.pret_produs <= 100;
        } else {
            return true; // No filter.
        }
    });

    // Sort if necessary. You can use `filteredProducts.sort(...)`

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
                <p>Descriere: ${product.descriere_produs}</p>
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
        

        <!-- Filtering -->
        <form onsubmit="event.preventDefault(); filterAndSortProducts();">
    <input type="hidden" name="subcategorie" value="<?php echo $_GET['subcategorie']; ?>">
    <label for="pret">Filtrare după preț:</label>
    <select name="pret" id="pret">
        <option value="">Toate</option>
        <option value="0-50">0 - 50</option>
        <option value="51-100">51 - 100</option>
    </select>
    <button type="button" onclick="filterAndSortProducts()">Filtrare</button>
</form>



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
</script>

</body>
</html>
