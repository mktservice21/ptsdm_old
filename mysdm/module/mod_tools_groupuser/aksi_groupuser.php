<?php
session_start();
include "../../config/koneksimysqli.php";

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

// Input user
if ($module=='groupuser' AND $act=='input'){
    mysqli_query($cnmy, "INSERT INTO dbmaster.sdm_groupuser(nama_group)
                               VALUES('$_POST[nama]')");
    header('location:../../media.php?module='.$module.'&act='.$idmenu.'&idmenu='.$idmenu);
}
// Update user
elseif ($module=='groupuser' AND $act=='update'){
    mysqli_query($cnmy, "UPDATE dbmaster.sdm_groupuser SET nama_group    = '$_POST[nama]'
                           WHERE id_group      = '$_POST[id]'");
    header('location:../../media.php?module='.$module.'&act='.$idmenu.'&idmenu='.$idmenu);
}elseif ($module=='groupuser' AND $act=='hapususer'){
    if ($_SESSION['LEVELUSER']=="admin") {
        mysqli_query($cnmy, "delete from dbmaster.sdm_groupuser where id_group='$_GET[id]'");
    }
    header('location:../../media.php?module='.$module.'&act='.$idmenu.'&idmenu='.$idmenu);
}

// Edit Group Menu
elseif ($module=='groupuser' AND $act=='updatemenugrop'){

    $tag_id = $_POST['tag_km'];
    mysqli_query($cnmy, "delete from dbmaster.sdm_groupmenu where id_group='$_GET[idgroup]'");

    
    for ($k=0;$k<=count($tag_id);$k++) {
        if (!empty($tag_id[$k])){
            /*
            $cTambah="Y";$cEdit="Y";$cHapus="Y";
            
            $cTa=$_POST['arr_tambah'.$tag_id[$k]];
            $cEa=$_POST['arr_edit'.$tag_id[$k]];
            $cHa=$_POST['arr_hapus'.$tag_id[$k]];

            if (empty ($cTa)) $cTambah="N";
            if (empty ($cEa)) $cEdit="N";
            if (empty ($cHa)) $cHapus="N";
             * 
             */

             $cTambah="N";$cEdit="N";$cHapus="N";
            //echo "$_GET[idgroup], $tag_id[$k], $cTambah, $cEdit, $cHapus<br/>";
            mysqli_query($cnmy, "INSERT INTO dbmaster.sdm_groupmenu(id_group, id, TAMBAH, EDIT, HAPUS)VALUES('$_GET[idgroup]', '$tag_id[$k]', '$cTambah', '$cEdit', '$cHapus')");
        }
    }
    $act="editgroupmenu";
    $gid=$_GET['idgroup'];
    $namaa=$_GET['nama'];
    header('location:../../media.php?module='.$module.'&act='.$idmenu.'&id='.$gid.'&nama='.$namaa.'&idmenu='.$idmenu);

}
?>
