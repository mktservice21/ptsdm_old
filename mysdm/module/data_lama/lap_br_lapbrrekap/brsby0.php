<html>
<head>
    <title>REPORT BR SBY</title>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<body>
<form id="brsby0" action="rpbreq7.php" method=post>

<?php
        session_start();
	include("../../../config/common.php");
	//include("common3.php");
        include "../../../config/koneksimysqli.php";

	if (empty($_SESSION['IDCARD'])) {
      echo 'not authorized';
      exit;
	} else {
		
		$divprodid = $_POST['divprodid'];
		$periode = $_POST['periode'];
		$tanggal1 = $_POST['tanggal1'];
		$bln1 = $_POST['bln1']; //echo"bln1=$bln1";
		$tahun1 = $_POST['tahun1'];
		$tglbrsby = $tahun1.'-'.$bln1.'-'.$tanggal1; 
                
	/*	$query_dl = "update hrd.br0 set sby='' where divprodid='$divprodid'"; echo"$query_dl";

		$result_dl = mysqli_query($cnmy, $query_dl); 
		if ($result_dl) {	      
		} else {
			exit;
		} */
		$brsby = "";
		$result = "";
		if ($divprodid == 'EAGLE') {
			$num_results1 = $_POST['records1']; //echo"num=$num_results1<br>";
		
			for ($i=0;$i <= $num_results1;$i++) {
			    $j = "0000" . $i;
				$j = substr($j,-4);
			    $var_ = "kl" . $j; //echo"$var_";
                                if (isset($_POST[$var_])) {
                                    $var_ = $_POST[$var_];//echo"$var_<br>";
                                    
                                    if ($var_ <>"") {
                                        $brsby = $var_; //echo"$custid";
                                            $query = "update hrd.klaim set sby='Y',tglrpsby='$tglbrsby' where klaimid='$brsby'"; //echo"$query<br>";
                                            $result = mysqli_query($cnmy, $query);
                                    }
                                }
                                
			}
			if ($result) {	      
				echo "<br>Save OK!, Klik Back kemudian refresh halaman<br>";
			} else {
                            if (!empty($brsby)) {
				$query_dll = "update hrd.klaim set sby='',tglrpsby='$tglbrsby' where klaimid='$brsby'"; //echo"$query_dll";
				$result_dll = mysqli_query($cnmy, $query_dll);
				if ($result_dl) {	      
					echo "<br>Save OK!, Klik Back kemudian refresh halaman<br>";
				} else {
					
				} 
                            }
			} 		
		}
		
		$num_results = $_POST['records']; //echo"num=$num_results<br>";
		
		for ($i=0;$i <= $num_results;$i++) {
		    $j = "0000" . $i;
			$j = substr($j,-4);
                        $var_ = "br" . $j; //echo"$var_";
                        if (isset($_POST[$var_])) {
                            $var_ = $_POST[$var_];//echo"$var_<br>";

                            if ($var_ <>"") {
                                $brsby = $var_; //echo"$custid";
                                    $query = "update hrd.br0 set sby='Y',tglrpsby='$tglbrsby' where brid='$brsby'"; //echo"$query<br>";
                                    $result = mysqli_query($cnmy, $query);
                            }
			}
		}
		if ($result) {	      
			echo "<br>Save OK!, Klik Back kemudian refresh halaman<br>";
		} else {
                    if (!empty($brsby)) {
			$query_dll = "update hrd.br0 set sby='',tglrpsby='$tglbrsby' where brid='$brsby'"; //echo"$query_dll";
			$result_dll = mysqli_query($cnmy, $query_dll);
			if ($result_dl) {	      
				echo "<br>Save OK!, Klik Back kemudian refresh halaman<br>";
			} else {
				exit;
			} 
                    }
		} 
			
	}  // if (empty($_SESSION['srid'])) 
	
	echo "<br><input type=hidden name=cmdSave id=cmdSave value=OK>";
	echo "<input type=hidden name=divprodid value=$divprodid />";  
	echo "<input type=hidden name=periode value=$periode />"; 
?>
 <input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
</form>
</body>
</html>


