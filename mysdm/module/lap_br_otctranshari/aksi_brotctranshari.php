<html>
<head>
  <title>LAPORAN BR TRANSFER</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
  
<style>
    body {
        font-family: "Times New Roman", Times, serif;
        font-size: 14px;
    }
    table {
        font-family: "Times New Roman", Times, serif;
        font-size: 15px;
    }
</style>

</head>
<body>
<?php
	session_start();
        include "config/koneksimysqli_it.php";
        include_once("config/common.php");
	$srid = $_SESSION['USERID'];
	$srnama = $_SESSION['NAMALENGKAP'];;
	$sr_id = substr('0000000000'.$srid,-10);
	$userid = $_SESSION['USERID'];
	$lamp = $_POST['cb_lampiran'];
        
        $tgl01=$_POST['e_periode01'];
        $periode1= date("Y-m-d", strtotime($tgl01));
        $myperiode1= date("d F Y", strtotime($tgl01));
        
	$icabangid_o = $_POST['e_idcabang'];
 

	echo "<big>To : Sdri. Marianne<br><br><br>";
	echo "Rekap Budget Request (RBR) Team OTC</big><br>";
	echo "<b>$myperiode1</b><br><br>";
        
        $where_="";
	if ($icabangid_o=="") {	
	} else {
            $where_ = "and icabangid_o='$icabangid_o'"; //echo"$where_";
	}
	$jns="";
	if ($lamp==""){
		//echo "<td><small>&nbsp;<small></td>";
	} else {
            if ($lamp=='A') {
                $where_ = $where_." and lampiran='Y'";
                $jns = '** Ada Kuitansi';
            } else {
                $where_ = $where_." and lampiran='N'";
                $jns = '** Belum Ada Kuitansi';
            }
	}
	
	echo "<b> $jns</b><br><br>";


	$query = "select * from hrd.br_otc where tgltrans = '$periode1'  ".$where_." order by noslip";//echo"$query";
        //echo $query; exit;
	$header_ = add_space('No Slip',10);
	$header3_ = add_space('Posting',30);
	$header1_ = add_space('Keterangan',100);
	$header2_ = add_space('Jumlah',10);
	echo '<table border="1" cellspacing="0" cellpadding="1">';
	echo "<tr>\n";
		
	echo "<th>No</th>";
	echo '<th align="left">'.$header_."</th>";
	echo '<th align="left">'.$header3_."</th>";
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
		$no = $no + 1;	
		$first_ = 1;
		$first1_ = 1;
	   while ( ($i<=$records) and ($noslip_ == $row['noslip']) ) {
		echo "<tr>";
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
				
		if ($first_) {
			echo "<td><small>$no</small></td>";
			$first_ = 0;
		} else {
			echo "<td><small>&nbsp;</small></td>";
		}
		
		
		if ($first1_) {
			if ($noslip_=='') {
				echo "<td align=center><small>&nbsp;</small></td>";
			} else {
				echo "<td align=center><small>$noslip_</small></td>";
			}
			$first1_ = 0;
		} else {
			echo "<td><small>&nbsp;</small></td>";
		}
		
		echo "<td align=left><small>$nama_kd</small></td>";
		echo "<td align=left><small>$real1</small></td>";
		echo "<td align=left><small>$keterangan1 $keterangan2</small></td>";
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
		echo "<td>&nbsp;</td><td>&nbsp;</td>";
		echo "<td>&nbsp;</td><td>&nbsp;</td>";
		echo "<td align=right><b>Grand Total :</td>";
		echo "<td align=right><b>".number_format($gtotal,0)."</b></td>";
		echo "</tr>";
	echo "</table>";
	} else {
		echo "<br /><b>Data tidak ditemukan!!!</b><br/><br/>";		
	}	

	echo "</table>";
	
	echo "<table>";
	
	$query1 = "select biaya from hrd.br_otc where tgltrans='$periode1'";// ECHO"$query1";
	$result1 = mysqli_query($cnit, $query1);
	$num_results1 = mysqli_num_rows($result1);
	$row1 = mysqli_fetch_array($result1);	 
        $biaya=0;
	if ($num_results1) {
            $biaya = $row1['biaya'];
	}
	
	
	echo "<tr><td align='right'><small><b><big>+ Biaya Transfer Rp ".number_format($biaya,0)."</big></b></small></td></tr>";
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	echo "<tr><td>Dibuat oleh,</td>";
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	echo "<tr><td>Saiful Rahmat</td>";


	echo "</table>\n";


	echo "</table>\n";

?>

</body>
</html>
