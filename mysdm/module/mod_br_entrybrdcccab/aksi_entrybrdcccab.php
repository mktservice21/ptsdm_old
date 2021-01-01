<?php
date_default_timezone_set('Asia/Jakarta');
session_start();
$puserid=$_SESSION['IDCARD'];
if (empty($puserid)) {
    echo "ANDA HARUS LOGIN ULANG...!!!";
    exit;
}

include "../../config/koneksimysqli.php";
include "../../config/fungsi_sql.php";
$dbname = "dbmaster";

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

// Hapus
if ($module=='entrybrdcccabang' AND $act=='hapus')
{
    $kodenya=$_GET['id'];
    $pkethapus=$_GET['kethapus'];
    if (!empty($pkethapus)) $pkethapus = str_replace("'", " ", $pkethapus);
    
    mysqli_query($cnmy, "update $dbname.t_br_cab set stsnonaktif='Y', alasan_batal='$pkethapus', userid='$puserid' WHERE bridinputcab='$kodenya'");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
    //echo "$kodenya, $pkethapus";
}
elseif ($module=='entrybrdcccabang')
{
    $kodenya=$_POST['e_id'];
    
    $ptgl = str_replace('/', '-', $_POST['e_tglinput']);
    $ptglpengajuan= date("Y-m-d", strtotime($ptgl));
            
    //$pcabangid=$_POST['cb_idcabang'];
    $pcabangid=$_POST['cb_cabangpil'];
    $pkaryawanid=$_POST['e_idkaryawan'];
    $pjabatanid=$_POST['e_jabatanid'];
    $pmrid=$_POST['cb_idmr'];
    $pdokterid=$_POST['cb_iddokter'];
    $pdivisi=$_POST['cb_divisi'];
    $pcoa=$_POST['cb_kode'];
    $pkodeid="";
    
    $paktivitas=$_POST['e_aktivitas'];
    $pccyid=$_POST['cb_jenis'];
    $pjumlahusul=$_POST['e_jmlusulan'];
    
    $patasan1=$_POST['cb_apvspv'];
    $patasan2=$_POST['cb_apvdm'];
    $patasan3=$_POST['cb_sm'];
    $patasan4=$_POST['cb_gsm'];
    
    
    if ($act=='input') {
        $query = "select bridinputcab from dbmaster.t_br_cab WHERE karyawanid='$pkaryawanid' AND dokterid='$pdokterid' AND tgl='$ptglpengajuan'";
        $tampilb= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampilb);
        if ($ketemu>0) {
            $nr= mysqli_fetch_array($tampilb);
            $pbrinputada=$nr['bridinputcab'];
            echo "Karyawan dan dokter pada tanggal tersebut sudah ada : $pbrinputada";
            mysqli_close($cnmy);
            exit;
        }
    }
    
    $pjumlahusul=str_replace(",","", $pjumlahusul);
    if (!empty($paktivitas)) $paktivitas = str_replace("'", " ", $paktivitas);
    if (empty($pccyid)) $pccyid= "IDR";
    
    $query = "select kodeid from dbmaster.coa_level4 where COA4='$pcoa'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $nr= mysqli_fetch_array($tampil);
        $pkodeid=$nr['kodeid'];
    }
    
    
    $pelevel="";
    $query = "select LEVELPOSISI from dbmaster.jabatan_level where jabatanId='$pjabatanid'";
    $tampil_= mysqli_query($cnmy, $query);
    $ketemu_= mysqli_num_rows($tampil_);
    if ($ketemu_>0) {
        $nx= mysqli_fetch_array($tampil_);
        $pelevel=TRIM($nx['LEVELPOSISI']);
    }
    
    
    //cari daerah
    $pcabangytd=  getfieldcnit("select distinct idcabang as lcfields from dbmaster.cabangytd where icabangid='$pcabangid'");
    $pcarijbt=$pkaryawanid;
    //if (!empty($pmrid)) $pcarijbt=$pmrid;
    
    $pcaricbgytd="";
    if ($pjabatanid=="15") {
        $pcaricbgytd=  getfieldcnit("select idcbg as lcfields from MKT.cabangareaytd WHERE CONCAT(icabangid,areaid) IN 
                (select CONCAT(icabangid,areaid) from MKT.imr0 where karyawanid='$pcarijbt') LIMIT 1");
    }elseif ($pjabatanid=="10" OR $pjabatanid=="18") {
        $pcaricbgytd=  getfieldcnit("select idcbg as lcfields from MKT.cabangareaytd WHERE CONCAT(icabangid,areaid) IN 
                (select CONCAT(icabangid,areaid) from MKT.ispv0 where karyawanid='$pcarijbt') LIMIT 1");
    }
    if (!empty($pcaricbgytd)) $pcabangytd=$pcaricbgytd;
    
            //echo "$pcabangytd"; exit;
    
    //END cari daerah
    
    if ($act=='input') {
        $pgambar=$_POST['txtgambar'];
        
        
        $sql=  mysqli_query($cnmy, "select MAX(RIGHT(bridinputcab,8)) as NOURUT from $dbname.t_br_cab");
        $ketemu=  mysqli_num_rows($sql);
        $awal=8; $urut=1; $kodenya=""; $periode=date('Ymd');
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            if (empty($o['NOURUT'])) $o['NOURUT']=0;
            $urut=$o['NOURUT']+1;
            $jml=  strlen($urut);
            $awal=$awal-$jml;
            $kodenya="CB".str_repeat("0", $awal).$urut;
        }else{
            $kodenya="CB00000001";
        }
        
        
        
        $sql=  mysqli_query($cnmy, "select bridinputcab from $dbname.t_br_cab where bridinputcab='$kodenya'");
        $ketemu=  mysqli_num_rows($sql);
        if ($ketemu>0){
            echo "Kode : $kodenya, sudah ada";
            exit;
        }
        
        $query="insert into $dbname.t_br_cab (bridinputcab, tglinput, tgl, kode, coa4, dokterid, "
                . "aktivitas, ccyid, jumlah, divisi, icabangid, karyawanid, jabatanid, karyawanid2, gambar)values"
                . "('$kodenya', CURRENT_DATE(), '$ptglpengajuan', '$pkodeid', '$pcoa', '$pdokterid', "
                . " '$paktivitas', '$pccyid', '$pjumlahusul', '$pdivisi', '$pcabangid', '$pkaryawanid', '$pjabatanid', '$pmrid', '$pgambar')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
    
    
    
    
    $query = "UPDATE $dbname.t_br_cab SET "
            . " tgl='$ptglpengajuan', kode='$pkodeid', coa4='$pcoa', dokterid='$pdokterid', "
            . " aktivitas='$paktivitas', ccyid='$pccyid', jumlah='$pjumlahusul', divisi='$pdivisi', icabangid='$pcabangid', "
            . " karyawanid='$pkaryawanid', jabatanid='$pjabatanid', karyawanid2='$pmrid', "
            . " userid='$puserid', atasan1='$patasan1', atasan2='$patasan2', atasan3='$patasan3', atasan4='$patasan4', idcabang='$pcabangytd' WHERE "
            . " bridinputcab='$kodenya'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
    
    
    $query = "";
    if ($pelevel=="FF2") {
        $query = "update $dbname.t_br_cab set atasan1='$pkaryawanid', tgl_atasan1=NOW() WHERE "
                . " bridinputcab='$kodenya'";
    }elseif ($pelevel=="FF3") {
        $query = "update $dbname.t_br_cab set atasan1='$pkaryawanid', tgl_atasan1=NOW(), atasan2='$pkaryawanid', tgl_atasan2=NOW() WHERE "
                . " bridinputcab='$kodenya'";
    }elseif ($pelevel=="FF4") {
        $query = "update $dbname.t_br_cab set atasan3='$pkaryawanid', tgl_atasan3=NOW(), "
                . " atasan1='$pkaryawanid', tgl_atasan1=NOW(), atasan2='$pkaryawanid', tgl_atasan2=NOW() WHERE bridinputcab='$kodenya'";
    }else{
        $nolevel=0;
        if (trim(substr($pelevel, 0, 2)=="FF")) {
            if (!empty(substr($pelevel, 2, 2))) {
                $nolevel=(int)substr($pelevel, 2, 2);
                if ($nolevel>4) {
                    $query = "update $dbname.t_br_cab set atasan4='$pkaryawanid', atasan3='$pkaryawanid', tgl_atasan3=NOW(), "
                            . " atasan1='$pkaryawanid', tgl_atasan1=NOW(), atasan2='$pkaryawanid', tgl_atasan2=NOW() WHERE bridinputcab='$kodenya'";
                }
            }
        }
    }
    if (!empty($query)) {
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
    
    //MR jika SPV/AM nya NN
    if ((int)$pjabatanid==15) {
        $query = "SELECT distinct karyawanid FROM dbmaster.t_karyawan_apv where status='SPV' and karyawanid='$patasan1'";
        $ketemu= mysqli_num_rows(mysqli_query($cnmy, $query));
        if ($ketemu>0) {
            $query = "update $dbname.t_br_cab set tgl_atasan1=NOW() WHERE bridinputcab='$kodenya'";
            mysqli_query($cnmy, $query);
        }
        if (!empty($patasan2) AND empty($patasan1)) {
            $query = "update $dbname.t_br_cab set tgl_atasan1=NOW() WHERE bridinputcab='$kodenya'";
            mysqli_query($cnmy, $query);
        }else{
        
            if (empty($patasan1) AND empty($patasan2) AND !empty($patasan3)) {
                $query = "update $dbname.t_br_cab set tgl_atasan1=NOW(), tgl_atasan2=NOW() WHERE bridinputcab='$kodenya'";
                mysqli_query($cnmy, $query);
            }
        }
    }
    
    //AM/SPV jika DM nya NN
    if ((int)$pjabatanid==10 OR (int)$pjabatanid==18) {
        $query = "SELECT distinct karyawanid FROM dbmaster.t_karyawan_apv where status='DM' and karyawanid='$patasan2'";
        $ketemu= mysqli_num_rows(mysqli_query($cnmy, $query));
        if ($ketemu>0) {
            $query = "update $dbname.t_br_cab set tgl_atasan2=NOW() WHERE bridinputcab='$kodenya'";
            mysqli_query($cnmy, $query);
        }
        
        if (!empty($patasan3) AND empty($patasan2)) {
            $query = "update $dbname.t_br_cab set tgl_atasan2=NOW() WHERE bridinputcab='$kodenya'";
            mysqli_query($cnmy, $query);
        }
    }
    
    
    if ((int)$pjabatanid==5 OR $pjabatanid=="05") {
        if ($puserid==$pkaryawanid) {
            $query = "update $dbname.t_br_cab set atasan4='$puserid', tgl_atasan4=NOW(), gbr_atasan4='$pgambar' WHERE bridinputcab='$kodenya'";
            mysqli_query($cnmy, $query);
        }
    }
    
    //echo "$kodenya, $ptglpengajuan, $pcabangid, $pkaryawanid, $pelevel : $pjabatanid, $pmrid, $pdokterid, $pdivisi, $pcoa : $pkodeid, $paktivitas, $pccyid, $pjumlahusul, $patasan1, $patasan2, $patasan3, $patasan4 <br/>";
    
    
    
    
    //detail
    $ptujuandari=$_POST['e_tjdari'];
    $ptujuanke=$_POST['e_tjke'];
    
    $ptiket="";
    if (isset($_POST['chk_tiket'])) $ptiket=$_POST['chk_tiket'];
    $ptiketpulang="";
    if (isset($_POST['chk_pulang'])) $ptiketpulang=$_POST['chk_pulang'];
    $photel="";
    if (isset($_POST['chk_hotel'])) $photel=$_POST['chk_hotel'];
    $psewa="";
    if (isset($_POST['chk_sewa'])) $psewa=$_POST['chk_sewa'];
    
    
    
    mysqli_query($cnmy, "DELETE FROM $dbname.t_br_cab1 WHERE bridinputcab='$kodenya' AND noid='01'"); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    if (!empty($ptiket)) {
        $pjenistiket=$_POST['cb_jenistiket'];
        $ptglpergi=$_POST['e_tglpergi'];
        $pjampergi=$_POST['e_jampergi'];
        $pketpergi=$_POST['e_ketpergi'];
        $pharapergi=$_POST['e_rphargapergi'];
        
        $ptglpergi= date("Y-m-d", strtotime($ptglpergi));
        
        
        $query = "INSERT INTO $dbname.t_br_cab1 (bridinputcab, noid, jenistiket, kota1, kota2, tgl1, jam1, tgl2, jam2, notes)VALUES"
                . "('$kodenya', '01', '$pjenistiket', '$ptujuandari', '$ptujuanke', '$ptglpergi', '$pjampergi', '$ptglpergi', '$pjampergi', '$pketpergi')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        //echo "pergi : $ptglpergi, $pjampergi, $pketpergi, $pharapergi</br>";
        
        mysqli_query($cnmy, "DELETE FROM $dbname.t_br_cab1 WHERE bridinputcab='$kodenya' AND noid='02'"); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        if (!empty($ptiketpulang)) {
            $ptglpulang=$_POST['e_tglpulang'];
            $pjampulang=$_POST['e_jampulang'];
            $pketpulang=$_POST['e_ketpulang'];
            $pharapulang=$_POST['e_rphargapulang'];
            
            $ptglpulang= date("Y-m-d", strtotime($ptglpulang));
            
            
            $query = "INSERT INTO $dbname.t_br_cab1 (bridinputcab, noid, jenistiket, kota1, kota2, tgl1, jam1, tgl2, jam2, notes)VALUES"
                    . "('$kodenya', '02', '$pjenistiket', '$ptujuanke', '$ptujuandari', '$ptglpulang', '$pjampulang', '$ptglpulang', '$pjampulang', '$pketpulang')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            //echo "pulang : $ptglpulang, $pjampulang, $pketpulang, $pharapulang</br>";
        }
        
    }
    
    mysqli_query($cnmy, "DELETE FROM $dbname.t_br_cab1 WHERE bridinputcab='$kodenya' AND noid='03'"); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    if (!empty($photel)) {
        $pnginapdi=$_POST['e_nginapdi'];
        $ptglmulai=$_POST['e_tglmulai'];
        $ptglsampai=$_POST['e_tglsampai'];
        $pkethotel=$_POST['e_kethotel'];
        $pharahotel=$_POST['e_rphargahotel'];
        $pstsbayarhotel=$_POST['cb_stsbayarhotel'];
        
        $ptglmulai= date("Y-m-d", strtotime($ptglmulai));
        $ptglsampai= date("Y-m-d", strtotime($ptglsampai));
        
        
        $query = "INSERT INTO $dbname.t_br_cab1 (bridinputcab, noid, kota1, tgl1, tgl2, notes, stsbayar)VALUES"
                . "('$kodenya', '03', '$pnginapdi', '$ptglmulai', '$ptglsampai', '$pkethotel', '$pstsbayarhotel')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        //echo "hotel : $ptglmulai - $ptglsampai, $pkethotel, $pharahotel</br>";
        
    }
    
    
    mysqli_query($cnmy, "DELETE FROM $dbname.t_br_cab1 WHERE bridinputcab='$kodenya' AND noid='04'"); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    if (!empty($psewa)) {
        $pkotasewa=$_POST['e_kotasewa'];
        $ptglsewa1=$_POST['e_tglsewa1'];
        $pjamsewa1=$_POST['e_jamsewa1'];
        $ptglsewa2=$_POST['e_tglsewa2'];
        $pjamsewa2=$_POST['e_jamsewa2'];
        $pketsewa=$_POST['e_ketsewa'];
        $pharasewa1=$_POST['e_rphargasewa'];
        $pstsbayarsewa=$_POST['cb_stsbayarsewa'];
        
        $ptglsewa1= date("Y-m-d", strtotime($ptglsewa1));
        $ptglsewa2= date("Y-m-d", strtotime($ptglsewa2));
        
        
        $query = "INSERT INTO $dbname.t_br_cab1 (bridinputcab, noid, tgl1, jam1, tgl2, jam2, notes, stsbayar, kota1)VALUES"
                . "('$kodenya', '04', '$ptglsewa1', '$pjamsewa1', '$ptglsewa2', '$pjamsewa2', '$pketsewa', '$pstsbayarsewa', '$pkotasewa')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        //echo "sewa : $ptglsewa1, $pjamsewa1, $ptglsewa2, $pjamsewa2, $pketsewa, $pharasewa1</br>";
        
    }
    
    
    //echo "$ptujuandari- $ptujuanke, $ptiket, $ptiketpulang, $photel, $psewa";
    
    
    
    //end detail
    
    
    
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
    
    
    //echo "$pjnspajak, $pnmpengusaha, $pnoseri, $ptglfp, $prpdpp, $pppn, $prpppn, $pjnspph, $ppph, $prppph, $ppembulatan, $prpmaterai, $fieldjasa, $pchkjasa, $pchkatrika, $prpjmljasa, ";
    
    
    $query = "update $dbname.t_br_cab set pajak='$pjnspajak', nama_pengusaha='$pnmpengusaha', noseri='$pnoseri',"
            . " tgl_fp='$ptglfp', dpp='$prpdpp', ppn='$pppn', ppn_rp='$prpppn', "
            . " pph_jns='$pjnspph', pph='$ppph', pph_rp='$prppph', pembulatan='$ppembulatan', materai_rp='$prpmaterai' $fieldjasa WHERE bridinputcab='$kodenya' ";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    mysqli_close($cnmy);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act='.$act);
}

?>

