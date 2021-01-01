<?php

if ($_GET['module']=="viewcoadivisi"){
    
    include "../../../config/koneksimysqli.php";
    $mydivisi = $_POST['udivi'];
    $fil = " AND (c.DIVISI2 = '$mydivisi' OR RTRIM(IFNULL(c.DIVISI2,'')) in ('OTHER', ''))";
    if (empty($mydivisi)) $fil = " AND RTRIM(IFNULL(c.DIVISI2,'')) in ('OTHER', '')";
    $query = "select a.COA4, a.NAMA4 from dbmaster.coa_level4 a 
        LEFT JOIN dbmaster.coa_level3 b on a.COA3=b.COA3
        LEFT JOIN dbmaster.coa_level2 c on b.COA2=c.COA2
        WHERE 1=1 $fil ";
    $query .= " ORDER BY a.COA4";
    $tampil = mysqli_query($cnmy, $query);
    echo "<option value='' selected>--Pilihan--</option>";
    while ($z= mysqli_fetch_array($tampil)) {
        echo "<option value='$z[COA4]'>$z[COA4] - $z[NAMA4]</option>";
    }
    
}elseif ($_GET['module']=="viewcoadivisi2"){
    include "../../../config/koneksimysqli.php";
    $mydivisi = $_POST['udivi'];
    $fil = " AND (c.DIVISI2 = '$mydivisi' OR RTRIM(IFNULL(c.DIVISI2,'')) in ('OTHER', ''))";
    if (empty($mydivisi)) $fil = " AND RTRIM(IFNULL(c.DIVISI2,'')) in ('OTHER', '')";
    $query = "select a.COA4, a.NAMA4 from dbmaster.coa_level4 a 
        LEFT JOIN dbmaster.coa_level3 b on a.COA3=b.COA3
        LEFT JOIN dbmaster.coa_level2 c on b.COA2=c.COA2
        WHERE 1=1 $fil ";
    $query .= " ORDER BY a.COA4";
    $tampil = mysqli_query($cnmy, $query);
    echo "<option value='' selected>--Pilihan--</option>";
    while ($z= mysqli_fetch_array($tampil)) {
        echo "<option value='$z[COA4]'>$z[COA4] - $z[NAMA4]</option>";
    }
}elseif ($_GET['module']=="xxx"){
    
}