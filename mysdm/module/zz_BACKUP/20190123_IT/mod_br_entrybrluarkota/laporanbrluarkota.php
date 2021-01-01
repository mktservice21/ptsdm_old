<?php
    session_start();
    
    if (isset($_GET['iprint'])) {
        if ($_GET['iprint']=="print") {
            include "module/mod_br_brrutin/pritnbrrutin.php";
        }elseif ($_GET['iprint']=="lihatgambar") {
            include "module/mod_br_brrutin/lihatgambar.php";
        }elseif ($_GET['iprint']=="kunjungan") {
            include "module/mod_br_entrybrluarkota/kunjungan.php";
        }
        exit;
    }
    
    $printdate= date("d_m_Y");
    $jamnow=date("H_i_s");
    
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=Data Biaya Luar Kota $printdate $jamnow.xls");
    }
    
    $printdate= date("d/m/Y");
    $jamnow=date("H:i:s");
?>

<html>
    <head>
        <title>Data Biaya Luar Kota <?PHP echo $printdate." ".$jamnow; ?></title>
        <meta http-equiv="Expires" content="Mon, 01 Sep 2018 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        <link rel="shortcut icon" href="../../images/icon.ico" />
        <script>
            function printContent(el){
                var restorepage = document.body.innerHTML;
                var printcontent = document.getElementById(el).innerHTML;
                document.body.innerHTML = printcontent;
                window.print();
                document.body.innerHTML = restorepage;
            }
        </script>
        
        <script>
            var EventUtil = new Object;
            EventUtil.formatEvent = function (oEvent) {
                    return oEvent;
            }


            function goto2(pForm_,pPage_) {
               document.getElementById(pForm_).action = pPage_;
               document.getElementById(pForm_).submit();

            }
        </script>
        
        <style>
        @page 
        {
            /*size: auto;   /* auto is the current printer page size */
            /*margin: 0mm;  /* this affects the margin in the printer settings */
            margin-left: 7mm;  /* this affects the margin in the printer settings */
            margin-right: 7mm;  /* this affects the margin in the printer settings */
            margin-top: 5mm;  /* this affects the margin in the printer settings */
            margin-bottom: 5mm;  /* this affects the margin in the printer settings */
            size: landscape;
        }
        </style>
        
        <style>
            body {
                font-family: "Times New Roman", Times, serif;
                font-size: 13px;
                border: 0px solid #000;
            }
            table.example_2 {
                color: #000;
                font-family: Helvetica, Arial, sans-serif;
                width: 100%;
                border-collapse:
                collapse; border-spacing: 0;
                font-size: 11px;
                border: 1px solid #000;
            }

            table.example_2 td, table.example_2 th {
                border: 1px solid #000; /* No more visible border */
                height: 20px;
                transition: all 0.3s;  /* Simple transition for hover effect */
            }

            table.example_2 th {
                background: #DFDFDF;  /* Darken header a bit */
                font-weight: bold;
            }

            table.example_2 td {
                background: #FAFAFA;
            }

            /* Cells in even rows (2,4,6...) are one color */
            tr:nth-child(even) td { background: #F1F1F1; }

            /* Cells in odd rows (1,3,5...) are another (excludes header cells)  */
            tr:nth-child(odd) td { background: #FEFEFE; }

            tr td:hover.biasa { background: #666; color: #FFF; }
            tr td:hover.left { background: #ccccff; color: #000; }

            tr td.center1, td.center2 { text-align: center; }

            tr td:hover.center1 { background: #666; color: #FFF; text-align: center; }
            tr td:hover.center2 { background: #ccccff; color: #000; text-align: center; }
            /* Hover cell effect! */

            table {
                font-family: "Times New Roman", Times, serif;
                font-size: 11px;
            }
            table.tjudul {
                font-size: 13px;
                width: 97%;
            }


            #kotakjudul {
                border: 0px solid #000;
                width:100%;
                height: 1.3cm;
            }
            #isikiri {
                float   : left;
                width   : 49%;
                border-left: 0px solid #000;
            }
            #isikanan {
                text-align: right;
                float   : right;
                width   : 49%;
            }
            h2 {
                font-size: 17px;
            }
        </style>
    </head>

    <body>
        <?PHP
            include "config/koneksimysqli_it.php";
            include_once("config/common.php");
            
            $now=date("mdYhis");
            $tmp01 =" dbtemp.DTBRARLUKA01_$_SESSION[IDCARD]$now ";
            $tmp02 =" dbtemp.DTBRARLUKA02_$_SESSION[IDCARD]$now ";
            
            $tgl01=$_POST['e_periode01'];
            $tgl02=$_POST['e_periode02'];
            $periode1= date("Y-m", strtotime($tgl01));
            $periode2= date("Y-m", strtotime($tgl02));

            $sql = "SELECT idrutin, DATE_FORMAT(tgl,'%d %M %Y') as tgl, DATE_FORMAT(bulan,'%M %Y') as bulan, DATE_FORMAT(periode1,'%d/%m/%Y') as periode1, "
                    . " DATE_FORMAT(periode2,'%d/%m/%Y') as periode2, "
                    . " divisi, karyawanid, nama, areaid, nama_area, jumlah, keterangan, "
                    . " COA4, NAMA4 ";
            $sql.=" FROM dbmaster.v_brrutin0 ";
            $sql.=" WHERE kode=2 AND stsnonaktif <> 'Y' "; //kode = 2 BIAYA RUTIN
            $sql.=" AND Date_format(bulan, '%Y-%m') between '$periode1' and '$periode2' ";
            if (!empty($_POST['cb_divisi'])) $sql.=" and divisi='$_POST[cb_divisi]' ";
            if (!empty($_POST['e_idarea']))
                $sql.=" and icabangid='$_POST[e_idarea]' ";
            else{
                if ($_SESSION['ADMINKHUSUS']=="Y") {
                    //if (!empty($_SESSION['KHUSUSSEL'])) $sql .=" AND divisi in $_SESSION[KHUSUSSEL]";
                }
            }
            if ($_SESSION['LVLPOSISI']=="FF1" OR $_SESSION['LVLPOSISI']=="FF2" OR $_SESSION['LVLPOSISI']=="FF3" OR $_SESSION['LVLPOSISI']=="FF4" OR $_SESSION['LVLPOSISI']=="FF5" OR $_SESSION['LVLPOSISI']=="FF6") $sql .=" and karyawanid ='$_SESSION[IDCARD]' ";
            if ($_SESSION['JABATANID']==38) $sql .=" and karyawanid ='$_SESSION[IDCARD]' ";
            
            $sql = "create temporary table $tmp01 ($sql)";
            mysqli_query($cnit, $sql);
            
            $sql = "select * from dbmaster.v_brrutin1 where idrutin in (select distinct idrutin from $tmp01)";
            $sql = "create temporary table $tmp02 ($sql)";
            mysqli_query($cnit, $sql);
        ?>
        <div id="div1">
            <center><h2><u>DATA BIAYA LUAR KOTA</u></h2></center>
            
            <div id="kotakjudul">
                <div id="isikiri">
                    <table class='tjudul' width='100%'>
                        <tr><td align='left' width="100px">View Date</td><td align='left'><?PHP echo $printdate; ?></td></tr>
                    </table>
                </div>
                <div id="isikanan">
                    
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
            
            <table id='example_2' class='table table-striped table-bordered example_2' width="100%" border="1px">
                <tr style='background-color:#cccccc; font-size: 13px;'>
                <th align="center">No</th>
                <th align="center">Yang Membuat</th>
                <th align="center">No ID</th>
                <th align="center" colspan="2">Periode</th>
                <th align="center">Jumlah</th>
                <th align="center">Keterangan</th>
                </tr>
                <?PHP
                $total=0;
                $query = "select * from $tmp01 order by nama, bulan, nama_area";
                $result = mysqli_query($cnit, $query);
                $records = mysqli_num_rows($result);
                $row = mysqli_fetch_array($result);
                if ($records) {
                    $reco = 1;
                    while ($reco <= $records) {
                        $periode=$row['bulan'];
                        echo "<tr>";
                        echo "<td></td>";
                        echo "<td align='left' colspan=6><b><i>$periode</i></b></td>";
                        echo "</tr>";
                        $no=1;
                        $subtot=0;
                        while ( ($reco<=$records) and ($periode == $row['bulan'])) {
                            $idcab=$row['areaid'];
                            $nmcab=$row['nama_area'];
                            $nmkaryawan=$row['nama'];
                            if ($_GET['ket']=="excel")
                                $idrutin="'".$row['idrutin'];
                            else
                                $idrutin=$row['idrutin'];
                            
                            $idku=$row['idrutin'];
                            $divisi=$row['divisi'];
                            $jumlah=$row['jumlah'];
                            $ket=$row['keterangan'];
                            $bper1=$row['periode1'];
                            $bper2=$row['periode2'];
                            
                            $subtot = $subtot + $jumlah;
                                    
                            echo "<tr>";
                            echo "<td>$no</td>";
                            echo "<td><b>$nmkaryawan</b></td>";
                            echo "<td>$idrutin</td>";
                            echo "<td>$bper1</td>";
                            echo "<td>$bper2</td>";
                            echo "<td align='right'></td>";
                            echo "<td>$ket</td>";
                            echo "</tr>";
                            
                            $row = mysqli_fetch_array($result);
                            $reco++;
                            $no++;
                            
                            $no_id=1;
                            $query2 = "select * from $tmp02 where idrutin='$idku' order by nobrid";
                            $result2 = mysqli_query($cnit, $query2);
                            $records2 = mysqli_num_rows($result2);
                            $row2 = mysqli_fetch_array($result2);
                            if ($records2) {
                                $reco2 = 1;
                                while ($reco2 <= $records2) {
                                    $nobrid=$row2['nobrid'];
                                    $nmbrid=$row2['nama_brid'];
                                    $rptotal=number_format($row2['rptotal'],0);
                                    echo "<tr>";
                                    echo "<td></td>";
                                    echo "<td align='right' colspan=1>$no_id</td>";
                                    echo "<td align='left' colspan=3>$nmbrid</td>";
                                    echo "<td align='right'>$rptotal</td>";
                                    echo "<td align='left'></td>";
                                    echo "</tr>";
                                    $row2 = mysqli_fetch_array($result2);
                                    $reco2++;
                                    $no_id++;
                                }
                                
                                echo "<tr>";
                                echo "<td>$no</td>";
                                echo "<td align='right' colspan=4>Rp.</td>";
                                echo "<td align='right'><b>".number_format($jumlah,0)."</b></td>";
                                echo "<td>$ket</td>";
                                echo "</tr>";
                                
                            }
                            
                            
                        }
                        //SUB TOTAL
                        $total=$total+$subtot;
                        echo "<tr>";
                        echo "<td align='right' colspan='5'><b>Sub Total $periode : &nbsp; &nbsp;</b></td>";
                        echo "<td align='right'><b>".number_format($subtot,0)."</b></td>";
                        echo "<td></td>";
                        echo "</tr>";
                    }
                    //TOTAL
                    echo "<tr style='background-color:#ffcc99;'>";
                    echo "<td align='right' colspan='5'><b>TOTAL : &nbsp; &nbsp;</b></td>";
                    echo "<td align='right'><b>".number_format($total,0)."</b></td>";
                    echo "<td></td>";
                    echo "</tr>";
                    
                }
                
                ?>
            </table>
            
            <?PHP
            mysqli_query($cnit, "drop temporary table $tmp01");
            mysqli_query($cnit, "drop temporary table $tmp02");
            ?>
        </div>
    </body>
</html>