<?php
session_start();
include "../../config/koneksimysqli.php";
//$cnmy=$cnit;
$dbname = "dbmaster";

if ($_GET['module']=="carikodesama") {
    $query=mysqli_query($cnmy, "select COA4 from $dbname.coa_level4 WHERE COA4   = '$_POST[ukode]'");
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
if ($module=='coalevel4' AND $act=='hapus'){
    $query=mysqli_query($cnmy, "select * from $dbname.coa_level4 WHERE COA4   = '$_GET[id]'");
    $r=  mysqli_fetch_array($query);
    if ($r['AKTIF4']=="Y")
        mysqli_query($cnmy, "update $dbname.coa_level4 set AKTIF4='N', userid='$_SESSION[IDCARD]' WHERE COA4='$_GET[id]'");
    else
        mysqli_query($cnmy, "update $dbname.coa_level4 set AKTIF4='Y', userid='$_SESSION[IDCARD]' WHERE COA4='$_GET[id]'");
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}

// Input modul
elseif ($module=='coalevel4' AND $act=='input'){
        
  // Input data jabatan
  mysqli_query($cnmy, "INSERT INTO $dbname.coa_level4(COA4, NAMA4, COA3, DESKRIPSI4)
	                       VALUES('$id', '$_POST[e_nmcoa]', '$_POST[cb_level]', '$_POST[e_desk]')");
  if (isset($_POST['rb_gol'])) {
    mysqli_query($cnmy, "UPDATE $dbname.coa_level4 SET GOL4 = '$_POST[rb_gol]', userid='$_SESSION[IDCARD]' WHERE COA4 = '$id'");
  }else{
      mysqli_query($cnmy, "UPDATE $dbname.coa_level4 SET GOL4 = null, userid='$_SESSION[IDCARD]' WHERE COA4 = '$_POST[id]'");
  }
  
  if (!empty($_POST['cb_kode'])) {
      mysqli_query($cnmy, "UPDATE $dbname.coa_level4 SET kodeid = '$_POST[cb_kode]', userid='$_SESSION[IDCARD]' WHERE COA4 = '$_POST[id]'");
  }else{
      mysqli_query($cnmy, "UPDATE $dbname.coa_level4 SET kodeid = null, userid='$_SESSION[IDCARD]' WHERE COA4 = '$_POST[id]'");
  }
  
  mysqli_query($cnmy, "UPDATE $dbname.coa_level4 SET subpost = '$_POST[cb_kodesub]', userid='$_SESSION[IDCARD]' WHERE COA4 = '$_POST[id]'");
  
  header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}

// Update modul
elseif ($module=='coalevel4' AND $act=='update'){
  
  mysqli_query($cnmy, "UPDATE $dbname.coa_level4 SET NAMA4 = '$_POST[e_nmcoa]', COA3='$_POST[cb_level]', DESKRIPSI4='$_POST[e_desk]', userid='$_SESSION[IDCARD]'    
                          WHERE COA4   = '$_POST[id]'");
  if (isset($_POST['rb_gol'])) {
    mysqli_query($cnmy, "UPDATE $dbname.coa_level4 SET GOL4 = '$_POST[rb_gol]', userid='$_SESSION[IDCARD]' WHERE COA4 = '$_POST[id]'");
  }else{
      mysqli_query($cnmy, "UPDATE $dbname.coa_level4 SET GOL4 = null, userid='$_SESSION[IDCARD]' WHERE COA4 = '$_POST[id]'");
  }
  
  if (!empty($_POST['cb_kode'])) {
      mysqli_query($cnmy, "UPDATE $dbname.coa_level4 SET kodeid = '$_POST[cb_kode]', userid='$_SESSION[IDCARD]' WHERE COA4 = '$_POST[id]'");
  }else{
      mysqli_query($cnmy, "UPDATE $dbname.coa_level4 SET kodeid = null, userid='$_SESSION[IDCARD]' WHERE COA4 = '$_POST[id]'");
  }
  
  mysqli_query($cnmy, "UPDATE $dbname.coa_level4 SET subpost = '$_POST[cb_kodesub]', userid='$_SESSION[IDCARD]' WHERE COA4 = '$_POST[id]'");
  
  header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
?>
