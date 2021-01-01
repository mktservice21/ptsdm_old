<?php

session_start();
include "../../config/koneksimysqli_it.php";
include "../../config/fungsi_combo.php";
$cnmy=$cnit;
$dbname = "hrd";

$module=$_POST['u_module'];
$act=$_POST['u_act'];
$idmenu=$_POST['u_idmenu'];


if ($module=='breditcoa' AND $act=='input') {
    
    $filterid=('');
    if (!empty($_POST['chkbox_id'])){
        $filterid=$_POST['chkbox_id'];
        $filterid=PilCekBox($filterid);
    }
    $filterid=" brId in $filterid ";
    
    if (!empty($_POST['cb_coa'])) {
        $now=date("mdYhis");
        $tmp01 =" dbtemp.DTINPUTCOAISIS01_$_SESSION[IDCARD]$now ";
        $query = "select distinct brId, icabangid, cast('' as char(1)) region, cast('' as char(2)) pwil, cast(right(icabangid,3) as char(3)) pcab, cast('' as char(6)) kdwilayah from $dbname.br0 where $filterid";
        mysqli_query($cnmy, "Create table $tmp01 (".$query.")");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        mysqli_query($cnmy, "update $tmp01 set region=(select distinct b.region from dbmaster.icabang b where $tmp01.icabangid=b.icabangid limit 1)");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query = "update $tmp01 set pwil='01' where icabangid in ('0000000001', 'HO', '')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        //divisi ETHICAL
        mysqli_query($cnmy, "update $tmp01 set pwil=(select distinct b.kode from dbmaster.t_wilayah b where "
                . " $tmp01.region=b.icabangid AND b.divisi='ETC' limit 1) where ifnull(pwil,'')=''");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        mysqli_query($cnmy, "update $tmp01 set kdwilayah=CONCAT(pwil,'-', pcab)");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

        //============================================================================================
        
        //update COA
        $query = "update $dbname.br0 set COA4='$_POST[cb_coa]', MODIFDATE=NOW() where $filterid";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        //update wilayah
        $query = "update $dbname.br0 as a set a.KODEWILAYAH=(SELECT distinct b.kdwilayah from $tmp01 as b where a.brId=b.brId limit 1) where $filterid";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        mysqli_query($cnit, "drop table $tmp01");
        
    }
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complit');
}
?>

