


<?php
// Start the session
session_start();



if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'client') {
    //  utilizatorul nu este  autentificat sau nu este client, redirecționează către pagina de logare
    // header('Location: clienti-login-form.php');
    header('Location: clienti-login-form.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));

    exit;
  }
  $username = $_SESSION['username'];





  ?>

  <!DOCTYPE html>
  <html>
      <head>
          <meta charset="utf-8">
          <title>Plasare comanda</title>
          <link href="../CSS/plasare-comanda.css" rel="stylesheet" type="text/css">
          <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
          </head>
  <body>
  <header>
          <h1><a href="../index.php">SolarQuery Home</a></h1>
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
                  <li><a href="#">Despre noi</a></li>
                  <li><a href="#">Contact</a></li>
                  <li class="dropdown">
                      <a href="#" class="dropbtn">Clienti</a>
                      <div class="dropdown-content">
                          <a href="HTML/clienti-register.html">Înregistrare</a>
                          <a href="PHP/clienti-login-form.php">Logare</a>
                          
                      </div>
                  </li>
                  <li class="dropdown">
                      <a href="#" class="dropbtn">Furnizori</a>
                      <div class="dropdown-content">
                          <a href="HTML/firme-register.html">Înregistrare</a>
                          <a href="PHP/firme-login-form.php">Logare</a>
                          
                      </div>
                  </li>
                  <li class="button"><a href="../ADD_RECENZIE/adaugare_recenzie.php">Lasa o recenzie</a></li>
                  <li class="search-container">
                      <!-- Formularul de căutare -->
                      <form method="GET">
                          <input type="text" class="search-box" id="search-box" name="termen_cautare" placeholder="Caută o firmă..." onkeyup="fetchResults()">
                          <div id="search-results-container"></div>
                      </form>
                  </li>

                  <div class="cart-icon-container">
                        <a href="cos-cumparaturi.php">
                             <i class="fas fa-shopping-cart"></i> 
                                    <span class="cart-item-count">
                            <?php 
                        if(isset($_SESSION['cos-cumparaturi']) && is_array($_SESSION['cos-cumparaturi'])) {
                            echo array_sum($_SESSION['cos-cumparaturi']); 
                        } else {
                            echo 0;
                        }
                            ?>
                            </span>
                        </a>
                    </div>
 
              </ul>
             
              <div>                 
<?php if(isset($_SESSION['user_id'])): ?>
  <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'client'): ?>
    <a href="../PHP/profil_client.php" class="account-button">My Account</a>
    <a href="../PHP/logout.php" class="logout-button">Logout</a>
  <?php elseif (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'firma'): ?>  
    <a href="../PHP/firme-dashboard.php" class="account-button">My Account</a>
    <a href="../PHP/logout.php" class="logout-button">Logout</a>
  <?php endif; ?>
<?php endif; ?>
</div>     
            
    
    
  
          </nav>
  
          
  
      </header>
  
  <!-- script search box -->
      <script>
      function fetchResults() {
          let query = document.getElementById('search-box').value;
       // Check if the query is empty or not
       if (query.trim() === '') {
          document.getElementById('search-results-container').innerHTML = ''; // Clear any previous results
          return; // Exit the function early
      }
          // Efectuăm un request AJAX către scriptul PHP
          fetch('../ADD_RECENZIE/cauta_firma.php?query=' + query)
          .then(response => response.json())
          .then(data => {
              let resultsContainer = document.getElementById('search-results-container');
              resultsContainer.innerHTML = ''; // Resetează containerul
      
              if (data.length > 0) {
                  data.forEach(firma => {
                      let firmaDiv = document.createElement('div');
                      let firmaLink = document.createElement('a');
                      firmaLink.href = '../PHP/profil_firma_copy.php?id=' + firma.id;
                      firmaLink.textContent = firma.nume_firma;
      
                      firmaDiv.appendChild(firmaLink);
                      resultsContainer.appendChild(firmaDiv);
                  });
              } else {
                  resultsContainer.innerHTML = 'Niciun rezultat găsit.';
              }
          })
          .catch(error => console.error('Error:', error));
      }
      
  
      </script>



<?php



// verificare cos 
if (empty($_SESSION['cos-cumparaturi'])) {
    echo 'Cosul de cumparaturi este gol!';
} else {
    // conectare la baza de date
    $host = 'localhost';
    $dbname = 'solarquery';
    $user = 'root';
    $db_password = '';

    $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $db_password);

    // aducem produsele din baza de date pe baza id urilor din cos
    
    $product_ids = array_keys($_SESSION['cos-cumparaturi']);
    $stmt = $db->prepare('
    SELECT  p.*,
    (
        SELECT i.nume_imagine 
        FROM tbl_imagini i 
        WHERE p.id = i.id_produs 
        LIMIT 1
    ) AS imagine,
    f.nume_firma
FROM tbl_produse p
LEFT JOIN tbl_firme f ON p.id_firma = f.id
WHERE p.id IN (' . implode(',', $product_ids) . ')');


    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $groupedByfurnizor = [];

        foreach ($products as $product) {
    $groupedByfurnizor[$product['id_firma']][] = $product;
    }
}




?>

    <!-- afiseaza produsele din cos -->
    <div class="cart content-wrapper">
    <h1>Plasare comanda</h1>
    <form method="post">
        <table>
            <thead>
                <tr>
                    <td colspan="1"></td>
                    <td style="padding-right: 80px;">Produs</td>
                    <td style="padding-right: 80px;">Furnizor</td>
                    <td style="padding-right: 80px;">Pret</td>
                    <td style="padding-right: 80px;">Cantitate</td>
                    <td style="padding-right: 80px;">Total</td>
                </tr>
            </thead>
            <tbody>
                <!-- trecem prin fiecare produs din cos-->
                <?php foreach ($groupedByfurnizor as $furnizorId => $furnizorProducts): ?>
                    <?php foreach ($furnizorProducts as $product): ?>
                        <tr>
                            <td><img src="../IMAGES/products/<?=$product['imagine']?>" width="300" height="300" alt="<?=$product['nume_produs']?>"></td>
                            <td style="width: 150px;"><?=$product['nume_produs']?></td>

                            <td style="padding-right: 30px;width: 150px;"><?=$product['nume_firma']?></td>

                            <td style="width: 150px;"><?=$product['pret_produs']?> Lei</td>

                            <td><?=$_SESSION['cos-cumparaturi'][$product['id']]?></td>

                            <td style="width: 150px;"><?=$product['pret_produs'] * $_SESSION['cos-cumparaturi'][$product['id']]?> Lei</td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        <!-- Rest of your order form elements -->
    </form>
</div>

<?php

// verifica daca utilizatorul este logat
if (isset($_SESSION['user_id'])) {
    // preia datele din baza de date
    $user_id = $_SESSION['user_id'];
    $stmt = $db->prepare('SELECT * FROM tbl_adrese WHERE id_client = :user_id');
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $address = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt = $db->prepare('SELECT persoana_juridica FROM tbl_clienti WHERE id = :user_id');
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    $persoana_juridica = $userData['persoana_juridica'];


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
        else {
        // seteaza variabilele de sesiune cu valori goale
        $tara = '';
        $judet = '';
        $localitate = '';
        $adresa = '';
        $cod_postal = '';
        $telefon = '';
        $nume = '';


    } 
    // afiseaza mesajul de mai jos daca adresa a fost incarcata din baza de date
    if (!empty($id_adresa) && !empty($tara) && !empty($judet) && !empty($localitate) && !empty($adresa) && !empty($cod_postal) && !empty($telefon) && !empty($nume)) {
        echo '<p>Adresa a fost incarcata din baza de date. Daca doriti sa o modificati, va rugam sa mergeti la pagina de profil.</p>';
    }

    

}

// afiseaza mesajul de mai jos daca adresa nu a fost gasita in baza de date
if (empty($id_adresa) ||  empty($tara) || empty($judet) || empty($localitate) || empty($adresa) || empty($cod_postal) || empty($telefon) || empty($nume)) {
    echo '<p>Adresa nu a fost gasita in baza de date. Va rugam sa mergeti la <a href="profil_client.php?username=' . $username . '">pagina de profil</a> pentru a o adauga.</p>';
}





















?>
<div class="outer">
    <form method="post">
        <div>
            <label for="metoda_plata">Metoda plata:</label>
            <select name="metoda_plata" id="metoda_plata">
                <option value="cash" selected>Cash</option>
                <option value="card" disabled>Card (Coming Soon)</option>
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

        <?php
       
if($persoana_juridica) { // daca poate fi facutrata ca persoana juridica
    echo '<div>
            <label for="tip_facturare">Tip facturare:</label>
            <select name="tip_facturare" id="tip_facturare">
                <option value="persoana_fizica">Persoana Fizica</option>
                <option value="persoana_juridica">Persoana Juridica</option>
            </select>
        </div>';
} else { // daca poate fi facturat doar ca persona fizica
    echo '<input type="hidden" name="tip_facturare" value="persoana_fizica">';
}
?>






        <input type="submit" name="placeorder" value="Plaseaza comanda">
 
       
    
    
    </form>
</div>

<?php
if (isset($_POST['placeorder'])) {
    // preia datele din formular
    $metoda_plata = $_POST['metoda_plata'];
    $tara = $_POST['tara'];
    $judet = $_POST['judet'];
    $localitate = $_POST['localitate'];
    $adresa = $_POST['adresa'];
    $cod_postal = $_POST['cod_postal'];
    $telefon = $_POST['telefon'];
    $nume = $_POST['nume'];
    $tip_facturare = $_POST['tip_facturare'];

    foreach ($groupedByfurnizor as $furnizorId => $furnizorProducts) {
        $total_price = 0;

        foreach ($furnizorProducts as $product) {
            $id_produs = $product['id'];
            $cantitate = $_SESSION['cos-cumparaturi'][$id_produs];
            $pret_produs = $product['pret_produs'];
            $total_price += $cantitate * $pret_produs;
        }

   

     // Insert the order into the database for each furnizorId
     $stmt = $db->prepare('INSERT INTO tbl_comenzi (id_client, id_firma, data_comanda, id_adresa_livrare, status_comanda, metoda_plata, total_de_plata, tip_facturare) VALUES (:id_client, :id_firma, NOW(), :id_adresa_livrare, "neprocesata", :metoda_plata, :total_de_plata, :tip_facturare)');
     $stmt->bindParam(':id_client', $user_id);
     $stmt->bindParam(':id_firma', $furnizorId);
     $stmt->bindParam(':id_adresa_livrare', $id_adresa);
     $stmt->bindParam(':metoda_plata', $metoda_plata);
     $stmt->bindParam(':total_de_plata', $total_price);
     $stmt->bindParam(':tip_facturare', $tip_facturare);
     $stmt->execute();
     $id_comanda = $db->lastInsertId();


     $id_comanda_array[] = $id_comanda;

     // insereaza produsele din cos in tabela tbl_produse_comanda pentru fiecare furnizor_id
     foreach ($furnizorProducts as $product) {
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
 }
//curata cosul de cumparaturi
 $_SESSION['cos-cumparaturi'] = array();


    // redirectioneaza catre pagina de confirmare comanda
   $id_comanda_query_string = implode(',', $id_comanda_array);
   echo '<meta http-equiv="refresh" content="0;url=confirmare-comanda.php?ids=' . $id_comanda_query_string . '">';

    exit;
}
