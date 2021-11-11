<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    session_start();

    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }
  
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    
    
    if ($pmodule=="mapbagislsmanual") {
        
        if ($pact=="hapusdatasalahmapinguser") {
            $berhasil="Tidak ada data yang diproses...";
            
            $pidkode=$_POST['ukode'];
            $pdistid=$_POST['udistid'];
            $ptgljual=$_POST['utgljual'];
            $pidprod=$_POST['uproduk'];
            $pfakturid=$_POST['ufakturid'];
            $piduser=$_POST['uidusr'];
            
            if (!empty($pidkode) AND !empty($pfakturid)) {
                include "../../config/koneksimysqli.php";
                $query = "DELETE FROM mkt.msales_new WHERE noMSales='$pidkode' AND user1='$piduser' AND fakturid='$pfakturid' AND tgl='$ptgljual' AND iprodid='$pidprod' AND distid='$pdistid' LIMIT 1";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                mysqli_close($cnmy);
                
                $berhasil="berhasil";
            }
            
            
        }
        echo $berhasil; exit;
        
    }
?>