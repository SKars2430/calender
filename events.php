<?php
include 'dbConnection.php';

// Veranstaltung löschen, falls angefordert
if (isset($_GET['delete'])) {
    $eventId = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM events WHERE id = :id");
    $stmt->bindParam(':id', $eventId);
    $stmt->execute();
}

// Veranstaltungen aus der Datenbank abrufen
$stmt = $conn->prepare("SELECT * FROM events ORDER BY event_date DESC");
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Veranstaltungen verwalten</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="event-management-container">
        <h2>Veranstaltungen verwalten</h2>
        <button onclick="window.location.href='index.php'">Zurück zum Kalender</button>
        <button onclick="window.location.href='addEvent.php'">Neue Veranstaltung hinzufügen</button>
        <div class="event-feed">
            <?php foreach ($events as $event) : ?>
                <div class="event-item" style="background-color: <?= $event['color'] ?>">
                    <h3><?= $event['title'] ?></h3>
                    <p><?= date('d.m.Y', strtotime($event['event_date'])) ?> | <?= $event['event_time'] ?></p>
                    <p><?= $event['description'] ?></p>
                    <p>Kategorie: <?= $event['category'] ?></p>
                    <p>Teilnehmer: <?= $event['participants'] ?></p>
                    <button onclick="window.location.href='editEvent.php?id=<?= $event['id'] ?>'">Bearbeiten</button>
                    <button onclick="window.location.href='events.php?delete=<?= $event['id'] ?>'">Löschen</button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
