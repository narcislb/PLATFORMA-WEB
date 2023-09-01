<?php
session_start(); 
// preia datele despre utilizatorul curent
$username = $_GET['username'];

// conectarea la baza de date
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "solarquery";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Verifică dacă s-a realizat conexiunea la baza de date
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Selectează toate datele despre utilizatorul cu numele de utilizator $username
$stmt = $conn->prepare("SELECT * FROM tbl_firme WHERE nume_firma = ?");
if ($stmt === false) {
    die("Error preparing SQL statement: " . $conn->error);
}
$stmt->bind_param("s", $username); 

// Execută instrucțiunea SQL
$stmt->execute();

// Preia rezultatele interogării
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc(); // preia datele din rândul curent

    
?>

<!DOCTYPE html>
<html>
<head>
	<title><?php echo $row['nume_firma']; ?> Profile</title>
    
</head>
<body>
	<h1><?php echo $row['nume_firma']; ?> Profile</h1>
    <a href="firme-dashboard.php?user_id=<?php echo $row['id']; ?>">Dashboard</a>

	<p>Here is some information about <?php echo $row['nume_firma']; ?>:</p>
    <p>Persoana de contact: <?php echo $row['persoana_de_contact']; ?></p>
    <p>Email: <?php echo $row['email']; ?></p>
    <p>Zona: <?php echo $row['zona']; ?></p>
    

  <!-- afisare restul datelor aici -->
	<h2>Servicii oferite</h2>
<?php
if (isset($_POST['edit'])) {
	// afiseaza formularul de editare daca butonul de editare a fost apasat
?>
<form method="post" action="">
	<textarea name="services" rows="10" cols="50"><?php echo $row['services']; ?></textarea>
	<br><br>
	<input type="submit" name="submit" value="Submit">
</form>
<?php
} else {
	// afiseaza serviciile daca butonul de editare nu a fost apasat
?>
<p><?php echo $row['services']; ?></p>
<?php
}
?>

<!-- butonul de editare --> 
<form method="post" action="">
	<input type="submit" name="edit" value="Edit" >
</form>

<span style="display: inline-block; width: 10px;"></span>


<form action="logout.php" method="post">
          <button type="submit" name="logout">Logout</button>
      </form>


<?php
// actualizeaza serviciile in baza de date daca butonul de submit a fost apasat
if (isset($_POST['submit'])) {
    // obtine serviciile din formular
    $new_services = $_POST['services'];

    // actualizeaza serviciile in baza de date
    $stmt = $conn->prepare("UPDATE tbl_firme SET services = ? WHERE nume_firma = ?");
    if ($stmt === false) {
        die("Error preparing SQL statement: " . $conn->error);
    }
    $stmt->bind_param("ss", $new_services, $username);
    if ($stmt->execute()) {
        echo "Servicii field updated successfully.Please refresh the page";

        // Selectează toate datele despre utilizatorul cu numele de utilizator $username
        $stmt = $conn->prepare("SELECT * FROM tbl_firme WHERE nume_firma = ?");
        if ($stmt === false) {
            die("Error preparing SQL statement: " . $conn->error);
        }
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        // Redirecționează către pagina de profil
        header("Location: profil_firma.php?username=$username");
        exit();
    } else {
        echo "Error updating servicii: " . $conn->error;
    }
    $stmt->close();
}

?>



</body>
</html>

<?php
} else {
    // Display an error message if the username does not exist in the database
    echo "User not found";
}

// Close the database connection

$conn->close();
?>
