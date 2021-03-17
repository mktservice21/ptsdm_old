<html>
<head>
  <title>Save</title>
</head>
<body>
<?php
	include("../../../config/koneksimysqli.php");
	include("../../../config/common.php");
  session_start();
  if (empty($_SESSION['USERID'])) {
     $srid = $_POST['srid'];
     $pin = $_POST['pin'];
  } else {
     $srid = substr('000000000'.$_SESSION['USERID'],-10);
  }

  $tgl = $_POST['tgl'];
  $dokter = $_POST['dokter'];
  $jv_dokter = $_POST['jv_dokter'];
  $compl = $_POST['compl'];
  $compl2 = $_POST['compl2'];
  $akt = $_POST['akt'];
  $saran = $_POST['saran'];
  $saran2 = $_POST['saran2'];
  $ket = $_POST['ket'];
  $ket2 = $_POST['ket2'];
  $ketid = $_POST['ketid'];
  $ketid2 = "";

  
  $query = "delete from hrd.dkd0 where srid='".$srid."' and tgl='".
           $tgl."'"; //echo"$query";
  $result0 = mysqli_query($cnmy, $query);
  $query = "delete from hrd.dkd1 where srid='".$srid."' and tgl='".
           $tgl."'";// echo"$query";
  $result0 = mysqli_query($cnmy, $query);
  
  //save to detail #1
  $dokter_array = explode(' ',$dokter);
  $num_dokter = count($dokter_array);  
  $saved_rec = 0;
  for ($i=0; $i < $num_dokter; $i++)
  {
     $dokterid = substr('0000000000'.$dokter_array[$i],-10);
     $query = "insert into hrd.dkd1 (srid,tgl,dokterid) values
             ('".$srid."','".$tgl."','".$dokterid."')"; //echo"$query";
     $result = mysqli_query($cnmy, $query);
     if ($result) {
        $saved_rec ++;
     } else {
       echo 'ErrSavingDetail #1'.$srid.'<br>';
     }  
  }
  
  //save to detail JV Dokter
  $saved_rec2 = 0;
  if ($jv_dokter != '') {
     $jv_dokter_array = explode(' ',$jv_dokter);
     $jv_num_dokter = count($jv_dokter_array);  
     for ($i=0; $i < $jv_num_dokter; $i++)
     {
        $jv_dokterid = substr('0000000000'.$jv_dokter_array[$i],-10);
        $query = "insert into hrd.dkd1 (srid,tgl,dokterid,jenis) values
               ('".$srid."','".$tgl."','".$jv_dokterid."','J')";//echo"$query";
        $result = mysqli_query($cnmy, $query);
        if ($result) {
           $saved_rec2 ++; 
        } else {
          echo 'ErrSavingDetail #2'.$srid.'<br>';
        }  
     }  // end for
  } // if 
  
  //save to header
  $query = "insert into hrd.dkd0 (srid,tgl,dokter,kompl,akt,saran,ket,
            kompl2,saran2,ketid,ketid2,ket2,jv_dokter,posted,Approval_status)
            values
            ('".$srid."','".$tgl."','".$dokter."','".$compl."',
             '".$akt."','".$saran."',
             '".$ket."',
             '".$compl2."',
             '".$saran2."',
             '".$ketid."',
             '".$ketid2."',
             '".$ket2."',
             '".$jv_dokter."','0','Pending')"; 
  $result = mysqli_query($cnmy, $query);
  if ($result)
     {
	  $query = "select sys_now from hrd.dkd0 where srid='$srid' and tgl='$tgl'";
	  $result = mysqli_query($cnmy, $query);
	  $row = mysqli_fetch_array($result);
	  $sys_now = $row['sys_now'];
      echo "<big>SaveOK<br>";
	  echo "$saved_rec dokter, $saved_rec2 JV dokter<br>";
	  echo "$sys_now</big><br><br>";
     }
  else
     {
      echo "<h1>ErrSavingHeader</h1><br>";
    }

  echo '<a href="mr0.php">Isi lagi</a><br>';
  if (empty($_SESSION['USERID'])) {
     echo '<a href="mr4.php">Rekap DKD</a>';
  } else {
     //echo '<a href="auth2.php">Menu</a><br>';
     //do_show_menu($_SESSION['jabatanid'],'N');
  }
?>  
</body>
</html>
