<html>
<head>
  <title>Rekap DKD Per Tahun</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<form action="mr410.php" method=post>

<?php
	include("../../../config/koneksimysqli.php");
	include("../../../config/common.php");
   session_start();

      if ($_SESSION['JABATANID']=='01' or $_SESSION['JABATANID']=='02' or $_SESSION['JABATANID']=='03' or 
          $_SESSION['JABATANID']=='04' or $_SESSION['JABATANID']=='07' or
          $_SESSION['JABATANID']=='05' or
          $_SESSION['JABATANID']=='06' or
          $_SESSION['JABATANID']=='12' or
          $_SESSION['JABATANID']=='09') {
         //dir, mm, amm, nsm, pm, admin
		 $bawahanid = $_POST['karyawanid']; // dari doktinf2.php (informasi dokter)
         $atasanid = $_SESSION['userid'];
         $atasan_id = substr('0000000000'.$atasanid,-10);
         $icabangid = $_SESSION['icabangid'];
         mysqli_select_db('hrd');
      }
      if ($_SESSION['JABATANID']=='08') {
         //dm
		 $bawahanid = $_POST['karyawanid']; // dari doktinf2.php (informasi dokter)
         $atasanid = $_SESSION['userid'];
         $atasan_id = substr('0000000000'.$atasanid,-10);
         $icabangid = $_SESSION['icabangid'];
         mysqli_select_db('hrd');
         $query = "select karyawanid,nama from karyawan where
                 icabangid='".$icabangid."' and
                 (jabatanid='10' or jabatanid='18' or jabatanid='15') 
                 order by nama";
				 // or karyawanid = '$atasan_id'
         $result = mysqli_query($cnmy, $query);
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
      if ($_SESSION['JABATANID']=='10' or $_SESSION['JABATANID']=='18') {
         //spv, koordinator
		 $bawahanid = $_POST['karyawanid']; // dari doktinf2.php (informasi dokter)
         $atasanid = $_SESSION['userid'];
         $atasan_id = substr('0000000000'.$atasanid,-10);
         mysqli_select_db('hrd');
         $query = "select karyawanid,nama from karyawan where
                 atasanid='".$atasan_id."' 
                 order by nama";
				 // or karyawanid='$atasan_id'
         $result = mysqli_query($cnmy, $query);
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

      } 
   echo '<table>';
   echo '<tr>';
   echo '<td>Tahun : </td>';
   echo '<td>';
   $tahun = date('Y');
   echo '<select name="tahun">';
   for ($i=2007; $i <= $tahun; $i++)
   {
      if ($i == $tahun) {
		 echo '<option selected="selected" value="'.$i.'">'.$i.'</option>';
	  } else {
         echo '<option value="'.$i.'">'.$i.'</option>';
	  }
   }; //end for
   echo '</select></td>'; // <br>';

   echo '</tr>';
   echo '</table>';
   echo '<br><input type=submit value="Lihat"><br>';

   if (empty($_SESSION['USERID'])) {
   } else {
      //echo '<a href="auth2.php">Menu</a><br>';
      //do_show_menu($_SESSION['JABATANID'],'N');
   }
?>

</form>
</html>

