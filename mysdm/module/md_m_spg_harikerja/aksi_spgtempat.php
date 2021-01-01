<?php

session_start();
include "../../config/koneksimysqli.php";
$cnmy=$cnmy;
$dbname = "dbmaster";

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

$pidspg=$_POST['uidspg'];
$pcabang=$_POST['ucabang'];
$parea=$_POST['uarea'];
$ppenempatan=$_POST['upenempatan'];

$pbulan=$_POST['ubulan'];
$ptglnya= date("Y-m", strtotime($pbulan));

if ($pcabang=="JKT_MT") {
    $pcabang="0000000007";
}elseif ($pcabang=="JKT_RETAIL") {
    $pcabang="0000000007";
}

$berhasil="tidak ada data yang disimpan";

//$berhasil="$pidspg, $pcabang, $parea, $ppenempatan";

if ($act=='input') {
    
    $query = "DELETE FROM dbmaster.t_spg_tempat WHERE id_spg='$pidspg'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Simpan"; exit; }
    
    
    $query = "INSERT INTO dbmaster.t_spg_tempat (id_spg, icabangid, areaid, penempatan, userid) VALUES "
            . "('$pidspg', '$pcabang', '$parea', '$ppenempatan', '$_SESSION[IDCARD]')";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Simpan"; exit; }
    
    
    $pidzona="";
    $query = "select id_zona FROM dbmaster.t_spg_gaji_area_zona where icabangid='$pcabang' AND areaid='$parea' ORDER BY bulan DESC LIMIT 1";
    $tampilar = mysqli_query($cnmy, $query);
    $nar= mysqli_fetch_array($tampilar);
    $pidzona=$nar['id_zona'];
    
    $query="UPDATE dbmaster.t_spg_gaji_br0 SET icabangid='$pcabang', areaid='$parea', id_zona='$pidzona' WHERE id_spg='$pidspg' AND DATE_FORMAT(periode,'%Y-%m')='$ptglnya'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Simpan"; exit; }
    
    
    mysqli_close($cnmy);
    $berhasil="";
}

echo $berhasil;

?>
