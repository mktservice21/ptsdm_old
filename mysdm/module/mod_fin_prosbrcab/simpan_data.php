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
    $pjmlminta=$_POST['ujmlminta'];
    $pjmlrp=$_POST['ujmlrp'];
    $pjmlexp=$_POST['ujmlexp'];
    
    //$ptgl=$_POST['utglrilis'];
    //$ntglril="";
    //if (!empty($ptgl)) $ntglril= date("Y-m-d", strtotime($ptgl));
    
    $pjmlminta=str_replace(",","", $pjmlminta);
    $pjmlrp=str_replace(",","", $pjmlrp);
    $pjmlexp=str_replace(",","", $pjmlexp);
    
    if (empty($pjmlminta)) $pjmlminta=0;
    if (empty($pjmlrp)) $pjmlrp=0;
    if (empty($pjmlexp)) $pjmlexp=0;
    
    if ((double)$pjmlrp==0) $pjmlrp=$pjmlminta;
    
    if (!empty($pidbr)) {
        $query = "UPDATE dbmaster.t_br_cab SET jumlah='$pjmlrp', jml_expired='$pjmlexp', userid='$puserid' WHERE bridinputcab='$pidbr'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Update"; exit; }
        
        $berhasil="berhasil";
        //$berhasil="$pidbr, $pjmlminta, $pjmlrp, $pjmlexp";
    }
}

mysqli_close($cnmy);
echo $berhasil;
?>

