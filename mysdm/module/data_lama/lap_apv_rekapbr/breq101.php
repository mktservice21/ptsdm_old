<html>
<head>
  <title>Isi Budget Request</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<script src="breq6.js">
</script>
<body>
<form id="breq101" action="breq100.php" method=post>

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
		
		$icabangid = $_POST['icabangid'];			
		$divprodid = $_POST['divprodid'];
	    $karyawanid= $_POST['karyawanid'];
		$tgl = "";//$_POST['tgl'];
		$kodeid = $_POST['kodeid'];
		$aktiv1 = $_POST['aktiv1'];
		$aktiv2 = $_POST['aktiv2'];
		$ccyid = $_POST['ccyid'];
		$jumlah = $_POST['jumlah_'];
		$real1 = $_POST['real1'];
		$noslip = $_POST['noslip'];
		$tgltrans = "";//$_POST['tgltrans'];
		$trmskid = "";//$_POST['trmskid'];
		$tglacc = "";//$_POST['tglacc'];

                
                $chklamp="";
                if (isset($_POST['chklamp'])) $chklamp = $_POST['chklamp'];
                
                $chklamp1="";
		if (isset($_POST['chklamp1'])) $chklamp1 = $_POST['chklamp1'];
                
                $chklamp2="";
		if (isset($_POST['chklamp2'])) $chklamp2 = $_POST['chklamp2'];
                
                
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
		//tglacc
                $periode3="";
                /*
		$tanggal3 = $_POST['tanggal3'];
		$bulan3 = $_POST['bulan3'];
		$tahun3 = $_POST['tahun3'];
		$periode3 = $tahun3.'-'.$bulan3.'-'.$tanggal3; 
		if ($periode3 == '--') {
			//$periode3 = $_POST['periode3']; 
			$periode3 = "0000-00-00";
		}
		*/
		if ($divprodid=="") {
		echo "BR harus diisi!<br><br>";
		echo "<input type=button name=cmdBack id=cmdBack value=Back onclick='go_back(1)'><br>";
		exit;
	}
        
		$entrymode = $_POST['entrymode'];
		$mode = $_POST['mode'];
	
		if ($entrymode=='E') {
		    $brid = $_POST['brid'];
			$query = "update hrd.br0 set brid='$brid',
									 tgl='$periode1',
									 kode='$kodeid',
									 aktivitas1='$aktiv1',
									 aktivitas2='$aktiv2',
									 ccyid='$ccyid',
									 jumlah='$jumlah',
									 realisasi1='$real1',
									 karyawanid='$karyawanid',
									 noslip='$noslip',
									 lampiran='$lamp',
									 tgltrans='$periode2',
									 user1='$userid',
									 icabangid='$icabangid',
									 divprodid='$divprodid',
									 trmskid='$trmskid',
									 tglacc='$periode3',
									 via='$via',
									 ca='$ca'
						where brid='$brid'"; //echo"$query";
			
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
			
			$query = "insert into hrd.br0 (brid,tgl,kode,aktivitas1,aktivitas2,ccyid,jumlah,realisasi1,karyawanid,noslip,lampiran,
									   tgltrans,user1,icabangid,divprodid,trmskid,tglacc,via,ca) 
					  values ('$brid','$periode1','$kodeid','$aktiv1','$aktiv2','$ccyid','$jumlah','$real1','$karyawanid','$noslip',
							  '$lamp','$periode2','$userid','$icabangid','$divprodid','$trmskid','$periode3','$via','$ca')"; 
							 // echo"$query";
		    
		}
			 
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
			}
		} else {
			echo "Error : ".mysqli_error();
			exit;
		} 
			
	}  // if (empty($_SESSION['srid'])) 
	if ($entrymode=="E") {
		
		echo "<input type=hidden name=divprodid id=divprodid value='$divprodid'>";
		echo "<input type=hidden name=kodeid id=kodeid value='$kodeid'>";
		if ($mode=="R") {
			$periode1 = substr($periode1,0,7); 
			echo "<input type=hidden name=periode1 id=periode1 value='$periode1'>";
			//echo "<br><input type=button id=cmdSave name=cmdSave value='Save' onclick='goto2(\"breq101\",\"rpbrsby1.php\")'>";
		} else {
			echo "<input type=hidden name=periode id=periode value='$periode2'>";
			//echo "<br><input type=button id=cmdSave name=cmdSave value='Save' onclick='goto2(\"breq101\",\"breq201.php\")'>";
		}
		
		
	} else {
		//echo "<input type=submit id=cmdOK name=cmdOK value='OK'>";
	}
?>
 
</form>
</body>
</html>


