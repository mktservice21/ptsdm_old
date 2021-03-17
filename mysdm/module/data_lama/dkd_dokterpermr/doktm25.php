<html>
<head>
  <title>DAFTAR CUSTOMER OTC MT</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<body>
<form id="doktm25" action="doktm21.php" method=post>

<?php
	include("../../../config/koneksimysqli.php");
	include("../../../common.php");
	include("../../../common3.php");
	

	if (empty($_SESSION['USERID'])) {
      echo 'not authorized';
      exit;
	} else {
		$karyawanid = $_POST['karyawanid'];
		$query_dl = "update hrd.mr_dokt set aktif='' where karyawanid='$karyawanid'"; //echo"$query_dl";

		$result_dl = mysqli_query($cnmy, $query_dl);
		if ($result_dl) {	      
		} else {
			exit;
		} 
		$num_results = $_POST['num_results']; //echo"num=$num_results";
		
		for ($i=0;$i <= $num_results;$i++) {
		    $j = "0000" . $i;
			$j = substr($j,-4);
		    $var_ = "dokt" . $j; //echo"$var_";
			$$var_ = $_POST[$var_];//echo"$$var_<br>";
			
			if ($$var_ <>"") {
			    $dokt = $$var_; //echo"$dokt";
				$query = "update hrd.mr_dokt set aktif='Y' where karyawanid='$karyawanid' and  dokterid='$dokt'";// echo"$query";
				$result = mysqli_query($cnmy, $query);
			}
		}
		if ($result) {	      
			echo "<br>Save OK!<br><br>";
		} else {
		} 
			
	}  // if (empty($_SESSION['srid'])) 
	echo "<input type=submit id=cmdOK name=cmdOK value='OK'>";
	echo "<input type=hidden name=karyawanid value=$karyawanid />";
   
	if (empty($_SESSION['srid'])) {
	} else {
		do_show_menu($_SESSION['jabatanid'],'N');
	}
?>
 
</form>
</body>
</html>


