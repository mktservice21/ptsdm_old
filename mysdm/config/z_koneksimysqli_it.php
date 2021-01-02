<?php
    /* Database connection start */
	$servername = "203.142.71.82";
	$servername = "103.130.192.132";// untuk conect di navicat
    //$servername = "192.168.88.25";
    $username = "root";
    $password = "sdmmysqlserver2017";
	
    $username = "sdmuserapp";
    $password = "sdmuserapp123";
	
    
    //hilangkan
    /*
    $servername = "localhost";
    $username = "root";
    $password = "";
    */
    //===hilangkan
    
	
    //hilangkan
    /*
    
    $servername = "203.142.71.92:3303";
	$servernamems = "103.130.192.140:3303";
    $servername = "192.168.88.189:3303";
    $username = "root";
    $password = "Ganteng123456";
    
    //===hilangkan */
    
    $cnit = mysqli_connect($servername, $username, $password) or die("Connection failed: " . mysqli_connect_error());
?>
