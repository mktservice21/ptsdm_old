<?php
session_start();

    date_default_timezone_set('Asia/Jakarta');
    
$puserid="";
if (isset($_SESSION['USERID'])) $puserid=$_SESSION['IDCARD'];

if (empty($puserid)) {
    echo "ANDA HARUS LOGIN ULANG...";
    exit;
}


$pmodule=$_GET['module'];

$berhasil="Tidak ada data yang disimpan...";
if ($pmodule=="simpandataotsbr") {
    $ptahun=$_POST['utahun'];
    $pdivisiid=$_POST['udivisi'];
    $pjmlrp=$_POST['ujmlrp'];
    if (empty($pjmlrp)) $pjmlrp=0;
    
    $pjmlrp=str_replace(",","", $pjmlrp);
    
    if (Empty($ptahun)) {
        echo "Periode Kosong...";
        exit;
    }
    
    include "../../../config/koneksimysqli.php";
    
    $query = "DELETE FROM dbmaster.t_outstanding_br WHERE tahun='$ptahun' AND divisi='$pdivisiid'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    
    $query ="ALTER TABLE dbmaster.t_outstanding_br AUTO_INCREMENT = 1";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    
    $query = "INSERT INTO dbmaster.t_outstanding_br (tahun,divisi,jml_ots,userid)values('$ptahun', '$pdivisiid', '$pjmlrp', '$puserid')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    
    mysqli_close($cnmy);
    $berhasil="berhasil disimpan...";
}

echo "$berhasil";
?>
