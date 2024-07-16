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



$user_id = $_SESSION['user_id']; // This would be set during login
$user_type = $_SESSION['user_type']; // Either 'client' or 'firma'

if($user_type == 'client') {
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
    }  else {
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

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    



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


     <!-- Second search box -->
     <div style="text-align: center; margin-top: 50px;">
        <strong>Incepe o noua conversatie:</strong>
        <div class="search-container">
            <form method="GET">
                <input type="text" class="search-box" id="search-box-2" name="termen_cautare" placeholder="Caută firma dupa nume..." onkeyup="fetchResults_simple()">
                <div id="search-results-container-2"></div>
            </form>
        </div>
    </div>
   
    <form id="startChatForm">
        <input type="hidden" id="selectedFirmaId" name="id_firma">

        <input type="text" id="subject" placeholder="Subiectul mesajului..." required>

        <textarea id="initialMessage" placeholder="Scrie mesajul tău aici..." required></textarea>


        <div class="g-recaptcha" data-sitekey="6LeKjfcnAAAAAMrUlLDRKf6XhQFkr0lq_XkzGwbg"></div>

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

    // verificam daca query-ul este gol sau nu
    if (query.trim() === '') {
        document.getElementById('search-results-container-2').innerHTML = ''; // eliminam rezultatele anterioare
        return; // iese din functie
    }
    
    // face request la scriptul php 
    fetch('../ADD_RECENZIE/cauta_firma.php?query=' + query)
    .then(response => response.json())
    .then(data => {
        let resultsContainer = document.getElementById('search-results-container-2');
        resultsContainer.innerHTML = ''; // reseteaza containerul

        if (data.length > 0) {
            data.forEach(firma => {
                let firmaDiv = document.createElement('div');
                
                firmaDiv.textContent = firma.nume_firma;
                
                // la click pe o firma, se seteaza id-ul firmei in input-ul ascuns
                firmaDiv.onclick = function() {
                    document.getElementById('search-box-2').value = firma.nume_firma;
                    document.getElementById('selectedFirmaId').value = firma.id;  
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
    // functia care trimite mesajul initial
function startChat() {
    let firmaId = document.getElementById('selectedFirmaId').value;
    if (!firmaId) {
        document.getElementById('messageStatus').textContent = 'Selectați o firmă întâi.';
        return;
    }
    
    let formData = new FormData();
    formData.append('id_firma', firmaId); // folosim id-ul firmei selectate
    
    let message = document.getElementById('initialMessage').value;
    if (message.trim() === '') {
        document.getElementById('messageStatus').textContent = 'Mesajul nu poate fi gol.';
        return;
    }

    let subject = document.getElementById('subject').value;
    if (subject.trim() === '') {
        document.getElementById('messageStatus').textContent = 'Subiectul nu poate fi gol.';
        return;
    }

    formData.append('subiect', subject);
    formData.append('id_client', <?php echo $_SESSION['user_id']; ?>);
    formData.append('message', message);

    let recaptchaResponse = grecaptcha.getResponse();
if (recaptchaResponse === '') {
    alert('Please complete the reCAPTCHA verification.');
    return;
}
formData.append('g-recaptcha-response', recaptchaResponse);

    fetch('../CHAT/send_initial_message.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('messageStatus').textContent = 'Mesaj trimis cu succes!';
            document.getElementById('initialMessage').value = ''; // curata mesajul
            document.getElementById('subject').value = '';  // curata subiectul
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


<a href="https://ec.europa.eu/consumers/odr/main/index.cfm?event=main.home2.show&lng=RO">
        <img src="../IMAGES/extra/anpc-sal.webp" alt="Image Description"  />
    </a >

        <p>&copy;  2023 Platforma SolarQuery</p>
    </footer>



        </body>
</html>