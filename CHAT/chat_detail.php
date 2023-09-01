<?php  
session_start();


if (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] !== 'client' && $_SESSION['user_type'] !== 'firma')) {
    echo "<script>alert('Va rugam sa va autentificati');</script>";
    header('Location: ../index.php'); 
    exit;
}

// Connect to the database
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
        <h1>Platforma SolarQuery</h1>
        <nav>
            <ul>
                <li><a href="#">Acasă</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Shop</a>
                    <div class="dropdown-content">
                        <a href="../HTML/produse.html">Produse</a>
                        <a href="../HTML/servicii.html">Servicii</a>
                    </div>
                </li>
                <li><a href="#">Despre noi</a></li>
                <li><a href="#">Contact</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Clienti</a>
                    <div class="dropdown-content">
                        <a href="../HTML/clienti-register.html">Înregistrare</a>
                        <a href="../PHP/clienti-login-form.php">Logare</a>
                        <a href="../PHP/register.html">Contul meu</a>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Firme</a>
                    <div class="dropdown-content">
                        <a href="..HTML/firme-register.html">Înregistrare</a>
                        <a href="..PHP/firme-login-form.php">Logare</a>
                        <a href="../PHP/profil_client">Contul meu</a>
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
                
            </ul>
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
                            // Add the new message to the chat
                            let p = document.createElement('p');
                            p.innerHTML = `<strong>You:</strong> ${message}`;
                            document.getElementById('messageDisplay').appendChild(p);

                            // Clear the textarea
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
        </div>
    </div>
