<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REKAP SALES MODERN TRADE SDM.xls");
    }
?>
<html>
<head>
  <title>REKAP SALES MODERN TRADE SDM</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<form action="rpkaskk0.php" method=post>

<?php
	include("config/common.php");
	//include("common3.php");
	include("config/koneksimysqli.php");
        
	$srid = $_SESSION['USERID'];
	$srnama = $_SESSION['NAMALENGKAP'];
	$sr_id = substr('0000000000'.$srid,-10);
	$userid = $_SESSION['IDCARD'];
	$tahun = $_POST['tahun1'];
        
        for ($i=0; $i < 20; $i++) {
            $prodTQty_[$i]=0;
        }
        $index=0;
        $index_=0;
        $qty =0;
        $t_total=0;
        $kode_ ="";
	echo "<b><big>REKAP KAS KECIL $tahun</big><br><br>";
	
        
        $puserid=$_SESSION['USERID'];

        $now=date("mdYhis");
        $tmp00 =" dbtemp.tmprekapkas00_".$puserid."_$now ";
        $tmp01 =" dbtemp.tmprekapkas01_".$puserid."_$now ";
        
        $query = "select a.*, b.nama namakode, d.NAMA4, CAST('' as CHAR(50)) as nodivisi from hrd.kas a join hrd.bp_kode b on a.kode=b.kodeid
            LEFT JOIN dbmaster.posting_coa_kas c on a.kode=c.kodeid and b.kodeid=c.kodeid
            LEFT JOIN dbmaster.coa_level4 d on a.COA4=d.COA4
            WHERE left(a.periode1,4)='$tahun'";
        $query = "create TEMPORARY table $tmp00 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        $query = "CREATE INDEX `norm1` ON $tmp00 (kasId, kode, COA4)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        $query = "select distinct b.idinput, b.divisi, b.nodivisi, a.kodeinput, a.bridinput, b.pilih from dbmaster.t_suratdana_br1 a "
                . " JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput WHERE "
                . " IFNULL(b.stsnonaktif,'')<>'Y' AND IFNULL(b.nodivisi,'')<>'' "
                . " AND a.bridinput IN (select distinct IFNULL(kasId,'') from $tmp00)";
        $query = "create TEMPORARY table $tmp01 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        $query = "CREATE INDEX `norm1` ON $tmp01 (idinput, nodivisi, bridinput, kodeinput)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp00 a JOIN $tmp01 b ON a.kasId=b.bridinput SET a.nodivisi=b.nodivisi";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
  //report rekap sales 
        $query = "select sum(jumlah) as nilai,kode,periode1,bp_kode.nama
				  from hrd.kas kas 
				  join hrd.bp_kode bp_kode on kas.kode=bp_kode.kodeid
				  where left(periode1,4)='$tahun'
				  GROUP by kas.kode,left(periode1,7)
				  order by kas.kode,left(periode1,7)"; //echo"$query";
        
        $query = "select sum(jumlah) as nilai, kode, periode1, namakode as nama, COA4, NAMA4, nodivisi from $tmp00 "
                . " GROUP by kode, namakode, left(periode1,7), COA4, NAMA4, nodivisi "
                . " order by kode, left(periode1,7)";
		$result = mysqli_query($cnmy, $query);
		$num_results = mysqli_num_rows($result);
		
		for ($i=0; $i < $num_results; $i++) {
			$row = mysqli_fetch_array($result);
			if ($i==0) {
			   $tgljual_ = $row['periode1']; 
			   $bln_ = substr($tgljual_,-5,2); 
			}
					
		} // end for
	
		mysqli_data_seek($result,0);
		//ambil shorts (bulan)
		echo '<table border="1" cellspacing="0" cellpadding="1">';
		echo '<tr>';
		$header0_ = add_space('No Divisi',20);
		$header1_ = add_space('COA',20);
		$header2_ = add_space('Perkiraan',40);
		$header_ = add_space('Nama Group',50);
		echo "<th rowspan=2 align='left'><small>$header0_</small></th>"; 
		echo "<th rowspan=2 align='left'><small>$header1_</small></th>"; 
		echo "<th rowspan=2 align='left'><small>$header2_</small></th>"; 
		echo "<th rowspan=2 align='left'><small>$header_</small></th>"; 
		echo "<th colspan=12 align=center><small><b>$tahun</b></small></th>";
		echo '<td rowspan=2 align="center"><small><b>Total</b></small></td>';
		$query2 = "select number from hrd.bulan order by number";
		$result2 = mysqli_query($cnmy, $query2);
		$num_results2 = mysqli_num_rows($result2);
		$j = 0;
		echo "<tr>";
		unset($Bln_);
		for ($i=0; $i < $num_results2; $i++) {
			$row2 = mysqli_fetch_array($result2);
			$j++ ;
			$Bln_[$j] = $row2['number'];
			echo "<th><small>".$Bln_[$j]."</small></th>"; 
		} // end for
		
		echo '</tr>';

		//array for total qty 
		$dummy_ = $num_results2+1;  //echo"$dummy_";
		for ($k=1; $k < $dummy_ ; $k++) {
			$produkQty_[$k]="";   // qty
			$produkTQty_[$k]="";   // total qty
			$produkSls_[$k]=0;   //sales
			$produkHNA_[$k]=0;   //hna
		} 

		$tot_sales = 0;
		$row = mysqli_fetch_array($result);
		$i = 1;
		while ($i <= $num_results) { 	
			$grp_mt_ = $row['kode'];
			$nm_grp = $row['nama'];
			$nnodivisi = $row['nodivisi'];
			$nkodecoa = $row['COA4'];
			$nnamacoa = $row['NAMA4'];
			echo "<tr>";
			echo '<td align="left"><small>'.$nnodivisi."</small></td>";
			echo '<td align="left"><small>'.$nkodecoa."</small></td>";
			echo '<td align="left"><small>'.$nnamacoa."</small></td>";
			echo '<td align="left"><small>'.$nm_grp."</small></td>";
			$tot_prod = 0;
			$prodTQty_[$index_] = 0;
			$prodSls_[$index_] = 0;
			while ($nm_grp == $row['nama'] && $grp_mt_ == $row['kode'] ) {
			//$total = 0;
				$tgljual_ = $row['periode1'];
				$bln = substr($tgljual_,-5,2);
				$tot_qty = 0;
				$tot_qty = $row['nilai']; 
				$row = mysqli_fetch_array($result);
				$i++;
			 
			   $index_ = array_search($bln,$Bln_);
			   $produkQty_[$index_] = $tot_qty;  
			   $produkTQty_[$index_] = (double)$produkTQty_[$index_] + (double)$tot_qty; 
			   
				//jumlah qty dan sales
                                if (empty($kode_)) $kode_ =0;
			    $index_ = ($kode_);
			    $prodTQty_[$index_] = $prodTQty_[$index_] + $tot_qty; 
                            /*
				if ($row['qty'] <> "") {
					$tot = 1;
				} else {
					$tot = 0;
				}
                             * 
                             */
                                $tot = 0;
				$tot_prod = $tot_prod + $tot;
                                
			}  // break per outlet
			//cetak
			$total = $prodTQty_[$index_];  //echo"$total<br>";
			$t_total = $t_total + $total; 
			
			for ($k=1; $k < $dummy_; $k++) {
				if ($produkQty_[$k]=="") {
				   echo '<td align="center"><small>&nbsp;</small></td>';
				} else {
				   echo '<td align="center"><small>'.number_format($produkQty_[$k],0)."</small></td>";
				} 
				$produkQty_[$k]="";   
			} 

			echo '<td align="right"><small><b>'.number_format($total,0)."</b></small></td>";
		echo "</tr>";
			
		} // end while

		echo "<tr>";
		echo '<td><small><b></b></small></td>';
		echo '<td><small><b></b></small></td>';
		echo '<td><small><b></b></small></td>';
		echo '<td><small><b>Total :</b></small></td>';
		for ($k=1; $k < $dummy_; $k++) {
			if ($produkTQty_[$k]==0) {
				echo '<td align="center"><small>&nbsp;</small></td>';
			} else {
				echo '<td align="center"><small><b>'.number_format($produkTQty_[$k],0)."</b></small></td>";
			} 
		} 
		echo '<td align="right"><small><b>'.number_format($t_total,0)."</b></small></td>";
		echo "</tr>";
		echo "</table>\n";
		echo '<br><input type=hidden value="Back"><br>';
?>

</body>
</form>
</html>

<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_close($cnmy);
?>