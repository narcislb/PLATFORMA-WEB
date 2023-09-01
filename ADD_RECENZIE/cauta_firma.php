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

$response = [];

if(isset($_GET['query'])) {
    $query = $_GET['query'];
    $searchTerm = "%" . $query . "%";
    
    // Pregătirea și executarea interogării
    $sql = "SELECT id, nume_firma FROM tbl_firme WHERE nume_firma LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $response[] = ['id' => $row['id'], 'nume_firma' => $row['nume_firma']];
    }
}

echo json_encode($response);

$conn->close();
?>
