<?php

    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    $idbr=$_GET['idmenu'];
    $idca=$_POST['e_idkaryawan_ca'];
    
    $chkdari = "";
    
    if (isset($_POST['chk_dari'])) {
        $chkdari = "&ca=$idca";
    }else{
        $act = "tambahbaru";
    }
    
    
    header('location:../../media.php?module='.$module.'&id='.$idbr.'&idmenu='.$idmenu.'&act='.$act.$chkdari);
?>