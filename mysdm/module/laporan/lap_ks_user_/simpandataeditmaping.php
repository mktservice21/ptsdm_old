<?php
date_default_timezone_set('Asia/Jakarta');
session_start();
include "../../../config/koneksimysqli.php";
$dbname = "dbmaster";

$puserid=$_SESSION['IDCARD'];
if (empty($puserid)) {
    echo "ANDA HARUS LOGIN ULANG...!!!";
    mysqli_close($cnmy);
    exit;
}

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

$berhasil="";
if ($module=='lapksmonituser' AND $act=='input') {
    
    $piddoktlama=$_POST['uiddokt'];
    $piddoktmaping=$_POST['uidokmaping'];
    
    //$berhasil = "$piddoktlama, $piddoktmaping";
    
    if (!empty($piddoktlama)) {
        $query = "DELETE FROM dr.dokter_mapping WHERE dokterid='$piddoktlama' LIMIT 1";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error hapus"; exit; }
    }
    
    if (!empty($piddoktlama) AND !empty($piddoktmaping)) {
        
        $query = "INSERT INTO dr.dokter_mapping (dokterid, dokterid_new)values('$piddoktlama', '$piddoktmaping')";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error tambah dokter"; exit; }
        
        $query = "select namalengkap as namalengkap, spesialis as spesialis from dr.masterdokter WHERE id='$piddoktmaping'";
        $tampil=mysqli_query($cnmy, $query);
        $ketemu=mysqli_num_rows($tampil);
        if ((INT)$ketemu>0) {
            $row=mysqli_fetch_array($tampil);
            $berhasil=$row['namalengkap'].", ".$row['spesialis'];
        }else{
            $berhasil="";
        }
        
        //$berhasil="berhasil";
        
    }
    
    
    
}
mysqli_close($cnmy);
echo $berhasil;
?>