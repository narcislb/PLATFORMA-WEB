



<?php
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "solarquery";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$response = ['success' => false];
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is authenticated and is a client
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'client') {
        // User is not authenticated or is not a client, return an error message
        $response = array('success' => false, 'message' => 'Nu sunteti autentificat sau nu sunteti client.');
        echo json_encode($response);
        exit;
    }
if(isset($_POST['id_firma'], $_POST['id_client'], $_POST['message'])) {
   
    $id_firma = $_POST['id_firma'];
    $id_client = $_POST['id_client'];
    $message = $_POST['message'];
    $user_type = $_SESSION['user_type'];

$conn->begin_transaction();

try {
    // First, check if there's already a conversation
    $sql = "SELECT id FROM tbl_conversatii WHERE id_client = ? AND id_firma = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_client, $id_firma);
    $stmt->execute();
    $result = $stmt->get_result();
    $conversatie_id = null;
    if($row = $result->fetch_assoc()) {
        $conversatie_id = $row['id'];
    } else {
        // If not, create a new conversation
        $sql = "INSERT INTO tbl_conversatii (id_client, id_firma) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id_client, $id_firma);
        if($stmt->execute()) {
            $conversatie_id = $conn->insert_id;
        }
    }

    // Insert the message
    if ($conversatie_id) {
        // insereaza mesajul in baza de date
        $sql = "INSERT INTO tbl_mesaje (id_conversatie, sender_type ,sender_id , continut) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isis", $conversatie_id ,$user_type , $id_client , $message);
        if (!$stmt->execute()) {
            throw new Exception("Error inserting message");
        }
        $response['success'] = true;
    }

    $conn->commit(); // If we reached here, everything worked. Commit changes.

} catch (Exception $e) {
    $conn->rollback(); // If any operation failed, rollback all changes.
    $response['message'] = $e->getMessage();
}
}

}
echo json_encode($response);
$conn->close();
?>