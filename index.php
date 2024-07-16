<?php

session_start();

?>



<!DOCTYPE html>
<html>
<head>
    <title>Platforma SolarQuery</title>
    <link rel="stylesheet" type="text/css" href='../CSS/style.css'>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
   
    
</head>

<body>
<div class="content-wrapper">
    <header>
        <h1>Platforma SolarQuery</h1>
        <nav>
            <ul>
                <li><a href="#">Acasă</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Shop</a>
                    <div class="dropdown-content">
                        <a href="php/produse.php">Produse</a>
                        <a href="php/servicii.php">Servicii</a>
                    </div>
                </li>
                <li><a href="despre_noi.php">Despre noi</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Clienti</a>
                    <div class="dropdown-content">
                        <a href="../PHP/clienti-register-form.php">Înregistrare</a>
                        <a href="PHP/clienti-login-form.php">Logare</a>
                        
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Furnizori</a>
                    <div class="dropdown-content">
                        <a href="../PHP/firme-register-form.php">Înregistrare</a>
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
<?php if(isset($_SESSION['user_id'])):  ?> 
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
        <h2>Bun venit pe platforma SolarQuery</h2>
        <p>Achiziționați sisteme fotovoltaice și găsiți furnizorii de servicii de instalare a acestora într-un singur loc.</p>
        <button>Cumpărați acum</button>
        
        

<script>
    function fetchResults() {
        let query = document.getElementById('search-box').value;
     // verificăm dacă query-ul nu este gol
     if (query.trim() === '') {
        document.getElementById('search-results-container').innerHTML = ''; // Resetăm containerul
        return; // Oprim executarea funcției
    }
        // Efectuăm un request AJAX către scriptul PHP
        fetch('../ADD_RECENZIE/cauta_firma.php?query=' + query)
        .then(response => response.json())
        .then(data => {
            let resultsContainer = document.getElementById('search-results-container');
            resultsContainer.innerHTML = ''; // Resetează containerul
    
            if (data.length > 0) { // Verificăm dacă avem rezultate
                data.forEach(firma => {
                    let firmaDiv = document.createElement('div');
                    let firmaLink = document.createElement('a');
                    firmaLink.href = '../PHP/profil_firma_copy.php?id=' + firma.id; // Adaugă link către profilul firmei
                    firmaLink.textContent = firma.nume_firma;
    
                    firmaDiv.appendChild(firmaLink);
                    resultsContainer.appendChild(firmaDiv);
                });
            } else {
                resultsContainer.innerHTML = 'Niciun rezultat găsit.';
            }
        })
        .catch(error => console.error('Error:', error));// erorile vor fi afișate în consolă
    }
    

    </script>
    
    
          
    


    </main>

    <style>
        .account-button {
    background-color: #4CAF50; /* Green background */
    border: none; /* Remove border */
    color: white; /* White text */
    padding: 12px 24px; /* Some padding */
    text-align: center; /* Center text */
    text-decoration: none; /* Remove underline */
    display: inline-block; /* Make it a block element */
    font-size: 16px; /* Increase font size */
    margin-right: 10px; /* Add some margin to the right */
  }
  
  .logout-button {
    background-color: #f44336; /* Red background */
    border: none; /* Remove border */
    color: white; /* White text */
    padding: 12px 24px; /* Some padding */
    text-align: center; /* Center text */
    text-decoration: none; /* Remove underline */
    display: inline-block; /* Make it a block element */
    font-size: 16px; /* Increase font size */
  }

  </style>



</body>



<footer>


<a href="https://ec.europa.eu/consumers/odr/main/index.cfm?event=main.home2.show&lng=RO">
        <img src="../IMAGES/extra/anpc-sal.webp" alt="Image Description"  />
    </a >

        <p>&copy;  2023 Platforma SolarQuery</p>
    </footer>

</html>

