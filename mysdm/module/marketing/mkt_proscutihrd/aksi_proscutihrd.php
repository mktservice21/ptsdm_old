<?php
session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    
    include "../../config/koneksimysqli.php";
    
    $module=$_GET['module'];
    $idmenu=$_GET['idmenu'];
    $act=$_GET['act'];
    
    $pnamalogin=$_SESSION['NAMALENGKAP'];
    
    
if ($module=="mktproscutihrd") {
        
    $noidbr=$_POST['unobr'];
    if ($noidbr=="()") $noidbr = "";
    $stsspv=$_POST['ket'];
    $karyawanapv=$_POST['ukaryawan'];
    
    $karyawanapv=$_SESSION['IDCARD'];
    
    
    if (empty($karyawanapv)) {
        echo "Anda tidak berhak proses...";
        mysqli_close($cnmy); exit;
    }
    
    
    
    
    $noteapv = "tidak ada data yang diapprove";
    //$noteapv="$noidbr, $karyawanapv";
    if (!empty($noidbr) AND !empty($karyawanapv)) {
        
        if ($act=="simpan_ttdallam") {
            $gbrapv=$_POST['uttd'];
            
            mysqli_query($cnmy, "update hrd.t_cuti0 a JOIN dbttd.t_cuti_ttd b on a.idcuti=b.idcuti SET a.hrd_user='$karyawanapv', "
                    . " a.hrd_date=NOW(), b.gbr_hrd='$gbrapv' WHERE "
                    . " a.idcuti IN $noidbr AND (IFNULL(tgl_atasan3,'')<>'' AND IFNULL(tgl_atasan3,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00')");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

            $noteapv = "data berhasil diproses...";
                
                
        }elseif ($act=="unapprove") {
            
            mysqli_query($cnmy, "update hrd.t_cuti0 SET hrd_user=NULL, hrd_date=NULL WHERE "
                    . " idcuti IN $noidbr AND (IFNULL(tgl_atasan3,'')<>'' AND IFNULL(tgl_atasan3,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00')");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
                mysqli_query($cnmy, "update hrd.t_cuti0 a JOIN dbttd.t_cuti_ttd b on a.idcuti=b.idcuti SET a.hrd_user=NULL, "
                        . " a.hrd_date=NULL, b.gbr_hrd=NULL WHERE "
                        . " ( IFNULL(a.hrd_date,'')='' OR IFNULL(a.hrd_date,'0000-00-00 00:00:00')='0000-00-00 00:00:00' ) AND "
                        . " a.idcuti IN $noidbr");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
            $noteapv = "batal proses berhasil...";
            
            

        }elseif ($act=="reject") {
            $pkethapus=$_POST['ketrejpen'];
            
            if (!empty($pkethapus)) $pkethapus = str_replace("'", " ", $pkethapus);
            
            
            if (!empty($pnamalogin) AND !empty($karyawanapv)) {
                mysqli_query($cnmy, "update hrd.t_cuti0 set stsnonaktif='Y', userid='$karyawanapv', "
                        . " keterangan=CONCAT(IFNULL(keterangan,''),' Ket Reject : $pkethapus', ' user : $pnamalogin') WHERE "
                        . " idcuti in $noidbr AND "
                        . " ( IFNULL(hrd_date,'')='' OR IFNULL(hrd_date,'0000-00-00 00:00:00')='0000-00-00 00:00:00' ) AND "
                        . " (IFNULL(tgl_atasan3,'')<>'' AND IFNULL(tgl_atasan3,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00')");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                $noteapv = "data berhasil direject...";
            }
        }
        
    }
    
    
}
    
    
    mysqli_close($cnmy);
    echo $noteapv;


?>
