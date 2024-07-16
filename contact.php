<?php

session_start();

?>



<!DOCTYPE html>
<html>
<head>
    <title>Platforma SolarQuery</title>
    <link rel="stylesheet" type="text/css" href='../CSS/contact.css'>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
   
    
</head>

<body>
<div class="content-wrapper">
    <header>
    <h1><a href="../index.php">SolarQuery</a></h1>
        <nav>
            <ul>
                <li><a href="index.php">Acasă</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Shop</a>
                    <div class="dropdown-content">
                        <a href="HTML/produse.html">Produse</a>
                        <a href="HTML/servicii.html">Servicii</a>
                    </div>
                </li>
                <li><a href="despre_noi.php">Despre noi</a></li>
                <li><a href="contact.php">Contact</a></li>
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


    

<main>


<!-- Inside your contact.php page, where you want to insert the section -->
<section class="email-assistance">
    <h2>Asistență prin email</h2>
    <p>În funcție de întrebarea ta, poți utiliza următoarele adrese de e-mail:</p>

    <div class="email-category">
        <h3>Informații</h3>
        <p>Informații despre serviiciile oferite, sugestii și comentarii și informații generale – GDPR</p>
        <p>Email: <a href="mailto:firme@solarquery.ro">firme@solarquery.ro</a></p>
    </div>

    <div class="email-category">
        <h3>Suport</h3>
        <p>Departament pentru asistență la înregistrare, suport tehnic pentru utilizatori înregistrați</p>
        <p>Email: <a href="mailto:suport@solarquery.ro">suport@solarquery.ro</a></p>
    </div>

    <div class="email-category">
        <h3>Marketing</h3>
        <p>Promovarea serviciilor noastre și promovarea serviciilor oferite de partenerii noștri</p>
        <p>Email: <a href="mailto:marketing@solarquery.ro">marketing@solarquery.ro</a></p>
    </div>
</section>


</main>



<footer>


<a href="https://ec.europa.eu/consumers/odr/main/index.cfm?event=main.home2.show&lng=RO">
        <img src="../IMAGES/extra/anpc-sal.webp" alt="Image Description"  />
    </a >

        <p>&copy;  2023 Platforma SolarQuery</p>
    </footer>








    <script>
    function fetchResults() {
        let query = document.getElementById('search-box').value;
     // verificăm dacă query-ul nu este gol
     if (query.trim() === '') {
        document.getElementById('search-results-container').innerHTML = ''; // eliminăm conținutul din container
        return; // ies din funcție
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



</body>
</html>

