
<?php

session_start();

?>






<!DOCTYPE html>
<html>
<head>
    <title>Servicii</title>
    <link rel="stylesheet" href="../CSS/produse.css">
   

</head>
<body>

<div class="content-wrapper">
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


    <main>
        <h2>Servicii</h2>
        <div class="row">
            <div class="column">
                <details>
                    <summary><img src="../IMAGES/servicii_consultanta.jpeg"  class="category-image">Servicii consultanta</summary>
                    <ul>
                        <!-- de aici trebuie sa facem un link catre pagina cu produsele din subcategorie -->
                         <li><a href="../PHP/lista-produse.php?subcategorie=servicii de consultanță și proiectare">servicii de consultanță și proiectare</a></li>
                         <li><a href="../PHP/lista-produse.php?subcategorie=documentatie prosumator">documentatie prosumator</a></li>
                         <li><a href="../PHP/lista-produse.php?subcategorie=Consultanta_Altele">Altele</a></li>

                        
                    </ul>
                </details>
            </div>
            <div class="column">
                <details>
                    <summary><img src="../IMAGES/servicii_montaj.jpeg" class="category-image"> Servicii de montaj</summary>
                    <ul>
                        <!-- de aici trebuie sa facem un link catre pagina cu produsele din subcategorie -->
                        <li><a href="../PHP/lista-produse.php?subcategorie=instalare sisteme">instalare sisteme</a></li>

                        <li><a href="../PHP/lista-produse.php?subcategorie=modificare sisteme">modificare sisteme</a></li>

                        <li><a href="../PHP/lista-produse.php?subcategorie=Montaj_Altele">Altele</a></li>

                    </ul>
                </details>
            </div>
            <div class="column">
                <details>
                    <summary><img src="../IMAGES/servicii_mentenanta.jpg"  class="category-image"> Servicii de mentenanta</summary>
                    <ul>
                    <li><a href="../PHP/lista-produse.php?subcategorie=verificare sisteme">verificare sisteme</a></li>

                    <li><a href="../PHP/lista-produse.php?subcategorie=Mentenanta_Altele">Altele</a></li>


                    </ul>
                </details>
            </div>
           
    </main>
</body>


<footer>


<a href="https://ec.europa.eu/consumers/odr/main/index.cfm?event=main.home2.show&lng=RO">
        <img src="../IMAGES/extra/anpc-sal.webp" alt="Image Description"  />
    </a >

        <p>&copy;  2023 Platforma SolarQuery</p>
    </footer>


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

</html>