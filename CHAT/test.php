<!DOCTYPE html>
<html>
<head>
    <title>Send Initial Message</title>
</head>
<body>
    <h1>Send Initial Message</h1>
    <form method="post" action="send_initial_message.php">
        <label for="id_firma">Firma ID:</label>
        <input type="text" name="id_firma" id="id_firma"><br>

        <label for="id_client">Client ID:</label>
        <input type="text" name="id_client" id="id_client"><br>

        <label for="message">Message:</label>
        <textarea name="message" id="message"></textarea><br>

        <input type="submit" value="Send">
    </form>
</body>
</html>