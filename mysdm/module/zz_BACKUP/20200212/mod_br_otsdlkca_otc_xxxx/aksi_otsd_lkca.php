<?php

session_start();
include "../../config/koneksimysqli.php";
$cnmy=$cnmy;
$dbname = "dbmaster";

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];



$pbulan=$_POST['ubln'];
$pidkry=$_POST['ukry'];
$pdivisi=$_POST['udiv'];
$psaldo=$_POST['usaldo'];
$pca=$_POST['uca'];
$pselisih=$_POST['uselisih'];
$pkembali_rp=$_POST['ukembali'];
$ptgl=$_POST['utgl'];
$pstatus=$_POST['usts'];
$nstsjenis="1";//dimasukan ke ots dulu statusnya $pstatus ada hubungannya
$pket=$_POST['uketerangan'];
$pnamakaryawan=$_POST['unamakaryawan'];


if (empty($psaldo)) $psaldo=0;
if (empty($pca)) $pca=0;
if (empty($pselisih)) $pselisih=0;
if (empty($pkembali_rp)) $pkembali_rp=0;

$pblnnya= date("Y-m-01", strtotime($pbulan));
$pblnthn= date("Ym", strtotime($pbulan));
$ptgl_kembali="0000-00-00";
if (!empty($ptgl)) $ptgl_kembali= date("Y-m-d", strtotime($ptgl));

$psaldo=str_replace(",","", $psaldo);
$pca=str_replace(",","", $pca);
$pselisih=str_replace(",","", $pselisih);
$pkembali_rp=str_replace(",","", $pkembali_rp);


$plebih_rp=(double)$pkembali_rp-(double)$pselisih;
if (empty($plebih_rp)) $plebih_rp=0;

$prp_kembali=$pkembali_rp;
if ((double)$plebih_rp>0){
    //$prp_kembali=$pselisih; //simpan disesuaikan dengan selisih, sisa di entry kembali ke coa berbeda
}

$pcoa_1="105-02"; //uang muka
$pcoa_2="905-02"; //pembulatan


$berhasil="Tidak ada data yang disimpan";

if ($act=='input') {
    //$berhasil="$pblnnya, $pidkry, $pdivisi, $psaldo, $pca, $pselisih, $pkembali_rp, $ptgl_kembali, $pstatus : $nstsjenis, $pket, lebih : $plebih_rp";
    
    $query = "DELETE FROM $dbname.t_brrutin_outstanding WHERE DATE_FORMAT(bulan,'%Y%m')='$pblnthn' AND karyawanid='$pidkry'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Delete"; exit; }
    
    $query="INSERT INTO $dbname.t_brrutin_outstanding (tglinput, bulan, karyawanid, divisi, saldo, ca, selisih, jumlah_kembali, kembali_rp, tgl_kembali, ots_status, keterangan, coa, userid, nama_karyawan)VALUES"
            . "(CURRENT_DATE(), '$pblnnya' ,'$pidkry', '$pdivisi', '$psaldo', '$pca', '$pselisih', '$pkembali_rp', '$prp_kembali', '$ptgl_kembali', '$nstsjenis', '$pket', '$pcoa_1', '$_SESSION[IDCARD]', '$pnamakaryawan')";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Simpan"; exit; }
    
    if ((double)$plebih_rp<>0){
        $nstsjenis=$pstatus; // di ubah jadi adjusment, 1 = outstanding
        if ($pstatus=="4") $pcoa_2=$pcoa_1;
        
        $query="INSERT INTO $dbname.t_brrutin_outstanding (tglinput, bulan, karyawanid, divisi, saldo, ca, selisih, jumlah_kembali, kembali_rp, tgl_kembali, ots_status, keterangan, coa, userid, nama_karyawan)VALUES"
                . "(CURRENT_DATE(), '$pblnnya' ,'$pidkry', '$pdivisi', '$psaldo', '$pca', '$pselisih', '$pkembali_rp', '$plebih_rp', '$ptgl_kembali', '$nstsjenis', '$pket', '$pcoa_2', '$_SESSION[IDCARD]', '$pnamakaryawan')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Simpan Lebih / Kurang"; exit; }
        
    }
    
    mysqli_close($cnmy);
    
    $berhasil="";
    
}elseif ($act=='hapus') {
    
    $query = "DELETE FROM $dbname.t_brrutin_outstanding WHERE DATE_FORMAT(bulan,'%Y%m')='$pblnthn' AND karyawanid='$pidkry'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Delete"; exit; }
    
    $pidca=$_POST['uidca'];
    if (!empty($pidca)) {
        mysqli_query($cnmy, "update $dbname.t_ca0 set batal='N', alasan_batal='' WHERE idca='$pidca' AND IFNULL(batal,'')='Y'");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error UPDATE BATAL"; exit; }
    }
    
    mysqli_close($cnmy);
    
    $berhasil="";
}elseif ($act=='batal') {    
    $pidca=$_POST['uidca'];
    
    $nstsjenis="3";
    $prp_kembali=0;
    
    
    if ($ptgl_kembali=="0000-00-00") $ptgl_kembali = date("Y-m-d");
    
    $query = "DELETE FROM $dbname.t_brrutin_outstanding WHERE DATE_FORMAT(bulan,'%Y%m')='$pblnthn' AND karyawanid='$pidkry'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Delete"; exit; }
    
    $query="INSERT INTO $dbname.t_brrutin_outstanding (tglinput, bulan, karyawanid, divisi, saldo, ca, selisih, jumlah_kembali, kembali_rp, tgl_kembali, ots_status, keterangan, coa, userid, nama_karyawan)VALUES"
            . "(CURRENT_DATE(), '$pblnnya' ,'$pidkry', '$pdivisi', '$psaldo', '$pca', '$pselisih', '$pkembali_rp', '$prp_kembali', '$ptgl_kembali', '$nstsjenis', '$pket', '$pcoa_1', '$_SESSION[IDCARD]', '$pnamakaryawan')";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Simpan"; exit; }
    
    $ketbatal= $_POST['uketbatal'];
    if ($ketbatal=="null") $ketbatal="";
    if (!empty($ketbatal)) $ketbatal = str_replace("'", " ", $ketbatal);
    
    mysqli_query($cnmy, "update $dbname.t_ca0 set batal='Y', alasan_batal='$ketbatal' WHERE idca='$pidca'");
    
    mysqli_close($cnmy);
    
    $berhasil="";
}

echo $berhasil;


?>

