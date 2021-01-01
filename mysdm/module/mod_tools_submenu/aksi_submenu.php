<?php
session_start();
include "../../config/koneksimysqli.php";

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

// Hapus submenu
if ($module=='submenu' AND $act=='hapus'){
  mysqli_query($cnmy, "DELETE FROM dbmaster.sdm_menu WHERE id='$_GET[id]'");
  header('location:../../media.php?module='.$module.'&act='.$idmenu.'&idmenu='.$idmenu);
}

// Input submenu
elseif ($module=='submenu' AND $act=='input'){
  // Cari angka urutan terakhir
  $u=mysqli_query($cnmy, "SELECT urutan FROM dbmaster.sdm_menu where parent_id <> '0' and parent_id='$_POST[menu]' ORDER by urutan DESC");
  $d=mysqli_fetch_array($u);
  $urutan=$d[urutan]+1;
  
  // Input data submenu
  mysqli_query($cnmy, "INSERT INTO dbmaster.sdm_menu(judul,
                                 url,
                                 publish, 
                                 kriteria, 
                                 urutan, parent_id) 
	                       VALUES('$_POST[nama_menu]',
                                '$_POST[link]',
                                '$_POST[publish]',
                                '$_POST[ckriteria]',
                                '$urutan', '$_POST[menu]')");
  header('location:../../media.php?module='.$module.'&act='.$idmenu.'&idmenu='.$idmenu);
}

// Update submenu
elseif ($module=='submenu' AND $act=='update'){
  mysqli_query($cnmy, "UPDATE dbmaster.sdm_menu SET judul = '$_POST[nama_menu]',
                                url       = '$_POST[link]',
                                publish    = '$_POST[publish]',
                                kriteria    = '$_POST[ckriteria]',
                                urutan     = '$_POST[urutan]', parent_id='$_POST[menu]', m_khusus='$_POST[mkhusus]'
                          WHERE id   = '$_POST[id]'");
  header('location:../../media.php?module='.$module.'&act='.$idmenu.'&idmenu='.$idmenu);
}
?>
