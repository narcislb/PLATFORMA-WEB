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

if(isset($_POST['id_firma']) && isset($_POST['id_client']) && isset($_POST['recenzie'])) {
    $id_firma = $_POST['id_firma'];
    $id_client = $_POST['id_client'];
    $recenzieText = $_POST['recenzie'];

    // Verificăm dacă există deja o recenzie pentru această pereche client-firmă
    $checkSql = "SELECT id FROM tbl_recenzii WHERE id_firma = ? AND id_client = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("ii", $id_firma, $id_client);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        // Actualizăm recenzia existentă
        $updateSql = "UPDATE tbl_recenzii SET descriere = ? WHERE id_firma = ? AND id_client = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("sii", $recenzieText, $id_firma, $id_client);
        if($updateStmt->execute()) {
            $response["success"] = true;
        }
    } else {
        // Inserăm o recenzie nouă
        $insertSql = "INSERT INTO tbl_recenzii (id_firma, id_client, descriere) VALUES (?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("iis", $id_firma, $id_client, $recenzieText);
        if($insertStmt->execute()) {
            $response["success"] = true;
        }
    }
}

echo json_encode($response);

$conn->close();
?>
