<?php
require 'dbConnection.php';
require 'vendor/autoload.php';



require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use Dompdf\Dompdf;

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
$pdfPath = '/path/to/save/monthly_calendar.pdf';
file_put_contents($pdfPath, $pdfOutput);

// Mailpit SMTP Einstellungen
$mail = new PHPMailer\PHPMailer\PHPMailer();
$mail->isSMTP();
$mail->Host = 'localhost';
$mail->Port = 1025;

$mail->setFrom('your-email@example.com', 'Kalender');
$mail->addAddress('recipient@example.com');
$mail->Subject = 'Monatliche Veranstaltungen';
$mail->Body = 'Im Anhang finden Sie die PDF mit den monatlichen Veranstaltungen.';
$mail->addAttachment($pdfPath);

if ($mail->send()) {
    echo 'E-Mail erfolgreich versendet';
} else {
    echo 'Fehler beim E-Mail-Versand: ' . $mail->ErrorInfo;
}
