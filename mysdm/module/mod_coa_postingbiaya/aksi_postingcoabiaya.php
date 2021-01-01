<?php

session_start();
include "../../config/koneksimysqli_it.php";
include "../../config/fungsi_combo.php";
$cnmy=$cnit;
$dbname = "dbmaster";

$module=$_POST['u_module'];
$act=$_POST['u_act'];
$idmenu=$_POST['u_idmenu'];
$divisi=$_POST['u_divisi'];
$coa=$_POST['cb_coa'];

if ($module=='postingcoabiaya' AND $act=='input') {
    $filterid=('');
    if (!empty($_POST['chkbox_id'])){
        $datanya=$_POST['chkbox_id'];
        
        $tag = implode(',',$datanya);
        $arr_kata = explode(",",$tag);
        $count_kata = count($arr_kata);
        $jumlah_tag = substr_count($tag, ",") + 1;
        $u=0;
        $unsel="";
        for ($x=0; $x<=$jumlah_tag; $x++){
            if (!empty($arr_kata[$u])){
                $uTag=trim($arr_kata[$u]);
                if (!empty($uTag)) {
                    mysqli_query($cnmy, "delete from $dbname.posting_coa_rutin where divisi='$divisi' and nobrid='$uTag'");
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                    
                    mysqli_query($cnmy, "insert into $dbname.posting_coa_rutin (divisi, nobrid, COA4)values('$divisi', '$uTag', '$coa')");
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                }
            }
            $u++;
        }
        
    }
}
$datasavems=SaveDataMS("dbmaster", "posting_coa_rutin");
header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complit');
?>
