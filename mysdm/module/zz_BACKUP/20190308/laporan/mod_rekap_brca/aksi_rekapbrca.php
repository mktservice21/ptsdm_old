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
    <style>
        #myBtn {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 30px;
            z-index: 99;
            font-size: 18px;
            border: none;
            outline: none;
            background-color: red;
            color: white;
            cursor: pointer;
            padding: 15px;
            border-radius: 4px;
            opacity: 0.5;
        }

        #myBtn:hover {
            background-color: #555;
        }
    </style>
    
</head>

<body>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?php

    $cnit=$cnmy;
    $date1=$_POST['bulan1'];
    $tgl1= date("Y-m-01", strtotime($date1));
    $bulan= date("Ym", strtotime($date1));
    
    $_SESSION['CLSLKCA']=date("F Y", strtotime($date1));
    
    
    $tglnow = date("d/m/Y");
    $tgl01 = $_POST['bulan1'];
    $periode1 = date("Y-m", strtotime($tgl01));
    $per1 = date("F Y", strtotime($tgl01));
    
    $stsreport = $_POST['sts_rpt'];
    
    
    $tglini = date("d F Y");
    $pbulan = date("F", strtotime($tgl01));
    $periodeygdipilih = date("Y-m-01", strtotime($tgl01));
    $bulanberikutnya = date('Y-m-d', strtotime("+1 months", strtotime($periodeygdipilih)));
    $pbulanberikutnya = date("F", strtotime($bulanberikutnya));
    
    
    include ("module/mod_br_closing_lkca/seleksi_data_lk_ca.php");
    
    $query ="select distinct divisi, karyawanid, nama_karyawan nama, '' as nama_area, ca1 CA1, saldo LK1, "
            . " IFNULL(ca1,0)-IFNULL(saldo,0) SALDO, ca2 CA2, IFNULL(ca1,0)-IFNULL(saldo,0) LK2, saldo CAKIRIM from $tmp01";
    $query = "create Temporary table $tmp02 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
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
                    $query = "select * from $tmp02 order by divisi, nama, karyawanid";
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
                    
                    
                    
                    mysqli_query($cnit, "drop temporary table $tmp02");
                    mysqli_query($cnit, "drop TEMPORARY table $tmp01");
                    mysqli_query($cnit, "drop TEMPORARY table $tmp08");
                ?>
            </tbody>
        </table>
        
        <br/>&nbsp;<br/>&nbsp;
        <br/>&nbsp;<br/>&nbsp;
        
    <script>
        // When the user scrolls down 20px from the top of the document, show the button
        window.onscroll = function() {scrollFunction()};
        function scrollFunction() {
          if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            document.getElementById("myBtn").style.display = "block";
          } else {
            document.getElementById("myBtn").style.display = "none";
          }
        }

        // When the user clicks on the button, scroll to the top of the document
        function topFunction() {
          document.body.scrollTop = 0;
          document.documentElement.scrollTop = 0;
        }
    </script>
</body>
</html>
