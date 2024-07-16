<?php
 session_start();

$review_id = $_GET['id'];

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "solarquery";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

$stmt = $conn->prepare("UPDATE tbl_recenzii SET flagged = TRUE WHERE id = ?");
$stmt->bind_param("i", $review_id);
$stmt->execute();

// redirectioneaza catre pagina de afisare a recenziilor
header('Location: ../PHP/firme_afisare_recenzii.php?flagged=true');
exit;
?>
