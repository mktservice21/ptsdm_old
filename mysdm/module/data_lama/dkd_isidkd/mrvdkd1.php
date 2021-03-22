<html>
<head>
  <title>DKD View</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<body>

<?php
	include("../../../config/koneksimysqli.php");
	include("../../../config/common.php");
  session_start();
  $srid = $_POST['srid'];
  $tgl1  = $_POST['tgl1'];
  $tgl2  = $_POST['tgl2'];

  $sr_id = substr('0000000000'.$srid,-10);
  $query = "select nama,pin,atasanid from hrd.karyawan where
           karyawanId='".$sr_id."'";
  $result = mysqli_query($cnmy, $query);
  $row = mysqli_fetch_array($result);
  $num_results = mysqli_num_rows($result);
  $ok2_ = 1;
  $nama_sr = $row['nama'];
  echo '<strong>'.$nama_sr.'</strong><br>';

  if ($ok2_) {
     $query = "select dkd0.srid,dkd0.tgl,dkd0.kompl,dkd0.kompl2,
               dkd0.akt,dkd0.saran,dkd0.saran2,dkd0.ket,
			   dkd0.ket2 as ket2_,
               dkd0.ketid,dkd0.ketid2,
               dkd0.posted,dkd0.Approval_Status as Status, ket.nama as ket_nama,
			   dkd0.jv_dokter,
               ket2.nama as ket_nama2, karyawan.nama as App_By, Tgl_App as App_Date, dkd0.Sys_Now as Created_Date 
               from hrd.dkd0
               left join hrd.ket on dkd0.ketid=ket.ketid
               left join hrd.ket as ket2 on dkd0.ketid2=ket2.ketid
			   left join hrd.karyawan on dkd0.srid_app = karyawan.karyawanID
               where
               (dkd0.srId='".$sr_id."') and
               ('".$tgl1."' <= tgl and tgl <= '".$tgl2."')
               order by tgl
      ";
      //echo"$query";


     $result = mysqli_query($cnmy, $query);
     $num_results = mysqli_num_rows($result);

     for ($i=0; $i < $num_results; $i++)
     {
       $row = mysqli_fetch_array($result);
       $tgl_ = $row['tgl'];
       // echo $row['Posted'].'~'.$row['Status'].'<br>';
       //echo $tgl_;
       if ($row['posted']=='1' and $row['Status']=='Approved' ) {
          echo '<font color="green"><b>'.$tgl_.' (Approved by '.$row['App_By'].' on '.$row['App_Date'].')</b></font><br>'; 
	     } elseif ($row['posted']=='0' and $row['Status']=='Suspend' ) {
		   echo '<font color="red"><b>'.$tgl_.' (Suspend)</b> DKD Created on '.$row['Created_Date'].'</font><br>';
	     } elseif ($row['posted']=='0' and $row['Status']=='Pending' ) {
		   echo '<font color="Orange"><b>'.$tgl_.' (Pending Approval)</b> DKD Last Edit on '.$row['Created_Date'].'</font><br>';		   
       } else {
          echo $tgl_.'<br>';
       } 

       // cetak detail
      $query2 = "
        SELECT dkd1.srid,dkd1.tgl,dkd1.dokterid,dkd1.jenis,dokter.nama
        FROM hrd.dkd1 as dkd1 
        LEFT JOIN hrd.dokter as dokter ON dkd1.dokterid=dokter.dokterid
        WHERE dkd1.srid='".$sr_id."'
        AND ('".$tgl_."' = dkd1.tgl)
        ORDER BY nama
      ";
      // echo"$query2<br>";

       $result2 = mysqli_query($cnmy, $query2);
       $num_rows = mysqli_num_rows($result2);
       for ($j=0; $j < $num_rows; $j++)
       {
         $row2 = mysqli_fetch_array($result2);
         if (substr($row2['dokterid'],-5)=='00000') {
         } else {
		    if ($row2['jenis']=='J') {
			   echo "<small><strong>(JV) </strong></small>";
			}
            echo '<small>'.substr($row2['dokterid'],-5).'&nbsp;&nbsp;&nbsp;'.
                 $row2['nama'].'</small><br>';
         }
		 
       }
       // end cetak detail
       // cetak header
       if ( $row['kompl'] != '')
          echo 'Compl : '.$row['kompl'].'<br>';
       if ( $row['kompl2'] != '')
          echo 'Compl : '.$row['kompl2'].'<br>';
       if ( $row['akt'] != '')
          echo 'Akt : '.$row['akt'].'<br>';
       if ( $row['saran'] != '')
          echo 'Saran : '.$row['saran'].'<br>';
       if ( $row['saran2'] != '')
          echo 'Saran : '.$row['saran2'].'<br>';
       if ( $row['ket_nama'] !='')
          echo 'Ket #1: '.$row['ket_nama'].'<br>';
       if ( $row['ket_nama2'] !='')
          echo 'Ket #2: '.$row['ket_nama2'].'<br>';
       if ( $row['ket'] !='')
          echo 'Ket : '.$row['ket'].'<br>';
       if ( $row['ket2_'] !='')
          echo 'Ket : '.$row['ket2_'].'<br>';
		  
		  
		if ($row['posted']=='1' and $row['Status']=='Approved') {
        //  echo '<font color="green"><b>'.$tgl_.' (Approved)</b></font><br>'; 
	   } elseif ($row['posted']=='0' and $row['Status']=='Suspend' ) {
       } else {
          echo "<a href='mr0.php?tgl=$tgl_&srid=$srid&entrymode=E'>View/Edit</a>&nbsp;&nbsp;&nbsp;&nbsp;";
		  echo "<a href='mr0.php?tgl=$tgl_&srid=$srid&tgl1=$tgl1&tgl2=$tgl2&entrymode=D'>Delete</a><br>";
       }   

	   echo '<hr>';
     }

  }
  echo '<a href="mrvdkd.php">Lihat Lagi</a><br>';

  if (empty($_SESSION['srid'])) {
  } else {
     //echo '<a href="auth2.php">Menu</a><br>';
     do_show_menu($_SESSION['jabatanid'],'N');
  }

?>
<form>
</form>
</body>
</html>
