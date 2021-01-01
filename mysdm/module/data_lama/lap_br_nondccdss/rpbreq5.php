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
    <title>Laporan Bulanan Non DCC/DSS</title>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<script src="breq.js">
</script>
<body>
<form id="rpbreq5" action="rpbreq4.php" method=post>
<?php
	//include("common.php");
	//include("common3.php");
        include "config/koneksimysqli_it.php";
	

	if (empty($_SESSION['USERID'])) {
	  echo 'not authorized';
	  exit;
	} else {
	  
		$tahun = $_POST['tahun1'];
		$kodeid = $_POST['kodeid'];
		$divprodid = $_POST['divprodid'];
		
		if ($divprodid=="" or $divprodid=="blank") {
			echo "Divisi harus dipilih!<br>";
			echo '<br><input type=button value="Back" onclick="go_back()">';
			exit;
		}
	
		$query = "select min(tgltrans) as awal from hrd.br0 where divprodid='$divprodid' and kode='$kodeid'"; //echo"$query";
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
                
                $ccyId_="";
                
                
		$query = "select nama,kodeid from hrd.br_kode where kodeid='$kodeid'"; 
		$result = mysqli_query($cnit, $query);
		$row = mysqli_fetch_array($result);
		$num_results = mysqli_num_rows($result);
		$nm_kd = $row['nama'];
		
		$query = "select nama,divprodid from MKT.divprod where divprodid='$divprodid'"; // echo"$query";
		$result = mysqli_query($cnit, $query);
		$row = mysqli_fetch_array($result);
		$num_results = mysqli_num_rows($result);
		$nm_div = $row['nama'];
		
		if ($kodeid == "700-01-05" ) {
			echo "<b>LAPORAN BULANAN $nm_kd TAHUN $tahun";
		} else {
			echo "<b>LAPORAN BULANAN $nm_kd - $nm_div TAHUN $tahun";
		}
		
		echo"<br><br>";
		$query = "select * from hrd.br0 where divprodid='$divprodid' and kode='$kodeid' and ((left(tgltrans,4)='$tahun' and 
				  tglunrtr='0000-00-00') or LEFT(tglunrtr,4)='$tahun') and retur <> 'Y' order by tglunrtr,tgltrans,noslip"; //echo"$query";// and tgl <= '$akhir'";
		$result = mysqli_query($cnit, $query);
		$records = mysqli_num_rows($result);	
		$row = mysqli_fetch_array($result);
		if ($records) {
		$i = 1;
		$gtotal = $gtotal_ = 0;
		while ($i <= $records) {
			$tglunrtr = $row['tglunrtr']; //echo"$tglunrtr";
			if ($tglunrtr <>'0000-00-00') {
				$row['tgltrans'] = $row['tglunrtr']; 
			}
			$bln_ = substr($row['tgltrans'],0,7);
			$bulan_ = $row['tgltrans']; //echo"$tglunrtr";
			echo '<table border="1" cellspacing="0" cellpadding="1">';
			echo "<br>";
			echo "<b>Bulan : $bln_</b>";
			echo '<tr>';
			echo "<th align=center><small>No.</small></th>"; 
			echo "<th align=center><small>Tgl. Transfer</small></th>"; 
			echo "<th align=center><small>No Slip</small></th>"; 
			echo "<th align=center><small>Nama</small></th>";
			echo "<th align=center><small>Nama Dokter</small></th>";
			if ($divprodid=="EAGLE") {
				echo "<th align=center><small>Cabang</small></th>";
			} else {
			}
			echo "<th align=center>Keterangan</th>";
			echo "<th align=center>Realisasi</th>";
			echo "<th align=center><small>Jumlah IDR</small></th>";
			echo "<th align=center><small>Jumlah USD</small></th>";
			echo '</tr>';
			$total = $total_ = 0;
			$no = 0;
		    while ( ($i<=$records) and ($bln_ == substr($row['tgltrans'],0,7)) ) {
			echo "<tr>";
			$brid = $row['brId'];	
			$no = $no + 1;			
			$tgltrans = $row['tgltrans'];
			$tglunrtr = $row['tglunrtr'];
			$icabangid = $row['icabangid'];	
			$noslip = $row['noslip'];
			$dokterId = $row['dokterId'];  
			$divprodid = $row['divprodid']; //echo"$divprodid";
			$aktivitas1 = $row['aktivitas1'];
			$aktivitas2 = $row['aktivitas2'];
			$realisasi1 = $row['realisasi1'];
			$jumlah = $row['jumlah']; 
			$jumlah1 = $row['jumlah1'];// echo"$jumlah1";
			$karyawanId = $row['karyawanId']; 
			$ccyId = $row['ccyId'];
			$user1  = $row['user1'];
			//$nama_ar = $row['nama_ar'];
			$batal = $row['batal'];
			
			$nama_mr = '';
			$query_mr = "select nama from hrd.karyawan where karyawanId='$karyawanId'";
			$result_mr = mysqli_query($cnit, $query_mr);
			$num_results_mr = mysqli_num_rows($result_mr);
			if ($num_results_mr) {
				 $row_mr = mysqli_fetch_array($result_mr);
				 $nama_mr = $row_mr['nama'];
			}
			
			$nama_dkt = '';
			$query_dkt = "select nama from hrd.dokter where dokterId='$dokterId'"; //echo"$query_dkt";
			$result_dkt = mysqli_query($cnit, $query_dkt);
			$num_results_dkt = mysqli_num_rows($result_dkt);
			if ($num_results_dkt) {
				 $row_dkt = mysqli_fetch_array($result_dkt);
				 $nama_dkt = $row_dkt['nama'];
			}
			
			$nama_cab = '';
			$query_cab = "select MKT.icabang.nama from hrd.karyawan karyawan join MKT.icabang on karyawan.icabangid=MKT.icabang.icabangid
					  where karyawan.karyawanId='$karyawanId'";
			$result_cab = mysqli_query($cnit, $query_cab);
			$num_results_cab = mysqli_num_rows($result_cab);
			if ($num_results_cab) {
			 $row_cab = mysqli_fetch_array($result_cab);
			 $nama_cab = $row_cab['nama'];
			}
			
			echo "<td><small>$no</small></td>";
			if ($tglunrtr == '0000-00-00') {
				echo "<td><small>$tgltrans</small></td>";
			} else {
				echo "<td><small>$tglunrtr</small></td>";
			}

			if ($noslip=="") {
				echo "<td><small>&nbsp;<small></td>";
			} else {
				echo "<td><small>$noslip</small></td>";
			}

			echo "<td><small>$nama_mr</small></td>";
			echo "<td><small>$nama_dkt</small></td>";
			
			if ($divprodid=="EAGLE") {
				echo "<td><small>$nama_cab</samll></td>";
			} else {
			}

			echo "<td><small>$aktivitas1 $aktivitas2</small></td>";
			if ($realisasi1=="") {
				echo "<td><small>&nbsp;<small></td>";
			} else {
				echo "<td><small>$realisasi1</samll></td>";
			}

			if ($jumlah1==0) {
				if ($ccyId<>'IDR') {
					if ($batal=='Y') {
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
					if ($batal=='Y') {
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
			echo "<tr>";
			if ($divprodid=="EAGLE") {
				echo "<td>&nbsp;</td>";
			} else {
			}
			echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
			echo "<td align=right><b>Total :</td>";
			echo "<td align=right><b>".number_format($total,0)."</b></td>";
			if ($total_ == 0) {
				echo "<td align=right><b>".number_format($total_,0)."</b></td>";
			} else {
				echo "<td align=right><b>$ccyId_ ".number_format($total_,0)."</b></td>";
			}
			
			echo "</tr>";
		}// eof  i<= num_results
			echo "<tr>";
			if ($divprodid=="EAGLE") {
				echo "<td>&nbsp;</td>";
			} else {
			}
			echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
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


