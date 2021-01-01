<html>
<head>
  <title>Realisasi BR</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <link rel="shortcut icon" href="images/icon.ico" />
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<script>
function disp_confirm1(pText_)  {
    ok_ = 1;

	if (ok_) {
		var r=confirm(pText_)
		if (r==true) {
			//document.write("You pressed OK!")
			document.getElementById("rlbr02").action = "module/data_lama/lap_br_realisasi/rlbr03.php";
			document.getElementById("rlbr02").submit();
			return 1;
		}
	} else {
		//document.write("You pressed Cancel!")
		return 0;
	}
}

function click_reset() {
	document.getElementById("rlbr02").action = "rlbr02.php";
	document.getElementById("rlbr02").submit();
}
</script>
<body>
<form id="rlbr02" action="" method=post>

<?php
   include "config/koneksimysqli_it.php"; 
   include("config/common.php");
   include("config/fungsi_sql.php");
   //include("common3.php");
   session_start();

   if (empty($_SESSION['IDCARD'])) {
      echo 'not authorized';
      exit;
   } else {
		
		
		$ncarisudahclosebrid=CariSudahClosingBRID1($_GET['brid'], "A");
		
		
		$srid = $_SESSION['USERID'];
		$srnama = $_SESSION['NAMALENGKAP'];
		$sr_id = substr('0000000000'.$srid,-10);
		$userid = $_SESSION['USERID'];
		$lampiran1 = $_GET['lampiran1']; //echo"$lampiran1";
		$brid = $_GET['brid'];		
		
		$tahun = date('Y');
		$tahun_1 = $tahun - 1;
		$tahun_2 = $tahun + 1;
		$bulan = $_GET['bulan'];
		$tanggal = $_GET['bulan'];
		if ($bulan=="") {
			$bulan = date('m');
		}
		if ($tanggal=="") {
			$tanggal = date('d');
		}
		$tanggal1 = $tanggal2 = $tanggal3 = date('d'); 
		$bln1 = $bulan2= $bulan3 = date('m');
		$tahun1 = $tahun2 = $tahun3 = date('Y');
		
                $set_focus="";
		$checked_ = "";
		$disabled_ = "";
                $entrymode="";
		if ($entrymode=='D') {
			$disabled_ = "disabled";
		}
		
		//ambil data
		$query = "select * from hrd.br0 where brid='$brid'"; 
		// echo $query;
		$result = mysqli_query($cnit, $query);
		$num_results = mysqli_num_rows($result);
		if ($num_results) {
			$row = mysqli_fetch_array($result);;		
			$brid = $row['brId'];			
			$jumlah = $row['jumlah'];
			$jumlah1 = $row['jumlah1'];
			$karyawanid = $row['karyawanId'];
			$tgltrans = $row['tgltrans'];
			$tgltrm = $row['tgltrm'];
			$divprodid = $row['divprodid'];
			$aktivitas1 = $row['aktivitas1'];
			$aktivitas2 = $row['aktivitas2'];		
			$lain2 = $row['lain2'];		
		}
		
		if ($tgltrm <> '0000-00-00') {
			$tanggal1 = substr($tgltrm,-2,2);
			$bln1 = substr($tgltrm,5,2); 
			$tahun1 = substr($tgltrm,0,4); 
			$tahun_1 = $tahun1 - 1;
			$tahun_2 = $tahun1 + 1;
		}
		
		$nama_mr = '';
		$query_mr = "select nama from hrd.karyawan where karyawanid='$karyawanid'"; //echo"$query_mr";
		$result_mr = mysqli_query($cnit, $query_mr);
		$num_results_mr = mysqli_num_rows($result_mr);
		if ($num_results_mr) {
			 $row_mr = mysqli_fetch_array($result_mr);
			 $nama_mr = $row_mr['nama'];
		}
			
		echo "<table>";
		echo '<tr>';
		echo "<td align=right>Nama Pembuat :</td>";
		echo "<td>$nama_mr</td>";
		echo '</tr>';
		
		echo '<tr>';
		echo "<td align=right>Tanggal Transfer :</td>";
		echo "<td>$tgltrans</td>";
		echo '</tr>';
		
		echo '<tr>';
		echo "<td align=right>Keterangan :</td>";
		echo "<td>$aktivitas1 $aktivitas2</td>";
		echo '</tr>';
		
		echo '<tr>';
		echo "<td align=right>Jumlah :</td>";
		echo "<td>".number_format($jumlah,0)."</td>";
		echo '</tr>';
		
		echo '<tr>';
		echo '<td align=right>Tanggal Terima :</td>';
		echo "<td><select name='tanggal1' id='tanggal1' $disabled_>";
		for ($i=1; $i<32; $i++) {
			$i_ = substr('0'.$i,-2);		
			if ($i == $tanggal1) {
				echo "<option selected='selected' value='$i_'>$i_</option>";	
			} else {
				echo "<option value='$i_'>$i_</option>";					
			}
		}		
		echo '</select>';
	
		echo "&nbsp;&nbsp;<select name='bln1' id='bln1' $disabled_>";
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

		echo "&nbsp;&nbsp;<select name='tahun1' id='tahun1' $disabled_>";
		echo "<option value='$tahun_1'>$tahun_1</option>";
		echo "<option selected='selected' value='$tahun'>$tahun</option>";
		echo "<option value='$tahun_2'>$tahun_2</option>";
		echo '</select>';		
		echo '</td>';		  
		echo '</tr>'; 
		
		echo '<tr>';
		echo '<td align=right>Jumlah Realisasi :</td>';
		echo '<td><input type="text" id="jumlah1" name="jumlah1" value="'.number_format($jumlah1,0,'','').'" size=10 maxlength=10></<td>';
		echo '</tr>';
		
		echo '<tr>';
		echo "<td align=right>Lain - lain:</td>";
		echo "<td><input type=lain2 id='lain2' name='lain2' maxlength=20 size=22 value='$lain2'></td>";
		echo '</tr>'; 
		
                    if ($ncarisudahclosebrid==true) {
                        echo "<tr>";
                        echo "<td align=right>&nbsp;</td>";
                        echo "<td>";
                        echo "<span style='color:red;'>Tidak bisa dibatalkan karena sudah closing SURABAYA...</span>";
                        echo "</td>";
                        echo '</tr>';
                    }else{
						echo "<tr>";
						echo "<td align=right>Batal :</td>";
						echo "<td><input type=checkbox name=chkFull id=chkFull value=1></td>";
						echo '</tr>'; 
					}
		
		echo "</table>";
	   
		echo "<input type=hidden id='set_focus' name='set_focus' value=".$set_focus."></td>";	  	  
		echo "<SCRIPT LANGUAGE='javascript'>\n";
		echo "   set_focus('$set_focus');\n";
		echo "</SCRIPT>\n";
	  
	} 
	echo "<br>";
	
	echo "<input type=button id=cmdSave name=cmdSave value='Save' onclick='disp_confirm1(\"Simpan ?\")'>";
	//echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button id=cmdReset name=cmdReset value='Reset' onclick='click_reset()'>";
		  
	echo "<input type=hidden id='brid' name='brid' value='$brid'>";
	echo "<input type=hidden id='tgltrans' name='tgltrans' value='$tgltrans'>";
	echo "<input type=hidden id='divprodid' name='divprodid' value='$divprodid'>";
	echo "<input type=hidden id='lampiran1' name='lampiran1' value='$lampiran1'>";
	
?>
 
</form>
</body>
</html>
