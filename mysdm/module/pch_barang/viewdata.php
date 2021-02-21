<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="viewdatabrand") {
    
    $ppilihanwewenang=$_POST['uwewenang'];
    $pnpilihdiv=$_POST['ucbgrp'];
    
    include "../../config/koneksimysqli.php";
    
    echo "<option value='' selected>--Pilihan--</option>";
    $query = "select distinct IDBRAND, NAMA_BRAND from dbmaster.t_barang_brand WHERE IFNULL(AKTIF,'')<>'N' ";
    if ($ppilihanwewenang=="AL") {
    }else{
        $query .= " AND DIVISIID IN (select distinct DIVISIID from dbmaster.t_divisi_gimick WHERE IFNULL(STSAKTIF,'')='Y' AND PILIHAN='$ppilihanwewenang') ";
    }
    if (!empty($pnpilihdiv)) $query .=" AND DIVISIID='$pnpilihdiv' ";
    $query .=" ORDER BY NAMA_BRAND";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    while ($row= mysqli_fetch_array($tampil)) {
        $npidbrand=$row['IDBRAND'];
        $npnmbrand=$row['NAMA_BRAND'];

        if ($npidbrand==$pbrandprod)
            echo "<option value='$npidbrand' selected>$npnmbrand</option>";
        else {
            if ($ketemu==1) {
                echo "<option value='$npidbrand' selected>$npnmbrand</option>";
            }else{
                echo "<option value='$npidbrand'>$npnmbrand</option>";
            }
        }
    }
    
    mysqli_close($cnmy);
    
    
}

?>