<?php
    function BuatFormatNum($prp, $ppilih) {
        if (empty($prp)) $prp=0;
        
        $numrp=$prp;
        if ($ppilih=="1") $numrp=number_format($prp,0,",",",");
        elseif ($ppilih=="2") $numrp=number_format($prp,0,".",".");
            
        return $numrp;
    }
    
    $ppilformat="1";
    
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
        header("Content-Disposition: attachment; filename=Sales Vs Discount By Distributor.xls");
    }
    
    include("config/koneksimysqli.php");
    
    
    $printdate= date("d/m/Y");
    $pgroupid=$_SESSION['GROUP'];
    $karyawanid=$_SESSION['IDCARD'];
    $pcardid=$_SESSION['IDCARD'];
    $pthn=$_POST['e_periode01'];
    $pdistid=$_POST['e_iddist'];
    $pidregion=$_POST['e_region'];
    $piddivisi=$_POST['e_iddivisi'];
    $pstsdiscount=$_POST['e_stsdisc'];
    $prptby=$_POST['e_rptby'];
    $pjenisklaim=$_POST['cb_jenisklaim'];
    
    
    $fprodothfilter="";
    if (isset($_POST['chkprodoth'])) {
        foreach ($_POST['chkprodoth'] as $pnprod) {
            if (!empty($pnprod)) {
                $fprodothfilter .="'".$pnprod."',";
            }
        }
        
        if (!empty($fprodothfilter)) $fprodothfilter="(".substr($fprodothfilter, 0, -1).")";
    }
    
    $pnmprodukoth="";
    if (!empty($fprodothfilter)) {
        $query = "select nama from MKT.iproduk WHERE iProdId IN $fprodothfilter";
        $tampilp=mysqli_query($cnmy, $query);
        while ($nrow=mysqli_fetch_array($tampilp)) {
            $pnmprodukoth .=$nrow['nama'].",";
        }
        if (!empty($pnmprodukoth)) $pnmprodukoth="(".substr($pnmprodukoth, 0, -1).")";
    }
    
    
    
    $periode=$pthn;
    
    $pnotest=$piddivisi;
    if ($piddivisi=="OTC" OR $piddivisi=="CHC") $pnotest="CHC";
    elseif ($piddivisi=="EO") $pnotest="Tanpa CHC & OTHERS";
    elseif ($piddivisi=="CAN") $pnotest="CANARY / ETHICAL (EAGLE & PIGEON)";
    elseif ($piddivisi=="EAGLE") $pnotest="EAGLE";
    elseif ($piddivisi=="PEACO") $pnotest="PEACOCK";
    elseif ($piddivisi=="PIGEO") $pnotest="PIGEON";
    elseif ($piddivisi=="OTC" OR $piddivisi=="CHC") $pnotest="CHC";
    elseif ($piddivisi=="OTHER") $pnotest="OTHER";
    elseif ($piddivisi=="OTHERS") $pnotest="OTHERS";
    
    $ppilihregion="All Region";
    if ($pidregion=="B") $ppilihregion="Barat";
    if ($pidregion=="BB") $ppilihregion="Barat";
    if ($pidregion=="T") $ppilihregion="Timur";
    if ($piddivisi=="OTC" OR $piddivisi=="CHC" OR $piddivisi=="OTHER" OR $piddivisi=="OTHERS") $ppilihregion="All Region";
    
    $pketstsdic="Termasuk PPN & PPH";
    if ($pstsdiscount=="S") $pketstsdic="Sebelum PPN & PPH";
    
    $pperiodeby="Bulan Klaim";
            
    $now=date("mdYhis");
    $puserid=$_SESSION['USERID'];
    $tmp01 =" dbtemp.tmplapslsvsdisc01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmplapslsvsdisc02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmplapslsvsdisc03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmplapslsvsdisc04_".$puserid."_$now ";
    
    if ($piddivisi!="CHC" AND $piddivisi!="OTC" AND $piddivisi!="OTHER" AND $piddivisi!="OTHERS") {
        $query = "SELECT a.bulan, a.idcbg, c.nama as nama_cabdist, a.distid, b.nama as nama_dist, a.divprodid, "
                . " a.iprodid, a.qty, a.hna, a.`value` as tvalue "
                . " FROM sls.ytd_dist as a "
                . " JOIN sls.distrib0 as b on a.distid=b.distid "
                . " JOIN ms.cbgytd as c on a.idcbg=c.idcabang "
                . " WHERE YEAR(a.bulan)='$pthn' ";
        if (!empty($pdistid)) {
            $query .=" AND a.distid='$pdistid'";
        }
        
        if (!empty($pidregion)) {
            if ($pidregion=="BB") $query .=" AND c.region='B'";
            else $query .=" AND c.region='$pidregion'";
        }
        
        if ($piddivisi=="EO") {
            $query .=" AND a.divprodid NOT IN ('OTHERS', 'OTHERS')";
        }elseif ($piddivisi=="CAN" OR $piddivisi=="ETH") {
            $query .=" AND a.divprodid IN ('EAGLE', 'PIGEO')";
        }else{
            $query .=" AND a.divprodid='$piddivisi'";
        }
    }else{
        $query = "bulan DATE, idcbg varchar(3), nama_cabdist varchar(100), distid varchar(10), nama_dist varchar(100), divprodid varchar(5), "
                . " iprodid varchar(10), qty DECIMAL(20,2), hna DECIMAL(20,2), tvalue DECIMAL(20,2)";
        if ($piddivisi=="OTHER" OR $piddivisi=="OTHERS") {
            $query = "select CONCAT(LEFT(a.tgljual,8), '01') as bulan, a.distid, sum(a.`value`) as tvalue "
                    . " from MKT.otc_etl as a "//JOIN MKT.icabang_o as c on a.icabangid=c.icabangid_o
                    . " where year(a.tgljual)='$pthn' AND a.divprodid ='OTHER' and a.icabangid <> 22 ";
            /*
            if (!empty($pidregion)) {
                if ($pidregion=="BB") $query .=" AND c.region='B' ";
                else $query .=" AND c.region='$pidregion' ";
            }
             * 
             */
            if (!empty($pdistid)) {
                $query .=" AND a.distid='$pdistid' ";
            }
            if (!empty($fprodothfilter)) {
                $query .=" AND a.iprodid IN $fprodothfilter ";
            }
            $query .= " GROUP BY 1,2";
            //echo $query;
        }else{
            $query = "select CONCAT(LEFT(a.tgljual,8), '01') as bulan, a.distid, sum(`value`) as tvalue "
                    . " from MKT.otc_etl as a "
                    . " where year(a.tgljual)='$pthn' AND a.divprodid <>'OTHER' and a.icabangid <> 22 ";
            if (!empty($pdistid)) {
                $query .=" AND a.distid='$pdistid'";
            }
            $query .= " GROUP BY 1,2";
            //echo $query;
        }
    }
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    

    $query = "select klaimid, bulan, karyawanid, user1, distid, region, jumlah, ppn_rp, pph_rp from hrd.klaim WHERE "
            . " YEAR(bulan)='$pthn' AND klaimId not in (SELECT DISTINCT ifnull(klaimId,'') from hrd.klaim_reject)";
    if (!empty($pdistid)) {
        $query .=" AND distid='$pdistid'";
    }
    if ($piddivisi=="EO") $query .=" AND pengajuan NOT IN ('OTC', 'CHC', 'OTHER', 'OTHERS')";
    elseif ($piddivisi=="OTC" OR $piddivisi=="CHC") $query .=" AND pengajuan IN ('OTC', 'CHC')";
    elseif ($piddivisi=="CAN" OR $piddivisi=="ETH") $query .=" AND pengajuan IN ('CAN', 'ETH', 'EAGLE', 'PIGEO')";
    else{
        if (!empty($piddivisi)) $query .=" AND IFNULL(pengajuan,'')='$piddivisi'";
    }
    
    //if ($pidregion=="BB") $query .=" AND c.region='B'";
    
    
    if (!empty($pidregion)) {
        //if ($pgroupid=="43" OR $pgroupid=="40") {//ahmad dan titik
        if ($pidregion=="T") {
            if ($puserid=="144")//titik
                $query.=" AND (user1='$puserid' OR user1='$pcardid' OR ( (user1='0000001043' OR user1='1043') AND ( karyawanid='$pcardid' OR karyawanid='0000002073' ) ) ) ";//MULYA RAYA PETRA SEJAHTERA
            else
                $query.=" AND (user1='144' OR user1='0000000144' OR ( (user1='0000001043' OR user1='1043') AND ( karyawanid='0000000144' OR karyawanid='0000002073' ) ) ) ";//MULYA RAYA PETRA SEJAHTERA
        }else{
            //$pidregion=B dan BB
            if ($puserid=="266")//ahmad ahmed
                $query.=" AND (user1='$puserid' OR user1='$pcardid' OR ( (user1='0000001043' OR user1='1043') AND karyawanid='$pcardid' ) ) ";
            else
                $query.=" AND (user1='266' OR user1='0000000266' OR ( (user1='0000001043' OR user1='1043') AND karyawanid='0000000266' ) ) ";
            
        }
    }
    
    //REGULER ATAU ONLINE SKS
    if ($pjenisklaim=="R" OR $pjenisklaim=="O") {
        if ($pjenisklaim=="R") $query .=" AND IFNULL(jenisklaim,'')=''";
        elseif ($pjenisklaim=="O") $query .=" AND IFNULL(jenisklaim,'')<>''";
    }
    
    //$query .=" AND region='B'";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
    
    if ($pstsdiscount=="S") {
        
        $query = "UPDATE $tmp02 SET jumlah=IFNULL(jumlah,0)-IFNULL(ppn_rp,0)+IFNULL(pph_rp,0)"; 
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }
    
    //ENGGAL dan GMP jadi SPP
    $query = "UPDATE $tmp02 SET distid='0000000002' WHERE distid IN ('0000000015', '0000000017')"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    if ($prptby=="K") {
        $query = "SELECT DISTINCT a.distid, b.nama as nama_dist "
                . " FROM $tmp02 as a JOIN sls.distrib0 as b on a.distid=b.distid";
    }else{
        $query = "SELECT DISTINCT dis.distid, b.nama as nama_dist "
                . " from (select distinct distid FROM $tmp01 UNION select distinct distid FROM $tmp02) as dis JOIN "
                . " sls.distrib0 as b on dis.distid=b.distid";
    }
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
    
    
    $addcolumn="";
    for ($x=1;$x<=12;$x++) {
        $addcolumn .= " ADD S$x DECIMAL(20,2),ADD D$x DECIMAL(20,2),ADD R$x DECIMAL(20,2),";
    }
    $addcolumn .= " ADD STOTAL DECIMAL(20,2), ADD DTOTAL DECIMAL(20,2), ADD RTOTAL DECIMAL(20,2)";
    
    $query = "ALTER TABLE $tmp03 $addcolumn";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $urut=2;
    for ($x=1;$x<=12;$x++) {
        $jml=  strlen($x);
        $awal=$urut-$jml;
        $nbulan=$periode."-".str_repeat("0", $awal).$x;
        $nfield="S".$x;
        $nfield2="D".$x;
        $nfield3="R".$x;
        
        $query = "UPDATE $tmp03 a JOIN ( select distid, sum(tvalue) as tvalue FROM $tmp01 WHERE DATE_FORMAT(bulan, '%Y-%m')='$nbulan' GROUP BY 1 ) as b ON "
                . " a.distid=b.distid SET a.$nfield=b.tvalue";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp03 a JOIN ( select distid, sum(jumlah) as jumlah FROM $tmp02 WHERE DATE_FORMAT(bulan, '%Y-%m')='$nbulan' GROUP BY 1 ) as b ON "
                . " a.distid=b.distid SET a.$nfield2=b.jumlah";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp03 SET $nfield3=CASE WHEN IFNULL($nfield,0)=0 THEN 0 else IFNULL($nfield2,0)/IFNULL($nfield,0)*100 END";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    }
    
    $query = "UPDATE $tmp03 a JOIN ( select distid, sum(tvalue) as tvalue FROM $tmp01 GROUP BY 1 ) as b ON "
            . " a.distid=b.distid SET a.STOTAL=b.tvalue";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp03 a JOIN ( select distid, sum(jumlah) as jumlah FROM $tmp02 GROUP BY 1 ) as b ON "
            . " a.distid=b.distid SET a.DTOTAL=b.jumlah";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    $query = "UPDATE $tmp03 SET RTOTAL=CASE WHEN IFNULL(STOTAL,0)=0 THEN 0 else IFNULL(DTOTAL,0)/IFNULL(STOTAL,0)*100 END";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    
?>



<HTML>
<HEAD>
    <title>Sales VS Discount By Distributor</title>
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

    <center><div class='h1judul'>Sales Vs Discount By Distributor</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <tr><td>Tahun</td><td>:</td><td><?PHP echo "$pthn"; ?></td></tr>
            <tr class='miring text2'><td>view date</td><td>:</td><td><?PHP echo "$printdate"; ?></td></tr>
            <tr class='miring text2'><td>Region</td><td>:</td><td><?PHP echo "$ppilihregion"; ?></td></tr>
            <tr class='miring text2'><td>Divisi</td><td>:</td><td><?PHP echo "$pnotest"; ?></td></tr>
            <tr class='miring text2'><td>Periode By</td><td>:</td><td><?PHP echo "$pperiodeby"; ?></td></tr>
            <tr class='miring text2'><td>Status Disc.</td><td>:</td><td><?PHP echo "$pketstsdic"; ?></td></tr>
            <?PHP
            if ( ($piddivisi=="OTHER" OR $piddivisi=="OTHERS") AND !empty($fprodothfilter)) {
                echo "<tr class='miring text2'><td>Produk</td><td>:</td><td>$pnmprodukoth</td></tr>";
            }
            
            if ($pjenisklaim=="R" OR $pjenisklaim=="O") {
                $pnmklaimjenis="";
                if ($pjenisklaim=="R") $pnmklaimjenis="Reguler";
                elseif ($pjenisklaim=="O") $pnmklaimjenis="Online";
            }
            
            if (!empty($pnmklaimjenis)) {
                echo "<tr class='miring text2'><td>Jenis Klaim </td><td>:</td><td>$pnmklaimjenis</td></tr>";
            }
            ?>
        </table>
    </div>
    <div class="clearfix"></div>
    <hr/>
    
    
    <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
        <thead>
            <tr>
                <th width='10px' align='center' rowspan='2'>NO</th>
                <th width='100px' align='center' rowspan='2'>DISTRIBUTOR</th>
                <?PHP
                $urut=2;
                for ($x=1;$x<=12;$x++) {
                    $jml=  strlen($x);
                    $awal=$urut-$jml;
                    $nbulan=$periode."-".str_repeat("0", $awal).$x;
                    $pbln = date('F Y', strtotime($nbulan));
                    
                    echo "<th width='100px' align='center' colspan='3'>$pbln</th>";
                }
                ?>
                <th width='100px' align='center' colspan='3'>GRAND TOTAL</th>
            </tr>
            
            <tr>
                
                <?PHP
                for ($x=1;$x<=12;$x++) {
                    echo "<th width='50px' align='center'>SALES</th>";
                    echo "<th width='50px' align='center'>DISCOUNT</th>";
                    echo "<th width='50px' align='center' nowrap>RATIO %</th>";
                }
                ?>
                <th width='50px' align='center'>TOTAL SALES</th>
                <th width='50px' align='center'>TOTAL DISCOUNT</th>
                <th width='50px' align='center' nowrap>RATIO %</th>
            </tr>
            
        </thead>
        <tbody>
            <?php
            $no=1;
            for ($x=1;$x<=12;$x++) {
                $pgrandtotalsls[$x]=0;
                $pgrandtotaldisc[$x]=0;
            }
            
            $ptotgrandsls=0;
            $ptotgranddisc=0;
            
            $query = "select * from $tmp03 ORDER BY nama_dist, distid";
            $tampil1= mysqli_query($cnmy, $query);
            while ($row1= mysqli_fetch_array($tampil1)) {
                $piddist=$row1['distid'];
                $pnmdist=$row1['nama_dist'];
                
                echo "<tr>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap>$pnmdist</td>";
                
                for ($x=1;$x<=12;$x++) {
                    $nmcolsls="S".$x;
                    $nmcoldisc="D".$x;
                    $nmcolratio="R".$x;
                    
                    $pjmlsls=$row1[$nmcolsls];
                    $pjmldisc=$row1[$nmcoldisc];
                    $pjmlratio=$row1[$nmcolratio];
                    
                    if (empty($pjmlsls)) $pjmlsls=0;
                    if (empty($pjmldisc)) $pjmldisc=0;
                    if (empty($pjmlratio)) $pjmlratio=0;
                    
                    $pgrandtotalsls[$x]=(double)$pgrandtotalsls[$x]+(double)$pjmlsls;
                    $pgrandtotaldisc[$x]=(double)$pgrandtotaldisc[$x]+(double)$pjmldisc;
                    
                    $pjmlsls=BuatFormatNum($pjmlsls, $ppilformat);
                    $pjmldisc=BuatFormatNum($pjmldisc, $ppilformat);
                    $pjmlratio=ROUND($pjmlratio,2);
                    
                    echo "<td nowrap align='right'>$pjmlsls</td>";
                    echo "<td nowrap align='right'>$pjmldisc</td>";
                    echo "<td nowrap align='right'>$pjmlratio</td>";
                }
                
                $prptotsls=$row1['STOTAL'];
                $prptotdisc=$row1['DTOTAL'];
                $prptotratio=$row1['RTOTAL'];
                
                if (empty($prptotsls)) $prptotsls=0;
                if (empty($prptotdisc)) $prptotdisc=0;
                
                $ptotgrandsls=(DOUBLE)$ptotgrandsls+(DOUBLE)$prptotsls;
                $ptotgranddisc=(DOUBLE)$ptotgranddisc+(DOUBLE)$prptotdisc;
                
                $prptotsls=BuatFormatNum($prptotsls, $ppilformat);
                $prptotdisc=BuatFormatNum($prptotdisc, $ppilformat);
                $prptotratio=ROUND($prptotratio,2);
                    
                echo "<td style='font-weight:bold;' nowrap align='right'>$prptotsls</td>";
                echo "<td style='font-weight:bold;' nowrap align='right'>$prptotdisc</td>";
                echo "<td style='font-weight:bold;' nowrap align='right'>$prptotratio</td>";
                
                echo "</tr>";
                
                $no++;
            }
            
            echo "<tr style='font-weight:bold;'>";
            echo "<td nowrap></td>";
            echo "<td nowrap>GRAND TOTAL</td>";
                
            for ($x=1;$x<=12;$x++) {
                
                $pgrandtotalsls_=$pgrandtotalsls[$x];
                $pgrandtotaldisc_=$pgrandtotaldisc[$x];
                
                if (empty($pgrandtotalsls_)) $pgrandtotalsls_=0;
                if (empty($pgrandtotaldisc_)) $pgrandtotaldisc_=0;
                
                $pgrandtotratio=0;
                if ((DOUBLE)$pgrandtotalsls_<>0) {
                    $pgrandtotratio=(DOUBLE)$pgrandtotaldisc_/(DOUBLE)$pgrandtotalsls_*100;
                    $pgrandtotratio=ROUND($pgrandtotratio,2);
                }
                
                $pgrandtotalsls_=BuatFormatNum($pgrandtotalsls_, $ppilformat);
                $pgrandtotaldisc_=BuatFormatNum($pgrandtotaldisc_, $ppilformat);
                
                
                echo "<td nowrap align='right'>$pgrandtotalsls_</td>";
                echo "<td nowrap align='right'>$pgrandtotaldisc_</td>";
                echo "<td nowrap align='right'>$pgrandtotratio</td>";
                
                
            }
            
            
            $pgrandtotratio=0;
            if ((DOUBLE)$ptotgrandsls<>0) {
                $pgrandtotratio=(DOUBLE)$ptotgranddisc/(DOUBLE)$ptotgrandsls*100;
                $pgrandtotratio=ROUND($pgrandtotratio,2);
            }
                
            $ptotgrandsls=BuatFormatNum($ptotgrandsls, $ppilformat);
            $ptotgranddisc=BuatFormatNum($ptotgranddisc, $ppilformat);
            
            echo "<td nowrap align='right'>$ptotgrandsls</td>";
            echo "<td nowrap align='right'>$ptotgranddisc</td>";
            echo "<td nowrap align='right'>$pgrandtotratio</td>";

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
    
    
        $(document).ready(function() {
            
            
            var table1 = $('#mydatatable1').DataTable({
                fixedHeader: true,
                "ordering": false,
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": -1,
                "order": [[ 0, "asc" ]],
                "columnDefs": [
                    { "visible": false },
                    { className: "text-right", "targets": [5,6,7,8,9] },//right
                    { className: "text-nowrap", "targets": [0,1,2,3,4,5,6,7,8,9] }//nowrap

                ],
                bFilter: true, bInfo: true, "bLengthChange": true, "bLengthChange": true,
                "bPaginate": true
            } );
            

        } );
    
    
    </script>

</HTML>


<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");
    mysqli_close($cnmy);
?>