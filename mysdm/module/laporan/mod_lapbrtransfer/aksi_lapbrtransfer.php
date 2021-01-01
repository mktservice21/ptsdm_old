<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=TRNASFER BIAYA RUTIN OTC.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>
<html>
<head>
    <title>TRANSFER BIAYA RUTIN OTC</title>
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
    $fperiode = " AND date_format(a.bulan,'%Y-%m') ='$periode1' ";
    $per1 = date("F Y", strtotime($tgl01));
    
    $tgl02= date('Y-m-d', strtotime('-1 month', strtotime($tgl01)));
    $periode2 = date("Y-m", strtotime($tgl02));
    $per2 = date("F Y", strtotime($tgl02));
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DTBRROTCTRF01_".$_SESSION['IDCARD']."_$now ";
	$tmp02 =" dbtemp.DTBRROTCTRF02_".$_SESSION['IDCARD']."_$now ";
    
    $query="select a.idrutin, a.karyawanid, k.nama nama_karyawan, b.b_norek, nama_karyawan as nmasli, SUM(a.jumlah) jumlah from dbmaster.t_brrutin0 a 
        JOIN hrd.karyawan k on a.karyawanid=k.karyawanId
        LEFT JOIN dbmaster.t_karyawan_posisi b on a.karyawanid=b.karyawanId 
        WHERE a.kode=1 AND a.stsnonaktif<>'Y' AND a.divisi='OTC' $fperiode";
    $query .=" GROUP BY 1,2,3,4,5";
    
    $query = "create temporary table $tmp02 ($query)"; 
    mysqli_query($cnit, $query);
	
	
    $query = "UPDATE $tmp02 SET nama_karyawan=nmasli, karyawanid=idrutin WHERE karyawanid='0000002200'"; 
    mysqli_query($cnit, $query);
	
	
    $query="select karyawanid, nama_karyawan, b_norek, SUM(jumlah) jumlah from $tmp02 ";
    $query .=" GROUP BY 1,2,3";
    
    $query = "create temporary table $tmp01 ($query)"; 
    mysqli_query($cnit, $query);
	
	
	
        
?>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td width="150px"><b>PT SDM</b></td><td></td></tr>
                <tr><td width="180px"><b>Transfer Biaya Rutin OTC Per </b></td><td><?PHP echo "$per1 "; ?></td></tr>
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
                <th align="center">EMPLOYEE NAME</th>
                <th align="center">ACCOUNTNO</th>
                <th align="center">TRANSFER AMOUNT</th>
            </thead>
            <tbody>
                <?PHP
                    $no=1;
                    $totalrp=0;
                    
                    $query = "select * from $tmp01 order by nama_karyawan, karyawanid";
                    $result = mysqli_query($cnit, $query);
                    $records = mysqli_num_rows($result);
                    $row = mysqli_fetch_array($result);
                    
                    if ($records) {
                        $reco = 1;
                        while ($reco <= $records) {
                            $pkaryawanid=$row['karyawanid'];
                            $pnmkaryawan=$row['nama_karyawan'];
                            while (($reco <= $records) AND $row['karyawanid']==$pkaryawanid) {
                                
                                $nmkaryawan=$row['nama_karyawan'];
                                $idkaryawan=$row['karyawanid'];
                                $norek=$row['b_norek'];
                                
                                if ($_GET['ket']=="excel")
                                     $norek="'".$row['b_norek'];
                                
                                $pjumlah=number_format($row['jumlah'],0,",",",");
                                $totalrp =$totalrp+$row['jumlah'];
                                
                                echo "<tr>";
                                echo "<td nowrap>$nmkaryawan</td>";
                                echo "<td nowrap>$norek</td>";
                                echo "<td nowrap align='right'>$pjumlah</td>";
                                echo "</tr>";

                                $no++;
                                $row = mysqli_fetch_array($result);
                                $reco++;
                            }
                                
                        }
                        $totalrp=number_format($totalrp,0,",",",");
                        echo "<tr>";
                        //echo "<td colspan='2' align='right'> TOTAL : &nbsp;</td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td nowrap align='right'><b>$totalrp</b></td>";
                        echo "</tr>";
                    }
                    
                    
                    
                    mysqli_query($cnit, "drop temporary table $tmp01");
					mysqli_query($cnit, "drop temporary table $tmp02");
                ?>
            </tbody>
        </table>

        <br/>&nbsp;<br/>&nbsp;
</body>
</html>
