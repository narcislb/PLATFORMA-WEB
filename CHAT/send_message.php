<?php
session_start();

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "solarquery";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$response = ["success" => false];

if(isset($_POST['message']) && isset($_POST['conversatie_id'])) {
    $message = $_POST['message'];
    $conversatie_id = $_POST['conversatie_id'];
    $sender_type = $_SESSION['user_type'] ;  // Or "firma", depending on the session or other logic.
    $sender_id = $_SESSION['user_id'];  // Assuming you have a session with user_id.

    $sql = "INSERT INTO tbl_mesaje (continut, id_conversatie, sender_type, sender_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisi", $message, $conversatie_id, $sender_type, $sender_id);

    if($stmt->execute()) {
        $response["success"] = true;
    } else {
        $response["error"] = $stmt->error;
    }

    $stmt->close();
}

echo json_encode($response);
?>
