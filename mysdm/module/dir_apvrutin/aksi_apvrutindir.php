<?php

    session_start();
    include "../../config/koneksimysqli.php";
    
    $kodeinput = " AND kode=3 ";
    $apvfinance = " AND ifnull(tgl_fin,'')='' ";
    
    $module=$_GET['module'];
    $idmenu=$_GET['idmenu'];
    $act=$_GET['act'];
    $cnmy=$cnmy;
    $dbname = "dbmaster";
    
if ($module=="appdirrutin") {
    $noidbr=$_POST['unobr'];
    if ($noidbr=="()") $noidbr = "";
    $stsspv=$_POST['ket'];
    $karyawanapv=$_POST['ukaryawan'];
    $noteapv = "tidak ada data yang diapprove";
    
    if (!empty($noidbr) AND !empty($karyawanapv)) {
        
        if ($act=="simpan_ttdallam") {
            //include "../../config/ttdkosong.php";
            //$ttdkosong = ttdimage ();
            $gbrapv=$_POST['uttd'];
            //if ($ttdkosong==$gbrapv) { echo "ttdkosong"; exit; }
            
            mysqli_query($cnmy, "update $dbname.t_brrutin0 set dir='$karyawanapv', tgl_dir=NOW(), gbr_dir='$gbrapv' WHERE idrutin in $noidbr $apvfinance");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $noteapv = "data berhasil diapprove...";
            
        }elseif ($act=="unapprove") {
            
            mysqli_query($cnmy, "update $dbname.t_brrutin0 set dir=NULL, tgl_dir=NULL, gbr_dir=NULL WHERE idrutin in $noidbr $apvfinance");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $noteapv = "data berhasil diunapprove...";
            
        }elseif ($act=="reject") {
            $kethapus = $_POST['ketrejpen'];
            if ($kethapus=="null") $kethapus="";
            if (!empty($kethapus)) $kethapus =", Ket Reject : ".$kethapus;
            if (!empty($kethapus)) $kethapus = str_replace("'", " ", $kethapus);
            
            mysqli_query($cnmy, "update $dbname.t_brrutin0 set stsnonaktif='Y', "
                    . " keterangan=CONCAT(keterangan,'$kethapus',', $_SESSION[NAMALENGKAP], ', NOW()) WHERE idrutin in $noidbr $apvfinance");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $noteapv = "data berhasil direject...";
        }
    }
    
    echo $noteapv;
    
}
    
?>

