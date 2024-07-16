<?php  
session_start();


if (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] !== 'client' && $_SESSION['user_type'] !== 'firma')) {
    echo "<script>alert('Va rugam sa va autentificati');</script>";
    header('Location: ../index.php'); 
    exit;
}

// conectare la baza de date
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "solarquery";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conversatie_id = $_GET['conversatie_id'];
$sql = "
SELECT 
    m.id,
    m.continut,
    m.timestamp,
    CASE 
        WHEN m.sender_type = 'client' THEN c.nume
        WHEN m.sender_type = 'firma' THEN f.nume_firma
        ELSE 'Unknown'
    END as sender_name
FROM 
    tbl_mesaje m
LEFT JOIN 
    tbl_firme f ON m.sender_id = f.id AND m.sender_type = 'firma'
LEFT JOIN 
    tbl_adrese c ON m.sender_id = c.id_client AND m.sender_type = 'client'
WHERE 
    m.id_conversatie = ?
ORDER BY 
    m.timestamp ASC;
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $conversatie_id);
$stmt->execute();


?>





<!DOCTYPE html>
<html>
<head>
    <title>Platforma SolarQuery</title>
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
       

        <button onclick="goBack()">Mergi inapoi</button>
    </header>
    <div style="display: flex; justify-content: center;">
        <div>
           
            <?php
            $results = $stmt->get_result();
            while ($row = $results->fetch_assoc()) {
                echo "<p><strong>" . $row['sender_name'] . ":</strong> " . $row['continut'] . " - " . $row['timestamp'] . "</p>";
            }

            $stmt->close();
            ?>

            <div id="messageSection">
                <div id="messageDisplay"></div>
                <textarea id="messageInput"></textarea>
                <button onclick="sendMessage()">Trimite</button>
            </div>

            <style>
       
        
        #messageInput {
            width: 600px;
            height: 200px;
            font-size: 18px;
            padding: 10px;
        }
    </style>




            <script>
                function sendMessage() {
                    let message = document.getElementById('messageInput').value;

                    // Basic validation
                    if (message.trim() === "") {
                        alert("Nu poti trimite un mesaj gol");
                        return;
                    }

                    fetch('send_message.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'message=' + encodeURIComponent(message) + '&conversatie_id=' + <?php echo $conversatie_id; ?>
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // adauga mesajul in pagina
                            let p = document.createElement('p');
                            p.innerHTML = `<strong>You:</strong> ${message}`;
                            document.getElementById('messageDisplay').appendChild(p);

                            // curata zona de mesaj
                            document.getElementById('messageInput').value = '';
                        } else {
                            alert("Error sending message: " + data.error);
                        }
                    });
                }

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


        </div>
    </div>
