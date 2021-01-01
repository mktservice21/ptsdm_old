<?php
session_start();
include "../../config/koneksimysqli.php";

if (!isset($_SESSION['IDCARD'])) {
    echo "ANDA HARUS LOGIN ULANG...!!!";
    exit;
}

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

// Hapus jabatan
if ($module=='gimicgroupprod' AND $act=='hapus'){
    $query=mysqli_query($cnmy, "select * from dbmaster.t_divisi_gimick WHERE DIVISIID   = '$_GET[id]'");
    $r=  mysqli_fetch_array($query);
    if ($r['STSAKTIF']=="Y")
        mysqli_query($cnmy, "update dbmaster.t_divisi_gimick set STSAKTIF='N' WHERE DIVISIID='$_GET[id]'");
    else
        mysqli_query($cnmy, "update dbmaster.t_divisi_gimick set STSAKTIF='Y' WHERE DIVISIID='$_GET[id]'");
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}

// Input modul
elseif ($module=='gimicgroupprod' AND $act=='input'){
  
  // Input data jabatan
  mysqli_query($cnmy, "INSERT INTO dbmaster.t_divisi_gimick(PILIHAN, DIVISIID, DIVISINM,USERID)
	                       VALUES('$_POST[cb_gprod]', '$_POST[e_divisiid]', '$_POST[e_nmgrpprod]', '$_SESSION[IDCARD]')");
  $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
  header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}

// Update modul
elseif ($module=='gimicgroupprod' AND $act=='update'){
  
  mysqli_query($cnmy, "UPDATE dbmaster.t_divisi_gimick SET DIVISINM = '$_POST[e_nmgrpprod]', USERID='$_SESSION[IDCARD]', PILIHAN='$_POST[cb_gprod]' 
                          WHERE DIVISIID   = '$_POST[id]'");
  $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
  header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
?>
