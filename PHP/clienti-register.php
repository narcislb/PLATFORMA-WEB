<?php
// preia datele din formular folosind metoda POST

$email = $_POST['email'];
$password = $_POST['password'];
$confirmare_parola = $_POST['confirmare_parola'];


// verifica daca formularul a fost trimis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmare_parola = $_POST['confirmare_parola'];
    
    
    // verificari formular completat corect
    if ( empty($email) || empty($password) || empty($confirmare_parola) )  {
        echo "Toate câmpurile sunt obligatorii!";
        exit();
    }
    if ($password != $confirmare_parola) {
        echo "Parolele nu se potrivesc!";
        exit();
    }
    

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
    
// Function to check if email is already registered
function emailExists($conn, $email) {
    $stmt = $conn->prepare("SELECT email FROM tbl_clienti WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result(); // Store the result so we can check how many rows returned
    $numRows = $stmt->num_rows;
    $stmt->close();

    return $numRows > 0; // return true if email exists
}

if (emailExists($conn, $email)) {
    echo "Emailul este deja înregistrat!";
    exit();
}

$current_date = date('Y-m-d');
    // pregateste statement-ul si il executa 
    $stmt = $conn->prepare("INSERT INTO tbl_clienti (email, password, date) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $password, $current_date);

    //Obiectul "stmt" poate fi folosit pentru a executa statement-ul SQL 
    //de mai multe ori cu valori diferite, fără a fi necesară recompilarea acestuia de fiecare dată.

    // verifica daca statement-ul s-a executat cu succes si afiseaza un mesaj corespunzator
    if ($stmt->execute()) {
        echo "Înregistrare reușită!";
        echo '<button onclick="window.location.href=\'../PHP/clienti-login-form.php\'">Catre pagina de logare</button>';
    } else {
        echo "Eroare la înregistrare: " . $stmt->error;
    }
    
    // inchide statement-ul si conexiunea
    $stmt->close();
    $conn->close();
}
?>