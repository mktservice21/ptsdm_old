<html>
<head>
  <title>REPORT BR SBY</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="../../../images/icon.ico" />
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<body>
<form id="brsby0" action="rpbosby1.php" method=post>

<?php
	include("../../../config/common.php");
	//include("common3.php");
	include("../../../config/koneksimysqli_it.php");
	session_start();

	if (empty($_SESSION['IDCARD'])) {
      echo 'not authorized';
      exit;
	} else {
		
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
                    
                    if (isset($_POST[$var_])) {
			$var_ = $_POST[$var_];//echo"$var_<br>";
			if ($var_ <>"") {
			    $brsby = $var_; //echo"$custid";
				$query = "update hrd.br_otc set sby='Y',tglrpsby='$tglbrsby',jenis='$jenis' WHERE brOtcId='$brsby'"; //echo"$query<br>";
				$result = mysqli_query($cnit, $query);
			}
                    }
		}
		if ($result) {	      
			echo "<br>Save OK!<br>";
		} else {
			$query_dll = "update hrd.br_otc set sby='',tglrpsby='$tglbrsby',jenis='$jenis' WHERE brOtcId='$brsby'";//echo"$query_dll";
			$result_dll = mysqli_query($cnit, $query_dll);
			if ($result_dl) {	      
				echo "<br>Save OK!<br>";
			} else {
				exit;
			} 
		} 
			
	}  // if (empty($_SESSION['srid'])) 
	
	echo "<br><input type=submit name=cmdSave id=cmdSave value=OK>"; 
	echo "<input type=hidden name=periode value=$periode />";  
?>
 
</form>
</body>
</html>


