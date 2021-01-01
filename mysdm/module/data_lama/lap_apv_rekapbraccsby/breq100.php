<html>
<head>
  <title>Budget Request</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <link rel="shortcut icon" href="../../../images/icon.ico" />
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<script src="breq6.js">
</script>
<script src="breq5.js">
</script>
<script src="breq9.js">
</script>
<body>
<form id="breq100" action="" method=post>

<?php
	include("../../../config/koneksimysqli_it.php");
	include_once("../../../config/common.php");
   //include("common3.php");
   session_start();

   if (empty($_SESSION['USERID'])) {
      echo 'not authorized';
      exit;
   } else {
		$srid = $_SESSION['USERID'];
		$srnama = $_SESSION['NAMALENGKAP'];
		$sr_id = substr('0000000000'.$srid,-10);
		$userid = $_SESSION['IDCARD'];
		$tahun = date('Y');
		$tahun_1 = $tahun - 1;
		$tahun_2 = $tahun + 1;
		$bulan = $_GET['bulan1'];
		if ($bulan=="") {
			$bulan = date('m');
		}
                $tanggal="";
		if ($tanggal=="") {
			$tanggal = date('d');
		}
		$tanggal1 = $tanggal2 = $tanggal3 = date('d'); 
		$bln1 = $bulan2= $bulan3 = date('m');
		$tahun1 = $tahun2 = $tahun3 = date('Y');

		$brid = $_GET['brid'];		
		$karyawanid = "";//$_POST['karyawanid'];
		$icabangid = "";//$_POST['icabangid'];
		$divprodid = "";//$_POST['divprodid'];
		$kodeid = "";//$_POST['kodeid'];
		
		
		
		
        //setfocus
		$set_focus = "";//$_POST['set_focus'];
                
		if ($set_focus=="") {		    
			$set_focus = "divprodid";
		}
		$mode = $_GET['mode'];// echo"$mode";
		$entrymode = $_GET['entrymode'];
		//echo "fff".$entrymode;
		if ($entrymode=='E') {
			echo "<big><b>View/Edit Budget Request</b></big><br /><br />";		 
				
		}
		if ($entrymode=='A' or $entrymode=="") {
			echo "<big><b>Isi Budget Request Promosi, Simposium, Daerah/HO, OTC, Iklan Melanox, Iklan Ethical</b></big><br /><br />";			
		}
		$checked_ = "";
		$disabled_ = "";
		if ($entrymode=='D') {
			$disabled_ = "disabled";
		}
		
                $jumlah0="";
                $checked2_="";
                $checked1_ ="";
                $namadkt="";
                
                
		if ($brid=="") {
		} else {
			//$brid = $_GET['brid'];		
			//ambil data
			$query = "select * from hrd.br0 where brid='$brid'";
			$result = mysqli_query($cnit, $query);
			$num_results = mysqli_num_rows($result);
			if ($num_results) {
				$row = mysqli_fetch_array($result);	
				$icabangid = $row['icabangid'];			
				$divprodid = $row['divprodid'];
				$brid = $row['brId'];			
				$tgl = $row['tgl'];
				$kodeid = $row['kode']; 	
				$aktiv1 = $row['aktivitas1'];
				$aktiv2 = $row['aktivitas2'];
				$ccyid = $row['ccyId'];
				$jumlah = $row['jumlah'];
				$real1 = $row['realisasi1'];
				$karyawanid = $row['karyawanId'];
				$noslip = $row['noslip'];
				$lampiran = $row['lampiran']; //echo"$lampiran";
				$tgltrans = $row['tgltrans'];
				$trmskid = $row['trmskid'];
				$ca = $row['ca']; //echo"$ca";
				$via = $row['via']; //echo"$via";
				$tglacc = $row['tglacc']; 
				$user1  = $row['user1']; 	  							
				
				if ($lampiran=='Y') {
					$checked_ = "checked";
				}
				
				if ($ca=='Y') {
					$checked1_ = "checked";
				}
				
				if ($via=='Y') {
					$checked2_ = "checked";
				}
			}
		}	
		
		if ($tgl<>'') {
			$tanggal1 = substr($tgl,-2,2);
			$bln1 = substr($tgl,5,2); 
			$tahun1 = substr($tgl,0,4); 
			$tahun_1 = $tahun1 - 1;
			$tahun_2 = $tahun1 + 1;
		}
	
		if ($tgltrans<>'') {
			$tanggal2 = substr($tgltrans,-2,2); 
			$bulan2 = substr($tgltrans,5,2); 
			$tahun2 = substr($tgltrans,0,4);
			$tahun_1 = $tahun2 - 1;
			$tahun_2 = $tahun2 + 1;
		}
		
		if ($tglacc<>'') {
			$tanggal3 = substr($tglacc,-2,2);
			$bulan3 = substr($tglacc,5,2); 
			$tahun3 = substr($tglacc,0,4);
			$tahun_1 = $tahun3 - 1;
			$tahun_2 = $tahun3 + 1;
			
		}
		
		//echo"$tanggal3/$bulan3/$tahun_1/$tahun_2/$tahun3";
		echo "<table>";
		$query = "select divprodid,nama from MKT.divprod where br='Y' order by nama";
		$result = mysqli_query($cnit, $query);
		$num_results = mysqli_num_rows($result);
		echo '<tr>';
		echo '<td align=right>Divisi :</td>'; 	  	  
	    echo "<td><select name=\"divprodid\" id='divprodid' onchange=\"showKode(this.value)\" $disabled_>";
	    echo '<option value="blank">(Blank)</option>';
	    for ($i=0; $i < $num_results; $i++) {
			$row = mysqli_fetch_array($result);
			$str_ = $row['nama'];		
			if ($divprodid == $row['divprodid']) {
				echo '<option selected="selected" value="'.$row['divprodid'].'">'.$str_.'</option>';		
			} else {
				echo '<option value="'.$row['divprodid'].'">'.$str_.'</option>';		
			}
		}  //end for
		echo '</select></td>';	  
		echo '</tr>';
		
		echo "<tr>";
		if ($entrymode=='E') {
			$query = "select kodeid,nama from hrd.br_kode where kodeid='$kodeid'"; 
			$result = mysqli_query($cnit, $query);
			$num_results = mysqli_num_rows($result);
			echo "<td align=right>Kode :</td>";
			echo "<td><select name=\"kodeid\" id=\"kodeid\" $disabled_>";
			for ($i=0; $i < $num_results; $i++)
			{
				$row = mysqli_fetch_array($result);
				$str_ = $row['nama'];		
				if ($kodeid == $row['kodeid']) {
					echo '<option selected="selected" value="'.$row['kodeid'].'">'.$str_.'</option>';		
				} else {
					echo '<option value="'.$row['kodeid'].'">'.$str_.'</option>';		
				}
			}
			echo '</select></td>';	  
		} else {
			echo "<td align=right>Kode :</td>";
			echo "<td><select name=\"kodeid\" id=\"kodeid\" $disabled_ onchange=\"show_nourut()\">";	
			echo "</select></td>"; 
		}
		echo "</tr>";	
		
		$query = "select karyawanid,nama from hrd.karyawan order by nama";
		$result = mysqli_query($cnit, $query);
		$num_results = mysqli_num_rows($result);
		echo '<tr>';
		echo '<td align=right>Yang membuat BR :</td>'; 	  	  
	    echo "<td><select name=\"karyawanid\" id='karyawanid' onchange=\"showCabang(this.value)\" $disabled_>";
	    echo '<option value="blank">(Blank)</option>';
	    for ($i=0; $i < $num_results; $i++)
	    {
			$row = mysqli_fetch_array($result);
			$str_ = $row['nama'];		
			if ($karyawanid == $row['karyawanid']) {
				echo '<option selected="selected" value="'.$row['karyawanid'].'">'.$str_.'</option>';		
			} else {
				echo '<option value="'.$row['karyawanid'].'">'.$str_.'</option>';		
			}
		}
		echo '</select></td>';	  
		echo '</tr>';
		
		$bulan = substr($tanggal,0,7);
		echo '<input type="hidden" name="bulan" id="bulan" value="'.$bulan.'">';
		
		echo "<tr>";
		if ($entrymode=='E') {
			$query = "select icabangid,nama from MKT.icabang where icabangid='$icabangid'"; 
			$result = mysqli_query($cnit, $query);
			$num_results = mysqli_num_rows($result);
			echo "<td align=right>Cabang :</td>";
			echo "<td><select name=\"icabangid\" id=\"icabangid\" $disabled_>";
			for ($i=0; $i < $num_results; $i++)
			{
				$row = mysqli_fetch_array($result);
				$str_ = $row['nama'];		
				if ($icabangid == $row['icabangid']) {
					echo '<option selected="selected" value="'.$row['icabangid'].'">'.$str_.'</option>';		
				} else {
					echo '<option value="'.$row['icabangid'].'">'.$str_.'</option>';		
				}
			}
			echo '</select></td>';	  
		} else {
			echo "<td align=right>Cabang :</td>";
			echo "<td><select name=\"icabangid\" id=\"icabangid\" $disabled_>";	
			echo "</select></td>"; 
		}
		echo "</tr>";
		
		echo '<tr>';
		echo '<td align=right>Tanggal BR :</td>';
		echo "<td><select name='tanggal1' id='tanggal1' $disabled_>";
		echo '<option value="00">-</option>';
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
		echo '<option value="00">-</option>';
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
		
		if ($entrymode == 'E') {
			if ($tahun1=="0000") {
				$tahun1_ = '-';
			} else {
				$tahun1_ = $tahun1;
			}
			
			echo "&nbsp;&nbsp;<select name='tahun1' id='tahun1' $disabled_>";
			echo '<option value="0000">-</option>';
			$tahun_1 = $tahun - 1;
			echo "<option value='$tahun_1'>$tahun_1</option>";
			echo "<option selected='selected' value='$tahun1'>$tahun1_</option>";
			$tahun_2 = $tahun;
			echo "<option value='$tahun_2'>$tahun_2</option>";
		} else {
			echo "&nbsp;&nbsp;<select name='tahun1' id='tahun1' $disabled_>";
			echo '<option value="0000">-</option>';
			echo "<option value='$tahun_1'>$tahun_1</option>";
			echo "<option selected='selected' value='$tahun'>$tahun</option>";
			echo "<option value='$tahun_2'>$tahun_2</option>";
			echo '</select>';		
			echo '</td>';		  
			echo '</tr>';
		}
		
		echo '<tr>';	  	  	  
		echo '<td align=right>Aktivitas :</td>';	  
		echo "<td><input type=text id='aktiv1' name='aktiv1' maxlength=50 size=52 value='$aktiv1' $disabled_></td>";	  	 
		echo '</tr>';
		
		echo '<tr>';
		echo '<td align=right></td>';	  
		echo "<td><input type=text id='aktiv2' name='aktiv2' maxlength=50 size=52 value='$aktiv2' $disabled_></td>";	  	  
		echo '</tr>';
		
		$query = "select * from hrd.ccy";
		$result = mysqli_query($cnit, $query);
		$num_results = mysqli_num_rows($result);
		echo '<tr>';
		echo '<td align=right>Ccy. :</td>';
		echo "<td><select name='ccyid' id='ccyid' $disabled_>";
		echo '<option value="Blank">(Blank)</option>';
		for ($i=0; $i < $num_results; $i++)
		{
			$row = mysqli_fetch_array($result);
			$str_ = $row['nama'];					
			if ($ccyid == $row['ccyId']) {
				echo '<option selected="selected" value="'.$row['ccyId'].'">'.$str_.'</option>';		
			} else {
				echo '<option value="'.$row['ccyId'].'">'.$str_.'</option>';
			}
			
		}
		echo '</select></td>';	  
		echo '</tr>';
		echo '<tr>';
		echo "<td align=right>Jumlah :</td>";
		echo "<td><input type=text id=\"jumlah0\" name=\"jumlah0\" onBlur=\"this.value=say_it(this.value,'jumlah')\" 
		                onfocus = \"this.select()\"
		                validchars=\"0123456789.\" onkeypress=\"return allowChars(this,event)\"
						maxlength=15 size=17 value=\"".number_format($jumlah,0)."\" $disabled_>";
		echo "<input type=text align=\"right\" id=\"jumlah\" name=\"jumlah\" value=\"\" size=17 disabled></td>";	  	  
		echo '</tr>';

		echo '<tr>';
		echo '<td align=right>Realisasi :</td>';
		echo "<td><input type=text id='real1' name='real1' maxlength=30	size=21 value='$real1' $disabled_></td>";
		echo '</tr>';
		
		echo '<tr>';
		echo '<td align=right>No. Slip :</td>';
		echo "<td><input type=text id='noslip' name='noslip' maxlength=15 size=17 value='$noslip' $disabled_></td>";	  
		echo '</tr>';	  
/*
		echo '<tr>';
		echo '<td align=right>Tgl ACC :</td>';
		echo "<td><select name='tanggal3' id='tanggal3' $disabled_>";
		echo '<option value="00">-</option>';
		for ($i=1; $i<32; $i++) {
			$i_ = substr('0'.$i,-2);		
			if ($i == $tanggal3) {
				echo "<option selected='selected' value='$i_'>$i_</option>";	
			} else {
				echo "<option value='$i_'>$i_</option>";					
			}
		}		
		echo '</select>';
	
		echo "&nbsp;&nbsp;<select name='bulan3' id='bulan3' $disabled_>";
		echo '<option value="00">-</option>';
		for ($i=0; $i<12; $i++) {
			$j = '0'.ltrim(strval($i+1));
			$j = substr($j,-2,2);
			$bln_ = nama_bulan($j);
			if ($j == $bulan3) {
				echo "<option selected='selected' value='$j'>$bln_</option>";	
			} else {
				echo "<option value='$j'>$bln_</option>";					
			}
		}		
		echo '</select>';

		echo "&nbsp;&nbsp;<select name='tahun3' id='tahun3' $disabled_>";
		echo '<option value="0000">-</option>';
		echo "<option value='$tahun_1'>$tahun_1</option>";
		echo "<option selected='selected' value='$tahun'>$tahun</option>";
		echo "<option value='$tahun_2'>$tahun_2</option>";
		echo '</select>';		
		echo '</td>';	
		echo '</tr>';
		*/
		echo '<tr>';
		echo '<td align=right>Tgl Transfer :</td>';
		echo "<td><select name='tanggal2' id='tanggal2' $disabled_>";
		echo '<option value="00">-</option>';
		for ($i=1; $i<32; $i++) {
			$i_ = substr('0'.$i,-2);		
			if ($i == $tanggal2) {
				echo "<option selected='selected' value='$i_'>$i_</option>";	
			} else {
				echo "<option value='$i_'>$i_</option>";					
			}
		}		
		echo '</select>';
	
		echo "&nbsp;&nbsp;<select name='bulan2' id='bulan2' $disabled_>";
		echo '<option value="00">-</option>';
		for ($i=0; $i<12; $i++) {
			$j = '0'.ltrim(strval($i+1));
			$j = substr($j,-2,2);
			$bln_ = nama_bulan($j);
			if ($j == $bulan2) {
				echo "<option selected='selected' value='$j'>$bln_</option>";	
			} else {
				echo "<option value='$j'>$bln_</option>";					
			}
		}		
		echo '</select>';

		if ($entrymode == 'E') {
			if ($tahun2=="0000") {
				$tahun2_ = '-';
			} else {
				$tahun2_ = $tahun2;
			}
			
			echo "&nbsp;&nbsp;<select name='tahun2' id='tahun2' $disabled_>";
			echo '<option value="0000">-</option>';
			$tahun_1 = $tahun - 1;
			echo "<option value='$tahun_1'>$tahun_1</option>";
			echo "<option selected='selected' value='$tahun2'>$tahun2_</option>";
			$tahun_2 = $tahun;
			echo "<option value='$tahun_2'>$tahun_2</option>";
		} else {
			echo "&nbsp;&nbsp;<select name='tahun2' id='tahun2' $disabled_>";
			echo '<option value="0000">-</option>';
			echo "<option value='$tahun_1'>$tahun_1</option>";
			echo "<option selected='selected' value='$tahun'>$tahun</option>";
			echo "<option value='$tahun_2'>$tahun_2</option>";
		}
	
		echo '</select>';		
		echo '</td>';	
		echo '</tr>';

		echo '<tr>';
		echo '<td align=right>Lampiran :</td>';
		echo "<td><input type='checkbox' id='chklamp' name='chklamp' $checked_ $disabled_>";
		echo "&nbsp;&nbsp;&nbsp;CA : <input type='checkbox' id='chklamp1' name='chklamp1' $checked1_ $disabled_>";
		echo "&nbsp;&nbsp;&nbsp;Via Surabaya : <input type='checkbox' id='chklamp2' name='chklamp2' $checked2_ $disabled_></td>";
		echo '</tr>';
	  	  
		echo "</table>";
	   
		echo "<input type=hidden id='set_focus' name='set_focus' value=".$set_focus."></td>";	  	  
		echo "<SCRIPT LANGUAGE='javascript'>\n";
		echo "   set_focus('$set_focus');\n";
		echo "</SCRIPT>\n";
	  
	}  // if (empty($_SESSION['srid'])) 
	echo "<br>";
	
	if ($entrymode=='D') {
   	    echo "<input type=button id=cmdDel name=cmdDel value='Delete' onclick='disp_hapus(\"Hapus ?\")'>";
	    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button id=cmdCancel name=cmdCancel value='Cancel' onclick='hapus_cancel()'>";
		echo "<input type=hidden id='divprodid' name='divprodid' value='$divprodid'>";
		//echo "<input type=hidden id='bulan' name='bulan' value='$bulan'>";
		echo "<input type=hidden id='kodeid' name='kodeid' value='$kodeid'>";
	} else {
		echo "<input type=button id=cmdSave name=cmdSave value='Save' onclick='disp_confirm(\"Simpan ?\")'>";
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button id=cmdReset name=cmdReset value='Reset' onclick='click_reset()'>";
	}	  
	echo "<input type=hidden id='entrymode' name='entrymode' value='$entrymode'>";
	echo "<input type=hidden id='mode' name='mode' value='$mode'>";
	echo "<input type=hidden id='brid' name='brid' value='$brid'>";
	//echo "<input type=hidden id='namadkt' name='namadkt' value='$namadkt'>";
	echo "<input type=hidden id='jumlah_' name='jumlah_' value='$jumlah'>";
	
	
	if (empty($_SESSION['srid'])) {
	} else {
		do_show_menu($_SESSION['jabatanid'],'N');
	}
?>
 <?  ?>
</form>
</body>
</html>
