<?php

    session_start();
    include "../../config/koneksimysqli.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    
    $pidbr=$_POST['uidbr'];
    
    $ptglsby=$_POST['utglsby'];
    
    $ptglrptsby="0000-00-00";
    if (!empty($ptglsby)) {
        $ptglsby = str_replace('/', '-', $_POST['utglsby']);
        $ptglrptsby= date("Y-m-d", strtotime($ptglsby));
    }
    
    
    
    $berhasil="Tidak ada data yang disimpan";
    
    if (empty($pidbr)) {
        echo $berhasil; exit;
    }
    
    $pidbr="(".substr($pidbr, 0, -1).")";
    
    //echo "$module, $act, $idmenu, $pidbr, $ptglrptsby";exit;
    
    
    if ($module=='entrybrnon' AND $act=='inputsby') {
        
        mysqli_query($cnmy, "UPDATE hrd.br0 SET sby='Y', tglrpsby='$ptglrptsby' WHERE brId IN $pidbr");
        
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "GAGAL.... Error Simpan"; mysqli_close($cnmy); exit; }
        
        $berhasil="";
    }
    
    mysqli_close($cnmy);
    
    echo $berhasil;
?>
