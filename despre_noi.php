<?php
session_start(); // Start the session
?>

<!DOCTYPE html>
<html>
<head>
  <title>My Page</title>
  <link rel="stylesheet" type="text/css" href="../CSS/despre_noi.css">
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
    <section class="mission-statement">
    <h2>MISIUNEA NOASTRĂ</h2>
    <h3>Platforma web pentru facilitarea achizitiei de sisteme fotovoltaice</h3>
    <p>Unind Clienții și Furnizorii în Lumea Energiei Regenerabile.</p>
    <p>Încurajăm Încrederea, Sustenabilitatea și Colaborarea.</p>
    <p>Registrul Furnizorilor Fotovoltaici este o platformă ce găzduiește recenzii,produse,servicii de instalare si informații relevante despre furnizorii de sisteme fotovoltaice. Aceasta a fost fondată cu viziunea de a deveni un instrument independent și de încredere pentru consumatorii interesați de energia solară. Pentru furnizori, platforma oferă o metodă transparentă și eficientă de promovare în mediul online, facilitând astfel adoptarea pe scară largă a energiei regenerabile.</p>
</section>

<section class="features">
    <div class="feature-item">Citește Recenzii</div>
    <div class="feature-item">Scrie Recenzii</div>
    <div class="feature-item">Adaugă Firme</div>
    <div class="feature-item">Contactează Furnizorii</div>
</section>















    <script>
    function fetchResults() {
        let query = document.getElementById('search-box').value;
     // verificăm dacă query-ul nu este gol
     if (query.trim() === '') {
        document.getElementById('search-results-container').innerHTML = ''; // curata containerul
        return;
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


<footer>


<a href="https://ec.europa.eu/consumers/odr/main/index.cfm?event=main.home2.show&lng=RO">
        <img src="../IMAGES/extra/anpc-sal.webp" alt="Image Description"  />
    </a >

        <p>&copy;  2023 Platforma SolarQuery</p>
    </footer>