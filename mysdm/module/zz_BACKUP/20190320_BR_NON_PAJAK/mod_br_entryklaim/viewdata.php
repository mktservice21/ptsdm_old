<?php
session_start();
if ($_GET['module']=="viewdatacombocoa"){
    include "../../config/koneksimysqli_it.php";
    
    $divprodid = $_POST['udiv'];
    
    $query = "SELECT COA4, NAMA4 FROM dbmaster.v_coa_all where (DIVISI='$divprodid' or ifnull(DIVISI,'')='')";
    if ($_SESSION['ADMINKHUSUS']=="Y") {
        //$query .= " AND COA4 in (select distinct COA4 from dbmaster.coa_wewenang where karyawanId='$_SESSION[IDCARD]')";
        $query .= " AND ifnull(kodeid,'') <> ''";
    }
    $query .= " order by COA4";
    $tampil=mysqli_query($cnit, $query);
    $coa="701-03";
    if ($divprodid=="PIGEO") $coa="702-03";
    if ($divprodid=="PEACO") $coa="703-03";
    if ($divprodid=="OTC") $coa="704-03";
    
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        if ($a['COA4']==$coa)
            echo "<option value='$a[COA4]' selected>$a[NAMA4]</option>";
        else
            echo "<option value='$a[COA4]'>$a[NAMA4]</option>";
    }
}elseif ($_GET['module']=="viewdatacombokodenon"){
    include "../../config/koneksimysqli_it.php";
    include "../../config/fungsi_sql.php";
    $kodeidcoa="";
    if (!empty($_POST['ucoa']))
        $kodeidcoa= getfieldcnmy("select kodeid as lcfields from dbmaster.v_coa where COA4='$_POST[ucoa]'");
    
    $divprodid = $_POST['udiv'];
    $query = "select kodeid,nama,divprodid from dbmaster.br_kode where (divprodid='$divprodid')  "
            . " and (divprodid='$divprodid') order by nama";
    $result = mysqli_query($cnit, $query); 
    $record = mysqli_num_rows($result);
    echo "<option value='' selected>-- Pilihan --</option>";
    for ($i=0;$i < $record;$i++) {
        $row = mysqli_fetch_array($result); 
        $kodeid  = $row['kodeid'];
        $nama = $row['nama'];
        if ($kodeid==$kodeidcoa)
            echo "<option value=\"$kodeid\" selected>$nama</option>";
        else
            echo "<option value=\"$kodeid\">$nama</option>";
    }
}

?>
