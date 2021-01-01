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

    $ppilgrpdivisi=$_SESSION['DIVISI'];
    $ppilidcard=$_SESSION['IDCARD'];
    $pnmlengkap=$_SESSION['NAMALENGKAP'];
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    
    $kembali = "tambahbaru";
    if ($ppilgrpdivisi != "OTC") {
        $kembali = "complete";
    }
    
    
    
include "../../config/koneksimysqli.php";
include "../../config/fungsi_sql.php";


if ($pmodule=='entrybrrutinotcho')
{
    if ($pact=="hapus") {
        
        $kethapus= $_GET['kethapus'];
        if ($kethapus=="null") $kethapus="";
        if (!empty($kethapus)) $kethapus =", Ket Hapus : ".$kethapus;
        if (!empty($kethapus)) $kethapus = str_replace("'", " ", $kethapus);
    
        $kodenya=$_GET['id'];
        
        if (empty($kodenya)) {
            echo "KOSONG...";
            mysqli_close($cnmy);
            exit;
        }
        
        //echo "$kethapus, $kodenya"; mysqli_close($cnmy); exit;
        
        mysqli_query($cnmy, "update dbmaster.t_brrutin0 set stsnonaktif='Y', keterangan=CONCAT(keterangan,'$kethapus',', $pnmlengkap, ', NOW()) WHERE idrutin='$kodenya' LIMIT 1");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
        mysqli_close($cnmy);
        header('location:../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&act=complt');
        exit;
    }elseif ($pact=="hapusgambar") {
        $kodenya=$_GET['id'];
        $idgam="";
        if (isset($_GET['idgam'])) $idgam=$_GET['idgam'];
        if (!empty($idgam)) {
            mysqli_query($cnmy, "delete from dbimages.img_brrutin1 WHERE nourut='$idgam' and idrutin='$kodenya' LIMIT 1");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            mysqli_close($cnmy);
        }
        
        header('location:../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&act=uploaddok&id='.$kodenya);
        exit;
    }elseif ($pact=="uploaddok") {
        include "../../config/fungsi_image.php";
        $kodenya=$_POST['e_id'];
        $gambarnya=$_POST['e_imgconv'];
        if (!empty($gambarnya)) {
            mysqli_query($cnmy, "insert into dbimages.img_brrutin1 (idrutin, gambar2) values ('$kodenya', '$gambarnya')");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            mysqli_close($cnmy);
        }
        header('location:../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&act=uploaddok&id='.$kodenya);
        exit;
    }else{
        $pdivprodid="OTC";
        $pdivipilih="OTC";//OTC / ETH
        
        
        $kodenya=$_POST['e_id'];
        $pidkaryawan = $_POST['e_idkaryawan'];
        $pnomorpol = $_POST['e_nopol'];
        $ppkdperiode = $_POST['e_periode'];
        
        $pbln =  $_POST['e_bulan'];
        $ptgl1 =  $_POST['e_periode01'];
        $ptgl2 =  $_POST['e_periode02'];
        $pket = $_POST['e_ket'];
        $prpsemua = $_POST['e_totalsemua'];
        
        
        $pjabatanid = $_POST['cb_idjabatan'];
        $pidcabang = $_POST['cb_idcabang'];
        $pidarea = $_POST['cb_idarea'];
        
        
        $patasan1 = $_POST['cb_idspv'];
        $patasan2 = $_POST['cb_idam'];
        $patasan3 = "";
        $patasan4 = $_POST['cb_idhos'];
        
        $pnonekrykd_=$_POST['e_kdkrynone'];
        $pnonekrybaru_=trim($_POST['e_nmkrynone']);
        $pnonekrynmlama_=trim($_POST['e_nmkrynone2']);
        
        
        $num_records = $_POST['num_records'];
        
        if (!empty($pnonekrybaru_)) $pnonekrybaru_= strtoupper ($pnonekrybaru_);
        if (!empty($pnonekrynmlama_)) $pnonekrynmlama_= strtoupper ($pnonekrynmlama_);
        
        $pkryawanbarukontrak=false;
        $pkaryawannone=false;
        if ($pidkaryawan=="0000002200" || $pidkaryawan=="0000002083" || (DOUBLE)$pidkaryawan==2200 || (DOUBLE)$pidkaryawan==2083) {
            $pkaryawannone=true;
        }
        
        
        
        $ptgl1 = str_replace('/', '-', $ptgl1);
        $ptgl2 = str_replace('/', '-', $ptgl2);
        
        $pbulan =  date("Y-m-01", strtotime($pbln));
        $pbulan_ =  date("Ym", strtotime($pbln));
        $ptgl01 =  date("Y-m-d", strtotime($ptgl1));
        $ptgl02 =  date("Y-m-d", strtotime($ptgl2));
        
        
        if (!empty($pket)) $pket = str_replace("'", " ", $pket);
        $prpsemua=str_replace(",","", $prpsemua);
        
        
        if (!empty($pnonekrynmlama_)) $pnonekrynmlama_= strtoupper ($pnonekrynmlama_);
        
        $pkryawanbarukontrak=false;
        $pkaryawannone=false;
        if ($pidkaryawan=="0000002200" || $pidkaryawan=="0000002083" || (DOUBLE)$pidkaryawan==2200 || (DOUBLE)$pidkaryawan==2083) {
            $pkaryawannone=true;
        }
        
        $pwilgabungan="";
        $pregion="";
        $pwilayah="01";
        $pcabwil=  substr($pidcabang, 7,3);
        if ($pidcabang=="0000000001")
            $pwilayah="01";
        else{
            
            $tampilr = mysqli_query($cnmy, "select distinct region from dbmaster.icabang where iCabangId='$pidcabang'");
            $nw= mysqli_fetch_array($tampilr);
            $pregion=$nw['region'];
            if ($pdivprodid=="OTC") {
                if ($pregion=="B")
                    $pwilayah="04";
                else
                    $pwilayah="05";
            }else{
                if ($pregion=="B")
                    $pwilayah="02";
                else
                    $pwilayah="03";
            }
            $pwilgabungan=$pwilayah."-".$pcabwil;
            
        }
        
        
        if ((DOUBLE)$num_records==0) {
            echo "detail kosong...";
            mysqli_close($cnmy);
            exit;
        }
        
        
        if ($pjabatanid=="undefined") $pjabatanid="";
        if ($pidcabang=="undefined") $pidcabang="";
        if ($pidarea=="undefined") $pidarea="";
        if ($patasan1=="undefined") $patasan1="";
        if ($patasan2=="undefined") $patasan2="";
        if ($patasan3=="undefined") $patasan3="";
        if ($patasan4=="undefined") $patasan4="";
        
        $patasanid=$patasan1;
        if (empty($patasanid)) $patasanid=$patasan2;
        if (empty($patasanid)) $patasanid=$patasan3;
        if (empty($patasanid)) $patasanid=$patasan4;
            
        if ($pact=="input") {
            
            
            
            if ($pkaryawannone==true) {
                
                if (empty($pnonekrykd_)) {//id karyawan kontrak kosong = karyawan kontrak baru
                    
                    if (empty($pidcabang)) {
                        echo "cabang masih kosong...!!!";
                        mysqli_close($cnmy);
                        exit;
                    }
                    
                    if (empty($pidarea)) {
                        echo "area masih kosong...!!!";
                        mysqli_close($cnmy);
                        exit;
                    }
                    
                    if (empty($patasan1) AND empty($patasan2) AND empty($patasan4)) {
                        echo "atasan masih kosong...!!!";
                        mysqli_close($cnmy);
                        exit;
                    }
                    
                    $pkryawanbarukontrak=true;
                }else{
                    
                }
                
                
                //karyawan baru kontrak
                if ($pkryawanbarukontrak==true) {
                    //echo "BARU<br/>";
                    $query = "select `id` as idkry from dbmaster.t_karyawan_kontrak WHERE divisi='OTC' AND nama='$pnonekrybaru_' AND icabangid_o='$pidcabang' AND areaid_o='$pidarea' AND atasan1='$patasan1' AND atasan2='$patasan2' AND atasan4='$patasan4' ORDER BY `id`";
                    $tampil_=mysqli_query($cnmy, $query);
                    $adak_=mysqli_num_rows($tampil_);
                    if ($adak_==0) {
                        $query = "INSERT INTO dbmaster.t_karyawan_kontrak (divisi, nama, atasan1, atasan2, atasan3, atasan4, icabangid_o, areaid_o, jabatanid)values"
                                . "('OTC', '$pnonekrybaru_', '$patasan1', '$patasan2', '$patasan3', '$patasan4', '$pidcabang', '$pidarea', '$pjabatanid')";
                        mysqli_query($cnmy, $query);
                        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo $erropesan; exit; }
                    }
                    
                    $query = "select `id` as idkry from dbmaster.t_karyawan_kontrak WHERE divisi='OTC' AND nama='$pnonekrybaru_' AND icabangid_o='$pidcabang' AND areaid_o='$pidarea' AND atasan1='$patasan1' AND atasan2='$patasan2' AND atasan4='$patasan4' ORDER BY `id`";
                    $tampil1_=mysqli_query($cnmy, $query);
                    $nr= mysqli_fetch_array($tampil1_);
                    $pnonekrykd_=$nr['idkry'];
                    $pnonekrynmlama_=$pnonekrybaru_;
                    
                }
                
                
            }else{
            
                $query = "select icabangid, areaid, spv atasan1, dm atasan2, sm atasan3, gsm atasan4, jabatanid from dbmaster.t_karyawan_posisi where karyawanid='$pidkaryawan'";
                $tampil= mysqli_query($cnmy, $query);
                $row= mysqli_fetch_array($tampil);
                $pidcabang=$row['icabangid'];
                $pidarea=$row['areaid'];
                $pjabatanid=$row['jabatanid'];

                $query = "select icabangid, areaid, jabatanid from hrd.karyawan where karyawanid='$pidkaryawan'";
                $tampil= mysqli_query($cnmy, $query);
                $row= mysqli_fetch_array($tampil);
                if (empty($pidcabang)) $pidcabang=$row['icabangid'];
                if (empty($pidarea)) $pidarea=$row['areaid'];
                if (empty($pjabatanid)) $pjabatanid=$row['jabatanid'];
                
            }
            
            
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
            
            
        }//end input 1
        
        if (empty($kodenya)) {
            echo "ID kosong...";
            mysqli_close($cnmy);
            exit;
        }
        
        
        $papvats1=$patasan1;
        $papvats2=$patasan2;
        $papvats3=$patasan3;
        $papvats4=$patasan4;
        
        if (empty($papvats1)) $papvats1=$pidkaryawan;
        if (empty($papvats2)) $papvats2=$papvats1;
        if (empty($papvats3)) $papvats3=$papvats2;
        if (empty($papvats4)) $papvats4=$papvats3;
        
        $pjeniupdate="";
        if (empty($patasan1)) {
            $pjeniupdate="1";
        }
        
        if (empty($patasan1) AND empty($patasan2)) {
            $pjeniupdate="2";
        }
        
        if (empty($patasan1) AND empty($patasan2) AND empty($patasan3)) {
            $pjeniupdate="3";
        }
        
        if (empty($patasan1) AND empty($patasan2) AND empty($patasan3) AND empty($patasan4)) {
            $pjeniupdate="";
        }
        
        //echo "JENIS $pjeniupdate. APV : $papvats1, $papvats2, $papvats3, $papvats4<br/>"; mysqli_close($cnmy); exit;
        
        //echo "id : $kodenya, kry : $pidkaryawan, $pnomorpol, bln : $pbln ($pbulan | $pbulan_), Kd Periode : $ppkdperiode, $ptgl1 ($ptgl01) - $ptgl2 ($ptgl02)<br/>$pket<br/>Rp. $prpsemua, $pjabatanid, $pidcabang, $pidarea, $patasan1, $patasan2, $patasan3, $patasan4<br/>Jml Inpt : $num_records, Wil : $pwilgabungan"; mysqli_close($cnmy); exit;
        
        if ($pact=="input") {
            
            $tampilsm =  mysqli_query($cnmy, "select idrutin from dbmaster.t_brrutin0 WHERE idrutin='$kodenya'");
            $ketemusm =  mysqli_num_rows($tampilsm);
            if ($ketemusm>0){
                echo "ID $kodenya, SUDAH ADA...!!!";
                mysqli_close($cnmy);
                exit;
            }
            
            
            if ($pkaryawannone==true) {
                $sql=  mysqli_query($cnmy, "select idrutin from dbmaster.t_brrutin0 WHERE kode=1 AND stsnonaktif <> 'Y' AND karyawanid='$pidkaryawan' AND "
                        . " kodeperiode='$ppkdperiode' AND DATE_FORMAT(bulan,'%Y%m')='$pbulan_' AND "
                        . " divisi='OTC' AND ikdkry_kontrak='$pnonekrykd_'");//AND nama_karyawan='$pnonekrybaru_' AND icabangid_o='$pidcabang' AND areaid_o='$pidarea' AND atasan1='$patasan1' AND atasan2='$patasan2' AND atasan4='$patasan4'
            }else{
                $sql=  mysqli_query($cnmy, "select idrutin from dbmaster.t_brrutin0 WHERE kode=1 AND stsnonaktif <> 'Y' AND karyawanid='$pidkaryawan' AND "
                        . " kodeperiode='$ppkdperiode' AND DATE_FORMAT(bulan,'%Y%m')='$pbulan_'");
            }
            $ketemu=  mysqli_num_rows($sql);
            if ($ketemu>0){
                $o=  mysqli_fetch_array($sql);
                $dnobrid=$o['idrutin'];
                echo "PERIODE TERSEBUT SUDAH PERNAH INPUT DATA DENGAN ID : $dnobrid";
                exit;
            }
            
            
            $query="insert into dbmaster.t_brrutin0 (idrutin, tgl, kode, karyawanid, icabangid, areaid, icabangid_o, areaid_o, jabatanid, divisi, "
                    . " bulan, kodeperiode, periode1, periode2, nopol, keterangan, KODEWILAYAH, "
                    . " atasanid, atasan1, atasan2, atasan3,atasan4, "
                    . " userid, divi, ikdkry_kontrak, nama_karyawan)values"
                    . " ('$kodenya', Current_Date(), 1, '$pidkaryawan', '$pidcabang', '$pidarea', '$pidcabang', '$pidarea', '$pjabatanid', '$pdivprodid', "
                    . " '$pbulan', '$ppkdperiode', '$ptgl01', '$ptgl02', '$pnomorpol', '$pket', '$pwilgabungan', "
                    . " '$patasanid', '$patasan1', '$patasan2', '$patasan3', '$patasan4', "
                    . " '$ppilidcard', '$pdivipilih', '$pnonekrykd_', '$pnonekrybaru_')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            
            
        }else{//else input 2
            
            if ($pkaryawannone==true) {
                $sql=  mysqli_query($cnmy, "select idrutin from dbmaster.t_brrutin0 WHERE idrutin<>'$kodenya' AND kode=1 AND stsnonaktif <> 'Y' AND karyawanid='$pidkaryawan' AND "
                        . " kodeperiode='$ppkdperiode' AND DATE_FORMAT(bulan,'%Y%m')='$pbulan_' AND "
                        . " divisi='OTC' AND ikdkry_kontrak='$pnonekrykd_'");//AND nama_karyawan='$pnonekrybaru_' AND icabangid_o='$pidcabang' AND areaid_o='$pidarea' AND atasan1='$patasan1' AND atasan2='$patasan2' AND atasan4='$patasan4'
            }else{
                $sql=  mysqli_query($cnmy, "select idrutin from dbmaster.t_brrutin0 WHERE idrutin<>'$kodenya' AND kode=1 AND stsnonaktif <> 'Y' AND karyawanid='$pidkaryawan' AND "
                        . " kodeperiode='$ppkdperiode' AND DATE_FORMAT(bulan,'%Y%m')='$pbulan_'");
            }
            $ketemu=  mysqli_num_rows($sql);
            if ($ketemu>0){
                $o=  mysqli_fetch_array($sql);
                $dnobrid=$o['idrutin'];
                echo "PERIODE TERSEBUT SUDAH PERNAH INPUT DATA DENGAN ID : $dnobrid";
                exit;
            }
            
        }//end input 2
        
        
        
        $query = "update dbmaster.t_brrutin0 set karyawanid='$pidkaryawan', "
                 . " icabangid='$pidcabang', "
                 . " areaid='$pidarea', "
                 . " icabangid_o='$pidcabang', "
                 . " areaid_o='$pidarea', "
                 . " bulan='$pbulan', "
                 . " kodeperiode='$ppkdperiode', "
                 . " periode1='$ptgl01', "
                 . " periode2='$ptgl02', "
                 . " nopol='$pnomorpol', "
                 . " keterangan='$pket', "							 
                 . " jabatanid='$pjabatanid', "
                 . " divisi='$pdivprodid', "
                 . " KODEWILAYAH='$pwilgabungan', "
                 . " atasanid='$patasanid', "
                 . " atasan1='$patasan1', "
                 . " atasan2='$patasan2', "
                 . " atasan3='$patasan3', "
                 . " atasan4='$patasan4', "
                 . " userid='$ppilidcard', divi='$pdivipilih', "
                 . " ikdkry_kontrak='$pnonekrykd_', nama_karyawan='$pnonekrybaru_' WHERE "
                 . " idrutin='$kodenya' LIMIT 1"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        $query_apv = "update dbmaster.t_brrutin0 SET tgl_atasan1=NULL, tgl_atasan2=NULL, tgl_atasan3=NULL WHERE idrutin='$kodenya' AND (IFNULL(tgl_atasan4,'')='' AND IFNULL(tgl_atasan4,'0000-00-00 00:00:00')='0000-00-00 00:00:00' AND IFNULL(tgl_atasan4,'0000-00-00')='0000-00-00') LIMIT 1"; 
        mysqli_query($cnmy, $query_apv);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_query($cnmy, "UPDATE dbmaster.t_brrutin0 SET stsnonaktif='Y', keterangan=CONCAT('ERROR : ', keterangan) WHERE idrutin='$kodenya' LIMIT 1"); echo $erropesan; mysqli_close($cnmy); exit; }
            
        $query_apv_atas="";
        if ($pjeniupdate=="1"){
            $query_apv_atas = "update dbmaster.t_brrutin0 SET tgl_atasan1=NOW(), tgl_atasan2=NULL, tgl_atasan3=NULL WHERE idrutin='$kodenya' AND (IFNULL(tgl_atasan4,'')='' AND IFNULL(tgl_atasan4,'0000-00-00 00:00:00')='0000-00-00 00:00:00' AND IFNULL(tgl_atasan4,'0000-00-00')='0000-00-00') LIMIT 1"; 
        }elseif ($pjeniupdate=="2"){
            $query_apv_atas = "update dbmaster.t_brrutin0 SET tgl_atasan1=NOW(), tgl_atasan2=NOW(), tgl_atasan3=NULL WHERE idrutin='$kodenya' AND (IFNULL(tgl_atasan4,'')='' AND IFNULL(tgl_atasan4,'0000-00-00 00:00:00')='0000-00-00 00:00:00' AND IFNULL(tgl_atasan4,'0000-00-00')='0000-00-00') LIMIT 1"; 
        }elseif ($pjeniupdate=="3"){
            $query_apv_atas = "update dbmaster.t_brrutin0 SET tgl_atasan1=NOW(), tgl_atasan2=NOW(), tgl_atasan3=NOW() WHERE idrutin='$kodenya'"; 
        }
        
        
        if (!empty($query_apv_atas)) {
            mysqli_query($cnmy, $query_apv_atas);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_query($cnmy, "UPDATE dbmaster.t_brrutin0 SET stsnonaktif='Y', keterangan=CONCAT('ERROR : ', keterangan) WHERE idrutin='$kodenya' LIMIT 1"); echo $erropesan; mysqli_close($cnmy); exit; }
        }
        
        
        
        $ttotal=0;

        $query = "DELETE FROM dbmaster.t_brrutin1 WHERE idrutin='$kodenya' LIMIT 30";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_query($cnmy, "UPDATE dbmaster.t_brrutin0 SET stsnonaktif='Y', keterangan=CONCAT('ERROR : ', keterangan) WHERE idrutin='$kodenya' LIMIT 1"); echo $erropesan; mysqli_close($cnmy); exit; }
        
        
        
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
                $blqty="1";
                if (isset($_POST[$var_nmqty])) $blqty = $_POST[$var_nmqty];
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
                    $tglkuitansi=$ptgl01;
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

                    $query = "insert into dbmaster.t_brrutin1 (idrutin,nobrid,qty,rp,rptotal,notes,coa,tgl1,obat_untuk,km,tgl2) "
                            . " values ('$kodenya','$blid','$blqty','$blnilai','$bltotal','$blnote', '$coadet','$tglkuitansi','$cbisikesehatan','$isikmdetail', '$tglkuitansi2')";
                    mysqli_query($cnmy, $query);
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_query($cnmy, "UPDATE dbmaster.t_brrutin0 set jumlah=0 WHERE idrutin='$kodenya'"); echo $erropesan; exit; }

                    $ttotal=floatval($ttotal)+floatval($bltotal);

                }



            }


            $tampilkan=  mysqli_query($cnmy, "select sum(rptotal) rptotal from dbmaster.t_brrutin1 WHERE idrutin='$kodenya'");
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
            $query = "update dbmaster.t_brrutin0 set "
                     . " jumlah='$ttotal' WHERE "
                    . " idrutin='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

        }

        mysqli_close($cnmy);
        
        
        if ($pact=="input")
            header('location:../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&act='.$kembali);
        else
            header('location:../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&act=complete');
         
        exit;
        
    }//end else input or atau update
    
}


mysqli_close($cnmy);

?>