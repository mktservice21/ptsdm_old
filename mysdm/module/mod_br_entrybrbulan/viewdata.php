<?php

if ($_GET['module']=="viewdatacabangkaryawan"){
    include "../../config/koneksimysqli.php";
    
    $karyawanId = $_POST['umr']; 
    $query = "select karyawan.iCabangId, cabang.nama from hrd.karyawan as karyawan join mkt.icabang as cabang on "
            . " karyawan.icabangid=cabang.icabangid where karyawanId='$karyawanId'"; 
    $result = mysqli_query($cnmy, $query); 
    $record = mysqli_num_rows($result);
    echo "<option value='' selected>-- Pilihan --</option>";
    for ($i=0;$i < $record;$i++) {
        $row = mysqli_fetch_array($result); 
        $kodeid  = $row['iCabangId'];
        $nama = $row['nama'];
        echo "<option value=\"$kodeid\" selected>$nama</option>";
    }
}
elseif ($_GET['module']=="viewdivisimr"){
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    $karyawanId = $_POST['umr'];
    $jabatan = getfieldcnmy("select distinct rank as lcfields from dbmaster.v_karyawan_all where karyawanId='$karyawanId'");
    
    if ($jabatan=="04")
        $query = "select distinct divisiid as lcfields from MKT.ispv0 WHERE karyawanid='$karyawanId'";
    elseif ($jabatan=="05")
        $query = "select distinct divisiid as lcfields from MKT.imr0 WHERE karyawanid='$karyawanId'";
    else
        $query = "select divisiId as lcfields from hrd.karyawan WHERE karyawanid='$karyawanId' and ifnull(divisiId,'') <>'' 
            UNION select divisiId2 as lcfields from hrd.karyawan WHERE karyawanid='$karyawanId' and ifnull(divisiId2,'') <>''";
    
    $tampil = mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu==0) {
        $tampil = mysqli_query($cnmy, "select DivProdId lcfields from MKT.divprod WHERE br='Y'");
    }
    echo "<option value='' selected>--Pilihan--</option>";
    while ($z= mysqli_fetch_array($tampil)) {
        echo "<option value='$z[lcfields]'>$z[lcfields]</option>";
    }
    
    
}elseif ($_GET['module']=="viewcoadivisi"){
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    $karyawanId = $_POST['umr'];
    $mydivisi = $_POST['udivi'];
    $fil="";
    
    if (empty($mydivisi)) {
        $jabatan = getfieldcnmy("select distinct rank as lcfields from dbmaster.v_karyawan_all where karyawanId='$karyawanId'");

        if ($jabatan=="04")
            $query = "select distinct divisiid as lcfields from MKT.ispv0 WHERE karyawanid='$karyawanId'";
        elseif ($jabatan=="05")
            $query = "select distinct divisiid as lcfields from MKT.imr0 WHERE karyawanid='$karyawanId'";
        else
            $query = "select CASE WHEN IFNULL(divisiId2,'')='' THEN CONCAT('''',divisiId,'''') ELSE "
                . "CONCAT('''',divisiId,''',','''',divisiId2,''',') END lcfields from hrd.karyawan WHERE karyawanid='$karyawanId'";

        $tampil = mysqli_query($cnmy, $query);
        $divisi="";
        while ($z= mysqli_fetch_array($tampil)) {
            if ($jabatan=="04" OR $jabatan=="05") {
                $divisi .="'".$z['lcfields']."',";
            }else
                $divisi =$z['lcfields'];
        }
        if (!empty($divisi)) {
            $divisi="(".substr($divisi, 0, -1).")";
            $fil = " AND c.DIVISI2 in $divisi";
        }
    }else{
        $fil = "AND c.DIVISI2 = '$mydivisi'";
    }
    
    //$query = "select COA4, NAMA4 from dbmaster.v_coa_all WHERE ( ifnull(kodeid,'') = '' AND COA4 not in (select distinct COA4 from dbmaster.posting_coa)) $fil";
    $query = "select COA4, NAMA4 from dbmaster.v_coa_all WHERE COA4 in (select distinct ifnull(COA4,'') from dbmaster.posting_coa_rutin) $fil";
    
    $query = "select a.COA4, a.NAMA4 from dbmaster.coa_level4 a 
        LEFT JOIN dbmaster.coa_level3 b on a.COA3=b.COA3
        LEFT JOIN dbmaster.coa_level2 c on b.COA2=c.COA2
        WHERE a.COA4 in (select distinct ifnull(COA4,'') from dbmaster.posting_coa_rutin) $fil ";
    
    $tampil = mysqli_query($cnmy, $query);
    echo "<option value='' selected>--Pilihan--</option>";
    while ($z= mysqli_fetch_array($tampil)) {
        echo "<option value='$z[COA4]'>$z[NAMA4]</option>";
    }
    
    
}elseif ($_GET['module']=="viewareadivisi"){
    include "../../config/koneksimysqli.php";
    $mydivisi = trim($_POST['udivi']);
    if ($mydivisi=="OTC") {
        $query = "select icabangid_o iCabangId, nama from dbmaster.v_icabang_o where aktif='Y' and "
                . " icabangid_o not in ('JKT_MT', 'JKT_RETAIL', 'MD', 'PM_ACNEMED', 'PM_CARMED', 'PM_LANORE', 'PM_MELANOX', 'PM_PARASOL') order by nama"; 
    }else{
        $query = "select iCabangId, nama from dbmaster.icabang where aktif='Y' order by nama"; 
    }
    $result = mysqli_query($cnmy, $query); 
    $record = mysqli_num_rows($result);
    echo "<option value='' selected>-- Pilihan --</option>";
    for ($i=0;$i < $record;$i++) {
        $row = mysqli_fetch_array($result); 
        $kodeid  = $row['iCabangId'];
        $nama = $row['nama'];
        if ($idcabang==$kodeid)
            echo "<option value=\"$kodeid\" selected>$nama</option>";
        else
            echo "<option value=\"$kodeid\">$nama</option>";
    }
}elseif ($_GET['module']=="x"){
}elseif ($_GET['module']=="x"){
    
}
?>
