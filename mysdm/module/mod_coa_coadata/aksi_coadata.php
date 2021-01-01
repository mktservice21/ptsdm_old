<?php
session_start();
include "../../config/koneksimysqli.php";

if ($_GET['module']=="carikodesama") {
    $query=mysqli_query($cnmy, "select COA_KODE from dbmaster.coa WHERE COA_KODE   = '$_POST[ukode]'");
    $ketemu=  mysqli_num_rows($query);
    if ($ketemu>0)
        echo "sudah ada";
    else
        echo "";
    
    exit;
}

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

$id=  str_replace("_", "", $_POST['id']);
$arr_kata = explode("-",$id);
if (empty($arr_kata[1])) $id=$arr_kata[0];
if (empty($id)) $id=str_replace("_", "", $_POST['id']);

$arr_kata2 = explode("-",$id);
if (isset($arr_kata2[2])) {
    if (empty($arr_kata2[2])) $id=$arr_kata2[0]."-".$arr_kata2[1];
}



// Hapus jabatan
if ($module=='coadata' AND $act=='hapus'){
    $query=mysqli_query($cnmy, "select * from dbmaster.coa WHERE COA_KODE   = '$_GET[id]'");
    $r=  mysqli_fetch_array($query);
    if ($r['AKTIF']=="Y")
        mysqli_query($cnmy, "update dbmaster.coa set AKTIF='N' WHERE COA_KODE='$_GET[id]'");
    else
        mysqli_query($cnmy, "update dbmaster.coa set AKTIF='Y' WHERE COA_KODE='$_GET[id]'");
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}

// Input modul
elseif ($module=='coadata' AND $act=='input'){
  $sldawal_00=str_replace(",","", $_POST['e_saldoawal']);
  
  // Input data jabatan
  mysqli_query($cnmy, "INSERT INTO dbmaster.coa(COA_KODE, COA_NAMA, COA4, SLDAWAL_00)
	                       VALUES('$id', '$_POST[e_nmcoa]', '$_POST[cb_level]', '$sldawal_00')");
  if (isset($_POST['rb_tipe'])) {
    mysqli_query($cnmy, "UPDATE dbmaster.coa SET TIPE = '$_POST[rb_tipe]' WHERE COA_KODE = '$id'");
  }
  
  if (isset($_POST['rb_gol'])) {
    mysqli_query($cnmy, "UPDATE dbmaster.coa SET GOL = '$_POST[rb_gol]' WHERE COA_KODE = '$id'");
  }
  
  header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}

// Update modul
elseif ($module=='coadata' AND $act=='update'){
  $sldawal_00=str_replace(",","", $_POST['e_saldoawal']);
  mysqli_query($cnmy, "UPDATE dbmaster.coa SET COA_NAMA = '$_POST[e_nmcoa]', COA4='$_POST[cb_level]', SLDAWAL_00='$sldawal_00'   
                          WHERE COA_KODE   = '$_POST[id]'");
  if (isset($_POST['rb_tipe'])) {
    mysqli_query($cnmy, "UPDATE dbmaster.coa SET TIPE = '$_POST[rb_tipe]' WHERE COA_KODE = '$_POST[id]'");
  }
  if (isset($_POST['rb_gol'])) {
    mysqli_query($cnmy, "UPDATE dbmaster.coa SET GOL = '$_POST[rb_gol]' WHERE COA_KODE = '$_POST[id]'");
  }
  
  header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
?>
