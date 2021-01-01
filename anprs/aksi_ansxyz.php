<?php

    session_start();
    include "../mysdm/config/koneksimysqli.php";
    
    $module=$_GET['module'];
    $idmenu=$_GET['idmenu'];
    $act=$_GET['act'];
    $cnmy=$cnmy;
    $dbname = "dbmaster";
    
    $noteapv = "Tidak ada data yang diapprove";
    if ($module=="apvdirpilihlink") {
        
        
        $noidbr=$_POST['unobr'];
        if ($noidbr=="()") $noidbr = "";
        $stsspv=$_POST['ket'];
        $karyawanapv=$_POST['ukaryawan'];
        $pidgrouplnk=$_POST['uidgrplnk'];
        
        
        
        if ($act=="simpan_ttdallam") {
            $gbrapv=$_POST['uttd'];
            
            mysqli_query($cnmy, "update $dbname.t_suratdana_br set dir='$karyawanapv', tgl_dir=NOW(), gbr_dir='$gbrapv' WHERE idinput in $noidbr AND IFNULL(tgl_dir2,'')='' AND IFNULL(tgl_apv2,'')<>''");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            mysqli_query($cnmy, "update $dbname.t_suratdana_br_link set stsapvdir='Y', tgl_dir=NOW() WHERE idgroup='$pidgrouplnk'");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $noteapv = "data berhasil diapprove...";
            
        }elseif ($act=="unapprove") {
            
            mysqli_query($cnmy, "update $dbname.t_suratdana_br set dir=NULL, tgl_dir=NULL, gbr_dir=NULL WHERE idinput in $noidbr AND IFNULL(tgl_dir2,'')=''");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            mysqli_query($cnmy, "update $dbname.t_suratdana_br_link set stsapvdir=null, tgl_dir=null WHERE idgroup='$pidgrouplnk'");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            
            $noteapv = "data berhasil diunapprove...";
            
        }
        
        
    }
    
    
    mysqli_close($cnmy);
    echo $noteapv;
?>
