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
    <title>REKAP BUDGET REQUEST</title>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>

<form id="rptgltr1" action="module/data_lama/lap_apv_rekapbr/tgltr0.php" method=post>
<?php
	include("config/koneksimysqli_it.php");
	include("config/common.php");
	//include("config/common3.php");

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
	
	   if ($_SESSION['JABATANID']=='12' OR $_SESSION['GROUP']=='1') { //adm       
			if ($divprodid=="PIGEO" or $divprodid=="PEACO") {
				$query = "select * from hrd.br0 where divprodid='$divprodid'
						  and left(tgl,7)='$periode' order by tgl"; //echo"$query";// and tgl <= '$akhir'";
			} else {
				$query = "select * from hrd.br0 where divprodid='$divprodid' 
						  and left(tgl,7)='$periode' order by tgl";//echo"$query";// and tgl <= '$akhir'";
			}
	   } 
	   // echo"$query";
		if (empty($query)) exit;
                $records1=0;
		$result = mysqli_query($cnit, $query);
		$records = mysqli_num_rows($result);	
		$row = mysqli_fetch_array($result);
		if ($records) {
			$i = 1;
			$gtotal = $gtotal_ = 0;
			while ($i <= $records) {
				$bln_ = $row['tgl'];		
				$bulan_ = $row['tgl']; //echo"$bulan_";
				echo '<table border="1" cellspacing="0" cellpadding="1">';
				echo "<br>";
				$tt_ = $row['tgl'];
				echo "<b>Tanggal BR : $tt_</b>";
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
				echo '<th align="center"><small>ACC</small></th>';
				echo '<th align="center"><small>Tgl. ACC</small></th>';
				echo '<th align="center"><small>Transfer</small></th>';
				echo '<th align="center"><small>Tgl. Transfer</small></th>';
				echo '<th align="center"><small>Edit</small></th>';
				echo '</tr>';
				$total = $total_ = 0;
				$no = 0;
			    while ( ($i<=$records) and ($bulan_ == $row['tgl']) ) {
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
					$kode = $row['kode'];
					
					$kode1 = substr($kode,-2);
				
					$j = "0000" . $i;
					$j = substr($j,-4);
				    $var_ = "br" . $j;   //nama variable diawali dengan "cust", contoh cust0001,cust0002,dst.
					$brsby = $brid;  //ganti $j dengan custid dari database
					
					
					$a = "0000" . $i;
					$a = substr($a,-4);
				    $vbr_ = "acc" . $a;   //nama variable diawali dengan "cust", contoh cust0001,cust0002,dst.
					$bracc = $brid;  //ganti $j dengan custid dari database
					
					
				
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
					
					if (($trf=='Y') or ($acc <> 'Y') or ($tgltrans<>'0000-00-00')) {
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
					
					if ($kode1=='03' or $kode1=='04') {
						echo "<td><a href='module/data_lama/lap_apv_rekapbr/breq00.php?brid=$brid&bulan1=$periode&entrymode=E&mode=A'>View/Edit</a></td>";
					} else {
						echo "<td><a href='module/data_lama/lap_apv_rekapbr/breq100.php?brid=$brid&bulan1=$periode&entrymode=E&mode=A'>View/Edit</a></td>";
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
							$query_cc = "select nilai from hrd.ccy where ccyId='$ccyId'";
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
						echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
						      <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
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
				echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>"; // report SBY
				
				echo "</tr>";
			}// eof  i<= num_results
			echo "<tr>";
			echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
			echo "<td align=right><b>Grand Total :</td>";
			echo "<td align=right><b>".number_format($gtotal,0)."</b></td>";
			if ($gtotal_ == 0 ) {
				echo "<td align=right><b>".number_format($gtotal_,0)."</b></td>";
			} else {
				echo "<td align=right><b>$ccyId_ ".number_format($gtotal_,0)."</b></td>";
			}
			echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>"; // report SBY
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
	echo "&nbsp;&nbsp;<input type=button id=cmdBack name=cmdBack value='Back' onclick='goto2(\"rptgltr1\",\"rptgltr0.php\")'>";
	echo "<input type=hidden name=records value=$records />";
	echo "<input type=hidden name=records1 value=$records1 />";
	echo "<input type=hidden name=divprodid value=$divprodid />";  
	echo "<input type=hidden name=periode value=$periode />";  

?>
</form>

</html>
