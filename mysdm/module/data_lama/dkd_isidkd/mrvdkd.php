<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>DKD View Per MR</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<body>

<body class="blurBg-false" style="background-color:#EBEBEB">
<script type="text/javascript" src="../jquery-ui-1.12.1/external/jquery/jquery.js" ></script>
<script type="text/javascript" src="../jquery-ui-1.12.1/jquery-ui.js" ></script>
<script type="text/javascript" src="../jquery-ui-1.12.1/jquery-ui.min.js" ></script>
<link rel="stylesheet" href="../jquery-ui-1.12.1/jquery-ui.css" type="text/css" media="all">
<link rel="stylesheet" href="../jquery-ui-1.12.1/jquery-ui.min.css" type="text/css" media="all">
<link rel="stylesheet" href="../jquery-ui-1.12.1/jquery-ui.theme.css" type="text/css" media="all">
<link rel="stylesheet" href="../jquery-ui-1.12.1/jquery-ui.theme.min.css" type="text/css" media="all">
<script>
  function jvDate(){
    $('#tgl1,#tgl2').datepicker({
      altFormat: 'yy-mm-dd',
      dateFormat: 'yy-mm-dd',
      changeMonth: true,
      changeYear: true,
      useThisTheme: 'UI darkness',
      beforeShow: function(){
        var zindex = $('div.ui-dialog').css('z-index');
        $('#ui-datepicker-div').css('z-index', zindex + 1);
      }
    });
  }
</script>

<?php
	include("../../../config/koneksimysqli.php");
	include("../../../config/common.php");
  session_start();

  echo '<form action="mrvdkd1.php" method=post>';
  if ($_SESSION['JABATANID']=='15') {
      //mr
      $srid = $_SESSION['IDCARD'];
     echo '<input type=hidden name="srid" size="10" value="'.$srid.'" maxlength="10"><br>';
  } else {
      if ($_SESSION['JABATANID']=='01' or $_SESSION['JABATANID']=='02' or $_SESSION['JABATANID']=='03' or 
          $_SESSION['JABATANID']=='04' or $_SESSION['JABATANID']=='07' or
          $_SESSION['JABATANID']=='05' or $_SESSION['JABATANID']=='06' or
          $_SESSION['JABATANID']=='13' or $_SESSION['JABATANID']=='09') {
         //dir, mm, amm, nsm, pm, admin, rsm
         $atasanid = $_SESSION['IDCARD'];
         $atasan_id = substr('0000000000'.$atasanid,-10);
         $icabangid = "";//$_SESSION['icabangid'];
		 
		 if ( $_SESSION['JABATANID']=='04' or $_SESSION['JABATANID']=='05') {
			   $userid = $_SESSION['IDCARD'];
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
            echo '<option value="'.$row['karyawanid'].'">'.$str_.'</option>';
         }; //end for
         echo '</select><br>';

     }

	  if ($_SESSION['JABATANID']=='12') {
			$karyid = $_SESSION['IDCARD'];
			$srnama = $_SESSION['srnama'];
			$kary_id= substr('0000000000'.$karyid,-10);

         //dir, mm, amm, nsm, pm, admin
		 $bawahanid = $_POST['karyawanid']; // dari doktinf2.php (informasi dokter)
         $atasanid = $_SESSION['IDCARD'];
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
         $atasanid = $_SESSION['IDCARD'];
         $atasan_id = substr('0000000000'.$atasanid,-10);
         $icabangid = $_SESSION['icabangid'];
         mysqli_select_db('hrd');
         $query = "select karyawanid,nama from hrd.karyawan where
                 icabangid='".$icabangid."' and
                 (jabatanid='10' or jabatanid='18' or jabatanid='15') and tglkeluar='0000-00-00'
                 order by nama";
				 
				// echo "$query";
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
            echo '<option value="'.$row['karyawanid'].'">'.$str_.'</option>';
         }; //end for
         echo '</select><br>';

     }

     if ($_SESSION['JABATANID']=='10' or $_SESSION['JABATANID']=='18') {
        //spv, koordinator
         $atasanid = $_SESSION['IDCARD'];
         $atasan_id = substr('0000000000'.$atasanid,-10);
         mysqli_select_db('hrd');
         $query = "select karyawanid,nama from hrd.karyawan where
                 atasanid='".$atasan_id."' and tglkeluar='0000-00-00'
                 order by nama";
         $result = mysqli_query($cnmy, $query);
         $num_results = mysqli_num_rows($result);
         echo 'MR : ';
         echo '<select name="srid">';
         //spv himself
         echo '<option value="'.$atasan_id.'">'.$_SESSION['srnama'].'</option>';
         for ($i=0; $i < $num_results; $i++)
         {
            $row = mysqli_fetch_array($result);
            $str_ = $row['nama'];
            echo '<option value="'.$row['karyawanid'].'">'.$str_.'</option>';
         }; //end for
         echo '</select><br>';
     }
  }
  echo '
    Periode. : <input type=text name="tgl1" id="tgl1" value="'.start_of_month(date('Y-m-d')).'" size="10" maxlength="10" onclick="jvDate();" readonly> 
    s/d <input type=text name="tgl2" id="tgl2" value="'.date('Y-m-d').'" size="10" maxlength="10" onclick="jvDate();" readonly><br>
    <input type=submit value="Proses"><br>
  ';

  if (empty($_SESSION['IDCARD'])) {
  } else {
     //echo '<a href="auth2.php">Menu</a><br>';
     //do_show_menu($_SESSION['JABATANID'],'N');
  }
  
?>

</form>
</body>
</html>
