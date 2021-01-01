<?PHP
    session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
?>
<HTML>
<HEAD>
    <style type="text/css">
    table.one
    {
        font-size: smaller
    }
    </style>
    <title>Informasi Dokter</title>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <link rel="shortcut icon" href="images/icon.ico" />
    <style> .str{ mso-number-format:\@; } </style>
</HEAD>


<BODY>
<form id="doktinf2" action="" method=post>

<?php
    include("config/koneksimysqli.php");
    include "config/fungsi_combo.php";
    include "config/fungsi_sql.php";
    include("config/common.php");
    
    $cnit=$cnmy;
  
  
    $dokterid = $_GET['iid'];
    $jabatanid = $_SESSION['JABATANID'];
  
  $query = "select * from hrd.dokter where dokterid='$dokterid'";
  $result = mysqli_query($cnit, $query);
  $num_results = mysqli_num_rows($result);
  $row = mysqli_fetch_array($result);

  echo '<table class="one" border="1">';
  echo '<tr>';
  echo '<td>Nama Dokter : </td>';
  echo "<td>".$row['nama']."</td>";
  echo '</tr>';
  echo '<tr>';
  echo '<td>Alamat : </td>';
  echo "<td>".$row['alamat1']."&nbsp;</td>";
  echo '</tr>';
  echo '<tr>';
  echo '<td>&nbsp;</td>';
  echo "<td>".$row['alamat2']."&nbsp;</td>";
  echo '</tr>';
  echo '<tr>';
  echo '<td>Kota :</td>';
  echo "<td>".$row['kota']."&nbsp;</td>";
  echo '</tr>';
  echo '<tr>';
  echo '<td>Telp. :</td>';
  echo "<td>".$row['telp']."&nbsp;</td>";
  echo '</tr>';
  echo '<tr>';
  echo '<td>HP :</td>';
  echo "<td>".$row['hp']."&nbsp;</td>";
  echo '</tr>';
  echo '<td>Bagian :</td>';
  echo "<td>".$row['bagian']."&nbsp;</td>";
  echo '</tr>';
/*  echo '<tr>';
  echo '<td>CN (%) :</td>';
  echo "<td>".$row['cn']."&nbsp;</td>";
  echo '</tr>';
*/  
  echo '</table>';  
  echo '<br>';
  
  
  //mr dan spv, area
  $show_dkd = 1;
  $show_ks = 1;
  if ($_SESSION['JABATANID']=='01' or $_SESSION['JABATANID']=='02' or $_SESSION['JABATANID']=='03' or
      $_SESSION['JABATANID']=='04' or $_SESSION['JABATANID']=='07' or
      $_SESSION['JABATANID']=='06' or
      $_SESSION['JABATANID']=='09' ) {
      //dir, mm, amm, nsm, pm
	  $show_dkd = 1;
  }
  if ($_SESSION['JABATANID']=='10' or $_SESSION['JABATANID']=='18') {
     //spv, koordinator
     $show_dkd = 5;
  }
  $query = "select mr_dokt.dokterid,mr_dokt.karyawanId as bawahanid,
            karyawan.nama,karyawan.jabatanid,
			karyawan.icabangid,karyawan.areaid,
			jabatan.nama as nmjabat,
			MKT.iarea.nama as nmarea
            from hrd.mr_dokt as mr_dokt  
            join hrd.karyawan as karyawan on mr_dokt.karyawanid=karyawan.karyawanid
			join MKT.iarea on karyawan.icabangid=MKT.iarea.icabangid and karyawan.areaid=MKT.iarea.areaid
			join hrd.jabatan as jabatan on karyawan.jabatanid=jabatan.jabatanid
            where mr_dokt.dokterid='$dokterid' order by karyawan.jabatanid";
  $result = mysqli_query($cnit, $query);
  $num_results = mysqli_num_rows($result);
  
  echo '<table border="1" cellspacing="0" cellpadding="1">';
  echo '<tr>';
  echo "<th align=\"center\"><small><b>MR/Spv/Koord.</b></small></th>";
  echo "<th align=\"center\"><small><b>&nbsp;</b></small></th>";
  echo "<th align=\"center\"><small><b>Area</b></small></th>";
  echo "<th>&nbsp;</th>";
  echo "<th>&nbsp;</th>";
  echo '</tr>';
  for ($i=0; $i < $num_results; $i++){
      $row = mysqli_fetch_array($result);
	  echo '<tr>';
	  echo '<td><small>'.$row['nama'].'</small></td>';
	  echo '<td><small>'.$row['nmjabat'].'</small></td>';
	  echo '<td><small>'.$row['nmarea'].'</small></td>';
      if ($show_dkd) {
	     //// echo '<td><small><a href=mr4.php?karyawanid='.$row['bawahanid'].'>Show DKD</a></small></td>';
	     /*echo "<td align=\"center\"><small><input type=\"button\" id=\"cmdDKS\" value=\"Show DKD\" 
		       onclick=\"show_dkd('".$row['bawahanid']."')\"></small></td>"; */
                       echo "<td align=\"center\"></td>";//baru
		 } else {
	     echo '<td>&nbsp;</td>';
	  }
      if ($show_ks) {
	     /* echo "<td align=\"center\"><small><input type=\"button\" id=\"cmdKS\" value=\"Show Kartu Status\" 
		       onclick=\"show_ks('".$row['bawahanid']."')\"></small></td>"; */
             $idinm=$row['bawahanid'];
             echo "<td align=\"center\">";
             echo "<a class='btn btn-info btn-xs' href='eksekusi3.php?module=lihatdataksusr&ket=bukan&iid=$idinm&ind=$dokterid' target='_blank'>Show Kartu Status</a>";
             echo "</td>";
	  } else {
	     echo '<td>&nbsp;</td>';
	  }
	  echo '</tr>';
  }
  echo '</table>';  
  
  //apotik
  echo '<br>';
  $query = "select ks1.dokterid,ks1.aptid,mr_apt.nama,mr_apt.alamat1,
            mr_apt.alamat2,mr_apt.kota,
			mr_apt.aktif
			from hrd.ks1 as ks1  
			join hrd.mr_apt as mr_apt on ks1.aptid = mr_apt.aptid
			where dokterid='$dokterid' 
            group by dokterid,aptid";
  $result = mysqli_query($cnit, $query);
  $num_results = mysqli_num_rows($result);
  echo '<table border="1" cellspacing="0" cellpadding="1">';
  echo '<tr>';
  $header_ = add_space('Apotik',40);
  echo "<th align=\"center\"><small><b>Apotik</b></small></th>";
  $header_ = add_space('Alamat',40);
  echo "<th align=\"center\"><small><b>Alamat</b></small></th>";
  echo '<th align="center"><small><b>Aktif</b></small></th>';
  echo '</tr>';
  for ($i=0; $i < $num_results; $i++){
      $row = mysqli_fetch_array($result);
	  echo '<tr>';
	  echo '<td><small>'.$row['nama'].'</small></td>';
	  echo '<td><small>'.$row['alamat1'].'&nbsp;</small></td>';
	  if ($row['aktif']=='N') {
	     echo '<td><small>Tidak</small></td>';
	  } else {
	     echo '<td><small>Ya</small></td>';
	  }
	  echo '</tr>';
	  if ($row['alamat2']) {
	     echo '<tr>';
		 echo '<td>&nbsp;</td>';
		 echo '<td><small>'.$row['alamat2'].'</small></td>';
		 echo '<td>&nbsp;</td>';
		 echo '</tr>';
	  }
	  if ($row['kota']) {
	     echo '<tr>';
		 echo '<td>&nbsp;</td>';
		 echo '<td><small>'.$row['kota'].'</small></td>';
		 echo '<td>&nbsp;</td>';
		 echo '</tr>';
	  }
  }
  
  
  echo '</table>';
	
  echo '<br>'; 
  /*
  echo "<input type=\"button\" value=\"Show DKD\"
           onclick=\"submit_to('doktinf2','ksApr.php')\"
           <br>&nbsp;&nbsp;&nbsp;";
  
  echo "<input type=\"button\" value=\"Show Kartu Status\"
           onclick=\"submit_to('doktinf2','ksApr1.php')\"<br>&nbsp;&nbsp;&nbsp;";
  */
  $bln_ = substr('0'.strval(date('m')),-2);
  $bulan = date('Y').'-'.$bln_;
  
  echo '<input type="hidden" name="srid" id="srid" value="">';
  echo '<input type="hidden" name="karyawanid" id="karyawanid" value="">';
  echo "<input type=\"hidden\" name=\"jabatanid\" id=\"jabatanid\" value='$jabatanid'>";
  echo "<input type=\"hidden\" name=\"bulan\" id=\"bulan\" value='$bulan'>";
  echo "<input type=\"hidden\" name=\"dokterid\" id=\"dokterid\" value='$dokterid'>";


?>

</form>
</BODY>
</HTML>
