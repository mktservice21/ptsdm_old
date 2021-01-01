<?php
session_start();

$_SESSION['FOLDERGL']="mysdm";
include "$_SESSION[FOLDERGL]/config/koneksimysqli.php";
include "$_SESSION[FOLDERGL]/config/koneksimysqli_it.php";
include "$_SESSION[FOLDERGL]/config/fungsi_sql.php";

$username   = $_POST['t_email'];
$pass       = $_POST['t_pass'];



$masukpass=0;
$ketemuuser=0;
$ketemumysql=0;
$fromuser=mysqli_query($cnmy, "SELECT * FROM dbmaster.sdm_users WHERE USERNAME='$username'");
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
$ketemuuser=mysqli_num_rows($fromuser);
if ($ketemuuser>0) {
    $ketemuuser=0;
    $rx=mysqli_fetch_array($fromuser);
    $idkaryawan=$rx['karyawanId'];
    if (!empty($rx['PASSWORD'])){
        include "$_SESSION[FOLDERGL]/config/encriptpassword.php";
        $tglnya=$rx['CREATEDPW'];

        $thn=substr($tglnya,0,4);
        $bln=substr($tglnya,5,2);
        $tgl=substr($tglnya,8,2);
        $tanggal=$thn.$bln.$tgl;

        $password   = encriptpasswordSSQl($pass, $tanggal);
        $masukpass=1;
    }
    
    if ($masukpass==1){
        $masukpass=0;
        $fromuser=mysqli_query($cnmy, "SELECT * FROM dbmaster.sdm_users WHERE USERNAME='$username' and PASSWORD='$password'");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        $ketemuuser=mysqli_num_rows($fromuser);
        if ($ketemuuser>0) {
            $zz=mysqli_fetch_array($fromuser);
            $masukpass=1;
        }
    }
    $username=$idkaryawan;
}

$karyawankhusus=false;
if ($masukpass==1){
    $sql=mysqli_query($cnmy, "SELECT * FROM dbmaster.t_karyawan_khusus WHERE karyawanId='$username' AND aktif<>'N'");
    $ketemukhusus=mysqli_num_rows($sql);
    if ($ketemukhusus==1) $karyawankhusus=true;
}

if ($karyawankhusus==false) {
	if ($masukpass==1){
		$sql=mysqli_query($cnit, "SELECT * FROM hrd.karyawan WHERE karyawanId=$username AND aktif<>'N'");
	}else{
		$sql=mysqli_query($cnit, "SELECT * FROM hrd.karyawan WHERE karyawanid=$username and pin='$pass' AND aktif<>'N'");
	}
	$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
}

if (!empty($sql))
    $ketemumysql=mysqli_num_rows($sql);
else
    $ketemumysql=0;


if ($ketemumysql > 0){
    
    $r=mysqli_fetch_array($sql);
    include "timeout.php";
    
    $_SESSION['NAMAPT'] = "PT. Surya Dermato Medica";
    $_SESSION['EMAIL']=$username;
    $_SESSION['IDCARD']=$r['karyawanId'];
    $_SESSION['USERID']=(int)$r['karyawanId'];
    $_SESSION['USERNAME']=(int)$r['karyawanId'];
    $_SESSION['NAMALENGKAP']=$r['nama'];
    
    $periodemasuk="2018-07-02";
    if (!empty($r['tglmasuk']) OR $r['tglmasuk']<>"0000-00-00")
        $periodemasuk= date("d-m-Y", strtotime($r['tglmasuk']));
    $_SESSION['MEMBERSEJAK']=$periodemasuk;
    $_SESSION['DIVISI']=$r['divisiId'];
    if (empty($_SESSION['DIVISI'])) $_SESSION['DIVISI']="HO";
    $_SESSION['JABATANID'] = $r['jabatanId'];
    
    $_SESSION['ATASANID']=$r['atasanId'];
    $t = mysqli_fetch_array(mysqli_query($cnit, "select nama from hrd.karyawan where karyawanId='$_SESSION[ATASANID]'"));
    $_SESSION['NAMAATASAN']=$t['nama'];
    
    $carijabatan = mysqli_query($cnit, "select jabatanId, nama nama_jabatan, rank from hrd.jabatan WHERE jabatanId='$_SESSION[JABATANID]'");
    $jb = mysqli_fetch_array($carijabatan);
    $_SESSION['JABATANNM']=$jb['nama_jabatan'];
    $_SESSION['JABATANRANK']=(int)$jb['rank'];
    
    $carijabatanlvl = mysqli_query($cnit, "select jabatanId, LEVELPOSISI, ID_GROUP from dbmaster.jabatan_level WHERE jabatanId='$_SESSION[JABATANID]'");
    $jl = mysqli_fetch_array($carijabatanlvl);
    
    $_SESSION['GROUP']=$jl['ID_GROUP'];
    
    $_SESSION['LVLPOSISI']=trim($jl['LEVELPOSISI']);
    if (empty($_SESSION['LVLPOSISI'])) $_SESSION['LVLPOSISI']="HO1";
    
    
    $_SESSION['IDCABANG']="";
    $_SESSION['NMCABANG']="";
    $_SESSION['IDAREA']="";
    $_SESSION['NMAREA']="";
    $_SESSION['REGION']="";
    $_SESSION['CABID']="";
    
    $cari = "select * from dbmaster.t_karyawan_posisi WHERE karyawanId='$_SESSION[IDCARD]'";
    $tampil = mysqli_query($cnit, $cari);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    $ketemu = mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $j= mysqli_fetch_array($tampil);
        
        $_SESSION['JABATANID'] = $j['jabatanId'];
        
        $carijabatan = mysqli_query($cnit, "select jabatanId, nama nama_jabatan, rank from hrd.jabatan WHERE jabatanId='$_SESSION[JABATANID]'");
        $jb = mysqli_fetch_array($carijabatan);
        $_SESSION['JABATANNM']=$jb['nama_jabatan'];
        $_SESSION['JABATANRANK']=(int)$jb['rank'];
        
        $carijabatanlvl = mysqli_query($cnit, "select jabatanId, LEVELPOSISI, ID_GROUP from dbmaster.jabatan_level WHERE jabatanId='$_SESSION[JABATANID]'");
        $jl = mysqli_fetch_array($carijabatanlvl);
        $_SESSION['GROUP']=$jl['ID_GROUP'];
        
        
        $_SESSION['LVLPOSISI']=trim($jl['LEVELPOSISI']);
        if (empty($_SESSION['LVLPOSISI'])) $_SESSION['LVLPOSISI']="HO1";
        
        $_SESSION['DIVISI']=$j['divisiId'];
        if (empty($_SESSION['DIVISI'])) $_SESSION['DIVISI']="HO";
        
        $_SESSION['IDCABANG']=$j['iCabangId'];
        $_SESSION['IDAREA']=$j['areaId'];
        
        if ($_SESSION['DIVISI']=="OTC") {
            $t = mysqli_fetch_array(mysqli_query($cnit, "select nama from MKT.iarea_o where icabangid_o='$j[iCabangId]' and areaid_o='$j[areaId]'"));
            $_SESSION['NMAREA'] = $t['nama'];
            $t = mysqli_fetch_array(mysqli_query($cnit, "select nama from MKT.icabang_o where icabangid_o='$j[iCabangId]'"));
            $_SESSION['NMCABANG'] = $t['nama'];
        }else{
            $t = mysqli_fetch_array(mysqli_query($cnit, "select Nama as nama from MKT.iarea where iCabangId='$j[iCabangId]' and areaId='$j[areaId]'"));
            $_SESSION['NMAREA'] = $t['nama'];
            $t = mysqli_fetch_array(mysqli_query($cnit, "select nama from MKT.icabang where iCabangId='$j[iCabangId]'"));
            $_SESSION['NMCABANG'] = $t['nama'];
        }
        
        $_SESSION['REGION']=$j['region'];
        $_SESSION['CABID']=$j['IDCAB'];
        if ($_SESSION['CABID']==0) $_SESSION['CABID'] = "";
        $_SESSION['ATASANID']=$j['atasanId'];
        $t = mysqli_fetch_array(mysqli_query($cnit, "select nama from hrd.karyawan where karyawanId='$_SESSION[ATASANID]'"));
        $_SESSION['NAMAATASAN']=$t['nama'];
        
    }
    
    
    $_SESSION['UKHUSUS']="N";
    $_SESSION['LEVELUSER'] = "guest";
    $_SESSION['STSADMIN']="guest";
    
    $_SESSION['ADMINKHUSUS']="N";
    $_SESSION['KHUSUSSEL']="";
    
    
    $_SESSION['FOTOKU']="";

    //$_SESSION['S_TAMBAH']="";
    //$_SESSION['S_EDIT']="";
    //$_SESSION['S_HAPUS']="";
    
    
    $query=mysqli_query($cnmy, "SELECT * FROM dbmaster.sdm_users WHERE karyawanId=$username");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    $ketemu=mysqli_num_rows($query);
    if ($ketemu>0) {
        $rx=mysqli_fetch_array($query);
        
        if (!empty($rx['ID_GROUP'])) $_SESSION['GROUP']=$rx['ID_GROUP'];
        $_SESSION['USERNAME']=$rx['USERNAME'];
        $_SESSION['STSADMIN']=$rx['LEVEL'];
        $_SESSION['LEVELUSER'] = $rx['LEVEL'];
        if (!empty($rx['AKHUSUS']))
            $_SESSION['ADMINKHUSUS']=$rx['AKHUSUS'];
        
        
        if ($_SESSION['ADMINKHUSUS']=="Y"){
            $query="select DivProdId from dbmaster.sdm_users_khusus where karyawanId='$_SESSION[IDCARD]'";
            $sql=  mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            $ketemu=  mysqli_num_rows($sql);
            if ($ketemu>0) {
                $divnya="";
                while ($k=  mysqli_fetch_array($sql)) {
                    $divnya=$divnya."'".$k['DivProdId']."',";
                }
                if (!empty($divnya)) {
                    $divnya="(".substr($divnya, 0, -1).")";
                    $_SESSION['KHUSUSSEL']=$divnya;
                }
            }
        }
        
        //$_SESSION['FOTOKU']=$r['GAMBAR'];

        
    }
    
    include "$_SESSION[FOLDERGL]/config/mobile.php";
    if(mobile_device_detect(true,true,true,true,false,false)){
        $_SESSION['MOBILE']="Y";
    }else{
        $_SESSION['MOBILE']="N";
    }
    
    $_SESSION['DIVISIKHUSUS']="N";
    if ($_SESSION['IDCARD']=="0000000432") {
        $_SESSION['DIVISIKHUSUS']="Y";
        $_SESSION['DIVISI'] = "HO";
    }
    
    $_SESSION['AKSES_JABATAN']="";
    $_SESSION['AKSES_REGION']="";
    
    if ($_SESSION['GROUP']!="") {
        $cari = "select * from dbmaster.sdm_aksesbygroupuser WHERE ID_GROUP=$_SESSION[GROUP]";
        $tampil = mysqli_query($cnmy, $cari);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        $ketemu = mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $j= mysqli_fetch_array($tampil);
            if (!empty($j['JABATANID'])) {
                $_SESSION['AKSES_JABATAN']=$j['JABATANID'];
            }

            if (!empty($j['REGION']))
                $_SESSION['AKSES_REGION']=$j['REGION'];
        }
    }
    
    $_SESSION['AKSES_CABANG'] = "";
    $cari = "select c.IDCAB, o.ICABANGID from dbmaster.sdm_aksesbycabang as c 
            JOIN dbmaster.sdm_admincabang_on as o on c.IDCAB=o.IDCAB WHERE KARYAWANID='$_SESSION[IDCARD]'";
    $tampil = mysqli_query($cnmy, $cari);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    $ketemu = mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $aksescab = "";
        while ($j= mysqli_fetch_array($tampil)) {
            if (!empty($j['ICABANGID']))
                $aksescab=$aksescab."'".$j['ICABANGID']."',";
        }
        if (!empty($aksescab)) {
            $aksescab = substr($aksescab, 0, -1);
            $_SESSION['AKSES_CABANG']=$aksescab;
        }
    }
	
    $_SESSION['JMLRECSPD']=30;
    $cari = "select IFNULL(jmlrec_spd,0) jmlrec_spd from dbmaster.t_setup WHERE IFNULL(jmlrec_spd,0) <> 0 LIMIT 1";
    $tampilz = mysqli_query($cnmy, $cari);
    $ketemu = mysqli_num_rows($tampilz);
    if ($ketemu>0) {
        $jz= mysqli_fetch_array($tampilz);
        if (!empty($jz['jmlrec_spd'])) {
            $_SESSION['JMLRECSPD']=$jz['jmlrec_spd'];
        }
    }
	
	$_SESSION['KRYNONE']="0000002083";
    
    timer();

    $sid_lama = session_id();
    session_regenerate_id();
    $sid_baru = session_id();
    $_SESSION['IDSESI']=$sid_baru;
    

    mysqli_query($cnmy, "insert into dbmaster.sdm_users_log (KARYAWANID, SESSION_ID, AKTIF)values('$_SESSION[IDCARD]', '$sid_baru', 'Y')");
    mysqli_query($cnmy, "UPDATE dbmaster.sdm_users SET ID_SESSION='$sid_baru', ONLINE='Y' WHERE (karyawanId='$username' or USERNAME='$username')");
    
    mysqli_close($cnmy);
    mysqli_close($cnit);
	
    header('location:'.$_SESSION['FOLDERGL'].'/media.php?module=home&users='.$sid_baru);
}else{
    echo "<script>alert('user atau password anda tidak terdaftar'); window.location = 'index.php'</script>";
}

?>