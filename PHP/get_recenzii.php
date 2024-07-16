<?php

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "solarquery";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$response = ["success" => false, "recenzii" => []];

if(isset($_GET['id_firma'])) {
    $id_firma = $_GET['id_firma'];

    $sql = "SELECT descriere FROM tbl_recenzii WHERE id_firma = ? ORDER BY data_recenzie DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_firma);
    $stmt->execute();

    $result = $stmt->get_result();

    while($row = $result->fetch_assoc()) {
        $response["recenzii"][] = $row['descriere'];
    }

    $response["success"] = true;
}

echo json_encode($response);

$conn->close();
?>
