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

$firma_id = $_GET['id_firma']; // Get the business ID from the URL
$client_id = $_SESSION['user_id']; // Get the client ID from the session
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat with <?php echo $firma_id; ?></title> <!-- Ideally, replace $firma_id with the actual business name -->
</head>
<body>
    <div class="chat-box">
        <!-- Display messages here -->
        <?php
        $sql = "SELECT * FROM tbl_mesaje WHERE (id_client = ? AND id_firma = ?) OR (id_firma = ? AND id_client = ?) ORDER BY timestamp DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiii", $client_id, $firma_id, $firma_id, $client_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            echo "<p>" . $row['continut'] . "</p>";
        }
        $stmt->close();
        ?>
    </div>

    <!-- Message input and send button -->
    <textarea id="messageInput" placeholder="Write your message"></textarea>
    <button onclick="sendMessage()">Send</button>

    <script>
        function sendMessage() {
            let message = document.getElementById('messageInput').value;
            // Send the message using AJAX or a form submission
            // You can further expand on this
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
