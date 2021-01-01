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
    
    $pnodiv=$_GET['ispd'];
    
    $tglnow = date("d/m/Y");
    $tgl01 = date("Y-m-d");
    
    $gmrheight = "100px";
    $ngbr_idinput="";
    $gbrttd_fin1="";
    $gbrttd_fin2="";
    $gbrttd_dir1="";
    $gbrttd_dir2="";
    
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DBRROTCPBLL01_".$_SESSION['IDCARD']."_$now ";
    $tmp02 =" dbtemp.DBRROTCPBLL02_".$_SESSION['IDCARD']."_$now ";
    $tmp03 =" dbtemp.DBRROTCPBLL03_".$_SESSION['IDCARD']."_$now ";
    
    $query = "select a.*, b.tglf, b.tglt, b.divisi divisipd, b.nodivisi, b.nomor, b.tgl as tglpd, b.coa4 coa, c.NAMA4 coa_nama,
        b.jumlah jumlahpd, b.kodeid, d.nama kodenama, b.subkode, d.subnama, b.jenis_rpt  
        from dbmaster.t_suratdana_br1 a JOIN  dbmaster.t_suratdana_br b 
        ON a.idinput=b.idinput LEFT JOIN dbmaster.coa_level4 c on b.coa4=c.COA4 
        LEFT JOIN dbmaster.t_kode_spd d on b.kodeid=d.kodeid and b.subkode=d.subkode 
        WHERE a.idinput = '$pnodiv'";
    $query = "create temporary table $tmp03 ($query)"; 
    mysqli_query($cnit, $query);
    
        $query = "select tglf from $tmp03 where IFNULL(tglf,'')<>'' LIMIT 1";
        $tampil=mysqli_query($cnit, $query);
        $ketemu=mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $ks= mysqli_fetch_array($tampil);
            if (!empty($ks['tglf'])) $tgl01=$ks['tglf'];
        }
        
        $periode1 = date("Y-m", strtotime($tgl01));
        $per1 = date("F Y", strtotime($tgl01));
        $pbulan = date("F", strtotime($tgl01));
        
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
        LEFT JOIN MKT.iarea AS a ON br.areaid = a.areaId and br.icabangid=a.iCabangId WHERE br.kode=2 AND br.stsnonaktif <> 'Y' AND br.divisi='OTC' 
        AND br.idrutin IN (select IFNULL(bridinput,'') from $tmp03)";
    //echo $query;
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
    
        
?>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td width="150px"><b>PT SDM</b></td><td></td></tr>
                <tr><td width="200px"><b>Realisasi Luar Kota OTC Per </b></td><td><?PHP echo "$per1 "; ?></td></tr>
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
<?PHP
hapusdata:
    mysqli_query($cnit, "drop temporary table $tmp01");
    mysqli_query($cnit, "drop temporary table $tmp02");
    mysqli_query($cnit, "drop temporary table $tmp03");
    mysqli_close($cnit);
?>
</body>
</html>
