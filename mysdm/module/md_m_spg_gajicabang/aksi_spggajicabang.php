<?php

session_start();
include "../../config/koneksimysqli.php";
$cnmy=$cnmy;
$dbname = "dbmaster";

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

$pbulan=$_POST['ubulan'];
$ptglnya= date("Y-m", strtotime($pbulan));
$pcabang=$_POST['ucabang'];

$palokid="";
$fcabang ="  icabangid='$pcabang' ";
if ($pcabang=="JKT_MT") {
    $pcabang="0000000007";//JAKARTA
    $fcabang = "  IFNULL(icabangid,'') = '$pcabang' AND alokid='001' ";
    $palokid="001";
}elseif ($pcabang=="JKT_RETAIL") {
    $pcabang="0000000007";//JAKARTA
    $fcabang = "  IFNULL(icabangid,'') = '$pcabang' AND alokid='002' ";
    $palokid="002";
}


if ($act=='input') {
    $pgaji=str_replace(",","", $_POST['ugaji']);
    $pmakan=str_replace(",","", $_POST['umakan']);
    $psewa=str_replace(",","", $_POST['usewa']);
    $ppulsa=str_replace(",","", $_POST['upulsa']);
    $pparkir=str_replace(",","", $_POST['uparkir']);
    $pbbm=str_replace(",","", $_POST['ubbm']);
}

$berhasil="Tidak ada data yang disimpan";
if ($module=='spgmastergajicabang' AND $act=='input') {
    mysqli_query($cnmy, "DELETE FROM $dbname.t_spg_gaji_cabang WHERE DATE_FORMAT(periode,'%Y-%m')='$ptglnya' AND $fcabang");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query="INSERT INTO $dbname.t_spg_gaji_cabang (periode, icabangid, alokid, gaji, umakan, sewakendaraan, pulsa, parkir, bbm, userid)VALUES"
            . "('$pbulan', '$pcabang', '$palokid', '$pgaji', '$pmakan', '$psewa', '$ppulsa', '$pparkir', '$pbbm', '$_SESSION[IDCARD]')";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    $berhasil="Berhasil disimpan";
}elseif ($module=='spgmastergajicabang' AND $act=='hapus') {
    mysqli_query($cnmy, "DELETE FROM $dbname.t_spg_gaji_cabang WHERE DATE_FORMAT(periode,'%Y-%m')='$ptglnya' AND $fcabang");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    $berhasil="Berhasil dihapus";
}
echo $berhasil;
?>

