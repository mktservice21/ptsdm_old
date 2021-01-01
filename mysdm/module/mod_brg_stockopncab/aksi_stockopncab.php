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


if ($module=='gimicstockcabopn')
{

    include "../../config/koneksimysqli.php";
    
    $pdivisiwwn=$_POST['txt_udivwwn'];
    $picabangid=$_POST['txt_uidcabang'];
    $ptgl=$_POST['e_tglbulan'];
    $pbulanopname= date("Y-m-d", strtotime($ptgl));
    $pblnpilih= date("Ym", strtotime($ptgl));
    
    $_SESSION['BRGOPNCABTGL1']=$ptgl;
    $_SESSION['BRGOPNCABDIVP']=$pdivisiwwn;
    $_SESSION['BRGOPNCABCABA']=$picabangid;
    
    
    $filcab="ICABANGID='$picabangid' ";
    if ($pdivisiwwn=="OT") $filcab="ICABANGID_O='$picabangid' ";
    
    if ($act=="hapus") {
        
        $query = "DELETE FROM dbmaster.t_barang_cab_opname WHERE DATE_FORMAT(BULAN,'%Y%m')='$pblnpilih' AND PILIHAN='$pdivisiwwn' AND $filcab";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
        $query = "DELETE FROM dbmaster.t_barang_cab_opname_d WHERE DATE_FORMAT(BULAN,'%Y%m')='$pblnpilih' AND PILIHAN='$pdivisiwwn' AND $filcab";
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
            $pjmlblmpross=$_POST['txt_njmlblmproces'][$pbarangid];
            
            
            if (empty($pjmlop)) $pjmlop=0;
            if (empty($pjmlakhir)) $pjmlakhir=0;
            if (empty($pjmllalu)) $pjmllalu=0;
            if (empty($pjmlterima)) $pjmlterima=0;
            if (empty($pjmlkeluar)) $pjmlkeluar=0;
            if (empty($pjmlinput)) $pjmlinput=0;
            if (empty($pjmlblmpross)) $pjmlblmpross=0;
            
            $pjmlop=str_replace(",","", $pjmlop);
            $pjmlakhir=str_replace(",","", $pjmlakhir);
            $pjmllalu=str_replace(",","", $pjmllalu);
            $pjmlterima=str_replace(",","", $pjmlterima);
            $pjmlkeluar=str_replace(",","", $pjmlkeluar);
            $pjmlinput=str_replace(",","", $pjmlinput);
            $pjmlblmpross=str_replace(",","", $pjmlblmpross);
            
            //echo "$pbarangid, $pjmlop, $pjmlakhir, $pjmllalu, $pjmlterima, $pjmlkeluar, $pjmlinput, $pjmlblmpross<br/>";
            
            $query = "DELETE FROM dbmaster.t_barang_cab_opname_d WHERE DATE_FORMAT(BULAN,'%Y%m')='$pblnpilih' AND IDBARANG='$pbarangid' AND PILIHAN='$pdivisiwwn' AND $filcab";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            $query = "INSERT INTO dbmaster.t_barang_cab_opname_d (PILIHAN, BULAN, IDBARANG, JMLOP, JMLAKHIR, "
                    . " JMLLALU, JMLBLMPROS,  USERID, ICABANGID, ICABANGID_O, JMLTERIMA)VALUES"
                    . "('$pdivisiwwn', '$pbulanopname', '$pbarangid', '$pjmlop', '$pjmlakhir', "
                    . " '$pjmllalu', '$pjmlblmpross', '$pidcard', '$picabangid', '$picabangid', '$pjmlterima')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            
            $psimpandata=true;
        }
        
        if ($psimpandata==true) {
            $query = "DELETE FROM dbmaster.t_barang_cab_opname WHERE DATE_FORMAT(BULAN,'%Y%m')='$pblnpilih' AND PILIHAN='$pdivisiwwn' AND $filcab";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            $query = "INSERT INTO dbmaster.t_barang_cab_opname (PILIHAN, BULAN, USERID, ICABANGID, ICABANGID_O)VALUES"
                    . "('$pdivisiwwn', '$pbulanopname', '$pidcard', '$picabangid', '$picabangid')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        }
        
    }
    
    
    //echo "$act $pdivisiwwn, $pbulanopname";
    
    mysqli_close($cnmy);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=sudahsimpan');
}
?>