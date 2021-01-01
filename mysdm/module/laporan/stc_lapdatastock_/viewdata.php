<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="caridataproduk") {
    
    include "../../../config/koneksimysqli_ms.php";
    
    $pdivisiid=$_POST['udivisiid'];
    
    $filterdivisi="";
    if (!empty($pdivisiid)) $filterdivisi=" AND divprodid='$pdivisiid' ";
    
    echo "<option value='' selected>--All--</option>";
    $query = "select divprodid, iprodid, nama from sls.iproduk WHERE aktif='Y' $filterdivisi ORDER BY divprodid, nama";
    $tampil= mysqli_query($cnms, $query);
    while($na= mysqli_fetch_array($tampil)) {
        $nidprod=$na['iprodid'];
        $nnmprod=$na['nama'];
        $nnmdivisi=$na['divprodid'];
        echo "<option value='$nidprod'>$nnmprod ($nnmdivisi)</option>";
    }
                                                    
    mysqli_close($cnms);
}
?>

