<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="viewdatacabang") {
    
    $piddivisi=$_POST['udivsi'];
    $fkaryawan=$_SESSION['IDCARD'];
    $pidgroup=$_SESSION['GROUP'];
    
    include "../../config/koneksimysqli.php";
    echo "<option value='' selected>--All--</option>";
    
    $filtercabaut=false;
    
    $query ="select PILIHAN from dbmaster.t_barang_wewenang WHERE KARYAWANID='$fkaryawan'";
    $tampilk=mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampilk);
    if ((DOUBLE)$ketemu<=0) $filtercabaut=true;
    
    
    if ($pidgroup=="1") {
        $filtercabaut=false;
    }
    
    if ($piddivisi=="OT") {
        $query = "select icabangid_o icabangid, nama from MKT.icabang_o WHERE aktif='Y' ";
        if ($filtercabaut==true) $query .= " AND icabangid_o IN ";
    }else{
        $query = "select icabangid, nama from MKT.icabang WHERE aktif='Y' ";
        if ($filtercabaut==true) $query .= " AND icabangid IN ";
    }
    
    if ($filtercabaut==true) $query .=" (select ifnull(icabangid,'') from hrd.rsm_auth where karyawanid='$fkaryawan') ";
    
    $query .=" ORDER BY nama";
    $tampil= mysqli_query($cnmy, $query);
    while ($row= mysqli_fetch_array($tampil)) {
        $npidcab=$row['icabangid'];
        $npnmcab=$row['nama'];
        echo "<option value='$npidcab'>$npnmcab</option>";
    }
    
    
    mysqli_close($cnmy);
    
}

?>