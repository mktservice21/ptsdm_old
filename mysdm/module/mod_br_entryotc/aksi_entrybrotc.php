<?php
session_start();
//include "../../config/koneksimysqli_it.php";
include "../../config/koneksimysqli.php";
include "../../config/fungsi_sql.php";
$cnmy=$cnmy;
$dbname = "hrd";
$dbname2 = "dbmaster";//untuk update kontak realisasi

$module=$_POST['u_module'];
$act=$_POST['u_act'];
$idmenu=$_POST['u_idmenu'];




//HAPUS DATA
if (isset($_GET['ket'])) {
    $kodenya= $_GET['id'];
    $kethapus= $_GET['kethapus'];
    if ($kethapus=="null") $kethapus="";
    if (!empty($kethapus)) $kethapus = str_replace("'", " ", $kethapus);
    
    if (!empty($kodenya)) {
        
        $ncarisudahclosebrid=CariSudahClosingBRID2($kodenya, "D");
        
        if ($ncarisudahclosebrid==true) {
            echo "<span style='color:red;'>BR tersebut sudah closing SURABAYA tidak bisa dihapus/batal....</span>";
            exit;
        }
        
        if ($_GET['ket']=="batal") {
            
            $puserbatal = getfieldcnmy("select nama as lcfields from hrd.karyawan WHERE karyawanId='$_SESSION[IDCARD]'");
            $puserbatal="USER : $puserbatal";
            if (!empty($kethapus)) $kethapus=$kethapus.", ";
            $kethapus=$kethapus."".$puserbatal;
            
            mysqli_query($cnmy, "UPDATE $dbname.br_otc SET batal='Y', alasan_batal='$kethapus' WHERE brOtcId='$kodenya' AND IFNULL(batal,'N')='N'");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            
        }else{
            
            $sql = "insert into $dbname2.backup_br_otc 
                   SELECT * FROM $dbname.br_otc WHERE brOtcId='$kodenya'";
            mysqli_query($cnmy, $sql);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

            $sql = "insert into $dbname.br_otc_reject(brOtcId, KET, IDREJECT, TGLREJECT)values"
                    . "('$kodenya', '$kethapus', '$_SESSION[IDCARD]', NOW())";
            mysqli_query($cnmy, $sql);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

            //delete
            mysqli_query($cnmy, "DELETE FROM $dbname.br_otc WHERE brOtcId='$kodenya' ");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
        }
        mysqli_close($cnmy);
        
    }
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act='.$act);
}


// Hapus entry
if ($module=='entrybrotc' AND $act=='hapus')
{
    //mysqli_query($cnmy, "update $dbname.br_otc set NONAKTIF='Y' WHERE brOtcId='$_GET[id]'");
    //header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='entrybrotc' AND ($act=='editterima' OR $act=='edittransfer' OR $act=='input' OR $act=='update'))
{
    
    
    //terima
    if ($act=='editterima'){
    
        $kodenya=$_POST['e_nobr'];
        $pnoslip=$_POST['e_noslip'];
        $prprealisasi=str_replace(",","", $_POST['e_realisasi']);
        $datetrm="";
        $ptgl="null";
        if (!empty($_POST['e_tgltrm'])) {
            $datetrm = str_replace('/', '-', $_POST['e_tgltrm']);
            $ptgl= date("Y-m-d", strtotime($datetrm));
        }
        
        $pjenisid=$_POST['e_idjns'];
		
        $query = "update $dbname.br_otc set noslip='$pnoslip', "
                . "  tglreal='$ptgl', "
                . "  realisasi='$prprealisasi', lampiran='Y',ca='N' where brOtcId='$kodenya' LIMIT 1";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query = "update $dbname.br_otc set sby='Y', tglrpsby='$ptgl', jenis='$pjenisid' where brOtcId='$kodenya' LIMIT 1";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }  
        
        
        //update modif terima
        $query = "update $dbname.br_otc_ttd SET MODIFTERIMAID='$_SESSION[IDCARD]', "
                . " MODIFTERIMADATE=NOW() WHERE brOtcId='$kodenya'";
        mysqli_query($cnmy, $query);
        
		mysqli_close($cnmy);
		
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
        exit;
    }
    
    
    
    // transfer
    if ($act=='edittransfer'){
    
        $kodenya=$_POST['e_nobr'];
        $pnoslip=$_POST['e_noslip'];
        $prprealisasi=str_replace(",","", $_POST['e_realisasi']);
        $datetrm="";
        $ptgl= "0000-00-00";
        if (!empty($_POST['e_tgltrans'])) {
            $datetrm = str_replace('/', '-', $_POST['e_tgltrans']);
            $ptgl= date("Y-m-d", strtotime($datetrm));
        }
        
        $query = "update $dbname.br_otc set noslip='$pnoslip', "
                . "  tgltrans='$ptgl', "
                . "  realisasi='$prprealisasi' where brOtcId='$kodenya'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        //update modif transfer
        $query = "update $dbname.br_otc_ttd SET MODIFTRANSID='$_SESSION[IDCARD]', "
                . " MODIFTRANSDATE=NOW() WHERE brOtcId='$kodenya'";
        mysqli_query($cnmy, $query);
        
		mysqli_close($cnmy);
		
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
        exit;
    }
    
    
    
    
    
    if ($act=='input') {
        $sql=  mysqli_query($cnmy, "select max(brOtcId) as NOURUT from dbmaster.t_setup");
        $ketemu=  mysqli_num_rows($sql);
        $awal=10; $urut=1; $kodenya=""; $periode=date('Ymd');
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            $urut=$o['NOURUT']+1;
            $jml=  strlen($urut);
            $awal=$awal-$jml;
            $kodenya=str_repeat("0", $awal).$urut;
        }else{
            $kodenya=$_POST['e_idno'];
        }
        
        mysqli_query($cnmy, "UPDATE dbmaster.t_setup SET brOtcId='$kodenya'");
        
    }else{
        $kodenya=$_POST['e_idno'];
    }
    
    if (empty($kodenya)){
        echo "ID kosong, ulang lagi....";
        exit;
    }

    
    
    $date1 = str_replace('/', '-', $_POST['e_tglinput']);
    
    $ptglinput= date("Y-m-d", strtotime($date1));
    $paktivitas1=$_POST['e_aktivitas'];
    $paktivitas2=$_POST['e_aktivitas2'];
    
    if (!empty($paktivitas1)) $paktivitas1 = str_replace("'", " ", $paktivitas1);
    if (!empty($paktivitas2)) $paktivitas2 = str_replace("'", " ", $paktivitas2);
    
    $prpnya=str_replace(",","", $_POST['e_jmlusulan']);
    $pjenisuang=$_POST['cb_jenis'];
    
    $prprealisasikd="";
    $prekreal12="";
    $prprealisasi=$_POST['e_realisasi'];
    $prprealisasi2=$_POST['e_realisasi2'];
    
    $pbankreal1=$_POST['e_bank'];
    $pcabreal1=$_POST['e_cabbank'];
    //$prekreal1=$_POST['e_norekrel'];
    $prekreal12=$_POST['e_norekrel2'];
    
    
    $rekeningbnk=  str_replace("_", "", $_POST['e_norekrel']);
    $arr_kata = explode("-",$rekeningbnk);
    if (empty($arr_kata[1])) $rekeningbnk=$arr_kata[0];
    if (empty($rekeningbnk)) $rekeningbnk=str_replace("_", "", $_POST['e_norekrel']);

    $arr_kata2 = explode("-",$rekeningbnk);
    if (isset($arr_kata2[2])) {
        if (empty($arr_kata2[2])) $rekeningbnk=$arr_kata2[0]."-".$arr_kata2[1];
    }
    $prekreal1=$rekeningbnk;
    
    
    $pidcabang=  $_POST['e_idcabang'];
    $pareasdm=  $_POST['cb_areasdm'];
    
    if (trim($prprealisasi)==trim($prprealisasi2)){
        $prprealisasikd=$_POST['e_kdrealisasi'];
        
        if (trim($prekreal1)==trim($prekreal12)){
            $pkdbankreal=$_POST['e_idbank'];
        }else{
            
            //bank
            $query="insert into $dbname2.t_kontak_bank(idkontak, bank, bankcab, bankrek)values('$prprealisasikd', '$pbankreal1', '$pcabreal1', '$prekreal1')";
            mysqli_query($cnmy, $query);
            $pkdbankreal = getfieldcnmy("select MAX(id) as lcfields from $dbname2.t_kontak_bank");
        }
    }else{
        
        $query="insert into $dbname2.t_kontak_realisasi(nama, icabangid_o)values('$prprealisasi', '$pidcabang')";
        mysqli_query($cnmy, $query);
        $prprealisasikd = getfieldcnmy("select MAX(idkontak) as lcfields from $dbname2.t_kontak_realisasi");
        
        //bank
        $query="insert into $dbname2.t_kontak_bank(idkontak, bank, bankcab, bankrek)values('$prprealisasikd', '$pbankreal1', '$pcabreal1', '$prekreal1')";
        mysqli_query($cnmy, $query);
        $pkdbankreal = getfieldcnmy("select MAX(id) as lcfields from $dbname2.t_kontak_bank");
    }
    
    $pcoa=$_POST['cb_coa'];
    //$pkode=  getfieldcnmy("select kodeid as lcfields from dbmaster.v_coa WHERE COA4='$pcoa'");
    $psubkode=$_POST['cb_subpost'];
    $pkode=$_POST['cb_post'];
    
    $pbral=$_POST['cb_alokasi'];
    
    $pgrpidbiaya=$_POST['cb_grpbiaya'];
    if (empty($pgrpidbiaya)) $pgrpidbiaya="000";
    
    //if (empty($pidcabang)) $pidcabang="0000000001";
    //selain OTC
    $pwilayah="01";
    $pcabwil=  substr($pidcabang, 7,3);
    if (empty($pcabwil)) $pcabwil=substr($pidcabang, 0,3);
    
    if ($pidcabang=="0000000001")
        $pwilayah="01";
    else{
        $reg=  getfieldcnmy("select distinct region as lcfields from dbmaster.icabang WHERE iCabangId='$pidcabang'");
        if ($reg=="B")
            $pwilayah="02";
        else
            $pwilayah="03";
    }
    
    $pwilgabungan=$pwilayah."-".$pcabwil;
    
    
    
    
    
    if ($act=='input') {
        $sql=  mysqli_query($cnmy, "select brOtcId from $dbname.br_otc WHERE brOtcId='$kodenya'");
        $ketemu=  mysqli_num_rows($sql);
        if ($ketemu>0){
            echo "Kode : $kodenya, sudah ada";
            exit;
        }
        
        // jika orang HO / Fianance Input
        $query = "insert into $dbname.br_otc_ttd (brOtcId, TTDPROS_ID, TTDPROS_DATE)values('$kodenya', '$_SESSION[IDCARD]', NOW())";
        mysqli_query($cnmy, $query);
        
        $query="insert into $dbname.br_otc (brOtcId, tglbr, icabangid_o, subpost, kodeid)values"
                . "('$kodenya', '$ptglinput', '$pidcabang', '$psubkode', '$pkode')";
        mysqli_query($cnmy, $query);
        
        
        $erropesan = mysqli_error($cnmy);
        if (!empty($erropesan)) {
            echo $erropesan;
            exit;
        }

    }
    
    
    
    $query = "update $dbname.br_otc set tglbr='$ptglinput',
             icabangid_o='$pidcabang', areaid='$pareasdm', 
             subpost='$psubkode',
             kodeid='$pkode',
             gkode='$pgrpidbiaya',
             keterangan1='$paktivitas1',
             keterangan2='$paktivitas2',
             ccyid='$pjenisuang', 
             jumlah='$prpnya',
             real1='$prprealisasi',
             bankreal1='$pbankreal1',
             cbreal1='$pcabreal1',
             norekreal1='$prekreal1',
             bralid='$pbral',
             lampiran='N',
             ca='N',
             via='N',
             lampiran2='N',
             ca2='N',
             via2='N',
             idkontak='$prprealisasikd',
             user1='$_SESSION[USERID]' WHERE "
            . " brOtcId='$kodenya'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
        
    if (!empty($_POST['cx_lapir'])) mysqli_query($cnmy, "update $dbname.br_otc set lampiran='Y', lampiran2='Y' WHERE brOtcId='$kodenya'");
    if (!empty($_POST['cx_ca'])) mysqli_query($cnmy, "update $dbname.br_otc set ca='Y', ca2='Y' WHERE brOtcId='$kodenya'");
    if (!empty($_POST['cx_via'])) mysqli_query($cnmy, "update $dbname.br_otc set via='Y', via2='Y' WHERE brOtcId='$kodenya'");
    
    
    
    
    $query = "update $dbname.br_otc set "
            . "  COA4='$pcoa' WHERE "
            . "  brOtcId='$kodenya'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "update $dbname.br_otc set "
            . "  KODEWILAYAH='$pwilgabungan' WHERE "
            . "  brOtcId='$kodenya'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    //echo $pbankreal1.", ".$pcabreal1.", ".$prekreal1; exit;

    
    mysqli_query($cnmy, "delete from $dbname.br_otc_ext WHERE brotcid='$kodenya' ");
    $pareasdm=  $_POST['cb_areasdm'];
    $ptokosdm=  $_POST['cb_custsdm'];
    $pperiodesdm=  $_POST['cb_ps'];
    if (!empty($_POST['e_tglmulaisewa'])) {
        $datemulai = str_replace('/', '-', $_POST['e_tglmulaisewa']);
        $ptglmulaisdm= date("Y-m-d", strtotime($datemulai));
    }else{
        $ptglmulaisdm= "0000-00-00";
    }
    
    $query = "insert into $dbname.br_otc_ext (brotcid, icabangid_o, areaid_o, icustid_o, subpost, kodeid, periode, tglmulaisewa, user1)values"
            . "('$kodenya', '$pidcabang', '$pareasdm', '$ptokosdm', '$psubkode', '$pkode', '$pperiodesdm', '$ptglmulaisdm', '$_SESSION[USERID]')";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    if (empty($pareasdm) AND empty($ptokosdm)) {
        $query = "update $dbname.br_otc_ext set periode='', tglmulaisewa=null WHERE brotcid='$kodenya'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
    
    
    if ($act=='update') {
        $prprealisasirpjml=str_replace(",","", $_POST['e_realisasirp']);
        
        if (!empty($_POST['e_tglrptsby'])) {
            $datesby = str_replace('/', '-', $_POST['e_tglrptsby']);
            $ptglsby= date("Y-m-d", strtotime($datesby));
        }else{
            $ptglsby= "0000-00-00";
        }
        
        $pnoslip=$_POST['e_noslip'];
        
        if (!empty($_POST['e_tgltrans'])) {
            $ptgltrans = str_replace('/', '-', $_POST['e_tgltrans']);
            $ptgl= date("Y-m-d", strtotime($ptgltrans));
        }else{
            $ptgl= "0000-00-00";
        }
        
        $query = "update $dbname.br_otc set tgltrans='$ptgl', realisasi='$prprealisasirpjml', "
                . "  tglrpsby='$ptglsby', jenis='', noslip='$pnoslip' WHERE "
                . "  brOtcId='$kodenya'";
        mysqli_query($cnmy, $query);
        
        $erropesan = mysqli_error($cnmy);
        if (!empty($erropesan)) {
            echo $erropesan;
            exit;
        }
    
        if (!empty($_POST['cx_adv'])) mysqli_query($cnmy, "update $dbname.br_otc set jenis='A' WHERE brOtcId='$kodenya'");
        if (!empty($_POST['cx_klaim'])) mysqli_query($cnmy, "update $dbname.br_otc set jenis='K' WHERE brOtcId='$kodenya'");
        if (!empty($_POST['cx_sudah'])) mysqli_query($cnmy, "update $dbname.br_otc set jenis='S' WHERE brOtcId='$kodenya'");
        
        
    }
    
    

    
    
    mysqli_query($cnmy, "delete from $dbname.br_otc_bank WHERE brOtcId='$kodenya' ");
    $query = "insert into $dbname.br_otc_bank (brOtcId, id, idkontak)values('$kodenya', '$pkdbankreal' , '$prprealisasikd')";
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
    
    $ppembulatan=0;
    if (!empty($_POST['e_jmlbulat'])) $ppembulatan=str_replace(",","", $_POST['e_jmlbulat']);
    $prpmaterai=0;
    if (!empty($_POST['e_jmlmaterai'])) $prpmaterai=str_replace(",","", $_POST['e_jmlmaterai']);
    
    
    $fieldjasa = ", jasa_rp=NULL, jenis_dpp=NULL ";
    
    $pchkjasa="";
    if (isset($_POST['chk_jasa'])) $pchkjasa=$_POST['chk_jasa'];
    
    $pchkatrika="";
    if (isset($_POST['chk_atrika'])) $pchkatrika=$_POST['chk_atrika'];
    
    $prpjmljasa=0;
    if (!empty($_POST['e_rpjmljasa'])) $prpjmljasa=str_replace(",","", $_POST['e_rpjmljasa']);
    
    if (!empty($pchkjasa)) {
        $fieldjasa = ", jasa_rp='$prpjmljasa', jenis_dpp='A' ";
    }elseif (!empty($pchkatrika)) {
        $fieldjasa = ", jasa_rp='$prpjmljasa', jenis_dpp='B' ";
    }
    
    
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
        $prpmaterai=0;
    }
    
    
    $query = "update $dbname.br_otc set pajak='$pjnspajak', nama_pengusaha='$pnmpengusaha', noseri='$pnoseri',"
            . " tgl_fp='$ptglfp', dpp='$prpdpp', ppn='$pppn', ppn_rp='$prpppn', "
            . " pph_jns='$pjnspph', pph='$ppph', pph_rp='$prppph', pembulatan='$ppembulatan', materai_rp='$prpmaterai' $fieldjasa WHERE brOtcId='$kodenya' ";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
    $query = "update $dbname.br_otc set MODIFDATE=NOW() WHERE brOtcId='$kodenya' ";
    mysqli_query($cnmy, $query);
    
    
    //echo "<script>alert('sukses... <br/>No ID terakhir diinput : <b>Ada</b>'); window.location = '../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=tambahbaru'</script>";
    
	mysqli_close($cnmy);
	//mysqli_close($cnit);
	
    if ($act=='input')
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=tambahbaru');
    else
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
    
}

?>

