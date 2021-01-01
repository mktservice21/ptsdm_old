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
    
    $pnamalogin=$_SESSION['NAMALENGKAP'];
    
    
if ($module=="finproseskkcab") {
        
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
            mysqli_query($cnmy, "update dbmaster.t_kaskecilcabang SET fin='', tgl_fin=NOW() WHERE "
                    . " idkascab IN $noidbr AND (IFNULL(tgl_atasan3,'')<>'' AND IFNULL(tgl_atasan3,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00')");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

            $noteapv = "data berhasil diproses...";
                
                
        }elseif ($act=="unapprove") {
            
            mysqli_query($cnmy, "update dbmaster.t_kaskecilcabang SET fin=NULL, tgl_fin=NULL WHERE "
                    . " idkascab IN $noidbr AND (IFNULL(tgl_atasan3,'')<>'' AND IFNULL(tgl_atasan3,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00')");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $noteapv = "batal proses berhasil...";
            
            

        }elseif ($act=="reject") {
            $pkethapus=$_POST['ketrejpen'];
            
            if (!empty($pkethapus)) $pkethapus = str_replace("'", " ", $pkethapus);
            
            
            if (!empty($pnamalogin) AND !empty($karyawanapv)) {
                mysqli_query($cnmy, "update dbmaster.t_kaskecilcabang set stsnonaktif='Y', userid='$karyawanapv', "
                        . " keterangan=CONCAT(keterangan,' Ket Reject : $pkethapus', ' user : $pnamalogin') WHERE "
                        . " idkascab in $noidbr AND "
                        . " ( IFNULL(tgl_fin,'')='' OR IFNULL(tgl_fin,'0000-00-00 00:00:00')='0000-00-00 00:00:00' ) AND "
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
