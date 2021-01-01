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
    
    
    $pjmlbatasp=0;
    $pjmlbatast=0;

    $query = "select status_trf, jumlah as jmlbts from dbmaster.t_br_batas_trf GROUP BY 1";
    $ntampil= mysqli_query($cnmy, $query);
    $nketemu= mysqli_num_rows($ntampil);
    if ($nketemu>0) {
        while ($nrx= mysqli_fetch_array($ntampil)) {
            $pststrf=$nrx['status_trf'];
            if ($pststrf=="P") $pjmlbatasp=$nrx['jmlbts'];
            if ($pststrf=="T") $pjmlbatast=$nrx['jmlbts'];
        }
    }
        
    $query ="select * from dbmaster.t_br_antrian WHERE IFNULL(stsnonaktif,'')<>'Y' and DATE_FORMAT(tanggal,'%Y%m')='$pbulan'";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "select tanggal, CAST('' as CHAR(1)) as sts_trf, CAST(0 as DECIMAL(20,2)) as jmlbatas1, "
            . " CAST(0 as DECIMAL(20,2)) as jumlah1, CAST(0 as DECIMAL(20,2)) as jmlsisa1, "
            . " CAST(0 as DECIMAL(20,2)) as jmlbatas2, "
            . " CAST(0 as DECIMAL(20,2)) as jumlah2, CAST(0 as DECIMAL(20,2)) as jmlsisa2 "
            . " from $tmp01 WHERE tanggal=''";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    mysqli_query($cnmy, "DELETE FROM $tmp02");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    unset($pinsert_data_detail);//kosongkan array
    while (strtotime($ptgl1) <= strtotime($ptgl2)) {
        
        $pinsert_data_detail[] = "('$ptgl1', '$pjmlbatasp', '$pjmlbatast')";
        
        $ptgl1 = date ("Y-m-d", strtotime("+1 days", strtotime($ptgl1)));
    }
    
    $query_detail="INSERT INTO $tmp02 (tanggal, jmlbatas1, jmlbatas2) VALUES ".implode(', ', $pinsert_data_detail);
    mysqli_query($cnmy, $query_detail);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

    $query = "UPDATE $tmp02 a JOIN (select tanggal, SUM(jumlah) jumlah from $tmp01 WHERE status_trf='P' GROUP BY 1) b ON a.tanggal=b.tanggal SET a.jumlah1=b.jumlah";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

    $query = "UPDATE $tmp02 a JOIN (select tanggal, SUM(jumlah) jumlah from $tmp01 WHERE status_trf='T' GROUP BY 1) b ON a.tanggal=b.tanggal SET a.jumlah2=b.jumlah";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp02 SET jmlsisa1=IFNULL(jmlbatas1,0)-IFNULL(jumlah1,0), jmlsisa2=IFNULL(jmlbatas2,0)-IFNULL(jumlah2,0)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
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
    
    <table id='datatablestockopn' class='table table-striped table-bordered' width='60%'>
        <thead>
            <tr>
                <th align="center">Tanggal</th>
                <th align="center" colspan="3">Payroll</th>
                <th align="center" colspan="3">Transfer</th>
            </tr>
            <tr>
                <th align="center"></th>
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
                
                $pnmhari= strtoupper(date("l", strtotime($ptangga)));
                $ptangga= date("d", strtotime($ptangga));
                
                $pjmlbatas1=number_format($pjmlbatas1,0);
                $pjml1=number_format($pjml1,0);
                $pjmlsisa1=number_format($pjmlsisa1,0);
                
                $pjmlbatas2=number_format($pjmlbatas2,0);
                $pjml2=number_format($pjml2,0);
                $pjmlsisa2=number_format($pjmlsisa2,0);
                
                
                $pwarna="";
                if ($pnmhari=="SATURDAY" OR $pnmhari=="SUNDAY") {
                    $pwarna="style='color:red;'";
                    $pjmlbatas1="";
                    $pjml1="";
                    $pjmlsisa1="";
                    $pjmlbatas2="";
                    $pjml2="";
                    $pjmlsisa2="";
                }
                
                echo "<tr $pwarna>";
                echo "<td nowrap>$ptangga</td>";
                echo "<td nowrap align='right'>$pjmlbatas1</td>";
                echo "<td nowrap align='right'>$pjml1</td>";
                echo "<td nowrap align='right'>$pjmlsisa1</td>";
                
                echo "<td nowrap align='right'>$pjmlbatas2</td>";
                echo "<td nowrap align='right'>$pjml2</td>";
                echo "<td nowrap align='right'>$pjmlsisa2</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
    <hr/>
    
</BODY>

</HTML>


<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    
    mysqli_close($cnmy);
?>