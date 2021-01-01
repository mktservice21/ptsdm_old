<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REALISASI LUAR KOTA.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
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
    
    
    $gmrheight = "100px";
    $ngbr_idinput="";
    $gbrttd_fin1="";
    $gbrttd_fin2="";
    $gbrttd_dir1="";
    $gbrttd_dir2="";
    
    $namapengaju_ttd_fin1="";
    $namapengaju_ttd_fin2="";
    
    $namapengaju_ttd1="";
    $namapengaju_ttd2="";
    
    
    $pnomorbr="";
    if (isset($_GET['ispd'])) {
        $idinputspd=$_GET['ispd'];
        $_POST['bulan1']="2000-01-00";
        $_POST['sts_rpt']="";
        
        $query = "select * from dbmaster.t_suratdana_br WHERE idinput='$idinputspd'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $ra= mysqli_fetch_array($tampil);
            if (!empty($ra['tglf']))
                $_POST['bulan1']=$ra['tglf'];
            
            $_POST['sts_rpt']=$ra['sts'];
            
            $pnomorbr= $ra['nodivisi'];
            
            $ngbr_idinput=$ra['idinput'];
            
            $gbrttd_fin1=$ra['gbr_apv1'];
            $gbrttd_fin2=$ra['gbr_apv2'];
            
            $gbrttd_dir1=$ra['gbr_dir'];
            $gbrttd_dir2=$ra['gbr_dir2'];
            
            if (!empty($gbrttd_fin1)) {
                $data="data:".$gbrttd_fin1;
                $data=str_replace(' ','+',$data);
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);
                $namapengaju_ttd_fin1="imgfin1_".$ngbr_idinput."TTDSPD_.png";
                file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd_fin1, $data);
            }
            
            if (!empty($gbrttd_fin2)) {
                $data="data:".$gbrttd_fin2;
                $data=str_replace(' ','+',$data);
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);
                $namapengaju_ttd_fin2="imgfin2_".$ngbr_idinput."TTDSPD_.png";
                file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd_fin2, $data);
            }
            
            if (!empty($gbrttd_dir1)) {
                $data="data:".$gbrttd_dir1;
                $data=str_replace(' ','+',$data);
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);
                $namapengaju_ttd1="imgdr1_".$ngbr_idinput."TTDSPD_.png";
                file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd1, $data);
            }
            
            if (!empty($gbrttd_dir2)) {
                $data="data:".$gbrttd_dir2;
                $data=str_replace(' ','+',$data);
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);
                $namapengaju_ttd2="imgdr2_".$ngbr_idinput."TTDSPD_.png";
                file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd2, $data);
            }
            
            
            
        }
    }
    
    
    
    $date1=$_POST['bulan1'];
    $tgl01 = $_POST['bulan1'];
    $stsreport = $_POST['sts_rpt'];
    
    
    $tgl1= date("Y-m-01", strtotime($date1));
    $bulan= date("Ym", strtotime($date1));
    
    $_SESSION['CLSLKCA']=date("F Y", strtotime($date1));
    
    
    $tglnow = date("d/m/Y");
    
    $periode1 = date("Y-m", strtotime($tgl01));
    $per1 = date("F Y", strtotime($tgl01));
    
    
    
    
    $tglini = date("d F Y");
    $pbulan = date("F", strtotime($tgl01));
    $periodeygdipilih = date("Y-m-01", strtotime($tgl01));
    $bulanberikutnya = date('Y-m-d', strtotime("+1 months", strtotime($periodeygdipilih)));
    $pbulanberikutnya = date("F", strtotime($bulanberikutnya));
    
    
    include ("module/mod_br_closing_lkca/seleksi_data_lk_ca.php");
    
    $query ="select distinct divisi, karyawanid, nama_karyawan nama, '' as nama_area, saldo AMOUNT from $tmp01";
    $query = "create Temporary table $tmp02 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $query = "DELETE FROM $tmp02 WHERE IFNULL(AMOUNT,0)=0"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    if (empty($pnomorbr)) {
        $nnnodiv="";
        if (isset($_POST['e_nomordiv'])) $nnnodiv=$_POST['e_nomordiv'];
        $tglinput = date("Y-m-01", strtotime($tgl01));
        //cari no br / no divisi yang sudah di save
        $pkode="2";
        $psubkode="21";
        $query = "SELECT nodivisi as pnomor FROM dbmaster.t_suratdana_br WHERE stsnonaktif<>'Y' AND divisi<>'OTC' AND nodivisi='$nnnodiv'";
        /*
        $query = "SELECT nodivisi as pnomor "
                . " FROM dbmaster.t_suratdana_br WHERE stsnonaktif<>'Y' AND divisi<>'OTC' AND kodeid='$pkode' AND subkode='$psubkode' AND "
                . " tgl='$tglinput' LIMIT 1";
         * 
         */
        $showkan= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($showkan);
        if ($ketemu>0){
            $s= mysqli_fetch_array($showkan);
            if (!empty($s['pnomor'])) { 
                $pnomorbr= $s['pnomor'];
            }
        }
    }
    //end cari no br / no divisi yang sudah di save
        
?>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td width="150px"><b>PT SDM</b></td><td></td></tr>
                <tr><td width="200px"><b>Realisasi Luar Kota Per </b></td><td><?PHP echo "$per1 "; ?></td></tr>
                <tr><td width="150px"><b>No.BR </b></td><td><?PHP echo "$pnomorbr"; ?></td></tr>
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
                            $jumlah=number_format($row['AMOUNT'],0,",",",");
                            
                            $total = $total + $row['AMOUNT'];
                            
                            echo "<tr>";
                            echo "<td align='center'>$no</td>";
                            echo "<td nowrap style='padding-left:5px;'>$nama</td>";
                            echo "<td nowrap align='center'>$pdivisi</td>";
                            echo "<td align='right' style='padding-right:5px;'>$jumlah</td>";
                            echo "</tr>";
                            
                            $no++;
                            $row = mysqli_fetch_array($result);
                            $reco++;  
                            
                        }
                        $total=number_format($total,0,",",",");
                        //TOTAL
                        echo "<tr>";
                        echo "<td colspan=3 align='right'><b>Total : </b></td>";
                        echo "<td align='right' style='padding-right:5px;'><b>$total</b></td>";
                        echo "</tr>";
                    }
                    
                    
                    
                    mysqli_query($cnit, "drop temporary table $tmp01");
                    mysqli_query($cnit, "drop temporary table $tmp02");
                    mysqli_query($cnit, "drop TEMPORARY table $tmp08");
                    mysqli_close($cnit);
                ?>
            </tbody>
        </table>
        <br/>&nbsp;<br/>&nbsp;
        <?PHP
        
        
        echo "<table class='tjudul' width='100%'>";
            echo "<tr>";

                echo "<td align='center'>";
                echo "Yang Membuat,";
                if (!empty($namapengaju_ttd_fin1))
                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin1' height='$gmrheight'><br/>";
                else
                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                echo "<b>MARSISTO SUSIWATI</b></td>";


                echo "<td align='center'>";
                echo "Checker,";
                if (!empty($namapengaju_ttd_fin2))
                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin2' height='$gmrheight'><br/>";
                else
                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                echo "<b>MARIANNE PRASANTI</b></td>";


                echo "<td align='center'>";
                echo "Mengetahui,";
                if (!empty($namapengaju_ttd1))
                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd1' height='$gmrheight'><br/>";
                else
                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                echo "<b>FARIDA SOEWANTO</b></td>";

                /*
                echo "<td align='center'>";
                echo "Disetujui,";
                if (!empty($namapengaju_ttd2))
                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd2' height='$gmrheight'><br/>";
                else
                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                echo "<b>IRA BUDISUSETYO</b></td>";
                */
            echo "</tr>";

        echo "</table>";
        
        /*
        echo "<table width='100%' border='0px' >";
        echo "<tr align='center'>";
        echo "<td>Yang membuat,</td> <td></td> <td>Checker</td> <td></td> <td>Menyetujui,</td>";
        echo "</tr>";

        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";

        echo "<tr align='center'>";
        echo "<td>(..................)</td> <td></td> <td>(Marianne Prasanti)</td> <td></td> <td>(dr. Farida Soewanto)</td>";
        echo "</tr>";
        
        echo "</table>";
         * 
         */
        ?>
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
