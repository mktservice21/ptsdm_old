<?php

if ($_GET['module']=="viewdatalevel2"){
    include "../../config/koneksimysqli.php";
    echo "<option value='' selected>-- Pilihan --</option>";
    $tampil=mysqli_query($cnmy, "SELECT COA2, NAMA2 FROM dbmaster.coa_level2 where COA1='$_POST[ulevel1]' order by COA2");
    while($a=mysqli_fetch_array($tampil)){
    echo "<option value='$a[COA2]'>$a[COA2] - $a[NAMA2]</option>";
    }
}elseif ($_GET['module']=="viewdatalevel3"){
    include "../../config/koneksimysqli.php";
    echo "<option value='' selected>-- Pilihan --</option>";
    $tampil=mysqli_query($cnmy, "SELECT COA3, NAMA3 FROM dbmaster.coa_level3 where COA2='$_POST[ulevel2]' order by COA3");
    while($a=mysqli_fetch_array($tampil)){
    echo "<option value='$a[COA3]'>$a[COA3] - $a[NAMA3]</option>";
    }
}elseif ($_GET['module']=="viewdataposting"){
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    $div=  getfieldcnmy("select DIVISI2 as lcfields from dbmaster.coa_level2 where COA2='$_POST[ulevel2]'");
    $filter="where divprodid='$div'";
    if (empty($div)) $filter="";
    echo "<option value='' selected>-- Pilihan --</option>";
    $tampil=mysqli_query($cnmy, "SELECT kodeid, nama, divprodid FROM dbmaster.br_kode $filter order by kodeid");
    while($a=mysqli_fetch_array($tampil)){
    echo "<option value='$a[kodeid]'>$a[kodeid] - $a[nama]</option>";
    }
}elseif ($_GET['module']=="viewdatasubpos"){
    include "../../config/koneksimysqli.php";
    $filter="where subpost='$_POST[ukode]'";
    if (empty($_POST['ukode'])) $filter="";
    echo "<option value='' selected>-- Pilihan --</option>";
    $tampil=mysqli_query($cnmy, "SELECT kodeid, nama FROM dbmaster.brkd_otc $filter order by kodeid");
    while($a=mysqli_fetch_array($tampil)){
    echo "<option value='$a[kodeid]'>$a[kodeid] - $a[nama]</option>";
    }
}


?>