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
if ($module=='spgbr' AND $act=='realisasi')
{
    
    $kodenya=$_POST['e_id'];
    if (!empty($kodenya)) {
        $ptgltrans="";
        if (!empty($_POST['e_tgltrans'])){
            $datetrans = str_replace('/', '-', $_POST['e_tgltrans']);
            $ptgltrans= date("Y-m-d", strtotime($datetrans));
        }
        
        $ptglreal="";
        if (!empty($_POST['e_tglreal'])){
            $datereal = str_replace('/', '-', $_POST['e_tglreal']);
            $ptglreal= date("Y-m-d", strtotime($datereal));
        }
        $rpinsentif=str_replace(",","", $_POST['re_insentif']);
        $rpgaji=str_replace(",","", $_POST['re_gaji']);
        $rpumakan=str_replace(",","", $_POST['re_makan']);
        $rpsewa=str_replace(",","", $_POST['re_sewa']);
        $rppulsa=str_replace(",","", $_POST['re_pulsa']);
        $rpparkir=str_replace(",","", $_POST['re_parkir']);
        $rptotal=str_replace(",","", $_POST['re_total']);
        
        
        //INSENTIF 01
        $query="UPDATE $dbname.t_spg_br1 SET realisasirp='$rpinsentif' WHERE idbrspg='$kodenya' AND kodeid='01'";
        mysqli_query($cnmy, $query);
        //GAJI 02
        $query="UPDATE $dbname.t_spg_br1 SET realisasirp='$rpgaji' WHERE idbrspg='$kodenya' AND kodeid='02'";
        mysqli_query($cnmy, $query);
        //MAKAN 03
        $query="UPDATE $dbname.t_spg_br1 SET realisasirp='$rpumakan' WHERE idbrspg='$kodenya' AND kodeid='03'";
        mysqli_query($cnmy, $query);
        //SEWA 04
        $query="UPDATE $dbname.t_spg_br1 SET realisasirp='$rpsewa' WHERE idbrspg='$kodenya' AND kodeid='04'";
        mysqli_query($cnmy, $query);
        //PULSA 05
        $query="UPDATE $dbname.t_spg_br1 SET realisasirp='$rppulsa' WHERE idbrspg='$kodenya' AND kodeid='05'";
        mysqli_query($cnmy, $query);
        //PARKIR 06
        $query="UPDATE $dbname.t_spg_br1 SET realisasirp='$rpparkir' WHERE idbrspg='$kodenya' AND kodeid='06'";
        mysqli_query($cnmy, $query);
        
        // HITUNG LAGI REALISASI YANG TERINPUT
        $query="select sum(realisasirp) as rptotalreal FROM $dbname.t_spg_br1 WHERE idbrspg='$kodenya'";
        $tampil= mysqli_query($cnmy, $query);
        $tot= mysqli_fetch_array($tampil);
        $ptotal=$tot['rptotalreal'];
        if (empty($ptotal)) $ptotal=0;
        $query = "UPDATE $dbname.t_spg_br0 SET realisasi='$ptotal'  WHERE idbrspg='$kodenya'";
        mysqli_query($cnmy, $query);
        
        //TGL REALISASI
        if (!empty($ptglreal)) {
            $query = "UPDATE $dbname.t_spg_br0 SET tglreal='$ptglreal'  WHERE idbrspg='$kodenya'";    
        }else{
            $query = "UPDATE $dbname.t_spg_br0 SET tglreal=null  WHERE idbrspg='$kodenya'";
        }
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        //TGL TRANSFER
        if (!empty($ptgltrans)) {
            $query = "UPDATE $dbname.t_spg_br0 SET tgltrans='$ptgltrans'  WHERE idbrspg='$kodenya'";
        }else{
            $query = "UPDATE $dbname.t_spg_br0 SET tgltrans=null  WHERE idbrspg='$kodenya'";
        }
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        //echo $rptotal;
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    }
    
}