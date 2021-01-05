<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="caricabangregion") {
    $pidreg=$_POST['uregid'];
    
    $nfil_reg="";
    if (!empty($pidreg)) $nfil_reg=" AND region='$pidreg' ";
    
    
    include "../../config/koneksimysqli_ms.php";
    
    echo "<option value=''>-- Pilih --</option>";
    
    if (!empty($pidreg)) {
        if ($pidreg=="B") echo "<option value='B'>Barat</option>";
        if ($pidreg=="T") echo "<option value='T'>Timur</option>";
    }else{
        echo "<option value='N'>Nasional</option>";
        echo "<option value='B'>Barat</option>";
        echo "<option value='T'>Timur</option>";
    }
    
    $query = "select idcabang, nama from ms.cbgytd where aktif='Y' $nfil_reg order by nama";
    $tampil = mysqli_query($cnms, $query);
    while ($rx= mysqli_fetch_array($tampil)) {
        $nidcab=$rx['idcabang'];
        $nnmcab=$rx['nama'];
        echo "<option value='$nidcab'>$nnmcab</option>";
    }
    
    
    mysqli_close($cnms);
}

?>