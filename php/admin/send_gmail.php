<?php
header('Content-Type: application/json');

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

// Validate input
$emailTo = filter_var($_POST['emailTo'] ?? '', FILTER_VALIDATE_EMAIL);
$message = trim($_POST['emailMessage'] ?? '');

if (!$emailTo || $message === '') {
    echo json_encode(['success' => false, 'error' => 'Valid email and message required']);
    exit;
}

// Check for Composer autoload
$autoload = __DIR__ . '/../../vendor/autoload.php';
if (!file_exists($autoload)) {
    echo json_encode(['success' => false, 'error' => 'PHPMailer not installed. Run: composer require phpmailer/phpmailer']);
    exit;
}
require_once($autoload);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // SMTP settings (Gmail or any SMTP server)
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'prj294165@gmail.com'; // your Gmail address
    $mail->Password = 'ahxl fkhc yvax uemu'; // your Gmail App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
        ],
    ];

    // Sender and recipient
    $mail->setFrom('prj294165@gmail.com', 'School Admin');
    $mail->addAddress($emailTo);

    // Content
    $mail->isHTML(false);
    $mail->Subject = 'Message from School Management System';
    $mail->Body    = $message;

    $mail->send();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Mailer Error: ' . $mail->ErrorInfo . ' (Check Gmail credentials, App Password, and less secure app settings.)'
    ]);
}
