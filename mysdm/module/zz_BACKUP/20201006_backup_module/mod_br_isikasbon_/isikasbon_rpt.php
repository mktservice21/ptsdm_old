<?PHP
    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REKAP KAS BON.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>

<html>
<head>
    <title>Rekap Kas Bon</title>
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
        $ptgl1=$_POST['e_periode01'];
        $ptgl2=$_POST['e_periode02'];
        $pperiode1 =date("Y-m-d", strtotime($ptgl1));
        $pperiode2 =date("Y-m-d", strtotime($ptgl2));
        
        $pbln1 =date("d F Y", strtotime($ptgl1));
        $pbln2 =date("d F Y", strtotime($ptgl2));
        
        $now=date("mdYhis");
        
        $tmp01 =" dbtemp.RPTREKOTCF01_".$_SESSION['USERID']."_$now ";
        
        $query = "select * FROM dbmaster.t_kasbon WHERE IFNULL(stsnonaktif,'')<>'Y' AND tgl BETWEEN '$pperiode1' AND '$pperiode2'";
        $query = "create TEMPORARY table $tmp01 ($query)"; 
        mysqli_query($cnit, $query);
                
        echo "<table class='tjudul' width='100%'>";
        echo "<tr> <td width='300px' colspan='3'>Data Kas Bon</td></tr>";
        echo "<tr> <td width='300px' colspan='3'>Periode $pbln1 s/d. $pbln2</td></tr>";
        echo "</table>";
                
        echo "<br./>&nbsp;<br./>&nbsp;";
    ?>
    
        <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                <th align="center">NO.</th>
                <th align="center">NAMA</th>
                <th align="center">KETERANGAN</th>
                <th align="center">JUMLAH ( Rp. )</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $ptotjml=0;
                $no=1;
                $query = "select * FROM $tmp01 order by nama, tgl";
                $tampil2=mysqli_query($cnit, $query);
                while ($row= mysqli_fetch_array($tampil2)) {
                    $ptgl =date("d-M-Y", strtotime($row['tgl']));

                    $pnama = $row['nama'];
                    $pketerangan = $row['keterangan'];

                    $pjumlah=$row['jumlah'];

                    $ptotjml=$ptotjml+$pjumlah;
                    $pjumlah=number_format($pjumlah,0,",",",");
                    


                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                        echo "<td nowrap>$pnama</td>";
                    echo "<td nowrap>$pketerangan</td>";
                    echo "<td nowrap align='right'>$pjumlah</td>";
                    echo "</tr>";

                    $no++;
                }
                $ptotjml=number_format($ptotjml,0,",",",");
                echo "<tr>";
                echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                echo "<td nowrap align='right'><b>Grand Total</b>&nbsp; &nbsp;</td>";
                echo "<td nowrap align='right'><b>$ptotjml</b></td>";
                echo "</tr>";
                ?>
            </tbody>
        </table>
    <?PHP
        echo "<br./>&nbsp;<br./>&nbsp;";
    hapusdata:
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
        
        mysqli_close($cnit);
    ?>
</body>
</html>