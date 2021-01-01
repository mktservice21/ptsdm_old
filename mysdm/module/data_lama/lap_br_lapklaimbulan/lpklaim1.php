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
    <title>Laporan Bulanan Klaim Diskon</title>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<script src="klaim.js">
</script>
<body>
<form id="lpklaim1" action="" method=post>
<?php
	//include("config/common.php");
	//include("config/common3.php");
        include "config/koneksimysqli_it.php";
        

	if (empty($_SESSION['IDCARD'])) {
	  echo 'not authorized';
	  exit;
	} else {
	  
		
		$tahun = $_POST['tahun1'];
		$distid = $_POST['distid'];
		
		if ($distid=="" or $distid=="blank") {
			echo "Distributor harus dipilih!<br>";
			echo '<br><input type=button value="Back" onclick="go_back()">';
			exit;
		}
		
		$query = "select min(tgltrans) as awal from hrd.klaim"; //echo"$query";
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
                 * 
                 */
			
		$query_dv = "select nama,distid from MKT.distrib0 where distid='$distid'"; 
		$result_dv = mysqli_query($cnit, $query_dv);
		$num_results_dv = mysqli_num_rows($result_dv);
		if ($num_results_dv) {
			 $row_dv = mysqli_fetch_array($result_dv);
			 $nama_dv = $row_dv['nama'];
		}		
			
		echo "<b>LAPORAN BULANAN BR KLAIM DISKON $nama_dv TAHUN $tahun</b>";
			
		$query = "select klaim.*, karyawan.nama as nm_mr 
				  from hrd.klaim klaim 
				  JOIN hrd.karyawan karyawan on klaim.karyawanid=karyawan.karyawanid 
				  where klaim.distid='$distid' and left(tgltrans,4) = '$tahun' order by tgltrans"; //echo"$query";// and tgl <= '$akhir'";
		$result = mysqli_query($cnit, $query);
		$records = mysqli_num_rows($result);	
		$row = mysqli_fetch_array($result);
		if ($records) {
		$i = 1;
		$gtotal = 0;
		while ($i <= $records) {
			$bln_ = substr($row['tgltrans'],0,7);
			$bulan_ = $row['tgltrans'];
			echo '<table border="1" cellspacing="0" cellpadding="1">';
			echo "<br>";
			echo "<b>Bulan : $bln_</b>";
			echo '<tr>';
			echo '<th align="left"><small>No</small></th>';
			echo '<th align="center"><small>Tanggal Transfer</small></th>';
			echo '<th align="center"><small>Nama</small></th>';
			echo '<th align="center"><small>Keterangan</th>';
			echo '<th align="center"><small>Jumlah</small></th>';
			echo '</tr>';
			$total = 0;
			$no = 0;
		   while ( ($i<=$records) and ($bln_ == substr($row['tgltrans'],0,7)) ) {
			echo "<tr>";
			$brid = $row['klaimId'];	
			$no = $no + 1;	
			$tgltrans = $row['tgltrans'];
			$aktivitas1 = $row['aktivitas1'];
			$aktivitas2 = $row['aktivitas2'];
			$jumlah = $row['jumlah']; 
			//$karyawanid = $row['karyawanId']; 
			$nm_mr = $row['nm_mr']; 
			$total = $total + $jumlah;
			$gtotal = $gtotal + $jumlah;
			
			echo "<td><small>$no</small></td>";
			echo "<td><small>$tgltrans</small></td>";
			echo "<td><small>$nm_mr</small></td>";
			if ($aktivitas1=="" and $aktivitas2==""){
				echo "<td>&nbsp;</td>";
			} else {
				echo "<td><small>$aktivitas1 $aktivitas2</small></td>";
			}
			echo "<td align=right><small>".number_format($jumlah,0)."</small></td>";
			echo "</tr>";

              $row = mysqli_fetch_array($result);
              $i++;
		}// break per bulan
			echo "<tr>";
			echo "<td>&nbsp;</td>";
			echo "<td>&nbsp;</td>";
			echo "<td>&nbsp;</td>";
			echo "<td align=right><b>Total :</td>";
			echo "<td align=right><b>".number_format($total,0)."</b></td>";
			echo "</tr>";
		}// eof  i<= num_results
			echo "<tr>";
			echo "<td>&nbsp;</td>";
			echo "<td>&nbsp;</td>";
			echo "<td>&nbsp;</td>";
			echo "<td align=right><b>Grand Total :</td>";
			echo "<td align=right><b>".number_format($gtotal,0)."</b></td>";
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


