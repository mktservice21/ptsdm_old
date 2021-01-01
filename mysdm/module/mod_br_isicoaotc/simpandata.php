<?php

session_start();
include "../../config/koneksimysqli.php";
include "../../config/fungsi_combo.php";
//$cnmy=$cnit;
$dbname = "hrd";

$module=$_POST['u_module'];
$act=$_POST['u_act'];
$idmenu=$_POST['u_idmenu'];


if ($module=='breditcoaotc' AND $act=='input') {
    
    $filterid=('');
    if (!empty($_POST['chkbox_id'])){
        $filterid=$_POST['chkbox_id'];
        $filterid=PilCekBox($filterid);
    }
    $filterid=" brOtcId in $filterid ";
    
    if (!empty($_POST['cb_coa'])) {
        $now=date("mdYhis");
        $tmp01 =" dbtemp.DTINPUTCOAISISOTCZ01_$_SESSION[IDCARD]$now ";
        $query = "select distinct brOtcId, icabangid_o, cast('' as char(1)) region, cast('' as char(2)) pwil, cast(right(icabangid_o,3) as char(3)) pcab, cast('' as char(6)) kdwilayah from $dbname.br_otc where $filterid";
        mysqli_query($cnmy, "Create table $tmp01 (".$query.")");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        mysqli_query($cnmy, "update $tmp01 set region=(select distinct b.region from dbmaster.v_icabang_o b where $tmp01.icabangid_o=b.icabangid_o limit 1)");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query = "update $tmp01 set pwil='01' where icabangid_o in ('0000000001', 'HO', '')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        //divisi OTC
        mysqli_query($cnmy, "update $tmp01 set pwil=(select distinct b.kode from dbmaster.t_wilayah b where "
                . " $tmp01.region=b.icabangid AND b.divisi='OTC' limit 1) where ifnull(pwil,'')=''");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        mysqli_query($cnmy, "update $tmp01 set kdwilayah=CONCAT(pwil,'-', pcab)");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

        //============================================================================================
        
        //update COA
        $query = "update $dbname.br_otc set COA4='$_POST[cb_coa]', MODIFDATE=NOW() where $filterid";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        //update wilayah
        $query = "update $dbname.br_otc as a set a.KODEWILAYAH=(SELECT distinct b.kdwilayah from $tmp01 as b where a.brOtcId=b.brOtcId limit 1) where $filterid";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        mysqli_query($cnit, "drop table $tmp01");
        
    }else{
    }
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complit');
}
?>

