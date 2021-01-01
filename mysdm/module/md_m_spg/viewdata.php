<?php

if ($_GET['module']=="viewcoadivisi"){
    
    include "../../config/koneksimysqli.php";
    $mydivisi = $_POST['udivi'];
    $fil = " AND c.DIVISI2 = '$mydivisi'";
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
    
    
}elseif ($_GET['module']=="viewdataareacab"){
    include "../../config/koneksimysqli.php";
    
    $cabang = $_POST['ucab'];
    
    $query = "select areaid_o, nama, aktif from MKT.iarea_o where icabangid_o='$cabang' order by nama";
    
    $tampil=mysqli_query($cnmy, $query);
    
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        echo "<option value='$a[areaid_o]'>$a[nama]</option>";
    }
}elseif ($_GET['module']=="viewdatatokocab"){
    include "../../config/koneksimysqli.php";
    
    $cabang = $_POST['ucab'];
    $area="";
    if (isset($_POST['uarea'])) {
        if (!empty($_POST['uarea']))
            $area=" and areaid_o='$_POST[uarea]' ";
    }
    
    $query = "select icustid_o, nama from MKT.icust_o where icabangid_o='$cabang' $area order by nama";
    
    $tampil=mysqli_query($cnmy, $query);
    
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        echo "<option value='$a[icustid_o]'>$a[nama]</option>";
    }
}elseif ($_GET['module']=="viewdataareacabbytoko"){
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    
    $cabang = $_POST['ucab'];
    $tokoo = $_POST['utoko'];
    
    $areatoko= getfieldcnit("select areaid_o as lcfields from MKT.icust_o where icustid_o='$tokoo' and icabangid_o='$cabang'");
    
    $query = "select areaid_o, nama, aktif from MKT.iarea_o where icabangid_o='$cabang' order by nama";
    $tampil=mysqli_query($cnmy, $query);
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        if ($a['areaid_o']==$areatoko)
            echo "<option value='$a[areaid_o]' selected>$a[nama]</option>";
        else
            echo "<option value='$a[areaid_o]'>$a[nama]</option>";
    }
    
}