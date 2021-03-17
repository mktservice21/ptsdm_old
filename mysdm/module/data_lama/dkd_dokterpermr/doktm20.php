<html>
<head>
  <title>Data Dokter Per MR</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<form id="doktm20" action="doktm21.php" method=post>
<?php
	include("../../../config/koneksimysqli.php");
	include("../../../config/common.php");
	session_start();

	$tahun = date('Y') - 1;
	$srid = $_SESSION['USERID'];
	$karyawanId= $_SESSION['IDCARD'];
	$srnama = $_SESSION['NAMALENGKAP'];
	$sr_id = substr('0000000000'.$srid,-10);

	echo "<b>Data Dokter Per MR</b><br><br>";
	echo '<table>';
	$query = "select nama,jabatanid,tglkeluar,karyawanId from hrd.karyawan
			  where (jabatanid in ('08','10','15','18'))
			  and (tglkeluar='0000-00-00' or LEFT(tglkeluar,4)>='$tahun')
			  order by nama";
	
	$result = mysqli_query($cnmy, $query); 
	$num_results = mysqli_num_rows($result);	 // echo "$query <br> $num_results <br>";
	echo '<tr>';
	echo '<td align=right>MR/AM/SPV/DM : </td>';
	echo "<td><select name=\"karyawanid\" id=\"karyawanid\">";

	for ($i=0; $i < $num_results; $i++) {
		$row = mysqli_fetch_array($result);
		$str_ = $row['nama'];
		if ($row['karyawanId'] == $karyawanId) {
			echo '<option selected="selected" value="'.$row['karyawanId'].'">'.$str_.'</option>';
		} else {
			echo '<option value="'.$row['karyawanId'].'">'.$str_.'</option>';
		}
	}; 
	echo '</select><br />';
	echo '</td>';
	echo '</tr>';
	echo '</table>';

    $set_focus = "karyawanid";
	echo "<SCRIPT LANGUAGE='javascript'>\n";
	echo "   document.getElementById(\"$set_focus\").focus();\n";
	echo "</SCRIPT>\n";
	
	echo '<br><input type=submit value="Next"><br>';

	if (empty($_SESSION['srid'])) {
	} else {
		do_show_menu($_SESSION['jabatanid'],'N');
	}
?>
</form>
</body>
</html>

