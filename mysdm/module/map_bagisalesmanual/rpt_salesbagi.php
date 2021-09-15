<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
session_start();

$puser="";
if (isset($_SESSION['USERID'])) $puser=$_SESSION['USERID'];

if (empty($puser)) {
    echo "ANDA HARUS LOGIN ULANG....!!!";
    exit;
}

$userid=$_SESSION['USERID'];
$fkaryawanid=$_SESSION['IDCARD'];
$pidjbt=$_SESSION['JABATANID']; 
$pidgrp=$_SESSION['GROUP'];

$ppilihrpt=$_GET['ket'];
if ($ppilihrpt=="excel") {
    // Fungsi header dengan mengirimkan raw data excel
    header("Content-type: application/vnd-ms-excel");
    // Mendefinisikan nama file ekspor "hasil-export.xls"
    header("Content-Disposition: attachment; filename=Data Bagi Sales.xls");
}
include("config/koneksimysqli_ms.php");

$pviewdate=date("d/m/Y H:i:s");

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

$pdistid=$_POST['cb_dist'];
$pecabid=$_POST['cb_ecabang'];
$pbulan=$_POST['e_bulan'];
$pfakturid=$_POST['e_namafilter'];

$pbulan = date("Y-m", strtotime($pbulan));

$milliseconds = round(microtime(true) * 1000);
$now=date("mdYhis");
$tmp01 ="dbtemp.tmpslsbagirpt01_".$puser."_$now$milliseconds";
$tmp02 ="dbtemp.tmpslsbagirpt02_".$puser."_$now$milliseconds";
$tmp03 ="dbtemp.tmpslsbagirpt02_".$puser."_$now$milliseconds";
$tmp03 ="dbtemp.tmpslsbagirpt03_".$puser."_$now$milliseconds";


$query = "SELECT distid, nama, sls_data, initial FROM MKT.distrib0 WHERE distid='$pdistid'";
$tampil=mysqli_query($cnms, $query);
$row=mysqli_fetch_array($tampil);
$pnamadist=$row['nama'];
$pnmtblsales=$row['sls_data'];
    
    
$query = "select a.nomsales, a.icabangid, b.areaid, a.icustid, a.distid, a.ecustid, a.ecabangid, a.tgl, a.fakturid, 
        b.iprodid, LPAD(a.user1,'10','0') as user1, b.qty 
        from MKT.msales0 as a join MKT.msales1 as b on a.noMSales=b.noMsales
        WHERE a.distid='$pdistid' AND a.eCabangId='$pecabid' AND left(a.tgl,7)='$pbulan' ";
if ($pidjbt=="38" AND $pidgrp<>"24") {
    $query .=" AND ( a.icabangid IN (SELECT IFNULL(icabangid,'') FROM hrd.rsm_auth WHERE karyawanid='$fkaryawanid') OR a.user1='$puser' ) ";
}
if (!empty($pfakturid)) $query .=" AND a.fakturid='$pfakturid' ";

$query = "create TEMPORARY table $tmp01 ($query)"; 
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "select a.*, b.nama as nama_cabang, c.nama as nama_area, d.nama as nama_icust, e.nama as nama_ecust, f.nama as nama_produk, g.nama as nama_dist, h.nama as nama_userinput   
    from $tmp01 as a 
    LEFT JOIN MKT.icabang as b on a.icabangid=b.iCabangId 
    LEFT JOIN MKT.iarea as c on a.icabangid=c.iCabangId AND a.areaid=c.areaId 
    LEFT JOIN MKT.icust as d on a.icabangid=d.iCabangId AND a.areaid=d.areaId AND a.icustid=d.iCustId 
    JOIN MKT.ecust as e on a.distid=e.DistId AND a.ecabangid=e.cabangid AND a.ecustid=e.eCustId 
    JOIN MKT.iproduk as f on a.iProdId=f.iProdId JOIN MKT.distrib0 as g on a.distid=g.distid LEFT JOIN hrd.karyawan as h on a.user1=h.karyawanid";
$query = "create TEMPORARY table $tmp02 ($query)"; 
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
$query = "SELECT '$pdistid' as distid, a.cabangid, a.brgid, a.custid, a.tgljual, a.harga, a.fakturid, "
        . " e.iprodid, e.nama as nmprod, SUM(a.qbeli) qbeli "
        . " FROM MKT.$pnmtblsales as a "
        . " JOIN MKT.eproduk as e ON a.brgid=e.eprodid  "
        . " JOIN $tmp01 as f on a.fakturid=f.fakturid AND a.cabangid=f.ecabangid AND '$pdistid'=f.distid AND a.custid=f.ecustid AND e.iprodid=f.iprodid"
        . " WHERE a.cabangid='$pecabid' "
        . " AND LEFT(tgljual,7)='$pbulan' AND e.distid='$pdistid' "
        . " GROUP BY 1,2,3,4,5,6,7,8,9 ORDER BY nmprod";
//echo "$query";
$query = "create TEMPORARY table $tmp03 ($query)"; 
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    

$query = "Alter table $tmp02 ADD COLUMN harga DECIMAL(20,2), ADD COLUMN qtyfaktur DECIMAL(20,2)";
mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp02 as a JOIN $tmp03 as f on a.fakturid=f.fakturid AND a.ecabangid=f.cabangid AND a.distid=f.distid "
        . " AND a.ecustid=f.custid AND a.iprodid=f.iprodid SET "
        . " a.harga=f.harga, a.qtyfaktur=f.qbeli";
mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
//echo "$pdistid, $pecabid, $pbulan, $pfakturid";

?>

<HTML>
<HEAD>
    <title>Data Bagi Sales</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Mei 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <link href="css/laporanbaru.css" rel="stylesheet">
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        
        <!-- Bootstrap -->
        <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    
        <!-- Datatables -->
        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">
    
        <!-- Datatables -->
        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

        <!-- Datatables -->
        <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <!-- jQuery -->
        <script src="vendors/jquery/dist/jquery.min.js"></script>
        
        
    <?PHP } ?>
    
</HEAD>

<BODY>
    
<?PHP if ($ppilihrpt!="excel") { ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?PHP } ?>
    
<div id='n_content'>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <?PHP if ($ppilihrpt=="excel") {
                    echo "<tr><td colspan=5 width='150px'><b>Data Bagi Sales</b></td></tr>";
                    echo "<tr><td colspan=5 width='150px'>view date : $pviewdate</td></tr>";
                }else{
                    echo "<tr><td width='150px'><b><h3>Data Bagi Sales</h3></b></td></tr>";
                    echo "<tr><td width='150px'><i>view date : $pviewdate</i></td></tr>";
                }
                ?>
            </table>
        </div>
        <div id="isikanan">

        </div>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
    
    
    <table id="datatable2" class="table table-striped table-bordered" width="100%" border="1px solid black">
            <thead>
                <tr>
                    <th width='10px'>No</th>
                    <th width='50px'>Customer</th>
                    <th width='50px'>Customer SDM</th>
                    <th width='50px'>Cabang</th>
                    <th width='50px'>Faktur</th>
                    <th width='50px'>Tanggal</th>
                    <th width='50px'>Produk</th>
                    <th width='50px'>ID Input</th>
                    <th width='20px'>Qty</th>
                    <th width='20px'>User Input</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select * from $tmp02 Order By nama_ecust, nama_icust, nama_cabang, nama_area, fakturid, nama_produk, nomsales";
                $tampil=mysqli_query($cnms, $query);
                while ($row=mysqli_fetch_array($tampil)) {
                    $pnomsales=$row['nomsales'];
                    $pnmcuste=$row['nama_ecust'];
                    $pnmcusti=$row['nama_icust'];
                    $pnmcab=$row['nama_cabang'];
                    $pnmarea=$row['nama_area'];
                    $pfakturid=$row['fakturid'];
                    $ptgljual=$row['tgl'];
                    $pnmproduk=$row['nama_produk'];
                    $pqty=$row['qty'];
                    
                    $pidecust=$row['ecustid'];
                    $pidicust=$row['icustid'];
                    $pidcab=$row['icabangid'];
                    $pidarea=$row['areaid'];
                    $pidprod=$row['iprodid'];
                    
                    $piduser=$row['user1'];
                    $pnmuser=$row['nama_userinput'];
                    
                    if (!empty($pidicust)) $pidicust=(INT)$pidicust;
                    if (!empty($pidcab)) $pidcab=(INT)$pidcab;
                    if (!empty($pidarea)) $pidarea=(INT)$pidarea;
                    if (!empty($pidprod)) $pidprod=(INT)$pidprod;
                    if (!empty($piduser)) $piduser=(INT)$piduser;
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pnmcuste ($pidecust)</td>";
                    echo "<td nowrap>$pnmcusti ($pidicust)</td>";
                    echo "<td nowrap>$pnmcab ($pidcab) - $pnmarea ($pidarea)</td>";
                    echo "<td nowrap>$pfakturid</td>";
                    echo "<td nowrap>$ptgljual</td>";
                    echo "<td nowrap>$pnmproduk ($pidprod)</td>";
                    echo "<td nowrap>$pnomsales</td>";
                    echo "<td nowrap align='right'>$pqty</td>";
                    echo "<td nowrap>$pnmuser ($piduser)</td>";
                    echo "</tr>";
                    
                    $no++;
                }
                ?>
            </tbody>
        
    </table>
    
    
    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
</div>
    
    
    
    <?PHP if ($ppilihrpt!="excel") { ?>
        <!-- Bootstrap -->
        <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    
        <!-- Datatables -->
        <script src="vendors/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
        <script src="vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
        <script src="vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
        <script src="vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
        <script src="vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
        <script src="vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
        <script src="vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
        <script src="vendors/jszip/dist/jszip.min.js"></script>
        <script src="vendors/pdfmake/build/pdfmake.min.js"></script>
        <script src="vendors/pdfmake/build/vfs_fonts.js"></script>

        
        
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

            #n_content {
                color:#000;
                font-family: "Arial";
                margin: 20px;
                /*overflow-x:auto;*/
            }
        </style>

        <style>
            .divnone {
                display: none;
            }
            #datatable2, #datatable3 {
                color:#000;
                font-family: "Arial";
            }
            #datatable2 th, #datatable3 th {
                font-size: 13px;
            }
            #datatable2 td, #datatable3 td { 
                font-size: 12px;
            }
        </style>
        
    <?PHP }else{ ?>
        <style>
            .tjudul {
                font-family: Georgia, serif;
                font-size: 15px;
                margin-left:10px;
                margin-right:10px;
            }
            .tjudul td {
                padding: 4px;
            }
            #datatable2, #datatable3 {
                font-family: Georgia, serif;
                margin-left:10px;
                margin-right:10px;
            }
            #datatable2 th, #datatable2 td, #datatable3 th, #datatable3 td {
                padding: 4px;
            }
            #datatable2 thead, #datatable3 thead{
                background-color:#cccccc; 
                font-size: 13px;
            }
            #datatable2 tbody, #datatable3 tbody{
                font-size: 12px;
            }
        </style>
    <?PHP } ?>
        
        
</BODY>

    <script>
        // SCROLL
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
        // END SCROLL
    
    
        $(document).ready(function() {
            var table = $('#datatable2, #datatable3').DataTable({
                fixedHeader: true,
                "ordering": false,
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": -1,
                "order": [[ 0, "asc" ]],
                "columnDefs": [
                    { "visible": false },
                    { className: "text-right", "targets": [8] },//right
                    { className: "text-nowrap", "targets": [0, 1, 2, 3,4,,5,6,7,8] }//nowrap

                ],
                bFilter: true, bInfo: true, "bLengthChange": true, "bLengthChange": true,
                "bPaginate": true
            } );

        } );
    
    </script>

</HTML>


<?PHP
hapusdata:
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp03");
    mysqli_close($cnms);
?>