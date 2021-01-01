<html>
<head>
  <title>INPUT BR OTC</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <link rel="shortcut icon" href="../../../images/icon.ico" />
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<script src="brotc0.js">
</script>
<body>
<form id="brotc01" action="brotc00.php" method=post>

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
		$tanggal = date('Y-m-d');
		//tglbr
		$tanggal1 = $_POST['tanggal1'];
		$bulan1 = $_POST['bulan1'];
		$tahun1 = $_POST['tahun1'];
		$periode1 = $tahun1.'-'.$bulan1.'-'.$tanggal1; 
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
	
		$icabangid_o = $_POST['icabangid_o'];
		$subpost = $_POST['subpost'];
		$kodeid = $_POST['kodeid'];
		$tglrpsby = $_POST['tglrpsby'];
		// echo $tglrpsby;die();
		$keterangan1 = $_POST['keterangan1'];
		$keterangan2 = $_POST['keterangan2'];
		$jumlah = $_POST['jumlah_'];// echo"jumlah=$jumlah";
		$noslip = $_POST['noslip'];
		//$realisasi = $_POST['jumlah1_'];// echo"real=$realisasi";
		$real1 = $_POST['real1'];
        $bankreal1 = $_POST['bankreal1'];
        $cbreal1 = $_POST['cbreal1'];
        $norekreal1 = $_POST['norekreal1'];

		$ccyid = $_POST['ccyid'];
		$bralid = $_POST['bralid'];
                
                $chklamp="";
                if (isset($_POST['chklamp']))
                    $chklamp = $_POST['chklamp'];
                $chklamp1 = "";
                if (isset($_POST['chklamp1']))
                    $chklamp1 = $_POST['chklamp1'];
                $chklamp2 = "";
                if (isset($_POST['chklamp2']))
                    $chklamp2 = $_POST['chklamp2'];
	
		$lamp = 'N';
		if (trim($chklamp)=="on") {
		   $lamp = 'Y';
		}		
		
		$ca = 'N';
		if (trim($chklamp1)=="on") {
		   $ca = 'Y';
		}	

		$via = 'N';
		if (trim($chklamp2)=="on") {
		   $via = 'Y';
		}
		$chkjns="";
                if (isset($_POST['chkjns']))
                    $chkjns = $_POST['chkjns'];
                
                $chkjns1="";
                if (isset($_POST['chkjns1']))
                    $chkjns1 = $_POST['chkjns1'];
                
                $chkjns2="";
                if (isset($_POST['chkjns2']))
                    $chkjns2 = $_POST['chkjns2'];
		
                
                
                $jenis="";
                $periode="";
		if (trim($chkjns)=="on") {
		   $jenis = 'A';
		} else {
			if (trim($chkjns1)=="on") {
				$jenis = 'K';
			} else {
				if (trim($chkjns2)=="on") {
					$jenis = 'S';
				} else {
				}
			}
		}

		if ($tglrpsby == '0000-00-00') {
			$sby = 'N';
		} else {
			$sby = 'Y';
		}
		
	
		if (($kodeid=="") or ($kodeid=="blank")) {
                    echo "Alokasi Budget harus diisi!<br><br>";
                    echo "<input type=button name=cmdBack id=cmdBack value=Back onclick='go_back(1)'><br>";
                    exit;
                }
                
		$entrymode = $_POST['entrymode'];
		$mode = $_POST['mode'];
		$per1 = $_POST['per1'];
		$per2 = $_POST['per2'];
		
		
		if (trim($entrymode)=='E') {
		    $brotcid = $_POST['brotcid'];
			$query = "update hrd.br_otc set brotcid='$brotcid',
										icabangid_o='$icabangid_o',
										kodeid='$kodeid',
										subpost='$subpost',
										tglbr='$periode1',
										keterangan1='$keterangan1',
										keterangan2='$keterangan2',
										jumlah='$jumlah',
										tgltrans='$periode2',
										noslip='$noslip',		
										user1='$userid',
										real1='$real1',
                                        bankreal1='$bankreal1',
                                        cbreal1='$cbreal1',
                                        norekreal1='$norekreal1',
										lampiran='$lamp',
										via='$via',
										ca='$ca',
										ccyid='$ccyid',
										bralid='$bralid',
										tglrpsby='$tglrpsby',
										jenis='$jenis',
										sby='$sby' WHERE brotcid='$brotcid'";
			// echo"$query";
			
		} else {
			$brotcid = "0000000000";
			$query = "select nobrotc from hrd.setup0";
			$result = mysqli_query($cnit, $query);
			$num_results = mysqli_num_rows($result);
			$row = mysqli_fetch_array($result);
			if ($num_results) {
				$brotcid = $row['nobrotc'];
			}
			$brotcid = plus1($brotcid,10);
			
			$query = "insert into hrd.br_otc (brotcid,icabangid_o,kodeid,tglbr,keterangan1,keterangan2,jumlah,tgltrans,
										  noslip,user1,real1,bankreal1,cbreal1,norekreal1,lampiran,via,ca,ccyid,bralid,tglrpsby,jenis,sby)
					  values ('$brotcid','$icabangid_o','$kodeid','$periode1','$keterangan1','$keterangan2','$jumlah','$periode2',
							  '$noslip','$userid','$real1','$bankreal1','$cbreal1','$norekreal1','$lamp','$via','$ca','$ccyid','$bralid','$tglrpsby','$jenis','$sby')"; //echo"$query";
		}
			 
		$result = mysqli_query($cnit, $query);
		if ($result) {        
			echo "<br>Save OK!<br><br>";
			if ($entrymode=="E") {
			} else {
				$query = "update hrd.setup0 set nobrotc='$brotcid'";
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
	//echo"$entrymode";
	if ($entrymode=="E") {
		if ($mode=="R") {
			//$periode = substr($periode2,0,7);
			$periode1 = $per1;
			$periode2 = $per2;			
			echo "<input type=hidden name=per1 id=per1 value='$per1'>";
			echo "<input type=hidden name=per2 id=per2 value='$per2'>";
			echo "<br><input type=button id=cmdSave name=cmdSave value='Save' onclick='goto2(\"brotc01\",\"brotc11.php\")'>";
		} else {
			$periode1 = $per1;
			$periode2 = $per2;
			echo "<input type=hidden name=per1 id=per1 value='$per1'>";
			echo "<input type=hidden name=per2 id=per2 value='$per2'>";
			echo "<br><input type=button id=cmdSave name=cmdSave value='Save' onclick='goto2(\"brotc01\",\"brotc21.php\")'>";
		}
	
		echo "<input type=hidden name=icabangid_o id=icabangid_o value='$icabangid_o'>";
		echo "<input type=hidden name=bralid id=bralid value='$bralid'>";
		echo "<input type=hidden name=periode id=periode value='$periode'>";
	} else {
		echo "<input type=submit id=cmdOK name=cmdOK value='OK'>";
	}
	
?>
 
</form>
</body>
</html>


