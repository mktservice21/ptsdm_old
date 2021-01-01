<?php
session_start();
include "../../config/koneksimysqli.php";
include "../../config/fungsi_combo.php";

$module=$_GET['module'];
$act=$_POST['e_stsapv'];
$idmenu=$_GET['idmenu'];



if (strtoupper($_POST['buttonapv'])=="APPROVE") {
    $filterbr=('');
    if (!empty($_POST['chkbox_br'])){
        $filterbr=$_POST['chkbox_br'];
        $filterbr=PilCekBox($filterbr);
    }
    $filterbr=" NOBR in $filterbr ";
    
    echo "$filterbr <br/> Approve";
    
}elseif (strtoupper($_POST['buttonapv'])=="UNAPPROVE") {

    $filterbr=('');
    if (!empty($_POST['chkbox_br'])){
        $filterbr=" NOBR in (".substr($_POST['chkbox_br'], 0, -1).")";
    }
    

    $ssql="select TTDSPV, TTDSPV_GBR from dbbudget.t_br_ttd where $filterbr";
    $tampil=mysqli_query($cnmy, $ssql);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0){
        while ($r=  mysqli_fetch_array($tampil)){
            if (!empty($r['TTDSPV_GBR']))
                unlink("../../images/tanda_tangan_base64/$r[TTDSPV_GBR]");
        }
    }
    mysqli_query($cnmy, "DELETE FROM dbbudget.t_br_ttd where $filterbr");
    
    echo "sukes";
    //echo "$filterbr <br/> Un Approve";
}elseif (strtoupper($_POST['buttonapv'])=="REJECT") {
    $filterbr=('');
    if (!empty($_POST['chkbox_br'])){
        $filterbr=$_POST['chkbox_br'];
        $filterbr=PilCekBox($filterbr);
    }
    $filterbr=" NOBR in $filterbr ";
    
    echo "$filterbr <br/> Reject";
}

header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act='.$act);
?>
