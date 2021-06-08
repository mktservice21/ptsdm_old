<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REKAP BIAYA RUTIN OTC.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>
<html>
<head>
    <title>REKAP BIAYA RUTIN OTC</title>
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
        $_POST['e_periode']="";
        $_POST['sts_apv']="fin";
        
        $query = "select * from dbmaster.t_suratdana_br WHERE idinput='$idinputspd'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $ra= mysqli_fetch_array($tampil);
            if (!empty($ra['tglf']))
                $_POST['bulan1']=$ra['tglf'];
            
            $_POST['e_periode']=$ra['kodeperiode'];
            
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
            
        }
    }else{
			$pperiodepuktttd = $_POST['bulan1'];
			if (!empty($pperiodepuktttd)) {
				$pperiodepuktttd = date("Ymd", strtotime($pperiodepuktttd));
					if ((double)$pperiodepuktttd>='20200601') {
						$nnama_ss_mktdir=$nnama_ss_mktdir2;
					}
			}
			
			
	}
    
    
    $tglnow = date("d/m/Y");
    $kdperiode = $_POST['e_periode'];
    $tgl01 = $_POST['bulan1'];
    $periode1 = date("Y-m", strtotime($tgl01));
    $fperiode = " AND date_format(bulan,'%Y-%m') ='$periode1' ";
    $per1 = date("F Y", strtotime($tgl01));
    if ($kdperiode==1)
        $pertgl = date("01/m/Y", strtotime($tgl01));
    else
        $pertgl = date("16/m/Y", strtotime($tgl01));
    $fkdperiode = " AND br.kodeperiode='$kdperiode' ";
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DTBRROTCPD01_".$_SESSION['IDCARD']."_$now ";
    $tmp02 =" dbtemp.DTBRROTCPD02_".$_SESSION['IDCARD']."_$now ";
    
    $query = "select k.karyawanId karyawanid, k.nama, b.areaid, b.icabangid, o.nama nama_area, CAST(0  AS DECIMAL(30,2)) AMOUNT 
        , CAST(0  AS DECIMAL(30,2)) POTONGAN, CAST(0  AS DECIMAL(30,2)) PENAMBAHAN, CAST(0  AS DECIMAL(30,2)) BAYAR
        , CAST(''  AS char(100)) KET, CAST(''  AS char(150)) nama_karyawan 
        , CAST(''  AS char(1)) IKET 
        from hrd.karyawan k JOIN dbmaster.t_karyawan_posisi b on k.karyawanId=b.karyawanId
        LEFT JOIN MKT.iarea_o o on b.areaId=o.areaid_o and b.icabangid=o.icabangid_o
        WHERE IFNULL(b.rutin_chc,'')='Y'";
        //WHERE k.karyawanId not in (select DISTINCT karyawanId from dbmaster.t_karyawanadmin) and b.divisiId='OTC' and b.aktif='Y' AND k.karyawanId <> '0000001272'";
    $query = "create temporary table $tmp01 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
    $query = "SELECT
	br.idrutin,
	br.tgl,
	br.karyawanid,
	br.icabangid,
	br.areaid,
	br.jumlah,
        pem.penambahan,
	br.keterangan,
	k.nama,
	a.nama nama_area, br.nama_karyawan 
        FROM
                dbmaster.t_brrutin0 AS br
        LEFT JOIN hrd.karyawan AS k ON br.karyawanid = k.karyawanId
        LEFT JOIN MKT.iarea AS a ON br.areaid = a.areaId and br.icabangid=a.iCabangId 
        LEFT JOIN dbmaster.t_brrutin2 pem on br.idrutin=pem.idrutin 
        WHERE br.kode=1 AND br.stsnonaktif <> 'Y' AND br.divisi='OTC' $fperiode $fkdperiode";
    
    
    $query = "create temporary table $tmp02 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
    $query = "DELETE FROM $tmp01 WHERE karyawanid='0000002200'";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
    
    $query = "INSERT INTO $tmp01 (karyawanId, nama, areaid, icabangid, nama_area, IKET)"
            . " select a.idrutin as karyawanId, a.nama_karyawan as nama, a.areaid, a.icabangid, b.nama nama_area, 'Y' as iket from $tmp02 a 
        LEFT JOIN MKT.iarea_o b on a.areaId=b.areaid_o and a.icabangid=b.icabangid_o
        where karyawanid='0000002200'";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
    $query = "UPDATE $tmp02 SET nama=nama_karyawan, karyawanid=idrutin WHERE karyawanid='0000002200'";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
    
    //goto hapusdata;
    
    $query = "UPDATE $tmp01 set AMOUNT=ifnull((select sum(jumlah) from  $tmp02 where $tmp02.karyawanid=$tmp01.karyawanId),0)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
    $query = "UPDATE $tmp01 set PENAMBAHAN=ifnull((select sum(penambahan) from  $tmp02 where $tmp02.karyawanid=$tmp01.karyawanId),0)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
    $query = "UPDATE $tmp01 set nama_area=ifnull((select o.nama from MKT.iarea_o o where o.icabangid_o=$tmp01.icabangid AND o.areaid_o=$tmp01.areaid),'')";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
    $query = "UPDATE $tmp01 set BAYAR=ifnull(AMOUNT,0)-ifnull(POTONGAN,0)+ifnull(PENAMBAHAN,0)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
    $query = "DELETE FROM $tmp01 WHERE karyawanid='0000002200' AND IFNULL(BAYAR,0)=0";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
    $query = "DELETE FROM $tmp02 WHERE karyawanid='0000002200' AND IFNULL(jumlah,0)=0";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
    
    $query = "DELETE FROM $tmp01 WHERE IFNULL(BAYAR,0)=0 AND karyawanid IN (select IFNULL(karyawanid,'') FROM "
            . " hrd.karyawan WHERE aktif='N')";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
    $query = "DELETE FROM $tmp01 WHERE IFNULL(BAYAR,0)=0 AND karyawanid IN (select IFNULL(karyawanid,'') FROM "
            . " dbmaster.t_karyawanadmin)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
    
    
    
    
    //mysqli_query($cnit, "drop temporary table $tmp01");
    //mysqli_query($cnit, "drop temporary table $tmp02");
    //exit;
    
    //cari no br / no divisi yang sudah di save
    if (empty($pnomorbr)) {
        $pkode="1";
        $psubkode="03";
        $query = "SELECT nodivisi as pnomor FROM dbmaster.t_suratdana_br WHERE stsnonaktif<>'Y' AND divisi='OTC' AND "
                . " kodeid='$pkode' AND subkode='$psubkode' AND DATE_FORMAT(tglf,'%Y-%m')='$periode1'";
        $showkan= mysqli_query($cnit, $query);
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
                <tr><td width="150px"><b>Rekap Biaya Rutin Per </b></td><td align="left"><?PHP echo "$per1 "; ?></td></tr>
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
                <th align="center">Amount</th>
                <th align="center">Pot.</th>
                <th align="center">Penambahan</th>
                <th align="center">B. Rutin yg dibayarkan</th>
                <th align="center">Ket</th>
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
                            $potongan = "";
                            //$potongan=number_format($row['POTONGAN'],0,",",",");
                            $penambahan=number_format($row['PENAMBAHAN'],0,",",",");
                            if ($penambahan==0) $penambahan="";
                            $bayar=number_format($row['BAYAR'],0,",",",");
                            
                            $total = $total + $row['AMOUNT'];
                            $totalpot = $totalpot + $row['POTONGAN'];
                            $totalpen = $totalpen + $row['PENAMBAHAN'];
                            $totalbay = $totalbay + $row['BAYAR'];
                            
                            echo "<tr>";
                            echo "<td>$no</td>";
                            echo "<td>$nama</td>";
                            echo "<td>$area</td>";
                            echo "<td align='right'>$jumlah</td>";
                            echo "<td align='right'>$potongan</td>";
                            echo "<td align='right'>$penambahan</td>";
                            echo "<td align='right'>$bayar</td>";
                            echo "<td></td>";
                            echo "</tr>";
                            
                            $no++;
                            $row = mysqli_fetch_array($result);
                            $reco++;  
                            
                        }
                        $total=number_format($total,0,",",",");
                        $totalbay=number_format($totalbay,0,",",",");
                        $totalpen=number_format($totalpen,0,",",",");
                        if ($totalpen==0) $totalpen="";
                        //TOTAL
                        echo "<tr>";
                        echo "<td colspan=3 align='right'><b>Total : </b></td>";
                        echo "<td align='right'><b>$total</b></td>";
                        echo "<td align='right'><b></b></td>";
                        echo "<td align='right'><b>$totalpen</b></td>";
                        echo "<td align='right'><b>$totalbay</b></td>";
                        echo "<td><b></b></td>";
                        echo "</tr>";
                    }
                    
                    
                    
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
<?PHP
hapusdata:
    mysqli_query($cnit, "drop temporary table $tmp01");
    mysqli_query($cnit, "drop temporary table $tmp02");
    mysqli_close($cnit);
?>