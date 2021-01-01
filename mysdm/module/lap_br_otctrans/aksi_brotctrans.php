<html>
<head>
  <title>LAPORAN BR OTC JKT</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<style>
    body {
        font-family: "Times New Roman", Times, serif;
        font-size: 13px;
    }
    table {
        font-family: "Times New Roman", Times, serif;
        font-size: 15px;
    }
</style>
<body>
<?php
	session_start();
        include "config/koneksimysqli_it.php";
        include_once("config/common.php");
	$srid = $_SESSION['USERID'];
	$srnama = $_SESSION['NAMALENGKAP'];
	$sr_id = substr('0000000000'.$srid,-10);
	$userid = $_SESSION['USERID'];
	$jenis = $_POST['cb_jenis'];
        
        $tgl01=$_POST['e_periode01'];
        $tgl02=$_POST['e_periode02'];
        $tgl03=$_POST['e_periode03'];

        $periode1= date("Y-m-d", strtotime($tgl01));
        $periode2= date("Y-m-d", strtotime($tgl02));
        $periode3= date("d F Y", strtotime($tgl03));

	echo "<big>To : Sdri. Marianne<br><br>";
	echo "Rekap Budget Request (BR) Team OTC</big><br><br>";
	echo "<b>$periode3</b><br><br>";

	if ($jenis==""){
            echo "<td><small>&nbsp;<small></td>";
	} else {
            if ($jenis=='A') {
                $jns = 'Advance';
            } else {
                $jns = 'Klaim';
            }
	}
	
	echo "<b> ** $jns</b><br><br>";

	

	$query = "select * from hrd.br_otc where '$periode1' <= tglbr and tglbr <= '$periode2' and tgltrans='0000-00-00' and via='N' order by icabangid_o,real1,kodeid";//echo"$query";
	$header_ = add_space('No Slip',10);
	$header3_ = add_space('Posting',30);
	$header1_ = add_space('Keterangan',150);
	$header2_ = add_space('Jumlah',10);
	echo '<table border="1" cellspacing="0" cellpadding="1">';
	echo "<tr>\n";
		
	echo "<th>No</th>";
	echo '<th align="left">'.$header_."</th>";
	echo '<th align="center">'.$header3_."</th>";
	echo '<th colspan=2 align="left">'.$header1_."</th>";
	echo '<th align="left">'.$header2_."</th>";
	echo "</tr>";

	$result = mysqli_query($cnit, $query);
	$records = mysqli_num_rows($result);	
	$row = mysqli_fetch_array($result);	
	if ($records) {
	$i = 1;
	$gtotal = 0;
	$no = 0;
	while ($i <= $records) {
		$noslip_ = $row['noslip'];
		$total = 0;
	

	   while ( ($i<=$records) and ($noslip_ == $row['noslip']) ) {
		echo "<tr>";
		$no = $no + 1;	
		$real1 = $row['real1'];
		$icabangid_o = $row['icabangid_o'];
		$keterangan1 = $row['keterangan1'];
		$keterangan2 = $row['keterangan2'];
		$kodeid = $row['kodeid'];
		$jumlah = $row['jumlah'];
		$total = $total + $jumlah;
		$gtotal = $gtotal + $jumlah;
		
		$query_kd = "SELECT nama as nmkd FROM hrd.brkd_otc where kodeid='$kodeid' ";// echo"$query_mr";
		$result_kd = mysqli_query($cnit, $query_kd);
		$num_results_kd = mysqli_num_rows($result_kd);
		if ($num_results_kd) {
			 $row_kd = mysqli_fetch_array($result_kd);
			 $nama_kd = $row_kd['nmkd'];
		}

		echo "<td><small>$no</small></td>";
		
		if ($noslip_=='') {
			echo "<td align=center><small>&nbsp;</small></td>";
		} else {
			echo "<td align=center><small>$noslip_</small></td>";
		}
		echo "<td align=left><small>$nama_kd</small></td>";
		echo "<td align=left><small>$real1</small></td>";
		echo "<td align=left><small><b>$keterangan1 $keterangan2</small></td>";
		echo '<td align="right"><small>'.number_format($jumlah,0)."</small></td>";
		echo "</tr>";

		  $row = mysqli_fetch_array($result);
		  $i++;
	}// break per bulan
		
		echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td align='right'><small><b>Sub Total </td>";
		echo "<td align=right><b><small>".number_format($total,0)."</b></td></tr>";
		echo "<tr><td colspan=6>&nbsp;</td></tr>";
	}// eof  i<= num_results
		echo "<tr>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
		echo "<td align=right><b>Grand Total :</td>";
		echo "<td align=right><b>".number_format($gtotal,0)."</b></td>";
		echo "</tr>";
	echo "</table>";
	} else {
		echo "<br /><b>Data tidak ditemukan!!!</b><br/><br/>";		
	}	
	
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
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td align=center>Mengetahui,</td>";
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	echo "<tr><td>Roy Ardian</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
	echo "<td align=center>Ernilya</td>";


	echo "</table>\n";

?>

</body>
</html>
