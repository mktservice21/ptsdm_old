<html>
<head>
  <title>Budget Request</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <link rel="shortcut icon" href="../../../images/icon.ico" />
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<script src="breq.js">
</script>
<script src="breq0.js"> //show dokter
</script>
<script src="breq8.js"> // show kode
</script>
<script src="breq7.js"> //cn
</script>
<script src="breq3.js"> // karyawan
</script>
<script src="breq4.js"> //mr
</script>
<body>
<form id="breq00" action="" method=post>

<?php
   include("../../../config/common.php");
   include("../../../config/koneksimysqli_it.php");
	//include("common3.php");
	session_start();

	if (empty($_SESSION['USERID'])) {
		echo 'not authorized';
		exit;
	} 
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
		//wh20130930
		$noref = '';  //noref br online
		if (isset($_POST['noref'])) {
			$noref = $_POST['noref'];
		}
		$kirim_br = '';
		if (isset($_POST['mr_id'])) {
			$mr_id = $_POST['mr_id'];
		}
		if (isset($_POST['kirim_br'])) {
			$kirim_br = $_POST['kirim_br'];
		}

		$set_focus = "";//$_POST['set_focus'];
		if ($set_focus=="") {		    
			$set_focus = "icabangid";
		}
		
		$entrymode = $_GET['entrymode']; //echo"$entrymode";
		$mode = $_GET['mode']; //echo"$mode";
		if ($entrymode=='E') {
			echo "<big><b>View/Edit Budget Request</b></big><br /><br />";		 
		}
		if ($entrymode=='A' or $entrymode=="") {
			echo "<big><b>Isi Budget Request DCC/DSS</b></big><br /><br />";			
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
			//ambil data
			
			$query = "select * from hrd.br0 where brid='$brid'"; 
			$result = mysqli_query($cnit, $query);
			$num_results = mysqli_num_rows($result);
			if ($num_results) {
				$row = mysqli_fetch_array($result);
				
				$mr_id = $row['mrid'];	//echo"$mr_id";
				$icabangid = $row['icabangid'];			
				$divprodid = $row['divprodid'];
				$brid = $row['brId'];			
				$tgl = $row['tgl'];
				$kodeid = $row['kode']; 	
				$dokterid = $row['dokterId']; 
				$aktiv1 = $row['aktivitas1'];
				$aktiv2 = $row['aktivitas2'];
				$ccyid = $row['ccyId'];
				$jumlah = $row['jumlah'];
				$cn = $row['cn'];
				$real1 = $row['realisasi1'];
				$karyawanid = $row['karyawanId'];
				$karyawani2 = $row['karyawanI2'];
				$noslip = $row['noslip'];
				$lampiran = $row['lampiran'];
				$tgltrans = $row['tgltrans'];
				$dokter = $row['dokter'];
				$trmskid = $row['trmskid'];
				$tglacc = $row['tglacc'];
				$via = $row['via'];
				$ca = $row['ca'];
				$user1  = $row['user1']; 	  							
			//	echo"$tgltrans";
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
		if ($kirim_br=='Y') {
			$disabled_ = 'disabled';
		}
		
		
		if ($tgl<>'') {
			$tanggal1 = substr($tgl,-2,2);
			$bln1 = substr($tgl,5,2); 
			$tahun1 = substr($tgl,0,4); 
			$tahun_1 = $tahun1 - 1;
			$tahun_2 = $tahun1 + 1;
		//	echo"$tahun_1//$tahun_2";
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

		echo "<table>";
		$query = "SELECT icabangid,nama FROM MKT.icabang WHERE aktif='Y' ORDER BY nama";
		$result = mysqli_query($cnit, $query);
		$num_results = mysqli_num_rows($result);
		if ($noref!='') {
			echo "No. Ref. : $noref<br>";
		}
		echo '<tr>';
		echo '<td align=right>Cabang :</td>'; 	  	  
	    echo "<td><select name=\"icabangid\" id='icabangid' onchange=\"showKaryawan(this.value)\" value=\"$icabangid\" $disabled_>";
    	echo '<option value="blank">(Blank)</option>';
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
		echo '</tr>';
		
		echo "<tr>";
		if ($entrymode=='E') {
			// echo 'Atas';
			$query = "SELECT karyawanid,nama FROM hrd.karyawan WHERE aktif = 'Y' ORDER BY karyawanid='$karyawanid',nama";
		// echo "$query<br>";
			$result = mysqli_query($cnit, $query);
			$num_results = mysqli_num_rows($result);
			echo "<td align=right>Yang membuat BR :</td>";
			echo "<td><select name=\"karyawanid\" id=\"karyawanid\" onchange=\"showMR(this.value)\" value=\"$karyawanid\" $disabled_>";
			for ($i=0; $i < $num_results; $i++) {
				$row = mysqli_fetch_array($result);
				$str_ = $row['nama'];		
				if ($karyawanid == $row['karyawanid']) {
					echo '<option selected="selected" value="'.$row['karyawanid'].'">'.$str_.'</option>';		
				} else {
					echo '<option value="'.$row['karyawanid'].'">'.$str_.'</option>';		
				}
			}
			echo '</select></td>';	  
		} else {
			// echo 'BAwah';
			echo "<td align=right>Yang membuat BR :</td>";
			echo "<td><select name=\"karyawanid\" id=\"karyawanid\" onchange=\"showMR(this.value)\" value=\"$karyawanid\" $disabled_>";
			if ($karyawanid != "") {
				$qnama = "SELECT nama FROM hrd.karyawan WHERE karyawanid='$karyawanid' AND aktif = 'Y'";
				// echo "$qnama<br>";
				$rnama = mysqli_query($cnit, $qnama);
				$nnama = mysqli_num_rows($rnama);
				if ($nnama) {
					$rownama = mysqli_fetch_array($rnama);
					$karyawan_nm = $rownama['nama'];
				}
				echo "<option value=\"$karyawanid\">$karyawan_nm</option>\n";		
			}
			echo "</select></td>"; 
		}
		// echo "$query<br>";
		echo "</tr>";
		
		echo "<tr>";
		if ($entrymode=='E') {
				$query = "select karyawanid,nama from hrd.karyawan where atasanid='$karyawanid' order by karyawanid='$mr_id',nama"; //cho"$query";
				$query = "select karyawanid,nama from hrd.karyawan where karyawanid='$mr_id'"; //cho"$query";
				$result = mysqli_query($cnit, $query);
				$num_results = mysqli_num_rows($result); //echo"$num_results";
				echo "<td align=right>MR :</td>";
				echo "<td><select name=\"mr_id\" id=\"mr_id\" onchange=\"showDokter(this.value)\" $disabled_>";
				if ($mr_id=="blank") {
					echo '<option value="blank">(Blank)</option>';
				} else {
				}
				for ($i=0; $i < $num_results; $i++){
					$row = mysqli_fetch_array($result);
					$str_ = $row['nama'];		
					if ($mr_id == $row['karyawanid']) {
						echo '<option selected="selected" value="'.$row['karyawanid'].'">'.$str_.'</option>';		
					} else {
						echo '<option value="'.$row['karyawanid'].'">'.$str_.'</option>';		
					}
				}
				echo '</select></td>';	  
			
		} else {
			echo "<td align=right>MR :</td>";
			echo "<td><select name=\"mr_id\" id=\"mr_id\" value=\"$mr_id\" onchange=\"showDokter(this.value)\" $disabled_>";	
			if ($mr_id != '') {
				$qnama = "select nama from hrd.karyawan where karyawanid='$mr_id'"; 
				$rnama = mysqli_query($cnit, $qnama);
				$nnama = mysqli_num_rows($rnama);
				if ($nnama) {
					$rownama = mysqli_fetch_array($rnama);
					$karyawan_nm = $rownama['nama'];
				}
				echo "<option value=\"$mr_id\">$karyawan_nm</option>\n";		
			
			}
			echo "</select></td>"; 
		}
		echo "</tr>";
		
		/*echo '<tr>';
		echo '<td align=right>Tanggal :</td>';
		if ($entrymode=='E') {
			//$tgl = $_GET['tgl'];
			echo "<td><input type=text id='tgl' name='tgl' maxlength=10 size=12 value='$tgl' $disabled_></td>";	 
		} else {
			echo "<td><input type=text id='tgl' name='tgl' maxlength=10 size=12 value='$tanggal' $disabled_></td>";	  
		}
		echo '</tr>';*/
		
		//wh20130930
		//$noref = ''; //noref br online
		if ($kirim_br=='Y') {
			$tgl_br = $_POST['tgl'];
			$tanggal1 = substr($tgl_br,8,2);
			$bln1 = substr($tgl_br,5,2);
			$tahun = substr($tgl_br,0,4);
			$kodeid = $_POST['kodeid'];
			$karyawanid_ = $_POST['karyawanid'];
			$aktiv1 = $_POST['aktifitas1'];
			$aktiv2 = $_POST['aktifitas2'];
			$dokterid = $_POST['dokterid'];
			$ccyid = 'IDR';
			$jumlah0 = $_POST['kontribusi'];
			$jumlah = number_format($jumlah0,2);
			$real1 = $_POST['a_n'];
			$noref = $_POST['noref'];
			$lampiran = $_POST['lampiran'];
		}
		echo '<tr>';
		echo '<td align=right>Tanggal Input BR :</td>';
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
			}  else {
				$tahun1_ = $tahun1;
			}//echo "$tahun";
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
	
		$query = "select divprodid,nama from MKT.divprod where br='Y' order by nama";
		$result = mysqli_query($cnit, $query);
		$num_results = mysqli_num_rows($result);
		echo '<tr>';
		echo '<td align=right>Divisi :</td>'; 	  	  
	    echo "<td><select name=\"divprodid\" id=\"divprodid\" value=\"$divprodid\" onchange=\"showKode(this.value)\" $disabled_>";
	    echo '<option value="blank">(Blank)</option>';
	    for ($i=0; $i < $num_results; $i++) {
			$row = mysqli_fetch_array($result);
			$str_ = $row['nama'];		
			if ($divprodid == $row['divprodid']) {
				echo '<option selected="selected" value="'.$row['divprodid'].'">'.$str_.'</option>';		
			} else {
				echo '<option value="'.$row['divprodid'].'">'.$str_.'</option>';		
			}
		}
		echo '</select></td>';	  
		echo '</tr>';
		
		echo "<tr>";
		if ($entrymode=='E') {
			$query = "select kodeid,nama from hrd.br_kode where kodeid='$kodeid'"; 
			$result = mysqli_query($cnit, $query);
			$num_results = mysqli_num_rows($result);
			echo "<td align=right>Kode :</td>";
			echo "<td><select name=\"kodeid\" id=\"kodeid\" value=\"$kodeid\" $disabled_>";
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
		} else {
			echo "<td align=right>Kode :</td>";
			echo "<td><select name=\"kodeid\" id=\"kodeid\" value=\"$kodeid\" $disabled_>";	
			echo "<option value=\"$kodeid\">$kodeid</option>\n";
			echo "</select></td>"; 
		}
		echo "</tr>";	

		echo '<tr>';	  	  	  
		echo '<td align=right>Aktivitas :</td>';	  
		echo "<td><input type=text id='aktiv1' name='aktiv1' maxlength=50 size=52 value='$aktiv1' $disabled_></td>";	  	  
		echo '</tr>';
		
		echo '<tr>';
		echo '<td align=right></td>';	  
		echo "<td><input type=text id='aktiv2' name='aktiv2' maxlength=50 size=52 value='$aktiv2' $disabled_></td>";	  	  
		echo '</tr>';
	    
		echo "<tr>";
		if ($entrymode=='E') {
			$query = "select dokter.dokterid, dokter.nama 
					  FROM hrd.mr_dokt mr_dokt
					  join hrd.karyawan karyawan on mr_dokt.karyawanid=karyawan.karyawanId
					  join hrd.dokter dokter on mr_dokt.dokterid=dokter.dokterid
					  where mr_dokt.aktif <> 'N' and karyawan.karyawanid='$mr_id' and dokter.nama <> ''
					  order by dokter.dokterid='$dokterid',nama"; //echo"$query";
			$result = mysqli_query($cnit, $query);
			$num_results = mysqli_num_rows($result);
			echo "<td align=right>Dokter :</td>";
			echo "<td><select name=\"dokterid\" id=\"dokterid\" onchange=\"showCN(this.value)\" $disabled_>";
			echo '<option value="">(blank)</option>';					
			for ($i=0; $i < $num_results; $i++) {
				$row = mysqli_fetch_array($result);
				$str_ = $row['nama'];		
				$dokterid = $row['dokterid'];		
				if ($dokterid == $row['dokterid']) {
					/*echo '<option selected="selected" value="'.$row['dokterid'].'">'.$str_.'</option>';		*/
					//echo "$dokterid";
					echo "<option selected=\"selected\" value=\"$dokterid\">$str_ - $dokterid</option>";		
					
					//echo "<option value=\"$aptid\">$nama - $aptid</option>";
					
				} else {
					echo "<option value=\"$dokterid\">$str_ - $dokterid</option>";		
				}
			}
			echo '</select></td>';	  
		} else {
			echo "<td align=right>Dokter :</td>";
			echo "<td><select name=\"dokterid\" id=\"dokterid\" value=\"$dokterid\" onchange=\"showCN(this.value)\"  $disabled_>";		
			if ($dokterid != '') {
				$qdokt = "select nama from hrd.dokter where dokterid='$dokterid'";
				$rdokt = mysqli_query($cnit, $qdokt);
				$ndokt = mysqli_num_rows($rdokt);
				if ($ndokt) {
					$rowdokt = mysqli_fetch_array($rdokt);
					$dokter = $rowdokt['nama'];
					echo "<option value=\"$dokterid\">$dokter</option>\n";
				}
			}
			echo "</select></td>"; 
		}
		$bulan = substr($tanggal,0,7);
		$karyawanid = $_SESSION['IDCARD'];
		echo '<input type="hidden" name="srid" id="srid" value="'.$karyawanid.'">';					
		echo '<input type="hidden" name="bulan" id="bulan" value="'.$bulan.'">';		
		if ($kirim_br=='Y') {
			echo "<td>&nbsp</td>\n";
		} else {
			echo "<td><input type=button id=cmdKS name=cmdKS value='Lihat KS' onclick='disp_ks()'></td>";
		}
		echo '</tr>';
		
		echo '<tr>';	  	  	  
		echo '<td align=right>Nama Dokter :</td>';	  
		echo "<td><input type=text id='dokter' name='dokter' maxlength=35 size=37 value='$dokter' $disabled_></td>";	  	  
		echo '</tr>';
		
		$query = "select * from hrd.ccy";
		$result = mysqli_query($cnit, $query);
		$num_results = mysqli_num_rows($result);
		echo '<tr>';
		echo '<td align=right>Ccy. :</td>';
		echo "<td><select name='ccyid' id='ccyid' value='$ccyid' $disabled_>";
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
		echo "<td align=right>Jumlah :</td>";
		echo "<td><input type=text id=\"jumlah0\" name=\"jumlah0\" value=\"$jumlah\" onBlur=\"this.value=say_it(this.value,'jumlah')\" 
		                onfocus = \"this.select()\"
		                validchars=\"0123456789.\" onkeypress=\"return allowChars(this,event)\"
						maxlength=15 size=17 value=\"".number_format($jumlah,0)."\" $disabled_>";
		echo "<input type=text align=\"right\" id=\"jumlah\" name=\"jumlah\" value=\"$jumlah0\" size=17 disabled></td>";	  	  
		echo '</tr>';
		
		echo '<tr>';
		echo '<td align=right>Realisasi :</td>';
		echo "<td><input type=text id='real1' name='real1' maxlength=30	size=21 value='$real1' $disabled_></td>";
		echo '</tr>';
	
	/*	$query = "select trmsk.*, sum(br0.jumlah) as jmlh
				  from trmsk
				  join br0 on trmsk.trmskid=br0.trmskid
				  group by br0.trmskid  ";
		$result = mysqli_query($cnit, $query);
		$num_results = mysqli_num_rows($result);
		for ($i=0; $i < $num_results; $i++) {
			 $row = mysqli_fetch_array($result);
			 $jmlh = $row['jmlh'];
			 $jumlah = $row['jumlah']; //echo"$nama";
			 $selisih = $jumlah - $jmlh; //echo"$selisih<br>";
		}	*/
		
		/*$query = "select trmsk.*, br_bank.nama
				  from trmsk
				  join br_bank on trmsk.bankid=br_bank.bankid
				  where jumlah not like realisasi";
		$result = mysqli_query($cnit, $query);
		$num_results = mysqli_num_rows($result);
		echo '<tr>';
		echo '<td align=right>Nama Bank :</td>';
		echo "<td><select name='trmskid' id='trmskid' $disabled_>";
		echo '<option value="Blank">(Blank)</option>';
		for ($i=0; $i < $num_results; $i++) {
			$row = mysqli_fetch_array($result);
			$str_ = $row['nama'];
			$jumlah = $row['jumlah'];
			if ($trmskid == $row['trmskid']) {
				echo '<option selected="selected" value="'.$row['trmskid'].'">'.$str_.'  ('.$jumlah.')</option>';		
			} else {
				echo '<option value="'.$row['trmskid'].'">'.$str_.'  ('.$jumlah.') </option>';
			}
		}
		echo '</select></td>';	  
		echo '</tr>';*/
		
		echo "<tr>";
		if ($entrymode=='E') {
			$query = "select cn from hrd.br0 where brid='$brid'";// echo"$query";
			$result = mysqli_query($cnit, $query);
			$num_results = mysqli_num_rows($result);
			echo "<td align=right>CN :</td>";
			echo "<td><select name=\"cn\" id=\"cn\" $disabled_>";
			for ($i=0; $i < $num_results; $i++){
				$row = mysqli_fetch_array($result);
				$str_ = $row['cn'];		
				if ($cn == $row['cn']) {
					echo '<option selected="selected" value="'.$row['cn'].'">'.$str_.'</option>';		
				} else {
					echo '<option value="'.$row['cn'].'">'.$str_.'</option>';		
				}
			}
			echo '</select></td>';	  
		} else {
			echo "<td align=right>CN :</td>";
			echo "<td><input id='cn' name='cn' disabled></td>";	
		}
		echo "</tr>";
		
		echo '<tr>';
		echo '<td align=right>No. Slip :</td>';
		echo "<td><input type=text id='noslip' name='noslip' maxlength=15 size=17 value='$noslip' $disabled_></td>";	  
		echo '</tr>';	  

		/*echo '<tr>';
		echo '<td align=right>Tgl ACC :</td>';
		if ($entrymode=='E') {
			echo "<td><input type=text id='tglacc' name='tglacc' maxlength=10 size=12 value='$tglacc' $disabled_></td>";	 
		} else {
			echo "<td><input type=text id='tglacc' name='tglacc' maxlength=10 size=12 value='$tanggal' $disabled_></td>";	  
		}
		echo '</tr>';*/
		
		/*echo '<tr>';
		echo '<td align=right>Tgl ACC :</td>';
		echo "<td><select name='tanggal3' id='tanggal3' $disabled_>";
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
		echo "<option value='$tahun_1'>$tahun_1</option>";
		echo "<option selected='selected' value='$tahun'>$tahun</option>";
		echo "<option value='$tahun_2'>$tahun_2</option>";
		echo '</select>';		
		echo '</td>';	
		echo '</tr>';
		
		*/
		
		/*echo '<tr>';
		echo '<td align=right>Tgl Transfer :</td>';
		if ($entrymode=='E') {
			echo "<td><input type=text id='tgltrans' name='tgltrans' maxlength=10 size=12 value='$tgltrans' $disabled_></td>";	 
		} else {
			echo "<td><input type=text id='tgltrans' name='tgltrans' maxlength=10 size=12 value='$tanggal' $disabled_></td>";	  
		}
		echo '</tr>';*/
		
		if ($kirim_br=='Y') {
			$tanggal2 = '00';
			$bulan2 = '00';
			$tahun = '0000';
		}
		echo '<tr>';
		echo '<td align=right>Tgl Transfer :</td>';
		echo "<td><select name='tanggal2' id='tanggal2' $disabled_>";
		echo '<option value="00">00</option>';
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
		echo '<option value="00">00</option>';
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
			}  else {
				$tahun2_ = $tahun2;
			}//echo "$tahun";
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
		
		$disabled2_ = $disabled_;
		if ($kirim_br=='Y') {
			$disabled2_ = '';
			if ($lampiran=='Y') {
				$checked_ = 'checked';
			}
		}
		echo '<tr>';
		echo '<td align=right>Lampiran :</td>';
		echo "<td><input type='checkbox' id='chklamp' name='chklamp' $checked_ $disabled2_>";
		echo "&nbsp;&nbsp;&nbsp;CA : <input type='checkbox' id='chklamp1' name='chklamp1' $checked1_ $disabled_>";
		echo "&nbsp;&nbsp;&nbsp;Via Surabaya : <input type='checkbox' id='chklamp2' name='chklamp2' $checked2_ $disabled_></td>";
		echo '</tr>';
		  
		echo "</table>";
	   
		echo "<input type=hidden id='set_focus' name='set_focus' value=".$set_focus."></td>";	  	  
		echo "<SCRIPT LANGUAGE='javascript'>\n";
		echo "   set_focus('$set_focus');\n";
		echo "</SCRIPT>\n";
	  
	
	echo "<br>";
	$bulan1 = $_GET['bulan1'];// echo"$bulan";
	if ($entrymode=='D') {
   	    echo "<input type=button id=cmdDel name=cmdDel value='Delete' onclick='disp_hapus(\"Hapus ?\")'>";
		$bulan1 = $_GET['bulan1'];// echo"$bulan";
		echo "<input type=hidden id='divprodid' name='divprodid' value='$divprodid'>";
		echo "<input type=hidden id='icabangid' name='icabangid' value='$icabangid'>";
		echo "<input type=hidden id='kodeid' name='kodeid' value='$kodeid'>";
		echo "<input type=hidden id='bulan1' name='bulan1' value='$bulan1'>";
	} else {
		echo "<input type=button id=cmdSave name=cmdSave value='Save' onclick='disp_confirm(\"Simpan ?\")'>";
		if ($kirim_br != 'Y') {
			//echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button id=cmdReset name=cmdReset value='Reset' onclick='click_reset()'>";
		}
		
		if ($entrymode=='E' and $mode='A') {
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button id=cmdDel name=cmdDel value='Delete' onclick='disp_hapus(\"Hapus ?\")'>";
			$bulan1 = $_GET['bulan1'];// echo"$bulan";
			echo "<input type=hidden id='divprodid' name='divprodid' value='$divprodid'>";
			echo "<input type=hidden id='icabangid' name='icabangid' value='$icabangid'>";
			echo "<input type=hidden id='kodeid' name='kodeid' value='$kodeid'>";
			echo "<input type=hidden id='bulan1' name='bulan1' value='$bulan1'>";
		}
		
		
		
	}	  
	echo "<input type=hidden id='entrymode' name='entrymode' value='$entrymode'>";
	echo "<input type=hidden id='mode' name='mode' value='$mode'>";
	echo "<input type=hidden id='brid' name='brid' value='$brid'>";
	echo "<input type=hidden id='namadkt' name='namadkt' value='$namadkt'>";
	echo "<input type=hidden id='jumlah_' name='jumlah_' value='$jumlah'>";
	echo "<input type=hidden id='bulan1' name='bulan1' value='$bulan1'>\n";
	
	//wh20130930
	echo "<input type=hidden id='noref' name='noref' value='$noref'>\n";
	echo "<input type=hidden id='kirim_br' name='kirim_br' value='$kirim_br'>\n";
	if ($kirim_br=='Y') {
		$tahun = substr($_POST['tgl'],0,4);
		echo "<input type=\"hidden\" id=\"icabangid\" name=\"icabangid\" value=\"$icabangid\">\n";
		echo "<input type=\"hidden\" id=\"karyawanid\" name=\"karyawanid\" value=\"$karyawanid_\">\n";
		echo "<input type=\"hidden\" id=\"divprodid\" name=\"divprodid\" value=\"$divprodid\">\n";
		echo "<input type=\"hidden\" id=\"mr_id\" name=\"mr_id\" value=\"$mr_id\">\n";
		echo "<input type=\"hidden\" id=\"tanggal1\" name=\"tanggal1\" value=\"$tanggal1\">\n";
		echo "<input type=\"hidden\" id=\"bln1\" name=\"bln1\" value=\"$bln1\">\n";
		echo "<input type=\"hidden\" id=\"tahun1\" name=\"tahun1\" value=\"$tahun\">\n";
		echo "<input type=\"hidden\" id=\"kodeid\" name=\"kodeid\" value=\"$kodeid\">\n";
		echo "<input type=\"hidden\" id=\"aktiv1\" name=\"aktiv1\" value=\"$aktiv1\">\n";
		echo "<input type=\"hidden\" id=\"aktiv2\" name=\"aktiv2\" value=\"$aktiv2\">\n";
		echo "<input type=\"hidden\" id=\"dokterid\" name=\"dokterid\" value=\"$dokterid\">\n";
		echo "<input type=\"hidden\" id=\"dokter\" name=\"dokter\" value=\"$dokter\">\n";
		echo "<input type=\"hidden\" id=\"ccyid\" name=\"ccyid\" value=\"$ccyid\">\n";
		echo "<input type=\"hidden\" id=\"jumlah_\" name=\"jumlah_\" value=\"$jumlah0\">\n";
		echo "<input type=\"hidden\" id=\"real1\" name=\"real1\" value=\"$real1\">\n";
	}
	
	if (empty($_SESSION['srid'])) {
	} else {
		if ($kirim_br!='Y') {
			do_show_menu($_SESSION['jabatanid'],'N');
		}
	}
?>
 
</form>
</body>
</html>
