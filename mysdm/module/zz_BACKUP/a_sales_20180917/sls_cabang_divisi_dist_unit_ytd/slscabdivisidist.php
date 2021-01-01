<script>
$(document).ready(function() {
    var groupColumn = 1;
    $('#datatable1').DataTable( {
        fixedHeader: true,
        "ordering": false,
        "lengthMenu": [[500, 1000, 1500, -1], [500, 1000, 1500, "All"]],
        "columnDefs": [
            { "visible": false },
            { className: "text-right", "targets": [2,3,4, 5, 6, 7] }//,//right
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
        $dist=$_POST["distibutor"];
        $karyawanid=$_SESSION["IDCARD"];
        $link="";
    }else {
        $bulan=$_GET["bulan"];
        $region=$_GET["region"];
        $dist=$_GET["distibutor"];
        $karyawanid=$_GET["id"];
        $link =$_GET["link"]."/report/slsspv.aspx";
    }
    
    $date=date_create($bulan);
    $tanggal=date_format($date,"Y-m-d");

    if ($region=="B"){ $namaregion="Barat";} else if ($region=="T"){$namaregion="Timur";}

    require_once 'meekrodb.2.3.class.php';
    
    $nminitialdist = DB::queryFirstField("SELECT initial FROM sls.distrib0 WHERE Distid = '$dist'");

    $thnbln=date_format($date,"F Y");
    $thnbln2=date('F Y', strtotime('-1 year', strtotime($tanggal)));
    $printdate= date("d/m/Y");
    
    
    if (!empty($link)) {
        echo "<h4><b><a href='$link'>back</a></b></h4>";
    }
    
    $mybln=(int)date_format($date,"m")-1;
    $filbln=date_format($date,"Ymd");
    $filblnprod1=date_format($date,"Ym");
    $filblnprod2=date('Ym', strtotime('-1 year', strtotime($tanggal)));
    
    $tanggal2=date('Y-m-d', strtotime('-1 year', strtotime($tanggal)));
    
    $fbln1=date('Ym', strtotime('-'.$mybln.' month', strtotime($tanggal)));
    $fbln2=date_format($date,"Ym");
    
    $fblngw1=date('Ym', strtotime('-'.$mybln.' month', strtotime($tanggal2)));
    $fblngw2=$filblnprod2;
    
    $closing = DB::queryFirstField("SELECT status FROM sls.closing_sales WHERE date_format(bulan,'%Y%m') = '$filblnprod1'");
    if (empty($closing)) $closing="belum closing";
    
    $thnbln=date_format($date,"F Y");
    $printdate= date("d/m/Y");
    echo "<table width='90%' align='center' border='0' class='table table-striped table-bordered'>";
        echo "<tr>";
            echo "<td valign='top'>";
                echo "<table border='0' width='100%'>";
                echo "<tr><td><small style='color:blue;'>PT. Surya Dermato Medica</small></td></tr>";
                echo "<tr><td><b>SALES YTD <u>$nminitialdist</u> PRODUK & DIVISI BY UNIT</b></td></tr>";
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
    $tmp0 ="DTSAESCABDIVDISTVDISTUNT00".$karyawanid."_$now$milliseconds";
    $tmp1 ="DTSAESCABDIVDISTVDISTUNT01".$karyawanid."_$now$milliseconds";
    $tmp2 ="DTSAESCABDIVDISTVDISTUNT02".$karyawanid."_$now$milliseconds";
    $tmp3 ="DTSAESCABDIVDISTVDISTUNT03".$karyawanid."_$now$milliseconds";
    
    

    
    DB::useDB("dbtemp");
    
    
    $query = "select date_format(s.bulan,'%Y%m') thnbln, d.initial, s.iProdId iprodid, s.divprodid, s.hna, SUM(s.qty) qty, SUM(s.qty*s.hna) as tvalue 
        from sls.ytd_dist s
        JOIN sls.distrib0 as d on s.distid=d.distid
        JOIN ms.cbgytd as c on s.idcbg=c.idcabang
        where ( (date_format(s.bulan,'%Y%m') between '$fbln1' AND '$fbln2') OR (date_format(s.bulan,'%Y%m') between '$fblngw1' AND '$fblngw2') )
        AND s.distid='$dist' and c.region='$region'
        GROUP BY date_format(s.bulan,'%Y%m'), d.initial, s.divprodid, s.iProdId, s.hna ";
    $results1 = DB::query("create table $tmp1($query)");
    
    
    $query = "select date_format(s.bulan,'%Y%m') thnbln, s.divprodid, s.iProdId iprodid,
        sum(ytd_qty_sales) sqty1, sum(ytd_value_sales) stvalue1,
        sum(ytd_qty_target) tqty, sum(ytd_value_target) ttvalue,
        sum(ytd_qty_thnlalu) sqty2, sum(ytd_value_thnlalu) stvalue2
        from sls.ytd as s
        where date_format(s.bulan,'%Y%m') = '$filblnprod1'
        and s.region='$region'
        GROUP BY date_format(s.bulan,'%Y%m'), s.divprodid, s.iddaerah, s.iProdId";
    $results1 = DB::query("create table $tmp2($query)");
    
    
    $query="select distinct divprodid, iprodid, nama, hna from  ms.iproduk WHERE divprodId in (select distinct divprodid from $tmp1)";
    $results1 = DB::query("create table $tmp0($query)");
    
    $results1 = DB::query("alter table $tmp0 ADD QTOT1 DOUBLE(20,2), ADD QTOTtarget DOUBLE(20,2), ADD QTOTach DOUBLE(20,2), ADD QTOT2 DOUBLE(20,2), ADD QTOTgrw DOUBLE(20,2)");
    $results1 = DB::query("alter table $tmp0 ADD TOT1 DOUBLE(20,2), ADD TOTtarget DOUBLE(20,2), ADD TOTach DOUBLE(20,2), ADD TOT2 DOUBLE(20,2), ADD TOTgrw DOUBLE(20,2)");
    
    //sales skrang
    $query="update $tmp0 set QTOT1=ifnull((select sum(qty) from $tmp1 where $tmp0.iprodid=$tmp1.iprodid and $tmp0.divprodid=$tmp1.divprodid and $tmp1.thnbln between '$fbln1' AND '$fbln2'),0)";
    $results1 = DB::query($query);
    //sales lalu
    $query="update $tmp0 set QTOT2=ifnull((select sum(qty) from $tmp1 where $tmp0.iprodid=$tmp1.iprodid and $tmp0.divprodid=$tmp1.divprodid and $tmp1.thnbln between '$fblngw1' AND '$fblngw2'),0)";
    $results1 = DB::query($query);
    //target
    $query="update $tmp0 set QTOTtarget=ifnull((select sum(sqty1) from $tmp2 where $tmp0.iprodid=$tmp2.iprodid and $tmp0.divprodid=$tmp2.divprodid),0)";
    $results1 = DB::query($query);
    //ach
    $query="update $tmp0 set QTOTach=QTOT1/QTOTtarget*100";
    $results1 = DB::query($query);
    //growth
    $query="update $tmp0 set QTOTgrw=(QTOT1-QTOT2)/QTOT2*100";
    $results1 = DB::query($query);
    
    
    //VALUE
    //sales skrang
    $query="update $tmp0 set TOT1=ifnull((select sum(tvalue) from $tmp1 where $tmp0.iprodid=$tmp1.iprodid and $tmp0.divprodid=$tmp1.divprodid and $tmp1.thnbln between '$fbln1' AND '$fbln2'),0)";
    $results1 = DB::query($query);
    //sales lalu
    $query="update $tmp0 set TOT2=ifnull((select sum(tvalue) from $tmp1 where $tmp0.iprodid=$tmp1.iprodid and $tmp0.divprodid=$tmp1.divprodid and $tmp1.thnbln between '$fblngw1' AND '$fblngw2'),0)";
    $results1 = DB::query($query);
    //target
    $query="update $tmp0 set TOTtarget=ifnull((select sum(stvalue1) from $tmp2 where $tmp0.iprodid=$tmp2.iprodid and $tmp0.divprodid=$tmp2.divprodid),0)";
    $results1 = DB::query($query);
    //ach
    $query="update $tmp0 set TOTach=TOT1/TOTtarget*100";
    $results1 = DB::query($query);
    //growth
    $query="update $tmp0 set TOTgrw=(TOT1-TOT2)/TOT2*100";
    $results1 = DB::query($query);
    
   
    
?>
<table id="datatable1" class="display  table table-striped table-bordered" style="width:100%" border='0px'>
    <thead>
        <tr style="background-color: #cccfff;">
            <th>No</th>
            <th>Produk</th>
            <th>HNA</th>
            <th><?PHP echo "Sales $nminitialdist $thnbln"; ?></th>
            <th><?PHP echo "Sales R1 $thnbln"; ?></th>
            <th>Ctr %</th>
            <th><?PHP echo "$nminitialdist $thnbln2"; ?></th>
            <th>Growth %</th>
        </tr>
    </thead>
    <tbody>
    <?PHP
    
        $query = "select distinct divprodid from $tmp0 order by divprodid";
        $results1 = DB::query($query);
        foreach ($results1 as $r1) {
            $divisi=$r1['divprodid'];
            echo "<tr>";
            echo "<td></td>";
            echo "<td nowrap colspan='7'><b>$divisi</b></td>";
            echo "<td align='right' class='divnone'></td>";
            echo "<td align='right' class='divnone'></td>";
            echo "<td align='right' class='divnone'></td>";
            echo "<td align='right' class='divnone'></td>";
            echo "<td align='right' class='divnone'></td>";
            echo "<td align='right' class='divnone'></td>";
            echo "</tr>";
            
            $no=1;
            $query2 = "select * from $tmp0 where divprodid='$divisi' order by nama";
            $results2 = DB::query($query2);
            foreach ($results2 as $r2) {

                $nmproduk=$r2['nama'];
                $hna=$r2['hna'];
                $sls1=$r2['QTOT1'];
                $trg=$r2['QTOTtarget'];
                $ach=$r2['QTOTach'];
                $sls2=$r2['QTOT2'];
                $grw=$r2['QTOTgrw'];

                echo "<tr>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap>$nmproduk</td>";

                echo "<td align='right'>".number_format($hna,0,",",",")."</td>";
                echo "<td align='right'>".number_format($sls1,0,",",",")."</td>";
                echo "<td align='right'>".number_format($trg,0,",",",")."</td>";
                echo "<td align='right'>".round($ach,2)."</td>";
                echo "<td align='right'>".number_format($sls2,0,",",",")."</td>";
                echo "<td align='right'>".round($grw,2)."</td>";
                echo "</tr>";

                $no++;
            }
            //sub total
            $querytot = "select sum(TOT1) TOT1, sum(TOTtarget) TOTtarget, sum(TOT2) TOT2 from $tmp0 where divprodid='$divisi'";
            $resultstot = DB::query($querytot);
            foreach ($resultstot as $rt) {
                $sls1=$rt['TOT1'];
                $trg=$rt['TOTtarget'];
                if ($rt['TOTtarget']==0)
                    $ach=0;
                else
                    $ach=$rt['TOT1']/$rt['TOTtarget']*100;
                $sls2=$rt['TOT2'];
                if ($rt['TOT2']==0)
                    $grw=0;
                else
                    $grw=($rt['TOT1']-$rt['TOT2'])/$rt['TOT2']*100;
                echo "<tr style='background-color: #ccffff;'>";
                echo "<td nowrap></td>";
                echo "<td nowrap colspan=2><b>Total $divisi : </b></td>";
                echo "<td align='right' class='divnone'></td>";
                echo "<td align='right'>".number_format($sls1,0,",",",")."</td>";
                echo "<td align='right'>".number_format($trg,0,",",",")."</td>";
                echo "<td align='right'>".round($ach,2)."</td>";
                echo "<td align='right'>".number_format($sls2,0,",",",")."</td>";
                echo "<td align='right'>".round($grw,2)."</td>";
                echo "</tr>";
            }
        }
        
        //grand total
        $querytot = "select sum(TOT1) TOT1, sum(TOTtarget) TOTtarget, sum(TOT2) TOT2 from $tmp0";
        $resultstot = DB::query($querytot);
        foreach ($resultstot as $rt) {
            $sls1=$rt['TOT1'];
            $trg=$rt['TOTtarget'];
            if ($rt['TOTtarget']==0)
                $ach=0;
            else
                $ach=$rt['TOT1']/$rt['TOTtarget']*100;
            $sls2=$rt['TOT2'];
            if ($rt['TOT2']==0)
                $grw=0;
            else
                $grw=($rt['TOT1']-$rt['TOT2'])/$rt['TOT2']*100;
            echo "<tr style='background-color: #cccfff;'>";
            echo "<td nowrap></td>";
            echo "<td nowrap colspan=2><b>Grand Total : </b></td>";
            echo "<td align='right' class='divnone'></td>";
            echo "<td align='right'>".number_format($sls1,0,",",",")."</td>";
            echo "<td align='right'>".number_format($trg,0,",",",")."</td>";
            echo "<td align='right'>".round($ach,2)."</td>";
            echo "<td align='right'>".number_format($sls2,0,",",",")."</td>";
            echo "<td align='right'>".round($grw,2)."</td>";
            echo "</tr>";
        }
        
        
        
    ?>
    </tbody>
</table>
<?PHP
    $results1 = DB::query("drop table $tmp0");
    $results1 = DB::query("drop table $tmp1");
    $results1 = DB::query("drop table $tmp2");
?>