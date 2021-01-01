<html>
<head>
</head>
<body>
<form> 

<?php
	include("../../../config/koneksimysqli_it.php");
	include_once("../../../common.php");
	$divprodid = $_GET['divprodid']; 
	$query = "select kodeid,nama,divprodid from hrd.br_kode where (divprodid='$divprodid' and br = '')  and (divprodid='$divprodid' and br<>'N') order by nama";
	$result = mysqli_query($cnit, $query); 
	$record = mysqli_num_rows($result);
	//echo"$record";
	for ($i=0;$i < $record;$i++) {
	    $row = mysqli_fetch_array($result); 
		$kodeid  = $row['kodeid'];
		$nama = $row['nama'];
	    echo "<option value=\"$kodeid\">$nama</option>";
	}
	//echo"$kodeid";
?>
</body>
</form> 
</html>
