<?php 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
date_default_timezone_set('Europe/Bucharest');

//Load Composer's autoloader
require 'vendor/autoload.php';

// Initialize Dotenv
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Rate limiting
$ip = $_SERVER['REMOTE_ADDR'];
$limit = 2;
$duration = 3600;
$logFile = __DIR__ . '/submission_log2.json';

$submissions = file_exists($logFile) ? json_decode(file_get_contents($logFile), true) : [];

// Clean up old entries
foreach ($submissions as $loggedIp => $data) {
    if(time() - $data['first_time'] > $duration) {
        unset($submissions[$loggedIp]);
    }
}

// Check current IP
if (!isset($submissions[$ip])) {
    $submissions[$ip] = ['count' => 0, 'first_time' => time()];
}

if($submissions[$ip]['count'] >= $limit) {
    session_start();
    $_SESSION['rate_limited'] = true;
    header('Location: too-many-submissions.php');
    exit();
}

// Increment and save
$submissions[$ip]['count']++;
file_put_contents($logFile, json_encode($submissions));

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

if($_SERVER["REQUEST_METHOD"] == "POST") {

    $turnstileSecretKey = $_ENV['TURNSTILE_SECRET_KEY'];
    $turnstileResponse = $_POST['cf-turnstile-response-sigplast2'];

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

    // full-name and phone number required
    if(empty($fullName) || empty($phoneNum) || strlen($phoneNum) < 7){
        die("Numele si un numar de telefon valid sunt obligatorii.");
    }

    try {
        $mail->isSMTP();                                            
        $mail->Host       = $_ENV['SMTP_HOST'];           // Use the SMTP host from .env
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USERNAME'];       // Use the SMTP username from .env
        $mail->Password   = $_ENV['SMTP_PASSWORD'];       // Use the SMTP password from .env
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = $_ENV['SMTP_PORT']; 
    
        //Recipients
        $mail->setFrom($_ENV['SMTP_USERNAME'], "Sigplast website");
        $mail->addAddress('daniel.deaconescu98@gmail.com', "$fullName");
        $mail->CharSet = 'UTF-8';

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Formular Atelier ' . $fullName . ' ' . date('d-m-Y H:i:s');;
        $mail->Body    = "
            <h3>Informatii Comanda Atelier</h3>
            <div>
                <p>Nume: <strong>$fullName</strong></p>
            </div>
            <div>
                <p>Numar de telefon: <strong>$phoneNum</strong></p>
            </div>
            <div>
                <p>Localitate: <strong>$location</strong></p>
            </div>
            <div>
                <p>Email: <strong>$email</strong></p>
            </div>
            <div>
                <p>Descrierea lucrarii:        <strong>$orderDescription</strong></p>
            </div>
            
        ";
        // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    
        $mail->send();
        session_start();
        $_SESSION['form_submitted'] = true;
        header('Location: submitted.php');
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}