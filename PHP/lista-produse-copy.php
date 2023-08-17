<?php
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "solarquery";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM tbl_produse";
$params = [];

if (isset($_GET['subcategorie'])) {
    $subcategorie = $_GET['subcategorie'];
    $sql .= " WHERE subcategorie = ?";
    $params[] = $subcategorie;

    if (isset($_GET['pret'])) {
        $pret = $_GET['pret'];

        if ($pret == '0-50') {
            $sql .= " AND pret_produs BETWEEN 0 AND 50";
        } elseif ($pret == '51-100') {
            $sql .= " AND pret_produs BETWEEN 51 AND 100";
        }
    }
}

$stmt = $conn->prepare($sql);

if ($params) {
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error executing query: " . $conn->error);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Lista produse</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../CSS/lista_produse_style.css">
</head>
<body>
    <header>
        
        <a href="../index.html"><h1>Platforma SolarQuery</h1></a>
        <nav>
            <ul>
                <li><a href="#">Acasă</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Shop</a>
                    <div class="dropdown-content">
                        <a href="HTML/produse.html">Produse</a>
                        <a href="HTML/servicii.html">Servicii</a>
                    </div>
                </li>
            </ul>
        </nav>
    </header>
    <h2>Produse</h2>
    <div class="content-wrapper">
        <div class="filter-section">
            <form method="get" action="lista-produse.php">
                <input type="hidden" name="subcategorie" value="<?= isset($_GET['subcategorie']) ? htmlspecialchars($_GET['subcategorie']) : ''; ?>">
                <label for="pret">Filtrare după preț:</label>
                <select name="pret" id="pret">
                    <option value="">Toate</option>
                    <option value="0-50">0 - 50</option>
                    <option value="51-100">51 - 100</option>
                </select>
                <button type="submit">Filtrare</button>
            </form>
        </div>
        <div class="product-section">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $image_sql = "SELECT nume_imagine FROM tbl_imagini WHERE id_produs = ?";
                $image_stmt = $conn->prepare($image_sql);
                $image_stmt->bind_param('i', $row['id']);
                $image_stmt->execute();
                $image_result = $image_stmt->get_result();
                $image_row = $image_result->fetch_assoc();
                $image_path = $image_row ? "../IMAGES/products/" . $image_row['nume_imagine'] : null;
                ?>
                <div class='produs'>
                    <h3><a href='produs.php?id=<?= $row['id'] ?>'><?= htmlspecialchars($row['nume_produs']) ?></a></h3>
                    <p>Pret: <?= htmlspecialchars($row['pret_produs']) ?> lei</p>
                    <p>Descriere: <?= htmlspecialchars($row['descriere_produs']) ?></p>
                    <?php if ($image_path): ?>
                        <img src="<?= $image_path ?>" alt='Imagine produs' style='max-width: 300px;'>
                    <?php else: ?>
                        <p>Imagine indisponibilă</p>
                    <?php endif; ?>
                </div>
                <?php
            }
        } else {
            echo "Nu există produse în baza de date.";
        }
        ?>
            <button onclick="window.location.href='../HTML/produse.html'">Înapoi la pagina produse</button>
        </div>
    </div>
    <?php $conn->close(); ?>
</body>
</html>
