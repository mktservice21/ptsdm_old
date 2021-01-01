<?php

    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    $idbr=$_GET['idmenu'];
    $idca=$_POST['e_idca'];
    $idkaryawan=$_POST['e_idkaryawan'];
    $chkdari = "";
    
    if (!empty($idca))
        $chkdari = "&ca=$idca&buat=$idkaryawan";
    else
        $act = "tambahbaru";
    
    header('location:../../media.php?module='.$module.'&id='.$idbr.'&idmenu='.$idmenu.'&act='.$act.$chkdari);
?>