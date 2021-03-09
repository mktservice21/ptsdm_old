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
                echo "<tr><td><b>SALES YTD PRODUK & DIVISI BY UNIT</b></td></tr>";
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
    $tmp0 ="DTSALESCABDIVYTDXX00".$karyawanid."_$now$milliseconds";
    $tmp1 ="DTSALESCABDIVYTDXX01".$karyawanid."_$now$milliseconds";
    $tmp2 ="DTSALESCABDIVYTDXX02".$karyawanid."_$now$milliseconds";
    $tmp3 ="DTSALESCABDIVYTDXX03".$karyawanid."_$now$milliseconds";
    
    

    
    DB::useDB("dbtemp");
    
    $query = "select date_format(s.bulan,'%Y%m') thnbln, s.kategori, s.divprodid, s.iprodid, s.hna_sales, 
        sum(ytd_qty_sales) sqty1, sum(ytd_value_sales) stvalue1,
        sum(ytd_qty_target) tqty, sum(ytd_value_target) ttvalue,
        sum(ytd_qty_thnlalu) sqty2, sum(ytd_value_thnlalu) stvalue2
        from sls.ytd as s
        where date_format(s.bulan,'%Y%m') = '$filblnprod1'
        and s.region='$region'
        GROUP BY 1,2,3,4,5";
    $results1 = DB::query("create table $tmp1($query)");
    
    
    
    $query="select distinct CAST('' AS CHAR(50)) AS kategori, divprodid, iprodid, nama, CAST(NULL as DECIMAL(30,2)) as hna from  sls.iproduk WHERE divprodId in (select distinct divprodid from $tmp1)";
    $results1 = DB::query("create table $tmp0($query)");
    
    //UPDATE KATEGORU 
    $query="update $tmp0 as a set a.kategori=ifnull((select distinct b.kategori from $tmp1 as b where a.iprodid=b.iprodid and a.divprodid=b.divprodid),0) "
            . " where a.iprodid in (select distinct iprodid from $tmp1)";
    $results1 = DB::query($query);
    $results1 = DB::query("update $tmp0 as a set a.kategori='EXISTING' where ifnull(a.kategori,'')=''");
    
    
    
    //update HNA dari SALES
    $query="update $tmp0 as a set a.hna=ifnull((select b.hna_sales from $tmp1 as b where a.iprodid=b.iprodid and a.divprodid=b.divprodid),0) "
            . " where a.iprodid in (select distinct iprodid from $tmp1)";
    $results1 = DB::query($query);
    
    
    $results1 = DB::query("alter table $tmp0 ADD QTOT1 DOUBLE(20,2), ADD QTOTtarget DOUBLE(20,2), ADD QTOTach DOUBLE(20,2), ADD QTOT2 DOUBLE(20,2), ADD QTOTgrw DOUBLE(20,2)");
    $results1 = DB::query("alter table $tmp0 ADD TOT1 DOUBLE(20,2), ADD TOTtarget DOUBLE(20,2), ADD TOTach DOUBLE(20,2), ADD TOT2 DOUBLE(20,2), ADD TOTgrw DOUBLE(20,2)");
    
    //sales skrang
    $query="update $tmp0 set QTOT1=ifnull((select sum(sqty1) from $tmp1 where $tmp0.iprodid=$tmp1.iprodid and $tmp0.divprodid=$tmp1.divprodid),0)";
    $results1 = DB::query($query);
    //sales lalu
    $query="update $tmp0 set QTOT2=ifnull((select sum(sqty2) from $tmp1 where $tmp0.iprodid=$tmp1.iprodid and $tmp0.divprodid=$tmp1.divprodid),0)";
    $results1 = DB::query($query);
    //target
    $query="update $tmp0 set QTOTtarget=ifnull((select sum(tqty) from $tmp1 where $tmp0.iprodid=$tmp1.iprodid and $tmp0.divprodid=$tmp1.divprodid),0)";
    $results1 = DB::query($query);
    //ach
    $query="update $tmp0 set QTOTach=QTOT1/QTOTtarget*100";
    $results1 = DB::query($query);
    //growth
    $query="update $tmp0 set QTOTgrw=(QTOT1-QTOT2)/QTOT2*100";
    $results1 = DB::query($query);
    
    //VALUE
    //sales skrang
    $query="update $tmp0 set TOT1=ifnull((select sum(stvalue1) from $tmp1 where $tmp0.iprodid=$tmp1.iprodid and $tmp0.divprodid=$tmp1.divprodid),0)";
    $results1 = DB::query($query);
    //sales lalu
    $query="update $tmp0 set TOT2=ifnull((select sum(stvalue2) from $tmp1 where $tmp0.iprodid=$tmp1.iprodid and $tmp0.divprodid=$tmp1.divprodid),0)";
    $results1 = DB::query($query);
    //target
    $query="update $tmp0 set TOTtarget=ifnull((select sum(Ttvalue) from $tmp1 where $tmp0.iprodid=$tmp1.iprodid and $tmp0.divprodid=$tmp1.divprodid),0)";
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
            <th>Sales YTD <?PHP echo $thnbln; ?></th>
            <th>Target YTD <?PHP echo $thnbln; ?></th>
            <th>Ach %</th>
            <th>Sales YTD <?PHP echo $thnbln2; ?></th>
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
            
            
            //if ($divisi=="PEACO") {
                $no=1;
            //}else{
            //}
            
            $queryk = "select distinct divprodid, kategori from $tmp0 where divprodid='$divisi' order by divprodid, kategori";
            $resultsk = DB::query($queryk);
            foreach ($resultsk as $rk) {
                $kategori=$rk['kategori'];
                
                //if ($divisi=="PEACO") {
                //}else{
                    echo "<tr>";
                    echo "<td></td>";
                    echo "<td nowrap colspan='7'><b>$kategori</b></td>";
                    echo "<td align='right' class='divnone'></td>";
                    echo "<td align='right' class='divnone'></td>";
                    echo "<td align='right' class='divnone'></td>";
                    echo "<td align='right' class='divnone'></td>";
                    echo "<td align='right' class='divnone'></td>";
                    echo "<td align='right' class='divnone'></td>";
                    echo "</tr>";
                    $no=1;
                //}
                
                $query2 = "select * from $tmp0 where divprodid='$divisi' and kategori='$kategori' order by nama";
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
                
                //if ($divisi=="PEACO") {
                //}else{
                    //sub total divisi kategori
                    $querytot = "select sum(TOT1)/1000 TOT1, sum(TOTtarget)/1000 TOTtarget, sum(TOT2)/1000 TOT2 from $tmp0 where divprodid='$divisi' and kategori='$kategori'";
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
                        echo "<td nowrap colspan=2><b>Total $divisi - $kategori : </b></td>";
                        echo "<td align='right' class='divnone'></td>";
                        echo "<td align='right'>".number_format($sls1,0,",",",")."</td>";
                        echo "<td align='right'>".number_format($trg,0,",",",")."</td>";
                        echo "<td align='right'>".round($ach,2)."</td>";
                        echo "<td align='right'>".number_format($sls2,0,",",",")."</td>";
                        echo "<td align='right'>".round($grw,2)."</td>";
                        echo "</tr>";
                    }
                //}
                
                
            }
            
            //sub total divisi
            $querytot = "select sum(TOT1)/1000 TOT1, sum(TOTtarget)/1000 TOTtarget, sum(TOT2)/1000 TOT2 from $tmp0 where divprodid='$divisi'";
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
                echo "<tr style='background-color: #99ffff;'>";
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
        $querytot = "select sum(TOT1)/1000 TOT1, sum(TOTtarget)/1000 TOTtarget, sum(TOT2)/1000 TOT2 from $tmp0";
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
?>