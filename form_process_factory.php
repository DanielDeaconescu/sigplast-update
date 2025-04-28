<?php 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

// Load Composer's autoloader
require 'vendor/autoload.php';

// Load the environment variables from the .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Captcha verification (no changes here)
    $turnstileSecretKey = '0x4AAAAAABT9YwYgFdd1QdEjcoFCnOEQUOs';
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

    // Input sanitization and validation (no changes here)
    $fullName = filter_input(INPUT_POST, 'full-name', FILTER_SANITIZE_STRING);
    $phoneNum = filter_input(INPUT_POST, 'phone-num', FILTER_SANITIZE_STRING);
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    $orderDescription = filter_input(INPUT_POST, 'order-description', FILTER_SANITIZE_STRING);

    if (strlen($fullName) > 100 || strlen($phoneNum) > 100 || strlen($location) > 100 || strlen($email) > 100) {
        die("Too large input data.");
    }

    // full-name and phone number required
    if (empty($fullName) || empty($phoneNum) || strlen($phoneNum) < 7) {
        die("Numele si un numar de telefon valid sunt obligatorii.");
    }

    try {
        $mail->isSMTP();
        // Use environment variables for SMTP settings
        $mail->Host       = getenv('MAIL_HOST');
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('MAIL_USERNAME');
        $mail->Password   = getenv('MAIL_PASSWORD');
        $mail->SMTPSecure = getenv('MAIL_SMTPSECURE');
        $mail->Port       = getenv('MAIL_PORT');

        // Recipients
        $mail->setFrom(getenv('MAIL_FROM_EMAIL'), getenv('MAIL_FROM_NAME'));
        $mail->addAddress(getenv('MAIL_TO_EMAIL'), "$fullName");
        $mail->CharSet = 'UTF-8';

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Formular Fabrică ' . $fullName;
        $mail->Body    = "
            <h3>Informatii Comanda Fabrică</h3>
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
                <p>Descrierea lucrarii: <strong>$orderDescription</strong></p>
            </div>
        ";

        $mail->send();
        header('Location: submitted.html');
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
