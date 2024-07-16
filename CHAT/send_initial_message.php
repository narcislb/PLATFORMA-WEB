



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
    // verifica daca userul e logat si este client
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'client') {
        // eroare
        $response = array('success' => false, 'message' => 'Nu sunteti autentificat sau nu sunteti client.');
        echo json_encode($response);
        exit;
    }
if(isset($_POST['id_firma'], $_POST['id_client'], $_POST['message'])) {
   
    $id_firma = $_POST['id_firma'];
    $id_client = $_POST['id_client'];
    $message = $_POST['message'];
    $subiect = $_POST['subiect'];

    $user_type = $_SESSION['user_type'];


$conn->begin_transaction();

try {
   // mai intai verificam daca exista o conversatie intre client si firma
$sql = "SELECT id FROM tbl_conversatii WHERE subiect = ? AND id_client = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $subiect, $id_client); // leaga parametrii de interogare pentru a preveni SQL injection
$stmt->execute();
$result = $stmt->get_result();
$conversatie_id = null;
if($row = $result->fetch_assoc()) {
    $conversatie_id = $row['id'];
    } else {
        // daca nu exista conversatie, o cream
        $sql = "INSERT INTO tbl_conversatii (id_client, id_firma, subiect) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $id_client, $id_firma, $subiect);
        if($stmt->execute()) {
            $conversatie_id = $conn->insert_id;
        }
    }

    // daca exista conversatie, inseram mesajul in baza de date
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

    $conn->commit(); // daca am ajuns aici, inseamna ca totul a mers bine, deci facem commit la schimbari

} catch (Exception $e) {
    $conn->rollback(); // daca a aparut o eroare, facem rollback la schimbari
    $response['message'] = $e->getMessage();
}
}

}
echo json_encode($response);
$conn->close();
?>