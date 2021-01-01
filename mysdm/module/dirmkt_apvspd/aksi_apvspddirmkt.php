<?php

    session_start();
    include "../../config/koneksimysqli.php";
    
    $module=$_GET['module'];
    $idmenu=$_GET['idmenu'];
    $act=$_GET['act'];
    $cnmy=$cnmy;
    $dbname = "dbmaster";
    
    $pses_grpuser=$_SESSION['GROUP'];
    $pses_divisi=$_SESSION['DIVISI'];
    $pses_idcard=$_SESSION['IDCARD'];
    
    
if ($module=="apvdanamktdir") {
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
            
            mysqli_query($cnmy, "update $dbname.t_suratdana_br set dir='$karyawanapv', tgl_dir=NOW(), gbr_dir='$gbrapv' WHERE idinput in $noidbr AND IFNULL(tgl_dir2,'')='' AND IFNULL(tgl_apv2,'')<>''");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $noteapv = "data berhasil diapprove...";
            
        }elseif ($act=="unapprove") {
            
            mysqli_query($cnmy, "update $dbname.t_suratdana_br set dir=NULL, tgl_dir=NULL, gbr_dir=NULL WHERE idinput in $noidbr AND IFNULL(tgl_dir2,'')='' AND IFNULL(tgl_proses,'')=''");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $noteapv = "data berhasil diunapprove...";
            
        }elseif ($act=="reject") {
            $kethapus = $_POST['ketrejpen'];
            if ($kethapus=="null") $kethapus="";
            if (!empty($kethapus)) $kethapus =", Ket Reject : ".$kethapus;
            if (!empty($kethapus)) $kethapus = str_replace("'", " ", $kethapus);
            
            mysqli_query($cnmy, "update $dbname.t_suratdana_br set stsnonaktif='Y', "
                    . " keterangan=CONCAT(IFNULL(keterangan,''),'$kethapus',', $_SESSION[NAMALENGKAP], ', NOW()) WHERE idinput in $noidbr");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $noteapv = "data berhasil direject...";
        }
    }
    
    echo $noteapv;
    
}
    
?>

