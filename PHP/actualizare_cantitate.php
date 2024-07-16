<?php
session_start();
// conectare la baza de date
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "solarquery";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// obtine datele 
$data = json_decode(file_get_contents("php://input"));

$response = ['success' => false];

if (isset($data->productId) && isset($data->quantity)) {
    $productId = $data->productId;
    $quantity = $data->quantity;

    // actualizare cantitate in baza de date
    $sql = "UPDATE tbl_produse SET cantitate = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $quantity, $productId);
    
    if ($stmt->execute()) {
        $response['success'] = true;
    }

    $stmt->close();
}

echo json_encode($response);

$conn->close();
?>
