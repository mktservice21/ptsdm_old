<?php

session_start();

    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","500M");
    ini_set('max_execution_time', 0);
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    
    
// Hapus 
if ($module=='ksisiestimasiki' AND $act=='hapus')
{
    
}
elseif ($module=='ksisiestimasiki' AND ($act=='input' OR $act=='update') )
{
    $puserid=$_POST['e_idinputuser'];
    $pcardid=$_POST['e_idcarduser'];
    
    if (empty($puserid)) {
        $puserid="";
        if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
        if (isset($_SESSION['IDCARD'])) $pcardid=$_SESSION['IDCARD'];

        if (empty($puserid)) {
            mysqli_close($cnmy);
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }
    }
    
    $pperiodejml=6; //set 6bulan
    $pbulaninput = date("ym");
    
    $kodenya=$_POST['e_id'];
    $pbln=$_POST['e_bulan'];
    $pkdkaryawan=$_POST['cb_karyawan'];
    $piddoktere=$_POST['e_iddokt'];
    $pjmlki=$_POST['e_jumlahki'];
    $pperiode1=$_POST['e_periode1'];
    $pperiode2=$_POST['e_periode2'];
    $pperiodeall=$_POST['e_periodeall'];
    $pjmlestbln=$_POST['e_etimasiperbln'];
    $pjmlestroi=$_POST['e_mintaroi'];
    $pjml_sales=$_POST['e_cn'];
    $proi_sales=$_POST['e_roi'];
    
    $pbulan = date("Y-m-01", strtotime($pbln));
    
    if (empty($pperiode1)) $pperiode1="0000-00-00";
    if (empty($pperiode2)) $pperiode2="0000-00-00";
    
    if (!empty($pperiode1) AND $pperiode1<>"0000-00-00") $pperiode1 .="-01";
    if (!empty($pperiode2) AND $pperiode2<>"0000-00-00") $pperiode2 .="-01";
    
    
    if (empty($pjmlki)) $pjmlki=0;
    if (empty($pjmlestbln)) $pjmlestbln=0;
    if (empty($pjmlestroi)) $pjmlestroi=0;
    if (empty($pjml_sales)) $pjml_sales=0;
    if (empty($proi_sales)) $proi_sales=0;

    $pjmlki=str_replace(",","", $pjmlki);
    $pjmlestbln=str_replace(",","", $pjmlestbln);
    $pjmlestroi=str_replace(",","", $pjmlestroi);
    $pjml_sales=str_replace(",","", $pjml_sales);
    $proi_sales=str_replace(",","", $proi_sales);
    
    if ($act == 'input') {
        
        $purutan=0;
        $query = "select IFNULL(MAX(RIGHT(IFNULL(noid,0),4)),0) as NOURUT FROM hrd.t_estimasi_ki WHERE LEFT(RIGHT(noid,8),4)='$pbulaninput'";
        $tampil=mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ((INT)$ketemu>0) {
            $nrow= mysqli_fetch_array($tampil);
            if (isset($nrow['NOURUT'])) $purutan=$nrow['NOURUT'];
        }
        $purutan=PlusKode1($purutan, 4);
        $kodenya="ES".$pbulaninput.$purutan;
        
        if (!empty($kodenya)) {
            $query = "INSERT INTO hrd.t_estimasi_ki (noid, dokterid, srid, jumlah, est_perbln, est_roi, "
                    . " jml_bulan, periode1, periode2, periode_ket, "
                    . " cn, roi, userid, bulan) VALUES "
                    . " ('$kodenya', '$piddoktere', '$pkdkaryawan', '$pjmlki', '$pjmlestbln', '$pjmlestroi', "
                    . " '$pperiodejml', '$pperiode1', '$pperiode2', '$pperiodeall', "
                    . " '$pjml_sales', '$proi_sales', '$pcardid', '$pbulan')";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        }
        
    }
    
    if (!empty($kodenya)) {
        $query = "UPDATE hrd.t_estimasi_ki SET "
                . " jumlah='$pjmlki', est_perbln='$pjmlestbln', est_roi='$pjmlestroi', "
                . " jml_bulan='$pperiodejml', periode1='$pperiode1', periode2='$pperiode2', periode_ket='$pperiodeall', "
                . " cn='$pjml_sales', roi='$proi_sales', "
                . " userid='$pcardid', bulan='$pbulan' WHERE noid='$kodenya' LIMIT 1";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    }
    
    
    
    
    //echo "$puserid, $pcardid, $pperiode1 - $pperiode2 : $pperiodeall, ID : $kodenya, KI : $pjmlki, Est Bln : $pjmlestbln, Est ROI : $pjmlestroi, Perkiraan 6 bln : $pjml_sales, ROI : $proi_sales";    
    
    
    mysqli_close($cnmy);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=sudahsimpan');
    
}


function PlusKode1($pVar_,$pDigit_)
{
    if ($pVar_ == str_repeat('9',$pDigit_)) {
        $myVar_ = str_repeat('0',$pDigit_);
    } else {
        $myVar_ = intval($pVar_) + 1;
        $myVar_ = str_repeat('0',$pDigit_) . strval($myVar_);
        $myVar_ = substr($myVar_,0-$pDigit_);
    }
    return $myVar_;
}

?>