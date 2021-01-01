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
    
    mysqli_close($cnmy);
    $berhasil="";
}

echo $berhasil;

?>
