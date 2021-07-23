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
}elseif ($_GET['module']=="viwewbiayauntuk"){
    include "../../../config/koneksimysqli.php";
    $mydivisi = $_POST['udivi'];
    
    if ($mydivisi=="OTC") {
        $query = "select gkode, nama_group FROM hrd.brkd_otc_group 
            WHERE 1=1 ";
        $query .= " ORDER BY gkode";
        $tampil = mysqli_query($cnmy, $query);
        echo "<option value='' selected>-- All --</option>";
        while ($z= mysqli_fetch_array($tampil)) {
            echo "<option value='$z[gkode]'>$z[gkode] - $z[nama_group]</option>";
        }
    }else{
        echo "<option value=''>-- All --</option>";
    }
    
    mysqli_close($cnmy);
}elseif ($_GET['module']=="viwewdatacabang"){
    include "../../../config/koneksimysqli.php";
    $mydivisi = $_POST['udivi'];
    
    if ($mydivisi=="OTC") {
        $query = "SELECT * FROM (select icabangid_o as icabangid, nama as nama_cabang FROM MKT.icabang_o WHERE aktif<>'N' ";
        $query .= "UNION ";
        $query .= "select cabangid_ho as icabangid, nama as nama_cabang FROM dbmaster.cabang_otc WHERE aktif<>'N') as tcab ";
        $query .= " ORDER BY nama_cabang";
        
        echo "<option value='' selected>-- All CHC --</option>";
    }else{
        $query = "select iCabangId as icabangid, nama as nama_cabang FROM MKT.icabang WHERE aktif<>'N' ";
        $query .= " AND LEFT(nama,5) NOT IN ('OTC -', 'PEA -') ";
        $query .= " ORDER BY nama";
        
        echo "<option value='' selected>-- All Ethical --</option>";
    }

    $tampil = mysqli_query($cnmy, $query);
    while ($z= mysqli_fetch_array($tampil)) {
        $nnicabid=$z['icabangid'];
        $nnicabnm=$z['nama_cabang'];

        if ($nnicabid==$picabangid)
            echo "<option value='$nnicabid' selected>$nnicabnm - $nnicabid</option>";
        else
            echo "<option value='$nnicabid'>$nnicabnm - $nnicabid</option>";
    }
                                            
    mysqli_close($cnmy);
}elseif ($_GET['module']=="xxx"){
    
}