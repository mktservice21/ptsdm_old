<html>
<head>
  <title>Isi BR OTC</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <link rel="shortcut icon" href="../../../images/icon.ico" />
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<script src="brotc.js">
</script>
<body>
<form id="brotc41" action="brotc11.php" method=post>

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
		$brotcid = $_POST['brotcid']; //echo"$brid";
		$tgltrans = $_POST['tgltrans']; 
		$tglbr = $_POST['tglbr']; 
		//$jumlah = $_POST['jumlah_'];
		//$real1 = $_POST['real1'];
		$noslip = $_POST['noslip'];
		$bralid = $_POST['bralid'];
		$mode = $_POST['mode'];
		$icabangid_o = $_POST['icabangid_o'];
		
		$tanggal1 = $_POST['tanggal1'];
		$bln1 = $_POST['bln1']; //echo"bln1=$bln1";
		$tahun1 = $_POST['tahun1'];
		$tglreal = $tahun1.'-'.$bln1.'-'.$tanggal1; 
		if ($tglreal == '--') {
			$tglreal = $_POST['tglreal']; 
		}
		
		if ($tglreal <>'0000-00-00') {
			$lampiran = 'Y';
		}
                $jumlah1 = "";
		// echo"mode=$mode";
		
		if ($jumlah1=='') {
		    $jumlah1 = $_POST['jumlah1'];
			$query = "update hrd.br_otc set 
									 realisasi='$jumlah1',
									 tglreal='$tglreal',
									 lampiran='Y',ca='N',
									 noslip='$noslip'
					  where brotcid='$brotcid'";// echo"$query";
		} else {
			$query = "insert into hrd.br0 (realisasi,tglreal,lampiran,ca,noslip) values ('$jumlah1','$tglreal','Y','N','$noslip') where brotcid='$brotcid'"; //echo"$query";
		}
			 
		$result = mysqli_query($cnit, $query);
		if ($result) {	      
			echo "<br>Save OK!<br><br>";
		} else {
			echo "Error : ".mysqli_error();
			exit;
		} 
			
	} 
		/*if ($mode=='B') {
			$periode = substr($tglbr,0,7);
		} else {
			if ($mode=='R') {
				$periode = substr($tgltrans,0,7);
			} else {
			}
		}*/
		
		if ($mode=='B') {
			$periode = $tglbr;
		} else {
			if ($mode=='R') {
				$periode = $tgltrans;
			} else {
			}
		}
		
		
		
		 
		echo "<input type=hidden id='icabangid_o' name='icabangid_o' value='$icabangid_o'>";
		echo "<input type=hidden name=bralid id=bralid value='$bralid'>";
		echo "<input type=hidden name=periode id=periode value='$periode'>";
		if ($mode=='B') {
			echo "<input type=button name=cmdOK id=cmdOK value=OK onclick='goto2(\"brotc41\",\"brotc21.php\")'>&nbsp;&nbsp;&nbsp;";
		} else {
			if ($mode=='R') {
				echo "<input type=button name=cmdOK id=cmdOK value=OK onclick='goto2(\"brotc41\",\"brotc11.php\")'>&nbsp;&nbsp;&nbsp;";
			} else {
			}
		}
		//echo "<input type=submit id=cmdOK name=cmdOK value='OK'>";
   
	if (empty($_SESSION['srid'])) {
	} else {
		do_show_menu($_SESSION['jabatanid'],'N');
	}
?>
 
</form>
</body>
</html>


