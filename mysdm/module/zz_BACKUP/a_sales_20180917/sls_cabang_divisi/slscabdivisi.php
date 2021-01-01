<script>
$(document).ready(function() {
    var groupColumn = 1;
    $('#datatable1').DataTable( {
        fixedHeader: true,
        "ordering": false,
        "lengthMenu": [[500, 1000, 1500, -1], [500, 1000, 1500, "All"]],
        "columnDefs": [
            { "visible": false },
            { className: "text-right", "targets": [2,3,4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16] }//,//right
            //{ className: "text-nowrap", "targets": [0] }//nowrap

        ],
        dom: 'Bfrtip',
        buttons: [
            'excel', 'print'
        ],
        bFilter: false, bInfo: false, "bLengthChange": false,
		"bPaginate": false
    } );
} );

</script>

<style>
    .divnone {
        display: none;
    }
    #datatable1 th {
        font-size: 12px;
        border: 0.1px solid #ccc;
    }
    #datatable1 td { 
        font-size: 12px;
        border: 0.1px solid #ccc;
    }
</style>

<?php

    if (isset($_GET['module'])){
        $tgl01=$_POST["bulan"];
        $bulan= date("Y-m-d", strtotime($tgl01));
        $region=$_POST["region"];
        $karyawanid=$_SESSION["IDCARD"];
        $link="";
    }else {
        $bulan=$_GET["bulan"];
        $region=$_GET["region"];
        $karyawanid=$_GET["id"];
        $link =$_GET["link"]."/report/slsspv.aspx";
    }
    
    $date=date_create($bulan);
    $tanggal=date_format($date,"Y-m-d");

    if ($region=="B"){ $namaregion="Barat";} else if ($region=="T"){$namaregion="Timur";}

    require_once 'meekrodb.2.3.class.php';

    $thnbln=date_format($date,"F Y");
    $thnbln2=date('F Y', strtotime('-1 year', strtotime($tanggal)));
    $printdate= date("d/m/Y");

    if (!empty($link)) {
        echo "<h4><b><a href='$link'>back</a></b></h4>";
    }
    
    
    $filbln=date_format($date,"Ymd");
    $filblnprod1=date_format($date,"Ym");
    $filblnprod2=date('Ym', strtotime('-1 year', strtotime($tanggal)));
    
    $closing = DB::queryFirstField("SELECT status FROM sls.closing_sales WHERE date_format(bulan,'%Y%m') = '$filblnprod1'");
    if (empty($closing)) $closing="belum closing";
    
    $thnbln=date_format($date,"F Y");
    $printdate= date("d/m/Y");
    echo "<table width='90%' align='center' border='0' class='table table-striped table-bordered'>";
        echo "<tr>";
            echo "<td valign='top'>";
                echo "<table border='0' width='100%'>";
                echo "<tr><td><small style='color:blue;'>PT. Surya Dermato Medica</small></td></tr>";
                echo "<tr><td><b>SALES CABANG & DIVISI BY VALUE</b></td></tr>";
                echo "<tr><td>Region : $namaregion</td></tr>";
                echo "<tr><td>Periode : $thnbln</td></tr>";
                echo "<tr><td>Status closing : $closing</td></tr>";
                echo "<tr><td><small><i>View Date : $printdate</i></small></td></tr>";
                echo "</table>";
            echo "</td>";
            echo "<td>";
                echo "<table align='left' border='0' width='100%'>";
                echo "<tr><td align='left'><h3></h3></td></tr>";
                echo "<tr><td align='center'><b></b></td></tr>";
                //echo "<tr><td align='right'><small><i>View Date : $printdate</i></small></td></tr>";
                echo "</table>";
            echo "</td>";
        echo "</tr>";
    echo "</table><hr>&nbsp;</hr>";
    
    
    $milliseconds = round(microtime(true) * 1000);
    $now=date("mdYhis");
    $tmp0 ="DTSALESCABDIVXX00".$karyawanid."_$now$milliseconds";
    $tmp1 ="DTSALESCABDIVXX01".$karyawanid."_$now$milliseconds";
    $tmp2 ="DTSALESCABDIVXX02".$karyawanid."_$now$milliseconds";
    $tmp3 ="DTSALESCABDIVXX03".$karyawanid."_$now$milliseconds";
    
    

    
    DB::useDB("dbtemp");
    
    
    $query = "select date_format(s.tgljual,'%Y%m') as thnbln, s.icabangid, c.nama as nama_cabang, s.divprodid, s.iprodid, 
        sum(qty) qty, sum(s.qty*s.hna) as tvalue from sls.mr_sales2 as s
        JOIN ms.icabang as c on s.icabangid=c.icabangid
        JOIN ms.iarea as a on s.areaid=a.areaid and s.icabangid=a.icabangid and c.icabangid=a.icabangid
        where (date_format(s.tgljual,'%Y%m') = '$filblnprod1' OR date_format(s.tgljual,'%Y%m') = '$filblnprod2')
        and a.aktif='Y' and c.aktif='Y' and c.region='$region'
        group by date_format(s.tgljual,'%Y%m'), s.icabangid, c.nama, s.divprodid, s.iprodid";
    //$results1 = DB::query("create table $tmp1($query)");
    
    $query = "select date_format(tgl,'%Y%m') thnbln, t0.icabangid, c.nama as nama_cabang, t0.areaId, t0.divprodid, t1.iprodid, sum(t1.target) as target, sum(t1.hna*t1.target) as tvalue from ms.target1_new as t1 join ms.target0_new as t0 on t1.targetId=t0.targetId
        JOIN ms.icabang as c on t0.icabangid=c.icabangid
        JOIN ms.iarea as a on t0.areaid=a.areaid and t0.icabangid=a.icabangid and c.icabangid=a.icabangid
        where (date_format(tgl,'%Y%m')= '$filblnprod1')
        and c.aktif='Y' and a.aktif='Y' and c.region='$region'
        GROUP BY date_format(tgl,'%Y%m'), t0.icabangid, t0.areaId, t0.divprodid, t1.iprodid";
    //$results1 = DB::query("create table $tmp2($query)");
    
    $query="SELECT iCabangId icabangid, nama FROM ms.icabang WHERE aktif='Y' and region='$region'";
    //$results1 = DB::query("create table $tmp0($query)");
    
    /*
    
    $query="SELECT iCabangId icabangid, nama FROM ms.icabang WHERE aktif='Y' and region='$region'";
    $results1 = DB::query("create table $tmp0($query)");
    
    $query = "select distinct a.divprodid from $tmp1 as a union select distinct b.divprodid from $tmp2 as b order by divprodid";
    $results1 = DB::query($query);
    foreach ($results1 as $r1) {
        $fdivisi=$r1['divprodid'];
        $fsales1=$r1['divprodid']."1";
        $ftarget=$r1['divprodid']."target";
        $fach=$r1['divprodid']."ach";
        $fsales2=$r1['divprodid']."2";
        $fgrowth=$r1['divprodid']."grw";
        $results1 = DB::query("alter table $tmp0 ADD $fsales1 DOUBLE(20,2), ADD $ftarget DOUBLE(20,2), ADD $fach DOUBLE(20,2), ADD $fsales2 DOUBLE(20,2), ADD $fgrowth DOUBLE(20,2)");
        
        
        //sales skrang
        $query="update $tmp0 set $fsales1=ifnull((select sum(tvalue) from $tmp1 where $tmp0.icabangid=$tmp1.icabangid and $tmp1.divprodid='$fdivisi' and $tmp1.thnbln='$filblnprod1'),0)";
        $results1 = DB::query($query);
        //sales lalu
        $query="update $tmp0 set $fsales2=ifnull((select sum(tvalue) from $tmp1 where $tmp0.icabangid=$tmp1.icabangid and $tmp1.divprodid='$fdivisi' and $tmp1.thnbln='$filblnprod2'),0)";
        $results1 = DB::query($query);
        //target
        $query="update $tmp0 set $ftarget=ifnull((select sum(tvalue) from $tmp2 where $tmp0.icabangid=$tmp2.icabangid and $tmp2.divprodid='$fdivisi'),0)";
        $results1 = DB::query($query);
        //ach
        $query="update $tmp0 set $fach=$fsales1/$ftarget*100";
        $results1 = DB::query($query);
        //growth
        $query="update $tmp0 set $fgrowth=($fsales1-$fsales2)/$fsales2*100";
        $results1 = DB::query($query);
        
    }
    
    $fsales1="TOT1";
    $ftarget="TOTtarget";
    $fach="TOTach";
    $fsales2="TOT2";
    $fgrowth="TOTgrw";
    $results1 = DB::query("alter table $tmp0 ADD $fsales1 DOUBLE(20,2), ADD $ftarget DOUBLE(20,2), ADD $fach DOUBLE(20,2), ADD $fsales2 DOUBLE(20,2), ADD $fgrowth DOUBLE(20,2)");
    
    //sales skrang
    $query="update $tmp0 set $fsales1=ifnull((select sum(tvalue) from $tmp1 where $tmp0.icabangid=$tmp1.icabangid and $tmp1.thnbln='$filblnprod1'),0)";
    $results1 = DB::query($query);
    //sales lalu
    $query="update $tmp0 set $fsales2=ifnull((select sum(tvalue) from $tmp1 where $tmp0.icabangid=$tmp1.icabangid and $tmp1.thnbln='$filblnprod2'),0)";
    $results1 = DB::query($query);
    //target
    $query="update $tmp0 set $ftarget=ifnull((select sum(tvalue) from $tmp2 where $tmp0.icabangid=$tmp2.icabangid),0)";
    $results1 = DB::query($query);
    //ach
    $query="update $tmp0 set $fach=$fsales1/$ftarget*100";
    $results1 = DB::query($query);
    //growth
    $query="update $tmp0 set $fgrowth=($fsales1-$fsales2)/$fsales2*100";
    $results1 = DB::query($query);
    */
    
    
    
    
    $query = "select date_format(s.bulan,'%Y%m') thnbln, s.divprodid, s.iddaerah icabangid, c.nama nama_cabang,
        sum(mtd_qty_sales) sqty1, sum(mtd_value_sales) stvalue1,
        sum(mtd_qty_target) tqty, sum(mtd_value_target) ttvalue,
        sum(mtd_qty_thnlalu) sqty2, sum(mtd_value_thnlalu) stvalue2
        from sls.ytd as s
        JOIN ms.cbgytd as c on s.iddaerah=c.idcabang
        where date_format(s.bulan,'%Y%m') = '$filblnprod1'
        and s.region='$region'
        GROUP BY date_format(s.bulan,'%Y%m'), s.divprodid, s.iddaerah, c.nama";
    $results1 = DB::query("create table $tmp1($query)");
    
    
    
    $query="select distinct idcabang icabangid, nama nama_cabang from ms.cbgytd WHERE region='$region'";
    $results1 = DB::query("create table $tmp0($query)");
    
    $query = "select distinct a.divprodid from $tmp1 as a order by a.divprodid";
    $results1 = DB::query($query);
    foreach ($results1 as $r1) {
        $fdivisi=$r1['divprodid'];
        $fsales1=$r1['divprodid']."1";
        $ftarget=$r1['divprodid']."target";
        $fach=$r1['divprodid']."ach";
        $fsales2=$r1['divprodid']."2";
        $fgrowth=$r1['divprodid']."grw";
        $results1 = DB::query("alter table $tmp0 ADD $fsales1 DOUBLE(20,2), ADD $ftarget DOUBLE(20,2), ADD $fach DOUBLE(20,2), ADD $fsales2 DOUBLE(20,2), ADD $fgrowth DOUBLE(20,2)");
        
        
        //sales skrang
        $query="update $tmp0 set $fsales1=ifnull((select sum(stvalue1) from $tmp1 where $tmp0.icabangid=$tmp1.icabangid and $tmp1.divprodid='$fdivisi'),0)";
        $results1 = DB::query($query);
        //sales lalu
        $query="update $tmp0 set $fsales2=ifnull((select sum(stvalue2) from $tmp1 where $tmp0.icabangid=$tmp1.icabangid and $tmp1.divprodid='$fdivisi'),0)";
        $results1 = DB::query($query);
        //target
        $query="update $tmp0 set $ftarget=ifnull((select sum(ttvalue) from $tmp1 where $tmp0.icabangid=$tmp1.icabangid and $tmp1.divprodid='$fdivisi'),0)";
        $results1 = DB::query($query);
        //ach
        $query="update $tmp0 set $fach=$fsales1/$ftarget*100";
        $results1 = DB::query($query);
        //growth
        $query="update $tmp0 set $fgrowth=($fsales1-$fsales2)/$fsales2*100";
        $results1 = DB::query($query);
        
    }
    
    $fsales1="TOT1";
    $ftarget="TOTtarget";
    $fach="TOTach";
    $fsales2="TOT2";
    $fgrowth="TOTgrw";
    $results1 = DB::query("alter table $tmp0 ADD $fsales1 DOUBLE(20,2), ADD $ftarget DOUBLE(20,2), ADD $fach DOUBLE(20,2), ADD $fsales2 DOUBLE(20,2), ADD $fgrowth DOUBLE(20,2)");
    
    //sales skrang
    $query="update $tmp0 set $fsales1=ifnull((select sum(stvalue1) from $tmp1 where $tmp0.icabangid=$tmp1.icabangid),0)";
    $results1 = DB::query($query);
    //sales lalu
    $query="update $tmp0 set $fsales2=ifnull((select sum(stvalue2) from $tmp1 where $tmp0.icabangid=$tmp1.icabangid),0)";
    $results1 = DB::query($query);
    //target
    $query="update $tmp0 set $ftarget=ifnull((select sum(ttvalue) from $tmp1 where $tmp0.icabangid=$tmp1.icabangid),0)";
    $results1 = DB::query($query);
    //ach
    $query="update $tmp0 set $fach=$fsales1/$ftarget*100";
    $results1 = DB::query($query);
    //growth
    $query="update $tmp0 set $fgrowth=($fsales1-$fsales2)/$fsales2*100";
    $results1 = DB::query($query);
    
?>
<table id="datatable1" class="display  table table-striped table-bordered" style="width:100%" border='0px'>
    <thead>
        <tr style="background-color: #cccfff;">
            <th>No</th><!-- rowspan="2"-->
            <th>Cabang YTD</th><!-- rowspan="2"-->
            <?PHP
            /*
            $query = "select distinct a.divprodid from $tmp1 as a order by a.divprodid";
            $results1 = DB::query($query);
            foreach ($results1 as $r1) {
                ?>
                <th colspan="5"><?PHP echo $r1['divprodid']; ?></th>
                <?PHP
            }
            $kettotal = "SALES $thnbln REGION $namaregion";
             * 
             */
            ?>
            <!--<th colspan="5"><?PHP //echo $kettotal; ?></th>
        </tr>
        <tr>-->
            <?PHP
            $query = "select distinct a.divprodid from $tmp1 as a order by a.divprodid";
            $results1 = DB::query($query);
            foreach ($results1 as $r1) {
                $fdivisi=$r1['divprodid'];
                echo "<th valign='top'><u>$fdivisi</u>&nbsp;<br/>Sales $thnbln</th>";
                echo "<th valign='top'><u>$fdivisi</u>&nbsp;<br/>Target $thnbln</th>";
                echo "<th valign='top'><u>$fdivisi</u>&nbsp;<br/>Ach %</th>";
                echo "<th valign='top'><u>$fdivisi</u>&nbsp;<br/>Sales $thnbln2</th>";
                echo "<th valign='top'><u>$fdivisi</u>&nbsp;<br/>Growth %</th>";
                
            }
            ?>
            <th valign='top'>Sales <?PHP echo $thnbln; ?></th>
            <th valign='top'>Target <?PHP echo $thnbln; ?></th>
            <th valign='top'>Ach %</th>
            <th valign='top'>Sales <?PHP echo $thnbln2; ?></th>
            <th valign='top'>Growth</th>
        </tr>
    </thead>
    <tbody>
    <?PHP
        $no=1;
        $query2 = "select * from $tmp0 order by nama_cabang";
        $results2 = DB::query($query2);
        foreach ($results2 as $r2) {
            
            $nmcabang=$r2['nama_cabang'];
            echo "<tr>";
            echo "<td nowrap>$no</td>";
            echo "<td nowrap>$nmcabang</td>";
            
            $query = "select distinct a.divprodid from $tmp1 as a order by a.divprodid";
            $results1 = DB::query($query);
            foreach ($results1 as $r1) {
                $fdivisi=$r1['divprodid'];
                $fsales1=$r1['divprodid']."1";
                $ftarget=$r1['divprodid']."target";
                $fach=$r1['divprodid']."ach";
                $fsales2=$r1['divprodid']."2";
                $fgrowth=$r1['divprodid']."grw";
                
                $sls1=$r2[$fsales1];
                $trg=$r2[$ftarget];
                $ach=$r2[$fach];
                $sls2=$r2[$fsales2];
                $grw=$r2[$fgrowth];
                
                echo "<td align='right'>".number_format($sls1,0,",",",")."</td>";
                echo "<td align='right'>".number_format($trg,0,",",",")."</td>";
                echo "<td align='right'>".round($ach,2)."</td>";
                echo "<td align='right'>".number_format($sls2,0,",",",")."</td>";
                echo "<td align='right'>".number_format($sls2,0,",",",")."</td>";
            }
            
            $fsales1="TOT1";
            $ftarget="TOTtarget";
            $fach="TOTach";
            $fsales2="TOT2";
            $fgrowth="TOTgrw";
    
            $sls1=$r2[$fsales1];
            $trg=$r2[$ftarget];
            $ach=$r2[$fach];
            $sls2=$r2[$fsales2];
            $grw=$r2[$fgrowth];
            
            echo "<td align='right'>".number_format($sls1,0,",",",")."</td>";
            echo "<td align='right'>".number_format($trg,0,",",",")."</td>";
            echo "<td align='right'>".round($ach,2)."</td>";
            echo "<td align='right'>".number_format($sls2,0,",",",")."</td>";
            echo "<td align='right'>".round($grw,2)."</td>";
            echo "</tr>";
            
            $no++;
        }
        
        $fsum="";
        $query = "select distinct a.divprodid from $tmp1 as a order by a.divprodid";
        $results1 = DB::query($query);
        foreach ($results1 as $r1) {
            $fdivisi="SUM(".$r1['divprodid'].") AS ".$r1['divprodid'];
            $fsales1="SUM(".$r1['divprodid']."1".") AS ".$r1['divprodid']."1";
            $ftarget="SUM(".$r1['divprodid']."target".") AS ".$r1['divprodid']."target";
            $fach=$r1['divprodid']."ach";
            $fsales2="SUM(".$r1['divprodid']."2".") AS ".$r1['divprodid']."2";
            $fgrowth=$r1['divprodid']."grw".") AS ".$r1['divprodid']."grw";
            $fsum .=$fsales1.",".$ftarget.",".$fsales2.",";
        }
        
        echo "<tr style='background-color: #cccfff; border: 1px solid #000;'>";
        echo "<td></td>";
        echo "<td><b>TOTAL : </b></td>";
        
        $querytot = "select $fsum sum(TOT1) TOT1, sum(TOTtarget) TOTtarget, sum(TOT2) TOT2 from $tmp0";
        $resultstot = DB::query($querytot);
        foreach ($resultstot as $rt) {
            
            $query = "select distinct a.divprodid from $tmp1 as a order by a.divprodid";
            $results1 = DB::query($query);
            foreach ($results1 as $r1) {
                $fdivisi=$r1['divprodid'];
                $fsales1=$r1['divprodid']."1";
                $ftarget=$r1['divprodid']."target";
                $fach=$r1['divprodid']."ach";
                $fsales2=$r1['divprodid']."2";
                $fgrowth=$r1['divprodid']."grw";
                
                $sls1=$rt[$fsales1];
                $trg=$rt[$ftarget];
                if ($rt[$ftarget]==0)
                    $ach=0;
                else
                    $ach=$rt[$fsales1]/$rt[$ftarget]*100;
                $sls2=$rt[$fsales2];
                if ($rt[$fsales2]==0)
                    $grw=0;
                else
                    $grw=($rt[$fsales1]-$rt[$fsales2])/$rt[$fsales2]*100;
                
                echo "<td align='right'><b>".number_format($sls1,0,",",",")."</b></td>";
                echo "<td align='right'><b>".number_format($trg,0,",",",")."</b></td>";
                echo "<td align='right'><b>".round($ach,2)."</b></td>";
                echo "<td align='right'><b>".number_format($sls2,0,",",",")."</b></td>";
                echo "<td align='right'><b>".round($grw,2)."</b></td>";
            }
            $fsales1="TOT1";
            $ftarget="TOTtarget";
            $fach="TOTach";
            $fsales2="TOT2";
            $fgrowth="TOTgrw";
    
            $sls1=$rt[$fsales1];
            $trg=$rt[$ftarget];
            if ($rt[$ftarget]==0)
                $ach=0;
            else
                $ach=$rt[$fsales1]/$rt[$ftarget]*100;
            $sls2=$rt[$fsales2];
            if ($rt[$fsales2]==0)
                $grw=0;
            else
                $grw=($rt[$fsales1]-$rt[$fsales2])/$rt[$fsales2]*100;
            
            echo "<td align='right'><b>".number_format($sls1,0,",",",")."</b></td>";
            echo "<td align='right'><b>".number_format($trg,0,",",",")."</b></td>";
            echo "<td align='right'><b>".round($ach,2)."</b></td>";
            echo "<td align='right'><b>".number_format($sls2,0,",",",")."</b></td>";
            echo "<td align='right'><b>".round($grw,2)."</b></td>";
        }
        echo "</tr>";
    ?>
    </tbody>
</table>
<?PHP
    $results1 = DB::query("drop table $tmp0");
    $results1 = DB::query("drop table $tmp1");
?>