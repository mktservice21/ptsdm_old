<?php

    $pnpidmenu_="";
    $pnpidgroup_="";
    $npicardid_="";
    if (isset($_GET['idmenu'])) $pnpidmenu_=$_GET['idmenu'];
    if (isset($_SESSION['GROUP'])) $pnpidgroup_=$_SESSION['GROUP'];
    if (isset($_SESSION['IDCARD'])) $npicardid_=$_SESSION['IDCARD'];
    
    if ($pnpidgroup_=="1") {
    }else{
        if ($pnpidmenu_=="115"){//khusus sedang dalam perbaikan
            //echo "Anda Tidak Berhat Dengan Menu INI...";
            //exit;
        }
    }
    
    $padamodulekhususmenu=false;
    
    $query = "select distinct b.`id` as idmenu from dbmaster.t_karyawan_menu as a join dbmaster.t_karyawan_menu_d as b on a.igroup=b.igroup where a.karyawanid='$npicardid_' AND b.id='$pnpidmenu_'";
    $nptampiltm_= mysqli_query($cnmy, $query);
    $ketemupiltmgrp= mysqli_num_rows($nptampiltm_);
    if ((DOUBLE)$ketemupiltmgrp>0) $padamodulekhususmenu=true;
    
    if ($padamodulekhususmenu==false) {
        
        $query = "SELECT ID from dbmaster.sdm_groupmenu WHERE ID_GROUP='$pnpidgroup_' AND ID='$pnpidmenu_'";
        $nptampil_= mysqli_query($cnmy, $query);
        $ketemupilgrp= mysqli_num_rows($nptampil_);
        if ((DOUBLE)$ketemupilgrp==0) {
            echo "Anda Tidak Berhat Dengan Menu INI...";
            exit;
        }
        
    }
    
?>