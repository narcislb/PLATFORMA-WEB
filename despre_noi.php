<?php
session_start(); // Start the session
?>

<!DOCTYPE html>
<html>
<head>
  <title>My Page</title>
  <link rel="stylesheet" type="text/css" href="../CSS/clienti_comenzi.css">
</head>
<body>
    <header>
        <h1><a href="../index.php">SolarQuery</a></h1>
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
    <section class="mission-statement">
    <h2>MISIUNEA NOASTRĂ</h2>
    <h3>REGISTRUL FURNIZORILOR FOTOVOLTAICI</h3>
    <p>Unind Clienții și Furnizorii în Lumea Energiei Regenerabile.</p>
    <p>Încurajăm Încrederea, Sustenabilitatea și Colaborarea.</p>
    <p>Registrul Furnizorilor Fotovoltaici este o platformă ce găzduiește recenzii, opinii, recomandări și informații relevante despre furnizorii de sisteme fotovoltaice și servicii de instalare. Aceasta a fost fondată cu viziunea de a deveni un instrument independent și de încredere pentru consumatorii interesați de energia solară. Pentru furnizori, Registrul Furnizorilor Fotovoltaici oferă o metodă transparentă și eficientă de promovare în mediul online, facilitând astfel adoptarea pe scară largă a energiei regenerabile.</p>
</section>




<style>
   /* Basic styling reset for better cross-browser consistency */


.mission-statement {
    background-color: #00BFA6; /* A fresh turquoise background */
    padding: 40px; /* Increased spacing around the content */
    border-radius: 15px; /* Rounded corners for the section */
    margin: 30px 0; /* Increased spacing top and bottom */
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* A stronger shadow for depth */
    font-family: 'Arial', sans-serif; /* A web-safe font, but you can replace with others like 'Verdana', 'Trebuchet MS', etc. */
    color: #fff; /* White text for better contrast on the turquoise background */
}

.mission-statement h2 {
    font-size: 2.2em; /* Larger size for the main title */
    margin-bottom: 20px; /* Spacing below the heading */
    border-bottom: 3px solid #ffffff66; /* A subtle underline for the title */
    padding-bottom: 10px; /* Spacing between title text and its underline */
}

.mission-statement h3 {
    font-size: 1.6em; /* Distinct size for the sub-heading */
    margin: 20px 0; /* Spacing above and below the sub-heading */
    font-style: italic; /* Italicize the sub-heading for emphasis */
}

.mission-statement p {
    line-height: 1.7; /* Increase line-height for readability */
    margin-bottom: 20px; /* Space between paragraphs */
    font-size: 1.1em; /* A bit larger font size for better readability */
}

</style>










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


