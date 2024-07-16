<?php
session_start();



// verificare daca userul este logat si daca este admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == false) {
    header('Location: admin_login.php');
    exit;
}

// verificare daca userul este admin
if ($_SESSION['user_role'] != 'admin') {
    die('Access denied! Only admins can view this page.');
}

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "solarquery";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



// Delete Review if delete parameter is set
if(isset($_GET['delete'])) {
    $idToDelete = $_GET['delete'];
    
    $deleteStmt = $conn->prepare("DELETE FROM tbl_recenzii WHERE id = ?");
    $deleteStmt->bind_param("i", $idToDelete);
    
    if ($deleteStmt->execute()) {
        echo "<script>alert('Review deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting review: " . $conn->error . "');</script>";
    }

    // Redirectionare catre admin_page.php
    header("Location: admin_flagged_review.php");
    exit;
}

// resetare flag review daca unsetflag parameter este setat
if(isset($_GET['unsetflag'])) {
    $idToUnsetFlag = $_GET['unsetflag'];
    
    $unsetFlagStmt = $conn->prepare("UPDATE tbl_recenzii SET flagged = 0 WHERE id = ?");
    $unsetFlagStmt->bind_param("i", $idToUnsetFlag);
    
    if ($unsetFlagStmt->execute()) {
        echo "<script>alert('Raportare eliminata cu succes!');</script>";
    } else {
        echo "<script>alert('  Eroare intampinata : " . $conn->error . "');</script>";
    }

    // redirectionare catre admin_page.php
    header("Location: admin_flagged_review.php");
    exit;
}

?>


<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../CSS/admin_page.css">
</head>
<body>



   <!-- Header -->
   <header>
    <div class="header-content">
    <div class="logo"><a href="admin_page.php">SolarQuery Admin</a></div>

        <nav>
            <ul>
                
                <li><a href="#">Administrare useri</a></li>
                <li><a href="#">Vizualizare rapoarte</a></li>
                <li><a href="../PHP/logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>


<h1>Admin Dashboard</h1>

<!-- adaugare functionalitati -->
<div>
    <h2>Flagged Reviews</h2>

    <?php
    $stmt = $conn->prepare("SELECT * FROM tbl_recenzii WHERE flagged = 1");
$stmt->execute();
$result = $stmt->get_result();

while($row = $result->fetch_assoc()) {
    echo "<div>";
    echo "<strong>" . $row['nume_client'] . "</strong>: " . $row['descriere'];
    echo " <a href='admin_flagged_review.php?delete=" . $row['id'] . "' onclick='return confirmDelete()'>Delete</a>";
    echo "</div>";
    echo " | <a href='admin_flagged_review.php?unsetflag=" . $row['id'] . "' onclick='return confirmUnsetFlag()'>Unset Flag</a>";
    echo "</div>";
}
?>

</div>

<script>
function confirmDelete() {
    return confirm('Sunteti sigur ca vreti sa stergeti aceasta recenzie?');
}


function confirmUnsetFlag() {
    return confirm('Sunteti sigur ca doriti sa resetati acest raport?');
}

</script>