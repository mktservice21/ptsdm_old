<?php
session_start();

    
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
$puserid="";
if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

if (empty($puserid)) {
    echo "ANDA HARUS LOGIN ULANG...";
    exit;
}


$pidcard=$_SESSION['IDCARD'];
$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];


if ($module=='gimicstockopn')
{

    include "../../config/koneksimysqli.php";
    
    $pdivisiwwn=$_POST['txt_udivwwn'];
    $ptgl=$_POST['e_tglbulan'];
    $pbulanopname= date("Y-m-d", strtotime($ptgl));
    $pblnpilih= date("Ym", strtotime($ptgl));
    
    $_SESSION['BRGOPNHOTGL1']=$ptgl;
    $_SESSION['BRGOPNHODIVP']=$pdivisiwwn;
    
    
    if ($act=="hapus") {
        
        $query = "DELETE FROM dbmaster.t_barang_opname WHERE DATE_FORMAT(BULAN,'%Y%m')='$pblnpilih' AND PILIHAN='$pdivisiwwn'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
        $query = "DELETE FROM dbmaster.t_barang_opname_d WHERE DATE_FORMAT(BULAN,'%Y%m')='$pblnpilih' AND PILIHAN='$pdivisiwwn'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
    }elseif ($act=="simpan") {
        $psimpandata=false;
        foreach ($_POST['chkbox_br'] as $pbarangid) {

            if (empty($pbarangid)) {
                continue;
            }
            
            $pjmlop=$_POST['txt_njmlop'][$pbarangid];
            $pjmlakhir=$_POST['txt_njmlakhir'][$pbarangid];
            $pjmllalu=$_POST['txt_njmllalu'][$pbarangid];
            $pjmlterima=$_POST['txt_njmlterima'][$pbarangid];
            $pjmlkeluar=$_POST['txt_njmlkeluar'][$pbarangid];
            $pjmlinput=$_POST['txt_njmlinput'][$pbarangid];
            $pjmlintransit=$_POST['txt_njmlintransit'][$pbarangid];
            $pnotes=$_POST['txt_nnotes'][$pbarangid];
            
            if (!empty($pnotes)) $pnotes = str_replace("'", " ", $pnotes);
            
            if (empty($pjmlop)) $pjmlop=0;
            if (empty($pjmlakhir)) $pjmlakhir=0;
            if (empty($pjmllalu)) $pjmllalu=0;
            if (empty($pjmlterima)) $pjmlterima=0;
            if (empty($pjmlkeluar)) $pjmlkeluar=0;
            if (empty($pjmlinput)) $pjmlinput=0;
            if (empty($pjmlintransit)) $pjmlintransit=0;
            
            $pjmlop=str_replace(",","", $pjmlop);
            $pjmlakhir=str_replace(",","", $pjmlakhir);
            $pjmllalu=str_replace(",","", $pjmllalu);
            $pjmlterima=str_replace(",","", $pjmlterima);
            $pjmlkeluar=str_replace(",","", $pjmlkeluar);
            $pjmlinput=str_replace(",","", $pjmlinput);
            $pjmlintransit=str_replace(",","", $pjmlintransit);
            
            //echo "$pbarangid, $pjmlop, $pjmlakhir, $pjmllalu, $pjmlterima, $pjmlkeluar, $pjmlinput, $pjmlintransit<br/>";
            
            $query = "DELETE FROM dbmaster.t_barang_opname_d WHERE DATE_FORMAT(BULAN,'%Y%m')='$pblnpilih' AND IDBARANG='$pbarangid' AND PILIHAN='$pdivisiwwn'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            $query = "INSERT INTO dbmaster.t_barang_opname_d (PILIHAN, BULAN, IDBARANG, JMLOP, JMLAKHIR, "
                    . " JMLLALU, JMLTERIMA, JMLKELUAR, JMLINPUT, JMLINTRANSIT,  USERID, NOTES)VALUES"
                    . "('$pdivisiwwn', '$pbulanopname', '$pbarangid', '$pjmlop', '$pjmlakhir', "
                    . " '$pjmllalu', '$pjmlterima', '$pjmlkeluar', '$pjmlinput', '$pjmlintransit', '$pidcard', '$pnotes')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            
            $psimpandata=true;
        }
        
        if ($psimpandata==true) {
            $query = "DELETE FROM dbmaster.t_barang_opname WHERE DATE_FORMAT(BULAN,'%Y%m')='$pblnpilih' AND PILIHAN='$pdivisiwwn'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            $query = "INSERT INTO dbmaster.t_barang_opname (PILIHAN, BULAN, USERID)VALUES"
                    . "('$pdivisiwwn', '$pbulanopname', '$pidcard')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        }
        
    }
    
    
    //echo "$act $pdivisiwwn, $pbulanopname";
    
    mysqli_close($cnmy);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=sudahsimpan');
}
?>