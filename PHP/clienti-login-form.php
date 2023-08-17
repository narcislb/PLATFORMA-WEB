<?php

session_start(); // pornește o sesiune

//verifică dacă utilizatorul este deja autentificat si daca este client
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'client') {
    //  utilizatorul este deja autentificat, redirecționează către pagina de profil
    header('Location: profil_client.php?username=' . $_SESSION['username']);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Autentificare companie - Platforma SolarQuery</title>
  <meta charset="utf-8">
  <link rel="stylesheet" type="text/css" href="../CSS/style.css">
</head>
<body>
<header>
    <h1>
      <a href="../index.html">Platforma SolarQuery</a>
    </h1>
    </header>
    <main>
        
        <h2>Autentificare client</h2>
        <form action="clienti-login.php" method="POST"> <!-- aici se va trimite formularul catre pagina firme-login.php-->
            
                <label for="email">Adresa de email:</label>
                <input type="email" id="email" name="email" required>
                <label for="password">Parolă:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Autentificare</button>
            </form>
            <p>Nu aveți cont? <a href="#">Înregistrați-vă aici</a></p>
    </main>
    <footer>
        <p>&copy; 2023 Platforma SolarQuery</p>
    </footer>
</body>
</html>


