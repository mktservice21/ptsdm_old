<?php
date_default_timezone_set("Asia/Jakarta");

session_start();

    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG...!!!";
        exit;
    }
    include "config/koneksimysqli.php";

    $pnamalengkapprint=$_SESSION['NAMALENGKAP'];
    $tgl_print = date("d/m/Y h:i:s");
    
    
    $userid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TMPANTRI01_".$userid."_$now ";
    $tmp02 =" dbtemp.TMPANTRI02_".$userid."_$now ";
    $tmp03 =" dbtemp.TMPANTRI03_".$userid."_$now ";
    
    $ptypepilih=$_POST['cb_rtptype'];
    $ptgl=$_POST['cbtgl'];
    $pbln=$_POST['bulan1'];
    
    $ptgl1=date("Y-m-01", strtotime($ptgl));
    $ptgl2=date("Y-m-t", strtotime($ptgl));
    
    $pbulan= date("Ym", strtotime($ptgl));
    $pnmbulan= date("F Y", strtotime($ptgl));
    if ($ptypepilih=="B") {
        $pbulan= date("Ym", strtotime($pbln));
        $pnmbulan= date("F Y", strtotime($pbln));
        
        $ptgl1=date("Y-m-01", strtotime($pbln));
        $ptgl2=date("Y-m-t", strtotime($pbln));
    }
    
    
    $pjmlbatasca=0;
    $pjmlbatasbc=0;
    $pjmlbatasnb=0;
    $pjmlbatasva=0;
    $pjmlbataspy=0;
    $pjmlbatastg=0;

    $query = "select status_trf, jumlah as jmlbts from dbmaster.t_br_batas_trf GROUP BY 1";
    $ntampil= mysqli_query($cnmy, $query);
    $nketemu= mysqli_num_rows($ntampil);
    if ($nketemu>0) {
        while ($nrx= mysqli_fetch_array($ntampil)) {
            $pststrf=$nrx['status_trf'];
            if ($pststrf=="CA") $pjmlbatasca=$nrx['jmlbts'];
            if ($pststrf=="BC") $pjmlbatasbc=$nrx['jmlbts'];
            if ($pststrf=="NB") $pjmlbatasnb=$nrx['jmlbts'];
            if ($pststrf=="VA") $pjmlbatasva=$nrx['jmlbts'];
            if ($pststrf=="PY") $pjmlbataspy=$nrx['jmlbts'];
            if ($pststrf=="TG") $pjmlbatastg=$nrx['jmlbts'];
        }
    }
        
    $query ="select * from dbmaster.t_br_antrian WHERE IFNULL(stsnonaktif,'')<>'Y' and DATE_FORMAT(tanggal,'%Y%m')='$pbulan'";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "select tanggal, CAST('' as CHAR(2)) as sts_trf, CAST(0 as DECIMAL(20,2)) as jmlbatas1, "
            . " CAST(0 as DECIMAL(20,2)) as jumlah1, CAST(0 as DECIMAL(20,2)) as jmlsisa1, "
            . " CAST(0 as DECIMAL(20,2)) as jmlbatas2, CAST(0 as DECIMAL(20,2)) as jumlah2, CAST(0 as DECIMAL(20,2)) as jmlsisa2, "
            . " CAST(0 as DECIMAL(20,2)) as jmlbatas3, CAST(0 as DECIMAL(20,2)) as jumlah3, CAST(0 as DECIMAL(20,2)) as jmlsisa3, "
            . " CAST(0 as DECIMAL(20,2)) as jmlbatas4, CAST(0 as DECIMAL(20,2)) as jumlah4, CAST(0 as DECIMAL(20,2)) as jmlsisa4, "
            . " CAST(0 as DECIMAL(20,2)) as jmlbatas5, CAST(0 as DECIMAL(20,2)) as jumlah5, CAST(0 as DECIMAL(20,2)) as jmlsisa5, "
            . " CAST(0 as DECIMAL(20,2)) as jmlbatas6, CAST(0 as DECIMAL(20,2)) as jumlah6, CAST(0 as DECIMAL(20,2)) as jmlsisa6 "
            . " from $tmp01 WHERE tanggal=''";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    mysqli_query($cnmy, "DELETE FROM $tmp02");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    unset($pinsert_data_detail);//kosongkan array
    while (strtotime($ptgl1) <= strtotime($ptgl2)) {
        
        $pinsert_data_detail[] = "('$ptgl1', '$pjmlbatasca', '$pjmlbatasbc', '$pjmlbatasnb', '$pjmlbatasva', '$pjmlbataspy', '$pjmlbatastg')";
        
        $ptgl1 = date ("Y-m-d", strtotime("+1 days", strtotime($ptgl1)));
    }
    
    $query_detail="INSERT INTO $tmp02 (tanggal, jmlbatas1, jmlbatas2, jmlbatas3, jmlbatas4, jmlbatas5, jmlbatas6) VALUES ".implode(', ', $pinsert_data_detail);
    mysqli_query($cnmy, $query_detail); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

    $query = "UPDATE $tmp02 a JOIN (select tanggal, SUM(jumlah) jumlah from $tmp01 WHERE status_trf='CA' GROUP BY 1) b ON a.tanggal=b.tanggal SET a.jumlah1=b.jumlah";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

    $query = "UPDATE $tmp02 a JOIN (select tanggal, SUM(jumlah) jumlah from $tmp01 WHERE status_trf='BC' GROUP BY 1) b ON a.tanggal=b.tanggal SET a.jumlah2=b.jumlah";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp02 a JOIN (select tanggal, SUM(jumlah) jumlah from $tmp01 WHERE status_trf='NB' GROUP BY 1) b ON a.tanggal=b.tanggal SET a.jumlah3=b.jumlah";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp02 a JOIN (select tanggal, SUM(jumlah) jumlah from $tmp01 WHERE status_trf='VA' GROUP BY 1) b ON a.tanggal=b.tanggal SET a.jumlah4=b.jumlah";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp02 a JOIN (select tanggal, SUM(jumlah) jumlah from $tmp01 WHERE status_trf='PY' GROUP BY 1) b ON a.tanggal=b.tanggal SET a.jumlah5=b.jumlah";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp02 a JOIN (select tanggal, SUM(jumlah) jumlah from $tmp01 WHERE status_trf='TG' GROUP BY 1) b ON a.tanggal=b.tanggal SET a.jumlah6=b.jumlah";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp02 SET jmlsisa1=IFNULL(jmlbatas1,0)-IFNULL(jumlah1,0), jmlsisa2=IFNULL(jmlbatas2,0)-IFNULL(jumlah2,0), "
            . " jmlsisa3=IFNULL(jmlbatas3,0)-IFNULL(jumlah3,0), jmlsisa4=IFNULL(jmlbatas4,0)-IFNULL(jumlah4,0), "
            . " jmlsisa5=IFNULL(jmlbatas5,0)-IFNULL(jumlah5,0), jmlsisa6=IFNULL(jmlbatas6,0)-IFNULL(jumlah6,0)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
?>
<HTML>
<HEAD>
    <TITLE>LIST DATA ANTRIAN</TITLE>
    <meta http-equiv="Expires" content="Mon, 01 Sep 2050 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <link rel="shortcut icon" href="images/icon.ico" />
    
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
            size: portrait;
        }
        </style>
        
        <style>
            body {
                font-family: "Times New Roman", Times, serif;
                font-size: 13px;
                border: 0px solid #000;
            }
            h2 {
                font-size: 15px;
            }
            h3 {
                font-size: 20px;
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
                height: 28px;
                transition: all 0.3s;  /* Simple transition for hover effect */
                padding: 5px;
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
            
            tr th {
                border-bottom:1px solid black;
            }
            table {
                font-family: "Times New Roman", Times, serif;
                font-size: 13px;
            }
            table.tjudul {
                font-size: 13px;
                width: 97%;
            }
            
            #container {
                width:100%;
                text-align:center;
            }

            #left {
                float:left;
                width:40%;
            }
            #right {
                float:right;
                width:40%;
            }
            .clear { clear: both; }
        </style>
    
    
</HEAD>


<BODY>
    
    <div id="container">
        
        <div id="left">
            <table>
                <tr><td>List Data Sisa Antrian BR <b><?PHP echo $pnmbulan; ?></b></td></tr>
            </table>
        </div>
        
        <div id="right">
            <table>
                <tr><td></td></tr>
            </table>
        </div>
        
    </div>
    <div class="clear"></div>
    
    <div align="">
        <table>
            <tr><td nowrap><?PHP echo "<i>print by : $pnamalengkapprint $tgl_print</i>"; ?></td></tr>
        </table>
    </div>
    
    
    <hr/>
    
    <table id='datatablestockopn' class='table table-striped table-bordered' width='100%'>
        <thead>
            <tr>
                <th align="center">Tgl.</th>
                <th align="center" colspan="3">Tunai/Cash</th>
                <th align="center" colspan="3">BCA</th>
                <th align="center" colspan="3">Non BCA</th>
                <th align="center" colspan="3">Virtual Account</th>
                <th align="center" colspan="3">Payroll</th>
                <th align="center" colspan="3">Tagihan</th>
            </tr>
            <tr>
                <th align="center"></th>
                <th align="center">Batas</th>
                <th align="center">Jumlah</th>
                <th align="center">Sisa</th>
                
                <th align="center">Batas</th>
                <th align="center">Jumlah</th>
                <th align="center">Sisa</th>
                
                <th align="center">Batas</th>
                <th align="center">Jumlah</th>
                <th align="center">Sisa</th>
                <th align="center">Batas</th>
                
                <th align="center">Jumlah</th>
                <th align="center">Sisa</th>
                
                <th align="center">Batas</th>
                <th align="center">Jumlah</th>
                <th align="center">Sisa</th>
                
                <th align="center">Batas</th>
                <th align="center">Jumlah</th>
                <th align="center">Sisa</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $no=1;
            $query = "select * from $tmp02 ORDER BY tanggal";
            $tampil1= mysqli_query($cnmy, $query);
            while ($row= mysqli_fetch_array($tampil1)) {
                $ptangga=$row['tanggal'];
                $pjmlbatas1=$row['jmlbatas1'];
                $pjml1=$row['jumlah1'];
                $pjmlsisa1=$row['jmlsisa1'];
                
                $pjmlbatas2=$row['jmlbatas2'];
                $pjml2=$row['jumlah2'];
                $pjmlsisa2=$row['jmlsisa2'];
                
                $pjmlbatas3=$row['jmlbatas3'];
                $pjml3=$row['jumlah3'];
                $pjmlsisa3=$row['jmlsisa3'];
                
                $pjmlbatas4=$row['jmlbatas4'];
                $pjml4=$row['jumlah4'];
                $pjmlsisa4=$row['jmlsisa4'];
                
                $pjmlbatas5=$row['jmlbatas5'];
                $pjml5=$row['jumlah5'];
                $pjmlsisa5=$row['jmlsisa5'];
                
                $pjmlbatas6=$row['jmlbatas6'];
                $pjml6=$row['jumlah6'];
                $pjmlsisa6=$row['jmlsisa6'];
                
                $pnmhari= strtoupper(date("l", strtotime($ptangga)));
                $ptangga= date("d", strtotime($ptangga));
                
                $pjmlbatas1=number_format($pjmlbatas1,0);
                $pjml1=number_format($pjml1,0);
                $pjmlsisa1=number_format($pjmlsisa1,0);
                
                $pjmlbatas2=number_format($pjmlbatas2,0);
                $pjml2=number_format($pjml2,0);
                $pjmlsisa2=number_format($pjmlsisa2,0);
                
                $pjmlbatas3=number_format($pjmlbatas3,0);
                $pjml3=number_format($pjml3,0);
                $pjmlsisa3=number_format($pjmlsisa3,0);
                
                $pjmlbatas4=number_format($pjmlbatas4,0);
                $pjml4=number_format($pjml4,0);
                $pjmlsisa4=number_format($pjmlsisa4,0);
                
                $pjmlbatas5=number_format($pjmlbatas5,0);
                $pjml5=number_format($pjml5,0);
                $pjmlsisa5=number_format($pjmlsisa5,0);
                
                $pjmlbatas6=number_format($pjmlbatas6,0);
                $pjml6=number_format($pjml6,0);
                $pjmlsisa6=number_format($pjmlsisa6,0);
                
                
                $pwarna="";
                if ($pnmhari=="SATURDAY" OR $pnmhari=="SUNDAY") {
                    $pwarna="style='color:red;'";
                    /*
                    $pjmlbatas1="";
                    $pjml1="";
                    $pjmlsisa1="";
                    $pjmlbatas2="";
                    $pjml2="";
                    $pjmlsisa2="";
                    
                    $pjmlbatas3="";
                    $pjml3="";
                    $pjmlsisa3="";
                    
                    $pjmlbatas4="";
                    $pjml4="";
                    $pjmlsisa4="";
                    
                    $pjmlbatas5="";
                    $pjml5="";
                    $pjmlsisa5="";
                    
                    $pjmlbatas6="";
                    $pjml6="";
                    $pjmlsisa6="";
                     * 
                     */
                }
                
                echo "<tr $pwarna>";
                echo "<td nowrap style='border-left:1px solid black;border-right:1px solid black;'>$ptangga</td>";
                echo "<td nowrap align='right'>$pjmlbatas1</td>";
                echo "<td nowrap align='right'>$pjml1</td>";
                echo "<td nowrap align='right' style='font-weight:bold; border-right:1px solid black;'>$pjmlsisa1</td>";
                
                echo "<td nowrap align='right'>$pjmlbatas2</td>";
                echo "<td nowrap align='right'>$pjml2</td>";
                echo "<td nowrap align='right' style='font-weight:bold; border-right:1px solid black;'>$pjmlsisa2</td>";
                
                echo "<td nowrap align='right'>$pjmlbatas3</td>";
                echo "<td nowrap align='right'>$pjml3</td>";
                echo "<td nowrap align='right' style='font-weight:bold; border-right:1px solid black;'>$pjmlsisa3</td>";
                
                echo "<td nowrap align='right'>$pjmlbatas4</td>";
                echo "<td nowrap align='right'>$pjml4</td>";
                echo "<td nowrap align='right' style='font-weight:bold; border-right:1px solid black;'>$pjmlsisa4</td>";
                
                echo "<td nowrap align='right'>$pjmlbatas5</td>";
                echo "<td nowrap align='right'>$pjml5</td>";
                echo "<td nowrap align='right' style='font-weight:bold; border-right:1px solid black;'>$pjmlsisa5</td>";
                
                echo "<td nowrap align='right'>$pjmlbatas6</td>";
                echo "<td nowrap align='right'>$pjml6</td>";
                echo "<td nowrap align='right' style='font-weight:bold; border-right:1px solid black;'>$pjmlsisa6</td>";
                
                
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
    <hr/>
    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
</BODY>

</HTML>


<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    
    mysqli_close($cnmy);
?>