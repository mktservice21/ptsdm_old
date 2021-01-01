<html>
<head>
    <title>Lihat/Edit/Delete Data BR OTC</title>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<body>
<form id="brotc11" action="brotc10.php" method=post>
<?php
	include("config/common.php");
	//include("common3.php");
	include("config/koneksimysqli_it.php");
	session_start();

	if (empty($_SESSION['IDCARD'])) {
	  echo 'not authorized';
	  exit;
	} else {
            
		$tgl01 = $_POST['bulan1'];
                $periode1= date("Y-m-d", strtotime($tgl01));
                $tanggal1= date("d", strtotime($tgl01));
                $bulan1= date("m", strtotime($tgl01));
                $nm_bln1 = nama_bulan($bulan1); //echo "$nm_bln1";
                $tahun1= date("Y", strtotime($tgl01));
            
		$tgl02 = $_POST['bulan2'];
                $periode2= date("Y-m-d", strtotime($tgl02));
                $tanggal2= date("d", strtotime($tgl02));
                $bulan2= date("m", strtotime($tgl02));
                $nm_bln2 = nama_bulan($bulan1); //echo "$nm_bln2";
                $tahun2= date("Y", strtotime($tgl02));

		if (isset($_POST['per1'])) {
			$periode1 = $_POST['per1'];
			$periode2 = $_POST['per2'];
		}
		
		$icabangid_o = $_POST['icabangid_o']; 
		$bralid = $_POST['bralid'];
                
		$nama_al = "";
		$where_  = "";
		$usulan  = "";
		$kodeid_o  = "";
		$total1   = 0;
		$selisih1   = 0;
                
		$query_al = "select bralid,nama from hrd.bral_otc where bralid='$bralid'";
		$result_al = mysqli_query($cnit, $query_al);
		$num_results_al = mysqli_num_rows($result_al);
		if ($num_results_al) {
			 $row_al = mysqli_fetch_array($result_al);
			 $nama_al = $row_al['nama'];
		}
		
		
		echo "<b>Data BR OTC Periode : $periode1 s/d $periode2</b><br><br>";
		
		if ($icabangid_o=='*') {
			if ($bralid=='blank') {
			} else {
				$where_ = " and bralid='$bralid'";
			}
			
			
		} else {
			if ($icabangid_o=='HO' or $icabangid_o=='MD') {
				$nm_cab = $icabangid_o;
			} else {
				$query = "select nama,icabangid_o from MKT.icabang_o where icabangid_o='$icabangid_o'"; 
				$result = mysqli_query($cnit, $query);
				$row = mysqli_fetch_array($result);
				$num_results = mysqli_num_rows($result);
				$nm_cab = $row['nama'];
			}
			
			echo "Cabang : $nm_cab<br>";
			if ($nama_al=='') {
			} else {
				echo "Alokasi : $nama_al";
			}
			echo "<br><br>";
			
			
			if ($bralid=='blank') {
				$where_ = $where_." and icabangid_o='$icabangid_o'";
			} else {
				$where_ = $where_." and icabangid_o='$icabangid_o' and bralid='$bralid'";
			}

		}
	 
		$query = "select * from hrd.br_otc where ('$periode1' <= tgltrans and tgltrans <= '$periode2') ".$where_." order by noslip"; //echo"$query";
		$result = mysqli_query($cnit, $query);
		$num_results = mysqli_num_rows($result);	  	  	  	  
                //echo $query;exit;
		if ($num_results) {
			$i = 0;
			$total = 0;
			echo '<table border="1" cellspacing="0" cellpadding="1">';
			echo "<tr>";
			echo "<th align=center><small>No</small></th>";
			echo "<th align=center><small>Tgl BRr</small></th>";
			echo "<th align=center><small>Tgl. Transfer</small></th>";
			echo "<th align=center><small>No. Slip</small></th>";
			echo "<th align=center><small>Alokasi Budget</small></th>";
			if ($icabangid_o=='blank') {
			} else {
				echo "<th align=center><small>Cabang</small></th>";
			}
			echo "<th align=center><small>Keterangan Tempat</small></th>";	     
			echo "<th align=center><small>Keterangan</small></th>";	     
			echo "<th align=center><small>Usulan BR</small></th>";
			echo "<th align=center><small>Realisasi</small></th>";
			echo "<th align=center><small>Tgl Realisasi</small></th>";
			echo "<th align=center><small>Jumlah Realisasi</small></th>";
			echo "<th align=center><small>Selisih</small></th>";
			echo "<th align=center><small>Tgl Report SBY</small></th>";
			echo "<th align=center><small>Jenis Report SBY</small></th>";
			echo "<th align=center><small>Edit Jumlah Realisasi</small></th>";		
			echo "<th align=center><small>Edit BR</small></th>";			
			echo "<th align=center><small>Delete BR</small></th>";	
                        $no=0;
			while ($i < $num_results) {		 	    
				$row = mysqli_fetch_array($result);
				$brotcid = $row['brOtcId'];		
				$no = $no + 1;
				$icabangid_o = $row['icabangid_o'];			
				$tglbr = $row['tglbr'];
				$nobr = $row['nobr'];
				$tgltrans = $row['tgltrans'];
				$noslip = $row['noslip'];
				$kodeid = $row['kodeid'];
				$keterangan1 = $row['keterangan1'];
				$keterangan2 = $row['keterangan2'];
				$jumlah = $row['jumlah']; //echo"$jumlah";
				//$usulan = $row['usulan'];
				$tglreal = $row['tglreal'];
				$real1 = $row['real1'];
				$realisasi = $row['realisasi'];
				$tglrpsby = $row['tglrpsby'];
				$jenis = $row['jenis'];
				
				$nama_kd = '';
				$query_kd = "select nama from hrd.brkd_otc where kodeid='$kodeid'"; 
				$result_kd = mysqli_query($cnit, $query_kd);
				$num_results_kd = mysqli_num_rows($result_kd);
				if ($num_results_kd) {
					 $row_kd = mysqli_fetch_array($result_kd);
					 $nama_kd = $row_kd['nama'];
				}
				
				echo "<tr>";
				echo "<td align=center><small>$no</small></td>";
				echo "<td align=right><small>$tglbr</small></td>";
				echo "<td><small>$tgltrans</small></td>";
				if ($noslip=="") {
					echo "<td>&nbsp;</td>";
				} else {
					echo "<td><small>$noslip</small></td>";
				}		
				
				echo "<td><small>$nama_kd</small></td>";
				
				$query_cab = "select nama,icabangid_o from MKT.icabang_o where icabangid_o='$icabangid_o'"; 
				$result_cab = mysqli_query($cnit, $query_cab);
				$row_cab = mysqli_fetch_array($result_cab);
				$num_results_cab = mysqli_num_rows($result_cab);
				$nama_cab = $row_cab['nama'];
				
				if ($nama_cab=="") {
					if ($icabangid_o=='MD') {
						echo "<td><small>MD</small></td>";
					} else {
						if ($icabangid_o=='HO') {
							echo "<td><small>HO</small></td>";
						} else {
							echo "<td>&nbsp;</td>";
						}
					}
				} else {
					echo "<td><small>$nama_cab</small></td>";
				}		
				
				
			//	echo "<td><small>$nama_cab</small></td>";
				
				if ($keterangan1=="") {
					echo "<td>&nbsp;</td>";
				} else {
					echo "<td><small>$keterangan1</small></td>";	
				}		
				if ($keterangan2=="") {
					echo "<td>&nbsp;</td>";
				} else {
					echo "<td><small>$keterangan2</small></td>";	
				}	
				echo "<td align=right><small>".number_format($jumlah,0)."</small></td>";	
				echo "<td align=right><small>$real1</small></td>";
				if ($tglreal=="0000-00-00") {
					echo "<td>&nbsp;</td>";
				} else {
					echo "<td align=right><small>$tglreal</small></td>";
				}		
				if ($realisasi=="") {
					echo "<td>&nbsp;</td>";
				} else {
					echo "<td align=right><small>".number_format($realisasi,0)."</small></td>";	
				}
	
				$selisih = $jumlah - $realisasi;
				if ($selisih==0) {
					echo "<td><small>&nbsp;</small></td>";
				} else {
					echo "<td align=right><small>".number_format($selisih,0)."</small></td>";
					$selisih1 = $selisih1 + $selisih; 
				}
				$total = $total + $jumlah;
				$total1 = $total1 + $realisasi;
				
				if ($tglrpsby=="0000-00-00") {
					echo "<td>&nbsp;</td>";
				} else {
					echo "<td align=right><small>$tglrpsby</small></td>";
				}	
				
				if ($jenis=="A") {
					$jns = 'Advance';
				} else {
					if ($jenis=='K') {
						$jns = 'Klaim';
					} else {
						$jns = 'Sudah minta uang muka';
					}
				}	
				
				if ($jenis=="") {
					echo "<td>&nbsp;</td>";
				} else {
					echo "<td align=right><small>$jns</small></td>";
				}
				echo "<td><a href='module/data_lama/otc_br_viewtrans/brotc40.php?brotcid=$brotcid&jumlah=$jumlah&tglreal=$tglreal&mode=R'>Realisasi</a></td>";
				echo "<td><a href='module/data_lama/otc_br_viewtrans/brotc00.php?brotcid=$brotcid&per1=$periode1&per2=$periode2&entrymode=E&mode=R'>View/Edit</a></td>";;
				echo "<td><a href='module/data_lama/otc_br_viewtrans/brotc00.php?brotcid=$brotcid&periode=$tgltrans&entrymode=D&mode=R'>Delete</a></td>";
			  	echo "</tr>";
				$i ++;
		    } 
			echo "<tr>";
			echo "<td colspan=8 align=right><b>Total :</td>";
			echo "<td align=right><b>".number_format($total,0)."</b></td>";
			echo "<td>&nbsp;</td><td>&nbsp;</td>";
			echo "<td align=right><b>".number_format($total1,0)."</b></td>";
			echo "<td align=right><b>".number_format($selisih1,0)."</b></td>";
			echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
			echo "</tr>";
		    echo "</table>";
	    } else {
			echo "<br /><b>Data tidak ditemukan!!!</b><br/><br/>";		
	    }	
    }  // if (empty($_SESSION['srid'])) 
		echo "<br><input type=hidden id=cmdBack name=cmdBack value='Back'>";
		echo "<input type=hidden id='icabangid_o' name='icabangid_o' value='$icabangid_o'>";
		echo "<input type=hidden id='kodeid' name='kodeid' value='$kodeid_o'>";
		echo "<input type=hidden id='periode1' name='periode1' value='$periode1'>";
		echo "<input type=hidden id='periode2' name='periode2' value='$periode2'>";
	if (empty($_SESSION['srid'])) {
	} else {
	  do_show_menu($_SESSION['jabatanid'],'N');
	}
?>
</form>
</body>
</html>


