
<html>
<head>
  <title>Budget Request OTC</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <link rel="shortcut icon" href="../../../images/icon.ico" />
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<script>
function disp_confirm1(pText_)  {
    ok_ = 1;

	if (ok_) {
		var r=confirm(pText_)
		if (r==true) {
			//document.write("You pressed OK!")
			document.getElementById("brotc40").action = "brotc41.php";
			document.getElementById("brotc40").submit();
			return 1;
		}
	} else {
		//document.write("You pressed Cancel!")
		return 0;
	}
}
</script>
<body>
<form id="brotc40" action="" method=post>

<?php
   include("../../../config/common.php");
   //include("common3.php");
   include("../../../config/koneksimysqli_it.php");
   
   session_start();

   if (empty($_SESSION['IDCARD'])) {
      echo 'not authorized';
      exit;
   } else {
		
		$srid = $_SESSION['USERID'];
		$srnama = $_SESSION['NAMALENGKAP'];
		$sr_id = substr('0000000000'.$srid,-10);
		$userid = $_SESSION['IDCARD'];
		
		$brotcid = $_GET['brotcid'];	
		$mode = $_GET['mode'];			
                
		$tahun = date('Y');
		$tahun_1 = $tahun - 1;
		$tahun_2 = $tahun + 1;
		$bulan = $bulan = date('m');
		if ($bulan=="") {
			$bulan = date('m');
		}
                $tanggal = date('d');
		if ($tanggal=="") {
			$tanggal = date('d');
		}
		$tanggal1 = $tanggal2 = $tanggal3 = date('d'); 
		$bln1 = $bulan2= $bulan3 = date('m');
		$tahun1 = $tahun2 = $tahun3 = date('Y');
		
		$checked_ = "";
		$disabled_ = "";
		$entrymode = "";
		if ($entrymode=='D') {
			$disabled_ = "disabled";
		}
		
		//ambil data
		$query = "select * from hrd.br_otc where brOtcId='$brotcid'"; 
		$result = mysqli_query($cnit, $query);
		$num_results = mysqli_num_rows($result);
		if ($num_results) {
			$row = mysqli_fetch_array($result);
			$brotcid = $row['brOtcId'];					
			$tglbr = $row['tglbr'];		
			$keterangan1 = $row['keterangan1'];
			$keterangan2 = $row['keterangan2']; 
			$jumlah = $row['jumlah']; 
			$icabangid_o = $row['icabangid_o']; 
			$noslip  = $row['noslip']; 	  	
			$real1  = $row['real1'];
			$tglreal = $row['tglreal'];
			$tgltrans = $row['tgltrans'];
			$bralid = $row['bralid'];
			$jumlah1 = $row['realisasi'];
		}
		//echo"$bralid";
		
		//echo"$tglreal";

		if ($tglreal<>'0000-00-00') {
			$tanggal1 = substr($tglreal,-2,2);
			$bln1 = substr($tglreal,5,2); 
			$tahun1 = substr($tglreal,0,4); 
			$tahun_1 = $tahun1 - 1;
			$tahun_2 = $tahun1 + 1;
			}
			
		else {
			$tahun_1 = $tahun1 - 1;
			$tahun_2 = $tahun1 + 1;
		
		}
		
		$nama_cab = '';
		$query_cab = "select nama from MKT.icabang_o where icabangid_o='$icabangid_o'";
		$result_cab = mysqli_query($cnit, $query_cab);
		$num_results_cab = mysqli_num_rows($result_cab);
		if ($num_results_cab) {
			 $row_cab = mysqli_fetch_array($result_cab);
			 $nama_cab = $row_cab['nama'];
		}

		if($icabangid_o == 'PM_PARASOL'){
			$nama_cab = 'PM - PARSOL';
		}elseif($icabangid_o == 'PM_CARMED'){
			$nama_cab = 'PM - CARMED';
		}elseif($icabangid_o == 'PM_MELANOX'){
			$nama_cab = 'PM - MEALNOX';
		}elseif($icabangid_o == 'PM_ANCEMED'){
			$nama_cab = 'PM - ACNE MED';
		}elseif ($icabangid_o=='JKT_MT') {
			$namacab = 'JAKARTA - MT';
		}elseif ($icabangid_o=='JKT_RETAIL') {
			$namacab = 'JAKARTA - RETAIL';
		}else{
			$nama_cab = 'PM - LANORE';
		}
		
		echo "<table>";
		echo '<tr>';
		echo "<td align=right>Tanggal BR :</td>";
		echo "<td>$tglbr</td>";
		echo '</tr>';
		echo '<tr>';
		echo "<td align=right>Cabang :</td>";
		echo "<td>$nama_cab</td>";
		echo '</tr>';
		echo '<tr>';
		echo "<td align=right>No Slip :</td>";
		echo "<td>$noslip</td>";
		echo '</tr>';
		echo '<tr>';
		echo "<td align=right>Keterangan :</td>";
		echo "<td>$keterangan1 $keterangan2</td>";
		echo '</tr>';
		echo '<tr>';
		echo "<td align=right>Realisasi :</td>";
		echo "<td>$real1</td>";
		echo '</tr>';
		echo '<tr>';
		echo "<td align=right>Jumlah Transfer:</td>";
		echo "<td>".number_format($jumlah,0)."</td>";	  
		echo '</tr>';	  
		/*echo '<tr>';
		echo "<td align=right>Tanggal Terima :</td>";
		echo "<td><input type=text id='tgltrm' name='tgltrm' maxlength=10 size=12 value='$tgltrm'></td>";
		echo '</tr>';
		*/
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
		echo "<option selected='selected' value='$tahun1'>$tahun1</option>";
		echo "<option value='$tahun_2'>$tahun_2</option>";
		echo '</select>';		
		echo '</td>';		  
		echo '</tr>';
		
		echo '<tr>';
		echo '<td align=right>Jumlah Realisasi :</td>';
		echo '<td><input type="text" id="jumlah1" name="jumlah1" value="'.number_format($jumlah1,0,'','').'" size=10 maxlength=10></<td>';
		echo '</tr>';
		
		echo '<tr>';
		echo '<td align=right>No Slip :</td>';
		echo "<td><input type=text id=noslip name=noslip value=$noslip size=10 maxlength=10></<td>";
		echo '</tr>';
		
		echo "</table>";
                $set_focus="";
		echo "<input type=hidden id='set_focus' name='set_focus' value=".$set_focus."></td>";	  	  
		echo "<SCRIPT LANGUAGE='javascript'>\n";
		echo "   set_focus('$set_focus');\n";
		echo "</SCRIPT>\n";
	  
	}  // if (empty($_SESSION['srid'])) 
	echo "<br>";
	
	echo "<input type=button id=cmdSave name=cmdSave value='Save' onclick='disp_confirm1(\"Simpan ?\")'>";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button id=cmdReset name=cmdReset value='Reset' onclick='click_reset()'>";
		  
	echo "<input type=hidden id='entrymode' name='entrymode' value='$entrymode'>";
	echo "<input type=hidden id='brotcid' name='brotcid' value='$brotcid'>";
	echo "<input type=hidden id='tgltrans' name='tgltrans' value='$tgltrans'>";
	echo "<input type=hidden id='tglbr' name='tglbr' value='$tglbr'>";
	echo "<input type=hidden id='mode' name='mode' value='$mode'>";
	echo "<input type=hidden id='icabangid_o' name='icabangid_o' value='$icabangid_o'>";
	echo "<input type=hidden id='bralid' name='bralid' value='$bralid'>";
	
	
?>
 
</form>
</body>
</html>
