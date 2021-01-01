<?php
session_status();

if ($_GET['module']=="viewdatacabang") {
    include "../../config/koneksimysqli_ms.php";
    $pikaryawanpilih=$_POST['uidkaryawan'];
    
    $query = "select distinct idcabang, nama from ms.cbgytd where id_sm='$pikaryawanpilih' order by nama";
    $tampil=mysqli_query($cnms, $query);
    echo "<option value=''>--Pilih--</option>";
    while ($r=  mysqli_fetch_array($tampil)) {
        $picabangid=$r['idcabang'];
        $pnmcabang=$r['nama'];
        echo "<option value='$picabangid'>$pnmcabang</option>";
    }
    
    mysqli_close($cnms);
    
}


?>
