<?php
// Start the session
session_start();



if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'client') {
    //  utilizatorul nu este  autentificat sau nu este client, redirecționează către pagina de logare
    header('Location: clienti-login-form.php');
    exit;
  }
  $username = $_SESSION['username'];
// verifică dacă utilizatorul este deja autentificat si daca este client
if (empty($_SESSION['cos-cumparaturi'])) {
    echo 'Cosul de cumparaturi este gol!';
} else {
    // Connect to the database
    $host = 'localhost';
    $dbname = 'solarquery';
    $user = 'root';
    $db_password = '';

    $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $db_password);

    // incarca produsele din cos din baza de date 
    $product_ids = array_keys($_SESSION['cos-cumparaturi']);
    $stmt = $db->prepare('SELECT p.*, i.nume_imagine AS imagine FROM tbl_produse p LEFT JOIN tbl_imagini i ON p.id = i.id_produs WHERE p.id IN (' . implode(',', $product_ids) . ')');
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // afiseaza produsele din cos
    foreach ($products as $product) {
        $id_produs = $product['id'];
        $cantitate = $_SESSION['cos-cumparaturi'][$id_produs];
        ?>
        <div class="product">
            <img src="../IMAGES/products/<?=$product['imagine']?>" width="100" height="100" alt="<?=$product['nume_produs']?>">
            <div>
                <h3 class="name"><?=$product['nume_produs']?></h3>
                <div class="price">
                    &dollar;<?=$product['pret_produs']?> x <?=$cantitate?>
                </div>
            </div>
        </div>
        <?php
    }
}

// verifică dacă utilizatorul este deja autentificat si daca este client
if (isset($_SESSION['user_id'])) {
    // Retrieve the user's address from the database
    $user_id = $_SESSION['user_id'];
    $stmt = $db->prepare('SELECT * FROM tbl_adrese WHERE id_client = :user_id');
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $address = $stmt->fetch(PDO::FETCH_ASSOC);

    // seteaza variabilele de sesiune cu datele din baza de date
    if (!empty($address)) {
    $id_adresa = $address['id_adresa'];
    $tara = $address['tara'];
    $judet = $address['judet'];
    $localitate = $address['localitate'];
    $adresa = $address['adresa'];
    $cod_postal = $address['cod_postal'];
    $telefon = $address['telefon'];
    $nume = $address['nume'];
    
    }
    // afiseaza mesajul de mai jos daca adresa a fost incarcata din baza de date
    if (!empty($id_adresa) && !empty($tara) && !empty($judet) && !empty($localitate) && !empty($adresa) && !empty($cod_postal) && !empty($telefon) && !empty($nume)) {
        echo '<p>Adresa a fost incarcata din baza de date. Daca doriti sa o modificati, va rugam sa mergeti la pagina de profil.</p>';
    }

    else {
        // setea variabilele de sesiune cu valori goale
        $tara = '';
        $judet = '';
        $localitate = '';
        $adresa = '';
        $cod_postal = '';
        $telefon = '';
        $nume = '';


    } 

}

// afiseaza mesajul de mai jos daca adresa nu a fost gasita in baza de date
if (empty($id_adresa) ||  empty($tara) || empty($judet) || empty($localitate) || empty($adresa) || empty($cod_postal) || empty($telefon) || empty($nume)) {
    echo '<p>Adresa nu a fost gasita in baza de date. Va rugam sa mergeti la <a href="profil_client.php?username=' . $username . '">pagina de profil</a> pentru a o adauga.</p>';
}

?>
<form method="post">
        <div>
            <label for="metoda_plata">Metoda de plata:</label>
            <select name="metoda_plata" id="metoda_plata">
                <option value="card">Card</option>
                <option value="cash">Cash</option>
            </select>
        </div>
        <div>
            <label for="tara">Tara:</label>
            <input type="text" name="tara" id="tara" value="<?php echo htmlspecialchars($tara); ?>" readonly>
        </div>
        <div>
            <label for="judet">Judet:</label>
            <input type="text" name="judet" id="judet" value="<?php echo htmlspecialchars($judet); ?>" readonly>
        </div>
        <div>
            <label for="localitate">Localitate:</label>
            <input type="text" name="localitate" id="localitate" value="<?php echo htmlspecialchars($localitate); ?>" readonly>
        </div>
        <div>
            <label for="adresa">Adresa:</label>
            <input type="text" name="adresa" id="adresa" value="<?php echo htmlspecialchars($adresa); ?>" readonly>
        </div>
        <div>
            <label for="cod_postal">Cod postal:</label>
            <input type="text" name="cod_postal" id="cod_postal" value="<?php echo htmlspecialchars($cod_postal); ?>" readonly>
        </div>
        <div>
            <label for="telefon">Telefon:</label>
            <input type="text" name="telefon" id="telefon" value="<?php echo htmlspecialchars($telefon); ?>" readonly>
        </div>
        <div>
            <label for="nume">Nume:</label>
            <input type="text" name="nume" id="nume" value="<?php echo htmlspecialchars($nume); ?>" readonly>
        </div>
        <input type="submit" name="placeorder" value="Plaseaza comanda">
    </form>

<?php
if (isset($_POST['placeorder'])) {
    // Get the form data
    $metoda_plata = $_POST['metoda_plata'];
    $tara = $_POST['tara'];
    $judet = $_POST['judet'];
    $localitate = $_POST['localitate'];
    $adresa = $_POST['adresa'];
    $cod_postal = $_POST['cod_postal'];
    $telefon = $_POST['telefon'];
    $nume = $_POST['nume'];



    // Calculate the total price of the order
    $total_price = 0;
    foreach ($products as $product) {
        $id_produs = $product['id'];
        $cantitate = $_SESSION['cos-cumparaturi'][$id_produs];
        $pret_produs = $product['pret_produs'];
        $total_price += $cantitate * $pret_produs;
    }

    // insereaza comanda in baza de date
    $stmt = $db->prepare('INSERT INTO tbl_comenzi (id_client, data_comanda, id_adresa_livrare, status_comanda, metoda_plata, total_de_plata) VALUES (:id_client, NOW(), :id_adresa_livrare, "neprocesata", :metoda_plata, :total_de_plata)');
    $stmt->bindParam(':id_client', $user_id);
    $stmt->bindParam(':id_adresa_livrare', $id_adresa);
    $stmt->bindParam(':metoda_plata', $metoda_plata);
    $stmt->bindParam(':total_de_plata', $total_price);
    $stmt->execute();
    $id_comanda = $db->lastInsertId();

    // insereaza produsele comenzii respective in baza de date 
    foreach ($products as $product) {
        $id_produs = $product['id'];
        $cantitate = $_SESSION['cos-cumparaturi'][$id_produs];
        $pret_produs = $product['pret_produs'];
        $stmt = $db->prepare('INSERT INTO tbl_produse_comanda (id_comanda, id_produs, cantitate, pret_produs) VALUES (:id_comanda, :id_produs, :cantitate, :pret_produs)');
        $stmt->bindParam(':id_comanda', $id_comanda);
        $stmt->bindParam(':id_produs', $id_produs);
        $stmt->bindParam(':cantitate', $cantitate);
        $stmt->bindParam(':pret_produs', $pret_produs);
        $stmt->execute();
    }

    // redirectioneaza catre pagina de confirmare a comenzii
    //header('Location: confirmare-comanda.php?id=' . $order_id);
    exit;
}
?>