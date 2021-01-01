<html>
<head>
  <title>Hapus Data Budget Request</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<script src="breq6.js">
</script>
<body onload="setFocus('nama')">
<form id="breq202" action="breq201.php" method=post>

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
	 $srid = $_SESSION['srid'];
     $srnama = $_SESSION['srnama'];
     $sr_id = substr('0000000000'.$srid,-10);
	 $userid = $_SESSION['userid'];
	 $icabangid = $_POST['icabangid'];			
	 $divprodid = $_POST['divprodid'];
	 $kodeid = $_POST['kodeid'];
	 $entrymode = $_POST['entrymode'];
	 $brid = $_POST['brid'];
	 $namadkt = $_POST['namadkt'];
	 $bulan = $_POST['bulan'];
	// echo"$bulan";
	 //echo "$kodeid";
	 //echo "<br>Hapus Data<br>";
	 $query = "delete from br0 where brId = '$brid'";
	 $result = mysql_query($query);
	 if ($result) {
	    echo "<br>Data BR <b>$namadkt</b> sudah dihapus!<br><br>";		 
	 } else {
	    echo "Error : ".mysql_error();
		exit;		 
	 }
		 
	 echo "<input type=submit id=cmdHapus name=cmdHapus value='OK'>";
     echo "<input type=hidden id='periode' name='periode' value='$bulan'>";
	 echo "<input type=hidden id='icabangid' name='icabangid' value='$icabangid'>";
	 echo "<input type=hidden id='divprodid' name='divprodid' value='$divprodid'>";
	 echo "<input type=hidden id='kodeid' name='kodeid' value='$kodeid'>";
   }  // if (empty($_SESSION['srid'])) 
   
   
   
   if (empty($_SESSION['srid'])) {
   } else {
      do_show_menu($_SESSION['jabatanid'],'N');
   }
?>
 
</form>
</body>
</html>


