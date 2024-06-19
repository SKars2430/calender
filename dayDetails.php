<?php
include 'dbConnection.php';

$date = $_GET['date'];
$stmt = $conn->prepare("SELECT * FROM events WHERE event_date = :event_date");
$stmt->bindParam(':event_date', $date);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Details für <?= date('d.m.Y', strtotime($date)) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="event-details-container">
        <h2>Details für <?= date('d.m.Y', strtotime($date)) ?></h2>
        <button onclick="window.location.href='index.php'">Zurück zum Kalender</button>
        <?php foreach ($events as $event) : ?>
            <div class="event-item" style="background-color: <?= $event['color'] ?>">
                <h3><?= $event['title'] ?></h3>
                <p><?= date('d.m.Y', strtotime($event['event_date'])) ?> | <?= $event['event_time'] ?></p>
                <p><?= $event['description'] ?></p>
                <p>Kategorie: <?= $event['category'] ?></p>
                <p>Teilnehmer: <?= $event['participants'] ?></p>
                <button onclick="window.location.href='eventParticipation.php?id=<?= $event['id'] ?>'">Teilnehmen</button>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
