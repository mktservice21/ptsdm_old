<?php
session_start();
include "../../config/koneksimysqli.php";

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

// Hapus menu
if ($module=='entrydatabudget' AND $act=='hapus'){
    $pid=$_GET['id'];
    mysqli_query($cnmy, "UPDATE dbmaster.t_budget SET stsnonaktif='Y' WHERE idbudget='$pid'");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
}

// Input modul
elseif ($module=='entrydatabudget'){
    $pid=$_POST['e_id'];
    $ptahun=$_POST['e_tglberlaku'];
    $pdivisi=$_POST['cb_divisi'];
    $pkodeid=$_POST['cb_kodeid'];
    $puserid=$_SESSION['IDCARD'];
    $pjumlah=$_POST['e_jumlah'];
    if (empty($pjumlah)) $pjumlah=0;
    $pjumlah=str_replace(",","", $pjumlah);
    
    if ($act=='input') {
        $nupdate=" kodeid='$pkodeid' and tahun='$ptahun' AND g_divisi='$pdivisi' ";
        
        $sql=  mysqli_query($cnmy, "select idbudget from dbmaster.t_budget WHERE kodeid='$pkodeid' and tahun='$ptahun' AND g_divisi='$pdivisi'");
        $ketemu=  mysqli_num_rows($sql);
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            $dnobrid=$o['idbudget'];
            echo "PERIODE TERSEBUT SUDAH ADA DENGAN ID : $dnobrid";
            exit;
        }
            
        //mysqli_query($cnmy, "delete from dbmaster.t_budget WHERE kodeid='$pkodeid' and tahun='$ptahun' AND g_divisi='$pdivisi'");
        mysqli_query($cnmy, "insert into dbmaster.t_budget (tglinput, kodeid, tahun, g_divisi, userid, jumlah) values"
                . "(CURRENT_DATE(), '$pkodeid', '$ptahun', '$pdivisi', '$puserid', '$pjumlah')");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
    }else{
        $nupdate=" idbudget='$pid' ";
        
        mysqli_query($cnmy, "UPDATE dbmaster.t_budget SET kodeid='$pkodeid', g_divisi='$pdivisi', jumlah='$pjumlah' WHERE idbudget='$pid'");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
    $jan=0;    $feb=0;
    $mar=0;    $apr=0;
    $mei=0;    $jun=0;
    $jul=0;    $agu=0;                
    $sep=0;    $okt=0;
    $nov=0;    $des=0;
    $RP=0;
    $no=1;
    $ketbln=$_POST['e_jmlbln'];
    for ($k=0;$k<count($ketbln);$k++) {
        $prp=0;
        if (!empty($ketbln[$k])){
            $prp=str_replace(",","", $ketbln[$k]);
        }
        $nfield="jan";
        if ((int)$k==1) $nfield="feb";
        if ((int)$k==2) $nfield="mar";
        if ((int)$k==3) $nfield="apr";
        if ((int)$k==4) $nfield="mei";
        if ((int)$k==5) $nfield="jun";
        if ((int)$k==6) $nfield="jul";
        if ((int)$k==7) $nfield="agu";
        if ((int)$k==8) $nfield="sep";
        if ((int)$k==9) $nfield="okt";
        if ((int)$k==10) $nfield="nov";
        if ((int)$k==11) $nfield="des";
        
        if (!empty($nupdate)) {
            mysqli_query($cnmy, "UPDATE dbmaster.t_budget SET $nfield='$prp' WHERE $nupdate");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }
        //echo "$k. $prp<br/>";
    }
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
}
?>
