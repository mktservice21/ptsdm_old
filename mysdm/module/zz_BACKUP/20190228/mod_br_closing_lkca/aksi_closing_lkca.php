<?php

session_start();
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
        
        if ($psudahpernah!="SUDAH") {
            
            $query = "INSERT INTO dbmaster.t_brrutin_ca_close (tglinput, bulan, idrutin, idca1, idca2, userid)
                select CURRENT_DATE() tglinput, '' bulan, idrutin, idca1, idca2, '$_SESSION[IDCARD]' userid FROM 
                dbmaster.tmp_lk_closing WHERE idsession='$_SESSION[IDSESI]' and userid='$_SESSION[IDCARD]' AND idrutin NOT IN"
                    . " (select distinct IFNULL(idrutin,'') FROM dbmaster.t_brrutin_ca_close)";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }
        
        $query = "UPDATE dbmaster.t_brrutin0 SET $uptgltrans, nobukti='$pbukti' WHERE idrutin IN ("
                . "SELECT distinct IFNULL(idrutin,'') FROM dbmaster.tmp_lk_closing WHERE nourut IN $pnourt)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        $berhasil="Berhasil disimpan";
    }
}elseif ($module=='closingbrlkca' AND $act=='hapus') {
    
}
echo $berhasil;
?>

