<?php
include 'dbConnection.php';

$eventId = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $conn->prepare("UPDATE events SET participants = participants + 1 WHERE id = :id");
    $stmt->bindParam(':id', $eventId);
    if ($stmt->execute()) {
        header("Location: dayDetails.php?date=" . date('Y-m-d', strtotime($_POST['event_date'])));
        exit();
    } else {
        echo "Fehler beim Hinzufügen des Teilnehmers.";
    }
} else {
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = :id");
    $stmt->bindParam(':id', $eventId);
    $stmt->execute();
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$event) {
        echo "Event nicht gefunden.";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang>
<head>
    <meta charset="UTF-8">
    <title>Event Teilnahme</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="calendar-container">
        <div class="calendar-header">
            <h2>Event Teilnahme</h2>
        </div>
        <div class="calendar-body">
            <form action="eventParticipation.php?id=<?= $event['id'] ?>" method="post">
                <input type="hidden" name="event_date" value="<?= $event['event_date'] ?>">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>

                <label for="email">E-Mail:</label>
                <input type="email" id="email" name="email" required>

                <button type="submit">Teilnehmen</button>
            </form>
        </div>
    </div>
</body>
</html>
