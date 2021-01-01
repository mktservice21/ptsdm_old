<html>
<head>
</head>
<body>
<form> 

<?php
	include("../../../config/koneksimysqli_it.php");
	include_once("../../../common.php");

	if (!connect_server()) {
		echo 'Error connection to database';
		exit;
	}
	mysqli_select_db('hrd');
	
	$mr_id = $_GET['mr_id']; 
	$dokterid = $_GET['dokterid']; 
	
	$query1 = "select tgl,awal,cn from hrd.mr_dokt where dokterid='$dokterid'  and karyawanid='$mr_id'"; 
	$result1 = mysqli_query($cnit, $query1); 
	$record1 = mysqli_num_rows($result1);
	for ($i=0;$i < $record1;$i++) {
		$row = mysqli_fetch_array($result1); 
		$tglawal_ = substr($row['tgl'],0,7);
		if ($tglawal_=="0000-00") {
			$tglawal_ = date('Y-').'01';
		}
		$awal = $row['awal'];
		$cn = $row['cn'];
	}	
	
	$query2 = "select max(bulan) as bulan from hrd.ks1 where dokterid='$dokterid' and srid='$mr_id'";
	$result2 = mysqli_query($cnit, $query2); 
	$record2 = mysqli_num_rows($result2);	
	for ($i=0;$i < $record2;$i++) {
	    $row = mysqli_fetch_array($result2); 
		$bulan = substr($row['bulan'],0,7); //echo"balan=$bulan";
	} 		  
	
	$query3 = "select SUM(qty*ks1.hna*$cn/100) as jumlah
			  from hrd.ks1 ks1 
			  join hrd.dokter dokter on ks1.dokterid=dokter.dokterid
			  join MKT.iproduk on ks1.iprodid = MKT.iproduk.iProdId 
			  join hrd.mr_apt mr_apt on (ks1.srid=mr_apt.srid and ks1.aptid=mr_apt.aptid)
			  where ks1.srid='$mr_id' and  ('$tglawal_' <= bulan and bulan<='$bulan')  
			  and ks1.dokterid='$dokterid' and ks1.apttype=1 GROUP by ks1.srid order by ks1.dokterid,bulan"; 	
	$result3 = mysqli_query($cnit, $query3); 
	$record3 = mysqli_num_rows($result3);
	for ($i=0;$i < $record3;$i++) {
		$row = mysqli_fetch_array($result3); 
		$jumlah1 = $row['jumlah']; //echo"jml1,$jumlah1<br>";
	}		

	$query4 = "select ks1.apttype,SUM(qty*ks1.hna*0.8*$cn/100) as jumlah
			  from hrd.ks1 ks1 
			  join hrd.dokter dokter on ks1.dokterid=dokter.dokterid
			  join MKT.iproduk on ks1.iprodid = MKT.iproduk.iProdId 
			  join hrd.mr_apt mr_apt on (ks1.srid=mr_apt.srid and ks1.aptid=mr_apt.aptid)
			  where ks1.srid='$mr_id' and  ('$tglawal_' <= bulan and bulan<='$bulan')  
			  and ks1.dokterid='$dokterid' and ks1.apttype<>1 GROUP by ks1.apttype order by ks1.dokterid,bulan"; 	
	$result4 = mysqli_query($cnit, $query4); 
	$record4 = mysqli_num_rows($result4);
	for ($i=0;$i < $record4;$i++) {
		$row = mysqli_fetch_array($result4); 
		$jumlah2 = $row['jumlah'];// echo"jml2,$jumlah2<br>";
	}	

	$jumlah=$jumlah1 + $jumlah2; //echo"jml=$jumlah<br>";
	 		  
	$query5 = "select max(tgl) as tgl from hrd.br0 where dokterid='$dokterid'";
	$result5 = mysqli_query($cnit, $query5); 
	$record5 = mysqli_num_rows($result5);	
	for ($i=0;$i < $record5;$i++) {
	    $row = mysqli_fetch_array($result5); 
		$bln = substr($row['tgl'],0,7); //echo"btln=$bln";
	} 		  
			  
	$query6 = "select SUM(JUMLAH) as jml from hrd.br0 where dokterid='$dokterid' and left(tgl,7)<='$bln' GROUP BY DOKTERID order by tgl";
	$result6 = mysqli_query($cnit, $query6); 
	$record6 = mysqli_num_rows($result6);
	for ($i=0;$i < $record6;$i++) {
	    $row = mysqli_fetch_array($result6); 
		$jml = $row['jml'];
	} 
	
	$total=$awal-$jumlah+$jml; 
	if ($total==0) {
		$query7 = "select akhir from hrd.cn where karyawanid='$mr_id' and dokterid='$dokterid'"; 
		$result7 = mysqli_query($cnit, $query7); 
		$record7 = mysqli_num_rows($result7);
		for ($i=0;$i < $record7;$i++) {
		    $row = mysqli_fetch_array($result7); 
			$akhir = $row['akhir'];
	} 
		echo '<input value='.number_format($akhir,0)." disabled>";
		echo "<input type=\"hidden\" name=\"cn\" id=\"cn\" value=\"$total\">";
	} else {
		echo '<input value='.number_format($total,0)." disabled>";
		echo "<input type=\"hidden\" name=\"cn\" id=\"cn\" value=\"$total\">";
	}
	
?>
</body>
</form> 
</html>
