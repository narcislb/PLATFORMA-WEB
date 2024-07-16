
<?php
    // Inițializarea sesiunii
session_start(); 
    //verifică dacă utilizatorul este deja autentificat si daca este client
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'client') {
  //  utilizatorul nu este autentificat, redirecționează către pagina de autentificare
header('Location: ../PHP/clienti-login-form.php?redirect=../ADD_RECENZIE/adaugare_recenzie.php');
  exit;
}
    // Preluarea ID-ului firmei din URL
    $id_client = $_SESSION['user_id'];

    // Conectarea la baza de date
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $dbname = "solarquery";

    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    // Verificarea conexiunii
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    ?>
 
  

<!DOCTYPE html>
<html>
<head>
  <title>Adaugare Recenzie</title>
  <link rel="stylesheet" type="text/css" href="../CSS/adaugare_recenzie.css">

  <script src="https://www.google.com/recaptcha/api.js" async defer></script>

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
                        <a href="php/produse.php">Produse</a>
                        <a href="php/servicii.php">Servicii</a>
                    </div>
                </li>
                <li><a href="../despre_noi.php">Despre noi</a></li>
                <li><a href="../contact.php">Contact</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Clienti</a>
                    <div class="dropdown-content">
                        <a href="../PHP/clienti-register-form.php">Înregistrare</a>
                        <a href="../PHP/clienti-login-form.php">Logare</a>
                        
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Furnizori</a>
                    <div class="dropdown-content">
                        <a href="../PHP/firme-register-form.php">Înregistrare</a>
                        <a href="../PHP/firme-login-form.php">Logare</a>
                        
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

    <script>
    function fetchResults() {
        let query = document.getElementById('search-box').value;
     // verifică dacă termenul de căutare este gol
     if (query.trim() === '') {
        document.getElementById('search-results-container').innerHTML = ''; // elimină conținutul containerului
        return; // iese din funcție
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








    <div class="content">
    <input type="text" id="searchFirma" placeholder="Cauta firma">
    <div id="suggestions"></div>

    <textarea id="recenzie" placeholder="Scrie recenzia ta aici..."></textarea>
   

    <div class="g-recaptcha" data-sitekey="6LeKjfcnAAAAAMrUlLDRKf6XhQFkr0lq_XkzGwbg"></div>


    <button onclick="saveRecenzie()">Salveaza recenzie</button>
</div>



<script>
document.getElementById('searchFirma').addEventListener('input', function() {
    let query = this.value;

    if (query.length > 2) {
        fetch(`cauta_firma.php?query=${query}`)
        .then(response => response.json())
        .then(data => {
            let suggestionHTML = '';
            data.forEach(firma => {
                suggestionHTML += `<div onclick="selectFirma('${firma.nume_firma}', ${firma.id})">${firma.nume_firma}</div>`;
            });
            document.getElementById('suggestions').innerHTML = suggestionHTML;
        });
    }
});

function selectFirma(nume_firma, id) {
    document.getElementById('searchFirma').value = nume_firma;
    document.getElementById('suggestions').innerHTML = '';
    document.getElementById('searchFirma').dataset.selectedId = id;

    // incarca recenzia firmei
    fetch(`get_recenzie.php?id_firma=${id}&id_client=<?php echo $id_client; ?>`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('recenzie').value = data.text;
        } else {
            document.getElementById('recenzie').value = '';
        }
    });
}

function saveRecenzie() {
    let firmaId = document.getElementById('searchFirma').dataset.selectedId;
    let recenzieText = document.getElementById('recenzie').value;

    let formData = new FormData();
    formData.append('id_firma', firmaId);
    formData.append('id_client', '<?php echo $id_client; ?>');
    formData.append('recenzie', recenzieText);

    let recaptchaResponse = grecaptcha.getResponse();
if (recaptchaResponse === '') {
    alert('Please complete the reCAPTCHA verification.');
    return;
}
formData.append('g-recaptcha-response', recaptchaResponse);

    fetch('salveaza_recenzie.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Recenzie salvată cu succes!');
        } else {
            alert('Eroare la salvarea recenziei.');
        }
    });
}




</script>


 





