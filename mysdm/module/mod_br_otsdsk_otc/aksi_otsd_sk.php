<?php

session_start();
include "../../config/koneksimysqli.php";
$cnmy=$cnmy;
$dbname = "dbmaster";

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];



$pidservice=$_POST['eidservice'];
$pjumlah=$_POST['ejumlah'];
$ptanggal=$_POST['etgl'];

$puserid=$_SESSION['IDCARD'];


if (empty($pjumlah)) $pjumlah=0;
$pjumlah=str_replace(",","", $pjumlah);

if (!empty($ptanggal)) $ptanggal= date("Y-m-d", strtotime($ptanggal));
else $ptanggal="0000-00-00";

$pcoa_1="105-02"; //uang muka

//$berhasil="$pidservice, $pjumlah, $ptanggal, $pcoa_1"; echo "$berhasil"; exit;


$berhasil="Tidak ada data yang disimpan";

if ($act=='input') {
    //$berhasil="$pblnnya, $pidkry, $pdivisi, $psaldo, $pca, $pselisih, $pkembali_rp, $ptgl_kembali, $pstatus : $nstsjenis, $pket, lebih : $plebih_rp";
    
    $query = "DELETE FROM $dbname.t_brrutin_outstanding_sk WHERE idservice='$pidservice'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Delete"; exit; }
    
    $query="INSERT INTO $dbname.t_brrutin_outstanding_sk (idservice, tglinput, tanggal, jumlah, coa, userid)VALUES"
            . "('$pidservice', CURRENT_DATE(), '$ptanggal' ,'$pjumlah', '$pcoa_1', '$puserid')";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Simpan"; exit; }
    
    mysqli_close($cnmy);
    
    $berhasil="";
    
}elseif ($act=='hapus') {
    
    $query = "DELETE FROM $dbname.t_brrutin_outstanding_sk WHERE idservice='$pidservice'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Delete"; exit; }
    
    
    mysqli_close($cnmy);
    
    $berhasil="";
}elseif ($act=='batal') {    
    
    
    $berhasil="batal";
}

echo $berhasil;


?>

