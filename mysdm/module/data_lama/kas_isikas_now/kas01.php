<html>
<head>
  <title>ISI HARIAN</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="../../../images/icon.ico" />
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<script src="kas.js">
</script>
<body>
<form id="kas01" action="kas00.php" method=post>

<?php
	include("../../../config/common.php");
	//include("common3.php");
	include("../../../config/koneksimysqli_it.php");
	session_start();


	if (empty($_SESSION['IDCARD'])) {
      echo 'not authorized';
      exit;
	} else {
        $module=$_GET['module'];
        $act=$_GET['act'];
        $idmenu=$_GET['idmenu'];
        
        
        
    
		$srid = $_SESSION['USERID'];
		$srnama = $_SESSION['NAMALENGKAP'];
		$sr_id = substr('0000000000'.$srid,-10);
		$userid = $_SESSION['IDCARD'];
		$tanggal = date('Y-m-d');
		$entrymode = $_POST['entrymode'];
                if ($entrymode=='E') {
                    $periode1 = $_POST['periode1']; 
                    $periode2 = $_POST['periode2']; 
                }else{
                    $tanggal1 = $_POST['tanggal1'];
                    $bulan1 = $_POST['bulan1'];
                    $tahun1 = $_POST['tahun1'];
                    $periode1 = $tahun1.'-'.$bulan1.'-'.$tanggal1; 
                    if ($periode1 == '--') {
                            $periode1 = $_POST['periode1']; 
                    }

                    $tanggal2 = $_POST['tanggal2'];
                    $bulan2 = $_POST['bulan2'];
                    $tahun2 = $_POST['tahun2'];
                    $periode2 = $tahun2.'-'.$bulan2.'-'.$tanggal2; 
                    if ($periode2 == '--') {
                            $periode2 = $_POST['periode2']; 
                    }
                }
		$karyawanid = $_POST['karyawanid'];
		$nama = $_POST['nama'];
		$kodeid = $_POST['kodeid'];
		$aktiv1 = $_POST['aktiv1'];
		$aktiv2 = $_POST['aktiv2'];
		$jumlah = $_POST['jumlah_'];
                $nobukti = "";
                if (isset($_POST['nobukti']))
                    $nobukti = $_POST['nobukti'];
	
            if ($kodeid=="") {
		echo "Kode perkiraan harus diisi!<br><br>";
		echo "<input type=button name=cmdBack id=cmdBack value=Back onclick='go_back(1)'><br>";
		exit;
	}
		
                
                
		if ($entrymode=='E') {
                    
		    $kasid = $_POST['kasid'];
                    if (empty($kasid)) exit;
			$query = "update hrd.kas set kasid='$kasid',
									 karyawanid='$karyawanid',
									 nama='$nama',
									 periode1='$periode1',
									 kode='$kodeid',
									 aktivitas1='$aktiv1',
									 aktivitas2='$aktiv2',
									 jumlah='$jumlah',
									 periode2='$periode2',
    user1='$userid', nobukti='$nobukti' WHERE kasid='$kasid'"; //echo"$query";
			
		} else {
                    
			$kasid = "0000000000";
			$query = "select noKas from hrd.setup0";
			$result = mysqli_query($cnit, $query);
			$num_results = mysqli_num_rows($result);
			$row = mysqli_fetch_array($result);
			if ($num_results) {
				$kasid = $row['noKas'];
			}
                        
			$kasid = plus1($kasid,10);
			
			$query = "insert into hrd.kas (kasid,karyawanid,nama,periode1,kode,aktivitas1,aktivitas2,jumlah,periode2,user1,nobukti) 
					  values ('$kasid','$karyawanid','$nama','$periode1','$kodeid','$aktiv1','$aktiv2','$jumlah','$periode2','$userid','$nobukti')"; //echo"$query";
		    
		}
			 
		$result = mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
		if ($result) {
                        
			//echo "<br>Save OK!<br><br>";
			if ($entrymode=="E") {
			} else {
				$query = "update hrd.setup0 set noKas='$kasid'";
				$result = mysqli_query($cnit, $query);
				if ($result) {
				} else {
					echo "Error : ".mysqli_error();
				}
			}
                        header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt&entry=E&id='.$kasid);
		} else {
			echo "Error : ".mysqli_error();
			exit;
		} 
			
	}  // if (empty($_SESSION['srid'])) 
	echo "<input type=submit id=cmdOK name=cmdOK value='OK'>";
?>
 
</form>
</body>
</html>


