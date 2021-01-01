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
    
//HAPUS DATA
if ($module=='entrybrcashotc' AND $act=='hapus')
{
    $kethapus= $_GET['kethapus'];
    if ($kethapus=="null") $kethapus="";
    if (!empty($kethapus)) $kethapus =", Ket Hapus : ".$kethapus;
    if (!empty($kethapus)) $kethapus = str_replace("'", " ", $kethapus);
    
    mysqli_query($cnmy, "update $dbname.t_ca0 set stsnonaktif='Y', keterangan=CONCAT(keterangan,'$kethapus',', $_SESSION[NAMALENGKAP], ', NOW()) WHERE idca='$_GET[id]'");
    
    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}

elseif ($module=='entrybrcashotc' AND $act=='hapusgambar')
{
    $kodenya=$_GET['id'];
    if (!empty($idgam)) {
        mysqli_query($cnmy, "delete from $dbname2.img_ca0 WHERE nourut='$idgam' and idca='$kodenya'");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        mysqli_close($cnmy);
    }
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=uploaddok&id='.$kodenya);
}

elseif ($module=='entrybrcashotc' AND $act=='uploaddok')
{
    include "../../config/fungsi_image.php";
    $kodenya=$_POST['e_id'];
    $gambarnya=$_POST['e_imgconv'];
    if (!empty($gambarnya)) {
        mysqli_query($cnmy, "insert into $dbname2.img_ca0 (idca, gambar2) values ('$kodenya', '$gambarnya')");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        mysqli_close($cnmy);
    }
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=uploaddok&id='.$kodenya);
}
elseif ($module=='entrybrcashotc')
{
    
    $pkaryawan = $_POST['e_idkaryawan'];
    
    $hrdjabatan="";
    $hrdcabang="";
    $hrdarea="";
    $hrdatasan="";
    $pnmkaryawan="";

    $query = "select jabatanId, iCabangId, areaId, atasanId, nama nama_karyawan from hrd.karyawan where karyawanId='$pkaryawan'";
    $tampil = mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $k=mysqli_fetch_array($tampil);
        $hrdjabatan=$k['jabatanId'];
        $hrdcabang=$k['iCabangId'];
        $hrdarea=$k['areaId'];
        $hrdatasan=$k['atasanId'];
        $pnmkaryawan=$k['nama_karyawan'];
    }

    
    
    $mstjabatan="";
    $mstcabang="";
    $mstarea="";
    $mstatasan="";
    
    $patasan1="";
    $patasan2="";
    $patasan3="";
    $patasan4="";

    $query = "select jabatanId, iCabangId, areaId, atasanId, spv, dm, sm, gsm from dbmaster.t_karyawan_posisi where karyawanId='$pkaryawan'";
    $tampil = mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $m=mysqli_fetch_array($tampil);
        $mstjabatan=$m['jabatanId'];
        $mstcabang=$m['iCabangId'];
        $mstarea=$m['areaId'];
        $mstatasan=$m['atasanId'];
        
        $patasan1=$m['spv'];
        $patasan2=$m['dm'];
        $patasan3=$m['sm'];
        $patasan4=$m['gsm'];
    }
    
    $pjabatanid=$hrdjabatan;
    if (empty($pjabatanid)) $pjabatanid=$mstjabatan;
    
    $pidcabang=$mstcabang;
    if (empty($pidcabang)) $pidcabang=$hrdcabang;
    
    $pareaid=$mstarea;
    if (empty($pareaid)) $pareaid=$hrdarea;
    
    $patasanid=$mstatasan;
    if (empty($patasanid)) $patasanid=$hrdatasan;
    
    
    
    $pdivprodid="OTC";
    
    if (empty(trim($pidcabang))) { $pidcabang="0000000007"; $pareaid="0000000001";}
    
    $date1 =  date("d-m-Y", strtotime($_POST['e_bulan']));
    $date2 = str_replace('/', '-', $_POST['e_periode01']);
    $date3 = str_replace('/', '-', $_POST['e_periode02']);
    
    $pkadeperiode = $_POST['e_periode'];
    $pbulan =  date("Y-m-01", strtotime($date1));
    $pp01 =  date("Y-m-d", strtotime($date2));
    $pp02 =  date("Y-m-d", strtotime($date3));
    
    $pket = $_POST['e_ket'];
    if (!empty($pket)) $pket = str_replace("'", " ", $pket);
    
    if (empty($pkadeperiode)) { echo "Kode Periode Kosong"; exit; }
    
    //cek double data
    if ($act=="input") {
        if ($_SESSION['KRYNONE']==$pkaryawan) {
        }else{
            
            $dbln =  date("Ym", strtotime($pbulan));
            $dnobrid="";
            $sql=  mysqli_query($cnmy, "select idca from $dbname.t_ca0 WHERE stsnonaktif <> 'Y' AND karyawanid='$pkaryawan' AND "
                    . " DATE_FORMAT(periode,'%Y%m')='$dbln'");
            $ketemu=  mysqli_num_rows($sql);
            if ($ketemu>0){
                $o=  mysqli_fetch_array($sql);
                $dnobrid=$o['idca'];
                echo "PERIODE TERSEBUT SUDAH PERNAH INPUT DATA DENGAN ID : $dnobrid";
                exit;
            }
            
        }
        
    }
    
    $pnopol="";
    $query = "select * from dbmaster.t_kendaraan WHERE 
        nopol in (select distinct nopol from dbmaster.t_kendaraan_pemakai where karyawanid='$pkaryawan' and stsnonaktif <> 'Y')";
    $tampil = mysqli_query($cnmy, $query);
    $a=mysqli_fetch_array($tampil);
    if (!empty($a['nopol'])) {
        $pnopol=$a['nopol'];
    }
    
    
    $pwilayah="01";
    $pcabwil=  substr($pidcabang, 7,3);
    if ($pidcabang=="0000000001")
        $pwilayah="01";
    else{
        $reg=  getfieldcnmy("select distinct region as lcfields from MKT.icabang_o where icabangid_o='$pidcabang'");
        if ($reg=="B")
            $pwilayah="04";
        else
            $pwilayah="05";
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
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $num_records = $_POST['num_records'];
    
    $piduser = $_SESSION['IDCARD'];
    
    $jenisca = $_POST['e_jenisca'];
    
    if (empty(trim($kodenya))) { echo "kode kosong"; exit; }
    if (empty(trim($pkaryawan))) { echo "karyawan kosong"; exit; }
    if (empty(trim($pbulan))) { echo "bulan kosong"; exit; }
    if (empty(trim($pkadeperiode))) { echo "periode kosong"; exit; }
    
    
    
    //echo "$pnmkaryawan, div : $pdivprodid, jab : $pjabatanid, cab : $pidcabang, $pareaid, $pkadeperiode, Bln : $pbulan, $pp01, $pp02, KET : $pket, nopol : $pnopol, wil : $pwilgabungan, idca : $kodenya, jlm Rec : $num_records, atasan : $patasanid, 1 : $patasan1, 2 : $patasan2, 3 : $patasan3, 4 : $patasan4, jenis ca : $jenisca"; exit;
    
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
             . " periode='$pbulan', "
             . " bulan='$pbulan', "
             . " keterangan='$pket', "							 
             . " jabatanid='$pjabatanid', "
             . " divisi='$pdivprodid', "
             . " KODEWILAYAH='$pwilgabungan', "
             . " divi='OTC', "
             . " icabangid_o='$pidcabang', "
             . " areaid_o='$pareaid', "
             . " jenis_ca='$jenisca', "
             . " atasan1='$patasan1', "
             . " atasan2='$patasan2', "
             . " atasan3='$patasan3', "
             . " atasan4='$patasan4', "
             . " userid='$_SESSION[IDCARD]' WHERE "
            . " idca='$kodenya'"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_query($cnmy, "delete from $dbname.t_ca0 where idca='$kodenya'"); echo $erropesan; exit; }
    
    
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
        
        $tampilkan=  mysqli_query($cnmy, "select sum(rptotal) rptotal from $dbname.t_ca1 WHERE idca='$kodenya'");
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
    
    
    if ($pjabatanid=="18" OR $pjabatanid=="35") {
        $query = "update $dbname.t_ca0 set atasan1='$pkaryawan', tgl_atasan1=NOW(), atasan2='$patasan2' WHERE idca='$kodenya'";
        mysqli_query($cnmy, $query);
        if (empty($patasan2) AND empty($patasan3)) {
            $query = "update $dbname.t_ca0 set atasan2='$pkaryawan', tgl_atasan2=NOW(), atasan3='$pkaryawan', tgl_atasan3=NOW() WHERE idca='$kodenya'";
            mysqli_query($cnmy, $query);
        }elseif (empty($patasan2)) {
            $query = "update $dbname.t_ca0 set atasan2='$pkaryawan', tgl_atasan2=NOW() WHERE idca='$kodenya'";
            mysqli_query($cnmy, $query);
        }
    }
    
    if ($pjabatanid=="23") {
        if (empty($patasan1) AND empty($patasan2) AND empty($patasan3)) {
            $query = "update $dbname.t_ca0 set atasan1='$pkaryawan', tgl_atasan1=NOW(), atasan2='$pkaryawan', tgl_atasan2=NOW(), atasan3='$pkaryawan', tgl_atasan3=NOW() WHERE idca='$kodenya'";
            mysqli_query($cnmy, $query);
        }elseif (empty($patasan1) AND empty($patasan2)) {
            $query = "update $dbname.t_ca0 set atasan1='$pkaryawan', tgl_atasan1=NOW(), atasan2='$pkaryawan', tgl_atasan2=NOW() WHERE idca='$kodenya'";
            mysqli_query($cnmy, $query);
        }elseif (empty($patasan1)) {
            $query = "update $dbname.t_ca0 set atasan1='$pkaryawan', tgl_atasan1=NOW() WHERE idca='$kodenya'";
            mysqli_query($cnmy, $query);
        }
    }
    
    if ($pjabatanid=="08" OR $pjabatanid=="10" OR $pjabatanid=="36" OR $pjabatanid=="06") {
        $query = "update $dbname.t_ca0 set atasan1='$pkaryawan', tgl_atasan1=NOW(), atasan2='$pkaryawan', tgl_atasan2=NOW(), atasan3='$pkaryawan', tgl_atasan3=NOW() WHERE idca='$kodenya'";
        mysqli_query($cnmy, $query);
    }
    
    if ($pjabatanid=="36") {//HOS
        $query = "update $dbname.t_ca0 set atasan4='$pkaryawan', tgl_atasan4=NOW() WHERE idca='$kodenya'";
        mysqli_query($cnmy, $query);
    }
    
    
    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
    
}

    
?>
