<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=Rekap Budget Request Klaim.xls");
    }
?>

<html>
<head>
  <title>Rekap Budget Request</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<script src="tgltr.js">
</script>
<body>
<form id="rptgltr3" action="module/data_lama/lap_apv_rekapbrklaim/tgltr1.php" method=post>
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
		$userid = $_SESSION['IDCARD'];
                $tgl01=$_POST['bulan'];
                $periode1= date("Y-m", strtotime($tgl01));
                $tahun_= date("Y", strtotime($tgl01));
                $bulan_= date("m", strtotime($tgl01));
                $bln_= date("F", strtotime($tgl01));
		$periode = $tahun_.'-'.$bulan_; 
		if ($periode == "-") {
			$periode = $_POST['periode'];
		} 
		
		
		$bln_nm = nama_bulan($bulan_);
		$divprodid = $_POST['divprodid'];
		$disabled_ = "disabled";
		
		if ($divprodid=="" or $divprodid=="blank") {
			echo "Cabang dan Divisi harus dipilih!<br>";
			echo '<br><input type=button value="Back" onclick="go_back()">';
			exit;
		}
				
		if ($divprodid=='PIGEO' or $divprodid=='PEACO') {
			$nama = $divprodid .'& OTC';
		} else {
			$nama = $divprodid;
		}
				
		$tanggal1 = date('d'); 
		$bln1 = date('m');
		$tahun1 = date('Y');
		$tanggal11 = date('d'); 
		$bln11 = date('m');
		$tahun11 = date('Y');
		$tahun_1 = $tahun1 - 1;
		$tahun_2 = $tahun1 + 1;
		
		echo "<b>REKAP APPROVAL BR $nama PERIODE $bln_nm $tahun_</b><br>";
	 
		$query = "select * from hrd.klaim where left(tgl,7)='$periode' order by tgl";  //echo"$query1";
		$result = mysqli_query($cnit, $query);
		$records = mysqli_num_rows($result);	
		$row = mysqli_fetch_array($result);
		if ($records) {
			$i = 1;
			$gtotal = 0;
			while ($i <= $records) {
				$bln_ = $row['tgl'];		
				$bulan_ = $row['tgl']; //echo"$bulan_";
				echo '<table border="1" cellspacing="0" cellpadding="1">';
				echo "<br>";
				$tt_ = $row['tgl'];
				echo "<b>Tanggal BR : $tt_</b>";
				echo '<tr>';
				echo '<th align="center"><small>No.</small></th>';
				echo '<th align="center"><small>No. Slip Transfer</small></th>';
				echo '<th align="center"><small>Realisasi</small></th>';
				echo '<th align="center"><small>Nama</small></th>';
				echo '<th align="center"><small>Keterangan</th>';
				echo '<th align="center"><small>Jumlah</small></th>';
				echo '<th align="center"><small>App. FS</small></th>';
				echo '<th align="center"><small>App. IS</small></th>';
				echo '<th align="center"><small>ACC</small></th>';
				echo '<th align="center"><small>Tgl. ACC</small></th>';
				echo '<th align="center"><small>Transfer</small></th>';
				echo '<th align="center"><small>Tgl. Transfer</small></th>';
				//echo '<th align="center"><small>Edit</small></th>';
				echo '</tr>';
				$total = 0;
				$no = 0;
			    while ( ($i<=$records) and ($bulan_ == $row['tgl']) ) {
					$no = $no + 1;			
					$klaimid = $row['klaimId'];			
					$tgl = $row['tgl'];
					$aktivitas1 = $row['aktivitas1'];
					$aktivitas2 = $row['aktivitas2'];
					$jumlah = $row['jumlah'];
					$realisasi = $row['realisasi1'];
					$karyawanid = $row['karyawanid'];
					$noslip = $row['noslip'];
					$tgltrans = $row['tgltrans'];
					$trf = $row['trf'];
					$app_director = $row['app_director'];
					$app_owner = $row['app_owner'];
					$app_director_date = $row['app_director_date'];
					$app_owner_date = $row['app_owner_date'];
					$app_acc = $row['app_acc'];
					$acc = $row['acc'];
					$total = $total + $jumlah;
					$gtotal = $gtotal + $jumlah;
				
					$j = "0000" . $i;
					$j = substr($j,-4);
				    $var_ = "br" . $j;   //nama variable diawali dengan "cust", contoh cust0001,cust0002,dst.
					$brsby = $klaimid;  //ganti $j dengan custid dari database
					
					
					$a = "0000" . $i;
					$a = substr($a,-4);
				    $vbr_ = "acc" . $a;   //nama variable diawali dengan "cust", contoh cust0001,cust0002,dst.
					$bracc = $klaimid;  //ganti $j dengan custid dari database
					
					$nama_mr = '';
					$query_mr = "select nama from hrd.karyawan where karyawanid='$karyawanid'";
					$result_mr = mysqli_query($cnit, $query_mr);
					$num_results_mr = mysqli_num_rows($result_mr);
					if ($num_results_mr) {
						 $row_mr = mysqli_fetch_array($result_mr);
						 $nama_mr = $row_mr['nama'];
					}
									
					echo "<tr>";
				
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
					
					if (($acc=='Y') or ($app_director <> 'Y') or ($app_owner <> 'Y')) {
						$checked_ = "checked";
						echo "<td align=center><input type='checkbox' name='$vbr_' value='$bracc' $checked_  $disabled_>";
					} else {
						echo '<td align=center><input type="checkbox" name="'.$vbr_.'" value="'.$bracc.'"></td>';
					}
					
					if ($app_acc=="0000-00-00"){
						echo "<td align=center><small>&nbsp;<small></td>";
					} else {
						echo "<td align=center><small>$app_acc</small></td>";
					}
					
					if (($trf=='Y') or ($acc <> 'Y')) {
						$checked_ = "checked";
						echo "<td align=center><input type='checkbox' name='$var_' value='$brsby' $checked_  $disabled_>";
					} else {
						echo '<td align=center><input type="checkbox" name="'.$var_.'" value="'.$brsby.'"></td>';
					}
					
					if ($tgltrans=="0000-00-00"){
						echo "<td align=center><small>&nbsp;<small></td>";
					} else {
						echo "<td align=center><small>$tgltrans</small></td>";
					}
					
					//echo "<td><a href='breq100.php?brid=$brid&bulan1=$periode&entrymode=E&mode=A'>View/Edit</a></td>";
									
					echo "</tr>";
					
					
					
		            $row = mysqli_fetch_array($result);
		            $i++;
				}// break per tanggal transfer
				echo "<tr>";
				echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
				echo "<td align=right><b>Total :</td>";
				echo "<td align=right><b>".number_format($total,0)."</b></td>";
				echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>"; // report SBY
				
				echo "</tr>";
			}// eof  i<= num_results
			echo "<tr>";
			echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
			echo "<td align=right><b>Grand Total :</td>";
			echo "<td align=right><b>".number_format($gtotal,0)."</b></td>";
			echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>"; // report SBY
			echo "</tr>";
			echo "</table>";
		} else {
			echo "<br /><b>Data tidak ditemukan!!!</b><br/><br/>";		
		}	
		
		echo '<br><tr>';
		echo '<td>Tanggal ACC :</td>';
		echo "<td>&nbsp;&nbsp;<select name='tanggal11' id='tanggal11' >";
		for ($i=1; $i<32; $i++) {
			$i_ = substr('0'.$i,-2);		
			if ($i == $tanggal11) {
				echo "<option selected='selected' value='$i_'>$i_</option>";	
			} else {
				echo "<option value='$i_'>$i_</option>";					
			}
		}		
		echo '</select>';
	
		echo "&nbsp;&nbsp;<select name='bln11' id='bln11'>";
		for ($i=0; $i<12; $i++) {
			$j = '0'.ltrim(strval($i+1));
			$j = substr($j,-2,2);
			$bln_ = nama_bulan($j);
			if ($j == $bln11) {
				echo "<option selected='selected' value='$j'>$bln_</option>";	
			} else {
				echo "<option value='$j'>$bln_</option>";					
			}
		}		
		echo '</select>';
		//201401
		$tahun_1 = date('Y') - 1;
		$tahun_2 = date('Y') + 1;
		echo "&nbsp;&nbsp;<select name='tahun11' id='tahun11'>";
		echo "<option value='$tahun_1'>$tahun_1</option>";
		echo "<option selected='selected' value='$tahun1'>$tahun1</option>";
		echo "<option value='$tahun_2'>$tahun_2</option>";
		echo '</select>';		
		echo '</td>';		  
		echo '</tr>'; 
		
		
		echo '<br><br><tr>';
		echo '<td>Tanggal Transfer :</td>';
		echo "<td>&nbsp;&nbsp;<select name='tanggal1' id='tanggal1' >";
		for ($i=1; $i<32; $i++) {
			$i_ = substr('0'.$i,-2);		
			if ($i == $tanggal1) {
				echo "<option selected='selected' value='$i_'>$i_</option>";	
			} else {
				echo "<option value='$i_'>$i_</option>";					
			}
		}		
		echo '</select>';
	
		echo "&nbsp;&nbsp;<select name='bln1' id='bln1'>";
		for ($i=0; $i<12; $i++) {
			$j = '0'.ltrim(strval($i+1));
			$j = substr($j,-2,2);
			$bln_ = nama_bulan($j);
			if ($j == $bln1) {
				echo "<option selected='selected' value='$j'>$bln_</option>";	
			} else {
				echo "<option value='$j'>$bln_</option>";					
			}
		}		
		echo '</select>';
		//201401
		$tahun_1 = date('Y') - 1;
		$tahun_2 = date('Y') + 1;
		echo "&nbsp;&nbsp;<select name='tahun1' id='tahun1'>";
		echo "<option value='$tahun_1'>$tahun_1</option>";
		echo "<option selected='selected' value='$tahun1'>$tahun1</option>";
		echo "<option value='$tahun_2'>$tahun_2</option>";
		echo '</select>';		
		echo '</td>';		  
		echo '</tr>'; 

    }  // if (empty($_SESSION['srid'])) 
      
	echo "<br><br><input type=submit name=cmdSave id=cmdSave value=Save>";
	echo "&nbsp;&nbsp;<input type=button id=cmdBack name=cmdBack value='Back' onclick='goto2(\"rptgltr3\",\"rptgltr2.php\")'>";
	echo "<input type=hidden name=records value=$records />";
	echo "<input type=hidden name=divprodid value=$divprodid />";  
	echo "<input type=hidden name=periode value=$periode />";  

?>
</form>
</body>
</html>


