<?php

if ($_GET['module']=="viewdatakode"){
    
    include "../../config/koneksimysqli.php";
    $mydivisi = $_POST['udivisi'];
    //$fil="";
    //if (!empty($mydivisi)) 
    $fil = " AND g_divisi = '$mydivisi'";
    $query = "select kodeid, nama from dbmaster.t_budget_kode_otc WHERE 1=1 $fil ";
    $query .= " ORDER BY kodeid";
    $tampil = mysqli_query($cnmy, $query);
    echo "<option value='' selected>--Pilihan--</option>";
    while ($z= mysqli_fetch_array($tampil)) {
        echo "<option value='$z[kodeid]'>$z[kodeid] - $z[nama]</option>";
    }
    
    
}