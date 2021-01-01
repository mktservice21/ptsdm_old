<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=Lihat/Edit/Delete Kas Kecil.xls");
    }
?>
<html>
<head>
  <title>Lihat/Edit/Delete Kas Kecil</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<script src="kas.js">
</script>
<body>
<form id="kas11" action="" method=post>
<?php
	include("config/common.php");
	//include("common3.php");
	include("config/koneksimysqli.php");
	$srid = $_SESSION['USERID'];
	$srnama = $_SESSION['NAMALENGKAP'];
	$sr_id = substr('0000000000'.$srid,-10);
	$userid = $_SESSION['IDCARD'];

	if (empty($_SESSION['IDCARD'])) {
	  echo 'not authorized';
	  exit;
	} else {
	  
		
		$tanggal = $_POST['tanggal'];
		$bulan = $_POST['bulan'];
		$tahun = $_POST['tahun'];
		$periode1 = $tahun.'-'.$bulan.'-'.$tanggal; 
		$kodeid = $_POST['kodeid'];
		if ($periode1 == '--') {
			$periode1 = $_POST['periode1']; 
		}
		
		
		if ($kodeid=="" or $kodeid=="blank" ) {
			//echo "Kode perkiraan harus diisi!<br><br>";
			//echo "<input type=hidden name=cmdBack id=cmdBack value=Back onclick='go_back(1)'><br>";
			//exit;
		}
		
		$filterkodenya="";
		if ($kodeid!="" AND $kodeid!="blank" ) {
			$filterkodenya=" AND kode='$kodeid' ";
		}
		
		echo "<b>Data Harian Tanggal : $periode1</b><br /><br />"; 
		$query = "select nama,kodeid from hrd.bp_kode where kodeid='$kodeid'"; 
		$result = mysqli_query($cnmy, $query);
		$row = mysqli_fetch_array($result);
		$num_results = mysqli_num_rows($result);
		$nm_kode = $row['nama'];
		echo "$nm_kode<br><br>";
	
		//kode='$kodeid'
		$query = "select * from hrd.kas where 1=1 $filterkodenya and (periode1 = '$periode1' OR periode2 = '$periode1') order by nobukti";  //echo"$query";
		$result = mysqli_query($cnmy, $query);
		$num_results = mysqli_num_rows($result);	  	  	  	  
                
                $no=0;
		if ($num_results) {
			$i = 0;
			$total = 0; 
			echo "<table border='1'>";
			echo "<tr>";
			echo "<th align=center><small>No</small></th>";
			echo "<th align=center><small>No.Bukti</small></th>";
			echo "<th align=center><small>ID</small></th>";
			echo "<th align=center><small>Nama</small></th>";
			echo "<th align=center><small>Keterangan</small></th>";
			echo "<th align=center><small>Jumlah</small></th>";
			echo "<th align=center><small></small></th>";	  
			echo "<th align=center><small></small></th>";	  
			while ($i < $num_results) {	 
				$row = mysqli_fetch_array($result);
				$kasid = $row['kasId'];		
				$no = $no + 1; 
				$nobukti = $row['nobukti'];
				$periode1 = $row['periode1'];
				$karyawanid = $row['karyawanid']; 
				$nama = $row['nama']; 
				$aktivitas1 = $row['aktivitas1'];
				$aktivitas2 = $row['aktivitas2'];
				$jumlah = $row['jumlah']; 
				$periode2 = $row['periode2'];
				$user1  = $row['user1'];
				$total = $total + $jumlah;

				$nama_mr = '';
				$query_mr = "select nama from hrd.karyawan where karyawanId='$karyawanid'"; 
				$result_mr = mysqli_query($cnmy, $query_mr);
				$num_results_mr = mysqli_num_rows($result_mr);
				if ($num_results_mr) {
					 $row_mr = mysqli_fetch_array($result_mr);
					 $nama_mr = $row_mr['nama'];
				}
				
				if (empty($jumlah)) $jumlah=0;
				$pjml_pil=number_format($jumlah,0,",",",");
				
				echo "<tr>";
				echo "<td align=center><small>$no</small></td>";
				if ($nobukti=="") {
					echo "<td>&nbsp;</td>";
				} else {
					echo "<td><small>$nobukti</small></td>";
				}
				echo "<td><small>$kasid</small></td>";
				echo "<td><small>$nama</small></td>";
				if ($aktivitas1=="") {
					echo "<td>&nbsp;</td>";
				} else {
					echo "<td><small>$aktivitas1 $aktivitas2</small></td>";	
				}		
				echo "<td align=right><small>$pjml_pil</small></td>";							
			    echo "<td><a href='media.php?module=kasisikas&idmenu=700&act=lama&entry=E&id=$kasid'>View/Edit</a></td>";
				echo "<td><a href='media.php?module=kasisikas&idmenu=700&act=lama&entry=D&id=$kasid'>Delete</a></td>";
			  	echo "</tr>";
				$i ++;
		    } 
			echo "<tr>";
			echo "<td align=right><b>Total :</td>";
			echo "<td>&nbsp;</td>";
			echo "<td>&nbsp;</td>";
			echo "<td>&nbsp;</td>";
			echo "<td>&nbsp;</td>";
			echo "<td align=right><b>".number_format($total,0)."</b></td>";
			echo "<td>&nbsp;</td>";
			echo "<td>&nbsp;</td>";
			echo "</tr>";
		    echo "</table>";
	    } else {
			echo "<br /><b>Data tidak ditemukan!!!</b><br/><br/>";		
	    }	
    }  // if (empty($_SESSION['srid'])) 

?>
</form>
</body>
</html>


