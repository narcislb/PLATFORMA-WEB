

<?php

session_start();


// verifica daca id-ul comenzii a fost trimis prin query string
if (!isset($_GET['ids']) || empty($_GET['ids'])) {
    die('Order IDs not provided.');
}

// transforma stringul de id-uri intr-un array
$order_ids = explode(',', $_GET['ids']);



?>


  <!DOCTYPE html>
  <html>
      <head>
          
          <link href="../CSS/plasare-comanda.css" rel="stylesheet" type="text/css">
          <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
          
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>Confirmare Comanda</title>
    
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
  
  <!-- script search box -->
      <script>
      function fetchResults() {
          let query = document.getElementById('search-box').value;
       // verificam daca inputul de cautare nu este gol
       if (query.trim() === '') {
          document.getElementById('search-results-container').innerHTML = ''; // Resetam containerul
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

<?php foreach ($order_ids as $order_id): ?>
        <p>Mulțumim pentru comanda cu numărul: <?= htmlspecialchars($order_id) ?>!</p>
    <?php endforeach; ?>

