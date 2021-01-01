<?php

    session_start();
    include "../../../config/koneksimysqli_it.php";
    include "../../../config/koneksimysqli.php";
    
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    
    $berhasil="Tidak ada data yang tersimpan";
    
    if ($module=="sbydatabudgetreq" AND $act=="input") {
        $pbrid=$_POST['uidbr'];

        $pdivisi_pilih=$_POST['udivisi_p'];
        $pnoseri=$_POST['unoseri'];
        
        $ptglfp="";
        if (!empty($_POST['utglfp'])) {
            $ptgl01 = str_replace('/', '-', $_POST['utglfp']);
            $ptglfp= date("Y-m-d", strtotime($ptgl01));
        }
        
        if (!empty($pbrid) AND !empty($pdivisi_pilih)) {
            
            $field_fp=" tgl_fp_pph=NULL ";
            if (!empty($ptglfp)) {
                $field_fp=" tgl_fp_pph='$ptglfp' ";
            }
            
            if ($pdivisi_pilih=="OTC") {
                $query = "UPDATE hrd.br_otc SET noseri_pph='$pnoseri', $field_fp WHERE brOtcId='$pbrid' AND pajak='Y'";
            }else{
                $query = "UPDATE hrd.br0 SET noseri_pph='$pnoseri', $field_fp WHERE brId='$pbrid' AND pajak='Y'";
            }
            
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            
            $query = "DELETE FROM dbmaster.t_br0_update_sby WHERE brId='$pbrid' AND divisi='$pdivisi_pilih'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $query = "INSERT INTO dbmaster.t_br0_update_sby (brId, divisi, userid)VALUES"
                    . "('$pbrid', '$pdivisi_pilih', '$_SESSION[IDCARD]')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            
            $berhasil="";
        }
    }
    
    mysqli_close($cnit);
    mysqli_close($cnmy);
    
    echo $berhasil;
?>

