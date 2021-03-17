 <html>
<head>
  <title>DKD Confirmation</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<body>

<?php
	include("../../../config/koneksimysqli.php");
	include("../../../config/common.php");


  session_start();
  // echo "<pre>";
  // print_r($_POST);
  // echo "</pre>";

  if (empty($_SESSION['USERID'])) {
     $srid = $_POST['USERID'];
  } else {
     $srid = $_SESSION['USERID'];
  }
  $pin = $_POST['pin']; 
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
  // $ketid2 = $_POST['ketid2'];
  $EntryMode = $_POST['entrymode'];

  // echo "$dokter<br>";
  // echo "$jv_dokter<br>";

  //cek tanggal, sabtu atau minggu atau libur
  $tglquery = "select * from hrd.kalendar where tgl='$tgl'";
  //echo $tglquery;
  $tglresult = mysqli_query($cnmy, $tglquery);
  $tglrow = mysqli_fetch_array($tglresult);
  if ($tglrow['libur']=="Y") {
     echo "<br><b>Tidak bisa simpan karena $tgl<br>";
     echo "Hari Libur : ".$tglrow['ket']."</b><br>";
	 exit;
  }

		$entrymode = "";
		if ($entrymode=='E') {

		}else{
		  $sr_id = substr('0000000000'.$srid,-10);
		  $filterquery = "Select Tgl,Approval_status FROM hrd.`dkd0` where tgl='$tgl' and srid='$sr_id' and approval_status In ('Approved','Suspend','Pending') ";
		  $filterresult = mysqli_query($cnmy, $filterquery);
		  $filterrow = mysqli_fetch_array($filterresult);
		  if ($filterrow['Approval_status']=="Approved") {
			 echo "<br><b>DKD sudah pernah dibuat dan sudah di approved untuk tgl : $tgl . Proses dibatalkan !<br>";
			 exit;
		  }

//		  if ($filterrow['Approval_status']=="Pending") {
//			 echo "<br><b>DKD sudah pernah dibuat untuk tgl : $tgl <br>";
//		  }		  
		  
		}
  
  echo $entrymode;
  
  $sr_id = substr('0000000000'.$srid,-10);
  $query = "select nama,pin from hrd.karyawan where karyawanId='".$sr_id."'";
  // echo "$query<br>";
  
  $result = mysqli_query($cnmy, $query);
  $row = mysqli_fetch_array($result);
  $num_results = mysqli_num_rows($result);

  echo '<strong>'.$row['nama'].'</strong><br>';
  
  $ok2_ = 1;
  if (empty($_SESSION['srid'])) {
     if ($pin == $row['pin'])
        {
          $ok2_ = 1;
        }
     else {
        $ok2_ = 0;
        echo 'PIN salah !<br>';
        exit;
        }
     if ($num_results ==0) {
        echo 'ID tidak ada!';
        exit;
     }
  }
  if ($ok2_) {
     echo $tgl.'<br>';
     echo 'Ket : <br>'.$ket.'<br>';
     echo $ket2.'<br><br>';
  

     $dokter_array = explode(' ',$dokter);
     // print_r($dokter_array);
     // echo "<br>";
     $num_dokter = count($dokter_array);  
     $dokter = '';
     for ($i=0; $i < $num_dokter; $i++)
     {
       $dokter_id = substr('0000000000'.$dokter_array[$i],-10);
       // $query = "SELECT nama FROM dokter WHERE dokterId='".$dokter_id."'";
       $query = "
        SELECT mr_dokt.karyawanid,mr_dokt.dokterid,dokter.nama
			  FROM hrd.mrdoktbaru as  mr_dokt 
        LEFT JOIN hrd.dokter as dokter ON mr_dokt.dokterid=dokter.dokterid
			  WHERE mr_dokt.karyawanid='".$sr_id."'
			  AND mr_dokt.dokterid='".$dokter_id."';
      "; 
      // echo"$query<br>";
       $result = mysqli_query($cnmy, $query);
       $num_results = mysqli_num_rows($result);
       // print_r($result);
       if ($num_results > 0) {
          $dokter = $dokter . $dokter_array[$i].' ';
          $row = mysqli_fetch_array($result);
          $dokter_nama = $row['nama'];
          echo $dokter_nama.'<br>';
       }
     }; //end for
     $dokter = chop($dokter);  // rtrim()
	 
	 if ($jv_dokter != '') {
	    echo "<br><strong>JV Dokter : </strong><br>" ;
        $jv_dokter_array = explode(' ',$jv_dokter);
        $jv_num_dokter = count($jv_dokter_array);  
        $jv_dokter = '';
        for ($i=0; $i < $jv_num_dokter; $i++)
        {
          $jv_dokter_id = substr('0000000000'.$jv_dokter_array[$i],-10);
          $query = "select nama from hrd.dokter as dokter where dokterId='".$jv_dokter_id."'";
          $query = "select mr_dokt.karyawanid,mr_dokt.dokterid,dokter.nama
				    from hrd.mrdoktbaru as mr_dokt left join hrd.dokter as dokter on mr_dokt.dokterid=dokter.dokterid
				    where mr_dokt.karyawanid='".$sr_id."'
				    and mr_dokt.dokterid='".$jv_dokter_id."'"; 
          // echo"$query";
          $result = mysqli_query($cnmy, $query); 
          $num_results = mysqli_num_rows($result);
          if ($num_results > 0) {
             $jv_dokter = $jv_dokter . $jv_dokter_array[$i].' ';
             $row = mysqli_fetch_array($result);
             $jv_dokter_nama = $row['nama'];
             echo $jv_dokter_nama.'<br>';
          }
        }; //end for
        $jv_dokter = chop($jv_dokter);  // rtrim()
		echo '<br>';
	 }  // if 
	 
  }  // if ok2_
?>

<form action="mr10.php" method=post>
 <input type=submit value="Save"><br>

 <a href="mr_03.php">Isi DKD</a>
 
 <input type="HIDDEN" name="srid" value="<?php echo($sr_id); ?>" ><br>
 <input type="HIDDEN" name="tgl" value="<?php echo($tgl); ?>" ><br>
 <input type="HIDDEN" name="dokter" value="<?php echo($dokter); ?>" ><br>
 <input type="HIDDEN" name="jv_dokter" value="<?php echo($jv_dokter); ?>" ><br>
 <input type="HIDDEN" name="compl" value="<?php echo($compl); ?>" ><br>
 <input type="HIDDEN" name="compl2" value="<?php echo($compl2); ?>" ><br>
 <input type="HIDDEN" name="akt" value="<?php echo($akt); ?>" ><br>
 <input type="HIDDEN" name="saran" value="<?php echo($saran); ?>" ><br>
 <input type="HIDDEN" name="saran2" value="<?php echo($saran2); ?>" ><br>
 <input type="HIDDEN" name="ket" value="<?php echo($ket); ?>" ><br>
 <input type="HIDDEN" name="ket2" value="<?php echo($ket2); ?>" ><br>
 <input type="HIDDEN" name="ketid" value="<?php echo($ketid); ?>" ><br>
 <!-- <input type="HIDDEN" name="ketid2" value="<?php echo($ketid2); ?>" ><br> -->
 
</form>

</body>
</html>
