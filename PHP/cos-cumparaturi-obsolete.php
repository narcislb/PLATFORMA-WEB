<?php
session_start();
// conectare la baza de date
$host = 'localhost';
$dbname = 'solarquery';
$user = 'root';
$db_password = '';

$db = new PDO("mysql:host=$host;dbname=$dbname", $user, $db_password);
// If the user clicked the add to cart button on the product page we can check for the form data
if (isset($_POST['id_produs'], $_POST['cantitate']) && is_numeric($_POST['id_produs']) && is_numeric($_POST['cantitate'])) {
    // Set the post variables so we easily identify them, also make sure they are integer
    $id_produs = (int)$_POST['id_produs'];
    $cantitate = (int)$_POST['cantitate'];
    // Prepare the SQL statement, we basically are checking if the product exists in our databaser
    $stmt = $db->prepare('SELECT * FROM tbl_produse WHERE id = ?');
    $stmt->execute([$_POST['id_produs']]);
    // Fetch the product from the database and return the result as an Array
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if the product exists (array is not empty)
    if ($product && $cantitate > 0) {
        // Product exists in database, now we can create/update the session variable for the cart
        if (isset($_SESSION['cos-cumparaturi']) && is_array($_SESSION[' cos-cumparaturi '])) {
            if (array_key_exists($id_produs, $_SESSION['cos-cumparaturi'])) {
                // Product exists in cart so just update the quanity
                $_SESSION['cos-cumparaturi'][$id_produs] += $cantitate;
            } else {
                // Product is not in cart so add it
                $_SESSION['cos-cumparaturi '][$id_produs] = $cantitate;
            }
        } else {
            // There are no products in cart, this will add the first product to cart
            $_SESSION['cos-cumparaturi    '] = array($id_produs => $cantitate);
        }
    }
    // Prevent form resubmission...
    header('location: cos-cumparaturi.php?id=' . $id_produs);
    exit;
}

// Check the session variable for products in cart
$products_in_cart = isset($_SESSION['cos-cumparaturi']) ? $_SESSION['cos-cumparaturi'] : array();
$products = array();
$subtotal = 0.00;
// If there are products in cart
if ($products_in_cart) {
    // There are products in the cart so we need to select those products from the database
    // Products in cart array to question mark string array, we need the SQL statement to include IN (?,?,?,...etc)
    $array_to_question_marks = implode(',', array_fill(0, count($products_in_cart), '?'));
    $stmt = $db->prepare('SELECT p.*, i.nume_imagine FROM tbl_produse p LEFT JOIN tbl_imagini i ON p.id = i.id_produs WHERE p.id IN (' . $array_to_question_marks . ')');
    // $stmt = $db->prepare('SELECT * FROM tbl_produse WHERE id IN (' . $array_to_question_marks . ')');
    // We only need the array keys, not the values, the keys are the id's of the products
    $stmt->execute(array_keys($products_in_cart));
    // Fetch the products from the database and return the result as an Array
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Calculate the subtotal
    foreach ($products as $product) {
        $subtotal += (float)$product['pret_produs'] * (int)$products_in_cart[$product['id']];
    }
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>$cos-cumparaturi</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	<body>
        <header>
            <div class="content-wrapper">
                <h1>Shopping Cart System</h1>
                <nav>
                    <a href="index.php">Home</a>
                    <a href="index.php?page=products">Products</a>
                </nav>
                <div class="link-icons">
                    <a href="index.php?page=cart">
						<i class="fas fa-shopping-cart"></i>
					</a>
                </div>
            </div>
        </header>
        <main>



        </main>
        <footer>
            <div class="content-wrapper">
                <p>&copy; $year, Shopping Cart System</p>
            </div>
        </footer>
    </body>
</html>


<div class="cart content-wrapper">
    <h1>Cos cumparaturi</h1>
    <form action="plasare-comanda.php" method="post">
        <table>
            <thead>
                <tr>
                    <td colspan="2">Produs</td>
                    <td>Pret</td>
                    <td>Cantitate</td>
                    <td>Total</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                <tr>
                    <td colspan="5" style="text-align:center;">Nu aveti produse in cos</td>
                </tr>
                <?php else: ?>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td class="img">
                        <a href="index.php?page=product&id=<?=$product['id']?>">
                        <img src="../IMAGES/products/<?=$product['nume_imagine']?>" width="200" height="200" alt="<?=$product['nume_produs']?>">
                        </a>
                    </td>
                    <td>
                        <a href="index.php?page=product&id=<?=$product['id']?>"><?=$product['nume_produs']?></a>
                        <br>
                        <a href="index.php?page=cart&remove=<?=$product['id']?>" class="remove">Elimina</a>
                    </td>
                    <td class="price">&dollar;<?=$product['pret_produs']?></td>
                    <td class="quantity">
                        <input type="number" name="quantity-<?=$product['id']?>" value="<?=$products_in_cart[$product['id']]?>" min="1" max="<?=$product['cantitate']?>" placeholder="Cantitate" required>
                    </td>
                    <td class="price">&dollar;<?=$product['pret_produs'] * $products_in_cart[$product['id']]?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="subtotal">
            <span class="text">Subtotal</span>
            <span class="price">&dollar;<?=$subtotal?></span>
        </div>
        <div class="buttons">
            <input type="submit" value="Actualizeaza" name="update">
    
            <input type="submit" value="Plaseaza comanda" name="placeorder">
        </div>
    </form>
</div>