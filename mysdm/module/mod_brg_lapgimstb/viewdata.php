<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];
//$ptgl = str_replace('/', '-', $_POST['utgl']);
//$ptglpengajuan= date("Y-m-d", strtotime($ptgl));
//$pjumlah=str_replace(",","", $pjumlah);

if ($pmodule=="caridatagrpprod") {
    
    
    $pdivuntuk=$_POST['udivuntuk'];
    
    
    $onclick="";
    $onc="";
    if (!empty($onclick)) $onc=" onclick=".$onclick."";
    
    include "../../config/koneksimysqli.php";
    
    $query = "select DIVISIID, DIVISINM from dbmaster.t_divisi_gimick WHERE STSAKTIF='Y' AND PILIHAN='$pdivuntuk'";
    $tampil= mysqli_query($cnmy, $query);
    while ($Xt=mysqli_fetch_array($tampil)){
        $pdivid=$Xt['DIVISIID'];
        $pdivnama=$Xt['DIVISINM'];
        $cek="checked";
        echo "<input type=checkbox value='$pdivid' name='chkbox_divisiprodgrp[]' $onc $cek> $pdivnama<br/>";
    }
    
    mysqli_close($cnmy);
    
    
}elseif ($pmodule=="caridatacabang") {
    include "../../config/koneksimysqli.php";
    
    
    $pdivuntuk=$_POST['udivuntuk'];
    
    if ($pdivuntuk=="OT")
        $query = "select icabangid_o icabangid, nama from MKT.icabang_o WHERE aktif='Y' ";
    else
        $query = "select icabangid, nama from MKT.icabang WHERE aktif='Y' ";
    
    $query .= " order by nama";
    $tampil= mysqli_query($cnmy, $query);
    while ($Xt=mysqli_fetch_array($tampil)){
        $pidcab=$Xt['icabangid'];
        $pnmcab=$Xt['nama'];
        $cek="checked";
        echo "<input type=checkbox value='$pidcab' name='chkbox_cabangid[]' $cek> $pnmcab<br/>";
    }
    
    mysqli_close($cnmy);
    
}elseif ($pmodule=="caridataprduk") {
    
    include "../../config/koneksimysqli.php";
    
    
    $pidgrpdiv=$_POST['uallnobrgprprd'];
    $pidkat=$_POST['uallnobrkatid'];
    
    if (!empty($pidgrpdiv)) {
        $pidgrpdiv=" AND DIVISIID IN (".substr($pidgrpdiv, 0, -1).")";
    }
    
    if (!empty($pidkat)) {
        $pidkat=" AND IDKATEGORI IN (".substr($pidkat, 0, -1).")";
    }

    $query = "select IDBARANG, NAMABARANG from dbmaster.t_barang WHERE IFNULL(STSNONAKTIF,'')<>'Y' $pidgrpdiv $pidkat ";
    $query .= " order by NAMABARANG";
    $tampil= mysqli_query($cnmy, $query);
    while ($Xt=mysqli_fetch_array($tampil)){
        $pidbrg=$Xt['IDBARANG'];
        $pnmbrg=$Xt['NAMABARANG'];
        $cek="checked";
        echo "<input type=checkbox value='$pidbrg' name='chkbox_produkid[]' $cek> $pnmbrg<br/>";
    }
    
    mysqli_close($cnmy);
    
    
    
}elseif ($pmodule=="xxx") {
    
}

?>