<?php
// ⚠️ Enable errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include PHPMailer
require 'vendor/PHPMailer/PHPMailer.php';
require 'vendor/PHPMailer/SMTP.php';
require 'vendor/PHPMailer/Exception.php';

// Include Dompdf
require 'vendor/dompdf/dompdf.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;

// === GET FORM DATA ===
$clientName    = $_POST['clientName'] ?? '';
$clientEmail   = $_POST['clientEmail'] ?? '';
$clientAddress = $_POST['clientAddress'] ?? '';
$clientPhone   = $_POST['clientPhone'] ?? '';
$items         = $_POST['items'] ?? []; // array of items ['name'=>'Item1','qty'=>1,'price'=>100]
$received      = $_POST['received'] ?? 0;

if (!$clientName || !$clientEmail) {
    die('Client Name or Email is required!');
}

// === CREATE HTML INVOICE ===
$invoiceHTML = '<h1>Invoice</h1>';
$invoiceHTML .= "<p><b>Bill To:</b> $clientName, $clientAddress, $clientPhone</p>";

$invoiceHTML .= '<table border="1" cellpadding="5" cellspacing="0">';
$invoiceHTML .= '<tr><th>#</th><th>Item</th><th>Qty</th><th>Price</th><th>Amount</th></tr>';

$total = 0;
foreach ($items as $index => $item) {
    $amount = $item['qty'] * $item['price'];
    $total += $amount;
    $invoiceHTML .= "<tr>
        <td>".($index+1)."</td>
        <td>{$item['name']}</td>
        <td>{$item['qty']}</td>
        <td>{$item['price']}</td>
        <td>$amount</td>
    </tr>";
}

$balance = $total - $received;

$invoiceHTML .= "<tr><td colspan='4'><b>Total</b></td><td><b>$total</b></td></tr>";
$invoiceHTML .= "<tr><td colspan='4'><b>Received</b></td><td>$received</td></tr>";
$invoiceHTML .= "<tr><td colspan='4'><b>Balance</b></td><td>$balance</td></tr>";
$invoiceHTML .= '</table>';

// === GENERATE PDF ===
$dompdf = new Dompdf();
$dompdf->loadHtml($invoiceHTML);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$pdfFilePath = 'invoices/invoice_'.time().'.pdf';
file_put_contents($pdfFilePath, $dompdf->output());

// === SEND EMAIL ===
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'website.sumahi@gmail.com';        // 🔑 Your Gmail
    $mail->Password   = 'tzxmbecszttucfvv';     // 🔑 App Password from Google
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('website.sumahi@gmail.com', 'Sumahi');
    $mail->addAddress($clientEmail, $clientName);
    $mail->addAttachment($pdfFilePath);

    $mail->isHTML(true);
    $mail->Subject = "Invoice from Your Company";
    $mail->Body    = "Dear $clientName,<br><br>Please find your invoice attached.<br><br>Thank you.";

    $mail->send();
    echo "✅ Invoice generated and emailed successfully to $clientEmail!";
} catch (Exception $e) {
    echo "❌ Email failed: {$mail->ErrorInfo}";
}
?>