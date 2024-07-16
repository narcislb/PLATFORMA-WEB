<?php


// conectare la baza de date
$servername = "localhost";
$username = "root";
$db_password = "";
$dbname = "solarquery";

// creeaza conexiunea
$conn = new mysqli($servername, $username, $db_password, $dbname); 

// verifica conexiunea
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$recaptchaSecret = '6LeKjfcnAAAAAAR6hej6kvQ9kyH3kcbsP-Gbn4mp';
$recaptchaResponse = $_POST['g-recaptcha-response'];

$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecret&response=$recaptchaResponse");
$responseKeys = json_decode($response, true);

if (intval($responseKeys["success"]) !== 1) {
    echo "Please complete the CAPTCHA.";
} else {
    // CAPTCHA was completed successfully
    // Your existing form processing code



// preia datele din formular folosind metoda POST
$nume_firma = $_POST['nume_firma'];
$persoana_de_contact = $_POST['persoana_de_contact'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirmare_parola = $_POST['confirmare_parola'];

$zona = $_POST['zona'];

// valideaza datele din formular (nu sunt goale, email-ul este valid, parolele coincid)
// daca nu sunt valide, se afiseaza un mesaj de eroare si se opreste executia scriptului


// functie care verifica daca email-ul exista deja in baza de date
function emailExists($conn, $email) {
    $stmt = $conn->prepare("SELECT email FROM tbl_firme WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result(); // stocheaza rezultatul  
    $numRows = $stmt->num_rows;
    $stmt->close();

    return $numRows > 0; // returneaza true daca email-ul exista deja in baza de date
}

// verifica daca formularul a fost trimis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nume_firma = $_POST['nume_firma'];
    $persoana_de_contact = $_POST['persoana_de_contact'];
    $email = $_POST['email'];
    $plain_password = $_POST['password'];
    $telefon = $_POST['telefon'];
    $confirmare_parola = $_POST['confirmare_parola'];
    
    $zona = $_POST['zona'];
    
    // verificari formular completat corect
    if (empty($nume_firma) || empty($persoana_de_contact) || empty($email) || empty($plain_password) || empty($telefon) ||   empty($confirmare_parola)  || empty($zona) ) {
        echo "Toate câmpurile sunt obligatorii!";
        exit();
    }
    if ($plain_password != $confirmare_parola) {
        echo "Parolele nu se potrivesc!";
        exit();
    }
    
    


if (emailExists($conn, $email)) {
    echo "Emailul este deja înregistrat!";
    exit();
}
    // Hash the password before saving it to the database
    $password = password_hash($plain_password, PASSWORD_BCRYPT);
    
    // pregateste statement-ul si il executa
    $stmt = $conn->prepare("INSERT INTO tbl_firme (nume_firma, persoana_de_contact, email, password ,telefon, zona) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nume_firma, $persoana_de_contact, $email, $password,$telefon, $zona);
    //Obiectul "stmt" poate fi folosit pentru a executa statement-ul SQL 
    //de mai multe ori cu valori diferite, fără a fi necesară recompilarea acestuia de fiecare dată.

    // verifica daca statement-ul s-a executat cu succes si afiseaza un mesaj corespunzator
    if ($stmt->execute()) {
        echo "Înregistrare reușită!";
        echo '<button onclick="window.location.href=\'../PHP/firme-login-form.php\'">Catre pagina de logare</button>';
    } else {
        echo "Eroare la înregistrare: " . $stmt->error;
    }
    
    // inchide statement-ul si conexiunea
    $stmt->close();
    $conn->close();
}
}
?>