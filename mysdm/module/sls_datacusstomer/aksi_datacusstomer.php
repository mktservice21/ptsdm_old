<?php
session_start();

    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
$puserid="";
if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

if (empty($puserid)) {
    echo "ANDA HARUS LOGIN ULANG...";
    exit;
}

$pmodule=$_GET['module'];
//include "../../config/koneksimysqli_it.php";
include "../../config/koneksimysqli_ms.php";
include "../../config/koneksimysqli.php";



$berhasil="Tidak ada data yang disimpan....";

if ($pmodule=="simpanperubahancust") {
    $pnid=$_POST['unid'];
    $pnstatus=$_POST['ustatus'];
    $pdiscount=$_POST['udisc'];
    if (empty($pdiscount)) $pdiscount="0";
    
    $pdiscount=str_replace(",","", $pdiscount);
    
    if (!empty($pnid) AND !empty($pnstatus)) {
        
        $sql_update = "UPDATE MKT.icust SET istatus='$pnstatus', idisc='$pdiscount' WHERE CONCAT(IFNULL(iCabangId,''),IFNULL(areaId,''),IFNULL(iCustid,''))='$pnid' LIMIT 1";
        //mysqli_query($cnit, $sql_update); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "A DB ".$erropesan; exit; }
        
        $sql_update_new = "UPDATE sls.icust SET istatus='$pnstatus', idisc='$pdiscount' WHERE CONCAT(IFNULL(iCabangId,''),IFNULL(areaId,''),IFNULL(iCustid,''))='$pnid' LIMIT 1";
        mysqli_query($cnms, $sql_update_new); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "B DB ".$erropesan; exit; }
        
        mysqli_query($cnmy, $sql_update); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "C DB ".$erropesan; exit; }
        
        $berhasil="berhasil disimpan...";
    }
    
}


//mysqli_close($cnit);
mysqli_close($cnms);
mysqli_close($cnmy);
echo $berhasil;
?>