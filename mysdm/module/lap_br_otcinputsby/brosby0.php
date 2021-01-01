<html>
<head>
  <title>REPORT BR SBY</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<body>
<form id="brsby0" action="rpbosby1.php" method=post>

<?php
	include("common.php");
	include("common3.php");
	session_start();
	if (!connect_server())
	{
		echo 'Error connection to database';
		exit;
	}

	if (empty($_SESSION['srid'])) {
      echo 'not authorized';
      exit;
	} else {
		mysql_select_db('hrd');
		$periode = $_POST['periode'];
		$tanggal1 = $_POST['tanggal1'];
		$bln1 = $_POST['bln1']; //echo"bln1=$bln1";
		$tahun1 = $_POST['tahun1'];
		$tglbrsby = $tahun1.'-'.$bln1.'-'.$tanggal1; 
		$jenis = $_POST['jenis'];

		
		$num_results = $_POST['records']; //echo"num=$num_results<br>";
		
		for ($i=0;$i <= $num_results;$i++) {
		    $j = "0000" . $i;
			$j = substr($j,-4);
		    $var_ = "br" . $j; //echo"$var_";
			$$var_ = $_POST[$var_];//echo"$$var_<br>";
			
			if ($$var_ <>"") {
			    $brsby = $$var_; //echo"$custid";
				$query = "update br_otc set sby='Y',tglrpsby='$tglbrsby',jenis='$jenis' where brOtcId='$brsby'"; //echo"$query<br>";
				$result = mysql_query($query);
			}
		}
		if ($result) {	      
			echo "<br>Save OK!<br>";
		} else {
			$query_dll = "update br_otc set sby='',tglrpsby='$tglbrsby',jenis='$jenis' where brOtcId='$brsby'";//echo"$query_dll";
			$result_dll = mysql_query($query_dll);
			if ($result_dl) {	      
				echo "<br>Save OK!<br>";
			} else {
				exit;
			} 
		} 
			
	}  // if (empty($_SESSION['srid'])) 
	
	echo "<br><input type=submit name=cmdSave id=cmdSave value=OK>"; 
	echo "<input type=hidden name=periode value=$periode />";  
	if (empty($_SESSION['srid'])) {
	} else {
		do_show_menu($_SESSION['jabatanid'],'N');
	}
?>
 
</form>
</body>
</html>


