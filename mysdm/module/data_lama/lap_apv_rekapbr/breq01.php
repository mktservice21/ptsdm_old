<html>
<head>
  <title>Isi Budget Request</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <link rel="shortcut icon" href="../../../images/icon.ico" />
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<script src="breq.js">
</script>
<body>
<form id="breq01" action="breq00.php" method=post>

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
		$tanggal = date('Y-m-d');
		
		$mr_id = $_POST['mr_id'];	
		$icabangid = $_POST['icabangid'];			
		$divprodid = $_POST['divprodid'];
	    $karyawanid= $_POST['karyawanid'];
	//	$tgl = $_POST['tgl']; 
		$kodeid = $_POST['kodeid']; 
		$aktiv1 = $_POST['aktiv1'];
		$aktiv2 = $_POST['aktiv2'];
		$dokterid = $_POST['dokterid'];
		$ccyid = $_POST['ccyid'];
		$jumlah = $_POST['jumlah_'];
		$real1 = $_POST['real1'];
		$cn = $_POST['cn']; 
		$noslip = $_POST['noslip'];
		//$tgltrans = $_POST['tgltrans'];
		$dokter = $_POST['dokter'];
		$trmskid = "";//$_POST['trmskid'];
	//	$tglacc = $_POST['tglacc'];
                
                $chklamp="";
                if (isset($_POST['chklamp'])) $chklamp = $_POST['chklamp'];
                
                $chklamp1="";
		if (isset($_POST['chklamp1'])) $chklamp1 = $_POST['chklamp1'];
                
                $chklamp2="";
		if (isset($_POST['chklamp2'])) $chklamp2 = $_POST['chklamp2'];
		
		
		//tglbr
		$tanggal1 = $_POST['tanggal1'];
		$bln1 = $_POST['bln1']; //echo"bln1=$bln1";
		$tahun1 = $_POST['tahun1'];
		$periode1 = $tahun1.'-'.$bln1.'-'.$tanggal1; 
		if ($periode1 == '--') {
			$periode1 = $_POST['periode1']; 
		}
		//tgltrans
		$tanggal2 = $_POST['tanggal2'];
		$bulan2 = $_POST['bulan2'];
		$tahun2 = $_POST['tahun2'];
		$periode2 = $tahun2.'-'.$bulan2.'-'.$tanggal2; 
		if ($periode2 == '--') {
			$periode2 = $_POST['periode2']; 
		}
		//echo "tahun=$periode2";
		//tglacc
                $periode3="";
                /*
		$tanggal3 = $_POST['tanggal3'];
		$bulan3 = $_POST['bulan3'];
		$tahun3 = $_POST['tahun3'];
		$periode3 = $tahun3.'-'.$bulan3.'-'.$tanggal3; 
		if ($periode3 == '--') {
			$periode3 = $_POST['periode3']; 
		}
                */
                
		//DCC/DSS
		$query_kd = "select nama from hrd.br_kode where kodeid='$kodeid'";
		$result_kd = mysqli_query($cnit, $query_kd);
		$num_results_kd = mysqli_num_rows($result_kd);
		if ($num_results_kd) {
			 $row_kd = mysqli_fetch_array($result_kd);
			 $nama_kd = $row_kd['nama'];
		}
		
		//AREA
		if ($nama_kd == 'DCC') {
			$query_ar = "select areaid from hrd.karyawan where karyawanid='$karyawanid'";
		} else {
			$query_ar = "select areaid from hrd.karyawan where karyawanid='$mr_id'";
		}
		$result_ar = mysqli_query($cnit, $query_ar);
		$num_results_ar = mysqli_num_rows($result_ar);
		if ($num_results_ar) {
			 $row_ar = mysqli_fetch_array($result_ar);
			 $areaid = $row_ar['areaid'];
		}
		if ($areaid=='') {
			$areaid='0000000001';
			} 
			
		$lamp = 'N';
		if ($chklamp=="on") {
		   $lamp = 'Y';
		}		
		
		$ca = 'N';
		if ($chklamp1=="on") {
		   $ca = 'Y';
		}	

		$via = 'N';
		if ($chklamp2=="on") {
		   $via = 'Y';
		}
		
		if ($icabangid=="") {
			echo "BR harus diisi!<br><br>";
			echo "<input type=button name=cmdBack id=cmdBack value=Back onclick='go_back(1)'><br>";
			exit;
		}
		$entrymode = $_POST['entrymode'];
		$mode = $_POST['mode'];
                
                //echo "$entrymode, $via, $ca, $lamp";exit;
		if ($entrymode=='E') {
		    $brid = $_POST['brid'];
			$query = "update hrd.br0 set brid='$brid',
									 tgl='$periode1',
									 kode='$kodeid',
									 dokterid='$dokterid',
									 aktivitas1='$aktiv1',
									 aktivitas2='$aktiv2',
									 ccyid='$ccyid',
									 jumlah='$jumlah',
									 cn='$cn',
									 realisasi1='$real1',
									 mrid='$mr_id',
									 karyawanid='$karyawanid',
									 noslip='$noslip',
									 lampiran='$lamp',
									 tgltrans='$periode2',
									 user1='$userid',
									 icabangid='$icabangid',
									 divprodid='$divprodid',
									 dokter='$dokter',
									 trmskid='$trmskid',
									 tglacc='$periode3',
									 via='$via',
									 ca='$ca',
									 areaid='$areaid'
						where brid='$brid'"; //echo"$query<BR><BR>";
			
		} else {
			$brid = "0000000000";
			$query = "select noBr from hrd.setup0";
			$result = mysqli_query($cnit, $query);
			$num_results = mysqli_num_rows($result);
			$row = mysqli_fetch_array($result);
			if ($num_results) {
				$brid = $row['noBr'];
			}
			$brid = plus1($brid,10);

			if ($ca <> 'Y') {
				$query = "insert into hrd.br0 (brid,tgl,kode,dokterid,aktivitas1,aktivitas2,ccyid,jumlah,cn,realisasi1,karyawanid,
						  noslip,lampiran,tgltrans,user1,icabangid,divprodid,mrid,dokter,trmskid,tglacc,via,ca,areaid,jumlah1) 
						  values ('$brid','$periode1','$kodeid','$dokterid','$aktiv1','$aktiv2','$ccyid','$jumlah','$cn','$real1',
						  '$karyawanid','$noslip','$lamp','$periode2','$userid','$icabangid','$divprodid','$mr_id',
						  '$dokter','$trmskid','$periode3','$via','$ca','$areaid','$jumlah')";// echo"$query";
			} else {
				$query = "insert into hrd.br0 (brid,tgl,kode,dokterid,aktivitas1,aktivitas2,ccyid,jumlah,cn,realisasi1,karyawanid,
						  noslip,lampiran,tgltrans,user1,icabangid,divprodid,mrid,dokter,trmskid,tglacc,via,ca,areaid) 
						  values ('$brid','$periode1','$kodeid','$dokterid','$aktiv1','$aktiv2','$ccyid','$jumlah','$cn','$real1',
						  '$karyawanid','$noslip','$lamp','$periode2','$userid','$icabangid','$divprodid','$mr_id',
						  '$dokter','$trmskid','$periode3','$via','$ca','$areaid')"; //echo"$query";
			}

		}//echo"$query";
		$result = mysqli_query($cnit, $query);
		if ($result) {	      
			echo "<br>Save OK!<br><br>";
			if ($entrymode=="E") {
			} else {
				$query = "update hrd.setup0 set nobr='$brid'";
				$result = mysqli_query($cnit, $query);
				if ($result) {
				} else {
					echo "Error : ".mysqli_error();
				}
				if (isset($_POST['kirim_br'])) {
					$kirim_br = $_POST['kirim_br'];
					if ($kirim_br=='Y') {
						$noref = $_POST['noref'];
						$qupd = "update mkt_2013.br_100 set nobr='$brid' where noref='$noref'";
						$rupd = mysqli_query($cnit, $qupd);
						if (!$rupd) {
							echo "Error : <br> $qupd<br>".mysqli_error()."<br>";
						}	
					}
				}
				
			}
		} else {
			echo "Error : ".mysqli_error();
			exit;
		} 
			
	}  // if (empty($_SESSION['srid'])) 

	if ($entrymode=="E") {
		if ($mode=="R") {
			$periode1 = substr($periode1,0,7); 
			echo "<br><input type=button id=cmdSave name=cmdSave value='Save' onclick='goto2(\"breq01\",\"rpbrsby1.php\")'>";
			echo "<input type=hidden name=periode1 id=periode1 value='$periode1'>";
		} else {
			if ($mode=="A") {
				$periode1 = substr($periode1,0,7); 
				//echo "<br><input type=button id=cmdSave name=cmdSave value='Save' onclick='goto2(\"breq01\",\"rptgltr1.php\")'>";
				echo "<input type=hidden name=periode id=periode value='$periode1'>";
				echo "<input type=hidden name=divprodid id=divprodid value='$divprodid'>";
			} else {
				//echo "<br><input type=button id=cmdSave name=cmdSave value='Save' onclick='goto2(\"breq01\",\"breq11.php\")'>";
				echo "<input type=hidden name=bulan1 id=bulan1 value='$bulan1'>";
			}
		
		
			
		}
		echo "<input type=hidden name=icabangid id=icabangid value='$icabangid'>";
		echo "<input type=hidden name=divprodid id=divprodid value='$divprodid'>";
		echo "<input type=hidden name=kodeid id=kodeid value='$kodeid'>";
	
	} else {
		echo "<input type=submit id=cmdOK name=cmdOK value='OK'>";
	}
   
	if (empty($_SESSION['srid'])) {
	} else {
		do_show_menu($_SESSION['jabatanid'],'N');
	}
?>
 
</form>
</body>
</html>


