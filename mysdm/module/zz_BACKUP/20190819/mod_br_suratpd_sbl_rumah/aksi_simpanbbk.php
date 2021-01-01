<?php

session_start();
include "../../config/koneksimysqli.php";
$cnmy=$cnmy;

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];


$pjmldara=$_POST['e_jmldata'];

if ($module=='suratpd' AND ( $act=='editdatanobbk' OR $act=='editdatanobbm' ) ) {
    $no=1;
    while ((INT)$no <= (INT)$pjmldara) {
        $nmfield="txtbbk".$no;
        if (isset($_POST[$nmfield])) {
            $pnomor=$_POST[$nmfield];
            //$query = "UPDATE dbtemp.t_sp4 SET nobbk='$pnomor' WHERE mynourut=$no";
            
            $isimpan = " nobbk='$pnomor' ";
            if ($act=='editdatanobbm') $isimpan = " nobbm='$pnomor' ";
            
            $query = "UPDATE dbmaster.t_suratdana_br1 SET $isimpan WHERE CONCAT(idinput,urutan)="
                    . "(SELECT IFNULL(CONCAT(idinput,urutan),'') FROM dbtemp.t_sp4 WHERE mynourut=$no)";
            
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }
        $no++;
    }
    
    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
?>

