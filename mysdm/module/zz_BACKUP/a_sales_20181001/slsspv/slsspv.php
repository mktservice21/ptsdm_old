<script>
$(document).ready(function() {
    var groupColumn = 1;
    $('#datatable1').DataTable( {
        fixedHeader: true,
        "ordering": false,
        "lengthMenu": [[500, 1000, 1500, -1], [500, 1000, 1500, "All"]],
        "columnDefs": [
            { "visible": false },
            { className: "text-right", "targets": [2,3,4] }//,//right
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

    if (isset($_GET['module'])){
        $tgl01=$_POST["bulan"];
        $bulan= date("Y-m-d", strtotime($tgl01));
        $idspv=$_POST["spv"];
        $karyawanid=$_SESSION["IDCARD"];
        $link="";
    }else {
        $bulan=$_GET["bulan"];
        $idspv=$_GET["spv"];
        $karyawanid=$_GET["id"];
        $link =$_GET["link"]."/report/slsspv.aspx";
    }
    
    $date=date_create($bulan);
    $tanggal=date_format($date,"Y-m-d");

    require_once 'meekrodb.2.3.class.php';
    $namaspv = DB::queryFirstField("SELECT nama FROM hrd.karyawan WHERE karyawanId=%s", $idspv);
    
    $thnbln=date_format($date,"F Y");
    $thnbln2=date('F Y', strtotime('-1 year', strtotime($tanggal)));
    $printdate= date("d/m/Y");

    if (!empty($link)) {
        echo "<h4><b><a href='$link'>back</a></b></h4>";
    }
    
    
    $filbln=date_format($date,"Ymd");

    $thnbln=date_format($date,"F Y");
    $printdate= date("d/m/Y");


    $thnbln=date_format($date,"F Y");
    $printdate= date("d/m/Y");
    echo "<table width='90%' align='center' border='0' class='table table-striped table-bordered'>";
        echo "<tr>";
            echo "<td valign='top'>";
                echo "<table border='0' width='100%'>";
                echo "<tr><td><small style='color:blue;'>PT. Surya Dermato Medica</small></td></tr>";
                echo "<tr><td><b>SALES PER SUPERVISOR</b></td></tr>";
                echo "<tr><td>Nama SPV: $namaspv</td></tr>";
				echo "<tr><td>Periode: $thnbln</td></tr>";
                echo "</table>";
            echo "</td>";
            echo "<td>";
                echo "<table align='left' border='0' width='100%'>";
                echo "<tr><td align='left'><h3></h3></td></tr>";
                echo "<tr><td align='center'><b></b></td></tr>";
                echo "<tr><td align='right'><small><i>View Date : $printdate</i></small></td></tr>";
                echo "</table>";
            echo "</td>";
        echo "</tr>";
    echo "</table><hr>&nbsp;</hr>";
    
    
    $milliseconds = round(microtime(true) * 1000);
    $now=date("mdYhis");
    $tmp1 ="DTSALESSPVXX01".$idspv."_$now$milliseconds";
    $tmp2 ="DTSALESSPVXX02".$idspv."_$now$milliseconds";
    $tmp3 ="DTSALESSPVXX03".$idspv."_$now$milliseconds";
    
    $filbln=date_format($date,"Ymd");
    $hari_ini =date_format($date,"Y-m-d");
    $tgl_pertama = date('Y-m-01', strtotime($hari_ini));
    $tgl_terakhir = date('Y-m-t', strtotime($hari_ini));
    
	
    $filblnprod=date_format($date,"Ym");
    
    $query="select s.icabangid, s.areaid, a.nama as nama_area, s.divprodid, s.iprodid, sum(s.qty_sales) as qty, sum(s.qty_sales*s.hna_sales) as tvalue from dbmaster.sales as s
        join MKT.iarea as a on s.areaid=a.areaid and s.icabangid=a.icabangid
        where date_format(s.bulan,'%Y%m') = '$filblnprod' and a.aktif='Y'
        and CONCAT(s.icabangid,s.areaid,s.divprodid) in (select distinct CONCAT(sp.icabangid,sp.areaid,sp.divisiid) from MKT.ispv0 as sp  
        where sp.karyawanid='$idspv')
        group by s.icabangid, s.areaid, a.nama, s.divprodid, s.iprodid";
    DB::useDB("dbtemp");
    $results1 = DB::query("create table $tmp1($query)");
    
    
    $query = "select t0.icabangid, t0.areaId, t0.divprodid, t1.iprodid, sum(t1.target) as qty, sum(t1.hna*t1.target) as tvalue from MKT.target1_new as t1 join MKT.target0_new as t0 
            on t1.targetId=t0.targetId
            where date_format(tgl,'%Y%m')= '$filblnprod' 
            and CONCAT(t0.icabangid,t0.areaId,t0.divProdId) in (select distinct CONCAT(sp.icabangid,sp.areaid,sp.divisiid) from MKT.ispv0 as sp 
            join MKT.iarea as a on sp.areaid=a.areaid where sp.karyawanid='$idspv' and a.aktif='Y')
            GROUP BY t0.icabangid, t0.areaId, t0.divprodid, t1.iprodid";
    
    $results1 = DB::query("create table $tmp2($query)");
            
    $query = "select distinct a.icabangid, a.areaid, a.nama_area, p.divprodid, p.iprodid, p.nama, CAST(0 as DECIMAL(20,2)) as SLS, CAST(0 as DECIMAL(20,2)) as TRG, 
            CAST(0 as DECIMAL(20,2)) as ACH, CAST(0 as DECIMAL(20,2)) as sqty, CAST(0 as DECIMAL(20,2)) as tqty from $tmp1 as a, MKT.iproduk as p
            where p.divprodid in (select distinct divisiid from MKT.ispv0 where karyawanid='$idspv')";
    $results1 = DB::query("create table $tmp3($query)");
    
    $query="UPDATE $tmp3 set SLS=ifnull((select sum(tvalue) from $tmp1 where $tmp3.divprodid=$tmp1.divprodid and $tmp3.iprodid=$tmp1.iprodid and $tmp3.icabangid=$tmp1.icabangid and $tmp3.areaid=$tmp1.areaid),0)";
    $results1 = DB::query($query);
    
    $query="UPDATE $tmp3 set sqty=ifnull((select sum(qty) from $tmp1 where $tmp3.divprodid=$tmp1.divprodid and $tmp3.iprodid=$tmp1.iprodid and $tmp3.icabangid=$tmp1.icabangid and $tmp3.areaid=$tmp1.areaid),0)";
    $results1 = DB::query($query);
    
    $query="UPDATE $tmp3 set TRG=ifnull((select sum(tvalue) from $tmp2 where $tmp3.divprodid=$tmp2.divprodid and $tmp3.iprodid=$tmp2.iprodid  and $tmp3.icabangid=$tmp2.icabangid and $tmp3.areaid=$tmp2.areaid),0)";
    $results1 = DB::query($query);
    
    $query="UPDATE $tmp3 set tqty=ifnull((select sum(qty) from $tmp2 where $tmp3.divprodid=$tmp2.divprodid and $tmp3.iprodid=$tmp2.iprodid  and $tmp3.icabangid=$tmp2.icabangid and $tmp3.areaid=$tmp2.areaid),0)";
    $results1 = DB::query($query);
    
    $query="UPDATE $tmp3 set ACH=ifnull(case when ifnull(TRG,0)=0 then 0 else (SLS/TRG*100) end,0)";
    $results1 = DB::query($query);
?>


<table id="datatable1" class="display  table table-striped table-bordered" style="width:100%" border='0px'>
    <thead>
        <tr>
            <th>No</th>
            <th>Produk</th>
            <th width="100px">Sales</th>
            <th width="100px">Target</th>
            <th>Ach %</th>
        </tr>
    </thead>
    <tbody>
        <?PHP
        $results1 = DB::query("SELECT distinct icabangid, areaid, nama_area, divprodid FROM $tmp3 order by nama_area, areaid, divprodid");
        foreach ($results1 as $r1) {
            $idcabang=$r1['icabangid'];
            $idarea=$r1['areaid'];
            $area=$r1['nama_area'];
            $divisi=$r1['divprodid'];
            echo "<tr style='background-color:#f2efef;'>";
            echo "<td></td>";
            echo "<td><b>$area ($divisi)</b></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "</tr>";
                
            $no=1;
            $results2 = DB::query("SELECT * FROM $tmp3 where icabangid='$idcabang' and areaid='$idarea' and divprodid='$divisi' order by nama, iprodid");
            foreach ($results2 as $r2) {
                $produk=$r2['nama'];
                $sales=0;
                $target=0;
                $ach=0;

                if (!empty($r2['SLS']))
                    $sales=number_format($r2['SLS'],0,",",",");

                if (!empty($r2['TRG']))
                    $target=number_format($r2['TRG'],0,",",",");

                if (!empty($r2['ACH']))
                    $ach=$r2['ACH'];

                $sqty=0;
                $tqty=0;
                if (!empty($r2['sqty']))
                    $sqty=number_format($r2['sqty'],0,",",",");
                
                if (!empty($r2['tqty']))
                    $tqty=number_format($r2['tqty'],0,",",",");

                echo "<tr>";
                echo "<td>$no</td>";
                echo "<td>$produk</td>";
                echo "<td align='right'>$sqty</td>";
                echo "<td align='right'>$tqty</td>";
                echo "<td align='right'>$ach</td>";
                echo "</tr>";

                $no++;
            }
            
            //sub total
            $resultssub1 = DB::query("SELECT sum(SLS) as SLS, sum(TRG) as TRG, sum(SLS*TRG/100) as ACH FROM $tmp3 where icabangid='$idcabang' and areaid='$idarea' and divprodid='$divisi'");
            foreach ($resultssub1 as $s1) {
                $sales=0;
                $target=0;
                $ach=0;

                if (!empty($s1['SLS']))
                    $sales=number_format($s1['SLS'],0,",",",");

                if (!empty($s1['TRG']))
                    $target=number_format($s1['TRG'],0,",",",");

                if (!empty($s1['ACH']))
                    //$ach=number_format($s1['ACH'],0,",",",");
                
                if ($target>0) {
                    $ach=round((double)$s1['SLS']/(double)$s1['TRG']*100,2);
                }
                

                echo "<tr style='background-color:#ccffff;'>";
                echo "<td></td>";
                echo "<td><b>Total $area ($divisi) : </b></td>";
                echo "<td align='right'>$sales</td>";
                echo "<td align='right'>$target</td>";
                echo "<td align='right'>$ach</td>";
                echo "</tr>";
                
            }
            
        }
        
        //grand total
        /*
        $resultssub2 = DB::query("SELECT sum(SLS) as SLS, sum(TRG) as TRG FROM $tmp3");
        foreach ($resultssub2 as $s2) {
            $sales=0;
            $target=0;
            $ach=0;

            if (!empty($s2['SLS']))
                $sales=number_format($s2['SLS'],0,",",",");

            if (!empty($s2['TRG']))
                $target=number_format($s2['TRG'],0,",",",");

            if (!empty($s2['ACH']))
                $ach=number_format($s2['ACH'],0,",",",");

            if ($target>0) {
                $ach=round((double)$sales/(double)$target*100,2);
            }


            echo "<tr>";
            echo "<td></td>";
            echo "<td><b>Grand Total Area : </b></td>";
            echo "<td align='right'>$sales</td>";
            echo "<td align='right'>$target</td>";
            echo "<td align='right'>$ach</td>";
            echo "</tr>";

        }
        */
        
        //REKAP DIVISI
        echo "<tr><td></td><td></td><td></td><td></td><td></td></tr>";
        echo "<tr style='background-color:#cccccc;'>";
        echo "<td></td>";
        echo "<td><b>REKAP DIVISI</b></td>";
        echo "<td align='right'></td>";
        echo "<td align='right'></td>";
        echo "<td align='right'></td>";
        echo "</tr>";
        
            
        $results1 = DB::query("SELECT distinct divprodid FROM $tmp3 order by divprodid");
        foreach ($results1 as $r1) {
            $divisi=$r1['divprodid'];
            echo "<tr style='background-color:#f2efef;'>";
            echo "<td></td>";
            echo "<td><b>$divisi</b></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "</tr>";
            
            $no=1;
            $results2 = DB::query("SELECT nama, iprodid, sum(sqty) sqty, sum(tqty) tqty, sum(SLS) as SLS, sum(TRG) as TRG FROM $tmp3 where divprodid='$divisi' group by nama, iprodid order by nama, iprodid");
            foreach ($results2 as $r2) {
                $produk=$r2['nama'];
                $sales=0;
                $target=0;
                $ach=0;

                if (!empty($r2['SLS']))
                    $sales=number_format($r2['SLS'],0,",",",");

                if (!empty($r2['TRG']))
                    $target=number_format($r2['TRG'],0,",",",");

                if ($target>0) {
                    $ach=round((double)$sales/(double)$target*100,2);
                }
                
                $sqty=0;
                $tqty=0;
                if (!empty($r2['sqty']))
                    $sqty=number_format($r2['sqty'],0,",",",");
                
                if (!empty($r2['tqty']))
                    $tqty=number_format($r2['tqty'],0,",",",");
                

                echo "<tr>";
                echo "<td>$no</td>";
                echo "<td>$produk</td>";
                echo "<td align='right'>$sqty</td>";
                echo "<td align='right'>$tqty</td>";
                echo "<td align='right'>$ach</td>";
                echo "</tr>";

                $no++;
            }
            
            //sub total
            $resultssub1 = DB::query("SELECT sum(SLS) as SLS, sum(TRG) as TRG FROM $tmp3 where divprodid='$divisi'");
            foreach ($resultssub1 as $s1) {
                $sales=0;
                $target=0;
                $ach=0;

                if (!empty($s1['SLS']))
                    $sales=number_format($s1['SLS'],0,",",",");

                if (!empty($s1['TRG']))
                    $target=number_format($s1['TRG'],0,",",",");

                if (!empty($s1['ACH']))
                    $ach=number_format($s1['ACH'],0,",",",");
                
                if ($target>0) {
                    $ach=round((double)$sales/(double)$target*100,2);
                }
                

                echo "<tr style='background-color:#ccffff;'>";
                echo "<td></td>";
                echo "<td><b>Total $divisi : </b></td>";
                echo "<td align='right'>$sales</td>";
                echo "<td align='right'>$target</td>";
                echo "<td align='right'>$ach</td>";
                echo "</tr>";
                
            }
            
        }
        
        //grand total
        $resultssub2 = DB::query("SELECT sum(SLS) as SLS, sum(TRG) as TRG FROM $tmp3");
        foreach ($resultssub2 as $s2) {
            $sales=0;
            $target=0;
            $ach=0;

            if (!empty($s2['SLS']))
                $sales=number_format($s2['SLS'],0,",",",");

            if (!empty($s2['TRG']))
                $target=number_format($s2['TRG'],0,",",",");

            if (!empty($s2['ACH']))
                $ach=number_format($s2['ACH'],0,",",",");

            if ($target>0) {
                $ach=round((double)$sales/(double)$target*100,2);
            }


            echo "<tr style='background-color:#cccccc;'>";
            echo "<td></td>";
            echo "<td><b>Grand Total : </b></td>";
            echo "<td align='right'>$sales</td>";
            echo "<td align='right'>$target</td>";
            echo "<td align='right'>$ach</td>";
            echo "</tr>";

        }
        
        
        //REKAP DIVISI
        ?>
    </tbody>
</table>
        


<?PHP
    $results1 = DB::query("drop table $tmp1");
    $results1 = DB::query("drop table $tmp2");
    $results1 = DB::query("drop table $tmp3");
?>



