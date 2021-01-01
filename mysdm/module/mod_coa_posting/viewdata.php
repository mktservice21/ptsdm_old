<?php

if ($_GET['module']=="viewdataposting"){
    include "../../config/koneksimysqli_it.php";
    
    $subposting = $_POST['usubpost'];
    
    $query = "select distinct kodeid, nama from hrd.brkd_otc where subpost='$subposting' order by nama ";
    
    $tampil=mysqli_query($cnit, $query);
    
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        echo "<option value='$a[kodeid]'>$a[nama]</option>";
    }
}elseif ($_GET['module']=="viewdatacoa"){
    include "../../config/koneksimysqli_it.php";
    
    $subposting = $_POST['usubpost'];
    $posting = $_POST['upost'];
    
    $query = "select distinct COA4, NAMA4 from dbmaster.coa_level4 where (kodeid='$posting' OR subpost = '$subposting') order by NAMA4";
    $tampil=mysqli_query($cnit, $query);
    $x=mysqli_fetch_array($tampil);
    $coa4=$x['COA4'];
    
    
    $query = "select distinct COA4, NAMA4 from dbmaster.v_coa_all where (DIVISI='OTC' or ifnull(DIVISI,'')='') order by NAMA4";
    $tampil=mysqli_query($cnit, $query);
    //echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        if ($a['COA4']==$coa4)
            echo "<option value='$a[COA4]' selected>$a[NAMA4]</option>";
        else
            echo "<option value='$a[COA4]'>$a[NAMA4]</option>";
    }
}

?>
