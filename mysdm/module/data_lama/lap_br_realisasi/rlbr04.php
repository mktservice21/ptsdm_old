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
        include "../../../config/koneksimysqli.php";
        
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
		$brid = $_GET['brid'];
		
                
		$query = "update hrd.br0 set tgltrm='0000-00-00',lain2='' WHERE brid='$brid'"; //echo"$query";
		$result = mysqli_query($cnmy, $query);
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


