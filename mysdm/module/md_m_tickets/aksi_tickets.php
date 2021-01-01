<?php

    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    //$cnmy=$cnit;
    $dbname = "dbmaster";
// Hapus 
if ($module=='mstsupporttickets' AND $act=='hapus')
{
    mysqli_query($cnmy, "DELETE FROM $dbname.t_tickets WHERE idtickets='$_GET[id]'");
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='mstsupporttickets')
{
    $kodenya=$_POST['e_id'];
    $pmenu=$_POST['cb_menu'];
    $pnamauntuk=$_POST['e_nama'];
    
    $pket=$_POST['e_ket'];
    if (!empty($pket)) $pket = str_replace("'", " ", $pket);
    
    $ptgl = str_replace('/', '-', $_POST['e_tglberlaku']);
    $periode1= date("Y-m-d", strtotime($ptgl));
    
    $ptgl2=$_POST['e_tglselesai'];
    $periode2="0000-00-00";
    if (!empty($ptgl2)) {
        $ptgl2 = str_replace('/', '-', $ptgl2);
        $periode2= date("Y-m-d", strtotime($ptgl2));
    }
    
    if ($act=="input") {
        $query = "INSERT INTO $dbname.t_tickets (idmenu, nama, keterangan, tglpengajuan, tglselesai)values"
                . "('$pmenu', '$pnamauntuk', '$pket', '$periode1', '$periode2')";
    }else{
        $query = "UPDATE $dbname.t_tickets SET idmenu='$pmenu', tglpengajuan='$periode1', "
                . " tglselesai='$periode2', nama='$pnamauntuk', keterangan='$pket' WHERE "
                . " idtickets='$kodenya'";
    }
    
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    if ($act=="input")
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=tambahbaru');
    else
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
  
    
?>
