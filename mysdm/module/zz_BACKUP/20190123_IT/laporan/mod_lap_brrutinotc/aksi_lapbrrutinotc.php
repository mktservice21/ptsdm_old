<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=COA RUTIN OTC.xls");
    }
    
    include("config/koneksimysqli_it.php");
    include("config/common.php");
    
?>
<html>
<head>
    <title>COA RUTIN OTC</title>
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
    $tglnow = date("d F Y");
    $tgl01 = $_POST['bulan1'];
    $periode1 = date("Ym", strtotime($tgl01));
    
    $per1 = date("F Y", strtotime($tgl01));
    
    $ekaryawan = $_POST['e_karyawan'];
    $ecabang = $_POST['icabangid_o'];
    
    $fkaryawan = "";
    $fcabang = "";
    
    $fperiode = " AND DATE_FORMAT(br.bulan, '%Y%m') = '$periode1'";
    if (!empty($ekaryawan) AND ($ekaryawan <> "*")) $fkaryawan = " AND br.karyawanid='$ekaryawan' ";
    if (!empty($ecabang) AND ($ecabang <> "*")) $fcabang = " AND br.icabangid='$ecabang' ";
    
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
	a.nama nama_area,
	b1.nobrid,
	i.nama nama_brid,
	b1.qty,
	b1.rp,
	b1.rptotal,
	b1.notes,
	b1.coa,
	c1.NAMA4 nama_coa
        FROM
                dbmaster.t_brrutin1 AS b1
        LEFT JOIN dbmaster.t_brrutin0 AS br ON b1.idrutin = br.idrutin
        LEFT JOIN hrd.karyawan AS k ON br.karyawanid = k.karyawanId
        LEFT JOIN MKT.iarea_o AS a ON br.areaid = a.areaid_o and br.icabangid=a.icabangid_o
        LEFT JOIN dbmaster.t_brid AS i ON b1.nobrid = i.nobrid
        LEFT JOIN dbmaster.coa_level4 c1 ON c1.COA4 = b1.coa WHERE br.kode=1 AND br.stsnonaktif <> 'Y' AND br.divisi='OTC' $fperiode $fkaryawan $fcabang ";
        
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DTBRRTNOTC01_".$_SESSION['IDCARD']."_$now ";
        
?>
    <center><h2><u></u></h2></center>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td><b>To </b></td><td>:</td><td>Sdri. Lina (Finance)</td></tr>
                <tr><td><b>Biaya Rutin Per <?PHP echo "$per1"; ?></b></td><td>:</td><td></td></tr>
                <tr><td><b>** Klaim </b></td><td>:</td><td><?PHP echo "$tglnow"; ?></td></tr>
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
                <th align="center">NO</th>
                <th align="center">Nama</th>
                <th align="center">Jenis</th>
                <th align="center">Deskripsi</th>
                <th align="center">Lain2</th>
                <th align="center">Debit</th>
                <th align="center">Credit</th>
                <th align="center">Saldo</th>
                <th align="center">DPP</th>
                <th align="center">PPN</th>
                <th align="center">PPH</th>
                <th align="center" nowrap>TGL FP<br/>PPN</th>
                <th align="center" nowrap>NO.<br/>SERI FP</th>
                <th align="center" nowrap>TGL FP<br/>PPH</th>
                <th align="center" nowrap>NO.<br/>SERI FP</th>
            </thead>
            <tbody>
                <?PHP
                    $totaldebit=0;
                    $totalkredit=0;
                    $totalsaldo=0;
                    
                    $query = "create temporary table $tmp01 ($query)";
                    mysqli_query($cnit, $query);
                    $query = "select distinct karyawanid from $tmp01 order by nama";
                    $result = mysqli_query($cnit, $query);
                    while ($row= mysqli_fetch_array($result)) {
                        $pkaryawanid=$row['karyawanid'];
                        
                        $totperdivd=0;
                        $totperdivk=0;
                        $totperdivs=0;
                    
                        $query2 = "select * from $tmp01 where karyawanid='$pkaryawanid' order by nama, coa";
                        $result2 = mysqli_query($cnit, $query2);
                        while ($row2= mysqli_fetch_array($result2)) {
                            $noid=$row2['idrutin'];
                            $pkaryawanid=$row2['karyawanid'];
                            $pnama=$row2['nama'];
                            $area=$row2['nama_area'];
                            $kodeper=$row2['kodeperiode'];
                            $bulan=$row2['bulan'];
                            $bbulan = date("F Y", strtotime($bulan));
                            $kp = "Periode 1";
                            if ($kodeper==2) $kp = "Periode 2";
                            
                            $pcoa=$row2['coa'];
                            $pnmcoa=$row2['nama_coa'];
                            $nourutid=(int)$row2['nobrid'];
                            $pnmbrid=$row2['nama_brid'];
                            
                            $pqtyjml="";
                            if ($nourutid==4) {
                                $pqtyjml="(".$row2['qty']." x ".number_format($row2['rp'],0,",",",").")";
                            }
                            
                            $pjumlahk=number_format($row2['rp'],0,",",",");
                            
                            $totperdivk=$row2['jumlah'];
                            
                            echo "<tr>";
                            echo "<td nowrap>$tglnow</td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap>$pcoa</td>";
                            echo "<td nowrap>$pnmcoa</td>";
                            echo "<td nowrap>$area</td>";
                            
                            echo "<td nowrap></td>";
                            echo "<td nowrap>$pnama</td>";
                            echo "<td nowrap>$pnmbrid</td>";
                            echo "<td nowrap>$pqtyjml</td>";
                            echo "<td nowrap></td>";
                            
                            echo "<td nowrap align='right'></td>";
                            echo "<td nowrap align='right'>$pjumlahk</td>";
                            echo "<td nowrap align='right'></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "</tr>";
                            
                        }
                        $totperdivs=$totperdivk;
                        
                        $totalsaldo=$totalsaldo+$totperdivs;
                        
                        $totperdivk=number_format($totperdivk,0,",",",");
                        $totperdivs=number_format($totperdivs,0,",",",");
                        
                        echo "<tr>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";

                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        
                        echo "<td nowrap align='right'></td>";
                        echo "<td nowrap align='right'></td>";
                        echo "<td nowrap align='right'><b>$totperdivs</b></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "</tr>";
                        
                        $totperdivd=0;
                        $totperdivk=0;
                        $totperdivs=0;
                            
                    }
                    
                    $totalsaldo=number_format($totalsaldo,0,",",",");
                    
                    echo "<tr>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";

                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td colspan=4 align='center'><b>Grand Total</b></td>";
                    
                    echo "<td nowrap align='right'><b>$totalsaldo</b></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";

                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "</tr>";
                ?>
            </tbody>
        </table>
    
    <!--
        <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                <th align="center">ID</th>
                <th align="center">Yang Mengajukan</th>
                <th align="center">Area</th>
                <th align="center">Periode</th>
                <th align="center">Akun</th>
                <th align="center">COA</th>
                <th align="center">Nama COA</th>
                <th align="center">hari x Rp.</th>
                <th align="center">Jumlah (Rp.)</th>
            </thead>
            <tbody>
                <?PHP
                    $query = "create temporary table $tmp01 ($query)";
                    mysqli_query($cnit, $query);
                    $total=0;
                    $query = "select * from $tmp01 order by idrutin, nobrid";
                    $result = mysqli_query($cnit, $query);
                    $records = mysqli_num_rows($result);
                    $row = mysqli_fetch_array($result);
                    
                    if ($records) {
                        $reco = 1;
                        while ($reco <= $records) {
                            $noid=$row['idrutin'];
                            $nama=$row['nama'];
                            $area=$row['nama_area'];
                            $kodeper=$row['kodeperiode'];
                            $bulan=$row['bulan'];
                            $bbulan = date("F Y", strtotime($bulan));
                            $kp = "Periode 1";
                            if ($kodeper==2) $kp = "Periode 2";
                            $jumlah=number_format($row['jumlah'],0,",",",");
                            
                            $total = $total + $row['jumlah'];
                            
                            
                            echo "<tr>";
                            echo "<td>$noid</td>";
                            echo "<td>$nama</td>";
                            echo "<td>$area</td>";
                            echo "<td>($kp) $bbulan</td>";
                            echo "<td colspan=5></td>";
                            echo "</tr>";
                            
                            while (($reco <= $records) AND $row['idrutin']==$noid) {
                                $nourutid=(int)$row['nobrid'];
                                $akun=$row['nama_brid'];
                                $coa=$row['coa'];
                                $coanama=$row['nama_coa'];
                                $rptotal=number_format($row['rptotal'],0,",",",");
                                
                                $qtyjml="";
                                if ($nourutid==4) {
                                    $qtyjml="(".$row['qty']." x ".number_format($row['rp'],0,",",",").")";
                                }
                                
                                echo "<tr>";
                                echo "<td colspan=4></td>";
                                echo "<td>$akun</td>";
                                echo "<td>$coa</td>";
                                echo "<td>$coanama</td>";
                                echo "<td align='right'>$qtyjml</td>";
                                echo "<td align='right'>$rptotal</td>";
                                echo "</tr>";
                                
                                $row = mysqli_fetch_array($result);
                                $reco++;    
                            }
                            //sub total ID
                            echo "<tr>";
                            echo "<td colspan=7 align='right'><b>Sub Total : </b></td>";
                            echo "<td align='right'><b></b></td>";
                            echo "<td align='right'><b>$jumlah</b></td>";
                            echo "</tr>";
                            
                        }
                        $total=number_format($total,0,",",",");
                        //TOTAL
                        echo "<tr>";
                        echo "<td colspan=7 align='right'><b>Total : </b></td>";
                        echo "<td align='right'><b></b></td>";
                        echo "<td align='right'><b>$total</b></td>";
                        echo "</tr>";
                    }
                    
                    
                    
                    mysqli_query($cnit, "drop temporary table $tmp01");
                ?>
            </tbody>
        </table>
    -->
    
</body>
</html>
