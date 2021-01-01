<?php
if ($pilihdarims==true) {
    $karyawanid=$_SESSION['IDCARD'];
    $ptgl=$_POST['bulan'];
    $bulan = date("Y-m-01", strtotime($ptgl));
    $date=date_create($bulan);
    $region=$_POST["cb_region"];
    
    $namaregion="";
    if ($region=="B"){ $namaregion="Barat";} else if ($region=="T"){$namaregion="Timur";}
    $cbgytd=$_POST["cb_cabang"];
    
    $link="#";
    
    require_once 'module/a_new/meekrodb.2.3.class.php';
    
    $namacabangytd = DB::queryFirstField("SELECT nama FROM ms.cbgytd WHERE idcabang=%s", $cbgytd);
    
    
    $namatabel="tmp.ytd_daerah".$karyawanid;
    
    //echo "$karyawanid - $bulan, $region - $namaregion, $cbgytd $namacabangytd"; exit;
    
}else{
    $bulan=$_GET["bulan"];
    $date=date_create($bulan);
    $region=$_GET["region"];
    if ($region=="B"){ $namaregion="Barat";} else if ($region=="T"){$namaregion="Timur";}
    $cbgytd=$_GET["cbgytd"];
    $karyawanid=$_GET["id"];
    $namatabel="tmp.ytd_daerah".$karyawanid;
    $link =$_GET["link"]."/report/lpb_daerah.aspx";

    require_once 'meekrodb.2.3.class.php';
    //echo $bulan."<br>";
    //echo $region."<br>";
    //echo $cbgytd."<br>";
    $namacabangytd = DB::queryFirstField("SELECT nama FROM ms.cbgytd WHERE idcabang=%s", $cbgytd);
}
?>

<script>
$(document).ready(function() {
    var groupColumn = 1;
    $('#datatable1, #datatable2, #datatable3, #datatable4, #datatable5, #datatable6, #datatable7, #datatable8, #datatable9, #datatable10').DataTable( {
        fixedHeader: true,
        "ordering": false,
        "lengthMenu": [[500, 1000, 1500, -1], [500, 1000, 1500, "All"]],
        "columnDefs": [
            { "visible": false },
            { className: "text-right", "targets": [1] }//,//right
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
<h4><b><a href="<?php echo $link ; ?>">back</a></b></h4>
<h3>
<?php

    $printdate= date("d/m/Y");
    echo "<table width='90%' align='center' border='0' class='table table-striped table-bordered'>";
        echo "<tr>";
            echo "<td valign='top'>";
                echo "<table border='0' width='100%'>";
                echo "<tr><td><small style='color:blue;'>PT. Surya Dermato Medica</small></td></tr>";
                echo "<tr><td>Nama Region : $namaregion</td></tr>";
                echo "<tr><td>Nama Daerah : $namacabangytd</td></tr>";
                echo "</table>";
            echo "</td>";
            echo "<td valign='top'>";
                echo "<table align='center' border='0' width='100%'>";
                echo "<tr><td align='center'><h2>LPB ETHICAL - Laporan Penjualan Barang</h2></td></tr>";
                echo "<tr><td align='center'><b>Periode : ".date_format($date,"F Y")."</b></td></tr>";
                echo "<tr><td align='right'><small><i>View Date : $printdate</i></small></td></tr>";
                echo "</table>";
            echo "</td>";
        echo "</tr>";
    echo "</table><hr>&nbsp;</hr>";
    
    $now=date("mdYhis");
    $tmp1 ="DTSALESLPB01_$now";
    $tmp2 ="DTSALESLPB02_$now";
    $tmp3 ="DTSALESLPB03_$now";
    $tmp4 ="DTSALESLPB04_$now";
    $tmp5 ="DTSALESLPB05_$now";
    
    $filbln=date_format($date,"Ym");
    $query="select s.*, c.idcbg from sls.mr_sales2 as s 
        inner JOIN ms.cabangareaytd as c on s.icabangid=c.icabangid and s.areaid=c.areaid
        where Date_Format(s.tgljual,'%Y%m') = '$filbln' and c.idcbg=$cbgytd";
    
    DB::useDB("dbtemp");
    $results1 = DB::query("create table $tmp1($query)");
    
    $query ="select  s.distid, CONCAT(s.initial,'-',s.ecabangid) as initialcab, s.initial, s.ecabangid, 
        s.idcbg, s.icabangid, s.iprodid, p.nama as nama_produk, s.divprodid, sum(s.qty) as qty, SUM(s.qty*s.hna) as tvalue
         from $tmp1 as s INNER JOIN ms.iproduk as p on s.iprodid=p.iProdId and s.divprodid=p.DivProdId
        group by CONCAT(s.initial,'-',s.ecabangid), s.idcbg, s.icabangid, 
        s.iprodid, s.distid, s.initial, s.ecabangid, s.divprodid, p.nama";
    $results1 = DB::query("create table $tmp2($query)");
    
    $results1 = DB::query("create table $tmp4(select divprodid, CONCAT(initial,'-',ecabangid) as initialcab, initial, sum(qty*hna) as tvalue from $tmp1 group by divprodid, CONCAT(initial,'-',ecabangid), initial)");
    
    $results1 = DB::query("drop table $tmp1");
    
    $results1 = DB::query("create table $tmp1(select DISTINCT divprodid, iprodid, nama_produk from $tmp2)");
    $results1 = DB::query("create table $tmp3(select DISTINCT trim(replace(initialcab,'-','')) as initi, initialcab from $tmp2)");
    
    $results1 = DB::query("SELECT * FROM %l order by initi, initialcab",$tmp3);
    foreach ($results1 as $r1) {
        $fil=$r1['initi'];
        $initialcab=$r1['initialcab'];
        $results1 = DB::query("alter table $tmp1 ADD $fil DOUBLE(20,2)");
        
        $query="update $tmp1 set $fil=(select sum(qty) from $tmp2 where initialcab='$initialcab' and "
                . " $tmp1.divprodid=$tmp2.divprodid and $tmp1.iprodid=$tmp2.iprodid)";
        $results1 = DB::query($query);
    }
    $results1 = DB::query("alter table $tmp1 ADD totqty DOUBLE(20,2), add totval DOUBLE(20,2)");
    $results1 = DB::query("update $tmp1 set totqty=(select sum(qty) from $tmp2 where $tmp1.divprodid=$tmp2.divprodid and $tmp1.iprodid=$tmp2.iprodid)");
    $results1 = DB::query("update $tmp1 set totval=(select sum(tvalue) from $tmp2 where $tmp1.divprodid=$tmp2.divprodid and $tmp1.iprodid=$tmp2.iprodid)");
    
    // tambah produk yang tidak ada jualannya
    $query="select distinct b.DivProdId, a.iprodid, b.nama from sls.ytdprod  as a inner join ms.iproduk as b "
            . " on a.iprodid=b.iprodid where Date_Format(a.bulan,'%Y%m') = '$filbln' "
            . " and a.iprodid not in (select distinct iprodid from $tmp1)";
    $results1 = DB::query("create table $tmp5($query)");
    $results1 = DB::query("insert into $tmp1 (divprodid, iprodid, nama_produk)select DivProdId, iprodid, nama from $tmp5");
    
    
?>
</h3>

    <table id="datatable1" class="display  table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th rowspan='2'>Produk</th>
                <?PHP
                $results1 = DB::query("SELECT DISTINCT initialcab FROM %l order by initialcab",$tmp3);
                foreach ($results1 as $r1) {
                    $initialcab=$r1['initialcab'];
                    echo "<th rowspan='2'>$initialcab</th>";
                }
                ?>
                <th colspan='2'>Measure</th>
            </tr>
            <tr>
                <th>Qty</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $totfield = DB::queryFirstField("SELECT count(*) FROM %l",$tmp3);
            $totfield=(int)$totfield+2;
            $no=1;
            $resultsDiv = DB::query("SELECT DISTINCT divprodid FROM %l order by divprodid",$tmp1);
            foreach ($resultsDiv as $rD) {
                $divprod=$rD['divprodid'];
                echo "<tr scope='row'>";
                echo "<td colspan='$totfield'><b>$divprod</b></td>";
                for ($i=1; $i <= (int)$totfield; $i++)
                    echo "<td class='divnone'></td>";
                echo "</tr>";
                
                    
                $results1 = DB::query("SELECT * FROM $tmp1 where divprodid='$divprod' order by divprodid, nama_produk");
                foreach ($results1 as $r1) {
                    echo "<tr scope='row'>";
                    echo "<td>".$r1['nama_produk']."</td>";
                    $results2 = DB::query("SELECT DISTINCT initi FROM %l order by initi",$tmp3);
                    foreach ($results2 as $r2) {
                        $fil=$r2['initi'];
                        echo "<td align='right'>".number_format($r1[$fil],0,",",",")."</td>";
                    }
                    echo "<td align='right'>".number_format($r1["totqty"],0,",",",")."</td>";
                    echo "<td align='right'>".number_format($r1["totval"],0,",",",")."</td>";
                    echo "</tr>";
                }
                ?>
                <tr>
                    <?PHP
                    echo "<td align='center'><b>TOTAL DIVISI $divprod</b></td>";
                    $results3 = DB::query("SELECT DISTINCT initialcab FROM %l order by initialcab",$tmp3);
                    foreach ($results3 as $r3) {
                        $initialcab=$r3['initialcab'];
                        $totvalue = DB::queryFirstField("SELECT tvalue FROM %l where divprodid='$divprod' and initialcab='$initialcab'",$tmp4);
                        echo "<td align='right'><b>".number_format($totvalue,0,",",",")."</b></td>";
                    }
                    $totvalue = DB::queryFirstField("SELECT sum(tvalue) as tvalue FROM %l where divprodid='$divprod'",$tmp4);
                    echo "<td></td>";
                    echo "<td align='right'><b>".number_format($totvalue,0,",",",")."</b></td>";
                    ?>
                </tr>
            <?PHP
                $no++;
            }
            ?>
            <tr>
                <td align='center'><b>GRAND TOTAL  : </b></td>
                <?PHP
                $results3 = DB::query("SELECT DISTINCT initialcab FROM %l order by initialcab",$tmp3);
                foreach ($results3 as $r3) {
                    $initialcab=$r3['initialcab'];
                    $totvalue = DB::queryFirstField("SELECT sum(tvalue) as tvalue FROM %l where initialcab='$initialcab'",$tmp4);
                    echo "<td align='right'><b>".number_format($totvalue,0,",",",")."</b></td>";
                }
                $totvalue = DB::queryFirstField("SELECT sum(tvalue) as tvalue FROM %l",$tmp4);
                echo "<td></td>";
                echo "<td align='right'><b>".number_format($totvalue,0,",",",")."</b></td>";
                ?>
            </tr>
        </tbody>
    </table>
<!--
<table id="datatable<?PHP echo $no; ?>" class="display nowrap table table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th rowspan='2'></th>
            <?PHP
            $results1 = DB::query("SELECT DISTINCT initialcab FROM %l order by initialcab",$tmp3);
            foreach ($results1 as $r1) {
                $initialcab=$r1['initialcab'];
                echo "<th rowspan='2'>$initialcab</th>";
            }
            ?>
            <th>&nbsp;</th>
            <th>Value</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>GRAND TOTAL &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;&nbsp;&nbsp;</td>
            <?PHP /*
            $results3 = DB::query("SELECT DISTINCT initialcab FROM %l order by initialcab",$tmp3);
            foreach ($results3 as $r3) {
                $initialcab=$r3['initialcab'];
                $totvalue = DB::queryFirstField("SELECT sum(tvalue) as tvalue FROM %l where initialcab='$initialcab'",$tmp4);
                echo "<td align='right'>".number_format($totvalue,0,",",",")."</td>";
            }
            $totvalue = DB::queryFirstField("SELECT sum(tvalue) as tvalue FROM %l",$tmp4);
            echo "<td></td>";
            echo "<td align='right'>".number_format($totvalue,0,",",",")."</td>";
            */ ?>
        </tr>
    </tbody>
</table>
-->
<?PHP


$results1 = DB::query("drop table $tmp1");
$results1 = DB::query("drop table $tmp2");
$results1 = DB::query("drop table $tmp3");
$results1 = DB::query("drop table $tmp4");
$results1 = DB::query("drop table $tmp5");

?>

<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;