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
$pmodule=$_GET['module'];
$pact=$_GET['act'];
$pidmenu=$_GET['idmenu'];

include "../../config/koneksimysqli.php";
include "../../config/fungsi_sql.php";

if ($pmodule=='entrybrdcc')
{
    
    
    if ($pact=="hapus") {
        
        
        $kodenya=$_GET['id'];
        $pkethapus=$_GET['kethapus'];
        if (!empty($pkethapus)) $pkethapus = str_replace("'", " ", $pkethapus);
        
        
        $ncarisudahclosebrid=CariSudahClosingBRID2($kodenya, "A");
        
        if ($ncarisudahclosebrid==true) {
            echo "<span style='color:red;'>BR tersebut sudah closing SURABAYA tidak bisa dihapus....</span>";
            mysqli_close($cnmy);
            exit;
        }
        
        
        $sql = "insert into dbmaster.backup_br0 
               SELECT * FROM hrd.br0 WHERE brId='$kodenya' LIMIT 1";
        mysqli_query($cnmy, $sql);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $sql = "UPDATE dbmaster.backup_br0 SET alasan_b='$pkethapus' WHERE brId='$kodenya' LIMIT 1";
        mysqli_query($cnmy, $sql);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $sql = "insert into hrd.br0_reject(brId, KET, IDREJECT, TGLREJECT)values"
                . "('$kodenya', '$pkethapus', '$pidcard', NOW())";
        mysqli_query($cnmy, $sql);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        //delete
        mysqli_query($cnmy, "DELETE FROM hrd.br0 WHERE brId='$kodenya' LIMIT 1");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        //echo "$kodenya : $pkethapus, ";
        mysqli_close($cnmy);
        header('location:../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&act='.$pact);
        exit;
        
        
    }else {
        
        $kodenya=$_POST['e_nobr'];
        
        
        $ptglinp = str_replace('/', '-', $_POST['e_tglinput']);
        $ptgltrf = str_replace('/', '-', $_POST['e_tgltrans']);
        $ptgltransf="0000-00-00";
        $ptglinput= date("Y-m-d", strtotime($ptglinp));
        if (!empty($ptgltrf)) $ptgltransf= date("Y-m-d", strtotime($ptgltrf));
        
        $pdivprodid=$_POST['cb_divisi'];
        $pcoa=$_POST['cb_coa'];
        $pkode=$_POST['cb_kode'];
        $pkaryawan=$_POST['e_idkaryawan'];
        $pmrid=$_POST['cb_mr'];
        $pdoktermrid=$_POST['e_iddokter'];
        $pidcabang=$_POST['e_idcabang'];
        $paktivitas1=$_POST['e_aktivitas'];
        $paktivitas2=$_POST['e_aktivitas2'];
        $pnmrealisasi=$_POST['e_realisasi'];
        
        
        
        
        $pnmrelasireal="";
        $pchkrealjenis="";
        $pnmdokterrl="";
        if (isset($_POST['e_nmrealasi'])) $pnmrelasireal=$_POST['e_nmrealasi'];
        if (isset($_POST['rb_jenisreal'])) $pchkrealjenis=$_POST['rb_jenisreal'];
        if (isset($_POST['e_namadokter'])) $pnmdokterrl=$_POST['e_namadokter'];
        
        if ($pchkrealjenis=="S" AND empty($pnmrealisasi)) {
            //$pnmrealisasi=$pnmdokterrl;
        }
        
        //echo "$pchkrealjenis - $pnmrealisasi - $pnmrelasireal"; exit;
        
        if (!empty($paktivitas1)) $paktivitas1 = str_replace("'", " ", $paktivitas1);
        if (!empty($paktivitas2)) $paktivitas2 = str_replace("'", " ", $paktivitas2);
        if (!empty($pnmrealisasi)) $pnmrealisasi = str_replace("'", " ", $pnmrealisasi);
        if (!empty($pnmrelasireal)) $pnmrelasireal = str_replace("'", " ", $pnmrelasireal);
        
        $pjenisuang=$_POST['cb_jenis'];
        $prpnya=$_POST['e_jmlusulan'];
        $prpcn=$_POST['e_cn'];
        
        if (empty($prpnya)) $prpnya=0;
        if (empty($prpcn)) $prpcn=0;
        
        $prpnya=str_replace(",","", $prpnya);
        $prpcn=str_replace(",","", $prpcn);
        
        $pnoslip=$_POST['e_noslip'];
        if (!empty($pnoslip)) $pnoslip = str_replace("'", " ", $pnoslip);
        
        $pinplampiran="N";
        $pinpca="N";
        $pinpsby="N";
        
        if (isset($_POST['cx_lapir'])) {
            if (!empty($_POST['cx_lapir'])) $pinplampiran="Y";
        }
        
        if (isset($_POST['cx_ca'])) {
            if (!empty($_POST['cx_ca'])) $pinpca="Y";
        }
        
        if (isset($_POST['cx_via'])) {
            if (!empty($_POST['cx_via'])) $pinpsby="Y";
        }
            
        
        
        if ($pcoa=="") {
            echo "coa kosong..."; mysqli_close($cnmy); exit;
        }

        if ($pkode=="") {
            echo "kode posting kosong..."; mysqli_close($cnmy); exit;
        }
    
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
    
        
        //cari daerah
        $pcabangytd=  getfieldcnmy("select distinct idcabang as lcfields from dbmaster.cabangytd where icabangid='$pidcabang'");
        $pjabtanid="";
        $pcarijbt=$pkaryawan;
        if (!empty($pmr)) $pcarijbt=$pmr;

        $pjabatanid=  getfieldcnmy("select distinct jabatanid as lcfields from hrd.karyawan where karyawanId='$pcarijbt'");

        $pcaricbgytd="";
        if ($pjabatanid=="15") {
            $pcaricbgytd=  getfieldcnmy("select idcbg as lcfields from mkt.cabangareaytd WHERE CONCAT(icabangid,areaid) IN 
                    (select CONCAT(icabangid,areaid) from mkt.imr0 where karyawanid='$pcarijbt') LIMIT 1");
        }elseif ($pjabatanid=="10" OR $pjabatanid=="18") {
            $pcaricbgytd=  getfieldcnmy("select idcbg as lcfields from mkt.cabangareaytd WHERE CONCAT(icabangid,areaid) IN 
                    (select CONCAT(icabangid,areaid) from mkt.ispv0 where karyawanid='$pcarijbt') LIMIT 1");
        }
        if (!empty($pcaricbgytd)) $pcabangytd=$pcaricbgytd;
        
        
        //echo "$kodenya, $ptglinput, $ptgltransf<br/>$pdivprodid, $pcoa, $pkode, $pkaryawan, $pidcabang<br/>$paktivitas1<br/>$pjenisuang, $prpnya, $pnmrealisasi, $prpcn, $pnoslip, $pwilgabungan<br>Jbt : $pjabatanid, daerah : $pcabangytd<br/>Lamp : $pinplampiran, $pinpca, $pinpsby"; mysqli_close($cnmy); exit;
        
        if ($pact=="simpan") {
            
            $sql=  mysqli_query($cnmy, "select max(brId) as NOURUT from dbmaster.t_setup");
            $ketemu=  mysqli_num_rows($sql);
            $awal=10; $urut=1; $kodenya=""; $periode=date('Ymd');
            if ($ketemu>0){
                $o=  mysqli_fetch_array($sql);
                $urut=$o['NOURUT']+1;
                $jml=  strlen($urut);
                $awal=$awal-$jml;
                $kodenya=str_repeat("0", $awal).$urut;

                mysqli_query($cnmy, "UPDATE dbmaster.t_setup SET brId='$kodenya'");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            }
            
            if (empty($kodenya)) {
                echo "Kode ID input kosong...."; mysqli_close($cnmy); exit;
            }
            
            $sql=  mysqli_query($cnmy, "select brId from hrd.br0 where brId='$kodenya'");
            $ketemu=  mysqli_num_rows($sql);
            if ($ketemu>0){
                echo "Kode : $kodenya, sudah ada"; mysqli_close($cnmy); exit;
            }
            
            
            //hapus dulu di temporary triger
            $query = "DELETE FROM hrd.t_b_br0 WHERE brid='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            //INSERT INTO
            $query = "insert into hrd.br0 (brid, tgl, divprodid, COA4, kode, user1, aktivitas1, aktivitas2, ccyid, "
                    . " jumlah, cn, realisasi1, karyawanid, icabangid, KODEWILAYAH, idcabang, "
                    . " lampiran, ca, via, noslip, mrid, dokterId, realisasi2, idkontak) VALUES"
                    . " ('$kodenya', '$ptglinput', '$pdivprodid', '$pcoa', '$pkode', '$puserid', '$paktivitas1', '$paktivitas2', '$pjenisuang', "
                    . " '$prpnya', '$prpcn', '$pnmrealisasi', '$pkaryawan', '$pidcabang', '$pwilgabungan', '$pcabangytd', "
                    . " '$pinplampiran', '$pinpca', '$pinpsby', '$pnoslip', '$pmrid', '$pdoktermrid', '$pnmrelasireal', '$pchkrealjenis')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $query = "UPDATE hrd.br0 SET tgltrans='0000-00-00', tgl1='0000-00-00', "
                    . " tglacc='0000-00-00', tglretur='0000-00-00', tglunrtr='0000-00-00', tgltrm='0000-00-00', "
                    . " tglst='0000-00-00', tglrpsby='0000-00-00', "
                    . " app_owner_date='0000-00-00', app_director_date='0000-00-00', app_acc='0000-00-00' WHERE brid='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            // jika orang HO / Fianance Input
            $query = "insert into hrd.br0_ttd (brId, TTDPROS_ID, TTDPROS_DATE)values('$kodenya', '$pidcard', NOW())";
            mysqli_query($cnmy, $query);
            
            //update modif transfer
            if (!empty($ptgltransf) AND $ptgltransf<>"0000-00-00") {
                $query = "update hrd.br0_ttd SET MODIFTRANSID='$pidcard', MODIFTRANSDATE=NOW() WHERE brId='$kodenya' LIMIT 1";
                mysqli_query($cnmy, $query);
            }
            
        }else{
            if (empty($kodenya)) {
                echo "Kode ID input kosong...."; mysqli_close($cnmy); exit;
            }
        }
        
        
        $query = "UPDATE hrd.br0 SET tgltrans='$ptgltransf', tgl='$ptglinput', "
                . " divprodid='$pdivprodid', COA4='$pcoa', kode='$pkode', aktivitas1='$paktivitas1', "
                . " aktivitas2='$paktivitas2', ccyid='$pjenisuang', "
                . " jumlah='$prpnya', cn='$prpcn', realisasi1='$pnmrealisasi', realisasi2='$pnmrelasireal', idkontak='$pchkrealjenis', "
                . " karyawanid='$pkaryawan', icabangid='$pidcabang', KODEWILAYAH='$pwilgabungan', idcabang='$pcabangytd', "
                . " lampiran='$pinplampiran', ca='$pinpca', via='$pinpsby', "
                . " noslip='$pnoslip', mrid='$pmrid', dokterId='$pdoktermrid' WHERE brid='$kodenya' LIMIT 1";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            
        
        //DCC/DSS
        $nama_kd="";
        $query_kd = "select nama from hrd.br_kode where kodeid='$pkode'";
        $result_kd = mysqli_query($cnmy, $query_kd);
        $num_results_kd = mysqli_num_rows($result_kd);
        if ($num_results_kd) {
             $row_kd = mysqli_fetch_array($result_kd);
             $nama_kd = $row_kd['nama'];
        }
        
        
        //AREA
        $pareaid="";
        if ($nama_kd == 'DCC') {
            $query_ar = "select areaId from hrd.karyawan where karyawanId='$pkaryawan'";
        } else {
            $query_ar = "select areaId from hrd.karyawan where karyawanId='$pmrid'";
        }
        $result_ar = mysqli_query($cnmy, $query_ar);
        $num_results_ar = mysqli_num_rows($result_ar);
        if ($num_results_ar) {
            $row_ar = mysqli_fetch_array($result_ar);
            $pareaid = $row_ar['areaId'];
        }
        if ($pareaid=='') {
            $pareaid='0000000001';
        }
        
        $query = "update hrd.br0 set areaid='$pareaid' where brId='$kodenya' LIMIT 1";
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
        
        
        $query = "update hrd.br0 set pajak='$pjnspajak', nama_pengusaha='$pnmpengusaha', noseri='$pnoseri',"
                . " tgl_fp='$ptglfp', dpp='$prpdpp', ppn='$pppn', ppn_rp='$prpppn', "
                . " pph_jns='$pjnspph', pph='$ppph', pph_rp='$prppph', pembulatan='$ppembulatan', "
                . " materai_rp='$prpmaterai' $fieldjasa WHERE brId='$kodenya' LIMIT 1";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
        
        
        $query = "update hrd.br0 set MODIFDATE=NOW() where brId='$kodenya' LIMIT 1";
        mysqli_query($cnmy, $query);

        
        if ($pact=='simpan') {
            $query = "INSERT INTO dbmaster.t_br0_coa (brId, coa4, kodeid) values('$kodenya', '$pcoa', '$pkode')";
            mysqli_query($cnmy, $query);
        }else{
            $query = "update dbmaster.t_br0_coa set coa_u2='$pcoa', kodeid_u2='$pkode' where brId='$kodenya' AND IFNULL(coa_u1,'')<>'' AND IFNULL(coa_u2,'')=''";
            mysqli_query($cnmy, $query);

            $query = "update dbmaster.t_br0_coa set coa_u1='$pcoa', kodeid_u1='$pkode' where brId='$kodenya' AND IFNULL(coa_u1,'')=''";
            mysqli_query($cnmy, $query);
        }
    
    
        
        
        
        mysqli_close($cnmy);
        //header('location:../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&act='.$pact);
        
        if ($pact=='simpan')
            header('location:../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&act=tambahbaru');
        else
            header('location:../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&act=complete');
        
        exit;
        
        
    }
    
    
}

mysqli_close($cnmy);
?>