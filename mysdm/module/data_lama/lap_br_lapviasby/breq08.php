<html>
<head>
</head>
<body>
<form> 

<?php
	include("../../../config/koneksimysqli_it.php");
	include_once("../../../common.php");
	
	$karyawanid = $_GET['karyawanid']; 
	$query = "select karyawan.icabangid, MKT.icabang.nama from hrd.karyawan join MKT.icabang on karyawan.icabangid=icabang.icabangid
			  where karyawanid='$karyawanid'"; 
	$result = mysqli_query($cnit, $query); 
	$record = mysqli_num_rows($result);
	//echo"$record";
	for ($i=0;$i < $record;$i++) {
	    $row = mysqli_fetch_array($result); 
		$icabangid  = $row['icabangid'];
		$nama = $row['nama'];
	    echo "<option value=\"$icabangid\">$nama</option>";
	}
	//echo"$dokterid";
?>
</body>
</form> 
</html>
