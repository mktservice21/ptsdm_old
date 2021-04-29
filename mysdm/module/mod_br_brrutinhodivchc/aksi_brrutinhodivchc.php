<?php

session_start();

    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    
    
// Hapus 
if ($module=='entrybrrutinhodivchc' AND $act=='hapus')
{
    $puserid="";
    $pnamalengkap="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
    if (isset($_SESSION['NAMALENGKAP'])) $pnamalengkap=$_SESSION['NAMALENGKAP'];

    if (empty($puserid)) {
        mysqli_close($cnmy);
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }
    
    $kodenya=$_GET['id'];
    $kethapus= $_GET['kethapus'];
    if ($kethapus=="null") $kethapus="";
    
    if (!empty($kethapus)) $kethapus = str_replace("'", " ", $kethapus);
        
        //hapus data
        mysqli_query($cnmy, "UPDATE dbmaster.t_brrutin0 SET stsnonaktif='Y', keterangan=CONCAT(IFNULL(keterangan,''),'$kethapus',', $pnamalengkap, ', NOW()) WHERE idrutin='$kodenya' LIMIT 1");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
        mysqli_query($cnmy, "DELETE FROM dbimages.img_brrutin1 WHERE idrutin='$kodenya' LIMIT 1");
        
    mysqli_close($cnmy);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='entrybrrutinhodivchc' AND $act=='ttdupdate')
{
    
    $kodenya=$_POST['e_id'];
    $pimgttd=$_POST['txtgambar'];
    
    if (!empty($kodenya)) {
        mysqli_query($cnmy, "UPDATE dbmaster.t_brrutin0 SET gambar='$pimgttd' WHERE idrutin='$kodenya' LIMIT 1");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
        mysqli_close($cnmy);
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    }
    
    exit;
}
elseif ($module=='entrybrrutinhodivchc' AND $act=='uploaddok')
{
    $kodenya=$_POST['e_id'];
    if (!empty($kodenya)) {
        include "../../config/fungsi_image.php";
        
        $gambarnya=$_POST['e_imgconv'];
        
        if (!empty($gambarnya)) {
            mysqli_query($cnmy, "insert into dbimages.img_brrutin1 (idrutin, gambar2) values ('$kodenya', '$gambarnya')");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }
        
        mysqli_close($cnmy);
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    }
        
    exit;
}
elseif ($module=='entrybrrutinhodivchc' AND $act=='hapusgambar')
{
    $kodenya=$_GET['id'];
    $idgam="";
    if (isset($_GET['idgam'])) $idgam=$_GET['idgam'];
    
    if (!empty($kodenya) AND !empty($idgam)) {
        mysqli_query($cnmy, "delete from dbimages.img_brrutin1 WHERE nourut='$idgam' and idrutin='$kodenya' LIMIT 1");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
		
        mysqli_close($cnmy);
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=uploaddok&id='.$kodenya);
    }
    
    exit;
}
elseif ($module=='entrybrrutinhodivchc' AND ($act=='input' OR $act=='update'))
{
    $puserid=$_POST['e_idinputuser'];
    $pcardid=$_POST['e_idcarduser'];
    
    if (empty($puserid)) {
        $puserid="";
        if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
        if (isset($_SESSION['IDCARD'])) $pcardid=$_SESSION['IDCARD'];

        if (empty($puserid)) {
            mysqli_close($cnmy);
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }
    }
    
    $pdivisiid=$_POST['e_divisiid'];
    if (empty($pdivisiid)) $pdivisiid="OTC";
    
    
    $kodenya=$_POST['e_id'];
    
    $pjbtid=$_POST['e_jabatanid'];
    $pidcabang=$_POST['e_cabangid'];
    $pareaid=$_POST['e_areaid'];
    $pidnopol=$_POST['e_nopolid'];
    $pidkaryawan=$_POST['e_idkaryawan'];
    
    $pbln_c=$_POST['e_bulan'];
    $pkdperiode=$_POST['e_periode'];
    $ptgl1=$_POST['e_periode01'];
    $ptgl2=$_POST['e_periode02'];
    $pnotes=$_POST['e_ket'];
    $patasan=$_POST['e_atasan'];
    $ptotalrp=$_POST['e_totalsemua'];
    
    $patasan1="";//$_POST['e_atasan'];
    $patasan2="";//$_POST['e_atasan'];
    $patasan3="";//$_POST['e_atasan'];
    $patasan4=$_POST['e_atasan'];
    
    
    if ($act=="input" AND $pidkaryawan<>$pcardid) {
        $pjbtid="";
        $pidnopol="";
    }
    
    
    
    if (empty($pjbtid)) {
        $pjbtid=  getfieldcnmy("select distinct jabatanid as lcfields from hrd.karyawan where karyawanid='$pidkaryawan'");
        if (empty($pjbtid)) {
            $pjbtid=  getfieldcnmy("select distinct jabatanid as lcfields from dbmaster.t_karyawan_posisi where karyawanid='$pidkaryawan'");
        }
    }
    
    if (empty($pidnopol)) {
        $pidnopol=  getfieldcnmy("select nopol as lcfields from dbmaster.t_kendaraan_pemakai where karyawanid='$pidkaryawan' AND IFNULL(stsnonaktif,'')<>'Y' order by tglawal desc");
    }
    
    //echo "$pidkaryawan, $pjbtid, $pidnopol";exit;
    
    
    // CABANG dan AREA KHUSUS HO CHC = 
    if (empty($pidcabang)) {
        $pidcabang="0000000007";
    }
    
    if (empty($pareaid)) {
        $pareaid="0000000001";
    }
    
    
    if (empty($pidnopol)) {
        $pidnopol = getfieldcnmy("select nopol as lcfields from dbmaster.t_kendaraan_pemakai where karyawanid='$pidkaryawan' AND IFNULL(stsnonaktif,'')<>'Y' order by tglawal desc LIMIT 1");
    }
    
    
    //echo "buat test"; mysqli_close($cnmy); exit;
    
    
    $pbln= date("Y-m-01", strtotime($pbln_c));
    $pcari_bln= date("Ym", strtotime($pbln_c));
    
    $ptgl1 = str_replace('/', '-', $ptgl1);
    $ptgl2 = str_replace('/', '-', $ptgl2);
    $ptgl1= date("Y-m-d", strtotime($ptgl1));
    $ptgl2= date("Y-m-d", strtotime($ptgl2));
    
    if (!empty($pnotes)) $pnotes = str_replace("'", " ", $pnotes);
    
    if (empty($ptotalrp)) $ptotalrp=0;
    $ptotalrp=str_replace(",","", $ptotalrp);
    
    $pnamakaryawan=  getfieldcnmy("select distinct nama as lcfields from hrd.karyawan where karyawanid='$pidkaryawan'");
    
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
    
    
    
    $pbolehsimpan=false;
    foreach ($_POST['chk_kodeid'] as $no_brid) {
        $pdet_rptotal= $_POST['e_txttotalrp'][$no_brid];
        
        if (empty($pdet_rptotal)) $pdet_rptotal=0;
        $pdet_rptotal=str_replace(",","", $pdet_rptotal);
        
        if ((DOUBLE)$pdet_rptotal>0) {
            $pbolehsimpan=true;
            //echo "$pdet_rptotal<br/>";
        }
    }
    
    
    if ($pbolehsimpan == true) {
        
        //eksekusi 1
        
        if ($act=="input") {
            $sql=  mysqli_query($cnmy, "select MAX(RIGHT(idrutin,7)) as NOURUT from dbmaster.t_brrutin0");
            $ketemu=  mysqli_num_rows($sql);
            $awal=7; $urut=1; $kodenya=""; $periode=date('Ymd');
            if ($ketemu>0){
                $o=  mysqli_fetch_array($sql);
                $urut=$o['NOURUT']+1;
                $jml=  strlen($urut);
                $awal=$awal-$jml;
                $kodenya="BRT".str_repeat("0", $awal).$urut;
            }
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }
        
        if (empty($kodenya)){
            echo "ID kosong, ulang lagi....";
            mysqli_close($cnmy);
            exit;
        }
        
        
        $pttotal=0;
        unset($pinsert_data_detail);//kosongkan array
        foreach ($_POST['chk_kodeid'] as $no_brid) {
            
            $pdet_coa= $_POST['e_txtcoa4'][$no_brid];
            
            $pdet_qty= $_POST['e_txtjmlrp'][$no_brid];
            $pdet_nilairp= $_POST['e_txtnilairp'][$no_brid];
            $pdet_rptotal= $_POST['e_txttotalrp'][$no_brid];
            
            $pdet_km= $_POST['e_txtkm'][$no_brid];
            $pdet_tkesuntuk= $_POST['cb_tkes'][$no_brid];
            $pdet_notes= $_POST['e_txtnotes'][$no_brid];
            
            $pdet_tgl01= $_POST['e_tglpilih01'][$no_brid];
            $pdet_tgl02= $_POST['e_tglpilih02'][$no_brid];
            
            
            
            if (empty($pdet_qty)) $pdet_qty=0;
            if (empty($pdet_nilairp)) $pdet_nilairp=0;
            if (empty($pdet_rptotal)) $pdet_rptotal=0;
            
            $pdet_qty=str_replace(",","", $pdet_qty);
            $pdet_nilairp=str_replace(",","", $pdet_nilairp);
            $pdet_rptotal=str_replace(",","", $pdet_rptotal);
            
            if (empty($pdet_km)) $pdet_km=0;
            $pdet_km=str_replace(",","", $pdet_km);
            
            if (!empty($pdet_notes)) $pdet_notes = str_replace("'", " ", $pdet_notes);
            
            if (empty($pdet_tgl01)) $pdet_tgl01="0000-00-00";
            if (empty($pdet_tgl02)) $pdet_tgl02="0000-00-00";
            
            if ((DOUBLE)$pdet_rptotal>0) {
                if ((DOUBLE)$pdet_qty==0) $pdet_qty=1;
                if ((DOUBLE)$pdet_nilairp==0) $pdet_nilairp=$pdet_rptotal;
                
                if ($pdet_tgl01<>"0000-00-00") $pdet_tgl01=date("Y-m-d", strtotime($pdet_tgl01));
                if ($pdet_tgl02<>"0000-00-00") $pdet_tgl02=date("Y-m-d", strtotime($pdet_tgl02));
                
                
                
                if (empty($pdet_coa)) {
                    $pdet_coa=getfieldcnmy("select distinct COA4 as lcfields FROM dbmaster.posting_coa_rutin WHERE divisi='$pdivisiid' and nobrid='$no_brid'");
                }
                
                $pttotal=floatval($pttotal)+floatval($pdet_rptotal);
                
                //echo "COA : $pdet_coa, ";
                //echo "QTY : $pdet_qty, Nilai Rp. $pdet_nilairp, Total : $pdet_rptotal, ";
                //echo "KM : $pdet_km, Obat Untuk : $pdet_tkesuntuk, notes : $pdet_notes, ";
                //echo "TGL01 : $pdet_tgl01, TGL02 : $pdet_tgl02<br/>";
                //idrutin, nobrid, qty, rp, rptotal, notes, tgl1, tgl2, km, obat_untuk, coa
                
                $pinsert_data_detail[] = "('$kodenya', '$no_brid', '$pdet_qty', '$pdet_nilairp', '$pdet_rptotal', '$pdet_notes', '$pdet_tgl01', '$pdet_tgl02', '$pdet_km', '$pdet_tkesuntuk', '$pdet_coa')";


            }
        }
        
        if ((DOUBLE)$ptotalrp<>(DOUBLE)$pttotal) $ptotalrp=$pttotal;
        
        
        
        if (empty(trim($kodenya))) { echo "kode kosong"; mysqli_close($cnmy); exit; }
        if (empty(trim($pidkaryawan))) { echo "karyawan kosong"; mysqli_close($cnmy); exit; }
        if (empty(trim($pbln))) { echo "bulan kosong"; mysqli_close($cnmy); exit; }
        if (empty(trim($pkdperiode))) { echo "periode kosong"; mysqli_close($cnmy); exit; }
        
        
        
        //cek jika sudah ada inputan
        $query = "select idrutin from dbmaster.t_brrutin0 WHERE DATE_FORMAT(bulan,'%Y%m')='$pcari_bln' AND "
                . " karyawanid='$pidkaryawan' AND IFNULL(stsnonaktif,'')<>'Y' AND idrutin<>'$kodenya'";//AND kodeperiode='$pkdperiode' 
        
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);

        if ($ketemu>0) {
            $row= mysqli_fetch_array($tampil);
            $nidrutin=$row['idrutin'];
            if (!empty($nidrutin)) {
                echo "Data Sudah Ada, dengan ID : $nidrutin";
                mysqli_close($cnmy); exit;
            }
        }
        
        
        //echo "ID : $kodenya, $pdivisiid, $pjbtid, KRY : $pidkaryawan, Bln : ($pcari_bln) $pbln ($pkdperiode : $ptgl1 - $ptgl2)<br/>$pnotes, Atasan : $patasan, Rp. $ptotalrp<br/>ID CAB : $pidcabang, ID WIL : $pwilgabungan, area : $pareaid, nopol : $pidnopol<br/>A 1 : $patasan1, A 2 : $patasan2, A 3 : $patasan3, A 4 : $patasan4";
        //echo "Total Jumlah : $ptotalrp, Total Rinci : $pttotal<br/>"; mysqli_close($cnmy); exit;
        
        //eksekusi 2
        
        if ($act=="input") {
            
            $query="insert into dbmaster.t_brrutin0 (idrutin, karyawanid, icabangid, areaid, KODEWILAYAH, tgl, kode, "
                    . " bulan, kodeperiode, periode1, periode2, nama_karyawan, jabatanid, divisi, atasanid, atasan4, "
                    . " keterangan, nopol, userid, divi)values"
                    . "('$kodenya', '$pidkaryawan', '$pidcabang', '$pareaid', '$pwilgabungan', Current_Date(), 1, "
                    . " '$pbln', '$pkdperiode', '$ptgl1', '$ptgl2', '$pnamakaryawan', '$pjbtid', '$pdivisiid', '$patasan', '$patasan4', "
                    . " '$pnotes', '$pidnopol', '$pcardid', 'OTC')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

            //simpan ke tabel images
            mysqli_query($cnmy, "DELETE FROM dbimages.t_brrutin0_ttd WHERE idrutin='$kodenya' LIMIT 1");
            $query = "INSERT INTO dbimages.t_brrutin0_ttd (idrutin)values('$kodenya')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_query($cnmy, "DELETE FROM dbmaster.t_brrutin0 WHERE idrutin='$kodenya' LIMIT 1"); echo $erropesan; exit; }
            
        }
        
        
        
        $query = "UPDATE dbmaster.t_brrutin0 SET karyawanid='$pidkaryawan', "
                 . " icabangid='$pidcabang', "
                 . " areaid='$pareaid', "
                 . " bulan='$pbln', "
                 . " kodeperiode='$pkdperiode', "
                 . " periode1='$ptgl1', "
                 . " periode2='$ptgl2', "
                 . " nopol='$pidnopol', "
                 . " keterangan='$pnotes', "							 
                 . " jabatanid='$pjbtid', "
                 . " divisi='$pdivisiid', "
                 . " KODEWILAYAH='$pwilgabungan', "
                 . " atasanid='$patasan', "
                 . " idca='HO', "
                 . " userid='$pcardid', "
                . "  jumlah='$ptotalrp' WHERE "
                . " idrutin='$kodenya' LIMIT 1"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        if ($act=="input") {
            $pimgttd=$_POST['txtgambar'];
            $query = "UPDATE dbmaster.t_brrutin0 SET gambar='$pimgttd'  WHERE idrutin='$kodenya' LIMIT 1"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }
        
        //update ke atasan
        $query = "UPDATE dbmaster.t_brrutin0 SET atasan1='', tgl_atasan1=NOW(), atasan2='', tgl_atasan2=NOW(), "
                . " atasan3='', tgl_atasan3=NOW(), atasan4='$patasan4' WHERE "
                . " idrutin='$kodenya' LIMIT 1";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        if ($pjbtid=="01" AND $act=="input") {
            $query = "UPDATE dbmaster.t_brrutin0 SET atasan4='$pidkaryawan', tgl_atasan4=NULL WHERE idrutin='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }
        
        if ($pidkaryawan=="0000001479" AND $act=="input") {
            $query = "UPDATE dbmaster.t_brrutin0 SET atasan4='', tgl_atasan4=NOW() WHERE idrutin='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }
        
        
        //detail rincian
        $query = "DELETE from dbmaster.t_brrutin1 where idrutin='$kodenya'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        $query = "insert into dbmaster.t_brrutin1 (idrutin, nobrid, qty, rp, rptotal, notes, tgl1, tgl2, km, obat_untuk, coa) "
                . " VALUES ".implode(', ', $pinsert_data_detail);
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_query($cnmy, "UPDATE dbmaster.t_brrutin0 set jumlah=0 WHERE idrutin='$kodenya' LIMIT 1"); echo $erropesan; exit; }
        
        
        
        
        
        //end eksekusi 2
        
        mysqli_close($cnmy);
        
        ////if ($act=='input') header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=tambahbaru');
        ////else 
            header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');

            exit;
            
    }
    
    mysqli_close($cnmy);
    //header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    
    
}
