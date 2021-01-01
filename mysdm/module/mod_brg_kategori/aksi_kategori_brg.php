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
if ($module=='barangkategori' AND $act=='hapus'){
    $query=mysqli_query($cnmy, "select * from dbmaster.t_barang_kategori WHERE IDKATEGORI   = '$_GET[id]'");
    $r=  mysqli_fetch_array($query);
    if ($r['STSAKTIF']=="Y")
        mysqli_query($cnmy, "update dbmaster.t_barang_kategori set STSAKTIF='N' WHERE IDKATEGORI='$_GET[id]'");
    else
        mysqli_query($cnmy, "update dbmaster.t_barang_kategori set STSAKTIF='Y' WHERE IDKATEGORI='$_GET[id]'");
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}

// Input modul
elseif ($module=='barangkategori' AND $act=='input'){
  
  // Input data jabatan
  mysqli_query($cnmy, "INSERT INTO dbmaster.t_barang_kategori(NAMA_KATEGORI,USERID)
	                       VALUES('$_POST[e_nmkategori]', '$_SESSION[IDCARD]')");
  header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}

// Update modul
elseif ($module=='barangkategori' AND $act=='update'){
  
  mysqli_query($cnmy, "UPDATE dbmaster.t_barang_kategori SET NAMA_KATEGORI = '$_POST[e_nmkategori]', USERID='$_SESSION[IDCARD]' 
                          WHERE IDKATEGORI   = '$_POST[id]'");
  header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
?>
