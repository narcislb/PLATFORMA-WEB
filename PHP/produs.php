<?php
// pornește o sesiune
session_start();

// conectare la baza de date
$host = 'localhost';
$dbname = 'solarquery';
$user = 'root';
$db_password = '';

$db = new PDO("mysql:host=$host;dbname=$dbname", $user, $db_password);

// verifică dacă formularul a fost trimis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // preia datele din formular
    $id_produs = $_POST['id_produs'];
    $cantitate = $_POST['cantitate'];

    // validare formular - verifică dacă cantitatea este mai mare decât 0
    if (isset($_SESSION['cos-cumparaturi'][$id_produs])) {
        // Adăugăm cantitatea la produsul existent în coș
        $_SESSION['cos-cumparaturi'][$id_produs] += $cantitate;
        

    } else {
        // Adăugăm produsul în coș
        $_SESSION['cos-cumparaturi'][$id_produs] = $cantitate;
    }

    // redirecționează către pagina de produs
    header('Location: produs.php?id=' . $id_produs);
    exit;
}

// verifică dacă există un id de produs

if (isset($_GET['id'])) {
    // Fetch the product details
    $stmt_product = $db->prepare('
    SELECT tbl_produse.*, tbl_firme.nume_firma 
    FROM tbl_produse 
    LEFT JOIN tbl_firme ON tbl_produse.id_firma = tbl_firme.id 
    WHERE tbl_produse.id = ?
');
    $stmt_product->execute([$_GET['id']]);
    $product = $stmt_product->fetch(PDO::FETCH_ASSOC);
    
    // verifică dacă produsul există
    if (!$product) {
        exit('Produsul nu exista!');
    }
    
    // interogare pentru a prelua imaginile produsului
    $stmt_images = $db->prepare('SELECT nume_imagine FROM tbl_imagini WHERE id_produs = ?');
    $stmt_images->execute([$_GET['id']]);
    $images = $stmt_images->fetchAll(PDO::FETCH_ASSOC);
    
} else {
    // Nu există id, redirectionam către pagina de produse
    exit('Produsul nu exista!');
}

// acum $product va conține toate detaliile produsului
// si $images va conține toate imaginile produsului

?>
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






<div class="product content-wrapper"  style="text-align: center;">


      <div class="slider" style="text-align: center;">  
    <?php if (!empty($images)): ?>
        <?php foreach ($images as $image): ?>
            <div class="slide">
                <img src="../IMAGES/products/<?= $image['nume_imagine'] ?>" width="500" height="500" alt="<?= $product['nume_produs'] ?>">
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <button class="prev" onclick="changeSlide(-1)">❮</button>
    <button class="next" onclick="changeSlide(1)">❯</button>
</div>


    <!-- <img src="../IMAGES/products/<?=$product['imagine']?>" width="200" height="200" alt="<?=$product['nume_produs']?>"> -->
    <div>
        <h1 class="name"><?=$product['nume_produs']?></h1>
        <span class="price">
        <h2>Pret:</h2>
            <?=$product['pret_produs']?><span> Lei</span>
            
        </span>
        <h2>Vandut de :</h2>
            <span><?=$product['nume_firma']?></span>

        <h2>În stoc:</h2>
        <span><?=$product['cantitate']?></span><span> de unități</span>


        <!-- afisare descriere -->
        <div class="description">
    <h2>Descriere:</h2>
    <?= nl2br($product['descriere_produs']) ?>
</div>


        <form method="post"                                                                 style="display: flex; flex-direction: column; align-items: center;">
    <label for="cantitate" style="margin-bottom: 10px;">Cantitate:</label>
    <input type="number" name="cantitate" value="1" min="1" max="<?=$product['cantitate']?>" placeholder="Cantitate" required style="padding: 5px; border: 1px solid #ccc; border-radius: 5px; margin-bottom: 10px;">
    <input type="hidden" name="id_produs" value="<?=$product['id']?>">
    <input type="submit" value="Adauga in cos"            style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer;">
        </form>
       
    </div>

    

<!-- slider script -->
<script>

let slideIndex = 1;
showSlide(slideIndex);

function changeSlide(n) {
    showSlide(slideIndex += n);
}

function showSlide(n) {
    let slides = document.querySelectorAll(".slide");
    if (n > slides.length) { slideIndex = 1 }
    if (n < 1) { slideIndex = slides.length }
    for (let i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    slides[slideIndex - 1].style.display = "block";
}

</script>


</div>
</html>

