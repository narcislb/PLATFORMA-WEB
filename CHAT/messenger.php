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


$user_id = $_SESSION['user_id']; // va fi setat in momentul in care utilizatorul se logheaza
$user_type = $_SESSION['user_type']; // poate fi client sau firma

if($user_type == 'client') { // daca utilizatorul este client, vom afisa conversatiile cu firmele cu care a discutat pana acum 
    $sql = "

        SELECT 
            c.id,
            c.timestamp,
            c.subiect,
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
    } else {// daca utilizatorul este firma, vom afisa conversatiile cu clientii cu care a discutat pana acum
        $sql = "
        SELECT 
            c.id,
            c.timestamp,
            c.subiect,
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
    <link rel="stylesheet" href="../CSS/messenger.css">
    
    
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
                        <a href="../PHP/produse.php">Produse</a>
                        <a href="../php/servicii.php">Servicii</a>
                    </div>
                </li>
                <li><a href="../despre_noi.php">Despre noi</a></li>
                <li><a href="../contact.php">Contact</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Clienti</a>
                    <div class="dropdown-content">
                        <a href="../php/clienti-register-form.php">Înregistrare</a>
                        <a href="../php/clienti-login-form.php">Logare</a>
                        
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Furnizori</a>
                    <div class="dropdown-content">
                        <a href="../php/firme-register-form.php">Înregistrare</a>
                        <a href="../php/firme-login-form.php">Logare</a>
                        
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
  <?php if($_SESSION['user_type'] == 'client'): ?>
    <a href="../PHP/profil_client.php" class="account-button">My Account</a>
    <a href="../PHP/logout.php" class="logout-button">Logout</a>
  <?php elseif ($_SESSION['user_type'] == 'firma'): ?>  
    <a href="../PHP/firme-dashboard.php" class="account-button">My Account</a>
    <a href="../PHP/logout.php" class="logout-button">Logout</a>
  <?php endif; ?>
<?php endif; ?>
  </div>   


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
                    <th>Subiect :</th>   
                    
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
            <td>" . $row['subiect'] . "</td>
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
    function goBack() {
        window.history.back();
    }
</script>



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


  
<footer>


<a href="https://ec.europa.eu/consumers/odr/main/index.cfm?event=main.home2.show&lng=RO">
        <img src="../IMAGES/extra/anpc-sal.webp" alt="Image Description"  />
    </a >

        <p>&copy;  2023 Platforma SolarQuery</p>
    </footer>

    </html>
