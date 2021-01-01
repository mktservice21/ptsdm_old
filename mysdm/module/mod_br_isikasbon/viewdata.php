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
    
    mysqli_close($cnmy);
    
}elseif ($_GET['module']=="caricoaperkode"){
    
    
    include "../../config/koneksimysqli.php";
    
    $mydivisi = $_POST['udivi'];
    $ikode = $_POST['ukode'];
    $fil = " AND ( c.DIVISI2 = '$mydivisi' OR RTRIM(IFNULL(c.DIVISI2,'')) in ('OTHER', '') )";
    if (empty($mydivisi)) $fil = " AND RTRIM(IFNULL(c.DIVISI2,'')) in ('OTHER', '')";
    
    $query = "select COA4 FROM dbmaster.posting_coa_kas WHERE kodeid='$ikode'";
    $tampils= mysqli_query($cnmy, $query);
    $nr= mysqli_fetch_array($tampils);
    $ppilcoa=$nr['COA4'];
    
    echo "<option value='' selected>--Pilihan--</option>";
    //$query = "select COA4, NAMA4 FROM dbmaster.coa_level4 ";
    
    $query = "select a.COA4, a.NAMA4 from dbmaster.coa_level4 a 
        LEFT JOIN dbmaster.coa_level3 b on a.COA3=b.COA3
        LEFT JOIN dbmaster.coa_level2 c on b.COA2=c.COA2
        WHERE 1=1 $fil OR a.COA4='$ppilcoa' ";
    
    $query .= " ORDER BY a.COA4";
    $tampil = mysqli_query($cnmy, $query);
    while ($z= mysqli_fetch_array($tampil)) {
        $pcoa=$z['COA4'];
        $pnmcoa=$z['NAMA4'];
        if ($pcoa==$ppilcoa)
            echo "<option value='$pcoa' selected>$pcoa - $pnmcoa</option>";
        else
            echo "<option value='$pcoa'>$pcoa - $pnmcoa</option>";
    }
    
    mysqli_close($cnmy);
}