<?php
include 'dbConnection.php';

$eventDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $category = $_POST['category'];
    $color = $_POST['color'];
    $participants = 0;

    $stmt = $conn->prepare("INSERT INTO events (title, description, event_date, event_time, category, color, participants) VALUES (:title, :description, :event_date, :event_time, :category, :color, :participants)");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':event_date', $event_date);
    $stmt->bindParam(':event_time', $event_time);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':color', $color);
    $stmt->bindParam(':participants', $participants);
    // test commit
    if ($stmt->execute()) {
        header("Location: index.php?month=" . date('m', strtotime($event_date)) . "&year=" . date('Y', strtotime($event_date)));
        exit();
    } else {
        echo "Fehler beim Hinzufügen des Ereignisses.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Event hinzufügen</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function cancelEdit() {
            window.history.back();
        }
    </script>
</head>
<body>
    <div class="event-form-container">
        <h2>Event hinzufügen</h2>
        <form action="addEvent.php" method="post">
            <label for="title">Titel:</label>
            <input type="text" id="title" name="title" required>

            <label for="description">Beschreibung:</label>
            <textarea id="description" name="description"></textarea>

            <label for="event_date">Datum:</label>
            <input type="date" id="event_date" name="event_date" required>

            <label for="event_time">Uhrzeit:</label>
            <input type="time" id="event_time" name="event_time">

            <label for="category">Kategorie:</label>
            <select id="category" name="category">
                <option value="Sport">Sport</option>
                <option value="Konzert">Konzert</option>
                <option value="Meeting">Meeting</option>
                <option value="Other">Other</option>
            </select>

            <label for="color">Farbe:</label>
            <input type="color" id="color" name="color" value="#ff0000">

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit">Event hinzufügen</button>
                <button type="button" onclick="cancelEdit()">Abbrechen</button>
            </div>
        </form>
    </div>
</body>
</html>
