<?php

session_start();
include "../../config/koneksimysqli_ms.php";
$cnmy=$cnms;

$module=$_GET['module'];
$act=$_GET['act'];
$eact=$_GET['nact'];
$idmenu=$_GET['idmenu'];

if ($module=='penempatanmarketing'){
    $pidnumber=$_POST['e_id'];
    $ptgl=$_POST['e_periode'];
    $pbulan= date("Y-m-01", strtotime($ptgl));
    $pidcab=$_POST['e_idcabang'];
    $pidarea=$_POST['e_idarea'];
    
    if ($eact=="editdatamr") {
        
        $pdivisi=$_POST['e_divisi'];
        $pmrid=$_POST['cb_mr'];

        if (isset($_POST['e_pilih'])) {
            $ppilih=$_POST['e_pilih'];
            if ($ppilih=="V") $pmrid="000";
        }

        if ($act=='update') {
            //echo "$eact : $pidnumber, $pbulan, $pdivisi, MR : $pmrid, $ppilih, Cab : $pidcab, Area : $pidarea"; exit;
            
            $query = "insert into dbmaster.backup_penempatan_marketing (id, bulan, region, icabangid, areaid, divprodid, gsm, sm, dm, am, mr)"
                    . " SELECT id, bulan, region, icabangid, areaid, divprodid, gsm, sm, dm, am, mr FROM ms.penempatan_marketing WHERE "
                    . " id='$pidnumber' AND bulan='$pbulan' AND divprodid='$pdivisi' AND icabangid='$pidcab' AND areaid='$pidarea'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "BACKUP ERROR MR,... $erropesan"; exit; }
            
            $query = "UPDATE ms.penempatan_marketing SET mr='$pmrid' WHERE id='$pidnumber' AND bulan='$pbulan' AND divprodid='$pdivisi' AND "
                    . " icabangid='$pidcab' AND areaid='$pidarea'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
        }
        
    }elseif ($eact=="editdataam") {
        $pkryawal=$_POST['e_nkaryawan'];
        $pamid=$_POST['cb_am'];

        if (isset($_POST['e_pilih'])) {
            $ppilih=$_POST['e_pilih'];
            if ($ppilih=="V") $pamid="000";
        }
        
        
        
        if ($act=='update') {
            //echo "$eact : $pidnumber, $pbulan, AM : $pamid, $ppilih, Cab : $pidcab, Area : $pidarea, awal : $pkryawal"; exit;
            
            $query = "insert into dbmaster.backup_penempatan_marketing (id, bulan, region, icabangid, areaid, divprodid, gsm, sm, dm, am, mr)"
                    . " SELECT id, bulan, region, icabangid, areaid, divprodid, gsm, sm, dm, am, mr FROM ms.penempatan_marketing WHERE "
                    . " IFNULL(am,'')='$pkryawal' AND bulan='$pbulan' AND icabangid='$pidcab' AND areaid='$pidarea'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "BACKUP ERROR AM,... $erropesan"; exit; }
            
            $query = "UPDATE ms.penempatan_marketing SET am='$pamid' WHERE IFNULL(am,'')='$pkryawal' AND bulan='$pbulan' AND "
                    . " icabangid='$pidcab' AND areaid='$pidarea'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
        }
        
    }elseif ($eact=="editdatadm") {
        
        $pkryawal=$_POST['e_nkaryawan'];
        $pdmid=$_POST['cb_dm'];

        if (isset($_POST['e_pilih'])) {
            $ppilih=$_POST['e_pilih'];
            if ($ppilih=="V") $pdmid="000";
        }
        
        
        if ($act=='update') {
            //echo "$eact : $pidnumber, $pbulan, DM : $pdmid, $ppilih, Cab : $pidcab, Area : $pidarea, awal : $pkryawal"; exit;
            
            $query = "insert into dbmaster.backup_penempatan_marketing (id, bulan, region, icabangid, areaid, divprodid, gsm, sm, dm, am, mr)"
                    . " SELECT id, bulan, region, icabangid, areaid, divprodid, gsm, sm, dm, am, mr FROM ms.penempatan_marketing WHERE "
                    . " dm='$pkryawal' AND bulan='$pbulan' AND icabangid='$pidcab' AND areaid='$pidarea'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "BACKUP ERROR DM,... $erropesan"; exit; }
            
            $query = "UPDATE ms.penempatan_marketing SET dm='$pdmid' WHERE dm='$pkryawal' AND bulan='$pbulan' AND "
                    . " icabangid='$pidcab' AND areaid='$pidarea'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
        }
        
        
    }
}

?>

