<?php
session_status();

if ($_GET['module']=="caridataecust") {
    include "../../config/koneksimysqli_ms.php";
    $pdistidpl=$_POST['ucabang'];
    
    echo "<option value='' selected>--All--</option>";
    if (!empty($pdistidpl)) {
	echo "
		<option value='A'>ALL</option>
		<option value='B'>BARAT</option>
		<option value='T'>TIMUR</option>
	";
        $query="SELECT distid, ecabangid, nama from MKT.ecabang where distid='$pdistidpl' AND IFNULL(aktif,'')='Y'";
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
