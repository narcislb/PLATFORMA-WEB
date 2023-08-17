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
    }  else {
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


     <!-- Second search box -->
     <div style="text-align: center; margin-top: 50px;">
        <strong>Incepe o noua conversatie:</strong>
        <div class="search-container">
            <form method="GET">
                <input type="text" class="search-box" id="search-box-2" name="termen_cautare" placeholder="Caută..." onkeyup="fetchResults_simple()">
                <div id="search-results-container-2"></div>
            </form>
        </div>
    </div>
   
    <form id="startChatForm">
        <input type="hidden" id="selectedFirmaId" name="id_firma">

        <textarea id="initialMessage" placeholder="Scrie mesajul tău aici..."></textarea>
        <button type="button" onclick="startChat()">Trimite mesaj</button>
        <div id="messageStatus"></div>
    </form>



    <?php

echo "
            </tbody>
        </table>
    </div>
    <div style='position: absolute; left: 10%; top: 150px;'>
    <a href='../CHAT/messenger.php'><button>Messenger</button></a>
    </div>";
   
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
 


 <script>

function fetchResults_simple() {

    let query = document.getElementById('search-box-2').value;

    // Check if the query is empty or not
    if (query.trim() === '') {
        document.getElementById('search-results-container-2').innerHTML = ''; // Clear any previous results
        return; // Exit the function early
    }
    
    // Make an AJAX request to the PHP script
    fetch('../ADD_RECENZIE/cauta_firma.php?query=' + query)
    .then(response => response.json())
    .then(data => {
        let resultsContainer = document.getElementById('search-results-container-2');
        resultsContainer.innerHTML = ''; // Reset the container

        if (data.length > 0) {
            data.forEach(firma => {
                let firmaDiv = document.createElement('div');
                
                firmaDiv.textContent = firma.nume_firma;
                
                // On click, populate the search bar with the firm's name and hide the results
                firmaDiv.onclick = function() {
                    document.getElementById('search-box-2').value = firma.nume_firma;
                    resultsContainer.innerHTML = ''; 
                 };
                
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
let userId = <?php echo $_SESSION['user_id']; ?>;
</script>


<script>
function startChat() {
    let firmaId = document.getElementById('selectedFirmaId').value;
    if (!firmaId) {
        document.getElementById('messageStatus').textContent = 'Selectați o firmă întâi.';
        return;
    }
    
    let formData = new FormData();
    formData.append('id_firma', firmaId); // Use the updated variable
    
    let message = document.getElementById('initialMessage').value;
    if (message.trim() === '') {
        document.getElementById('messageStatus').textContent = 'Mesajul nu poate fi gol.';
        return;
    }
    
    formData.append('id_client', <?php echo $_SESSION['user_id']; ?>);
    formData.append('message', message);

    fetch('../CHAT/send_initial_message.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('messageStatus').textContent = 'Mesaj trimis cu succes!';
            document.getElementById('initialMessage').value = ''; // Clear the textarea
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
    #search-results-container-2 div {
    cursor: pointer;
}
</style>


    <footer>
        <p>© 2023 SolarQuery. Toate drepturile rezervate.</p>



        </body>
</html>