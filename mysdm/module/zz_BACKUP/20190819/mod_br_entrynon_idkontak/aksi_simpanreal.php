<?php

    session_start();
    include "../../config/koneksimysqli_it.php";
    $cnmy=$cnit;
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    
    $pidbr=$_POST['uidbr'];
    $pjmlreal=$_POST['ujmlreal'];
    if (empty($pjmlreal)) $pjmlreal=0;
    $pjmlreal=str_replace(",","", $pjmlreal);
    
    $plain=$_POST['ulain'];
    $nbatal=$_POST['ubatal'];
    $ptglterima=$_POST['utglterima'];
    
    $tglrealterima="0000-00-00";
    if (!empty($ptglterima)) {
        $tglrealterima= date("Y-m-d", strtotime($ptglterima));
    }
    
    $pbatal="";
    if ($nbatal=="true") $pbatal="Y";
    
    //echo "$module, $act, $idmenu, $pidbr, $pjmlreal, $plain, $tglrealterima, $pbatal";exit;
    
    $berhasil="Tidak ada data yang disimpan";
    
    if (empty($pidbr)) {
        echo $berhasil; exit;
    }
    
    if ($module=='entrybrnon' AND $act=='input') {
        
        mysqli_query($cnmy, "UPDATE hrd.br0 SET jumlah1='$pjmlreal', tgltrm='$tglrealterima', "
                . " lampiran='Y', batal='$pbatal', lain2='$plain' WHERE brid='$pidbr'");
        
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "GAGAL.... Error Simpan"; exit; }
        
        mysqli_close($cnmy);
        
        $berhasil="";
    }
    
    
    echo $berhasil;
?>
