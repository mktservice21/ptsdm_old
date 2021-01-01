<?php
if ($pilihdarims==true) {
    $karyawanid=$_SESSION['IDCARD'];
    $ptgl=$_POST['bulan'];
    $bulan = date("Y-m-01", strtotime($ptgl));
    $date=date_create($bulan);
    $region=$_POST["cb_region"];
    
    $namaregion="";
    if ($region=="B"){ $namaregion="Barat";} else if ($region=="T"){$namaregion="Timur";}
    
    $link="#";
    $mylink="../ptsdm/mysdm/";
    
    require_once 'module/a_new/meekrodb.2.3.class.php';
    
    //echo "$karyawanid - $bulan, $region - $namaregion"; exit;
}else{
    $bulan=$_GET["bulan"];
    $date=date_create($bulan);
    $region=$_GET["region"];
    if ($region=="B"){ $namaregion="Barat";} else if ($region=="T"){$namaregion="Timur";}
    $karyawanid=$_GET["id"];
    $namatabel="tmp.ytd_region".$karyawanid;
    $link =$_GET["link"]."/report/lpb_region.aspx";
    $mylink="../ptsdm/mysdm/";

    require_once 'meekrodb.2.3.class.php';
}
?>



<style>
    .divnone {
        display: none;
    }
    
</style>
<h4><b><a href="<?php echo $link ; ?>">back</a></b></h4>
<h3>
<?php

    $printdate= date("d/m/Y");
    echo "<table width='50%' align='left' border='0' class='table table-striped table-bordered'>";
        echo "<tr>";
            echo "<td valign='top'>";
                echo "<table border='0' width='40%'>";
                echo "<tr><td><small style='color:blue;'>PT. Surya Dermato Medica</small></td></tr>";
                echo "<tr><td>Nama Region : $namaregion</td></tr>";
                echo "</table>";
            echo "</td>";
            echo "<td valign='top'>";
                echo "<table align='left' border='0' width='100%'>";
                echo "<tr><td align='left'><h2>LPB ETHICAL - Laporan Penjualan Barang</h2></td></tr>";
                echo "<tr><td align='left'><b>Periode : ".date_format($date,"F Y")."</b></td></tr>";
                echo "<tr><td align='left'><small><i>View Date : $printdate</i></small></td></tr>";
                echo "</table>";
            echo "</td>";
        echo "</tr>";
    echo "</table><hr>&nbsp;</hr>";
    
    $now=date("mdYhis");
    $tmp1 ="DTSALESLPBREGIONXX01_$now";
    $tmp2 ="DTSALESLPBREGIONXX02_$now";
    $tmp3 ="DTSALESLPBREGIONXX03_$now";
    $tmp4 ="DTSALESLPBREGIONXX04_$now";
    $tmp5 ="DTSALESLPBREGIONXX05_$now";
    
    $filbln=date_format($date,"Ymd");
    $hari_ini =date_format($date,"Y-m-d");
    $tgl_pertama = date('Y-m-01', strtotime($hari_ini));
    $tgl_terakhir = date('Y-m-t', strtotime($hari_ini));
    
    $filblnprod=date_format($date,"Ym");
    
    $query = "SELECT
	s.divprodid, CONCAT(
		s.`initial`,
		'-',
		s.`ecabangid`
	) initialcab, s.iprodid, 
	ip.`nama` nama_produk,
	SUM(s.`qty`) AS qty,
	SUM(s.`qty` * s.hna) AS tvalue
        FROM sls.mr_sales2 s
        JOIN ms.iproduk ip ON s.`iprodid` = ip.`iprodid`
        WHERE s.tgljual BETWEEN '$tgl_pertama'
        AND '$tgl_terakhir'
        AND s.`icabangid` IN (
                SELECT DISTINCT a.`icabangid`
                FROM ms.`cabangareaytd` a
                JOIN ms.`cbgytd` b ON a.`idcbg` = b.`idcabang`
                WHERE b.`region` = '$region'
        )
        GROUP BY 1, 2, 3, 4";
    
    DB::useDB("dbtemp");
    $results1 = DB::query("create table $tmp2($query)");
    
    $results1 = DB::query("create table $tmp4(select divprodid, initialcab, sum(tvalue) as tvalue from $tmp2 group by 1, 2)");
    
    
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
            . " on a.iprodid=b.iprodid where Date_Format(a.bulan,'%Y%m') = '$filblnprod' "
            . " and a.iprodid not in (select distinct iprodid from $tmp1)";
    $results1 = DB::query("create table $tmp5($query)");
    $results1 = DB::query("insert into $tmp1 (divprodid, iprodid, nama_produk)select DivProdId, iprodid, nama from $tmp5");

?>
</h3>

    <table id="datatable1" class="display  table table-striped table-bordered" style="width:100%" border='0px'>
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

<script>
$(document).ready(function() {
    var groupColumn = 1;
    $('#datatable1, #datatable2, #datatable3, #datatable4, #datatable5, #datatable6, #datatable7, #datatable8, #datatable9, #datatable10').DataTable( {
        fixedHeader: true,
        "ordering": false,
        "lengthMenu": [[500, 1000, 1500, -1], [500, 1000, 1500, "All"]],
        "columnDefs": [
            { "visible": false },
            { className: "text-right", "targets": [1,2,3,4,5,6,7,8,9,10] }//,//right
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