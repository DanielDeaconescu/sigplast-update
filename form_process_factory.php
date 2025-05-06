<?php 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
date_default_timezone_set('Europe/Bucharest');

// Load Composer's autoloader
require 'vendor/autoload.php';

// Initialize Dotenv
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// === IP Rate Limiting Logic ===
$ip = $_SERVER['REMOTE_ADDR'];
$limit = 2; // Max allowed submissions per IP
$duration = 3600; // Time window in seconds (e.g., 1 hour)
$logFile = __DIR__ . '/submission_log.json';

$submissions = file_exists($logFile) ? json_decode(file_get_contents($logFile), true) : [];

// Clean up old entries
foreach ($submissions as $loggedIp => $data) {
    if (time() - $data['first_time'] > $duration) {
        unset($submissions[$loggedIp]);
    }
}

// Check current IP
if (!isset($submissions[$ip])) {
    $submissions[$ip] = ['count' => 0, 'first_time' => time()];
}

if ($submissions[$ip]['count'] >= $limit) {
    session_start();
    $_SESSION['rate_limited'] = true;
    header('Location: too-many-submissions.php');
    exit();
}

// Increment and save
$submissions[$ip]['count']++;
file_put_contents($logFile, json_encode($submissions));

// === End Rate Limiting ===

$mail = new PHPMailer(true);

if($_SERVER["REQUEST_METHOD"] == "POST") {

    $turnstileSecretKey = $_ENV['TURNSTILE_SECRET_KEY'];
    $turnstileResponse = $_POST['cf-turnstile-response-sigplast'];

    $verifyResponse = file_get_contents("https://challenges.cloudflare.com/turnstile/v0/siteverify", false, stream_context_create([
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query([
                'secret'   => $turnstileSecretKey,
                'response' => $turnstileResponse,
                'remoteip' => $_SERVER['REMOTE_ADDR'] ?? null
            ])
        ]
    ]));

    $captchaSuccess = json_decode($verifyResponse);

    if (!$captchaSuccess || !$captchaSuccess->success) {
        die("Verificarea Turnstile a eșuat. Te rugăm să încerci din nou.");
    }

    $fullName = filter_input(INPUT_POST, 'full-name', FILTER_SANITIZE_STRING);
    $phoneNum = filter_input(INPUT_POST, 'phone-num', FILTER_SANITIZE_STRING);
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    $orderDescription = filter_input(INPUT_POST, 'order-description', FILTER_SANITIZE_STRING);

    if(strlen($fullName) > 100 || strlen($phoneNum) > 100 || strlen($location) > 100 || strlen($email) > 100){
        die("Too large input data.");
    }

    if(empty($fullName) || empty($phoneNum) || strlen($phoneNum) < 7){
        die("Numele si un numar de telefon valid sunt obligatorii.");
    }

    $uploadedFile = $_FILES['uploaded-image'] ?? null;
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5 MB

    $attachmentPath = null;
    $attachmentName = null;

    if ($uploadedFile && $uploadedFile['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $uploadedFile['tmp_name'];
        $fileName = basename($uploadedFile['name']);
        $fileType = mime_content_type($fileTmpPath);
        $fileSize = filesize($fileTmpPath);

    if (in_array($fileType, $allowedTypes) && $fileSize <= $maxSize) {
        $attachmentPath = $fileTmpPath;
        $attachmentName = $fileName;
    } else {
        die("Fișierul trebuie să fie o imagine validă (JPG, PNG, GIF, WEBP) și să nu depășească 5MB.");
    }
} elseif ($uploadedFile && $uploadedFile['error'] !== UPLOAD_ERR_NO_FILE) {
    die("A apărut o eroare la încărcarea fișierului.");
}


    try {
        $mail->isSMTP();                                            
        $mail->Host       = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USERNAME'];
        $mail->Password   = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = $_ENV['SMTP_PORT'];
    
        $mail->setFrom($_ENV['SMTP_USERNAME'], "Sigplast website");
        $mail->addAddress('albarim@gmail.com', "$fullName");
        $mail->CharSet = 'UTF-8';

        $mail->isHTML(true);
        $mail->Subject = 'Formular Fabrică ' . $fullName . ' ' . date('d-m-Y H:i:s');
        $mail->Body    = "
            <h3>Informatii Comandă Fabrică</h3>
            <div><p>Nume: <strong>$fullName</strong></p></div>
            <div><p>Numar de telefon: <strong>$phoneNum</strong></p></div>
            <div><p>Localitate: <strong>$location</strong></p></div>
            <div><p>Email: <strong>$email</strong></p></div>
            <div><p>Descrierea lucrarii: <strong>$orderDescription</strong></p></div>
        ";
        
        if ($attachmentPath && $attachmentName) {
            $mail->addAttachment($attachmentPath, $attachmentName);
        }

        $mail->send();
        session_start();
        $_SESSION['form_submitted'] = true;
        header('Location: submitted.php');
        exit();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
