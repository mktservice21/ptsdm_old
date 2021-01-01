<html>
<head>
  <title>REPORT BR SBY</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="../../../images/icon.ico" />
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<body>

<form id="tgltr0" action="rpbrsby3.php" method=post>

<?php
//sebelumnya action ke rptgltr1.php
	include("../../../config/koneksimysqli_it.php");
	include("../../../config/common.php");
	//include("common3.php");
	session_start();
$result_dl="";
	if (empty($_SESSION['USERID'])) {
      echo 'not authorized';
      exit;
	} else {
		
		$divprodid = $_POST['divprodid'];
		$periode = $_POST['periode'];
		$tanggal1 = $_POST['tanggal1'];
		$bln1 = $_POST['bln1']; //echo"bln1=$bln1";
		$tahun1 = $_POST['tahun1'];
		$tgltrans = $tahun1.'-'.$bln1.'-'.$tanggal1; 
		
		$tanggal11 = $_POST['tanggal11'];
		$bln11 = $_POST['bln11']; //echo"bln1=$bln1";
		$tahun11 = $_POST['tahun11'];
		$tglacc = $tahun11.'-'.$bln11.'-'.$tanggal11; 
			
		if ($divprodid == 'EAGLE') {
			$num_results1 = $_POST['records1']; //echo"num=$num_results1<br>";
		
			for ($i=0;$i <= $num_results1;$i++) {
			    $j = "0000" . $i;
				$j = substr($j,-4);
			    $var_ = "kl" . $j; //echo"$var_";
				$var_ = $_POST[$var_];//echo"$var_<br>";
				
				if ($var_ <>"") {
				    $brsby = $var_; //echo"$custid";
					$query = "update hrd.klaim set trf='Y',tgltrans='$tgltrans' where klaimid='$brsby'"; echo"$query<br>";
					$result = mysqli_query($cnit, $query);
				}
			}
			if ($result) {	      
				echo "<br>Save OK!<br>";
			} else {
				$query_dll = "update hrd.klaim set trf='',tgltrans='$tgltrans' where klaimid='$brsby'"; echo"$query_dll";
				$result_dll = mysqli_query($cnit, $query_dll);
				if ($result_dl) {	      
					echo "<br>Save OK!<br>";
				} else {
					
				} 
			} 		
		}
		
		$num_results = $_POST['records']; //echo"num=$num_results<br>";
		
		for ($i=0;$i <= $num_results;$i++) {
		    $j = "0000" . $i;
			$j = substr($j,-4);
		    $var_ = "br" . $j;// echo"$var_";
                    
                    if (isset($_POST[$var_])) {
			$var_ = $_POST[$var_];//echo"$var_<br>";
                    }
			if ($var_ <>"") {
			    $brsby = $var_; //Echo"$brsby<br>";
				$query = "update hrd.br0 set trf='Y',tgltrans='$tgltrans' where brid='$brsby'"; echo"$query<br>";
				$result = mysqli_query($cnit, $query);
			}
			
			$a = "0000" . $i;
			$a = substr($a,-4);
		    $vbr_ = "acc" . $j; //echo"$var_";
                    if (isset($_POST[$vbr_])) {
			$vbr_ = $_POST[$vbr_];//echo"$vbr_<br>";
                    }
			
			if ($vbr_ <>"") {
			    $bracc = $vbr_; //echo"$custid";
				$query = "update hrd.br0 set acc='Y',app_acc='$tglacc' where brid='$bracc'"; echo"$query<br>";
				$result = mysqli_query($cnit, $query);
			}
		}
		if ($result) {	      
			echo "<br>Save OK!<br>";
		} else {
			$query_dll = "update hrd.br0 set trf='',tgltrans='$tgltrans' where brid='$brsby'"; echo"$query_dll";
			$result_dll = mysqli_query($cnit, $query_dll);
			if ($result_dl) {	      
				echo "<br>Save OK!<br>";
			} else {
				exit;
			} 
			
			$query_dll = "update hrd.br0 set acc='',app_acc='$tglacc' where brid='$bracc'"; echo"$query_dll";
			$result_dll = mysqli_query($cnit, $query_dll);
			if ($result_dl) {	      
				echo "<br>Save OK!<br>";
			} else {
				exit;
			} 
		} 
			
	}  // if (empty($_SESSION['srid'])) 
	
        
	//echo "<br><input type=submit name=cmdSave id=cmdSave value=OK>";
	echo "<input type=hidden name=divprodid value=$divprodid />";  
	echo "<input type=hidden name=periode value=$periode />";  
?>
 
</form>
</body>
</html>


