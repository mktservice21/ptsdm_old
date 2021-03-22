<html>
<head>
  <title>Rekap DKD Per Tahun</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<body>
<?php
	include("../../../config/koneksimysqli.php");
	include("../../../config/common.php");

  session_start();

  $srid = "";//$_POST['srid'];
  $tahun = $_POST['tahun'];

  
  echo "<b><big>Rekap DKD Per Tahun $tahun</big></b><br><br>";
  echo '<table border="1%" cellpadding="1">';
  echo '<tr>';
  echo '<th align="center"><small>Nama</small></th>';
  echo '<th align="center"><small>Jan</small></th>';
  echo '<th align="center"><small>Feb</small></th>';
  echo '<th align="center"><small>Mar</small></th>';
  echo '<th align="center"><small>Apr</small></th>';
  echo '<th align="center"><small>Mei</small></th>';
  echo '<th align="center"><small>Jun</small></th>';
  echo '<th align="center"><small>Jul</small></th>';
  echo '<th align="center"><small>Agu</small></th>';
  echo '<th align="center"><small>Sep</small></th>';
  echo '<th align="center"><small>Okt</small></th>';
  echo '<th align="center"><small>Nov</small></th>';
  echo '<th align="center"><small>Des</small></th>';
  echo '<th align="center"><small>% Average</small></th>';
  echo '</tr>';
  
  $query = "select persen_call.*,karyawan.nama,karyawan.tglkeluar
            from hrd.persen_call as persen_call 
            join hrd.karyawan as karyawan on persen_call.srid = karyawan.karyawanid
   	        where left(thnbln,4) = $tahun and karyawan.tglkeluar='0000-00-00'
            order by karyawan.nama,srid,thnbln,cycleid";
 
  $result = mysqli_query($cnmy, $query);
  $num_results = mysqli_num_rows($result);
  $row = mysqli_fetch_array($result);
  $i = 0;
  while ($i <= $num_results) {
	 $nama = $row['nama'];
	 $srid = $row['srid'];

     for ($j=1;$j<=12;$j++) {
         $arrBln[$j] = 0.00;
     }
     //echo $nama.' ';
	 while (($i <= $num_results) and ($nama==$row['nama']) and ($srid==$row['srid'])) {
        $bln_ = substr($row['thnbln'],5,2);
	    $persen = 0;
		$per = 0;
   	    while (($i <= $num_results) and ($nama==$row['nama']) and ($srid==$row['srid']) and ($bln_ == substr($row['thnbln'],5,2))) {
		   $persen = $persen + floatval($row['persen']);
		   $per ++;
           $row = mysqli_fetch_array($result);
	       $i++;
		}  // end while $bln_
 	    $bln = intval($bln_);
        $arrBln[$bln] = $persen / $per;

	 }  // end while $nama + $srid
	 if ($nama) {
	    $total_ = 0.00;
		$per = 0;
	    echo "<tr>";
		echo "<td><small>$nama</small></td>";
        for ($j=1;$j<=12;$j++) {
		    if ($arrBln[$j] == 0){
			   echo "<td>&nbsp;</td>";
			} else {
			  $total_ = $total_ + $arrBln[$j];
			  $per ++;
              echo "<td align='center'><small>".number_format($arrBln[$j],2)."</small></td>";
			} 
        }
		if ($per) {
		   $average_ = $total_ / $per;
		   echo "<td align='center'><small>".number_format($average_,2)."</small></td>";
		} else {
		   echo "<td>&nbsp;</td>";
		}
	    echo '</tr>';
	 }
	 
  } // end while ($i <= $num_results)
  echo '</table>';

  if (empty($_SESSION['USERID'])) {
     echo '<a href="mr0.php">Isi DKD</a><br>';
  } else {
     //echo '<a href="auth2.php">Menu</a><br>';
     //do_show_menu($_SESSION['jabatanid'],'N');
  }
?>

  
</body>
</html>
