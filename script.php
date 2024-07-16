<?php
session_start();

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "solarquery";

$mysqli = new mysqli($servername, $db_username, $db_password, $dbname);


// incarcare toate tabelele din baza de date
$tablesQuery = "SHOW TABLES";
$tablesResult = $mysqli->query($tablesQuery);

while ($tableName = $tablesResult->fetch_row()) {
    echo "Table " . $tableName[0] . " {\n";

        // incarca coloanele pentru fiecare tabel
        $columnsQuery = "SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_KEY 
                         FROM information_schema.COLUMNS 
                         WHERE TABLE_NAME = '{$tableName[0]}' AND TABLE_SCHEMA = '{$dbname }'
                         ORDER BY ORDINAL_POSITION";
        $columnsResult = $mysqli->query($columnsQuery);
        while ($column = $columnsResult->fetch_assoc()) {
            echo "  " . $column['COLUMN_NAME'] . " " . $column['COLUMN_TYPE'];
            if ($column['COLUMN_KEY'] === 'PRI') {
                echo " [primary key]";
            }
            echo " \n";
        }
        echo "}\n\n";
    }
    
   
    ?>

<!DOCTYPE html>
<html>
<head>
    <title>Database Structure</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background-color: #f4f4f4;
            padding: 20px;
        }
        pre {
            background-color: #fff;
            padding: 15px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>

<?php






// incarcare toate tabelele din baza de date
$tablesQuery = "SHOW TABLES";
$tablesResult = $mysqli->query($tablesQuery);

echo "<pre>";  

while ($tableName = $tablesResult->fetch_row()) {
    echo "Table " . $tableName[0] . " { \n";

    // incarca coloanele pentru fiecare tabel
    $columnsQuery = "SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_KEY 
                     FROM information_schema.COLUMNS 
                     WHERE TABLE_NAME = '{$tableName[0]}' AND TABLE_SCHEMA = '{$dbname}'
                     ORDER BY ORDINAL_POSITION";
    $columnsResult = $mysqli->query($columnsQuery);
    while ($column = $columnsResult->fetch_assoc()) {
        echo "  " . $column['COLUMN_NAME'] . " " . $column['COLUMN_TYPE'];
        if ($column['COLUMN_KEY'] === 'PRI') {
            echo " [primary key]";
        }
        echo " \n";
    }
    echo "}\n\n";
}

echo "</pre>"; 


