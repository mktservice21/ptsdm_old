<?php
session_start();
include "../../config/koneksimysqli_it.php";
$cnmy=$cnit;
$dbname = "dbmaster";

if (isset($_GET['module'])) {
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
}else{
    $module=$_POST['u_module'];
    $act=$_POST['u_act'];
    $idmenu=$_POST['u_idmenu'];
}



// Hapus
if ($module=='coawewenang' AND $act=='hapus'){


    mysqli_query($cnmy, "delete from $dbname.coa_wewenang where karyawanId='$_GET[id]'");
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}

// Input modul
elseif ($module=='coawewenang'){

    
    
    mysqli_query($cnmy, "delete from $dbname.coa_wewenang where karyawanId='$_POST[e_idkaryawan]'");
    
    if (isset($_POST['tag_coa'])){
        $tag_id = $_POST['tag_coa'];
        for ($k=0;$k<=count($tag_id);$k++) {
            if (!empty($tag_id[$k])){
                mysqli_query($cnmy, "insert into $dbname.coa_wewenang (karyawanId, COA4) values "
                        . "('$_POST[e_idkaryawan]', '$tag_id[$k]')");
            }
        }
    }
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
?>
