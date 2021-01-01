<?php

if ($_GET['module']=="viewcoadivisi"){
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    $karyawanId = $_POST['umr'];
    $mydivisi = $_POST['udivi'];
    $fil="";
    
    if (empty($mydivisi)) {
        $jabatan = getfieldcnit("select distinct rank as lcfields from dbmaster.v_karyawan_all where karyawanId='$karyawanId'");

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
    
    $query = "select a.COA4, a.NAMA4 from dbmaster.coa_level4 a 
        LEFT JOIN dbmaster.coa_level3 b on a.COA3=b.COA3
        LEFT JOIN dbmaster.coa_level2 c on b.COA2=c.COA2
        WHERE 1=1 $fil ";
    
    $tampil = mysqli_query($cnmy, $query);
    echo "<option value='' selected>--Pilihan--</option>";
    while ($z= mysqli_fetch_array($tampil)) {
        echo "<option value='$z[COA4]'>$z[COA4] - $z[NAMA4]</option>";
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
}elseif ($_GET['module']=="viewsubkode"){
    include "../../config/koneksimysqli.php";
    $pkode = trim($_POST['ukode']);
    $query = "select kodeid, subkode, subnama from dbmaster.t_kode_spd WHERE kodeid='$pkode'";
    $result = mysqli_query($cnmy, $query); 
    $record = mysqli_num_rows($result);
    for ($i=0;$i < $record;$i++) {
        $row = mysqli_fetch_array($result); 
        $subkode  = $row['subkode'];
        $namasub = $row['subnama'];
        echo "<option value=\"$subkode\">$subkode - $namasub</option>";
    }
}elseif ($_GET['module']=="hitungtotal"){
    include "../../config/koneksimysqli.php";
    //$cnmy=$cnit;
    $ptotal=0;
    
    $pfilterA=$_POST['ufila'];
    $pfilterB=$_POST['ufilb'];
    $pfilterC=$_POST['ufilc'];
    $pfilterD=$_POST['ufild'];
    $pfilterE=$_POST['ufile'];
    $pfilterF=$_POST['ufilf'];
    $pfilterG=$_POST['ufilg'];
    $pfilterH=$_POST['ufilh'];
    $pfilterI=$_POST['ufili'];
    $pfilterJ=$_POST['ufilj'];
    $pfilterK=$_POST['ufilk'];
    
    $totalinputA=0;
    $totalinputB=0;
    $totalinputC=0;
    $totalinputD=0;
    $totalinputE=0;
    $totalinputF=0;
    $totalinputG=0;
    $totalinputH=0;
    $totalinputI=0;
    $totalinputJ=0;
    $totalinputK=0;
    
    if (!empty($pfilterA)) {
        $query="SELECT SUM(jumlah) as jumlah from hrd.br0 where brId in $pfilterA";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $tr= mysqli_fetch_array($tampil);
            if (!empty($tr['jumlah'])) $totalinputA=$tr['jumlah'];
        }
    }
    
    if (!empty($pfilterB)) {
        $query="SELECT SUM(jumlah) as jumlah from hrd.klaim where klaimId in $pfilterB";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $tr= mysqli_fetch_array($tampil);
            if (!empty($tr['jumlah'])) $totalinputB=$tr['jumlah'];
        }
    }
    
    if (!empty($pfilterC)) {
        $query="SELECT SUM(jumlah) as jumlah from hrd.kas where kasId in $pfilterC";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $tr= mysqli_fetch_array($tampil);
            if (!empty($tr['jumlah'])) $totalinputC=$tr['jumlah'];
        }
    }
    
    if (!empty($pfilterD)) {
        $query="SELECT SUM(jumlah) as jumlah from hrd.br_otc where brOtcId in $pfilterD";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $tr= mysqli_fetch_array($tampil);
            if (!empty($tr['jumlah'])) $totalinputD=$tr['jumlah'];
        }
    }
    
    if (!empty($pfilterE)) {
        $query="SELECT SUM(jumlah) as jumlah from dbmaster.t_br_bulan where idbr in $pfilterE";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $tr= mysqli_fetch_array($tampil);
            if (!empty($tr['jumlah'])) $totalinputE=$tr['jumlah'];
        }
    }
    
    if (!empty($pfilterF)) {
        $query="SELECT SUM(jumlah) as jumlah from dbmaster.t_brrutin0 where idrutin in $pfilterF";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $tr= mysqli_fetch_array($tampil);
            if (!empty($tr['jumlah'])) $totalinputF=$tr['jumlah'];
        }
    }
    
    if (!empty($pfilterG)) {
        $query="SELECT SUM(jumlah) as jumlah from dbmaster.t_brrutin0 where idrutin in $pfilterG";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $tr= mysqli_fetch_array($tampil);
            if (!empty($tr['jumlah'])) $totalinputG=$tr['jumlah'];
        }
    }
    
    if (!empty($pfilterH)) {
        $query="SELECT SUM(jumlah) as jumlah from dbmaster.t_ca0 where idca in $pfilterH";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $tr= mysqli_fetch_array($tampil);
            if (!empty($tr['jumlah'])) $totalinputH=$tr['jumlah'];
        }
    }
    
    if (!empty($pfilterI)) {
        $query="SELECT SUM(jumlah) as jumlah from dbmaster.t_ca0 where idca in $pfilterI";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $tr= mysqli_fetch_array($tampil);
            if (!empty($tr['jumlah'])) $totalinputI=$tr['jumlah'];
        }
    }
    
    
    
    $ptotal=(double)$totalinputA+(double)$totalinputB+(double)$totalinputC+(double)$totalinputD+
            (double)$totalinputE+(double)$totalinputF+(double)$totalinputG+
            (double)$totalinputH+(double)$totalinputI+(double)$totalinputJ+(double)$totalinputK;
    
    echo $ptotal;
    
}elseif ($_GET['module']=="x"){
    
}
?>
