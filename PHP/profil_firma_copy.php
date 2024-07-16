


<?php
session_start();

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "solarquery";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// verifica daca exista id-ul firmei in URL
if(!isset($_GET['id'])) {
    die("ID-ul firmei lipsește.");
}

$id_firma = $_GET['id'];
$firma_details = [];
$recenzii = [];
$_SESSION['user_id']=1;

$sql = "SELECT * FROM tbl_firme WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_firma);
$stmt->execute();

$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $firma_details = $result->fetch_assoc();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Profil Firma - <?php echo $firma_details['nume_firma']; ?></title>
    <link rel="stylesheet" href="../CSS/style.css">
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
        <!-- <button onclick="goBack()">Mergi inapoi</button> -->
    </header>

    <main>
        <h1>Profil Firma</h1>
        <h1><?php echo $firma_details['nume_firma']; ?></h1>
        <p><strong>Descriere:</strong> <?php echo $firma_details['services']; ?></p>
        <!-- Zona -->
        
        <p><strong>Zona in care ofera servicii:</strong> <?php echo $firma_details['zona']; ?></p>
        
        <h2>Recenzii:</h2>

<div id="recenzie-display">Asteapta recenzia...</div>
<button onclick="previousRecenzie()">Anterior</button>
<button onclick="nextRecenzie()">Următor</button>


<h2>Date de contact:</h2>

<p><strong>Persoana de contact:</strong> <?php echo htmlspecialchars($firma_details['persoana_de_contact']); ?></p>
<p><strong>Email:</strong> <?php echo htmlspecialchars($firma_details['email']); ?></p>
<p><strong>Telefon:</strong> <?php echo htmlspecialchars($firma_details['telefon']); ?></p>





        <h2>Portofoliu furnizor:</h2>

        
        <?php


$offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;  // We use an offset to determine which portfolio to display.

// Fetch the current portofoliu based on offset
$query = "SELECT * FROM tbl_portofoliu WHERE id_firma = ? LIMIT 1 OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $id_firma, $offset);
$stmt->execute();
$current_portofoliu = $stmt->get_result()->fetch_assoc();

if (empty($current_portofoliu)) {
    $hasPortofoliu = false;
} else {
    $hasPortofoliu = true;
}

// Fetch associated images
$query = "SELECT * FROM tbl_imagini_portofoliu WHERE id_descriere = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $current_portofoliu['id']);
$stmt->execute();
$images = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Check if there's a next portofoliu entry
$query = "SELECT * FROM tbl_portofoliu WHERE id_firma = ? LIMIT 1 OFFSET ?";
$stmt = $conn->prepare($query);
$offset_plus_one = $offset + 1;
$stmt->bind_param("ii", $id_firma, $offset_plus_one);
$stmt->execute();
$next_portofoliu = $stmt->get_result()->fetch_assoc();

$stmt->close();

?>

<!-- HTML part -->

<?php if ($hasPortofoliu): ?>

<h2>Portofoliu cu lucrari : </h2>
<h2><?php echo $current_portofoliu['titlu']; ?></h2>
<h2><?php echo $current_portofoliu['descriere']; ?></h2>

<!-- Display images -->
<?php foreach ($images as $image): ?>
    <img src="../IMAGES/portofoliu/<?php echo $image['nume_imagine']; ?>" alt="" width="300" height="300" style="object-fit: contain;">
<?php endforeach; ?>

<!-- Previous button -->
<?php if ($offset > 0): ?>
    <a href="portofoliu_furnizor.php?offset=<?php echo $offset - 1; ?>">Previous</a>
<?php endif; ?>


<!-- Next button -->
<?php if ($next_portofoliu): ?>
    <a href="portofoliu_furnizor.php?offset=<?php echo $offset + 1; ?>">Next</a>
<?php endif; ?>

<?php else: ?>

<h3>Nu exista portofoliu</h3>

<?php endif; ?>


<!-- Assuming you are inside PHP and have access to the business's ID -->
<h2>Trimite un mesaj catre aceasta firma:</h2>
<form id="startChatForm">
    
    <input type="text" id="subiect" placeholder="Subiect:">
    <textarea id="initialMessage" placeholder="Scrie mesajul tău aici..."></textarea>
    <button type="button" onclick="startChat()">Trimite mesaj</button>
    <div id="messageStatus"></div>
</form>



    </main>



    <style>
        #subiect {
            width: 400px;
            height: 40px;
            font-size: 18px;
            padding: 10px;
        }
        
        #initialMessage {
            width: 400px;
            height: 200px;
            font-size: 18px;
            padding: 10px;
        }
    </style>




    <footer>


<a href="https://ec.europa.eu/consumers/odr/main/index.cfm?event=main.home2.show&lng=RO">
        <img src="../IMAGES/extra/anpc-sal.webp" alt="Image Description"  />
    </a >

        <p>&copy;  2023 Platforma SolarQuery</p>
    </footer>


</body>
</html>

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



<script>
let currentRecenzieIndex = 0;
let recenzii = [];



function fetchRecenzii(id_firma) {
    fetch(`../PHP/get_recenzii.php?id_firma=${id_firma}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            recenzii = data.recenzii;
            displayRecenzie();
        } else {
            document.getElementById('recenzie-display').textContent = 'Nu există recenzii.';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('recenzie-display').textContent = 'Eroare la obținerea recenziei.';
    });
}

function displayRecenzie() {
    if (recenzii.length > 0) {
        document.getElementById('recenzie-display').textContent = recenzii[currentRecenzieIndex];
    } else {
        document.getElementById('recenzie-display').textContent = 'Nu există recenzii.';
    }
}

function nextRecenzie() {
    if (currentRecenzieIndex < recenzii.length - 1) {
        currentRecenzieIndex++;
        displayRecenzie();
    }
}

function previousRecenzie() {
    if (currentRecenzieIndex > 0) {
        currentRecenzieIndex--;
        displayRecenzie();
    }
}
fetchRecenzii(<?php echo $id_firma; ?>);
</script>


<script>
function startChat() {
    let message = document.getElementById('initialMessage').value;
    if (message.trim() === '') {
        document.getElementById('messageStatus').textContent = 'Mesajul nu poate fi gol.';
        return;
    }
    let subiect = document.getElementById('subiect').value;
    let formData = new FormData();
    formData.append('id_firma', <?php echo $id_firma; ?>);
    formData.append('id_client', <?php echo $_SESSION['user_id']; ?>);
    formData.append('message', message);
    //subiect
    formData.append('subiect', subiect);

    fetch('../CHAT/send_initial_message.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('messageStatus').textContent = 'Mesaj trimis cu succes!';
            document.getElementById('initialMessage').value = ''; // Clear the textarea
            document.getElementById('subiect').value = '';     
        } else {
            document.getElementById('messageStatus').textContent = 'Eroare la trimiterea mesajului.Daca nu sunteti autentificat va rugam sa va autentificati';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('messageStatus').textContent = 'Eroare la trimiterea mesajului. Încercați din nou.';
    });
}

</script>

<script>
    function goBack() {
    window.history.back();
}
</script>


<style>
.account-button {
    background-color: #4CAF50; /* Green background */
    border: none; /* Remove border */
    color: white; /* White text */
    padding: 12px 24px; /* Some padding */
    text-align: center; /* Center text */
    text-decoration: none; /* Remove underline */
    display: inline-block; /* Make it a block element */
    font-size: 16px; /* Increase font size */
    margin-right: 10px; /* Add some margin to the right */
  }
  
  .logout-button {
    background-color: #f44336; /* Red background */
    border: none; /* Remove border */
    color: white; /* White text */
    padding: 12px 24px; /* Some padding */
    text-align: center; /* Center text */
    text-decoration: none; /* Remove underline */
    display: inline-block; /* Make it a block element */
    font-size: 16px; /* Increase font size */
  }
  
    </style>
