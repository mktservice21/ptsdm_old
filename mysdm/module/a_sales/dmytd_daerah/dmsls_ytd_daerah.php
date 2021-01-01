<?php
$bulan=$_GET["bulan"];
$date=date_create($bulan);
$region=$_GET["region"];
if ($region=="B"){ $namaregion="Barat";} else if ($region=="T"){$namaregion="Timur";}
$cbgytd=$_GET["cbgytd"];
$karyawanid=$_GET["id"];
$namatabel="tmp.ytd_daerah2".$karyawanid;
$link =$_GET["link"]."/report/ytd_daerah2.aspx";

require_once 'meekrodb.2.3.class.php';
//echo $bulan."<br>";
//echo $region."<br>";
//echo $cbgytd."<br>";
$namacabangytd = DB::queryFirstField("SELECT nama FROM ms.cbgytd WHERE idcabang=%s", $cbgytd);
?>

<script>
$(document).ready(function() {
    var groupColumn = 1;
    $('#datatable').DataTable( {
        fixedHeader: true,
        "ordering": false,
        "lengthMenu": [[500, 1000, 1500, -1], [500, 1000, 1500, "All"]],
        "columnDefs": [
            { "visible": false },
            { className: "text-right", "targets": [1,2,3,4,5,6,7,8,9,10,11] }//,//right
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
                echo "<tr><td align='center'><h2>Report Sales Year To Date (YTD) By Product</h2></td></tr>";
                echo "<tr><td align='center'><b>Periode : ".date_format($date,"F Y")."</b></td></tr>";
                echo "<tr><td align='right'><small><i>View Date : $printdate</i></small></td></tr>";
                echo "</table>";
            echo "</td>";
        echo "</tr>";
    echo "</table><hr>&nbsp;</hr>";
?>
</h3>
    
<table id="datatable" class="display nowrap table table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th rowspan="2">Produk</th>
            <th rowspan="2">HNA</th>
            <th colspan="5">Monthly</th>
            <th colspan="5">Year to Date</th>
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
        </tr>
    </thead>
    <tbody>
        <?php
            $results1 = DB::query("SELECT DISTINCT divprodid FROM %l",$namatabel);
            foreach ($results1 as $r1) {
                $divprodid=$r1['divprodid'];
                
                //echo "<tr><td colspan=\"2\"><b>" . $r1['divprodid'] . "</b></td><td colspan=\"12\"></td>\n</tr>\n";

                echo "<tr scope='row'>";
                echo "<td colspan='2'><b>" . $r1['divprodid'] . "</b></td>";
                echo "<td class='divnone'></td>";
                echo "<td class='divnone'></td>";
                echo "<td colspan='12'></td>";
                echo "<td class='divnone'></td>";
                echo "<td class='divnone'></td>";
                echo "<td class='divnone'></td>";
                echo "<td class='divnone'></td>";
                echo "<td class='divnone'></td>";
                echo "<td class='divnone'></td>";
                echo "<td class='divnone'></td>";
                echo "<td class='divnone'></td>";
                echo "</tr>";
                
                $result2 = DB::query("SELECT DISTINCT kategori FROM %l0 WHERE divprodid=%s1",$namatabel,$divprodid);
                foreach ($result2 as $r2){
                    $kategori=$r2['kategori'];
                    echo "<tr><td align='center' colspan='2'><b>".$kategori."</b></td>"
                            . "<td class='divnone'></td><td class='divnone'></td>\n";
                    echo "<td align=\"center\" colspan=\"12\"></td>"
                    . "<td class='divnone'></td><td class='divnone'></td><td class='divnone'></td><td class='divnone'></td>"
                            . "<td class='divnone'></td><td class='divnone'></td><td class='divnone'></td><td class='divnone'></td>"
                            . "<td class='divnone'></td><td class='divnone'></td></tr>\n";

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
                        $hna_sales=number_format($r6['hna_sales'],0,",",",");
                        echo "<tr>";
                        echo "<td>".$r6["namaproduk"]."</td>\n";
                        echo "<td>".$hna_sales."</td>\n";
                        echo "<td>".number_format($r6["mtd_target"],0,",",",")."</td>\n";
                        echo "<td>".number_format($r6["mtd_sales"],0,",",",")."</td>\n";
                        echo "<td>".$r6["mtd_ach"]."</td>\n";
                        echo "<td>".number_format($r6["thnlalu"],0,",",",")."</td>\n";
                        echo "<td>".$r6["grw"]."</td>\n";
                        echo "<td>".number_format($r6["ytd_target"],0,",",",")."</td>\n";
                        echo "<td>".number_format($r6["ytd_sales"],0,",",",")."</td>\n";
                        echo "<td>".$r6["ytd_ach"]."</td>\n";
                        echo "<td>".number_format($r6["ytd_thnlalu"],0,",",",")."</td>\n";
                        echo "<td>".$r6["ytd_grw"]."</td>\n";
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
                        /*
                        echo ""
                        . "<td align=\"right\" colspan=\"2\"><b>Total ".$divprodid." ".$kategori."</b></td>"
                                . "<td class='divnone'></td><td class='divnone'></td>\n";
                         * 
                         */
                        echo "<td colspan='2' align='center'><b>Total $divprodid $kategori<b></td><td class='divnone'></td>";
                        
                        echo "<td><b>".number_format($r5["mtd_target"],0,",",",")."</b></td>\n";
                        echo "<td><b>".number_format($r5["mtd_sales"],0,",",",")."</b></td>\n\n";
                        echo "<td><b>".$r5["mtd_ach"]."</b></td>\n";
                        echo "<td><b>".number_format($r5["thnlalu"],0,",",",")."</b></td>\n";
                        echo "<td><b>".$r5["grw"]."</b></td>\n";
                        echo "<td><b>".number_format($r5["ytd_target"],0,",",",")."</b></td>\n";
                        echo "<td><b>".number_format($r5["ytd_sales"],0,",",",")."</b></td>\n";
                        echo "<td><b>".$r5["ytd_ach"]."</b></td>\n";
                        echo "<td><b>".number_format($r5["ytd_thnlalu"],0,",",",")."</b></td>\n";
                        echo "<td><b>".$r5["ytd_grw"]."</b></td>\n";
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
                    /*
                    echo "<td align=\"right\" colspan=\"2\"><b>Total ".$divprodid."</b></td>"
                            . "<td class='divnone'></td><td class='divnone'></td>\n";
                     * 
                     */
                    echo "<td colspan='2' align='center'><b>Total $divprodid<b></td><td class='divnone'></td>";
                    echo "<td><b>".number_format($r3["mtd_target"],0,",",",")."</b></td>\n";
                    echo "<td><b>".number_format($r3["mtd_sales"],0,",",",")."</b></td>\n";
                    echo "<td><b>".$r3["mtd_ach"]."</b></td>\n";
                    echo "<td><b>".number_format($r3["thnlalu"],0,",",",")."</b></td>\n";
                    echo "<td><b>".$r3["grw"]."</b></td>\n";
                    echo "<td><b>".number_format($r3["ytd_target"],0,",",",")."</b></td>\n";
                    echo "<td><b>".number_format($r3["ytd_sales"],0,",",",")."</b></td>";
                    echo "<td><b>".$r3["ytd_ach"]."</b></td>\n";
                    echo "<td><b>".number_format($r3["ytd_thnlalu"],0,",",",")."</b></td>\n";
                    echo "<td><b>".$r3["ytd_grw"]."</b></td>\n";
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
                  /*
                  echo "<td align=\"left\" colspan=\"2\"><b>Total ".$namaregion."</b></td>"
                          . "<td class='divnone'></td><td class='divnone'></td>\n";
                   * 
                   */
                  echo "<td colspan='2'><b>Total $namacabangytd<b></td><td class='divnone'></td>";
                  echo "<td><b>".number_format($r4["mtd_target"],0,",",",")."</b></td>\n";
                  echo "<td><b>".number_format($r4["mtd_sales"],0,",",",")."</b></td>\n";
                  echo "<td><b>".$r4["mtd_ach"]."</b></td>\n";
                  echo "<td><b>".number_format($r4["thnlalu"],0,",",",")."</b></td>\n";
                  echo "<td><b>".$r4["grw"]."</b></td>\n";
                  echo "<td><b>".number_format($r4["ytd_target"],0,",",",")."</b></td>\n";
                  echo "<td><b>".number_format($r4["ytd_sales"],0,",",",")."</b></td>\n";
                  echo "<td><b>".$r4["ytd_ach"]."</b></td>\n";
                  echo "<td><b>".number_format($r4["ytd_thnlalu"],0,",",",")."</b></td>\n";
                  echo "<td><b>".$r4["ytd_grw"]."</b></td>\n";
                  echo "</tr>\n";
              }
                
        ?>
    </tbody>
</table>
<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;