<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=LAPORAN BIAYA RUTIN.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>
<html>
<head>
    <title>LAPORAN BIAYA RUTIN</title>
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
    $tgl02 = $_POST['bulan2'];
    $periode1 = date("Ym", strtotime($tgl01));
    $periode2 = date("Ym", strtotime($tgl02));
    
    $per1 = date("F Y", strtotime($tgl01));
    $per2 = date("F Y", strtotime($tgl02));
    
    $edivisi = $_POST['divprodid'];
    $stsapv = $_POST['sts_apv'];
    $pkodeperiode = $_POST['t_kodeperiode'];
    
    
    $fdivisi = "";
    $fcabang = "";
    $fstsapv = "";
    $fkodeperiode = "";
    
    $fperiode = " AND DATE_FORMAT(br.bulan, '%Y%m') between '$periode1' AND '$periode1' ";
    if (!empty($edivisi) AND ($edivisi <> "*")) $fdivisi = " AND br.divisi='$edivisi' ";
    
    if (!empty($pkodeperiode) AND ($pkodeperiode <> "*")) $fkodeperiode = " AND br.kodeperiode='$pkodeperiode' ";
    
    
    $e_stsapv="Semua Data";
    if ($stsapv == "fin") {
        $fstsapv = " AND ifnull(br.tgl_fin,'') <> '' AND ifnull(br.tgl_fin,'0000-00-00') <> '0000-00-00' ";
        $e_stsapv="Sudah Proses Finance";
    }elseif ($stsapv == "belumfin") {
        $fstsapv = " AND (ifnull(br.tgl_fin,'') = '' OR ifnull(br.tgl_fin,'0000-00-00') = '0000-00-00') ";
        $e_stsapv="Belum Proses Finance";
    }
    
    $query = "SELECT
	b1.nourut,
	br.idrutin,
	br.kode,
	br.karyawanid,
	br.bulan,
	br.kodeperiode,
	br.periode1,
	br.periode2,
	br.jumlah,
	br.keterangan,
	br.divisi,
	br.tgltrans,
	br.jmltrans,
	k.nama,
	a.nama nama_area_o,
	aa.Nama nama_area,
	c.nama nama_cabang,
	co.nama nama_cabang_o,
	b1.nobrid,
	i.nama nama_brid,
	b1.qty,
	b1.rp,
	b1.rptotal,
	b1.notes,
        b1.alasanedit_fin,
	b1.coa,
	c1.NAMA4 nama_coa,
        br.nama_karyawan
        FROM
                dbmaster.t_brrutin1 AS b1
        LEFT JOIN dbmaster.t_brrutin0 AS br ON b1.idrutin = br.idrutin
        LEFT JOIN hrd.karyawan AS k ON br.karyawanid = k.karyawanId
        LEFT JOIN MKT.icabang AS c ON c.iCabangId=br.icabangid
        LEFT JOIN MKT.icabang_o AS co ON co.icabangid_o=br.icabangid_o
        LEFT JOIN MKT.iarea_o AS a ON br.areaid_o = a.areaid_o and br.icabangid_o=a.icabangid_o
        LEFT JOIN MKT.iarea AS aa ON br.areaid = aa.areaId and br.icabangid=aa.iCabangId
        LEFT JOIN dbmaster.t_brid AS i ON b1.nobrid = i.nobrid
        LEFT JOIN dbmaster.coa_level4 c1 ON c1.COA4 = b1.coa WHERE br.kode=1 AND br.stsnonaktif <> 'Y' $fperiode $fdivisi $fstsapv $fkodeperiode ";
    
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DTBRRTNOTC01_".$_SESSION['IDCARD']."_$now ";
    
    $query = "create temporary table $tmp01 ($query)";
    mysqli_query($cnit, $query);
    
    $query = "UPDATE $tmp01 a set a.coa=IFNULL((select b.COA4 from dbmaster.posting_coa_rutin b WHERE 
        a.divisi=b.divisi AND a.nobrid=b.nobrid LIMIT 1),a.coa)";
    mysqli_query($cnit, $query);
    
    $query = "UPDATE $tmp01 a set a.nama_coa=IFNULL((select b.NAMA4 from dbmaster.coa_level4 b WHERE 
        a.coa=b.COA4 LIMIT 1),a.nama_coa)";
    mysqli_query($cnit, $query);
    
    
    $query = "UPDATE $tmp01 a set a.nama=a.nama_karyawan WHERE karyawanid='$_SESSION[KRYNONE]'";
    mysqli_query($cnit, $query);
    
    
    $query = "UPDATE $tmp01 a set a.notes=a.alasanedit_fin WHERE IFNULL(alasanedit_fin,'')<>''";
    mysqli_query($cnit, $query);
    
    
    $mydiv=$edivisi;
    if ($edivisi=="CAN") $mydiv="CANARY";
    
    $query = "select distinct tgl, nodivisi from dbmaster.t_suratdana_br WHERE DATE_FORMAT(tgl,'%Y%m')='$periode1' AND kodeid='1' AND subkode='03' and kodeperiode='$pkodeperiode' ";
    $resul = mysqli_query($cnit, $query);
    $ro = mysqli_fetch_array($resul);
    
    $nodivisi = $ro['nodivisi'];
    $ctgl = date("d F Y");
    if (!empty($ro['tgl']))
        $ctgl = date('d F Y', strtotime($ro['tgl']));
    
    $perpilihper = date("15 F y", strtotime($tgl01));
    if ((INT)$pkodeperiode==2) $perpilihper = date("t F y", strtotime($tgl01));
?>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td><b>To </b></td><td>:</td><td>Sdr. Lina (Finance)</td></tr>
                <tr><td><b>&nbsp;</b></td><td>&nbsp;</td><td>&nbsp;</td></tr>
                <tr><td><b>Biaya Rutin Per <?PHP echo "$perpilihper"; ?> </b></td><td>:</td><td><?PHP echo "$nodivisi"; ?></td></tr>
                <tr><td><b>** Klaim </b></td><td>:</td><td><?PHP echo "$ctgl"; ?></td></tr>
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
                <th align="center">Date</th>
                <th align="center">Bukti</th>
                <th align="center">Kode</th>
                <th align="center">Perkiraan</th>
                <th align="center">Cabang</th>
                <th align="center">No</th>
                <th align="center">Nama</th>
                <th align="center">Jenis</th>
                <th align="center">Description</th>
                <th align='center'>Lain2</th>
                <?PHP if ($_GET['ket']=="excel") echo "<th align='center'>&nbsp;</th>"; ?> 
                <th align="center">Debit</th>
                <th align="center">Credit</th>
                <th align="center">Saldo</th>
            </thead>
            <tbody>
                <?PHP
                    $gtotalsaldo=0;
                    $totalcredit=0;
                    $query = "select distinct karyawanid, nama, idrutin, jumlah from $tmp01 order by nama, idrutin";
                    $result0 = mysqli_query($cnit, $query);
                    while ($row0 = mysqli_fetch_array($result0)) {
                        $pkaryawanid=$row0['karyawanid'];
                        $pidrutin=$row0['idrutin'];
                        $psaldo=$row0['jumlah'];
                        $gtotalsaldo=$gtotalsaldo+$psaldo;
                        
                        
                        $query = "select * from $tmp01 WHERE karyawanid='$pkaryawanid' AND idrutin='$pidrutin' order by kodeperiode, nama, idrutin, coa";
                        $result = mysqli_query($cnit, $query);
                        $records = mysqli_num_rows($result);
                        while ($row = mysqli_fetch_array($result)) {
                            $pdate="";
                            $pnobukti="";
                            $pcoa=$row['coa'];
                            $pnmcoa=$row['nama_coa'];
                            $pcabang=$row['nama_cabang'];
                            

                            $pnama=$row['nama'];
                            $pjenis=$row['nama_brid'];
                            $pdesc=$row['notes'];
                            $plain="";

                            $pdebit="";
                            $pcredit=$row['rptotal'];
                            $totalcredit=$totalcredit+$pcredit;
                            
                            if ($_GET['ket']=="excel" AND $_SESSION['IDCARD']=="0000000143")
                                $pcredit=number_format($pcredit,0,".",".");
                            else
                                $pcredit=number_format($pcredit,0,",",",");

                            echo "<tr>";
                            echo "<td nowrap>$pdate</td>";
                            echo "<td nowrap>$pnobukti</td>";
                            echo "<td nowrap>$pcoa</td>";
                            echo "<td nowrap>$pnmcoa</td>";
                            echo "<td nowrap>$pcabang</td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap>$pnama</td>";
                            echo "<td nowrap>$pjenis</td>";
                            echo "<td >$pdesc</td>";
                            echo "<td nowrap>$plain</td>";
                            if ($_GET['ket']=="excel") echo "<td nowrap>&nbsp;</td>";
                            echo "<td nowrap>$pdebit</td>";
                            echo "<td nowrap align='right'>$pcredit</td>";
                            echo "<td nowrap></td>";
                            echo "</tr>";
                        }
                        if ($_GET['ket']=="excel" AND $_SESSION['IDCARD']=="0000000143")
                            $psaldo=number_format($psaldo,0,".",".");
                        else
                            $psaldo=number_format($psaldo,0,",",",");
                        echo "<tr>";
                        if ($_GET['ket']=="excel") {
                            echo "<td></td><td></td><td></td><td></td><td></td><td></td>";
                            echo "<td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
                        }else{
                            echo "<td colspan=12></td>";
                        }
                        echo "<td nowrap align='right'><b>$psaldo</b></td>";
                        echo "</tr>";
                    }
                    if ($_GET['ket']=="excel" AND $_SESSION['IDCARD']=="0000000143") {
                        $gtotalsaldo=number_format($gtotalsaldo,0,".",".");
                        $totalcredit=number_format($totalcredit,0,".",".");
                    }else{
                        $gtotalsaldo=number_format($gtotalsaldo,0,",",",");
                        $totalcredit=number_format($totalcredit,0,",",",");
                    }
                    echo "<tr>";
                    if ($_GET['ket']=="excel") {
                        echo "<td></td><td></td><td></td><td></td><td></td><td></td>";
                            echo "<td></td><td></td><td></td><td></td><td></td><td></td>";
                    }else{
                        echo "<td colspan=11 align='center'>TOTAL</td>";
                    }
                    echo "<td nowrap align='right'><b>$totalcredit</b></td>";
                    echo "<td nowrap align='right'><b>$gtotalsaldo</b></td>";
                    echo "</tr>";
                ?>
            </tbody>
        </table>
    <?PHP
    mysqli_query($cnit, "drop temporary table $tmp01");
    ?>
</body>
</html>
