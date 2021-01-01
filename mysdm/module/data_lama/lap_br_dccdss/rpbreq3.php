<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=Rekap Permohonan Dana Budged Request.xls");
    }
?>

<html>
<head>
    <title>Laporan DCC/DSS</title>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>

<script src="breq.js">
</script>
<body>
<form id="rpbreq3" action="rpbreq2.php" method=post>
<?php
	//include("config/common.php");
	//include("config/common3.php");
        include "config/koneksimysqli_it.php";
        

	if (empty($_SESSION['USERID'])) {
	  echo 'not authorized';
	  exit;
	} else {
		//$bankid = $_POST['bankid']; 
		$tahun = $_POST['tahun1'];
		$cabangid = $_POST['cabangid']; 
		$kodeid = $_POST['kodeid'];
		$divprodid = $_POST['divprodid'];
		// echo "$cabangid<br>";
		$nm_cab="";
		if (($divprodid=="" or $divprodid=="blank") or ($cabangid=="" or $cabangid=="blank")) {
			echo "Cabang dan Divisi harus dipilih!<br>";
			echo '<br><input type=button value="Back" onclick="go_back()">';
			exit;
		}
                
                
		//wh20130328, cek kode area

		/*
		$query = "select icabangid,nama,region from br_area where cabangid='$cabangid' limit 1";
		//echo "$query<br>";
		$result = mysqli_query($cnit, $query);
		$records = mysqli_num_rows($result);
		if ($records) {
			$row = mysqli_fetch_array($result);
			$nama_ = $row['nama'];
			$region_ = $row['region'];
			$thnbln_ = date('Y-m-') . '01';
			$icabangid_ = $row['icabangid'];
			$query = "select areaid from MKT.ispv where icabangid='$icabangid_' and aktif='Y' 
					  and areaid not in (select areaid from dbmaster.t_br_area where cabangid='$cabangid' order by areaid)
					  group by areaid;";
			//echo "$query<br>";
			$result = mysqli_query($cnit, $query);
			$records = mysqli_num_rows($result);
			for ($i=0;$i<$records;$i++) {
				$row = mysqli_fetch_array($result);
				$areaid_ = $row['areaid'];
				$qInsert_ = "insert into br_area (cabangid,nama,icabangid,areaid,region,thnbln) values
							('$cabangid','$nama_','$icabangid_','$areaid_','$region_','$thnbln_')";
				//echo "$qInsert_<br>";
				$rInsert_ = mysqli_query($cnit, $qInsert_);
				if (!$rInsert_) {
					echo "Error $qInsert_ : <br>";
					echo mysqli_error()."<br>";
				}	
			}
		}
		*/
		
                $query = "DELETE FROM dbmaster.t_br_area";
                mysqli_query($cnit, $query);
                
                $query = "INSERT INTO dbmaster.t_br_area (cabangid, nama, icabangid, areaid, region, aktif) "
                        . "select a.iCabangId, a.nama, a.iCabangId icabangid, b.areaId areaid, a.region, b.aktif from MKT.iarea b JOIN MKT.icabang a 
                        on b.iCabangId=a.iCabangId
                        WHERE a.iCabangId='$cabangid'";// and b.aktif='Y'
                //echo $query;
                mysqli_query($cnit, $query);
                
                mysqli_query($cnit, $query);
		
		$query_cab = "SELECT nama FROM dbmaster.t_br_area WHERE cabangid='$cabangid'"; 
		//echo"$query_cab <br>";
		$result_cab = mysqli_query($cnit, $query_cab);
		$num_results_cab = mysqli_num_rows($result_cab);	
		if ($num_results_cab) {
			 $row_cab = mysqli_fetch_array($result_cab);
			 $nm_cab = $row_cab['nama'];
		}
		
		$query_dv = "SELECT nama FROM MKT.divprod WHERE divprodid='$divprodid'"; //echo"$query_dv";
		$result_dv = mysqli_query($cnit, $query_dv);
		$num_results_dv = mysqli_num_rows($result_dv);	
		if ($num_results_dv) {
			 $row_dv = mysqli_fetch_array($result_dv);
			 $nama = $row_dv['nama'];
		}
		
		$query = "SELECT min(tgltrans) as awal FROM hrd.br0 WHERE divprodid='$divprodid' and kode='$kodeid'"; //echo"$query";
		$result = mysqli_query($cnit, $query);
		$num_results = mysqli_num_rows($result);
		$row = mysqli_fetch_array($result);
		$tglawal_ = substr($row['awal'],0,7); //echo"tgl=$tglawal_";
		if ($tglawal_=="0000-00") {
			$tglawal_ = date('Y-').'01';
		} 
                /*
		$akhir = $_POST['bulan'];
		if ($tglawal_=="") {
			$tglawal_ = $akhir;
		} // echo"akir=$akhir";
		*/
                
		$query_kd = "SELECT nama,br FROM hrd.br_kode WHERE divprodid='$divprodid' and kodeid='$kodeid'"; 
		//echo"$query_kd";
		$result_kd = mysqli_query($cnit, $query_kd);
		$num_results_kd = mysqli_num_rows($result_kd);
		if ($num_results_kd) {
			 $row_kd = mysqli_fetch_array($result_kd);
			 $nama_kd = $row_kd['nama'];
			 $br = $row_kd['br']; //echo"br=$br";
		}	
			
		echo "<b>LAPORAN BULANAN BR $nama_kd - $nama TAHUN $tahun</b>";
		echo"<br><br>";
		
		$query11 = "drop temporary table if exists dbtemp.tmp_rpt1";
		$result11 = mysqli_query($cnit, $query11);
		$query_rpt1 = "
			CREATE TEMPORARY table dbtemp.tmp_rpt1(
				brid char (10), tglunrtr date, tgltrans date, icabangid char (10),areaid char (10), dokterId char (10), dokter char (30), mrid char (10), noslip char (10),
				divprodid char (10), aktivitas1 char (50), aktivitas2 char (50), jumlah decimal (10,2) default 0,jumlah1 decimal (10,2) default 0, realisasi1 char (30), karyawanId char (10),
				ccyid char (3), batal char (1)
			)
		";
		//echo"$query_rpt1<br>";
		$result_rpt1 = mysqli_query($cnit, $query_rpt1);
		
	
		$query1 = "SELECT DISTINCT icabangid FROM dbmaster.t_br_area WHERE cabangid='$cabangid'";
		//echo"$query1<br>";
		
		$result1 = mysqli_query($cnit, $query1);
		$num_results1 = mysqli_num_rows($result1);
		$row1 = mysqli_fetch_array($result1);
                
                $ccyId_="";
                
                
		$j = 0;
		while ($j < $num_results1) {
			$icabangid = $row1['icabangid']; 
			// ECHO "$icabangid<br>";
			
			$query_str = "SELECT * FROM dbmaster.t_br_area WHERE cabangid='$cabangid' AND icabangid='$icabangid' ORDER BY icabangid"; 
			//echo"$query_str <Br><br>";

			$result_str = mysqli_query($cnit, $query_str); 
			$num_results_str = mysqli_num_rows($result_str); 
			$str_ = '(';
			for ($i=0;$i < $num_results_str;$i++) {
				$row_str = mysqli_fetch_array($result_str);
				$areaid = $row_str['areaid'];
				$str_ = $str_ . "'$areaid',";
			} 
			if (substr($str_,-1)==",") {
				$str_ = substr($str_,0,-1);
			}
			$str_ = $str_ . ")"; 
			
		//	echo"str====$str_<br>";
			//AND areaid IN $str_ // dihilangkan dulu
			$query2 = "
				SELECT * FROM hrd.br0 br0 
				WHERE br0.icabangid='$icabangid'  AND divprodid='$divprodid' and br0.kode='$kodeid'
				AND ((left(tgltrans,4)='$tahun' AND tglunrtr='0000-00-00') OR LEFT(tglunrtr,4)='$tahun')
				AND retur <> 'Y' 
				ORDER BY tglunrtr,tgltrans,noslip
			";
			//echo"$query2<BR>";exit;

			$result2 = mysqli_query($cnit, $query2);
			$records2 = mysqli_num_rows($result2);	
			$row2 = mysqli_fetch_array($result2);
			$a = 1;
			while ($a <= $records2) {
				$brid = $row2['brId'];// echo"$brid";
				$tgltrans = $row2['tgltrans'];
				$tglunrtr = $row2['tglunrtr'];
				$icabangid = $row2['icabangid'];	
				$areaid = $row2['areaid'];
				$dokterId = $row2['dokterId']; 
				$dokter = $row2['dokter'];
				$mrid = $row2['mrid'];
				$noslip = $row2['noslip'];
				$divprodid = $row2['divprodid'];
				$aktivitas1 = $row2['aktivitas1'];
				$aktivitas2 = $row2['aktivitas2'];
				$jumlah = $row2['jumlah'];
				$jumlah1 = $row2['jumlah1'];
				//echo "jumlah1=$jumlah1<br>";
				$realisasi1 = $row2['realisasi1']; 			
				$karyawanId = $row2['karyawanId']; 
				$ccyId = $row2['ccyId'];
				$user1  = $row2['user1'];
				$batal  = $row2['batal'];
				
				//ECHO"$brid/$tgltrans/$tglunrtr/$icabangid/$areaid/$dokterId/$dokter/$mrid/$noslip/$divprodid/$aktivitas1/$aktivitas2/
				//$jumlah/$jumlah1/$realisasi1/$ccyId/$batal<br>";
				$query_in = "
					INSERT INTO dbtemp.tmp_rpt1 (brid,tglunrtr,tgltrans,icabangid,areaid,dokterId,dokter,mrid,noslip,divprodid,aktivitas1,aktivitas2,jumlah,jumlah1,realisasi1,karyawanId,ccyid,batal) 
					VALUES('$brid','$tglunrtr','$tgltrans','$icabangid','$areaid','$dokterId','$dokter','$mrid','$noslip','$divprodid','$aktivitas1','$aktivitas2','$jumlah','$jumlah1','$realisasi1','$karyawanId','$ccyId','$batal')
				";
				//echo"$query_in<br>;";

				$result_in = mysqli_query($cnit, $query_in);
				if ($result_in) {	      
				} else {
					echo "Error : ".mysqli_error();
					exit;
				} 
				$row2 = mysqli_fetch_array($result2);
				$a ++;
			}		
			
			$j++;
			$row1 = mysqli_fetch_array($result1);
			
		}

		echo "<b>Cabang : $nm_cab";	
		
		$query = "SELECT * FROM dbtemp.tmp_rpt1 ORDER BY tglunrtr,tgltrans,noslip";
		$result = mysqli_query($cnit, $query);
		$records = mysqli_num_rows($result);	
		$row = mysqli_fetch_array($result);
		if ($records) {
		$i = 1;
		$gtotal = $gtotal_ = 0;
		while ($i <= $records) {		
			$tglunrtr = $row['tglunrtr']; 
			if ($tglunrtr <>'0000-00-00') {
				$row['tgltrans'] = $row['tglunrtr']; 
			}
			$bln_ = substr($row['tgltrans'],0,7);
			$bulan_ = $row['tgltrans']; 
			echo '<table border="1" cellspacing="0" cellpadding="1">';
			echo "<br>";
			echo "<b>Bulan : $bln_</b>";
			echo '<tr>';
			echo '<th align="left"><small>No</small></th>';
			echo '<th align="center"><small>Tanggal Transfer</small></th>';
			echo '<th align="center"><small>No Slip</small></th>';
			echo '<th align="center"><small>Nama</small></th>';
			echo '<th align="center"><small>Daerah</small></th>';
			if ($kodeid=="700-02-03") {
			} else {
				echo '<th align="center"><small>Nama Dokter</small></th>';
			}
			echo '<th align="center">Keterangan</th>';
			echo '<th align="center">Realisasi</th>';
			echo '<th align="center"><small>Jumlah IDR</small></th>';
			echo '<th align="center"><small>Jumlah USD</small></th>';
			echo '</tr>';
			$total = $total_ = 0;
			$no = 0;
		   while ( ($i<=$records) and ($bln_ == substr($row['tgltrans'],0,7)) ) {
			echo "<tr>";
			$brid = $row['brid'];	
			$no = $no + 1;			
			$tgltrans = $row['tgltrans'];
			$tglunrtr = $row['tglunrtr'];
			$icabangid = $row['icabangid'];	
			$areaid = $row['areaid'];
			$dokterId = $row['dokterId']; 
			$dokter = $row['dokter'];
			$mrid = $row['mrid'];
			$noslip = $row['noslip'];
			$divprodid = $row['divprodid'];
			$aktivitas1 = $row['aktivitas1'];
			$aktivitas2 = $row['aktivitas2'];
			$jumlah = $row['jumlah'];
			$jumlah1 = $row['jumlah1']; //echo "jumlah1=$jumlah1<br>";
			$realisasi1 = $row['realisasi1']; 			
			$karyawanId = $row['karyawanId']; 
			$ccyId = $row['ccyid'];
			//$user1  = $row['user1'];
			$batal  = $row['batal'];
			
			$nama_mr = '';
			$query_mr = "select nama from hrd.karyawan where karyawanId='$karyawanId'";
			$result_mr = mysqli_query($cnit, $query_mr);
			$num_results_mr = mysqli_num_rows($result_mr);
			if ($num_results_mr) {
				 $row_mr = mysqli_fetch_array($result_mr);
				 $nama_mr = $row_mr['nama'];
			}
			
			$nama_mr1 = '';
			$query_mr1 = "select nama from hrd.karyawan where karyawanId='$mrid'";
			$result_mr1 = mysqli_query($cnit, $query_mr1);
			$num_results_mr1 = mysqli_num_rows($result_mr1);
			if ($num_results_mr1) {
				 $row_mr1 = mysqli_fetch_array($result_mr1);
				 $nama_mr1 = $row_mr1['nama'];
			}
			
			$nama_dkt = '';
			$query_dkt = "select nama from hrd.dokter where dokterId='$dokterId'"; //echo"$query_dkt";
			$result_dkt = mysqli_query($cnit, $query_dkt);
			$num_results_dkt = mysqli_num_rows($result_dkt);
			if ($num_results_dkt) {
				 $row_dkt = mysqli_fetch_array($result_dkt);
				 $nama_dkt = $row_dkt['nama'];
			}
			
			$nama_ar = '';
			$query_ar = "select br_area.nama
						from hrd.br0 br0  
						join dbmaster.t_br_area br_area on br0.icabangid=br_area.icabangid and br0.areaid=br_area.areaid 
						where br0.icabangid='$icabangid' and br0.areaid='$areaid'"; //echo"$query_ar";
			$result_ar = mysqli_query($cnit, $query_ar);
			$num_results_ar = mysqli_num_rows($result_ar);
			if ($num_results_ar) {
				$row_ar = mysqli_fetch_array($result_ar);
				$nama_ar = $row_ar['nama'];
			}
			
			echo "<td><small>$no</small></td>";
			if ($tglunrtr == '0000-00-00') {
				echo "<td><small>$tgltrans</small></td>";
			} else {
				echo "<td><small>$tglunrtr</small></td>";
			}
			echo "<td><small>$noslip</small></td>";
			echo "<td><small>$nama_mr</small></td>";
			if ($nama_ar==""){
					echo "<td>&nbsp;</td>";
				} else {
					echo "<td><small>$nama_ar</small></td>";
				}
			if ($kodeid=="700-02-03") {
			} else {
				if ($nama_dkt==""){
					if ($dokter==""){
						echo "<td><small>&nbsp;</small></td>";
					} else {
						echo "<td><small>$dokter</small></td>";
					}
				} else {
					echo "<td><small>$nama_dkt</small></td>";
				}
			}
			if ($aktivitas1==""){
				echo "<td><small>&nbsp;</small></td>";
			} else {
				echo "<td><small>$aktivitas1 $aktivitas2</small></td>";
			}

			if ($realisasi1==""){
				echo "<td><small>&nbsp;</small></td>";
			} else {
				echo "<td><small>$realisasi1</small></td>";
			}
			
			if ($jumlah1==0) {
				if ($ccyId<>'IDR') {
					if ($batal == 'Y') {
						$jumlah = 0;
						echo "<td><small>&nbsp;</small></td>";
						echo "<td align=right><small>$ccyId ".number_format($jumlah,0)."</small></td>";
					} else {
						echo "<td><small>&nbsp;</small></td>";
						echo "<td align=right><small>$ccyId ".number_format($jumlah,0)."</small></td>";
					}
					$total_ = $total_ + $jumlah;
					$gtotal_ = $gtotal_ + $jumlah;
					$ccyId_ = $ccyId;
					
				} else {
					if ($batal == 'Y' ) {
						$jumlah = 0;
						echo "<td align=right><small>".number_format($jumlah,0)."</small></td>";
						echo "<td><small>&nbsp;</small></td>";
						
					} else {
						echo "<td align=right><small>".number_format($jumlah,0)."</small></td>";
						echo "<td><small>&nbsp;</small></td>";
					}
					$total = $total + $jumlah;
					$gtotal = $gtotal + $jumlah;
				}
			} else {
				if ($ccyId<>'IDR') {
					if ($batal=='Y') {
						$jumlah1 = 0;
						echo "<td><small>&nbsp;</small></td>";
						echo "<td align=right><small>$ccyId ".number_format($jumlah1,0)."</small></td>";
					} else {
						echo "<td><small>&nbsp;</small></td>";
						echo "<td align=right><small>$ccyId ".number_format($jumlah1,0)."</small></td>";
						
					}
					$total_ = $total_ + $jumlah1;
					$gtotal_ = $gtotal_ + $jumlah1;
					$ccyId_ = $ccyId;
				} else {
					if ($batal=='Y') {
						$jumlah1 = 0;
						echo "<td align=right><small>".number_format($jumlah1,0)."</small></td>";
						echo "<td><small>&nbsp;</small></td>";
					} else {
						echo "<td align=right><small>".number_format($jumlah1,0)."</small></td>";
						echo "<td><small>&nbsp;</small></td>";
					}
					$total = $total + $jumlah1;
					$gtotal = $gtotal + $jumlah1;
				}
			}
			
			echo "</tr>";
			
			  $row = mysqli_fetch_array($result);
			  $i++;
		}// break per bulan
			//echo"$ccyId";
			echo "<tr>";
			if ($kodeid=="700-02-03") {
			} else {
				echo "<td>&nbsp;</td>";
			}
			echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
			echo "<td align=right><b>Total :</td>";
			echo "<td align=right><b>".number_format($total,0)."</b></td>";
			echo "<td align=right><b>$ccyId_ ".number_format($total_,0)."</b></td>";
			echo "</tr>";
		}// eof  i<= num_results
			echo "<tr>";
			if ($kodeid=="700-02-03") {
			} else {
				echo "<td>&nbsp;</td>";
			}
			echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
			echo "<td align=right><b>Grand Total :</td>";
			echo "<td align=right><b>".number_format($gtotal,0)."</b></td>";
			echo "<td align=right><b>$ccyId_ ".number_format($gtotal_,0)."</b></td>";
			echo "</tr>";
		echo "</table>";
		} else {
			echo "<br /><b>Data tidak ditemukan!!!</b><br/><br/>";		
	    }	
    }  // if (empty($_SESSION['srid'])) 

?>
</form>
</body>
</html>


