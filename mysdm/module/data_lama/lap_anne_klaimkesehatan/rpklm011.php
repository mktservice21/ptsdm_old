<html>
<head>
  <title>Laporan Klaim Kesehatan</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<script src="sekt_sl.js">
</script>
<body>
<form action="rpklm01.php" method=post>

<?php
	include("config/koneksimysqli_it.php");
	include("config/common.php");
	include_once("module/data_lama/lap_anne_klaimkesehatan/fnklaim.php");
	session_start();
        
	$tahun = $_POST['tahun'];
        $detail="";
        $rekap="";
	if (isset($_POST['detail'])) $detail = $_POST['detail'];
	if (isset($_POST['rekap'])) $rekap = $_POST['rekap'];
	$srid = $_SESSION['USERID'];
	$srnama = $_SESSION['NAMALENGKAP'];
	$sr_id = substr('0000000000'.$srid,-10);
  
	
	$query = "select kobat0.*,jklaim.nama as nmklaim 
	          from hrd.kobat0 kobat0 
			  join hrd.jklaim jklaim on kobat0.kode=jklaim.kode
			  where left(tglklaim,4)='$tahun' and karyawanid='$sr_id'
			  order by tglklaim,an,kode";
    $result = mysqli_query($cnit, $query);
	$records = mysqli_num_rows($result);
	if ($detail) {
        klaim_detail($result,$records,$srnama,$tahun);
	} // end if rekap=="d"
	
	if ($rekap) {
	    klaim_rekap($query,$srnama,$tahun);
	}  // if rekap
        mysqli_close($cnit);
?>



</body>
</form>
</html>
