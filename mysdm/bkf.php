<?php
    session_start();
    $nmfiles="images/files/".$_GET['id'];
    
    // Store the file name into variable 
    $file = $nmfiles; 
    $filename = $_GET['id']; 
    // Header content type 
    header('Content-type: application/pdf'); 
    header('Content-Disposition: inline; filename="' . $filename . '"'); 
    header('Content-Transfer-Encoding: binary'); 
    header('Accept-Ranges: bytes'); 
    // Read the file 
    @readfile($file); 
    
    
  
?> 