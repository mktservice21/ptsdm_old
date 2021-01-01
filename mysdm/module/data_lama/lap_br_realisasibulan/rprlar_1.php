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
    <title>LAPORAN REALISASI BR PER BULAN</title>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>

<form action="rprlar_0.php" method=post>

<?php
	include_once("config/common.php");	
        include "config/koneksimysqli_it.php";
            
        /*
	$bulan = $_POST['bulan'];
	$tahun = $_POST['tahun'];
	$periode1 = $tahun.'-'.$bulan; 
	if ($periode1 == '-') {
		$periode1 = $_POST['periode1']; //echo"$periode1";
		$bulan = substr($periode1,5,2);
		$tahun = substr($periode1,0,4);
	}
	$bln_ = nama_bulan($bulan);
        */
        $tgl01=$_POST['bulan'];
        $periode1= date("Y-m", strtotime($tgl01));
        $tahun= date("Y", strtotime($tgl01));
        $bulan= date("m", strtotime($tgl01));
        $bln_= date("F", strtotime($tgl01));
        
        
	$divprodid = $_POST['divprodid'];

	if ($divprodid=="" or $divprodid=="blank") {
		echo "Divisi harus dipilih";
		echo "<br><br><input type=submit id=cmdBack name=cmdBack value='Back'>";
		exit;
	}
	
	//if (($divprodid=="OTC") or ($divprodid=='PIGEO')) {
	//	$divprodid = 'PIGEO';
	//	$nama = 'PIGEON & OTC';
	//} else {
	//	$nama = 'EAGLE';
	//}
	if (($divprodid=="OTC") or ($divprodid=='PIGEO')) {
		$divprodid = 'PIGEO';
		$nama = 'PIGEON & OTC';
	} elseif (($divprodid=="PEACO")) {
		$divprodid = 'PEACO';
		$nama = 'PEACOCK';
	}
	else {
		$nama = 'EAGLE';
	}
 
	echo"<b>LAPORAN REALISASI BR BULAN $bln_ $tahun<br>";	
	echo "DIVISI : $nama</b><br><br>";
		
  //INBAR IDR
	echo "<b>DCC/DSS INDBAR (IDR)</b>";
		$query_bidr = " select sum(jumlah) as jumlah,kode,ccyid, br_area.cabangid,br_area.nama,region 
				   from hrd.br0 br0 
				   join hrd.br_area br_area on br0.icabangid=br_area.icabangid 
				   and br0.areaid=br_area.areaid 
				   join hrd.br_kode br_kode on br0.kode=br_kode.kodeid 
				   where ((left(tgltrans,7)='$periode1' and tglunrtr='0000-00-00') or LEFT(tglunrtr,7)='$periode1') and retur <> 'Y' 
				   and left(tgltrans,7)='$periode1' and br0.divprodid='$divprodid' and br='Y' and region='B' and ccyid='IDR'
				   group by cabangid,kode,ccyid order by cabangid,kode,ccyid"; //echo"$query";
				   
		//wh20130403, tambah kondisi "and br_area.aktif='Y'"
		$query_bidr = " select sum(jumlah) as jumlah,kode,ccyid, br_area.cabangid,br_area.nama,region 
				   from hrd.br0 br0 
				   join hrd.br_area br_area on br0.icabangid=br_area.icabangid 
				   and br0.areaid=br_area.areaid and br_area.aktif='Y'
				   join hrd.br_kode br_kode on br0.kode=br_kode.kodeid 
				   where ((left(tgltrans,7)='$periode1' and tglunrtr='0000-00-00') or LEFT(tglunrtr,7)='$periode1') and retur <> 'Y' 
				   and left(tgltrans,7)='$periode1' and br0.divprodid='$divprodid' and br='Y' and region='B' and ccyid='IDR'
				   group by cabangid,kode,ccyid order by cabangid,kode,ccyid"; 
                //echo"$query_bidr"; exit;
		$result_bidr = mysqli_query($cnit, $query_bidr);
		$num_results_bidr = mysqli_num_rows($result_bidr);
		
		for ($i=0; $i < $num_results_bidr; $i++) {
			$row_bidr = mysqli_fetch_array($result_bidr);
			if ($i==0) {
			   $kodeid = $row_bidr['kode']; 
			   $cabangid = $row_bidr['cabangid'];
			}
		} // end for
	
		mysqli_data_seek($result_bidr,0);
		//ambil shorts (bulan)
		echo '<table border="1" cellspacing="0" cellpadding="1">';
		echo '<tr>';
                
                
                
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
                
                
		$header_ = add_space('Nama Daerah',50);
		
		echo "<th align='left'><small>$header_ $eprodid</small></th>"; 
		$query2 = "select * from hrd.br_kode where divprodid='$divprodid' and br='Y' order by kodeid";
		$result2 = mysqli_query($cnit, $query2);
		$num_results2 = mysqli_num_rows($result2);
		$j = 0;
		unset($Bln_);
                
		for ($i=0; $i < $num_results2; $i++) {
                    $row2 = mysqli_fetch_array($result2);
                    $j++ ;
                    $Bln_[$j] = $row2['kodeid']; 
                    $initial_[$j] = $row2['nama']; 
                    echo "<th><small>".$initial_[$j]."</small></th>";
		} // end for
		echo '<td align="center"><small><b>Total</b></small></td>';
		echo"</tr>";
                
                
		//array for total qty 
		$dummy_ = $num_results2+1;  //echo"$dummy_";
		for ($k=1; $k < $dummy_ ; $k++) {
                    $produkQty_[$k]="";   // qty
                    $produkTQty_[$k]="";   // total qty
		} 
                
                
		$tot_sales = 0;
		$row_bidr = mysqli_fetch_array($result_bidr);
		$i = 1;
                
                while ($i <= $num_results_bidr) {	
                    $nama_ = $row_bidr['nama']; 
                    $cabangid_ = $row_bidr['cabangid'];
                    echo "<tr>";
                    echo '<td align="left"><small>'.$row_bidr['nama']."</small></td>";
                    $tot_prod = 0;
                    
                    
                    while ($nama_ == $row_bidr['nama'] && $cabangid_ == $row_bidr['cabangid'] ) {
                        $kodeid_ = $row_bidr['kode']; 
                        $tot_qty = 0;
                        $tot_qty = $row_bidr['jumlah']; 
                        $row_bidr = mysqli_fetch_array($result_bidr);
                        $i++;

                        $index_ = array_search($kodeid_,$Bln_);
                        $produkQty_[$index_] = $tot_qty;  //echo"$tot_qty";
                        $produkTQty_[$index_] = $produkTQty_[$index_] + $tot_qty; 
                        
                        //jumlah qty dan sales
                        $index_ = ($cabangid_);
                        $prodTQty_[$index_] = $prodTQty_[$index_] + $tot_qty; 

                        if ($row_bidr['jumlah'] <> "") {
                            $tot = 1;
                        } else {
                            $tot = 0;
                        }
                        $tot_prod = $tot_prod + $tot;
                    }  // break per outlet
                     
                    
                    //cetak
                    $total = $prodTQty_[$index_]; 
                    $t_total = $t_total + $total; 


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
                echo '<td><small><b>Total :</b></small></td>';
                for ($k=1; $k < $dummy_; $k++) {
                    if ($produkTQty_[$k]==0) {
                        echo '<td align="right"><small>&nbsp;</small></td>';
                    } else {
                        echo '<td align="right"><small><b>'.number_format($produkTQty_[$k],0)."</b></small></td>";
                        $t_ibr = $produkTQty_[$k]; 
                    } 
                }
                echo '<td align="right"><small><b>'.number_format($t_total,0)."</b></small></td>";
                echo "</tr>";
                echo "</table>\n";
                echo "<br><br>";

                
		
		 //INBAR USD
		echo "<b>DCC/DSS INDBAR (USD)</b>";
		$query_busd = " select sum(jumlah) as jumlah,kode,ccyid, br_area.cabangid,br_area.nama,region 
				   from hrd.br0 br0  
				   join hrd.br_area br_area on br0.icabangid=br_area.icabangid 
				   and br0.areaid=br_area.areaid 
				   join hrd.br_kode br_kode on br0.kode=br_kode.kodeid 
				   where ((left(tgltrans,7)='$periode1' and tglunrtr='0000-00-00') or LEFT(tglunrtr,7)='$periode1') and retur <> 'Y' 
				   and left(tgltrans,7)='$periode1' and br0.divprodid='$divprodid' and br='Y' and region='B' and ccyid='USD'
				   group by cabangid,kode,ccyid order by cabangid,kode,ccyid"; //echo"$query";
		//wh20130403, tambah kondisi "and br_area.aktif='Y'"
		$query_busd = " select sum(jumlah) as jumlah,kode,ccyid, br_area.cabangid,br_area.nama,region 
				   from hrd.br0 br0  
				   join hrd.br_area br_area on br0.icabangid=br_area.icabangid 
				   and br0.areaid=br_area.areaid and br_area.aktif='Y'
				   join hrd.br_kode br_kode on br0.kode=br_kode.kodeid 
				   where ((left(tgltrans,7)='$periode1' and tglunrtr='0000-00-00') or LEFT(tglunrtr,7)='$periode1') and retur <> 'Y' 
				   and left(tgltrans,7)='$periode1' and br0.divprodid='$divprodid' and br='Y' and region='B' and ccyid='USD'
				   group by cabangid,kode,ccyid order by cabangid,kode,ccyid"; //echo"$query";
		$result_busd = mysqli_query($cnit, $query_busd);
		$num_results_busd = mysqli_num_rows($result_busd);
		
                for ($i=0; $i < $num_results_busd; $i++) {
                    $row_busd = mysqli_fetch_array($result_busd);
                    if ($i==0) {
                        $kodeid = $row_busd['kode']; 
                        $cabangid = $row_busd['cabangid'];
                    }
                } // end for
	
		mysqli_data_seek($result_busd,0);
		//ambil shorts (bulan)
		echo '<table border="1" cellspacing="0" cellpadding="1">';
		echo '<tr>';
		$header_ = add_space('Nama Daerah',50);
		
		echo "<th align='left'><small>$header_ $eprodid</small></th>"; 
		$query2 = "select * from hrd.br_kode where divprodid='$divprodid' and br='Y' order by kodeid";
		$result2 = mysqli_query($cnit, $query2);
		$num_results2 = mysqli_num_rows($result2);
		$j = 0;
		unset($Bln_);
		for ($i=0; $i < $num_results2; $i++) {
                    $row2 = mysqli_fetch_array($result2);
                    $j++ ;
                    $Bln_[$j] = $row2['kodeid']; 
                    $initial_[$j] = $row2['nama']; 
                    echo "<th><small>".$initial_[$j]."</small></th>";
		} // end for
		echo '<td align="center"><small><b>Total</b></small></td>';
		echo"</tr>";

		//array for total qty 
		$dummy_ = $num_results2+1;  //echo"$dummy_";
		for ($k=1; $k < $dummy_ ; $k++) {
                    $produkQty1_[$k]="";   // qty
                    $produkTQty1_[$k]="";   // total qty
		} 

		$tot1_sales = 0;
		$row_busd = mysqli_fetch_array($result_busd);
		$i = 1;
		while ($i <= $num_results_busd) { 	
                    $nama_ = $row_busd['nama']; 
                    $cabangid1_ = $row_busd['cabangid'];
                    echo "<tr>";
                    echo '<td align="left"><small>'.$row_busd['nama']."</small></td>";
                    while ($nama_ == $row_busd['nama'] && $cabangid1_ == $row_busd['cabangid'] ) {
                        $kodeid_ = $row_busd['kode']; 
                        $tot1_qty = 0;
                        $tot1_qty = $row_busd['jumlah']; 
                        $ccyid = $row_busd['ccyid']; //echo"$ccyid";
                        $row_busd = mysqli_fetch_array($result_busd);
                        $i++;

                       $index_ = array_search($kodeid_,$Bln_);
                       $produkQty1_[$index_] = $tot1_qty;  //echo"$tot_qty";
                       $produkTQty1_[$index_] = $produkTQty1_[$index_] + $tot1_qty; 

                            //jumlah qty dan sales
                        $index_ = ($cabangid1_);
                        $prodTQty1_[$index_] = $prodTQty1_[$index_] + $tot1_qty; //echo"$prodTQty_[$index_]";
                    }  // break per outlet
                    //cetak
                    $total1 = $prodTQty1_[$index_];  
                    $t1_total = $t1_total + $total1; 


                    for ($k=1; $k < $dummy_; $k++) {
                        if ($produkQty1_[$k]=="") {
                            echo '<td align="right"><small>&nbsp;</small></td>';
                        } else {
                            echo "<td align=right><small>$ccyid ".number_format($produkQty1_[$k],0)."</small></td>";
                        }
                        $produkQty1_[$k]="";   
                    } 
                    echo "<td align=right><small><b>$ccyid ".number_format($total1,0)."</b></small></td>";
                    echo "</tr>";
			
		} // end while
                
		//cetak total qty
		echo "<tr>";
		echo '<td><small><b>Total :</b></small></td>';
		for ($k=1; $k < $dummy_; $k++) {
                    if ($produkTQty1_[$k]==0) {
                        echo '<td align="right"><small>&nbsp;</small></td>';
                    } else {
                        echo "<td align=right><small><b>$ccyid ".number_format($produkTQty1_[$k],0)."</b></small></td>";
                        $t_uibr = $produkTQty1_[$k]; 
                    } 
		}
		echo "<td align=right><small><b>$ccyid ".number_format($t1_total,0)."</b></small></td>";
		echo "</tr>";
		echo "</table>\n";
		echo "<br><br>";
		
                
		//INTIM IDR
		echo "<b>DCC/DSS INDTIM (IDR)</b>";
		$query = " select sum(jumlah) as jumlah,kode,ccyid, br_area.cabangid,br_area.nama,region
				   from hrd.br0 br0  
				   join hrd.br_area br_area on br0.icabangid=br_area.icabangid and br0.areaid=br_area.areaid 
				   join hrd.br_kode br_kode on br0.kode=br_kode.kodeid
				   where ((left(tgltrans,7)='$periode1' and tglunrtr='0000-00-00') or LEFT(tglunrtr,7)='$periode1') and retur <> 'Y' 
				   and left(tgltrans,7)='$periode1' and br0.divprodid='$divprodid' and br='Y' and region='T' and ccyid='IDR'
			       group by cabangid,kode,ccyid order by cabangid,kode,ccyid ";// echo"$query";
		//wh20130403, tambah kondisi and br_area.aktif='Y'
		$query = " select sum(jumlah) as jumlah,kode,ccyid, br_area.cabangid,br_area.nama,region
				   from hrd.br0 br0  
				   join hrd.br_area br_area on br0.icabangid=br_area.icabangid and br0.areaid=br_area.areaid and br_area.aktif='Y'
				   join hrd.br_kode br_kode on br0.kode=br_kode.kodeid
				   where ((left(tgltrans,7)='$periode1' and tglunrtr='0000-00-00') or LEFT(tglunrtr,7)='$periode1') and retur <> 'Y' 
				   and left(tgltrans,7)='$periode1' and br0.divprodid='$divprodid' and br='Y' and region='T' and ccyid='IDR'
			       group by cabangid,kode,ccyid order by cabangid,kode,ccyid ";// echo"$query";
		$result = mysqli_query($cnit, $query);
		$num_results = mysqli_num_rows($result);
		
		for ($i=0; $i < $num_results; $i++) {
                    $row = mysqli_fetch_array($result);
                    if ($i==0) {
                       $kodeid = $row['kode']; 
                       $cabangid = $row['cabangid'];
                    }
		} // end for
	
		mysqli_data_seek($result,0);
		//ambil shorts (bulan)
		echo '<table border="1" cellspacing="0" cellpadding="1">';
		echo '<tr>';
		$header_ = add_space('Nama Daerah',50);
		
		echo "<th align='left'><small>$header_ $eprodid</small></th>"; 
		$query2 = "select * from hrd.br_kode where divprodid='$divprodid' and br='Y' order by kodeid";
		$result2 = mysqli_query($cnit, $query2);
		$num_results2 = mysqli_num_rows($result2);
		$j = 0;
		unset($Bln_);
		for ($i=0; $i < $num_results2; $i++) {
                    $row2 = mysqli_fetch_array($result2);
                    $j++ ;
                    $Bln_[$j] = $row2['kodeid']; 
                    $initial_[$j] = $row2['nama']; 
                    echo "<th><small>".$initial_[$j]."</small></th>";
		} // end for
		echo '<td align="center"><small><b>Total</b></small></td>';
		echo"</tr>";

		//array for total qty 
		$dummy_ = $num_results2+1;  //echo"$dummy_";
		for ($k=1; $k < $dummy_ ; $k++) {
                    $produkQty_[$k]="";   // qty
                    $produkTQty_[$k]="";   // total qty
		} 
		$row = mysqli_fetch_array($result);
		$i = 1;
		while ($i <= $num_results) { 	
			$nama_ = $row['nama']; 
			$cabangid_ = $row['cabangid'];
			echo "<tr>";
			echo '<td align="left"><small>'.$row['nama']."</small></td>";
			$tot_prod = 0;
			while ($nama_ == $row['nama'] && $cabangid_ == $row['cabangid'] ) {
				$kodeid_ = $row['kode']; 
				$tot_qty = 0;
				$tot_qty = $row['jumlah']; 
				$row = mysqli_fetch_array($result);
				$i++;
			 
			   $index_ = array_search($kodeid_,$Bln_);
			   $produkQty_[$index_] = $tot_qty;  //echo"$tot_qty";
			   $produkTQty_[$index_] = $produkTQty_[$index_] + $tot_qty; 
			   
				//jumlah qty dan sales
			    $index_ = ($cabangid_);
			    $prodTQty_[$index_] = $prodTQty_[$index_] + $tot_qty; 
				if ($row['jumlah'] <> "") {
					$tot = 1;
				} else {
					$tot = 0;
				}
				$tot_prod = $tot_prod + $tot;
			}  // break per outlet
			//cetak
			$total1 = $prodTQty_[$index_]; 
			$t_total1 = $t_total1 + $total1; 

			for ($k=1; $k < $dummy_; $k++) {
				if ($produkQty_[$k]=="") {
					echo '<td align="right"><small>&nbsp;</small></td>';
				} else {
				   echo '<td align="right"><small>'.number_format($produkQty_[$k],0)."</small></td>";
				}
				$produkQty_[$k]="";   
			} 
			echo '<td align="right"><small><b>'.number_format($total1,0)."</b></small></td>";
			echo "</tr>";
			
		} // end while
                
                
		//cetak total qty
		echo "<tr>";
		echo '<td><small><b>Total :</b></small></td>';
		for ($k=1; $k < $dummy_; $k++) {
                    if ($produkTQty_[$k]==0) {
                        echo '<td align="right"><small>&nbsp;</small></td>';
                    } else {
                        echo '<td align="right"><small><b>'.number_format($produkTQty_[$k],0)."</b></small></td>";
                        $t_itm = $produkTQty_[$k]; 
                    } 
		}
		echo '<td align="right"><small><b>'.number_format($t_total1,0)."</b></small></td>";
		echo "</tr>";
		echo "<tr>";
		echo '<td><small><b>Total INDBAR + INDTIM (IDR):</b></small></td>';
		for ($k=1; $k < $dummy_; $k++) {
			if ($k ==1) {
				$dcc_ibr = $t_total - $t_ibr;
				$dcc_itm = $t_total1 - $t_itm;
				$total2 = $dcc_ibr + $dcc_itm;
			} else {
				$total2 = $t_ibr + $t_itm;
			}
			echo '<td align="right"><small><b>'.number_format($total2,0)."</b></small></td>";
			 
		}
		$t_total3 = $t_total + $t_total1;
		echo '<td align="right"><small><b>'.number_format($t_total3,0)."</b></small></td>";
		echo "</tr>";
		echo "</table>\n";
		echo "<br><br>";
		
                
		//INTIM USD
		echo "<b>DCC/DSS INDTIM (USD)</b>";
		$query = " select sum(jumlah) as jumlah,kode, br_area.cabangid,br_area.nama,region
					from hrd.br0 br0  
					join hrd.br_area br_area on br0.icabangid=br_area.icabangid and br0.areaid=br_area.areaid 
					join hrd.br_kode br_kode on br0.kode=br_kode.kodeid
					where ((left(tgltrans,7)='$periode1' and tglunrtr='0000-00-00') or LEFT(tglunrtr,7)='$periode1') and retur <> 'Y' 
					and left(tgltrans,7)='$periode1' and br0.divprodid='$divprodid' and br='Y' and region='T' and ccyid='USD'
			        group by cabangid,kode,ccyid order by cabangid,kode,ccyid ";// echo"$query";
		//wh20130403, tambah kondisi and br_area.aktif='Y'
		$query = " select sum(jumlah) as jumlah,kode, br_area.cabangid,br_area.nama,region
					from hrd.br0 br0 
					join hrd.br_area br_area on br0.icabangid=br_area.icabangid and br0.areaid=br_area.areaid and br_area.aktif='Y'
					join hrd.br_kode br_kode on br0.kode=br_kode.kodeid
					where ((left(tgltrans,7)='$periode1' and tglunrtr='0000-00-00') or LEFT(tglunrtr,7)='$periode1') and retur <> 'Y' 
					and left(tgltrans,7)='$periode1' and br0.divprodid='$divprodid' and br='Y' and region='T' and ccyid='USD'
			        group by cabangid,kode,ccyid order by cabangid,kode,ccyid ";// echo"$query";
		$result = mysqli_query($cnit, $query);
		$num_results = mysqli_num_rows($result);
		
		for ($i=0; $i < $num_results; $i++) {
			$row = mysqli_fetch_array($result);
			if ($i==0) {
			   $kodeid = $row['kode']; 
			   $cabangid = $row['cabangid'];
			}
		} // end for
	
		mysqli_data_seek($result,0);
		//ambil shorts (bulan)
		echo '<table border="1" cellspacing="0" cellpadding="1">';
		echo '<tr>';
		$header_ = add_space('Nama Daerah',50);
		
		echo "<th align='left'><small>$header_ $eprodid</small></th>"; 
		$query2 = "select * from hrd.br_kode where divprodid='$divprodid' and br='Y' order by kodeid";
		$result2 = mysqli_query($cnit, $query2);
		$num_results2 = mysqli_num_rows($result2);
		$j = 0;
		unset($Bln_);
		for ($i=0; $i < $num_results2; $i++) {
			$row2 = mysqli_fetch_array($result2);
			$j++ ;
			$Bln_[$j] = $row2['kodeid']; 
			$initial_[$j] = $row2['nama']; 
			echo "<th><small>".$initial_[$j]."</small></th>";
		} // end for
		echo '<td align="center"><small><b>Total</b></small></td>';
		echo"</tr>";

		//array for total qty 
		$dummy_ = $num_results2+1;  //echo"$dummy_";
		for ($k=1; $k < $dummy_ ; $k++) {
			$produkQty2_[$k]="";   // qty
			$produkTQty2_[$k]="";   // total qty
		} 
		$row = mysqli_fetch_array($result);
		$i = 1;
		while ($i <= $num_results) { 	
			$nama_ = $row['nama']; 
			$cabangid2_ = $row['cabangid'];
			echo "<tr>";
			echo '<td align="left"><small>'.$row['nama']."</small></td>";
			$tot_prod = 0;
			while ($nama_ == $row['nama'] && $cabangid2_ == $row['cabangid'] ) {
				$kodeid_ = $row['kode']; 
				$tot_qty2 = 0;
				$tot_qty2 = $row['jumlah']; 
				$row = mysqli_fetch_array($result);
				$i++;
			 
			   $index_ = array_search($kodeid_,$Bln_);
			   $produkQty2_[$index_] = $tot_qty2;  //echo"$tot_qty";
			   $produkTQty2_[$index_] = $produkTQty2_[$index_] + $tot_qty2; 
			   
				//jumlah qty dan sales
			    $index_ = ($cabangid2_);
			    $prodTQty2_[$index_] = $prodTQty2_[$index_] + $tot_qty2; 
				if ($row['jumlah'] <> "") {
					$tot = 1;
				} else {
					$tot = 0;
				}
				$tot_prod = $tot_prod + $tot;
			}  // break per outlet
			//cetak
			$total2 = $prodTQty2_[$index_]; 
			$t_total2 = $t_total2 + $total2; 

			for ($k=1; $k < $dummy_; $k++) {
				if ($produkQty2_[$k]=="") {
					echo '<td align="right"><small>&nbsp;</small></td>';
				} else {
				   echo "<td align=right><small>$ccyid ".number_format($produkQty2_[$k],0)."</small></td>";
				}
				$produkQty2_[$k]="";   
			} 
			echo "<td align=right><small><b>$ccyid ".number_format($total2,0)."</b></small></td>";
			echo "</tr>";
			
		} // end while

		//cetak total qty
		echo "<tr>";
		echo '<td><small><b>Total :</b></small></td>';
		for ($k=1; $k < $dummy_; $k++) {
			if ($produkTQty2_[$k]==0) {
				echo '<td align="right"><small>&nbsp;</small></td>';
			} else {
				echo "<td align=right><small><b>$ccyid ".number_format($produkTQty2_[$k],0)."</b></small></td>";
				$t_uitm = $produkTQty2_[$k]; 
			} 
		}
		echo "<td align=right><small><b>$ccyid ".number_format($t_total2,0)."</b></small></td>";
		echo "</tr>";
		echo "<tr>";
		echo '<td><small><b>Total INDBAR + INDTIM (USD) :</b></small></td>';
		for ($k=1; $k < $dummy_; $k++) {
			if ($k ==1) {
				$dcc_uibr = $t1_total - $t_uibr;
				$dcc_uitm = $t_total2 - $t_uitm;
				$total5 = $dcc_uibr + $dcc_uitm;
			} else {
				$total5 = $t_uibr + $t_uitm; 
			}
			echo "<td align=right><small><b>$ccyid ".number_format($total5,0)."</b></small></td>";
		}
		$t_total4 = $t1_total + $t_total2;
		echo "<td align=right><small><b>$ccyid ".number_format($t_total4,0)."</b></small></td>";
		echo "</tr>";
		echo "</table>\n";

		
		// NON DCC/DSS
		if ($divprodid=='EAGLE') {
			$query_nd = "select SUM(jumlah) as jumlah,ccyid, br_kode.nm_real
					  from hrd.br_kode br_kode 
					  join hrd.br0 br0 on br_kode.kodeid=br0.kode and br_kode.divprodid=br0.divprodid
					  where br0.divprodid='EAGLE' and ((left(tgltrans,7)='$periode1' and tglunrtr='0000-00-00') 
					  or LEFT(tglunrtr,7)='$periode1') and retur <> 'Y' and left(tgltrans,7)='$periode1' and br_kode.br<>'Y'
					  group by kodeid,ccyid";  
		} else {
			$query_nd = "select SUM(jumlah) as jumlah,ccyid,br_kode.nm_real,br0.divprodid
					  from hrd.br_kode br_kode  
					  join hrd.br0 br0 on br_kode.kodeid=br0.kode and br_kode.divprodid=br0.divprodid
					  where (br0.divprodid='PIGEO' or br0.divprodid='otc') and ((left(tgltrans,7)='$periode1' and tglunrtr='0000-00-00') 
					  or LEFT(tglunrtr,7)='$periode1') and retur <> 'Y' and left(tgltrans,7)='$periode1' and br_kode.br<>'Y'
					  group by kodeid,ccyid"; 
		}//echo"$query_nd";
		$result_nd = mysqli_query($cnit, $query_nd);
		$num_results_nd = mysqli_num_rows($result_nd);	  	  	  	  
	  
		if ($num_results_nd) {
			echo "<br><br><b>NON DCC/DSS</b><br>";
			$i = 0;
			$total = $total1 = 0; 
			echo '<table border="1" cellspacing="0" cellpadding="1">';
			echo "<tr>";
			echo "<th align=center><small>No</small></th>";
			echo "<th align=center><small>Alokasi Rincian</small></th>";
			echo "<th align=center><small>Jumlah IDR</small></th>";
			echo "<th align=center><small>Jumlah USD</small></th>";
			while ($i < $num_results_nd) {	 
				$row_nd = mysqli_fetch_array($result_nd);
				$jumlah = $row_nd['jumlah'];		
				$no = $no + 1; 
				$nama = $row_nd['nm_real'];
				$ccyid = $row_nd['ccyid'];
				

				echo "<tr>";
				echo "<td align=center><small>$no</small></td>";
				echo "<td><small>$nama</small></td>";
				if ($ccyid == 'IDR') {
					echo "<td align=right><small>".number_format($jumlah,0)."</small></b></td>";
					echo "<td><small>&nbsp;</small></td>";
					$total = $total + $jumlah;
				} else {
					echo "<td><small>&nbsp;</small></td>";
					echo "<td align=right><small>$ccyid ".number_format($jumlah,0)."</small></b></td>";
					$total1 = $total1 + $jumlah;
					$ccyid_ = $ccyid;
				}
										
				echo "</tr>";
				$i ++;
			} 
			echo "<tr>";
			echo "<td><small>&nbsp;</small></td>";
			echo "<td align=right><small><b>Total :</small></td>";
			echo "<td align=right><small><b>".number_format($total,0)."</b></small></td>";
			echo "<td align=right><small><b>$ccyid_ ".number_format($total1,0)."</b></small></td>";
			echo "</tr>";
			echo "</table>";
		} else {
		}	
		
		//KD
		if ($divprodid=='EAGLE') {
			$query_kd = "select sum(jumlah) AS jumlah, MKT.distrib0.nama
						 from hrd.klaim 
						 join MKT.distrib0 on hrd.klaim.distid=MKT.distrib0.distid 
						 where left(tgltrans,7)='$periode1' GROUP by hrd.klaim.distid";  //echo"$query";
		
                    $result_kd = mysqli_query($cnit, $query_kd);
                    $num_results_kd = mysqli_num_rows($result_kd);	  	  	  	  
                    $no = 0;
                    if ($num_results_kd) {
                            echo "<br><br><b>KLAIM DISKON</b><br>";
                            $i = 0;
                            $total = 0; 
                            echo '<table border="1" cellspacing="0" cellpadding="1">';
                            echo "<tr>";
                            echo "<th align=center><small>No</small></th>";
                            echo "<th align=center><small>Distributor</small></th>";
                            echo "<th align=center><small>Jumlah</small></th>";
                            while ($i < $num_results_kd) {	 
                                    $row_kd = mysqli_fetch_array($result_kd);
                                    $jumlah = $row_kd['jumlah'];		
                                    $no = $no + 1; 
                                    $nama = $row_kd['nama'];
                                    $total = $total + $jumlah;

                                    echo "<tr>";
                                    echo "<td align=center><small>$no</small></td>";
                                    echo "<td><small>$nama</small></td>";
                                    echo "<td align=right><small>".number_format($jumlah,0)."</b></td>";						
                                    echo "</tr>";
                                    $i ++;
                            } 
                            echo "<tr>";
                            echo "<td><small>&nbsp;</small></td>";
                            echo "<td align=right><b><small>Total :</small></b></td>";
                            echo "<td align=right><b><small>".number_format($total,0)."</small></b></td>";
                            echo "</tr>";
                            echo "</table>";
                    } else {
                    }
                }
?>

</body>
</form>
</html>
