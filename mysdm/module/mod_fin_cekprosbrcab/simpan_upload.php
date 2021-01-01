<?php

    session_start();
    $puserid=$_SESSION['IDCARD'];
    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...!!!";
        exit;
    }
    
    include "../../config/koneksimysqli.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
  
    
if ($module=='fincekprosesbrcab' AND $act=='hapusgambar')
{
    $pnourut=$_GET['idgam'];
    if (!empty($pnourut)) {
        mysqli_query($cnmy, "DELETE FROM dbimages.img_br_cab1 WHERE nourut='$pnourut'");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
    }
    
    mysqli_close($cnmy);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
    
}
elseif ($module=='fincekprosesbrcab' AND $act=='input')
{
    
    $pbrid=$_POST['e_id'];
    $pnobrid=$_POST['e_id2'];
    $gambarnya=$_POST['e_imgconv'];
    
    
    if (!empty($gambarnya)) {
        mysqli_query($cnmy, "insert into dbimages.img_br_cab1 (bridinputcab, noid, gambar) values ('$pbrid', '$pnobrid', '$gambarnya')");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
    mysqli_close($cnmy);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
    
}
    
?>

