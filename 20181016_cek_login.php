<?php
session_start();
$_SESSION['FOLDERGL']="mysdm";
include "$_SESSION[FOLDERGL]/config/koneksimysqli.php";
include "$_SESSION[FOLDERGL]/config/fungsi_sql.php";

$username   = $_POST['t_email'];
$pass       = $_POST['t_pass'];


//khusus
$tampil=mysqli_query($cnmy, "SELECT * FROM dbmaster.sdm_users_guest WHERE PASSWORD='$pass' AND USERNAME='$username'");
$ketemuuserg=mysqli_num_rows($tampil);
if ($ketemuuserg>0) {
    $rx=mysqli_fetch_array($tampil);
    include "timeout.php";
    
    $_SESSION['GROUP']=$rx['ID_GROUP'];

    $_SESSION['EMAIL']=$username;
    $_SESSION['IDCARD']=$username;
    $_SESSION['USERNAME']=$username;
    $_SESSION['USERID']=$username;
    $_SESSION['NAMAPT'] = "PT. Surya Dermato Medica";
    
    $_SESSION['NAMALENGKAP']=$rx['NAMALENGKAP'];
    $_SESSION['MEMBERSEJAK']="2018-08-03";
    $_SESSION['UKHUSUS']=$rx['AKHUSUS'];
    $_SESSION['LEVELUSER'] = $rx['LEVEL'];
    $_SESSION['STSADMIN']=$rx['LEVEL'];
    
    $_SESSION['JABATANID'] = $rx['JABATANID'];
    $_SESSION['JABATANNM']=$rx['NAMAJABATAN'];
    
    $_SESSION['ADMINKHUSUS']=$rx['AKHUSUS'];
    
    $_SESSION['LVLPOSISI']=$rx['LVLPOSISI'];
    $_SESSION['DIVISI']=$rx['DIVISI'];
    
    $_SESSION['IDCABANG']="";
    $_SESSION['NMCABANG']="";
    $_SESSION['IDAREA']="";
    $_SESSION['NMAREA']="";
    
    include "$_SESSION[FOLDERGL]/config/mobile.php";
    if(mobile_device_detect(true,true,true,true,false,false)){
        $_SESSION['MOBILE']="Y";
    }else{
        $_SESSION['MOBILE']="N";
    }
    
    timer();

    $sid_lama = session_id();
    session_regenerate_id();
    $sid_baru = session_id();
    $_SESSION['IDSESI']=$sid_baru;
    
    mysqli_query($cnmy, "UPDATE dbmaster.sdm_users_guest SET ID_SESSION='$sid_baru', ONLINE='Y' WHERE USERNAME='$username'");
    
    header('location:'.$_SESSION['FOLDERGL'].'/media.php?module=home&users='.$sid_baru);
    exit;
}


$masukpass=0;
$fromuser=mysqli_query($cnmy, "SELECT * FROM dbmaster.sdm_users WHERE karyawanId='$username' or USERNAME='$username'");
$ketemuuser=mysqli_num_rows($fromuser);
if ($ketemuuser>0) {
    $rx=mysqli_fetch_array($fromuser);
    if (!empty($rx['PASSWORD'])){
        include "$_SESSION[FOLDERGL]/config/encriptpassword.php";
        $tglnya=$rx['CREATEDPW'];

        $thn=substr($tglnya,0,4);
        $bln=substr($tglnya,5,2);
        $tgl=substr($tglnya,8,2);
        $tanggal=$thn.$bln.$tgl;

        $pass   = encriptpasswordSSQl($pass, $tanggal);
        $masukpass=1;
    }
}

if ($masukpass==1){
    $sql=mysqli_query($cnmy, "SELECT * FROM dbmaster.v_karyawan WHERE (karyawanId='$username' or USERNAME='$username') and PASSWORD='$pass' and ifnull(AKTIF,'N')='Y'");
}else{
    $sql=mysqli_query($cnmy, "SELECT * FROM dbmaster.v_karyawan WHERE (karyawanId='$username' or USERNAME='$username') and pin='$pass' and ifnull(AKTIF,'N')='Y'");
}
$ketemumysql=mysqli_num_rows($sql);

if ($ketemumysql > 0){
    $r=mysqli_fetch_array($sql);

    include "timeout.php";

    $_SESSION['GROUP']=$rx['ID_GROUP'];

    $_SESSION['EMAIL']=$username;
    $_SESSION['IDCARD']=$r['karyawanId'];
    $_SESSION['USERID']=(int)$r['karyawanId'];
    $_SESSION['USERNAME']=$r['USERNAME'];
    //$_SESSION['GAMBARKU']=$r['GAMBAR'];
    //$_SESSION['FOTOKU']=$r['GAMBAR'];

    //$_SESSION['S_TAMBAH']=$r['TAMBAH'];
    //$_SESSION['S_EDIT']=$r['EDIT'];
    //$_SESSION['S_HAPUS']=$r['HAPUS'];

    $_SESSION['NAMAPT'] = "PT. Surya Dermato Medica";
    
    $_SESSION['NAMALENGKAP']=$r['nama'];
    $_SESSION['MEMBERSEJAK']="2018-07-02";
    $_SESSION['UKHUSUS']="N";
    $_SESSION['LEVELUSER'] = $r['LEVEL'];
    $_SESSION['STSADMIN']=$r['LEVEL'];
    
    $_SESSION['JABATANID'] = $r['jabatanId'];
    $_SESSION['JABATANNM']=$r['nama_jabatan'];
    
    $_SESSION['ADMINKHUSUS']="N";
    if (!empty($r['AKHUSUS']))
        $_SESSION['ADMINKHUSUS']=$r['AKHUSUS'];
    
    $_SESSION['KHUSUSSEL']="";
    if ($_SESSION['ADMINKHUSUS']=="Y"){
        $query="select DivProdId from dbmaster.sdm_users_khusus where karyawanId='$_SESSION[IDCARD]'";
        $sql=  mysqli_query($cnmy, $query);
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
    
    
    
    $_SESSION['LVLPOSISI']=$r['LEVELPOSISI'];
    $_SESSION['DIVISI']=$r['divisiId'];
    if (empty($_SESSION['LVLPOSISI'])) $_SESSION['LVLPOSISI']="HO1";
    if (empty($_SESSION['DIVISI'])) $_SESSION['DIVISI']="HO";
    
    $_SESSION['IDCABANG']="";
    $_SESSION['NMCABANG']="";
    $_SESSION['IDAREA']="";
    $_SESSION['NMAREA']="";
    
    
    $query="select iCabangId, nama from dbmaster.icabang where iCabangId in ('$r[iCabangId]', '$r[areaId]')";
    $sql=  mysqli_query($cnmy, $query);
    while ($ca=  mysqli_fetch_array($sql)) {
        if ($ca['iCabangId']==$r['iCabangId']){
            $_SESSION['IDCABANG']=$r['iCabangId'];
            $_SESSION['NMCABANG']=$r['nama'];
        }elseif ($ca['iCabangId']==$r['iCabangId']){
            $_SESSION['IDAREA']=$r['iCabangId'];
            $_SESSION['NMAREA']=$r['nama'];
        }
    }

    
    
    include "$_SESSION[FOLDERGL]/config/mobile.php";
    if(mobile_device_detect(true,true,true,true,false,false)){
        $_SESSION['MOBILE']="Y";
    }else{
        $_SESSION['MOBILE']="N";
    }
    
    timer();

    $sid_lama = session_id();
    session_regenerate_id();
    $sid_baru = session_id();
    $_SESSION['IDSESI']=$sid_baru;

    mysqli_query($cnmy, "UPDATE dbmaster.sdm_users SET ID_SESSION='$sid_baru', ONLINE='Y' WHERE (karyawanId='$username' or USERNAME='$username')");
    header('location:'.$_SESSION['FOLDERGL'].'/media.php?module=home&users='.$sid_baru);
}else{
    echo "<script>alert('user atau password anda tidak terdaftar'); window.location = 'index.php'</script>";
}

?>