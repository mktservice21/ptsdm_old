<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REALISASI LUAR KOTA.xls");
    }
    
    include("config/koneksimysqli_it.php");
    include("config/common.php");
    
?>
<html>
<head>
    <title>REALISASI LUAR KOTA</title>
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
    
    $fperiode = " AND ( (DATE_FORMAT(br.periode1, '%Y-%m') = '$periode1') OR (DATE_FORMAT(br.periode2, '%Y-%m') = '$periode1') ) ";
    
    $per1 = date("F Y", strtotime($tgl01));
    $pbulan = date("F", strtotime($tgl01));
    
    $stsapv = $_POST['sts_apv'];
    $fstsapv = "";
    $e_stsapv="Semua Data";
    if ($stsapv == "fin") {
        $fstsapv = " AND ifnull(br.tgl_fin,'') <> '' AND ifnull(br.tgl_fin,'0000-00-00') <> '0000-00-00' ";
        $e_stsapv="Sudah Proses Finance";
    }elseif ($stsapv == "belumfin") {
        $fstsapv = " AND (ifnull(br.tgl_fin,'') = '' OR ifnull(br.tgl_fin,'0000-00-00') = '0000-00-00') ";
        $e_stsapv="Belum Proses Finance";
    }
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DBRROTCPBLL01_".$_SESSION['IDCARD']."_$now ";
    $tmp02 =" dbtemp.DBRROTCPBLL02_".$_SESSION['IDCARD']."_$now ";
    $tmp03 =" dbtemp.DBRROTCPBLL03_".$_SESSION['IDCARD']."_$now ";
    
    $query = "SELECT
	br.idrutin,
	br.tgl,
	br.karyawanid,
	br.icabangid,
	br.areaid,
	br.jumlah,
	br.keterangan,
	k.nama,
	a.nama nama_area,
        br.divisi
        FROM
                dbmaster.t_brrutin0 AS br
        LEFT JOIN hrd.karyawan AS k ON br.karyawanid = k.karyawanId
        LEFT JOIN MKT.iarea AS a ON br.areaid = a.areaId and br.icabangid=a.iCabangId WHERE br.kode=2 AND br.stsnonaktif <> 'Y' AND br.divisi <> 'OTC' $fperiode $fstsapv";
    
    $query = "create temporary table $tmp02 ($query)"; 
    mysqli_query($cnit, $query);
    
    $query = "select karyawanid, sum(jumlah) as AMOUNT, CAST(''  AS char(10)) icabangid, CAST(''  AS char(10)) areaid, CAST(''  AS char(10)) divisi from $tmp02 Group by karyawanid";
    $query = "create temporary table $tmp03 ($query)";
    mysqli_query($cnit, $query);
    
    $query = "UPDATE $tmp03 set icabangid=ifnull((select icabangid from $tmp02 where $tmp02.karyawanid=$tmp03.karyawanid LIMIT 1),0)";
    mysqli_query($cnit, $query);
    $query = "UPDATE $tmp03 set areaid=ifnull((select areaid from $tmp02 where $tmp02.karyawanid=$tmp03.karyawanid LIMIT 1),0)";
    mysqli_query($cnit, $query);
    $query = "UPDATE $tmp03 set divisi=ifnull((select divisi from $tmp02 where $tmp02.karyawanid=$tmp03.karyawanid LIMIT 1),0)";
    mysqli_query($cnit, $query);
    
    
    $query = "select distinct br.karyawanid, k.nama, br.areaid, a.nama nama_area, br.icabangid, c.nama nama_cabang, br.divisi 
        , AMOUNT from $tmp03 br JOIN hrd.karyawan k ON br.karyawanid=k.karyawanId 
        LEFT JOIN MKT.icabang c on br.icabangid=c.iCabangId LEFT JOIN MKT.iarea a on br.areaid=a.areaId And br.icabangid=a.iCabangId";
    $query = "create temporary table $tmp01 ($query)";
    mysqli_query($cnit, $query);
    
        
?>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td width="150px"><b>PT SDM</b></td><td></td></tr>
                <tr><td width="200px"><b>Realisasi Luar Kota Per </b></td><td><?PHP echo "$per1 "; ?></td></tr>
                <tr><td><b>Status Approve </b></td><td><?PHP echo "$e_stsapv"; ?></td></tr>
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
                <th align="center">Divisi</th>
                <th align="center">Biaya Luar Kota <?PHP echo "$pbulan "; ?></th>
            </thead>
            <tbody>
                <?PHP
                    $no=1;
                    $total=0;
                    $totalpot=0;
                    $totalpen=0;
                    $totalbay=0;
                    $query = "select * from $tmp01 order by divisi, nama, karyawanid";
                    $result = mysqli_query($cnit, $query);
                    $records = mysqli_num_rows($result);
                    $row = mysqli_fetch_array($result);
                    
                    if ($records) {
                        $reco = 1;
                        while ($reco <= $records) {
                            $noid=$row['karyawanid'];
                            $nama=$row['nama'];
                            $area=$row['nama_area'];
                            $pdivisi=$row['divisi'];
                            if ($pdivisi=="CAN") $pdivisi="CANARY";
                            $jumlah=number_format($row['AMOUNT'],0,",",",");
                            
                            $total = $total + $row['AMOUNT'];
                            
                            echo "<tr>";
                            echo "<td>$no</td>";
                            echo "<td nowrap>$nama</td>";
                            echo "<td nowrap>$pdivisi</td>";
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
                ?>
            </tbody>
        </table>
        <br/>&nbsp;<br/>&nbsp;
        <?PHP
        echo "<table width='100%' border='0px' >";
        echo "<tr align='center'>";
        echo "<td>Yang membuat,</td><td colspan=2></td><td>Menyetujui,</td>";
        echo "</tr>";

        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";

        echo "<tr align='center'>";
        echo "<td>(..................)</td><td></td><td></td><td>(dr. Farida Soewanto)</td>";
        echo "</tr>";
        echo "</table>";
        ?>
        <br/>&nbsp;<br/>&nbsp;
</body>
</html>
