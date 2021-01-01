<script>
$(document).ready(function() {

                var groupColumn = 1;
                var groupColumn2 = 2;
                var table = $('#datatable').DataTable({
                    fixedHeader: true,
                    "columnDefs": [
                        { "visible": false, "targets": groupColumn },
                        { "visible": false, "targets": groupColumn2 },
                        { className: "text-right", "targets": [4,5,6,7,8,9,10,11,12,13,14,15] },//right
                        { className: "text-nowrap", "targets": [0] }//nowrap
                    ],
                    "order": [[ groupColumn, 'asc' ]],
                    "order": [[ groupColumn2, 'asc' ]],
                    "ordering": false,
                    "lengthMenu": [[500, 1000, 1500, -1], [500, 1000, 1500, "All"]],
                    "displayLength": -1,
                    dom: 'Bfrtip',
                    buttons: [
                        'excel', 'print'
                    ],
                    bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
                    "bPaginate": false,
                    "drawCallback": function ( settings ) {
                        var api = this.api();
                        var rows = api.rows( {page:'current'} ).nodes();
                        var last=null;

                        api.column(groupColumn, {page:'current'} ).data().each( function ( group, i ) {
                            if ( last !== group ) {
                                $(rows).eq( i ).before(
                                    '<tr class="group"><td colspan="15">'+group+'</td></tr>'
                                );

                                last = group;
                            }
                        } );
                        api.column(groupColumn2, {page:'current'} ).data().each( function ( group, i ) {
                            if ( last !== group ) {
                                $(rows).eq( i ).before(
                                    '<tr class="group"><td colspan="15">'+group+'</td></tr>'
                                );

                                last = group;
                            }
                        } );
                    }
                    
                    /*,
                        "footerCallback": function ( row, data, start, end, display ) {
                            var api = this.api(), data;

                            // Remove the formatting to get integer data for summation
                            var intVal = function ( i ) {
                               return typeof i === 'string' ?
                                   i.replace(/[\$,]/g, '')*1 :
                                   typeof i === 'number' ?
                                       i : 0;
                            };

                            // Total over all pages
                            total = api
                               .column( 4 )
                               .data()
                               .reduce( function (a, b) {
                                   return intVal(a) + intVal(b);
                               }, 0 );

                            // Total over this page
                            pageTotal = api
                               .column( 4, { page: 'current'} )
                               .data()
                               .reduce( function (a, b) {
                                   return intVal(a) + intVal(b);
                               }, 0 );

                            // Update footer
                            $( api.column( 4 ).footer() ).html(
                               '$'+pageTotal +' ( $'+ total +' total)'
                            );
                        }
                    */
                } );

                $('#enable').on( 'click', function () {
                    table.fixedHeader.enable();
                } );

                $('#disable').on( 'click', function () {
                    table.fixedHeader.disable();
                } );
 
                // Order by the grouping
                $('#datatable tbody').on( 'click', 'tr.group', function () {
                    var currentOrder = table.order()[0];
                    if ( currentOrder[0] === groupColumn && currentOrder[1] === 'asc' ) {
                        table.order( [ groupColumn, 'desc' ] ).draw();
                    }
                    else {
                        table.order( [ groupColumn, 'asc' ] ).draw();
                    }
                } );

} );

</script>

<?php
    include "config/koneksimysqli.php";
    include "config/fungsi_combo.php";
    
    $rbtipe=$_POST['rb_rpttipe'];
    $karyawan=$_POST['e_idkaryawan'];

    if ($rbtipe=="P") {
        $karyawan=" and CONCAT(icabangid,areaid) in (select distinct CONCAT(icabangid,areaid) from sls.imr0 where karyawanid='$_POST[e_idkaryawan]')";
    }else $karyawan="";
    
    $lvlposisi="FF1";

    $now=date("mdYhis");
    $tmpsales =" dbtemp.DTSALESYTDSALES01_$_SESSION[IDCARD]$now ";
    $tmptarget =" dbtemp.DTSALESYTDTARGET01_$_SESSION[IDCARD]$now ";

    $tmp01 =" dbtemp.DTSALESYTD01_$_SESSION[IDCARD]$now ";
    $tmp02 =" dbtemp.DTSALESYTD02_$_SESSION[IDCARD]$now ";
    $tmp03 =" dbtemp.DTSALESYTD03_$_SESSION[IDCARD]$now ";

    $tgl01=$_POST['e_periode01'];
    $tgl02=$_POST['e_periode02'];
    $tgl03=$_POST['e_gperiode01'];
    $tgl04=$_POST['e_gperiode02'];

    $periode01= date("Y-m-d", strtotime($tgl01));
    $periode02= date("Y-m-d", strtotime($tgl02));

    $growth01= date("Y-m-d", strtotime($tgl03));
    $growth02= date("Y-m-d", strtotime($tgl04));


    $bln01= date("Ym", strtotime($tgl01));
    $bln02= date("Ym", strtotime($tgl02));

    $gwh01= date("Ym", strtotime($tgl03));
    $gwh02= date("Ym", strtotime($tgl04));

    $thnbln01= date("Y-m", strtotime($tgl01));
    $thnbln02= date("Y-m", strtotime($tgl02));

    $thngwh01= date("Y-m", strtotime($tgl03));
    $thngwh02= date("Y-m", strtotime($tgl04));

    
    $filterdiv=('');
    if (!empty($_POST['chkbox_divisi'])){
        $filterdiv=$_POST['chkbox_divisi'];
        $filterdiv=PilCekBox($filterdiv);
    }
    $filterdiv=" and divprodid in $filterdiv ";
    
    
    $filterprod=('');
    if (!empty($_POST['chkbox_produk'])){
        $filterprod=$_POST['chkbox_produk'];
        $filterprod=PilCekBox($filterprod);
    }
    $filterprod=" and iprodid in $filterprod ";
    
    $filtercab=('');
    if (!empty($_POST['chkbox_cabang'])){
        $filtercab=$_POST['chkbox_cabang'];
        $filtercab=PilCekBox($filtercab);
    }
    $filtercab=" and icabangid in $filtercab ";
    

/*    
    
    $sql="select icabangid, areaid, icustid, ecustid, iprodid, distid, initial, fakturid, tgljual, ecabangid, divprodid, qty, hna, initialecabang
        from sls.mr_sales2 where 
        ((tgljual between '$periode01' and '$periode02') or
        (tgljual between '$growth01' and '$growth02'))
        $filterprod $karyawan
    ";
    
    $sql="create table $tmpsales ($sql)";
    mysqli_query($cnmy, $sql);


    $sql="select iprodid, sum(qty) as qtytarget, sum(qty) as tvaluetarget, sum(qty) as qty, sum(qty*hna) as tvalue, sum(qty) as tvaluelalu, sum(qty) as grw, sum(qty) as ach
        from $tmpsales where DATE_FORMAT(tgljual, '%Y%m') = '$bln02' group by iprodid";
    $sql="create table $tmp01 ($sql)";
    mysqli_query($cnmy, $sql);
    
    $sql="select iprodid, sum(qty) as qty, sum(qty*hna) as tvalue from $tmpsales where DATE_FORMAT(tgljual, '%Y%m') = '$gwh02' group by iprodid";
    $sql="create table $tmp02 ($sql)";
    mysqli_query($cnmy, $sql);
    
    $sql="insert into $tmp01 (iprodid) select distinct iprodid from $tmp02 where
        iprodid not in (select iprodid from $tmp01 )";
    mysqli_query($cnmy, $sql);

    mysqli_query($cnmy, "update $tmp01 set qtytarget=null, tvaluetarget=null, tvaluelalu=null, grw=null, ach=null");
    
    mysqli_query($cnmy, "update $tmp01 set tvaluelalu=ifnull((select sum(tvalue) from $tmp02 where $tmp02.iprodid=$tmp01.iprodid),0)");


    $sql="insert into $tmp01 (iprodid) select distinct iprodid from $tmpsales where
        iprodid not in (select iprodid from $tmp01 )";
    mysqli_query($cnmy, $sql);

    mysqli_query($cnmy, "alter table $tmp01 add COLUMN (
            ytdqtytarget decimal(32,2), ytdtvaluetarget decimal(32,2),
            ytdqty decimal(32,2), ytdtvalue decimal(32,2),
            ytdtvaluelalu decimal(32,2), ytdgrw decimal(32,2), ytdach decimal(32,2)
            )");

    mysqli_query($cnmy, "update $tmp01 set ytdqty=ifnull((select sum(qty) from $tmpsales where $tmpsales.iprodid=$tmp01.iprodid and tgljual between '$periode01' and '$periode02'),0)");
    mysqli_query($cnmy, "update $tmp01 set ytdtvalue=ifnull((select sum(qty*hna) from $tmpsales where $tmpsales.iprodid=$tmp01.iprodid and tgljual between '$periode01' and '$periode02'),0)");

    mysqli_query($cnmy, "update $tmp01 set ytdtvaluelalu=ifnull((select sum(qty*hna) from $tmpsales where $tmpsales.iprodid=$tmp01.iprodid and tgljual between '$growth01' and '$growth02'),0)");
    
    $nmtbl_target=" dbmaster.v_target ";
    if ($lvlposisi=="FF1"){
        $nmtbl_target=" dbmaster.v_target_mr ";
    }
    
    $sql="select * from $nmtbl_target where  periode between '$thnbln01' and '$thnbln02'
        $filterprod";
    $sql="create table $tmptarget ($sql)";
    mysqli_query($cnmy, $sql);

    $sql="insert into $tmp01 (iprodid) select distinct iprodid from $tmptarget where
        iprodid not in (select iprodid from $tmp01 )";
    mysqli_query($cnmy, $sql);

    mysqli_query($cnmy, "update $tmp01 set qtytarget=ifnull((select sum(target) from $tmptarget where $tmptarget.iprodid=$tmp01.iprodid and periode = '$thnbln02'),0)");
    mysqli_query($cnmy, "update $tmp01 set tvaluetarget=ifnull((select sum(target*hna) from $tmptarget where $tmptarget.iprodid=$tmp01.iprodid and periode = '$thnbln02'),0)");
    mysqli_query($cnmy, "update $tmp01 set ytdqtytarget=ifnull((select sum(target) from $tmptarget where $tmptarget.iprodid=$tmp01.iprodid and periode between '$thnbln01' and '$thnbln02'),0)");
    mysqli_query($cnmy, "update $tmp01 set ytdtvaluetarget=ifnull((select sum(target*hna) from $tmptarget where $tmptarget.iprodid=$tmp01.iprodid and periode between '$thnbln01' and '$thnbln02'),0)");

    //$sql="update $tmp01 set ytdqtytarget=ifnull((select sum(target) from $tmptarget where $tmptarget.iprodid=$tmp01.iprodid and periode between '$thngwh01' and '$thngwh02'),0)";
    //echo $sql;exit;

    mysqli_query($cnmy, "update $tmp01 set
            ach=case when ifnull(tvaluetarget,0)=0 THEN 0 ELSE
            ifnull(tvalue,0)/ifnull(tvaluetarget,0)*100 END,
            grw=case when ifnull(tvaluelalu,0)=0 then 0 ELSE
            (ifnull(tvalue,0)-ifnull(tvaluelalu,0))/ifnull(tvaluelalu,0)*100 END");

    
    mysqli_query($cnmy, "update $tmp01 set
            ytdach=case when ifnull(ytdtvaluetarget,0)=0 THEN 0 ELSE
            ifnull(ytdtvalue,0)/ifnull(ytdtvaluetarget,0)*100 END,
            ytdgrw=case when ifnull(ytdtvaluelalu,0)=0 then 0 ELSE
            (ifnull(ytdtvalue,0)-ifnull(ytdtvaluelalu,0))/ifnull(ytdtvaluelalu,0)*100 END");

    

    //$tmp01="dtsalesytd03_000000185407102018090326";

    $sql="select A.*, B.nama as nama_produk, B.divprodid, C.groupp from $tmp01 as A
        inner JOIN ms.iproduk as B on A.iprodid=B.iProdId inner JOIN ms.gpeth as C on B.DivProdId=C.divprodid and A.iprodid=C.iprodid";
    $sql="create table $tmp03 ($sql)";
    mysqli_query($cnmy, $sql);


    */
    
    
    $printdate= date("d/m/Y");
    echo "<table width='90%' align='center' border='0' class='table table-striped table-bordered'>";
        echo "<tr>";
            echo "<td valign='top'>";
                echo "<table border='0' width='100%'>";
                echo "<tr><td><small style='color:blue;'>$_SESSION[NAMAPT]</small></td></tr>";
                if ($rbtipe=="P") { echo "<tr><td>Employee : <u>$_POST[e_karyawan]</u></td></tr>"; }
                echo "</table>";
            echo "</td>";
            echo "<td valign='top'>";
                echo "<table align='center' border='0' width='100%'>";
                echo "<tr><td align='center'><h2>Report Sales Year To Date (YTD) By Product</h2></td></tr>";
                echo "<tr><td align='center'><b>Periode $_POST[e_periode01] s/d. $_POST[e_periode02]<br/>
                    Growth $_POST[e_gperiode01] s/d. $_POST[e_gperiode02]</b></td></tr>";
                echo "<tr><td align='right'><small><i>View Date : $printdate</i></small></td></tr>";
                echo "</table>";
            echo "</td>";
        echo "</tr>";
    echo "</table><hr>&nbsp;</hr>";

    
    echo "<table id='datatable' class='table table-striped table-bordered'>";
    echo "<thead>";
    
    echo "<tr>
        <th rowspan=3 width='10px'>No</th>
        <th rowspan=3><center>Divisi</center>
        <th rowspan=3><center>Group</center>
        <th rowspan=3><center>Product</center></th>
        <th colspan=6><center>This Month</center></th>

        <th colspan=6><center>Year To Date</center></th>
        </tr>";

    echo "<tr>
        
        <th colspan=2><center>Plan</center></th>
        <th colspan=2><center>Actual</center></th>
        <th rowspan=2>Growth</th><th rowspan=2>Ach</th>

        <th colspan=2><center>Plan</center></th>
        <th colspan=2><center>Actual</center></th>
        <th rowspan=2>Growth</th><th rowspan=2>Ach</th>
        </tr>";

    echo "<tr>
        
        <th>Qty</th><th>Value</th>
        <th>Qty</th><th>Value</th>
        

        <th>Qty</th><th>Value</th>
        <th>Qty</th><th>Value</th>
        
        </tr>";
    echo "</thead>";
    echo "<tbody>";
    $lcgroup1="";
    $lcgroup2="";
    $no=1;
    $tmp03="dbtemp.dtsalesytd03_000000185407102018090453";
    $tampil = mysqli_query($cnmy, "select * from $tmp03 order by divprodid, groupp, nama_produk, iprodid");
    while ($r=mysqli_fetch_array($tampil)){
        $qtytarget=number_format($r['qtytarget'],0,",",".");
        $tvaluetarget=number_format($r['tvaluetarget'],0,",",".");
        $qty=number_format($r['qty'],0,",",".");
        $tvalue=number_format($r['tvalue'],0,",",".");

        $ytdqtytarget=number_format($r['ytdqtytarget'],0,",",".");
        $ytdtvaluetarget=number_format($r['ytdtvaluetarget'],0,",",".");
        $ytdqty=number_format($r['ytdqty'],0,",",".");
        $ytdtvalue=number_format($r['ytdtvalue'],0,",",".");


        echo "<tr scope='row'><td>$no</td>";
        echo "<td><b>$r[divprodid]</b></td>";
        echo "<td><i>$r[groupp]</i></td>";
        echo "<td>$r[nama_produk]</td>";
        echo "<td>$qtytarget</td>";
        echo "<td>$tvaluetarget</td>";
        echo "<td>$qty</td>";
        echo "<td>$tvalue</td>";
        echo "<td>$r[grw]</td>";
        echo "<td>$r[ach]</td>";

        echo "<td>$ytdqtytarget</td>";
        echo "<td>$ytdtvaluetarget</td>";
        echo "<td>$ytdqty</td>";
        echo "<td>$ytdtvalue</td>";
        echo "<td>$r[ytdgrw]</td>";
        echo "<td>$r[ytdach]</td>";

        echo "</tr>";
        
        $no++;
    }
    echo "<tfoot>";
    echo "<tr>";
    echo "<th colspan='15' style='text-align:right'>Total:</th>";
    echo "<th></th>";
    echo "</tr>";
    echo "</tfoot>";
    echo "</tbody>";
    echo "</table>";
    
    
    mysqli_query($cnmy, "drop table $tmpsales");
    mysqli_query($cnmy, "drop table $tmptarget");
    mysqli_query($cnmy, "drop table $tmp01");//
    mysqli_query($cnmy, "drop table $tmp02");
    //mysqli_query($cnmy, "drop table $tmp03");//
?>
