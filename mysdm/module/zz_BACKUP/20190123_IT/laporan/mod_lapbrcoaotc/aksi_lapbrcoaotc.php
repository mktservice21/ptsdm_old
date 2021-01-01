<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=LAPORAN BIAYA RUTIN COA OTC.xls");
    }
    
    include("config/koneksimysqli_it.php");
    include("config/common.php");
    
?>
<html>
<head>
    <title>LAPORAN BIAYA RUTIN COA OTC</title>
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
    $tglnow = date("d/m/Y");
    $tgl01 = $_POST['bulan1'];
    $periode1 = date("Y-m", strtotime($tgl01));
    $fperiode = " AND date_format(b.bulan,'%Y-%m') ='$periode1' ";
    $per1 = date("F Y", strtotime($tgl01));
    
    $tgl02= date('Y-m-d', strtotime('-1 month', strtotime($tgl01)));
    $periode2 = date("Y-m", strtotime($tgl02));
    $per2 = date("F Y", strtotime($tgl02));
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DTBRROTCCOA01_".$_SESSION['IDCARD']."_$now ";
    $tmp02 =" dbtemp.DTBRROTCCOA02_".$_SESSION['IDCARD']."_$now ";
    $tmp03 =" dbtemp.DTBRROTCCOA03_".$_SESSION['IDCARD']."_$now ";
    
    $query="SELECT
	b.tgl,
	b.bulan,
	b.kodeperiode,
	b.periode1,
	b.periode2,
	b.karyawanid,
        c.nama nama_karyawan,
	b.icabangid,
	d.nama nama_cabang,
	b.areaid,
	e.Nama nama_area,
	b.icabangid_o,
	g.nama nama_cabang_o,
	b.areaid_o,
	h.nama nama_area_o,
	a.nourut,
	a.idrutin,
	a.nobrid,
	i.nama nama_des,
	l.COA1 coa1, l.NAMA1 nama_coa1,
	k.COA2 coa2, k.NAMA2 nama_coa2,
	j.COA3 coa3, j.NAMA3 nama_coa3,
	a.coa coa4,
	f.NAMA4 nama_coa4,
	a.qty,
	a.rp,
	a.rptotal,
        a.deskripsi,
        b.tgltrans,
        b.divisi,
        a.rptotal as ca1,
        a.rptotal as selisih,
        a.rptotal as ca2,
        a.rptotal as jmltrans 
        FROM
                dbmaster.t_brrutin1 a
        LEFT JOIN dbmaster.t_brrutin0 b ON a.idrutin = b.idrutin
        LEFT JOIN hrd.karyawan c ON b.karyawanid = c.karyawanId
        LEFT JOIN MKT.icabang d ON b.icabangid = d.iCabangId
        LEFT JOIN MKT.iarea e ON b.areaid = e.areaId
        AND b.icabangid = e.iCabangId
        LEFT JOIN dbmaster.coa_level4 f ON a.coa = f.COA4
        LEFT JOIN MKT.icabang_o g ON b.icabangid_o = g.icabangid_o
        LEFT JOIN MKT.iarea_o h ON b.icabangid = h.icabangid_o
        AND b.areaid_o = h.areaid_o
        JOIN dbmaster.t_brid i ON a.nobrid = i.nobrid
        LEFT JOIN dbmaster.coa_level3 j on f.COA3=j.COA3
        LEFT JOIN dbmaster.coa_level2 k on j.COA2=k.COA2
        LEFT JOIN dbmaster.coa_level1 l on k.COA1=l.COA1
        WHERE b.stsnonaktif <> 'Y' AND b.kode = 1 and b.divisi='OTC' $fperiode";
    
    $query = "create temporary table $tmp01 ($query)"; 
    mysqli_query($cnit, $query);
    
    mysqli_query($cnit, "update $tmp01 set ca1=0, ca2=0, selisih=0, jmltrans=0");
    
    $query = "select tgltrans, coa4, nama_coa4, icabangid_o icabangid, nama_cabang_o nama_cabang, areaid_o areaid, nama_area_o nama_area, karyawanid, nama_karyawan, divisi,
        nobrid, nama_des, deskripsi, sum(qty) qty, sum(rp) rp, sum(rptotal) rptotal, ca1, selisih, ca2, jmltrans
        from $tmp01 
        GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13";
    
    $query = "create temporary table $tmp02 ($query)"; 
    mysqli_query($cnit, $query);
    
    $query="select karyawanid, periode, jumlah from dbmaster.t_ca0 where stsnonaktif <> 'Y' and divisi='OTC' AND "
            . " DATE_FORMAT(periode,'%Y-%m') between '$periode1' AND '$periode2' AND "
            . " karyawanid in (select distinct karyawanid from $tmp01)";
    $query = "create temporary table $tmp03 ($query)"; 
    mysqli_query($cnit, $query);
    
    $query = "update $tmp02 set ca1=ifnull((select sum(jumlah) from $tmp03 where $tmp03.karyawanid=$tmp02.karyawanid AND DATE_FORMAT($tmp03.periode,'%Y-%m')='$periode1'),0)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "update $tmp02 set ca2=ifnull((select sum(jumlah) from $tmp03 where $tmp03.karyawanid=$tmp02.karyawanid AND DATE_FORMAT($tmp03.periode,'%Y-%m')='$periode2'),0)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    //mysqli_query($cnit, "UPDATE $tmp02 set selisih=ca1-rptotal");
    
    //mysqli_query($cnit, "drop temporary table $tmp01");
    //mysqli_query($cnit, "drop temporary table $tmp02");
    //exit;
        
?>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td width="150px"><b>PT SDM</b></td><td></td></tr>
                <tr><td width="150px"><b>Laporan Biaya Rutin COA OTC Per </b></td><td><?PHP echo "$per1 "; ?></td></tr>
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
                <th align="center">Date Trsfr</th>
                <th align="center">Bukti</th>
                <th align="center" colspan="2">COA</th>
                <th align="center">DAERAH</th>
                <th align="center">No LK</th>
                <th align="center">NAMA</th>
                <th align="center">DIVISI</th>
                <th align="center" colspan="2">Description</th>
                <th align="center">Jenis</th>
                <th align="center">Credit</th>
                <th align="center">Saldo REAL</th>
                <th align="center">CA <?PHP echo $per1; ?></th>
                <th align="center">Selisih</th>
                <th align="center">SPV/DM/SM/GSM</th>
                <th align="center">CA  <?PHP echo $per2; ?></th>
                <th align="center">JUML TRSF</th>
                <th align="center">TGL TRSF REALISASI</th>
            </thead>
            <tbody>
                <?PHP
                    $no=1;
                    $totalrp=0;
                    
                    $query = "select * from $tmp02 order by nama_karyawan, karyawanid";
                    $result = mysqli_query($cnit, $query);
                    $records = mysqli_num_rows($result);
                    $row = mysqli_fetch_array($result);
                    
                    if ($records) {
                        $reco = 1;
                        while ($reco <= $records) {
                            $pkaryawanid=$row['karyawanid'];
                            $pnmkaryawan=$row['nama_karyawan'];
                            while (($reco <= $records) AND $row['karyawanid']==$pkaryawanid) {
                                
                                $ptgltrasn=$row['tgltrans'];
                                $pcoa4=$row['coa4'];
                                $pnmcoa4=$row['nama_coa4'];
                                $pkdcabang=$row['icabangid'];
                                $pnmcabang=$row['nama_cabang'];
                                $pkdarea=$row['areaid'];
                                $pnmarea=$row['nama_area'];
                                $pdivisi=$row['divisi'];
                                $pnobrid=$row['nobrid'];
                                $pnmdes=$row['nama_des'];
                                $pdeskripsi=$row['deskripsi'];

                                $pqty=number_format($row['qty'],0,",",",");
                                $prp=number_format($row['rp'],0,",",",");
                                $prptotal=number_format($row['rptotal'],0,",",",");
                                $pca1=number_format($row['ca1'],0,",",",");
                                $pselisih=number_format($row['selisih'],0,",",",");
                                $pca2=number_format($row['ca2'],0,",",",");
                                
                                if ( (int)$pca1==0 ) $pca1="";
                                if ( (int)$pselisih==0 ) $pselisih="";
                                if ( (int)$pca2==0 ) $pca2="";
                                
                                if ($pnobrid=="04") {
                                    if ($_GET['ket']=="excel")
                                         $pnmdes=$pnmdes." (".$pqty."x".$prp.")";
                                    else
                                        $pnmdes=$pnmdes."<br/>(".$pqty."x".$prp.")";
                                }
                                
                                $totalrp =$totalrp+$row['rptotal'];
                                
                                echo "<tr>";
                                echo "<td>$ptgltrasn</td>";
                                echo "<td></td>";
                                echo "<td nowrap>$pcoa4</td>";
                                echo "<td nowrap>$pnmcoa4</td>";
                                echo "<td nowrap>$pnmarea</td>";
                                echo "<td></td>";
                                echo "<td nowrap>$pnmkaryawan</td>";
                                echo "<td>$pdivisi</td>";
                                echo "<td>$pnmdes</td>";
                                echo "<td>$pdeskripsi</td>";
                                echo "<td></td>";
                                echo "<td nowrap align='right'>$prptotal</td>";
                                echo "<td nowrap align='right'></td>";
                                echo "<td nowrap align='right'>$pca1</td>";
                                echo "<td nowrap align='right'>$pselisih</td>";
                                echo "<td></td>";
                                echo "<td nowrap align='right'>$pca2</td>";
                                echo "<td></td>";
                                echo "<td></td>";
                                echo "</tr>";

                                $no++;
                                $row = mysqli_fetch_array($result);
                                $reco++;
                            }
                            
                            echo "<tr>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td></td>";
                            echo "<td nowrap></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td nowrap align='right'></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "</tr>";
                                
                        }
                        $totalrp=number_format($totalrp,0,",",",");
                        echo "<tr>";
                        //echo "<td colspan='11' align='right'> TOTAL : &nbsp;</td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td></td>";
                        echo "<td nowrap></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td nowrap align='right'>$totalrp</td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "</tr>";
                    }
                    
                    
                    
                    mysqli_query($cnit, "drop temporary table $tmp01");
                    mysqli_query($cnit, "drop temporary table $tmp02");
                    mysqli_query($cnit, "drop temporary table $tmp03");
                ?>
            </tbody>
        </table>

        <br/>&nbsp;<br/>&nbsp;
</body>
</html>
