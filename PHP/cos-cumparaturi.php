<?php
session_start();





// conectare la baza de date
$host = 'localhost';
$dbname = 'solarquery';
$user = 'root';
$db_password = '';

$db = new PDO("mysql:host=$host;dbname=$dbname", $user, $db_password);




// daca utilizatorul a trimis formularul de adaugare in cos
if (isset($_POST['id_produs'], $_POST['cantitate']) && is_numeric($_POST['id_produs']) && is_numeric($_POST['cantitate'])) {
    // Set the post variables so we easily identify them, also make sure they are integer
    $id_produs = (int)$_POST['id_produs'];
    $cantitate = (int)$_POST['cantitate'];
    // Prepare the SQL statement, we basically are checking if the product exists in our database
    $stmt = $db->prepare('SELECT * FROM tbl_produse WHERE id = ?');
    $stmt->execute([$_POST['id_produs']]);
    // Fetch the product from the database and return the result as an Array
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if the product exists (array is not empty)
    if ($product && $cantitate > 0) {
        // Product exists in database, now we can create/update the session variable for the cart
        if (isset($_SESSION['cos-cumparaturi']) && is_array($_SESSION['cos-cumparaturi'])) {
            if (array_key_exists($id_produs, $_SESSION['cos-cumparaturi'])) {
                // Product exists in cart so just update the quantity
                $_SESSION['cos-cumparaturi'][$id_produs] += $cantitate;
            } else {
                // Product is not in cart so add it
                $_SESSION['cos-cumparaturi'][$id_produs] = $cantitate;
            }
        } else {
            // There are no products in cart, this will add the first product to cart
            $_SESSION['cos-cumparaturi'] = array($id_produs => $cantitate);
        }
    }
    // Prevent form resubmission...
    header('location: cos-cumparaturi.php?id=' . $id_produs);
    exit;
}

// verifica daca exista produse in cos
$products_in_cart = isset($_SESSION['cos-cumparaturi']) ? $_SESSION['cos-cumparaturi'] : array();
$products = array();
$subtotal = 0.00;
// daca sunt
if ($products_in_cart) {
    // There are products in the cart so we need to select those products from the database
    // Products in cart array to question mark string array, we need the SQL statement to include IN (?,?,?,...etc)
    $array_to_question_marks = implode(',', array_fill(0, count($products_in_cart), '?'));
    
    $stmt = $db->prepare('
    
    SELECT p.*, i.nume_imagine, v.nume_firma
    FROM tbl_produse p 
    LEFT JOIN (
        SELECT id_produs, MIN(nume_imagine) as nume_imagine
        FROM tbl_imagini
        GROUP BY id_produs
    ) i ON p.id = i.id_produs 
    LEFT JOIN tbl_firme v ON p.id_firma = v.id
    WHERE p.id IN (' . $array_to_question_marks . ') 
    ORDER BY p.id_firma');
$stmt->execute(array_keys($products_in_cart));
$products_with_images = $stmt->fetchAll(PDO::FETCH_ASSOC);

// At the top of cos-cumparaturi.php
if (isset($_GET['remove']) && is_numeric($_GET['remove']) && isset($_SESSION['cos-cumparaturi'][$_GET['remove']])) {
    // Remove the product from the cart
    unset($_SESSION['cos-cumparaturi'][$_GET['remove']]);
    // Optionally, you can redirect back to the cart to refresh the page
    header('Location: cos-cumparaturi.php');
    exit;
}

// Handle the updating of the product quantities

if(isset($_POST['update'])) {
    foreach($products_in_cart as $id => $cantitate) {
        if(isset($_POST['cantitate-' . $id]) && is_numeric($_POST['cantitate-' . $id])) {
            $_SESSION['cos-cumparaturi'][$id] = (int)$_POST['cantitate-' . $id];
        }
    }
    header('Location: cos-cumparaturi.php');
    exit;
}
  
if(isset($_POST['to-checkout'])) {
    
    header('Location: plasare-comanda.php');
    exit;
}

    

    $stmt->execute(array_keys($products_in_cart));
    // Fetch the products from the database and return the result as an Array
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Calculate the subtotal
    foreach ($products as $product) {
        $subtotal += (float)$product['pret_produs'] * (int)$products_in_cart[$product['id']];
    }
    $groupedProducts = [];

foreach ($products as $product) {
    $key = $product['id_firma'] . '-' . $product['nume_firma'];
    $groupedProducts[$key][] = $product;
}







}


?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Cos cumparaturi</title>
		<link href="../CSS/cos-cumparaturi.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
        </head>
<body>
<header>
    <h1><a href="../index.php">SolarQuery</a></h1>
        <nav>
            <ul>
                <li><a href="../index.php">Acasă</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Shop</a>
                    <div class="dropdown-content">
                        <a href="produse.php">Produse</a>
                        <a href="servicii.php">Servicii</a>
                    </div>
                </li>
                <li><a href="../despre_noi.php">Despre noi</a></li>
                <li><a href="../contact.php">Contact</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Clienti</a>
                    <div class="dropdown-content">
                        <a href="clienti-register-form.php">Înregistrare</a>
                        <a href="clienti-login-form.php">Logare</a>
                        
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Furnizori</a>
                    <div class="dropdown-content">
                        <a href="firme-register-form.php">Înregistrare</a>
                        <a href="firme-login-form.php">Logare</a>
                        
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
                        <a href="../PHP/cos-cumparaturi.php">
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
     // verificam daca inputul de cautare este gol
     if (query.trim() === '') {
        document.getElementById('search-results-container').innerHTML = ''; // eliminam continutul din container
        return; // iesim din functie
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
        
        
        
        <main>



        </main>
        
    </body>
</html>


<div class="cart content-wrapper">
    <h1>Cos cumparaturi</h1>
    
    <form action="cos-cumparaturi.php" method="post">
        <table>
            <thead>
            <tr>
            <td colspan="1" style="padding-right: 50px;"></td>    
    <td colspan="1" style="padding-right: 50px;">Produs</td>
    <td style="padding-right: 70px;"></td>
    <td style="padding-right: 50px;">Furnizor</td>
    <td style="padding-right: 50px;">Pret</td>
    <td style="padding-right: 50px;">Cantitate</td>
    <td>Total</td>
</tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                <tr>
                    <td colspan="5" style="text-align:center;">Nu aveti produse in cos</td>
                </tr>
                <?php else: ?>

                    <?php foreach ($groupedProducts as $key => $currentProducts): ?>
                    
                    
    
    

                         <?php foreach ($currentProducts as $product): ?>
                        
                <tr>
                    

               
                <tr>
    <!-- Image -->
    <td class="img">
        <a href="../PHP/produs.php?id=<?=$product['id']?>">
            <img src="../IMAGES/products/<?=$product['nume_imagine']?>" width="200" height="200" alt="<?=$product['nume_produs']?>"> 
        </a>
    </td>
    <!-- Product Name -->
    <td style="width: 150px;">
        <a href="../PHP/produs.php?id=<?=$product['id']?>"><?=$product['nume_produs']?></a>
    </td>
    <!-- Remove Button -->
    <td>
        <a href="cos-cumparaturi.php?remove=<?=$product['id']?>" class="remove">Elimina</a>
    </td>
    <!-- Furnizor -->
    <td style="padding-right: 30px; width: 150px;"><?=$product['nume_firma']?></td>

    <!-- Price -->
    <td class="price" style="width: 150px;"><?=$product['pret_produs']?><span> Lei</span></td>
    <!-- Quantity -->
    <td class="quantity">
        <input type="number" name="cantitate-<?=$product['id']?>" value="<?=$products_in_cart[$product['id']]?>" min="1" max="<?=$product['cantitate']?>" placeholder="Cantitate" required>
    </td>
    <!-- Total -->
    <td class="price" style="width: 150px;"><?=$product['pret_produs'] * $products_in_cart[$product['id']]?><span> Lei</span></td>
</tr>

                        
                
                        <?php endforeach; ?>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="subtotal">
            <span class="text">Subtotal</span>
            <span class="price"><?=$subtotal?></span><span> Lei</span>
        </div>
        <div class="buttons">
            <input type="submit" value="Actualizeaza" name="update">
    
            <input type="submit" value="Plaseaza comanda" name="to-checkout">
        </div>
    </form>
</div>


  
<footer>


<a href="https://ec.europa.eu/consumers/odr/main/index.cfm?event=main.home2.show&lng=RO">
        <img src="../IMAGES/extra/anpc-sal.webp" alt="Image Description"  />
    </a >

        <p>&copy;  2023 Platforma SolarQuery</p>
    </footer>