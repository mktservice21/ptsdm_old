<?php
session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    
    date_default_timezone_set('Asia/Jakarta');
    //ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    
    include "../../config/koneksimysqli.php";
    
    $module=$_GET['module'];
    $idmenu=$_GET['idmenu'];
    $act=$_GET['act'];
    
if ($module=="apvbrutinho") {
        
    $noidbr=$_POST['unobr'];
    if ($noidbr=="()") $noidbr = "";
    $stsspv=$_POST['ket'];
    $karyawanapv=$_POST['ukaryawan'];
    $noteapv = "tidak ada data yang diapprove";
    $noteapv="$noidbr, $karyawanapv";
    if (!empty($noidbr) AND !empty($karyawanapv)) {
        
        if ($act=="simpan_ttdallam") {
            $gbrapv=$_POST['uttd'];
            
            mysqli_query($cnmy, "update dbmaster.t_brrutin0 SET tgl_atasan4=NOW(), gbr_atasan4='$gbrapv' WHERE atasan4='$karyawanapv' AND "
                    . " idrutin in $noidbr AND "
                    . " ( IFNULL(tgl_atasan4,'')='' OR IFNULL(tgl_atasan4,'0000-00-00')='0000-00-00' OR IFNULL(tgl_atasan4,'0000-00-00 00:00:00')='0000-00-00 00:00:00' ) "
                    . " AND ( IFNULL(tgl_fin,'0000-00-00')='0000-00-00' OR IFNULL(tgl_fin,'0000-00-00 00:00:00')='0000-00-00 00:00:00' )");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $noteapv = "data berhasil diapprove...";
            
        }elseif ($act=="unapprove") {
            
            mysqli_query($cnmy, "update dbmaster.t_brrutin0 SET tgl_atasan4=NULL, gbr_atasan4=NULL WHERE atasan4='$karyawanapv' AND idrutin in $noidbr AND "
                    . " ( IFNULL(tgl_fin,'')='' OR IFNULL(tgl_fin,'0000-00-00 00:00:00')='0000-00-00 00:00:00' )");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $noteapv = "data berhasil diunapprove...";
        }elseif ($act=="reject") {
            
            mysqli_query($cnmy, "update dbmaster.t_brrutin0 set stsnonaktif='Y', USERID='$karyawanapv' WHERE atasan4='$karyawanapv' AND "
                    . " idrutin in $noidbr AND "
                    . " ( IFNULL(tgl_atasan4,'')='' OR IFNULL(tgl_atasan4,'0000-00-00')='0000-00-00' OR IFNULL(tgl_atasan4,'0000-00-00 00:00:00')='0000-00-00 00:00:00' ) "
                    . " AND ( IFNULL(tgl_fin,'0000-00-00')='0000-00-00' OR IFNULL(tgl_fin,'0000-00-00 00:00:00')='0000-00-00 00:00:00' )");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            $noteapv = "data berhasil direject...";
        }
        
    }
    
    
}
    
    
    mysqli_close($cnmy);
    echo $noteapv;


?>
