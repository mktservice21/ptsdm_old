<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="caridataproduk") {
    
    include "../../../config/koneksimysqli_ms.php";
    
    $pdivisiid=$_POST['udivisiid'];
    
    $filterdivisi="";
    //if (!empty($pdivisiid)) $filterdivisi=" AND divprodid='$pdivisiid' ";
    if (!empty($pdivisiid)) $filterdivisi=" AND (b.divprodid='$pdivisiid' OR IFNULL(a.iprodid,'')='') ";
    
    echo "<option value='' selected>--All--</option>";
    //$query = "select divprodid, iprodid, nama from sls.iproduk WHERE aktif='Y' $filterdivisi ORDER BY divprodid, nama";
    
    
    $query = "select a.id, a.kdproduk, a.nmproduk, b.divprodid, a.iprodid from "
            . " sls.imaping_produk a "
            . " LEFT JOIN sls.iproduk b on a.iprodid=b.iprodid WHERE 1=1 $filterdivisi ";
    $query .= " ORDER BY b.divprodid, a.nmproduk";
    
    $tampil= mysqli_query($cnms, $query);
    while($na= mysqli_fetch_array($tampil)) {
        $nidprod=$na['kdproduk'];
        $nnmprod=$na['nmproduk'];
        $nnmdivisi=$na['divprodid'];
        if (empty($nnmdivisi)) $nnmdivisi="uncategorized";
        
        echo "<option value='$nidprod'>$nnmprod ($nnmdivisi)</option>";
    }
                                                    
    mysqli_close($cnms);
}
?>

