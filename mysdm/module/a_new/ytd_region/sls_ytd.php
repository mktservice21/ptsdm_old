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
    
    require_once 'module/a_new/meekrodb.2.3.class.php';
    
    $now=date("mdYhis");
    $tmp00 =" dbtemp.TMPYTDDAR00_".$karyawanid."_$now ";
    
    $query = "select * from sls.ytd where bulan='$bulan' and region='$region' AND iddaerah<>'026'";
    $results1 = DB::query("CREATE TEMPORARY TABLE $tmp00 ($query)");
    $query = "UPDATE $tmp00 SET divprodid='ZOTHER' WHERE kategori='OTHER'";
    $results1 = DB::query($query);
    $namatabel=$tmp00;
    
    //echo "$karyawanid - $bulan, $region - $namaregion"; exit;
    
}else{
    $bulan=$_GET["bulan"];
    $date=date_create($bulan);
    $region=$_GET["region"];
    if ($region=="B"){ $namaregion="Barat";} else if ($region=="T"){$namaregion="Timur";}
    $karyawanid=$_GET["id"];
    $namatabel="tmp.ytd_region".$karyawanid;
    $link =$_GET["link"]."/report/ytd_region.aspx";
    $mylink="../ptsdm/mysdm/";

    require_once 'meekrodb.2.3.class.php';
    //echo $bulan."<br>";
    //echo $region."<br>";
    //echo $cbgytd."<br>";
    //$namacabangytd = DB::queryFirstField("SELECT nama FROM ms.cbgytd WHERE idcabang=%s", $cbgytd);


    $now=date("mdYhis");
    $tmp00 =" dbtemp.TMPYTDREG00_".$karyawanid."_$now ";
	
	$results1 = DB::query("CREATE TEMPORARY TABLE $tmp00 (SELECT * FROM %l)",$namatabel);

	$query = "UPDATE $tmp00 SET divprodid='ZOTHER' WHERE kategori='OTHER'";
	$results1 = DB::query($query);

	$namatabel=$tmp00;
}


$pprodukmaklo="";
//$resultsdel = DB::query("DELETE FROM $namatabel WHERE IFNULL(mtd_qty_sales,0)=0 AND IFNULL(mtd_value_sales,0)=0 AND IFNULL(mtd_qty_thnlalu,0)=0 AND IFNULL(mtd_value_thnlalu,0)=0");
$resultssel = DB::query("SELECT DISTINCT divprodid FROM %l WHERE divprodid IN  ('MAKLO', 'MAKLON') ORDER BY 1",$namatabel);
foreach ($resultssel as $sl) {
    if (!empty($sl['divprodid'])) $pprodukmaklo=$sl['divprodid'];
}


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
            { className: "text-right", "targets": [1,2,3,4,5,6,7,8,9,10,11,12,13] }//,//right
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
                echo "<tr><td>Nama Region: $namaregion</td></tr>";
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
            $results1 = DB::query("SELECT DISTINCT divprodid FROM %l WHERE divprodid NOT IN  ('ZOTHER', 'ZOTHE', 'ZOTHERS', 'ZOTH') ORDER BY 1",$namatabel);
            foreach ($results1 as $r1) {
                $divprodid=$r1['divprodid'];
                
				$pnmdividprodid=$divprodid;
				if ($divprodid=="ZOTHER") $pnmdividprodid="OTHER";
				
                //echo "<tr><td colspan=\"2\"><b>" . $r1['divprodid'] . "</b></td><td colspan=\"12\"></td>\n</tr>\n";

                echo "<tr scope='row'>";
                echo "<td colspan='2'><b>" . $pnmdividprodid . "</b></td>";
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
                echo "<td class='divnone'></td>";
                echo "<td class='divnone'></td>";
                echo "</tr>";
                
                $result2 = DB::query("SELECT DISTINCT kategori FROM %l0 WHERE divprodid=%s1 AND divprodid NOT IN  ('ZOTHER', 'ZOTHE', 'ZOTHERS', 'ZOTH')",$namatabel,$divprodid);
                foreach ($result2 as $r2){
                    $kategori=$r2['kategori'];
					
                    if ($pnmdividprodid!="OTHER" AND $pnmdividprodid!="OTHERS") {

                        echo "<tr><td align='center' colspan='2'><b>".$kategori."</b></td>"
                            . "<td class='divnone'></td><td class='divnone'></td>\n";
                        echo "<td align=\"center\" colspan=\"12\"></td>"
                            . "<td class='divnone'></td><td class='divnone'></td><td class='divnone'></td><td class='divnone'></td>"
                            . "<td class='divnone'></td><td class='divnone'></td><td class='divnone'></td><td class='divnone'></td>"
                            . "<td class='divnone'></td><td class='divnone'></td></tr>\n";
                    }
					
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
                  WHERE s.divprodid=%s1 AND s.kategori=%s2 AND s.divprodid NOT IN  ('ZOTHER', 'ZOTHE', 'ZOTHERS', 'ZOTH') 
                  GROUP BY ip.`nama`,s.hna_sales",$namatabel,$divprodid,$kategori);
                    
                    foreach ($result6 as $r6){
                        $hna_sales=number_format($r6['hna_sales'],0,",",",");
                        echo "<tr>";
                        echo "<td>".$r6["namaproduk"]."</td>\n";
                        echo "<td align='right'>".$hna_sales."</td>\n";
                        echo "<td align='right'>".number_format($r6["mtd_target"],0,",",",")."</td>\n";
                        echo "<td align='right'>".number_format($r6["mtd_sales"],0,",",",")."</td>\n";
                        echo "<td align='right'>".$r6["mtd_ach"]."</td>\n";
                        echo "<td align='right'>".number_format($r6["thnlalu"],0,",",",")."</td>\n";
                        echo "<td align='right'>".$r6["grw"]."</td>\n";
                        echo "<td align='right'>".number_format($r6["ytd_target"],0,",",",")."</td>\n";
                        echo "<td align='right'>".number_format($r6["ytd_sales"],0,",",",")."</td>\n";
                        echo "<td align='right'>".$r6["ytd_ach"]."</td>\n";
                        echo "<td align='right'>".number_format($r6["ytd_thnlalu"],0,",",",")."</td>\n";
                        echo "<td align='right'>".$r6["ytd_grw"]."</td>\n";
                        echo "<td align='right'>".number_format($r6["thn_tgt"],0,",",",")."</td>\n";
                        echo "<td align='right'>".$r6["ach_year"]."</td>\n";
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
                    FROM %l0 WHERE divprodid=%s1 and kategori=%s2 AND divprodid NOT IN  ('ZOTHER', 'ZOTHE', 'ZOTHERS', 'ZOTH')",$namatabel,$divprodid,$kategori);


                    foreach ($result5 as $r5){
						
						
                        if ($pnmdividprodid!="OTHER" AND $pnmdividprodid!="OTHERS") {

                            echo "<tr>";
                            /*
                            echo ""
                            . "<td align=\"right\" colspan=\"2\"><b>Total ".$divprodid." ".$kategori."</b></td>"
                                    . "<td class='divnone'></td><td class='divnone'></td>\n";
                             * 
                             */
						 
						
							
                            echo "<td colspan='2' align='center'><b>Total $pnmdividprodid $kategori<b></td><td class='divnone'></td>";

                            echo "<td align='right'><b>".number_format($r5["mtd_target"],0,",",",")."</b></td>\n";
                            echo "<td align='right'><b>".number_format($r5["mtd_sales"],0,",",",")."</b></td>\n\n";
                            echo "<td align='right'><b>".$r5["mtd_ach"]."</b></td>\n";
                            echo "<td align='right'><b>".number_format($r5["thnlalu"],0,",",",")."</b></td>\n";
                            echo "<td align='right'><b>".$r5["grw"]."</b></td>\n";
                            echo "<td align='right'><b>".number_format($r5["ytd_target"],0,",",",")."</b></td>\n";
                            echo "<td align='right'><b>".number_format($r5["ytd_sales"],0,",",",")."</b></td>\n";
                            echo "<td align='right'><b>".$r5["ytd_ach"]."</b></td>\n";
                            echo "<td align='right'><b>".number_format($r5["ytd_thnlalu"],0,",",",")."</b></td>\n";
                            echo "<td align='right'><b>".$r5["ytd_grw"]."</b></td>\n";
                            echo "<td align='right'><b>".number_format($r5["thn_tgt"],0,",",",")."</b></td>\n";
                            echo "<td align='right'><b>".$r5["ach_year"]."</b></td>\n";
                            echo "</tr>\n";

                        }
						
						
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
                FROM %l0 WHERE divprodid=%s1 AND divprodid NOT IN  ('ZOTHER', 'ZOTHE', 'ZOTHERS', 'ZOTH')",$namatabel,$divprodid);
                
                foreach ($result3 as $r3){
                    echo "<tr>";
                    /*
                    echo "<td align=\"right\" colspan=\"2\"><b>Total ".$divprodid."</b></td>"
                            . "<td class='divnone'></td><td class='divnone'></td>\n";
                     * 
                     */
                    echo "<td colspan='2' align='center'><b>Total $pnmdividprodid<b></td><td class='divnone'></td>";
                    echo "<td align='right'><b>".number_format($r3["mtd_target"],0,",",",")."</b></td>\n";
                    echo "<td align='right'><b>".number_format($r3["mtd_sales"],0,",",",")."</b></td>\n";
                    echo "<td align='right'><b>".$r3["mtd_ach"]."</b></td>\n";
                    echo "<td align='right'><b>".number_format($r3["thnlalu"],0,",",",")."</b></td>\n";
                    echo "<td align='right'><b>".$r3["grw"]."</b></td>\n";
                    echo "<td align='right'><b>".number_format($r3["ytd_target"],0,",",",")."</b></td>\n";
                    echo "<td align='right'><b>".number_format($r3["ytd_sales"],0,",",",")."</b></td>";
                    echo "<td align='right'><b>".$r3["ytd_ach"]."</b></td>\n";
                    echo "<td align='right'><b>".number_format($r3["ytd_thnlalu"],0,",",",")."</b></td>\n";
                    echo "<td align='right'><b>".$r3["ytd_grw"]."</b></td>\n";
                    echo "<td align='right'><b>".number_format($r3["thn_tgt"],0,",",",")."</b></td>\n";
                    echo "<td align='right'><b>".$r3["ach_year"]."</b></td>\n";
                    echo "</tr>\n";
                }
                
            }
            
            
            /*
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
              FROM %l0 WHERE divprodid NOT IN  ('ZOTHER', 'ZOTHE', 'ZOTHERS', 'ZOTH')",$namatabel);

              foreach ($result4 as $r4){
                  echo "<tr>";
                  /*
                  echo "<td align=\"left\" colspan=\"2\"><b>Total ".$namaregion."</b></td>"
                          . "<td class='divnone'></td><td class='divnone'></td>\n";
                   * 
                   */
            /*
                  echo "<td colspan='2'><b>Total $namaregion<b></td><td class='divnone'></td>";
                  echo "<td align='right'><b>".number_format($r4["mtd_target"],0,",",",")."</b></td>\n";
                  echo "<td align='right'><b>".number_format($r4["mtd_sales"],0,",",",")."</b></td>\n";
                  echo "<td align='right'><b>".$r4["mtd_ach"]."</b></td>\n";
                  echo "<td align='right'><b>".number_format($r4["thnlalu"],0,",",",")."</b></td>\n";
                  echo "<td align='right'><b>".$r4["grw"]."</b></td>\n";
                  echo "<td align='right'><b>".number_format($r4["ytd_target"],0,",",",")."</b></td>\n";
                  echo "<td align='right'><b>".number_format($r4["ytd_sales"],0,",",",")."</b></td>\n";
                  echo "<td align='right'><b>".$r4["ytd_ach"]."</b></td>\n";
                  echo "<td align='right'><b>".number_format($r4["ytd_thnlalu"],0,",",",")."</b></td>\n";
                  echo "<td align='right'><b>".$r4["ytd_grw"]."</b></td>\n";
                  echo "<td align='right'><b>".number_format($r4["thn_tgt"],0,",",",")."</b></td>\n";
                  echo "<td align='right'><b>".$r4["ach_year"]."</b></td>\n";
                  echo "</tr>\n";
              }
			  
            */
			  
			  
			  
			  
			  //OTHER
			  
                echo "<tr scope='row'>";
                echo "<td colspan='2'><b>OTHER</b></td>";
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
                echo "<td class='divnone'></td>";
                echo "<td class='divnone'></td>";
                echo "</tr>";
				
				
				
				
                    $result9 = DB::query("SELECT
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
                  WHERE s.divprodid IN ('ZOTHER', 'ZOTHE', 'ZOTHERS', 'ZOTH') 
                  GROUP BY ip.`nama`,s.hna_sales",$namatabel);
                    
                    foreach ($result9 as $r9){
                        $hna_sales=number_format($r9['hna_sales'],0,",",",");
                        echo "<tr>";
                        echo "<td>".$r9["namaproduk"]."</td>\n";
                        echo "<td align='right'>".$hna_sales."</td>\n";
                        echo "<td align='right'>".number_format($r9["mtd_target"],0,",",",")."</td>\n";
                        echo "<td align='right'>".number_format($r9["mtd_sales"],0,",",",")."</td>\n";
                        echo "<td align='right'>".$r9["mtd_ach"]."</td>\n";
                        echo "<td align='right'>".number_format($r9["thnlalu"],0,",",",")."</td>\n";
                        echo "<td align='right'>".$r9["grw"]."</td>\n";
                        echo "<td align='right'>".number_format($r9["ytd_target"],0,",",",")."</td>\n";
                        echo "<td align='right'>".number_format($r9["ytd_sales"],0,",",",")."</td>\n";
                        echo "<td align='right'>".$r9["ytd_ach"]."</td>\n";
                        echo "<td align='right'>".number_format($r9["ytd_thnlalu"],0,",",",")."</td>\n";
                        echo "<td align='right'>".$r9["ytd_grw"]."</td>\n";
                        echo "<td align='right'>".number_format($r9["thn_tgt"],0,",",",")."</td>\n";
                        echo "<td align='right'>".$r9["ach_year"]."</td>\n";
                        echo "</tr>\n";
                    }
				
				
                $result10 = DB::query("SELECT
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
                FROM %l0 WHERE divprodid IN  ('ZOTHER', 'ZOTHE', 'ZOTHERS', 'ZOTH')",$namatabel);
                
                foreach ($result10 as $r10){
                    echo "<tr>";
                    echo "<td colspan='2' align='center'><b>Total OTHER<b></td><td class='divnone'></td>";
                    echo "<td align='right'><b>".number_format($r10["mtd_target"],0,",",",")."</b></td>\n";
                    echo "<td align='right'><b>".number_format($r10["mtd_sales"],0,",",",")."</b></td>\n";
                    echo "<td align='right'><b>".$r10["mtd_ach"]."</b></td>\n";
                    echo "<td align='right'><b>".number_format($r10["thnlalu"],0,",",",")."</b></td>\n";
                    echo "<td align='right'><b>".$r10["grw"]."</b></td>\n";
                    echo "<td align='right'><b>".number_format($r10["ytd_target"],0,",",",")."</b></td>\n";
                    echo "<td align='right'><b>".number_format($r10["ytd_sales"],0,",",",")."</b></td>";
                    echo "<td align='right'><b>".$r10["ytd_ach"]."</b></td>\n";
                    echo "<td align='right'><b>".number_format($r10["ytd_thnlalu"],0,",",",")."</b></td>\n";
                    echo "<td align='right'><b>".$r10["ytd_grw"]."</b></td>\n";
                    echo "<td align='right'><b>".number_format($r10["thn_tgt"],0,",",",")."</b></td>\n";
                    echo "<td align='right'><b>".$r10["ach_year"]."</b></td>\n";
                    echo "</tr>\n";
                }
				
				
			  
                echo "<tr scope='row'>";
                echo "<td colspan='2'><b>&nbsp;</b></td>";
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
                echo "<td class='divnone'></td>";
                echo "<td class='divnone'></td>";
                echo "</tr>";
                
			  
                
                
              $result81 = DB::query("SELECT
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
              FROM %l0 WHERE divprodid NOT IN  ('ZOTHER', 'ZOTHE', 'ZOTHERS', 'ZOTH', 'MAKLO', 'MAKLON')",$namatabel);
			  
              foreach ($result81 as $r81){
                  echo "<tr>";
                  echo "<td colspan='2'><b>Total $namaregion<b></td><td class='divnone'></td>";
                  echo "<td align='right'><b>".number_format($r81["mtd_target"],0,",",",")."</b></td>\n";
                  echo "<td align='right'><b>".number_format($r81["mtd_sales"],0,",",",")."</b></td>\n";
                  echo "<td align='right'><b>".$r81["mtd_ach"]."</b></td>\n";
                  echo "<td align='right'><b>".number_format($r81["thnlalu"],0,",",",")."</b></td>\n";
                  echo "<td align='right'><b>".$r81["grw"]."</b></td>\n";
                  echo "<td align='right'><b>".number_format($r81["ytd_target"],0,",",",")."</b></td>\n";
                  echo "<td align='right'><b>".number_format($r81["ytd_sales"],0,",",",")."</b></td>\n";
                  echo "<td align='right'><b>".$r81["ytd_ach"]."</b></td>\n";
                  echo "<td align='right'><b>".number_format($r81["ytd_thnlalu"],0,",",",")."</b></td>\n";
                  echo "<td align='right'><b>".$r81["ytd_grw"]."</b></td>\n";
                  echo "<td align='right'><b>".number_format($r81["thn_tgt"],0,",",",")."</b></td>\n";
                  echo "<td align='right'><b>".$r81["ach_year"]."</b></td>\n";
                  echo "</tr>\n";
              }
              
              
                
                
                
              $result8 = DB::query("SELECT
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
              FROM %l0 WHERE divprodid NOT IN  ('MAKLO', 'MAKLON')",$namatabel);
			  
              foreach ($result8 as $r8){
                  echo "<tr>";
                  echo "<td colspan='2'><b>Total $namaregion + OTHER<b></td><td class='divnone'></td>";
                  echo "<td align='right'><b>".number_format($r8["mtd_target"],0,",",",")."</b></td>\n";
                  echo "<td align='right'><b>".number_format($r8["mtd_sales"],0,",",",")."</b></td>\n";
                  echo "<td align='right'><b>".$r8["mtd_ach"]."</b></td>\n";
                  echo "<td align='right'><b>".number_format($r8["thnlalu"],0,",",",")."</b></td>\n";
                  echo "<td align='right'><b>".$r8["grw"]."</b></td>\n";
                  echo "<td align='right'><b>".number_format($r8["ytd_target"],0,",",",")."</b></td>\n";
                  echo "<td align='right'><b>".number_format($r8["ytd_sales"],0,",",",")."</b></td>\n";
                  echo "<td align='right'><b>".$r8["ytd_ach"]."</b></td>\n";
                  echo "<td align='right'><b>".number_format($r8["ytd_thnlalu"],0,",",",")."</b></td>\n";
                  echo "<td align='right'><b>".$r8["ytd_grw"]."</b></td>\n";
                  echo "<td align='right'><b>".number_format($r8["thn_tgt"],0,",",",")."</b></td>\n";
                  echo "<td align='right'><b>".$r8["ach_year"]."</b></td>\n";
                  echo "</tr>\n";
              }
			  
              
              if (!empty($pprodukmaklo)) {
                  
                    $result82 = DB::query("SELECT
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
                    FROM %l0 WHERE divprodid NOT IN  ('ZOTHER', 'ZOTHE', 'ZOTHERS', 'ZOTH')",$namatabel);

                    foreach ($result82 as $r82){
                        echo "<tr>";
                        echo "<td colspan='2'><b>Total $namaregion + MAKLON<b></td><td class='divnone'></td>";
                        echo "<td align='right'><b>".number_format($r82["mtd_target"],0,",",",")."</b></td>\n";
                        echo "<td align='right'><b>".number_format($r82["mtd_sales"],0,",",",")."</b></td>\n";
                        echo "<td align='right'><b>".$r82["mtd_ach"]."</b></td>\n";
                        echo "<td align='right'><b>".number_format($r82["thnlalu"],0,",",",")."</b></td>\n";
                        echo "<td align='right'><b>".$r82["grw"]."</b></td>\n";
                        echo "<td align='right'><b>".number_format($r82["ytd_target"],0,",",",")."</b></td>\n";
                        echo "<td align='right'><b>".number_format($r82["ytd_sales"],0,",",",")."</b></td>\n";
                        echo "<td align='right'><b>".$r82["ytd_ach"]."</b></td>\n";
                        echo "<td align='right'><b>".number_format($r82["ytd_thnlalu"],0,",",",")."</b></td>\n";
                        echo "<td align='right'><b>".$r82["ytd_grw"]."</b></td>\n";
                        echo "<td align='right'><b>".number_format($r82["thn_tgt"],0,",",",")."</b></td>\n";
                        echo "<td align='right'><b>".$r82["ach_year"]."</b></td>\n";
                        echo "</tr>\n";
                    }
              
              
              
              
              
                    $result83 = DB::query("SELECT
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

                    foreach ($result83 as $r83){
                        echo "<tr>";
                        echo "<td colspan='2'><b>Total All<b></td><td class='divnone'></td>";
                        echo "<td align='right'><b>".number_format($r83["mtd_target"],0,",",",")."</b></td>\n";
                        echo "<td align='right'><b>".number_format($r83["mtd_sales"],0,",",",")."</b></td>\n";
                        echo "<td align='right'><b>".$r83["mtd_ach"]."</b></td>\n";
                        echo "<td align='right'><b>".number_format($r83["thnlalu"],0,",",",")."</b></td>\n";
                        echo "<td align='right'><b>".$r83["grw"]."</b></td>\n";
                        echo "<td align='right'><b>".number_format($r83["ytd_target"],0,",",",")."</b></td>\n";
                        echo "<td align='right'><b>".number_format($r83["ytd_sales"],0,",",",")."</b></td>\n";
                        echo "<td align='right'><b>".$r83["ytd_ach"]."</b></td>\n";
                        echo "<td align='right'><b>".number_format($r83["ytd_thnlalu"],0,",",",")."</b></td>\n";
                        echo "<td align='right'><b>".$r83["ytd_grw"]."</b></td>\n";
                        echo "<td align='right'><b>".number_format($r83["thn_tgt"],0,",",",")."</b></td>\n";
                        echo "<td align='right'><b>".$r83["ach_year"]."</b></td>\n";
                        echo "</tr>\n";
                    }
              
              }
                
        ?>
    </tbody>
</table>
<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
<?PHP
$results1 = DB::query("DROP TEMPORARY TABLE $tmp00");
?>
