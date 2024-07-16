<?php

// Inițializarea sesiunii
session_start(); 
header('Content-Type: application/json');

//verifică dacă utilizatorul este deja autentificat si daca este firma
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'firma') {
//  utilizatorul nu este autentificat, redirecționează către pagina de autentificare
header('Location: firme-login-form.php');
exit;
}
// Preluarea ID-ului firmei din URL
$id_firma = $_SESSION['user_id'];


// Conectarea la baza de date
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "solarquery";
$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

$response = [
    'success' => false
];

$data = json_decode(file_get_contents("php://input"));

if (isset($data->id) && isset($data->nume_produs) && isset($data->descriere_produs) && isset($data->pret_produs) && isset($data->cantitate)) {
    $sql = "UPDATE tbl_produse SET nume_produs = ?, descriere_produs = ?, pret_produs = ?, cantitate = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiii", $data->nume_produs, $data->descriere_produs, $data->pret_produs, $data->cantitate, $data->id);

    if ($stmt->execute()) {
        $response['success'] = true;
    }
    $stmt->close();
}


echo json_encode($response);

?>
