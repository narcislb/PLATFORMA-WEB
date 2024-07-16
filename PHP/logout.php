<?php
session_start(); // Start the session

// deseteaza toate variabilele de sesiune
$_SESSION = array();

// distruge sesiunea
session_destroy();


// redirectioneaza catre pagina corespunzatoare
if ($_SESSION['user_type'] == 'client') {
    header("Location: client-login-form.php");
} else if ($_SESSION['user_type'] == 'firma') {
    header("Location: firme-login-form.php");
} else {
    header("Location: ../index.php");
}
exit;
?>