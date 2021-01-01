<?php

session_start();
include "../../config/koneksimysqli_it.php";
include "../../config/fungsi_sql.php";
$cnmy=$cnit;
$dbname = "hrd";
$dbname2 = "dbmaster";


$module=$_POST['u_module'];
$act=$_POST['u_act'];
$idmenu=$_POST['u_idmenu'];


//HAPUS DATA
if (isset($_GET['ket'])) {
    $kodenya= $_GET['id'];
    $kethapus= $_GET['kethapus'];
    if ($kethapus=="null") $kethapus="";
    
    if (!empty($kodenya)) {
        
        
        $sql = "insert into $dbname2.backup_klaim 
               SELECT * FROM $dbname.klaim WHERE klaimId='$kodenya'";
        mysqli_query($cnit, $sql);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        $sql = "insert into $dbname.klaim_reject(klaimId, KET, IDREJECT, TGLREJECT)values"
                . "('$kodenya', '$kethapus', '$_SESSION[IDCARD]', NOW())";
        mysqli_query($cnit, $sql);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        //delete
        mysqli_query($cnit, "DELETE FROM $dbname.klaim WHERE klaimId='$kodenya'");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        mysqli_close($cnit);
        
    }
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act='.$act);
}


//=================================



// Hapus entry
if ($module=='entrybrklaim' AND $act=='hapus')
{
    //mysqli_query($cnmy, "update $dbname.klaim set NONAKTIF='Y' WHERE klaimId='$_GET[id]'");
    //header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='entrybrklaim' AND ($act=='editterima' OR $act=='edittransfer' OR $act=='input' OR $act=='update'))
{
    
    
    
    if ($act=='input') {
        $sql=  mysqli_query($cnmy, "select max(klaimId) as NOURUT from dbmaster.t_setup");
        $ketemu=  mysqli_num_rows($sql);
        $awal=10; $urut=1; $kodenya=""; $periode=date('Ymd');
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            $urut=$o['NOURUT']+1;
            $jml=  strlen($urut);
            $awal=$awal-$jml;
            $kodenya=str_repeat("0", $awal).$urut;
        }else{
            $kodenya=$_POST['e_klaimid'];
        }
        
        mysqli_query($cnmy, "UPDATE dbmaster.t_setup SET klaimId='$kodenya'");
        
    }else{
        $kodenya=$_POST['e_klaimid'];
    }
    
    if (empty($kodenya)){
        echo "ID kosong, ulang lagi....";
        exit;
    }

    $date1 = str_replace('/', '-', $_POST['e_tglinput']);
    
    $date2="";
    if (!empty($_POST['e_tgltrans']))
        $date2 = str_replace('/', '-', $_POST['e_tgltrans']);
    
    $ptglinput= date("Y-m-d", strtotime($date1));
    $pkaryawan=$_POST['e_idkaryawan'];
    $pdist=$_POST['e_iddist'];
    $paktivitas1=$_POST['e_aktivitas'];
    $paktivitas2=$_POST['e_aktivitas2'];
    
    if (!empty($paktivitas1)) $paktivitas1 = str_replace("'", " ", $paktivitas1);
    if (!empty($paktivitas2)) $paktivitas2 = str_replace("'", " ", $paktivitas2);
	
	
    $prpnya=str_replace(",","", $_POST['e_jmlusulan']);
    $prprealisasi=str_replace(",","", $_POST['e_realisasi']);
    $pnoslip=$_POST['e_noslip'];
    
    $ptgltras = "0000-00-00";
    if (!empty($date2)) $ptgltras= date("Y-m-d", strtotime($date2));
    
    $pcoa=$_POST['cb_coa'];
    //$pkode=  getfieldcnmy("select kodeid as lcfields from dbmaster.v_coa where COA4='$pcoa'");
    $pkode=$_POST['cb_kode'];
    $pdivisi=$_POST['cb_divisi'];
    
    $pidcabang=  getfieldcnit("select distinct iCabangId as lcfields from dbmaster.karyawan where karyawanId='$pkaryawan'");
    if (empty($pidcabang)) $pidcabang="0000000001";
    //selain OTC
    $pwilayah="01";
    $pcabwil=  substr($pidcabang, 7,3);
    if ($pidcabang=="0000000001")
        $pwilayah="01";
    else{
        $reg=  getfieldcnit("select distinct region as lcfields from dbmaster.icabang where iCabangId='$pidcabang'");
        if ($reg=="B")
            $pwilayah="02";
        else
            $pwilayah="03";
    }
    
    $pwilgabungan=$pwilayah."-".$pcabwil;
    
    
    if ($act=='input') {
        $sql=  mysqli_query($cnmy, "select klaimId from $dbname.klaim where klaimId='$kodenya'");
        $ketemu=  mysqli_num_rows($sql);
        if ($ketemu>0){
            echo "Kode : $kodenya, sudah ada";
            exit;
        }
        
        $query="insert into $dbname.klaim (klaimId, tgl, karyawanid, distid)values"
                . "('$kodenya', '$ptglinput', '$pkaryawan', '$pdist')";
        mysqli_query($cnmy, $query);
        
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

    }
        
    
    $query = "update $dbname.klaim set tgl='$ptglinput',
             aktivitas1='$paktivitas1',
             aktivitas2='$paktivitas2',
             jumlah='$prpnya',
             realisasi1='$prprealisasi',
             karyawanid='$pkaryawan',
             noslip='$pnoslip',
             tgltrans='$ptgltras',
             user1='$_SESSION[USERID]',
             distid='$pdist' where "
            . " klaimId='$kodenya'";
    mysqli_query($cnmy, $query);
    
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    if (!empty($_POST['cx_lapir'])) mysqli_query($cnmy, "update $dbname.klaim set lampiran='Y' where klaimId='$kodenya'");
    
    
    $query = "update $dbname.klaim set DIVISI='$pdivisi', "
            . "  COA4='$pcoa' where "
            . "  klaimId='$kodenya'";
    mysqli_query($cnmy, $query);
    
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $query = "update $dbname.klaim set "
            . "  KODEWILAYAH='$pwilgabungan' where "
            . "  klaimId='$kodenya'";
    mysqli_query($cnmy, $query);
    
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
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
            . " pph_jns='$pjnspph', pph='$ppph', pph_rp='$prppph', pembulatan='$ppembulatan' WHERE klaimId='$kodenya' ";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=tambahbaru');
    
}

?>

