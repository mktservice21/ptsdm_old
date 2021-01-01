<?PHP
    session_start();
    $ketprint=$_GET['ket'];
    if ($ketprint=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REPORT REALISASI BIAYA MARKETING CABANG.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/koneksimysqli_ms.php");
    include("config/fungsi_sql.php");
    include("config/common.php");
    $cnit=$cnmy;
?>

<html>
<head>
    <title>REPORT REALISASI BIAYA MARKETING CABANG</title>
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
    $pdivisi = $_POST['cb_divisi'];
    $pregion = $_POST['cb_region'];
    $sisid=$_SESSION['IDSESI'];
    $pidcard=$_SESSION['IDCARD'];
    
    $pwilayah="BARAT";
    if ($pregion=="T") $pwilayah="TIMUR";
    
    $milliseconds = round(microtime(true) * 1000);
    $now=date("mdYhis");
    $tmp01 =" dbtemp.RBMCRPT01_".$_SESSION['USERID']."_$now$milliseconds ";
    $tmp02 =" dbtemp.RBMCRPT02_".$_SESSION['USERID']."_$now$milliseconds ";
    $tmp03 =" dbtemp.RBMCRPT03_".$_SESSION['USERID']."_$now$milliseconds ";
    $tmp04 =" dbtemp.RBMCRPT04_".$_SESSION['USERID']."_$now$milliseconds ";
    $tmp05 =" dbtemp.RBMCRPT05_".$_SESSION['USERID']."_$now$milliseconds ";
    $tmp06 =" dbtemp.RBMCRPT06_".$_SESSION['USERID']."_$now$milliseconds ";
    
    // sales
    /*
    $query = "select * from sls.mr_sales2 WHERE YEAR(tgljual)='$periode' <> 'OTC'";
    $query = "create TEMPORARY table $tmp04 ($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "DELETE FROM dbmaster.tmp_mr_sales2 WHERE userid='$pidcard' AND idsession='$sisid' AND tglinput=CURRENT_DATE()";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "INSERT INTO dbmaster.tmp_mr_sales2 "
            . " SELECT *, '$pidcard' as userid, '$sisid' as idsession, CURRENT_DATE() tglinput FROM $tmp04";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp04");
    
    $query = "select * from dbmaster.tmp_mr_sales2 WHERE userid='$pidcard' AND idsession='$sisid' AND tglinput=CURRENT_DATE()";
    $query = "create  table $tmp04 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    */
    
    $filslsdvi=" AND divprodid <> 'OTC' ";
    if ($pdivisi=="OTC") $filslsdvi=" AND divprodid = 'OTC' ";
        
    $fregion = " AND IFNULL(icabangid,'0000000001') IN (select distinct iCabangId From MKT.icabang WHERE region='$pregion')";
    if (empty($pregion)) $fregion="";
    
    $query = "select * from dbmaster.mr_sales2 WHERE YEAR(tgljual)='$periode' $filslsdvi $fregion";
    $query = "create TEMPORARY table $tmp05 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select DATE_FORMAT(a.tgljual,'%Y-%m') bulan, a.icabangid, b.nama nama_cabang, a.divprodid, sum(qty*hna) rpsales 
        from $tmp05 as a LEFT JOIN MKT.icabang b on a.icabangid=b.iCabangId GROUP BY 1,2,3,4";
    $query = "create TEMPORARY table $tmp04 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    if ($pdivisi=="ETHICAL") {
    
        $periodeby_br="tgl";
        if ($espd=="A") {
            $periodeby_br="tgltrans";
        }

        $query = "select IFNULL(icabangid,'0000000001') icabangid, tgl, tgltrans, divprodid, COA4, jumlah, jumlah1 from hrd.br0 WHERE YEAR($periodeby_br)='$periode' and IFNULL(retur,'') <> 'Y' $fregion";
        $query = "create TEMPORARY table $tmp01 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        $fregion = " AND IFNULL(c.region,'B') = '$pregion'";
        if (empty($pregion)) $fregion="";
        $query = "INSERT INTO $tmp01"
                . "select IFNULL(c.iCabangId,'0000000001') icabangid, a.tgl, a.tgltrans, IFNULL(a.DIVISI,'EAGLE') DIVISI, a.COA4, a.jumlah, a.jumlah jumlah1 
                from hrd.klaim a LEFT JOIN mkt.distrib0 b on a.distid=b.Distid
                LEFT JOIN mkt.icabang c ON b.iKotaId=c.iKotaId WHERE YEAR(a.$periodeby_br)='$periode' $fregion";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }

        if ($pregion!="T") {
            $query = "INSERT INTO $tmp01"
                    . "select '0000000001' as icabangid, a.periode1 tgl, a.periode1 tgltrans, e.DIVISI2 divprodid, b.COA4, a.jumlah, a.jumlah jumlah1 from hrd.kas as a 
                        LEFT JOIN dbmaster.posting_coa_kas b ON a.kode=b.kodeid
                        LEFT JOIN dbmaster.coa_level4 c ON b.COA4=c.COA4
                        LEFT JOIN dbmaster.coa_level3 d ON c.COA3=d.COA3
                        LEFT JOIN dbmaster.coa_level2 e ON d.COA2=e.COA2
                        WHERE YEAR(a.periode1)='$periode'";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        }

        $fdivisi=" AND b.divisi<>'OTC' ";
        if ($pdivisi=="OTC") $fdivisi=" AND b.divisi='OTC' ";
        $fregion = " AND IFNULL(b.icabangid,'0000000001') IN (select distinct iCabangId From MKT.icabang WHERE region='$pregion')";
        if (empty($pregion)) $fregion="";

        $query = "INSERT INTO $tmp01 
            select IFNULL(b.icabangid,'0000000001') icabangid, b.bulan tgl, b.bulan tgltrans, b.divisi, a.coa, sum(a.rptotal) jumlah, sum(a.rptotal) jumlah1 
            from dbmaster.t_brrutin1 a JOIN dbmaster.t_brrutin0 b ON a.idrutin=b.idrutin
            WHERE IFNULL(b.stsnonaktif,'')<>'Y' AND IFNULL(b.tgl_fin,'')<>''
            and YEAR(b.bulan)='$periode' $fdivisi $fregion 
            GROUP BY 1,2,3,4,5";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        if ($espd=="A") {
            $query = "UPDATE $tmp01 SET jumlah=jumlah1 WHERE IFNULL(jumlah1,0)<>0";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        }

        mysqli_query($cnit, "UPDATE $tmp01 SET icabangid='0000000001' WHERE IFNULL(icabangid,'')=''");

        $query = "select a.*, b.NAMA4, d.COA1, e.NAMA1, c.COA2, d.NAMA2, b.COA3, c.NAMA3, d.DIVISI2, f.nama nama_cabang
           from $tmp01 a 
           LEFT JOIN dbmaster.coa_level4 b ON a.COA4=b.COA4
           LEFT JOIN dbmaster.coa_level3 c ON c.COA3=b.COA3
           LEFT JOIN dbmaster.coa_level2 d ON c.COA2=d.COA2
           LEFT JOIN dbmaster.coa_level1 e ON e.COA1=d.COA1
           LEFT JOIN MKT.icabang f on a.icabangid=f.iCabangId";
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        $query = "select DISTINCT divprodid DIVISI, COA1, NAMA1, COA2, NAMA2, COA3, NAMA3, COA4, NAMA4 from $tmp02";
        $query = "create TEMPORARY table $tmp03 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
        $fregion = " AND region='$pregion'";
        if (empty($pregion)) $fregion="";
        $query = "select DISTINCT iCabangId icabangid, nama nama_cabang from MKT.icabang WHERE IFNULL(aktif,'')='Y' $fregion";
        $query = "create TEMPORARY table $tmp05 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }



        mysqli_query($cnit, "create TEMPORARY table $tmp06 (select * from $tmp05)");

        $query = "INSERT INTO $tmp05 (icabangid, nama_cabang)"
                . " SELECT DISTINCT icabangid, IFNULL(nama_cabang,'') nama_cabang FROM $tmp02 WHERE "
                . " icabangid NOT IN (select distinct icabangid FROM $tmp06) AND IFNULL(icabangid,'')<>''";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp06");
        mysqli_query($cnit, "create TEMPORARY table $tmp06 (select * from $tmp05)");

        $query = "INSERT INTO $tmp05 (icabangid, nama_cabang)"
                . " SELECT DISTINCT icabangid, IFNULL(nama_cabang,'') nama_cabang FROM $tmp04 WHERE "
                . " icabangid NOT IN (select distinct icabangid FROM $tmp06) AND IFNULL(icabangid,'')<>''";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        mysqli_query($cnit, "DELETE FROM $tmp05 WHERE icabangid ='blank'");
    
    }else{
        
        $periodeby_br="tglbr";
        if ($espd=="A") {
            $periodeby_br="tgltrans";
        }
        
        $fregion = " AND IFNULL(icabangid_o,'0000000007') IN (select distinct icabangid_o From MKT.icabang_o WHERE region='$pregion')";
        if (empty($pregion)) $fregion="";
    
        $query = "select icabangid_o icabangid, tglbr as tgl, tgltrans, 'OTC' divprodid, COA4, jumlah, realisasi as jumlah1 "
                . "from hrd.br_otc WHERE YEAR($periodeby_br)='$periode' $fregion";
        $query = "create TEMPORARY table $tmp01 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        if ($espd=="A") {
            $query = "UPDATE $tmp01 SET jumlah=jumlah1 WHERE IFNULL(jumlah1,0)<>0";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        }
        
        $fdivisi=" AND b.divisi='OTC' ";
        $fregion = " AND IFNULL(b.icabangid,'0000000001') IN (select distinct iCabangId From MKT.icabang WHERE region='$pregion')";
        if (empty($pregion)) $fregion="";

        $query = "INSERT INTO $tmp01 
            select IFNULL(b.icabangid,'0000000001') icabangid, b.bulan tgl, b.bulan tgltrans, b.divisi, a.coa, sum(a.rptotal) jumlah, sum(a.rptotal) jumlah1 
            from dbmaster.t_brrutin1 a JOIN dbmaster.t_brrutin0 b ON a.idrutin=b.idrutin
            WHERE IFNULL(b.stsnonaktif,'')<>'Y' 
            and YEAR(b.bulan)='$periode' $fdivisi $fregion 
            GROUP BY 1,2,3,4,5";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnit, "UPDATE $tmp01 SET icabangid='0000000007' WHERE IFNULL(icabangid,'')=''");

        $query = "select a.*, b.NAMA4, d.COA1, e.NAMA1, c.COA2, d.NAMA2, b.COA3, c.NAMA3, d.DIVISI2, f.nama nama_cabang
           from $tmp01 a 
           LEFT JOIN dbmaster.coa_level4 b ON a.COA4=b.COA4
           LEFT JOIN dbmaster.coa_level3 c ON c.COA3=b.COA3
           LEFT JOIN dbmaster.coa_level2 d ON c.COA2=d.COA2
           LEFT JOIN dbmaster.coa_level1 e ON e.COA1=d.COA1
           LEFT JOIN MKT.icabang_o f on a.icabangid=f.icabangid_o";
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        $query = "select DISTINCT divprodid DIVISI, COA1, NAMA1, COA2, NAMA2, COA3, NAMA3, COA4, NAMA4 from $tmp02";
        $query = "create TEMPORARY table $tmp03 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
        $fregion = " AND region='$pregion'";
        if (empty($pregion)) $fregion="";
        $query = "select DISTINCT icabangid_o icabangid, nama nama_cabang from MKT.icabang_o WHERE IFNULL(aktif,'')='Y' $fregion";
        $query = "create TEMPORARY table $tmp05 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnit, "create TEMPORARY table $tmp06 (select * from $tmp05)");

        $query = "INSERT INTO $tmp05 (icabangid, nama_cabang)"
                . " SELECT DISTINCT icabangid, IFNULL(nama_cabang,'') nama_cabang FROM $tmp02 WHERE "
                . " icabangid NOT IN (select distinct icabangid FROM $tmp06) AND IFNULL(icabangid,'')<>''";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp06");
        mysqli_query($cnit, "create TEMPORARY table $tmp06 (select * from $tmp05)");

        $query = "INSERT INTO $tmp05 (icabangid, nama_cabang)"
                . " SELECT DISTINCT icabangid, IFNULL(nama_cabang,'') nama_cabang FROM $tmp04 WHERE "
                . " icabangid NOT IN (select distinct icabangid FROM $tmp06) AND IFNULL(icabangid,'')<>''";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        mysqli_query($cnit, "DELETE FROM $tmp05 WHERE icabangid ='blank'");
        mysqli_query($cnit, "UPDATE $tmp05 SET nama_cabang=icabangid WHERE IFNULL(nama_cabang,'') =''");
        
    }


?>
    
    <style>
        .tjudul {
            font-family: Georgia, serif;
            font-size: 15px;
        }
        .tjudul td {
            padding: 8px;
        }
        #datatable2 {
            font-family: Georgia, serif;
        }
        #datatable2 th, #datatable2 td {
            padding: 4px;
        }
        #datatable2 thead{
            background-color:#cccccc; 
            font-size: 16px;
        }
        #datatable2 tbody{
            font-size: 14px;
        }
    </style>
    
    <!--<center><h2><u>REPORT REALISASI BIAYA MARKETING CABANG</u></h2></center>-->

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='50%'>
                <tr><td><b>PT. SURYA DERMATO MEDICA LABS </b></td></tr>
                <tr><td><b>REALISASI BIAYA MARKETING <?PHP echo "$periode"; ?> PER CABANG</b></td></tr>
                <?PHP if (!empty($pregion)) { ?>
                <tr><td><b>WILAYAH <?PHP echo "$pwilayah"; ?></b></td></tr>
                <?PHP } ?>
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

                    <th align="center" nowrap>%</th>
                    <th align="center" nowrap>Rp</th>
                </tr>
            </thead>
            <tbody>
            <?PHP
                $pjmlsales=0;
                $pgrandtot=0;
                $pgrandsalestot=0;
                $ptotcab=0;
                $ptotsalescab=0;
                $divisi="";
                $ilewat=false;
                $query = "select DISTINCT icabangid, nama_cabang from $tmp05 ORDER BY nama_cabang";
                $tampil0=mysqli_query($cnmy, $query);
                while ($row0= mysqli_fetch_array($tampil0)) {
                    $picabang=$row0['icabangid'];
                    $pnmcabang=$row0['nama_cabang'];
                    
                    echo "<tr>";
                    echo "<td nowrap colspan=4><b><span style='color:blue;'>$pnmcabang</span></b></td>";
                    echo "</tr>";
                        
                    $ptotdiv=0;
                    $ptotsalesdiv=0;
                    
                    $query = "select DISTINCT divprodid, icabangid, nama_cabang from $tmp02 WHERE icabangid='$picabang' ORDER BY divprodid";
                    $tampil1=mysqli_query($cnmy, $query);
                    $ketemu=mysqli_num_rows($tampil1);
                    if ($ketemu>0) {
                        
                        while ($row1= mysqli_fetch_array($tampil1)) {

                            $divisi=$row1['divprodid'];
                            $nmdivisi=$row1['divprodid'];
                            if ($nmdivisi=="CAN") $nmdivisi="CANARY";
                            if ($nmdivisi=="PIGEO") $nmdivisi="PIGEON";
                            if ($nmdivisi=="PEACO") $nmdivisi="PEACOCK";


                            //sales
                            $query = "select sum(rpsales) rpsales From $tmp04 WHERE divprodid='$divisi' AND icabangid='$picabang' ";
                            $rowsls=mysqli_query($cnmy, $query);
                            $rsls= mysqli_fetch_array($rowsls);
                            $pjmlsales=$rsls['rpsales'];
                            if (empty($pjmlsales)) $pjmlsales=0;
                            //echo "$pnmcabang ($nmdivisi) : $pjmlsales<br/>";
                            $ptotcoa2=0;

                            $query = "select DISTINCT divprodid, icabangid, nama_cabang, COA2, NAMA2 from $tmp02 WHERE divprodid='$divisi' AND icabangid='$picabang' ORDER BY nama_cabang, icabangid";
                            $tampil2=mysqli_query($cnmy, $query);
                            while ($row2= mysqli_fetch_array($tampil2)) {
                                $pcoa2=$row2['COA2'];
                                $pnmcoa2=$row2['NAMA2'];

                                echo "<tr>";
                                echo "<td nowrap><b>$pcoa2</b></td>";
                                echo "<td nowrap colspan=3><b>$pnmcoa2</b></td>";
                                echo "</tr>";


                                $query = "select divprodid, icabangid, nama_cabang, COA2, NAMA2, COA4, NAMA4, sum(jumlah) as jumlah "
                                        . " from $tmp02 WHERE divprodid='$divisi' AND icabangid='$picabang' AND COA2='$pcoa2' "
                                        . " GROUP BY 1,2,3,4,5,6,7"
                                        . " ORDER BY COA4, NAMA4";
                                $tampil3=mysqli_query($cnmy, $query);
                                while ($row3= mysqli_fetch_array($tampil3)) {
                                    $pcoa4=$row3['COA4'];
                                    $pnmcoa4=$row3['NAMA4'];



                                    $pjml=$row3['jumlah'];
                                    $ptotcoa2=(double)$ptotcoa2+(double)$pjml;

                                    if ((double)$pjmlsales==0) {
                                        $ipersen="";
                                    }else{
                                        $ipersen=ROUND((double)$pjml/(double)$pjmlsales*100,2);
                                    }

                                    $pjml=number_format($pjml,0,",",",");


                                    echo "<tr>";
                                    echo "<td nowrap>$pcoa4</td>";
                                    echo "<td nowrap>$pnmcoa4</td>";

                                    echo "<td nowrap align='right'>$ipersen</td>";
                                    echo "<td nowrap align='right'>$pjml</td>";

                                    echo "</tr>";


                                }

                                //total sub coa2

                                $ptotdiv=(double)$ptotdiv+(double)$ptotcoa2;
                                
                                if ((double)$pjmlsales==0) {
                                    $ipersen="";
                                }else{
                                    $ipersen=ROUND((double)$ptotcoa2/(double)$pjmlsales*100,2);
                                }
                                $ptotcoa2=number_format($ptotcoa2,0,",",",");

                                echo "<tr>";
                                echo "<td nowrap><b><span style='color:black;'>$pcoa2</span></b></td>";
                                echo "<td nowrap><b><span style='color:black;'>$pnmcoa2</span></b></td>";

                                echo "<td nowrap align='right'><b><span style='color:black;'>$ipersen</span></b></td>";
                                echo "<td nowrap align='right'><b><span style='color:black;'>$ptotcoa2</span</b></td>";

                                echo "</tr>";

                                //echo "<tr><td colspan=4><b></b></td></tr>";
                            }

                            //total sub divisi
                            $ptotsalesdiv=$pjmlsales;
                            
                            $ptotcab=(double)$ptotcab+(double)$ptotdiv;
                            $ptotsalescab=(double)$ptotsalescab+(double)$ptotsalesdiv;
                            if ((double)$ptotsalesdiv==0) {
                                $ipersen="";
                            }else{
                                $ipersen=ROUND((double)$ptotdiv/(double)$ptotsalesdiv*100,2);
                            }
                            $ptotdiv=number_format($ptotdiv,0,",",",");

                            echo "<tr>";
                            echo "<td nowrap><b></b></td>";
                            echo "<td nowrap><b><span style='color:red;'>BIAYA $nmdivisi ($pnmcabang)</span></b></td>";

                            echo "<td nowrap align='right'><b><span style='color:red;'>$ipersen</span></b></td>";
                            echo "<td nowrap align='right'><b><span style='color:red;'>$ptotdiv</span></b></td>";

                            echo "</tr>";

                            //sales
                            if ((double)$ptotsalesdiv==0) {
                                $ipersen="";
                            }else{
                                $ipersen=ROUND((double)$ptotsalesdiv/(double)$ptotsalesdiv*100,2);
                            }
                            $ptotsalesdiv=number_format($ptotsalesdiv,0,",",",");
                            
                            
                            echo "<tr>";
                            echo "<td nowrap><b></b></td>";
                            echo "<td nowrap><b><span style='color:green;'>PENJUALAN S2 $nmdivisi ($pnmcabang)</span></b></td>";

                            echo "<td nowrap align='right'><b><span style='color:green;'>$ipersen</span></b></td>";
                            echo "<td nowrap align='right'><b><span style='color:green;'>$ptotsalesdiv</span></b></td>";

                            echo "</tr>";

                            //echo "<tr><td colspan=4><b></b></td></tr>";
                        }
                    
                        
                    }//ketemu
                    else{
                        /*
                        //sales
                        $query = "select sum(rpsales) rpsales From $tmp04 WHERE icabangid='$picabang'";
                        $rowsls=mysqli_query($cnmy, $query);
                        $rsls= mysqli_fetch_array($rowsls);
                        $pjmlsalesL=$rsls['rpsales'];
                        if (empty($pjmlsalesL)) $pjmlsalesL=0;
                        
                        //$ptotsalescab=$pjmlsalesL;
                        $ptotsalescab=0;
                        $ptotcab=0;
                        
                        $ilewat=true;
                        //echo "$pnmcabang : $pjmlsales<br/>";
                         * 
                         */
                        $ptotcab=0;
                    }
                    //cari sales per cabang
                    $ptotsalescab=0;
                    $query = "select sum(rpsales) rpsales From $tmp04 WHERE icabangid='$picabang'";
                    $rowsls=mysqli_query($cnmy, $query);
                    $rsls= mysqli_fetch_array($rowsls);
                    $ptotsalescab=$rsls['rpsales'];
                    if (empty($ptotsalescab)) $ptotsalescab=0;
                    
                    
                    //cari total br per cabang
                    $ptotcab=0;
                    $query = "select sum(jumlah) jumlah From $tmp02 WHERE icabangid='$picabang'";
                    $rowslb=mysqli_query($cnmy, $query);
                    $rslb= mysqli_fetch_array($rowslb);
                    $ptotcab=$rslb['jumlah'];
                    if (empty($ptotcab)) $ptotcab=0;
                    
                    //total sub cabang
                    $ipersen="";
                    $pgrandtot=(double)$pgrandtot+(double)$ptotcab;
                    $pgrandsalestot=(double)$pgrandsalestot+(double)$ptotsalescab;
                    if ((double)$ptotsalescab==0) {
                        $ipersen="";
                    }else{
                        $ipersen=ROUND((double)$ptotcab/(double)$ptotsalescab*100,2);
                    }
                    $ptotcab=number_format($ptotcab,0,",",",");

                    echo "<tr>";
                    echo "<td nowrap><b><span style='color:blue;'></span></b></td>";
                    echo "<td nowrap><b><span style='color:blue;'>BIAYA $pnmcabang</span></b></td>";

                    echo "<td nowrap align='right'><b><span style='color:blue;'>$ipersen</span></b></td>";
                    echo "<td nowrap align='right'><b><span style='color:blue;'>$ptotcab</span></b></td>";

                    echo "</tr>";
                    
                    
                    //sales
                    if ((double)$ptotsalescab==0) {
                        $ipersen="";
                    }else{
                        $ipersen=ROUND((double)$ptotsalescab/(double)$ptotsalescab*100,2);
                    }
                    $ptotsalescab=number_format($ptotsalescab,0,",",",");
                    
                    
                    echo "<tr>";
                    echo "<td nowrap><b></b></td>";
                    echo "<td nowrap><b><span style='color:green;'>PENJUALAN S2 ($pnmcabang)</span></b></td>";

                    echo "<td nowrap align='right'><b><span style='color:green;'>$ipersen</span></b></td>";
                    echo "<td nowrap align='right'><b><span style='color:green;'>$ptotsalescab</span></b></td>";

                    echo "</tr>";
                        
                    echo "<tr><td colspan=4><b></b></td></tr>";

                    
                }
                
                //cari sales per all
                $pgrandsalestot=0;
                $query = "select sum(rpsales) rpsales From $tmp04";
                $rowsls=mysqli_query($cnmy, $query);
                $rsls= mysqli_fetch_array($rowsls);
                $pgrandsalestot=$rsls['rpsales'];
                if (empty($pgrandsalestot)) $pgrandsalestot=0;
                
                //cari total br
                $pgrandtot=0;
                $query = "select sum(jumlah) jumlah From $tmp02";
                $rowslb=mysqli_query($cnmy, $query);
                $rslb= mysqli_fetch_array($rowslb);
                $pgrandtot=$rslb['jumlah'];
                if (empty($pgrandtot)) $pgrandtot=0;
                    
                    
                //grand total 
                $ipersen="";
                if ((double)$pgrandsalestot==0) {
                    $ipersen="";
                }else{
                    $ipersen=ROUND((double)$pgrandtot/(double)$pgrandsalestot*100,2);
                }
                $pgrandtot=number_format($pgrandtot,0,",",",");

                echo "<tr>";
                echo "<td nowrap><b><span style='color:black;'></span></b></td>";
                echo "<td nowrap><b><span style='color:black;'>TOTAL BIAYA MARKETING</span></b></td>";

                echo "<td nowrap align='right'><b><span style='color:black;'>$ipersen</span></b></td>";
                echo "<td nowrap align='right'><b><span style='color:black;'>$pgrandtot</span></b></td>";

                echo "</tr>";
                
                //sales
                if ((double)$pgrandsalestot==0) {
                    $ipersen="";
                }else{
                    $ipersen=ROUND((double)$pgrandsalestot/(double)$pgrandsalestot*100,2);
                }
                $pgrandsalestot=number_format($pgrandsalestot,0,",",",");
                echo "<tr>";
                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b><span style='color:black;'>PENJUALAN S2 MARKETING</span></b></td>";

                echo "<td nowrap align='right'><b><span style='color:black;'>$ipersen</span></b></td>";
                echo "<td nowrap align='right'><b><span style='color:black;'>$pgrandsalestot</span></b></td>";

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
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp06");
    mysqli_close($cnit);
    mysqli_close($cnms);
?>
</body>
</html>