<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=LAPORAN AKHIR SURABAYA.xls");
    }
?>
<html>
<head>
  <title>LAPORAN BR OTC SBY</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<form action="rpbrasb0.php" method=post>
<body>
<?php
	
	include("config/common.php");
	//include("common3.php");
	include("config/koneksimysqli_it.php");
	$srid = $_SESSION['USERID'];
	$srnama = $_SESSION['NAMALENGKAP'];
	$sr_id = substr('0000000000'.$srid,-10);
	$userid = $_SESSION['IDCARD'];
	$jenis = $_POST['jenis'];
  
	
		$tgl03 = $_POST['bulan'];
                $periode3= date("Y-m-d", strtotime($tgl03));
                $tanggal3= date("d", strtotime($tgl03));
                $bulan3= date("m", strtotime($tgl03));
                $nm_bln3 = nama_bulan($bulan3); //echo "$nm_bln1";
                $tahun3= date("Y", strtotime($tgl03));

	echo "<big>To : Sdri. Vanda/Lina (Accounting)<br><br>";
	echo "PT. SDM - Surabaya</big><br><br>";
	echo "Laporan Budget Request Team OTC</big><br><br>";
	echo "<b>$tanggal3 $nm_bln3 $tahun3</b><br><br>";
        $jns="";
	if ($jenis==""){
		echo "<td><small>&nbsp;<small></td>";
	} else {
		if ($jenis=='A') {
			$jns = 'Advance';
		} else {
			if ($jenis=='K') {
				$jns = 'Klaim';
			} else {
				$jns = 'Sudah minta uang muka';
			}
		}
	}
	
	echo "<b> ** $jns</b><br><br>";


	$query = "select * from hrd.br_otc where tglrpsby='$periode3' and jenis='$jenis' order by tglrpsby,noslip";//echo"$query";
        //echo $query;
	$header_ = add_space('No Slip',10);
	$header1_ = add_space('Tgl.Transfer',10);
	$header4_ = add_space('Posting',30);
	$header2_ = add_space('Keterangan',150);
	$header3_ = add_space('Jumlah',10);
	echo '<table border="1" cellspacing="0" cellpadding="1">';
	echo "<tr>\n";
		
	echo "<th>No</th>";
	echo '<th align="left">'.$header_."</th>";
	echo '<th align="left">'.$header1_."</th>";
	//echo '<th align="left">'.$header4_."</th>";
	echo '<th align="left">'.$header2_."</th>";
	echo '<th align="left">'.$header3_."</th>";
	echo "</tr>";

	$result = mysqli_query($cnit, $query);
	$records = mysqli_num_rows($result);	
	$row = mysqli_fetch_array($result);	
	if ($records) {
	$i = 1;
	$gtotal = 0;
	$no = 0;
	while ($i <= $records) {
		$noslip_ = $row['noslip'];
		$total = 0;
		$no = $no + 1;	
		$first_ = 1;
	   while ( ($i<=$records) and ($noslip_ == $row['noslip']) ) {
		echo "<tr>";
		$real1 = $row['real1'];
		$icabangid_o = $row['icabangid_o'];
		$keterangan1 = $row['keterangan1'];
		$keterangan2 = $row['keterangan2'];
		$jumlah = $row['realisasi'];
		$tgltrans = $row['tgltrans'];
		$kodeid = $row['kodeid'];
		
		if ($tgltrans<>'') {
			$tanggal2 = substr($tgltrans,-2,2);
			$bulan2 = substr($tgltrans,5,2); 
			$tahun2 = substr($tgltrans,0,4);
			$nm_bln2 = nama_bulan($bulan2);
			$nama_bln = substr($nm_bln2,0,3);
			$periode2 = $tanggal2.'-'.$nama_bln.'-'.$tahun2;
		}
		
		$query_al = "select nama from hrd.brkd_otc where kodeid='$kodeid' ";// echo"$query_mr";
		$result_al = mysqli_query($cnit, $query_al);
		$num_results_al = mysqli_num_rows($result_al);
		if ($num_results_al) {
			 $row_al = mysqli_fetch_array($result_al);
			 $nama_al = $row_al['nama'];
		}
		
		$total = $total + $jumlah;
		$gtotal = $gtotal + $jumlah;
		
		if ($first_) {
			echo "<td><small>$no</small></td>";
			$first_ = 0;
		} else {
			echo "<td><small>&nbsp;</small></td>";
		}
		
		if ($noslip_=='') {
			echo "<td align=center><small>&nbsp;</small></td>";
		} else {
			echo "<td align=center><small>$noslip_</small></td>";
		}
		echo "<td align=center><small>$periode2</small></td>";
	//	echo "<td align=center><small>$nama_al</small></td>";
		echo "<td align=left><small><b>$real1</b> - $keterangan1 $keterangan2</small></td>";
		echo '<td align="right"><small>'.number_format($jumlah,0)."</small></td>";
		echo "</tr>";

		  $row = mysqli_fetch_array($result);
		  $i++;
	}// break per bulan
		
		echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td align='right'><small><b>Sub Total </td>";
		echo "<td align=right><b><small>".number_format($total,0)."</b></td></tr>";
		echo "<tr><td colspan=6>&nbsp;</td></tr>";
	}// eof  i<= num_results
		echo "<tr>";
		echo "<td>&nbsp;</td><td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "<td align=right><b>Grand Total :</td>";
		echo "<td align=right><b>".number_format($gtotal,0)."</b></td>";
		echo "</tr>";
	echo "</table>";
	} else {
		echo "<br /><b>Data tidak ditemukan!!!</b><br/><br/>";		
	}	
	
	echo "<table>";

	echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	echo "<tr><td>Dibuat oleh,</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td align=center>Mengetahui,</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td align=center>Disetujui,</td>";
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	echo "<tr><td>Saiful Rahmat</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td align=center>Marianne</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td align=center>Ira Budisusetyo</td>";


	echo "</table>\n";
	
	  echo "<br><input type=hidden name=cmdBack id=cmdBack value=Back>";
	 
if (empty($_SESSION['srid'])) {
  } else {
    do_show_menu($_SESSION['jabatanid'],'N');
  }

?>

</body>
</html>
