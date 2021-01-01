<?php

    session_start();
    $puserid="";
    if (isset($_SESSION['IDCARD'])) $puserid=$_SESSION['IDCARD'];
    if (empty($puserid)) {
        echo "data tidak ada yang diproses, silakan login ulang..."; exit;
    }
    
    include "../../config/koneksimysqli_ms.php";
    
    
$berhasil="Tidak ada data yang disimpan...";


$pmodule=$_GET['module'];
$pact=$_GET['act'];

if ($pmodule=="simpandatamapingstc" AND $pact=="input") {
    $pkdprodsby=$_POST['uprodsby'];
    $piprodid=$_POST['uprodid'];
    
    $query = "UPDATE sls.imaping_produk SET iprodid='$piprodid' WHERE kdproduk='$pkdprodsby'";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $berhasil="berhasil...";
}

mysqli_close($cnms);
echo "$berhasil";
    
?>

