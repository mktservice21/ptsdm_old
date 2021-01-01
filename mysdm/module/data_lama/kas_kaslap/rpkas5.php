<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=LAPORAN KAS KECIL.xls");
    }
?>

<html>
<head>
  <title>LAPORAN KAS KECIL</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<body>
<form id="rpkas5" action="" method=post>
<?php
	include("config/common.php");
	//include("common3.php");
	include("config/koneksimysqli.php");
	
        $puserid=$_SESSION['USERID'];
        $now=date("mdYhis");
        $tmp00 =" dbtemp.tmprekapkas00_".$puserid."_$now ";
        $tmp01 =" dbtemp.tmprekapkas01_".$puserid."_$now ";

	if (empty($_SESSION['IDCARD'])) {
		echo 'not authorized';
		exit;
	} else {
		
		
		$tanggal1 = $_POST['tanggal1']; 
		$bulan1 = $_POST['bulan1'];
		$tahun1 = $_POST['tahun1'];
		$periode1 = $tahun1.'-'.$bulan1.'-'.$tanggal1; //echo"$periode1";
		if ($periode1 == '--') {
			$periode1 = $_POST['periode1']; 
		}
		
		$tanggal2 = $_POST['tanggal2'];
		$bulan2 = $_POST['bulan2'];
		$tahun2 = $_POST['tahun2'];
		$periode2 = $tahun2.'-'.$bulan2.'-'.$tanggal2; 
		if ($periode2 == '--') {
			$periode2 = $_POST['periode2']; 
		}
	



                $query = "select a.*, b.nama namakode, d.NAMA4, CAST('' as CHAR(50)) as nodivisi from hrd.kas a join hrd.bp_kode b on a.kode=b.kodeid
                    LEFT JOIN dbmaster.posting_coa_kas c on a.kode=c.kodeid and b.kodeid=c.kodeid
                    LEFT JOIN dbmaster.coa_level4 d on a.COA4=d.COA4
                    WHERE ('".$periode1."' <= periode2 and periode2 <= '".$periode2."') ";
                $query = "create TEMPORARY table $tmp00 ($query)";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
                
                $query = "CREATE INDEX `norm1` ON $tmp00 (kasId, kode, COA4)";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

                $query = "select distinct b.idinput, b.divisi, b.nodivisi, a.kodeinput, a.bridinput, b.pilih from dbmaster.t_suratdana_br1 a "
                        . " JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput WHERE "
                        . " IFNULL(b.stsnonaktif,'')<>'Y' AND IFNULL(b.nodivisi,'')<>'' "
                        . " AND a.bridinput IN (select distinct IFNULL(kasId,'') from $tmp00)";
                $query = "create TEMPORARY table $tmp01 ($query)";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

                $query = "CREATE INDEX `norm1` ON $tmp01 (idinput, nodivisi, bridinput, kodeinput)";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

                $query = "UPDATE $tmp00 a JOIN $tmp01 b ON a.kasId=b.bridinput SET a.nodivisi=b.nodivisi";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
		echo "<b>LAPORAN KAS KECIL PERIODE $periode1 s/d $periode2</b>";
                
                
                
                
                
		$jml2=0;
		$full_search = $_POST['chkFull'];
		if ($full_search=="1") {
			$tanggal3 = $_POST['tanggal3']; 
			$bulan3 = $_POST['bulan3'];
			$tahun3 = $_POST['tahun3'];
			$periode3 = $tahun3.'-'.$bulan3.'-'.$tanggal3; 
			if ($periode3 == '--') {
				$periode3 = $_POST['periode3']; 
			}
			
			$tanggal4 = $_POST['tanggal4'];
			$bulan4 = $_POST['bulan4'];
			$tahun4 = $_POST['tahun4'];
			$periode4 = $tahun4.'-'.$bulan4.'-'.$tanggal4; 
			if ($periode4 == '--') {
			$periode4 = $_POST['periode4']; 
			}
			$query = "select sum(jumlah) as jml2 from hrd.kas where ('".$periode3."' <= periode2 and periode2 <= '".$periode4."') order by periode2"; //echo"$query";// and tgl <= '$akhir'";
			$result = mysqli_query($cnmy, $query);
			$num_results = mysqli_num_rows($result);
			$row = mysqli_fetch_array($result);
			$jml2 = $row['jml2'];
		} 
			
		//$query = "select * from hrd.kas where ('".$periode1."' <= periode2 and periode2 <= '".$periode2."') order by periode2,nama"; //echo"$query";// and tgl <= '$akhir'";
		$query = "select * from $tmp00 order by periode2,nama"; //echo"$query";// and tgl <= '$akhir'";
                
		$result = mysqli_query($cnmy, $query);
		$records = mysqli_num_rows($result);	
		$row = mysqli_fetch_array($result);
		echo '<table border="1" cellspacing="0" cellpadding="1">';
		echo '<br><tr>';
		echo '<th align="center"><small>No.</small></th>';
		echo '<th align="center"><small>Tanggal</small></th>';
		echo '<th align="center"><small>No Divisi</small></th>';
		echo '<th align="center"><small>Uraian</small></th>';
		echo '<th align="center"><small>Jumlah</small></th>';
		
		echo '</tr>';
		if ($records) {
			$i = 1;
			$gtotal = 0;
			$total = 0;
			$no = 0;
			while ($i <= $records) {
				$periode2 = $row['periode2'];
				$first__ = 1;
			    while ( ($i<=$records) and ($periode2 == $row['periode2'])) {
					$kasid = $row['kasId'];			
					$pnodivisi = $row['nodivisi'];			
					$nama = $row['nama'];
					$aktivitas1 = $row['aktivitas1'];
					$aktivitas2 = $row['aktivitas2'];
					$jumlah = $row['jumlah']; 
					$total = $total + $jumlah; 
					$no = $no + 1;	
					echo "<tr>";
					echo "<td align='center'><small>$no</small></td>";
					if ($first__) {
						echo "<td><small>$periode2</small></td>";
						$first__ = 0;
					} else {
						echo "<td><small>&nbsp;</small></td>";
					}	
					echo "<td><small>$pnodivisi</small></td>";
					echo "<td><small>$nama - $aktivitas1 $aktivitas2</small></td>";
					echo "<td align=right><small>".number_format($jumlah,0)."</b></td>";
						
					echo "</tr>";
					$row = mysqli_fetch_array($result);
					$i++;
				}
			}
			echo "<tr>";
			echo "<td colspan=4 align=right><small><b>Total :</td>";
			echo "<td align=right><b>".number_format($total,0)."</b></td>";
			echo "</tr>";
			
			$query_1 ="select max(periode) as periode from hrd.petty"; 
			$result_1 = mysqli_query($cnmy, $query_1);
			$num_results_1 = mysqli_num_rows($result_1);	
			$row1 = mysqli_fetch_array($result_1);
			$periode3 = $row1['periode']; 
			
			$query_2 ="select petty from hrd.petty where periode='$periode3'"; 
			$result_2 = mysqli_query($cnmy, $query_2);
			$num_results_2 = mysqli_num_rows($result_2);	
			$row2 = mysqli_fetch_array($result_2);
			$petty = $row2['petty']; 
			
			echo "<tr>";
			echo "<td colspan=4 align=right><small><b>Kas Kecil :</td>";
			echo "<td align=right><b>".number_format($petty,0)."</b></td>";
			echo "</tr>";
			
			$tanggal1 = $_POST['tanggal1']; 
			$bulan1 = $_POST['bulan1'];
			$tahun1 = $_POST['tahun1'];
			$periode1 = $tahun1.'-'.$bulan1.'-'.$tanggal1; 
			if ($periode1 == '--') {
				$periode1 = $_POST['periode1']; 
			}
			  
			/* //LAMA
			$query_3 ="select sum(jumlah) as jml from dbmaster.t_kasbon kasbon 
					   join hrd.karyawan karyawan on kasbon.karyawanid=karyawan.karyawanid
					   where ('".$periode1."' <= periode and periode <= '".$periode2."')
					   order by periode,nama";
						*/
			$query_3 ="select sum(jumlah) as jml from dbmaster.t_kasbon kasbon 
					   join hrd.karyawan karyawan on kasbon.karyawanid=karyawan.karyawanid
					   where ('".$periode1."' <= tgl and tgl <= '".$periode2."')
					   ";  //order by tgl,nama
			$result_3 = mysqli_query($cnmy, $query_3);
			$num_results_3 = mysqli_num_rows($result_3);	
			$row3 = mysqli_fetch_array($result_3);
			$jml = $row3['jml']; 
			
			echo "<tr>";
			echo "<td colspan=4 align=right><small><b>Kas Bon Terlampir :</td>";
			echo "<td align=right><b>".number_format($jml,0)."</b></td>";
			echo "</tr>";
			
			if ($full_search=="1") {
				echo "<tr>";
				echo "<td colspan=4 align=right><small><b>Kas Kecil - belum ditarik dari Bank Niaga :</td>";
				echo "<td align=right><b>".number_format($jml2,0)."</b></td>";
				echo "</tr>";
			}
			echo "<tr>";
			echo "<td colspan=4 align=right><small><b>Saldo Akhir :</td>";
			$gtotal=$petty-$total-$jml-$jml2;
			echo "<td align=right><b>".number_format($gtotal,0)."</b></td>";
			echo "</tr>";
			echo "</table>";
		} else {
			echo "<br /><b>Data tidak ditemukan!!!</b><br/><br/>";		
	    }	
    }  // if (empty($_SESSION['srid'])) 
      
	if (empty($_SESSION['srid'])) {
	} else {
	  do_show_menu($_SESSION['jabatanid'],'N');
	}
?>
</form>
</body>
</html>


<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_close($cnmy);
?>