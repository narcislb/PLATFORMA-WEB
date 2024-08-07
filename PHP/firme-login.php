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
        $_SESSION['error'] = 'Va rugam sa completati toate campurile';
        header('Location: firme-login-form.php');
        exit;
    }

    // verifică dacă utilizatorul există în baza de date
    $stmt = $db->prepare('SELECT * FROM tbl_firme WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    

   
    if (!$user) {
        // utilizatorul nu exista
        $_SESSION['error'] = 'Adresa de email nu exista in baza de date!';
        header('Location: firme-login-form.php');
        exit;
    } elseif (!password_verify($password, $user['password'])) {
        // utilizatorul există, dar parola este incorectă
        $_SESSION['error'] = 'Parola introdusa este incorecta!';
        
        header('Location: firme-login-form.php');
        exit;
    } else {
        //parola este corecta continua logarea     

    // autentificare cu succes, setează variabilele de sesiune și redirecționează către pagina de profil
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_type'] = 'firma'; // folosit pentru a seta dacă utilizatorul este firmă sau client
    $_SESSION['nume_firma'] =$user['nume_firma'];
    header('Location: firme-dashboard.php?user_id=' . $user['id']);
    exit;
}
}
