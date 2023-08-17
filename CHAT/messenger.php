<?php
session_start();

// Connect to the database
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "solarquery";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



$user_id = $_SESSION['user_id']; // This would be set during login
$user_type = $_SESSION['user_type']; // Either 'client' or 'firma'

if($user_type == 'client') {
    $sql = "

        SELECT 
            c.id,
            c.timestamp,
            a.nume AS nume_client,
            f.nume_firma AS nume_firma
        FROM 
            tbl_conversatii c
        LEFT JOIN
            tbl_adrese a ON c.id_client = a.id_client
        LEFT JOIN
            tbl_firme f ON c.id_firma = f.id
        WHERE 
            c.id_client = ?";
    } else {
        $sql = "
        SELECT 
            c.id,
            c.timestamp,
            a.nume AS nume_client,
            f.nume_firma AS nume_firma
        FROM 
            tbl_conversatii c
        LEFT JOIN
            tbl_adrese a ON c.id_client = a.id_client
        LEFT JOIN
            tbl_firme f ON c.id_firma = f.id
        WHERE 
            c.id_firma = ?";
    }

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
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
                        <a href="../PHP/profil_client.php">Contul meu</a>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Firme</a>
                    <div class="dropdown-content">
                        <a href="..HTML/firme-register.html">Înregistrare</a>
                        <a href="..PHP/firme-login-form.php">Logare</a>
                        <a href="../PHP/firme-dashboard.html">Contul meu</a>
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
   
    


    <?php
    
       
      
       echo "
    <div style='text-align: center; margin-top: 50px;'>
        <table border='1' cellspacing='0' cellpadding='10' align='center'>
            <thead>
                <tr>
                    <th>Conversația :</th>
                    <th>Data și ora</th>
                    <th>De la</th>
                    <th>Către</th>
                </tr>
            </thead>
            <tbody>";

$results = $stmt->get_result();
while ($row = $results->fetch_assoc()) {
    echo "
        <tr>
            <td><a href='chat_detail.php?conversatie_id=" . $row['id'] . "'>" . $row['id'] . "</a></td>
            <td>" . $row['timestamp'] . "</td>
            <td>" . $row['nume_client'] . "</td>
            <td>" . $row['nume_firma'] . "</td>
        </tr>";
}

echo '
    </tbody>
    </table>
    </div>
    <div style="position: fixed; left: 10%; top: 150px;">
        <a href="../CHAT/new_conv.php"><button>Creează o nouă conversație</button></a>
    </div>';


   
   


?>

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

  
    <footer>
        <p>© 2023 SolarQuery. Toate drepturile rezervate.</p>
