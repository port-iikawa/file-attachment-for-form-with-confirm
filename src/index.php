<?php
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer;

$mail = new PHPMailer\PHPMailer(true);
$mail->isSMTP();                                            //Send using SMTP
$mail->Host       = 'host';                     //Set the SMTP server to send through
$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
$mail->Username   = 'user';                     //SMTP username
$mail->Password   = 'pass';                               //SMTP password
$mail->SMTPSecure = PHPMailer\PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
$mail->Port       = 465;
$mail->CharSet = 'UTF-8';

$mail->Subject = 'テスト';
$mail->Body    = '本文';

$mail->send();
