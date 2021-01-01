<html>
<head>
  <title>INPUT BR OTC</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <link rel="shortcut icon" href="../../../images/icon.ico" />
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<script>
function disp_confirm(pText_)  {
    ok_ = 1;

	if (ok_) {
		var r=confirm(pText_)
		if (r==true) {
			//document.write("You pressed OK!")
			document.getElementById("brotc00").action = "brotc01.php";
			document.getElementById("brotc00").submit();
			return 1;
		}
	} else {
		//document.write("You pressed Cancel!")
		return 0;
	}
}

function click_reset() {
	document.getElementById("brotc00").action = "brotc00.php";
	document.getElementById("brotc00").submit();
}

function simpan_cancel()  {
	//document.getElementById("ikary00").action = "ikary21.php";
	//document.getElementById("ikary00").submit();
	window.history.back();

}
function disp_hapus(pText_)  {
    ok_ = 1;
	if (ok_) {
		var r=confirm(pText_)
		if (r==true) {
			//document.write("You pressed OK!")
			document.getElementById("brotc00").action = "brotc02.php";
			document.getElementById("brotc00").submit();
			return 1;
		}
	} else {
		//document.write("You pressed Cancel!")
		return 0;
	}
}
function hapus_cancel()  {
	//document.getElementById("ikary00").action = "ikary21.php";
	//document.getElementById("ikary00").submit();
	window.history.back();

}
</script>
<script type="text/javascript" src="/js/jquery-1.6.js" ></script>
<body>
<form id="brotc00" action="brotc01.php" method=post>

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
		$tahun = date('Y');
		$tahun_1 = $tahun - 1;
		$tahun_2 = $tahun + 1;
		$bulan = date('m');
		if ($bulan=="") {
			$bulan = date('m');
		}
                $tanggal = date('d');
		if ($tanggal=="") {
			$tanggal = date('d');
		}

		$brotcid = $_GET['brotcid'];
                $kodeid="";
		//$kodeid = $_POST['kodeid']; 
                if (isset($_GET['periode'])) {
                    $per1 = $_GET['periode'];		
                    $per2 = $_GET['periode']; 
                }else{
                    $per1 = $_GET['per1'];		
                    $per2 = $_GET['per2']; 
                }
                
		$tanggal1 = $tanggal2 = $tanggal3 = date('d'); 
		$bulan1 = $bulan2= $bulan3 = date('m');
		$tahun1 = $tahun2 = $tahun3 = date('Y');
                $set_focus="";
		//$set_focus = $_POST['set_focus'];
		if ($set_focus=="") {		    
			$set_focus = "karyawanid";
		}
		$mode = $_GET['mode'];
		$entrymode = $_GET['entrymode'];
		if ($entrymode=='E') {
			echo "<big><b>VIEW/EDIT BR OTC</big><br/><br/>";		 
		}
		if ($entrymode=='A' or $entrymode=="") {
			echo "<big><b>INPUT BUDGET REQUEST OTC</big><br /><br />";			
		}
		$checked_ = "";
		$disabled_ = "";
		if ($entrymode=='D') {
			$disabled_ = "disabled";
		}
                $disabled = "";
                $checked1_  = "";
                $checked2_  = "";
                $checked3_  = "";
                $checked4_  = "";
                $checked5_  = "";
                $jumlah2  = 0;
//echo"$entrymode//$mode//$per1/$per2";
		if ($brotcid=="") {
		} else {
			//ambil data
			$query = "select * from hrd.br_otc where brOtcId='$brotcid'"; 
			// echo $query;

			$result = mysqli_query($cnit, $query);
			$num_results = mysqli_num_rows($result);
			if ($num_results) {
				$row = mysqli_fetch_array($result);
				$brotcid = $row['brOtcId'];					
				$kodeid = $row['kodeid'];
				$posting = $row['subpost'];
			//	$nobr = $row['nobr'];
				$tglbr = $row['tglbr'];		
				$keterangan1 = $row['keterangan1'];
				$keterangan2 = $row['keterangan2']; 
				$jumlah = $row['jumlah']; 
				$jenis = $row['jenis']; 
				$icabangid_o = $row['icabangid_o']; 
				$tgltrans = $row['tgltrans']; 
				$tglrpsby = $row['tglrpsby']; 
				$noslip  = $row['noslip'];
                $bankreal1 = $row['bankreal1'];
                $cbreal1 = $row['cbreal1'];
                $norekreal1 = $row['norekreal1'];
				$user1  = $row['user1']; 
				$ccyid  = $row['ccyId']; //echo"$ccyid";
				$bralid  = $row['bralid'];
				$real1  = $row['real1'];
				$via = $row['via'];
				$ca = $row['ca'];
				$lampiran = $row['lampiran'];
				
				if ($lampiran=='Y') {
					$checked_ = "checked";
				}
				
				if ($ca=='Y') {
					$checked1_ = "checked";
				}
				
				if ($via=='Y') {
					$checked2_ = "checked";
				}
				
				if ($jenis=='A') {
					$checked3_ = "checked";
				} else {
					if ($jenis=='K') {
						$checked4_ = "checked";
					} else {
						if ($jenis=='S') {
							$checked5_ = "checked";
						} else {
						}
					}
				}
				if ($via=='Y') {
					$checked2_ = "checked";
				}
			}
		}	
		
		
		if ($tglbr<>'') {
			$tanggal1 = substr($tglbr,-2,2);
			$bulan1 = substr($tglbr,5,2); 
			$tahun1 = substr($tglbr,0,4); 
			$tahun_1 = $tahun1 - 1;
			$tahun_2 = $tahun1 + 1;
			$tahun1 = $tahun1;
		}
	
		//echo"$icabangid_o//$kodeid//$tahun_2<br>";
		
		echo "<table>";
		
		echo "<tr>";
		$query = "select icabangid_o,nama from MKT.icabang_o order by nama";
		$result = mysqli_query($cnit, $query);
		$num_results = mysqli_num_rows($result);
		echo "<td align=right>Cabang SDM:</td>";
		if (($icabangid_o=='HO') or ($icabangid_o=='MD' or $icabangid_o=='PM_LANORE' or $icabangid_o=='PM_PARASOL' or $icabangid_o=='PM_MELANOX' or $icabangid_o=='PM_ACNEMED' or $icabangid_o=='JKT_MT' or $icabangid_o=='JKT_RETAIL')) {
			echo "<td>";
			echo "<select name=\"icabangid_o\" id=\"icabangid_o\" $disabled_>";
			if ($icabangid_o=='HO') {
				echo '<option value="HO">HO</option>';
			}elseif($icabangid_o=='MD') {
				echo '<option value="MD">MD</option>';
			}elseif($icabangid_o=='PM_CARMED') {
				echo '<option value="PM_CARMED">PM - CARMED</option>';
			}elseif($icabangid_o=='PM_PARASOL') {
				echo '<option value="PM_PARASOL">PM - PARASOL</option>';
			}elseif($icabangid_o=='PM_MELANOX') {
				echo '<option value="PM_MELANOX">PM - MELANOX</option>';
			}elseif($icabangid_o=='PM_LANORE') {
				echo '<option value="PM_LANORE">PM - LANORE</option>';
			}elseif($icabangid_o=='PM_ACNEMED') {
				echo '<option value="PM_ACNEMED">PM - ACNE MED</option>';
			}elseif($icabangid_o=='JKT_MT') {
				echo '<option value="JKT_MT">JKT - MT</option>';
			}else{
				echo '<option value="JKT_RETAIL">JAKARTA - RETAIL</option>';
			}
			echo '<option value="blank">(Blank)</option>';
			
		}else{
			echo "<td><select name=\"icabangid_o\" id=\"icabangid_o\" $disabled_>";
			echo '<option value="blank">(Blank)</option>';
			echo '<option value="HO">HO</option>';
			echo '<option value="MD">MD</option>';
			echo '<option value="PM_CARMED">PM - CARMED</option>';
			echo '<option value="PM_MELANOX">PM - MELANOX</option>';
			echo '<option value="PM_LANORE">PM - LANORE</option>';
			echo '<option value="PM_ACNEMED">PM - ACNE MED</option>';
			echo '<option value="PM_PARASOL">PM - PARASOL</option>';
			echo '<option value="JKT_MT">JAKARTA - MT</option>';
			echo '<option value="JKT_RETAIL">JAKARTA - RETAIL</option>';
		}

		for ($i=0; $i < $num_results; $i++) {
			$row = mysqli_fetch_array($result);
			$str_ = $row['nama'];		
			if ($icabangid_o == $row['icabangid_o']) {
				echo '<option selected="selected" value="'.$row['icabangid_o'].'">'.$str_.'</option>';		
			} else {
				echo '<option value="'.$row['icabangid_o'].'">'.$str_.'</option>';		
			}
		}
		echo '</select></td>';	
		echo '</tr>';
		
		$query = "select bralid,nama from hrd.bral_otc  order by nama"; 
		$result = mysqli_query($cnit, $query);
		$num_results = mysqli_num_rows($result);
		echo "<td align=right>Alokasi BR :</td>";
		echo "<td><select name=\"bralid\" id=\"bralid\" $disabled_>";
		echo '<option value="blank">(Blank)</option>';
		for ($i=0; $i < $num_results; $i++) {
			$row = mysqli_fetch_array($result);
			$str_ = $row['nama'];		
			if ($bralid == $row['bralid']) {
				echo '<option selected="selected" value="'.$row['bralid'].'">'.$str_.'</option>';		
			} else {
				echo '<option value="'.$row['bralid'].'">'.$str_.'</option>';		
			}
		}

		echo '</select></td>';	  
		echo "</tr>";	

		echo "
			<tr>
				<td align=right>Posting :</td>
				<td>
					<select name='subpost' id='subpost' onchange='jvChangePosting();'>
					<option value='blank'>(Blank)</option>
		";

		// id posting
		//if($_GET['id_posting']){
		//	$posting = $_GET['id_posting'];
		//}

		// onchange='jvChangePosting();'
		$query = "select distinct subpost,nmsubpost from hrd.brkd_otc where aktif='Y' order by subpost"; 
		$result = mysqli_query($cnit, $query);
		$num_results = mysqli_num_rows($result);
		for ($i=0; $i < $num_results; $i++) {
			$row = mysqli_fetch_array($result);
			$str_ = $row['nmsubpost'];
			if ($posting == $row['subpost']) {
				echo '<option selected="selected" value="'.$row['subpost'].'">'.$str_.'</option>';		
			} else {
				echo '<option value="'.$row['subpost'].'">'.$str_.'</option>';		
			}
		}

		echo "
					</select>
				</td>
			</tr>
		";

		echo "
			<script type='text/javascript'>
				$(document).ready(function(){
					// jvChangePosting();
				});

				function jvChangePosting(){
					var posting = $('#subpost').val();
		            $.ajax({
		                method:'POST',
		                url:'brotc00.php?".$_SERVER['QUERY_STRING']."',
		                data:{id_posting:posting},
		                success:function(result){
		                    $('body').html(result);
		                }
		            });
				}
			</script>
		";

		// id posting
		//if($_GET['id_posting']){
		//	$id_posting = $_GET['id_posting'];
		//}else{
			$id_posting = $posting;
		//}
		
		echo "<td align=right>Sub-Posting :</td>";
		echo "<td><select name=\"kodeid\" id=\"kodeid\">";
		echo '<option value="blank">(Blank)</option>';
			// $query = "select kodeid,nama from brkd_otc where aktif='Y' order by nama"; 
			$query = "select kodeid,nama from hrd.brkd_otc where subpost = '".$id_posting."' and aktif='Y' order by nama";
			$result = mysqli_query($cnit, $query);
			$num_results = mysqli_num_rows($result);
			for ($i=0; $i < $num_results; $i++) {
				$row = mysqli_fetch_array($result);
				$str_ = $row['nama'];
				if ($kodeid == $row['kodeid']) {
					echo '<option selected="selected" value="'.$row['kodeid'].'">'.$str_.'</option>';		
				} else {
					echo '<option value="'.$row['kodeid'].'">'.$str_.'</option>';		
				}
			}
		echo '</select></td>';	  
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
	
		echo "&nbsp;&nbsp;<select name='bulan1' id='bulan1' $disabled_>";
		echo '<option value="00">-</option>';
		for ($i=0; $i<12; $i++) {
			$j = '0'.ltrim(strval($i+1));
			$j = substr($j,-2,2);
			$bln_ = nama_bulan($j);
			if ($j == $bulan1) {
				echo "<option selected='selected' value='$j'>$bln_</option>";	
			} else {
				echo "<option value='$j'>$bln_</option>";					
			}
		}		
		echo '</select>';

			//echo $tahun_1.'-'.$tahun1.'-'.$tahun_2;
			
		echo "&nbsp;&nbsp;<select name='tahun1' id='tahun1' $disabled_>";
		echo '<option value="0000">-</option>';
		echo "<option value='$tahun_1'>$tahun_1</option>";
		echo "<option selected='selected' value='$tahun1'>$tahun1</option>";
		echo "<option value='$tahun_2'>$tahun_2</option>";
		echo '</select>';		
		echo '</td>';		  
		echo '</tr>';	
		
		echo '<tr>';	  	  	  
		echo '<td align=right>Keterangan Tempat :</td>';
		echo "<td><textarea id='keterangan1' name='keterangan1' maxlength=255 size=120 rows=3 cols=70 $disabled>$keterangan1</textarea></td>";
		echo '</tr>';

		echo '<tr>';	  	  	  
		echo '<td align=right>Keterangan :</td>';
		echo "<td><textarea id='keterangan2' name='keterangan2' maxlength=255 size=120 rows=3 cols=70 $disabled>$keterangan2</textarea></td>";
		// echo "<td><input type=text id='keterangan2' name='keterangan2' maxlength=120 size=120 value='$keterangan2' $disabled_></td>";
		echo '</tr>';
	    
		$query = "select * from hrd.ccy";
		$result = mysqli_query($cnit, $query);
		$num_results = mysqli_num_rows($result);
		echo '<tr>';
		echo '<td align=right>Ccy. :</td>';
		echo "<td><select name='ccyid' id='ccyid' $disabled_>";
		echo '<option value="Blank">(Blank)</option>';
		for ($i=0; $i < $num_results; $i++) {
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
		echo "<td align=right>Jumlah Usulan BR:</td>";
		echo "<td><input type=text id=\"jumlah0\" name=\"jumlah0\" onBlur=\"this.value=say_it(this.value,'jumlah')\" 
		                onfocus = \"this.select()\"
		                validchars=\"0123456789.\" onkeypress=\"return allowChars(this,event)\"
						maxlength=15 size=17 value=\"".number_format($jumlah,0)."\" $disabled_>";
		echo "&nbsp;&nbsp;&nbsp;<input type=text align=\"right\" id=\"jumlah\" name=\"jumlah\" value=\"\" size=17 disabled></td>";	  	  
		echo '</tr>';

		if ($tgltrans<>'0000-00-00') {
			//echo"tgltrans=$tgltrans//$tahun2";
			$tanggal2 = substr($tgltrans,-2,2);
			$bulan2 = substr($tgltrans,5,2); 
			$tahun2 = substr($tgltrans,0,4);
			$tahun_1 = $tahun2 - 1;
			$tahun_2 = $tahun2 + 1;
			if ($tgltrans=='') {	
				$tahun2 = date('Y');
				$tahun_1 = $tahun2 - 1;
				$tahun_2 = $tahun2 + 1;
				$bulan2 = date('m');
				$tanggal2 = date('d');
			}
			
			
		} else {
		
			$tahun2 = date('Y');
			$tahun_1 = $tahun2 - 1;
			$tahun_2 = $tahun2 + 1;
			$bulan2 = date('m');
			$tanggal2 = date('d');
		}

		if ($entrymode=='A' or $entrymode=="") {
		} else {
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

			echo "&nbsp;&nbsp;<select name='tahun2' id='tahun2' $disabled_>";
			echo '<option value="0000">-</option>';
			echo "<option value='$tahun_1'>$tahun_1</option>";
			echo "<option selected='selected' value='$tahun2'>$tahun2</option>";
			echo "<option value='$tahun_2'>$tahun_2</option>";
			echo '</select>';		
			echo '</td>';	
			echo '</tr>';
			
			echo '<tr>';	  	  	  
			echo '<td align=right>No Slip :</td>';	  
			echo "<td><input type=text id='noslip' name='noslip' maxlength=10 size=12 value='$noslip' $disabled_></td>";
			echo '</tr>';
			
		}
		
	/*
		echo '<tr>';
		echo '<td align=right>Tgl Realisasi :</td>';
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
		
		echo '<tr>';
		echo "<td align=right>Jumlah Realisasi BR:</td>";
		echo "<td><input type=text id=\"jumlah1\" name=\"jumlah1\" onBlur=\"this.value=say_it1(this.value,'jumlah2')\" 
		                onfocus = \"this.select()\"
		                validchars=\"0123456789.\" onkeypress=\"return allowChars1(this,event)\"
						maxlength=15 size=17 value=\"".number_format($jumlah2,0)."\" $disabled_>";
		echo "&nbsp;&nbsp;&nbsp;<input type=text align=\"right\" id=\"jumlah2\" name=\"jumlah2\" value=\"\" size=17 disabled></td>";	  	  
		echo '</tr>';
		*/
		echo '<tr>';
		echo '<td align=right>Realisasi :</td>';
		echo "<td><input type=text id='real1' name='real1' maxlength=30	size=21 value='$real1' $disabled_></td>";
		echo '</tr>';

        echo '<tr>';
		echo '<td align=right>Bank Realisasi :</td>';
		echo "<td><input type=text id='bankreal1' name='bankreal1' maxlength=30	size=21 value='$bankreal1' $disabled_></td>";
		echo '</tr>';

        echo '<tr>';
		echo '<td align=right>Cabang Bank :</td>';
		echo "<td><input type=text id='cbreal1' name='cbreal1' maxlength=30	size=21 value='$cbreal1' $disabled_></td>";
		echo '</tr>';

        echo '<tr>';
		echo '<td align=right>No rek Realisasi :</td>';
		echo "<td><input type=text id='norekreal1' name='norekreal1' maxlength=30	size=21 value='$norekreal1' $disabled_></td>";
		echo '</tr>';
		
		echo '<tr>';
		echo '<td align=right>Lampiran :</td>';
		echo "<td><input type='checkbox' id='chklamp' name='chklamp' $checked_ $disabled_>";
		echo "&nbsp;&nbsp;&nbsp;CA : <input type='checkbox' id='chklamp1' name='chklamp1' $checked1_ $disabled_>";
		echo "&nbsp;&nbsp;&nbsp;Via Surabaya : <input type='checkbox' id='chklamp2' name='chklamp2' $checked2_ $disabled_></td>";
		echo '</tr>';
		
		if ($entrymode=='A' or $entrymode=="") {
		} else {
			echo '<tr>';
			echo '<td align=right>Tgl Report SBY :</td>';	  
			echo "<td><input type=text id='tglrpsby' name='tglrpsby' maxlength=10 size=12 value='$tglrpsby' $disabled_></td>";	  	  
			echo '</tr>';
			
			echo '<tr>';
			echo '<td align=right>Advance :</td>';
			echo "<td><input type='checkbox' id='chkjns' name='chkjns' $checked3_ $disabled_>";
			echo "&nbsp;&nbsp;&nbsp;Klaim : <input type='checkbox' id='chkjns1' name='chkjns1' $checked4_ $disabled_>";
			echo "&nbsp;&nbsp;&nbsp;Sudah minta uang muka : <input type='checkbox' id='chkjns2' name='chkjns2' $checked5_ $disabled_></td>";
			echo '</tr>';
		}
		
		echo "</table>";
	   
		echo "<input type=hidden id='set_focus' name='set_focus' value=".$set_focus."></td>";	  	  
		echo "<SCRIPT LANGUAGE='javascript'>\n";
		echo "   set_focus('$set_focus');\n";
		echo "</SCRIPT>\n";
	  
	}  // if (empty($_SESSION['srid'])) 
	echo "<br>";
	
	if ($entrymode=='D') {
   	    echo "<input type=button id=cmdDel name=cmdDel value='Delete' onclick='disp_hapus(\"Hapus ?\")'>";
	    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=hidden id=cmdCancel name=cmdCancel value='Cancel' onclick='hapus_cancel()'>";
		echo "<input type=hidden id='icabangid_o' name='icabangid_o' value='$icabangid_o'>";
		echo "<input type=hidden id='bralid' name='bralid' value='$bralid'>";
		echo "<input type=hidden id='periode' name='periode' value='$tgltrans'>";
		echo "<input type=hidden id='periode1' name='periode1' value='$tglbr'>";
		echo "<input type=hidden id='mode' name='mode' value='$mode'>";
	} else {
		echo "<input type=button id=cmdSave name=cmdSave value='Save' onclick='disp_confirm(\"Simpan ?\")'>";
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=hidden id=cmdReset name=cmdReset value='Reset' onclick='click_reset()'>";
	}	  
	echo "<input type=hidden id='entrymode' name='entrymode' value='$entrymode'>";
	echo "<input type=hidden id='brotdid' name='brotcid' value='$brotcid'>";
	echo "<input type=hidden id='jumlah_' name='jumlah_' value='$jumlah'>";
	echo "<input type=hidden id='jumlah1_' name='jumlah1_' value='$jumlah2'>";
	echo "<input type=hidden id='mode' name='mode' value='$mode'>";
	echo "<input type=hidden id='per1' name='per1' value='$per1'>";
	echo "<input type=hidden id='per2' name='per2' value='$per2'>";
	
	
	if (empty($_SESSION['srid'])) {
	} else {
		do_show_menu($_SESSION['jabatanid'],'N');
	}
?>
 
</form>
</body>
</html>
