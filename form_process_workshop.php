<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Europe/Bucharest');

// Start output buffering to prevent header issues
ob_start();

require 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// === Rate Limiting ===
$ip = $_SERVER['REMOTE_ADDR'];
$limit = 2;
$duration = 86400; // 24h
$logFile = __DIR__ . '/submission_log2.json';

$submissions = file_exists($logFile) ? json_decode(file_get_contents($logFile), true) : [];

// Remove old entries
foreach ($submissions as $loggedIp => $data) {
    if (time() - $data['first_time'] > $duration) {
        unset($submissions[$loggedIp]);
    }
}

// Initialize count for new IPs
if (!isset($submissions[$ip])) {
    $submissions[$ip] = ['count' => 0, 'first_time' => time()];
}

// Block if over limit
if ($submissions[$ip]['count'] >= $limit) {
    session_start();
    $_SESSION['rate_limited'] = true;
    header('Location: too-many-submissions.php');
    ob_end_clean(); // Discard buffer before redirect
    exit();
}

// Increment and save
$submissions[$ip]['count']++;
file_put_contents($logFile, json_encode($submissions));

// Only handle POST requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $mail = new PHPMailer(true);

    // === Turnstile Verification ===
    $turnstileSecretKey = $_ENV['TURNSTILE_SECRET_KEY'];
    $turnstileResponse = $_POST['cf-turnstile-response-sigplast2'] ?? '';

    if (!$turnstileResponse) {
        die("Nu am primit răspuns de la Turnstile.");
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://challenges.cloudflare.com/turnstile/v0/siteverify");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'secret'   => $turnstileSecretKey,
        'response' => $turnstileResponse,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? null
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $verifyResponse = curl_exec($ch);
    curl_close($ch);

    $captchaSuccess = json_decode($verifyResponse);

    if (!$captchaSuccess || !$captchaSuccess->success) {
        die("Verificarea Turnstile a eșuat. Te rugăm să încerci din nou.");
    }

    // === Sanitize and validate input ===
    $fullName = filter_input(INPUT_POST, 'full-name', FILTER_SANITIZE_STRING);
    $phoneNum = filter_input(INPUT_POST, 'phone-num', FILTER_SANITIZE_STRING);
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    $orderDescription = filter_input(INPUT_POST, 'order-description', FILTER_SANITIZE_STRING);

    if (strlen($fullName) > 100 || strlen($phoneNum) > 100 || strlen($location) > 100 || strlen($email) > 100) {
        die("Datele introduse sunt prea lungi.");
    }

    if (empty($fullName) || empty($phoneNum) || strlen($phoneNum) < 7) {
        die("Numele și un număr de telefon valid sunt obligatorii.");
    }

    // === File upload ===
    $uploadedFile = $_FILES['uploaded-image'] ?? null;
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB

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

    // === Send email ===
    try {
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USERNAME'];
        $mail->Password   = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = $_ENV['SMTP_PORT'];

        $mail->setFrom($_ENV['SMTP_USERNAME'], "Sigplast website");
        $mail->addAddress($_ENV['RECIPIENT_EMAIL'], $fullName);
        $mail->CharSet = 'UTF-8';

        $mail->isHTML(true);
        $mail->Subject = 'Formular Atelier ' . $fullName . ' ' . date('d-m-Y H:i:s');
        $mail->Body    = "
            <h3>Informații comandă atelier</h3>
            <p><strong>Nume:</strong> $fullName</p>
            <p><strong>Telefon:</strong> $phoneNum</p>
            <p><strong>Localitate:</strong> $location</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Descriere lucrare:</strong> $orderDescription</p>
        ";

        if ($attachmentPath && $attachmentName) {
            $mail->addAttachment($attachmentPath, $attachmentName);
        }

        $mail->send();

        session_start();
        $_SESSION['form_submitted'] = true;
        header('Location: submitted.php');
        ob_end_clean(); // Clean buffer before redirect
        exit();
    } catch (Exception $e) {
        ob_end_clean(); // Still clean the buffer if exception
        echo "Mesajul nu a putut fi trimis. Eroare: {$mail->ErrorInfo}";
    }
}
