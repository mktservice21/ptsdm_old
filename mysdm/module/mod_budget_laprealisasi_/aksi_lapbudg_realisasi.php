<?PHP
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REALISASI BUDGET MARKETING.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>

<html>
<head>
    <title>REALISASI BUDGET MARKETING</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Apr 2019 1:00:00 GMT">
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
        $tmp01 =" dbtemp.RPTREKBMA01_".$_SESSION['USERID']."_$now ";
        $tmp02 =" dbtemp.RPTREKBMA02_".$_SESSION['USERID']."_$now ";
        $tmp03 =" dbtemp.RPTREKBMA03_".$_SESSION['USERID']."_$now ";
        $tmp04 =" dbtemp.RPTREKBMA04_".$_SESSION['USERID']."_$now ";

        $tgl02=$_POST['tahun'];
        $tgl01=$_POST['tahun']-2;
        
        $nthn3=$_POST['tahun'];
        $nthn2=$_POST['tahun']-1;
        $nthn1=$_POST['tahun']-2;
        
        $espd = $_POST['radio1'];
        
        $query = "create TEMPORARY table $tmp01 (SELECT * FROM dbmaster.t_budget_realisasi_lap)"; 
        mysqli_query($cnit, $query);
        
        $query = "SELECT
            DATE_FORMAT(bulan,'%Y') tahun,
            a.divisi,
            a.kodeid,
            b.nama,
            SUM(a.jumlah) jumlah,
            SUM(a.ratio) ratio
            FROM
            dbmaster.t_budget_realisasi AS a
            JOIN dbmaster.t_budget_kode AS b ON a.kodeid = b.kodeid 
            WHERE DATE_FORMAT(bulan,'%Y') BETWEEN '$tgl01' AND '$tgl02'";
            $query .= "GROUP BY 1,2,3,4";
        
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnit, $query);
        
        /*
        //biaya rutin
        $query = "INSERT INTO $tmp02 (tahun, kodeid, divisi, jumlah) select DATE_FORMAT(c.tgl,'%Y') tahun, '01' kodeid, a.divisi, sum(a.jumlah) jumlah from dbmaster.t_suratdana_br_d a JOIN 
            (select DISTINCT idinput, kodeinput FROM dbmaster.t_suratdana_br1 ) b
            on a.idinput=b.idinput JOIN dbmaster.t_suratdana_br c on a.idinput=c.idinput AND 
            b.idinput=c.idinput
            WHERE b.kodeinput='F' and IFNULL(c.stsnonaktif,'') <> 'Y' AND DATE_FORMAT(c.tgl,'%Y') BETWEEN '$tgl01' AND '$tgl02'
            GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        
        //biaya luar kota
        $query = "INSERT INTO $tmp02 (tahun, kodeid, divisi, jumlah) select DATE_FORMAT(c.tgl,'%Y') tahun, '02' kodeid, a.divisi, sum(a.jumlah) jumlah from dbmaster.t_suratdana_br_d a JOIN 
            (select DISTINCT idinput, kodeinput FROM dbmaster.t_suratdana_br1 ) b
            on a.idinput=b.idinput JOIN dbmaster.t_suratdana_br c on a.idinput=c.idinput AND 
            b.idinput=c.idinput
            WHERE b.kodeinput='I' and IFNULL(c.stsnonaktif,'') <> 'Y' AND DATE_FORMAT(c.tgl,'%Y') BETWEEN '$tgl01' AND '$tgl02'
            GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        */
        
    //biaya rutin
    $query = "INSERT INTO $tmp02 (tahun, kodeid, divisi, jumlah)
        select YEAR(b.bulan) tahun, '01' kodeid, b.divisi, sum(a.rptotal) jumlah 
        from dbmaster.t_brrutin1 a JOIN dbmaster.t_brrutin0 b ON a.idrutin=b.idrutin
        WHERE IFNULL(b.stsnonaktif,'')<>'Y' AND IFNULL(b.tgl_fin,'')<>'' AND kode=1 AND b.divisi<>'OTC' 
        and YEAR(b.bulan)>='2019'
        GROUP BY 1,2,3";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        
    //biaya luar kota
    $query = "INSERT INTO $tmp02 (tahun, kodeid, divisi, jumlah)
        select YEAR(b.bulan) tahun, '02' kodeid, b.divisi, sum(a.rptotal) jumlah 
        from dbmaster.t_brrutin1 a JOIN dbmaster.t_brrutin0 b ON a.idrutin=b.idrutin
        WHERE IFNULL(b.stsnonaktif,'')<>'Y' AND IFNULL(b.tgl_fin,'')<>'' AND kode=2 AND b.divisi<>'OTC' 
        and YEAR(b.bulan) >='2019' AND YEAR(b.bulan) BETWEEN '$tgl01' AND '$tgl02' 
        GROUP BY 1,2,3";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
        
        $periodeby_br="tgl";
        if ($espd=="A") {
            $periodeby_br="tgltrans";
        }
    
	$query = "select * from hrd.br0
                 where DATE_FORMAT($periodeby_br,'%Y')>='2019' AND DATE_FORMAT($periodeby_br,'%Y') BETWEEN '$tgl01' AND '$tgl02' and retur <> 'Y' and batal <>'Y'";
        $query = "create TEMPORARY table $tmp04 ($query)";
        mysqli_query($cnit, $query);
        
        if ($espd=="A") {
            $query = "UPDATE $tmp04 SET jumlah=jumlah1 WHERE IFNULL(jumlah1,0)<>0";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        }
    
        //DSS
        $query = "INSERT INTO $tmp02 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '04' kodeid, divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE (kode='700-01-04' or kode='700-02-04' or kode='700-04-04')
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        
        //DCC
        $query = "INSERT INTO $tmp02 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '05' kodeid, divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE (kode='700-01-03' or kode='700-02-03' or kode='700-04-03')
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        
        //Gimmic GIMIK GIMMIC PROMOSI
        $query = "INSERT INTO $tmp02 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '06' kodeid, divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE (kode='700-01-01' or kode='700-02-01' or kode='700-04-01')
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        
        
        //IKLAN
        $query = "INSERT INTO $tmp02 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '07' kodeid, divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE kode='700-01-06' or kode='700-04-06' 
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        
        //SIMPOSIUM
        $query = "INSERT INTO $tmp02 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '08' kodeid, divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE (kode='700-01-08' or kode='700-02-05' OR kode='700-04-05')
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        
        // HO
        $query = "INSERT INTO $tmp02 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '10' kodeid, divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE (kode='700-01-099' or kode='700-02-099' or kode='700-04-099')
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        
        //KAS KECIL
	$query = "INSERT INTO $tmp02 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT(periode1,'%Y') tahun, '11' kodeid, 'HO' divisi, sum(jumlah) as jumlah 
                 from hrd.kas 
                 where DATE_FORMAT(periode1,'%Y')>='2019' AND DATE_FORMAT(periode1,'%Y') BETWEEN '$tgl01' AND '$tgl02' 
                 group by 1,2,3";
        mysqli_query($cnit, $query);
        
        
        $periodeby_br="tgl";
        if ($espd=="A") {
            $periodeby_br="tgltrans";
        }
        //CLAIM KLAIM DISCOUNT
	$query = "INSERT INTO $tmp02 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '09' kodeid, 'EAGLE' divisi, sum(jumlah) as jumlah 
                 from hrd.klaim where DATE_FORMAT($periodeby_br,'%Y')>='2019' AND DATE_FORMAT($periodeby_br,'%Y') BETWEEN '$tgl01' AND '$tgl02'
                 group by 1,2,3";
        mysqli_query($cnit, $query);
        
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah) FROM $tmp02 b WHERE a.kodeid=b.kodeid AND tahun='$nthn1')");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio1=(select sum(b.ratio) FROM $tmp02 b WHERE a.kodeid=b.kodeid AND tahun='$nthn1')");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah) FROM $tmp02 b WHERE a.kodeid=b.kodeid AND tahun='$nthn2')");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio2=(select sum(b.ratio) FROM $tmp02 b WHERE a.kodeid=b.kodeid AND tahun='$nthn2')");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah) FROM $tmp02 b WHERE a.kodeid=b.kodeid AND tahun='$nthn3')");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio3=(select sum(b.ratio) FROM $tmp02 b WHERE a.kodeid=b.kodeid AND tahun='$nthn3')");
        
        
        mysqli_query($cnit, "CREATE TEMPORARY TABLE $tmp03 (select * from $tmp01)");
        
        
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp03 b WHERE grp=1) WHERE nourut=5");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp03 b WHERE grp=1) WHERE nourut=5");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp03 b WHERE grp=1) WHERE nourut=5");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio1=(select sum(b.ratio1) FROM $tmp03 b WHERE grp=1) WHERE nourut=5");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio2=(select sum(b.ratio2) FROM $tmp03 b WHERE grp=1) WHERE nourut=5");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio3=(select sum(b.ratio3) FROM $tmp03 b WHERE grp=1) WHERE nourut=5");
        
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp03 b WHERE grp=2) WHERE nourut=10");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp03 b WHERE grp=2) WHERE nourut=10");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp03 b WHERE grp=2) WHERE nourut=10");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio1=(select sum(b.ratio1) FROM $tmp03 b WHERE grp=2) WHERE nourut=10");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio2=(select sum(b.ratio2) FROM $tmp03 b WHERE grp=2) WHERE nourut=10");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio3=(select sum(b.ratio3) FROM $tmp03 b WHERE grp=2) WHERE nourut=10");
        
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp03 b WHERE grp=3) WHERE nourut=14");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp03 b WHERE grp=3) WHERE nourut=14");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp03 b WHERE grp=3) WHERE nourut=14");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio1=(select sum(b.ratio1) FROM $tmp03 b WHERE grp=3) WHERE nourut=14");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio2=(select sum(b.ratio2) FROM $tmp03 b WHERE grp=3) WHERE nourut=14");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio3=(select sum(b.ratio3) FROM $tmp03 b WHERE grp=3) WHERE nourut=14");
        
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp03 b WHERE grp=4) WHERE nourut=20");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp03 b WHERE grp=4) WHERE nourut=20");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp03 b WHERE grp=4) WHERE nourut=20");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio1=(select sum(b.ratio1) FROM $tmp03 b WHERE grp=4) WHERE nourut=20");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio2=(select sum(b.ratio2) FROM $tmp03 b WHERE grp=4) WHERE nourut=20");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio3=(select sum(b.ratio3) FROM $tmp03 b WHERE grp=4) WHERE nourut=20");
        
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp03 b WHERE IFNULL(kodeid,'') <> '' and IFNULL(kodeid,'') not in ('01','02','03')) WHERE nourut=21");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp03 b WHERE IFNULL(kodeid,'') <> '' and IFNULL(kodeid,'') not in ('01','02','03')) WHERE nourut=21");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp03 b WHERE IFNULL(kodeid,'') <> '' and IFNULL(kodeid,'') not in ('01','02','03')) WHERE nourut=21");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio1=(select sum(b.ratio1) FROM $tmp03 b WHERE IFNULL(kodeid,'') <> '') WHERE nourut=21");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio2=(select sum(b.ratio2) FROM $tmp03 b WHERE IFNULL(kodeid,'') <> '') WHERE nourut=21");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio3=(select sum(b.ratio3) FROM $tmp03 b WHERE IFNULL(kodeid,'') <> '') WHERE nourut=21");
        
        
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp03");
        mysqli_query($cnit, "CREATE TEMPORARY TABLE $tmp03 (select * from $tmp01)");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp03 b WHERE nourut in (5,21)) WHERE nourut=22");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp03 b WHERE nourut in (5,21)) WHERE nourut=22");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp03 b WHERE nourut in (5,21)) WHERE nourut=22");
        
        
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp04");
        
        //SALES dari YTD sls
	$query = "select *from dbmaster.mr_sales2 where DATE_FORMAT(tgljual,'%Y')>='2019' AND DATE_FORMAT(tgljual,'%Y') BETWEEN '$tgl01' AND '$tgl02'";
        $query = "create TEMPORARY table $tmp04 ($query)";
        mysqli_query($cnit, $query);
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.hna*b.qty) FROM $tmp04 b WHERE DATE_FORMAT(tgljual,'%Y')='$nthn1') WHERE nourut=23");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.hna*b.qty) FROM $tmp04 b WHERE DATE_FORMAT(tgljual,'%Y')='$nthn2') WHERE nourut=23");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.hna*b.qty) FROM $tmp04 b WHERE DATE_FORMAT(tgljual,'%Y')='$nthn3') WHERE nourut=23");
        
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp03");
        mysqli_query($cnit, "CREATE TEMPORARY TABLE $tmp03 (select * from $tmp01)");
        
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
            mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio1=a.jumlah1/$jsales1*100");
        }
        if ((DOUBLE)$jsales2>0){
            mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio2=a.jumlah2/$jsales2*100");
        }
        if ((DOUBLE)$jsales3>0){
            mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio3=a.jumlah3/$jsales3*100");
        }
        
    ?>
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
    <?PHP
        echo "<table class='tjudul' width='100%'>";
        echo "<tr> <td width='300px'>REALISASI BUDGET MARKETING THN  $tgl02</td> <td> </td> <td></td> </tr>";
        echo "<tr> <td width='200px'>PT. SURYA DERMATO MEDICA LABORATORIES </td> <td> </td> <td></td> </tr>";
        echo "<tr> <td width='200px'>DIVISI ETHICAL</td> <td> </td> <td></td> </tr>";
        echo "</table>";
        echo "<br/>&nbsp;";
      
    
    ?>
    
    <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
        <thead>
            <tr>
            <th align="center" rowspan="2">NO</th>
            <th align="center" rowspan="2">KETERANGAN</th>
            <th align="center" colspan="2">REALISASI <?PHP echo "$nthn1"; ?></th>
            <th align="center" colspan="2">REALISASI <?PHP echo "$nthn2"; ?></th>
            <th align="center" colspan="2">REALISASI <?PHP echo "$nthn3"; ?></th>
            </tr>
            
            <tr>
            <th align="center">(EAGLE+PIGEON+PEACOCK)</th>
            <th align="center">COST <br/>RATIO</th>
            <th align="center">(EAGLE+PIGEON+PEACOCK)</th>
            <th align="center">COST <br/>RATIO</th>
            <th align="center">(EAGLE+PIGEON+PEACOCK)</th>
            <th align="center">COST <br/>RATIO</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $query = "select * FROM $tmp01 order by nourut";
            $tampil=mysqli_query($cnit, $query);
            while ($row= mysqli_fetch_array($tampil)) {
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

                echo "<td nowrap align='right'>$pjumlah1</td>";
                echo "<td nowrap align='right'>$pratio1</td>";
                echo "<td nowrap align='right'>$pjumlah2</td>";
                echo "<td nowrap align='right'>$pratio2</td>";
                echo "<td nowrap align='right'>$pjumlah3</td>";
                echo "<td nowrap align='right'>$pratio3</td>";
                
                echo "</tr>";
                
                
            }
            
            ?>
        </tbody>
    </table>
    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
    <?PHP
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp03");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp04");
        
        mysqli_close($cnit);
    ?>
</body>
</html>