<html>
<head>
</head>
<body>
<form> 

<?php
	include("../../../config/koneksimysqli_it.php");
	include_once("../../../common.php");
	$karyawanid = $_GET['karyawanid']; 
	$icabangid = $_GET['icabangid']; 
    $query = "select jabatanid from hrd.karyawan where karyawanid='$karyawanid'"; 	
	$result = mysqli_query($cnit, $query);
	$records = mysqli_num_rows($result);
	$row = mysqli_fetch_array($result);
	$jabatanid = $row['jabatanid'];

	if ($icabangid=="0000000001") { //ho
		$query = "select karyawanid as mr_id, nama, areaid from hrd.karyawan order by nama"; 
	} else {
		if (($icabangid=="0000000030") or ($icabangid=='0000000031') or ($icabangid=='0000000032')){ // irian, ambon, ntt
			$query = "select karyawanid as mr_id, nama, areaid from hrd.karyawan where icabangid='$icabangid' order by nama"; 
		} else {
			if (($jabatanid=="18") or ($jabatanid=="10")) { //spv,am
				$query = "select karyawanid as mr_id, nama, areaid from hrd.karyawan where (atasanid='$karyawanid' or atasanid2='$karyawanid') order by nama";
			}
			if ($jabatanid=="08") { //dm
				$query = "select karyawanid as mr_id, nama, areaid from hrd.karyawan where icabangid='$icabangid' order by nama"; 
			}
			if ($jabatanid=="15") { // mr
				$query = "select karyawanid as mr_id, nama, areaid from hrd.karyawan where karyawanid='$karyawanid'"; 
			}
		}
	}

	$result = mysqli_query($cnit, $query); 
	$record = mysqli_num_rows($result);
	echo '<option value="blank">(blank)</option>';
	for ($i=0;$i < $record;$i++) {
	    $row = mysqli_fetch_array($result); 
		$mr_id  = $row['mr_id'];
		$areaid = $row['areaid'];
		$nama = $row['nama'];
	    echo "<option value=\"$mr_id\">$nama</option>";
	}
	
?>
</body>
</form> 
</html>
