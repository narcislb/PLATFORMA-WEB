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



// preia datele din formular folosind metoda POST
$nume_firma = $_POST['nume_firma'];
$persoana_de_contact = $_POST['persoana_de_contact'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirmare_parola = $_POST['confirmare_parola'];

$zona = $_POST['zona'];

// Validate the form data (e.g. check if required fields are filled in, if passwords match, etc.)
// If the form data is not valid, display an error message and redirect the user back to the registration page


// Function to check if email is already registered
function emailExists($conn, $email) {
    $stmt = $conn->prepare("SELECT email FROM tbl_firme WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result(); // Store the result so we can check how many rows returned
    $numRows = $stmt->num_rows;
    $stmt->close();

    return $numRows > 0; // return true if email exists
}

// verifica daca formularul a fost trimis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nume_firma = $_POST['nume_firma'];
    $persoana_de_contact = $_POST['persoana_de_contact'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $telefon = $_POST['telefon'];
    $confirmare_parola = $_POST['confirmare_parola'];
    
    $zona = $_POST['zona'];
    
    // verificari formular completat corect
    if (empty($nume_firma) || empty($persoana_de_contact) || empty($email) || empty($password) || empty($telefon) ||   empty($confirmare_parola)  || empty($zona) ) {
        echo "Toate câmpurile sunt obligatorii!";
        exit();
    }
    if ($password != $confirmare_parola) {
        echo "Parolele nu se potrivesc!";
        exit();
    }
    
    


if (emailExists($conn, $email)) {
    echo "Emailul este deja înregistrat!";
    exit();
}
    
    
    // pregateste statement-ul si il executa
    $stmt = $conn->prepare("INSERT INTO tbl_firme (nume_firma, persoana_de_contact, email, password,telefon, zona) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nume_firma, $persoana_de_contact, $email, $password,$telefon, $zona);
    //Obiectul "stmt" poate fi folosit pentru a executa statement-ul SQL 
    //de mai multe ori cu valori diferite, fără a fi necesară recompilarea acestuia de fiecare dată.

    // verifica daca statement-ul s-a executat cu succes si afiseaza un mesaj corespunzator
    if ($stmt->execute()) {
        echo "Înregistrare reușită!";
        echo '<button onclick="window.location.href=\'../HTML/firme-login.html\'">Catre pagina de logare</button>';
    } else {
        echo "Eroare la înregistrare: " . $stmt->error;
    }
    
    // inchide statement-ul si conexiunea
    $stmt->close();
    $conn->close();
}
?>