<?php
session_start();
include "../../config/koneksimysqli.php";
//$cnmy=$cnit;
$dbname = "dbmaster";

if ($_GET['module']=="carikodesama") {
    $query=mysqli_query($cnmy, "select COA2 from $dbname.coa_level2 WHERE COA2   = '$_POST[ukode]'");
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


// Hapus jabatan
if ($module=='coalevel2' AND $act=='hapus'){
    $query=mysqli_query($cnmy, "select * from $dbname.coa_level2 WHERE COA2   = '$_GET[id]'");
    $r=  mysqli_fetch_array($query);
    if ($r['AKTIF2']=="Y")
        mysqli_query($cnmy, "update $dbname.coa_level2 set AKTIF2='N' WHERE COA2='$_GET[id]'");
    else
        mysqli_query($cnmy, "update $dbname.coa_level2 set AKTIF2='Y' WHERE COA2='$_GET[id]'");
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}

// Input modul
elseif ($module=='coalevel2' AND $act=='input'){
        
  // Input data jabatan
  mysqli_query($cnmy, "INSERT INTO $dbname.coa_level2(COA2, NAMA2, COA1, DIVISI2)
	                       VALUES('$_POST[id]', '$_POST[e_nmcoa]', '$_POST[cb_level]', '$_POST[cb_divisi]')");
  if (isset($_POST['rb_gol'])) {
    mysqli_query($cnmy, "UPDATE $dbname.coa_level2 SET GOL2 = '$_POST[rb_gol]' WHERE COA2 = '$_POST[id]'");
  }
  
  header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}

// Update modul
elseif ($module=='coalevel2' AND $act=='update'){
  
  mysqli_query($cnmy, "UPDATE $dbname.coa_level2 SET NAMA2 = '$_POST[e_nmcoa]', COA1='$_POST[cb_level]', DIVISI2='$_POST[cb_divisi]'  
                          WHERE COA2   = '$_POST[id]'");
  if (isset($_POST['rb_gol'])) {
    mysqli_query($cnmy, "UPDATE $dbname.coa_level2 SET GOL2 = '$_POST[rb_gol]' WHERE COA2 = '$_POST[id]'");
  }
  
  header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
?>
