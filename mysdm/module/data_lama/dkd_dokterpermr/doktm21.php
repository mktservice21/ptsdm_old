<html>
<head>
  <title>Data Dokter Per MR</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<script src="doktm.js">
</script>
<form id="doktm21" action="" method=post>
<?php
	include("../../../config/koneksimysqli.php");
	include("../../../config/common.php");
	

	$tahun = date('Y') - 1;
	$karyawanid = $_POST['karyawanid'];
	
	$query = "select nama,icabangid from hrd.karyawan where karyawanId='$karyawanid'";
    $result = mysqli_query($cnmy, $query);
    $row = mysqli_fetch_array($result);
    $num_results = mysqli_num_rows($result);
	$icabangid = $row['icabangid'];
    $nmmr = $row['nama'];
    echo "<b>Nama MR/AM/SPV/DM : $nmmr</b><br><br>";
	
	$query="select dokter.dokterId, dokter.nama as nmdokter, dokter.spId, dokter.bagian, dokter.alamat1, dokter.alamat2, dokter.kota,
			mr_dokt.karyawanId, mr_dokt.dokterId, 'Y' as aktif, spesial.spId, spesial.nama as nmspId
			from hrd.dokter as dokter
			join hrd.mrdoktbaru as mr_dokt on dokter.dokterId=mr_dokt.dokterId
			join hrd.spesial as spesial on dokter.spId=spesial.spId
			where mr_dokt.karyawanId='$karyawanid' 
			order by nmdokter"; //echo "$query";
	$result = mysqli_query($cnmy, $query); 
	$num_results = mysqli_num_rows($result);
	echo "Jumlah dokter : $num_results<br>";
	echo "<table border=1>";
	echo "<tr>";
	echo "<th>ID Dokter</th>";
	echo "<th>Nama Dokter</th>";
	echo "<th>Bagian</th>";
	echo "<th>Spid</th>";
	echo "<th>Alamat</th>";
	echo "<th>Kota</th>";
	echo "<th>KS</th>";
	echo "<th>DKD</th>";
	echo "<th>BR</th>";
	echo "<th>Aktif</th>";
	echo "<th>&nbsp;</th>";
	echo "</tr>";
	
	for ($i=0; $i<$num_results; $i++) {
		$row = mysqli_fetch_array($result);
		$dokterid = $row['dokterId'];
		$nmdokter = $row['nmdokter'];
		$bagian = $row['bagian'];
		$nmspId = $row['nmspId'];
		$alamat1 = $row['alamat1'];
		$alamat2 = $row['alamat2'];
		$kota = $row['kota'];
		$aktif = $row['aktif']; //echo"aktif=$aktif";
		
		$j = "0000" . $i;
		$j = substr($j,-4);
	    $var_ = "dokt" . $j;   //nama variable diawali dengan "cust", contoh cust0001,cust0002,dst.
		$dokt = $dokterid;  //ganti $j dengan custid dari database
		
		echo "<tr>";
		echo "<td><small> $dokterid </small></td>";
		echo "<td><small> $nmdokter </small></td>";
		if ($bagian=="") {
			echo "<td>&nbsp;</td>";
		} else {
			echo "<td><small> $bagian </small></td>";
		}
		echo "<td><small> $nmspId </small></td>";
		if ($alamat1=="") {
			echo "<td>&nbsp;</td>";
		} else {
			echo "<td><small> $alamat1 </small></td>";
		}
		if ($kota=="") {
		    echo "<td>&nbsp;</td>";
		} else {
			echo "<td><small> $kota </small></td>";
		}
		
		$query_ks = "select * from hrd.ks1 where srid='$karyawanid' and dokterid='$dokterid' "; //echo"$query_ks";
		$result_ks = mysqli_query($cnmy, $query_ks);
		$num_results_ks = mysqli_num_rows($result_ks); 
		if ($num_results_ks) {
			 echo "<td align=center><b><small> Y </b></small></td>";
		} else {
			 echo "<td align=center><small> N </small></td>";
		}
		
		$query_dkd = "select * from hrd.dkd1 where srid='$karyawanid' and dokterid='$dokterid' "; //echo"$query_ks";
		$result_dkd = mysqli_query($cnmy, $query_dkd);
		$num_results_dkd = mysqli_num_rows($result_dkd); 
		if ($num_results_dkd) {
			 echo "<td align=center><b><small> Y </b></small></td>";
		} else {
			 echo "<td align=center><small> N </small></td>";
		}
		$query_br = "select * from hrd.br0 where mrid='$karyawanid' and dokterid='$dokterid' "; //echo"$query_ks";
		$result_br = mysqli_query($cnmy, $query_br);
		$num_results_br = mysqli_num_rows($result_br); 
		if ($num_results_br) {
			 echo "<td align=center><b><small> Y </b></small></td>";
		} else {
			 echo "<td align=center><small> N </small></td>";
		}
		if ($aktif=='Y') {
			$checked_ = "checked";
			echo "<td><input type='checkbox' name='$var_' value='$dokt' $checked_ >";
		} else {
			echo '<td><input type="checkbox" name="'.$var_.'" value="'.$dokt.'"></td>';
		}
		/*if (($num_results_ks <> '') or ($num_results_br <> '')) {
		//if (($num_results_ks <> '') or ($num_results_dkd <> '') or ($num_results_br <> '')) {
			echo "<td>&nbsp;</td>";
		} else {
		    echo "<td><a href=doktm223.php?karyawanid=$karyawanid&dokterid=$dokterid>Delete</a></td>";
		}*/
		 echo "<td><a href=doktm223.php?karyawanid=$karyawanid&dokterid=$dokterid>Delete</a></td>";
		echo "</tr>";
	}
	echo "</table>";
	echo "<input type=hidden name=num_results value=$num_results />";
    echo "<input type=hidden name=icabangid id=icabangid value='$icabangid'>";	
    echo "<input type=hidden name=karyawanid id=karyawanid value='$karyawanid'>";	
//	echo "<br><input type=button id=cmdAdd name=cmdAdd value='Add' onclick='gotoPage(\"doktm22.php\")'>";
//	echo "&nbsp&nbsp&nbsp<input type=button id=cmdCopy name=cmdCopy value='Copy' onclick='gotoPage(\"doktm23.php\")'>";
	//echo"$num_results_ks";
//	echo "&nbsp&nbsp&nbsp<input type=button id=cmdMove name=cmdMove value='Move' onclick='gotoPage(\"doktm24.php\")'>";
	echo "&nbsp&nbsp&nbsp<input type=button id=cmdSave name=cmdSave value='Save' onclick='gotoPage(\"doktm25.php\")'>";
	//echo "&nbsp&nbsp&nbsp<input type=button id=cmdSave name=cmdSave value='Save' onclick='disp_confirm(\"Simpan ?\")'>";
	
	if (empty($_SESSION['srid'])) {
	} else {
		do_show_menu($_SESSION['jabatanid'],'N');
	}
?>
</form>
</body>
</html>
	