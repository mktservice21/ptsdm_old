<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REKAP BR TRANSFER VIA SURABAYA.xls");
    }
?>

<html>
<head>
  <title>REKAP BR TRANSFER VIA SURABAYA</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<script src="rpbrsby.js">
</script>
<body onload="setFocus('awal')">
<form id="rpbrsby3" action="module/data_lama/lap_apv_rekapbraccsby/tgltr0.php" method=post>

<?php
	include("config/koneksimysqli_it.php");
	include("config/common.php");


	if (empty($_SESSION['USERID'])) {
		echo 'not authorized';
		exit;
	} else {
		
                $tgl01=$_POST['bulan'];
                $periode1= date("Y-m", strtotime($tgl01));
                $tahun_= date("Y", strtotime($tgl01));
                $tahun= date("Y", strtotime($tgl01));
                $bulan_= date("m", strtotime($tgl01));
                $bulan= date("m", strtotime($tgl01));
                $bln_= date("F", strtotime($tgl01));
		$periode = $tahun_.'-'.$bulan_; 
		if ($periode == "-") {
			$periode = $_POST['periode'];
		} 
		$bln_ = nama_bulan($bulan);
		$divprodid = $_POST['divprodid']; //echo"$divprodid";
		$disabled_ = "disabled";

		if ($divprodid=="" or $divprodid=="blank") {
			echo "Divisi harus dipilih<br>";
			echo '<br><input type=button value="Back" onclick="go_back()">';
			exit;
		}
		
		if (($divprodid=="OTC") or ($divprodid=='PIGEO')) {
			$divprodid = 'PIGEO';
			$nama = 'PIGEON & OTC';
		} else {
			$nama = 'EAGLE';
		}
		
		$records1="";
/*
		$periode = $_POST['periode']; //echo"$periode";	
		$periode1 = $periode; 
		*/
		
		//echo"$periode1//$periode";
					
		$tanggal1 = date('d'); 
		$bln1 = date('m');
		$tahun1 = date('Y');
		$tanggal11 = date('d'); 
		$bln11 = date('m');
		$tahun11 = date('Y');
		$tahun_1 = $tahun1 - 1;
		$tahun_2 = $tahun1 + 1;
		
		$total_=0;
		$gdtotal=0;
		$total1=0;
		$gdtotal_1=0;
		if ($divprodid=='EAGLE') {
			$query = "select br0.*, br_kode.nama, MKT.icabang.nama as nm_cab, karyawan.areaid
					  from hrd.br0 br0
					  join hrd.br_kode br_kode on br0.kode=br_kode.kodeid 
					  join MKT.icabang on br0.icabangid=MKT.icabang.icabangid 
					  JOIN hrd.karyawan karyawan on br0.karyawanid=karyawan.karyawanid
					  where br0.divprodid='$divprodid' and left(tgl,7)='$periode' and via ='Y'
					  order by tgl,nama,tgltrans"; //echo"$query";
		} else {
			$query = "select br0.*, br_kode.nama, MKT.icabang.nama as nm_cab, karyawan.areaid
					  from hrd.br0 br0 
					  join hrd.br_kode br_kode on br0.kode=br_kode.kodeid 
					  join MKT.icabang on br0.icabangid=MKT.icabang.icabangid 
					  JOIN hrd.karyawan karyawan on br0.karyawanid=karyawan.karyawanid
					  where (br0.divprodid='$divprodid' or br0.divprodid='OTC') and left(tgl,7)='$periode' and via ='Y'
					  order by tgl,nama,tgltrans"; //echo"$query";
		}//echo"$query";
		$result = mysqli_query($cnit, $query);
		$records = mysqli_num_rows($result);
	
		if ($records) {
			echo "<b>REKAP BR TRANSFER VIA SURABAYA $nama PERIODE $bln_ $tahun</b>";
			echo"<br><br>";	
			$i=0;
			$row = mysqli_fetch_array($result);
			
			while ($i < $records) {	     
				$tgl = $row['tgl'];
				echo '<table border="1" cellspacing="0" cellpadding="1">';
				echo "<br>";
				echo "<br><b>Tanggal : $tgl</b><br>";
				
					$g_total = $g_total1 = 0;
					$gtotal = $gtotal1_ = 0;
				while (($i < $records)  and ($tgl==$row['tgl'])) {	         
					$kode = $row['kode'];
					$nama = $row['nama'];					

					echo "&nbsp;<small>Nama : <b>$nama</b></small>";
				//	echo '<table border="1" cellspacing="0" cellpadding="1">';
					//echo '<tr>';
					echo '<th align="left"><small>No</small></th>';
					if (($kode<>"700-01-04") and ($kode<>"700-02-04") and ($kode<>"700-01-03")) {
						echo '<th align="center"><small>Nama</small></th>';
					} else {
						echo "<th><small>Nama Dokter</small></th>";
					}
					echo '<th align="center"><small>Cabang</small></th>';
					echo '<th align="center"><small>Area</small></th>';
					if (($kode<>"700-01-04") and ($kode<>"700-02-04") and ($kode<>"700-01-03")) {
					} else {
						echo "<th><small>Spesialis</small></th>";
					}
					if (($kode<>"700-01-04") and ($kode<>"700-02-04") and ($kode<>"700-01-03")) {
					} else {
						echo "<th><small>Nama</small></th>";
					}
					echo '<th align="center"><small>Keterangan</th>';
					echo "<th align=center><small>Jumlah IDR</small></th>";
					echo "<th align=center><small>Jumlah USD</small></th>";
					if (($kode<>"700-01-04") and ($kode<>"700-02-04") and ($kode<>"700-01-03")) {
					} else {
						echo "<th><small>CN</small></th>";
					}
					echo '<th align="center"><small>Tanggal Transfer</small></th>';
					echo '<th align="center"><small>Nama Realisasi</small></th>';
					echo '<th align="center"><small>ACC</small></th>';
					echo '<th align="center"><small>Tgl. ACC</small></th>';
					echo '<th align="center"><small>Transfer</small></th>';
					echo '<th align="center"><small>Tgl. Transfer</small></th>';
					echo '</tr>';	
					$total = $total1_ = 0;
					//$gtotal = 0;
					$no = 0;
					$tot_cn = 0;
	
					while (($i < $records) and ($tgl==$row['tgl']) and ($kode==$row['kode'])) {		         
						$brid = $row['brId'];	//echo"$brid";
						$no = $no + 1;			
						$tgltrans = $row['tgltrans'];
						$icabangid = $row['icabangid'];	
						$dokterid = $row['dokterId']; 
						$dokter = $row['dokter'];
						$areaid = $row['areaid'];
						$nm_cab = $row['nm_cab']; 
						$divprodid = $row['divprodid'];
						$aktivitas1 = $row['aktivitas1'];
						$realisasi1 = $row['realisasi1'];
						$aktivitas2 = $row['aktivitas2'];
						$jumlah1 = $row['jumlah']; 
						$tgltrans = $row['tgltrans']; 
						$cn = $row['cn']; 
						$ccyId = $row['ccyId'];
						$karyawanid = $row['karyawanId']; 
						$user1  = $row['user1'];
						$app_acc = $row['app_acc'];
						$acc = $row['acc'];
						$trf = $row['trf']; // report SBY

					 
					 
						$j = "0000" . $i;
						$j = substr($j,-4);
						$var_ = "br" . $j;   //nama variable diawali dengan "cust", contoh cust0001,cust0002,dst.
						$brsby = $brid;  //ganti $j dengan custid dari database
						
						
						$a = "0000" . $i;
						$a = substr($a,-4);
						$vbr_ = "acc" . $a;   //nama variable diawali dengan "cust", contoh cust0001,cust0002,dst.
						$bracc = $brid;  //ganti $j dengan custid dari database
					 

						$nama_dkt = '';
						$query_dkt = "select nama from hrd.dokter where dokterid='$dokterid'"; 
						$result_dkt = mysqli_query($cnit, $query_dkt);
						$num_results_dkt = mysqli_num_rows($result_dkt);
						if ($num_results_dkt) {
							 $row_dkt = mysqli_fetch_array($result_dkt);
							 $nama_dkt = $row_dkt['nama'];
						}
				
						if ($nama_dkt=='') {
							$nama_dkt = $dokter;
						}

						$nama_mr = '';
						$query_mr = "select nama from hrd.karyawan where karyawanid='$karyawanid'";
						$result_mr = mysqli_query($cnit, $query_mr);
						$num_results_mr = mysqli_num_rows($result_mr);
						if ($num_results_mr) {
							 $row_mr = mysqli_fetch_array($result_mr);
							 $nama_mr = $row_mr['nama'];
						}
				
						$nama_ar = '';
						$query_ar = " select areaid, nama from MKT.iarea where areaid='$areaid' and icabangid='$icabangid'"; 
						$result_ar = mysqli_query($cnit, $query_ar);
						$num_results_ar = mysqli_num_rows($result_ar);
						if ($num_results_ar) {
							$row_ar = mysqli_fetch_array($result_ar);
							$nama_ar = $row_ar['nama']; 
						}	
						
						$nama_sp = '';
						$query_sp = " select dokter.spid, spesial.nama
									  from hrd.dokter dokter  
									  join hrd.spesial spesial on dokter.spid=spesial.spid
									  where dokterid='$dokterid'"; 
						$result_sp = mysqli_query($cnit, $query_sp);
						$num_results_sp = mysqli_num_rows($result_sp);
						if ($num_results_sp) {
						 $row_sp = mysqli_fetch_array($result_sp);
						 $nama_sp = $row_sp['nama'];
						}

						echo "<td><small>$no</small></td>";
						if (($kode<>"700-01-04") and ($kode<>"700-02-04") and ($kode<>"700-01-03")) {
							echo "<td><small>$nama_mr</small></td>";
						} else {
							if ($nama_dkt=="") {
								echo "<td>&nbsp;</td>";
							} else {
								echo "<td><small>$nama_dkt</small></td>";
							}
						}
						echo "<td><small>$nm_cab</small></td>";
						if ($nama_ar=="") {
							echo "<td>&nbsp;</td>";
						} else {
							echo "<td><small>$nama_ar/small></td>";
						}
						if (($kode<>"700-01-04") and ($kode<>"700-02-04") and ($kode<>"700-01-03")) {
						} else {
							if ($nama_sp=="") {
								echo "<td>&nbsp;</td>";
							} else {
								echo "<td><small>$nama_sp</small></td>";
							}
						}
						if (($kode<>"700-01-04") and ($kode<>"700-02-04") and ($kode<>"700-01-03")) {
						} else {
							echo "<td><small>$nama_mr</small></td>";
						}
						echo "<td><small>$aktivitas1 $aktivitas2</small></td>";
						
						
						if ($ccyId<>'IDR') {
							echo "<td><small>&nbsp;</small></td>";
							echo "<td align=right><small>$ccyId ".number_format($jumlah1,0)."</small></td>";
							$total1_ = $total1_ + $jumlah1;
							$gtotal1_ = $gtotal1_ + $jumlah1;
							$ccyId_ = $ccyId;
						} else {
							echo "<td align=right><small>".number_format($jumlah1,0)."</small></td>";
							echo "<td><small>&nbsp;</small></td>";
							$total = $total + $jumlah1;
							$gtotal = $gtotal + $jumlah1;
							$ccyid = $row['ccyId'];
						}
						
						
						if (($kode<>"700-01-04") and ($kode<>"700-02-04") and ($kode<>"700-01-03")) {
						} else {
							echo "<td align=right><small>".number_format($cn,0)."</small></td>";
						}
						if ($tgltrans=="0000-00-00") {
							echo "<td>&nbsp;</td>";
						} else {
							echo "<td><small>$tgltrans</small></td>";
						}
						if ($realisasi1=="") {
							echo "<td>&nbsp;</td>";
						} else {
							echo "<td><small>$realisasi1</small></td>";
						}
						$nama_edt = '';
						$query_edt = "select br from hrd.br_kode where kodeid='$kode'"; 
						$result_edt = mysqli_query($cnit, $query_edt);
						$num_results_edt = mysqli_num_rows($result_edt);
						if ($num_results_edt) {
							 $row_edt = mysqli_fetch_array($result_edt);
							 $br = $row_edt['br'];
						}
						
						if ($acc=='Y') {
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
						
						echo "</tr>";
						$tot_cn = $tot_cn + $cn; 
						$total_ = $total_ + $cn;
					    $row = mysqli_fetch_array($result);
						$i++;
					} 
					echo "<tr>"; 
					if (($kode<>"700-01-04") and ($kode<>"700-02-04") and ($kode<>"700-01-03")) {
					} else {
						echo "<td>&nbsp;</td>";
						echo "<td>&nbsp;</td>";
					}
					echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
					echo "<td align=right><b>Total :</td>";
					echo "<td align=right><b>".number_format($total,0)."</b></td>";
					if ($total1_ == 0) {
						echo "<td align=right><b>".number_format($total1_,0)."</b></td>";
					} else {
						echo "<td align=right><b>$ccyId_ ".number_format($total1_,0)."</b></td>";
					}
					
					echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";

					//total CN
					if (($kode<>"700-01-04") and ($kode<>"700-02-04") and ($kode<>"700-01-03")) {
					} else {
						echo "<td>&nbsp;</td>";
					}
					echo "</tr>";		
				//	echo "</table>";
				} 
				$g_total = $g_total + $gtotal; 
				$g_total1 = $g_total1 + $gtotal1_; 
				//echo "<b>Total $tgl :</b>";
				if ($g_total == 0) {
				} else {
				//	echo "<b>$ccyid ".number_format($g_total,0)."</b>&nbsp;&nbsp;";
					$gdtotal = $gdtotal + $g_total; 
				}
				
				if ($g_total1 == 0) {
				} else {
				//	echo "<b>$ccyId_ ".number_format($g_total1,0)."</b>";
					$gdtotal_1 = $gdtotal_1 + $g_total1; 
				}
				echo"<br><br>";
				
			}
			$gdtotal1 = $gdtotal + $total1;
			$gdtotal2 = $gdtotal_1 + $total1_;
			//echo "<b>Grand Total :</b>";
			if ($gdtotal1 == 0) {
			} else {
				//echo "<b>$ccyid ".number_format($gdtotal1,0)."</b>&nbsp;&nbsp;";
			}
			if ($gdtotal2 == 0) {
			} else {
			//	echo "<b>$ccyId_ ".number_format($gdtotal2,0)."</b>";
			}
		
			echo "<tr>";
			echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
			echo "<td align=right><b>Grand Total :</td>";
			echo "<td align=right><b>".number_format($gdtotal1,0)."</b></td>";
			if ($gdtotal2 == 0 ) {
				echo "<td align=right><b>0</b></td>";
			} else {
				echo "<td align=right><b>$ccyId_ ".number_format($gdtotal2,0)."</b></td>";
			}
			echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>"; // report SBY
			echo "</tr>";
			echo "</table>";
			
			
	
	  }	 else {
		echo "<b>DATA TIDAK DITEMUKAN</b><br>";
		//echo '<br><input type=button value="Back" onclick="go_back()">';
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

	  
   } 
   
   echo "<br><br><input type=submit name=cmdSave id=cmdSave value=Save>";
	echo "&nbsp;&nbsp;<input type=button id=cmdBack name=cmdBack value='Back' onclick='goto2(\"rpbrsby3\",\"rpbrsby2.php\")'>";
	echo "<input type=hidden name=records value=$records />";
	echo "<input type=hidden name=records1 value=$records1 />";
	echo "<input type=hidden name=divprodid value=$divprodid />";  
	echo "<input type=hidden name=periode value=$periode />";  
   
   
 ?>
 
</form>
</body>
</html>


