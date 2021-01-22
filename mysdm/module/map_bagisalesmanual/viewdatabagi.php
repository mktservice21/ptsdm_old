<?php
session_status();

if ($_GET['module']=="caridataecust") {
    include "../../config/koneksimysqli_ms.php";
    $pdistidpl=$_POST['ucabang'];
    
    echo "<option value=''>--All--</option>";
    if (!empty($pdistidpl)) {
        $query="SELECT distid, ecabangid, nama from MKT.ecabang where distid='$pdistidpl' ";
        $query .=" order by nama";
        $tampil= mysqli_query($cnms, $query);
        while ($row= mysqli_fetch_array($tampil)) {
            $pidecab=$row['ecabangid'];
            $pnmecab=$row['nama'];
            echo "<option value='$pidecab'>$pnmecab ($pidecab)</option>";
        }
       
    }
    
    mysqli_close($cnms);
    
}


?>
