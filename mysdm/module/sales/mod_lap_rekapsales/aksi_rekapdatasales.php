<?PHP
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);

    session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    $printdate= date("d/m/Y H:i");
    
    $pmodule=$_GET['module'];
    $ppilformat=1;
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        $ppilformat=3;
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REKAP DATA SALES.xls");
    }
    include("config/koneksimysqli.php");
    include("config/koneksimysqli_ms.php");
    include("config/fungsi_combo.php");
    include("config/fungsi_sql.php");
    
    $figroupuser=$_SESSION['GROUP'];
    
    $prpttype=$_POST['cb_rpttype'];
    $ptahun=$_POST['tahun'];
    $pbulan01=$ptahun."-01-01";
    $pbulan02=$ptahun."-12-31";
    
    $puserid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpslsrekaplap01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpslsrekaplap02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmpslsrekaplap03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmpslsrekaplap04_".$puserid."_$now ";
    
    
    $query = "select divprodid, CONCAT(LEFT(bulan,7),'-01') as bulan, sum(value_sales) as value_sales "
            . " from fe_ms.sales WHERE "
            . " bulan BETWEEN '$pbulan01' AND '$pbulan02' ";
    $query .=" GROUP BY 1,2";
    $query = "create temporary table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select * from $tmp01 WHERE divprodid IN ('EAGLE', 'PIGEO')";
    $query = "create temporary table $tmp03 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp03 SET divprodid='CAN'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "INSERT INTO $tmp01 (divprodid, bulan, value_sales)"
            . " select divprodid, CONCAT(LEFT(tgljual,7),'-01') as bulan, sum(IFNULL(qty,0)*IFNULL(hna,0)) as value_sales "
            . " from fe_it.otc_etl "
            . " WHERE icabangid <> 22 AND tgljual BETWEEN '$pbulan01' AND '$pbulan02' ";
    $query .=" GROUP BY 1,2";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query ="INSERT INTO $tmp01 (divprodid, bulan, value_sales) SELECT divprodid, bulan, value_sales FROM $tmp03";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    if ($prpttype=="npilbulan") {
    
        $query = "select distinct divprodid FROM $tmp01";
        $query = "create temporary table $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $addcolumn="";
        for ($x=1;$x<=12;$x++) {
            $addcolumn .= " ADD S$x DECIMAL(20,2),";
        }
        $addcolumn .= " ADD STOTAL DECIMAL(20,2)";

        $query = "ALTER TABLE $tmp02 $addcolumn";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        $urut=2;
        for ($x=1;$x<=12;$x++) {
            $jml=  strlen($x);
            $awal=$urut-$jml;
            $nbulan=$ptahun."-".str_repeat("0", $awal).$x;

            $nfield="a.S".$x;
            $query = "UPDATE $tmp02 a JOIN (select divprodid, SUM(value_sales) as value_sales FROM $tmp01 WHERE LEFT(bulan,7)='$nbulan' GROUP BY 1) as b "
                    . " on a.divprodid=b.divprodid SET $nfield=b.value_sales";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            //echo "$nbulan<br/>";
        }

        $query = "UPDATE $tmp02 a JOIN (select divprodid, SUM(value_sales) as value_sales FROM $tmp01 GROUP BY 1) as b "
                . " on a.divprodid=b.divprodid SET a.STOTAL=b.value_sales";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    }else{
        
        $query = "select distinct bulan FROM $tmp01";
        $query = "create temporary table $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $arriddivisi[]="";
        $arrnmdivisi[]="";
        $query = "select distinct divprodid from $tmp01 order by divprodid";
        $tampilk= mysqli_query($cnmy, $query);
        while ($zr= mysqli_fetch_array($tampilk)) {
            $ziddiv=$zr['divprodid'];
            $znmdiv=$ziddiv;

            if ($ziddiv=="CAN") $znmdiv="CANARY";
            if ($ziddiv=="PEACO") $znmdiv="PEACOCK";
            if ($ziddiv=="PIGEO") $znmdiv="PIGEON";
            if ($ziddiv=="MAKLO") $znmdiv="MAKLON";
            if ($ziddiv=="OTC") $znmdiv="CHC";
                
            $arriddivisi[]=$ziddiv;
            $arrnmdivisi[]=$znmdiv;
        }
    
    
        $addcolumn="";
        for($ix=1;$ix<count($arriddivisi);$ix++) {
            $ziddiv=$arriddivisi[$ix];
            $znmdiv=$arrnmdivisi[$ix];

            $nmfield2="S".$ziddiv;

            $addcolumn .= " ADD COLUMN $nmfield2 DECIMAL(20,2),";

        }
        $addcolumn .= " ADD COLUMN STOTAL DECIMAL(20,2)";

        $query = "ALTER TABLE $tmp02 $addcolumn";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        for($ix=1;$ix<count($arriddivisi);$ix++) {
            $ziddiv=$arriddivisi[$ix];
            $znmdiv=$arrnmdivisi[$ix];
            
            $nfield="a.S".$ziddiv;
            $query = "UPDATE $tmp02 a JOIN (select bulan, SUM(value_sales) as value_sales FROM $tmp01 WHERE divprodid='$ziddiv' GROUP BY 1) as b "
                    . " on a.bulan=b.bulan SET $nfield=b.value_sales";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        }
    
        $query = "UPDATE $tmp02 a JOIN (select bulan, SUM(value_sales) as value_sales FROM $tmp01 WHERE divprodid NOT IN ('CAN') GROUP BY 1) as b "
                . " on a.bulan=b.bulan SET a.STOTAL=b.value_sales";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }
    
    
?>

<HTML>
<HEAD>
  <TITLE>Rekap Data Sales</TITLE>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2030 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <link rel="shortcut icon" href="images/icon.ico" />
    <style> .str{ mso-number-format:\@; } </style>
</HEAD>
<script>
</script>

<BODY onload="initVar()">
    
    <?PHP
    echo "<b>Rekap Data Sales</b><br/>";
    echo "<b>Tahun : $ptahun</b><br/>";
    echo "<small><i>view data : $printdate</i></small><br/>";
    echo "<hr/><br/>";
    
    
    if ($prpttype=="npilbulan") {
        
        echo "<table id='tbltable' border='1' cellspacing='0' cellpadding='1'>";
            echo "<thead>";
                echo "<tr>";
                    echo "<th align='center'><small>Divisi</small></th>";
                    echo "<th align='center'><small>Januari</small></th>";
                    echo "<th align='center'><small>Februari</small></th>";
                    echo "<th align='center'><small>Maret</small></th>";
                    echo "<th align='center'><small>April</small></th>";
                    echo "<th align='center'><small>Mei</small></th>";
                    echo "<th align='center'><small>Juni</small></th>";
                    echo "<th align='center'><small>Juli</small></th>";
                    echo "<th align='center'><small>Agustus</small></th>";
                    echo "<th align='center'><small>September</small></th>";
                    echo "<th align='center'><small>Oktober</small></th>";
                    echo "<th align='center'><small>November</small></th>";
                    echo "<th align='center'><small>Desember</small></th>";
                    echo "<th align='center'><small>Total</small></th>";
                echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

                for ($x=1;$x<=12;$x++) {
                    $pgrandtotblnsls[$x]=0;
                }
                $pgrandtotalsls=0;
                $query = "select * from $tmp02 ORDER BY divprodid";
                $tampil=mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pdivisi=$row['divprodid'];
                    $pnmdivisi=$pdivisi;

                    if ($pdivisi=="CAN") $pnmdivisi="CANARY";
                    if ($pdivisi=="PEACO") $pnmdivisi="PEACOCK";
                    if ($pdivisi=="PIGEO") $pnmdivisi="PIGEON";
                    if ($pdivisi=="MAKLO") $pnmdivisi="MAKLON";
                    if ($pdivisi=="OTC") $pnmdivisi="CHC";

                    $ptotalsls=$row['STOTAL'];
                    
                    if ($pdivisi=="CAN"){}
                    else $pgrandtotalsls=(DOUBLE)$pgrandtotalsls+(DOUBLE)$ptotalsls;

                    $ptotalsls=BuatFormatNumberRp($ptotalsls, $ppilformat);//1 OR 2 OR 3

                    echo "<tr>";
                    echo "<td nowrap>$pnmdivisi</td>";

                    for ($x=1;$x<=12;$x++) {
                        $nmcol="S".$x;
                        $pjmlsls=$row[$nmcol];
                        if (empty($pjmlsls)) $pjmlsls=0;

                        if ($pdivisi=="CAN"){}
                        else $pgrandtotblnsls[$x]=(DOUBLE)$pgrandtotblnsls[$x]+(DOUBLE)$pjmlsls;

                        $pjmlsls=BuatFormatNumberRp($pjmlsls, $ppilformat);//1 OR 2 OR 3

                        echo "<td nowrap align='right'>$pjmlsls</td>";
                    }
                    echo "<td nowrap align='right'>$ptotalsls</td>";
                    echo "</tr>";


                }

                $pgrandtotalsls=BuatFormatNumberRp($pgrandtotalsls, $ppilformat);//1 OR 2 OR 3

                echo "<tr style='font-weight:bold;'>";
                echo "<td nowrap>GRAND TOTAL </td>";

                for ($x=1;$x<=12;$x++) {
                    $pgrandtotblnsls_=$pgrandtotblnsls[$x];

                    if (empty($pgrandtotblnsls_)) $pgrandtotblnsls_=0;

                    $pgrandtotblnsls_=BuatFormatNumberRp($pgrandtotblnsls_, $ppilformat);//1 OR 2 OR 3

                    echo "<td nowrap align='right'>$pgrandtotblnsls_</td>";

                }

                echo "<td nowrap align='right'>$pgrandtotalsls</td>";
                echo "</tr>";

            echo "</tbody>";
        echo "</table>";
        
    }else{
        
        echo "<table id='tbltable' border='1' cellspacing='0' cellpadding='1'>";
            echo "<thead>";
                echo "<tr>";
                    echo "<th align='center'><small>Bulan</small></th>";
                    
                    for($ix=1;$ix<count($arriddivisi);$ix++) {
                        $ziddiv=$arriddivisi[$ix];
                        $znmdiv=$arrnmdivisi[$ix];
                        
                        echo "<th align='center'><small>$znmdiv</small></th>";
                    }
                    
                    echo "<th align='center'><small>Total</small></th>";
                echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
        
                for($ix=1;$ix<count($arriddivisi);$ix++) {
                    $pgrandtotblnsls[$ix]=0;
                }
                $pgrandtotalsls=0;
                $query = "select * from $tmp02 ORDER BY bulan";
                $tampil=mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $vbulanpilih=$row['bulan'];
                    
                    $vblnpilih = date('F Y', strtotime($vbulanpilih));
                    
                    $ptotalsls=$row['STOTAL'];
                    $pgrandtotalsls=(DOUBLE)$pgrandtotalsls+(DOUBLE)$ptotalsls;

                    $ptotalsls=BuatFormatNumberRp($ptotalsls, $ppilformat);//1 OR 2 OR 3

                    echo "<tr>";
                    echo "<td nowrap>$vblnpilih</td>";
                    
                    for($ix=1;$ix<count($arriddivisi);$ix++) {
                        $ziddiv=$arriddivisi[$ix];
                        $znmdiv=$arrnmdivisi[$ix];
                        
                        $nmcol="S".$ziddiv;
                        $pjmlsls=$row[$nmcol];
                        if (empty($pjmlsls)) $pjmlsls=0;
                        
                        $pgrandtotblnsls[$ix]=(DOUBLE)$pgrandtotblnsls[$ix]+(DOUBLE)$pjmlsls;

                        $pjmlsls=BuatFormatNumberRp($pjmlsls, $ppilformat);//1 OR 2 OR 3

                        echo "<td nowrap align='right'>$pjmlsls</td>";
                        
                    }
                    
                    echo "<td nowrap align='right'>$ptotalsls</td>";
                    echo "</tr>";
                    
                }
                
                $pgrandtotalsls=BuatFormatNumberRp($pgrandtotalsls, $ppilformat);//1 OR 2 OR 3

                echo "<tr style='font-weight:bold;'>";
                echo "<td nowrap>GRAND TOTAL </td>";

                for($ix=1;$ix<count($arriddivisi);$ix++) {
                    
                    $pgrandtotblnsls_=$pgrandtotblnsls[$ix];

                    if (empty($pgrandtotblnsls_)) $pgrandtotblnsls_=0;

                    $pgrandtotblnsls_=BuatFormatNumberRp($pgrandtotblnsls_, $ppilformat);//1 OR 2 OR 3

                    echo "<td nowrap align='right'>$pgrandtotblnsls_</td>";

                }

                echo "<td nowrap align='right'>$pgrandtotalsls</td>";
                echo "</tr>";
                
            echo "</tbody>";
        echo "</table>";
        
    }
    
    ?>
    
</BODY>


    <style>
        #tbltable {
            border-collapse: collapse;
        }
        th {
            font-size : 16px;
            padding:5px;
            background-color: #ccccff;
        }
        tr td {
            font-size : 12px;
        }
        tr td {
            padding : 3px;
        }
        tr:hover {background-color:#f5f5f5;}
        thead tr:hover {background-color:#cccccc;}
    </style>
    
</HTML>


<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
    mysqli_close($cnmy);
    mysqli_close($cnms);
?>