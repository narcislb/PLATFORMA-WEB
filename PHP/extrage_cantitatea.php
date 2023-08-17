<?php
session_start(); // pornește o sesiune
// conectarea la baza de date
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "solarquery";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$response = ['success' => false];

if (isset($_GET['productId'])) {
    $productId = $_GET['productId'];
    
    // Query the database to retrieve the quantity of the product
    $sql = "SELECT cantitate FROM tbl_produse WHERE id = $productId";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        // Return the quantity of the product as a JSON response
        $row = $result->fetch_assoc();
        echo json_encode(array('success' => true, 'quantity' => $row['cantitate']));
    } 
    else {
       echo json_encode(array('success' => false));
    }
    
    
}

//echo json_encode($response);

$conn->close();
?>
