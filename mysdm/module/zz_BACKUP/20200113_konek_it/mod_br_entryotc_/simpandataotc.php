<?php

session_start();
include "../../config/koneksimysqli_it.php";
include "../../config/fungsi_combo.php";
$cnmy=$cnit;
$dbname = "hrd";

$module=$_POST['u_module'];
$act=$_POST['u_act'];
$idmenu=$_POST['u_idmenu'];
$isiinputtipe=$_POST['cb_tipeisiinput'];


if ($module=='entrybrotc' AND $act=='input') {
    $filterid=('');
    if (!empty($_POST['chkbox_id'])){
        $filterid=$_POST['chkbox_id'];
        $filterid=PilCekBox($filterid);
    }
    $filterid=" brOtcId in $filterid ";
    
    if (empty($filterid)) {
        echo "Tidak ada data yang akan diupdate"; exit;
    }
    
    $pnoslip=$_POST['e_noslip'];
    
    $ptgl= "0000-00-00";
    if (!empty($_POST['e_tgltrans'])) {
        $datetrm = str_replace('/', '-', $_POST['e_tgltrans']);
        $ptgl= date("Y-m-d", strtotime($datetrm));
    }
    
    //echo "$isiinputtipe, $module, $act, $filterid, $pnoslip, $ptgl"; exit;
    
    //hanya noslip
    if ($isiinputtipe=="A") {
        $query = "update $dbname.br_otc set noslip='$pnoslip' where $filterid";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
    //hanya transfer
    if ($isiinputtipe=="B") {
        
        $query = "update $dbname.br_otc set tgltrans='$ptgl' where $filterid";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
    }
    
    
    //noslip dan transfer
    if ($isiinputtipe=="C") {
        
        $query = "update $dbname.br_otc set noslip='$pnoslip', tgltrans='$ptgl' where $filterid";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
    }
    
    //hanya noslip dan transfer
    if ($isiinputtipe=="C" or $isiinputtipe=="B") {
        //update modif transfer
        $query = "update $dbname.br_otc_ttd SET MODIFTRANSID='$_SESSION[IDCARD]', "
                . " MODIFTRANSDATE=NOW() WHERE $filterid";
        mysqli_query($cnmy, $query);
        
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complit');
}

?>

