<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="viewdataareacabang") {
    include "../../config/koneksimysqli_ms.php";
    include "../../config/fungsi_sql.php";
    $cnit=$cnms;
    $pidcabang = $_POST['ucab'];
    
    $pnamacabang= getfieldcnnew("select nama as lcfields from MKT.icabang WHERE icabangid='$pidcabang'");
    
    echo "<option value='' selected>-- Pilih Area dari : $pnamacabang --</option>";
    $query = "select icabangid as icabangid, areaid as areaid, nama as nama from MKT.iarea WHERE icabangid='$pidcabang' AND IFNULL(aktif,'')='Y' ";
    $query .= " Order by nama";
    $tampil =mysqli_query($cnit, $query);
    while ($irow=mysqli_fetch_array($tampil)){
        $pidarea=$irow['areaid'];
        $pnamaarea=$irow['nama'];
        $iidarea=(INT)$pidarea;
        echo "<option value='$pidarea'>$pnamaarea ($iidarea)</option>";
    }
                                            
    mysqli_close($cnit);
}elseif ($pmodule=="viewdatadariareacabang") {
    include "../../config/koneksimysqli_ms.php";
    include "../../config/fungsi_sql.php";
    $cnit=$cnms;
    
    $pdridcabang = $_POST['udrcab'];
    
    $pnamacabang= getfieldcnnew("select nama as lcfields from MKT.icabang WHERE icabangid='$pdridcabang'");
    
    echo "<option value='' selected>-- Pilih Area dari : $pnamacabang --</option>";
    $query = "select DISTINCT a.icabangid as icabangid, a.areaid as areaid, a.nama as nama "
            . " from MKT.iarea as a JOIN MKT.ecust as b on a.icabangid=b.icabangid AND a.areaid=b.areaid "
            . " JOIN MKT.icust as c on b.icabangid=c.icabangid AND b.areaid=c.areaid AND b.icustid=c.icustid "
            . " WHERE a.icabangid='$pdridcabang' AND IFNULL(a.aktif,'')='Y' ";
    $query .= " Order by nama";
    $tampil =mysqli_query($cnit, $query);
    while ($irow=mysqli_fetch_array($tampil)){
        $pidarea=$irow['areaid'];
        $pnamaarea=$irow['nama'];
        $iidarea=(INT)$pidarea;
        echo "<option value='$pidarea'>$pnamaarea ($iidarea)</option>";
    }
                                            
    mysqli_close($cnit);
}

?>

