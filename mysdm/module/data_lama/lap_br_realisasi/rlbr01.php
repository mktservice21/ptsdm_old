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
    <title>Realisasi BR</title>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    </head>


<body>
<form id="rlbr01" action="rlbr00.php" method=post>
<?php
	//include("../../config/common.php");
	//include("../../config/common3.php");
	include "config/koneksimysqli.php";
	

        $now=date("mdYhis");
        $tmp01 =" dbtemp.RKPLPARL01_".$_SESSION['USERID']."_$now ";
        $tmp02 =" dbtemp.RKPLPARL02_".$_SESSION['USERID']."_$now ";
    
	if (empty($_SESSION['IDCARD'])) {
	  echo 'not authorized';
	  exit;
	} else {
	  
		$srid = $_SESSION['USERID'];
                $srnama = $_SESSION['NAMALENGKAP'];
		$sr_id = substr('0000000000'.$srid,-10);
		$userid = $_SESSION['USERID'];
		$tahun = $_POST['tahun'];	
		$divprodid = $_POST['divprodid']; 
		$lampiran1 = $_POST['lampiran'];
		
		echo "<B>REALISASI BR $tahun<BR>";
                if ($divprodid=='A') {
                } else {
                    echo "DIVISI : $divprodid</b><br>";
                    $where_ = "and divprodid='$divprodid'";
                }
		
                if ($lampiran1=='A') {
                } else {
                    if ($lampiran1=='L') {
                        echo "<b>LAMPIRAN : ADA</b>";
                        $where_ = $where_."and (lampiran='Y' or tgltrm<>'0000-00-00-00')";
                    } else {
                        if ($lampiran1=='T') {
                            echo "<b>LAMPIRAN : TIDAK ADA";
                            $where_ = $where_."and ca='Y' and tgltrm='0000-00-00'";
                        }
                    }
                }
                
                $ccyid_="";
		echo "<br>";
		$query = "select *, CAST('' as CHAR(50)) as nodivisi_spd from hrd.br0 where left(tgltrans,4)='$tahun' ".$where_." order by tgltrans,noslip"; 
		// echo"$query";
                $query = "create TEMPORARY table $tmp01 ($query)";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapudata; }
                
                $query = "select distinct c.nomor, c.nodivisi, b.bridinput from $tmp01 a
                    JOIN dbmaster.t_suratdana_br1 b on a.brId=b.bridinput JOIN 
                    dbmaster.t_suratdana_br c on b.idinput=c.idinput 
                    WHERE c.stsnonaktif<>'Y' AND c.divisi<>'OTC' AND c.jenis_rpt<>'D'";
                $query = "create TEMPORARY table $tmp02 ($query)";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapudata; }
                
                
                $query = "UPDATE $tmp01 a SET a.nodivisi_spd=(select b.nodivisi FROM $tmp02 b WHERE a.brId=b.bridinput LIMIT 1)";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapudata; }
                
                //$query = "select *, CAST('' as CHAR(50)) as nodivisi_spd from hrd.br0 where left(tgltrans,4)='$tahun' ".$where_." order by tgltrans,noslip"; 
                $query = "select * from $tmp01 where left(tgltrans,4)='$tahun' ".$where_." order by tgltrans,noslip"; 
		$result = mysqli_query($cnmy, $query);
		$records = mysqli_num_rows($result);	
		$row = mysqli_fetch_array($result);
		if ($records) {
		$i = 1;
		$gtotal = $gtotal_ = $gtotalru1_ =$gtotalru2_ = $gtotalri1_ =$gtotalri2_ = 0;
		while ($i <= $records) {
			$bln_ = substr($row['tgltrans'],0,7);
			$bulan_ = $row['tgltrans'];
			echo '<table border="1" cellspacing="0" cellpadding="1">';
			echo "<br>";
			echo "<b>Bulan : $bln_</b>";
			echo '<tr>';
			echo '<th><small>No. </small></th>';
			echo '<th><small>No. BR/Divisi </small></th>';
			echo '<th><small>Nama Pembuat</small></th>';
			echo '<th><small>Tgl. Transfer</small></th>';
			echo '<th><small>Keterangan</small></th>';
			echo '<th><small>Nama Dokter</small></th>';
			echo '<th><small>Jumlah IDR</small></th>';
			echo '<th><small>Jumlah USD</small></th>';
			echo '<th><small>Nama Realisasi</small></th>';
			echo '<th><small>No Slip</small></th>';
			echo '<th><small>Tgl. Terima</small></th>';
			echo '<th><small>Jumlah Realisasi (IDR)</small></th>';
			echo '<th><small>Jumlah Realisasi (USD)</small></th>';
			echo '<th><small>Lain - lain</small></th>';
			//echo '<th>&nbsp;</th>';
			//echo '<th>&nbsp;</th>';
			echo '</tr>';
			$total = $total_ = $totalru1_ = $totalru2_ = $totalri1_ = $totalri2_ = 0;
			$no = 0;
		   while ( ($i<=$records) and ($bln_ == substr($row['tgltrans'],0,7)) ) {
			$no = $no + 1;	
			$pnodivisi_spd = $row['nodivisi_spd'];
			$karyawanId = $row['karyawanId'];
			$jumlah = $row['jumlah'];
			$jumlah1 = $row['jumlah1'];
			$brid = $row['brId'];
			$tgltrm = $row['tgltrm'];
			$tgltrans = $row['tgltrans'];
			$divprodid = $row['divprodid'];
			$dokterId = $row['dokterId'];
			$dokter = $row['dokter']; //echo"$dokter";
			$aktivitas1 = $row['aktivitas1'];
			$aktivitas2 = $row['aktivitas2'];
			$realisasi1 = $row['realisasi1'];
			$noslip = $row['noslip'];
			$ccyid = $row['ccyId'];
			$lain2 = $row['lain2'];
			$lampiran = $row['lampiran'];
			$batal = $row['batal']; //echo"batal=$batal";
			
			
			$nama_mr = '';
			$query_mr = "select nama from hrd.karyawan where karyawanId='$karyawanId'"; 
			$result_mr = mysqli_query($cnmy, $query_mr);
			$num_results_mr = mysqli_num_rows($result_mr);
			if ($num_results_mr) {
				 $row_mr = mysqli_fetch_array($result_mr);
				 $nama_mr = $row_mr['nama'];
			}
			
			$nama_dkt = '';
			$query_dkt = "select nama from hrd.dokter where dokterId='$dokterId'"; 
			$result_dkt = mysqli_query($cnmy, $query_dkt);
			$num_results_dkt = mysqli_num_rows($result_dkt);
			if ($num_results_dkt) {
				 $row_dkt = mysqli_fetch_array($result_dkt);
				 $nama_dkt = $row_dkt['nama'];
			}
	
			if ($nama_dkt=='') {
				$nama_dkt = $dokter;
			}
			
			echo '<tr>';
			echo "<td><small>$no</small></td>";
			echo "<td nowrap><small>$pnodivisi_spd</small></td>";
			echo "<td><small>$nama_mr</small></td>";
			echo "<td><small>$tgltrans</small></td>";
			echo "<td><small>$aktivitas1 $aktivitas2</small></td>";
			if ($nama_dkt<>"") {
				echo "<td><small>$nama_dkt</small></td>";
			} else {
				echo '<td>&nbsp;</td>';
			}
			if ($ccyid<>'IDR') {
				echo "<td><small>&nbsp;</small></td>";
				echo "<td align=right><small>$ccyid ".number_format($jumlah,0)."</small></td>";
				$total_ = $total_ + $jumlah;
				$gtotal_ = $gtotal_ + $jumlah;
				$ccyid_ = $ccyid;
			} else {
				echo "<td align=right><small>".number_format($jumlah,0)."</small></td>";
				echo "<td><small>&nbsp;</small></td>";
				$total = $total + $jumlah;
				$gtotal = $gtotal + $jumlah;
			}
			echo "<td><small>$realisasi1</small></td>";
			echo "<td><small>$noslip</small></td>";			
			if ($tgltrm <>'0000-00-00') {
				if ($batal == 'Y' ) {
					echo "<td><small>&nbsp;</small></td>";
				} else {
					echo "<td><small>$tgltrm</small></td>";
				}
				
				if ($ccyid<>'IDR') {
					echo "<td><small>&nbsp;</small></td>";
					if ($jumlah1==0) {
						if ($batal=='Y') {
							echo "<td align=right><small>$ccyid ".number_format($jumlah1,0)."</small></td>";
						} else {
							echo "<td align=right><small>$ccyid ".number_format($jumlah,0)."</small></td>";
							$totalru1_ = $totalru1_ + $jumlah;
							$gtotalru1_ = $gtotalru1_ + $jumlah;
						}
							
					} else {
						echo "<td align=right><small>$ccyid ".number_format($jumlah1,0)."</small></td>";
						$totalru2_ = $totalru2_ + $jumlah1;
						$gtotalru2_ = $gtotalru2_ + $jumlah1;
					}
					$ccyid_ = $ccyid;
				} else {
					if ($jumlah1==0) {
						if ($batal=='Y') {
							echo "<td align=right><small>".number_format($jumlah1,0)."</small></td>";
						} else {
							echo "<td align=right><small>".number_format($jumlah,0)."</small></td>";
							$totalri1_ = $totalri1_ + $jumlah;
							$gtotalri1_ = $gtotalri1_ + $jumlah;
						}
					} else {
						echo "<td align=right><small>".number_format($jumlah1,0)."</small></td>";
						$totalri2_ = $totalri2_ + $jumlah1;
						$gtotalri2_ = $gtotalri2_ + $jumlah1;
					}
					echo "<td><small>&nbsp;</small></td>";
				}
			} else {
				echo '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>';
			}
			
			if ($lain2 <>'') {
				echo "<td><small>$lain2</small></td>";
			} else {
				if ($batal=='Y') {
					echo "<td><small>BATAL</small></td>";
				} else {
					echo "<td><small>&nbsp;</small></td>";
				}
			}
                        if ($_GET['ket']=="excel") {
                            
                        }else{
                            
                            if ($_SESSION['GROUP']=="22") {
                                echo "<td></td>";
                                echo "<td></td>";
                            }else{
                                echo "<td><a href='eksekusi3.php?module=rptlamarealbredit&brid=$brid&lampiran1=$lampiran1&bulan=$bulan_' target='_blank'><small>Edit</small></a></td>";
                                echo "<td><a href='module/data_lama/lap_br_realisasi/rlbr04.php?brid=$brid&tgltrans=$tgltrans&divprodid=$divprodid' target='_blank'>Delete</a></td>";
                            }
                            
                            //echo "<td><a href='rlbr02.php?brid=$brid&lampiran1=$lampiran1'><small>Edit</small></a></td>";
                            //echo "<td><a href='rlbr04.php?brid=$brid&tgltrans=$tgltrans&divprodid=$divprodid'>Delete</a></td>";			
                        }
			echo '</tr>';

		    $row = mysqli_fetch_array($result);
		    $i++;
		}// break per bulan
			echo "<tr>";
			echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td>";
			echo "<td align=right><b>Total :</td>";
			echo "<td align=right><b>".number_format($total,0)."</b></td>";
			echo "<td align=right><b>$ccyid_ ".number_format($total_,0)."</b></td>";
			echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
			if ($totalri1_==0) {
				echo "<td align=right><b>".number_format($totalri2_,0)."</b></td>";
			} else {
				echo "<td align=right><b>".number_format($totalri1_,0)."</b></td>";
			}
			if ($totalru1_==0) {
				echo "<td align=right><b>$ccyid_ ".number_format($totalru2_,0)."</b></td>";
			} else {
				echo "<td align=right><b>$ccyid_ ".number_format($totalru1_,0)."</b></td>";
			}
		
			echo "<td>&nbsp;</td>";
                        //echo "<td>&nbsp;</td><td>&nbsp;</td>";
			echo "</tr>";
		}// eof  i<= num_results
			echo "<tr>";
			echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td>";
			echo "<td align=right><b>Total :</td>";
			echo "<td align=right><b>".number_format($gtotal,0)."</b></td>";
			echo "<td align=right><b>$ccyid_ ".number_format($gtotal_,0)."</b></td>";
			echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
			if ($gtotalri1_==0) {
				echo "<td align=right><b>".number_format($gtotalri2_,0)."</b></td>";
			} else {
				echo "<td align=right><b>".number_format($gtotalri1_,0)."</b></td>";
			}
			if ($gtotalru1_==0) {
				echo "<td align=right><b>$ccyid_ ".number_format($gtotalru2_,0)."</b></td>";
			} else {
				echo "<td align=right><b>$ccyid_ ".number_format($gtotalru1_,0)."</b></td>";
			}
			echo "<td>&nbsp;</td>";
                        //echo "<td>&nbsp;</td><td>&nbsp;</td>";
			echo "</tr>";
		echo "</table>";
		} else {
			echo "<br /><b>Data tidak ditemukan!!!</b><br/><br/>";		
	    }	
    }  // if (empty($_SESSION['srid'])) 
?>
</form>
</body>
</html>

<?PHP
hapudata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
?>

