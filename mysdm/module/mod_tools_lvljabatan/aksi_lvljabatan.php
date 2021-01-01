<?php
session_start();
    ini_set("memory_limit","1024M");
    ini_set('max_execution_time', 0);
    
include "../../config/koneksimysqli_it.php";
$cnmy=$cnit;

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];


// Hapus employee
if ($module=='employee' AND $act=='hapus'){

}

// Input modul
else{
    mysqli_query($cnmy, "delete from dbmaster.karyawan_level where karyawanId in "
            . "(select distinct karyawanId from dbmaster.v_karyawan where jabatanId   = '$_POST[e_id]')");
    
    $cari=mysqli_query($cnmy, "select * from dbmaster.jabatan_level WHERE jabatanId   = '$_POST[e_id]'");
    $ketemu=mysqli_num_rows($cari);
    if ($ketemu==0){
        mysqli_query($cnmy, "insert into dbmaster.jabatan_level(jabatanId)values('$_POST[e_id]')");
    }
    
    $ssql="UPDATE dbmaster.jabatan_level SET LEVELPOSISI = '$_POST[e_level]' WHERE jabatanId   = '$_POST[e_id]'";
    mysqli_query($cnmy, $ssql);
    
    $level=  substr($_POST['e_level'], 2,1);
    //echo $level;
    //exit;
    /*
    $ssql="select distinct karyawanId, atasanId from dbmaster.v_karyawan where LEVELPOSISI = '$_POST[e_level]'";
    while ($r=  mysqli_fetch_array(mysqli_query($cnmy, $ssql))) {
        mysqli_query($cnmy, "insert into dbmaster.karyawan_level(karyawanId)values('$r[karyawanId]')");
    }
     * 
     */
    
    header('location:../../media.php?module='.$module.'&idmenu='.'&idmenu='.$idmenu.'&act=complt');

}
?>
