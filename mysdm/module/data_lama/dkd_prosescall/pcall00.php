<html>
<head>
  <title>Upload Persentase Call</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<script src="sekt_sl.js">
</script>
<script src="pcall.js">
</script>
<body>
<form id="pcall00" action="pcall01.php" method=post>
<?php

	include("../../../config/koneksimysqli.php");
	include("../../../config/common.php");
   session_start();
   

   if (empty($_SESSION['USERID'])) {
      echo 'not authorized';
      exit;
   } else {
         //setfocus
		 $set_focus = "";//$_POST['set_focus'];
		 if ($set_focus=="") {		    
			$set_focus = "icabangid";
		 }
		 		 
         $srid = $_SESSION['USERID'];
         $srnama = $_SESSION['NAMALENGKAP'];
		 $sr_id = substr('0000000000'.$srid,-10);
		 $tahun = date('Y');
		 $tahun_1 = $tahun - 1;
		 $tahun_2 = $tahun + 1;
		 $bulan = date('m');;//$_POST['bulan'];
		  
		 $icabangid = "";//$_POST['icabangid'];
		 $distid = "";//$_POST['distid'];
		 $srid = "";//$_POST['srid'];
		 $areaid = "";//$_POST['areaid'];
		
		 echo "<b>Persentase Call</b><br><br>";
		 echo '<table>';
		 		 
		 echo '<tr>';
		 echo '<td align=right>Periode : </td>';
		 echo '<td><select name="tahun" id="tahun">';
		 echo "<option value='$tahun_1'>$tahun_1</option>";
		 echo "<option selected='selected' value='$tahun'>$tahun</option>";
		 echo "<option value='$tahun_2'>$tahun_2</option>";
         echo '</select>';
		 echo ' - ';
		 echo '<select name="bulan">';
         for ($i=1; $i <= 12; $i++)
         {   
		     $i_ = substr('0'.$i,-2);			 
			 if ($i == $bulan) {
		        echo "<option selected='selected' value='$i_'>$i_</option>";
			 } else {
                echo "<option value='$i_'>$i_</option>";			 
			 }		 
		 }
         echo '</select>';
		 echo '</td></tr>';	     		 	

		 $query = "SELECT icabangid,nama FROM mkt.icabang WHERE aktif = 'Y' AND nama NOT LIKE 'eth -%' AND nama NOT LIKE 'OTH%' AND nama NOT LIKE 'eth -%' ORDER BY nama";
         $result = mysqli_query($cnmy, $query);
         $num_results = mysqli_num_rows($result);
  	     echo '<tr>';
         echo '<td align=right>Cabang : </td>';
         echo "<td><select name=\"icabangid\" onchange=\"get_areaid()\">";
		 echo '<option value="(blank)">(blank)</option>';
         for ($i=0; $i < $num_results; $i++)
         {
            $row = mysqli_fetch_array($result);
            $str_ = $row['nama'];
			//$icabangid = $row['icabangid'];
			if ($row['icabangid'] == $icabangid) {
               echo '<option selected="selected" value="'.$row['icabangid'].'">'.$str_.'</option>';
			} else {
               echo '<option value="'.$row['icabangid'].'">'.$str_.'</option>';
			}
         }; //end for
         echo '</select><br />';
		 echo '</td>';
		 echo '</tr>';
		 
         echo '</table>';
	 

		 echo "<br><input type=button id=cmdUpload name=cmdUpload value='Upload' onclick='upload(\"Upload data ?\")'>";
		 //echo "&nbsp&nbsp&nbsp<input type=button id=cmdReport name=cmdReport value='Report' onclick='simpan_cancel()'>";
	 
         //echo '<br><input type=submit value="Next"><br>';
		 
		 echo '<input type="hidden" id="set_focus" name="set_focus" value="'.$set_focus.'" />';
		 echo "<SCRIPT LANGUAGE='javascript'>\n";
		 echo "   set_focus('$set_focus');\n";
		 echo "</SCRIPT>\n";
		 
   }  // if (empty($_SESSION['USERID'])) 

   if (empty($_SESSION['USERID'])) {
   } else {
      //do_show_menu($_SESSION['jabatanid'],'N');
   }
?>
</form>
</body>
</html>

