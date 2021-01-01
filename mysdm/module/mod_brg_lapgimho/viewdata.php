<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];
//$ptgl = str_replace('/', '-', $_POST['utgl']);
//$ptglpengajuan= date("Y-m-d", strtotime($ptgl));
//$pjumlah=str_replace(",","", $pjumlah);

if ($pmodule=="caridatagrpprod") {
    
    $psesi_idcard=$_SESSION['IDCARD'];
    
    $pdivuntuk=$_POST['udivuntuk'];
    
    
    $onclick="";
    $onc="";
    if (!empty($onclick)) $onc=" onclick=".$onclick."";
    
    include "../../config/koneksimysqli.php";
    
    $query = "select DIVISIID, DIVISINM from dbmaster.t_divisi_gimick WHERE STSAKTIF='Y' AND PILIHAN='$pdivuntuk' ";
    
    if ($psesi_idcard=="0000000157") {
        $query .= " AND DIVISIID='PIGEO' ";
    }elseif ($psesi_idcard=="0000000910") {
        $query .= " AND DIVISIID='PEACO' ";
    }elseif ($psesi_idcard=="0000000257") {
        $query .= " AND DIVISIID='EAGLE' ";
    }
    
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
    $pidbrand=$_POST['uallnobrbrdid'];
    $pidkat=$_POST['uallnobrkatid'];
    
    if (!empty($pidgrpdiv)) {
        $pidgrpdiv=" AND a.DIVISIID IN (".substr($pidgrpdiv, 0, -1).")";
    }
    
    if (!empty($pidbrand)) {
        $pidbrand=" AND IFNULL(a.IDBRAND,'') IN (".substr($pidbrand, 0, -1).")";
    }
    
    if (!empty($pidkat)) {
        $pidkat=" AND a.IDKATEGORI IN (".substr($pidkat, 0, -1).")";
    }
    
    $query = "select a.IDBARANG, a.NAMABARANG from dbmaster.t_barang as a LEFT JOIN dbmaster.t_barang_tipe as b on "
            . " a.IDTIPE=b.IDTIPE "
            . " WHERE IFNULL(b.STS,'') IN ('G') and IFNULL(a.STSNONAKTIF,'')<>'Y' $pidgrpdiv $pidbrand $pidkat ";
    $query .= " order by a.NAMABARANG";
    $tampil= mysqli_query($cnmy, $query);
    while ($Xt=mysqli_fetch_array($tampil)){
        $pidbrg=$Xt['IDBARANG'];
        $pnmbrg=$Xt['NAMABARANG'];
        $cek="checked";
        echo "<input type=checkbox value='$pidbrg' name='chkbox_produkid[]' $cek> $pnmbrg<br/>";
    }
    
    mysqli_close($cnmy);
    
    
    
}elseif ($pmodule=="caridatabrand") {
    include "../../config/koneksimysqli.php";
    $psesi_idcard=$_SESSION['IDCARD'];
    
    $pdivuntuk=$_POST['udivuntuk'];
    
    echo "<input type=checkbox value='0' name='chkbox_brand[]' checked> Tanpa Brand (Others)<br/>";
    
    $query = "select distinct IDBRAND, NAMA_BRAND from dbmaster.t_barang_brand WHERE IFNULL(AKTIF,'')<>'N' ";
    $query .= " AND DIVISIID IN (select distinct DIVISIID from dbmaster.t_divisi_gimick WHERE IFNULL(STSAKTIF,'')='Y' AND PILIHAN='$pdivuntuk') ";
    
    if ($psesi_idcard=="0000000157") {
        $query .= " AND DIVISIID='PIGEO' ";
    }elseif ($psesi_idcard=="0000000910") {
        $query .= " AND DIVISIID='PEACO' ";
    }elseif ($psesi_idcard=="0000000257") {
        $query .= " AND DIVISIID='EAGLE' ";
    }
    
    $tampil= mysqli_query($cnmy, $query);
    while ($Xt=mysqli_fetch_array($tampil)){
        $pidbrand=$Xt['IDBRAND'];
        $pnmbrand=$Xt['NAMA_BRAND'];
        $cek="checked";
        echo "<input type=checkbox value='$pidbrand' name='chkbox_brand[]' $cek> $pnmbrand<br/>";
    }
    
    mysqli_close($cnmy);
}elseif ($pmodule=="xxx") {
    
}

?>