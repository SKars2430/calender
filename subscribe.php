<?php
// Include database connection
include 'dbConnection.php';
require_once 'dompdf/autoload.inc.php';
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['mail'];
    $subscribe = isset($_POST['subscribe']) ? 1 : 0;
    $agree = isset($_POST['agree']) ? 1 : 0;

    // Save to database
    $query = "INSERT INTO subscribers (name, email, subscribe, agree) VALUES (:name, :email, :subscribe, :agree)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':subscribe', $subscribe);
    $stmt->bindParam(':agree', $agree);

    if ($stmt->execute()) {
        echo "Abo erfolgreich!\n\n";

        // Generate PDF and send email
        $month = date('m');
        $year = date('Y');

        $query = "SELECT * FROM events WHERE MONTH(event_date) = :month AND YEAR(event_date) = :year ORDER BY event_date";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':month', $month);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $html = '<h1>Monatliche Veranstaltungen</h1>';
        $html .= '<ul>';
        foreach ($events as $event) {
            $html .= '<li>' . date('d.m.Y', strtotime($event['event_date'])) . ': ' . $event['title'] . ' (' . $event['category'] . ')</li>';
        }
        $html .= '</ul>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfOutput = $dompdf->output();
        $pdfPath = __DIR__ . '/temp/monthly_calendar.pdf';
        file_put_contents($pdfPath, $pdfOutput);

        // Mailpit SMTP settings
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'localhost';
        $mail->Port = 1025;

        $mail->setFrom('your-email@example.com', 'Kalender');
        $mail->addAddress($email); // Use the subscriber's email
        $mail->Subject = 'Monatliche Veranstaltungen';
        $mail->Body = 'Im Anhang finden Sie die PDF mit den monatlichen Veranstaltungen.';
        $mail->addAttachment($pdfPath);

        if ($mail->send()) {
            echo 'E-Mail erfolgreich versendet';
        } else {
            echo 'Fehler beim E-Mail-Versand: ' . $mail->ErrorInfo;
        }
    } else {
        echo "Fehler beim abonnieren";
    }
}