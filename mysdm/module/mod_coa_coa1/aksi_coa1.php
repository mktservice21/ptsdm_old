<?php
session_start();
include "../../config/koneksimysqli.php";
//$cnmy=$cnit;
$dbname = "dbmaster";

if ($_GET['module']=="carikodesama") {
    $query=mysqli_query($cnmy, "select COA1 from $dbname.coa_level1 WHERE COA1   = '$_POST[ukode]'");
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
if ($module=='coalevel1' AND $act=='hapus'){
    $query=mysqli_query($cnmy, "select * from $dbname.coa_level1 WHERE COA1   = '$_GET[id]'");
    $r=  mysqli_fetch_array($query);
    if ($r['AKTIF1']=="Y")
        mysqli_query($cnmy, "update $dbname.coa_level1 set AKTIF1='N' WHERE COA1='$_GET[id]'");
    else
        mysqli_query($cnmy, "update $dbname.coa_level1 set AKTIF1='Y' WHERE COA1='$_GET[id]'");
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}

// Input modul
elseif ($module=='coalevel1' AND $act=='input'){
 
  // Input data jabatan
  mysqli_query($cnmy, "INSERT INTO $dbname.coa_level1(COA1, NAMA1)
	                       VALUES('$_POST[id]', '$_POST[e_nmcoa]')");
  
  if (isset($_POST['rb_gol'])) {
    mysqli_query($cnmy, "UPDATE $dbname.coa_level1 SET GOL1 = '$_POST[rb_gol]' WHERE COA1 = '$_POST[id]'");
  }
  header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}

// Update modul
elseif ($module=='coalevel1' AND $act=='update'){
  mysqli_query($cnmy, "UPDATE $dbname.coa_level1 SET NAMA1 = '$_POST[e_nmcoa]' 
                          WHERE COA1   = '$_POST[id]'");
  if (isset($_POST['rb_gol'])) {
    mysqli_query($cnmy, "UPDATE $dbname.coa_level1 SET GOL1 = '$_POST[rb_gol]' WHERE COA1 = '$_POST[id]'");
  }
  header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
?>
