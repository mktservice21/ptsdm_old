<?php
    /* Database connection start */
    $servername = "localhost";
    $servername = "ms.sdm-mkt.com:3303";
	$servername = "203.142.71.92:3303";
	$servername = "103.130.192.140:3303";
    //$servername = "192.168.88.189:3303";
    $username = "root";
    $password = "Password123";
    $password = "Ganteng123456";
    
    //hilangkan
    /*
    $servername = "localhost";
    $username = "root";
    $password = "";
    */
    //===hilangkan
    

    
    $cnmy = mysqli_connect($servername, $username, $password) or die("Connection failed: " . mysqli_connect_error());
    
    $servernamems = "new.sdm-mkt.com:3303";
    $usernamems = "root";
    $passwordms = "Ganteng123456";
    
    //$cnms = mysqli_connect($servernamems, $usernamems, $passwordms) or die("Connection failed: " . mysqli_connect_error());
?>
