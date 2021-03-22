<html>
<head>
  <title>Rekap DKD </title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<body>
<?php
	include("../../../config/koneksimysqli.php");
	include("../../../config/common.php");
  session_start();

  $jabatan = $_SESSION['JABATANID'];
  if($jabatan==20){
  	$srid = $_SESSION['USERID'];
  }else{
  		if (isset($_POST['srid'])) $srid = $_POST['srid'];
	  else $srid = $_SESSION['USERID'];
  }
  $cycleid = $_POST['cycleid'];
  $tgl1 = $_POST['tgl1'];
  $tgl2 = $_POST['tgl2'];
  $show = $_POST['show'];
  // echo "$srid~$jabatan<br>";

 
  $sr_id = substr('0000000000'.$srid,-10);
  // echo "$sr_id<br>";

  $query = "SELECT nama, iCabangId as icabangid FROM hrd.karyawan WHERE karyawanId='".$sr_id."'";
  $result = mysqli_query($cnmy, $query);
  $row = mysqli_fetch_array($result);
  $cabang = $row['icabangid'];

  echo '<strong>'.$row['nama'].'</strong><br>';
  echo 'Cycle : '.$cycleid.'<br>';
  if ($show=="1" || $show=="2") {
	  $header_ = add_space('Nama Dokter',40);
	  echo '<table border="1" cellpadding="3">';
	  echo "<tr>\n";
	  echo '<th align="left"><small>'.$header_."</small></th>\n";
	  $header_ = add_space('Spesialisasi',30);
	  echo '<th align="left"><small>'.$header_."</small></th>\n";
	  $header_ = add_space('Bagian',40);
	  echo '<th align="left"><small>'.$header_."</small></th>\n";
	  echo "<th><small>01</small></th>\n";
	  echo "<th><small>02</small></th>\n";
	  echo "<th><small>03</small></th>\n";
	  echo "<th><small>04</small></th>\n";
	  echo "<th><small>05</small></th>\n";
	  echo "<th><small>06</small></th>\n";
	  echo "<th><small>07</small></th>\n";
	  echo "<th><small>08</small></th>\n";
	  echo "<th><small>09</small></th>\n";
	  echo "<th><small>10</small></th>\n";
	  echo "<th><small>11</small></th>\n";
	  echo "<th><small>12</small></th>\n";
	  echo "</tr>\n";

	  if($jabatan == 20){
	  	$where = "WHERE (dkd0.srid_app='".$sr_id."')";
	  }else{
	  	$where = "WHERE (dkd1.srid='".$sr_id."')";
	  }

	  if ($cycleid=="All") {
	      	$query2 = "
	      		SELECT 
	      			dkd1.*,cycle.cycleid,cycle.tgl as cycle_tgl,cycle.thnbln,dkd0.srId as dkd0_srid,dokter.dokterid as dokter_dokterid,dokter.nama,
	      			dokter.bagian,dokter.spid,spesial.nama as sp_nama,dkd0.posted,dkd0.tgl as dkd0_tgl
	            FROM hrd.dkd1 as dkd1
	            JOIN hrd.cycle as cycle ON dkd1.tgl=cycle.tgl 
	            JOIN hrd.dokter as dokter ON dkd1.dokterId=dokter.dokterId
	            JOIN hrd.dkd0 as dkd0 ON (dkd1.srid=dkd0.srid AND dkd1.tgl=dkd0.tgl)              
				JOIN hrd.spesial as spesial ON (dokter.spid=spesial.spid) 
	            $where
	            AND (dkd0.posted='1') 
	            AND ('".$tgl1."' <= dkd1.tgl AND dkd1.tgl <= '".$tgl2."') 
	            ORDER BY nama,tgl
	         ";
	  } else {
	    $query2 = "
	      	SELECT 
	      		dkd1.*,cycle.cycleid,cycle.tgl as cycle_tgl,cycle.thnbln,dkd0.srId as dkd0_srid,dokter.dokterid as dokter_dokterid,dokter.nama,dokter.bagian,
	      		dokter.spid,spesial.nama as sp_nama,dkd0.posted,dkd0.tgl as dkd0_tgl
	      	FROM hrd.dkd1 as dkd1
	        JOIN hrd.cycle as cycle ON dkd1.tgl=cycle.tgl 
	        JOIN hrd.dokter as dokter ON dkd1.dokterId=dokter.dokterId
	        JOIN hrd.dkd0 as dkd0 ON (dkd1.srid=dkd0.srid and dkd1.tgl=dkd0.tgl)              
			JOIN hrd.spesial as spesial ON (dokter.spid=spesial.spid) 
            $where
	        AND (dkd0.posted='1') 
	        AND ('".$tgl1."' <= dkd1.tgl AND dkd1.tgl <= '".$tgl2."') 
	        AND cycle.cycleid='".$cycleid."'
	        ORDER BY nama,tgl
	    ";

	  }
      // echo"$query2<br>";die();
	  
	  $result2 = mysqli_query($cnmy, $query2);
	  $num_results = mysqli_num_rows($result2);

	  $i=0;
	  $first_ = 1;
	  while ($i <= $num_results) 
	  {
	    if ($first_) {
	       $row = mysqli_fetch_array($result2);
	       $i++;
	       $first_ = 0;
	    }
	    $cycle_id = $row['cycleid'];
	    //if ( ($row['cycleid'] == $cycleid) && ($row['thnbln'] == substr($tgl2,0,7)) ) {
	    if ( ($row['cycleid'] == $cycleid) || ($cycleid=="All") ) {
	       //echo $row['thnbln'].'='.substr($tgl2,0,7).'=<br>';
	       $dokter_id = $row['dokterId'];
	       if ($dokter_id <> '0000000000') {
	          $dokter_nama = add_space(rtrim($row['nama']),40);
	          $dokter_bagian = add_space(rtrim($row['bagian']),40);
	          $sp_nama = add_space(rtrim($row['sp_nama']),30);
	          echo "<tr>\n";
	          echo "<td><small>".$dokter_nama."</small></td>\n";
	          echo "<td><small>".$sp_nama."</small></td>\n";
	          echo "<td><small>".$dokter_bagian."</small></td>\n";
	          for ($a=1;$a<=12;$a++) {
	              $bln_[$a] = '';
	          }
	          while ( $dokter_id==$row['dokterId'] ) {
	              $bulan_ = intval(substr($row['tgl'],5,2));
	              $bulan_ = intval(substr($row['thnbln'],5,2));
	              $tgl_ = substr($row['tgl'],8,2);
	              //echo $tgl_.'-'.$bulan_;
	              $bln_[$bulan_] = $bln_[$bulan_] . $tgl_ . '&nbsp;';
	              $row = mysqli_fetch_array($result2);
	              $i++;
	          }
	          for ($a=1;$a<=12;$a++) {
	              if ($bln_[$a] == '') {
	                  $bln_[$a] = '&nbsp';
	              }
	              echo "<td><small>".$bln_[$a]."</small></td>\n";
	          }
	          echo "</tr>\n";
	       } else {
	         $row = mysqli_fetch_array($result2);
	         $i++;
	       }
	    } else {
	      $row = mysqli_fetch_array($result2);
	      $i++;
	    }  // if cycle_id
	  };  // end while
	  echo "</table>";
  }  
  // if show=1 or show=2	

  if ($show=="1" || $show=="3") {
     //echo '<br>';
     if ($cycleid=="All") {
        $query = "
        	SELECT persen_call.*,karyawan.nama
            FROM hrd.persen_call as persen_call 
            JOIN hrd.karyawan as karyawan ON persen_call.srid = karyawan.karyawanid
			WHERE persen_call.srid='$sr_id' AND ('$tgl1' <= tgl AND tgl<= '$tgl2')
            ORDER BY cycleid,tgl
        ";
	 } else {
        $query = "
        	SELECT persen_call.*,karyawan.nama
            FROM hrd.persen_call as persen_call
            JOIN hrd.karyawan as karyawan ON persen_call.srid = karyawan.karyawanid
			WHERE persen_call.srid='$sr_id' AND ('$tgl1' <= tgl AND tgl<= '$tgl2') AND cycleid='$cycleid'
            ORDER BY tgl
        ";
	 
	 }
     // echo"$query<br>";

     $result = mysqli_query($cnmy, $query);
	 $num_results = mysqli_num_rows($result);
     $row = mysqli_fetch_array($result);

	 echo '
	 	<table border="15%" cellpadding="5">
	 		<tr>
		 		<th align="center"><small>Tanggal</small></th>
		 		<th align="center"><small>Keterangan</small></th>
		 		<th align="center"><small>Call</small></th>
		 		<th align="center"><small>Point</small></th>
	 		</tr>
	 ';
	 
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
		$totcall = $totcall + $row['totcall'];
		$totpoint2 = $totpoint2 + $row['totpoint2'];
		$jpoint = $jpoint + $row['jpoint'];
		$totpoint1 = $totpoint1 + abs($row['totpoint1']);
		$persen = $row['persen'];

		echo '
			<tr>
				<td><small><b>Cycle : $cycleid_</b></small></td>
				<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
			</tr>
		';

       $ncycle_ ++;
		while ($cycleid_ == $row['cycleid']) {
		   	echo "
		   		<tr>
		   			<td><small>".$row['tgl']."</small></td>
		   			<td><small>".$row['ket']."</small></td>
		   			<td align='right'><small>".$row['call1']."</small></td>
		   	";

		   if ($row['point1'] != 0) {
		      echo '<td align="right"><small>'.number_format($row['point1'],0)."</small></td>";
		   } else {
		      echo '<td>&nbsp;</td>';
		   }

		   echo '</tr>';

		  	/* 
			  	if ($row['ketid2']!="000") {
			      echo '<tr>';
			      echo '<td>&nbsp;</td>';
				  echo "<td><small>".$row['ket2']."</small></td>";
				  echo '<td>&nbsp;</td>';
				  echo '<td align="right"><small>'.number_format($row['point2'],0)."</small></td>";
			      echo '</tr>';
			   }
		   */

		   $row = mysqli_fetch_array($result);
		   $i++;
		} // break per cycleid
		//summary
		// echo $persen.'~'.$i;

		echo '
			<tr>
				<td>&nbsp;</td>
				<td><small><b>Per Cycle</b></small></td>
				<td align="right"><small><b>'.number_format($persen,2).'%</b></small></td>
				<td>&nbsp;</td>
			</tr><br>
		';
		$npersen_ = $npersen_ + $persen;
	 }  // end while /eof

	 // echo "(($totcall+$totpoint2) / ($jpoint-$totpoint1)) * 100<br>";

	 if ((DOUBLE)$jpoint-(DOUBLE)$totpoint1==0) {
		$summary_=0;
	 }else{
		$summary_ = (((DOUBLE)$totcall+(DOUBLE)$totpoint2) / ((DOUBLE)$jpoint-(DOUBLE)$totpoint1)) * 100;
	 }

	 // $summary_ = round(($npersen_ / $ncycle_),0) ;
	 echo '
	 	<tr>
	 		<td>&nbsp;</td>
	 		<td><small><b>Summary</b></small></td>
	 		<td align="right"><small><b>'.number_format($summary_,0).'%</b></small></td>
	 		<td>&nbsp;</td>
	 	</tr>
	 	</table>
	 ';

  }  // if show=1 or show=3 persentase call
  //echo '<a href="mr4.php">Lihat lagi</a><br>';
  if (empty($_SESSION['srid'])) {
     echo '<a href="mr0.php">Isi DKD</a><br>';
  } else {
     //echo '<a href="auth2.php">Menu</a><br>';
     do_show_menu($_SESSION['jabatanid'],'N');
  }
?>

  
</body>
</html>
