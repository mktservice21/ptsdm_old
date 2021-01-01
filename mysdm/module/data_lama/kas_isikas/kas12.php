<html>
<head>
  <title>Hapus Data Kas Kecil</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="../../../images/icon.ico" />
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<script src="kas1.js">
</script>
<body onload="setFocus('nama')">
<form id="kas12" action="kas11.php" method=post>

<?php
	include("../../../config/common.php");
	//include("common3.php");
	include("../../../config/koneksimysqli.php");
   session_start();


   if (empty($_SESSION['IDCARD'])) {
      echo 'not authorized';
      exit;
   } else {
	  
     
		$srid = $_SESSION['USERID'];
		$srnama = $_SESSION['NAMALENGKAP'];
		$sr_id = substr('0000000000'.$srid,-10);
		$userid = $_SESSION['IDCARD'];
                
	 $kodeid = $_POST['kodeid'];
	 $entrymode = $_POST['entrymode'];
	 $kasid = $_POST['kasid'];
	 $periode1 = $_POST['periode1']; 
	
	$ptahun_pilp = date("Y", strtotime($periode1));

	
	$query = "INSERT INTO hrd.kas_reject (kasId, karyawanid, nama, periode1, kode, aktivitas1, aktivitas2, 
			nobukti, jumlah, periode2, user1, trmskid, coa4) select kasId, karyawanid, nama, periode1, kode, aktivitas1, aktivitas2, 
			nobukti, jumlah, periode2, user1, trmskid, coa4 from hrd.kas WHERE kasId ='$kasid' LIMIT 1";
	mysqli_query($cnmy, $query);
	$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
	
	
	 $query = "delete from hrd.kas WHERE kasId ='$kasid' LIMIT 1";
	 $result = mysqli_query($cnmy, $query);
         $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
	 if ($result) {
	    echo "<br>Data Kas Harian sudah dihapus!<br><br>";		 
	 } else {
	    echo "Error : ".mysqli_error();
		exit;		 
	 }
	

	
	if ($ptahun_pilp=="2019") {
		$query = "UPDATE dbmaster.t_proses_bm_act SET hapus_nodiv_kosong='Y' where idkodeinput='$kasid' and kodeinput='C' and tahun='$ptahun_pilp' LIMIT 1";
		mysqli_query($cnmy, $query);
		$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan." t_proses_bm_act "; exit; }

	}
	
		 
	 echo "<input type='button' id=cmdHapus name=cmdHapus value='OK'>";
     echo "<input type=hidden id='periode1' name='periode1' value='$periode1'>";
	 echo "<input type=hidden id='kodeid' name='kodeid' value='$kodeid'>";
   }  // if (empty($_SESSION['srid'])) 

?>
 
</form>
</body>
</html>


