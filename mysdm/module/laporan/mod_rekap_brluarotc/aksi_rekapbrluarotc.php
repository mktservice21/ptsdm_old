<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REALISASI LUAR KOTA OTC.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>
<html>
<head>
    <title>REALISASI LUAR KOTA OTC</title>
<?PHP if ($_GET['ket']!="excel") { ?>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
    <link href="css/laporanbaru.css" rel="stylesheet">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
<?PHP } ?>
</head>

<body>
<?php


    $gmrheight = "100px";
    $ngbr_idinput="";
    $gbrttd_fin1="";
    $gbrttd_fin2="";
    $gbrttd_dir1="";
    $gbrttd_dir2="";
    
    $namapengaju_ttd_fin1="";
    $namapengaju_ttd_fin2="";
    
    $namapengaju_ttd1="";
    $namapengaju_ttd2="";
    
    $ntgl_apv1="";
    $ntgl_apv2="";
    $ntgl_apv_dir1="";
    $ntgl_apv_dir2="";
    
    $pnomorbr="";
	
	
    $nnama_ss_mktdir1="dr. Farida Soewanto";
    $nnama_ss_mktdir2="Evi Kosina Santoso";
    
    $nnama_ss_mktdir=$nnama_ss_mktdir1;
	
	$ptgltransbank="";
	
    if (isset($_GET['ispd'])) {
        $idinputspd=$_GET['ispd'];
        $_POST['bulan1']="2000-01-00";
        $_POST['sts_rpt']="";
        
        $query = "select * from dbmaster.t_suratdana_br WHERE idinput='$idinputspd'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $ra= mysqli_fetch_array($tampil);
            if (!empty($ra['tglf']))
                $_POST['bulan1']=$ra['tglf'];
            
            $_POST['sts_rpt']=$ra['sts'];
            
            $pnomorbr= $ra['nodivisi'];
            
            $ngbr_idinput=$ra['idinput'];
            
            $gbrttd_fin1=$ra['gbr_apv1'];
            $gbrttd_fin2=$ra['gbr_apv2'];
            
            $gbrttd_dir1=$ra['gbr_dir'];
            $gbrttd_dir2=$ra['gbr_dir2'];
            
			
			$tgljakukannya=$ra['tgl'];
			if ($tgljakukannya=="0000-00-00") $tgljakukannya="";
			if (!empty($tgljakukannya)) $tgljakukannya = date("Ymd", strtotime($tgljakukannya));
			
            $passdirid=$ra['dir'];
            if ($passdirid=="0000002403") $nnama_ss_mktdir=$nnama_ss_mktdir2;
			else{
				if (!empty($tgljakukannya)) {
					if ((double)$tgljakukannya>='20200701') {
						$nnama_ss_mktdir=$nnama_ss_mktdir2;
					}
				}
			}
			
			
			
            if (!empty($gbrttd_fin1)) {
                $data="data:".$gbrttd_fin1;
                $data=str_replace(' ','+',$data);
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);
                $namapengaju_ttd_fin1="imgfin1_".$ngbr_idinput."TTDSPD_.png";
                file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd_fin1, $data);
                
                if (!empty($ra['tgl_apv1']) AND $ra['tgl_apv1']<>"0000-00-00") $ntgl_apv1="Approved<br/>".date("d-m-Y", strtotime($ra['tgl_apv1']));
                
            }
            
            if (!empty($gbrttd_fin2)) {
                $data="data:".$gbrttd_fin2;
                $data=str_replace(' ','+',$data);
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);
                $namapengaju_ttd_fin2="imgfin2_".$ngbr_idinput."TTDSPD_.png";
                file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd_fin2, $data);
                
                if (!empty($ra['tgl_apv2']) AND $ra['tgl_apv2']<>"0000-00-00") $ntgl_apv2="Approved<br/>".date("d-m-Y", strtotime($ra['tgl_apv2']));
                
            }
            
            if (!empty($gbrttd_dir1)) {
                $data="data:".$gbrttd_dir1;
                $data=str_replace(' ','+',$data);
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);
                $namapengaju_ttd1="imgdr1_".$ngbr_idinput."TTDSPD_.png";
                file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd1, $data);
                
                if (!empty($ra['tgl_dir']) AND $ra['tgl_dir']<>"0000-00-00") $ntgl_apv_dir1="Approved<br/>".date("d-m-Y", strtotime($ra['tgl_dir']));
                
            }
            
            if (!empty($gbrttd_dir2)) {
                $data="data:".$gbrttd_dir2;
                $data=str_replace(' ','+',$data);
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);
                $namapengaju_ttd2="imgdr2_".$ngbr_idinput."TTDSPD_.png";
                file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd2, $data);
                
                if (!empty($ra['tgl_dir2']) AND $ra['tgl_dir2']<>"0000-00-00") $ntgl_apv_dir2="Approved<br/>".date("d-m-Y", strtotime($ra['tgl_dir2']));
                
            }
            
            
        }
		
		
        $query = "select tanggal from dbmaster.t_suratdana_bank where idinput='$idinputspd' and stsinput='K' order by tanggal";
		$tampil2= mysqli_query($cnmy, $query);
		$ketemu2= mysqli_num_rows($tampil2);
		if ($ketemu2>0) {
			$rs= mysqli_fetch_array($tampil2);
			$ptgltransbank=$rs['tanggal'];
			if ($ptgltransbank=="0000-00-00") $ptgltransbank="";
			if (!empty($ptgltransbank)) {
				$ptgltransbank = date("d/m/Y", strtotime($ptgltransbank));
			}
			
		}
        
        
    }else{
        $tgljakukannya=$_POST['bulan1'];
        $tgljakukannya = date("Ym01", strtotime($tgljakukannya));
        if ((double)$tgljakukannya>='20200701') {
            $nnama_ss_mktdir=$nnama_ss_mktdir2;
        }
    }
    
    
    $tglnow = date("d/m/Y");
    $tgl01 = $_POST['bulan1'];
    $periode1 = date("Y-m", strtotime($tgl01));
    
    $fperiode = " AND ( (DATE_FORMAT(br.periode1, '%Y-%m') = '$periode1') OR (DATE_FORMAT(br.periode2, '%Y-%m') = '$periode1') ) ";
    
    $per1 = date("F Y", strtotime($tgl01));
    $pbulan = date("F", strtotime($tgl01));
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DBRROTCPBLL01_".$_SESSION['IDCARD']."_$now ";
    $tmp02 =" dbtemp.DBRROTCPBLL02_".$_SESSION['IDCARD']."_$now ";
    
    $query = "select k.karyawanId karyawanid, k.nama, b.areaid, b.icabangid, o.nama nama_area, CAST(0  AS DECIMAL(30,2)) AMOUNT 
        , CAST(0  AS DECIMAL(30,2)) POTONGAN, CAST(0  AS DECIMAL(30,2)) PENAMBAHAN, CAST(0  AS DECIMAL(30,2)) BAYAR
        , CAST(''  AS char(100)) KET from hrd.karyawan k JOIN dbmaster.t_karyawan_posisi b on k.karyawanId=b.karyawanId
        LEFT JOIN MKT.iarea_o o on b.areaId=o.areaid_o and b.icabangid=o.icabangid_o
        WHERE k.karyawanId not in (select DISTINCT karyawanId from dbmaster.t_karyawanadmin) and b.divisiId='OTC' and b.aktif='Y'";
    
    $query = "create temporary table $tmp01 ($query)";
    mysqli_query($cnit, $query);
    
    $query = "INSERT INTO $tmp01 (karyawanId, nama, icabangid, areaid, nama_area)"
            . "select a.id, a.nama, a.icabangid_o, a.areaid_o, b.nama from dbmaster.t_karyawan_kontrak a JOIN MKT.iarea_o b on a.areaid_o=b.areaid_o AND a.icabangid_o=b.icabangid_o";
    mysqli_query($cnit, $query);
    
    $query = "SELECT
	br.idrutin,
	br.tgl,
	br.karyawanid,
	br.icabangid,
	br.areaid,
	br.jumlah,
	br.keterangan,
	k.nama,
	a.nama nama_area, br.nama_karyawan, br.atasan1, br.atasan2, br.atasan3, br.atasan4 
        FROM
                dbmaster.t_brrutin0 AS br
        LEFT JOIN hrd.karyawan AS k ON br.karyawanid = k.karyawanId
        LEFT JOIN MKT.iarea AS a ON br.areaid = a.areaId and br.icabangid=a.iCabangId WHERE br.kode=2 AND br.stsnonaktif <> 'Y' AND br.divisi='OTC' $fperiode";
    
    $query = "create temporary table $tmp02 ($query)"; 
    mysqli_query($cnit, $query);
    
    $query = "update $tmp02 a JOIN dbmaster.t_karyawan_kontrak b ON a.nama_karyawan=b.nama AND"
            . " a.atasan1=b.atasan1 AND a.atasan2=b.atasan2 AND a.atasan3=b.atasan3 AND a.atasan4=b.atasan4 "
            . " SET a.karyawanid=b.id WHERE a.karyawanid='0000002200'";
    mysqli_query($cnit, $query);
    
    
    
    $query = "UPDATE $tmp01 set AMOUNT=ifnull((select sum(jumlah) from  $tmp02 where $tmp02.karyawanid=$tmp01.karyawanId),0)";
    mysqli_query($cnit, $query);
    $query = "UPDATE $tmp01 set nama_area=ifnull((select o.nama from MKT.iarea_o o where o.icabangid_o=$tmp01.icabangid AND o.areaid_o=$tmp01.areaid),'')";
    mysqli_query($cnit, $query);
    
    mysqli_query($cnit, "DELETE FROM $tmp01 WHERE ifnull(AMOUNT,0)=0");
    
    
    
    if (empty($pnomorbr)) {
        $tglinput = date("Y-m", strtotime($tgl01));
        //cari no br / no divisi yang sudah di save
        $pkode="2";
        $psubkode="21";
        $query = "SELECT nodivisi as pnomor "
                . " FROM dbmaster.t_suratdana_br WHERE stsnonaktif<>'Y' AND divisi='OTC' AND kodeid='$pkode' AND subkode='$psubkode' AND "
                . " date_format(tglf,'%Y-%m')='$tglinput' LIMIT 1";
        //echo $query;
        $showkan= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($showkan);
        if ($ketemu>0){
            $s= mysqli_fetch_array($showkan);
            if (!empty($s['pnomor'])) { 
                $pnomorbr= $s['pnomor'];
            }
        }
    }
    //end cari no br / no divisi yang sudah di save
    
        
?>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td width="150px"><b>PT SDM</b></td><td></td></tr>
                <tr><td width="200px"><b>Realisasi Luar Kota OTC Per </b></td><td align="left"><?PHP echo "$per1 "; ?></td></tr>
                <tr><td width="150px"><b>No.BR </b></td><td><?PHP echo "$pnomorbr"; ?></td></tr>
				<?PHP
				if (!empty($ptgltransbank)) {
					echo "<tr><td width='200px'><b>Tanggal Transfer </b></td><td>$ptgltransbank</td></tr>";
				}
				?>
            </table>
        </div>
        <div id="isikanan">
            
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
    
        <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                <th align="center">No</th>
                <th align="center">Nama</th>
                <th align="center">Daerah</th>
                <th align="center">Biaya Luar Kota <?PHP echo "$pbulan "; ?></th>
            </thead>
            <tbody>
                <?PHP
                    $no=1;
                    $total=0;
                    $totalpot=0;
                    $totalpen=0;
                    $totalbay=0;
                    $query = "select * from $tmp01 order by nama, karyawanid";
                    $result = mysqli_query($cnit, $query);
                    $records = mysqli_num_rows($result);
                    $row = mysqli_fetch_array($result);
                    
                    if ($records) {
                        $reco = 1;
                        while ($reco <= $records) {
                            $noid=$row['karyawanid'];
                            $nama=$row['nama'];
                            $area=$row['nama_area'];
                            $jumlah=number_format($row['AMOUNT'],0,",",",");
                            
                            $total = $total + $row['AMOUNT'];
                            
                            echo "<tr>";
                            echo "<td>$no</td>";
                            echo "<td>$nama</td>";
                            echo "<td>$area</td>";
                            echo "<td align='right'>$jumlah</td>";
                            echo "</tr>";
                            
                            $no++;
                            $row = mysqli_fetch_array($result);
                            $reco++;  
                            
                        }
                        $total=number_format($total,0,",",",");
                        //TOTAL
                        echo "<tr>";
                        echo "<td colspan=3 align='right'><b>Total : </b></td>";
                        echo "<td align='right'><b>$total</b></td>";
                        echo "</tr>";
                    }
                    
                    
                    
                    mysqli_query($cnit, "drop temporary table $tmp01");
                    mysqli_query($cnit, "drop temporary table $tmp02");
                    mysqli_close($cnit);
                ?>
            </tbody>
        </table>
        <br/>&nbsp;<br/>&nbsp;
        <?PHP
        
        
        if ($_GET['ket']=="excel") {
    
                    echo "<table class='tjudul' width='100%'>";
                        echo "<tr>";
                        
                            echo "<td align='center'>";
                            echo "Yang membuat,";
                            echo "<br/>&nbsp;<br/>&nbsp;<br/>$ntgl_apv1<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                            echo "<b>(Saiful Rahmat)</b></td>";
                             
							
                            echo "<td align='center'>";
                            echo "Checker,";
                            echo "<br/>&nbsp;<br/>&nbsp;<br/>$ntgl_apv2<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                            echo "<b>(Marianne Prasanti)</b></td>";
                            
                            
                            echo "<td align='center'>";
                            echo "Menyetujui,";
                            echo "<br/>&nbsp;<br/>&nbsp;<br/>$ntgl_apv_dir1<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                            echo "<b>($nnama_ss_mktdir)</b></td>";
                            
							echo "<td align='center'>";
							echo "Mengetahui,";
							echo "<br/>&nbsp;<br/>&nbsp;<br/>$ntgl_apv_dir2<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
							echo "<b>(Ira Budisusetyo)</b></td>";
                            
                        echo "</tr>";
                        
                    echo "</table>";
            
        }else{
                    echo "<table class='tjudul' width='100%'>";
                        echo "<tr>";
                        
                            echo "<td align='center'>";
                            echo "Yang membuat,";
                            if (!empty($namapengaju_ttd_fin1))
                                echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin1' height='$gmrheight'><br/>";
                            else
                                echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                            echo "<b>(Saiful Rahmat)</b></td>";
                             
							
                            echo "<td align='center'>";
                            echo "Checker,";
                            if (!empty($namapengaju_ttd_fin2))
                                echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin2' height='$gmrheight'><br/>";
                            else
                                echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                            echo "<b>(Marianne Prasanti)</b></td>";
                            
                            
                            echo "<td align='center'>";
                            echo "Menyetujui,";
                            if (!empty($namapengaju_ttd1))
                                echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd1' height='$gmrheight'><br/>";
                            else
                                echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                            echo "<b>($nnama_ss_mktdir)</b></td>";
                            
							
							echo "<td align='center'>";
							echo "Mengetahui,";
							if (!empty($namapengaju_ttd2))
								echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd2' height='$gmrheight'><br/>";
							else
								echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
							echo "<b>(Ira Budisusetyo)</b></td>";
                            
							
							
                        echo "</tr>";
                        
                    echo "</table>";
                    
        }
        
        /*
        echo "<table width='100%' border='0px' style='border : 0px solid #fff; font-size: 11px; font-weight: bold;'>";
        echo "<tr align='center'>";
        echo "<td>Yang membuat,</td><td colspan=2></td><td>Menyetujui,</td>";
        echo "</tr>";

        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";

        echo "<tr align='center'>";
        echo "<td>(Saiful Rahmat)</td><td></td><td></td><td>($nnama_ss_mktdir)</td>";
        echo "</tr>";
        echo "</table>";
         * 
         */
        ?>
        <br/>&nbsp;<br/>&nbsp;
</body>
</html>
