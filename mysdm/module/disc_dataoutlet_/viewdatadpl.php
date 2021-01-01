<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="viewdataareacabang") {
    $pidcabang=$_POST['udcab'];
    
    include "../../config/koneksimysqli.php";
    
    $query = "select icabangid as icabangid, areaid as areaid, nama as nama from MKT.iarea WHERE icabangid='$pidcabang' ";
    $query .=" AND IFNULL(aktif,'')<>'N' ";
    $query .=" order by nama";
    $tampila= mysqli_query($cnmy, $query);
    $ketemua= mysqli_num_rows($tampila);
    if ((INT)$ketemua==0) echo "<option value='' selected>--Pilih--</option>";
    while ($arow= mysqli_fetch_array($tampila)) {
        $nidarea=$arow['areaid'];
        $nnmarea=$arow['nama'];

        if ($nidarea==$pidarea) 
            echo "<option value='$nidarea' selected>$nnmarea</option>";
        else
            echo "<option value='$nidarea'>$nnmarea</option>";

    }
    
    mysqli_close($cnmy);
    
}elseif ($pmodule=="viewdatacustomer") {
    $pidcabang=$_POST['udcab'];
    $pidarea=$_POST['udarea'];
    $pidcust="";
    
    include "../../config/koneksimysqli_it.php";
    $cnmy=$cnit;
    
    $query = "select icabangid as icabangid, areaid as areaid, icustid as icustid, nama as nama from MKT.icust WHERE "
            . " icabangid='$pidcabang' AND areaid='$pidarea' ";
    $query .=" AND IFNULL(aktif,'')<>'N' ";
    $query .=" order by nama";
    $tampila= mysqli_query($cnmy, $query);
    $ketemua= mysqli_num_rows($tampila);
    if ((INT)$ketemua==0) echo "<option value='' selected>--Pilih--</option>";
    while ($arow= mysqli_fetch_array($tampila)) {
        $nidcust=$arow['icustid'];
        $nnmcust=$arow['nama'];

        if ($nidcust==$pidcust) 
            echo "<option value='$nidcust' selected>$nnmcust ($nidcust)</option>";
        else
            echo "<option value='$nidcust'>$nnmcust ($nidcust)</option>";

    }
    
    mysqli_close($cnmy);
    
}

?>