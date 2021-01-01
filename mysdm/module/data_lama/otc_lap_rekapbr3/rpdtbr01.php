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
        
        $periode1 = $_POST['tahun'];
        $where_ = " AND YEAR(tglbr)='$periode1' ";
	$lamp = $_POST['lampiran'];

	if ($lamp=="*") {
	} else {
		if ($lamp=='Y') {
			echo "Ada Lampiran<br>";
			$where_ = $where_." and lampiran='Y'";
		} else {
			$where_ = $where_." and lampiran='N'";
		}
	}
	
        
        $now=date("mdYhis");
        $tmp01 =" dbtemp.RKPLPARLOTC01_".$_SESSION['USERID']."_$now ";
        $tmp02 =" dbtemp.RKPLPARLOTC02_".$_SESSION['USERID']."_$now ";
        
        
        echo "<b><big>REKAP BUDGET REQUEST OTC</big><br><br>";
        echo "Periode BR : $periode1<br>";
        
	echo "<br>";
	
	$header_ = add_space('Cabang',30);
	$header1_ = add_space('Tgl BR',20);
	$header2_ = add_space('Tgl Transfer',20);
	$header3_ = add_space('Posting',30);
	$header5_ = add_space('Keterangan',100);
	$header6_ = add_space('Nama Realisasi',40);
	$header7_ = add_space('Usulan BR',25);
	$header8_ = add_space('Jumlah Realisasi',25);
	$header9_ = add_space('Selisih',25);
	$header10_ = add_space('No Slip',15);
	$header11_ = add_space('Sub-Posting',50);
	$header12_ = add_space('No. BR/Divisi',30);
	

	
        
	$query = "select *, CAST('' as CHAR(50)) as nodivisi_spd from hrd.br_otc where 1=1 ".$where_." AND IFNULL(batal,'')<>'Y'"; 
	 //echo"$query";exit;
        $query = "create TEMPORARY table $tmp01 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapudata; }
                
        
        $query = "select distinct c.nomor, c.nodivisi, b.bridinput from $tmp01 a
            JOIN dbmaster.t_suratdana_br1 b on a.brOtcId=b.bridinput JOIN 
            dbmaster.t_suratdana_br c on b.idinput=c.idinput 
            WHERE c.stsnonaktif<>'Y' AND c.divisi='OTC'";
        $query = "create  table $tmp02 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapudata; }
        
                
        $query = "UPDATE $tmp01 a SET a.nodivisi_spd=(select b.nodivisi FROM $tmp02 b WHERE a.brOtcId=b.bridinput LIMIT 1)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapudata; }
        
    
    $query = "select tglbr from $tmp01 order by tglbr DESC LIMIT 1";
    $tampil = mysqli_query($cnit, $query);
    $xi= mysqli_fetch_array($tampil);
    $tglakhir=$xi['tglbr'];
    
    $grd_jumlah = 0;
    $grd_jumlah1 = 0;
    $grd_selisih = 0;
        
    echo "&nbsp;<br/>";
    $query = "select DISTINCT tglbr from $tmp01 order by tglbr";
    $result1 = mysqli_query($cnit, $query);
    while ($nrow= mysqli_fetch_array($result1)) {
    
        $ptglbr=$nrow['tglbr'];
        echo "Tgl. BR : $ptglbr<br>";
        
        //$query = "select * from $tmp01 where 1=1 ".$where_." AND IFNULL(batal,'')<>'Y' order by brOtcId";
        $query = "select * from $tmp01 where tglbr='$ptglbr' order by brOtcId";
	$result = mysqli_query($cnit, $query);
	$num_results = mysqli_num_rows($result);
	$tot_jumlah = 0;
	$tot_jumlah1 = 0;
	$tot_selisih = 0;
        
        
	echo '<table border="1" cellspacing="0" cellpadding="1">';
	echo "<tr>\n";
	echo '<th align="left"><small>'.$header12_."</small></th>";
	echo '<th align="left"><small>'.$header10_."</small></th>";
	echo '<th align="left"><small>'.$header_."</small></th>";
	//echo '<th align="left"><small>'.$header1_."</small></th>";
	echo '<th align="left"><small>'.$header2_."</small></th>";
	//echo '<th align="left"><small>'.$header3_."</small></th>";
	echo '<th align="left"><small>'.$header11_."</small></th>";
	echo '<th colspan=2 align="left"><small>'.$header5_."</small></th>";
	echo '<th align="left"><small>'.$header6_."</small></th>";
	echo '<th align="left"><small>'.$header7_."</small></th>";
	echo '<th align="left"><small>'.$header8_."</small></th>";
	echo '<th align="left"><small>'.$header9_."</small></th>";
	echo "</tr>";
        
	for ($i=0; $i < $num_results; $i++) {
		$row = mysqli_fetch_array($result);
                
                $pnodivisi_spd = $row['nodivisi_spd'];
                
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
		
		$query_kd = "select nama from hrd.brkd_otc where kodeid='$kodeid'";
		// echo $query_kd;
		$result_kd = mysqli_query($cnit, $query_kd);
		$num_results_kd = mysqli_num_rows($result_kd);
		if ($num_results_kd) {
			 $row_kd = mysqli_fetch_array($result_kd);
			 $nama_kd = $row_kd['nama'];
		}
	
		echo "<tr>";
                echo "<td nowrap><small>$pnodivisi_spd</small></td>";
		if ($noslip=='') {
			echo "<td align=left><small>&nbsp;</small></td>";
		} else {
			echo "<td align=center><small>$noslip</small></td>";
		}
		echo "<td align=left><small>$namacab</small></td>";
		//echo "<td align=left><small>$tglbr</small></td>";
		echo "<td align=left><small>$tgltrans</small></td>";

		$query_sub = "select nmsubpost from hrd.brkd_otc where subpost='$subpost'"; 
		$result_sub = mysqli_query($cnit, $query_sub);
		$num_results_sub = mysqli_num_rows($result_sub);
		if ($num_results_sub) {
			 $row_sub = mysqli_fetch_array($result_sub);
			 $nama_sub = $row_sub['nmsubpost'];
		}
                
		//echo "<td align=left><small>$nama_sub</small></td>";
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
	
        $grd_jumlah = (double)$grd_jumlah+(double)$tot_jumlah;
        $grd_jumlah1 = (double)$grd_jumlah1+(double)$tot_jumlah1;
        $grdtot_selisih = (double)$grd_jumlah - (double)$grd_jumlah1;
        $grd_selisih = (double)$grd_selisih+(double)$grdtot_selisih;
    
	if ($tot_jumlah<>0) {
	   echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
			 <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td align='right'><b>Total :</b></td>
			 <td align='right'><b><small>".number_format($tot_jumlah,0)."</small></b></td>
			 <td align='right'><b><small>".number_format($tot_jumlah1,0)."</small></b></td>
			 <td align='right'><b><small>".number_format($tot_selisih,0)."</small></b></td></tr>";
	}
        
        if ($ptglbr==$tglakhir) {
	   echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
			 <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td align='right'><b>&nbsp;</b></td>
			 <td align='right'><b><small></small></b></td>
			 <td align='right'><b><small></small></b></td>
			 <td align='right'><b><small></small></b></td></tr>";
           
	   echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
			 <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td align='right'><b>Grand Total :</b></td>
			 <td align='right'><b><small>".number_format($grd_jumlah,0)."</small></b></td>
			 <td align='right'><b><small>".number_format($grd_jumlah1,0)."</small></b></td>
			 <td align='right'><b><small>".number_format($grd_selisih,0)."</small></b></td></tr>";
        }
        
        echo "</table>\n";
        
        echo "<br/>&nbsp";
    }
     
echo "<br><input type=hidden name=cmdBack id=cmdBack value=Back>";

?>

</body>
</html>

<?PHP
hapudata:
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
?>
