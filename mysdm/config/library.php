<?php
include "koneksimysqli.php";
date_default_timezone_set('Asia/Jakarta'); // PHP 6 mengharuskan penyebutan timezone.
$seminggu = array("Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu");
$hari = date("w");
$hari_ini = $seminggu[$hari];
$tg=mysqli_query($cnmy, "select current_date as tanggal");
$tgl=mysqli_fetch_array($tg);
$ctgl=$tgl['tanggal'];

$tgl_sekarang = date("Ymd");
$tgl_skrg     = date("d");
$bln_sekarang = date("m");
$bln_sekarang_all = date("F");
$thn_sekarang = date("Y");
$jam_sekarang = date("H:i:s");

$nama_bln=array(1=> "Januari", "Februari", "Maret", "April", "Mei", 
                    "Juni", "Juli", "Agustus", "September", 
                    "Oktober", "November", "Desember");

function cariHurufBulan($ttbln) {
    if (strval($ttbln)==1) {
        $tbln="A";
    }elseif (strval($ttbln)==2) {
        $tbln="B";
    }elseif (strval($ttbln)==3) {
        $tbln="C";
    }elseif (strval($ttbln)==4) {
        $tbln="D";
    }elseif (strval($ttbln)==5) {
        $tbln="E";
    }elseif (strval($ttbln)==6) {
        $tbln="F";
    }elseif (strval($ttbln)==7) {
        $tbln="G";
    }elseif (strval($ttbln)==8) {
        $tbln="H";
    }elseif (strval($ttbln)==9) {
        $tbln="I";
    }elseif (strval($ttbln)==10) {
        $tbln="J";
    }elseif (strval($ttbln)==11) {
        $tbln="K";
    }elseif (strval($ttbln)==12) {
        $tbln="L";
    }else{
        $tbln="M";
    }
    return $tbln;
}

function cariTBT($tgl_aing, $skey) {
    $data_aing=str_replace("/", "", $tgl_aing);
    if($skey==1){
        $data_aing=substr($data_aing, 2, 2);//tanggal
    }elseif($skey==2){
        $data_aing=substr($data_aing, 0, 2);//bulan
    }else{
        $data_aing=substr($data_aing, 4, 4);//tahun
    }
    return $data_aing;
}

function cariTBT2($tgl_aing, $skey) {
    $data_aing=str_replace("-", "", $tgl_aing);
    if($skey==1){
        $data_aing=substr($data_aing, 6, 2);//tanggal
    }elseif($skey==2){
        $data_aing=substr($data_aing, 4, 2);//bulan
    }else{
        $data_aing=substr($data_aing, 0, 4);//tahun
    }
    return $data_aing;
}
?>
