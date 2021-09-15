<script>
$(document).ready(function() {
    var groupColumn = 1;
    $('#datatable1').DataTable( {
        fixedHeader: true,
        "ordering": false,
        "lengthMenu": [[500, 1000, 1500, -1], [500, 1000, 1500, "All"]],
        "columnDefs": [
            { "visible": false },
            { className: "text-right", "targets": [2,3,4,5,6,7,8] }//,//right
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
</style>

<?php

    if ($pilihdarims==true) {
        $tgl01=$_POST["bulan"];
        $bulan= date("Y-m-d", strtotime($tgl01));
        $idkaryawan=$_POST["cb_karyawanid"];
        $karyawanid=$_SESSION["IDCARD"];
        $link="";
        
        require_once 'module/a_new/meekrodb.2.3.class.php';
    }else {
        $bulan=$_GET["bulan"];
        $idkaryawan=$_GET["mr"];
        $karyawanid=$_GET["id"];
        $link =$_GET["link"]."/report/ytdmr.aspx";
        
        require_once 'meekrodb.2.3.class.php';
    }
    
    $date=date_create($bulan);
    $tanggal=date_format($date,"Y-m-d");
    
    $namakry = DB::queryFirstField("SELECT nama FROM hrd.karyawan WHERE karyawanId=%s", $idkaryawan);
    
    $thnbln=date_format($date,"F Y");
    $thnbln2=date('F Y', strtotime('-1 year', strtotime($tanggal)));
    
    $printdate= date("d/m/Y");

    if (!empty($link)) {
        echo "<h4><b><a href='$link'>back</a></b></h4>";
    }
    
    
    $filbln=date_format($date,"Ymd");
    $filblnprod1=date_format($date,"Y01");
    $filblnprod2=date_format($date,"Ym");
    
    $tgllalu=date('Y-m-d', strtotime('-1 year', strtotime($tanggal)));
    $lalufilblnprod1=date('Y01', strtotime('-1 year', strtotime($tanggal)));
    $lalufilblnprod2=date('Ym', strtotime('-1 year', strtotime($tanggal)));
    
    
    $thnbln=date_format($date,"F Y");
    $printdate= date("d/m/Y");
    echo "<table width='90%' align='center' border='0' class='table table-striped table-bordered'>";
        echo "<tr>";
            echo "<td valign='top'>";
                echo "<table border='0' width='100%'>";
                echo "<tr><td><small style='color:blue;'>PT. Surya Dermato Medica</small></td></tr>";
                echo "<tr><td><b>SALES YTD PER MEDICAL REPRESENTATIVE</b></td></tr>";
                echo "<tr><td>MR : $namakry</td></tr>";
                echo "<tr><td>Periode : $thnbln</td></tr>";
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
    $tmp0 ="DTSALESMRYTDF00".$karyawanid."_$now$milliseconds";
    $tmp1 ="DTSALESMRYTDF01".$karyawanid."_$now$milliseconds";
    $tmp2 ="DTSALESMRYTDF02".$karyawanid."_$now$milliseconds";
    $tmp3 ="DTSALESMRYTDF03".$karyawanid."_$now$milliseconds";
    
    
    DB::useDB("dbtemp");
    
    $query = "select DATE_FORMAT(bulan,'%Y%m') bulan, divprodid, iprodid, hna_sales, sum(qty_target) tqty, 
        sum(value_target) ttvalue, sum(qty_sales) sqty, sum(value_sales) tsvalue
        from ms.sales_mr s WHERE karyawanid='$idkaryawan'
        AND ( (DATE_FORMAT(bulan,'%Y%m') BETWEEN '$filblnprod1' AND '$filblnprod2') OR (DATE_FORMAT(bulan,'%Y%m') BETWEEN '$lalufilblnprod1' AND '$lalufilblnprod2') )
        GROUP BY 1,2,3,4";
    $results1 = DB::query("create table $tmp1($query)");
    //create index temp dist
    $results1 = DB::query("CREATE INDEX inx on $tmp1 (bulan, divprodid, iprodid)");
    
    
    $query="select distinct divprodid, iprodid, nama, CAST(NULL as DECIMAL(30,2)) as hna from sls.iproduk WHERE divprodId in (select distinct divprodid from $tmp1)";
    $results1 = DB::query("create table $tmp0($query)");
    //create index temp dist
    $results1 = DB::query("CREATE INDEX inx on $tmp0 (divprodid, iprodid)");
    
    $query = "alter table $tmp0 ADD mtqty DOUBLE(32,2), ADD mtvalue DOUBLE(32,2), "
            . "ADD msqty DOUBLE(32,2), ADD msvalue DOUBLE(32,2), ADD mach DOUBLE(32,2),"
            . "ADD ytqty DOUBLE(32,2), ADD ytvalue DOUBLE(32,2), "
            . "ADD ysqty DOUBLE(32,2), ADD ysvalue DOUBLE(32,2), ADD yach DOUBLE(32,2),"
            . "ADD gmtqty DOUBLE(32,2), ADD gmtvalue DOUBLE(32,2),"
            . "ADD gmsqty DOUBLE(32,2), ADD gmsvalue DOUBLE(32,2), ADD gmach DOUBLE(32,2),"
            . "ADD gytqty DOUBLE(32,2), ADD gytvalue DOUBLE(32,2), "
            . "ADD gysqty DOUBLE(32,2), ADD gysvalue DOUBLE(32,2), ADD gyach DOUBLE(32,2)";
    $results1 = DB::query($query);
    
    $query = "update $tmp0 as a set a.hna=(select b.hna_sales from $tmp1 as b WHERE a.iprodid=b.iprodid order by b.hna_sales desc LIMIT 1) WHERE "
            . " a.iprodid in (select distinct c.iprodid from $tmp1 as c)";
    $results1 = DB::query($query);
    
    //MTD
    $results1 = DB::query("update $tmp0 as a set a.mtqty=ifnull((select sum(b.tqty) from $tmp1 as b WHERE a.iprodid=b.iprodid AND b.bulan='$filblnprod2'),0)");
    $results1 = DB::query("update $tmp0 as a set a.mtvalue=ifnull((select sum(b.ttvalue) from $tmp1 as b WHERE a.iprodid=b.iprodid AND b.bulan='$filblnprod2'),0)");
    $results1 = DB::query("update $tmp0 as a set a.msqty=ifnull((select sum(b.sqty) from $tmp1 as b WHERE a.iprodid=b.iprodid AND b.bulan='$filblnprod2'),0)");
    $results1 = DB::query("update $tmp0 as a set a.msvalue=ifnull((select sum(b.tsvalue) from $tmp1 as b WHERE a.iprodid=b.iprodid AND b.bulan='$filblnprod2'),0)");
    //$results1 = DB::query("update $tmp0 set mach=msvalue/mtvalue*100");//berdasarkan value
	$results1 = DB::query("update $tmp0 set mach=msqty/mtqty*100");//berdasarkan qty
    
    //YTD
    $results1 = DB::query("update $tmp0 as a set a.ytqty=ifnull((select sum(b.tqty) from $tmp1 as b WHERE a.iprodid=b.iprodid AND b.bulan BETWEEN '$filblnprod1' AND '$filblnprod2'),0)");
    $results1 = DB::query("update $tmp0 as a set a.ytvalue=ifnull((select sum(b.ttvalue) from $tmp1 as b WHERE a.iprodid=b.iprodid AND b.bulan BETWEEN '$filblnprod1' AND '$filblnprod2'),0)");
    $results1 = DB::query("update $tmp0 as a set a.ysqty=ifnull((select sum(b.sqty) from $tmp1 as b WHERE a.iprodid=b.iprodid AND b.bulan BETWEEN '$filblnprod1' AND '$filblnprod2'),0)");
    $results1 = DB::query("update $tmp0 as a set a.ysvalue=ifnull((select sum(b.tsvalue) from $tmp1 as b WHERE a.iprodid=b.iprodid AND b.bulan BETWEEN '$filblnprod1' AND '$filblnprod2'),0)");
    //$results1 = DB::query("update $tmp0 set yach=ysvalue/ytvalue*100");//berdasarkan value
    $results1 = DB::query("update $tmp0 set yach=ysqty/ytqty*100");//berdasarkan qty
    
    
    //GRW MTD
    $results1 = DB::query("update $tmp0 as a set a.gmtqty=ifnull((select sum(b.tqty) from $tmp1 as b WHERE a.iprodid=b.iprodid AND b.bulan='$lalufilblnprod2'),0)");
    $results1 = DB::query("update $tmp0 as a set a.gmtvalue=ifnull((select sum(b.ttvalue) from $tmp1 as b WHERE a.iprodid=b.iprodid AND b.bulan='$lalufilblnprod2'),0)");
    $results1 = DB::query("update $tmp0 as a set a.gmsqty=ifnull((select sum(b.sqty) from $tmp1 as b WHERE a.iprodid=b.iprodid AND b.bulan='$lalufilblnprod2'),0)");
    $results1 = DB::query("update $tmp0 as a set a.gmsvalue=ifnull((select sum(b.tsvalue) from $tmp1 as b WHERE a.iprodid=b.iprodid AND b.bulan='$lalufilblnprod2'),0)");
    //$results1 = DB::query("update $tmp0 set gmach=gmsvalue/gmtvalue*100");//berdasarkan value
	$results1 = DB::query("update $tmp0 set gmach=gmsqty/gmtqty*100");//berdasarkan qty
    
    //GRW YTD
    $results1 = DB::query("update $tmp0 as a set a.gytqty=ifnull((select sum(b.tqty) from $tmp1 as b WHERE a.iprodid=b.iprodid AND b.bulan BETWEEN '$lalufilblnprod1' AND '$lalufilblnprod2'),0)");
    $results1 = DB::query("update $tmp0 as a set a.gytvalue=ifnull((select sum(b.ttvalue) from $tmp1 as b WHERE a.iprodid=b.iprodid AND b.bulan BETWEEN '$lalufilblnprod1' AND '$lalufilblnprod2'),0)");
    $results1 = DB::query("update $tmp0 as a set a.gysqty=ifnull((select sum(b.sqty) from $tmp1 as b WHERE a.iprodid=b.iprodid AND b.bulan BETWEEN '$lalufilblnprod1' AND '$lalufilblnprod2'),0)");
    $results1 = DB::query("update $tmp0 as a set a.gysvalue=ifnull((select sum(b.tsvalue) from $tmp1 as b WHERE a.iprodid=b.iprodid AND b.bulan BETWEEN '$lalufilblnprod1' AND '$lalufilblnprod2'),0)");
    //$results1 = DB::query("update $tmp0 set gyach=gysvalue/gytvalue*100");//berdasarkan value
	$results1 = DB::query("update $tmp0 set gyach=gysqty/gytqty*100");//berdasarkan qty
    
    $results1 = DB::query("drop table $tmp1");
	
	/*
	$query = "ALTER table $tmp0 ADD COLUMN kategori VARCHAR(100)";
	$results1 = DB::query($query);
	
	$query = "UPDATE $tmp0 a JOIN 
		(select distinct iprodid, kategori from sls.ytdprod WHERE DATE_FORMAT(bulan,'%Y%m')='$filblnprod1') b 
		on a.iprodid=b.iprodid 
		SET a.kategori=b.kategori";
	$results1 = DB::query($query);
	
	$query = "UPDATE $tmp0 SET divprodid='ZOTH' WHERE kategori='OTHER'";
	$results1 = DB::query($query);
	*/
    
?>


<table id="datatable1" class="display  table table-striped table-bordered" style="width:100%" border='0px'>
    <thead>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Produk</th>
            <th rowspan="2">HNA</th>
            <th colspan="4">Monthly</th>
            <th colspan="4">Year to Date</th>
            <th colspan="3">Last Year (Monthly)</th>
            <!--<th rowspan="2">Growth</th>-->
            <th colspan="3">Last Year (YTD)</th>
            <!--<th rowspan="2">Growth</th>-->
        </tr>
        <tr>
            <th>Target</th>
            <th>Sales</th>
            <th>Ach</th>
            <th>Growth</th>
            
            <th>Ytd. Target</th>
            <th>Ytd. Sales</th>
            <th>Ytd. Ach</th>
            <th>Ytd. Growth</th>
            
            <th>Target</th>
            <th>Sales</th>
            <th>Ach</th>            
            
            <th>Target</th>
            <th>Sales</th>
            <th>Ach</th>
        </tr>
    </thead>
    <tbody>
        <?PHP
        $results1 = DB::query("SELECT distinct divprodid FROM $tmp0 order by divprodid");
        foreach ($results1 as $r1) {
            $divisi=$r1['divprodid'];
			
				$pnmdividprodid=$divisi;
				if ($divisi=="ZOTHER" OR $divisi=="ZOTH") $pnmdividprodid="OTHER";
			
            echo "<tr style='background-color:#f2efef;'>";
            echo "<td></td>";
            echo "<td><b>$pnmdividprodid</b></td>";
            echo "<td></td>";
            
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            
            echo "<td></td>";
            echo "<td></td>";
            
            //echo "<td></td>";
            //echo "<td></td>";
            
            echo "</tr>";
            
            
            $no=1;
            $results2 = DB::query("SELECT * FROM $tmp0 where divprodid='$divisi' order by nama, iprodid");
            foreach ($results2 as $r2) {
                $produk=$r2['nama'];
                $hna=0;
                
                $mtarget=0;
                $mqty=0;
                $mach=0;

                if (!empty($r2['hna'])) $hna=number_format($r2['hna'],0,",",",");
                if (!empty($r2['mtqty'])) $mtarget=number_format($r2['mtqty'],0,",",",");
                if (!empty($r2['msqty'])) $mqty=number_format($r2['msqty'],0,",",",");
                if (!empty($r2['mach'])) $mach=$r2['mach'];
                
                $ytarget=0;
                $yqty=0;
                $yach=0;
                
                if (!empty($r2['ytqty'])) $ytarget=number_format($r2['ytqty'],0,",",",");
                if (!empty($r2['ysqty'])) $yqty=number_format($r2['ysqty'],0,",",",");
                if (!empty($r2['yach'])) $yach=$r2['yach'];

                
                $gmtarget=0;
                $gmqty=0;
                $gmach=0;

                
                if (!empty($r2['gmtqty'])) $gmtarget=number_format($r2['gmtqty'],0,",",",");
                if (!empty($r2['gmsqty'])) $gmqty=number_format($r2['gmsqty'],0,",",",");
                if (!empty($r2['gmach'])) $gmach=$r2['gmach'];
                
                $fgrowthmth=0;
                if (!empty($r2['gmsqty']) AND $r2['gmsqty']!="0") {
                    $fgrowthmth=($r2['msqty']-$r2['gmsqty'])/$r2['gmsqty']*100;
                    $fgrowthmth=round($fgrowthmth,2);
                }
                
                
                $gytarget=0;
                $gyqty=0;
                $gyach=0;
                
                if (!empty($r2['gytqty'])) $gytarget=number_format($r2['gytqty'],0,",",",");
                if (!empty($r2['gysqty'])) $gyqty=number_format($r2['gysqty'],0,",",",");
                if (!empty($r2['gyach'])) $gyach=$r2['gyach'];
                
                $fgrowthytd=0;
                if (!empty($r2['gysqty']) AND $r2['gysqty']!="0") {
                    $fgrowthytd=($r2['ysqty']-$r2['gysqty'])/$r2['gysqty']*100;
                    $fgrowthytd=round($fgrowthytd,2);
                }
                
                echo "<tr>";
                echo "<td>$no</td>";
                echo "<td nowrap>$produk</td>";
                echo "<td align='right'>$hna</td>";
                
                echo "<td align='right'>$mtarget</td>";
                echo "<td align='right'>$mqty</td>";
                echo "<td align='right'>$mach</td>";
                echo "<td align='right'>$fgrowthmth</td>";
                
                echo "<td align='right'>$ytarget</td>";
                echo "<td align='right'>$yqty</td>";
                echo "<td align='right'>$yach</td>";
                echo "<td align='right'>$fgrowthytd</td>";
                
                echo "<td align='right'>$gmtarget</td>";
                echo "<td align='right'>$gmqty</td>";
                echo "<td align='right'>$gmach</td>";
                //echo "<td align='right'></td>";//$fgrowthmth
                
                echo "<td align='right'>$gytarget</td>";
                echo "<td align='right'>$gyqty</td>";
                echo "<td align='right'>$gyach</td>";
                //echo "<td align='right'></td>";//$fgrowthytd
                
                echo "</tr>";

                $no++;
            }
            
            //sub total
            $resultssub1 = DB::query("SELECT sum(mtvalue) as mtvalue, sum(msvalue) as msvalue, sum(msvalue*mtvalue/100) as mach,"
                    . "sum(ytvalue) as ytvalue, sum(ysvalue) as ysvalue, sum(ysvalue*ytvalue/100) as yach, "
                    . " sum(gmtvalue) as gmtvalue, sum(gmsvalue) as gmsvalue, sum(gmsvalue*gmtvalue/100) as gmach, "
                    . " sum(gytvalue) as gytvalue, sum(gysvalue) as gysvalue, sum(gysvalue*gytvalue/100) as gyach "
                    . " FROM $tmp0 where divprodid='$divisi'");
            foreach ($resultssub1 as $s1) {
                
                $produk="<b>Total $pnmdividprodid : </b>";
                
                $mtarget=0;
                $mqty=0;
                $mach=0;
                
                if (!empty($s1['mtvalue'])) $mtarget=number_format($s1['mtvalue'],0,",",",");
                if (!empty($s1['msvalue'])) $mqty=number_format($s1['msvalue'],0,",",",");
                
                if ($s1['mtvalue']>0) {
                    $mach=round($s1['msvalue']/$s1['mtvalue']*100,2);
                }
                
                $ytarget=0;
                $yqty=0;
                $yach=0;
                
                if (!empty($s1['ytvalue'])) $ytarget=number_format($s1['ytvalue'],0,",",",");
                if (!empty($s1['ysvalue'])) $yqty=number_format($s1['ysvalue'],0,",",",");
                
                if ($s1['ytvalue']>0) {
                    $yach=round($s1['ysvalue']/$s1['ytvalue']*100,2);
                }

                
                $gmtarget=0;
                $gmqty=0;
                $gmach=0;
                
                if (!empty($s1['gmtvalue'])) $gmtarget=number_format($s1['gmtvalue'],0,",",",");
                if (!empty($s1['gmsvalue'])) $gmqty=number_format($s1['gmsvalue'],0,",",",");
                
                if ($s1['gmtvalue']>0) {
                    $gmach=round($s1['gmsvalue']/$s1['gmtvalue']*100,2);
                }
                
                $fgrowthmth=0;
                if (!empty($s1['gmsvalue']) AND $s1['gmsvalue']!="0") {
                    $fgrowthmth=($s1['msvalue']-$s1['gmsvalue'])/$s1['gmsvalue']*100;
                    $fgrowthmth=round($fgrowthmth,2);
                }
                
                
                $gytarget=0;
                $gyqty=0;
                $gyach=0;
                
                if (!empty($s1['gytvalue'])) $gytarget=number_format($s1['gytvalue'],0,",",",");
                if (!empty($s1['gysvalue'])) $gyqty=number_format($s1['gysvalue'],0,",",",");
                
                if ($s1['gytvalue']>0) {
                    $gyach=round($s1['gysvalue']/$s1['gytvalue']*100,2);
                }
                
                $fgrowthytd=0;
                if (!empty($s1['gysvalue']) AND $s1['gysvalue']!="0") {
                    $fgrowthytd=($s1['ysvalue']-$s1['gysvalue'])/$s1['gysvalue']*100;
                    $fgrowthytd=round($fgrowthytd,2);
                }
                
                
                
                echo "<tr style='background-color:#ccffff;'>";
                echo "<td></td>";
                echo "<td>$produk</td>";
                echo "<td align='right'></td>";
                
                echo "<td align='right'>$mtarget</td>";
                echo "<td align='right'>$mqty</td>";
                echo "<td align='right'>$mach</td>";
                echo "<td align='right'>$fgrowthmth</td>";
                
                echo "<td align='right'>$ytarget</td>";
                echo "<td align='right'>$yqty</td>";
                echo "<td align='right'>$yach</td>";
                echo "<td align='right'>$fgrowthytd</td>";
                
                echo "<td align='right'>$gmtarget</td>";
                echo "<td align='right'>$gmqty</td>";
                echo "<td align='right'>$gmach</td>";
                //echo "<td align='right'>$fgrowthmth</td>";
                
                echo "<td align='right'>$gytarget</td>";
                echo "<td align='right'>$gyqty</td>";
                echo "<td align='right'>$gyach</td>";
                //echo "<td align='right'>$fgrowthytd</td>";
                
                echo "</tr>";
                
            }
        }
        
        //Grand total
        $resultssub1 = DB::query("SELECT sum(mtvalue) as mtvalue, sum(msvalue) as msvalue, sum(msvalue*mtvalue/100) as mach,"
                . " sum(ytvalue) as ytvalue, sum(ysvalue) as ysvalue, sum(ysvalue*ytvalue/100) as yach, "
                . " sum(gmtvalue) as gmtvalue, sum(gmsvalue) as gmsvalue, sum(gmsvalue*gmtvalue/100) as gmach,"
                . " sum(gytvalue) as gytvalue, sum(gysvalue) as gysvalue, sum(gysvalue*gytvalue/100) as gyach "
                . " FROM $tmp0");
        foreach ($resultssub1 as $s2) {

            $produk="<b>Grand Total : </b>";

            $mtarget=0;
            $mqty=0;
            $mach=0;

            if (!empty($s2['mtvalue'])) $mtarget=number_format($s2['mtvalue'],0,",",",");
            if (!empty($s2['msvalue'])) $mqty=number_format($s2['msvalue'],0,",",",");

            if ($s2['mtvalue']>0) {
                $mach=round($s2['msvalue']/$s2['mtvalue']*100,2);
            }

            $ytarget=0;
            $yqty=0;
            $yach=0;

            if (!empty($s2['ytvalue'])) $ytarget=number_format($s2['ytvalue'],0,",",",");
            if (!empty($s2['ysvalue'])) $yqty=number_format($s2['ysvalue'],0,",",",");

            if ($s2['ytvalue']>0) {
                $yach=round($s2['ysvalue']/$s2['ytvalue']*100,2);
            }
            

            $gmtarget=0;
            $gmqty=0;
            $gmach=0;

            if (!empty($s2['gmtvalue'])) $gmtarget=number_format($s2['gmtvalue'],0,",",",");
            if (!empty($s2['gmsvalue'])) $gmqty=number_format($s2['gmsvalue'],0,",",",");

            if ($s2['gmtvalue']>0) {
                $gmach=round($s2['gmsvalue']/$s2['gmtvalue']*100,2);
            }

            $fgrowthmth=0;
            if (!empty($s2['gmsvalue']) AND $s2['gmsvalue']!="0") {
                $fgrowthmth=($s2['msvalue']-$s2['gmsvalue'])/$s2['gmsvalue']*100;
                $fgrowthmth=round($fgrowthmth,2);
            }
                
            $gytarget=0;
            $gyqty=0;
            $gyach=0;

            if (!empty($s2['gytvalue'])) $gytarget=number_format($s2['gytvalue'],0,",",",");
            if (!empty($s2['gysvalue'])) $gyqty=number_format($s2['gysvalue'],0,",",",");

            if ($s2['gytvalue']>0) {
                $gyach=round($s2['gysvalue']/$s2['gytvalue']*100,2);
            }
            
            $fgrowthytd=0;
            if (!empty($s2['gysvalue']) AND $s2['gysvalue']!="0") {
                $fgrowthytd=($s2['ysvalue']-$s2['gysvalue'])/$s2['gysvalue']*100;
                $fgrowthytd=round($fgrowthytd,2);
            }
                
            echo "<tr style='background-color:#cccccc;'>";
            echo "<td></td>";
            echo "<td>$produk</td>";
            echo "<td align='right'></td>";
            
            echo "<td align='right'>$mtarget</td>";
            echo "<td align='right'>$mqty</td>";
            echo "<td align='right'>$mach</td>";
            echo "<td align='right'>$fgrowthmth</td>";
            
            echo "<td align='right'>$ytarget</td>";
            echo "<td align='right'>$yqty</td>";
            echo "<td align='right'>$yach</td>";
            echo "<td align='right'>$fgrowthytd</td>";
            
            echo "<td align='right'>$gmtarget</td>";
            echo "<td align='right'>$gmqty</td>";
            echo "<td align='right'>$gmach</td>";
            //echo "<td align='right'>$fgrowthmth</td>";
            
            echo "<td align='right'>$gytarget</td>";
            echo "<td align='right'>$gyqty</td>";
            echo "<td align='right'>$gyach</td>";
            //echo "<td align='right'>$fgrowthytd</td>";
            
            echo "</tr>";

        }
        ?>
        
    </tbody>
</table>
<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
<?PHP
    $results1 = DB::query("drop table $tmp0");
?>