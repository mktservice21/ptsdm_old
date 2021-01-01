<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REKAP ACC BUDGET REQUEST.xls");
    }
?>

<html>
<head>
  <title>Realisasi BR OTC</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<body>
<form id="rlbr01" action="rlbr00.php" method=post>
<?php
	include("config/koneksimysqli_it.php");
	include("config/common.php");
	

	if (empty($_SESSION['USERID'])) {
	  echo 'not authorized';
	  exit;
	} else {
                $tahun=$_POST['tahun'];
		$srid = $_SESSION['USERID'];
        $srnama = $_SESSION['NAMALENGKAP'];
		$sr_id = substr('0000000000'.$srid,-10);
		$userid = $_SESSION['IDCARD'];
		
                $chklamp="";
                $chklamp1="";
                $chklamp2="";
		if (isset($_POST['chklamp'])) $chklamp = $_POST['chklamp'];
		if (isset($_POST['chklamp1'])) $chklamp1 = $_POST['chklamp1'];
		if (isset($_POST['chklamp2'])) $chklamp2 = $_POST['chklamp2'];
                
                
		$lamp = 'N';
		if ($chklamp=="L") {
		   $lamp = 'Y';
		}		
		
		$ca = 'N';
		if ($chklamp1=="C") {
		   $ca = 'Y';
		}	

		$via = 'N';
		if ($chklamp2=="S") {
		   $via = 'Y';
		}
		echo "<B>REALISASI BR OTC $tahun<BR>";
		
                $where_="";
		
		if ($lamp=='N') {
		} else {
			$where_ = "and lampiran='Y'";
		}
		
		if ($via=='N') {
		} else {
			$where_ = $where_." and via='Y'";
		}
		
		if ($ca=='N') {
		} else {
			$where_ = $where_." and ca='Y'";
		}

		$periode = $_POST['tahun']; 
		if ($periode =="") {
			$periode = $_POST['periode'];
			$periode = substr($periode,0,4);
		}
	
		echo "<br>";
		$query = "select * from hrd.br_otc where left(tgltrans,4)='$periode'  ".$where_." order by tgltrans,noslip"; //echo"$query";
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
			echo "<b>Bulan : $bln_</b>";
			echo '<tr>';
			echo '<th><small>No. </small></th>';
			echo '<th><small>Tgl. Transfer</small></th>';
			echo '<th><small>Keterangan</small></th>';
			echo '<th><small>Jumlah IDR</small></th>';
			echo '<th><small>Nama Realisasi</small></th>';
			echo '<th><small>No Slip</small></th>';
			echo '<th><small>Tgl. Terima Kwitansi</small></th>';
			echo '<th><small>Jumlah Realisasi (IDR)</small></th>';
			echo '<th>&nbsp;</th>';
			 echo '<th>&nbsp;</th>';
			echo '</tr>';
			$total = $total_ = $totalru1_ = $totalru2_ = $totalri1_ = $totalri2_ = 0;
			$no = 0;
		   while ( ($i<=$records) and ($bln_ == substr($row['tgltrans'],0,7)) ) {
			$no = $no + 1;	
			$karyawanid = "";//$row['karyawanid'];
			$jumlah = $row['jumlah'];
			$real1 = $row['real1'];
			$brotcid = $row['brOtcId'];
			$tglreal = $row['tglreal'];
			$tgltrans = $row['tgltrans'];
			$divprodid = "OTC";//$row['divprodid'];
			$aktivitas1 = $row['keterangan1'];
			$aktivitas2 = $row['keterangan2'];
			$realisasi = $row['realisasi'];
			$noslip = $row['noslip'];
			$ccyid = $row['ccyId'];
			$lain2 = "";//$row['lain2'];
		
			echo '<tr>';
			echo "<td><small>$no</small></td>";
			echo "<td><small>$tgltrans</small></td>";
			echo "<td><small>$aktivitas1 $aktivitas2</small></td>";
			echo "<td align=right><small>".number_format($jumlah,0)."</small></td>";
			
			$total = $total + $jumlah;
			$gtotal = $gtotal + $jumlah;
			
			echo "<td><small>$real1</small></td>";
			echo "<td><small>$noslip</small></td>";			
			if ($tglreal <>'0000-00-00') {
				echo "<td><small>$tglreal</small></td>";
				echo "<td align=right><small>".number_format($realisasi,0)."</small></td>";
				$totalri2_ = $totalri2_ + $realisasi;
				$gtotalri2_ = $gtotalri2_ + $realisasi;

			} else {
				echo '<td>&nbsp;</td><td>&nbsp;</td>';
			}
	
			echo "<td><a href='module/data_lama/lap_eth_realisasibrotc/brotc00.php?brotcid=$brotcid&entrymode=E'><small>Edit</small></a></td>";
			echo "<td><a href='module/data_lama/lap_eth_realisasibrotc/brotc00.php?brotcid=$brotcid&periode=$tgltrans&entrymode=D'>Delete</a></td>";	
			echo '</tr>';

		    $row = mysqli_fetch_array($result);
		    $i++;
		}// break per bulan
			echo "<tr>";
			echo "<td>&nbsp;</td><td>&nbsp;</td>";
			echo "<td align=right><b>Total :</td>";
			echo "<td align=right><b>".number_format($total,0)."</b></td>";
			echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
			if ($totalri1_==0) {
				echo "<td align=right><b>".number_format($totalri2_,0)."</b></td>";
			} else {
				echo "<td align=right><b>".number_format($totalri1_,0)."</b></td>";
			}
		
			echo "<td>&nbsp;</td><td>&nbsp;</td>";
			echo "</tr>";
		}// eof  i<= num_results
			echo "<tr>";
			echo "<td>&nbsp;</td><td>&nbsp;</td>";
			echo "<td align=right><b>Total :</td>";
			echo "<td align=right><b>".number_format($gtotal,0)."</b></td>";
			echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
			echo "<td align=right><b>".number_format($gtotalri2_,0)."</b></td>";
			echo "<td>&nbsp;</td><td>&nbsp;</td>";
			echo "</tr>";
		echo "</table>";
		} else {
			echo "<br /><b>Data tidak ditemukan!!!</b><br/><br/>";		
	    }	
    }  // if (empty($_SESSION['USERID'])) 
	echo "<BR><input type=submit id=cmdBack name=cmdBack value='Back'>";
?>
</form>
</body>
</html>


