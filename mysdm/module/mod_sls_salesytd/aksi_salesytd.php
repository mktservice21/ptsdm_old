<script>
$(document).ready(function() {

                var groupColumn = 1;
                var groupColumn2 = 2;
                var table = $('#datatable').DataTable({
                    fixedHeader: true,
                    "ordering": false,
                    "lengthMenu": [[500, 1000, 1500, -1], [500, 1000, 1500, "All"]],
                    "displayLength": -1,
                    dom: 'Bfrtip',
                    buttons: [
                        'excel'//, 'print'
                    ],
                    bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
                    "bPaginate": false,
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

<style>
    .divnone {
        display: none;
    }
    
</style>

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
        <th rowspan=3><center>Produk</center>
        
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
    //$tmp03="dbtemp.dtsalesytd03_000000185407102018090453";
    
    $group1 = mysqli_query($cnmy, "select distinct divprodid from $tmp03 order by divprodid");
    while ($g1=mysqli_fetch_array($group1)){
        $namagroup1=$g1['divprodid'];
        
        echo "<tr scope='row'><td></td>";
        echo "<td colaspan=13><b>$g1[divprodid]</b></td>";
        echo "<td class='divnone'></td><td class='divnone'></td><td class='divnone'></td>";
        echo "<td class='divnone'></td><td class='divnone'></td><td class='divnone'></td>";
        echo "<td class='divnone'></td><td class='divnone'></td><td class='divnone'></td>";
        echo "<td class='divnone'></td><td class='divnone'></td><td class='divnone'></td>";
        echo "</tr>";
        
        $group2 = mysqli_query($cnmy, "select distinct divprodid, groupp from $tmp03 where divprodid='$g1[divprodid]'  order by divprodid, groupp");
        while ($g2=mysqli_fetch_array($group2)){
            $namagroup2=$g2['groupp'];
            
            echo "<tr scope='row'><td></td>";
            echo "<td colaspan=13 align='center'><i>$g2[groupp]</i></td>";
            echo "<td class='divnone'></td><td class='divnone'></td><td class='divnone'></td>";
            echo "<td class='divnone'></td><td class='divnone'></td><td class='divnone'></td>";
            echo "<td class='divnone'></td><td class='divnone'></td><td class='divnone'></td>";
            echo "<td class='divnone'></td><td class='divnone'></td><td class='divnone'></td>";
            echo "</tr>";
            
            $no=1;
            $tampil = mysqli_query($cnmy, "select * from $tmp03 where divprodid='$g1[divprodid]' and groupp='$g2[groupp]' order by divprodid, groupp, nama_produk, iprodid");
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
                echo "<td>$r[nama_produk]</td>";
                echo "<td align='right'>$qtytarget</td>";
                echo "<td align='right'>$tvaluetarget</td>";
                echo "<td align='right'>$qty</td>";
                echo "<td align='right'>$tvalue</td>";
                echo "<td align='right'>$r[grw]</td>";
                echo "<td align='right'>$r[ach]</td>";

                echo "<td align='right'>$ytdqtytarget</td>";
                echo "<td align='right'>$ytdtvaluetarget</td>";
                echo "<td align='right'>$ytdqty</td>";
                echo "<td align='right'>$ytdtvalue</td>";
                echo "<td align='right'>$r[ytdgrw]</td>";
                echo "<td align='right'>$r[ytdach]</td>";

                echo "</tr>";

                $no++;
            }
            
            //Total Group 2
            echo "<tr scope='row'><td></td>";
            echo "<td colaspan=13 align='right'><i>Total $namagroup2</i></td>";
            echo "<td></td><td></td><td></td>";
            echo "<td></td><td></td><td></td>";
            echo "<td></td><td></td><td></td>";
            echo "<td></td><td></td><td></td>";
            echo "</tr>";
            //End Total Group 2
            
        }
        
        //Total Group 2
        echo "<tr scope='row'><td></td>";
        echo "<td colaspan=13 align='right'><b><i>Total $namagroup1</i></b></td>";
        echo "<td></td><td></td><td></td>";
        echo "<td></td><td></td><td></td>";
        echo "<td></td><td></td><td></td>";
        echo "<td></td><td></td><td></td>";
        echo "</tr>";
        //End Total Group 2
    }
    echo "</tbody>";
    echo "</table>";
    
    
    mysqli_query($cnmy, "drop table $tmpsales");
    mysqli_query($cnmy, "drop table $tmptarget");
    mysqli_query($cnmy, "drop table $tmp01");//
    mysqli_query($cnmy, "drop table $tmp02");
    mysqli_query($cnmy, "drop table $tmp03");//
?>
