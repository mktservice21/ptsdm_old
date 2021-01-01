<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="caridatabrperdivisi") {
    
    include "../../../config/koneksimysqli.php";
    
    $pdivisi=$_POST['udivi'];  
    
    $filtetrdiv="";
    if (!empty($pdivisi)) {
        if ($pdivisi=="CAN") {
            $filtetrdiv=" AND divisi NOT IN ('OTC', 'EAGLE', 'HO', 'PIGEO', 'PEACO') ";
        }else{
            $filtetrdiv=" AND divisi='$pdivisi' ";
        }
    }
    
    echo "<option value='' selected>-- Pilihan --</option>";
   $query = "select divisi, year(tgl) tgl, nodivisi, SUM(jumlah) jumlah from dbmaster.t_suratdana_br WHERE IFNULL(stsnonaktif,'')<>'Y' "
            . " AND IFNULL(nodivisi,'')<>'' "//AND  (userid='$_SESSION[IDCARD]' OR nodivisi='$ajsnobr')
            . " AND IFNULL(nomor,'')<>'' $filtetrdiv "//( DATE_FORMAT(tgl,'%Y-%m')='$ajsbulan' OR DATE_FORMAT(tglspd,'%Y-%m')='$ajsbulan' )
            . "GROUP BY 1,2,3 ORDER BY 1,2,3";
    $tampil = mysqli_query($cnmy, $query);
    while ($z= mysqli_fetch_array($tampil)) {
        $pajsjmlbr=$z['jumlah'];
        if (!empty($pajsjmlbr)) $pajsjmlbr=number_format($pajsjmlbr,0);
        $pajsnobr=$z['nodivisi'];
        $pajsdivisi=$z['divisi'];
        if (empty($pajsdivisi)) $pajsdivisi= "ETHICAL";
        $pajsketjml = "$pajsnobr";//$pajsdivisi -  &nbsp;&nbsp (Rp. $pajsjmlbr)
        if (trim($pajsnobr)==trim($ajsnobr)){
            echo "<option value='$pajsnobr' selected>$pajsketjml</option>";
            $lewatnodivspd2=true;
        }else
            echo "<option value='$pajsnobr'>$pajsketjml</option>";
    }
    
}

?>