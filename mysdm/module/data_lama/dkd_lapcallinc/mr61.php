
<html>
<head>
  <title>Laporan Call Incentive</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<body>
<?php
	include("../../../config/koneksimysqli.php");
	include("../../../config/common.php");
  	session_start();

	$srid = $_POST['srid'];
	$cycleid = "";//$_POST['cycleid'];
	$show = "";//$_POST['show'];

	$bulan1 = $_POST['bulan1'];
	$nm_bln1 = nama_bulan($bulan1); //echo "$nm_bln1";
	$tahun1 = $_POST['tahun1'];
	$periode1 = $tahun1.'-'.$bulan1; 

 
  	$sr_id = substr('0000000000'.$srid,-10);
  	$query = "SELECT nama FROM hrd.karyawan WHERE karyawanId='".$sr_id."'";
  	// echo "$query<br>";die();
  	
  	$result = mysqli_query($cnmy, $query);
  	$row = mysqli_fetch_array($result);
  	echo '<strong>'.$row['nama'].'</strong><br><br>';
 
	$query = "
	 	SELECT persen_call.*,karyawan.nama
		FROM hrd.persen_call as persen_call 
		JOIN hrd.karyawan as karyawan ON persen_call.srid = karyawan.karyawanid
		WHERE persen_call.srid='$sr_id' AND (left(tgl,7)= '$periode1')
		ORDER BY tgl
	"; 
	//echo"$query<br>";

     $result = mysqli_query($cnmy, $query);
	 $num_results = mysqli_num_rows($result);
     $row = mysqli_fetch_array($result);
	 echo '<table border="15%" cellpadding="5">';
	 echo '<tr>';
	 echo '<th align="center"><small>Tanggal</small></th>';
	 echo '<th align="center"><small>Keterangan</small></th>';
	 echo '<th align="center"><small>Call</small></th>';
	 echo '<th align="center"><small>Point</small></th>';
	 echo '</tr>';
     $i=1;
	 $first_ = 1;
	 $totcall = 0;
	 $totpoint2 = 0;
	 $jpoint = 0;
	 $totpoint1 = 0;
	 $ncycle_ = 0;
	 $npersen_ = 0;
	 while ($i <= $num_results) {
		$cycleid_ = $row['cycleid'];
		// echo "$cycleid_ -- ".$row['cycleid']."<br>";
		
		//$totpoint2 = $totpoint2 + $row['totpoint2'];
		//$totpoint1 = $totpoint1 + abs($row['totpoint1']);

		while ($cycleid_ == $row['cycleid']) {
		   echo '<tr>';
		   echo "<td><small>".$row['tgl']."</small></td>";
		   echo "<td><small>".$row['ket']."</small></td>";
		   echo '<td align="right"><small>'.$row['call1']."</small></td>";
		   $totcall = $totcall + $row['call1'];

		   if ($row['point1'] != 0) {
		      echo '<td align="right"><small>'.number_format($row['point1'],0)."</small></td>";
			  if ($row['point1'] >= 0) {
				$totpoint2 = $totpoint2 + $row['point1'];
			  }else{
				$totpoint1 = $totpoint1 + abs($row['point1']);
			  }

		   }else{
		      echo '<td>&nbsp;</td>';
		   }

		   echo '</tr>';

		   $row = mysqli_fetch_array($result);
		   $i++;
		} 
	 }  

	 $query = "SELECT jumlah FROM hrd.hrkrj WHERE left(periode1,7)='$periode1'";
	 //echo"$query<br>";

	 $result = mysqli_query($cnmy, $query);
	 $row = mysqli_fetch_array($result);
	 $num_results = mysqli_num_rows($result);
	 $jml_hari_krj = $row['jumlah']; 
	 //echo"$jml_hari_krj";
	 
	 //$query1 = "select jabatanid from karyawan where karyawanid='$sr_id'"; //echo"$query1<br>";
	 
	 //Read jabatan ID from table Persen_call bukan dari table karyawan, modified by Subhan
	$query1 = "
		SELECT DISTINCT jabatanid
		FROM hrd.persen_call 
		WHERE srid='$sr_id'
		AND (left(tgl,7)= '$periode1')
	" ;
	//echo"$query1<br>";

	 $result1 = mysqli_query($cnmy, $query1);
	 $row1 = mysqli_fetch_array($result1);
	 $num_results1 = mysqli_num_rows($result1);
	 $jabatanid = $row1['jabatanid']; //echo"$query1";
	 
	 if ($jabatanid=='08') {
		$jab = 4;
	 } else {
		 if (($jabatanid=='10') or ($jabatanid=='18')) {
			$jab = 6;
		 } else {
			if ($jabatanid=='15') {
				$jab = 10;
			}
		 }
	 }
	 
	 //echo"$jab<br>";
	 if (empty($jab)) $jab=0;
	 if (empty($jml_hari_krj)) $jml_hari_krj=0;

	 $jpoint = $jab * $jml_hari_krj;
	 //echo"$jpoint = $jab * $jumlah<br>";

	 if ((DOUBLE)$jpoint-(DOUBLE)$totpoint1==0) {
		$summary_=0;
	 }else{
		$summary_ = (((DOUBLE)$totcall+(DOUBLE)$totpoint2) / ((DOUBLE)$jpoint-(DOUBLE)$totpoint1)) * 100;
	 }
	 //echo "call : $totcall, point2 : $totpoint2, jpoint : $jpoint, point1 : $totpoint1<br/>";
	 // echo "($totcall+$totpoint2) / ($jpoint-$totpoint1)";

	 echo '<tr>';
     echo '<td>&nbsp;</td>';
	 echo '<td><small><b>Summary</b></small></td>';
	 echo '<td align="right"><small><b>'.round($summary_,2).'%</b></small></td>';
  	 echo '<td>&nbsp;</td>';
	 echo '</tr>';
	 echo '</table>';

  if (empty($_SESSION['USERID'])) {
     echo '<a href="mr0.php">Isi DKD</a><br>';
  } else {
  
     //do_show_menu($_SESSION['JABATANID'],'N');
  }
?>

  
</body>
</html>
