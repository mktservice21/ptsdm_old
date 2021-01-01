<?php

    session_start();
    include "../../config/koneksimysqli.php";
    
    $kodeinput = " AND kode=3 ";
    $apvfinance = " AND ifnull(fin,'')='' ";
    
    $module=$_GET['module'];
    $idmenu=$_GET['idmenu'];
    $act=$_GET['act'];
    $cnmy=$cnmy;
    $dbname = "dbmaster";
    
    $pidapprove=$_SESSION['IDCARD'];
    
if ($module=="finproscabiayaotc") {
    $noidbr=$_POST['unobr'];
    if ($noidbr=="()") $noidbr = "";
    $stsspv=$_POST['ket'];
    $karyawanapv=$_POST['ukaryawan'];
    
    $noteapv = "tidak ada data yang diapprove";
    
    
    if (!empty($noidbr) AND !empty($karyawanapv)) {
        
        if ($act=="simpan_ttdallam") {
            $gbrapv=$_POST['uttd'];
                
            mysqli_query($cnmy, "update $dbname.t_ca0 set fin='$pidapprove', tgl_fin=NOW(), gbr_fin='$gbrapv' WHERE idca in $noidbr AND IFNULL(tgl_atasan4,'') <>''");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

            mysqli_query($cnmy, "update $dbname.t_brrutin0 set fin='$pidapprove', tgl_fin=NOW(), gbr_fin='$gbrapv' WHERE idrutin in $noidbr AND IFNULL(tgl_atasan4,'') <>''");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

            $noteapv = "data berhasil diproses...";
            
        }elseif ($act=="unapprove") {
            
            mysqli_query($cnmy, "update $dbname.t_ca0 set fin=NULL, tgl_fin=NULL, gbr_fin=NULL WHERE idca in $noidbr");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

            mysqli_query($cnmy, "update $dbname.t_brrutin0 set fin=NULL, tgl_fin=NULL, gbr_fin=NULL WHERE idrutin in $noidbr");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            
            $noteapv = "data berhasil diunproses...";
            
        }elseif ($act=="reject") {
            
            $kethapus = $_POST['ketrejpen'];
            if ($kethapus=="null") $kethapus="";
            if (!empty($kethapus)) $kethapus =", Ket Reject : ".$kethapus;
            if (!empty($kethapus)) $kethapus = str_replace("'", " ", $kethapus);
                
            mysqli_query($cnmy, "update $dbname.t_ca0 set stsnonaktif='Y', userid='$pidapprove', keterangan=CONCAT(keterangan,'$kethapus',', $_SESSION[NAMALENGKAP], ', NOW()) WHERE idca in $noidbr AND IFNULL(tgl_fin,'')=''");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

            mysqli_query($cnmy, "update $dbname.t_brrutin0 set stsnonaktif='Y', userid='$pidapprove', keterangan=CONCAT(keterangan,'$kethapus',', $_SESSION[NAMALENGKAP], ', NOW()) WHERE idrutin in $noidbr AND IFNULL(tgl_fin,'')=''");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $noteapv = "data berhasil direject...";
            
        }
        
    }
    
    echo $noteapv;
    
}

?>

