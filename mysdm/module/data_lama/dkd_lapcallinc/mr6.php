<html>
<head>
  <title>Laporan call incentive</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<?php
	include("../../../config/koneksimysqli.php");
	include("../../../config/common.php");
   session_start();

   echo '<form action="mr61.php" method=post>';
   $tahun = date('Y');
	$tahun_1 = $tahun - 1;
	$tahun_2 = $tahun + 1;
	$bulan = "";//$_POST['bulan'];
   $tanggal2="";
   $tanggal1="";
   $karyawanid ="";
   $userid="";
   $karyawanid="";

	if ($bulan=="") {
		$bulan = date('m');
	}
	if ($tanggal2=="") {
		$tanggal2 = date('d');
	}
	
	if ($tanggal1=="") {
		$tanggal1 = start_of_month('d'); 
	}
	
   if (empty($_SESSION['USERID'])) {
      echo 'ID : ';
      echo'<input type=text name="srid" size="10" maxlength="10"><br>';
   } else {
	  echo "<b>LAPORAN CALL INCENTIVE</b><BR><br>";
      if ($_SESSION['JABATANID']=='15') {
         //mr
         $srid = $_SESSION['USERID'];
         echo'<input type=hidden name="srid" size="10" value="'.$srid.'" maxlength="10"><br>';
      }
      
      if ($_SESSION['JABATANID']=='01' or $_SESSION['JABATANID']=='02' or $_SESSION['JABATANID']=='03' or 
          $_SESSION['JABATANID']=='04' or $_SESSION['JABATANID']=='07' or $_SESSION['JABATANID']=='05' or 
		  $_SESSION['JABATANID']=='06' or $_SESSION['JABATANID']=='38' or $_SESSION['JABATANID']=='13' or
          $_SESSION['JABATANID']=='09') {
         //dir, mm, amm, nsm, pm, admin
		     $bawahanid = "";//$_POST['karyawanid']; // dari doktinf2.php (informasi dokter)
         $atasanid = $_SESSION['USERID'];
         $atasan_id = substr('0000000000'.$atasanid,-10);
         $icabangid = "";//$_SESSION['icabangid'];
         
         $query = "select karyawanid,nama from hrd.karyawan
                   where
                   (jabatanid='08' or (jabatanid='10' or jabatanid='18') or
                    jabatanid='15') and tglkeluar='0000-00-00'
                   order by nama";
         $result = mysqli_query($cnmy,$query);
         $num_results = mysqli_num_rows($result);
         echo 'DM/SPV/MR : ';
         echo '<select name="srid">';
         for ($i=0; $i < $num_results; $i++)
         {
            $row = mysqli_fetch_array($result);
            $str_ = $row['nama'];
			if ($bawahanid == $row['karyawanid']) {
			   echo '<option selected="selected" value="'.$row['karyawanid'].'">'.$str_.'</option>';
			} else {
               echo '<option value="'.$row['karyawanid'].'">'.$str_.'</option>';
			}
         }; //end for
         echo '</select><br>';

      }

      if ($_SESSION['JABATANID']=='08') {
         //dm
		     $bawahanid = $_POST['karyawanid']; // dari doktinf2.php (informasi dokter)
         $atasanid = $_SESSION['userid'];
         $atasan_id = substr('0000000000'.$atasanid,-10);
         $icabangid = $_SESSION['icabangid'];
         mysqli_select_db('hrd');
         $query = "select karyawanid,nama from hrd.karyawan where
                 icabangid='".$icabangid."' and
                 (jabatanid='10' or jabatanid='18' or jabatanid='15') and tglkeluar='0000-00-00'
                 order by nama";
				 // or karyawanid = '$atasan_id'
         $result = mysqli_query($cnmy,$query);
         $num_results = mysqli_num_rows($result);
         echo 'SPV & MR : ';
         echo '<select name="srid">';
         //dm himself
         echo '<option value="'.$atasan_id.'">'.$_SESSION['srnama'].'</option>';
         for ($i=0; $i < $num_results; $i++)
         {
            $row = mysqli_fetch_array($result);
            $str_ = $row['nama'];
			if ($bawahanid == $row['karyawanid']) {
			   echo '<option selected="selected" value="'.$row['karyawanid'].'">'.$str_.'</option>';
			} else {
               echo '<option value="'.$row['karyawanid'].'">'.$str_.'</option>';
			}
         }; //end for
         echo '</select><br>';

      }
      if ($_SESSION['JABATANID']=='10' or $_SESSION['JABATANID']=='18' or $_SESSION['JABATANID']=='20') {
         //spv, koordinator
		 $bawahanid = $_POST['karyawanid']; // dari doktinf2.php (informasi dokter)
         $atasanid = $_SESSION['userid'];
         $atasan_id = substr('0000000000'.$atasanid,-10);
         mysqli_select_db('hrd');
         $query = "select karyawanid,nama from karyawan where
                 atasanid='".$atasan_id."' and tglkeluar='0000-00-00'
                 order by nama";
				 // or karyawanid='$atasan_id'
         $result = mysqli_query($cnmy,$query);
         $num_results = mysqli_num_rows($result);
		 echo '<table>';
		 echo '<tr>';
         echo '<td>MR&nbsp;&nbsp;&nbsp; : </td>';
         echo '<td><select name="srid">';
         //spv himself
         echo '<option value="'.$atasan_id.'">'.$_SESSION['srnama'].'</option>';
         for ($i=0; $i < $num_results; $i++)
         {
            $row = mysqli_fetch_array($result);
            $str_ = $row['nama'];
			if ($bawahanid == $row['karyawanid']) {
			   echo '<option selected="selected" value="'.$row['karyawanid'].'">'.$str_.'</option>';
			} else {
               echo '<option value="'.$row['karyawanid'].'">'.$str_.'</option>';
			}
         }; //end for
         echo '</select></td>'; // <br>';
		 echo '</tr>';
		 echo '</table>';

      } else {
      }
   }
   echo '<table>';
   
   echo '<tr>';
   echo '<td align=right>Bulan : </td>';
	echo '<td><select name="bulan1" id="bulan1">';
	for ($i=0; $i<12; $i++) {
		$j = '0'.ltrim(strval($i+1));
		$j = substr($j,-2,2);
		$bln_ = nama_bulan($j);
		if ($j == $bulan) {
			echo "<option selected='selected' value='$j'>$bln_</option>";	
		} else {
			echo "<option value='$j'>$bln_</option>";					
		}
	}		
	echo '</select>';
	
	echo '&nbsp;&nbsp;<select name="tahun1" id="tahun1">';
	echo "<option value='$tahun_1'>$tahun_1</option>";
	echo "<option selected='selected' value='$tahun'>$tahun</option>";
	echo "<option value='$tahun_2'>$tahun_2</option>";
	echo '</select>';		
	echo '</td>';	
	echo "</tr>";
   echo '</table>';
   echo '<br><input type=submit value="View"><br>';

   if (empty($_SESSION['USERID'])) {
   } else {
      //echo '<a href="auth2.php">Menu</a><br>';
      //do_show_menu($_SESSION['JABATANID'],'N');
   }
?>

</form>
</html>

