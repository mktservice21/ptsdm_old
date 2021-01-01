<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="caridatacustid") {
    include "../../config/koneksimysqli_ms.php";
    
    $pidkrypilih=$_POST['umr'];
    
    $query = "SELECT iCabangId, areaId, iCustId, nama from sls.icust WHERE CONCAT(iCabangId,areaId) IN "
            . " (SELECT DISTINCT CONCAT(iCabangId,areaId) FROM sls.imr0 WHERE karyawanId='$pidkrypilih') "
            . " order by nama";
    $tampil = mysqli_query($cnms, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu==0) echo "<option value=''>-- Pilih --</option>";
    while ($rx= mysqli_fetch_array($tampil)) {
        $nidcab=$rx['iCabangId'];
        $nidarea=$rx['areaId'];
        $nidcustid=$rx['iCustId'];
        $nidcustnm=$rx['nama'];

        $pigrpkode=$nidcab.$nidarea.$nidcustid;

        echo "<option value='$pigrpkode'>$nidcustnm</option>";

    }
    
    mysqli_close($cnms);
}

?>