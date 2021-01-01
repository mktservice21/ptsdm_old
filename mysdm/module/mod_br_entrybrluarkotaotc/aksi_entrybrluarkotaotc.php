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
if ($module=='entrybrluarkotaotc' AND $act=='hapus')
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
elseif ($module=='entrybrluarkotaotc' AND $act=='hapusgambar')
{
    
    $kodenya=$_GET['id'];
    if (!empty($idgam)) {
        mysqli_query($cnmy, "delete from $dbname2.img_brrutin1 WHERE nourut='$idgam' and idrutin='$kodenya'");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        mysqli_close($cnmy);
    }
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=uploaddok&id='.$kodenya);
    
}
elseif ($module=='entrybrluarkotaotc' AND $act=='uploaddok')
{
    
    include "../../config/fungsi_image.php";
    $kodenya=$_POST['e_id'];
    $gambarnya=$_POST['e_imgconv'];
    if (!empty($gambarnya)) {
        mysqli_query($cnmy, "insert into $dbname2.img_brrutin1 (idrutin, gambar2) values ('$kodenya', '$gambarnya')");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        mysqli_close($cnmy);
    }
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=uploaddok&id='.$kodenya);
    
}
elseif ($module=='entrybrluarkotaotc')
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
            $sql=  mysqli_query($cnmy, "select idrutin from $dbname.t_brrutin0 WHERE kode=2 AND stsnonaktif <> 'Y' AND karyawanid='$pkaryawan' AND "
                    . " ( (periode1 BETWEEN '$pp01' AND '$pp02') OR (periode2 BETWEEN '$pp01' AND '$pp02') ) ");
            $ketemu=  mysqli_num_rows($sql);
            if ($ketemu>0){
                $o=  mysqli_fetch_array($sql);
                $dnobrid=$o['idrutin'];
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
        $sql=  mysqli_query($cnmy, "select MAX(RIGHT(idrutin,7)) as NOURUT from $dbname.t_brrutin0");
        $ketemu=  mysqli_num_rows($sql);
        $awal=7; $urut=1; $kodenya=""; $periode=date('Ymd');
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            $urut=$o['NOURUT']+1;
            $jml=  strlen($urut);
            $awal=$awal-$jml;
            $kodenya="BLK".str_repeat("0", $awal).$urut;
        }else{
            $kodenya=$_POST['e_id'];
        }
    }else{
        $kodenya=$_POST['e_id'];
    }
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $num_records = $_POST['num_records'];
    
    $piduser = $_SESSION['IDCARD'];
    
    
    if (empty(trim($kodenya))) { echo "kode kosong"; exit; }
    if (empty(trim($pkaryawan))) { echo "karyawan kosong"; exit; }
    if (empty(trim($pbulan))) { echo "bulan kosong"; exit; }
    if (empty(trim($pkadeperiode))) { echo "periode kosong"; exit; }
    
    
    //echo "$pnmkaryawan, div : $pdivprodid, jab : $pjabatanid, cab : $pidcabang, $pareaid, $pkadeperiode, Bln : $pbulan, $pp01, $pp02, KET : $pket, nopol : $pnopol, wil : $pwilgabungan, idrutin : $kodenya, jlm Rec : $num_records, atasan : $patasanid, 1 : $patasan1, 2 : $patasan2, 3 : $patasan3, 4 : $patasan4"; exit;
    
    if ($act=='input') {
        $query="insert into $dbname.t_brrutin0 (idrutin, karyawanid, icabangid, areaid, tgl, kode)values"
                . "('$kodenya', '$pkaryawan', '$pidcabang', '$pareaid', Current_Date(), 2)";
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
             . " icabangid_o='$pidcabang', "
             . " areaid_o='$pareaid', "
             . " bulan='$pbulan', "
             . " kodeperiode='$pkadeperiode', "
             . " periode1='$pp01', "
             . " periode2='$pp02', "
             . " nopol='$pnopol', "
             . " keterangan='$pket', "							 
             . " jabatanid='$pjabatanid', "
             . " divisi='$pdivprodid', "
             . " divi='OTC', "
             . " KODEWILAYAH='$pwilgabungan', "
             . " atasanid='$patasanid', "
             . " atasan1='$patasan1', "
             . " atasan2='$patasan2', "
             . " atasan3='$patasan3', "
             . " atasan4='$patasan4', "
             . " userid='$piduser' WHERE "
            . " idrutin='$kodenya'"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
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
            
            $tgl1="0000-00-00";
            $tgl2="0000-00-00";
            
            $var_date1="e_1isitgl".$i;
            $var_date2="e_2isitgl".$i;
            if (isset($_POST[$var_date1])) {
                $date1=$_POST[$var_date1];
                if (!empty($date1))
                    $tgl1 =  date("Y-m-d", strtotime($date1));
            }
            
            if (isset($_POST[$var_date2])) {
                $date2=$_POST[$var_date2];
                if (!empty($date2))
                    $tgl2 =  date("Y-m-d", strtotime($date2));
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
                
                $query = "insert into $dbname.t_brrutin1 (idrutin,nobrid,qty,rp,rptotal,notes, coa, tgl1, tgl2, km) "
                        . " values ('$kodenya','$blid','$blqty','$blnilai','$bltotal','$blnote', '$coadet', '$tgl1', '$tgl2', '$isikmdetail')";
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
    
        
    if ($act=="input") {
        //update gambar
        $img=$_POST['txtgambar'];
        $query = "update $dbname.t_brrutin0 set gambar='$img' WHERE idrutin='$kodenya'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
        $query = "update $dbname2.t_brrutin0_ttd set gambar='$img' WHERE idrutin='$kodenya'";
        mysqli_query($cnmy, $query);
        
    }
    
    
    if ($pjabatanid=="18" OR $pjabatanid=="35") {
        $query = "update $dbname.t_brrutin0 set atasan1='$pkaryawan', tgl_atasan1=NOW(), atasan2='$patasan2' WHERE idrutin='$kodenya'";
        mysqli_query($cnmy, $query);
        if (empty($patasan2) AND empty($patasan3)) {
            $query = "update $dbname.t_brrutin0 set atasan2='$pkaryawan', tgl_atasan2=NOW(), atasan3='$pkaryawan', tgl_atasan3=NOW() WHERE idrutin='$kodenya'";
            mysqli_query($cnmy, $query);
        }elseif (empty($patasan2)) {
            $query = "update $dbname.t_brrutin0 set atasan2='$pkaryawan', tgl_atasan2=NOW() WHERE idrutin='$kodenya'";
            mysqli_query($cnmy, $query);
        }
    }
    
    if ($pjabatanid=="23") {
        if (empty($patasan1) AND empty($patasan2) AND empty($patasan3)) {
            $query = "update $dbname.t_brrutin0 set atasan1='$pkaryawan', tgl_atasan1=NOW(), atasan2='$pkaryawan', tgl_atasan2=NOW(), atasan3='$pkaryawan', tgl_atasan3=NOW() WHERE idrutin='$kodenya'";
            mysqli_query($cnmy, $query);
        }elseif (empty($patasan1) AND empty($patasan2)) {
            $query = "update $dbname.t_brrutin0 set atasan1='$pkaryawan', tgl_atasan1=NOW(), atasan2='$pkaryawan', tgl_atasan2=NOW() WHERE idrutin='$kodenya'";
            mysqli_query($cnmy, $query);
        }elseif (empty($patasan1)) {
            $query = "update $dbname.t_brrutin0 set atasan1='$pkaryawan', tgl_atasan1=NOW() WHERE idrutin='$kodenya'";
            mysqli_query($cnmy, $query);
        }
    }
    
    if ($pjabatanid=="08" OR $pjabatanid=="10" OR $pjabatanid=="36" OR $pjabatanid=="06") {
        $query = "update $dbname.t_brrutin0 set atasan1='$pkaryawan', tgl_atasan1=NOW(), atasan2='$pkaryawan', tgl_atasan2=NOW(), atasan3='$pkaryawan', tgl_atasan3=NOW() WHERE idrutin='$kodenya'";
        mysqli_query($cnmy, $query);
    }
    
    if ($pjabatanid=="36") {//HOS
        $query = "update $dbname.t_brrutin0 set atasan4='$pkaryawan', tgl_atasan4=NOW() WHERE idrutin='$kodenya'";
        mysqli_query($cnmy, $query);
    }
    
    $query = "update $dbname.t_brrutin0 set nama_karyawan='$pnmkaryawan' WHERE idrutin='$kodenya'";
    mysqli_query($cnmy, $query);
    
    
    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
    
    
}
?>

