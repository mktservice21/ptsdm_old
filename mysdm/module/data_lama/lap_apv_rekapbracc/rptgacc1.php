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
  <title>REKAP ACC BUDGET REQUEST</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<script src="tgltr.js">
</script>
<body>
<form id="rptgacc1" action="rptgacc0.php" method=post>
<?php
	include("config/koneksimysqli_it.php");
	include("config/common.php");


	if (empty($_SESSION['USERID'])) {
	  echo 'not authorized';
	  exit;
	} else {
	  
		
		$srid = $_SESSION['USERID'];
		$srnama = $_SESSION['NAMALENGKAP'];
		$sr_id = substr('0000000000'.$srid,-10);
		$userid = $_SESSION['USERID'];
		
                $tanggal_ = str_replace('/', '-', $_POST['bulan']);
                $tgl01= date("Y-m-d", strtotime($tanggal_));
                
                $periode1= date("Y-m", strtotime($tgl01));
                $tahun_= date("Y", strtotime($tgl01));
                $tahun= date("Y", strtotime($tgl01));
                $bulan_= date("m", strtotime($tgl01));
                $mtgl_= date("d", strtotime($tgl01));
                $bln_= date("F", strtotime($tgl01));
		$periode = $tahun_.'-'.$bulan_.'-'.$mtgl_; 
		if ($periode == "-") {
			$periode = $_POST['periode'];
		} 
                
                $records1="";
		$bln_nm = nama_bulan($bulan_);
		$divprodid = $_POST['divprodid'];
		$disabled_ = "disabled";
		
		if ($divprodid=="" or $divprodid=="blank") {
			echo "Cabang dan Divisi harus dipilih!<br>";
			echo '<br><input type=button value="Back" onclick="go_back()">';
			exit;
		}
	
		$nama = $divprodid;
		
				
		$tanggal1 = date('d'); 
		$bln1 = date('m');
		$tahun1 = date('Y');
		$tanggal11 = date('d'); 
		$bln11 = date('m');
		$tahun11 = date('Y');		
		
		echo "Jakarta, $tanggal_ $bln_nm $tahun_<br><br>";
		echo "Kepada Yth :<br>";
		echo "Ibu Vanda / Lina <br>";
		echo "PT. SDM SBY<br>";
		echo "di tempat<br><br>";

		echo "<b><big>Rekap ACC Budget Request</big></b><br>";
		
	
	   if ($_SESSION['JABATANID']=='12' OR $_SESSION['GROUP']=='1') { //adm       
			if ($divprodid=="PIGEO" or $divprodid=="PEACO") {
				$query = "select * from hrd.br0 where divprodid='$divprodid'
						  and app_acc='$periode' order by app_acc"; //echo"$query";// and tgl <= '$akhir'";
			} else {
				$query1 = "select * from hrd.klaim where app_acc='$periode' order by app_acc";  //echo"$query1";
				$result1 = mysqli_query($cnit, $query1); //echo"$result1";
				$records1 = mysqli_num_rows($result1);	
				$row1 = mysqli_fetch_array($result1);
				if ($records1) {
					$i = 1;
					$gtotal1 = 0;
					while ($i <= $records1) {
						$bln_ = $row1['tgl'];
						$bulan_ = $row1['tgl']; //echo"$bulan_";
						echo '<table border="1" cellspacing="0" cellpadding="1">';
						echo "<br>";
						$tt_ = $row1['tgl'];
						echo '<tr>';
						echo '<th align="center"><small>No.</small></th>';
						echo '<th align="center"><small>No. Slip Transfer</small></th>';
						echo '<th align="center"><small>Realisasi</small></th>';
						echo '<th align="center"><small>Nama</small></th>';
						echo '<th align="center"><small>Keterangan</th>';
						echo '<th align="center"><small>Jumlah</small></th>';
						echo '<th align="center"><small>App. FS</small></th>';
						echo '<th align="center"><small>App. IS</small></th>';
						echo '</tr>';
						$total1 = 0;
						$no = 0;
					    while ( ($i<=$records1) and ($bulan_ == $row1['tgl']) ) {	 	    
							$no = $no + 1;
							$klaimid = $row1['klaimId'];			
							$tgl = $row1['tgl'];
							$aktivitas1 = $row1['aktivitas1'];
							$aktivitas2 = $row1['aktivitas2'];
							$jumlah = $row1['jumlah']; //echo"$jumlah";
							$realisasi = $row1['realisasi1'];
							$karyawanid = $row1['karyawanid']; //echo"kary=$karyawanid";
							$noslip = $row1['noslip'];
							$tgltrans = $row1['tgltrans'];
							$trf = $row1['trf'];
						//	$tglrpsby = $row1['tglrpsby'];
							$total1 = $total1 + $jumlah;
							$app_director = $row1['app_director'];
							$app_owner = $row1['app_owner'];
							$app_director_date = $row1['app_director_date'];
							$app_owner_date = $row1['app_owner_date'];
							$app_acc = $row1['app_acc'];
							$acc = $row1['acc'];
							
							$j = "0000" . $i;
							$j = substr($j,-4);
						    $var_ = "kl" . $j;   //nama variable diawali dengan "cust", contoh cust0001,cust0002,dst.
							$brsby = $klaimid;  //ganti $j dengan custid dari database
									
							$nama_mr = '';
						    $query_mr = "select nama from karyawan where karyawanid='$karyawanid'";// echo"$query_mr";
						    $result_mr = mysqli_query($cnit, $query_mr);
						    $num_results_mr = mysqli_num_rows($result_mr);
							if ($num_results_mr) {
							     $row_mr = mysqli_fetch_array($result_mr);
								 $nama_mr = $row_mr['nama'];
							}
					
							echo "<td><small>$no</small></td>";
							if ($noslip=="") {
								echo "<td>&nbsp;</td>";
							} else {
								echo "<td><small>$noslip</small></td>";
							}
							if ($realisasi=="") {
								echo "<td>&nbsp;</td>";
							} else {
								echo "<td><small>$realisasi</small></td>";
							}
							echo "<td><small>$nama_mr</small></td>";
							echo "<td><small>$aktivitas1 $aktivitas2</small></td>";
							echo "<td align=right><small>".number_format($jumlah,0)."</small></td>";
						
							if ($app_director == 'Y') {
								echo "<td><small>$app_director_date</small></td>";
							} else {
								if ($app_director == 'N') {
									echo "<td style=background-color:red><small>$app_director_date</small></td>";
								} else {
									echo "<td style=background-color:green><small>$app_director_date</small></td>";
								}
							}
							
							if ($app_owner == 'Y') {
								echo "<td><small>$app_owner_date</small></td>";
							} else {
								if ($app_owner == 'N') {
									echo "<td style=background-color:red><small>$app_owner_date</small></td>";
								} else {
									echo "<td style=background-color:green><small>$app_owner_date</small></td>";
								}
							}
							echo "</tr>";
						  	echo "</tr>";
							//$total1 = $total1 + $jumlah; 
							$gtotal1 = $gtotal1 + $jumlah;
							$row1 = mysqli_fetch_array($result1);// echo"$row";
							$i ++;
						} 
						echo "<tr>";
						echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
						echo "<td align=right><b>Total :</td>";
						echo "<td align=right><b>".number_format($total1,0)."</b></td>";
						echo "<td>&nbsp;</td><td>&nbsp;</td>";
						echo "</tr>";
					}// eof  i<= num_results
					echo "<tr>";
					echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
					echo "<td align=right><b>Grand Total :</td>";
					echo "<td align=right><b>".number_format($gtotal1,0)."</b></td>";
					echo "<td>&nbsp;</td><td>&nbsp;</td>";
					echo "</tr>";
					echo "</table>";
				} else {
					echo "<b><br><br>Data Klaim Diskon Periode $bln_nm $tahun tidak ditemukan!!!<br><br>";
				}
				echo "<br><b>REKAP APPROVAL BR EAGLE</b><br>";
				$query = "select * from hrd.br0 where divprodid='$divprodid' 
						  and app_acc='$periode' order by app_acc";//echo"$query";// and tgl <= '$akhir'";
			}
	   } 
		//echo $query;
		$result = mysqli_query($cnit, $query);
		$records = mysqli_num_rows($result);	
		$row = mysqli_fetch_array($result);
		if ($records) {
			$i = 1;
			$gtotal = $gtotal_ = 0;
			while ($i <= $records) {
				$bln_ = $row['app_acc'];		
				$bulan_ = $row['app_acc']; //echo"$bulan_";
				echo '<table border="1" cellspacing="0" cellpadding="1">';
				echo "<br>";
				$tt_ = $row['app_acc'];
				echo '<tr>';
				echo '<th align="left"><small>No</small></th>';
				echo '<th align="center"><small>No Slip</small></th>';
				echo '<th align="center"><small>Nama</small></th>';
				echo '<th align="center"><small>Nama Dokter</small></th>';
				echo '<th align="center">Keterangan</th>';
				echo '<th align="center"><small>Realisasi</small></th>';
				echo '<th align="center"><small>Jumlah IDR</small></th>';
				echo '<th align="center"><small>Jumlah USD</small></th>';
				echo '<th align="center"><small>App. FS</small></th>';
				echo '<th align="center"><small>App. IS</small></th>';
				
				echo '</tr>';
				$total = $total_ = 0;
				$no = 0;
			    while ( ($i<=$records) and ($bulan_ == $row['app_acc']) ) {
					$brid = $row['brId'];	
					$no = $no + 1;			
					$tglbr = $row['tgl'];
					$noslip = $row['noslip']; //echo"$noslip";
					$nosliptu = $row['nosliptu'];					
					$dokterid = $row['dokterId']; 
					$dokter = $row['dokter'];// echo"$dokter";
					$realisasi1 = $row['realisasi1'];
					$aktivitas1 = $row['aktivitas1'];
					$aktivitas2 = $row['aktivitas2'];
					$jumlah = $row['jumlah']; 
					$tgltrans = $row['tgltrans']; 
					$jumlah1 = $row['jumlah1'];
					$karyawanid = $row['karyawanId']; 
					$ccyId = $row['ccyId'];// echo"$ccyId";
					$trf = $row['trf']; // report SBY
					$batal = $row['batal']; // batal
					$app_director = $row['app_director'];
					$app_owner = $row['app_owner'];
					$app_director_date = $row['app_director_date'];
					$app_owner_date = $row['app_owner_date'];
					$app_acc = $row['app_acc'];
					$acc = $row['acc'];
				
					
				
					$nama_dkt = '';
					$query_dkt = "select nama from hrd.dokter where dokterid='$dokterid'"; //echo"$query_dkt";
					$result_dkt = mysqli_query($cnit, $query_dkt);
					$num_results_dkt = mysqli_num_rows($result_dkt);
					if ($num_results_dkt) {
						 $row_dkt = mysqli_fetch_array($result_dkt);
						 $nama_dkt = $row_dkt['nama'];
					}

					$nama_mr = '';
					$query_mr = "select nama from hrd.karyawan where karyawanid='$karyawanid'";
					$result_mr = mysqli_query($cnit, $query_mr);
					$num_results_mr = mysqli_num_rows($result_mr);
					if ($num_results_mr) {
						 $row_mr = mysqli_fetch_array($result_mr);
						 $nama_mr = $row_mr['nama'];
					}
				
					$nama_slp = '';
					$query_slp = "select noslip,jumlah,jumlah1,tgltrans from hrd.br0 where noslip1='$noslip' and noslip1<>''"; //echo"$query_slp";
					$result_slp = mysqli_query($cnit, $query_slp);
					$num_results_slp = mysqli_num_rows($result_slp);
					if ($num_results_slp) {
						 $row_slp = mysqli_fetch_array($result_slp);
						 $no_slip = $row_slp['noslip']; //echo"no=$no_slp";
						 $jmlh = $row_slp['jumlah'];
						 $jmlh1 = $row_slp['jumlah1']; 
						 $tgltr = $row_slp['tgltrans'];
					}	
										
					echo "<tr>";
				
						echo "<td><small>$no</small></td>";
					if ($nosliptu=="") {
						if ($noslip==""){
							echo "<td><small>&nbsp;<small></td>";
						} else {
							echo "<td><small>$noslip</small></td>";
						}
					} else {
						echo "<td><small>$nosliptu</small></td>";
					}
					echo "<td><small>$nama_mr</small></td>";
					if ($nama_dkt==""){
						if ($dokter==""){
							echo "<td><small>&nbsp;<small></td>";
						} else {
							echo "<td><small>$dokter</small></td>";
						}
					} else {
						echo "<td><small>$nama_dkt</small></td>";
					}
					echo "<td><small>$aktivitas1 $aktivitas2 $nama_slp</small></td>";
					
					if ($realisasi1==""){
						echo "<td><small>&nbsp;<small></td>";
					} else {
						echo "<td><small>$realisasi1</small></td>";
					}

					if ($jumlah1<>'0') {
						$jumlah = $jumlah1;
					} else {
						if ($batal == 'Y') {
							$jumlah = 0; 
						} else {
							$jumlah = $row['jumlah']; 
						}
					}
					
					if ($ccyId<>'IDR') {
						echo "<td><small>&nbsp;</small></td>";
						echo "<td align=right><small>$ccyId ".number_format($jumlah,0)."</small></td>";
						$total_ = $total_ + $jumlah;
						$gtotal_ = $gtotal_ + $jumlah;
						$ccyId_ = $ccyId;
					} else {
						echo "<td align=right><small>".number_format($jumlah,0)."</small></td>";
						echo "<td><small>&nbsp;</small></td>";
						$total = $total + $jumlah;
						$gtotal = $gtotal + $jumlah;
					}
					
					if ($app_director == 'Y') {
						echo "<td><small>$app_director_date</small></td>";
					} else {
						if ($app_director == 'N') {
							echo "<td style=background-color:red><small>$app_director_date</small></td>";
						} else {
							echo "<td style=background-color:green><small>$app_director_date</small></td>";
						}
					}
					
					if ($app_owner == 'Y') {
						echo "<td><small>$app_owner_date</small></td>";
					} else {
						if ($app_owner == 'N') {
							echo "<td style=background-color:red><small>$app_owner_date</small></td>";
						} else {
							echo "<td style=background-color:green><small>$app_owner_date</small></td>";
						}
					}
					
					
					echo "</tr>";
					
					
					if ($num_results_slp=='1') {
						echo "<tr>";
						echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
						if ($jmlh > $jmlh1) {
							echo "<td><small>Kelebihan BR $tgltr - $no_slip</small></td>";
						} else {
							echo "<td><small>Kekurangan BR $tgltr - $no_slip</small></td>";
						}
						
						$jmlh2 = $jmlh1 - $jmlh; 
						
						if ($ccyId<>'IDR') {
							$query_cc = "select nilai from ccy where ccyId='$ccyId'";
							$result_cc = mysqli_query($cnit, $query_cc);
							$num_results_cc = mysqli_num_rows($result_cc);
							if ($num_results_cc) {
								 $row_cc = mysqli_fetch_array($result_cc);
								 $nilai_cc = $row_cc['nilai'];
							}
							echo "<td align=right><small>$ccyId ".number_format($jmlh2,0)."</small></td>";
							$jmlh2 = $jmlh2 * $nilai_cc; 
						} else {
							echo "<td align=right><small>".number_format($jmlh2,0)."</small></td>";
						}
						echo "</tr>";
						$sub_total = $jumlah + $jmlh2;
						echo "<tr>";
						echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
						echo "<td><small>Sub Total</small></td>";
						echo "<td align=right><small>".number_format($sub_total,0)."</small></td>";
						echo "</tr>";
						echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;
						      <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
						$jumlah = $sub_total;
					} else {
					}
		            $row = mysqli_fetch_array($result);
		            $i++;
				}// break per tanggal transfer
				echo "<tr>";
				echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
				echo "<td align=right><b>Total :</td>";
				echo "<td align=right><b>".number_format($total,0)."</b></td>";
				if ($total_ == 0 ) {
					echo "<td align=right><b>".number_format($total_,0)."</b></td>";
				} else {
					echo "<td align=right><b>$ccyId_ ".number_format($total_,0)."</b></td>";
				}
				echo "<td>&nbsp;</td><td>&nbsp;</td>"; // report SBY
				
				echo "</tr>";
			}// eof  i<= num_results
			echo "</table>";
		} else {
			echo "<br /><b>Data tidak ditemukan!!!</b><br/><br/>";		
		}	
    }  // if (empty($_SESSION['srid'])) 
	
	echo "</table>";
	echo "<table>";
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	echo "<tr><td>Dibuat oleh,</td></tr>";
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	//echo "$sr_id";
	if ($sr_id=='0000000148') {
		echo "<tr><td>Marianne Prasanti</td></tr>";
	} else {
		if ($sr_id=='0000000566') {
			echo "<tr><td>Ernilya</td></tr>";
		}	
	}

	echo "</table>\n";
      
	//echo "<br><br><input type=submit name=cmdSave id=cmdSave value=Back>";
	//echo "&nbsp;&nbsp;<input type=button id=cmdBack name=cmdBack value='OK' onclick='goto2(\"rptgacc1\",\"rptgacc0.php\")'>";
	echo "<input type=hidden name=records value=$records />";
	echo "<input type=hidden name=records1 value=$records1 />";
	echo "<input type=hidden name=divprodid value=$divprodid />";  
	echo "<input type=hidden name=periode value=$periode />";  

?>
</form>
</body>
</html>


