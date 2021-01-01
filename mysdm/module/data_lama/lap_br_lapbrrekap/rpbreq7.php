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
    <title>Rekap Budget Request</title>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<script src="brsby.js">
</script>
<body>
<form id="rpbreq7" action="module/data_lama/lap_br_lapbrrekap/brsby0.php" method=post>
<?php
	include("config/common.php");
	//include("config/common3.php");
	include "config/koneksimysqli.php";

	if (empty($_SESSION['IDCARD'])) {
	  echo 'not authorized';
	  exit;
	} else {
	  
		$srid = $_SESSION['USERID'];
		$srnama = $_SESSION['NAMALENGKAP'];
		$sr_id = substr('0000000000'.$srid,-10);
		$userid = $_SESSION['USERID'];
		//$bulan_ = $_POST['bulan'];
		//$tahun_ = $_POST['tahun'];
                $tgl01=$_POST['bulan'];
                $periode1= date("Y-m", strtotime($tgl01));
                $tahun= date("Y", strtotime($tgl01));
                $tahun_= date("Y", strtotime($tgl01));
                $bulan= date("m", strtotime($tgl01));
                $bulan_= date("m", strtotime($tgl01));
                $bln_= date("F", strtotime($tgl01));
        
		//$namadokter = $_POST['namadokter'];
		$periode = $tahun_.'-'.$bulan_; 
		if ($periode == "-") {
                    $periode = $_POST['periode'];
		} 
		
		
		$bln_nm = nama_bulan($bulan);
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
		$tahun_1 = $tahun - 1;
		$tahun_2 = $tahun + 1;
                
                $records1="";
                
                
		echo "<b>REKAP BR $nama PERIODE $bln_nm $tahun</b>";
		//echo "<b>REKAP BR $divprodid PERIODE $bln_nm $tahun</b>";
		if ($_SESSION['JABATANID']=='05') { //rsm       
			if ($divprodid=="PIGEO") {
				$query = "select br0.*, br_kode.br
						  from hrd.br0 br0 
						  hrd.br_kode br_kode  on br0.kode=br_kode.kodeid
						  where icabangid in (select icabangid from hrd.rsm_auth where karyawanId='$sr_id') and
						  (br0.divprodid='$divprodid' or br0.divprodid='OTC') and ((left(tgltrans,7)='$periode' and tglunrtr='0000-00-00') 
						  or LEFT(tglunrtr,7)='$periode') and IFNULL(retur,'') <> 'Y' and IFNULL(via,'')<>'Y' and br='Y' order by tgltrans,noslip"; //echo"$query";
			} else {
				$query = "select br0.*, br_kode.br 
						  from hrd.br0 br0  
						  hrd.br_kode br_kode  on br0.kode=br_kode.kodeid
						  where icabangid in (select icabangid from hrd.rsm_auth where karyawanId='$sr_id') and
						  br0.divprodid='$divprodid' and ((left(tgltrans,7)='$periode' and tglunrtr='0000-00-00') or LEFT(tglunrtr,7)='$periode') 
						  and IFNULL(retur,'') <> 'Y' and IFNULL(via,'')<>'Y' and br='Y'
						  order by tgltrans,noslip"; //echo"$query";
			}
	   } 
		
	   if (($_SESSION['JABATANID']=='12') or ($_SESSION['JABATANID']=='24') or ($_SESSION['JABATANID']=='13')){ //adm,audit
               
			if ($divprodid=="PIGEO" or $divprodid=="PEACO" or $divprodid=="HO") {
				$query = "select * from hrd.br0 br0  where (divprodid='$divprodid' or divprodid='OTC')
						  and ((left(tgltrans,7)='$periode' and tglunrtr='0000-00-00') or LEFT(tglunrtr,7)='$periode')
						  and IFNULL(retur,'') <> 'Y' and IFNULL(via,'')<>'Y' order by tgltrans,noslip"; //echo"$query";// and tgl <= '$akhir'";
			} else {
				$query1 = "select * from hrd.klaim klaim  where left(tgltrans,7)='$periode' order by tgltrans";  //echo"$query1";
				$result1 = mysqli_query($cnmy, $query1); //echo"$result1";
				$records1 = mysqli_num_rows($result1);	
				$row1 = mysqli_fetch_array($result1);
				if ($records1) {
					$i = 1;
					$gtotal1 = 0;
					while ($i <= $records1) {
						$bln_ = $row1['tgltrans'];
						$bulan_ = $row1['tgltrans']; //echo"$bulan_";
						echo '<table border="1" cellspacing="0" cellpadding="1">';
						echo "<br>";
						$tt_ = $row1['tgltrans'];
						echo "<b>Tanggal Transfer : $tt_</b>";
						echo '<tr>';
						echo '<th align="center"><small>No.</small></th>';
						echo '<th align="center"><small>Tanggal Transfer</small></th>';
						echo '<th align="center"><small>No. Slip Transfer</small></th>';
						echo '<th align="center"><small>Realisasi</small></th>';
						echo '<th align="center"><small>Nama</small></th>';
						echo '<th align="center"><small>Keterangan</th>';
						echo '<th align="center"><small>Jumlah</small></th>';
						echo '<th align="center"><small>Tgl. Report SBY</small></th>';
						echo '<th align="center"><small>Report SBY</small></th>';
						echo '</tr>';
						$total1 = 0;
						$no = 0;
					    while ( ($i<=$records1) and ($bulan_ == $row1['tgltrans']) ) {	 	    
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
							$sby = $row1['sby'];
							$tglrpsby = $row1['tglrpsby'];
							$total1 = $total1 + $jumlah;
							
							$j = "0000" . $i;
							$j = substr($j,-4);
						    $var_ = "kl" . $j;   //nama variable diawali dengan "cust", contoh cust0001,cust0002,dst.
							$brsby = $klaimid;  //ganti $j dengan custid dari database
									
							$nama_mr = '';
						    $query_mr = "select nama from hrd.karyawan karyawan  where karyawanId='$karyawanId'";// echo"$query_mr";
						    $result_mr = mysqli_query($cnmy, $query_mr);
						    $num_results_mr = mysqli_num_rows($result_mr);
							if ($num_results_mr) {
							     $row_mr = mysqli_fetch_array($result_mr);
								 $nama_mr = $row_mr['nama'];
							}
					
							echo "<td><small>$no</small></td>";
							echo "<td><small>$tgltrans</small></td>";
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
							if ($tglrpsby=="0000-00-00"){
								echo "<td><small>&nbsp;<small></td>";
							} else {
								echo "<td><small>$tglrpsby</small></td>";
							}

							if ($sby=='Y') {
								$checked_ = "checked";
								echo "<td><input type='checkbox' name='$var_' value='$brsby' $checked_  $disabled_>";
							} else {
								echo '<td><input type="checkbox" name="'.$var_.'" value="'.$brsby.'"></td>';
							}
							echo "</tr>";
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
				$query = "select * from hrd.br0 br0  where divprodid='$divprodid' 
						  and ((left(tgltrans,7)='$periode' and tglunrtr='0000-00-00') or LEFT(tglunrtr,7)='$periode') 
						  and retur <> 'Y' and via<>'Y' order by nosliptu,tgltrans,noslip";//echo"$query";// and tgl <= '$akhir'";
			}
	   } 
		
		$result = mysqli_query($cnmy, $query);
		$records = mysqli_num_rows($result);	
		$row = mysqli_fetch_array($result);
		if ($records) {
			$i = 1;
			$gtotal = $gtotal_ = 0;
			while ($i <= $records) {
				$bln_ = $row['tgltrans'];
				$tglunrtr = $row['tglunrtr']; 
				if ($tglunrtr <>'0000-00-00') {
					$row['tgltrans'] = $row['tglunrtr']; 
				}
				$bulan_ = $row['tgltrans']; //echo"$bulan_";
				echo '<table border="1" cellspacing="0" cellpadding="1">';
				echo "<br>";
				$tt_ = $row['tgltrans'];
				echo "<b>Tanggal Transfer : $tt_</b>";
				echo '<tr>';
				echo '<th align="left"><small>No</small></th>';
				echo '<th align="center"><small>No Slip</small></th>';
				echo '<th align="center"><small>Nama</small></th>';
				echo '<th align="center"><small>Nama Dokter</small></th>';
				echo '<th align="center">Keterangan</th>';
				echo '<th align="center"><small>Realisasi</small></th>';
				echo '<th align="center"><small>Jumlah IDR</small></th>';
				echo '<th align="center"><small>Jumlah USD</small></th>';
				echo '<th align="center"><small>Tgl. Report SBY</small></th>';
				echo '<th align="center"><small>Report SBY</small></th>';
				echo '</tr>';
				$total = $total_ = 0;
				$no = 0;
			    while ( ($i<=$records) and ($bulan_ == $row['tgltrans']) ) {
					$brid = $row['brId'];	
					$no = $no + 1;			
					$tgltrans = $row['tgltrans'];
					$tglunrtr = $row['tglunrtr'];
					$noslip = $row['noslip']; //echo"$noslip";
					$nosliptu = $row['nosliptu'];					
					$dokterid = $row['dokterId']; 
					$dokter = $row['dokter'];// echo"$dokter";
					$realisasi1 = $row['realisasi1'];
					$aktivitas1 = $row['aktivitas1'];
					$aktivitas2 = $row['aktivitas2'];
					$jumlah = $row['jumlah']; 
					$tglrpsby = $row['tglrpsby']; 
					$jumlah1 = $row['jumlah1'];
					$karyawanId = $row['karyawanId']; 
					$ccyId = $row['ccyId'];// echo"$ccyId";
					$sby = $row['sby']; // report SBY
					$batal = $row['batal']; // batal
				
					$j = "0000" . $i;
					$j = substr($j,-4);
				    $var_ = "br" . $j;   //nama variable diawali dengan "cust", contoh cust0001,cust0002,dst.
					$brsby = $brid;  //ganti $j dengan custid dari database
				
					$nama_dkt = '';
					$query_dkt = "select nama from hrd.dokter dokter  where dokterid='$dokterid'"; //echo"$query_dkt";
					$result_dkt = mysqli_query($cnmy, $query_dkt);
					$num_results_dkt = mysqli_num_rows($result_dkt);
					if ($num_results_dkt) {
						 $row_dkt = mysqli_fetch_array($result_dkt);
						 $nama_dkt = $row_dkt['nama'];
					}

					$nama_mr = '';
					$query_mr = "select nama from hrd.karyawan karyawan  where karyawanId='$karyawanId'";
					$result_mr = mysqli_query($cnmy, $query_mr);
					$num_results_mr = mysqli_num_rows($result_mr);
					if ($num_results_mr) {
						 $row_mr = mysqli_fetch_array($result_mr);
						 $nama_mr = $row_mr['nama'];
					}
				
					$nama_slp = '';
					$query_slp = "select noslip,jumlah,jumlah1,tgltrans from hrd.br0 br0 where noslip1='$noslip' and noslip1<>''"; //echo"$query_slp";
					$result_slp = mysqli_query($cnmy, $query_slp);
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
					if ($tglrpsby=="0000-00-00"){
						echo "<td><small>&nbsp;<small></td>";
					} else {
						echo "<td><small>$tglrpsby</small></td>";
					}
					
					
					if ($sby=='Y') {
						$checked_ = "checked";
						echo "<td><input type='checkbox' name='$var_' value='$brsby' $checked_  $disabled_>";
					} else {
						echo '<td><input type="checkbox" name="'.$var_.'" value="'.$brsby.'"></td>';
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
							$query_cc = "select nilai from hrd.ccy ccy  where ccyId='$ccyId'";
							$result_cc = mysqli_query($cnmy, $query_cc);
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
						echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
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
			echo "<tr>";
			echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
			echo "<td align=right><b>Grand Total :</td>";
			echo "<td align=right><b>".number_format($gtotal,0)."</b></td>";
			if ($gtotal_ == 0 ) {
				echo "<td align=right><b>".number_format($gtotal_,0)."</b></td>";
			} else {
				echo "<td align=right><b>$ccyId_ ".number_format($gtotal_,0)."</b></td>";
			}
			echo "<td>&nbsp;</td><td>&nbsp;</td>"; // report SBY
			echo "</tr>";
			echo "</table>";
		} else {
			echo "<br /><b>Data tidak ditemukan!!!</b><br/><br/>";		
		}	
		
		
		if ($_SESSION['JABATANID']=='12' or $_SESSION['JABATANID']=='13'){ //adm     
			echo '<br><tr>';
			echo '<td align=right>Tanggal Report SBY :</td>';
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

    }  // if (empty($_SESSION['srid'])) 
    echo "<br><br>";
	if ($_SESSION['JABATANID']=='12' or $_SESSION['JABATANID']=='13'){ //adm     
		echo "<input type=submit name=cmdSave id=cmdSave value=Save>";
	}
	
	echo "<input type=hidden name=records value=$records />";
	echo "<input type=hidden name=records1 value=$records1 />";
	echo "<input type=hidden name=divprodid value=$divprodid />";  
	echo "<input type=hidden name=periode value=$periode />";  


?>
</form>
</body>
</html>


