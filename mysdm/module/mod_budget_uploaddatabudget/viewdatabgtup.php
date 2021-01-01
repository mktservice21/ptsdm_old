<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="caridatacabang") {
    include "../../config/koneksimysqli.php";
    
    $piddivipl=$_POST['udivpl'];
    
    $icabangid="";
    if (!empty($_SESSION['BGTUPDCAB'])) $icabangid=$_SESSION['BGTUPDCAB'];
    
    $pketdivipl="";
    if ($piddivipl=="OTC" OR $piddivipl=="OT" OR $piddivipl=="CHC") {
        $pketdivipl="";
        
        $query = "select icabangid_o as icabangid, nama as nama From dbmaster.v_icabang_o 
            WHERE IFNULL(aktif,'')<>'N' ";//AND icabangid_o NOT IN ('0000000001') 
        $query .= " ORDER BY nama";
    }else{
        $query = "select icabangid as icabangid, nama as nama From MKT.icabang
            WHERE IFNULL(aktif,'')<>'N' ";
        $query .= " AND LEFT(nama,5) NOT IN ('OTC -', 'PEA -')";
        $query .= " ORDER BY nama";
    }
    
    echo "<option value='' selected>--Pilih--</option>";
    $tampil = mysqli_query($cnmy, $query);
    while ($z= mysqli_fetch_array($tampil)) {
        $pcabangid=$z['icabangid'];
        $pcabnm=$z['nama'];
        $pcabid=(INT)$pcabangid;
        if ($pcabangid==$icabangid)
            echo "<option value='$pcabangid' selected>$pcabnm $pketdivipl</option>";
        else
            echo "<option value='$pcabangid'>$pcabnm $pketdivipl</option>";
    }
    
    mysqli_close($cnmy);
    
}

?>

