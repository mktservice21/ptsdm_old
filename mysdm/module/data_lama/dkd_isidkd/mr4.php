<html>
<head>
  <title>Rekap DKD Per Bulan</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<?php
	include("../../../config/koneksimysqli.php");
	include("../../../config/common.php");
   session_start();

   echo '<form action="mr41.php" method=post>';
   if (empty($_SESSION['USERID'])) {
      echo 'ID : ';
      echo'<input type=text name="srid" size="10" maxlength="10"><br>';
   } else {
   
   
      if ($_SESSION['JABATANID']=='15') {
         //mr
         $srid = $_SESSION['USERID'];
         echo'<input type=hidden name="srid" size="10" value="'.$srid.'" maxlength="10"><br>';
      }
      if ($_SESSION['JABATANID']=='01' or $_SESSION['JABATANID']=='02' or $_SESSION['JABATANID']=='03' or 
          $_SESSION['JABATANID']=='04' or $_SESSION['JABATANID']=='07' or $_SESSION['JABATANID']=='05' or
          $_SESSION['JABATANID']=='06' or  $_SESSION['JABATANID']=='09') {
         //dir, mm, amm, nsm, pm, admin
		 $bawahanid = $_POST['karyawanid']; // dari doktinf2.php (informasi dokter)
         $atasanid = $_SESSION['USERID'];
         $atasan_id = substr('0000000000'.$atasanid,-10);
         $icabangid = $_SESSION['icabangid'];
         mysqli_select_db('hrd');
		  if ( $_SESSION['JABATANID']=='04' or $_SESSION['JABATANID']=='05') {
			   $userid = $_SESSION['USERID'];
			   $karyawanid = substr('0000000000'.$userid,-10);
			   if ($_SESSION['JABATANID']=='04') {
			     $karyawanid = '0000000154';
			   }
			 
		  $query = "select karyawanid,nama from hrd.karyawan
                   where
                   (jabatanid='08' or (jabatanid='10' or jabatanid='18') or
                    jabatanid='15') and tglkeluar='0000-00-00' and icabangid in (select icabangid from hrd.rsm_auth where karyawanid='$karyawanid') 
                   order by nama";
		 
		 } else {
         $query = "select karyawanid,nama from hrd.karyawan
                   where
                   (jabatanid='08' or (jabatanid='10' or jabatanid='18') or
                    jabatanid='15') and tglkeluar='0000-00-00'
                   order by nama";
		 }
         $result = mysqli_query($cnmy, $query);
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
	    if ($_SESSION['JABATANID']=='12') {
			$karyid = $_SESSION['srid'];
			$srnama = $_SESSION['srnama'];
			$kary_id= substr('0000000000'.$karyid,-10);

         //dir, mm, amm, nsm, pm, admin
		//  $bawahanid = $_POST['karyawanid']; // dari doktinf2.php (informasi dokter)
         $atasanid = $_SESSION['USERID'];
         $atasan_id = substr('0000000000'.$atasanid,-10);
         $icabangid = $_SESSION['icabangid'];
         mysqli_select_db('hrd');
         $query = "select karyawanid,nama from hrd.karyawan
                   where
                   (jabatanid='08' or (jabatanid='10' or jabatanid='18') or
                    jabatanid='15') and tglkeluar='0000-00-00' and  
					icabangid in (select hrd.rsm_auth.icabangid from hrd.rsm_auth where hrd.rsm_auth.karyawanid='$kary_id') 
                   order by nama"; //echo "$query";
         $result = mysqli_query($cnmy, $query);
         $num_results = mysqli_num_rows($result);
         echo 'DM/SPV/MR : ';
         echo '<select name="srid">';
         for ($i=0; $i < $num_results; $i++)
         {
            $row = mysqli_fetch_array($result);
            $str_ = $row['nama'];
			// if ($bawahanid == $row['karyawanid']) {
			   // echo '<option selected="selected" value="'.$row['karyawanid'].'">'.$str_.'</option>';
			// } else {
               echo '<option value="'.$row['karyawanid'].'">'.$str_.'</option>';
			// }
         }; //end for
         echo '</select><br>';

      }
	   if ($_SESSION['JABATANID']=='08') {
         //dm
		 $bawahanid = $_POST['karyawanid']; // dari doktinf2.php (informasi dokter)
         $atasanid = $_SESSION['USERID'];
         $atasan_id = substr('0000000000'.$atasanid,-10);
         $icabangid = $_SESSION['icabangid'];
         mysqli_select_db('hrd');
         $query = "select karyawanid,nama from hrd.karyawan where
                 icabangid='".$icabangid."' and
                 (jabatanid='10' or jabatanid='18' or jabatanid='15') and tglkeluar='0000-00-00'
                 order by nama";
             // or karyawanid = '$atasan_id'
         // echo $query;
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
      if ($_SESSION['JABATANID']=='10' or $_SESSION['JABATANID']=='18' or $_SESSION['JABATANID']=='20') {
         //spv, koordinator
		//  $bawahanid = $_POST['karyawanid']; // dari doktinf2.php (informasi dokter)
         $atasanid = $_SESSION['USERID'];
         $atasan_id = substr('0000000000'.$atasanid,-10);
         mysqli_select_db('hrd');
         $query = "select karyawanid,nama from hrd.karyawan where
                   (atasanid='".$atasan_id."' or atasanid2 ='".$atasan_id."'  ) and tglkeluar='0000-00-00'
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
			// if ($bawahanid == $row['karyawanid']) {
			   // echo '<option selected="selected" value="'.$row['karyawanid'].'">'.$str_.'</option>';
			// } else {
               echo '<option value="'.$row['karyawanid'].'">'.$str_.'</option>';
			// }
         }; //end for
         echo '</select></td>'; // <br>';
		 echo '</tr>';
		 echo '</table>';

      } else {
         //echo 'ID : ';
         //echo'<input type=text name="srid" size="10" maxlength="10"><br>';

      }
   }
   echo '<table>';
   echo '<tr>';
   echo '<td>Cycle : </td>';
   echo '<td><input type=radio checked="checked" name="cycleid"
         value="1">1&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
   echo '<input type=radio name="cycleid" value="2">2&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
   echo '<input type=radio name="cycleid" value="3">3&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
   echo '<input type=radio name="cycleid" value="4">4&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
   echo '<input type=radio name="cycleid" value="All">All Cycle&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
   echo '</tr>';
   echo '<tr>';
   echo '<td>Show : </td>';
   echo '<td><input type=radio checked="checked" name="show"
         value="1">All&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
   echo '<input type=radio name="show" value="2">DKD&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
   echo '<input type=radio name="show" value="3">Persentase Call&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
   echo '</tr>';
   

   echo '<tr>';
   echo '<td>Bulan : </td>';
   echo '<td>';
   mysqli_select_db('hrd');
   $tahun = date('Y');
   $tahun_1 = $tahun - 1;
   $curBln = date('Y-m');
   $query = "select * from hrd.cycle where left(thnbln,4)=".$tahun." group by thnbln";
   $query = "select * from hrd.cycle where $tahun_1 <= left(thnbln,4) and left(thnbln,4) <=$tahun group by thnbln";
   $result = mysqli_query($cnmy, $query);
   $num_results = mysqli_num_rows($result);
   echo '<select name="tgl1">';
   for ($i=0; $i < $num_results; $i++)
   {
  	  $row = mysqli_fetch_array($result);
      $query2 = "select min(tgl) as tgl from hrd.cycle where thnbln='".$row['thnbln']."'";
      $result2 = mysqli_query($cnmy, $query2);
      $num_result2 = mysqli_num_rows($result2);
  	  $row2 = mysqli_fetch_array($result2);
      $str_ = $row['thnbln'];
      if ($curBln == $row['thnbln']) {
		 echo '<option selected="selected" value="'.$row2['tgl'].'">'.$row['thnbln'].'</option>';
	  } else {
         echo '<option value="'.$row2['tgl'].'">'.$row['thnbln'].'</option>';
	  }
   }; //end for
   echo '</select></td>'; // <br>';
   echo '<tr>';
   echo '<td>   s/d. : </td>';
   echo '<td>';
   echo '<select name="tgl2">';
   mysqli_data_seek($result,0);
   for ($i=0; $i < $num_results; $i++)
   {
  	  $row = mysqli_fetch_array($result);
      $query2 = "select max(tgl) as tgl from hrd.cycle where thnbln='".$row['thnbln']."'";
      $result2 = mysqli_query($cnmy, $query2);
      $num_result2 = mysqli_num_rows($result2);
  	  $row2 = mysqli_fetch_array($result2);
      $str_ = $row['thnbln'];
      if ($curBln == $row['thnbln']) {
		 echo '<option selected="selected" value="'.$row2['tgl'].'">'.$row['thnbln'].'</option>';
	  } else {
         echo '<option value="'.$row2['tgl'].'">'.$row['thnbln'].'</option>';
	  }
   }; //end for

   echo '</tr>';
   echo '</table>';
   echo '<input type=submit value="View"><br>';

   if (empty($_SESSION['srid'])) {
   } else {
      //echo '<a href="auth2.php">Menu</a><br>';
      do_show_menu($_SESSION['JABATANID'],'N');
   }
?>

</form>
</html>

