<?php

session_start(); // pornește o sesiune

//verifică dacă utilizatorul este deja autentificat si daca este firma
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'firma') {
    //  utilizatorul este deja autentificat, redirecționează către pagina de profil
    header('Location: firme-dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Autentificare companie - Platforma SolarQuery</title>
  <meta charset="utf-8">
  <link rel="stylesheet" type="text/css" href="../CSS/style.css">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
<header>
    <h1>
      <a href="../index.php">SolarQuery</a>
    </h1>
    </header>
    <main>
        
        <h2>Autentificare furnizor</h2>
        <form action="firme-login.php" method="POST"> <!-- aici se va trimite formularul catre pagina firme-login.php-->
            
                <label for="email">Adresa de email:</label>
                <input type="email" id="email" name="email" required>
                <label for="password">Parolă:</label>
                <input type="password" id="password" name="password" required>

                <div class="g-recaptcha" data-sitekey="6LeKjfcnAAAAAMrUlLDRKf6XhQFkr0lq_XkzGwbg"></div>


                <button type="submit" onclick="return checkRecaptcha()">Autentificare</button>

            </form>

            <?php
if (isset($_SESSION['error'])) {
  echo "<p class='error-message'>" . $_SESSION['error'] . "</p>"; 
  unset($_SESSION['error']); // sterge mesajul dupa afisare
}
?>


            <p>Nu aveți cont? <a href="firme-register-form.php">Înregistrați-vă aici</a></p>
    </main>

    
    <footer>
        <p>&copy; 2023 Platforma SolarQuery</p>
    </footer>

    <script>
  function checkRecaptcha() {
  if (grecaptcha.getResponse().length == 0) {
    alert("Vă rugăm să completați reCAPTCHA înainte de a continua.");
    return false;
  } else {
    return true;
  }
}
</script>


</body>
</html>


