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
if ($module=='realisasibudgetmarketing' AND $act=='hapus')
{
    mysqli_query($cnmy, "DELETE FROM $dbname.t_budget_realisasi WHERE idno='$_GET[id]'");
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='realisasibudgetmarketing')
{
    $kodenya=$_POST['e_id'];
    $pdivisi=$_POST['cb_divisi'];
    
    
    $pkodeid=$_POST['cb_kodeid'];
    
    $ptgl = $_POST['e_tglberlaku'];
    $periode1= date("Y-m-01", strtotime($ptgl));
    $pjumlah=str_replace(",","", $_POST['e_jumlah']);
    $pratio=str_replace(",","", $_POST['e_ratio']);
    
    if(empty($pjumlah)) $pjumlah=0;
    if(empty($pratio)) $pratio=0;
    
    if ($act=="input") {
        $query = "INSERT INTO $dbname.t_budget_realisasi (bulan, divisi, kodeid, jumlah, ratio, userid)values"
                . "('$periode1', '$pdivisi', '$pkodeid', '$pjumlah', '$pratio', '$_SESSION[IDCARD]')";
    }else{
        $query = "UPDATE $dbname.t_budget_realisasi SET divisi='$pdivisi', bulan='$periode1', "
                . " kodeid='$pkodeid', jumlah='$pjumlah', ratio='$pratio', userid='$_SESSION[IDCARD]' WHERE "
                . " idno='$kodenya'";
    }
    
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
  
    
?>
