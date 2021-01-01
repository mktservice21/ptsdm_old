<?PHP
    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REKAP BIAYA MARKETING SBY.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>

<html>
<head>
    <title>REKAP BIAYA MARKETING SBY</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Apr 2019 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <link href="css/laporanbaru.css" rel="stylesheet">
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <?PHP } ?>
</head>

<body>
    <?PHP
        include "config/koneksimysqli.php";
        include "config/fungsi_combo.php";
        
        $now=date("mdYhis");
        $tmp01 =" dbtemp.RPTREKOTCF01_".$_SESSION['USERID']."_$now ";

        $tgl01=$_POST['bulan1'];
        $periode1= date("Y-m-01", strtotime($tgl01));
        $periode2= date("Y-m-t", strtotime($tgl01));
        $filtertgl = "AND DATE_FORMAT(a.TANGGAL,'%Y-%m-%d') BETWEEN '$periode1' AND '$periode2'";
        
        $pdivisi=$_POST['divprodid'];
        $pcoa=$_POST['cb_coa'];
        
        $fdivisi="";
        if (!empty($pdivisi)) $fdivisi=" AND a.DIVISI='$pdivisi' ";
        $fcoa="";
        if (!empty($pcoa)) $fcoa=" AND a.COA4='$pcoa' ";
        
        
        $query = "SELECT
            a.TANGGAL,
            a.NOBBM,
            a.NOBBK,
            a.DIVISI,
            a.COA4,
            a.KETERANGAN,
            b.NAMA4,
            SUM(a.DEBIT) DEBIT,
            SUM(a.KREDIT) KREDIT,
            SUM(a.SALDO) SALDO
            FROM
            dbmaster.t_bm_sby a
            LEFT JOIN dbmaster.coa_level4 b ON a.COA4 = b.COA4 
            WHERE IFNULL(a.stsnonaktif,'')<>'Y' $filtertgl $fdivisi $fcoa";
        $query .= "GROUP BY 1,2,3,4,5,6,7";
        $query = "create TEMPORARY table $tmp01 ($query)"; 
        mysqli_query($cnit, $query);
        
        
    echo "<table class='tjudul' width='100%'>";
    echo "<tr> <td width='300px'>Rekap Biaya Marketing SBY </td> <td> </td> <td></td> </tr>";
    echo "<tr> <td width='200px'> </td> <td> </td> <td></td> </tr>";
    echo "</table>";
    echo "<br/>&nbsp;";
                
    ?>
    
    <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
        <thead>
            <tr style='background-color:#cccccc; font-size: 13px;'>
            <th align="center">Tgl.</th>
            <th align="center">Bukti</th>
            <th align="center" colspan="2">COA</th>
            <th align="center">Keterangan</th>
            <th align="center">Debit</th>
            <th align="center">Kredit</th>
            <th align="center">Saldo</th>
        </thead>
        <tbody>
            <?PHP
            $no=1;
            $pjmldebit=0;
            $pjmlkredit=0;
            $pjmlsaldo=0;
            $query = "select * FROM $tmp01 order by DIVISI, TANGGAL, COA4";
            $tampil2=mysqli_query($cnit, $query);
            while ($row= mysqli_fetch_array($tampil2)) {
                $ptgl = "";
                if (!empty($row['TANGGAL']) AND $row['TANGGAL']<>"0000-00-00")
                    $ptgl =date("d-M-Y", strtotime($row['TANGGAL']));


                $pbbk = $row['NOBBK'];
                $pbbm = $row['NOBBM'];
                $pcoa = $row['COA4'];
                $pnmcoa = $row['NAMA4'];

                $pketerangan = $row['KETERANGAN'];

                $pdebit=$row['DEBIT'];
                $pkredit=$row['KREDIT'];
                $psaldo=$row['SALDO'];

                $pjmldebit=$pjmldebit+$pdebit;
                $pjmlkredit=$pjmlkredit+$pkredit;
                
                $pdebit=number_format($pdebit,0,",",",");
                $pkredit=number_format($pkredit,0,",",",");
                $psaldo=number_format($psaldo,0,",",",");



                echo "<tr>";
                echo "<td nowrap>$ptgl</td>";
                echo "<td nowrap>$pbbk</td>";
                echo "<td nowrap>$pcoa</td>";
                echo "<td nowrap>$pnmcoa</td>";
                echo "<td nowrap>$pketerangan</td>";
                echo "<td nowrap align='right'>$pdebit</td>";
                echo "<td nowrap align='right'>$pkredit</td>";
                echo "<td nowrap align='right'>$psaldo</td>";
                echo "</tr>";


                $no++;
            }
            $pjmlsaldo=$pjmldebit-$pjmlkredit;
            
            $pjmldebit=number_format($pjmldebit,0,",",",");
            $pjmlkredit=number_format($pjmlkredit,0,",",",");
            $pjmlsaldo=number_format($pjmlsaldo,0,",",",");
            
            
            echo "<tr>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap><b>TOTAL</b></td>";
            echo "<td nowrap align='right'><b>$pjmldebit</b></td>";
            echo "<td nowrap align='right'><b>$pjmlkredit</b></td>";
            echo "<td nowrap align='right'><b>$pjmlsaldo</b></td>";
            echo "</tr>";
            ?>
        </tbody>
    </table>
    
    <?PHP
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
        
        mysqli_close($cnit);
    ?>
</body>
</html>