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
    
    $pnoslip=$_POST['unoslip'];
    $ptglterima=$_POST['utglterima'];
    
    $tglreal="0000-00-00";
    if (!empty($ptglterima)) {
        $tglreal= date("Y-m-d", strtotime($ptglterima));
    }
    
    $berhasil="Tidak ada data yang disimpan";
    //echo "$pidbr, $pjmlreal, $pnoslip, $tglreal === $module, $act, $idmenu";
    
    
    if (empty($pidbr)) {
        echo $berhasil; exit;
    }

    if ($module=='spdotc' AND $act=='input') {
        
        mysqli_query($cnmy, "UPDATE hrd.br_otc SET realisasi='$pjmlreal', noslip='$pnoslip', tglreal='$tglreal', "
                . " lampiran='Y', ca='N' WHERE brOtcId='$pidbr'");
        
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Simpan"; exit; }
        
        mysqli_close($cnmy);
        
        $berhasil="";
    }
?>

