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
        $mail->setFrom('contact@danieldeaconescu.com', "Daniel's website");
        $mail->addAddress('daniel.deaconescu98@gmail.com', "$name");
    
        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Formular Atelier ' . $fullName;
        $mail->Body    = "
            <h6>Nume: <strong>$fullName</strong></h6> <br/>
            <h6>Numar de telefon: <strong>$phoneNum</strong></h6> <br/>
            <h6>Localitate: <strong>$location</strong></h6> <br/>
            <h6>Email: <strong>$email</strong></h6> <br/>
            <h6>Descrierea lucrarii: <strong>$orderDescription</strong></h6> <br/>
        ";
        // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    
        $mail->send();
        header('Location: submitted.php');
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}