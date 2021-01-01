<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=Rekap Budget Request.xls");
    }
?>

<html>
<head>
  <title>Realisasi BR SBY</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<body>
<form id="rptrnsf1" action="rptrnsf0.php" method=post>
<?php
	include("config/common.php");
	//include("common3.php");
	include("config/koneksimysqli_it.php");
	

	if (empty($_SESSION['IDCARD'])) {
	  echo 'not authorized';
	  exit;
	} else {
	  
		$srid = $_SESSION['USERID'];
            $srnama = $_SESSION['NAMALENGKAP'];
		$sr_id = substr('0000000000'.$srid,-10);
		$userid = $_SESSION['IDCARD'];
		//$divprodid = $_POST['divprodid']; 
		//$lampiran1 = $_POST['lampiran'];
                
		$tgl01 = $_POST['bulan'];
                $periode1= date("Y-m", strtotime($tgl01));
                $tanggal1= date("d", strtotime($tgl01));
                $bulan1= date("m", strtotime($tgl01));
                $nm_bln1 = nama_bulan($bulan1); //echo "$nm_bln1";
                $tahun1= date("Y", strtotime($tgl01));
                
		$lamp = $_POST['lamp'];

		
		echo "<B>REKAP TRANSFER $tahun1<BR>";

		echo "<br>"; 
		$where_="";
		$brid="";
		$dokter ="";
		$nama_dkt="";
		$jns="";
		if ($lamp=="*"){
		//echo "<td><small>&nbsp;<small></td>";
		} else {
			if ($lamp=='A') {
				$where_ = " and lampiran='Y'";
				$jns = '** Ada Kuitansi';
			} else {
				$where_ = " and lampiran='N'";
				$jns = '** Belum Ada Kuitansi';
			}
		}
		
		echo "<b> $jns</b><br><br>";


		$query = "select * from hrd.br_otc where left(tgltrans,7) = '$periode1'  ".$where_." order by noslip";
		//echo"$query";

		$result = mysqli_query($cnit, $query);
		$records = mysqli_num_rows($result);	
		$row = mysqli_fetch_array($result);
		if ($records) {
		$i = 1;
		$gtotal = $gtotal_ = $gtotalru1_ =$gtotalru2_ = $gtotalri1_ =$gtotalri2_ = 0;
		while ($i <= $records) {
			$bln_ = substr($row['tgltrans'],0,7);
			$bulan_ = $row['tgltrans'];
			echo '<table border="1" cellspacing="0" cellpadding="1">';
			echo "<br>";
			echo "<b>Periode Transfer : $bulan_</b>";
			echo '<tr>';
			echo '<th><small>No. </small></th>';
			echo '<th><small>Cabang</small></th>';
			echo '<th><small>Alokasi Budget</small></th>';
			echo '<th><small>Tgl. Transfer</small></th>';
			echo '<th><small>No Slip</small></th>';
			echo '<th><small>Keterangan Tempat</small></th>';
			echo '<th><small>Keterangan</small></th>';
			echo '<th><small>Jumlah IDR</small></th>';
			echo '<th><small>Nama Realisasi</small></th>';
			echo '<th><small>Tgl. Terima</small></th>';
			echo '<th><small>Jumlah Realisasi (IDR)</small></th>';
			echo '</tr>';
			$total = $total_ = $totalru1_ = $totalru2_ = $totalri1_ = $totalri2_ = 0;
			$no = 0;
		   while ( ($i<=$records) and ($bulan_ == $row['tgltrans']) ) {
			$no = $no + 1;	
			$icabangid_o = $row['icabangid_o'];// echo"$icabangid_o";
			$jumlah = $row['jumlah'];
			$realisasi = $row['realisasi'];
			$brid = $row['brOtcId'];
			$tgltrm = $row['tglreal'];
			$tgltrans = $row['tgltrans'];
			//$divprodid = $row['divprodid'];
			$kodeid = $row['kodeid'];
			//$dokter = $row['dokter']; //echo"$dokter";
			$aktivitas1 = $row['keterangan1'];
			$aktivitas2 = $row['keterangan2'];
			$real1 = $row['real1'];
			$noslip = $row['noslip'];
			$ccyid = $row['ccyId'];
			
			
			if ($icabangid_o=='HO' or $icabangid_o=='MD' or $icabangid_o=='PM_LANORE' or $icabangid_o=='PM_PARASOL' or $icabangid_o=='PM_MELANOX' or $icabangid_o=='PM_ACNEMED' or $icabangid_o=='JKT_MT' or $icabangid_o=='JKT_RETAIL' or $icabangid_o=='PM_CARMED') {
				$nama_cab = $icabangid_o;
			} else {
				$query_cab = "select nama from MKT.icabang_o where icabangid_o='$icabangid_o'"; 
				$result_cab = mysqli_query($cnit, $query_cab);
				$num_results_cab = mysqli_num_rows($result_cab);
				if ($num_results_cab) {
					$row_cab = mysqli_fetch_array($result_cab);
					$nama_cab = $row_cab['nama'];
				}
			}
			
			$nama_kd = '';
			$query_kd = "select nama from hrd.brkd_otc where kodeid='$kodeid'"; 
			$result_kd = mysqli_query($cnit, $query_kd);
			$num_results_kd = mysqli_num_rows($result_kd);
			if ($num_results_kd) {
				 $row_kd = mysqli_fetch_array($result_kd);
				 $nama_kd = $row_kd['nama'];
			}
	
			if ($nama_dkt=='') {
				$nama_dkt = $dokter;
			}
			
			echo '<tr>';
			echo "<td><small>$no</small></td>";
			echo "<td><small>$nama_cab</small></td>";
			echo "<td><small>$nama_kd</small></td>";
			echo "<td><small>$tgltrans</small></td>";	
			echo "<td><small>$noslip</small></td>";					
			if ($aktivitas1=="") {
				echo "<td>&nbsp;</td>";
			} else {
				echo "<td><small>$aktivitas1</small></td>";	
			}		
			if ($aktivitas2=="") {
				echo "<td>&nbsp;</td>";
			} else {
				echo "<td><small>$aktivitas2</small></td>";	
			}	
			
			echo "<td align=right><small>".number_format($jumlah,0)."</small></td>";

				$total = $total + $jumlah;
				$gtotal = $gtotal + $jumlah;
			
			echo "<td><small>$real1</small></td>";
			
			if ($tgltrm <>'0000-00-00') {
				echo "<td><small>$tgltrm</small></td>";
				echo "<td align=right><small>".number_format($realisasi,0)."</small></td>";
				$totalri2_ = $totalri2_ + $realisasi;
				$gtotalri2_ = $gtotalri2_ + $realisasi;
			} else {
				echo '<td>&nbsp;</td><td>&nbsp;</td>';
			}

			echo '</tr>';

		    $row = mysqli_fetch_array($result);
		    $i++;
		}// break per bulan
			echo "<tr>";
			echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
			echo "<td align=right><b>Total :</td>";
			echo "<td align=right><b>".number_format($total,0)."</b></td>";
			echo "<td>&nbsp;</td><td>&nbsp;</td>";
			echo "<td align=right><b>".number_format($totalri2_,0)."</b></td>";
			echo "</tr>";
		}// eof  i<= num_results
			echo "<tr>";
			echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
			echo "<td align=right><b>Grand Total :</td>";
			echo "<td align=right><b>".number_format($gtotal,0)."</b></td>";
			echo "<td>&nbsp;</td><td>&nbsp;</td>";
			echo "<td align=right><b>".number_format($gtotalri2_,0)."</b></td>";
			echo "</tr>";
		echo "</table>";
		} else {
			echo "<br /><b>Data tidak ditemukan!!!</b><br/><br/>";		
	    }	
    }  // if (empty($_SESSION['srid'])) 
	echo "<BR><input type=hidden id=cmdBack name=cmdBack value='Back'>";
      
?>
</form>
</body>
</html>


