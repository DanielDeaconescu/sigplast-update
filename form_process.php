<?php 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

if($_SERVER["REQUEST_METHOD"] == "POST") {

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
        $mail->Host       = 'mail.danieldeaconescu.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'contact@danieldeaconescu.com';
        $mail->Password   = 'contactPassword!23';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465; 
    
        //Recipients
        $mail->setFrom('contact@danieldeaconescu.com', "Sigplast website");
        $mail->addAddress('daniel.deaconescu98@gmail.com', "$name");
        $mail->CharSet = 'UTF-8';

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Formular Atelier ' . $fullName;
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
        header('Location: submitted.php');
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}