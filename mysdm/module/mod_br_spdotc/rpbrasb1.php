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
        
        $nkaryawanid=$rx['karyawanid'];
        $nkryapv1=$rx['apv1'];
        $nkryapv2=$rx['apv2'];
        $npjenisrpt=$rx['jenis_rpt'];
        $npilihspd=$rx['pilih'];
		
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
            
            
            
        $query = "select nama as nama_karyawan from hrd.karyawan WHERE karyawanid='$nkaryawanid'";
        $tmapiln= mysqli_query($cnmy, $query);
        $irow= mysqli_fetch_array($tmapiln);
        $pnmkaryawan=$irow['nama_karyawan'];

        $query = "select nama as nama_apv1 from hrd.karyawan WHERE karyawanid='$nkryapv1'";
        $tmapiln= mysqli_query($cnmy, $query);
        $irow= mysqli_fetch_array($tmapiln);
        $pnmapv1=$irow['nama_apv1'];

        $query = "select nama as nama_apv2 from hrd.karyawan WHERE karyawanid='$nkryapv2'";
        $tmapiln= mysqli_query($cnmy, $query);
        $irow= mysqli_fetch_array($tmapiln);
        $pnmapv2=$irow['nama_apv2'];
        
        
        $pnamapembuat="DESI RATNA DEWI";
        $pnamaapprove="SAIFUL RAHMAT";

        //if ((double)$tgljakukannya>='20210523') {
            $pnamapembuat=$pnmapv1;//"SAIFUL RAHMAT";
            $pnamaapprove=$pnmapv2;//"MARIANNE PRASANTI";
        //}
            
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
        $tmpbudgetreq03 =" dbtemp.DTBUDGETBRREKAPSBYOTC03_$_SESSION[IDCARD]$now ";
    
	$query = "select * from dbmaster.t_suratdana_br1 WHERE idinput='$pidspd'";//echo"$query";
        $sql = "create TEMPORARY table $tmpbudgetreq02 ($query)";
        mysqli_query($cnmy, $sql);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
	$query = "select * from hrd.br_otc where brOtcId IN (select distinct ifnull(bridinput, '') bridinput FROM $tmpbudgetreq02) AND IFNULL(noslip,'')<>''";
        $sql = "create TEMPORARY  table $tmpbudgetreq01 ($query)";
        mysqli_query($cnmy, $sql);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $sql = "UPDATE $tmpbudgetreq01 a JOIN $tmpbudgetreq02 b on a.brOtcId=b.bridinput SET a.jumlah=b.amount";
        mysqli_query($cnmy, $sql);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $sql = "ALTER TABLE $tmpbudgetreq01 ADD COLUMN noslip_adj VARCHAR(1) DEFAULT 'N'";
        mysqli_query($cnmy, $sql); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query = "select a.* from $tmpbudgetreq01 as a JOIN (select bridinput from $tmpbudgetreq02 where ifnull(jml_adj,0)<>0) as b "
                . " on a.brOtcId=b.bridinput";
        $sql = "create TEMPORARY  table $tmpbudgetreq03 ($query)";
        mysqli_query($cnmy, $sql); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $sql = "UPDATE $tmpbudgetreq03 SET keterangan2='', keterangan1='', jumlah='0', realisasi='0'";
        mysqli_query($cnmy, $sql); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $sql = "UPDATE $tmpbudgetreq03 as a JOIN $tmpbudgetreq02 as b on a.brOtcId=b.bridinput SET a.noslip_adj='Y', a.keterangan1=b.aktivitas1, a.jumlah=b.jml_adj, a.realisasi=b.jml_adj";
        mysqli_query($cnmy, $sql); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $sql = "INSERT INTO $tmpbudgetreq01 SELECT * FROM $tmpbudgetreq03";
        mysqli_query($cnmy, $sql); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        

	$query = "select * from $tmpbudgetreq01 order by noslip, tglrpsby,tgltrans";
	//$query = "select * from hrd.br_otc where brOtcId IN (select distinct ifnull(bridinput, '') bridinput FROM dbmaster.t_suratdana_br1 WHERE idinput='$pidspd') order by tglrpsby,noslip";//echo"$query";
        //echo $query;
	$header_ = add_space('No Slip',20);
	$header1_ = add_space('Tgl.Transfer',10);
	$header4_ = add_space('Posting',30);
	$header2_ = add_space('Keterangan',150);
        $header5_ = add_space('Realisasi',30);
	$header3_ = add_space('Jumlah',10);
	//echo '<table border="1" cellspacing="0" cellpadding="1">';
        echo "<table id='datatable2' class='table table-striped table-bordered example_2' border='1px solid black'>";
	echo "<tr>\n";
		
	echo "<th>No</th>";
	echo '<th align="center" nowrap>'.$header_."</th>";
	echo '<th align="center">'.$header1_."</th>";
	//echo '<th align="center">'.$header4_."</th>";
	echo '<th align="center">'.$header2_."</th>";
	echo '<th align="center">'.$header5_."</th>";
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
               
                $nbatalkan=$row['batal'];
                $nbatalkan_alasan=$row['alasan_batal'];
                
                $pstylebatal="";
                if ($nbatalkan=="Y") {
                    $pstylebatal=" style='color:red;' ";
                }
                
		echo "<tr $pstylebatal>";
		$real1 = $row['real1'];
		$icabangid_o = $row['icabangid_o'];
		$keterangan1 = $row['keterangan1'];
		$keterangan2 = $row['keterangan2'];
		$pnosliphilangkan = $row['noslip_adj'];
                
                if ($nbatalkan=="Y") {
                    $keterangan2 .=" (".$nbatalkan_alasan.")";
                }
                
                
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
			echo "<td>$no</td>";
			$first_ = 0;
		} else {
			echo "<td><small>&nbsp;</small></td>";
		}
		
                if ($pnosliphilangkan=="Y") $noslip_="";
                
		if ($noslip_=='') {
			echo "<td align=center><small>&nbsp;</small></td>";
		} else {
			echo "<td align=center nowrap>$noslip_</td>";
		}
                
                if ($periode2=="00--0000") {
                    $periode2="";
                }
		echo "<td align=center>$periode2</td>";
	//	echo "<td align=center><small>$nama_al</small></td>";
		echo "<td align=left>$keterangan1</td>";
		echo "<td align=left><b>$real1</b></td>";
		echo '<td align="right">'.number_format($jumlah,0)."</td>";
		echo "</tr>";

		  $row = mysqli_fetch_array($result);
		  $i++;
	}// break per bulan
		
		echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td align='right'><b>Sub Total </td>";
		echo "<td align=right><b>".number_format($total,0)."</b></td></tr>";
		echo "<tr><td colspan=6>&nbsp;</td></tr>";
	}// eof  i<= num_results
		echo "<tr>";
		echo "<td>&nbsp;</td><td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
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
                echo "<b>$pnamapembuat</b></td>";

                //if ($pperiodeby=="S") {
                                echo "<td align='right'>";
                                echo "";
                                if (!empty($namapengaju_ttd_fin2))
                                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin2' height='$gmrheight'><br/>";
                                else
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "<b>$pnamaapprove</b></td>";

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
                                
                    if ($npilihspd=="N") {//$npjenisrpt
                    }else{
                        echo "<td align='center'>";
                        echo "Disetujui,";
                        if (!empty($namapengaju_ttd2))
                            echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd2' height='$gmrheight'><br/>";
                        else
                            echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        echo "<b>IRA BUDISUSETYO</b></td>";
                    }
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

<?PHP
    hapusdata:
        mysqli_query($cnmy, "drop temporary table $tmpbudgetreq01");
        mysqli_query($cnmy, "drop temporary table $tmpbudgetreq02");
        mysqli_query($cnmy, "drop temporary table $tmpbudgetreq03");
        mysqli_close($cnmy);
?>

    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 13px;
            border: 0px solid #000;
        }
        table.example_2 {
            color: #000;
            font-family: Helvetica, Arial, sans-serif;
            width: 100%;
            border-collapse:
            collapse; border-spacing: 0;
            font-size: 11px;
            border: 1px solid #000;
        }

        table.example_2 td, table.example_3 th {
            border: 1px solid #000; /* No more visible border */
            height: 28px;
            transition: all 0.3s;  /* Simple transition for hover effect */
            padding: 5px;
        }

        table.example_2 th, table.example_3 th {
            background: #DFDFDF;  /* Darken header a bit */
            font-weight: bold;
        }

        table.example_2 td, table.example_3 td {
            background: #FAFAFA;
        }

        /* Cells in even rows (2,4,6...) are one color */
        tr:nth-child(even) td { background: #F1F1F1; }

        /* Cells in odd rows (1,3,5...) are another (excludes header cells)  */
        tr:nth-child(odd) td { background: #FEFEFE; }

        tr td:hover.biasa { background: #666; color: #FFF; }
        tr td:hover.left { background: #ccccff; color: #000; }

        tr td.center1, td.center2 { text-align: center; }

        tr td:hover.center1 { background: #666; color: #FFF; text-align: center; }
        tr td:hover.center2 { background: #ccccff; color: #000; text-align: center; }
        /* Hover cell effect! */

        table {
            font-family: "Times New Roman", Times, serif;
            font-size: 11px;
        }
        table.tjudul {
            font-size: 13px;
            width: 97%;
        }


        #kotakjudul {
            border: 0px solid #000;
            width:100%;
            height: 1.3cm;
        }
        #isikiri {
            float   : left;
            width   : 49%;
            border-left: 0px solid #000;
        }
        #isikanan {
            text-align: right;
            float   : right;
            width   : 49%;
        }
        h2 {
            font-size: 15px;
        }
        h3 {
            font-size: 20px;
        }
        
        
        table.example_3 {
            color: #000;
            font-family: Helvetica, Arial, sans-serif;
            border-collapse:
            collapse; border-spacing: 0;
            font-size: 11px;
            border: 1px solid #000;
            padding:5px;
        }
        table.example_3 td {
            padding:5px;
        }
    </style>