<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=Rekap Permohonan Dana Budged Request.xls");
    }
?>

<html>
<head>
    <title>REKAP DCC/DSS PER TAHUN</title>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>
<form action="rpbrthn0.php" method=post>

<?php
	include_once("config/common.php");
        include "config/koneksimysqli_it.php";
	
	
	$tahun = $_POST['tahun1'];
        $wilayah = $_POST['wilayah'];
	$divprodid = $_POST['divprodid'];
	$kodeid = $_POST['kodeid'];
	
        $nama_kd="";
        $where_="";
        
	$query_dv = "select nama from MKT.divprod where divprodid='$divprodid'";// echo"$query_dv";
	$result_dv = mysqli_query($cnit, $query_dv);
	$num_results_dv = mysqli_num_rows($result_dv);	
	if ($num_results_dv) {
		 $row_dv = mysqli_fetch_array($result_dv);
		 $nama = $row_dv['nama'];
	}
	
	$query_kd = "select nama,br from hrd.br_kode where divprodid='$divprodid' and kodeid='$kodeid'"; //echo"$query_kd";
	$result_kd = mysqli_query($cnit, $query_kd);
	$num_results_kd = mysqli_num_rows($result_kd);
	if ($num_results_kd) {
		 $row_kd = mysqli_fetch_array($result_kd);
		 $nama_kd = $row_kd['nama'];
		 $br = $row_kd['br']; //echo"br=$br";
	}	
	
	echo "<b><big>YTD DCC/DSS $tahun</big><br>";
	
                
  //report rekap sales 
	if ($wilayah=="A") {
		echo "Region : NASIONAL<br>";
	} else {
		if ($wilayah=="B") {
			echo "REGIONAL 1<br>";
			$where_ = "and region='B'";
		 } else {
			echo "REGIONAL 2<br>";
			$where_ = "and region='T'";
		 }
	}
	if ($divprodid=="*") {
		echo "<br>";
	} else {
		echo "Divisi : $nama<br>";
		$where_ = $where_." and divprodid='$divprodid'";
	}
	if ($kodeid=="*" or $kodeid=="") {
		$where_ = $where_." and kode in (select kodeid from hrd.br_kode where br='Y')";
	} else {
		echo "Kode : $nama_kd";
		$where_ = $where_." and kode='$kodeid'";
	}
        
                $eprodid="";
                for ($x=0;$x<300;$x++) {
                    $prodTQty_[$x]=0;
                    $produkTQty_[$x]=0;
                    
                    $zzzx = substr('00'.$x,-2);
                    $prodTQty_[$zzzx]=0;
                    $produkTQty_[$zzzx]=0;
                }
                
                $t_total=0;
                $ccyid="";
                $t1_total=0;
                $t_total1=0;
                $t_total2=0;
                $t_uibr="";
                $t_uitm="";
                $no=0;
                $ccyid_="";
        
        
	echo"<br><br>";
	$query_i = "select * from hrd.br0 br0 
				join hrd.br_area br_area on br0.icabangid=br_area.icabangid and br0.areaid=br_area.areaid
				where ((left(tgltrans,4)='$tahun' and tglunrtr='0000-00-00') or LEFT(tglunrtr,4)='$tahun') 
			    and retur <> 'Y' and ccyid='IDR' ".$where_."  "; 
        
	$result_i = mysqli_query($cnit, $query_i);
	$num_results_i = mysqli_num_rows($result_i);
	if ($num_results_i) {
		$query = "select sum(jumlah1) as jumlah,br0.tgltrans,ccyid, br_area.region,cabangid,nama 
			  from hrd.br0 br0 
			  join hrd.br_area br_area on br0.icabangid=br_area.icabangid and br0.areaid=br_area.areaid
			  where ((left(tgltrans,4)='$tahun' and tglunrtr='0000-00-00') or LEFT(tglunrtr,4)='$tahun') 
			  and retur <> 'Y' and ccyid='IDR' ".$where_." 
			  group by cabangid,left(tgltrans,7),ccyid
			  order by nama,tglunrtr,tgltrans,ccyid"; //echo"$query";
		$result = mysqli_query($cnit, $query);
		$num_results = mysqli_num_rows($result);
		
		for ($i=0; $i < $num_results; $i++) {
			$row = mysqli_fetch_array($result);
			if ($i==0) {
			   $tgltrans_ = $row['tgltrans']; 
			   $bln_ = substr($tgltrans_,-5,2); 
			}
					
		} // end for

		mysqli_data_seek($result,0);
		//ambil shorts (bulan)
		echo '<table border="1" cellspacing="0" cellpadding="1">';
		echo '<tr>';
		echo "<b>** Dalam IDR</b>";
		$header_ = add_space('Nama Cabang',50);
		echo "<th rowspan=3 align='center'><small>$header_</small></th>"; 
		echo "<th colspan=12 align=center><small><b>Bulan</b></small></th>";
		echo '<td rowspan=3 align="center"><small><b>Total IDR</b></small></td>';
		$query2 = "select number from hrd.bulan order by number";
		$result2 = mysqli_query($cnit, $query2);
		$num_results2 = mysqli_num_rows($result2);
		$j = 0;
		echo "<tr>";
		unset($Bln_);
		for ($i=0; $i < $num_results2; $i++) {
			$row2 = mysqli_fetch_array($result2);
			$j++ ;
			$Bln_[$j] = $row2['number']; 
			$nm_bln= nama_bulan($Bln_[$j]); 
			echo "<th><small>".$nm_bln."</small></th>"; 
		} 
			
		echo '</tr>';


		//array for total qty 
		$dummy_ = $num_results2+1;  //echo"$dummy_";
		for ($k=1; $k < $dummy_ ; $k++) {
			$produkQty_[$k]="";   // qty
			$produkTQty_[$k]="";   // total qty
		} 

		echo '<tr>';
		$row = mysqli_fetch_array($result);
		$i = 1;
		while ($i <= $num_results) { 	
			$cabnm_ = $row['nama']; 
			$cabangid_ = $row['cabangid'];
			echo "<tr>";
			echo '<td align="left"><small>'.$row['nama']."</small></td>";

			while ($cabnm_ == $row['nama'] && $cabangid_ == $row['cabangid'] ) {
				$tgltrans_ = $row['tgltrans'];
				$bln = substr($tgltrans_,-5,2);
				$jumlah = $jmlh = 0;
				$ccyid = $row['ccyid']; //echo"$ccyid";
				$jumlah = $row['jumlah']; //echo"$jumlah";

				$row = mysqli_fetch_array($result);
				$i++;
			 
			   $index_ = array_search($bln,$Bln_);
			   $produkQty_[$index_] = $jumlah;  
			   $produkTQty_[$index_] = (double)$produkTQty_[$index_] + (double)$jumlah; 
			   
				//jumlah qty dan sales
				$index_ = ($cabangid_);
				$prodTQty_[$index_] = (double)$prodTQty_[$index_] + (double)$jumlah; 
			}  // break per outlet
			//cetak
			$total = $prodTQty_[$index_]; 
			$t_total = (double)$t_total + (double)$total;
			
			for ($k=1; $k < $dummy_; $k++) {
				
				if ($produkQty_[$k]=="") {
				    echo '<td align="right"><small>&nbsp;</small></td>';
				} else {
					echo '<td align="right"><small>'.number_format($produkQty_[$k],0)."</small></td>";
				} 
				$produkQty_[$k]="";   
			} 
			echo '<td align="right"><small><b>'.number_format($total,0)."</b></small></td>";
			echo "</tr>";
			
		} // end while

		//cetak total qty
		echo "<tr>";
		echo '<td><small><b>Total IDR:</b></small></td>';
		for ($k=1; $k < $dummy_; $k++) {
			if ($produkTQty_[$k]==0) {
				echo '<td align="right"><small>&nbsp;</small></td>';
			} else {
				echo '<td align="right"><small><b>'.number_format($produkTQty_[$k],0)."</b></small></td>";
			} 
		} 
		echo '<td align="right"><small><b>'.number_format($t_total,0)."</b></small></td>";
		echo "</tr>";
		echo "</table>\n";
	} else {
	}
	
	
	$query_u = "select * from hrd.br0 br0 
				join hrd.br_area br_area on br0.icabangid=br_area.icabangid and br0.areaid=br_area.areaid
				where ((left(tgltrans,4)='$tahun' and tglunrtr='0000-00-00') or LEFT(tglunrtr,4)='$tahun') 
			    and retur <> 'Y' and ccyid='USD' ".$where_."  "; 
	$result_u = mysqli_query($cnit, $query_u);
	$num_results_u = mysqli_num_rows($result_u);
	if ($num_results_u) {
		$query = "select sum(jumlah) as jumlah,br0.tgltrans,ccyid, br_area.region,cabangid,nama 
				  from hrd.br0 br0 
				  join hrd.br_area br_area on br0.icabangid=br_area.icabangid and br0.areaid=br_area.areaid
				  where ((left(tgltrans,4)='$tahun' and tglunrtr='0000-00-00') or LEFT(tglunrtr,4)='$tahun') 
				  and retur <> 'Y' and ccyid='USD' ".$where_." 
				  group by cabangid,left(tgltrans,7),ccyid
				  order by nama,tglunrtr,tgltrans,ccyid"; 
		$result = mysqli_query($cnit, $query);
		$num_results = mysqli_num_rows($result);
		
		for ($i=0; $i < $num_results; $i++) {
			$row = mysqli_fetch_array($result);
			if ($i==0) {
			   $tgltrans_ = $row['tgltrans']; 
			   $bln_ = substr($tgltrans_,-5,2); 
			}
					
		} // end for

		mysqli_data_seek($result,0);
		//ambil shorts (bulan)
		echo '<br><br><table border="1" cellspacing="0" cellpadding="1">';
		echo '<tr>';
		echo "<b>** Dalam USD</b>";
		$header_ = add_space('Nama Cabang',50);
		echo "<th rowspan=3 align='center'><small>$header_</small></th>"; 
		echo "<th colspan=12 align=center><small><b>Bulan</b></small></th>";
		echo '<td rowspan=3 align="center"><small><b>Total USD</b></small></td>';
		$query2 = "select number from hrd.bulan order by number";
		$result2 = mysqli_query($cnit, $query2);
		$num_results2 = mysqli_num_rows($result2);
		$j = 0;
		echo "<tr>";
		unset($Bln_);
		for ($i=0; $i < $num_results2; $i++) {
			$row2 = mysqli_fetch_array($result2);
			$j++ ;
			$Bln_[$j] = $row2['number']; 
			$nm_bln= nama_bulan($Bln_[$j]); 
			echo "<th><small>".$nm_bln."</small></th>"; 
		} 
			
		echo '</tr>';
		$dummy_ = $num_results2+1;  
		for ($k=1; $k < $dummy_ ; $k++) {
			$produkQty1_[$k]="";   
			$produkTQty1_[$k]="";   
		} 

		echo '<tr>';
		$row = mysqli_fetch_array($result);
		$i = 1;
		while ($i <= $num_results) { 	
			$cabnm_ = $row['nama']; 
			$cabangid_ = $row['cabangid'];
			echo "<tr>";
			echo '<td align="left"><small>'.$row['nama']."</small></td>";

			while ($cabnm_ == $row['nama'] && $cabangid_ == $row['cabangid'] ) {
				$tgltrans_ = $row['tgltrans'];
				$bln = substr($tgltrans_,-5,2);
				$jumlah = $jmlh = 0;
				$ccyid = $row['ccyid']; //echo"$ccyid";
				$jumlah1 = $row['jumlah'];// echo"$jumlah";

				$row = mysqli_fetch_array($result);
				$i++;
			 
			   $index_ = array_search($bln,$Bln_);
			   $produkQty1_[$index_] = $jumlah1;  
			   $produkTQty1_[$index_] = $produkTQty1_[$index_] + $jumlah1; 
			   
				//jumlah qty dan sales
				$index_ = ($cabangid_);
				$prodTQty1_[$index_] = $prodTQty1_[$index_] + $jumlah1; 
			}  // break per outlet
			//cetak
			$total1 = $prodTQty1_[$index_]; 
			$t_total1 = $t_total1 + $total1;
			
			for ($k=1; $k < $dummy_; $k++) {
				if ($produkQty1_[$k]=="") {
				    echo '<td align="right"><small>&nbsp;</small></td>';
				} else {
					echo '<td align="right"><small>'.number_format($produkQty1_[$k],0)."</small></td>";
				} 
				$produkQty1_[$k]="";   
			} 
			echo '<td align="right"><small><b>'.number_format($total1,0)."</b></small></td>";
			echo "</tr>";
			
		} // end while

		//cetak total qty
		echo "<tr>";
		echo '<td><small><b>Total USD:</b></small></td>';
		for ($k=1; $k < $dummy_; $k++) {
			if ($produkTQty1_[$k]==0) {
				echo '<td align="right"><small>&nbsp;</small></td>';
			} else {
				echo '<td align="right"><small><b>'.number_format($produkTQty1_[$k],0)."</b></small></td>";
			} 
		} 
		echo '<td align="right"><small><b>'.number_format($t_total1,0)."</b></small></td>";
		echo "</tr>";
		echo "</table>\n";
	} else {
	}
	
	$query_s = "select * from hrd.br0 br0  
				join hrd.br_area br_area on br0.icabangid=br_area.icabangid and br0.areaid=br_area.areaid
				where ((left(tgltrans,4)='$tahun' and tglunrtr='0000-00-00') or LEFT(tglunrtr,4)='$tahun') 
			    and retur <> 'Y' and ccyid='SGD' ".$where_."  "; 
	$result_s = mysqli_query($cnit, $query_s);
	$num_results_s = mysqli_num_rows($result_s);
	if ($num_results_s) {
		$query = "select sum(jumlah) as jumlah,br0.tgltrans,ccyid, br_area.region,cabangid,nama 
				  from hrd.br0 br0  
				  join hrd.br_area br_area on br0.icabangid=br_area.icabangid and br0.areaid=br_area.areaid
				  where ((left(tgltrans,4)='$tahun' and tglunrtr='0000-00-00') or LEFT(tglunrtr,4)='$tahun') 
				  and retur <> 'Y' and ccyid='SGD' ".$where_." 
				  group by cabangid,left(tgltrans,7),ccyid
				  order by nama,tglunrtr,tgltrans,ccyid"; //ECHO"$query";
		$result = mysqli_query($cnit, $query);
		$num_results = mysqli_num_rows($result);
		
		for ($i=0; $i < $num_results; $i++) {
			$row = mysqli_fetch_array($result);
			if ($i==0) {
			   $tgltrans_ = $row['tgltrans']; 
			   $bln_ = substr($tgltrans_,-5,2); 
			}
					
		} // end for

		mysqli_data_seek($result,0);
		//ambil shorts (bulan)
		echo '<br><br><table border="1" cellspacing="0" cellpadding="1">';
		echo '<tr>';
		echo "<b>** Dalam SGD</b>";
		$header_ = add_space('Nama Cabang',50);
		echo "<th rowspan=3 align='center'><small>$header_</small></th>"; 
		echo "<th colspan=12 align=center><small><b>Bulan</b></small></th>";
		echo '<td rowspan=3 align="center"><small><b>Total SGD</b></small></td>';
		$query2 = "select number from hrd.bulan order by number";
		$result2 = mysqli_query($cnit, $query2);
		$num_results2 = mysqli_num_rows($result2);
		$j = 0;
		echo "<tr>";
		unset($Bln_);
		for ($i=0; $i < $num_results2; $i++) {
			$row2 = mysqli_fetch_array($result2);
			$j++ ;
			$Bln_[$j] = $row2['number']; 
			$nm_bln= nama_bulan($Bln_[$j]); 
			echo "<th><small>".$nm_bln."</small></th>"; 
		} 
			
		echo '</tr>';
		$dummy_ = $num_results2+1;  
		for ($k=1; $k < $dummy_ ; $k++) {
			$produkQty2_[$k]="";   
			$produkTQty2_[$k]="";   
		} 

		echo '<tr>';
		$row = mysqli_fetch_array($result);
		$i = 1;
		while ($i <= $num_results) { 	
			$cabnm_ = $row['nama']; 
			$cabangid_ = $row['cabangid'];
			echo "<tr>";
			echo '<td align="left"><small>'.$row['nama']."</small></td>";

			while ($cabnm_ == $row['nama'] && $cabangid_ == $row['cabangid'] ) {
				$tgltrans_ = $row['tgltrans'];
				$bln = substr($tgltrans_,-5,2);
				$jumlah = $jmlh = 0;
				$ccyid = $row['ccyid']; //echo"$ccyid";
				$jumlah2 = $row['jumlah']; 

				$row = mysqli_fetch_array($result);
				$i++;
			 
			   $index_ = array_search($bln,$Bln_);
			   $produkQty2_[$index_] = $jumlah2;  
			   $produkTQty2_[$index_] = $produkTQty2_[$index_] + $jumlah2; 
			   
				//jumlah qty dan sales
				$index_ = ($cabangid_);
				$prodTQty2_[$index_] = $prodTQty2_[$index_] + $jumlah2; 
			}  // break per outlet
			//cetak
			$total2 = $prodTQty2_[$index_]; 
			$t_total2 = $t_total2 + $total2;
			
			for ($k=1; $k < $dummy_; $k++) {
				if ($produkQty2_[$k]=="") {
				    echo '<td align="right"><small>&nbsp;</small></td>';
				} else {
					echo '<td align="right"><small>'.number_format($produkQty2_[$k],0)."</small></td>";
				} 
				$produkQty2_[$k]="";   
			} 
			echo '<td align="right"><small><b>'.number_format($total2,0)."</b></small></td>";
			echo "</tr>";
			
		} // end while

		//cetak total qty
		echo "<tr>";
		echo '<td><small><b>Total SGD:</b></small></td>';
		for ($k=1; $k < $dummy_; $k++) {
			if ($produkTQty2_[$k]==0) {
				echo '<td align="right"><small>&nbsp;</small></td>';
			} else {
				echo '<td align="right"><small><b>'.number_format($produkTQty2_[$k],0)."</b></small></td>";
			} 
			$produkTQty2_[$k]="";   
		} 
		echo '<td align="right"><small><b>'.number_format($t_total2,0)."</b></small></td>";
		echo "</tr>";
		echo "</table>\n";
	} else {
	}	
?>

</body>
</form>
</html>
