<?php
$bulan=$_GET["bulan"];
$date=date_create($bulan);
$region=$_GET["region"];
if ($region=="B"){ $namaregion="Barat";} else if ($region=="T"){$namaregion="Timur";}
$idsm=$_GET["sm"];
$karyawanid=$_GET["id"];
$namatabel="tmp.ytd_sm".$karyawanid;
$link =$_GET["link"]."/report/ytd_sm.aspx";

require_once 'meekrodb.2.3.class.php';
//echo $bulan."<br>";
//echo $region."<br>";
//echo $cbgytd."<br>";
$namasm = DB::queryFirstField("SELECT nama FROM hrd.karyawan WHERE karyawanid=%s", $idsm);
?>

<!DOCTYPE html>
<html>

<head>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jq-3.2.1/jszip-2.5.0/dt-1.10.16/b-1.5.1/b-flash-1.5.1/b-html5-1.5.1/fc-3.2.4/fh-3.1.3/r-2.2.1/sl-1.2.5/datatables.min.css"/>
 
 <script type="text/javascript" src="https://cdn.datatables.net/v/dt/jq-3.2.1/jszip-2.5.0/dt-1.10.16/b-1.5.1/b-flash-1.5.1/b-html5-1.5.1/fc-3.2.4/fh-3.1.3/r-2.2.1/sl-1.2.5/datatables.min.js"></script>

<script type="text/javascript">
$(document).ready( function () {
    $('#report').DataTable({
        paging:false,
        searching:false,
        ordering:false,
        fixedHeader: true,
        "info":false,
        select:true,
        buttons: [
        'excel'
    ]
    });
} );

</script>

</head>

<body>
<div class="container-fluid">
<h4><b><a href="<?php echo $link ; ?>">back</a></b></h4>
  <h3>
    Report Sales YTD
    <?php
        echo date_format($date,"F Y")."<br>";
        echo "Nama Region: ".$namaregion."<br>";
        echo "Nama SM: ".$namasm."<br>";
    ?>
    </h3><hr>
<table id="report" class="table table-bordered table-hovered compact display cell-border" font>
    <thead>
        <tr>
            <th rowspan="2">Produk</th>
            <th rowspan="2">HNA</th>
            <th colspan="5">Monthly</th>
            <th colspan="5">Year to Date</th>
            <th colspan="2">Year</th>
        <tr>
            <th>Target</th>
            <th>Sales</th>
            <th>Ach</th>
            <th>Last Year</th>
            <th>Growth</th>
            <th>Target</th>
            <th>Sales</th>
            <th>Ach</th>
            <th>Last Year</th>
            <th>Growth</th>
            <th>Year Target</th>
            <th>Ach Year</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $results1 = DB::query("SELECT DISTINCT divprodid FROM %l",$namatabel);
            foreach ($results1 as $r1) {
                $divprodid=$r1['divprodid'];
                echo "<tr><td colspan=\"2\"><b>" . $r1['divprodid'] . "</b></td><td colspan=\"12\"></td>\n</tr>\n";

                $result2 = DB::query("SELECT DISTINCT kategori FROM %l0 WHERE divprodid=%s1",$namatabel,$divprodid);
                foreach ($result2 as $r2){
                    $kategori=$r2['kategori'];
                    echo "<tr><td align='center' colspan='2'><b>".$kategori."</b></td>\n";
                    echo "<td align=\"center\" colspan=\"12\"></td></tr>\n";

                    $result6 = DB::query("SELECT
                    ip.`nama` AS namaproduk,
                    s.hna_sales,
                    IFNULL(SUM(s.mtd_qty_target),0) AS mtd_target,
                    IFNULL(SUM(s.mtd_qty_sales),0) AS mtd_sales,
                    IFNULL(FORMAT((SUM(s.mtd_qty_sales)/SUM(s.mtd_qty_target))*100,2),0) AS mtd_ach,
                    IFNULL(SUM(s.mtd_qty_thnlalu),0) AS thnlalu,
                    IFNULL(FORMAT(((SUM(s.mtd_qty_sales)/SUM(s.mtd_qty_thnlalu))*100-100),2),0) AS grw,
                    IFNULL(SUM(s.ytd_qty_target),0) AS ytd_target,
                    IFNULL(SUM(s.ytd_qty_sales),0) AS ytd_sales,
                    IFNULL(FORMAT((SUM(s.ytd_qty_sales)/SUM(s.ytd_qty_target))*100,2),0) AS ytd_ach,
                    IFNULL(SUM(s.ytd_qty_thnlalu),0) AS ytd_thnlalu,
                    IFNULL(FORMAT(((SUM(s.ytd_qty_sales)/SUM(s.ytd_qty_thnlalu))*100-100),2),0) AS ytd_grw,
                    IFNULL(SUM(s.thn_qty_target),0) AS thn_tgt,
                    IFNULL(FORMAT((SUM(s.ytd_qty_sales)/SUM(s.thn_qty_target))*100,2),0) AS ach_year
                  FROM
                    %l0 s JOIN sls.`iproduk` ip
                    ON s.iprodid=ip.iprodid
                  WHERE s.divprodid=%s1 AND s.kategori=%s2
                  GROUP BY ip.`nama`,s.hna_sales",$namatabel,$divprodid,$kategori);

                    foreach ($result6 as $r6){
                        echo "<tr>";
                        echo "<td>".$r6["namaproduk"]."</td>\n";
                        echo "<td>".$r6["hna_sales"]."</td>\n";
                        echo "<td>".number_format($r6["mtd_target"],0,",","")."</td>\n";
                        echo "<td>".number_format($r6["mtd_sales"],0,",","")."</td>\n";
                        echo "<td>".$r6["mtd_ach"]."</td>\n";
                        echo "<td>".number_format($r6["thnlalu"],0,",","")."</td>\n";
                        echo "<td>".$r6["grw"]."</td>\n";
                        echo "<td>".number_format($r6["ytd_target"],0,",","")."</td>\n";
                        echo "<td>".number_format($r6["ytd_sales"],0,",","")."</td>\n";
                        echo "<td>".$r6["ytd_ach"]."</td>\n";
                        echo "<td>".number_format($r6["ytd_thnlalu"],0,",","")."</td>\n";
                        echo "<td>".$r6["ytd_grw"]."</td>\n";
                        echo "<td>".number_format($r6["thn_tgt"],0,",","")."</td>\n";
                        echo "<td>".$r6["ach_year"]."</td>\n";
                        echo "</tr>\n";
                    }

                    $result5 = DB::query("SELECT
                    IFNULL(SUM(mtd_value_target),0) AS mtd_target,
                    IFNULL(SUM(mtd_value_sales),0) AS mtd_sales,
                    IFNULL(FORMAT((SUM(mtd_value_sales)/SUM(mtd_value_target))*100,2),0) AS mtd_ach,
                    IFNULL(SUM(mtd_value_thnlalu),0) AS thnlalu,
                    IFNULL(FORMAT(((SUM(mtd_value_sales)/SUM(mtd_value_thnlalu))*100-100),2),0) AS grw,
                    IFNULL(SUM(ytd_value_target),0) AS ytd_target,
                    IFNULL(SUM(ytd_value_sales),0) AS ytd_sales,
                    IFNULL(FORMAT((SUM(ytd_value_sales)/SUM(ytd_value_target))*100,2),0) AS ytd_ach,
                    IFNULL(SUM(ytd_value_thnlalu),0) AS ytd_thnlalu,
                    IFNULL(FORMAT(((SUM(ytd_value_sales)/SUM(ytd_value_thnlalu))*100-100),2),0) AS ytd_grw,
                    IFNULL(SUM(thn_value_target),0) AS thn_tgt,
                    IFNULL(FORMAT((SUM(ytd_value_sales)/SUM(thn_value_target))*100,2),0) AS ach_year
                    FROM %l0 WHERE divprodid=%s1 and kategori=%s2",$namatabel,$divprodid,$kategori);


                    foreach ($result5 as $r5){
                        echo "<tr>";
                        echo "<td align=\"right\" colspan=\"2\"><b>Total ".$divprodid." ".$kategori."</b></td>\n";
                        echo "<td><b>".number_format($r5["mtd_target"],0,",","")."</b></td>\n";
                        echo "<td><b>".number_format($r5["mtd_sales"],0,",","")."</b></td>\n\n";
                        echo "<td><b>".$r5["mtd_ach"]."</b></td>\n";
                        echo "<td><b>".number_format($r5["thnlalu"],0,",","")."</b></td>\n";
                        echo "<td><b>".$r5["grw"]."</b></td>\n";
                        echo "<td><b>".number_format($r5["ytd_target"],0,",","")."</b></td>\n";
                        echo "<td><b>".number_format($r5["ytd_sales"],0,",","")."</b></td>\n";
                        echo "<td><b>".$r5["ytd_ach"]."</b></td>\n";
                        echo "<td><b>".number_format($r5["ytd_thnlalu"],0,",","")."</b></td>\n";
                        echo "<td><b>".$r5["ytd_grw"]."</b></td>\n";
                        echo "<td><b>".number_format($r5["thn_tgt"],0,",","")."</b></td>\n";
                        echo "<td><b>".$r5["ach_year"]."</b></td>\n";
                        echo "</tr>\n";
                    }
                }
                
                $result3 = DB::query("SELECT
                IFNULL(SUM(mtd_value_target),0) AS mtd_target,
                IFNULL(SUM(mtd_value_sales),0) AS mtd_sales,
                IFNULL(FORMAT((SUM(mtd_value_sales)/SUM(mtd_value_target))*100,2),0) AS mtd_ach,
                IFNULL(SUM(mtd_value_thnlalu),0) AS thnlalu,
                IFNULL(FORMAT(((SUM(mtd_value_sales)/SUM(mtd_value_thnlalu))*100-100),2),0) AS grw,
                IFNULL(SUM(ytd_value_target),0) AS ytd_target,
                IFNULL(SUM(ytd_value_sales),0) AS ytd_sales,
                IFNULL(FORMAT((SUM(ytd_value_sales)/SUM(ytd_value_target))*100,2),0) AS ytd_ach,
                IFNULL(SUM(ytd_value_thnlalu),0) AS ytd_thnlalu,
                IFNULL(FORMAT(((SUM(ytd_value_sales)/SUM(ytd_value_thnlalu))*100-100),2),0) AS ytd_grw,
                IFNULL(SUM(thn_value_target),0) AS thn_tgt,
                IFNULL(FORMAT((SUM(ytd_value_sales)/SUM(thn_value_target))*100,2),0) AS ach_year
                FROM %l0 WHERE divprodid=%s1",$namatabel,$divprodid);

                foreach ($result3 as $r3){
                    echo "<tr>";
                    echo "<td align=\"right\" colspan=\"2\"><b>Total ".$divprodid."</b></td>\n";
                    echo "<td><b>".number_format($r3["mtd_target"],0,",","")."</b></td>\n";
                    echo "<td><b>".number_format($r3["mtd_sales"],0,",","")."</b></td>\n";
                    echo "<td><b>".$r3["mtd_ach"]."</b></td>\n";
                    echo "<td><b>".number_format($r3["thnlalu"],0,",","")."</b></td>\n";
                    echo "<td><b>".$r3["grw"]."</b></td>\n";
                    echo "<td><b>".number_format($r3["ytd_target"],0,",","")."</b></td>\n";
                    echo "<td><b>".number_format($r3["ytd_sales"],0,",","")."</b></td>";
                    echo "<td><b>".$r3["ytd_ach"]."</b></td>\n";
                    echo "<td><b>".number_format($r3["ytd_thnlalu"],0,",","")."</b></td>\n";
                    echo "<td><b>".$r3["ytd_grw"]."</b></td>\n";
                    echo "<td><b>".number_format($r3["thn_tgt"],0,",","")."</b></td>\n";
                    echo "<td><b>".$r3["ach_year"]."</b></td>\n";
                    echo "</tr>\n";
                }
              }
            
              $result4 = DB::query("SELECT
              IFNULL(SUM(mtd_value_target),0) AS mtd_target,
              IFNULL(SUM(mtd_value_sales),0) AS mtd_sales,
              IFNULL(FORMAT((SUM(mtd_value_sales)/SUM(mtd_value_target))*100,2),0) AS mtd_ach,
              IFNULL(SUM(mtd_value_thnlalu),0) AS thnlalu,
              IFNULL(FORMAT(((SUM(mtd_value_sales)/SUM(mtd_value_thnlalu))*100-100),2),0) AS grw,
              IFNULL(SUM(ytd_value_target),0) AS ytd_target,
              IFNULL(SUM(ytd_value_sales),0) AS ytd_sales,
              IFNULL(FORMAT((SUM(ytd_value_sales)/SUM(ytd_value_target))*100,2),0) AS ytd_ach,
              IFNULL(SUM(ytd_value_thnlalu),0) AS ytd_thnlalu,
              IFNULL(FORMAT(((SUM(ytd_value_sales)/SUM(ytd_value_thnlalu))*100-100),2),0) AS ytd_grw,
              IFNULL(SUM(thn_value_target),0) AS thn_tgt,
              IFNULL(FORMAT((SUM(ytd_value_sales)/SUM(thn_value_target))*100,2),0) AS ach_year
              FROM %l0",$namatabel);

              foreach ($result4 as $r4){
                  echo "<tr>";
                  echo "<td align=\"left\" colspan=\"2\"><b>Total ".$namasm."</b></td>\n";
                  echo "<td><b>".number_format($r4["mtd_target"],0,",","")."</b></td>\n";
                  echo "<td><b>".number_format($r4["mtd_sales"],0,",","")."</b></td>\n";
                  echo "<td><b>".$r4["mtd_ach"]."</b></td>\n";
                  echo "<td><b>".number_format($r4["thnlalu"],0,",","")."</b></td>\n";
                  echo "<td><b>".$r4["grw"]."</b></td>\n";
                  echo "<td><b>".number_format($r4["ytd_target"],0,",","")."</b></td>\n";
                  echo "<td><b>".number_format($r4["ytd_sales"],0,",","")."</b></td>\n";
                  echo "<td><b>".$r4["ytd_ach"]."</b></td>\n";
                  echo "<td><b>".number_format($r4["ytd_thnlalu"],0,",","")."</b></td>\n";
                  echo "<td><b>".$r4["ytd_grw"]."</b></td>\n";
                  echo "<td><b>".number_format($r4["thn_tgt"],0,",","")."</b></td>\n";
                  echo "<td><b>".$r4["ach_year"]."</b></td>\n";
                  echo "</tr>\n";
              }
        ?>
    </tbody>
</table>
</div>
</body>
</html>