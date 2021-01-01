<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];

if ($pmodule=="caridatadokter") {
    include "../../../config/koneksimysqli_it.php";
    
    $query = "select distinct k.dokterid as dokterid, d.nama as nama from hrd.ks1 k "
            . " join hrd.dokter d on k.dokterid=d.dokterId "
            . " where left(k.bulan,4) in ('2020') order by 2";
    $tampil = mysqli_query($cnit, $query);
    while ($z= mysqli_fetch_array($tampil)) {
        $pidoktid=$z['dokterid'];
        $pnmdoktb=$z['nama'];

        echo "&nbsp; <input type=checkbox value='$pidoktid' name='chkbox_iddok[]' checked> $pnmdoktb ($pidoktid)<br/>";
    }
    
    mysqli_close($cnit);
    
}

?>