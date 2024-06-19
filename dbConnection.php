<?php

// Definition der Datenbankverbindungsparameter
$sName = "localhost";
$uName = "root";
$pass = "";
$dbName = "calender";

try {
    // Herstellung der Datenbankverbindung mit PDO
    $conn = new PDO("mysql:host=$sName;dbname=$dbName", $uName, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Ausgabe einer Fehlermeldung, falls die Verbindung fehlschlägt
    echo "Connection failed: " . $e->getMessage();
    // Beenden der Skriptausführung bei Verbindungsfehler
    die();
}
