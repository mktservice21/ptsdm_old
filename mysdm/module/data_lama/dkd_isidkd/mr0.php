<html>
<head>
  <title>DKD</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<script src="mr0.js"></script>

<!-- <script type="text/javascript" src="jquery/css/ui-lightness/jquery-ui-1.10.3.custom.css" ></script> -->
<script type="text/javascript" src="../jquery/js/jquery-1.9.1.js" ></script>
<script type="text/javascript" src="../jquery/js/jquery-ui-1.10.3.custom.js" ></script>
<script type="text/javascript" src="../jquery/js/jquery-ui-1.10.3.custom.min.js" ></script>
<script type="text/javascript" src="../jquery/themes/ui-lightness/jquery.ui.datepicker.css" ></script>
<link rel="stylesheet" href="../jquery/css/ui-lightness/jquery-ui-1.10.3.custom.css" type="text/css" media="all">
<link rel="stylesheet" href="../jquery/css/ui-lightness/jquery-ui-1.10.3.custom.min.css" type="text/css" media="all">

<script>
	$(document).ready(function(){
		initDate();
	});

	function initDate(){
		$("#tgl").datepicker({
			altFormat: "d/m/yy",
			dateFormat: "yy-mm-dd",
			beforeShow: function(){
				var zindex = $("div.ui-dialog").css("z-index");
				$("#ui-datepicker-div").css("z-index", zindex + 1);
			}
		})
	}

</script>
<body>
<form id="mr0" action="" method=post>

<?php
	session_start();

	include("../../../config/koneksimysqli.php");
	include("../../../config/common.php");
	//include("../../../config/common3.php");


   if (empty($_SESSION['IDCARD'])) {
      echo 'not authorized';
      exit;
   } else {
		$srid = $_SESSION['USERID'];
		$srnama = $_SESSION['NAMALENGKAP'];
		$sr_id = substr('0000000000'.$srid,-10);
		$userid = $_SESSION['IDCARD'];
		$tanggal = date('Y-m-d');

		$srid = $_SESSION['USERID'];		
		$tgl = $tanggal;
		$tgl1 = $tanggal;
		$tgl2 = $tanggal;
		
		$dokter="";
		$jv_dokter ="";
		$compl ="";
		$compl2 ="";
		$akt ="";
		$saran ="";
		$saran2 ="";
		$ket ="";
		$ket2 ="";

		$set_focus = "";
		if ($set_focus=="") {		    
			$set_focus = "icabangid";
		}
		
		$entrymode = "";
		if ($entrymode=='E') {
			echo "<big><b>View/Edit DKD</b></big><br />";		 
		}
		if ($entrymode=='A' or $entrymode=="") {
			echo "<big><b>Isi DKD</b></big><br />";			
		}
		$checked_ = "";
		$disabled_ = "";
		if ($entrymode=='D') {
			echo "<big><b>Delete DKD</b></big><br />";
			$disabled_ = "disabled";
		}
		$srid = $_SESSION['USERID'];
	    $sr_id = substr('0000000000'.$srid,-10);
	    $query = "select nama,pin from hrd.karyawan where karyawanId='".$sr_id."'";
	    $result = mysqli_query($cnmy, $query);
	    $row = mysqli_fetch_array($result);
	    $num_results = mysqli_num_rows($result);
	    echo '<strong>'.$row['nama'].'</strong><br><br>';
	    $pin = $row['pin'];
	   
		if ($entrymode=='A' or $entrymode=="") {
		} else {
			//ambil data
			$query = "select * from hrd.dkd0 where srid='$sr_id' and tgl ='$tgl'"; 
			$result = mysqli_query($cnmy, $query);
			$num_results = mysqli_num_rows($result);
			if ($num_results) {
				$row = mysqli_fetch_array($result);
				$mr_id = $row['mr_id'];		
				$dokter = $row['dokter'];		
				$jv_dokter = $row['jv_dokter'];			
				$compl = $row['kompl']; 
				$compl2 = $row['kompl2'];			
				$tgl = $row['tgl']; 
				$akt = $row['akt']; 	
				$saran = $row['saran'];
				$saran2 = $row['saran2'];
				$ket = $row['ket']; 
				$ket2 = $row['ket2'];
				$ketid = $row['ketId']; 
				//$ketid2 = $row['ketId2']; 
			}
		}	
		
		echo '
			<table>
				<tr>
					<td>Tgl</td><td>:</td>
		';	

		if ($entrymode=='E') {
			$tgl = $tanggal;
			echo '
				<td>
					<input type=text id="tgl" name="tgl" onclick="initDate()" value="'.$tgl.'" "'.$disabled_.'" maxlength=10 size=10 readonly>
				</td>
			';
		} else {
			echo '
				<td>
					<input type=text id="tgl" name="tgl" onclick="initDate()" value="'.$tanggal.'" "'.$disabled_.'" maxlength=10 size=10 readonly>
				</td>
			';
		}

		echo '
			</tr>
			<tr>
				<td>Dokter</td><td>:</td>
				<td><input type=text id="dokter" name="dokter" maxlength=100 size=100 value="'.$dokter.'" "'.$disabled_.'"></td>
			</tr>
			<tr>
				<td>JV Dokter</td><td>:</td>
				<td>
					<textarea name=jv_dokter id=jv_dokter maxlength=255 cols=75 '.$disabled_.'>'.$jv_dokter.'</textarea>
				</td>
			</tr>
			<tr>
				<td>Compl</td><td>:</td>
				<td><input type=text id="compl" name="compl" maxlength=60 size=60 value="'.$compl.'" "'.$disabled_.'"></td>
			</tr>
			<tr>
				<td>&nbsp;</td><td>&nbsp;</td>
				<td><input type=text id="compl2" name="compl2" maxlength=60 size=60 value="'.$compl2.'" "'.$disabled_.'"></td>
			</tr>
			<tr>
				<td>Akt</td><td>:</td>
				<td><input type=text id="akt" name="akt" maxlength=60 size=60 value="'.$akt.'" "'.$disabled_.'"></td>
			</tr>
			<tr>
				<td>Saran</td><td>:</td>
				<td><input type=text id="saran" name="saran" maxlength=60 size=60 value="'.$saran.'" "'.$disabled_.'"></td>
			</tr>
			<tr>
				<td>&nbsp;</td><td>&nbsp;</td>
				<td><input type=text id="saran2" name="saran2" maxlength=60 size=60 value="'.$saran2.'" "'.$disabled_.'"></td>
			</tr>
			<tr>
				<td>Ket</td><td>:</td>
				<td><input type=text id="ket" name="ket" maxlength=60 size=60 value="'.$ket.'" "'.$disabled_.'"></td>
			</tr>
			<tr>
				<td>&nbsp;</td><td>&nbsp;</td>
				<td><input type=text id="ket2" name="ket2" maxlength=60 size=60 value="'.$ket2.'" "'.$disabled_.'"></td>
			</tr>
			<tr>
				<td>&nbsp;</td><td>&nbsp;</td>
				<td>
		';
	
	  
		if ($entrymode=='E' or $entrymode=='D') {
			$query = "select ketid,nama from hrd.ket where aktif<>'N' order by ketid='$ketid' desc"; 
			$result = mysqli_query($cnmy, $query);
			$num_results = mysqli_num_rows($result);
			echo "<select name=\"ketid\" $disabled_>";
			for ($i=0; $i < $num_results; $i++) {
				$row = mysqli_fetch_array($result);
				echo '<option value="'.$row['ketid'].'">'.$row['nama'].'</option>';		
			}
			echo '</select><br>';	 
			/*$query = "select ketid,nama from ket where aktif<>'N' order by ketid='$ketid2' desc"; 
			$result = mysqli_query($cnmy, $query);
			$num_results = mysqli_num_rows($result);
			echo "<select name=\"ketid2\" $disabled_>";
			for ($i=0; $i < $num_results; $i++) {
				$row = mysqli_fetch_array($result);
				echo '<option value="'.$row['ketid'].'">'.$row['nama'].'</option>';		
			}
			echo '</select><br>';*/
		} else {
			$query = "select ketid,nama from hrd.ket where aktif<>'N' order by nama";
			$result = mysqli_query($cnmy, $query);
			$num_results = mysqli_num_rows($result);
			echo '<select name="ketid">';
			for ($i=0; $i < $num_results; $i++) {
				$row = mysqli_fetch_array($result);
				echo '<option value="'.$row['ketid'].'">'.$row['nama'].'</option>';
			}
			echo '</select><br>';

			mysqli_data_seek($result,0);
			/*echo '<select name="ketid2">';
			for ($i=0; $i < $num_results; $i++) {
				$row = mysqli_fetch_array($result);
				echo '<option value="'.$row['ketid'].'">'.$row['nama'].'</option>';
			}
			echo '</select>';*/
		}
		echo "
					<td>
				<tr>
			</table>
		";
	  
		echo "<input type=hidden id='set_focus' name='set_focus' value=".$set_focus."></td>";	  	  
		echo "<SCRIPT LANGUAGE='javascript'>\n";
		echo "   set_focus('$set_focus');\n";
		echo "</SCRIPT>\n";
	  
	}   
	
	if ($entrymode=='D') {
   	    echo "<input type=button id=cmdDel name=cmdDel value='Delete' onclick='disp_hapus(\"Hapus ?\")'>";
		echo "<input type=hidden id='srid' name='srid' value='$sr_id'>";
		echo "<input type=hidden id='tgl' name='tgl' value='$tgl'>";
		echo "<input type=hidden id='tgl1' name='tgl1' value='$tgl1'>";
		echo "<input type=hidden id='tgl2' name='tgl2' value='$tgl2'>";
	} else {
		echo "<input type=button id=cmdSave name=cmdSave value='Proses' onclick='disp_confirm(\"Simpan ?\")'>";
		echo '&nbsp;&nbsp;&nbsp;&nbsp;<input type=submit value="Reset">';
	}	  
	echo "<input type=hidden id='entrymode' name='entrymode' value='$entrymode'>";
	echo "<input type=hidden id='srid' name='srid' value='$sr_id'>";
	echo "<input type=hidden id='pin' name='pin' value='$pin'>";

	if (empty($_SESSION['USERID'])) {
	} else {
		//do_show_menu($_SESSION['jabatanid'],'N');
	}
?>
 
</form>
</body>
</html>

<!-- 
// ------------------------------------- old script -------------------------------------------- //
<html>
<head>
  <title>DKD</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <?php //header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<script src="mr0.js">
</script>
<body>
<form id="mr0" action="" method=post>
-->

<?php
 //   include("common.php");
 //   include("common3.php");
 //   session_start();
 //   if (!connect_server())
 //   {
 //     echo 'Error connection to database';
 //     exit;
 //   }

 //   if (empty($_SESSION['srid'])) {
 //      echo 'not authorized';
 //      exit;
 //   } else {
	// 	mysqli_select_db('hrd');
	// 	$srid = $_SESSION['srid'];
	// 	$srnama = $_SESSION['srnama'];
	// 	$sr_id = substr('0000000000'.$srid,-10);
	// 	$userid = $_SESSION['userid'];
	// 	$tanggal = date('Y-m-d');

	// 	$srid = $_GET['srid'];		
	// 	$tgl = $tanggal;
	// 	$tgl1 = $tanggal;
	// 	$tgl2 = $tanggal;
		
	// 	$set_focus = $_POST['set_focus'];
	// 	if ($set_focus=="") {		    
	// 		$set_focus = "icabangid";
	// 	}
		
	// 	$entrymode = $_GET['entrymode'];
	// 	if ($entrymode=='E') {
	// 		echo "<big><b>View/Edit DKD</b></big><br />";		 
	// 	}
	// 	if ($entrymode=='A' or $entrymode=="") {
	// 		echo "<big><b>Isi DKD</b></big><br />";			
	// 	}
	// 	$checked_ = "";
	// 	$disabled_ = "";
	// 	if ($entrymode=='D') {
	// 		echo "<big><b>Delete DKD</b></big><br />";
	// 		$disabled_ = "disabled";
	// 	}
	// 	$srid = $_SESSION['srid'];
	//     $sr_id = substr('0000000000'.$srid,-10);
	//     $query = "select nama,pin from karyawan where karyawanId='".$sr_id."'";
	//     $result = mysqli_query($cnmy, $query);
	//     $row = mysqli_fetch_array($result);
	//     $num_results = mysqli_num_rows($result);
	//     echo '<strong>'.$row['nama'].'</strong><br><br>';
	   
	// 	if ($entrymode=='A' or $entrymode=="") {
	// 	} else {
	// 		//ambil data
	// 		$query = "select * from dkd0 where srid='$sr_id' and tgl ='$tgl'"; 
	// 		$result = mysqli_query($cnmy, $query);
	// 		$num_results = mysqli_num_rows($result);
	// 		if ($num_results) {
	// 			$row = mysqli_fetch_array($result);
	// 			$mr_id = $row['mr_id'];		
	// 			$dokter = $row['dokter'];		
	// 			$jv_dokter = $row['jv_dokter'];			
	// 			$compl = $row['kompl']; 
	// 			$compl2 = $row['kompl2'];			
	// 			$tgl = $row['tgl']; 
	// 			$akt = $row['akt']; 	
	// 			$saran = $row['saran'];
	// 			$saran2 = $row['saran2'];
	// 			$ket = $row['ket']; 
	// 			$ket2 = $row['ket2'];
	// 			$ketid = $row['ketId']; 
	// 			//$ketid2 = $row['ketId2']; 
	// 		}
	// 	}	
		
	// 	echo "<table>";		
	// 	echo '<tr>Tgl :</tr>';	
	// 	if ($entrymode=='E') {
	// 		$tgl = $tanggal;
	// 		echo "<br><tr><input type=text id='tgl' name='tgl' maxlength=35 size=37 value='$tgl' $disabled_></tr>";	  
	// 	} else {
	// 		echo "<br><tr><input type=text id='tgl' name='tgl' maxlength=10 size=12 value='$tanggal' $disabled_></tr>";	  
	// 	}
	// 	echo '<br><tr>Dokter :</tr>';	  
	// 	echo "<br><tr><input type=text id='dokter' name='dokter' maxlength=100 size=100 value='$dokter' $disabled_></tr>";	
		
	// 	echo '<br><tr>JV Dokter :</tr>';	  	  	    
	// 	echo "<br><tr><input type=text id='jv_dokter' name='jv_dokter' maxlength=70 size=70 value='$jv_dokter' $disabled_></tr>";	
		
	// 	echo '<br><tr>Compl :</tr>';	  	  	    
	// 	echo "<br><tr><input type=text id='compl' name='compl' maxlength=60 size=60 value='$compl' $disabled_></tr>"; 	  	    
	// 	echo "<br><tr><input type=text id='compl2' name='compl2' maxlength=60 size=60 value='$compl2' $disabled_></tr>";	
	// 	echo '<br><tr>Akt :</tr>';	  	  	    
	// 	echo "<br><tr><input type=text id='akt' name='akt' maxlength=60 size=60 value='$akt' $disabled_></tr>";	
	// 	echo '<br><tr>Saran :</tr>';	  	  	    
	// 	echo "<br><tr><input type=text id='saran' name='saran' maxlength=60 size=60 value='$saran' $disabled_></tr>";	  	  	    
	// 	echo "<br><tr><input type=text id='saran2' name='saran2' maxlength=60 size=60 value='$saran2' $disabled_></tr>";	
	// 	echo '<br><tr>Ket :</tr>';	  	  	    
	// 	echo "<br><tr><input type=text id='ket' name='ket' maxlength=60 size=60 value='$ket' $disabled_></tr>";		  	  	    
	// 	echo "<br><tr><input type=text id='ket2' name='ket2' maxlength=60 size=60 value='$ket2' $disabled_></tr>";	
	  
	// 	if ($entrymode=='E' or $entrymode=='D') {
	// 		$query = "select ketid,nama from ket where aktif<>'N' order by ketid='$ketid' desc"; 
	// 		$result = mysqli_query($cnmy, $query);
	// 		$num_results = mysqli_num_rows($result);
	// 		echo "<br><select name=\"ketid\" $disabled_>";
	// 		for ($i=0; $i < $num_results; $i++) {
	// 			$row = mysqli_fetch_array($result);
	// 			echo '<br><option value="'.$row['ketid'].'">'.$row['nama'].'</option>';		
	// 		}
	// 		echo '</select><br>';	 
	// 		/*$query = "select ketid,nama from ket where aktif<>'N' order by ketid='$ketid2' desc"; 
	// 		$result = mysqli_query($cnmy, $query);
	// 		$num_results = mysqli_num_rows($result);
	// 		echo "<select name=\"ketid2\" $disabled_>";
	// 		for ($i=0; $i < $num_results; $i++) {
	// 			$row = mysqli_fetch_array($result);
	// 			echo '<option value="'.$row['ketid'].'">'.$row['nama'].'</option>';		
	// 		}
	// 		echo '</select><br>';*/
	// 	} else {
	// 		$query = "select ketid,nama from ket where aktif<>'N' order by nama";
	// 		$result = mysqli_query($cnmy, $query);
	// 		$num_results = mysqli_num_rows($result);
	// 		echo '<br><select name="ketid">';
	// 		for ($i=0; $i < $num_results; $i++) {
	// 			$row = mysqli_fetch_array($result);
	// 			echo '<br><option value="'.$row['ketid'].'">'.$row['nama'].'</option>';
	// 		}
	// 		echo '</select><br>';

	// 		mysqli_data_seek($result,0);
	// 		/*echo '<select name="ketid2">';
	// 		for ($i=0; $i < $num_results; $i++) {
	// 			$row = mysqli_fetch_array($result);
	// 			echo '<option value="'.$row['ketid'].'">'.$row['nama'].'</option>';
	// 		}
	// 		echo '</select>';*/
	// 	}
	// 	echo "</table>";
	  
	// 	echo "<input type=hidden id='set_focus' name='set_focus' value=".$set_focus."></td>";	  	  
	// 	echo "<SCRIPT LANGUAGE='javascript'>\n";
	// 	echo "   set_focus('$set_focus');\n";
	// 	echo "</SCRIPT>\n";
	  
	// }   
	
	// if ($entrymode=='D') {
 //   	    echo "<input type=button id=cmdDel name=cmdDel value='Delete' onclick='disp_hapus(\"Hapus ?\")'>";
	// 	echo "<input type=hidden id='srid' name='srid' value='$sr_id'>";
	// 	echo "<input type=hidden id='tgl' name='tgl' value='$tgl'>";
	// 	echo "<input type=hidden id='tgl1' name='tgl1' value='$tgl1'>";
	// 	echo "<input type=hidden id='tgl2' name='tgl2' value='$tgl2'>";
	// } else {
	// 	echo "<input type=button id=cmdSave name=cmdSave value='Proses' onclick='disp_confirm(\"Simpan ?\")'>";
	// 	echo '&nbsp;&nbsp;&nbsp;&nbsp;<input type=submit value="Reset">';
	// }	  
	// echo "<input type=hidden id='entrymode' name='entrymode' value='$entrymode'>";
	// echo "<input type=hidden id='srid' name='srid' value='$sr_id'>";

	// if (empty($_SESSION['srid'])) {
	// } else {
	// 	do_show_menu($_SESSION['jabatanid'],'N');
	// }
?>
 <!--
</form>
</body>
</html>
 -->