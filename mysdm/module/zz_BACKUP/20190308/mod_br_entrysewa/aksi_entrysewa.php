<?php
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    $dbname = "dbmaster";
    $dbname2 = "dbimages";

    $idgam="";
    if (isset($_GET['idgam']))
        $idgam=$_GET['idgam'];
    

//HAPUS DATA
if ($module=='entrybrsewa' AND $act=='hapus')
{
    $kethapus= $_GET['kethapus'];
    if ($kethapus=="null") $kethapus="";
    if (!empty($kethapus)) $kethapus =", Ket Hapus : ".$kethapus;
    
    mysqli_query($cnmy, "update $dbname.t_sewa set stsnonaktif='Y', keterangan=CONCAT(keterangan,'$kethapus',', $_SESSION[IDCARD], ', NOW()) WHERE idsewa='$_GET[id]'");
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}

elseif ($module=='entrybrsewa' AND $act=='hapusgambar')
{
    $kodenya=$_GET['id'];
    if (!empty($idgam)) {
        mysqli_query($cnmy, "delete from $dbname2.img_sewa WHERE nourut='$idgam' and idsewa='$kodenya'");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=uploaddok&id='.$kodenya);
}

elseif ($module=='entrybrsewa' AND $act=='uploaddok')
{
    include "../../config/fungsi_image.php";
    $kodenya=$_POST['e_id'];
    
    // save gambar
    $maximg=10;
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

                mysqli_query($cnmy, "insert into $dbname2.img_sewa (idsewa, gambar) values ('$kodenya', '$file')");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            }
        }
        
        $lokasi_file="";
        $nama_file_unik="";
    }
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=uploaddok&id='.$kodenya);
}

elseif ($module=='entrybrsewa')
{
    $idca = trim($_POST['e_idca']);
    $pkaryawan = $_POST['e_idkaryawan'];

    //AREA dan DIVISI SESUAI JABATAN
    
    $pjabatanid=$_POST['e_jabatan'];
    if (empty($pjabatanid))
        $pjabatanid = getfieldcnit("select jabatanId as lcfields from dbmaster.t_karyawan_posisi where karyawanId='$pkaryawan'");
    if (empty($pjabatanid))
        $pjabatanid = getfieldcnit("select jabatanId as lcfields from hrd.karyawan where karyawanId='$pkaryawan'");
    
    $pdivprodid="";
    if (isset($_POST['cb_divisi']))
        $pdivprodid = trim($_POST['cb_divisi']);
    
    if (empty($pdivprodid)) $pdivprodid = getfieldcnit("select divisiId as lcfields from hrd.karyawan where karyawanId='$pkaryawan'");
    if (empty($pdivprodid)) $pdivprodid = getfieldcnit ("select divisiId2 as lcfields from hrd.karyawan where karyawanId='$pkaryawan'");
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
            $pidcabang = getfieldcnit("select icabangid as lcfields from MKT.ism0 where aktif='Y' AND karyawanid='$pkaryawan' limit 1");
            $pareaid = getfieldcnit("select areaId as lcfields from MKT.iarea where aktif='Y' AND iCabangId='$pidcabang'");
            $pdivprodid="CAN";
        }elseif ((int)$pjabatanid==8) {
            $pidcabang = getfieldcnit("select icabangid as lcfields from MKT.idm0 where aktif='Y' AND karyawanid='$pkaryawan' limit 1");
            $pareaid = getfieldcnit("select areaId as lcfields from MKT.iarea where aktif='Y' AND iCabangId='$pidcabang'");
            $pdivprodid="CAN";
        }elseif ((int)$pjabatanid==15) {
            $sql="select DISTINCT icabangid, areaid from MKT.imr0 WHERE aktif='Y' AND karyawanid='$pkaryawan' LIMIT 1";
            $tampil=mysqli_query($cnmy, $sql);
            $a = mysqli_fetch_array($tampil);
            $pidcabang = $a['icabangid'];
            $pareaid = $a['areaid'];
            $jmldiv = getfieldcnit("select COUNT(DISTINCT divisiid) as lcfields from MKT.imr0 where aktif='Y' AND karyawanid='$pkaryawan'");
            if ((int)$jmldiv>1) {
                if (empty($pdivprodid)) $pdivprodid="CAN";
            }
        }else{
            $pidcabang = getfieldcnit("select iCabangId as lcfields from dbmaster.t_karyawan_posisi where karyawanId='$pkaryawan'");
            $pareaid = getfieldcnit("select areaId as lcfields from dbmaster.t_karyawan_posisi where karyawanId='$pkaryawan'");
        }
    }else{
        if ($pdivprodid=="OTC") {
            $pidcabang = getfieldcnit("select iCabangId as lcfields from dbmaster.t_karyawan_posisi where karyawanId='$pkaryawan'");
            $pareaid = getfieldcnit("select areaId as lcfields from dbmaster.t_karyawan_posisi where karyawanId='$pkaryawan'");
        }
    }
    if (empty(trim($pidcabang))) { $pidcabang="0000000001"; $pareaid="0000000001";}
    
    
    //echo "$pdivprodid, $pidcabang, $pareaid, $pjabatanid";exit;
    
    //END AREA dan DIVISI SESUAI JABATAN

    
    $thnsewa = (int)$_POST['e_jmlbln']*12;
    $pjmlbln = (int)$thnsewa-1;
    $pjmlblnasli = (int)$thnsewa;
    
    $date1 = str_replace('/', '-', $_POST['e_tgl']);
    $pp01 =  date("Y-m-d", strtotime($date1));
    $pp02 = date('Y-m-d', strtotime('+'.(int)$pjmlbln.' month', strtotime($date1)));
    
    $pket = $_POST['e_ket'];
    if (!empty($pket)) $pket = str_replace("'", " ", $pket);
    
    $pjumlah=str_replace(",","", $_POST['e_totalsemua']);
    
    $rpbulan = $pjumlah/$pjmlblnasli;
    
    
    $pjabatanid = getfieldcnit("select jabatanId as lcfields from dbmaster.t_karyawan_posisi where karyawanId='$pkaryawan'");
    if (empty($pjabatanid))
        $pjabatanid = getfieldcnit("select jabatanId as lcfields from hrd.karyawan where karyawanId='$pkaryawan'");
        
    
    
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
        $reg=  getfieldcnit("select distinct region as lcfields from dbmaster.icabang where iCabangId='$pidcabang'");
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
        $sql=  mysqli_query($cnmy, "select MAX(RIGHT(idsewa,7)) as NOURUT from $dbname.t_sewa");
        $ketemu=  mysqli_num_rows($sql);
        $awal=7; $urut=1; $kodenya=""; $periode=date('Ymd');
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            $urut=$o['NOURUT']+1;
            $jml=  strlen($urut);
            $awal=$awal-$jml;
            $kodenya="SWA".str_repeat("0", $awal).$urut;
        }else{
            $kodenya=$_POST['e_id'];
        }
    }else{
        $kodenya=$_POST['e_id'];
    }
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $nobrid="51";
    $coadet = getfieldcnit("select COA4 as lcfields from dbmaster.posting_coa_rutin where divisi='$pdivprodid' AND nobrid='$nobrid'");
    
    
    
    if (empty(trim($kodenya))) { echo "kode kosong"; exit; }
    //if (empty(trim($pdivprodid))) { echo "divisi kosong"; exit; }
    //if (empty(trim($pareaid))) { echo "area kosong"; exit; }
    if (empty(trim($pp01))) { echo "periode kosong"; exit; }
    
    
    if ($act=='input') {
        $query="insert into $dbname.t_sewa (idsewa, karyawanid, icabangid, areaid, tgl, nobrid, kode)values"
                . "('$kodenya', '$pkaryawan', '$pidcabang', '$pareaid', Current_Date(), '$nobrid', 4)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
    
    
    $query = "update $dbname.t_sewa set karyawanid='$pkaryawan', "
             . " icabangid='$pidcabang', "
             . " areaid='$pareaid', "
             . " tglmulai='$pp01', "
             . " tglakhir='$pp02', "
             . " keterangan='$pket', "							 
             . " jabatanid='$pjabatanid', "	
             . " divisi='$pdivprodid', "
             . " jumlah='$pjumlah', "
             . " periode='$pjmlblnasli', "
             . " nobrid='$nobrid', "
             . " KODEWILAYAH='$pwilgabungan', "
             . " COA4='$coadet', "
             . " atasan1='$patasan1', "
             . " atasan2='$patasan2', "
             . " atasan3='$patasan3', "
             . " atasan4='$patasan4', "
             . " idca='$idca', "
             . " userid='$_SESSION[IDCARD]' where "
            . " idsewa='$kodenya'"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_query($cnmy, "delete from $dbname.t_sewa where idsewa='$kodenya'"); echo $erropesan; exit; }
    
    $query = "";
    if ($pelevel=="FF2") {
        $query = "update $dbname.t_sewa set atasan1='$pkaryawan', tgl_atasan1=NOW() WHERE "
                . " idsewa='$kodenya'";
    }elseif ($pelevel=="FF3") {
        $query = "update $dbname.t_sewa set atasan1='$pkaryawan', tgl_atasan1=NOW(), atasan2='$pkaryawan', tgl_atasan2=NOW() WHERE "
                . " idsewa='$kodenya'";
    }elseif ($pelevel=="FF4") {
        $query = "update $dbname.t_sewa set atasan3='$pkaryawan', tgl_atasan3=NOW(), "
                . " atasan1='$pkaryawan', tgl_atasan1=NOW(), atasan2='$pkaryawan', tgl_atasan2=NOW() WHERE idsewa='$kodenya'";
    }else{
        $nolevel=0;
        if (trim(substr($pelevel, 0, 2)=="FF")) {
            if (!empty(substr($pelevel, 2, 2))) {
                $nolevel=(int)substr($pelevel, 2, 2);
                if ($nolevel>4) {
                    $query = "update $dbname.t_sewa set atasan4='$pkaryawan', atasan3='$pkaryawan', tgl_atasan3=NOW(), "
                            . " atasan1='$pkaryawan', tgl_atasan1=NOW(), atasan2='$pkaryawan', tgl_atasan2=NOW() WHERE idsewa='$kodenya'";
                }
            }
        }
    }
    if (!empty($query)) {
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
    
    if (trim($pdivprodid)=="OTC") {
        $query = "update $dbname.t_sewa set divi='OTC', icabangid_o='$pidcabang', areaid_o='$pareaid' where idsewa='$kodenya'";
        mysqli_query($cnmy, $query);
    }
    
    if ( (int)$pjabatanid==38) {
        $query = "update $dbname.t_sewa set atasan1='$patasan2', tgl_atasan1=NOW() where idsewa='$kodenya'";
        mysqli_query($cnmy, $query);
        
        
        $query = "SELECT distinct karyawanid, gsm FROM dbmaster.t_karyawan_app_gsm where karyawanid='$pkaryawan'";
        $ketemu= mysqli_num_rows(mysqli_query($cnmy, $query));
        if ($ketemu>0) {
            $ats= mysqli_fetch_array(mysqli_query($cnmy, $query));
            $atasangsm=$ats["gsm"];
            $query = "update $dbname.t_sewa set atasan1='', tgl_atasan1=NOW(),"
                    . "atasan2='', tgl_atasan2=NOW(), atasan3='', tgl_atasan3=NOW(), atasan4='$atasangsm' WHERE idsewa='$kodenya'";
            mysqli_query($cnmy, $query);
        }
        
    }
    
    
    //MR jika SPV/AM nya NN
    if ((int)$pjabatanid==15) {
        $query = "SELECT distinct karyawanid FROM dbmaster.t_karyawan_apv where status='SPV' and karyawanid='$patasan1'";
        $ketemu= mysqli_num_rows(mysqli_query($cnmy, $query));
        if ($ketemu>0) {
            $query = "update $dbname.t_sewa set tgl_atasan1=NOW() WHERE idsewa='$kodenya'";
            mysqli_query($cnmy, $query);
        }
    }
    
    
    //AM/SPV jika DM nya NN
    if ((int)$pjabatanid==10 OR (int)$pjabatanid==18) {
        $query = "SELECT distinct karyawanid FROM dbmaster.t_karyawan_apv where status='DM' and karyawanid='$patasan2'";
        $ketemu= mysqli_num_rows(mysqli_query($cnmy, $query));
        if ($ketemu>0) {
            $query = "update $dbname.t_sewa set tgl_atasan2=NOW() WHERE idsewa='$kodenya'";
            mysqli_query($cnmy, $query);
        }
    }
    
    
    //TAMBAHAN DETAIL SEWA
    mysqli_query($cnmy, "DELETE FROM $dbname.t_sewa1 WHERE idsewa='$kodenya'");
    $mydate=date("Y-m-01", strtotime($date1));;
    $pday =  date("d", strtotime($date1));
    $pday2 =  date("t", strtotime($date1));
    $pmonth =  date("m", strtotime($date1));
    $nojml=0;
    while (strtotime($mydate) <= strtotime($pp02)) {
        if ((int)$nojml <= (int)$pjmlbln) {
            if (($pday==$pday2) AND ((int)$pmonth<>2)) {
                $thari=date("t", strtotime($mydate));
            }else{
                $tbulan = date("m", strtotime($mydate));
                if ((int)$tbulan==2 and (int)$pday>28)
                    $thari="28";
                else
                    $thari=$pday;
            }
            $mybln =  date("Y-m-".$thari, strtotime($mydate));

            //EXSEKUSI
            $query = "INSERT INTO $dbname.t_sewa1 (idsewa, tgl, rp)values('$kodenya', '$mybln', '$rpbulan')";
            mysqli_query($cnmy, $query);
        }
        
        $nojml++;
        $mydate = date ("Y-m-d", strtotime("+1 month", strtotime($mydate)));
    }
    
        
    if ($act=="input")
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
    else
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
    
}
    
?>
