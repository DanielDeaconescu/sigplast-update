<?php 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

if($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = htmlspecialchars($_POST['name']);
    $email = $_POST['email'];

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
        $mail->Subject = 'New form submission from ' . $name;
        $mail->Body    = "
            <h2>Name: $name </h2> . '<br>'
            <h2>Email: $email </h2> . '<br>'
        ";
        // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    
        $mail->send();
        header('Location: submitted.php');
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}