<?php
    
	$servernamems = "203.142.71.90:3303";
	$servernamems = "103.130.192.138:3303";
    //$servernamems = "192.168.88.188:3303";
    $usernamems = "root";
    $passwordms = "Ganteng123456";
    
    $cnms = mysqli_connect($servernamems, $usernamems, $passwordms) or die("Connection failed: " . mysqli_connect_error());
    
?>

