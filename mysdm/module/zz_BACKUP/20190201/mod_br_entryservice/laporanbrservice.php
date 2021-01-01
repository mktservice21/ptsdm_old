<?php
    session_start();
    
    if (isset($_GET['iprint'])) {
        if ($_GET['iprint']=="print") {
            include "pritbrservice.php";
        }elseif ($_GET['iprint']=="lihatgambar") {
            include "module/mod_br_entryservice/lihatgambar.php";
        }
        exit;
    }
    
    $printdate= date("d_m_Y");
    $jamnow=date("H_i_s");
    
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=Data Service Kendaraan $printdate $jamnow.xls");
    }
    
    $printdate= date("d/m/Y");
    $jamnow=date("H:i:s");
?>

<html>
    <head>
        <title>Data Cash Advance <?PHP echo $printdate." ".$jamnow; ?></title>
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
            include "config/koneksimysqli.php";
            include_once("config/common.php");
            
            $now=date("mdYhis");
            $tmp01 =" dbtemp.DTBRARSK01_$_SESSION[IDCARD]$now ";
            
            $tgl01=$_POST['e_periode01'];
            $tgl02=$_POST['e_periode02'];
            $periode1= date("Y-m", strtotime($tgl01));
            $periode2= date("Y-m", strtotime($tgl02));
            
            $sql = "SELECT
                br.idservice,
                br.kode,
                br.nobrid,
                DATE_FORMAT(br.tgl,'%d %M %Y') tgl,
                DATE_FORMAT(br.tglservice,'%Y-%m') as per,
                DATE_FORMAT(br.tglservice,'%M %Y') as periode,
                br.divisi,
                br.karyawanid,
                br.icabangid,
                br.areaid,
                br.nopol,
                br.tglservice,
                br.km,
                br.jumlah,
                br.ppn,
                br.keterangan,
                br.KODEWILAYAH,
                br.COA4,
                br.stsnonaktif,
                br.userid,
                br.sysnow,
                k.nama,
                e.merk,
                e.tipe,
                e.tglbeli,
                a.Nama nama_area,
                c1.NAMA4,
                br.jabatanid,
                br.atasan1,
                br.atasan2,
                br.atasan3,
                br.atasan4,
                br.tgl_atasan1,
                br.tgl_atasan2,
                br.tgl_atasan3,
                br.tgl_atasan4,
                br.gbr_atasan1,
                br.gbr_atasan2,
                br.gbr_atasan3,
                br.gbr_atasan4,
                br.validate,
                br.validate_date,
                br.gambar,
                br.fin,
                br.tgl_fin,
                br.gbr_fin
                FROM
                        dbmaster.t_service_kendaraan br
                LEFT JOIN hrd.karyawan k ON br.karyawanid = k.karyawanId
                LEFT JOIN MKT.iarea AS a ON br.areaid = a.areaId and br.icabangid=a.iCabangId
                LEFT JOIN dbmaster.t_kendaraan e ON e.nopol = br.nopol
                LEFT JOIN dbmaster.coa_level4 c1 ON c1.COA4 = br.COA4";
            
            $sqlx = "SELECT idservice, DATE_FORMAT(tgl,'%d %M %Y') as tgl, DATE_FORMAT(tglservice,'%Y-%m') as per, DATE_FORMAT(tglservice,'%M %Y') as periode, "
                    . " km, nopol, merk, tipe, divisi, karyawanid, nama, areaid, nama_area, jumlah, keterangan ";
            $sqlx.=" FROM dbmaster.v_service_kendaraan ";
            
            $sql.=" WHERE br.stsnonaktif <> 'Y' "; //kode = 2 BIAYA RUTIN
            $sql.=" AND Date_format(br.tglservice, '%Y-%m') between '$periode1' and '$periode2' ";
            if (!empty($_POST['cb_divisi'])) $sql.=" and br.divisi='$_POST[cb_divisi]' ";
            if (!empty($_POST['e_idarea']))
                $sql.=" and br.icabangid='$_POST[e_idarea]' ";
            else{
                if ($_SESSION['ADMINKHUSUS']=="Y") {
                    //if (!empty($_SESSION['KHUSUSSEL'])) $sql .=" AND br.divisi in $_SESSION[KHUSUSSEL]";
                }
            }
            //echo $sql; exit;
            $sql = "create temporary table $tmp01 ($sql)";
            mysqli_query($cnmy, $sql);
        ?>
        <div id="div1">
            <center><h2><u>DATA SERVICE KENDARAAN</u></h2></center>
            
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
                <th align="center">COA</th>
                <th align="center">Nama COA</th>
                <th align="center">No. Polisi</th>
                <th align="center">Merk</th>
                <th align="center">Jumlah</th>
                <th align="center">Keterangan</th>
                </tr>
                <?PHP
                $total=0;
                $query = "select * from $tmp01 order by per, periode, divisi, nama, nama_area";
                $result = mysqli_query($cnmy, $query);
                $records = mysqli_num_rows($result);
                $row = mysqli_fetch_array($result);
                if ($records) {
                    $reco = 1;
                    while ($reco <= $records) {
                        $periode=$row['periode'];
                        echo "<tr>";
                        echo "<td></td>";
                        echo "<td align='left' colspan=8><b><i>$periode</i></b></td>";
                        echo "</tr>";
                        $subtotbln=0;
                        
                        while ( ($reco<=$records) and ($periode == $row['periode'])) {
                            $divisi=$row['divisi'];
                            echo "<tr>";
                            echo "<td></td>";
                            echo "<td align='left' colspan=8><b><i>$divisi</i></b></td>";
                            echo "</tr>";
                            
                            $no=1;
                            $subtot=0;
                            while ( ($reco<=$records) and ($periode == $row['periode']) and ($divisi == $row['divisi'])) {
                                $idbrb=$row['areaid'];
                                $nmcab=$row['nama_area'];
                                $nmkaryawan=$row['nama'];
                                if ($_GET['ket']=="excel")
                                    $idbr="'".$row['idservice'];
                                else
                                    $idbr=$row['idservice'];

                                $idku=$row['idservice'];
                                $coa=$row['COA4'];
                                $namacoa=$row['NAMA4'];
                                $idku=$row['idservice'];
                                $divisi=$row['divisi'];
                                $jumlah=$row['jumlah'];
                                $ket=$row['keterangan'];
                                $km=$row['km'];
                                $nopol=$row['nopol'];
                                $merk=$row['merk'];
                                $tipe=$row['tipe'];

                                $subtot = $subtot + $jumlah;
                                $subtotbln = $subtotbln + $jumlah;

                                $nmnncab="";
                                if (!empty($nmcab))
                                    $nmnncab = "(". $nmcab .")";

                                echo "<tr>";
                                echo "<td>$no</td>";
                                echo "<td>$nmkaryawan $nmnncab</td>";
                                echo "<td>$idbr</td>";
                                echo "<td>$coa</td>";
                                echo "<td>$namacoa</td>";
                                echo "<td>$nopol</td>";
                                echo "<td>$merk $tipe</td>";
                                echo "<td align='right'>".number_format($jumlah,0)."</td>";
                                echo "<td>$ket</td>";
                                echo "</tr>";
                                $row = mysqli_fetch_array($result);
                                $reco++;
                                $no++;


                            }
                            
                            //SUB TOTAL DIVISI
                            echo "<tr>";
                            echo "<td align='right' colspan='7'><b>Sub Total $divisi - $periode : &nbsp; &nbsp;</b></td>";
                            echo "<td align='right'><b>".number_format($subtot,0)."</b></td>";
                            echo "<td></td>";
                            echo "</tr>";    
                            
                        }
                        
                        
                        
                        //SUB TOTAL
                        $total=$total+$subtotbln;
                        echo "<tr>";
                        echo "<td align='right' colspan='7'><b>Sub Total $periode : &nbsp; &nbsp;</b></td>";
                        echo "<td align='right'><b>".number_format($subtotbln,0)."</b></td>";
                        echo "<td></td>";
                        echo "</tr>";
                    }
                    //TOTAL
                    echo "<tr style='background-color:#ffcc99;'>";
                    echo "<td align='right' colspan='7'><b>TOTAL : &nbsp; &nbsp;</b></td>";
                    echo "<td align='right'><b>".number_format($total,0)."</b></td>";
                    echo "<td></td>";
                    echo "</tr>";
                    
                }
                
                ?>
            </table>
            
            <?PHP
            mysqli_query($cnmy, "drop temporary table $tmp01");
            ?>
        </div>
    </body>
</html>