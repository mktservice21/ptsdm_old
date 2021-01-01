<?php
    session_start();
    //ini_set('memory_limit', '-1');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    include ("../../config/koneksimysqli_ms.php");
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DTTMLGTUSR_01".$_SESSION['USERID']."_$now ";
    $tmp02 =" dbtemp.DTTMLGTUSR_02".$_SESSION['USERID']."_$now ";
    $tmp03 =" dbtemp.DTTMLGTUSR_03".$_SESSION['USERID']."_$now ";
    $tmp04 =" dbtemp.DTTMLGTUSR_04".$_SESSION['USERID']."_$now ";
    $tmp05 =" dbtemp.DTTMLGTUSR_05".$_SESSION['USERID']."_$now ";
    
    
    $ptglpil=$_POST['uperiode1'];
    $pidcabpil=$_POST['uidcabang'];
    $prpttype=$_POST['urpttype'];
    $pareapil=$_POST['ucbarea'];
    
    $pfilter_area="";
    if (!empty($pareapil)) $pfilter_area=" AND areaid='$pareapil' ";
                
    $_SESSION['TGTTMLPERTPIL']=$ptglpil;
    $_SESSION['TGTTMLCABPIL']=$pidcabpil;
    $_SESSION['TGTTMLBYPIL']=$prpttype;
    $_SESSION['TGTTMLAREPILCB']=$pareapil;
    
    
    $tgl_pertama=$ptglpil;
    $ptglpilihupload = date("Y-m-d", strtotime($ptglpil));
    $pperiode_ = date("Ym", strtotime($ptglpil));
    
    $query ="select icabangid, areaid, nama from sls.iarea WHERE aktif='Y' AND icabangid='$pidcabpil' $pfilter_area";
    $query = "CREATE TEMPORARY TABLE $tmp01($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
    
    
    $query ="select * from tgt.targetarea WHERE DATE_FORMAT(bulan,'%Y%m')='$pperiode_' AND icabangid='$pidcabpil' $pfilter_area";
    $query = "CREATE TEMPORARY TABLE $tmp02($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
    
    //tanpa filter area
    $query ="select * from tgt.targetarea WHERE DATE_FORMAT(bulan,'%Y%m')='$pperiode_' AND icabangid='$pidcabpil'";
    $query = "CREATE TEMPORARY TABLE $tmp05($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
    
    
    
    
    $query ="select DISTINCT a.divprodid, a.iprodid, b.nama nmproduk, a.hna from $tmp02 a LEFT JOIN sls.iproduk b on CONVERT(a.iprodid, DECIMAL(20))=CONVERT(b.iprodid, DECIMAL(20))";
    $query = "CREATE TEMPORARY TABLE $tmp03($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
    
    $npilrptby=" `value` ";
    if ($prpttype=="tqty") $npilrptby=" `qty` ";
    
    $query = "SELECT * FROM $tmp01 ORDER BY nama, areaid";
    $tampil= mysqli_query($cnms, $query);
    while ($row= mysqli_fetch_array($tampil)) {
        $npidarea=$row['areaid'];
        $npnmarea=$row['nama'];
        
        $mysql_fields_h[]=$npidarea;
        $mysql_fields_h_nm[]=$npnmarea;
        
        $query ="ALTER TABLE $tmp03 ADD COLUMN `$npnmarea` DECIMAL(20,2)";
        mysqli_query($cnms, $query);
        $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
        
        $query ="UPDATE $tmp03 SET `$npnmarea`=(SELECT SUM($npilrptby) FROM $tmp02 WHERE areaid='$npidarea' AND $tmp02.iprodid=$tmp03.iprodid)";
        mysqli_query($cnms, $query);
        $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
        
    }

    $query ="ALTER TABLE $tmp03 ADD COLUMN TOTAL DECIMAL(20,2)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
        
    $query ="UPDATE $tmp03 SET $tmp03.TOTAL=(SELECT SUM(`value`) FROM $tmp02 WHERE $tmp02.iprodid=$tmp03.iprodid)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
        
    $query = "SELECT * FROM $tmp03 LIMIT 1";
    $mysql_query = mysqli_query($cnms, $query);
    while($mysql_query_fields = mysqli_fetch_field($mysql_query)){
        $nmfield=$mysql_query_fields->name;
        if (strtoupper($nmfield)!="DIVPRODID" and strtoupper($nmfield)!="IPRODID") {
            $mysql_fields[] = $nmfield;
        }
    }
    
    
    //target cabang
    
    $query ="select divprodid, sum(qty) cab_qty, sum(value) as cab_value, "
            . " CAST(0 as DECIMAL(20,2)) as area_qty, CAST(0 as DECIMAL(20,2)) as area_value, "
            . " CAST(0 as DECIMAL(20,2)) as sisa_qty, CAST(0 as DECIMAL(20,2)) as sisa_value "
            . " from tgt.targetcab WHERE DATE_FORMAT(bulan,'%Y%m')='$pperiode_' AND icabangid='$pidcabpil' GROUP BY 1";
    $query = "CREATE TEMPORARY TABLE $tmp04($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
    
    $query = "UPDATE $tmp04 a JOIN (SELECT divprodid, SUM(qty) tqty, SUM(value) tvalue FROM $tmp05 GROUP BY 1) b on "
            . " a.divprodid=b.divprodid SET a.area_qty=b.tqty, a.area_value=b.tvalue";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
    
    $query = "UPDATE $tmp04 SET sisa_qty=IFNULL(cab_qty,0)-IFNULL(area_qty,0), sisa_value=IFNULL(cab_value,0)-IFNULL(area_value,0)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
    
    //end target cabang
    
    
?>

<style>
    #myBtn {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 30px;
        z-index: 99;
        font-size: 18px;
        border: none;
        outline: none;
        background-color: red;
        color: white;
        cursor: pointer;
        padding: 15px;
        border-radius: 4px;
        opacity: 0.5;
    }

    #myBtn:hover {
        background-color: #555;
    }
</style>

<script>
    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() {scrollFunction()};
    function scrollFunction() {
      if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        document.getElementById("myBtn").style.display = "block";
      } else {
        document.getElementById("myBtn").style.display = "none";
      }
    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
      document.body.scrollTop = 0;
      document.documentElement.scrollTop = 0;
    }
</script>

<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

<style>

table {
    text-align: left;
    position: relative;
    border-collapse: collapse;
    background-color:#FFFFFF;
}

th {
    background: white;
    position: sticky;
    top: 0;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
}

.th2 {
    background: white;
    position: sticky;
    top: 23;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    border-top: 1px solid #000;
}
</style>

<form method='POST' action='' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content' style="margin-left:-20px; margin-right:-20px;">
        
        <table id='datatabletpldttargetarea' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th nowrap align='center'>NO</th>
                    <th nowrap align='center'>PRODUK</th>
                    <th nowrap align='center'>HNA</th>
                    <?PHP
                    foreach($mysql_fields_h_nm as $nmfields){
                        echo "<th nowrap align='center'>$nmfields</th>";
                    }
                    ?>
                    <th nowrap align='center'>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "SELECT distinct divprodid FROM $tmp03 ORDER BY divprodid";
                $tampil= mysqli_query($cnms, $query);
                $ketemu= mysqli_num_rows($tampil);
                if ($ketemu>0) {
                    while ($dv= mysqli_fetch_array($tampil)) {
                        
                        $npdivpord=$dv['divprodid'];

                        echo "<tr>";
                        echo "<td nowrap></td>";
                        foreach($mysql_fields as $fields){
                            $nmfield=$fields;
                            $p_nmdiv="";
                            if (strtoupper($nmfield)=="NMPRODUK") {
                                $p_nmdiv=$npdivpord;
                            }
                            echo "<td nowrap><b>$p_nmdiv</b></td>";
                            
                        }
                        
                        echo "</tr>";
                    
                        $query = "SELECT * FROM $tmp03 WHERE divprodid='$npdivpord' ORDER BY divprodid, nmproduk, iprodid";
                        $mysql_query = mysqli_query($cnms, $query);
                        
                        while($mysql_rows = mysqli_fetch_array($mysql_query)){

                            echo "<tr>";
                                echo "<td nowrap>$no</td>";
                                foreach($mysql_fields as $fields){
                                    $nmfield=$fields;
                                    
                                    $pnmrecord=$mysql_rows[$fields];
                                    $palignposisi="";
                                    if (strtoupper($nmfield)!="NMPRODUK") {

                                        if (empty($pnmrecord)) $pnmrecord=0;

                                        $pnmrecord=number_format($pnmrecord,0,",",",");

                                        $palignposisi=" align='right' ";
                                    }

                                    echo "<td nowrap $palignposisi>$pnmrecord</td>";
                                    
                                }
                                
                            echo "</tr>";
                            $no++;
                        }
                        
                        
                        $p_nmdiv="TOTAL ".$npdivpord;
                        echo "<td nowrap></td>";
                        echo "<td nowrap><b>$p_nmdiv</b></td>";
                        echo "<td nowrap><b></b></td>";

                        foreach($mysql_fields_h as $fields){
                            $idfield=$fields;
                            $query = "select SUM(value) as TVALUE FROM $tmp02 WHERE divprodid='$npdivpord' AND areaid='$idfield'";
                            $tamp_h= mysqli_query($cnms, $query);
                            $nhr= mysqli_fetch_array($tamp_h);
                            $pval_h=$nhr['TVALUE'];
                            if (empty($pval_h)) $pval_h=0;
                            $pval_h=number_format($pval_h,0,",",",");

                            echo "<td nowrap align='right'><b>$pval_h</b></td>";
                        }
                        
                        $query = "select SUM(value) as SVALUE FROM $tmp02 WHERE divprodid='$npdivpord'";
                        $tamp_s= mysqli_query($cnms, $query);
                        $nhs= mysqli_fetch_array($tamp_s);
                        $pval_s=$nhs['SVALUE'];
                        if (empty($pval_s)) $pval_s=0;
                        $pval_s=number_format($pval_s,0,",",",");
                        
                        echo "<td nowrap align='right'><b>$pval_s</b></td>";
                        
                        echo "</tr>";
                        
                    }
                    
                    $p_nmdiv="GRAND TOTAL ";
                    echo "<td nowrap></td>";
                    echo "<td nowrap><b>$p_nmdiv</b></td>";
                    echo "<td nowrap><b></b></td>";

                    foreach($mysql_fields_h as $fields){
                        $idfield=$fields;
                        $query = "select SUM(value) as GVALUE FROM $tmp02 WHERE areaid='$idfield'";
                        $tamp_h= mysqli_query($cnms, $query);
                        $nhr= mysqli_fetch_array($tamp_h);
                        $pval_h=$nhr['GVALUE'];
                        if (empty($pval_h)) $pval_h=0;
                        $pval_h=number_format($pval_h,0,",",",");

                        echo "<td nowrap align='right'><b>$pval_h</b></td>";
                    }
                    
                    $query = "select SUM(value) as GVALUE FROM $tmp02";
                    $tamp_g= mysqli_query($cnms, $query);
                    $nhg= mysqli_fetch_array($tamp_g);
                    $pval_g=$nhg['GVALUE'];
                    if (empty($pval_g)) $pval_g=0;
                    $pval_g=number_format($pval_g,0,",",",");
                    
                    echo "<td nowrap align='right'><b>$pval_g</b></td>";
                    
                    echo "</tr>";
                    
                }
                ?>
            </tbody>
        </table>
        
        <div class="clearfix"></div>
        <p/>&nbsp;
        <div class="title_left">
            <h4>
                <b>QUOTA PER CABANG</b>
            </h4>
            *) <i><u><b>klik divisi untuk melihat detail produk</b></u></i>
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
                    
                    $plihatdetail="<a href='eksekusi3.php?module=tgttargetarea&idmenu=287&act=detail&bln=$pperiode_&icab=$pidcabpil&rptby=$prpttype&idiv=$nnpdivisi' class='btn btn-success btn-xs' target='_blank'>$nnpdivisi</a>";
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$plihatdetail</td>";
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
        //$("#datatabletpldttargetarea").scrollTop( 50 );
        var dataTable = $('#datatabletpldttargetareax').DataTable( {
            fixedHeader: true,
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
                { className: "text-right", "targets": [6] },//right
                { className: "text-nowrap", "targets": [0, 1, 2] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            }
            /*,
            "scrollY": 460,
            "scrollX": true*/
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
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp05");
    mysqli_close($cnms);
?>
