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
    
    // pregateste statement-ul si il executa 
    $stmt = $conn->prepare("INSERT INTO tbl_clienti (email, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $password);

    //Obiectul "stmt" poate fi folosit pentru a executa statement-ul SQL 
    //de mai multe ori cu valori diferite, fără a fi necesară recompilarea acestuia de fiecare dată.

    // verifica daca statement-ul s-a executat cu succes si afiseaza un mesaj corespunzator
    if ($stmt->execute()) {
        echo "Înregistrare reușită!";
        echo '<button onclick="window.location.href=\'..PHP/clienti-login-form.php\'">Catre pagina de logare</button>';
    } else {
        echo "Eroare la înregistrare: " . $stmt->error;
    }
    
    // inchide statement-ul si conexiunea
    $stmt->close();
    $conn->close();
}
?>