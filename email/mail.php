<?php
include "classes/class.phpmailer.php";
$mail = new PHPMailer; 
$mail->IsSMTP();
$mail->SMTPSecure = 'ssl'; 
$mail->Host = "sdm-mkt.com"; //host masing2 provider email
$mail->SMTPDebug = 2;
$mail->Port = 465;
$mail->SMTPAuth = true;
$mail->Username = "huspan.nasrulloh@sdm-mkt.com"; //user email
$mail->Password = "huspan2018"; //password email 
$mail->SetFrom("huspan.nasrulloh@sdm-mkt.com"); //set email pengirim
$mail->Subject = "Testing"; //subyek email
$mail->AddAddress("ayrull79@gmail.com","huspan.nasrulloh@sdm-mkt.com");  //tujuan email
$mail->MsgHTML("Testing...");
if($mail->Send()) echo "Message has been sent";
else echo "Failed to sending message";
?>