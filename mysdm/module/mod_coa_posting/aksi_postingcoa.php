<?php

session_start();
include "../../config/koneksimysqli_it.php";
include "../../config/fungsi_sql.php";
include "../../config/fungsi_combo.php";
$cnmy=$cnit;
$dbname="dbmaster";

if (isset($_GET['module']) AND isset($_GET['act'])) {
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];

    // Hapus entry
    if ($module=='postingcoa' AND $act=='hapus')
    {
        $query = "delete from $dbname.posting_coa WHERE subpost='$_GET[id]' AND kodeid='$_GET[kodeid]' AND COA4='$_GET[coa4]'";
        mysqli_query($cnmy, $query);
        $datasavems=SaveDataMS("dbmaster", "posting_coa");
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
        exit;
    }

}


$module=$_POST['u_module'];
$act=$_POST['u_act'];
$idmenu=$_POST['u_idmenu'];

// Hapus entry
if ($module=='postingcoa' AND $act=='hapus')
{
    mysqli_query($cnmy, "delete $dbname.posting_coa WHERE subpost='$_GET[id]' AND kodeid='$_GET[kodeid]' AND COA4='$_GET[coa4]'");
    $datasavems=SaveDataMS("dbmaster", "posting_coa");
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='postingcoa')
{
    if ($act=='input') {
        $query = "insert into $dbname.posting_coa (subpost, kodeid, COA4)values('$_POST[cb_subpost]', '$_POST[cb_post]', '$_POST[cb_coa]')";
        mysqli_query($cnmy, $query);
    }else{
        $query = "update $dbname.posting_coa set subpost='$_POST[cb_subpost]', kodeid='$_POST[cb_post]', COA4='$_POST[cb_coa]' WHERE "
                . " subpost='$_POST[lama_subpost]' AND kodeid='$_POST[lama_kodeid]' AND COA4='$_POST[lama_coa4]'";
        mysqli_query($cnmy, $query);
    }
    $datasavems=SaveDataMS("dbmaster", "posting_coa");
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}

?>
