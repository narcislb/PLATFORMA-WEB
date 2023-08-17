<?php
// Start session and retrieve user data
session_start();
$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];

// Database connection
$host = 'localhost';
$dbname = 'solarquery';
$user = 'root';
$db_password = '';
$db = new PDO("mysql:host=$host;dbname=$dbname", $user, $db_password);

// Check if the user is authenticated and a client
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'client') {
  // Redirect if not authenticated or not a client
  // header('Location: clienti-login-form.php');
  // exit;
}
?>

<!-- HTML structure -->
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Page</title>
    <link rel="stylesheet" href="../CSS/adresa_styles.css">
</head>
<body>

<!-- Navbar -->
<div class="navbar">
  <h1>My Page</h1>
</div>

<!-- Sidebar -->
<div class="sidebar">
  <a href="profil_client.php?username=<?php echo $username; ?>">
    <h1>Dashboard</h1>
  </a>
  <ul>
      <li><a href="clienti_comenzi.php">Comenzi</a></li>
      <li><a href="adaugare_adresa_client.php">Adrese si date personale</a></li>
      <li><a href="logout.php">Logout</a></li>
  </ul>
</div>

<!-- Content box -->
<div class="content">
  <h2>Adrese</h2>
  
  <?php
  // Initialization
  $tara = $judet = $localitate = $adresa = $cod_postal = $telefon = $nume = "";

  // Fetch data
  $stmt = $db->prepare('SELECT * FROM tbl_adrese WHERE id_client = :user_id');
  $stmt->bindParam(':user_id', $user_id);
  $stmt->execute();
  $userData = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($userData) {
    extract($userData);
  }

  // Form submission logic
  if (isset($_POST['submit'])) {
    // Capture data from form
    $tara = $_POST['tara'];
    $judet = $_POST['judet'];
    $localitate = $_POST['localitate'];
    $adresa = $_POST['adresa'];
    $cod_postal = $_POST['cod_postal'];
    $telefon = $_POST['telefon'];
    $nume = $_POST['nume'];
  
    // Update the database
    $stmt = $db->prepare('UPDATE tbl_adrese SET tara = :tara, judet = :judet, localitate = :localitate, adresa = :adresa, cod_postal = :cod_postal, telefon = :telefon, nume = :nume WHERE id_adresa = :id_adresa');
    $stmt->bindParam(':tara', $tara);
    $stmt->bindParam(':judet', $judet);
    $stmt->bindParam(':localitate', $localitate);
    $stmt->bindParam(':adresa', $adresa);
    $stmt->bindParam(':cod_postal', $cod_postal);
    $stmt->bindParam(':telefon', $telefon);
    $stmt->bindParam(':nume', $nume);
    $stmt->bindParam(':id_adresa', $id_adresa);
    $stmt->execute();
    
    echo '<p>Adresa a fost adaugata cu succes.</p>';
  }
  ?>

  <!-- Address form -->
  <form method="post">
  <div>
      <label for="tara">Tara:</label>
      <input type="text" name="tara" id="tara" value="<?php echo htmlspecialchars($tara); ?>" required>
    </div>
    <div>
      <label for="judet">Judet:</label>
      <input type="text" name="judet" id="judet" value="<?php echo htmlspecialchars($judet); ?>"    required>
    </div>
    <div>
      <label for="localitate">Localitate:</label>
      <input type="text" name="localitate" id="localitate" value="<?php echo htmlspecialchars($localitate); ?>" required>
    </div>
    <div>
      <label for="adresa">Adresa:</label>
      <input type="text" name="adresa" id="adresa" value="<?php echo htmlspecialchars($adresa); ?>" required>
    </div>
    <div>
      <label for="cod_postal">Cod postal:</label>
      <input type="text" name="cod_postal" id="cod_postal" value="<?php echo htmlspecialchars($cod_postal); ?>" required>
    </div>
    <div>
      <label for="telefon">Telefon:</label>
      <input type="text" name="telefon" id="telefon" value="<?php echo htmlspecialchars($telefon); ?>" required>
    </div>
    <div>
      <label for="nume">Nume:</label>
      <input type="text" name="nume" id="nume" value="<?php echo htmlspecialchars($nume); ?>" required>
    </div>
    <input type="submit" name="submit" value="Adauga adresa">
  </form>
</div>

</body>
</html>

