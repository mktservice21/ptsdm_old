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

    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    $dbname = "hrd";
    $dbname2 = "dbmaster";
    
// Hapus 
if ($module=='entrybrklaim' AND $act=='hapus')
{
    
    $kodenya=$_GET['id'];
    $kethapus= $_GET['kethapus'];
    if ($kethapus=="null") $kethapus="";
    
        $sql = "insert into $dbname2.backup_klaim 
               SELECT * FROM $dbname.klaim WHERE klaimId='$kodenya' LIMIT 1";
        mysqli_query($cnmy, $sql);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    
        $sql = "insert into $dbname.klaim_reject(klaimId, KET, IDREJECT, TGLREJECT)values"
                . "('$kodenya', '$kethapus', '$_SESSION[IDCARD]', NOW())";
        mysqli_query($cnmy, $sql);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
        
        //delete
        mysqli_query($cnmy, "DELETE FROM $dbname.klaim WHERE klaimId='$kodenya' LIMIT 1");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
    mysqli_close($cnmy);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='entrybrklaim')
{
    
    $kodenya=$_POST['e_klaimid'];
    $date1 = str_replace('/', '-', $_POST['e_tglinput']);
    
    $date2="";
    if (!empty($_POST['e_tgltrans']))
        $date2 = str_replace('/', '-', $_POST['e_tgltrans']);
    
    $ptglinput= date("Y-m-d", strtotime($date1));
    $ptgltras = "0000-00-00";
    if (!empty($date2)) $ptgltras= date("Y-m-d", strtotime($date2));
    
    $pkaryawan=$_POST['e_idkaryawan'];
    $pdist=$_POST['e_iddist'];
    $paktivitas1=$_POST['e_aktivitas'];
    $paktivitas2=$_POST['e_aktivitas2'];
    
    if (!empty($paktivitas1)) $paktivitas1 = str_replace("'", " ", $paktivitas1);
    if (!empty($paktivitas2)) $paktivitas2 = str_replace("'", " ", $paktivitas2);
    
    $pdivpengajuan=$_POST['cb_divpengajuan'];
    
    $pcoa = "755-31";
    if ($pdivpengajuan=="EAGLE") $pcoa = "751-31";
    elseif ($pdivpengajuan=="PIGEO") $pcoa = "752-31";
    elseif ($pdivpengajuan=="PEACO") $pcoa = "753-31";
    elseif ($pdivpengajuan=="OTC") $pcoa = "754-31";
    elseif ($pdivpengajuan=="CAN") $pcoa = "755-31";
    
    $pdivisi="CAN";
    if ($pdivpengajuan=="EAGLE") $pdivisi = "EAGLE";
    elseif ($pdivpengajuan=="PIGEO") $pdivisi = "PIGEO";
    elseif ($pdivpengajuan=="PEACO") $pdivisi = "PEACO";
    elseif ($pdivpengajuan=="OTC") $pdivisi = "OTC";
    
    
    $pidcabang=  getfieldcnmy("select distinct iCabangId as lcfields from dbmaster.karyawan where karyawanId='$pkaryawan'");
    if (empty($pidcabang)) $pidcabang="0000000001";
    //selain OTC
    $pwilayah="01";
    $pcabwil=  substr($pidcabang, 7,3);
    if ($pidcabang=="0000000001")
        $pwilayah="01";
    else{
        $reg=  getfieldcnmy("select distinct region as lcfields from dbmaster.icabang where iCabangId='$pidcabang'");
        if ($reg=="B")
            $pwilayah="02";
        else
            $pwilayah="03";
    }
    
    $pwilgabungan=$pwilayah."-".$pcabwil;
    
    
    $pjmlrp=$_POST['e_jmlusulan'];
    if (empty($pjmlrp)) $pjmlrp="0";
    $prpnya=str_replace(",","", $pjmlrp);
    
    $pnmrealisasi=$_POST['e_realisasi'];
    if (!empty($pnmrealisasi)) $pnmrealisasi = str_replace("'", " ", $pnmrealisasi);
    
    
    $pnoslip=$_POST['e_noslip'];
    if (!empty($pnoslip)) $pnoslip = str_replace("'", " ", $pnoslip);
    
    
    //echo "$kodenya, $ptglinput, trans : $ptgltras, kry : $pkaryawan, $pdist, $paktivitas1, $paktivitas2<br/>$pdivpengajuan, $pcoa, $pdivisi, $pwilgabungan"; mysqli_close($cnmy); exit;
    
    if ($act=='input') {
        $sql=  mysqli_query($cnmy, "select max(RIGHT(klaimId,9)) as NOURUT from dbmaster.t_setup");
        $ketemu=  mysqli_num_rows($sql);
        $awal=9; $urut=1; $kodenya=""; $periode=date('Ymd');
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            $urut=$o['NOURUT']+1;
            $jml=  strlen($urut);
            $awal=$awal-$jml;
            $kodenya="D".str_repeat("0", $awal).$urut;
        }
        
        mysqli_query($cnmy, "UPDATE dbmaster.t_setup SET klaimId='$kodenya'");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
        $sql=  mysqli_query($cnmy, "select klaimId from $dbname.klaim where klaimId='$kodenya'");
        $ketemu=  mysqli_num_rows($sql);
        if ($ketemu>0){
            echo "Kode : $kodenya, sudah ada";
            mysqli_close($cnmy);
            exit;
        }
        
        
    }
    
    if (empty($kodenya)){
        echo "ID kosong, ulang lagi....";
        mysqli_close($cnmy);
        exit;
    }
    
    if ($act=='input') {
        
        $query = "INSERT INTO dbttd.klaim_ttd(klaimId)VALUES('$kodenya')";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

        
        $query="insert into $dbname.klaim (klaimId, tgl, karyawanid, distid, pengajuan)values"
                . "('$kodenya', '$ptglinput', '$pkaryawan', '$pdist', '$pdivpengajuan')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    }
    
    $query = "update $dbname.klaim set tgl='$ptglinput',
             aktivitas1='$paktivitas1',
             aktivitas2='$paktivitas2',
             jumlah='$prpnya',
             realisasi1='$pnmrealisasi',
             karyawanid='$pkaryawan',
             noslip='$pnoslip',
             tgltrans='$ptgltras',
             user1='$puserid',
             distid='$pdist', "
            . " DIVISI='$pdivisi', COA4='$pcoa', pengajuan='$pdivpengajuan', "
            . " KODEWILAYAH='$pwilgabungan' WHERE klaimId='$kodenya' LIMIT 1";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    
    
    if (!empty($_POST['cx_lapir'])) {
        mysqli_query($cnmy, "update $dbname.klaim set lampiran='Y' where klaimId='$kodenya' LIMIT 1");
        if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    }
   
    //pajak
    $pjnspajak=$_POST['cb_pajak'];
    $pnmpengusaha=$_POST['e_kenapajak'];
    $pnoseri=$_POST['e_noserifp'];
    
    $ptglfp="0000-00-00";
    if (!empty($_POST['e_tglpajak'])) {
        $datepajak = str_replace('/', '-', $_POST['e_tglpajak']);
        $ptglfp= date("Y-m-d", strtotime($datepajak));
    }
    
    $prpdpp=str_replace(",","", $_POST['e_jmldpp']);
    $pppn=$_POST['e_jmlppn'];
    $prpppn=str_replace(",","", $_POST['e_jmlrpppn']);
    $pjnspph=$_POST['cb_pph'];
    $ppph=$_POST['e_jmlpph'];
    $prppph=str_replace(",","", $_POST['e_jmlrppph']);
    $ppembulatan=str_replace(",","", $_POST['e_jmlbulat']);
    
    if ($pjnspajak!="Y") {
        $pnmpengusaha="";
        $pnoseri="";
        $ptglfp="0000-00-00";
        $prpdpp=0;
        $pppn=0;
        $prpppn=0;
        $pjnspph="";
        $ppph=0;
        $prppph=0;
        $ppembulatan=0;
    }
    
    
    $query = "update $dbname.klaim set pajak='$pjnspajak', nama_pengusaha='$pnmpengusaha', noseri='$pnoseri',"
            . " tgl_fp='$ptglfp', dpp='$prpdpp', ppn='$pppn', ppn_rp='$prpppn', "
            . " pph_jns='$pjnspph', pph='$ppph', pph_rp='$prppph', pembulatan='$ppembulatan' WHERE klaimId='$kodenya' LIMIT 1";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    mysqli_close($cnmy);
    
    if ($act=='input')
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=tambahbaru');
    else
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');

    
}
?>
