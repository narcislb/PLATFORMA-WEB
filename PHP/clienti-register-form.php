<?php

session_start();
?>


<!DOCTYPE html>
<html>
<head>
    <title>Înregistrare Clienti</title>
    <link rel="stylesheet" href="../CSS/register_page.css">


    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
     // verificăm dacă query-ul nu este gol
     if (query.trim() === '') {
        document.getElementById('search-results-container').innerHTML = ''; // Golește containerul de rezultate
        return; // Terminăm executarea funcției
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
    
    <div class="container">
        <h1>Formular inregistrare client</h1>
        <form id="registerForm"  method="post">
        
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Parolă:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirmare_parola">Confirmare Parolă</label>
                <input type="password" id="confirmare_parola" name="confirmare_parola" required>
            </div>
            <!-- <button type="submit" class="btn btn-primary">Trimite înregistrarea</button> -->

            <div class="g-recaptcha" data-sitekey="6LeKjfcnAAAAAMrUlLDRKf6XhQFkr0lq_XkzGwbg"></div>


            <button type="button" onclick="submitForm()" class="btn btn-primary">Trimite înregistrarea</button>


        </form>
    </div>


    <div id="message"></div>

    <script>
        function submitForm() {
    // obtinem datele din formular
    const formData = new FormData(document.getElementById('registerForm'));

    let recaptchaResponse = grecaptcha.getResponse();
if (recaptchaResponse === '') {
    alert('Please complete the reCAPTCHA verification.');
    return;
}
formData.append('g-recaptcha-response', recaptchaResponse);


    // folosim fetch API pentru a trimite datele catre server
    fetch('clienti-register.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text()) 
    .then(data => {
        // actualizam mesajul de eroare
        document.getElementById('message').innerText = data;

        // resetam formularul
        document.getElementById('registerForm').reset();
    })
    .catch(error => console.error('Error:', error));
}

    </script>




<style>
    #message {
    text-align: center; /* Center the text horizontally */
    margin-top: 20px;  /* Add some space between the form and the message */
    color: red;        /* Make the text color red to indicate an error */
    font-weight: bold; /* Bold the text for emphasis */
}
</style>


</body>
</html>