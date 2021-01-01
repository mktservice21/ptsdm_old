<?php

    session_start();
    include "../../../config/koneksimysqli.php";
    include "../../../config/fungsi_sql.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    //$cnmy=$cnit;
    $dbname = "dbmaster";
// Hapus 
if ($module=='sbyinputbm' AND $act=='hapus')
{
    mysqli_query($cnmy, "update $dbname.t_bm_sby set stsnonaktif='Y', userid='$_SESSION[IDCARD]' WHERE ID='$_GET[id]'");
    header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='sbyinputbm')
{
    $kodenya=$_POST['e_id'];
    $pdivisi=$_POST['cb_divisi'];
    
    $pnobbm=$_POST['e_nobbm'];
    $pnobbk=$_POST['e_nobbk'];
    
    $pcoa=$_POST['cb_coa'];
    $pcoa2=$_POST['cb_coa2'];
    $pket=$_POST['e_ket'];
    if (!empty($pket)) $pket = str_replace("'", " ", $pket);
    
    $ptgl = str_replace('/', '-', $_POST['e_tglberlaku']);
    $periode1= date("Y-m-d", strtotime($ptgl));
    $pjumlahd=str_replace(",","", $_POST['e_jmldebit']);
    $pjumlahk=str_replace(",","", $_POST['e_jmlkredit']);
    
    if(empty($pjumlahd)) $pjumlahd=0;
    if(empty($pjumlahk)) $pjumlahk=0;
    
    $saldo=(double)$pjumlahd-(double)$pjumlahk;
    
    if ($act=="input") {
        $query = "INSERT INTO $dbname.t_bm_sby (TGLINPUT, TANGGAL, DIVISI, COA4, DEBIT, KREDIT, KETERANGAN, USERID, NOBBM, NOBBK, SALDO, COA4_K)values"
                . "(CURRENT_DATE(), '$periode1', '$pdivisi', '$pcoa', '$pjumlahd', '$pjumlahk', '$pket', '$_SESSION[IDCARD]', '$pnobbm', '$pnobbk', '$saldo', '$pcoa2')";
    }else{
        $query = "UPDATE $dbname.t_bm_sby SET divisi='$pdivisi', TANGGAL='$periode1', "
                . " COA4='$pcoa', KETERANGAN='$pket', DEBIT='$pjumlahd', KREDIT='$pjumlahk', userid='$_SESSION[IDCARD]', "
                . " NOBBM='$pnobbm', NOBBK='$pnobbk', SALDO='$saldo', COA4_K='$pcoa2' WHERE "
                . " ID='$kodenya'";
    }
    
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
  
    
?>
