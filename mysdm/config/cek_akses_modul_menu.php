<?php

    $pnpidmenu_="";
    $pnpmodulepl_="";
    $pnpidgroup_="";
    $npicardid_="";
    if (isset($_GET['idmenu'])) $pnpidmenu_=$_GET['idmenu'];
    if (isset($_GET['module'])) $pnpmodulepl_=TRIM($_GET['module']);
    if (isset($_SESSION['GROUP'])) $pnpidgroup_=$_SESSION['GROUP'];
    if (isset($_SESSION['IDCARD'])) $npicardid_=$_SESSION['IDCARD'];
    
    if ($pnpidgroup_=="1") {
    }else{
        if ($pnpidmenu_=="115"){//khusus sedang dalam perbaikan
            //echo "Anda Tidak Berhak Dengan Menu INI...";
            //exit;
        }
    }
    
    $padamodulekhususmenu=false;
    
    $query = "select distinct b.`id` as idmenu from dbmaster.t_karyawan_menu as a "
            . " join dbmaster.t_karyawan_menu_d as b on a.igroup=b.igroup "
            . " JOIN dbmaster.sdm_menu as c on b.`id`=c.`ID` "
            . " where a.karyawanid='$npicardid_' AND b.id='$pnpidmenu_' AND REPLACE(c.`URL`, '?module=', '')='$pnpmodulepl_'";
    $nptampiltm_= mysqli_query($cnmy, $query);
    $ketemupiltmgrp= mysqli_num_rows($nptampiltm_);
    if ((DOUBLE)$ketemupiltmgrp>0) $padamodulekhususmenu=true;
    
    if ($padamodulekhususmenu==false) {
        
        $query = "SELECT a.ID from dbmaster.sdm_groupmenu as a "
                . " JOIN dbmaster.sdm_menu as b on a.`ID`=b.`ID` "
                . " WHERE a.ID_GROUP='$pnpidgroup_' AND a.ID='$pnpidmenu_' AND REPLACE(b.`URL`, '?module=', '')='$pnpmodulepl_'";
        $nptampil_= mysqli_query($cnmy, $query);
        $ketemupilgrp= mysqli_num_rows($nptampil_);
        if ((DOUBLE)$ketemupilgrp==0) {
            //header('location:'.$_SESSION['FOLDERGL'].'/media.php?module=home');
            ?>
            <meta content='0; url=media.php?module=home' http-equiv='refresh'>
            <?PHP
            //echo "Anda Tidak Berhak Dengan Menu INI...";
            exit;
        }
        
    }
    
?>