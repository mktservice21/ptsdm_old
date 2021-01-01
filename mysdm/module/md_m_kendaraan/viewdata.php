<?php
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="viewdatacabang") {
    $piddivisi=$_POST['udivsi'];
    
    include "../../config/koneksimysqli.php";
    $query ="select PILIHAN from dbmaster.t_divisi_gimick WHERE DIVISIID='$piddivisi' LIMIT 1";
    $tampilk=mysqli_query($cnmy, $query);
    $nr= mysqli_fetch_array($tampilk);
    $divpiliih=$nr['PILIHAN'];
    
    echo "<option value='' selected>--Pilihan--</option>";
    if (!empty($piddivisi)) {
        if ($divpiliih=="OT") {//MKT.icabang_o
            $query = "select icabangid_o as icabangid, nama as nama from dbmaster.v_icabang_o WHERE aktif='Y' ";
        }else{
            $query = "select icabangid as icabangid, nama as nama from MKT.icabang WHERE aktif='Y' ";
        }
        $query .=" ORDER BY nama";
        $tampil= mysqli_query($cnmy, $query);
        while ($row= mysqli_fetch_array($tampil)) {
            $npidcab=$row['icabangid'];
            $npnmcab=$row['nama'];

            if ($npidcab==$pcabangid)
                  echo "<option value='$npidcab' selected>$npnmcab</option>";
            else
                echo "<option value='$npidcab'>$npnmcab</option>";
        }
    }
    
    mysqli_close($cnmy);
    
}

?>