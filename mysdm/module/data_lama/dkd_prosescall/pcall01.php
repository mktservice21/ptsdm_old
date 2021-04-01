<html>
<head>
  <title>Upload Persentase Call</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<script src="sekt_sl.js">
</script>
<script src="pcall.js">
</script>
<body>
<form id="pcall01" action="pcall00.php" method=post>
<?php
	include("../../../config/koneksimysqli.php");
	include("../../../config/common.php");
   session_start();
   

   if (empty($_SESSION['USERID'])) {
      echo 'not authorized';
      exit;
   } else {
         //setfocus
		 $set_focus = "";//$_POST['set_focus'];
		 if ($set_focus=="") {		    
			$set_focus = "icabangid";
		 }
		 		 
         $srid = $_SESSION['USERID'];
         $srnama = $_SESSION['NAMALENGKAP'];
		 $sr_id = substr('0000000000'.$srid,-10);
		 $tahun = date('Y');
		 
		 $icabangid = $_POST['icabangid'];
		 $bulan = $_POST['bulan'];
		 $tahun = $_POST['tahun'];
		 $thnbln = $tahun.'-'.$bulan;
		 //cycle		 

		 $query = "drop table if exists dbtemp.tmp_call";
		 $result = mysqli_query($cnmy, $query);		  		 
		 $query = "
		 	CREATE  TABLE dbtemp.tmp_call (
		 		cycleid varchar(10), 
		 		icabangid varchar(10), 
		 		jabatanid varchar(10), 
		 		nama varchar(30), 
		 		jabatan varchar(30), 
		 		call1 decimal(3) not null default '0', 
				point1 decimal(6,2) not null default '0', 
				point2 decimal(6,2) not null default '0', 
				jpoint decimal(6,2) not null default '0', 
				persen decimal(6,2) not null default '0', 
				totcall decimal(6,2) not null default '0', 
				totpoint1 decimal(6,2) not null default '0', 
				totpoint2 decimal(6,2) not null default '0', 
				rata decimal(6,2) not null default '0'
			)ENGINE=MyISAM character set='latin1'
			(SELECT dkd0.* FROM hrd.dkd0 as dkd0 WHERE 0)
		 "; 	
		 			 
		 mysqli_query($cnmy, $query);	 		 
		 $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error : $erropesan"; exit; }

		 
		 $query = "SELECT * FROM hrd.cycle WHERE thnbln='$thnbln' ORDER BY tgl";		 		 
		 // echo "$query<br>";die();

         $result = mysqli_query($cnmy, $query);
         $num_results = mysqli_num_rows($result);		 		 		 
		 $num_results_1 = $num_results-1;		 
		 if ($num_results) {
			for ($i=0; $i<$num_results;$i++) {
				$row = mysqli_fetch_array($result);				
				if ($i==0) {
					$awal_ = $row['tgl'];
				}
				if ($i==$num_results_1) {
					$akhir_ = $row['tgl'];
				}
			}
		 }

		 /*
			$query = "select dkd0.*,cycle.cycleid, karyawan.icabangid, karyawan.jabatanid, 
				  karyawan.nama, jabatan.nama as nmjabatan, jabatan.point_ as pointjbt
				  from dkd0 
				  join cycle on dkd0.tgl = cycle.tgl 
				  join karyawan on karyawan.karyawanid = dkd0.srid
				  join jabatan on jabatan.jabatanId = karyawan.jabatanId 				  
				  where ('$awal_' <= dkd0.tgl and dkd0.tgl <= '$akhir_') and (posted='1') and
				        (karyawan.icabangid='$icabangid') and dkd0.srid='0000000001' 
				  order by srid,cycleid,tgl";		 echo"$query"; 
		*/

		$query = "
		 	SELECT dkd0.*,cycle.cycleid, karyawan.icabangid, karyawan.jabatanid, karyawan.nama, jabatan.nama as nmjabatan, jabatan.point_ as pointjbt
			FROM hrd.dkd0 as dkd0  
			JOIN hrd.cycle as cycle on dkd0.tgl = cycle.tgl 
			JOIN hrd.karyawan as karyawan on karyawan.karyawanid = dkd0.srid
			JOIN hrd.jabatan as jabatan on jabatan.jabatanId = karyawan.jabatanId 				  
			WHERE left(dkd0.tgl,7)='$thnbln' 
			and dkd0.srid='0000001888'
				AND (karyawan.icabangid='$icabangid')
			ORDER BY srid,cycleid,tgl
		";		//AND (posted='1')  
		
		// echo"$query";die();

		 /*$query = "select dkd0.*,cycle.cycleid, karyawan.icabangid, karyawan.jabatanid, 
					karyawan.nama, jabatan.nama as nmjabatan, jabatan.point_ as pointjbt
					from dkd0 
					join cycle on dkd0.tgl = cycle.tgl 
					join karyawan on karyawan.karyawanid = dkd0.srid
					join jabatan on jabatan.jabatanId = karyawan.jabatanId 				  
					where left(dkd0.tgl,7)='$thnbln' and (posted='1') and
					(karyawan.icabangid='$icabangid') and dkd0.srid='0000000259'
					order by srid,cycleid,tgl";		 //echo"$query";*/

         $result = mysqli_query($cnmy, $query);
         $num_results = mysqli_num_rows($result);			 		 
		 $i=0;

		 echo "<br>";

		 if ($num_results) {
			$row = mysqli_fetch_array($result);
			while ($i<$num_results) {
				$msrId_ = $row['srid'];
				$mCycleId_ = $row['cycleid'];
				$pointjbt_ = $row['pointjbt'];
				$first_ = 1;
				$totPoint1_ = 0; $totPoint2_ = 0; $totCall_ = 0; $nhari_ = 0;			
				//echo "$msrId_=$mCycleId_<br>";
				// echo "$msrId_ == ".$row['srid']." ~ $mCycleId_ == ".$row['cycleid']."<br>";

				while (($i<$num_results) and ($msrId_ == $row['srid']) and ($mCycleId_ == $row['cycleid'])) {
					$nhari_ = $nhari_ + 1;
					$jabatanid = $row['jabatanid'];
					$nama = $row['nama'];					
					$ketid = $row['ketId'];
					//$ketid2 = $row['ketId2'];
					$icabangid = $row['icabangid'];
					$jabatan = $row['nmjabatan'];
					$jabatanid = $row['jabatanid'];
					$mdokter_ = $row['dokter'];	
					$jvdokter_ = $row['jv_dokter'];		
					$call=0;
					if ($mdokter_) {
						// $call = substr_count($mdokter_,' ') + 1;
						$call = count(explode(' ',$mdokter_));	
					}

					if ($jvdokter_) {
						// $call = substr_count($jvdokter_,' ') + 5;
						$call += count(explode(' ',$jvdokter_));	
					}
					
					// echo count(explode(' ',$jvdokter_)).'~~'.$call.'~~'.$i.'<br>';
					$ok = 0;
					
					$query_call = "SELECT * FROM dbtemp.tmp_call WHERE srid='$msrId_' AND cycleid='$mCycleId_'";
					$result_call = mysqli_query($cnmy, $query_call);
					$num_results_call = mysqli_num_rows($result_call);
					if (!$num_results_call or $ketid) {
						$ok = 1;
					}
					// echo "$num_results_call=$ketid<br>";

					$tgl=$row['tgl'];
					$nmket1 = '';
					$pointDM = $pointSPV = $pointMR =$point_ = 0;
					if ($first_ or $ok) {
						$first_ = 0;
						if ($ketid) {
							$query_ket1 = "SELECT * FROM hrd.ket WHERE ketid='$ketid'";
							$result_ket1 = mysqli_query($cnmy, $query_ket1);
							$num_results_ket1 = mysqli_num_rows($result_ket1);
							if ($num_results_ket1) {
								$row_ket1 = mysqli_fetch_array($result_ket1);
								$nmket1 = $row_ket1['nama'];
								$pointDM = $row_ket1['pointDM'];
								$pointSPV = $row_ket1['pointSpv'];
								$pointMR = $row_ket1['pointMR'];													
							}

							$point_ = 0;
							switch ($jabatanid) {
								case '08';
									$point_ = $pointDM;
									break;
								case '10';
									$point_ = $pointSPV;
									break;
								case '15';
									$point_ = $pointMR;
									break;
								case '18';
									$point_ = $pointSPV;
									break;
							}

			                if ($point_ < 0) {
								$totPoint1_ = $totPoint1_ + $point_;
							}

							if ($point_ > 0) {
								$totPoint2_ = $totPoint2_ + $point_;
							}
						}

					/*	
						if (($ketid2) and ($ketid1 <> $ketid2)) {
							$query_ket2 = "select * from ket where ketid='$ketid2'";
							$result_ket2 = mysqli_query($cnmy, $query_ket2);
							$num_results_ket2 = mysqli_num_rows($result_ket2);
							if ($num_results_ket2) {
								$row_ket2 = mysqli_fetch_array($result_ket2);
								$nmket2 = $row_ket2['nama'];
								$pointDM = $row_ket2['pointDM'];
								$pointSPV = $row_ket2['pointSpv'];
								$pointMR = $row_ket2['pointMR'];																
							}
							$point2_ = 0;
							switch ($jabatanid) {
								case '08';
									$point2_ = $pointDM;
									break;
								case '10';
									$point2_ = $pointSPV;
									break;
								case '15';
									$point2_ = $pointMR;
									break;
								case '18';
									$point2_ = $pointSPV;
									break;
							}
			                if ($point2_ < 0) {
								$totPoint1_ = $totPoint1_ + $point2_;
							}
							if ($point2_ > 0) {
								$totPoint2_ = $totPoint2_ + $point2_;
							}			   
						}
					*/
						
						$query_add = "
							INSERT INTO dbtemp.tmp_call (srid,nama,cycleid,call1,ketid,icabangid,tgl,jabatan,jabatanid,ket,point1) 
							VALUES ('$msrId_','$nama','$mCycleId_','$call','$ketid','$icabangid','$tgl','$jabatan','$jabatanid','$nmket1','$point_')
						";

					   $result_add = mysqli_query($cnmy, $query_add);
						//echo "$query_add<br>";
						if ($result_add) {
							//echo "<br>Save OK !<br><br>";
						}
					}
					$totCall_ = $totCall_ + $call;
					$jpoint_ = $pointjbt_ * $nhari_;
					$persen_ = 0;
					// echo "$persen_ = (($totCall_ + $totPoint2_) / ($jpoint_ - abs($totPoint1_)))*100 ";die();
					
					if (($jpoint_ - abs($totPoint1_)) <> 0) {
						$persen_ = (($totCall_ + $totPoint2_) / ($jpoint_ - abs($totPoint1_)))*100 ;
					}

					$query_update = "
						UPDATE dbtemp.tmp_call 
						SET persen='$persen_',
						totcall='$totCall_',
						totpoint1='$totPoint1_',
						totpoint2='$totPoint2_',
						jpoint='$jpoint_'
						WHERE srid = '$msrId_' AND cycleid='$mCycleId_'
					";						
											
					$result_update = mysqli_query($cnmy, $query_update);					
					$row = mysqli_fetch_array($result);
					$i++;
				}
			}
		}

		echo "disini";
		exit;

		$query_del = "DELETE FROM hrd.persen_call WHERE icabangid='$icabangid' AND left(tgl,7)='$thnbln'";
		//echo"$query_del";

		$result_del = mysqli_query($cnmy, $query_del);
		
		$i = 0; $record=0;
		$query_tmp = "SELECT * FROM dbtemp.tmp_call";
		$result_tmp = mysqli_query($cnmy, $query_tmp);
        $num_results_tmp = mysqli_num_rows($result_tmp);			 		 
		$row_tmp = mysqli_fetch_array($result_tmp);

		while ($i<$num_results_tmp) {
			$srid=$row_tmp['srid'];
			$tgl=$row_tmp['tgl'];
			//$thnbln=$row_tmp['thnbln'];
			$ketid=$row_tmp['ketId'];
		//	$ketid2=$row_tmp['ketId2'];
			$ket=$row_tmp['ket'];
			$ket2=$row_tmp['ket2'];
			$cycleid=$row_tmp['cycleid'];
			$call=$row_tmp['call1'];
			$persen=$row_tmp['persen'];
			$point=$row_tmp['point1'];
			$jpoint=$row_tmp['jpoint'];
			$point2=$row_tmp['point2'];
			$totcall=$row_tmp['totcall'];
			$totpoint1=$row_tmp['totpoint1'];
			$totpoint2=$row_tmp['totpoint2'];
			$icabangid=$row_tmp['icabangid'];
			$jabatanid=$row_tmp['jabatanid'];
			/*
			echo"'$srid','$tgl','$thnbln','$ketid','$ket','$cycleid','$call',
								'$persen','$point','$jpoint','$totcall','$totpoint1','$totpoint2','$icabangid'";
				*/

			//Insert Field Jabatan ID, modified by Subhan			
			$query_add="
				INSERT INTO hrd.persen_call (srid,tgl,thnbln,ketid,ket,cycleid,call1,persen,point1,jpoint,totcall,totpoint1,totpoint2,icabangid,jabatanid) 
				VALUES ('$srid','$tgl','$thnbln','$ketid','$ket','$cycleid','$call','$persen','$point','$jpoint','$totcall','$totpoint1','$totpoint2','$icabangid','$jabatanid')
			";
			//	echo"$query_add<br>";
			
			$result_add = mysqli_query($cnmy, $query_add);
			if ($result_add) {
				$record = $record + 1;
			} else {
				echo "Error : ".mysql_error();
			}			
			$i ++;
			$row_tmp = mysqli_fetch_array($result_tmp);
		}

		$query_add_log ="
			INSERT INTO hrd.persen_call_log (cabangid,periode,sys_now) 
			VALUES ('$icabangid','$thnbln',NOW())
		";
		mysql_query($query_add_log);

		$qry_nm_cabang = "SELECT * FROM MKT.icabang WHERE icabangid = '$icabangid'";
		$result_cbg = mysqli_query($cnmy, $qry_nm_cabang);
        $num_results_cbg = mysqli_num_rows($result_cbg);	
		// echo $qry_nm_cabang;
		$row_cabang = mysqli_fetch_array($result_cbg);
		// while ($i<$num_results_cbg) {
			$nma_cabang = $row_cabang['nama'];
		// }

		echo "Upload Cabang <b>".$row_cabang['nama']."</b> Jumlah $record records OK<br>";
        echo '<br><input type=submit value="OK"><br>';
		 
		echo '<input type="hidden" id="set_focus" name="set_focus" value="'.$set_focus.'" />';
		echo "<SCRIPT LANGUAGE='javascript'>\n";
		echo "   set_focus('$set_focus');\n";
		echo "</SCRIPT>\n";
		 
	}  // if (empty($_SESSION['USERID']))

	if (empty($_SESSION['USERID'])) {
	} else {
		//do_show_menu($_SESSION['jabatanid'],'N');
	}
?>
</form>
</body>
</html>