<?php
    session_start();
    //ini_set('memory_limit', '-1');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    include ("../../config/koneksimysqli_ms.php");
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DTTMLGTUSRC_01".$_SESSION['USERID']."_$now ";
    $tmp02 =" dbtemp.DTTMLGTUSRC_02".$_SESSION['USERID']."_$now ";
    $tmp03 =" dbtemp.DTTMLGTUSRC_03".$_SESSION['USERID']."_$now ";
    $tmp04 =" dbtemp.DTTMLGTUSRC_04".$_SESSION['USERID']."_$now ";
    
    
    $ptglpil=$_POST['uperiode1'];
    $pidcabpil=$_POST['uidcabang'];
    
    $_SESSION['TGTTMLPERTPILCB']=$ptglpil;
    $_SESSION['TGTTMLCABPILCB']=$pidcabpil;
    
    
    $tgl_pertama=$ptglpil;
    $ptglpilihupload = date("Y-m-d", strtotime($ptglpil));
    $pperiode_ = date("Ym", strtotime($ptglpil));
    
    $fcabang_pil="";
    if (!empty($pidcabpil)) $fcabang_pil=" AND icabangid='$pidcabpil' ";
    
    $query ="select icabangid, divprodid, iprodid, hna, qty, value from tgt.targetcab WHERE DATE_FORMAT(bulan,'%Y%m')='$pperiode_' $fcabang_pil";
    $query = "CREATE TEMPORARY TABLE $tmp01($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
    
    $query = "select icabangid from sls.icabang WHERE aktif='Y' AND icabangid NOT IN (select IFNULL(icabangid,'') FROM $tmp01)";
    $query = "CREATE TEMPORARY TABLE $tmp03($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
    
    
    $query = "select DISTINCT icabangid from $tmp01";
    $query = "CREATE TEMPORARY TABLE $tmp02($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
    
    
    if (empty($fcabang_pil)) {
        
        $query = "INSERT INTO $tmp02 (icabangid) select icabangid from $tmp03";
        mysqli_query($cnms, $query);
        $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
        
    }
    
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp03");
    
    
    $query = "select b.region, a.icabangid, b.nama nmcabang, CAST(0 as DECIMAL(20,2)) as rpcabang, "
            . " CAST(0 as DECIMAL(20,2)) as rparea, CAST(0 as DECIMAL(20,2)) as rpsisa "
            . " from $tmp02 a LEFT JOIN sls.icabang b on a.icabangid=b.icabangid";
    $query = "CREATE TEMPORARY TABLE $tmp03($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
                        
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp02");
    
    
    $query ="select icabangid, divprodid, iprodid, hna, sum(qty) as qty, sum(value) as value "
            . " from tgt.targetarea WHERE DATE_FORMAT(bulan,'%Y%m')='$pperiode_' $fcabang_pil "
            . " GROUP BY 1,2,3,4";
    $query = "CREATE TEMPORARY TABLE $tmp02($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
    
    
    $query = "UPDATE $tmp03 a JOIN (SELECT icabangid, SUM(value) tvalue FROM $tmp01 GROUP BY 1) b on "
            . " a.icabangid=b.icabangid SET a.rpcabang=b.tvalue";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
    
    
    $query = "UPDATE $tmp03 a JOIN (SELECT icabangid, SUM(value) tvalue FROM $tmp02 GROUP BY 1) b on "
            . " a.icabangid=b.icabangid SET a.rparea=b.tvalue";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
    
    $query = "UPDATE $tmp03 SET rpsisa=IFNULL(rpcabang,0)-IFNULL(rparea,0)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
    
    
    //DETAIL PRODUK
    $query = "select b.region, a.icabangid, b.nama nmcabang, a.divprodid, a.iprodid, c.nama nmproduk, a.hna, "
            . " qty as cab_qty, value as cab_value, "
            . " CAST(0 as DECIMAL(20,2)) as area_qty, CAST(0 as DECIMAL(20,2)) as area_value, "
            . " CAST(0 as DECIMAL(20,2)) as sisa_qty, CAST(0 as DECIMAL(20,2)) as sisa_value "
            . " from $tmp01 a LEFT JOIN sls.icabang b on a.icabangid=b.icabangid "
            . " LEFT JOIN sls.iproduk c on CONVERT(a.iprodid, DECIMAL(20))=CONVERT(c.iprodid, DECIMAL(20))";
    $query = "CREATE TEMPORARY TABLE $tmp04($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
    
    
    $query = "UPDATE $tmp04 a JOIN (SELECT icabangid, divprodid, iprodid, SUM(qty) tqty, SUM(value) tvalue FROM $tmp02 GROUP BY 1,2,3) b on "
            . " a.icabangid=b.icabangid AND a.divprodid=b.divprodid AND a.iprodid=b.iprodid SET a.area_qty=b.tqty, a.area_value=b.tvalue";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
    
    $query = "UPDATE $tmp04 SET sisa_qty=IFNULL(cab_qty,0)-IFNULL(area_qty,0), sisa_value=IFNULL(cab_value,0)-IFNULL(area_value,0)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
    
    //END DETAIL PRODUK
    
    
    
?>
<form method='POST' action='' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content' style="margin-left:-20px; margin-right:-20px;">
        
        <table id='datatabletpldttargetarea' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width="15px" nowrap align='center'>No</th>
                    <th nowrap align='center'>Nama Cabang</th>
                    <th nowrap align='center'>Total Cabang</th>
                    <th nowrap align='center'>Total Area</th>
                    <th nowrap align='center'>Selisih</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $gtotcab=0;
                $gtotarea=0;
                $no=1;
                $query = "SELECT distinct region from $tmp03 order by region";
                $tampil= mysqli_query($cnms, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $npregion=$row['region'];
                    $nnmregion="BARAT";
                    if ($npregion=="T") $nnmregion="TIMUR";
                    
                    echo "<tr>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap><b>$nnmregion</b></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "</tr>";
                    
                    $subtotcab=0;
                    $subtotarea=0;
                    $no=1;
                    
                    $query = "SELECT * from $tmp03 WHERE region='$npregion' order by region, nmcabang, icabangid";
                    $tampil1= mysqli_query($cnms, $query);
                    while ($row1= mysqli_fetch_array($tampil1)) {
                        $nicabangid=$row1['icabangid'];
                        $nnmcabang=$row1['nmcabang'];
                        $nrpcab=$row1['rpcabang'];
                        $nrparea=$row1['rparea'];
                        $nrpsisa=$row1['rpsisa'];
                        
                        if (empty($nrpcab)) $nrpcab=0;
                        if (empty($nrparea)) $nrparea=0;
                        if (empty($nrpsisa)) $nrpsisa=0;
                        
                        $subtotcab=(double)$subtotcab+(double)$nrpcab;
                        $subtotarea=(double)$subtotarea+(double)$nrparea;
                        
                        $nrpcab=number_format($nrpcab,0,",",",");
                        $nrparea=number_format($nrparea,0,",",",");
                        $nrpsisa=number_format($nrpsisa,0,",",",");
                        
                        echo "<tr>";
                        echo "<td nowrap>$no</td>";
                        echo "<td nowrap>$nnmcabang</td>";
                        echo "<td nowrap align='right'>$nrpcab</td>";
                        echo "<td nowrap align='right'>$nrparea</td>";
                        echo "<td nowrap align='right'><b>$nrpsisa</b></td>";
                        echo "</tr>";

                        $no++;

                    }
                    
                    $gtotcab=(double)$gtotcab+(double)$subtotcab;
                    $gtotarea=(double)$gtotarea+(double)$subtotarea;
                    
                    $subtotselisih=(double)$subtotcab-(double)$subtotarea;
                    
                    $subtotcab=number_format($subtotcab,0,",",",");
                    $subtotarea=number_format($subtotarea,0,",",",");
                    $subtotselisih=number_format($subtotselisih,0,",",",");
                    
                    echo "<tr>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap><b>TOTAL $nnmregion</b></td>";
                    echo "<td nowrap align='right'><b>$subtotcab</b></td>";
                    echo "<td nowrap align='right'><b>$subtotarea</b></td>";
                    echo "<td nowrap align='right'><b>$subtotselisih</b></td>";
                    echo "</tr>";

                }
                
                $gtotselisih=(double)$gtotcab-(double)$gtotarea;
                
                $gtotcab=number_format($gtotcab,0,",",",");
                $gtotarea=number_format($gtotarea,0,",",",");
                $gtotselisih=number_format($gtotselisih,0,",",",");

                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap><b>GRAND TOTAL</b></td>";
                echo "<td nowrap align='right'><b>$gtotcab</b></td>";
                echo "<td nowrap align='right'><b>$gtotarea</b></td>";
                echo "<td nowrap align='right'><b>$gtotselisih</b></td>";
                echo "</tr>";
                ?>
            </tbody>
        </table>
        
        <div class="clearfix"></div>
        <p/>&nbsp;
        <div class="title_left">
            <h4>
                <b>Quota</b>
            </h4>
        </div>
        <div class="clearfix"></div>
        
        <table id='datatabletpldttargetarea2' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width="15px" nowrap align='center'>No</th>
                    <th nowrap align='center'>Divisi</th>
                    <th nowrap align='center'>Quota Qty</th>
                    <th nowrap align='center'>Quota Value</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "SELECT divprodid, SUM(sisa_qty) sisa_qty, SUM(sisa_value) as sisa_value "
                        . " FROM $tmp04 WHERE (IFNULL(sisa_qty,0)<>0 OR IFNULL(sisa_value,0)<>0) GROUP BY 1";
                $tampil= mysqli_query($cnms, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $nnpdivisi=$row['divprodid'];
                    $nnpqtysisa=$row['sisa_qty'];
                    $nnpvalsisa=$row['sisa_value'];
                    
                    $nnpqtysisa=number_format($nnpqtysisa,0,",",",");
                    $nnpvalsisa=number_format($nnpvalsisa,0,",",",");
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$nnpdivisi</td>";
                    echo "<td nowrap align='right'>$nnpqtysisa</td>";
                    echo "<td nowrap align='right'>$nnpvalsisa</td>";
                    echo "</tr>";
                    
                    $no++;
                }
                ?>
            </tbody>
        </table>
        
        
    </div>
</form>

<script>
    $(document).ready(function() {
        var dataTable = $('#datatabletpldttargetarea').DataTable( {
            "bPaginate": false,
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "ordering": false,
            "order": [[ 0, "desc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": -1,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { className: "text-right", "targets": [2,3,4] },//right
                { className: "text-nowrap", "targets": [0,1,2,3,4] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 460,
            "scrollX": true
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
</script>

<style>
    .divnone {
        display: none;
    }
    #datatabletpldttargetarea th, #datatabletpldttargetarea2 th {
        font-size: 13px;
    }
    #datatabletpldttargetarea td, #datatabletpldttargetarea2 td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>

<?PHP
hapusdata:
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp04");
    mysqli_close($cnms);
?>
