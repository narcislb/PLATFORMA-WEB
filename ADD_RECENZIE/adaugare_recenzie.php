
<?php
    // Inițializarea sesiunii
session_start(); 
    //verifică dacă utilizatorul este deja autentificat si daca este client
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'client') {
  //  utilizatorul nu este autentificat, redirecționează către pagina de autentificare
header('Location: ../PHP/clienti-login-form.php?redirect=../ADD_RECENZIE/adaugare_recenzie.php');
  exit;
}
    // Preluarea ID-ului firmei din URL
    $id_client = $_SESSION['user_id'];

    // Conectarea la baza de date
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $dbname = "solarquery";

    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    // Verificarea conexiunii
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    ?>
 
    <!-- Navbar -->
<div class="navbar">
  <h1><a href="../index.html">SolarQuery</a></h1>
</div>
</div>
<!DOCTYPE html>
<html>
<head>
  <title>Adaugare recenzie</title>
  <link rel="stylesheet" type="text/css" href="../CSS/adaugare_recenzie.css">
</head>

<!DOCTYPE html>
    <div class="content">
    <input type="text" id="searchFirma" placeholder="Cauta firma">
    <div id="suggestions"></div>

    <textarea id="recenzie" placeholder="Scrie recenzia ta aici..."></textarea>
   
    <button onclick="saveRecenzie()">Salveaza recenzie</button>
</div>



<script>
document.getElementById('searchFirma').addEventListener('input', function() {
    let query = this.value;

    if (query.length > 2) {
        fetch(`cauta_firma.php?query=${query}`)
        .then(response => response.json())
        .then(data => {
            let suggestionHTML = '';
            data.forEach(firma => {
                suggestionHTML += `<div onclick="selectFirma('${firma.nume_firma}', ${firma.id})">${firma.nume_firma}</div>`;
            });
            document.getElementById('suggestions').innerHTML = suggestionHTML;
        });
    }
});

function selectFirma(nume_firma, id) {
    document.getElementById('searchFirma').value = nume_firma;
    document.getElementById('suggestions').innerHTML = '';
    document.getElementById('searchFirma').dataset.selectedId = id;

    // incarca recenzia firmei
    fetch(`get_recenzie.php?id_firma=${id}&id_client=<?php echo $id_client; ?>`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('recenzie').value = data.text;
        } else {
            document.getElementById('recenzie').value = '';
        }
    });
}

function saveRecenzie() {
    let firmaId = document.getElementById('searchFirma').dataset.selectedId;
    let recenzieText = document.getElementById('recenzie').value;

    let formData = new FormData();
    formData.append('id_firma', firmaId);
    formData.append('id_client', '<?php echo $id_client; ?>');
    formData.append('recenzie', recenzieText);

    fetch('salveaza_recenzie.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Recenzie salvată cu succes!');
        } else {
            alert('Eroare la salvarea recenziei.');
        }
    });
}

</script>


 





