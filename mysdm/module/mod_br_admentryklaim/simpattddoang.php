<?php

session_start();

    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    
if ($module=='bgtadmentrybrklaim' AND $act=='ttdupdate')
{
    $kodenya=$_POST['e_id'];
    $pimgttd=$_POST['txtgambar'];
    if (!empty($pimgttd) AND !empty($kodenya)) {
        
        include "../../config/koneksimysqli.php";
        $query = "update dbttd.klaim_ttd set gambar='$pimgttd' WHERE klaimId='$kodenya' LIMIT 1";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan." EDIT TTD ID $kodenya: "; mysqli_close($cnmy); exit; }
        
        mysqli_close($cnmy);
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
        
    }
}
    
?>