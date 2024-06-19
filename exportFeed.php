<?php
// Include Datenbankverbindung
include 'dbConnection.php';

// Überprüfen, ob Monat und Jahr als GET-Parameter übergeben wurden
$month = isset($_GET['month']) ? $_GET['month'] : date('m');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Veranstaltungen für den Feed abfragen
$monthlyEventsQuery = "SELECT * FROM events WHERE MONTH(event_date) = :month AND YEAR(event_date) = :year ORDER BY event_date";
$monthlyEventsStmt = $conn->prepare($monthlyEventsQuery);
$monthlyEventsStmt->bindParam(':month', $month, PDO::PARAM_INT);
$monthlyEventsStmt->bindParam(':year', $year, PDO::PARAM_INT);
$monthlyEventsStmt->execute();
$monthlyEvents = $monthlyEventsStmt->fetchAll(PDO::FETCH_ASSOC);

// FPDF laden
require('fpdf/fpdf.php');

class PDF extends FPDF {
    // Kopfzeile
    function Header() {
        // Arial fett 15
        $this->SetFont('Arial', 'B', 15);
        // Titel
        $this->Cell(0, 10, 'Monatliche Veranstaltungen', 0, 1, 'C');
        $this->Ln(10);
    }

    // Fußzeile
    function Footer() {
        // Position 1.5 cm von unten
        $this->SetY(-15);
        // Arial kursiv 8
        $this->SetFont('Arial', 'I', 8);
        // Seitennummer
        $this->Cell(0, 10, 'Seite ' . $this->PageNo(), 0, 0, 'C');
    }

    // Veranstaltungsinhalt
    function EventTable($events) {
        $this->SetFont('Arial', '', 12);
        foreach ($events as $event) {
            $date = date('d.m.Y', strtotime($event['event_date']));
            $this->Cell(0, 10, "$date - {$event['title']} ({$event['category']})", 0, 1);
        }
    }
}

// PDF-Dokument erstellen
$pdf = new PDF();
$pdf->AddPage();
$pdf->EventTable($monthlyEvents);
$pdf->Output('D', "Veranstaltungen_$month-$year.pdf");
