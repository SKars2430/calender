<?php
// Include Datenbankverbindung
include 'dbConnection.php';

// Überprüfen, ob Monat und Jahr als GET-Parameter übergeben wurden, andernfalls aktuelle Werte setzen
$month = isset($_GET['month']) ? $_GET['month'] : date('m');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$filterCategory = isset($_GET['category']) ? $_GET['category'] : '';

// Namen der Monate und abgekürzten Wochentage auf Deutsch
$months = ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];
$daysOfWeek = ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'];

// Erster Tag des Monats als Zeitstempel
$firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
// Anzahl der Tage im Monat
$numberDays = date('t', $firstDayOfMonth);
// Datumskomponenten des ersten Tages im Monat
$dateComponents = getdate($firstDayOfMonth);
// Name des Monats
$monthName = $months[$month - 1];
// Wochentag des ersten Tages im Monat
$dayOfWeek = $dateComponents['wday'];

// Vorheriger und nächster Monat mit entsprechender Jahresberechnung
$prevMonth = $month - 1;
$nextMonth = $month + 1;
$prevYear = $year;
$nextYear = $year;

if ($prevMonth < 1) {
    $prevMonth = 12;
    $prevYear = $year - 1;
}
if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear = $year + 1;
}

// Ereignisse aus der Datenbank abrufen
$query = "SELECT * FROM events WHERE MONTH(event_date) = :month AND YEAR(event_date) = :year";
if (!empty($filterCategory)) {
    $query .= " AND category = :category";
}
$stmt = $conn->prepare($query);
$stmt->bindParam(':month', $month);
$stmt->bindParam(':year', $year);
if (!empty($filterCategory)) {
    $stmt->bindParam(':category', $filterCategory);
}
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Veranstaltungen für den Feed abfragen
$monthlyEventsQuery = "SELECT * FROM events WHERE MONTH(event_date) = :month AND YEAR(event_date) = :year ORDER BY event_date";
$monthlyEventsStmt = $conn->prepare($monthlyEventsQuery);
$monthlyEventsStmt->bindParam(':month', $month);
$monthlyEventsStmt->bindParam(':year', $year);
$monthlyEventsStmt->execute();
$monthlyEvents = $monthlyEventsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Kalender</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="calendar-container">
        <div class="calendar-header">
            <!-- Navigationstasten für den vorherigen und nächsten Monat sowie für die Ansicht 'Heute' -->
            <button onclick="window.location.href='?month=<?= $prevMonth ?>&year=<?= $prevYear ?>&category=<?= $filterCategory ?>'">Vorheriger Monat</button>
            <button onclick="window.location.href='?month=<?= date('m') ?>&year=<?= date('Y') ?>&category=<?= $filterCategory ?>'">Heute</button>
            <span><?= $monthName ?> <?= $year ?></span>
            <button onclick="window.location.href='?month=<?= $nextMonth ?>&year=<?= $nextYear ?>&category=<?= $filterCategory ?>'">Nächster Monat</button>
            <button onclick="window.location.href='events.php'">Zur Veranstaltungsansicht</button>
        </div>
        <div class="calendar-body">
            <!-- Formular zur Filterung der Ereignisse nach Kategorie -->
            <form method="get" action="index.php">
                <input type="hidden" name="month" value="<?= $month ?>">
                <input type="hidden" name="year" value="<?= $year ?>">
                <label for="category">Filter nach Kategorie:</label>
                <select id="category" name="category" onchange="this.form.submit()">
                    <option value="">Alle</option>
                    <option value="Sport" <?= $filterCategory == 'Sport' ? 'selected' : '' ?>>Sport</option>
                    <option value="Konzert" <?= $filterCategory == 'Konzert' ? 'selected' : '' ?>>Konzert</option>
                    <option value="Meeting" <?= $filterCategory == 'Meeting' ? 'selected' : '' ?>>Meeting</option>
                    <option value="Other" <?= $filterCategory == 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </form>
            <table class="calendar-table">
                <thead>
                    <tr>
                        <!-- Wochentage in der Tabellenüberschrift -->
                        <?php foreach ($daysOfWeek as $day) : ?>
                            <th><?= $day ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <?php
                        // Leere Zellen für die Tage vor dem ersten Tag des Monats
                        if ($dayOfWeek > 0) {
                            for ($i = 0; $i < $dayOfWeek; $i++) {
                                echo '<td></td>';
                            }
                        }

                        $currentDay = 1;
                        while ($currentDay <= $numberDays) {
                            // Neue Zeile beginnen, wenn die Woche voll ist
                            if ($dayOfWeek == 7) {
                                $dayOfWeek = 0;
                                echo '</tr><tr>';
                            }

                            // Datum verlinken und Ereignisse für den Tag anzeigen
                            echo "<td><a href='dayDetails.php?date=$year-$month-$currentDay'>$currentDay</a>";
                            foreach ($events as $event) {
                                if (date('j', strtotime($event['event_date'])) == $currentDay) {
                                    echo "<div class='event' style='background-color: {$event['color']}'>{$event['title']}</div>";
                                }
                            }
                            echo "</td>";

                            $currentDay++;
                            $dayOfWeek++;
                        }

                        // Leere Zellen für die Tage nach dem letzten Tag des Monats
                        if ($dayOfWeek != 7) {
                            $remainingDays = 7 - $dayOfWeek;
                            for ($i = 0; $i < $remainingDays; $i++) {
                                echo '<td></td>';
                            }
                        }
                        ?>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="event-feed">
            <h2>Monatliche Veranstaltungen</h2>
            <?php if (!empty($monthlyEvents)) : ?>
                <ul>
                    <?php foreach ($monthlyEvents as $event) : ?>
                        <li>
                            <span><?= date('d.m.Y', strtotime($event['event_date'])) ?>:</span>
                            <span><?= $event['title'] ?></span>
                            <span>(<?= $event['category'] ?>)</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <button class="download-button" onclick="window.location.href='exportFeed.php?month=<?= $month ?>&year=<?= $year ?>'">PDF herunterladen</button>
            <?php endif; ?>

            <!-- Newsletter anmeldung -->
            <form action="subscribe.php" method="post">
                <h3>Newsletter abonnieren</h3>
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
                <label for="mail">E-Mail:</label>
                <input type="email" id="mail" name="mail" required>
                <label for="subscribe">
                    <input type="checkbox" id="subscribe" name="subscribe" checked> Abonnieren
                </label>
                <label for="agree">
                    <input type="checkbox" id="agree" name="agree" required> Ich stimme den Bedingungen zu
                </label>
                <button type="submit">Abonnieren</button>
            </form>
        </div>
    </div>
</body>
</html>
