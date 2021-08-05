<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="caridataaktivitas") {
    $pregion=$_POST['uregion'];
    $pdivisi=$_POST['udivisi'];
    $pdistid=$_POST['udistid'];
    $pbln=$_POST['ubln'];
    $pper1=$_POST['uper1'];
    $pper2=$_POST['uper2'];
    
    $pnamadiv=$pdivisi;
    if ($pdivisi=="CAN") $pnamadiv="Ethical";
    elseif ($pdivisi=="EAGLE") $pnamadiv="Eagle";
    elseif ($pdivisi=="OTC") $pnamadiv="CHC";
    elseif ($pdivisi=="OTHER") $pnamadiv="Others";
    elseif ($pdivisi=="PIGEO") $pnamadiv="Pigeon";
    elseif ($pdivisi=="PEACO") $pnamadiv="Peacock";
    
    $pnamareg="";
    if ($pregion=="B") $pnamareg="Reg I";
    elseif ($pregion=="T") $pnamareg="Reg II";
    //spp = 2
    $pbulan=date('F Y', strtotime($pbln));
    if ((DOUBLE)$pdistid==299999 || $pdistid=="0000000002X") {
        $pbulan=date('F Y', strtotime($pbln))." ".date('d/m/Y', strtotime($pper1))." s/d. ".date('d/m/Y', strtotime($pper2));
    }
    
    
    $pnotes="Biaya Kerjasama Promosi Produk $pnamadiv $pnamareg $pbulan";
    
    
    
    
    echo $pnotes;
    
}elseif ($pmodule=="caridatarealisasi") {
    $pdistid=$_POST['udistid'];
    
    if ($pdistid=="0000000015" or $pdistid=="0000000012" or $pdistid=="0000000017") $pdistid="0000000002";
    
    include "../../config/koneksimysqli.php";
    $query = "select nama as nama from MKT.distrib0 WHERE Distid='$pdistid'";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    $pnmdist=$row['nama'];
    mysqli_close($cnmy);
    
    echo $pnmdist;
    
}elseif ($pmodule=="xxx") {
    
}

?>