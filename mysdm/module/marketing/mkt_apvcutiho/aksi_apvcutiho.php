<?php
session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    
    include "../../../config/koneksimysqli.php";
    
    $module=$_GET['module'];
    $idmenu=$_GET['idmenu'];
    $act=$_GET['act'];
    
    $pnamalogin=$_SESSION['NAMALENGKAP'];
    $pidgroup=$_SESSION['GROUP'];
    
if ($module=="mktapprovecutiho") {
        
    $noidbr=$_POST['unobr'];
    if ($noidbr=="()") $noidbr = "";
    $stsspv=$_POST['ket'];
    $karyawanapv=$_POST['ukaryawan'];
    
    if (empty($karyawanapv)) $karyawanapv=$_SESSION['IDCARD'];
    
    
    $tampil=mysqli_query($cnmy, "select jabatanId from hrd.karyawan where karyawanid='$karyawanapv'");
    $pr= mysqli_fetch_array($tampil);
    $pjabatanid=$pr['jabatanId'];
    if (empty($pjabatanid)) {
        $tampil=mysqli_query($cnmy, "select jabatanId from dbmaster.t_karyawan_posisi where karyawanid='$karyawanapv'");
        $pr= mysqli_fetch_array($tampil);
        $pjabatanid=$pr['jabatanId'];
    }
    
    
    
    $tampil=mysqli_query($cnmy, "select LEVELPOSISI from dbmaster.jabatan_level WHERE jabatanId='$pjabatanid'");
    $pr= mysqli_fetch_array($tampil);
    $plvlposisi=$pr['LEVELPOSISI'];
    
    $papproveby="";
    if ($pjabatanid=="13") {
    }else{
        $papproveby="apvatasan";
    }
    
    if (empty($pjabatanid) OR empty($papproveby) OR empty($karyawanapv)) {
        echo "Anda tidak berhak proses...";
        mysqli_close($cnmy); exit;
    }
    
    
    
    
    $noteapv = "tidak ada data yang diapprove";
    $noteapv="$noidbr, $karyawanapv";
    if (!empty($noidbr) AND !empty($karyawanapv)) {
        
        if ($act=="simpan_ttdallam") {
            $gbrapv=$_POST['uttd'];
            
            $fielduntukttd="";
            $fieldtglapprovenya="";
            
            $fielduntukttd=" a.tgl_atasan4=NOW(), b.gbr_atasan4='$gbrapv' ";
            $fieldtglapprovenya= " (IFNULL(a.tgl_atasan4,'')='' OR IFNULL(a.tgl_atasan4,'0000-00-00 00:00:00')='0000-00-00 00:00:00') AND (IFNULL(a.tgl_atasan3,'')<>'' AND IFNULL(a.tgl_atasan3,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
            
            //echo "$noidbr - APPROVE BY : $papproveby";
            
            
            if (!empty($fielduntukttd) AND !empty($fieldtglapprovenya)) {
                
                mysqli_query($cnmy, "update hrd.t_cuti0 a JOIN dbttd.t_cuti_ttd b on a.idcuti=b.idcuti SET $fielduntukttd WHERE "
                        . " a.idcuti IN $noidbr AND $fieldtglapprovenya");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                $noteapv = "data berhasil diapprove...";
            }
        }elseif ($act=="unapprove") {
            
            
            $fielduntukttd="";
            $fieldtglapprovenya="";
            
            $fielduntukttd=" a.tgl_atasan4=NULL, b.gbr_atasan4=NULL ";
            $fieldtglapprovenya= " (IFNULL(a.hrd_date,'')='' OR IFNULL(a.hrd_date,'0000-00-00 00:00:00')='0000-00-00 00:00:00') AND (IFNULL(a.tgl_atasan4,'')<>'' AND IFNULL(a.tgl_atasan4,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
            
            
            
            if (!empty($fielduntukttd) AND !empty($fieldtglapprovenya)) {
                
                mysqli_query($cnmy, "update hrd.t_cuti0 a JOIN dbttd.t_cuti_ttd b on a.idcuti=b.idcuti SET $fielduntukttd WHERE "
                        . " ( IFNULL(a.hrd_date,'')='' OR IFNULL(a.hrd_date,'0000-00-00 00:00:00')='0000-00-00 00:00:00' ) AND "
                        . " a.idcuti IN $noidbr AND $fieldtglapprovenya");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                
                $noteapv = "data berhasil diunapprove...";
            }
            

        }elseif ($act=="reject") {
            $pkethapus=$_POST['ketrejpen'];
            
            if (!empty($pkethapus)) $pkethapus = str_replace("'", " ", $pkethapus);
            
            
            if (!empty($pnamalogin) AND !empty($karyawanapv)) {
                mysqli_query($cnmy, "update hrd.t_cuti0 set stsnonaktif='Y', userid='$karyawanapv', "
                        . " keterangan=CONCAT(IFNULL(keterangan,''),'Ket Reject : $pkethapus', ', user reject : $pnamalogin') WHERE "
                        . " idcuti in $noidbr AND "
                        . " ( IFNULL(hrd_date,'')='' OR IFNULL(hrd_date,'0000-00-00 00:00:00')='0000-00-00 00:00:00' )");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                $noteapv = "data berhasil direject...";
            }
        }
        
    }
    
    
}
    
    
    mysqli_close($cnmy);
    echo $noteapv;


?>
