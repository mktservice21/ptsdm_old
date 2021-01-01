<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    
    session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
	
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REPORT REALISASI BIAYA MARKETING.xls");
    }
    
    include("config/koneksimysqli.php");
    $cnit=$cnmy;
    
    $printdate= date("d/m/Y");
    
    $picardid=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];
    
    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmplapglreak00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmplapglreak01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmplapglreak02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmplapglreak03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmplapglreak04_".$puserid."_$now ";
    $tmp05 =" dbtemp.tmplapglreak05_".$puserid."_$now ";
    $tmp06 =" dbtemp.tmplapglreak06_".$puserid."_$now ";
    $tmp07 =" dbtemp.tmplapglreak07_".$puserid."_$now ";
    
?>

<?PHP
    include "config/fungsi_combo.php";
    include("config/common.php");
    
    $periode = $_POST['bulan1'];
    
    $query = "select distinct b.idinput, b.divisi, b.nodivisi, a.kodeinput, a.bridinput from dbmaster.t_suratdana_br1 a "
            . " JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput WHERE "
            . " b.stsnonaktif<>'Y'";
    $query = "create TEMPORARY table $tmp00 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    // sales dbmaster.mr_sales2 YEAR(tgljual)='$periode'
    // sales dbmaster.sales YEAR(bulan)='$periode'
    //$query = "select date_format(tgljual,'%Y-%m') bulan, divprodid, sum(qty*hna) as rpsales from dbmaster.mr_sales2 WHERE YEAR(tgljual)='$periode' GROUP BY 1,2";
    $query = "select date_format(bulan,'%Y-%m') bulan, divprodid, sum(value_sales) as rpsales from dbmaster.sales WHERE YEAR(bulan)='$periode' GROUP BY 1,2";
    $query = "create TEMPORARY table $tmp04 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //BR
    $query = "select brId, tgl, tgltrans, divprodid, COA4, jumlah, jumlah1, CAST('' as CHAR(50)) as nodivisi "
            . " from hrd.br0 WHERE IFNULL(batal,'')<>'Y' AND "
            . " brId NOT IN (select DISTINCT IFNULL(brId,'') FROM hrd.br0_reject) AND "
            . " YEAR(tgltrans)='$periode'";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 SET jumlah=jumlah1 WHERE IFNULL(jumlah1,0)<>0";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    $query = "UPDATE $tmp02 a JOIN (select distinct nodivisi, bridinput FROM $tmp00 WHERE kodeinput IN ('A', 'B', 'C') AND divisi<>'OTC') b on a.brId=b.bridinput "
            . " SET a.nodivisi=b.nodivisi"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "DELETE FROM $tmp02 WHERE IFNULL(nodivisi,'')='' AND YEAR(tgl)>='2020'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
    
    
    $query = "select tgl, tgltrans, divprodid, COA4, sum(jumlah) as jumlah, sum(jumlah1) as jumlah1 from $tmp02 GROUP BY 1,2,3,4";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
    
    //KLAIM
    $query = "select klaimId, tgl, tgltrans, IFNULL(DIVISI,'EAGLE') DIVISI, COA4, jumlah, jumlah jumlah1, CAST('' as CHAR(50)) as nodivisi "
            . " from hrd.klaim WHERE "
            . " klaimId not in (SELECT DISTINCT ifnull(klaimId,'') from hrd.klaim_reject) AND "
            . " YEAR(tgltrans) = '$periode' ";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 a JOIN (select distinct nodivisi, bridinput FROM $tmp00 WHERE kodeinput IN ('E') AND divisi='EAGLE') b on a.klaimId=b.bridinput "
            . " SET a.nodivisi=b.nodivisi"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "DELETE FROM $tmp02 WHERE IFNULL(nodivisi,'')='' AND YEAR(tgl)>='2020'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    $query = "INSERT INTO $tmp01 (tgl, tgltrans, divprodid, COA4, jumlah, jumlah1) "
            . " select tgl, tgltrans, DIVISI, COA4, SUM(jumlah) as jumlah, SUM(jumlah1) as jumlah1 FROM $tmp02 GROUP BY 1,2,3,4";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
    
    
    //KAS
        //kas uang muka COA
        $query_XXX = "select kasId, periode1 tgl, periode1 tgltrans, 'HO' as DIVISI, '105-02' as COA4, jumlah, jumlah jumlah1, CAST('' as CHAR(50)) as nodivisi "
                . " FROM hrd.kas WHERE YEAR(periode1)= '$periode'";
        
        
    $query = "select a.kasId, a.periode1 tgl, a.periode1 tgltrans, e.DIVISI2 DIVISI, b.COA4, a.jumlah, a.jumlah jumlah1, CAST('' as CHAR(50)) as nodivisi "
            . " from hrd.kas as a LEFT JOIN dbmaster.posting_coa_kas b "
            . " ON a.kode=b.kodeid LEFT JOIN dbmaster.coa_level4 c "
            . " ON b.COA4=c.COA4 LEFT JOIN dbmaster.coa_level3 d "
            . " ON c.COA3=d.COA3 LEFT JOIN dbmaster.coa_level2 e ON d.COA2=e.COA2 WHERE "
            . " YEAR(a.periode1)='$periode'";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 SET COA4='105-02' WHERE IFNULL(COA4,'')=''";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 a JOIN (select distinct nodivisi, bridinput FROM $tmp00 WHERE kodeinput IN ('T', 'K')) b on a.kasId=b.bridinput "
            . " SET a.nodivisi=b.nodivisi"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "DELETE FROM $tmp02 WHERE IFNULL(nodivisi,'')='' AND YEAR(tgl)>='2020'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    $query = "INSERT INTO $tmp01 (tgl, tgltrans, divprodid, COA4, jumlah, jumlah1) "
            . " select tgl, tgltrans, DIVISI, COA4, SUM(jumlah) as jumlah, SUM(jumlah1) as jumlah1 FROM $tmp02 GROUP BY 1,2,3,4";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
    
    //KAS BON
    $query="select idkasbon, tgl, tgl as tgltrans, 'HO' as DIVISI, '105-02' as COA4, jumlah, jumlah as jumlah1, CAST('' as CHAR(50)) as nodivisi "
            . " FROM dbmaster.t_kasbon WHERE IFNULL(stsnonaktif,'')<>'Y' AND YEAR(tgl) = '$periode'";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 a JOIN (select distinct nodivisi, bridinput FROM $tmp00 WHERE kodeinput IN ('T', 'K')) b on a.idkasbon=b.bridinput "
            . " SET a.nodivisi=b.nodivisi"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "DELETE FROM $tmp02 WHERE IFNULL(nodivisi,'')='' AND YEAR(tgl)>='2020'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    $query = "INSERT INTO $tmp01 (tgl, tgltrans, divprodid, COA4, jumlah, jumlah1) "
            . " select tgl, tgltrans, DIVISI, COA4, SUM(jumlah) as jumlah, SUM(jumlah1) as jumlah1 FROM $tmp02 GROUP BY 1,2,3,4";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
    
    //BR OTC
    $query = "select brOtcId, tglbr as tgl, tgltrans, 'OTC' DIVISI, COA4, jumlah, realisasi as jumlah1, CAST('' as CHAR(50)) as nodivisi "
            . " from hrd.br_otc WHERE IFNULL(batal,'')<>'Y' AND "
            . " brOtcId NOT IN (select DISTINCT IFNULL(brOtcId,'') FROM hrd.br_otc_reject) AND "
            . " YEAR(tgltrans) ='$periode'";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 a JOIN (select distinct nodivisi, bridinput FROM $tmp00 WHERE kodeinput IN ('D') AND divisi='OTC') b on a.brOtcId=b.bridinput "
            . " SET a.nodivisi=b.nodivisi"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "DELETE FROM $tmp02 WHERE IFNULL(nodivisi,'')='' AND YEAR(tgl)>='2020'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    $query = "INSERT INTO $tmp01 (tgl, tgltrans, divprodid, COA4, jumlah, jumlah1) "
            . " select tgl, tgltrans, DIVISI, COA4, SUM(jumlah) as jumlah, SUM(jumlah1) as jumlah1 FROM $tmp02 GROUP BY 1,2,3,4";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
    
    //RUTIN
    $query = "select bulan, idrutin, divisi, divi, kode "
            . " from dbmaster.t_brrutin0 WHERE "
            . " IFNULL(stsnonaktif,'') <> 'Y' AND "
            . " YEAR(bulan)='$periode'";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select distinct idrutin, divisi from dbmaster.t_brrutin_ca_close WHERE idrutin IN (select distinct IFNULL(idrutin,'') from $tmp02)";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 a JOIN (select distinct idrutin, divisi from $tmp03) b on a.idrutin=b.idrutin SET a.divisi=b.divisi WHERE a.divisi<>'OTC' and a.kode=2";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
    
    //, CAST(NULL as date) bulan, CAST('' as CHAR(50)) as divisi
    $query = "select idrutin, coa, nobrid, rptotal  "
            . " FROM dbmaster.t_brrutin1 WHERE "
            . " idrutin in (select distinct IFNULL(idrutin,'') FROM $tmp02)";
    $query = "create TEMPORARY table $tmp05 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp05 a JOIN $tmp02 b on a.idrutin=b.idrutin SET a.bulan=b.bulan, a.divisi=b.divisi";
    //mysqli_query($cnmy, $query);
    //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //echo "ada"; goto hapusdata;
    
    $query_XXX = "select nobrid, idrutin, bulan tgl, bulan tgltrans, divisi DIVISI, coa COA4, rptotal as jumlah, rptotal as jumlah1, CAST('' as CHAR(50)) as nodivisi FROM $tmp05";
    $query = "select a.nobrid, a.idrutin, b.bulan tgl, b.bulan tgltrans, b.divisi DIVISI, a.coa COA4, a.rptotal as jumlah, a.rptotal as jumlah1, CAST('' as CHAR(50)) as nodivisi "
            . " from $tmp05 a JOIN $tmp02 b on a.idrutin=b.idrutin";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select * from dbmaster.posting_coa_rutin";
    $query = "create TEMPORARY table $tmp06 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp03 a JOIN $tmp06 b on a.DIVISI=b.divisi AND a.nobrid=b.nobrid SET a.COA4=b.COA4 WHERE IFNULL(a.DIVISI,'')<>''";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
    $query = "UPDATE $tmp03 a JOIN (select distinct nodivisi, bridinput FROM $tmp00 WHERE kodeinput IN ('F', 'I')) b on a.idrutin=b.bridinput "
            . " SET a.nodivisi=b.nodivisi"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "DELETE FROM $tmp03 WHERE IFNULL(nodivisi,'')='' AND YEAR(tgl)>='2020'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    $query = "INSERT INTO $tmp01 (tgl, tgltrans, divprodid, COA4, jumlah, jumlah1) "
            . " select tgl, tgltrans, DIVISI, COA4, SUM(jumlah) as jumlah, SUM(jumlah1) as jumlah1 FROM $tmp03 GROUP BY 1,2,3,4";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
    
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
    
    
    $urut=2;
    for ($x=1;$x<=12;$x++) {
        $jml=  strlen($x);
        $awal=$urut-$jml;
        $nbulan=$periode."-".str_repeat("0", $awal).$x;
        $nfield="B".$x;
        
        $query = "UPDATE $tmp03 a SET a.$nfield=(SELECT SUM(b.jumlah) FROM $tmp01 b WHERE a.COA4=b.COA4 AND DATE_FORMAT(tgltrans, '%Y-%m')='$nbulan')";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $nfield="S".$x;
        $query = "UPDATE $tmp03 a SET a.$nfield=(SELECT SUM(b.rpsales) FROM $tmp04 b WHERE a.DIVISI=b.divprodid AND b.bulan='$nbulan')";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }
    
    
?>

<HTML>
<HEAD>
    <title>REPORT REALISASI BIAYA MARKETING</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Mei 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <!--<link href="css/laporanbaru.css" rel="stylesheet">-->
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        
        <!-- Bootstrap -->
        <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    
        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">


        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">


        <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        
		
		
		
		<!-- jQuery -->
        <script src="vendors/jquery/dist/jquery.min.js"></script>
        
        
    <?PHP } ?>
    
</HEAD>
<BODY class="nav-md">
    
<?PHP if ($ppilihrpt!="excel") { ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?PHP } ?>
    
    
<div id='n_content'>
    
    <center><div class='h1judul'>REPORT REALISASI BIAYA MARKETING</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <tr><td>Tahun</td><td>:</td><td><?PHP echo "<b>$periode</b>"; ?></td></tr>
            <tr class='miring text2'><td>view date</td><td>:</td><td><?PHP echo "$printdate"; ?></td></tr>
        </table>
    </div>
    <div class="clearfix"></div>
    <hr/>
   
    
    
        <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
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


                    $urut=2;
                    for ($x=1;$x<=12;$x++) {
                        $ztotalbr[$x]=0;
                        $ztotalsls[$x]=0;

                        $jml=  strlen($x);
                        $awal=$urut-$jml;
                        $zbulan=$periode."-".str_repeat("0", $awal).$x;


                        //cari total br
                        $query = "select IFNULL(sum(jumlah),0) jumlah From $tmp02 WHERE divprodid='$divisi' AND DATE_FORMAT(tgltrans,'%Y-%m')='$zbulan'";
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


                $urut=2;
                for ($x=1;$x<=12;$x++) {
                    $ztotalbr[$x]=0;
                    $ztotalsls[$x]=0;

                    $jml=  strlen($x);
                    $awal=$urut-$jml;
                    $zbulan=$periode."-".str_repeat("0", $awal).$x;


                    //cari total br
                    $query = "select IFNULL(sum(jumlah),0) jumlah From $tmp02 WHERE DATE_FORMAT(tgltrans,'%Y-%m')='$zbulan'";
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

    
</div>
    
    
    <?PHP if ($ppilihrpt!="excel") { ?>

        
        <!-- Bootstrap -->
        <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    
        <!-- Datatables -->
		
        <script src="vendors/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
        <script src="vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
        <script src="vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
        <script src="vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
        <script src="vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
        <script src="vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
        <script src="vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
        <script src="vendors/jszip/dist/jszip.min.js"></script>
        <script src="vendors/pdfmake/build/pdfmake.min.js"></script>
        <script src="vendors/pdfmake/build/vfs_fonts.js"></script>

        
        
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

        <style>
            #n_content {
                color:#000;
                font-family: "Arial";
                margin: 5px 20px 20px 20px;
                /*overflow-x:auto;*/
            }
            
            .h1judul {
              color: blue;
              font-family: verdana;
              font-size: 140%;
              font-weight: bold;
            }
            table.tbljudul {
                font-size : 15px;
            }
            table.tbljudul tr td {
                padding: 1px;
                font-family : "Arial, Verdana, sans-serif";
            }
            .tebal {
                 font-weight: bold;
            }
            .miring {
                 font-style: italic;
            }
            table.tbljudul tr.text2 {
                font-size : 13px;
            }
            
            table {
                text-align: left;
                position: relative;
                border-collapse: collapse;
                background-color:#FFFFFF;
            }

            th {
                background: white;
                position: sticky;
                top: 0;
                box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
                z-index:1;
            }

            .th2 {
                background: white;
                position: sticky;
                top: 23;
                box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
                border-top: 1px solid #000;
            }
        </style>
    
        <style>
            
            .divnone {
                display: none;
            }
            #mydatatable1, #mydatatable2 {
                color:#000;
                font-family: "Arial";
            }
            #mydatatable1 th, #mydatatable2 th {
                font-size: 12px;
            }
            #mydatatable1 td, #mydatatable2 td { 
                font-size: 11px;
            }
        </style>
        
    <?PHP }else{ ?>
        <style>
            .h1judul {
              font-size: 140%;
              font-weight: bold;
            }
        </style>
    <?PHP } ?>
        
        
</BODY>


    <script>
        // SCROLL
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
        // END SCROLL
    </script>
    
    
    
</HTML>



<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp05");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp06");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp07");
    mysqli_close($cnmy);
?>