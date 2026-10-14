<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Required if not using Composer autoload
require 'phpmailer/Exception.php';
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';

function getMailerInstance() {
    $mail = new PHPMailer(true);
    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'hiidemodemo@gmail.com';
        $mail->Password   = 'dlqy fivp kbix mfrm'; // App password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('hiidemodemo@gmail.com', 'From Streamly, verify otp to website.');
        return $mail;
    } catch (Exception $e) {
        error_log("Mailer Init Error: {$mail->ErrorInfo}");
        return null;
    }
}

// ✅ Function for OTP only
function sendOtpMail($toEmail, $otp) {
    $mail = getMailerInstance();
    if (!$mail) return false;

    try {
        $mail->addAddress($toEmail);
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body    = "Your OTP is: <strong>$otp</strong>";
        $mail->AltBody = "Your OTP is: $otp";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("OTP Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

// ✅ Function for Order or any Custom email
function sendCustomMail($toEmail, $subject, $body) {
    $mail = getMailerInstance();
    if (!$mail) return false;

    try {
        $mail->addAddress($toEmail);
        $mail->isHTML(false); // Use plain text
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Custom Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
