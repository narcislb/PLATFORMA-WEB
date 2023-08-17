<?php
// pornește o sesiune
session_start();

// conectare la baza de date
$host = 'localhost';
$dbname = 'solarquery';
$user = 'root';
$db_password = '';

$db = new PDO("mysql:host=$host;dbname=$dbname", $user, $db_password);

// verifică dacă formularul a fost trimis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // preia datele din formular
    $id_produs = $_POST['id_produs'];
    $cantitate = $_POST['cantitate'];

    // validare formular - verifică dacă cantitatea este mai mare decât 0
    if (isset($_SESSION['cos-cumparaturi'][$id_produs])) {
        // Adăugăm cantitatea la produsul existent în coș
        $_SESSION['cos-cumparaturi'][$id_produs] += $cantitate;
    } else {
        // Adăugăm produsul în coș
        $_SESSION['cos-cumparaturi'][$id_produs] = $cantitate;
    }

    // redirecționează către pagina de produs
    header('Location: produs.php?id=' . $id_produs);
    exit;
}

// verifică dacă există un id de produs
if (isset($_GET['id'])) {
    // caută produsul în baza de date folosind id-ul produsului și returnează rezultatul 
    $stmt = $db->prepare('SELECT p.*, i.nume_imagine AS imagine FROM tbl_produse p LEFT JOIN tbl_imagini i ON p.id = i.id_produs WHERE p.id  = ?');
    $stmt->execute([$_GET['id']]);
    // returnează primul rând din rezultat ca un array asociativ 
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    // verifică dacă produsul există (array-ul nu este gol)
    if (!$product) {
        // Eroare simplă de afișat dacă id-ul produsului nu există (array-ul este gol)
        exit('Produsul nu exista!');
    }
} else {
    // afiseaza o eroare1 dacă nu există id de produs în URL
    exit('Produsul nu exista!');
}
?>
<!DOCTYPE html>
<html>
<div class="product content-wrapper">
    <img src="../IMAGES/products/<?=$product['imagine']?>" width="200" height="200" alt="<?=$product['nume_produs']?>">
    <div>
        <h1 class="name"><?=$product['nume_produs']?></h1>
        <span class="price">
            &dollar;<?=$product['pret_produs']?>
            
        </span>
        <form  method="post">
            <input type="number" name="cantitate" value="1" min="1" max="<?=$product['cantitate']?>" placeholder="Cantitate" required>
            <input type="hidden" name="id_produs" value="<?=$product['id']?>">
            <input type="submit" value="Adauga in cos">
        </form>
        <div class="description">
            <?=$product['descriere_produs']?>
        </div>
    </div>
</div>
</html>

