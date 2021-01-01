<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REKAP CASH ADVANCE.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>
<html>
<head>
    <title>REKAP CASH ADVANCE</title>
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
    $tglini = date("d F Y");
    $tgl01 = $_POST['bulan1'];
    $periode1 = date("Y-m", strtotime($tgl01));
    $fperiode = " AND date_format(br.periode,'%Y-%m') ='$periode1' ";
    $fperiode2 = " AND ( (DATE_FORMAT(br.periode1, '%Y-%m') = '$periode1') OR (DATE_FORMAT(br.periode2, '%Y-%m') = '$periode1') ) ";
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
    
    $periodeygdipilih = date("Y-m-01", strtotime($tgl01));
    $bulanberikutnya = date('Y-m-d', strtotime("+1 months", strtotime($periodeygdipilih)));
    $pbulanberikutnya = date("F", strtotime($bulanberikutnya));
    $periodeberikutnya = date("Y-m", strtotime($bulanberikutnya));
    $fperiodeberikutnya = " AND date_format(br.periode,'%Y-%m') ='$periodeberikutnya' ";
    
    $stsreport = $_POST['sts_rpt'];
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DTBRRETCPCA01_".$_SESSION['IDCARD']."_$now ";
    $tmp02 =" dbtemp.DTBRRETCPCA02_".$_SESSION['IDCARD']."_$now ";
    $tmp03 =" dbtemp.DTBRRETCPCA03_".$_SESSION['IDCARD']."_$now ";
    $tmp04 =" dbtemp.DTBRRETCPCA04_".$_SESSION['IDCARD']."_$now ";
    $tmp05 =" dbtemp.DTBRRETCPCA05_".$_SESSION['IDCARD']."_$now ";
    
    
    $query = "SELECT
	br.idca,
	br.periode,
	br.karyawanid, b.nama, 
	br.icabangid,
	br.areaid,
	br.jumlah,
	br.keterangan, br.divisi
        FROM dbmaster.t_ca0 AS br JOIN hrd.karyawan b on br.karyawanid=b.karyawanId WHERE br.jenis_ca='lk' AND br.stsnonaktif <> 'Y' AND br.divisi <> 'OTC' $fperiodeberikutnya $fstsapv";
    
    $query = "create temporary table $tmp05 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $query = "SELECT
	br.idca,
	br.periode,
	br.karyawanid,
	br.icabangid,
	br.areaid,
	br.jumlah,
	br.keterangan, br.divisi
        FROM dbmaster.t_ca0 AS br WHERE br.jenis_ca='lk' AND br.stsnonaktif <> 'Y' AND br.divisi <> 'OTC' $fperiode $fstsapv";
    
    $query = "create temporary table $tmp02 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "SELECT
	br.idrutin,
	br.bulan,
	br.karyawanid,
	br.icabangid,
	br.areaid,
	br.jumlah,
	br.keterangan, br.divisi
        FROM dbmaster.t_brrutin0 AS br WHERE br.kode=2 AND br.stsnonaktif <> 'Y' AND br.divisi <> 'OTC' $fperiode2 $fstsapv";
    
    $query = "create temporary table $tmp03 ($query)"; 
    mysqli_query($cnit, $query);
    
    
    if ($stsreport=="B") {
        mysqli_query($cnit, "DELETE FROM $tmp05 WHERE idca IN (select distinct ifnull(idca2,'') FROM dbmaster.t_brrutin_ca_close)");
        mysqli_query($cnit, "DELETE FROM $tmp02 WHERE idca IN (select distinct ifnull(idca1,'') FROM dbmaster.t_brrutin_ca_close)");
        mysqli_query($cnit, "DELETE FROM $tmp03 WHERE idrutin IN (select distinct ifnull(idrutin,'') FROM dbmaster.t_brrutin_ca_close)");
    }elseif ($stsreport=="S") {
        mysqli_query($cnit, "DELETE FROM $tmp05 WHERE idca NOT IN (select distinct ifnull(idca2,'') FROM dbmaster.t_brrutin_ca_close)");
        mysqli_query($cnit, "DELETE FROM $tmp02 WHERE idca NOT IN (select distinct ifnull(idca1,'') FROM dbmaster.t_brrutin_ca_close)");
        mysqli_query($cnit, "DELETE FROM $tmp03 WHERE idrutin NOT IN (select distinct ifnull(idrutin,'') FROM dbmaster.t_brrutin_ca_close)");
    }
    
    $query = "select bulan from dbmaster.t_brrutin_ca_close where date_format(bulan,'%Y-%m')='$periode1'";
    $ketemucls= mysqli_num_rows(mysqli_query($cnit, $query));
    if ($ketemucls>0){
        mysqli_query($cnit, "UPDATE $tmp05 set jumlah=0 WHERE idca not in (select IFNULL(idca2,'') from dbmaster.t_brrutin_ca_close)");
        mysqli_query($cnit, "UPDATE $tmp02 set jumlah=0 WHERE idca not in (select IFNULL(idca1,'') from dbmaster.t_brrutin_ca_close)");
    }
    
    
    $query = "create table $tmp04 (karyawanid VARCHAR(10), icabangid VARCHAR(10), areaid VARCHAR(10), divisi VARCHAR(5))"; 
    mysqli_query($cnit, $query);
    
    //$query = "select distinct karyawanid, icabangid, areaid, divisi from $tmp02";
    //$query = "create temporary table $tmp04 ($query)"; 
    //mysqli_query($cnit, $query);
    
    $query = "INSERT INTO $tmp04 (karyawanid) select distinct karyawanid from $tmp02"; 
    mysqli_query($cnit, $query);
    
    $query = "INSERT INTO $tmp04 (karyawanid) select distinct karyawanid from $tmp03 where karyawanid not in "
            . "(select distinct ifnull(karyawanid,'') from $tmp04)"; 
    mysqli_query($cnit, $query);
    
    $query = "INSERT INTO $tmp04 (karyawanid) select distinct karyawanid from $tmp05 where karyawanid not in "
            . "(select distinct ifnull(karyawanid,'') from $tmp04)"; 
    mysqli_query($cnit, $query);
    
    $query = "UPDATE $tmp04 set divisi=ifnull((select divisi from $tmp02  where $tmp04.karyawanid=$tmp02.karyawanid LIMIT 1),'')";
    mysqli_query($cnit, $query);
    $query = "UPDATE $tmp04 set areaid=ifnull((select areaid from $tmp02  where $tmp04.karyawanid=$tmp02.karyawanid LIMIT 1),'')";
    mysqli_query($cnit, $query);
    $query = "UPDATE $tmp04 set icabangid=ifnull((select icabangid from $tmp02  where $tmp04.karyawanid=$tmp02.karyawanid LIMIT 1),'')";
    mysqli_query($cnit, $query);
    
    $query = "UPDATE $tmp04 set divisi=ifnull((select divisi from $tmp03  where $tmp04.karyawanid=$tmp03.karyawanid LIMIT 1),'') WHERE ifnull(divisi,'')=''";
    mysqli_query($cnit, $query);
    $query = "UPDATE $tmp04 set areaid=ifnull((select areaid from $tmp03  where $tmp04.karyawanid=$tmp03.karyawanid LIMIT 1),'') WHERE ifnull(areaid,'')=''";
    mysqli_query($cnit, $query);
    $query = "UPDATE $tmp04 set icabangid=ifnull((select icabangid from $tmp03  where $tmp04.karyawanid=$tmp03.karyawanid LIMIT 1),'') WHERE ifnull(icabangid,'')=''";
    mysqli_query($cnit, $query);
    
    $query = "UPDATE $tmp04 set divisi=ifnull((select divisi from $tmp05  where $tmp04.karyawanid=$tmp05.karyawanid LIMIT 1),'') WHERE ifnull(divisi,'')=''";
    mysqli_query($cnit, $query);
    $query = "UPDATE $tmp04 set areaid=ifnull((select areaid from $tmp05  where $tmp04.karyawanid=$tmp05.karyawanid LIMIT 1),'') WHERE ifnull(areaid,'')=''";
    mysqli_query($cnit, $query);
    $query = "UPDATE $tmp04 set icabangid=ifnull((select icabangid from $tmp05  where $tmp04.karyawanid=$tmp05.karyawanid LIMIT 1),'') WHERE ifnull(icabangid,'')=''";
    mysqli_query($cnit, $query);
    
    $query = "select distinct br.karyawanId karyawanid, k.nama, br.areaid, a.nama nama_area, br.icabangid, c.nama nama_cabang, br.divisi 
        , CAST(0  AS DECIMAL(30,2)) CA1 
        , CAST(0  AS DECIMAL(30,2)) LK1, CAST(0  AS DECIMAL(30,2)) SALDO, CAST(0  AS DECIMAL(30,2)) LK2, CAST(0  AS DECIMAL(30,2)) CA2
        , CAST(0  AS DECIMAL(30,2)) CAKIRIM 
        , CAST(''  AS char(100)) KET from $tmp04 br JOIN hrd.karyawan k ON br.karyawanid=k.karyawanId 
        LEFT JOIN MKT.icabang c on br.icabangid=c.iCabangId LEFT JOIN MKT.iarea a on br.areaid=a.areaId And br.icabangid=a.iCabangId";
    $query = "create  table $tmp01 ($query)";
    mysqli_query($cnit, $query);
    
    $query = "INSERT INTO $tmp01 (karyawanid, nama, areaid, icabangid, divisi)"
            . " select karyawanid, nama, areaid, icabangid, divisi FROM $tmp05 WHERE "
            . " karyawanid not in (select distinct karyawanid from $tmp04)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp01 set CA1=ifnull((select sum(jumlah) from  $tmp02 where $tmp02.karyawanid=$tmp01.karyawanId),0)";
    mysqli_query($cnit, $query);
    
    $query = "UPDATE $tmp01 set LK1=ifnull((select sum(jumlah) from  $tmp03 where $tmp03.karyawanid=$tmp01.karyawanId),0)";
    mysqli_query($cnit, $query);
    
    $query = "UPDATE $tmp01 set SALDO=ifnull(CA1-LK1,0), LK2=ifnull(CA1-LK1,0)";
    mysqli_query($cnit, $query);
    
    $query = "UPDATE $tmp01 set CA2=ifnull((select sum(jumlah) from  $tmp05 where $tmp05.karyawanid=$tmp01.karyawanId),0)";
    mysqli_query($cnit, $query);
    
    
    //venti
    if ($periode1=="2019-01") {
        $query="INSERT INTO $tmp01 (karyawanid, nama, areaid, nama_area, icabangid, nama_cabang, divisi, CA2)
            select karyawanid, nama, areaid, nama nama_area, icabangid, nama nama_cabang,
            divisi, jumlah from dbmaster.v_ca0_mydata WHERE idca ='CA00000333'";
        //mysqli_query($cnit, $query);
        
        //mysqli_query($cnit, "UPDATE $tmp01 SET CA1='740000' WHERE karyawanid='0000001755'");
    }
    $query="INSERT INTO $tmp01 (karyawanid, nama, areaid, nama_area, icabangid, nama_cabang, divisi, CA2)
        select karyawanid, nama, areaid, nama nama_area, icabangid, nama nama_cabang,
        divisi, jumlah from dbmaster.v_ca0_mydata WHERE idca 
        in (select distinct idca2 from dbmaster.t_brrutin_ca_close where ifnull(tglinput,'')='')";
    //mysqli_query($cnit, $query);
    
    
    //mysqli_query($cnit, "UPDATE $tmp01 set CAKIRIM=NULL");
    
    $query = "UPDATE $tmp01 set CAKIRIM=ifnull(LK2,0)+ifnull(CA2,0)";
    //mysqli_query($cnit, $query);
    
    $query = "UPDATE $tmp01 set CAKIRIM=ifnull(CA2,0)+ifnull(LK2,0) ";
    //mysqli_query($cnit, $query);
    
    
    //mysqli_query($cnit, "drop temporary table $tmp01");
    //mysqli_query($cnit, "drop temporary table $tmp02");
    //exit;
        
?>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td width="150px"><b>PT SDM</b></td><td></td></tr>
                <tr><td width="180px"><b>Rekap Cash Advance </b></td><td><?PHP echo ""; ?></td></tr>
                <tr><td><b>Status Approve </b></td><td><?PHP echo "$e_stsapv"; ?></td></tr>
                <tr><td width="150px"><b>Tanggal </b></td><td><?PHP echo "$tglini"; ?></td></tr>
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
                <th align="center">CA <?PHP echo $pbulan; ?> yg harus dipertgjwbkan</th>
                <th align="center">Biaya Luar Kota <?PHP echo $pbulan; ?></th>
                <th align="center">Saldo <?PHP echo $pbulan; ?></th>
                <th align="center"></th>
                <th align="center">Kelebihan Biaya LK Bulan <?PHP echo $pbulan; ?></th>
                <th align="center">Permintaan CA Bulan <?PHP echo $pbulanberikutnya; ?></th>
                <th align="center">CA yang dikirim</th>
            </thead>
            <tbody>
                <?PHP
                    $totjumlahtrsf=0;
                    $no=1;
                    $total=0;
                    $totlk1=0;
                    $totalsaldo=0;
                    $totlk2=0;
                    $totca2=0;
                    $totlkirim=0;
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
                            $jumlahca1=number_format($row['CA1'],0,",",",");
                            $jumlahlk1=number_format($row['LK1'],0,",",",");
                            
                            $psaldo=number_format($row['SALDO'],0,",",",");
                            $plk2=number_format($row['LK2'],0,",",",");
                            
                            $pca2=number_format($row['CA2'],0,",",",");
                            
                            
                            $pcakirim=number_format($row['CAKIRIM'],0,",",",");
                            
                            $total = $total + $row['CA1'];
                            $totlk1 = $totlk1 + $row['LK1'];
                            $totalsaldo = $totalsaldo + $row['SALDO'];
                            $totlk2 = $totlk2 + $row['LK2'];
                            $totca2 = $totca2 + $row['CA2'];
                            $totlkirim = $totlkirim + $row['CAKIRIM'];
                            
                            $pselisih=$row['LK2'];
                            $pjumlahtrans=$row['CA2']-$pselisih;

                            if ($pselisih>0 AND $row['CA2']==0) $pjumlahtrans=0;
                            elseif ($pselisih>0 AND $row['CA2']>0) $pjumlahtrans=$row['CA2'];
                            elseif ($pselisih==0 AND $row['CA2']>0) $pjumlahtrans=$row['CA2'];
                            
                                    if (empty($pjumlahtrans)) $pjumlahtrans=0;
                                    $totjumlahtrsf=$totjumlahtrsf+$pjumlahtrans;
                                    $pjumlahtrans=number_format($pjumlahtrans,0,",",",");
                                    
                            echo "<tr>";
                            echo "<td>$no</td>";
                            echo "<td nowrap>$nama</td>";
                            echo "<td>$pdivisi</td>";
                            echo "<td align='right'>$jumlahca1</td>";
                            echo "<td align='right'>$jumlahlk1</td>";
                            echo "<td align='right'>$psaldo</td>";
                            echo "<td align='right'>&nbsp; &nbsp; &nbsp; &nbsp; </td>";
                            echo "<td align='right'>$plk2</td>";
                            echo "<td align='right'>$pca2</td>";
                            echo "<td align='right'>$pjumlahtrans</td>";
                            echo "</tr>";
                            
                            $no++;
                            $row = mysqli_fetch_array($result);
                            $reco++;  
                            
                        }
                        $total=number_format($total,0,",",",");
                        $totlk1=number_format($totlk1,0,",",",");
                        $totalsaldo=number_format($totalsaldo,0,",",",");
                        $totlk2=number_format($totlk2,0,",",",");
                        $totca2=number_format($totca2,0,",",",");
                        $totlkirim=number_format($totlkirim,0,",",",");
                        
                        $totjumlahtrsf=number_format($totjumlahtrsf,0,",",",");
                        
                        //TOTAL
                        echo "<tr>";
                        echo "<td colspan=3 align='right'><b>Total : </b></td>";
                        echo "<td align='right'><b>$total</b></td>";
                        echo "<td align='right'><b>$totlk1</b></td>";
                        echo "<td align='right'><b>$totalsaldo</b></td>";
                        echo "<td align='right'><b>&nbsp; &nbsp; </b></td>";
                        echo "<td align='right'><b>$totlk2</b></td>";
                        echo "<td align='right'><b>$totca2</b></td>";
                        echo "<td align='right'><b>$totjumlahtrsf</b></td>";
                        echo "</tr>";
                    }
                    
                    
                    
                    mysqli_query($cnit, "drop  table $tmp01");
                    mysqli_query($cnit, "drop temporary table $tmp02");
                    mysqli_query($cnit, "drop temporary table $tmp03");
                    mysqli_query($cnit, "drop table $tmp04");
                    mysqli_query($cnit, "drop temporary table $tmp05");
                ?>
            </tbody>
        </table>
        
        <br/>&nbsp;<br/>&nbsp;
</body>
</html>
