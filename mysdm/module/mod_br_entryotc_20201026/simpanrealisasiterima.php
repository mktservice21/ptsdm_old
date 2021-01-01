<?php
    date_default_timezone_set('Asia/Jakarta'); 
    session_start(); 
    
    //ini_set('display_errors', '0');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    $berhasil="tidak ada data yang diproses";
    include "../../config/koneksimysqli.php";
    
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    
    if ($pmodule=="simpanrealisasiterimaotc") {
        if ($pact=="hapus") {
            $kodenya=$_POST['uidbrotc'];
            
            $query = "update hrd.br_otc set tglreal='0000-00-00', "
                    . "  realisasi='0', lampiran='N', ca='Y' where brOtcId='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            //update modif terima
            $query = "update hrd.br_otc_ttd SET MODIFTERIMAID=NULL, "
                    . " MODIFTERIMADATE=NULL WHERE brOtcId='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query);
            
            
            $berhasil="data berhasil dihapus...";
        }
        
        
        if ($pact=="simpan") {
            
            $kodenya=$_POST['uidbrotc'];
            $pjumreal=$_POST['ujmlreal'];
            $datetrm=$_POST['utglreal'];
            
            if (empty($datetrm)) {
                exit;
            }
            
            $prprealisasi=str_replace(",","", $pjumreal);
            $ptgl= date("Y-m-d", strtotime($datetrm));
            
            //noslip='$pnoslip', 
            $query = "update hrd.br_otc set tglreal='$ptgl', "
                    . "  realisasi='$prprealisasi', lampiran='Y', ca='N' where brOtcId='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            //update modif terima
            $query = "update hrd.br_otc_ttd SET MODIFTERIMAID='$_SESSION[IDCARD]', "
                    . " MODIFTERIMADATE=NOW() WHERE brOtcId='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query);
            
            
            //$berhasil="$kodenya, $prprealisasi, $datetrm";
            $berhasil="";
        }
        
    }
    
    mysqli_close($cnmy);
    echo $berhasil;
?>
