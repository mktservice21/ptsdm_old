<?php
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    session_start();

    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }
    
    $pidcard=$_SESSION['IDCARD'];
    $piduser=$_SESSION['USERID'];
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    $berhasil="tidak ada data yang diproses...";
    
    //$berhasil = "$pmodule, $pact, $pidmenu";
    
    if ( ($pmodule=="detailsaleslappersektorreg" OR $pmodule=="detailsaleslappersektor" OR $pmodule=="detailsaleslappersektordm" OR $pmodule=="detailsaleslappersektorsm") AND $pact=="simpansektor") {
        $pidcustomer=$_POST['uidcust'];
        $pidsektor=$_POST['uidsektor'];
        
        if (!empty($pidcustomer) AND !empty($pidsektor)) {
            
            include "../../config/koneksimysqli.php";
            
            $query = "UPDATE mkt.icust SET isektorid='$pidsektor', User1='$pidcard' WHERE icustid='$pidcustomer' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            
            mysqli_close($cnmy);
            $berhasil="berhasil";
        }
        
    }
    
    echo $berhasil; exit;
    
?>