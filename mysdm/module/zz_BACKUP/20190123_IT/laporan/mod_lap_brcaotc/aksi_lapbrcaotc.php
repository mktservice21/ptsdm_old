<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=LAPORAN CASH ADVANCE OTC.xls");
    }
    
    include("config/koneksimysqli_it.php");
    include("config/common.php");
    
?>
<html>
<head>
    <title>LAPORAN CASH ADVANCE OTC</title>
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
    
    $ekaryawan = $_POST['e_karyawan'];
    $ecabang = $_POST['icabangid_o'];
    
    $fkaryawan = "";
    $fcabang = "";
    
    $fperiode = " AND DATE_FORMAT(br.periode, '%Y%m') between '$periode1' AND '$periode2' ";
    if (!empty($ekaryawan) AND ($ekaryawan <> "*")) $fkaryawan = " AND br.karyawanid='$ekaryawan' ";
    if (!empty($ecabang) AND ($ecabang <> "*")) $fcabang = " AND br.icabangid='$ecabang' ";
    
    $query = "SELECT
	b1.nourut,
	br.idca,
	br.karyawanid,
	br.periode,
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
                dbmaster.t_ca1 AS b1
        LEFT JOIN dbmaster.t_ca0 AS br ON b1.idca = br.idca
        LEFT JOIN hrd.karyawan AS k ON br.karyawanid = k.karyawanId
        LEFT JOIN MKT.iarea_o AS a ON br.areaid = a.areaid_o and br.icabangid=a.icabangid_o
        LEFT JOIN dbmaster.t_brid AS i ON b1.nobrid = i.nobrid
        LEFT JOIN dbmaster.coa_level4 c1 ON c1.COA4 = b1.coa WHERE br.stsnonaktif <> 'Y' AND br.divisi='OTC' $fperiode $fkaryawan $fcabang ";
        
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DTBRRTNOTC01_".$_SESSION['IDCARD']."_$now ";
        
?>
    <center><h2><u>LAPORAN DETAIL CASH ADVANCE OTC</u></h2></center>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td><b>Periode </b></td><td>:</td><td><?PHP echo "$per1 s/d. $per2"; ?></td></tr>
                <tr><td><b>View Date </b></td><td>:</td><td><?PHP echo "$tglnow"; ?></td></tr>
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
                <th align="center">ID</th>
                <th align="center">Yang Mengajukan</th>
                <th align="center">Area</th>
                <th align="center">Periode</th>
                <th align="center">Akun</th>
                <th align="center">COA</th>
                <th align="center">Nama COA</th>
                <th align="center">Jumlah (Rp.)</th>
            </thead>
            <tbody>
                <?PHP
                    $query = "create temporary table $tmp01 ($query)";
                    mysqli_query($cnit, $query);
                    
                    $total=0;
                    $query = "select * from $tmp01 order by idca, nobrid";
                    $result = mysqli_query($cnit, $query);
                    $records = mysqli_num_rows($result);
                    $row = mysqli_fetch_array($result);
                    
                    if ($records) {
                        $reco = 1;
                        while ($reco <= $records) {
                            $noid=$row['idca'];
                            $nama=$row['nama'];
                            $area=$row['nama_area'];
                            $bulan=$row['periode'];
                            $brperiode = date("d F Y", strtotime($bulan));
                            $jumlah=number_format($row['jumlah'],0,",",",");
                            
                            $total = $total + $row['jumlah'];
                            
                            echo "<tr>";
                            echo "<td>$noid</td>";
                            echo "<td>$nama</td>";
                            echo "<td>$area</td>";
                            echo "<td>$brperiode</td>";
                            echo "<td colspan=4></td>";
                            echo "</tr>";
                            
                            while (($reco <= $records) AND $row['idca']==$noid) {
                                $akun=$row['nama_brid'];
                                $coa=$row['coa'];
                                $coanama=$row['nama_coa'];
                                $rptotal=number_format($row['rptotal'],0,",",",");
                                
                                
                                echo "<tr>";
                                echo "<td colspan=4></td>";
                                echo "<td>$akun</td>";
                                echo "<td>$coa</td>";
                                echo "<td>$coanama</td>";
                                echo "<td align='right'>$rptotal</td>";
                                echo "</tr>";
                                
                                $row = mysqli_fetch_array($result);
                                $reco++;    
                            }
                            //sub total ID
                            echo "<tr>";
                            echo "<td colspan=7 align='right'><b>Sub Total : </b></td>";
                            echo "<td align='right'><b>$jumlah</b></td>";
                            echo "</tr>";
                            
                        }
                        $total=number_format($total,0,",",",");
                        //TOTAL
                        echo "<tr>";
                        echo "<td colspan=7 align='right'><b>Total : </b></td>";
                        echo "<td align='right'><b>$total</b></td>";
                        echo "</tr>";
                    }
                    
                    
                    
                    mysqli_query($cnit, "drop temporary table $tmp01");
                ?>
            </tbody>
        </table>
    
</body>
</html>
