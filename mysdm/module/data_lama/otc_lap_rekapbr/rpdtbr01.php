<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REKAP DATA BR.xls");
    }
?>
<html>
<head>
  <title>REKAP DATA BR OTC</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<form action="rpdtbr00.php" method=post>
<body>
<?php
	include("config/common.php");
	//include("common3.php");
	include("config/koneksimysqli.php");
	$cnit=$cnmy;
        $namacab="";
	$srid = $_SESSION['USERID'];
	$srnama = $_SESSION['NAMALENGKAP'];
	$sr_id = substr('0000000000'.$srid,-10);
	$userid = $_SESSION['IDCARD'];	
        
        $periode1 = "0000-00-00";
        if (!empty($_POST['bulan1'])) {
            $tgl01 = $_POST['bulan1'];
            $tanggal1 = date("d", strtotime($tgl01));
            $bulan1 = date("m", strtotime($tgl01));
            $nm_bln1 = nama_bulan($bulan1); //echo "$nm_bln1";
            $tahun1 = date("Y", strtotime($tgl01));
            $periode1 = $tahun1.'-'.$bulan1.'-'.$tanggal1; 
        }
        
        $periode2 = "0000-00-00";
        if (!empty($_POST['bulan2'])) {
            $tgl02 = $_POST['bulan2'];
            $tanggal2 = date("d", strtotime($tgl02));
            $bulan2 = date("m", strtotime($tgl02));
            $nm_bln2 = nama_bulan($bulan2); //echo "$nm_bln1";
            $tahun2 = date("Y", strtotime($tgl02));
            $periode2 = $tahun2.'-'.$bulan2.'-'.$tanggal2; 
        }
        
        $periode3 = "0000-00-00";
        if (!empty($_POST['bulan3'])) {
            $tgl03 = $_POST['bulan3'];
            $tanggal3 = date("d", strtotime($tgl03));
            $bulan3 = date("m", strtotime($tgl03));
            $nm_bln3 = nama_bulan($bulan3); //echo "$nm_bln1";
            $tahun3 = date("Y", strtotime($tgl03));
            $periode3 = $tahun3.'-'.$bulan3.'-'.$tanggal3; 
        }
        
        $periode4 = "0000-00-00";
        if (!empty($_POST['bulan4'])) {
            $tgl04 = $_POST['bulan4'];
            $tanggal4 = date("d", strtotime($tgl04));
            $bulan4 = date("m", strtotime($tgl04));
            $nm_bln4 = nama_bulan($bulan4); //echo "$nm_bln1";
            $tahun4 = date("Y", strtotime($tgl04));
            $periode4 = $tahun4.'-'.$bulan4.'-'.$tanggal4; 
        }
        
	
	$icabangid_o = $_POST['icabangid_o'];
	$posting = $_POST['posting'];
	$subposting = $_POST['subposting'];
        
	$lamp = $_POST['lamp'];
	$ca = $_POST['ca'];
	$via = $_POST['via'];
	$slip = $_POST['slip'];
	$order = $_POST['order'];
	
        
        
	$nmcab="";
	if ($icabangid_o=='MD') {
		$nmcab = 'MD';	
	} else {
		if ($icabangid_o=='HO') {
			$nmcab = 'HO';	
		} else {
			$query = "select nama from MKT.icabang_o where icabangid_o='$icabangid_o'";
			$result = mysqli_query($cnit, $query);
			$num_results = mysqli_num_rows($result);
			$row = mysqli_fetch_array($result);	 
			if ($num_results) {
				$nmcab = $row['nama'];
			}
		}
	}
        
        
        
        $row_kd = "";
        $nama_kd = "";
        $where_="";
        $order_="";
        
        //echo $icabangid_o;exit;
        
	$query_kd = "select nmsubpost nama from hrd.brkd_otc where subpost='$posting'"; 
	$result_kd = mysqli_query($cnit, $query_kd);
	$num_results_kd = mysqli_num_rows($result_kd);
	if ($num_results_kd) {
		 $row_kd = mysqli_fetch_array($result_kd);
		 $nama_kd = $row_kd['nama'];
	}
        
	echo "<b><big>REKAP BUDGET REQUEST OTC</big><br><br>";
	
	if ($periode1=='0000-00-00') {
	} else {
		echo "Periode BR : $periode1 s/d $periode2<br>";
	}

	if ($periode3=='0000-00-00') {
	} else {
		echo "Periode Transfer : $periode3 s/d $periode4<br>";
	}
	
	if ($periode1=="0000-00-00" or $periode2=="0000-00-00") {
	} else {
		$where_ = "('$periode1' <= tglbr and tglbr <= '$periode2')";
	}
	
	if ($periode3=="0000-00-00" or $periode4=="0000-00-00") {
	} else {
		if ($periode1=="0000-00-00" or $periode2=="0000-00-00") {
			$where_ = "('$periode3' <= tgltrans and tgltrans <= '$periode4')";
		} else {
			$where_ = $where_." and ('$periode3' <= tgltrans and tgltrans <= '$periode4')";
		}
	}
        
        
	if ($icabangid_o=="*") {
	} else {
		 echo "Cabang : $nmcab<br>";
		 $where_ = $where_." and icabangid_o='$icabangid_o'";
	}
        
	if ($posting=="*" OR empty($posting)) {
	} else {
		echo "Posting : $nama_kd<br>";
		$where_ = $where_." and subpost='$posting'";
	}

	if ($subposting=="*" OR empty($subposting)) {
	} else {
		echo "Sub-Posting : $nama_kd<br>";
		$where_ = $where_." and kodeid='$subposting'";
	}

	if ($lamp=="*") {
	} else {
		if ($lamp=='Y') {
			echo "Ada Lampiran<br>";
			$where_ = $where_." and lampiran='Y'";
		} else {
			$where_ = $where_." and lampiran='N'";
		}
	}

	if ($ca=="*") {
	} else {
		if ($ca=='Y') {
			echo "Cash Advance<br>";
			$where_ = $where_." and ca='Y'";
		} else {
			$where_ = $where_." and ca='N'";
		}
	}
	
	if ($via=="*") {
	} else {
		if ($via=='Y') {
			echo "Via Surabaya<br>";
			$where_ = $where_." and via='Y'";
		} else {
			$where_ = $where_." and via='N'";
		}
	}
	
	if ($slip=="*") {
	} else {
		if ($slip=='Y') {
			$where_ = $where_." and noslip<>''";
		} else {
			$where_ = $where_." and noslip=''";
		}
	}
	
	if ($order=='C') {
		$order_ = "icabangid_o";
	} else {
		if ($order=='P') {
			$order_ = "kodeid";
		} else {
			if ($order=='A') {
				$order_ = "bralid";
			} else {
				if ($order=='B') {
					$order_ = "tglbr";
				} else {
					if ($order=='T') {
						$order_ = "tgltrans";
					} else {						
					}
				}
			}
		}
	}
	
	echo "<br>";
	
	$header_ = add_space('Cabang',30);
	$header1_ = add_space('Tgl BR',20);
	$header2_ = add_space('Tgl Transfer',20);
	$header3_ = add_space('Posting',30);
	$header5_ = add_space('Keterangan',150);
	$header6_ = add_space('Nama Realisasi',40);
	$header7_ = add_space('Usulan BR',30);
	$header8_ = add_space('Jumlah Realisasi',30);
	$header9_ = add_space('Selisih',30);
	$header10_ = add_space('No Slip',15);
	$header11_ = add_space('Sub-Posting',30);
	
	echo '<table border="1" cellspacing="0" cellpadding="1">';
	echo "<tr>\n";
	echo '<th align="left"><small>'.$header10_."</small></th>";
	echo '<th align="left"><small>'.$header_."</small></th>";
	echo '<th align="left"><small>'.$header1_."</small></th>";
	echo '<th align="left"><small>'.$header2_."</small></th>";
	echo '<th align="left"><small>'.$header3_."</small></th>";
	echo '<th align="left"><small>'.$header11_."</small></th>";
	echo '<th colspan=2 align="left"><small>'.$header5_."</small></th>";
	echo '<th align="left"><small>'.$header6_."</small></th>";
	echo '<th align="left"><small>'.$header7_."</small></th>";
	echo '<th align="left"><small>'.$header8_."</small></th>";
	echo '<th align="left"><small>'.$header9_."</small></th>";
	echo "</tr>";
	

	$query = "select * from hrd.br_otc where ".$where_." AND IFNULL(batal,'')<>'Y' order by $order_"; 
	 //echo"$query";exit;

	$result = mysqli_query($cnit, $query);
	$num_results = mysqli_num_rows($result);
	$tot_jumlah = 0;
	$tot_jumlah1 = 0;
	$tot_selisih = 0;
	for ($i=0; $i < $num_results; $i++) {
		$row = mysqli_fetch_array($result);
		$icabangid_o = $row['icabangid_o'];
		$kodeid = $row['kodeid'];
		$subpost = $row['subpost'];
		$bralid = $row['bralid'];
		$keterangan1 = $row['keterangan1'];
		$keterangan2 = $row['keterangan2'];
		$jumlah = $row['jumlah'];
		$jumlah1 = $row['realisasi'];
		$real1 = $row['real1'];
		$tglbr = $row['tglbr'];
		$tgltrans = $row['tgltrans'];
		$noslip = $row['noslip'];
		$tot_jumlah = $tot_jumlah + $jumlah;
		$tot_jumlah1 = $tot_jumlah1 + $jumlah1;
		$selisih = $jumlah - $jumlah1;
		$tot_selisih = $tot_selisih + $selisih;

		if ($icabangid_o=='MD') {
			$namacab = 'MD';	
		}elseif ($icabangid_o=='PM_CARMED') {
			$namacab = 'PM - CARMED';
		}elseif ($icabangid_o=='PM_LANORE') {
			$namacab = 'PM - LANORE';
		}elseif ($icabangid_o=='PM_MELANOX') {
			$namacab = 'PM - MELANOX';
		}elseif ($icabangid_o=='PM_ACNEMED') {
			$namacab = 'PM - ACNE MED';
		}elseif ($icabangid_o=='PM_PARASOL') {
			$namacab = 'PM - PARASOL';
		}elseif ($icabangid_o=='JKT_MT') {
			$namacab = 'JAKARTA - MT';
		}elseif ($icabangid_o=='JKT_RETAIL') {
			$namacab = 'JAKARTA - RETAIL';
		}else{
			if ($icabangid_o=='HO') {
				$namacab = 'HO';	
			} else {
				$query_cab = "select nama from MKT.icabang_o where icabangid_o='$icabangid_o'";  
				// echo"$query_cab<br>";
				$result_cab = mysqli_query($cnit, $query_cab);
				$num_results_cab = mysqli_num_rows($result_cab);
				$row_cab = mysqli_fetch_array($result_cab);	 
				if ($num_results_cab) {
					$namacab = $row_cab['nama'];
				}
                                
                                if (empty($nama_cab)) {
                                    $query_cab = "select initial nama from dbmaster.cabang_otc where cabangid_ho='$icabangid_o'";
                                    $result_cab = mysqli_query($cnit, $query_cab);
                                    $num_results_cab = mysqli_num_rows($result_cab);
                                    if ($num_results_cab) {
                                             $row_cab = mysqli_fetch_array($result_cab);
                                             $nama_cab = $row_cab['nama'];
                                    }
                                }
                                
			}
		}
		
		$query_al = "select bralid,nama from hrd.bral_otc where bralid='$bralid'"; //echo"$query_al<br>";
		$result_al = mysqli_query($cnit, $query_al);
		$num_results_al = mysqli_num_rows($result_al);
		if ($num_results_al) {
			 $row_al = mysqli_fetch_array($result_al);
			 $nama_al = $row_al['nama'];
		}
		$nama_kd="";
		$query_kd = "select nama from hrd.brkd_otc where kodeid='$kodeid'";
		// echo $query_kd;
		$result_kd = mysqli_query($cnit, $query_kd);
		$num_results_kd = mysqli_num_rows($result_kd);
		if ($num_results_kd) {
			 $row_kd = mysqli_fetch_array($result_kd);
			 $nama_kd = $row_kd['nama'];
		}
	
		echo "<tr>";
		if ($noslip=='') {
			echo "<td align=left><small>&nbsp;</small></td>";
		} else {
			echo "<td align=center><small>$noslip</small></td>";
		}
		echo "<td align=left><small>$namacab</small></td>";
		echo "<td align=left><small>$tglbr</small></td>";
		echo "<td align=left><small>$tgltrans</small></td>";
		$nama_sub="";
		
		$query_sub = "select nmsubpost from hrd.brkd_otc where subpost='$subpost'"; 
		$result_sub = mysqli_query($cnit, $query_sub);
		$num_results_sub = mysqli_num_rows($result_sub);
		if ($num_results_sub) {
			 $row_sub = mysqli_fetch_array($result_sub);
			 $nama_sub = $row_sub['nmsubpost'];
		}
                
		echo "<td align=left><small>$nama_sub</small></td>";
		echo "<td align=left><small>$nama_kd</small></td>";

		if ($keterangan1=='') {
			echo "<td align=left><small>&nbsp;</small></td>";
		} else {
			echo "<td align=left><small>$keterangan1</small></td>";
		}
		if ($keterangan2=='') {
			echo "<td align=left><small>&nbsp;</small></td>";
		} else {
			echo "<td align=left><small>$keterangan2</small></td>";
		}
		echo "<td align=left><small>$real1</small></td>";
		echo "<td align='right'><b><small>".number_format($jumlah,0)."</small></b></td>";
		echo "<td align='right'><b><small>".number_format($jumlah1,0)."</small></b></td>";
		echo "<td align='right'><b><small>".number_format($selisih,0)."</small></b></td>";
		echo "</tr>";
	} // end for
	
	if ($tot_jumlah<>0) {
	   echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
			 <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td align='right'><b>Total :</b></td>
			 <td align='right'><b><small>".number_format($tot_jumlah,0)."</small></b></td>
			 <td align='right'><b><small>".number_format($tot_jumlah1,0)."</small></b></td>
			 <td align='right'><b><small>".number_format($tot_selisih,0)."</small></b></td></tr>";
	}

     echo "</table>\n";
	 echo "<br><input type=hidden name=cmdBack id=cmdBack value=Back>";

?>

</body>
</html>
