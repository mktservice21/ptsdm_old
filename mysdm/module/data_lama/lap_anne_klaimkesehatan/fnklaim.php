<?php
function klaim_detail($result,$records,$srnama,$tahun) {
    include("config/koneksimysqli_it.php");
		$row = mysqli_fetch_array($result);
		$i = 1;
		echo "<b>Laporan Klaim Kesehatan $srnama</b><br>";
		echo "<b>Periode $tahun</b><br>";
		echo "<table border=1>";
		echo "<tr>";
		echo "<th><small>Tgl. Klaim</small></th>";
		echo "<th><small>a/n.</small></th>";
		echo "<th><small>Keterangan</small></th>";
		echo "<th><small>Keterangan</small></th>";
		echo "<th><small>Nilai</small></th>";
		echo "</tr>";
		$total = 0;
		while ($i <= $records) {
			$tglklaim = $row['tglKlaim'];
			$first = 1;
			while (($i <= $records) and ($tglklaim == $row['tglKlaim'])) {
				$an = $row['an'];
				$nmklaim = $row['nmklaim'];
				$dibayar = $row['dibayar'];
				$ket = $row['ket'];
				if ($ket=="") {
				   $ket = "&nbsp;";
				} 
				echo "<tr>";
				if ($first) {
					echo "<td align=left><small>$tglklaim</small></td>";
					$first = 0;
				} else {
				    echo "<td align=left><small>&nbsp;</small></td>";
				}
				echo "<td align=center><small>$an</small></td>";
				echo "<td align=left><small>$nmklaim</small></td>";
				echo "<td align=left><small>$ket</small></td>";
				echo "<td align=right><small>".number_format($dibayar,0)."</small></td>";
				echo "</tr>";
			    $total = $total + $dibayar;
			    $row = mysqli_fetch_array($result);
				$i++;
			} // break per tglklaim
		}  // end of file
		if ($total <> 0) {
		   echo "<tr>";
		   echo "<td>&nbsp</td>";
		   echo "<td>&nbsp</td>";
		   echo "<td>&nbsp</td>";
		   echo "<td align=right><small><b>Total :</b></small></td>";
		   echo "<td align=right><small><b>".number_format($total,0)."</b></small></td>";
		   echo "</tr>";
		}
		echo "</table>";
		echo "<br>";
			
}

function klaim_rekap($query,$srnama,$tahun) {
    error_reporting(0);
    include("config/koneksimysqli_it.php");
	    $result = mysqli_query($cnit, $query);
		$records = mysqli_num_rows($result);
		$i = 1;
		$row = mysqli_fetch_array($result);
                
		unset($arrKode);
		while ($i <= $records) {
			$kode = $row['kode'];
			$nmklaim = $row['nmklaim'];
			$dibayar = $row['dibayar'];
                        
			if (is_null($arrKode[$kode]['dibayar'])) {
				$arrKode[$kode]['dibayar'] = 0;
			} 
			$arrKode[$kode]['dibayar'] = $arrKode[$kode]['dibayar'] + $dibayar;
			$arrKode[$kode]['nmklaim'] = $nmklaim; 
                        
			$row = mysqli_fetch_array($result);
			$i++;
		} // end of file
		echo "<b>Rekap Laporan Klaim Kesehatan $srnama</b><br>";
		echo "<b>Periode $tahun</b><br>";
		echo "<table border=1>";
		echo "<tr>";
		echo "<th>Keterangan</th>";
		echo "<th>Nilai</th>";
		echo "</tr>";

		ksort($arrKode);
		$arrKeys = array_keys($arrKode);
		$total = 0;
		foreach ($arrKeys as $key) {
		   $dibayar = $arrKode[$key]['dibayar'];
		   $nmklaim = $arrKode[$key]['nmklaim'];
		   echo "<tr>";
		   echo "<td align=left>$nmklaim</td>";
		   echo "<td align=right>".number_format($dibayar,0)."</td>";
		   echo "</tr>";
		   $total = $total + $dibayar;
		}
		if ($total <> 0){
		   echo "<tr>";
		   echo "<td align=right><b>Total :</b></td>";
		   echo "<td align=right><b>".number_format($total,0)."</b></td>";
		   echo "</tr>";
		}
		echo "</table>";

}
?>
