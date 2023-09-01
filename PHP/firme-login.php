<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// pornește o sesiune
session_start();

// conectare la baza de date
$host = 'localhost';
$dbname = 'solarquery';
$user = 'root';
$db_password = '';

$db = new PDO("mysql:host=$host;dbname=$dbname", $user, $db_password);

// verifică dacă formularul a fost trimis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // preia datele din formular
    $email = $_POST['email'];
    $password = $_POST['password'];

    // validare formular
    if (empty($email) || empty($password)) {
        // afișează un mesaj de eroare și redirecționează către pagina de login
        $_SESSION['error'] = 'Please fill in all fields';
        header('Location: firme-login-form.php');
        exit;
    }

    // verifică dacă utilizatorul există în baza de date
    $stmt = $db->prepare('SELECT * FROM tbl_firme WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    

   
    if (!$user || $password != $user['password']) {
    // utilizatorul nu există în baza de date, afișează un mesaj de eroare și redirecționează către pagina de login
    $_SESSION['error'] = 'Invalid email or password';
    header('Location: firme-login-form.php');
    exit;
}

    // autentificare cu succes, setează variabilele de sesiune și redirecționează către pagina de profil
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_type'] = 'firma'; // folosit pentru a seta dacă utilizatorul este firmă sau client
    $_SESSION['nume_firma'] =$user['nume_firma'];
    header('Location: profil_firma.php?username=' . $user['nume_firma']);
    exit;
}

