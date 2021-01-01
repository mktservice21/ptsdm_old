<html>
<head>
  <title>Hapus Data BR OTC</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <link rel="shortcut icon" href="../../../images/icon.ico" />
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<script src="brotc0.js">
</script>
<body onload="setFocus('nama')">
<form id="brotc02" action="" method=post>

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
	 $bralid = $_POST['bralid'];
	 $entrymode = $_POST['entrymode'];
	 $icabangid_o = $_POST['icabangid_o'];
	 $brotcid = $_POST['brotcid'];
	 $periode = $_POST['periode']; 
	 $periode1 = $_POST['periode1']; 
	 $mode = $_POST['mode']; 
	//echo"$mode";exit;
	
    $query = "DELETE from dbmaster.backup_br_otc WHERE brOtcId ='$brotcid'";
	mysqli_query($cnit, $query);
         
    $query = "INSERT INTO dbmaster.backup_br_otc SELECT * from hrd.br_otc WHERE brOtcId ='$brotcid'";
    mysqli_query($cnit, $query);
		 
	 $query = "DELETE from hrd.br_otc WHERE brOtcId ='$brotcid'";
	 $result = mysqli_query($cnit, $query);

	 if ($result) {
	    echo "<br>Data BR OTC sudah dihapus!<br><br>";		 
	 } else {
	    echo "Error : ".mysqli_error();
		exit;		 
	 }
	// echo"$entrymode";
	 $periode = substr($periode,0,7);	 
	 
	if ($mode=="R") {
		$periode = substr($periode,0,7); 
		echo "<br><input type=button id=cmdSave name=cmdHapus value='OK' onclick='goto2(\"brotc02\",\"brotc11.php\")'>";
	} else {
		$periode = substr($periode1,0,7); 
		echo "<br><input type=button id=cmdSave name=cmdHapus value='OK' onclick='goto2(\"brotc02\",\"brotc21.php\")'>";
	}

	echo "<input type=hidden name=icabangid_o id=icabangid_o value='$icabangid_o'>";
	echo "<input type=hidden name=bralid id=bralid value='$bralid'>";
	echo "<input type=hidden name=periode id=periode value='$periode'>";
	
   }  // if (empty($_SESSION['srid'])) 
   
   
?>
 
</form>
</body>
</html>


