<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=LAPORAN AKHIR SURABAYA.xls");
    }
?>
<html>
<head>
  <title>LAPORAN BR OTC SBY</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<form action="rpbrasb0.php" method=post>
<body>
<?php
	
	include("config/common.php");
	//include("common3.php");
	include("config/koneksimysqli.php");
        
	$srid = $_SESSION['USERID'];
	$srnama = $_SESSION['NAMALENGKAP'];
	$sr_id = substr('0000000000'.$srid,-10);
	$userid = $_SESSION['IDCARD'];
        
        $pidspd=$_GET['ispd'];
        $pperiodeby=$_GET['periodeby'];
        
    $nnama_ss_mktdir1="FARIDA SOEWANTO";
    $nnama_ss_mktdir2="EVI KOSINA SANTOSO";
	
	$nnama_ss_mktdir=$nnama_ss_mktdir1;
	
	
        $query = "select * from dbmaster.t_suratdana_br WHERE idinput=$pidspd";
	$sqlresult = mysqli_query($cnmy, $query);
	$rx = mysqli_fetch_array($sqlresult);
        
		
			$tgljakukannya=$rx['tgl'];
			if ($tgljakukannya=="0000-00-00") $tgljakukannya="";
			if (!empty($tgljakukannya)) $tgljakukannya = date("Ymd", strtotime($tgljakukannya));
			
            $passdirid=$rx['dir'];
            if ($passdirid=="0000002403") $nnama_ss_mktdir=$nnama_ss_mktdir2;
			else{
				if (!empty($tgljakukannya)) {
					if ((double)$tgljakukannya>='20200629') {
						$nnama_ss_mktdir=$nnama_ss_mktdir2;
					}
				}
			}
			
			
        $pkodeid = $rx['kodeid'];
        $jenis = "A";
        if ($pkodeid=="2") $jenis = "K";
        
        $tgl03 = $rx['tgl'];
        $periode3= date("Y-m-d", strtotime($tgl03));
        $tanggal3= date("d", strtotime($tgl03));
        $bulan3= date("m", strtotime($tgl03));
        $nm_bln3 = nama_bulan($bulan3); //echo "$nm_bln1";
        $tahun3= date("Y", strtotime($tgl03));
                
        
            $gmrheight = "100px";
            $ngbr_idinput=$rx['idinput'];
            $gbrttd_fin1=$rx['gbr_apv1'];
            $gbrttd_fin2=$rx['gbr_apv2'];
            $gbrttd_dir1=$rx['gbr_dir'];
            $gbrttd_dir2=$rx['gbr_dir2'];
            if (!empty($gbrttd_fin1)) {
                $data="data:".$gbrttd_fin1;
                $data=str_replace(' ','+',$data);
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);
                $namapengaju_ttd_fin1="imgfin1_".$ngbr_idinput."TTDSPDSBY_.png";
                file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd_fin1, $data);
            }
            
            if (!empty($gbrttd_fin2)) {
                $data="data:".$gbrttd_fin2;
                $data=str_replace(' ','+',$data);
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);
                $namapengaju_ttd_fin2="imgfin2_".$ngbr_idinput."TTDSPDSBY_.png";
                file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd_fin2, $data);
            }
            
            if (!empty($gbrttd_dir1)) {
                $data="data:".$gbrttd_dir1;
                $data=str_replace(' ','+',$data);
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);
                $namapengaju_ttd1="imgdr1_".$ngbr_idinput."TTDSPDSBY_.png";
                file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd1, $data);
            }
            
            if (!empty($gbrttd_dir2)) {
                $data="data:".$gbrttd_dir2;
                $data=str_replace(' ','+',$data);
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);
                $namapengaju_ttd2="imgdr2_".$ngbr_idinput."TTDSPDSBY_.png";
                file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd2, $data);
            }
            
            
            
	echo "<big>To : Sdri. Vanda/Lina (Accounting)<br>";
	echo "PT. SDM - Surabaya</big><br><br>";
	echo "Laporan Budget Request Team OTC</big><br><br>";
	echo "<b>$tanggal3 $nm_bln3 $tahun3</b><br><br>";
        $jns="";
	if ($jenis==""){
		echo "<td><small>&nbsp;<small></td>";
	} else {
		if ($jenis=='A') {
			$jns = 'Advance';
		} else {
			if ($jenis=='K') {
				$jns = 'Klaim';
			} else {
				$jns = 'Sudah minta uang muka';
			}
		}
	}
	
	echo "<b style='font-size:21px;'> ** $jns</b><br><br>";

        
        
        
        $now=date("mdYhis");
        $tmpbudgetreq01 =" dbtemp.DTBUDGETBRREKAPSBYOTC01_$_SESSION[IDCARD]$now ";
        $tmpbudgetreq02 =" dbtemp.DTBUDGETBRREKAPSBYOTC02_$_SESSION[IDCARD]$now ";
    
	$query = "select * from dbmaster.t_suratdana_br1 WHERE idinput='$pidspd'";//echo"$query";
        $sql = "create table $tmpbudgetreq02 ($query)";
        mysqli_query($cnmy, $sql);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
	$query = "select * from hrd.br_otc where brOtcId IN (select distinct ifnull(bridinput, '') bridinput FROM $tmpbudgetreq02)";
        $sql = "create table $tmpbudgetreq01 ($query)";
        mysqli_query($cnmy, $sql);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $sql = "UPDATE $tmpbudgetreq01 a JOIN $tmpbudgetreq02 b on a.brOtcId=b.bridinput SET a.jumlah=b.amount";
        mysqli_query($cnmy, $sql);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        

	$query = "select * from $tmpbudgetreq01 order by tglrpsby,noslip";
	//$query = "select * from hrd.br_otc where brOtcId IN (select distinct ifnull(bridinput, '') bridinput FROM dbmaster.t_suratdana_br1 WHERE idinput='$pidspd') order by tglrpsby,noslip";//echo"$query";
        //echo $query;
	$header_ = add_space('No Slip',20);
	$header1_ = add_space('Tgl.Transfer',10);
	$header4_ = add_space('Posting',30);
	$header2_ = add_space('Keterangan',150);
	$header3_ = add_space('Jumlah',10);
	echo '<table border="1" cellspacing="0" cellpadding="1">';
	echo "<tr>\n";
		
	echo "<th>No</th>";
	echo '<th align="center" nowrap>'.$header_."</th>";
	echo '<th align="center">'.$header1_."</th>";
	//echo '<th align="center">'.$header4_."</th>";
	echo '<th align="center">'.$header2_."</th>";
	echo '<th align="center">'.$header3_."</th>";
	echo "</tr>";

	$result = mysqli_query($cnmy, $query);
	$records = mysqli_num_rows($result);	
	$row = mysqli_fetch_array($result);	
	if ($records) {
	$i = 1;
	$gtotal = 0;
	$no = 0;
	while ($i <= $records) {
		$noslip_ = $row['noslip'];
		$total = 0;
		$no = $no + 1;	
		$first_ = 1;
	   while ( ($i<=$records) and ($noslip_ == $row['noslip']) ) {
		echo "<tr>";
		$real1 = $row['real1'];
		$icabangid_o = $row['icabangid_o'];
		$keterangan1 = $row['keterangan1'];
		$keterangan2 = $row['keterangan2'];
                if ($pperiodeby=="S") {
                    $jumlah = $row['realisasi'];
                }else{
                    if ($pkodeid=="2" AND $pperiodeby=="T") {
                        $jumlah = $row['realisasi'];
                    }else{
                        $jumlah = $row['jumlah'];
                    }
                }
		$tgltrans = $row['tgltrans'];
		$kodeid = $row['kodeid'];
		
		if ($tgltrans<>'') {
			$tanggal2 = substr($tgltrans,-2,2);
			$bulan2 = substr($tgltrans,5,2); 
			$tahun2 = substr($tgltrans,0,4);
			$nm_bln2 = nama_bulan($bulan2);
			$nama_bln = substr($nm_bln2,0,3);
			$periode2 = $tanggal2.'-'.$nama_bln.'-'.$tahun2;
		}
                
                
		$query_al = "select nama from hrd.brkd_otc where kodeid='$kodeid' ";// echo"$query_mr";
		$result_al = mysqli_query($cnmy, $query_al);
		$num_results_al = mysqli_num_rows($result_al);
		if ($num_results_al) {
			 $row_al = mysqli_fetch_array($result_al);
			 $nama_al = $row_al['nama'];
		}
		
		$total = $total + $jumlah;
		$gtotal = $gtotal + $jumlah;
		
		if ($first_) {
			echo "<td><small>$no</small></td>";
			$first_ = 0;
		} else {
			echo "<td><small>&nbsp;</small></td>";
		}
		
		if ($noslip_=='') {
			echo "<td align=center><small>&nbsp;</small></td>";
		} else {
			echo "<td align=center nowrap><small>$noslip_</small></td>";
		}
                
                if ($periode2=="00--0000") {
                    $periode2="";
                }
		echo "<td align=center><small>$periode2</small></td>";
	//	echo "<td align=center><small>$nama_al</small></td>";
		echo "<td align=left><small><b>$real1</b> - $keterangan1 $keterangan2</small></td>";
		echo '<td align="right"><small>'.number_format($jumlah,0)."</small></td>";
		echo "</tr>";

		  $row = mysqli_fetch_array($result);
		  $i++;
	}// break per bulan
		
		echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td align='right'><small><b>Sub Total </td>";
		echo "<td align=right><b><small>".number_format($total,0)."</b></td></tr>";
		echo "<tr><td colspan=6>&nbsp;</td></tr>";
	}// eof  i<= num_results
		echo "<tr>";
		echo "<td>&nbsp;</td><td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "<td align=right><b>Grand Total :</td>";
		echo "<td align=right><b>".number_format($gtotal,0)."</b></td>";
		echo "</tr>";
	echo "</table>";
	} else {
		echo "<br /><b>Data tidak ditemukan!!!</b><br/><br/>";		
	}	
	
        
        
        echo "<br/>&nbsp;<br/>&nbsp;";
        
        
        $nlokasi="left";
        
        //if ($pperiodeby=="S") $nlokasi="center";
        $nlokasi="center";
        echo "<table class='tjudul' width='100%'>";
            echo "<tr>";

                echo "<td align='$nlokasi'>";
                echo "Dibuat oleh,";
                if (!empty($namapengaju_ttd_fin1))
                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin1' height='$gmrheight'><br/>";
                else
                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                echo "<b>DESI RATNA DEWI</b></td>";

                //if ($pperiodeby=="S") {
                                echo "<td align='right'>";
                                echo "";
                                if (!empty($namapengaju_ttd_fin2))
                                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin2' height='$gmrheight'><br/>";
                                else
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "<b>SAIFUL RAHMAT</b></td>";

                                echo "<td align='center'>";
                                echo "Mengetahui,";
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "</td>";
                            
                            
                                echo "<td align='left'>";
                                echo "";
                                if (!empty($namapengaju_ttd1))
                                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd1' height='$gmrheight'><br/>";
                                else
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "<b>$nnama_ss_mktdir</b></td>";


                    echo "<td align='center'>";
                    echo "Disetujui,";
                    if (!empty($namapengaju_ttd2))
                        echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd2' height='$gmrheight'><br/>";
                    else
                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    echo "<b>IRA BUDISUSETYO</b></td>";
                //}
                
            echo "</tr>";

        echo "</table>";

        echo "<br/>&nbsp;<br/>&nbsp;";
        
        /*
	echo "<table>";
        
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	echo "<tr><td>Dibuat oleh,</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
        
        if ($pperiodeby=="S") {
            echo "<td align=center>Mengetahui,</td>";
            echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
            echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
            echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
            echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
            echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
            echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
            echo "<td align=center>Disetujui,</td>";
        }
            echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
            echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
            echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
            echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
       
        echo "<tr><td>Desi Ratna D</td>";
        if ($pperiodeby=="S") {
            echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
            echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
            echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
            echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
            echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
            echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
            echo "<td align=center>Saiful Rahmat</td>";
            echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
            echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
            echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
            echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
            echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
            echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
            echo "<td align=center>Ira Budisusetyo</td>";
        }
	echo "</table>\n";
	*/
	  echo "<br><input type=hidden name=cmdBack id=cmdBack value=Back>";
	 
if (empty($_SESSION['srid'])) {
  } else {
    do_show_menu($_SESSION['jabatanid'],'N');
  }

?>

</body>
</html>
