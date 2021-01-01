<?php
date_default_timezone_set('Asia/Jakarta');
session_start();
include "../../config/koneksimysqli.php";
$cnmy=$cnmy;
$dbname = "dbmaster";

$puserid=$_SESSION['IDCARD'];
if (empty($puserid)) {
    echo "ANDA HARUS LOGIN ULANG...!!!";
    mysqli_close($cnmy);
    exit;
}

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

$berhasil="Tidak ada data yang disimpan";
if ($module=='fincekprosesbrcab' AND $act=='input') {
    $pidbr=$_POST['uidbr'];
    $ptglbook=$_POST['utglbook'];
    
    $psavebooke=" tglbooking='$ptglbook', ";
    if (empty($ptglbook)) $psavebooke=" tglbooking=null, ";
    
    if (!empty($pidbr)) {
        $query = "UPDATE dbmaster.t_br_cab SET $psavebooke userid='$puserid' WHERE bridinputcab='$pidbr'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Update"; exit; }
        
        $berhasil="berhasil simpan booking";
        //$berhasil="$pidbr, $pjmlminta, $pjmlrp, $ptglissu";
    }
    
    
}elseif ($module=='fincekprosesbrcab' AND $act=='hapus') {
    $pidbr=$_POST['uidbr'];
    if (!empty($pidbr)) {
        $query = "UPDATE dbmaster.t_br_cab SET tglbooking=null, userid='$puserid' WHERE bridinputcab='$pidbr'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Update"; exit; }
        
        $berhasil="berhasil hapus";
    }
    
    
}

mysqli_close($cnmy);
echo $berhasil;
?>