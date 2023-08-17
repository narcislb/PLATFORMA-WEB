<?php
session_start();
// Connect to the database
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "solarquery";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the posted data
$data = json_decode(file_get_contents("php://input"));

$response = ['success' => false];

if (isset($data->productId) && isset($data->quantity)) {
    $productId = $data->productId;
    $quantity = $data->quantity;

    // Assuming you have a 'quantity' column in your 'tbl_produse' table
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
