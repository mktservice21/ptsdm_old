<html>
<head>
  <title>Realisasi BR</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<body>
<form id="rlbr03" action="rlbr01.php" method=post>

<?php

	//include("common.php");
	//include("common3.php");
        include "../../../config/koneksimysqli_it.php";
        
	session_start();

	if (empty($_SESSION['IDCARD'])) {
      echo 'not authorized';
      exit;
	} else {
		
		$srid = $_SESSION['USERID'];
		$srnama = $_SESSION['NAMALENGKAP'];
		$sr_id = substr('0000000000'.$srid,-10);
		$userid = $_SESSION['USERID'];
		$tanggal = date('Y-m-d');
		$brid = $_POST['brid']; 
                
		//$tgltrm = $_POST['tgltrm']; 
		$tgltrans = $_POST['tgltrans']; 
		$lain2 = $_POST['lain2']; 
		$divprodid = $_POST['divprodid']; 
		$jumlah1 = $_POST['jumlah1'];
		$lampiran1 = $_POST['lampiran1'];
                $full_search="0";
                if (isset($_POST['chkFull']))
                    $full_search = $_POST['chkFull']; 
		$tanggal1 = $_POST['tanggal1'];
		$bln1 = $_POST['bln1']; //echo"bln1=$bln1";
		$tahun1 = $_POST['tahun1'];
		$tgltrm = $tahun1.'-'.$bln1.'-'.$tanggal1; 
		if ($tgltrm == '--') {
			$tgltrm = $_POST['tgltrm']; 
		}
		
		if ($tgltrm <>'0000-00-00') {
			$lampiran = 'Y';
		}
		$batal="";
		if ($full_search=="1") {
			$batal = 'Y';
		}
                
		$query = "update hrd.br0 set tgltrm='$tgltrm',lain2='$lain2',jumlah1='$jumlah1',lampiran='Y',batal='$batal' WHERE brid='$brid'"; //echo"$query";
		$result = mysqli_query($cnit, $query);
		if ($result) {	      
			echo "<br>Save OK!, tutup untuk kembali<br><br>";
		} else {
			echo "Error : ".mysql_error();
			exit;
		} 
	}
?>
 
</form>
</body>
</html>


