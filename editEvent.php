<?php
include 'dbConnection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $category = $_POST['category'];
    $color = $_POST['color'];

    $stmt = $conn->prepare("UPDATE events SET title = :title, description = :description, event_date = :event_date, event_time = :event_time, category = :category, color = :color WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':event_date', $event_date);
    $stmt->bindParam(':event_time', $event_time);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':color', $color);

    if ($stmt->execute()) {
        header("Location: events.php");
        exit();
    } else {
        echo "Fehler beim Bearbeiten des Ereignisses.";
    }
} else {
    $eventId = $_GET['id'];
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
<html>
<head>
    <meta charset="UTF-8">
    <title>Event bearbeiten</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function cancelEdit() {
            window.history.back();
        }
    </script>
</head>
<body>
    <div class="event-form-container">
        <h2>Event bearbeiten</h2>
        <form action="editEvent.php" method="post">
            <input type="hidden" name="id" value="<?= $event['id'] ?>">

            <label for="title">Titel:</label>
            <input type="text" id="title" name="title" value="<?= $event['title'] ?>" required>

            <label for="description">Beschreibung:</label>
            <textarea id="description" name="description"><?= $event['description'] ?></textarea>

            <label for="event_date">Datum:</label>
            <input type="date" id="event_date" name="event_date" value="<?= $event['event_date'] ?>" required>

            <label for="event_time">Uhrzeit:</label>
            <input type="time" id="event_time" name="event_time" value="<?= $event['event_time'] ?>">

            <label for="category">Kategorie:</label>
            <select id="category" name="category">
                <option value="Sport" <?= $event['category'] == 'Sport' ? 'selected' : '' ?>>Sport</option>
                <option value="Konzert" <?= $event['category'] == 'Konzert' ? 'selected' : '' ?>>Konzert</option>
                <option value="Meeting" <?= $event['category'] == 'Meeting' ? 'selected' : '' ?>>Meeting</option>
                <option value="Other" <?= $event['category'] == 'Other' ? 'selected' : '' ?>>Other</option>
            </select>

            <label for="color">Farbe:</label>
            <input type="color" id="color" name="color" value="<?= $event['color'] ?>">

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit">Event bearbeiten</button>
                <button type="button" onclick="cancelEdit()">Abbrechen</button>
            </div>
        </form>
    </div>
</body>
</html>

