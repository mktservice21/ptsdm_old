<?php

    session_start();
    include "../../../config/koneksimysqli.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    
    $dbname = "dbmaster";
    
    
if ($module=='sbydatabudgetreq' AND $act=='input')
{
    
    $pidbr=$_POST['e_id'];
    $pdivisipilih=$_POST['e_divisi_p'];
    $ptglpilih1=$_POST['e_tgl1_p'];
    $ptglpilih2=$_POST['e_tgl2_p'];
    $pviapilih=$_POST['e_via_p'];
    $ppajakpilih=$_POST['e_pajak_p'];
    
    
    $pjmltrs=$_POST['cb_jml'];
    if (empty($pjmltrs)) $pjmltrs=1;
    
    $nm_tabel_pilih=" dbmaster.t_br0_via_sby ";
    if ($pdivisipilih=="OTC") $nm_tabel_pilih=" dbmaster.t_br_otc_via_sby ";
    if ($pdivisipilih=="KD") $nm_tabel_pilih=" dbmaster.t_klaim_via_sby ";
    
    //echo "$pidbr, $pdivisipilih, $ptglpilih1, $ptglpilih2, $pviapilih, $ppajakpilih...<br/>Jml : $pjmltrs, "; exit;
    
    $query = "DELETE FROM $nm_tabel_pilih WHERE bridinput='$pidbr'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
    $berhasil=false;
    for($ix=1;$ix<=$pjmltrs;$ix++) {
        $pnobukti=$_POST['e_nobukti'][$ix];
        $ptgltermin=$_POST['e_tgltermin'][$ix];
        $ptgltrans=$_POST['e_tgltrans'][$ix];
        $pjumlahrp=$_POST['e_jumlah'][$ix];
        $pjumlahrp=str_replace(",","", $pjumlahrp);
        
        if (empty($ptgltermin) AND !empty($ptgltrans)) $ptgltermin=$ptgltrans;
        if (empty($pjumlahrp)) $pjumlahrp=0;
        
        //echo "$ptgltermin<br/>";
        
        if (!empty($ptgltermin)) {
            
            $ninput_trans="NULL";
            if (!empty($ptgltrans)) $ninput_trans="'$ptgltrans'";
            
            $query = "INSERT INTO $nm_tabel_pilih (bridinput, tgltermin, tgltransfersby, jumlah, userid, nobukti) VALUES"
                    . "('$pidbr', '$ptgltermin', $ninput_trans, '$pjumlahrp', '$_SESSION[IDCARD]', '$pnobukti')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $berhasil=true;
        }
        
    }
    
    
    mysqli_close($cnmy);
    //if ($berhasil==true) {
        header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt'.'&xtgl1='.$ptglpilih1.'&xtgl2='.$ptglpilih2.'&xdivisi='.$pdivisipilih.'&xvia='.$pviapilih.'&xpajak='.$ppajakpilih);
    //}
    
}
    
?>

