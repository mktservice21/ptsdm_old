<?php

if ($_GET['module']=="viewdataatasanspv"){
    include "../../config/koneksimysqli.php";
    $karyawan=$_POST['umr'];
    $query = "select distinct a.karyawanid karyawanId, nama from dbmaster.v_penempatanspv as a WHERE CONCAT(a.divisiid, a.icabangid, a.areaid) in 
        (select CONCAT(b.divisiid, b.icabangid, b.areaid) 
        from MKT.imr0 b where b.karyawanid='$karyawan')";
    $query .=" order by nama, karyawanId";
    $tampil = mysqli_query($cnmy, $query);
    $ketemu = mysqli_num_rows($tampil);
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){ 
        $sel="";
        if ($ketemu==1) $sel="selected";
        echo "<option value='$a[karyawanId]' $sel>$a[nama]</option>";
    }
}elseif ($_GET['module']=="viewdataatasandm"){
    include "../../config/koneksimysqli.php";
    $karyawan=$_POST['umr'];
    $query = "select distinct a.karyawanid karyawanId, nama from dbmaster.v_penempatandm as a WHERE CONCAT(a.icabangid) in 
        (select CONCAT(b.icabangid) 
        from MKT.ispv0 b where b.karyawanid='$karyawan')";
    $query .=" order by nama, karyawanId";
    $tampil = mysqli_query($cnmy, $query);
    $ketemu = mysqli_num_rows($tampil);
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        $sel="";
        if ($ketemu==1) $sel="selected";
        echo "<option value='$a[karyawanId]' $sel>$a[nama]</option>";
    }
}elseif ($_GET['module']=="viewdataatasansm"){
    include "../../config/koneksimysqli.php";
    $karyawan=$_POST['umr'];
    $query = "select distinct a.karyawanid karyawanId, nama from dbmaster.v_penempatansm as a WHERE CONCAT(a.icabangid) in 
        (select CONCAT(b.icabangid) 
        from MKT.idm0 b where b.karyawanid='$karyawan')";
    $query .=" order by nama, karyawanId";
    $tampil = mysqli_query($cnmy, $query);
    $ketemu = mysqli_num_rows($tampil);
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        $sel="";
        if ($ketemu==1) $sel="selected";
        echo "<option value='$a[karyawanId]' $sel>$a[nama]</option>";
    }
}

?>

