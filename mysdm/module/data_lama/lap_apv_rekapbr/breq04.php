<html>
<head>
</head>
<body>
<form> 

<?php
	include("../../../config/koneksimysqli_it.php");
	include_once("../../../common.php");
	$icabangid = $_GET['icabangid']; 
	if (($icabangid=='30') or ($icabangid=='31') or ($icabangid=='0000000032')) {
		$query = "select karyawanId,nama,jabatanid,icabangid from hrd.karyawan where (karyawanId='0000000154' or karyawanId='0000000159') AND aktif = 'Y' order by nama"; 
	} else {
		$query = "select karyawanId,nama,jabatanid,icabangid from hrd.karyawan where icabangid='$icabangid' AND aktif = 'Y' order by nama"; 
	}
	$result = mysqli_query($cnit, $query); 
	$record = mysqli_num_rows($result);
	//echo"$record";
	for ($i=0;$i < $record;$i++) {
	    $row = mysqli_fetch_array($result); 
		$karyawanId  = $row['karyawanId'];
		$nama = $row['nama'];
	    echo "<option value=\"$karyawanId\">$nama</option>";
	}
	//echo"$dokterid";
?>
</body>
</form> 
</html>
