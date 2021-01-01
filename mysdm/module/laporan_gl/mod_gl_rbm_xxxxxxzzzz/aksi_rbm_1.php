<?PHP
    session_start();
    $ketprint=$_GET['ket'];
    if ($ketprint=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REPORT REALISASI BIAYA MARKETING.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/fungsi_sql.php");
    include("config/common.php");
    $cnit=$cnmy;
?>

<html>
<head>
    <title>REPORT REALISASI BIAYA MARKETING</title>
<?PHP if ($ketprint!="excel") { ?>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
    <link href="css/laporanbaru.css" rel="stylesheet">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
<?PHP } ?>
</head>

<body>
<?PHP
    $tglnow = date("d/m/Y");
    $periode = $_POST['bulan1'];
    
    $ptglprint1 = date("d F Y");
    
    
    $espd = $_POST['radio1'];
    
    $milliseconds = round(microtime(true) * 1000);
    $now=date("mdYhis");
    $tmp01 =" dbtemp.RBMRPT01_".$_SESSION['USERID']."_$now$milliseconds ";
    $tmp02 =" dbtemp.RBMRPT02_".$_SESSION['USERID']."_$now$milliseconds ";
    $tmp03 =" dbtemp.RBMRPT03_".$_SESSION['USERID']."_$now$milliseconds ";
    $tmp04 =" dbtemp.RBMRPT04_".$_SESSION['USERID']."_$now$milliseconds ";
    $tmp05 =" dbtemp.RBMRPT05_".$_SESSION['USERID']."_$now$milliseconds ";
    $tmp06 =" dbtemp.RBMRPT06_".$_SESSION['USERID']."_$now$milliseconds ";
    
    // sales
    $query = "select date_format(tgljual,'%Y-%m') bulan, divprodid, sum(qty*hna) as rpsales from dbmaster.mr_sales2 WHERE YEAR(tgljual)='$periode' GROUP BY 1,2";
    $query = "create  table $tmp04 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $periodeby_br="tgl";
    if ($espd=="A") {
        $periodeby_br="tgltrans";
    }
    
    $query = "select tgl, tgltrans, divprodid, COA4, jumlah, jumlah1 from hrd.br0 WHERE YEAR($periodeby_br)='$periode' and IFNULL(retur,'') <> 'Y'";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //klaim
    $query = "INSERT INTO $tmp01"
            . "select tgl, tgltrans, IFNULL(DIVISI,'EAGLE') DIVISI, COA4, jumlah, jumlah jumlah1 from hrd.klaim WHERE YEAR($periodeby_br)='$periode'";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //kas
    $query = "INSERT INTO $tmp01"
            . "select a.periode1 tgl, a.periode1 tgltrans, e.DIVISI2 divprodid, b.COA4, a.jumlah, a.jumlah jumlah1 from hrd.kas as a 
                LEFT JOIN dbmaster.posting_coa_kas b ON a.kode=b.kodeid
                LEFT JOIN dbmaster.coa_level4 c ON b.COA4=c.COA4
                LEFT JOIN dbmaster.coa_level3 d ON c.COA3=d.COA3
                LEFT JOIN dbmaster.coa_level2 e ON d.COA2=e.COA2
                WHERE YEAR(a.periode1)='$periode'";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    

    
    //rutin
    $query = "INSERT INTO $tmp01 
        select b.bulan tgl, b.bulan tgltrans, b.divisi, a.coa, sum(a.rptotal) jumlah, sum(a.rptotal) jumlah1 
        from dbmaster.t_brrutin1 a JOIN dbmaster.t_brrutin0 b ON a.idrutin=b.idrutin
        WHERE IFNULL(b.stsnonaktif,'')<>'Y' AND IFNULL(b.tgl_fin,'')<>''
        and YEAR(b.bulan)='$periode'
        GROUP BY 1,2,3,4";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //otc
    $periodeby_br="tglbr";
    if ($espd=="A") {
        $periodeby_br="tgltrans";
    }
     
    $query = "INSERT INTO $tmp01"
            . "select tglbr as tgl, tgltrans, 'OTC' divprodid, COA4, jumlah, realisasi as jumlah1 from hrd.br_otc WHERE YEAR($periodeby_br)='$periode'";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    if ($espd=="A") {
        $query = "UPDATE $tmp01 SET jumlah=jumlah1 WHERE IFNULL(jumlah1,0)<>0";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    
    
    $query = "select a.*, b.NAMA4, d.COA1, e.NAMA1, c.COA2, d.NAMA2, b.COA3, c.NAMA3, d.DIVISI2
       from $tmp01 a 
       LEFT JOIN dbmaster.coa_level4 b ON a.COA4=b.COA4
       LEFT JOIN dbmaster.coa_level3 c ON c.COA3=b.COA3
       LEFT JOIN dbmaster.coa_level2 d ON c.COA2=d.COA2
       LEFT JOIN dbmaster.coa_level1 e ON e.COA1=d.COA1";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select DISTINCT divprodid DIVISI, COA1, NAMA1, COA2, NAMA2, COA3, NAMA3, COA4, NAMA4 from $tmp02";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $addcolumn="";
    for ($x=1;$x<=12;$x++) {
        $addcolumn .= " ADD B$x DECIMAL(20,2),ADD S$x DECIMAL(20,2),";
    }
    $addcolumn .= " ADD TOTAL DECIMAL(20,2), ADD STOTAL DECIMAL(20,2)";
    
    $query = "ALTER TABLE $tmp03 $addcolumn";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $periodeby_br="tgl";
    if ($espd=="A") {
        $periodeby_br="tgltrans";
    }
    $urut=2;
    for ($x=1;$x<=12;$x++) {
        $jml=  strlen($x);
        $awal=$urut-$jml;
        $nbulan=$periode."-".str_repeat("0", $awal).$x;
        $nfield="B".$x;
        
        $query = "UPDATE $tmp03 a SET a.$nfield=(SELECT SUM(b.jumlah) FROM $tmp01 b WHERE a.COA4=b.COA4 AND DATE_FORMAT($periodeby_br, '%Y-%m')='$nbulan')";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $nfield="S".$x;
        $query = "UPDATE $tmp03 a SET a.$nfield=(SELECT SUM(b.rpsales) FROM $tmp04 b WHERE a.DIVISI=b.divprodid AND b.bulan='$nbulan')";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }
    
    
?>
    
    <!--<center><h2><u>REPORT REALISASI BIAYA MARKETING</u></h2></center>-->

    <style>
        .tjudul {
            font-family: Georgia, serif;
            font-size: 15px;
        }
        .tjudul td {
            padding: 4px;
        }
        #datatable2 {
            font-family: Georgia, serif;
        }
        #datatable2 th, #datatable2 td {
            padding: 4px;
        }
        #datatable2 thead{
            background-color:#cccccc; 
            font-size: 15px;
        }
        #datatable2 tbody{
            font-size: 14px;
        }
    </style>
    
    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='50%'>
                <tr><td><b>PT. SURYA DERMATO MEDICA LABS </b></td></tr>
                <tr><td><b>REALISASI BIAYA MARKETING <?PHP echo "$periode"; ?></b></td></tr>
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
                    <th align="center" nowrap>Kode</th>
                    <th align="center" nowrap>Nama Perkiraan</th>

                    <th align="center" nowrap>1</th>
                    <th align="center" nowrap>JANUARI</th>
                    <th align="center" nowrap>2</th>
                    <th align="center" nowrap>FEBRUARI</th>
                    <th align="center" nowrap>3</th>
                    <th align="center" nowrap>MARET</th>
                    <th align="center" nowrap>4</th>
                    <th align="center" nowrap>APRIL</th>
                    <th align="center" nowrap>5</th>
                    <th align="center" nowrap>MEI</th>
                    <th align="center" nowrap>6</th>
                    <th align="center" nowrap>JUNI</th>
                    <th align="center" nowrap>7</th>
                    <th align="center" nowrap>JULI</th>
                    <th align="center" nowrap>8</th>
                    <th align="center" nowrap>AGUSTUS</th>
                    <th align="center" nowrap>9</th>
                    <th align="center" nowrap>SEPTEMBER</th>
                    <th align="center" nowrap>10</th>
                    <th align="center" nowrap>OKTOBER</th>
                    <th align="center" nowrap>11</th>
                    <th align="center" nowrap>NOVEMBER</th>
                    <th align="center" nowrap>12</th>
                    <th align="center" nowrap>DESEMBER</th>
                    <th align="center" nowrap>%</th>
                    <th align="center" nowrap>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                for ($x=1;$x<=12;$x++) {
                    $pgrandtotal[$x]=0;
                    $pgrandtotalsls[$x]=0;
                }
                $query = "select distinct DIVISI from $tmp03 ORDER BY DIVISI";
                $tampil0=mysqli_query($cnmy, $query);
                while ($row0= mysqli_fetch_array($tampil0)) {
                    $divisi=$row0['DIVISI'];
                    $nmdivisi=$row0['DIVISI'];
                    if ($nmdivisi=="CAN") $nmdivisi="CANARY";
                    if ($nmdivisi=="PIGEO") $nmdivisi="PIGEON";
                    if ($nmdivisi=="PEACO") $nmdivisi="PEACOCK";
                    
                    for ($x=1;$x<=12;$x++) {
                        $ptotdivisi[$x]=0;
                        $ptotdivisisls[$x]=0;
                    }
                    
                    $query = "select distinct IFNULL(DIVISI,'') DIVISI, IFNULL(COA2,'') COA2, IFNULL(NAMA2,'') NAMA2 from $tmp03 WHERE DIVISI='$divisi' ORDER BY IFNULL(DIVISI,''), IFNULL(COA2,''), IFNULL(NAMA2,'')";
                    $tampil=mysqli_query($cnmy, $query);
                    while ($row= mysqli_fetch_array($tampil)) {
                        $pdivisi=$row['DIVISI'];
                        $pcoa2=$row['COA2'];
                        $pnmcoa2=$row['NAMA2'];

                        echo "<tr>";
                        echo "<td nowrap><b>$pcoa2</b></td>";
                        echo "<td nowrap colspan=27><b>$pnmcoa2</b></td>";
                        echo "</tr>";

                        for ($x=1;$x<=12;$x++) {
                            $psubtot[$x]=0;
                        }

                        $query = "select * from $tmp03 WHERE IFNULL(DIVISI,'')='$divisi' AND IFNULL(COA2,'')='$pcoa2' ORDER BY IFNULL(COA4,''), IFNULL(NAMA4,'')";
                        $tampil2=mysqli_query($cnmy, $query);
                        while ($row2= mysqli_fetch_array($tampil2)) {
                            $pcoa4=$row2['COA4'];
                            $pnmcoa4=$row2['NAMA4'];

                            $pers1="";
                            $pb1=$row2['B1'];

                            echo "<tr>";
                            echo "<td nowrap>$pcoa4</td>";
                            echo "<td nowrap>$pnmcoa4</td>";


                            //hitung dulu sales per jajar
                            $totsalestahunan=0;
                            for ($x=1;$x<=12;$x++) {
                                $snmcol="S".$x;
                                $pjml=$row2[$snmcol];
                                if (empty($pjml)) $pjml=0;
                                $totsalestahunan=(double)$totsalestahunan+(double)$pjml;
                            }
                            //END hitung dulu sales per jajar
                    
                            $ptotaltahund=0;
                            for ($x=1;$x<=12;$x++) {
                                $nmcol="B".$x;
                                $pjml=$row2[$nmcol];
                                if (empty($pjml)) $pjml=0;

                                $ptotaltahund=(double)$ptotaltahund+(double)$pjml;
                                $psubtot[$x]=(double)$psubtot[$x]+(double)$pjml;

                                //sales
                                $snmcol="S".$x;
                                $pjmlsls=$row2[$snmcol];
                                if (empty($pjmlsls)) $pjmlsls=0;
                                $ptotdivisisls[$x]=$pjmlsls;
                                
                                if ((double)$pjmlsls==0) {
                                    $npersen=0;
                                }else{
                                    $npersen=ROUND((double)$pjml/(double)$pjmlsls*100,2);
                                }
                                if ((double)$npersen==0) $npersen="";
                                    
                                $pjml=number_format($pjml,0,",",",");

                                echo "<td align='right' nowrap>$npersen</td>";
                                echo "<td align='right' nowrap>".$pjml."</td>";
                                
                            }

                            if ((double)$totsalestahunan==0) {
                                $inpersen=0;
                            }else{
                                $inpersen=ROUND((double)$ptotaltahund/(double)$totsalestahunan*100,2);
                            }
                            if ((double)$inpersen==0) $inpersen="";
                            
                            $ptotaltahund=number_format($ptotaltahund,0,",",",");
                            echo "<td align='right' nowrap>$inpersen</td>";
                            echo "<td align='right' nowrap>$ptotaltahund</td>";

                            echo "</tr>";

                        }
                        
                        
                        //sub total
                        echo "<tr>";
                        echo "<td nowrap><b>$pcoa2</b></td>";
                        echo "<td nowrap><b>$pnmcoa2</b></td>";

                        $ptotaltahund=0;
                        for ($x=1;$x<=12;$x++) {

                            $pjml=$psubtot[$x];
                            if (empty($pjml)) $pjml=0;
                            
                            $ptotaltahund=(double)$ptotaltahund+(double)$pjml;
                            $ptotdivisi[$x]=(double)$ptotdivisi[$x]+(double)$pjml;
                            
                            $pjmlsls=$ptotdivisisls[$x];
                            if ((double)$pjmlsls==0) {
                                $npersen=0;
                            }else{
                                $npersen=ROUND((double)$pjml/(double)$pjmlsls*100,2);
                            }
                            if ((double)$npersen==0) $npersen="";

                            
                            
                            $pjml=number_format($pjml,0,",",",");
                            
                            
                            echo "<td align='right' nowrap><b>$npersen</b></td>";
                            echo "<td align='right' nowrap><b>".$pjml."</b></td>";

                        }

                        if ((double)$totsalestahunan==0) {
                            $inpersen=0;
                        }else{
                            $inpersen=ROUND((double)$ptotaltahund/(double)$totsalestahunan*100,2);
                        }
                        if ((double)$inpersen==0) $inpersen="";
                    
                        $ptotaltahund=number_format($ptotaltahund,0,",",",");
                        echo "<td align='right' nowrap><b>$inpersen</b></td>";
                        echo "<td align='right' nowrap><b>$ptotaltahund</b></td>";

                        echo "</tr>";
                        
                        
                        echo "<tr>";
                        echo "<td nowrap colspan=28><b></b></td>";
                        echo "</tr>";

                    }
                    
                    //total per divisi
                    echo "<tr>";
                    echo "<td nowrap><b></b></td>";
                    echo "<td nowrap><b>BIAYA $nmdivisi</b></td>";

                    $ztotbr=0;
                    $ztotsls=0;

                    $periodeby_br="tgl";
                    if ($espd=="A") {
                        $periodeby_br="tgltrans";
                    }

                    $urut=2;
                    for ($x=1;$x<=12;$x++) {
                        $ztotalbr[$x]=0;
                        $ztotalsls[$x]=0;

                        $jml=  strlen($x);
                        $awal=$urut-$jml;
                        $zbulan=$periode."-".str_repeat("0", $awal).$x;


                        //cari total br
                        $query = "select IFNULL(sum(jumlah),0) jumlah From $tmp02 WHERE divprodid='$divisi' AND DATE_FORMAT($periodeby_br,'%Y-%m')='$zbulan'";
                        $rowslb=mysqli_query($cnmy, $query);
                        $ketemubr= mysqli_num_rows($rowslb);
                        if ($ketemubr>0) {
                            $rslb= mysqli_fetch_array($rowslb);
                            $ztotalbr[$x]=$rslb['jumlah'];
                            $ztotbr=(double)$ztotbr+(double)$ztotalbr[$x];
                        }

                        //cari total sales
                        $query = "select IFNULL(sum(rpsales),0) rpsales From $tmp04 WHERE divprodid='$divisi' AND bulan='$zbulan'";
                        $rowsls=mysqli_query($cnmy, $query);
                        $ketemusls= mysqli_num_rows($rowsls);
                        if ($ketemusls>0) {
                            $rsls= mysqli_fetch_array($rowsls);
                            $ztotalsls[$x]=$rsls['rpsales'];
                            $ztotsls=(double)$ztotsls+(double)$ztotalsls[$x];
                        }


                        if ((double)$ztotalsls[$x]==0) {
                            $zpersen=0;
                        }else{
                            $zpersen=ROUND((double)$ztotalbr[$x]/(double)$ztotalsls[$x]*100,2);
                        }

                        $ztotalbr[$x]=number_format($ztotalbr[$x],0,",",",");
                        echo "<td align='right' nowrap><b>$zpersen</b></td>";
                        echo "<td align='right' nowrap><b>".$ztotalbr[$x]."</b></td>";

                    }

                    if ((double)$ztotsls==0) {
                        $zpersen=0;
                    }else{
                        $zpersen=ROUND((double)$ztotbr/(double)$ztotsls*100,2);
                    }
                    $ztotbr=number_format($ztotbr,0,",",",");
                    echo "<td align='right' nowrap><b>$zpersen</b></td>";
                    echo "<td align='right' nowrap><b>$ztotbr</b></td>";

                    echo "</tr>";

                    //sales
                    echo "<tr>";
                    echo "<td nowrap><b></b></td>";
                    echo "<td nowrap><b>PENJUALAN S2 $nmdivisi</b></td>";

                        for ($x=1;$x<=12;$x++) {

                            if ((double)$ztotsls==0) {
                                $zpersen=0;
                            }else{
                                $zpersen=ROUND((double)$ztotalsls[$x]/(double)$ztotsls*100,2);
                            }

                            $ztotalsls[$x]=number_format($ztotalsls[$x],0,",",",");
                            echo "<td align='right' nowrap><b>$zpersen</b></td>";
                            echo "<td align='right' nowrap><b>".$ztotalsls[$x]."</b></td>";
                        }

                    $ztotsls=number_format($ztotsls,0,",",",");
                    echo "<td align='right' nowrap><b>100</b></td>";
                    echo "<td align='right' nowrap><b>$ztotsls</b></td>";

                    echo "</tr>";
                    
                                
                    echo "<tr>";
                    echo "<td nowrap colspan=28><b></b></td>";
                    echo "</tr>";
                    
                }
                
                // grand total
                echo "<tr>";
                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b>TOTAL BIAYA MARKETING</b></td>";

                $ztotbr=0;
                $ztotsls=0;

                $periodeby_br="tgl";
                if ($espd=="A") {
                    $periodeby_br="tgltrans";
                }

                $urut=2;
                for ($x=1;$x<=12;$x++) {
                    $ztotalbr[$x]=0;
                    $ztotalsls[$x]=0;

                    $jml=  strlen($x);
                    $awal=$urut-$jml;
                    $zbulan=$periode."-".str_repeat("0", $awal).$x;


                    //cari total br
                    $query = "select IFNULL(sum(jumlah),0) jumlah From $tmp02 WHERE DATE_FORMAT($periodeby_br,'%Y-%m')='$zbulan'";
                    $rowslb=mysqli_query($cnmy, $query);
                    $ketemubr= mysqli_num_rows($rowslb);
                    if ($ketemubr>0) {
                        $rslb= mysqli_fetch_array($rowslb);
                        $ztotalbr[$x]=$rslb['jumlah'];
                        $ztotbr=(double)$ztotbr+(double)$ztotalbr[$x];
                    }

                    //cari total sales
                    $query = "select IFNULL(sum(rpsales),0) rpsales From $tmp04 WHERE bulan='$zbulan'";
                    $rowsls=mysqli_query($cnmy, $query);
                    $ketemusls= mysqli_num_rows($rowsls);
                    if ($ketemusls>0) {
                        $rsls= mysqli_fetch_array($rowsls);
                        $ztotalsls[$x]=$rsls['rpsales'];
                        $ztotsls=(double)$ztotsls+(double)$ztotalsls[$x];
                    }


                    if ((double)$ztotalsls[$x]==0) {
                        $zpersen=0;
                    }else{
                        $zpersen=ROUND((double)$ztotalbr[$x]/(double)$ztotalsls[$x]*100,2);
                    }

                    $ztotalbr[$x]=number_format($ztotalbr[$x],0,",",",");
                    echo "<td align='right' nowrap><b>$zpersen</b></td>";
                    echo "<td align='right' nowrap><b>".$ztotalbr[$x]."</b></td>";

                }

                if ((double)$ztotsls==0) {
                    $zpersen=0;
                }else{
                    $zpersen=ROUND((double)$ztotbr/(double)$ztotsls*100,2);
                }
                $ztotbr=number_format($ztotbr,0,",",",");
                echo "<td align='right' nowrap><b>$zpersen</b></td>";
                echo "<td align='right' nowrap><b>$ztotbr</b></td>";

                echo "</tr>";

                //sales
                echo "<tr>";
                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b>PENJUALAN S2 MARKETING</b></td>";

                    for ($x=1;$x<=12;$x++) {

                        if ((double)$ztotsls==0) {
                            $zpersen=0;
                        }else{
                            $zpersen=ROUND((double)$ztotalsls[$x]/(double)$ztotsls*100,2);
                        }

                        $ztotalsls[$x]=number_format($ztotalsls[$x],0,",",",");
                        echo "<td align='right' nowrap><b>$zpersen</b></td>";
                        echo "<td align='right' nowrap><b>".$ztotalsls[$x]."</b></td>";
                    }

                
                $ztotsls=number_format($ztotsls,0,",",",");
                echo "<td align='right' nowrap><b>100</b></td>";
                echo "<td align='right' nowrap><b>$ztotsls</b></td>";

                echo "</tr>";
                    
                ?>
            </tbody>
        </table>
        <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
    <?PHP
    hapusdata:
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp03");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp04");
        mysqli_close($cnit);
    ?>
</body>
</html>