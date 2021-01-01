<?php
session_start();
include "../../config/koneksimysqli.php";
//$cnmy=$cnit;
$dbname = "dbmaster";

if ($_GET['module']=="carikodesama") {
    $query=mysqli_query($cnmy, "select COA3 from $dbname.coa_level3 WHERE COA3   = '$_POST[ukode]'");
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
if ($module=='coalevel3' AND $act=='hapus'){
    $query=mysqli_query($cnmy, "select * from $dbname.coa_level3 WHERE COA3   = '$_GET[id]'");
    $r=  mysqli_fetch_array($query);
    if ($r['AKTIF3']=="Y")
        mysqli_query($cnmy, "update $dbname.coa_level3 set AKTIF3='N' WHERE COA3='$_GET[id]'");
    else
        mysqli_query($cnmy, "update $dbname.coa_level3 set AKTIF3='Y' WHERE COA3='$_GET[id]'");
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}

// Input modul
elseif ($module=='coalevel3' AND $act=='input'){
        
  // Input data jabatan
  mysqli_query($cnmy, "INSERT INTO $dbname.coa_level3(COA3, NAMA3, COA2, DESKRIPSI3)
	                       VALUES('$id', '$_POST[e_nmcoa]', '$_POST[cb_level]', '$_POST[e_desk]')");
  if (isset($_POST['rb_gol'])) {
    mysqli_query($cnmy, "UPDATE $dbname.coa_level3 SET GOL3 = '$_POST[rb_gol]' WHERE COA3 = '$_POST[id]'");
  }
  if (isset($_POST['rb_gol'])) {
    mysqli_query($cnmy, "UPDATE $dbname.coa_level3 SET GOL3 = '$_POST[rb_gol]' WHERE COA3 = '$id'");
  }
  
  header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}

// Update modul
elseif ($module=='coalevel3' AND $act=='update'){
  
  mysqli_query($cnmy, "UPDATE $dbname.coa_level3 SET NAMA3 = '$_POST[e_nmcoa]', COA2='$_POST[cb_level]', DESKRIPSI3='$_POST[e_desk]'  
                          WHERE COA3   = '$_POST[id]'");
  if (isset($_POST['rb_gol'])) {
    mysqli_query($cnmy, "UPDATE $dbname.coa_level3 SET GOL3 = '$_POST[rb_gol]' WHERE COA3 = '$_POST[id]'");
  }
  if (isset($_POST['rb_gol'])) {
    mysqli_query($cnmy, "UPDATE $dbname.coa_level3 SET GOL3 = '$_POST[rb_gol]' WHERE COA3 = '$_POST[id]'");
  }
  
  header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
?>
