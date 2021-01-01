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
    
    $puserpilihinput="";
    
    $pnamalogin=$_SESSION['NAMALENGKAP'];
    $pgroupid = trim($_SESSION['GROUP']);
    
    if ($module == "approvedirekturklaimadm") {
        $puserpilihinput=$_SESSION['IDCARD'];
        if ($pgroupid=="40" OR $pgroupid=="43") {
            
        }else{
            echo "tidak berhak..."; exit;
        }
    }
    
if ($module=="apvklaimdiscmkt" OR $module=="dirapvklaimdisc" OR $module=="approvedirekturklaimadm") {
    
    $noidbr=$_POST['unobr'];
    if ($noidbr=="()") $noidbr = "";
    $stsspv=$_POST['ket'];
    $karyawanapv=$_POST['ukaryawan'];
    
    if (empty($karyawanapv)) {
        if ($module == "approvedirekturklaimadm") {
            echo "tidak berhak..."; exit;
        }else{
            $karyawanapv=$_SESSION['IDCARD'];
        }
    }
    
    
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
    if ($pjabatanid=="01" OR $pjabatanid=="02") {
        $papproveby="apvdirmkt";
    }elseif ($pjabatanid=="04" OR $pjabatanid=="05" OR $pjabatanid=="36") {
        $papproveby="apvgsm";
    }
    
    
    if (empty($pjabatanid) OR empty($papproveby) OR empty($karyawanapv)) {
        echo "Anda tidak berhak proses...";
        mysqli_close($cnmy); exit;
    }
    
    
    if ($karyawanapv=="0000001854") {
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
            if ($papproveby=="apvgsm") {
                $fielduntukttd=" b.atasan4='$karyawanapv', b.tgl_atasan4=NOW(), b.gbr_atasan4='$gbrapv', useridadm='$puserpilihinput' ";
                $fieldtglapprovenya= " (IFNULL(b.tgl_atasan4,'')='' OR IFNULL(b.tgl_atasan4,'0000-00-00 00:00:00')='0000-00-00 00:00:00') ";
            }elseif ($papproveby=="apvdirmkt") {
                $fielduntukttd=" b.atasan5='$karyawanapv', b.tgl_atasan5=NOW(), b.gbr_atasan5='$gbrapv', useridadm='$puserpilihinput' ";
                $fieldtglapprovenya= " (IFNULL(b.tgl_atasan5,'')='' OR IFNULL(b.tgl_atasan5,'0000-00-00 00:00:00')='0000-00-00 00:00:00') AND (IFNULL(b.tgl_atasan4,'')<>'' AND IFNULL(b.tgl_atasan4,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
            }
            
            if (!empty($fielduntukttd) AND !empty($fieldtglapprovenya)) {
                
                mysqli_query($cnmy, "update hrd.klaim a JOIN dbttd.klaim_ttd b on a.klaimId=b.klaimId SET $fielduntukttd WHERE "
                        . " a.klaimId IN $noidbr AND $fieldtglapprovenya");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                $noteapv = "data berhasil diapprove...";
                
            }
            
            
            
        }elseif ($act=="unapprove") {
            
            $fielduntukttd="";
            $fieldtglapprovenya="";
            if ($papproveby=="apvgsm") {
                $fielduntukttd=" b.tgl_atasan4=NULL, b.gbr_atasan4=NULL, useridadm='' ";
                $fieldtglapprovenya= " (IFNULL(b.tgl_atasan5,'')='' OR IFNULL(b.tgl_atasan5,'0000-00-00 00:00:00')='0000-00-00 00:00:00') AND (IFNULL(b.tgl_atasan4,'')<>'' AND IFNULL(b.tgl_atasan4,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
            }elseif ($papproveby=="apvdirmkt") {
                $fielduntukttd=" b.tgl_atasan5=NULL, b.gbr_atasan5=NULL, useridadm='' ";
                $fieldtglapprovenya= " (IFNULL(b.tgl_atasan5,'')<>'' AND IFNULL(b.tgl_atasan5,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
            }
            
            
            if (!empty($fielduntukttd) AND !empty($fieldtglapprovenya)) {
                
                mysqli_query($cnmy, "update hrd.klaim a JOIN dbttd.klaim_ttd b on a.klaimId=b.klaimId SET $fielduntukttd WHERE "
                        . " a.klaimId IN $noidbr AND $fieldtglapprovenya");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                
                $noteapv = "data berhasil diunapprove...";
                
            }
            
            
            
            
        }
        
    }
    
    
    
    
}
    
    mysqli_close($cnmy);
    echo $noteapv;
    
?>

