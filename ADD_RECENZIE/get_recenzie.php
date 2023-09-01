<?php
// Conectare la baza de date
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "solarquery";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Verificarea conexiunii
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$response = ["success" => false];

if(isset($_GET['id_firma']) && isset($_GET['id_client'])) {
    $id_firma = $_GET['id_firma'];
    $id_client = $_GET['id_client'];
    
    // Pregătirea și executarea interogării
    $sql = "SELECT descriere FROM tbl_recenzii WHERE id_firma = ? AND id_client = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_firma, $id_client);
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    if($row = $result->fetch_assoc()) {
        $response["success"] = true;
        $response["text"] = $row['descriere'];
    }
}

echo json_encode($response);

$conn->close();
?>
