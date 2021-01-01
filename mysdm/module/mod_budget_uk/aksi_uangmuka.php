<?php

    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    //$cnmy=$cnit;
    $dbname = "dbmaster";
// Hapus 
if ($module=='uangmuka' AND $act=='hapus')
{
    mysqli_query($cnmy, "DELETE FROM $dbname.t_uangmuka WHERE idno='$_GET[id]'");
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='uangmuka')
{
    $kodenya=$_POST['e_id'];
    $pdivisi=$_POST['cb_divisi'];
    
    $ptgl = $_POST['e_tglberlaku'];
    $periode1= date("Y-m-d", strtotime($ptgl));
    $pjumlah=str_replace(",","", $_POST['e_jumlah']);
    
    if(empty($pjumlah)) $pjumlah=0;
    
    if ($act=="input") {
        $query = "INSERT INTO $dbname.t_uangmuka (tanggal, divisi, jumlah, userid)values"
                . "('$periode1', '$pdivisi', '$pjumlah', '$_SESSION[IDCARD]')";
    }else{
        $query = "UPDATE $dbname.t_uangmuka SET divisi='$pdivisi', tanggal='$periode1', "
                . " jumlah='$pjumlah', userid='$_SESSION[IDCARD]' WHERE "
                . " idno='$kodenya'";
    }
    
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
  
    
?>
