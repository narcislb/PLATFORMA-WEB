<!DOCTYPE html>
<html>
<head>
    <title>Înregistrare Furnizor</title>
    <link rel="stylesheet" href="../CSS/register_page.css">
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


<!-- formular inregistrare -->

<div class="container">
    <h1>Înregistrare Furnizor</h1>
    <form action="../PHP/firme-register.php" method="post">
    <div class="form-group">
        <label for="nume_firma">Nume Firmă:</label>
        <input type="text" id="nume_firma" name="nume_firma" required><br><br>
    </div>

        <div class="form-group">
        <label for="persoana_de_contact">Persoană de contact:</label>
        <input type="text" id="persoana_de_contact" name="persoana_de_contact" required><br><br>
    </div>


    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
    </div>

    <div class="form-group">
        <label for="password">Parolă:</label>
        <input type="password" id="password" name="password" required><br><br>
    </div>

    <div class="form-group">
        <label for="confirmare_parola">Confirmare Parolă</label>
        <input type="password" id="confirmare_parola" name="confirmare_parola" required><br><br>
    </div>

    <div class="form-group">
        <label for="telefon">Telefon</label>
        <input type="telefon" id="telefon" name="telefon" required><br><br>
    </div>
       
    <div class="form-group">
        <label for="zona">Zona:</label>
        <input type="text" id="zona" name="zona" required><br><br>
    </div>

    <button type="submit" class="btn btn-primary">Trimite înregistrarea</button>

    </form>
</div>
</body>
</html>


