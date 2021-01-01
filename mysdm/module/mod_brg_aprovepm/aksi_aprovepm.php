<?php
session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    
    include "../../config/koneksimysqli.php";
    
    $module=$_GET['module'];
    $idmenu=$_GET['idmenu'];
    $act=$_GET['act'];
    
if ($module=="gimicapvbrgkeluar") {
        
    $noidbr=$_POST['unobr'];
    if ($noidbr=="()") $noidbr = "";
    $stsspv=$_POST['ket'];
    $karyawanapv=$_POST['ukaryawan'];
    $noteapv = "tidak ada data yang diapprove";
    $noteapv="$noidbr, $karyawanapv";
    if (!empty($noidbr) AND !empty($karyawanapv)) {
        
        if ($act=="simpan_ttdallam") {
            $gbrapv=$_POST['uttd'];
            
            mysqli_query($cnmy, "update dbmaster.t_barang_keluar a JOIN dbttd.t_barang_keluar_ttd b on a.IDKELUAR=b.IDKELUAR set a.PM_APV='$karyawanapv', a.PM_TGL=NOW(), b.PM_GBR='$gbrapv' WHERE "
                    . " a.IDKELUAR in $noidbr AND "
                    . " ( IFNULL(a.PM_TGL,'')='' OR IFNULL(a.PM_TGL,'0000-00-00')='0000-00-00' OR IFNULL(a.PM_TGL,'0000-00-00 00:00:00')='0000-00-00 00:00:00')"
                    . " AND ( IFNULL(a.APV1_TGL,'')='' OR IFNULL(a.APV1_TGL,'0000-00-00')='0000-00-00' OR IFNULL(a.APV1_TGL,'0000-00-00 00:00:00')='0000-00-00 00:00:00')");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $noteapv = "data berhasil diapprove...";
            
        }elseif ($act=="unapprove") {
            
            mysqli_query($cnmy, "update dbmaster.t_barang_keluar a JOIN dbttd.t_barang_keluar_ttd b on a.IDKELUAR=b.IDKELUAR "
                    . " LEFT JOIN dbmaster.t_barang_keluar_kirim c "
                    . " on a.IDKELUAR=c.IDKELUAR AND b.IDKELUAR=c.IDKELUAR SET a.PM_APV=NULL, a.PM_TGL=NULL, b.PM_GBR=NULL WHERE a.IDKELUAR in $noidbr AND "
                    . " ( IFNULL(c.TGLKIRIM,'')='' OR IFNULL(c.TGLKIRIM,'0000-00-00 00:00:00')='0000-00-00 00:00:00' ) AND IFNULL(c.GRPPRINT,'')=''");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $noteapv = "data berhasil diunapprove...";
        }elseif ($act=="reject") {
            
            mysqli_query($cnmy, "update dbmaster.t_barang_keluar set STSNONAKTIF='Y', USERID='$karyawanapv' WHERE "
                    . " IDKELUAR in $noidbr AND "
                    . " ( IFNULL(PM_TGL,'')='' OR IFNULL(PM_TGL,'0000-00-00')='0000-00-00' OR IFNULL(PM_TGL,'0000-00-00 00:00:00')='0000-00-00 00:00:00')");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            $noteapv = "data berhasil direject...";
        }
        
    }
    
    
}
    
    
    mysqli_close($cnmy);
    echo $noteapv;


?>
