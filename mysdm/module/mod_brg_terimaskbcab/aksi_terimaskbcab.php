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


$pidcard=$_SESSION['IDCARD'];
$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];


if ($module=='gimicterimaskbcab' AND $act="isiterima")
{
    include "../../config/koneksimysqli.php";
    $pidkaryawan=$_SESSION['IDCARD'];
    $pkodenya=$_POST['e_id'];
    $pnmpernerima=$_POST['e_nmpenerima'];
    $ptgl=$_POST['e_tglberlaku'];
    
    $ptglterima="";
    if (!empty($ptgl)) {
        $ptglterima = date('Y-m-d', strtotime($ptgl));
    }
    
    
    echo "$pidkaryawan, $pkodenya, $pnmpernerima, $ptglterima";
    
    if (empty($ptglterima)) {
        $query = "UPDATE dbmaster.t_barang_keluar_kirim SET KARYAWANTERIMA=NULL, NAMA_KARYAWAN=NULL, TGLTERIMA=NULL WHERE IDKELUAR='$pkodenya'";
    }else{
        $query = "UPDATE dbmaster.t_barang_keluar_kirim SET KARYAWANTERIMA='$pidkaryawan', NAMA_KARYAWAN='$pnmpernerima', TGLTERIMA='$ptglterima' WHERE IDKELUAR='$pkodenya'";
    }
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

    mysqli_close($cnmy);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=sudahsimpan');
    
}