<?PHP
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REALISASI BIAYA MARKETING VS BUDGET.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>

<html>
<head>
    <title>REALISASI BIAYA MARKETING VS BUDGET</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Mei 2050 1:00:00 GMT">
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
        
        $now=date("mdYhis");
        $tmp01 =" dbtemp.RPTREKBMABG01_".$_SESSION['USERID']."_$now ";
        $tmp02 =" dbtemp.RPTREKBMABG02_".$_SESSION['USERID']."_$now ";
        $tmp03 =" dbtemp.RPTREKBMABG03_".$_SESSION['USERID']."_$now ";
        $tmp04 =" dbtemp.RPTREKBMABG04_".$_SESSION['USERID']."_$now ";
        $tmp05 =" dbtemp.RPTREKBMABG05_".$_SESSION['USERID']."_$now ";

        
        $tgl1=$_POST['tahun'];
        $pbulan=date("Y-m", strtotime($tgl1));
        $ptahun=date("Y", strtotime($tgl1));
        $pblnthn=date("F Y", strtotime($tgl1));
        
        $espd = $_POST['radio1'];
        
        $query = "create TEMPORARY table $tmp01 (SELECT * FROM dbmaster.t_budget_realisasi_lap)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        mysqli_query($cnit, "update $tmp01 set keterangan=keterangan2");
        
        $query = "SELECT
            a.tahun,
            a.g_divisi,
            a.kodeid,
            a.jumlah
            FROM
            dbmaster.t_budget AS a
            WHERE tahun = '$ptahun' AND g_divisi='ETH'";
        
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        //biaya rutin
        $query = "select YEAR(b.bulan) tahun, '01' kodeid, b.divisi, sum(a.rptotal) jumlah 
            from dbmaster.t_brrutin1 a JOIN dbmaster.t_brrutin0 b ON a.idrutin=b.idrutin
            WHERE IFNULL(b.stsnonaktif,'')<>'Y' AND IFNULL(b.tgl_fin,'')<>'' AND kode=1 AND b.divisi<>'OTC' 
            and YEAR(b.bulan)='$ptahun' AND DATE_FORMAT(b.bulan,'%Y-%m')<='$pbulan'
            GROUP BY 1,2,3";
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        //biaya luar kota
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah)
            select YEAR(b.bulan) tahun, '02' kodeid, b.divisi, sum(a.rptotal) jumlah 
            from dbmaster.t_brrutin1 a JOIN dbmaster.t_brrutin0 b ON a.idrutin=b.idrutin
            WHERE IFNULL(b.stsnonaktif,'')<>'Y' AND IFNULL(b.tgl_fin,'')<>'' AND kode=2 AND b.divisi<>'OTC' 
            and YEAR(b.bulan) ='$ptahun' AND DATE_FORMAT(b.bulan,'%Y-%m')<='$pbulan' 
            GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah)"
                . "select YEAR(tglf) tahun, '03' kodeid, '' divisi, sum(jumlah) jumlah from dbmaster.t_suratdana_br where "
                . " kodeid=1 and subkode='04' and YEAR(tglf)='$ptahun' AND DATE_FORMAT(tglf,'%Y-%m')<='$pbulan' "
                . " AND IFNULL(stsnonaktif,'')<>'Y' "
                . " GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        //BR
        $periodeby_br="tgl";
        if ($espd=="A") {
            $periodeby_br="tgltrans";
        }
    
	$query = "select * from hrd.br0
                 where DATE_FORMAT($periodeby_br,'%Y')='$ptahun' AND DATE_FORMAT($periodeby_br,'%Y-%m') <= '$pbulan' and retur <> 'Y' and batal <>'Y'";
        $query = "create TEMPORARY table $tmp04 ($query)";
        mysqli_query($cnit, $query);
        
        if ($espd=="A") {
            $query = "UPDATE $tmp04 SET jumlah=jumlah1 WHERE IFNULL(jumlah1,0)<>0";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        }
        
        
        //DSS
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '04' kodeid, divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE (kode='700-01-04' or kode='700-02-04' or kode='700-04-04')
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        //DCC
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '05' kodeid, divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE (kode='700-01-03' or kode='700-02-03' or kode='700-04-03')
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        //Gimmic GIMIK GIMMIC PROMOSI
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '06' kodeid, divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE (kode='700-01-01' or kode='700-02-01' or kode='700-04-01')
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        //IKLAN
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '07' kodeid, divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE kode='700-01-06' or kode='700-04-06' 
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        //SIMPOSIUM
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '08' kodeid, divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE (kode='700-01-08' or kode='700-02-05' OR kode='700-04-05')
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        // HO
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '10' kodeid, divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE (kode='700-01-099' or kode='700-02-099' or kode='700-04-099')
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        //KAS KECIL
	$query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT(periode1,'%Y') tahun, '11' kodeid, 'HO' divisi, sum(jumlah) as jumlah 
                 from hrd.kas 
                 where DATE_FORMAT(periode1,'%Y')='$ptahun' AND DATE_FORMAT(periode1,'%Y-%m') <= '$pbulan' 
                 group by 1,2,3";
        
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $periodeby_br="tgl";
        if ($espd=="A") {
            $periodeby_br="tgltrans";
        }
        //CLAIM KLAIM DISCOUNT
	$query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '09' kodeid, 'EAGLE' divisi, sum(jumlah) as jumlah 
                 from hrd.klaim where DATE_FORMAT($periodeby_br,'%Y')='$ptahun' AND DATE_FORMAT($periodeby_br,'%Y-%m') <= '$pbulan' AND IFNULL(pengajuan,'') <> 'OTC' 
                 group by 1,2,3";
        
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah) FROM $tmp03 b WHERE a.kodeid=b.kodeid)");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah) FROM $tmp02 b WHERE a.kodeid=b.kodeid)");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=IFNULL(jumlah2,0)-IFNULL(jumlah1,0)");
        
        mysqli_query($cnit, "CREATE TEMPORARY TABLE $tmp05 (select * from $tmp01)");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp05 b WHERE grp=1) WHERE nourut=5");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp05 b WHERE grp=1) WHERE nourut=5");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp05 b WHERE grp=1) WHERE nourut=5");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp05 b WHERE grp=2) WHERE nourut=10");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp05 b WHERE grp=2) WHERE nourut=10");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp05 b WHERE grp=2) WHERE nourut=10");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp05 b WHERE grp=3) WHERE nourut=14");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp05 b WHERE grp=3) WHERE nourut=14");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp05 b WHERE grp=3) WHERE nourut=14");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp05 b WHERE grp=4) WHERE nourut=20");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp05 b WHERE grp=4) WHERE nourut=20");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp05 b WHERE grp=4) WHERE nourut=20");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp05 b WHERE IFNULL(kodeid,'') <> '' and IFNULL(kodeid,'') not in ('01','02','03')) WHERE nourut=21");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp05 b WHERE IFNULL(kodeid,'') <> '' and IFNULL(kodeid,'') not in ('01','02','03')) WHERE nourut=21");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp05 b WHERE IFNULL(kodeid,'') <> '' and IFNULL(kodeid,'') not in ('01','02','03')) WHERE nourut=21");
        
        
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
        mysqli_query($cnit, "CREATE TEMPORARY TABLE $tmp05 (select * from $tmp01)");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp05 b WHERE nourut in (5,21)) WHERE nourut=22");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp05 b WHERE nourut in (5,21)) WHERE nourut=22");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp05 b WHERE nourut in (5,21)) WHERE nourut=22");
        
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
        
        //SALES
	$query = "select *from dbmaster.sales where DATE_FORMAT(bulan,'%Y')='$ptahun' AND DATE_FORMAT(bulan,'%Y-%m') <= '$pbulan'";
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.value_sales) FROM $tmp02 b) WHERE nourut=23");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.value_target) FROM $tmp02 b) WHERE nourut=23");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=IFNULL(jumlah2,0)-IFNULL(jumlah1,0) WHERE nourut=23");
        
        
        $jsales1=0;
        $jsales2=0;
        $jsales3=0;
        $query = "select * FROM $tmp01 WHERE nourut=23";
        $tampil=mysqli_query($cnit, $query);
        while ($ro= mysqli_fetch_array($tampil)) {
            $jsales1=$ro['jumlah1'];
            $jsales2=$ro['jumlah2'];
            $jsales3=$ro['jumlah3'];
            if (empty($jsales1)) $jsales1=0;
            if (empty($jsales2)) $jsales2=0;
            if (empty($jsales3)) $jsales3=0;
        }
        if ((DOUBLE)$jsales1>0){
            mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio1=ifnull(a.jumlah1,0)/$jsales1*100");
        }
        if ((DOUBLE)$jsales2>0){
            mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio2=ifnull(a.jumlah2,0)/$jsales2*100");
        }
        if ((DOUBLE)$jsales2>0){
            mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio3=ifnull(a.jumlah3,0)/$jsales2*100");
        }
        
    ?>
    
    <style>
        .tjudul {
            font-family: "times new roman", Arial, Georgia, serif;
            margin-left:10px;
            margin-right:10px;
        }
        .tjudul td {
            padding: 4px;
            font-size: 15px;
        }
        #datatable2 {
            font-family: "times new roman", Arial, Georgia, serif;
            margin-left:10px;
            margin-right:10px;
        }
        #datatable2 th, #datatable2 td {
            padding: 10px;
        }
        #datatable2 thead{
            background-color:#cccccc; 
            font-size: 18px;
        }
        #datatable2 tbody{
            font-size: 16px;
        }
    </style>
    
    <?PHP
        $tglbulanbesar=strtoupper($pblnthn);
        echo "<table class='tjudul' width='100%'>";
        echo "<tr> <td width='400px' colspan='2'>REALISASI BIAYA MARKETING VS BUDGET s/d. $tglbulanbesar</td> </tr>";
        echo "<tr> <td width='200px' colspan='2'>PT. SURYA DERMATO MEDICA LABORATORIES </td></tr>";
        echo "<tr> <td width='200px' colspan='2'>DIVISI ETHICAL</td></tr>";
        echo "</table>";
        echo "<br/>&nbsp;";
        
    ?>
    
    <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
        <thead>
            <tr>
            <th align="center" rowspan="2">NO</th>
            <th align="center" rowspan="2">KETERANGAN</th>
            <th align="center" colspan="2"><?PHP echo "$ptahun"; ?></th>
            <th align="center" colspan="2"><?PHP echo "$ptahun"; ?></th>
            <th align="center" colspan="2"><?PHP echo "$ptahun"; ?></th>
            </tr>
            
            <tr>
            <th align="center">REALISASI BUDGET</th>
            <th align="center">COST <br/>RATIO</th>
            <th align="center">USULAN BUDGET</th>
            <th align="center">COST <br/>RATIO</th>
            <th align="center">SISA BUDGET</th>
            <th align="center">COST <br/>RATIO</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $query = "select * FROM $tmp01 order by nourut";
            $tampil=mysqli_query($cnit, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $pnourut=$row['nourut'];
                $pno=$row['no'];
                $pjudul=$row['keterangan'];
                
                $pjumlah1=$row['jumlah1'];
                $pjumlah2=$row['jumlah2'];
                $pjumlah3=$row['jumlah3'];

                $pratio1=ROUND($row['ratio1'],2);
                $pratio2=ROUND($row['ratio2'],2);
                $pratio3=ROUND($row['ratio3'],2);

                


                $pjumlah1=number_format($pjumlah1,0,",",",");
                $pjumlah2=number_format($pjumlah2,0,",",",");
                $pjumlah3=number_format($pjumlah3,0,",",",");
                
                if ($pjumlah1==0) $pjumlah1="";
                if ($pjumlah2==0) $pjumlah2="";
                if ($pjumlah3==0) $pjumlah3="";
                
                if ($pratio1==0) $pratio1="";
                if ($pratio2==0) $pratio2="";
                if ($pratio3==0) $pratio3="";
                
                echo "<tr>";
                echo "<td nowrap>$pno</td>";
                echo "<td nowrap>$pjudul</td>";
                if ((int)$pnourut==5 OR (int)$pnourut==10 OR (int)$pnourut==14 OR (int)$pnourut==20 OR (int)$pnourut==21 OR (int)$pnourut==22 OR (int)$pnourut==23) {
                    echo "<td nowrap align='right'><b>$pjumlah1</b></td>";
                    echo "<td nowrap align='right'><b>$pratio1</b></td>";
                    echo "<td nowrap align='right'><b>$pjumlah2</b></td>";
                    echo "<td nowrap align='right'><b>$pratio2</b></td>";
                    
                    echo "<td nowrap align='right'><b>$pjumlah3</b></td>";
                    echo "<td nowrap align='right'><b>$pratio3</b></td>";
                }else{
                    echo "<td nowrap align='right'>$pjumlah1</td>";
                    echo "<td nowrap align='right'>$pratio1</td>";
                    echo "<td nowrap align='right'>$pjumlah2</td>";
                    echo "<td nowrap align='right'>$pratio2</td>";
                    
                    echo "<td nowrap align='right'>$pjumlah3</td>";
                    echo "<td nowrap align='right'>$pratio3</td>";
                }
                echo "</tr>";
                
                if ((int)$pnourut==5 OR (int)$pnourut==10 OR (int)$pnourut==14 OR (int)$pnourut==20 OR (int)$pnourut==21 OR (int)$pnourut==23) {
                    echo "<tr>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "</tr>";
                }
                
            }
            
            ?>
        </tbody>
    </table>
    
    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
    <?PHP
    hapusdata:
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp03");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp04");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
        
        mysqli_close($cnit);
    ?>
</body>
</html>