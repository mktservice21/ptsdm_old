<?php

    session_start();
    include "../../config/koneksimysqli_it.php";
    include "../../config/fungsi_sql.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    $cnmy=$cnit;
    $dbname = "dbmaster";
    
    
// Hapus 
if ($module=='entrybrbulan' AND $act=='hapus')
{
    $kethapus= $_GET['kethapus'];
    mysqli_query($cnmy, "update $dbname.t_br_bulan set ket_hapus='$kethapus', stsnonaktif='Y' WHERE idbr='$_GET[id]'");
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='entrybrbulan')
{
    $pmr = $_SESSION['IDCARD'];
    $date1 = str_replace('/', '-', $_POST['e_periode01']);
    $date2 = str_replace('/', '-', $_POST['e_periode02']);
    $date3 = str_replace('/', '-', $_POST['e_tglberlaku']);
    
    $periode1= date("Y-m-d", strtotime($date1));
    $periode2= date("Y-m-d", strtotime($date2));
    
    $tanggal3x = "01"."-".$date3; 
    $periode3 = date("Y-m-d", strtotime($tanggal3x));
    
    $pkaryawan = $_SESSION['IDCARD']; 
    $pidcabang = $_POST['e_idcabang'];
    $pdivprodid = $_POST['cb_divisi'];
    $pcoa = $_POST['cb_coa'];
    
    if (empty($pdivprodid)) $pdivprodid = getfieldcnit("select divisiId as lcfields from hrd.karyawan where karyawanId='$pkaryawan'");
    if (empty($pdivprodid)) $pdivprodid = getfieldcnit ("select divisiId2 as lcfields from hrd.karyawan where karyawanId='$pkaryawan'");
    
    $pjumlah=str_replace(",","", $_POST['e_jmlusulan']);
    $pket = $_POST['e_aktivitas'];
    
    
    $pwilayah="01";
    $pcabwil=  substr($pidcabang, 7,3);
    if ($pidcabang=="0000000001")
        $pwilayah="01";
    else{
        $reg=  getfieldcnit("select distinct region as lcfields from dbmaster.icabang where iCabangId='$pidcabang'");
        if ($pdivprodid=="OTC") {
            if ($reg=="B")
                $pwilayah="04";
            else
                $pwilayah="05";
        }else{
            if ($reg=="B")
                $pwilayah="02";
            else
                $pwilayah="03";
        }
    }
    
    $pwilgabungan=$pwilayah."-".$pcabwil;
    
    
    
    if ($act=="input") {
        $sql=  mysqli_query($cnmy, "select max(idbr) as NOURUT from $dbname.t_br_bulan");
        $ketemu=  mysqli_num_rows($sql);
        $awal=10; $urut=1; $kodenya=""; $periode=date('Ymd');
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            $urut=$o['NOURUT']+1;
            $jml=  strlen($urut);
            $awal=$awal-$jml;
            $kodenya=str_repeat("0", $awal).$urut;
        }else{
            $kodenya=$_POST['e_id'];
        }
    }else{
        $kodenya=$_POST['e_id'];
    }
    
    if (empty($kodenya)){
        echo "ID kosong, ulang lagi....";
        exit;
    }
    
    if (empty(trim($pdivprodid))) { echo "divisi kosong"; exit; }
    if (empty(trim($pidcabang))) { echo "cabang kosong"; exit; }
    if (empty(trim($pcoa))) { echo "COA kosong"; exit; }
    if (empty(trim($periode3))) { echo "periode kosong"; exit; }
    
    
    if ($act=='input') {
        $sql=  mysqli_query($cnmy, "select idbr from $dbname.t_br_bulan where idbr='$kodenya'");
        $ketemu=  mysqli_num_rows($sql);
        if ($ketemu>0){
            echo "Kode : $kodenya, sudah ada";
            exit;
        }
        
        $query="insert into $dbname.t_br_bulan (idbr, tgl, karyawanid)values"
                . "('$kodenya', CURRENT_DATE(), '$pkaryawan')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
    }
    
    $query = "update $dbname.t_br_bulan set "
            . "  karyawanid='$pkaryawan',"
            . "  icabangid='$pidcabang', "
            . "  divisi='$pdivprodid', "
            . "  COA4='$pcoa', "
            . "  KODEWILAYAH='$pwilgabungan', "
            . "  periode='$periode3', "
            . "  jumlah='$pjumlah', "
            . "  keterangan='$pket', "
            . "  userid='$pmr' where idbr='$kodenya'";
    
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

    
    if (trim($pdivprodid)=="OTC") {
        $query = "update $dbname.t_br_bulan set divi='OTC'  where idbr='$kodenya'";
        mysqli_query($cnmy, $query);
    }
    
    if ($act=="input")
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=tambahbaru');
    else
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
}
?>

