<?php
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    $cnmy=$cnmy;
    $dbname = "dbmaster";
    $dbname2 = "dbimages";

    $idgam="";
    if (isset($_GET['idgam']))
        $idgam=$_GET['idgam'];
    
    
    $kembali = "tambahbaru";
    //if ($_SESSION['LVLPOSISI'] =="FF1" OR $_SESSION['LVLPOSISI'] =="FF2" OR $_SESSION['LVLPOSISI'] =="FF3" OR $_SESSION['LVLPOSISI'] =="FF4" OR $_SESSION['LVLPOSISI'] =="FF5" OR $_SESSION['LVLPOSISI'] =="FF6" ){
    if ($_SESSION['DIVISI'] != "OTC") {
        $kembali = "complete";
    }
    

//HAPUS DATA
if ($module=='entrybrcash' AND $act=='hapus')
{
    $kethapus= $_GET['kethapus'];
    if ($kethapus=="null") $kethapus="";
    if (!empty($kethapus)) $kethapus =", Ket Hapus : ".$kethapus;
    if (!empty($kethapus)) $kethapus = str_replace("'", " ", $kethapus);
    
    mysqli_query($cnmy, "update $dbname.t_ca0 set stsnonaktif='Y', keterangan=CONCAT(keterangan,'$kethapus',', $_SESSION[NAMALENGKAP], ', NOW()) WHERE idca='$_GET[id]'");
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}

elseif ($module=='entrybrcash' AND $act=='hapusgambar')
{
    $kodenya=$_GET['id'];
    if (!empty($idgam)) {
        mysqli_query($cnmy, "delete from $dbname2.img_ca0 WHERE nourut='$idgam' and idca='$kodenya'");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=uploaddok&id='.$kodenya);
}

elseif ($module=='entrybrcash' AND $act=='uploaddok')
{
    include "../../config/fungsi_image.php";
    $kodenya=$_POST['e_id'];
    $gambarnya=$_POST['e_imgconv'];
    if (!empty($gambarnya)) {
        mysqli_query($cnmy, "insert into $dbname2.img_ca0 (idca, gambar2) values ('$kodenya', '$gambarnya')");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
    /*
    // save gambar
    $maximg=1;
    for ($i=1;$i<=$maximg;$i++) {
        $nmimg="image".$i;
        $lokasi_file    = $_FILES[$nmimg]['tmp_name'];
        $tipe_file      = $_FILES[$nmimg]['type'];
        $nama_file      = $_FILES[$nmimg]['name'];
        $acak           = rand(1,99);
        $nama_file_unik = $acak.$nama_file; 
        $nama_file_unik = strtolower(str_replace(" ","_",$nama_file_unik));

        
        if (!empty($lokasi_file)) {
            
            $file = saveimagetemp($nmimg, $nama_file_unik, "800");

            if (!empty($file)){

                mysqli_query($cnmy, "insert into $dbname2.img_ca0 (idca, gambar) values ('$kodenya', '$file')");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            }
        }
        
        $lokasi_file="";
        $nama_file_unik="";
    }
     * 
     */
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=uploaddok&id='.$kodenya);
}

elseif ($module=='entrybrcash')
{
    $pkaryawan = $_POST['e_idkaryawan'];
    

    //AREA dan DIVISI SESUAI JABATAN
    
    $pjabatanid=$_POST['e_jabatan'];
    if (empty($pjabatanid))
        $pjabatanid = getfieldcnmy("select jabatanId as lcfields from dbmaster.t_karyawan_posisi where karyawanId='$pkaryawan'");
    if (empty($pjabatanid))
        $pjabatanid = getfieldcnmy("select jabatanId as lcfields from hrd.karyawan where karyawanId='$pkaryawan'");
    
    $pdivprodid="";
    if (isset($_POST['cb_divisi']))
        $pdivprodid = trim($_POST['cb_divisi']);
    
    if (empty($pdivprodid)) $pdivprodid = getfieldcnmy("select divisiId as lcfields from hrd.karyawan where karyawanId='$pkaryawan'");
    if (empty($pdivprodid)) $pdivprodid = getfieldcnmy ("select divisiId2 as lcfields from hrd.karyawan where karyawanId='$pkaryawan'");
    if (empty($pdivprodid)) $pdivprodid = "HO";
    
    $pidcabang = "";
    $pareaid = "";
    
    if (isset($_POST['e_idarea']))
        $pareaid = trim($_POST['e_idarea']);
    
    if (!empty($pareaid)) {
        $areacabaang = explode(",",$pareaid);
        $pidcabang = trim($areacabaang[0]);
        $pareaid = trim($areacabaang[1]);
    }
    $jmldiv=1;
    if (empty($pareaid) AND $pdivprodid!="OTC") {
        if ((int)$pjabatanid==10 OR (int)$pjabatanid==18) {
            $sql="select DISTINCT icabangid, areaid from MKT.ispv0 WHERE aktif='Y' AND karyawanid='$pkaryawan' LIMIT 1";
            $tampil=mysqli_query($cnmy, $sql);
            $a = mysqli_fetch_array($tampil);
            $pidcabang = $a['icabangid'];
            $pareaid = $a['areaid'];
            $pdivprodid="CAN";
        }elseif ((int)$pjabatanid==20) {
            $pidcabang = getfieldcnmy("select icabangid as lcfields from MKT.ism0 where aktif='Y' AND karyawanid='$pkaryawan' limit 1");
            $pareaid = getfieldcnmy("select areaId as lcfields from MKT.iarea where aktif='Y' AND iCabangId='$pidcabang'");
            $pdivprodid="CAN";
        }elseif ((int)$pjabatanid==8) {
            $pidcabang = getfieldcnmy("select icabangid as lcfields from MKT.idm0 where aktif='Y' AND karyawanid='$pkaryawan' limit 1");
            $pareaid = getfieldcnmy("select areaId as lcfields from MKT.iarea where aktif='Y' AND iCabangId='$pidcabang'");
            $pdivprodid="CAN";
        }elseif ((int)$pjabatanid==15) {
            $sql="select DISTINCT icabangid, areaid from MKT.imr0 WHERE aktif='Y' AND karyawanid='$pkaryawan' LIMIT 1";
            $tampil=mysqli_query($cnmy, $sql);
            $a = mysqli_fetch_array($tampil);
            $pidcabang = $a['icabangid'];
            $pareaid = $a['areaid'];
            $jmldiv = getfieldcnmy("select COUNT(DISTINCT divisiid) as lcfields from MKT.imr0 where aktif='Y' AND karyawanid='$pkaryawan'");
            if ((int)$jmldiv>1) {
                $pdivprodid="CAN";
            }
        }else{
            $pidcabang = getfieldcnmy("select iCabangId as lcfields from dbmaster.t_karyawan_posisi where karyawanId='$pkaryawan'");
            $pareaid = getfieldcnmy("select areaId as lcfields from dbmaster.t_karyawan_posisi where karyawanId='$pkaryawan'");
        }
    }else{
        if ($pdivprodid=="OTC") {
            $pidcabang = getfieldcnmy("select iCabangId as lcfields from dbmaster.t_karyawan_posisi where karyawanId='$pkaryawan'");
            $pareaid = getfieldcnmy("select areaId as lcfields from dbmaster.t_karyawan_posisi where karyawanId='$pkaryawan'");
        }
    }
    if (empty(trim($pidcabang))) { $pidcabang="0000000001"; $pareaid="0000000001";}
    
    
    //echo "$pdivprodid, $pidcabang, $pareaid, $pjabatanid";exit;
    
    //END AREA dan DIVISI SESUAI JABATAN
    
    $jenisca = $_POST['e_jenisca'];
    
    $date1 = str_replace('/', '-', $_POST['e_tgl']);
    $pp01 =  $_POST['e_tgl'];
    $pp01 =  date("Y-m-01", strtotime($date1));
    
    //cek double data
    if ($act=="input") {
        $pperi01 =  date("Y-m", strtotime($date1));
        $dnobrid="";
        $sql=  mysqli_query($cnmy, "select idca from $dbname.t_ca0 WHERE stsnonaktif <> 'Y' AND karyawanid='$pkaryawan' AND "
                . " DATE_FORMAT(periode,'%Y-%m')='$pperi01'");
        $ketemu=  mysqli_num_rows($sql);
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            $dnobrid=$o['idca'];
            echo "PERIODE TERSEBUT SUDAH PERNAH INPUT DATA DENGAN ID : $dnobrid";
            exit;
        }
    }
    
    
    $pket = $_POST['e_ket'];
    if (!empty($pket)) $pket = str_replace("'", " ", $pket);
        
    
    
    $patasan1 = $_POST['e_atasan'];
    $patasan2 = $_POST['e_atasan2'];
    $patasan3 = $_POST['e_atasan3'];
    $patasan4 = $_POST['e_atasan4'];
    $pelevel = trim($_POST['e_lvl']);
    
    $pwilayah="01";
    $pcabwil=  substr($pidcabang, 7,3);
    if ($pidcabang=="0000000001")
        $pwilayah="01";
    else{
        $reg=  getfieldcnmy("select distinct region as lcfields from dbmaster.icabang where iCabangId='$pidcabang'");
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
        $sql=  mysqli_query($cnmy, "select MAX(RIGHT(idca,8)) as NOURUT from $dbname.t_ca0");
        $ketemu=  mysqli_num_rows($sql);
        $awal=8; $urut=1; $kodenya=""; $periode=date('Ymd');
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            $urut=$o['NOURUT']+1;
            $jml=  strlen($urut);
            $awal=$awal-$jml;
            $kodenya="CA".str_repeat("0", $awal).$urut;
        }else{
            $kodenya=$_POST['e_id'];
        }
    }else{
        $kodenya=$_POST['e_id'];
    }
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $num_records = $_POST['num_records'];
    
    if (empty(trim($kodenya))) { echo "kode kosong"; exit; }
    //if (empty(trim($pdivprodid))) { echo "divisi kosong"; exit; }
    //if (empty(trim($pareaid))) { echo "area kosong"; exit; }
    if (empty(trim($pp01))) { echo "periode kosong"; exit; }
    
    
    if ($act=='input') {
        $query="insert into $dbname.t_ca0 (idca, karyawanid, icabangid, areaid, tgl, kode)values"
                . "('$kodenya', '$pkaryawan', '$pidcabang', '$pareaid', Current_Date(), 3)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        //simpan ke tabel images
        mysqli_query($cnmy, "DELETE FROM $dbname2.t_ca0_ttd WHERE idca='$kodenya'");
        $query = "INSERT INTO $dbname2.t_ca0_ttd (idca)values('$kodenya')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_query($cnmy, "DELETE FROM $dbname.t_ca0 WHERE idca='$kodenya'"); echo $erropesan; exit; }
        
    }
    
    $query = "update $dbname.t_ca0 set karyawanid='$pkaryawan', "
             . " icabangid='$pidcabang', "
             . " areaid='$pareaid', "
             . " periode='$pp01', "
             . " keterangan='$pket', "							 
             . " jabatanid='$pjabatanid', "
             . " divisi='$pdivprodid', "
             . " KODEWILAYAH='$pwilgabungan', "
             . " atasan1='$patasan1', "
             . " atasan2='$patasan2', "
             . " atasan3='$patasan3', "
             . " atasan4='$patasan4', "
             . " jenis_ca='$jenisca', "
             . " userid='$_SESSION[IDCARD]' where "
            . " idca='$kodenya'"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_query($cnmy, "delete from $dbname.t_ca0 where idca='$kodenya'"); echo $erropesan; exit; }
    
    
    
    $query = "";
    if ($pelevel=="FF2") {
        $query = "update $dbname.t_ca0 set atasan1='$pkaryawan', tgl_atasan1=NOW() WHERE "
                . " idca='$kodenya'";
    }elseif ($pelevel=="FF3") {
        $query = "update $dbname.t_ca0 set atasan1='$pkaryawan', tgl_atasan1=NOW(), atasan2='$pkaryawan', tgl_atasan2=NOW() WHERE "
                . " idca='$kodenya'";
    }elseif ($pelevel=="FF4") {
        $query = "update $dbname.t_ca0 set atasan3='$pkaryawan', tgl_atasan3=NOW(), "
                . " atasan1='$pkaryawan', tgl_atasan1=NOW(), atasan2='$pkaryawan', tgl_atasan2=NOW() WHERE idca='$kodenya'";
    }else{
        
        $nolevel=0;
        if (trim(substr($pelevel, 0, 2)=="FF")) {
            if (!empty(substr($pelevel, 2, 2))) {
                $nolevel=(int)substr($pelevel, 2, 2);
                if ((int)$nolevel>4) {
                    $query = "update $dbname.t_ca0 set atasan4='$pkaryawan', atasan3='$pkaryawan', tgl_atasan3=NOW(), "
                            . " atasan1='$pkaryawan', tgl_atasan1=NOW(), atasan2='$pkaryawan', tgl_atasan2=NOW() WHERE idca='$kodenya'";
                }
            }
        }
    }
    if (!empty($query)) {
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
    
    if (trim($pdivprodid)=="OTC") {
        $query = "update $dbname.t_ca0 set divi='OTC', icabangid_o='$pidcabang', areaid_o='$pareaid' where idca='$kodenya'";
        mysqli_query($cnmy, $query);
    }
    
    
    
    $ttotal=0;
    
    $query = "delete from $dbname.t_ca1 where idca='$kodenya'";
    mysqli_query($cnmy, $query);
    
    if ($num_records) {
        for ($i=1; $i < $num_records; $i++) {
            
            $var_nmidbl="e_idbl".$i;
            $var_nmnama="e_blnama".$i;
            $var_nmqty="e_qty".$i;
            $var_nmnilai="e_nilai".$i;
            $var_nmtotal="e_total".$i;
            $var_nmnote="e_note".$i;
            
            
            $blid = $_POST[$var_nmidbl];
            $blnama = $_POST[$var_nmnama];
            $bltgl1 = $_POST[$var_nmqty];
            $bltgl2 = $_POST[$var_nmnilai];
            $bltotal = $_POST[$var_nmtotal];
            $blnote = $_POST[$var_nmnote];
            if (!empty($blnote)) $blnote = str_replace("'", " ", $blnote);
            
            $blmytgl1= "0000-00-00";
            if (!empty($bltgl1)) {
                $bltgl1x = $bltgl1;//str_replace('/', '-', $bltgl1);
                $blmytgl1 =  date("Y-m-d", strtotime($bltgl1x));
            }
            
            $blmytgl2= "0000-00-00";
            if (!empty($bltgl2)) {
                $bltgl2x = $bltgl2;//str_replace('/', '-', $bltgl2);
                $blmytgl2 =  date("Y-m-d", strtotime($bltgl2x));
            }
                
    
            if ($bltotal=="0") $bltotal="";
            
            $bltotal=str_replace(",","", $bltotal);
            
            
            
            if (!empty($bltotal)) {
                
                $coadet = getfieldcnmy("select COA4 as lcfields from dbmaster.posting_coa_rutin where divisi='$pdivprodid' AND nobrid='$blid'");
                
                $query = "insert into $dbname.t_ca1 (idca,nobrid,tgl1,tgl2,rptotal,notes,coa) "
                        . " values ('$kodenya','$blid','$blmytgl1','$blmytgl2','$bltotal','$blnote', '$coadet')";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_query($cnmy, "UPDATE $dbname.t_ca0 SET jumlah=0 WHERE idca='$kodenya'"); echo $erropesan; exit; }
                
                $ttotal=floatval($ttotal)+floatval($bltotal);
                
            }
            
            
        }
        
        //update jumlah minta
        $query = "update $dbname.t_ca0 set "
                 . " jumlah='$ttotal' where "
                . " idca='$kodenya'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
    }
    

    
    if ($act=="input") {
        //update gambar
        $img=$_POST['txtgambar'];
        $query = "update $dbname.t_ca0 SET gambar='$img' WHERE idca='$kodenya'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query = "update $dbname2.t_ca0_ttd SET gambar='$img' WHERE idca='$kodenya'";
        mysqli_query($cnmy, $query);
        
    }
    

    //MR jika SPV/AM nya NN
    if ((int)$pjabatanid==15) {
        $query = "SELECT distinct karyawanid FROM dbmaster.t_karyawan_apv where status='SPV' and karyawanid='$patasan1'";
        $ketemu= mysqli_num_rows(mysqli_query($cnmy, $query));
        if ($ketemu>0) {
            $query = "update $dbname.t_ca0 set tgl_atasan1=NOW() WHERE idca='$kodenya'";
            mysqli_query($cnmy, $query);
        }
    }
    
    //AM/SPV jika DM nya NN
    if ((int)$pjabatanid==10 OR (int)$pjabatanid==18) {
        $query = "SELECT distinct karyawanid FROM dbmaster.t_karyawan_apv where status='DM' and karyawanid='$patasan2'";
        $ketemu= mysqli_num_rows(mysqli_query($cnmy, $query));
        if ($ketemu>0) {
            $query = "update $dbname.t_ca0 set tgl_atasan2=NOW() WHERE idca='$kodenya'";
            mysqli_query($cnmy, $query);
        }
    }
    
    if ($_SESSION['GROUP']=="28") {
        $query = "update $dbname.t_ca0 set tgl_atasan1=NOW(), tgl_atasan2=NOW(), tgl_atasan3=NOW() WHERE idca='$kodenya'";
        mysqli_query($cnmy, $query);
    }
    
    
    $query = "update $dbname.t_ca0 set bulan='$pp01' where idca='$kodenya'"; 
    mysqli_query($cnmy, $query);
    
    if ($act=="input")
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act='.$kembali);
    else
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
    
}
    
?>
