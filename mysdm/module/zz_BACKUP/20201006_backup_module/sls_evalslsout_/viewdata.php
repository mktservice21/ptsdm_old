<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="caridatacustid") {
    include "../../config/koneksimysqli_ms.php";
    
    $pidcabpilih=$_POST['ucabid'];
    $pidareapilih=$_POST['uareaid'];
    $pidkrypilih=$_POST['umr'];
    
    $pmyjabatanid=$_SESSION['JABATANID'];
    
    $query = "SELECT iCabangId, areaId, iCustId, nama from sls.icust WHERE  "
            . " 1=1 ";
    
    if ($pmyjabatanid=="15xxx") {
        $query .=" AND CONCAT(iCabangId,areaId) IN (SELECT DISTINCT CONCAT(iCabangId,areaId) FROM sls.imr0 WHERE karyawanId='$pidkrypilih') ";
    }else{
        if (!empty($pidcabpilih)) {
            $query .=" AND iCabangId='$pidcabpilih' ";
        }
        
        if (!empty($pidareapilih)) {
            $query .=" AND areaId='$pidareapilih' ";
        }
    }
    
    $query .=" order by nama";
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
}elseif ($pmodule=="cariareacabang") {
    $pmyjabatanid=$_SESSION['JABATANID'];
    $pmrpilih=$_SESSION['IDCARD'];
    $pidareapil="";
    
    include "../../config/koneksimysqli_ms.php";
    
    $pidcabang=$_POST['ucabid'];
    
    echo "<option value=''>-- All --</option>";
    $query = "select icabangid, areaid, nama, aktif from sls.iarea where 1=1 AND icabangid='$pidcabang' ";
    
    
    if ($pmyjabatanid=="15") {
        $query .= " AND CONCAT(IFNULL(icabangid,''),IFNULL(areaid,'')) IN "
                . " (SELECT DISTINCT CONCAT(IFNULL(icabangid,''),IFNULL(areaid,'')) FROM sls.imr0 WHERE karyawanid='$pmrpilih') ";
    }
    $query .=" order by aktif DESC, nama";
    
    $tampil = mysqli_query($cnms, $query);
    while ($rx= mysqli_fetch_array($tampil)) {
        $nidarea=$rx['areaid'];
        $nnmarea=$rx['nama'];
        $nnmaktif=$rx['aktif'];

        $namaaktif="Aktif";
        if ($nnmaktif!="Y") $namaaktif="Non Aktif";
        
        if ($pidareapil==$nidarea)
            echo "<option value='$nidarea' selected>$nnmarea ($namaaktif)</option>";
        else
            echo "<option value='$nidarea'>$nnmarea ($namaaktif)</option>";
    }
    
    mysqli_close($cnms);
    
}

?>