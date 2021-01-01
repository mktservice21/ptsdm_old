<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=LAPORAN CASH ADVANCE.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>
<html>
<head>
    <title>LAPORAN CASH ADVANCE</title>
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
    
    
    $fdivisi = "";
    $fcabang = "";
    $fstsapv = "";
    
    $fperiode = " AND DATE_FORMAT(br.periode, '%Y%m') between '$periode1' AND '$periode1' ";
    if (!empty($edivisi) AND ($edivisi <> "*")) $fdivisi = " AND br.divisi='$edivisi' ";
    
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
	br.idca,
	br.karyawanid,
	br.periode,
	br.jumlah,
	br.keterangan,
	br.divisi,
	br.tgltrans,
	br.jmltrans,
	k.nama,
	a.nama nama_area_o,
	aa.Nama nama_area,
	c.nama nama_cabang,
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
        LEFT JOIN MKT.icabang AS c ON c.iCabangId=br.icabangid
        LEFT JOIN MKT.iarea_o AS a ON br.areaid_o = a.areaid_o and br.icabangid_o=a.icabangid_o
        LEFT JOIN MKT.iarea AS aa ON br.areaid = aa.areaId and br.icabangid=aa.iCabangId
        LEFT JOIN dbmaster.t_brid AS i ON b1.nobrid = i.nobrid
        LEFT JOIN dbmaster.coa_level4 c1 ON c1.COA4 = b1.coa WHERE br.stsnonaktif <> 'Y' $fperiode $fdivisi $fstsapv ";
        
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DTBRRTNOTC01_".$_SESSION['IDCARD']."_$now ";
        
?>
    <center><h2><u>LAPORAN DETAIL CASH ADVANCE <?PHP echo $edivisi; ?></u></h2></center>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td><b>Periode </b></td><td>:</td><td><?PHP echo "$per1"; ?></td></tr>
                <tr><td><b>Status Approve </b></td><td>:</td><td><?PHP echo "$e_stsapv"; ?></td></tr>
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
                <!--<th align="center">Jumlah (Rp.)</th>-->
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
                    $query = "select * from $tmp01 order by periode, nama, idca, nobrid";
                    $result = mysqli_query($cnit, $query);
                    $records = mysqli_num_rows($result);
                    $row = mysqli_fetch_array($result);
                    
                    if ($records) {
                        $reco = 1;
                        $lclewat=false;
                        while ($reco <= $records) {
                            $noid=$row['idca'];
                            $nama=$row['nama'];
                            if (trim($row['divisi'])=="OTC"){
                                $area=$row['nama_area_o'];
                                $nmcabang=$row['nama_cabang_o'];
                            }else{
                                $area=$row['nama_area'];
                                $nmcabang=$row['nama_cabang'];
                            }
                            $bulan=$row['periode'];
                            $brperiode = date("F Y", strtotime($bulan));
                            $jumlah=number_format($row['jumlah'],0,",",",");
                            
                            $total = $total + $row['jumlah'];
                            
                            echo "<tr>";
                            echo "<td>$noid</td>";
                            echo "<td>$nama</td>";
                            //echo "<td>$nmcabang</td>";
                            //echo "<td align='right'><b>$jumlah</b></td>";
                            
                            if ($lclewat==true) {
                                echo "<td colspan=2></td>";
                                echo "</tr>";
                            }
                            
                            while (($reco <= $records) AND $row['idca']==$noid) {
                                $akun=$row['nama_brid'];
                                $coa=$row['coa'];
                                $coanama=$row['nama_coa'];
                                $rptotal=number_format($row['rptotal'],0,",",",");
                                
                                if ($lclewat==true) {
                                    echo "<tr>";
                                    echo "<td colspan=2></td>";
                                }
                                echo "<td>$akun</td>";
                                echo "<td>$coa</td>";
                                echo "<td>$coanama</td>";
                                echo "<td align='right'>$rptotal</td>";
                                echo "</tr>";
                                
                                $lclewat=true;
                                $row = mysqli_fetch_array($result);
                                $reco++;    
                            }
                            $lclewat=false;
                            
                            //sub total ID
                            echo "<tr>";
                            echo "<td colspan=5 align='right'><b>Sub Total : </b></td>";
                            echo "<td align='right'><b>$jumlah</b></td>";
                            echo "</tr>";
                            
                        }
                        $total=number_format($total,0,",",",");
                        //TOTAL
                        echo "<tr>";
                        echo "<td colspan=5 align='right'><b>Total : </b></td>";
                        echo "<td align='right'><b>$total</b></td>";
                        echo "</tr>";
                    }
                    
                    
                    
                    mysqli_query($cnit, "drop temporary table $tmp01");
                ?>
            </tbody>
        </table>
    
</body>
</html>
