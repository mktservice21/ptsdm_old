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
    <title>Realisasi BR SBY</title>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<body>
<form id="rprlsby1" action="rprlsby0.php" method=post>
<?php
	include("config/common.php");
	//include("common3.php");
        include "config/koneksimysqli_it.php";

	if (empty($_SESSION['USERID'])) {
	  echo 'not authorized';
	  exit;
	} else {
	  
		
		$srid = $_SESSION['USERID'];
        $srnama = $_SESSION['NAMALENGKAP'];
		$sr_id = substr('0000000000'.$srid,-10);
		$userid = $_SESSION['USERID'];
		$divprodid = $_POST['divprodid']; 
		//$lampiran1 = $_POST['lampiran'];
                /*
		$bulan1 = $_POST['bulan1'];
		$nm_bln1 = nama_bulan($bulan1); //echo "$nm_bln1";
		$tahun1 = $_POST['tahun1'];
		$periode1 = $tahun1.'-'.$bulan1; 
                */
                $tgl01=$_POST['bulan'];
                $periode1= date("Y-m", strtotime($tgl01));
                $tahun= date("Y", strtotime($tgl01));
                $tahun1= date("Y", strtotime($tgl01));
                $bulan1= date("m", strtotime($tgl01));
                $periode1 = $tahun1.'-'.$bulan1;
                
                $nm_bln1= date("F", strtotime($tgl01));
                $bln_nm= date("F", strtotime($tgl01));
		$bln_="";
		$where="";
		$ccyid_="";
		$where_="";
		$karyawanId="";
                
		echo "<B>REALISASI BR SBY $tahun<BR>";
		if ($divprodid=='A' or $divprodid=='') {
		} else {
			echo "DIVISI : $divprodid</b><br>";
			$where_ = $where."and divprodid='$divprodid'";
		}

		echo "<br>"; 
		//$query = "select * from br0 where '$periode1' <= tgltrm and tgltrm <= '$periode2' ".$where_." order by tgltrm,noslip";// echo"$query";
		
			if ($divprodid=="PIGEO" or $divprodid=="PEACO" or $divprodid=="HO") {
				$query = "select * from hrd.br0 where left(tglrpsby,7)='$periode1' ".$where_."  order by tglrpsby,noslip"; //echo"$query";
			} else {
				$query1 = "select * from hrd.klaim where left(tglrpsby,7)='$periode1' order by tglrpsby"; // echo"$query1";
				$result1 = mysqli_query($cnit, $query1); //echo"$query1";
				$records1 = mysqli_num_rows($result1);	
				$row1 = mysqli_fetch_array($result1);
				if ($records1) {
					$i = 1;
					$gtotal1 = 0;
					while ($i <= $records1) {
						$bln_ = $row1['tglrpsby'];
						$bulan_ = $row1['tglrpsby']; //echo"$bulan_";
						echo '<table border="1" cellspacing="0" cellpadding="1">';
						echo "<br>";
						$tt_ = $row1['tglrpsby'];
						echo "<b>Report SBY : $tt_</b>";
						echo '<tr>';
						echo '<th align="center"><small>No.</small></th>';
						echo '<th align="center"><small>Nama</small></th>';
						echo '<th align="center"><small>Tanggal Transfer</small></th>';
						echo '<th align="center"><small>No Slip</small></th>';
						echo '<th align="center"><small>Keterangan</th>';
						echo '<th align="center"><small>Distributor</th>';
						echo '<th align="center"><small>Jumlah IDR</small></th>';
						echo '<th align="center"><small>Jumlah USD</small></th>';
						echo '<th align="center"><small>Realisasi</small></th>';
						echo '</tr>';
						$total1 = 0;
						$no = 0;
					    while ( ($i<=$records1) and ($bulan_ == $row1['tglrpsby']) ) {	 	    
							$no = $no + 1;
							$klaimid = $row1['klaimId'];			
							$tgl = $row1['tgl'];
							$aktivitas1 = $row1['aktivitas1'];
							$aktivitas2 = $row1['aktivitas2'];
							$jumlah = $row1['jumlah']; //echo"$jumlah";
							$realisasi = $row1['realisasi1'];
							$karyawanId = $row1['karyawanid']; //echo"kary=$karyawanId";
							$noslip = $row1['noslip'];
							$tgltrans = $row1['tgltrans'];
							$distid = $row1['distid'];
							$sby = $row1['sby'];
							$tglrpsby = $row1['tglrpsby'];
							$total1 = $total1 + $jumlah;
							
							$j = "0000" . $i;
							$j = substr($j,-4);
						    $var_ = "kl" . $j;   //nama variable diawali dengan "cust", contoh cust0001,cust0002,dst.
							$brsby = $klaimid;  //ganti $j dengan custid dari database
									
							$nama_mr = '';
						    $query_mr = "select nama from hrd.karyawan where karyawanId='$karyawanId'";// echo"$query_mr";
						    $result_mr = mysqli_query($cnit, $query_mr);
						    $num_results_mr = mysqli_num_rows($result_mr);
							if ($num_results_mr) {
							     $row_mr = mysqli_fetch_array($result_mr);
								 $nama_mr = $row_mr['nama'];
							}
							
							$nama_dst = '';
						    $query_dst = "select nama from MKT.distrib0 where distid='$distid'";// echo"$query_mr";
						    $result_dst = mysqli_query($cnit, $query_dst);
						    $num_results_dst = mysqli_num_rows($result_dst);
							if ($num_results_dst) {
							     $row_dst = mysqli_fetch_array($result_dst);
								 $nama_dst = $row_dst['nama'];
							}
					
							echo "<td><small>$no</small></td>";
							echo "<td><small>$nama_mr</small></td>";
							echo "<td><small>$tgltrans</small></td>";
							if ($noslip=="") {
								echo "<td>&nbsp;</td>";
							} else {
								echo "<td><small>$noslip</small></td>";
							}
							echo "<td><small>$aktivitas1 $aktivitas2</small></td>";
							echo "<td><small>$nama_dst</small></td>";
							echo "<td align=right><small>".number_format($jumlah,0)."</small></td>";
							echo "<td>&nbsp;</td>";
							if ($realisasi=="") {
								echo "<td>&nbsp;</td>";
							} else {
								echo "<td><small>$realisasi</small></td>";
							}
							echo "</tr>";
							//$total1 = $total1 + $jumlah; 
							$gtotal1 = $gtotal1 + $jumlah;
							$row1 = mysqli_fetch_array($result1);// echo"$row";
							$i ++;
						} 
						echo "<tr>";
						echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
						echo "<td align=right><b>Total :</td>";
						echo "<td align=right><b>".number_format($total1,0)."</b></td>";
						echo "<td>&nbsp;</td><td>&nbsp;</td>";
						echo "</tr>";
					}// eof  i<= num_results
					echo "<tr>";
					echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
					echo "<td align=right><b>Grand Total :</td>";
					echo "<td align=right><b>".number_format($gtotal1,0)."</b></td>";
					echo "<td>&nbsp;</td><td>&nbsp;</td>";
					echo "</tr>";
					echo "</table>";
				} else {
					echo "<b><br><br>Data Klaim Diskon Periode $bln_nm $tahun tidak ditemukan!!!<br><br>";
				}
				echo "<br><b>REKAP BR EAGLE</b><br>";
				$query = "select * from hrd.br0 where left(tglrpsby,7)='$periode1' ".$where_."  order by tglrpsby,noslip"; //echo"$query";
			}

		//$query = "select * from br0 where left(tglrpsby,7)='$periode1' ".$where_."  order by tglrpsby,noslip"; //echo"$query";
		$result = mysqli_query($cnit, $query);
		$records = mysqli_num_rows($result);	
		$row = mysqli_fetch_array($result);
		if ($records) {
		$i = 1;
		$gtotal = $gtotal_ = $gtotalru1_ =$gtotalru2_ = $gtotalri1_ =$gtotalri2_ = 0;
		while ($i <= $records) {
			$bln_ = substr($row['tglrpsby'],0,7);
			$bulan_ = $row['tglrpsby'];
			echo '<table border="1" cellspacing="0" cellpadding="1">';
			echo "<br>";
			echo "<b>Report SBY : $bulan_</b>";
			echo '<tr>';
			echo '<th><small>No. </small></th>';
			echo '<th><small>Nama Pembuat</small></th>';
			echo '<th><small>Tgl. Transfer</small></th>';
			echo '<th><small>No Slip</small></th>';
			echo '<th><small>Keterangan</small></th>';
			echo '<th><small>Nama Dokter</small></th>';
			echo '<th><small>Jumlah IDR</small></th>';
			echo '<th><small>Jumlah USD</small></th>';
			echo '<th><small>Nama Realisasi</small></th>';
			echo '<th><small>Tgl. Terima</small></th>';
			echo '<th><small>Jumlah Realisasi (IDR)</small></th>';
			echo '<th><small>Jumlah Realisasi (USD)</small></th>';
			echo '</tr>';
			$total = $total_ = $totalru1_ = $totalru2_ = $totalri1_ = $totalri2_ = 0;
			$no = 0;
		   while ( ($i<=$records) and ($bulan_ == $row['tglrpsby']) ) {
			$no = $no + 1;	
			$karyawanId = $row['karyawanId'];
			$jumlah = $row['jumlah'];
			$jumlah1 = $row['jumlah1'];
			$brid = $row['brId'];
			$tgltrm = $row['tgltrm'];
			$tgltrans = $row['tgltrans'];
			$divprodid = $row['divprodid'];
			$dokterId = $row['dokterId'];
			$dokter = $row['dokter']; //echo"$dokter";
			$aktivitas1 = $row['aktivitas1'];
			$aktivitas2 = $row['aktivitas2'];
			$realisasi1 = $row['realisasi1'];
			$noslip = $row['noslip'];
			$ccyid = $row['ccyId'];

			$nama_mr = '';
			$query_mr = "select nama from hrd.karyawan where karyawanId='$karyawanId'"; 
			$result_mr = mysqli_query($cnit, $query_mr);
			$num_results_mr = mysqli_num_rows($result_mr);
			if ($num_results_mr) {
				 $row_mr = mysqli_fetch_array($result_mr);
				 $nama_mr = $row_mr['nama'];
			}
			
			$nama_dkt = '';
			$query_dkt = "select nama from hrd.dokter where dokterId='$dokterId'"; 
			$result_dkt = mysqli_query($cnit, $query_dkt);
			$num_results_dkt = mysqli_num_rows($result_dkt);
			if ($num_results_dkt) {
				 $row_dkt = mysqli_fetch_array($result_dkt);
				 $nama_dkt = $row_dkt['nama'];
			}
	
			if ($nama_dkt=='') {
				$nama_dkt = $dokter;
			}
			
			echo '<tr>';
			echo "<td><small>$no</small></td>";
			echo "<td><small>$nama_mr</small></td>";
			echo "<td><small>$tgltrans</small></td>";	
			echo "<td><small>$noslip</small></td>";					
			echo "<td><small>$aktivitas1 $aktivitas2</small></td>";
			if ($nama_dkt<>"") {
				echo "<td><small>$nama_dkt</small></td>";
			} else {
				echo '<td>&nbsp;</td>';
			}
			if ($ccyid<>'IDR') {
				echo "<td><small>&nbsp;</small></td>";
				echo "<td align=right><small>$ccyid ".number_format($jumlah,0)."</small></td>";
				$total_ = $total_ + $jumlah;
				$gtotal_ = $gtotal_ + $jumlah;
				$ccyid_ = $ccyid;
			} else {
				echo "<td align=right><small>".number_format($jumlah,0)."</small></td>";
				echo "<td><small>&nbsp;</small></td>";
				$total = $total + $jumlah;
				$gtotal = $gtotal + $jumlah;
			}
			echo "<td><small>$realisasi1</small></td>";
			
			if ($tgltrm <>'0000-00-00') {
				echo "<td><small>$tgltrm</small></td>";
				if ($ccyid<>'IDR') {
					echo "<td><small>&nbsp;</small></td>";
					if ($jumlah1==0) {
						echo "<td align=right><small>$ccyid ".number_format($jumlah,0)."</small></td>";
						$totalru1_ = $totalru1_ + $jumlah;
						$gtotalru1_ = $gtotalru1_ + $jumlah;
					} else {
						echo "<td align=right><small>$ccyid ".number_format($jumlah1,0)."</small></td>";
						$totalru2_ = $totalru2_ + $jumlah1;
						$gtotalru2_ = $gtotalru2_ + $jumlah1;
					}
					$ccyid_ = $ccyid;
				} else {
					if ($jumlah1==0) {
						echo "<td align=right><small>".number_format($jumlah,0)."</small></td>";
						$totalri1_ = $totalri1_ + $jumlah;
						$gtotalri1_ = $gtotalri1_ + $jumlah;
					} else {
						echo "<td align=right><small>".number_format($jumlah1,0)."</small></td>";
						$totalri2_ = $totalri2_ + $jumlah1;
						$gtotalri2_ = $gtotalri2_ + $jumlah1;
					}
					echo "<td><small>&nbsp;</small></td>";
				}
			} else {
				echo '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>';
			}

			echo '</tr>';

		    $row = mysqli_fetch_array($result);
		    $i++;
		}// break per bulan
			echo "<tr>";
			echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
			echo "<td align=right><b>Total :</td>";
			echo "<td align=right><b>".number_format($total,0)."</b></td>";
			echo "<td align=right><b>$ccyid_ ".number_format($total_,0)."</b></td>";
			echo "<td>&nbsp;</td><td>&nbsp;</td>";
			if ($totalri1_==0) {
				echo "<td align=right><b>".number_format($totalri2_,0)."</b></td>";
			} else {
				echo "<td align=right><b>".number_format($totalri1_,0)."</b></td>";
			}
			if ($totalru1_==0) {
				echo "<td align=right><b>$ccyid_ ".number_format($totalru2_,0)."</b></td>";
			} else {
				echo "<td align=right><b>$ccyid_ ".number_format($totalru1_,0)."</b></td>";
			}
			echo "</tr>";
		}// eof  i<= num_results
			echo "<tr>";
			echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
			echo "<td align=right><b>Grand Total :</td>";
			echo "<td align=right><b>".number_format($gtotal,0)."</b></td>";
			echo "<td align=right><b>$ccyid_ ".number_format($gtotal_,0)."</b></td>";
			echo "<td>&nbsp;</td><td>&nbsp;</td>";
			if ($gtotalri1_==0) {
				echo "<td align=right><b>".number_format($gtotalri2_,0)."</b></td>";
			} else {
				echo "<td align=right><b>".number_format($gtotalri1_,0)."</b></td>";
			}
			if ($gtotalru1_==0) {
				echo "<td align=right><b>$ccyid_ ".number_format($gtotalru2_,0)."</b></td>";
			} else {
				echo "<td align=right><b>$ccyid_ ".number_format($gtotalru1_,0)."</b></td>";
			}
			echo "</tr>";
		echo "</table>";
		} else {
			echo "<br /><b>Data tidak ditemukan!!!</b><br/><br/>";		
	    }	
    }  // if (empty($_SESSION['srid'])) 
	echo "<BR><input type=submit id=cmdBack name=cmdBack value='Back'>";
      
	if (empty($_SESSION['srid'])) {
	} else {
	  do_show_menu($_SESSION['jabatanid'],'N');
	}
?>
</form>
</body>
</html>


