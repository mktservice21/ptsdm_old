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
if ($module=='entrybrrutin' AND $act=='hapus')
{
    $kethapus= $_GET['kethapus'];
    if ($kethapus=="null") $kethapus="";
    if (!empty($kethapus)) $kethapus =", Ket Hapus : ".$kethapus;
    if (!empty($kethapus)) $kethapus = str_replace("'", " ", $kethapus);
    
    mysqli_query($cnmy, "update $dbname.t_brrutin0 set stsnonaktif='Y', keterangan=CONCAT(keterangan,'$kethapus',', $_SESSION[NAMALENGKAP], ', NOW()) WHERE idrutin='$_GET[id]'");
    
    //hapus images
    mysqli_query($cnmy, "DELETE FROM $dbname2.img_brrutin1 WHERE idrutin='$_GET[id]'");
    
	mysqli_close($cnmy);
	
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='entrybrrutin' AND $act=='hapusgambar')
{
    $kodenya=$_GET['id'];
    if (!empty($idgam)) {
        mysqli_query($cnmy, "delete from $dbname2.img_brrutin1 WHERE nourut='$idgam' and idrutin='$kodenya'");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
		
		mysqli_close($cnmy);
    }
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=uploaddok&id='.$kodenya);
}
elseif ($module=='entrybrrutin' AND $act=='uploaddok')
{
    include "../../config/fungsi_image.php";
    $kodenya=$_POST['e_id'];
    $gambarnya=$_POST['e_imgconv'];
    if (!empty($gambarnya)) {
        mysqli_query($cnmy, "insert into $dbname2.img_brrutin1 (idrutin, gambar2) values ('$kodenya', '$gambarnya')");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
		mysqli_close($cnmy);
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

                mysqli_query($cnmy, "insert into $dbname2.img_brrutin1 (idrutin, gambar) values ('$kodenya', '$file')");
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
elseif ($module=='entrybrrutin')
{
    $idca = trim($_POST['e_idca']);
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
    if ($pdivprodid=="EP") $pdivprodid = "HO";
    if ($pdivprodid=="00000") $pdivprodid = "HO";
    
    if ((int)$pjabatanid==15) {
        $jmldiv = getfieldcnmy("select COUNT(DISTINCT divisiid) as lcfields from MKT.imr0 where aktif='Y' AND karyawanid='$pkaryawan'");
        if ((int)$jmldiv>1) {
            $pdivprodid="CAN";
        }
    }
    
    $pidcabang = "";
    $pareaid = "";
    
    if (isset($_POST['e_idarea']))
        $pareaid = trim($_POST['e_idarea']);
    
    if (!empty($pareaid)) {
        $areacabaang = explode(",",$pareaid);
        if (isset($areacabaang[0])) $pidcabang = trim($areacabaang[0]);
        if (isset($areacabaang[1])) $pareaid = trim($areacabaang[1]);
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
    
    
    //$date1 = "01-".str_replace('/', '-', $_POST['e_bulan']);
    $date1 =  date("d-m-Y", strtotime($_POST['e_bulan']));
    
    $date2 = str_replace('/', '-', $_POST['e_periode01']);
    $date3 = str_replace('/', '-', $_POST['e_periode02']);
    
    $pkadeperiode = $_POST['e_periode'];
    $pbulan =  date("Y-m-d", strtotime($date1));
    $pp01 =  date("Y-m-d", strtotime($date2));
    $pp02 =  date("Y-m-d", strtotime($date3));
    
    
    if (empty($pkadeperiode)) { echo "Kode Periode Kosong"; exit; }
    if ($_POST['cb_divisi']!="OTC") {
        //jika periodenya dan bulan beda, dicek ulang
        if (date("Y-m", strtotime($date1)) <> date("Y-m", strtotime($pp01)) AND !empty($pkadeperiode)) {
            $bulanbd = $date1;
            if ($pkadeperiode==1) {
                $bln1= date("Y-m-d", strtotime($bulanbd));
                $bln2= date("Y-m-15", strtotime($bulanbd));
            }elseif ($pkadeperiode==2) {
                $bln1= date("Y-m-16", strtotime($bulanbd));
                $bln2= date("Y-m-t", strtotime($bulanbd));
            }
            $pp01= $bln1;
            $pp02= $bln2;
        }
    }
    
    
    //cek double data
    if ($act=="input") {
        if ($_SESSION['KRYNONE']==$pkaryawan) {
        }else{
            
            $dbln =  date("Ym", strtotime($pbulan));
            $dnobrid="";
            $sql=  mysqli_query($cnmy, "select idrutin from $dbname.t_brrutin0 WHERE kode=1 AND stsnonaktif <> 'Y' AND karyawanid='$pkaryawan' AND "
                    . " kodeperiode='$pkadeperiode' AND DATE_FORMAT(bulan,'%Y%m')='$dbln'");
            $ketemu=  mysqli_num_rows($sql);
            if ($ketemu>0){
                $o=  mysqli_fetch_array($sql);
                $dnobrid=$o['idrutin'];
                echo "PERIODE TERSEBUT SUDAH PERNAH INPUT DATA DENGAN ID : $dnobrid";
                exit;
            }
            
        }
        
    }
    
    
    $pket = $_POST['e_ket'];
    if (!empty($pket)) $pket = str_replace("'", " ", $pket);
    
    $pnopol = $_POST['e_nopol'];
    

    
    $patasanid = $_POST['e_atasan'];
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
        $sql=  mysqli_query($cnmy, "select MAX(RIGHT(idrutin,7)) as NOURUT from $dbname.t_brrutin0");
        $ketemu=  mysqli_num_rows($sql);
        $awal=7; $urut=1; $kodenya=""; $periode=date('Ymd');
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            $urut=$o['NOURUT']+1;
            $jml=  strlen($urut);
            $awal=$awal-$jml;
            $kodenya="BRT".str_repeat("0", $awal).$urut;
        }else{
            $kodenya=$_POST['e_id'];
        }
    }else{
        $kodenya=$_POST['e_id'];
    }
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $num_records = $_POST['num_records'];
    
    if (empty(trim($kodenya))) { echo "kode kosong"; exit; }
    if (empty(trim($pkaryawan))) { echo "karyawan kosong"; exit; }
    if (empty(trim($pbulan))) { echo "bulan kosong"; exit; }
    if (empty(trim($pkadeperiode))) { echo "periode kosong"; exit; }
    
    if ($act=='input') {
        $query="insert into $dbname.t_brrutin0 (idrutin, karyawanid, icabangid, areaid, tgl, kode)values"
                . "('$kodenya', '$pkaryawan', '$pidcabang', '$pareaid', Current_Date(), 1)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        //simpan ke tabel images
        mysqli_query($cnmy, "DELETE FROM $dbname2.t_brrutin0_ttd WHERE idrutin='$kodenya'");
        $query = "INSERT INTO $dbname2.t_brrutin0_ttd (idrutin)values('$kodenya')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_query($cnmy, "DELETE FROM $dbname.t_brrutin0 WHERE idrutin='$kodenya'"); echo $erropesan; exit; }
        
    }
    
    $query = "update $dbname.t_brrutin0 set karyawanid='$pkaryawan', "
             . " icabangid='$pidcabang', "
             . " areaid='$pareaid', "
             . " bulan='$pbulan', "
             . " kodeperiode='$pkadeperiode', "
             . " periode1='$pp01', "
             . " periode2='$pp02', "
             . " nopol='$pnopol', "
             . " keterangan='$pket', "							 
             . " jabatanid='$pjabatanid', "
             . " divisi='$pdivprodid', "
             . " KODEWILAYAH='$pwilgabungan', "
             . " atasanid='$patasanid', "
             . " atasan1='$patasan1', "
             . " atasan2='$patasan2', "
             . " atasan3='$patasan3', "
             . " atasan4='$patasan4', "
             . " idca='$idca', "
             . " userid='$_SESSION[IDCARD]' where "
            . " idrutin='$kodenya'"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
    $query = "";
    if ($pelevel=="FF2") {
        $query = "update $dbname.t_brrutin0 set atasan1='$pkaryawan', tgl_atasan1=NOW() WHERE "
                . " idrutin='$kodenya'";
    }elseif ($pelevel=="FF3") {
        $query = "update $dbname.t_brrutin0 set atasan1='$pkaryawan', tgl_atasan1=NOW(), atasan2='$pkaryawan', tgl_atasan2=NOW() WHERE "
                . " idrutin='$kodenya'";
    }elseif ($pelevel=="FF4") {
        $query = "update $dbname.t_brrutin0 set atasan3='$pkaryawan', tgl_atasan3=NOW(), "
                . " atasan1='$pkaryawan', tgl_atasan1=NOW(), atasan2='$pkaryawan', tgl_atasan2=NOW() WHERE idrutin='$kodenya'";
    }else{
        $nolevel=0;
        if (trim(substr($pelevel, 0, 2)=="FF")) {
            if (!empty(substr($pelevel, 2, 2))) {
                $nolevel=(int)substr($pelevel, 2, 2);
                if ($nolevel>4) {
                    $query = "update $dbname.t_brrutin0 set atasan4='$pkaryawan', atasan3='$pkaryawan', tgl_atasan3=NOW(), "
                            . " atasan1='$pkaryawan', tgl_atasan1=NOW(), atasan2='$pkaryawan', tgl_atasan2=NOW() WHERE idrutin='$kodenya'";
                }
            }
        }
    }
    if (!empty($query)) {
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
    
    if (trim($pdivprodid)=="OTC") {
        $query = "update $dbname.t_brrutin0 set divi='OTC', icabangid_o='$pidcabang', areaid_o='$pareaid' where idrutin='$kodenya'";
        mysqli_query($cnmy, $query);
    }
    
    
    $ttotal=0;
    
    $query = "delete from $dbname.t_brrutin1 where idrutin='$kodenya'";
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
            $blqty = $_POST[$var_nmqty];
            $blnilai = $_POST[$var_nmnilai];
            $bltotal = $_POST[$var_nmtotal];
            $blnote = $_POST[$var_nmnote];
            if (!empty($blnote)) $blnote = str_replace("'", " ", $blnote);
            
            if ($blqty=="0") $blqty="";
            if ($blnilai=="0") $blnilai="";
            if ($bltotal=="0") $bltotal="";
            
            if ((int)$blnilai=="0" AND (int)$bltotal>0) $blnilai=$bltotal;
            
            
            $blqty=str_replace(",","", $blqty);
            $blnilai=str_replace(",","", $blnilai);
            $bltotal=str_replace(",","", $bltotal);
            
            
            
            $tglkuitansi="0000-00-00";
            $var_date1="e_1isitgl".$i;
            if (isset($_POST[$var_date1])) {
                $date1=$_POST[$var_date1];
                if (!empty($date1))
                    $tglkuitansi =  date("Y-m-d", strtotime($date1));
            }
            
            
            $tglkuitansi2="0000-00-00";
            $var_date2="e_1isitgl2".$i;
            if (isset($_POST[$var_date2])) {
                $date2=$_POST[$var_date2];
                if (!empty($date2))
                    $tglkuitansi2 =  date("Y-m-d", strtotime($date2));
            }
            
            
            
            if ((int)$blid==10 OR (int)$blid==11 OR (int)$blid==16 OR (int)$blid==17) {
                $tglkuitansi=$pp01;
            }
            
            $cbisikesehatan="";
            $ver_cbisi="cb_isi".$i;
            if (isset($_POST[$ver_cbisi])) {
                $cbisikesehatan=$_POST[$ver_cbisi];
            }
            
            $isikmdetail="";
            $ver_isikm="e_kmdetail".$i;
            if (isset($_POST[$ver_isikm])) {
                $isikmdetail=$_POST[$ver_isikm];
                if (empty($isikmdetail)) $isikmdetail=0;
                $isikmdetail=str_replace(",","", $isikmdetail);
            }
            
            //!empty($blqty) AND !empty($blnilai) AND 
            if (!empty($bltotal)) {
                
                $coadet = getfieldcnmy("select COA4 as lcfields from dbmaster.posting_coa_rutin where divisi='$pdivprodid' AND nobrid='$blid'");
                
                $query = "insert into $dbname.t_brrutin1 (idrutin,nobrid,qty,rp,rptotal,notes,coa,tgl1,obat_untuk,km,tgl2) "
                        . " values ('$kodenya','$blid','$blqty','$blnilai','$bltotal','$blnote', '$coadet','$tglkuitansi','$cbisikesehatan','$isikmdetail', '$tglkuitansi2')";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_query($cnmy, "UPDATE $dbname.t_brrutin0 set jumlah=0 WHERE idrutin='$kodenya'"); echo $erropesan; exit; }
                
                $ttotal=floatval($ttotal)+floatval($bltotal);
                
            }
            
            
            
        }
        
        
        $tampilkan=  mysqli_query($cnmy, "select sum(rptotal) rptotal from $dbname.t_brrutin1 WHERE idrutin='$kodenya'");
        $nketemu= mysqli_num_rows($tampilkan);
        $pintot=0;
        if ($nketemu>0) {
            $ntot= mysqli_fetch_array($tampilkan);
            $pintot=$ntot['rptotal'];
        }
        if ((double)$pintot>0) {
            $ttotal=$pintot;
        }
                
        //update jumlah minta
        $query = "update $dbname.t_brrutin0 set "
                 . " jumlah='$ttotal' where "
                . " idrutin='$kodenya'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
    }
    
    //update penambahan
    if (trim($pdivprodid)=="OTC") {
        $prppenambahan = str_replace(",","", $_POST['e_penambahan']);
        $coatambahan = $_POST['e_coatambah'];
        mysqli_query($cnmy, "DELETE FROM $dbname.t_brrutin2 WHERE idrutin='$kodenya'");
        if ((double)$prppenambahan>0) {
            $query = "insert into $dbname.t_brrutin2 (idrutin, coa_tambah, penambahan) "
                    . " values ('$kodenya','$coatambahan','$prppenambahan')";
            mysqli_query($cnmy, $query);
        }
    }
    
    //delete gambar
    if (!empty($_POST['del_img'])){
        mysqli_query($cnmy, "delete from $dbname2.img_brrutin1 where idrutin='$kodenya'");
    }
    // save gambar
        $lokasi_file    = $_FILES['image']['tmp_name'];
        $tipe_file      = $_FILES['image']['type'];
        $nama_file      = $_FILES['image']['name'];
        $acak           = rand(1,99);
        $nama_file_unik = $acak.$nama_file; 
        $nama_file_unik = strtolower(str_replace(" ","_",$nama_file_unik));
        
        if (!empty($lokasi_file)) {
            include "../../config/fungsi_image.php";
            $file = saveimagetemp($nama_file_unik, "800");

            if (!empty($file)){
                
                mysqli_query($cnmy, "delete from $dbname2.img_brrutin1 where idrutin='$kodenya'");
                
                mysqli_query($cnmy, "insert into $dbname2.img_brrutin1 (idrutin, gambar) values ('$kodenya', '$file')");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            }
        }
        
        //echo $file; exit;
        
        
    if ( (int)$pjabatanid==38 OR (int)$pjabatanid==19) {
        $query = "update $dbname.t_brrutin0 set atasan1='$patasan2', tgl_atasan1=NOW() where idrutin='$kodenya'";
        mysqli_query($cnmy, $query);
        
        
        $query = "SELECT distinct karyawanid, gsm FROM dbmaster.t_karyawan_app_gsm where karyawanid='$pkaryawan'";
        $ketemu= mysqli_num_rows(mysqli_query($cnmy, $query));
        if ($ketemu>0) {
            $ats= mysqli_fetch_array(mysqli_query($cnmy, $query));
            $atasangsm=$ats["gsm"];
            $query = "update $dbname.t_brrutin0 set atasan1='', tgl_atasan1=NOW(),"
                    . "atasan2='', tgl_atasan2=NOW(), atasan3='', tgl_atasan3=NOW(), atasan4='$atasangsm' WHERE idrutin='$kodenya'";
            mysqli_query($cnmy, $query);
        }
        
    }
        
    if ( (int)$pjabatanid==38) {
        if (empty($patasan2)) {
            $query = "update $dbname.t_brrutin0 set atasan1='$patasan3', atasan2='$patasan3', tgl_atasan2=NOW() WHERE idrutin='$kodenya'";
            mysqli_query($cnmy, $query);
        }
    }
	
    if ($act=="input") {
        //update gambar
        $img=$_POST['txtgambar'];
        $query = "update $dbname.t_brrutin0 set gambar='$img' WHERE idrutin='$kodenya'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
        $query = "update $dbname2.t_brrutin0_ttd set gambar='$img' WHERE idrutin='$kodenya'";
        mysqli_query($cnmy, $query);
        
    }
    

    //MR jika SPV/AM nya NN
    if ((int)$pjabatanid==15) {
        $query = "SELECT distinct karyawanid FROM dbmaster.t_karyawan_apv where status='SPV' and karyawanid='$patasan1'";
        $ketemu= mysqli_num_rows(mysqli_query($cnmy, $query));
        if ($ketemu>0) {
            $query = "update $dbname.t_brrutin0 set tgl_atasan1=NOW() WHERE idrutin='$kodenya'";
            mysqli_query($cnmy, $query);
        }
        
        
        if (!empty($patasan2) AND empty($patasan1)) {
            $query = "update $dbname.t_brrutin0 set tgl_atasan1=NOW() WHERE idrutin='$kodenya'";
            mysqli_query($cnmy, $query);
        }else{
        
            if (empty($patasan1) AND empty($patasan2) AND !empty($patasan3)) {
                $query = "update $dbname.t_brrutin0 set tgl_atasan1=NOW(), tgl_atasan2=NOW() WHERE idrutin='$kodenya'";
                mysqli_query($cnmy, $query);
            }
        }
        
        
    }
    
    
    //AM/SPV jika DM nya NN
    if ((int)$pjabatanid==10 OR (int)$pjabatanid==18) {
        $query = "SELECT distinct karyawanid FROM dbmaster.t_karyawan_apv where status='DM' and karyawanid='$patasan2'";
        $ketemu= mysqli_num_rows(mysqli_query($cnmy, $query));
        if ($ketemu>0) {
            $query = "update $dbname.t_brrutin0 set tgl_atasan2=NOW() WHERE idrutin='$kodenya'";
            mysqli_query($cnmy, $query);
        }
        
        
        if (!empty($patasan3) AND empty($patasan2)) {
            $query = "update $dbname.t_brrutin0 set tgl_atasan2=NOW() WHERE idrutin='$kodenya'";
            mysqli_query($cnmy, $query);
        }
        
        
    }
    
    
    
    if ($_SESSION['GROUP']=="28") {
        if ($pdivprodid=="OTC") $pdivprodid="HO";
        $query = "update $dbname.t_brrutin0 set divisi='$pdivprodid', tgl_atasan1=NOW(), tgl_atasan2=NOW(), tgl_atasan3=NOW(), tgl_atasan4=NOW() WHERE idrutin='$kodenya'";
        mysqli_query($cnmy, $query);
        
        
        if ((int)$pjabatanid==5 OR $pjabatanid=="05") {
            $query = "update $dbname.t_brrutin0 set tgl_atasan4=NOW() WHERE idrutin='$kodenya'";
            mysqli_query($cnmy, $query);
        }
        
    }
    
    
    $pkrynone = "";
    if (isset($_POST['e_stskaryawan'])) $pkrynone = $_POST['e_stskaryawan'];
    if (!empty($pkrynone)) {
        if ($pkrynone=="NONE") {
            
            $pnamakrynone = $_POST['e_namakrynone'];
            $pidcabang = $_POST['e_idcabang'];
            $pareaid = $_POST['e_idarea'];
            $pdivprodid = $_POST['cb_divisi'];
            
            if (!empty($pareaid)) {
                $areacabaang = explode(",",$pareaid);
                if (isset($areacabaang[1])) $pareaid = trim($areacabaang[1]);
            }
            
            $query = "update $dbname.t_brrutin0 set nama_karyawan='$pnamakrynone', divisi='$pdivprodid', "
                    . " icabangid='$pidcabang', areaid='$pareaid' WHERE idrutin='$kodenya'";
            mysqli_query($cnmy, $query);
        }
        
    }else{
        $pnmkaryawan = getfieldcnmy("select nama as lcfields from hrd.karyawan where karyawanId='$pkaryawan'");
        $query = "update $dbname.t_brrutin0 set nama_karyawan='$pnamakrynone' WHERE idrutin='$kodenya'";
        mysqli_query($cnmy, $query);
    }
    
	mysqli_close($cnmy);
    
    if ($act=="input")
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act='.$kembali);
    else
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
    
}
    
?>
