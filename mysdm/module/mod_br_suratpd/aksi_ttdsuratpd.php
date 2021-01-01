<?php

session_start();
include "../../config/koneksimysqli.php";

$module=$_GET['module'];    
$brid=$_GET['brid'];    
$iprint=$_GET['iprint'];    
$act=$_GET['act'];


$karyawanapv=$_POST['ukaryawan'];
$noidbr=$_POST['unobr'];

$berhasi="Tidak ada data yang diproses.";

if ($module=="suratpd") {
    
    if (!empty($noidbr) AND !empty($karyawanapv)) {
        
        if ($act=="simpan_ttdallam") {
            $gbrapv=$_POST['uttd'];
            
            $query = "INSERT INTO dbmaster.t_suratdana_br_ttd (nomor, ttd1, gbr_ttd1, tgl_ttd1)VALUES"
                    . "('$noidbr', '$karyawanapv', '$gbrapv', NOW())";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            $berhasi="";
            
        }
        
    }
    
}

echo $berhasi;
?>