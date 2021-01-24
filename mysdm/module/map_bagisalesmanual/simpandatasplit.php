<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    session_status();

    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        //echo "ANDA HARUS LOGIN ULANG...";
        //exit;
    }
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    
    if ($pmodule=="mapbagislsmanual") {
        
        if ($pact=="datasimpansplit") {
            include "../../config/koneksimysqli_ms.php";
            
            
            mysqli_close($cnms);
            echo "berhasil";
            exit;
        }
        
    }
    
    echo "tidak ada data yang disimpan...";
    
?>

