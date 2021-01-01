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

if ($pmodule=='entrybrcashotcho')
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
        
        mysqli_query($cnmy, "update dbmaster.t_ca0 set stsnonaktif='Y', keterangan=CONCAT(keterangan,'$kethapus',', $pnmlengkap, ', NOW()) WHERE idca='$kodenya' LIMIT 1");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
        mysqli_close($cnmy);
        header('location:../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&act=complt');
        exit;
    }else {
        
        $kodenya=$_POST['e_id'];
        $ptgl =  $_POST['e_tgl'];
        $pp01 =  date("Y-m-01", strtotime($ptgl));
        $pperi01 =  date("Y-m", strtotime($ptgl));
        $pidkaryawan = $_POST['e_idkaryawan'];
        
        $pdivprodid="OTC";
        $pdivipilih="OTC";//OTC / ETH
        
        $pjabatanid = $_POST['cb_idjabatan'];
        $pidcabang = $_POST['cb_idcabang'];
        $pidarea = $_POST['cb_idarea'];
        $pjenisca = $_POST['e_jenisca'];
        $pket = $_POST['e_ket'];
        $prpsemua = $_POST['e_totalsemua'];
        
        $patasan1 = $_POST['cb_idspv'];
        $patasan2 = $_POST['cb_idam'];
        $patasan3 = "";
        $patasan4 = $_POST['cb_idhos'];
        
        $pnonekrykd_=$_POST['e_kdkrynone'];
        $pnonekrybaru_=trim($_POST['e_nmkrynone']);
        $pnonekrynmlama_=trim($_POST['e_nmkrynone2']);
        
        if (!empty($pnonekrybaru_)) $pnonekrybaru_= strtoupper ($pnonekrybaru_);
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
    
        
        $num_records = $_POST['num_records'];
        
        if (!empty($pket)) $pket = str_replace("'", " ", $pket);
        $prpsemua=str_replace(",","", $prpsemua);
        
        if ((int)$num_records==0) {
            echo "detail kosong...";
            mysqli_close($cnmy);
            exit;
        }
        
        if ($pact=="input") {
            
            if ($pjabatanid=="undefined") $pjabatanid="";
            if ($pidcabang=="undefined") $pidcabang="";
            if ($pidarea=="undefined") $pidarea="";
            if ($patasan1=="undefined") $patasan1="";
            if ($patasan2=="undefined") $patasan2="";
            if ($patasan3=="undefined") $patasan3="";
            if ($patasan4=="undefined") $patasan4="";
            
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
            
            //kodeperiode dijadikan id karyawan kontrak
            
            $sql=  mysqli_query($cnmy, "select MAX(RIGHT(idca,8)) as NOURUT from dbmaster.t_ca0");
            $ketemu=  mysqli_num_rows($sql);
            $awal=8; $urut=1; $kodenya=""; $periode=date('Ymd');
            if ($ketemu>0){
                $o=  mysqli_fetch_array($sql);
                $urut=$o['NOURUT']+1;
                $jml=  strlen($urut);
                $awal=$awal-$jml;
                $kodenya="CA".str_repeat("0", $awal).$urut;
            }
            
        }
        
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
        
        //echo "JENIS $pjeniupdate. APV : $papvats1, $papvats2, $papvats3, $papvats4"; mysqli_close($cnmy); exit;
        
        if ($pact=="input") {
            
            $tampilsm =  mysqli_query($cnmy, "select idca from dbmaster.t_ca0 WHERE idca='$kodenya'");
            $ketemusm =  mysqli_num_rows($tampilsm);
            if ($ketemusm>0){
                echo "ID $kodenya, SUDAH ADA...!!!";
                mysqli_close($cnmy);
                exit;
            }
            
            if ($pkaryawannone==true) {
                $sql=  mysqli_query($cnmy, "select idca from dbmaster.t_ca0 WHERE stsnonaktif <> 'Y' AND karyawanid='$pidkaryawan' AND DATE_FORMAT(periode,'%Y-%m')='$pperi01' AND divisi='OTC' AND nama_karyawan='$pnonekrybaru_' AND icabangid_o='$pidcabang' AND areaid_o='$pidarea' AND atasan1='$patasan1' AND atasan2='$patasan2' AND atasan4='$patasan4'");
            }else{
                $sql=  mysqli_query($cnmy, "select idca from dbmaster.t_ca0 WHERE stsnonaktif <> 'Y' AND karyawanid='$pidkaryawan' AND DATE_FORMAT(periode,'%Y-%m')='$pperi01'");
            }
            $ketemu=  mysqli_num_rows($sql);
            if ($ketemu>0){
                $o=  mysqli_fetch_array($sql);
                $dnobrid=$o['idca'];
                echo "PERIODE TERSEBUT SUDAH PERNAH INPUT DATA DENGAN ID : $dnobrid";
                mysqli_close($cnmy);
                exit;
            }
            
            
            $query="insert into dbmaster.t_ca0 (idca, karyawanid, icabangid, areaid, tgl, kode, periode, keterangan, jabatanid, divisi, "
                    . " KODEWILAYAH, atasan1, atasan2, atasan3, atasan4, "
                    . " jenis_ca, userid, divi, icabangid_o, areaid_o, nama_karyawan, bulan)values"
                    . "('$kodenya', '$pidkaryawan', '$pidcabang', '$pidarea', Current_Date(), 3, '$pp01', '$pket', '$pjabatanid', '$pdivprodid', "
                    . " '$pwilgabungan', '$patasan1', '$patasan2', '$patasan3', '$patasan4', "
                    . " '$pjenisca', '$ppilidcard', '$pdivipilih', '$pidcabang', '$pidarea', '$pnonekrybaru_', '$pp01')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
        }else{
            
            if ($pkaryawannone==true) {
                $sql=  mysqli_query($cnmy, "select idca from dbmaster.t_ca0 WHERE idca<>'$kodenya' AND stsnonaktif <> 'Y' AND karyawanid='$pidkaryawan' AND DATE_FORMAT(periode,'%Y-%m')='$pperi01' AND divisi='OTC' AND nama_karyawan='$pnonekrybaru_' AND icabangid_o='$pidcabang' AND areaid_o='$pidarea' AND atasan1='$patasan1' AND atasan2='$patasan2' AND atasan4='$patasan4'");
            }else{
                $sql=  mysqli_query($cnmy, "select idca from dbmaster.t_ca0 WHERE idca<>'$kodenya' AND stsnonaktif <> 'Y' AND karyawanid='$pidkaryawan' AND DATE_FORMAT(periode,'%Y-%m')='$pperi01'");
            }
            $ketemu=  mysqli_num_rows($sql);
            if ($ketemu>0){
                $o=  mysqli_fetch_array($sql);
                $dnobrid=$o['idca'];
                echo "PERIODE TERSEBUT SUDAH PERNAH INPUT DATA DENGAN ID : $dnobrid";
                mysqli_close($cnmy);
                exit;
            }
            
        }
        
        
        
        $query = "update dbmaster.t_ca0 set karyawanid='$pidkaryawan', "
                 . " icabangid='$pidcabang', "
                 . " areaid='$pidarea', "
                 . " icabangid_o='$pidcabang', "
                 . " areaid_o='$pidarea', "
                 . " periode='$pp01', "
                 . " keterangan='$pket', "							 
                 . " jabatanid='$pjabatanid', "
                 . " divisi='$pdivprodid', "
                 . " KODEWILAYAH='$pwilgabungan', "
                 . " atasan1='$patasan1', "
                 . " atasan2='$patasan2', "
                 . " atasan3='$patasan3', "
                 . " atasan4='$patasan4', "
                 . " jenis_ca='$pjenisca', "
                 . " userid='$ppilidcard', "
                 . " nama_karyawan='$pnonekrybaru_', "
                 . " bulan='$pp01',"
                 . " fin='$ppilidcard', tgl_fin=NOW() WHERE "
                . " idca='$kodenya' LIMIT 1"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_query($cnmy, "UPDATE dbmaster.t_ca0 SET stsnonaktif='Y', keterangan=CONCAT('ERROR : ', keterangan) WHERE idca='$kodenya'  LIMIT 1"); echo $erropesan; mysqli_close($cnmy); exit; }
    
        
        $query_apv = "update dbmaster.t_ca0 SET tgl_atasan1=NULL, tgl_atasan2=NULL, tgl_atasan3=NULL WHERE idca='$kodenya' AND (IFNULL(tgl_atasan4,'')='' AND IFNULL(tgl_atasan4,'0000-00-00 00:00:00')='0000-00-00 00:00:00' AND IFNULL(tgl_atasan4,'0000-00-00')='0000-00-00') LIMIT 1"; 
        mysqli_query($cnmy, $query_apv);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_query($cnmy, "UPDATE dbmaster.t_ca0 SET stsnonaktif='Y', keterangan=CONCAT('ERROR : ', keterangan) WHERE idca='$kodenya' LIMIT 1"); echo $erropesan; mysqli_close($cnmy); exit; }
            
        $query_apv_atas="";
        if ($pjeniupdate=="1"){
            $query_apv_atas = "update dbmaster.t_ca0 SET tgl_atasan1=NOW(), tgl_atasan2=NULL, tgl_atasan3=NULL WHERE idca='$kodenya' AND (IFNULL(tgl_atasan4,'')='' AND IFNULL(tgl_atasan4,'0000-00-00 00:00:00')='0000-00-00 00:00:00' AND IFNULL(tgl_atasan4,'0000-00-00')='0000-00-00') LIMIT 1"; 
        }elseif ($pjeniupdate=="2"){
            $query_apv_atas = "update dbmaster.t_ca0 SET tgl_atasan1=NOW(), tgl_atasan2=NOW(), tgl_atasan3=NULL WHERE idca='$kodenya' AND (IFNULL(tgl_atasan4,'')='' AND IFNULL(tgl_atasan4,'0000-00-00 00:00:00')='0000-00-00 00:00:00' AND IFNULL(tgl_atasan4,'0000-00-00')='0000-00-00') LIMIT 1"; 
        }elseif ($pjeniupdate=="3"){
            $query_apv_atas = "update dbmaster.t_ca0 SET tgl_atasan1=NOW(), tgl_atasan2=NOW(), tgl_atasan3=NOW() WHERE idca='$kodenya'"; 
        }
        
        
        if (!empty($query_apv_atas)) {
            mysqli_query($cnmy, $query_apv_atas);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_query($cnmy, "UPDATE dbmaster.t_ca0 SET stsnonaktif='Y', keterangan=CONCAT('ERROR : ', keterangan) WHERE idca='$kodenya' LIMIT 1"); echo $erropesan; mysqli_close($cnmy); exit; }
        }

        
        
        
        $ttotal=0;

        $query = "delete from dbmaster.t_ca1 where idca='$kodenya'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_query($cnmy, "UPDATE dbmaster.t_ca0 SET stsnonaktif='Y', keterangan=CONCAT('ERROR : ', keterangan) WHERE idca='$kodenya' LIMIT 1"); echo $erropesan; mysqli_close($cnmy); exit; }
        
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
                    
                    $tamplcoa=  mysqli_query($cnmy, "select COA4 from dbmaster.posting_coa_rutin WHERE divisi='$pdivprodid' AND nobrid='$blid'");
                    $nc= mysqli_fetch_array($tamplcoa);
                    $coadet=$nc['COA4'];
                    

                    $query = "insert into dbmaster.t_ca1 (idca,nobrid,tgl1,tgl2,rptotal,notes,coa) "
                            . " values ('$kodenya','$blid','$blmytgl1','$blmytgl2','$bltotal','$blnote', '$coadet')";
                    mysqli_query($cnmy, $query);
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_query($cnmy, "UPDATE dbmaster.t_ca0 SET stsnonaktif='Y', keterangan=CONCAT('ERROR : ', keterangan) WHERE idca='$kodenya' LIMIT 1"); echo $erropesan; mysqli_close($cnmy); exit; }

                    $ttotal=floatval($ttotal)+floatval($bltotal);

                }


            }

            $tampilkan=  mysqli_query($cnmy, "select sum(rptotal) rptotal from dbmaster.t_ca1 WHERE idca='$kodenya'");
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
            $query = "update dbmaster.t_ca0 set jumlah='$ttotal' where idca='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

        }
        
        
        
        //kodeperiode dijadikan id karyawan kontrak
        //echo "$num_records - ID : $kodenya, $pidkaryawan, $pdivipilih : $pdivprodid, $pjabatanid, $pidcabang, $pidarea, reg : $pregion, wil : $pwilgabungan, $pjenisca, atasan : $patasan1, $patasan2, $patasan3, $patasan4, kry other : $pnonekrykd_, $pnonekrybaru_, $pnonekrynmlama_, ket : $pket, $prpsemua";
        mysqli_close($cnmy);
        
        
        if ($pact=="input")
            header('location:../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&act='.$kembali);
        else
            header('location:../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&act=complete');
         
        exit;
        
    }
    
}

mysqli_close($cnmy);
?>