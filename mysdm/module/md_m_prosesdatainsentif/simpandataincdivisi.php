<?php

    session_start();
    include "../../config/koneksimysqli_ms.php";
    $cnmy=$cnms;
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    $berhasil = "tidak ada yang diproses...";
if ($module=='mstprosesinsentif' AND $act=='input')
{
    $ptgl=$_POST['utgl'];
    $ptgl1= date("Y-m-01", strtotime($ptgl));
    
    $pdivprod=$_POST['udivisi'];
    $pkryid=$_POST['ukry'];
    
    $query="UPDATE ms.incentiveperdivisi SET divisi='$pdivprod' WHERE bulan='$ptgl1' AND karyawanid='$pkryid'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    mysqli_close($cnmy);
    
    $berhasil="";
}
    echo $berhasil;
?>

