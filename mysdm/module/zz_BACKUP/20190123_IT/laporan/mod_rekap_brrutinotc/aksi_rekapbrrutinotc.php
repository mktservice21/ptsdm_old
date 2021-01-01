<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REKAP BIAYA RUTIN OTC.xls");
    }
    
    include("config/koneksimysqli_it.php");
    include("config/common.php");
    
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
        , CAST(''  AS char(100)) KET from hrd.karyawan k JOIN dbmaster.t_karyawan_posisi b on k.karyawanId=b.karyawanId
        LEFT JOIN MKT.iarea_o o on b.areaId=o.areaid_o and b.icabangid=o.icabangid_o
        WHERE k.karyawanId not in (select DISTINCT karyawanId from dbmaster.t_karyawanadmin) and b.divisiId='OTC' and b.aktif='Y'";
    $query = "create temporary table $tmp01 ($query)";
    mysqli_query($cnit, $query);
    
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
	a.nama nama_area
        FROM
                dbmaster.t_brrutin0 AS br
        LEFT JOIN hrd.karyawan AS k ON br.karyawanid = k.karyawanId
        LEFT JOIN MKT.iarea AS a ON br.areaid = a.areaId and br.icabangid=a.iCabangId 
        LEFT JOIN dbmaster.t_brrutin2 pem on br.idrutin=pem.idrutin 
        WHERE br.kode=1 AND br.stsnonaktif <> 'Y' AND br.divisi='OTC' $fperiode $fkdperiode";
    
    
    $query = "create temporary table $tmp02 ($query)"; 
    mysqli_query($cnit, $query);
    
    $query = "UPDATE $tmp01 set AMOUNT=ifnull((select sum(jumlah) from  $tmp02 where $tmp02.karyawanid=$tmp01.karyawanId),0)";
    mysqli_query($cnit, $query);
    $query = "UPDATE $tmp01 set PENAMBAHAN=ifnull((select sum(penambahan) from  $tmp02 where $tmp02.karyawanid=$tmp01.karyawanId),0)";
    mysqli_query($cnit, $query);
    $query = "UPDATE $tmp01 set nama_area=ifnull((select o.nama from MKT.iarea_o o where o.icabangid_o=$tmp01.icabangid AND o.areaid_o=$tmp01.areaid),'')";
    mysqli_query($cnit, $query);
    
    $query = "UPDATE $tmp01 set BAYAR=ifnull(AMOUNT,0)-ifnull(POTONGAN,0)+ifnull(PENAMBAHAN,0)";
    mysqli_query($cnit, $query);
    
    //mysqli_query($cnit, "drop temporary table $tmp01");
    //mysqli_query($cnit, "drop temporary table $tmp02");
    //exit;
        
?>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td width="150px"><b>PT SDM</b></td><td></td></tr>
                <tr><td width="150px"><b>Rekap Biaya Rutin Per </b></td><td><?PHP echo "$per1 "; ?></td></tr>
                <tr><td width="150px"><b>Periode </b></td><td><?PHP echo "$kdperiode"; ?></td></tr>
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
                    
                    
                    
                    mysqli_query($cnit, "drop temporary table $tmp01");
                    mysqli_query($cnit, "drop temporary table $tmp02");
                ?>
            </tbody>
        </table>
        <br/>&nbsp;<br/>&nbsp;
        <?PHP
        echo "<table width='100%' border='0px' style='border : 0px solid #fff; font-size: 11px; font-weight: bold;'>";
        echo "<tr align='center'>";
        echo "<td>Yang membuat,</td><td colspan=2></td><td>Menyetujui,</td>";
        echo "</tr>";

        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";

        echo "<tr align='center'>";
        echo "<td>(Saiful Rahmat)</td><td></td><td></td><td>(dr. Farida Soewanto)</td>";
        echo "</tr>";
        echo "</table>";
        ?>
        <br/>&nbsp;<br/>&nbsp;
</body>
</html>
