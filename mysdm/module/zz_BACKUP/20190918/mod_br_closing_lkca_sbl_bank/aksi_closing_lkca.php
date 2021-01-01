<?php
session_start();

    if (empty($_SESSION['IDCARD']) AND empty($_SESSION['NAMALENGKAP'])){
        echo "Belum ada data yang disimpan. Anda Harus Login Ulang."; exit;
    }
    
include "../../config/koneksimysqli.php";
$cnmy=$cnmy;
$dbname = "dbmaster";

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

$pnourt=$_POST['unourut'];
$ptrans=$_POST['utgltrans'];
$psudahpernah=$_POST['usudahpernah'];
$pbukti=$_POST['unobukti'];
if (!empty($pbukti)) $pbukti = str_replace("'", " ", $pbukti);
$ptglnya= date("Y-m-d", strtotime($ptrans));

$berhasil="Tidak ada data yang disimpan";
if ($module=='closingbrlkca' AND $act=='input') {
    if (!empty($pnourt)) {
        $uptgltrans= " tgltrans='$ptglnya' ";
        if (empty($ptrans)) $uptgltrans= " tgltrans=null ";
        
        //if ($psudahpernah!="SUDAH") {
            
            $query = "INSERT INTO dbmaster.t_brrutin_ca_close (tglinput, bulan, karyawanid, divisi, idrutin, idca1, idca2, credit, saldo, ca1, ca2, userid, sts, jml_adj)
                select CURRENT_DATE() tglinput, bulan, karyawanid, divisi, idrutin, idca1, idca2, jumlah, totalrutin, ca1, ca2, '$_SESSION[IDCARD]' userid, sts, jml_adj FROM 
                dbmaster.tmp_lk_closing WHERE idsession='$_SESSION[IDSESI]' and userid='$_SESSION[IDCARD]' AND nourut in $pnourt";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        //}
        
        $query = "UPDATE dbmaster.t_brrutin0 SET $uptgltrans, nobukti='$pbukti' WHERE idrutin IN ("
                . "SELECT distinct IFNULL(idrutin,'') FROM dbmaster.tmp_lk_closing WHERE nourut IN $pnourt)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        $berhasil="Berhasil disimpan";
    }
}elseif ($module=='closingbrlkca' AND $act=='hapus') {
    if (!empty($pnourt)) {
        
        $query = "UPDATE dbmaster.t_brrutin0 SET tgltrans=null, nobukti='' WHERE idrutin IN ("
                . "SELECT distinct IFNULL(idrutin,'') FROM dbmaster.t_brrutin_ca_close WHERE nourut IN $pnourt)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        $query = "DELETE FROM dbmaster.t_brrutin_ca_close WHERE nourut IN $pnourt";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

        $berhasil="Hapus Berhasil...";
    }
}elseif ($module=='closingbrlkca' AND $act=='simpan') {

    if (!empty($pnourt)) {
        $uptgltrans= " tgltrans='$ptglnya' ";
        if (empty($ptrans)) $uptgltrans= " tgltrans=null ";
        
        $query = "UPDATE dbmaster.t_brrutin_ca_close SET $uptgltrans, nobukti='$pbukti' WHERE nourut IN $pnourt";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query = "UPDATE dbmaster.t_brrutin0 SET $uptgltrans, nobukti='$pbukti' WHERE idrutin IN ("
                . "SELECT distinct IFNULL(idrutin,'') FROM dbmaster.t_brrutin_ca_close WHERE nourut IN $pnourt)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        $berhasil="Simpan Tgl Transfer dan No Bukti Berhasil...";
    }
}
echo $berhasil;
?>

