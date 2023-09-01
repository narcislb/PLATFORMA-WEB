<?php
// Inițializarea sesiunii
session_start();

// Check if the user is logged in and if it's a firma
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'firma') {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$id_firma = $_SESSION['user_id'];

// Database Connection
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "solarquery";
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check for database connection error
if ($conn->connect_error) {
    die(json_encode(["error" => $conn->connect_error]));
}

// Check for query parameter
if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $sql = "SELECT id, nume_produs FROM tbl_produse WHERE nume_produs LIKE ? AND id_firma = ? LIMIT 5";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $query . "%";
    $stmt->bind_param("si", $searchTerm, $id_firma);
    $stmt->execute();

    $result = $stmt->get_result();
    $suggestions = [];
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($suggestions);
    exit;
}
?>
