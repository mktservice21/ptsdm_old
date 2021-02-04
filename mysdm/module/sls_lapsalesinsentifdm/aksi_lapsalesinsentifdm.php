<?php
    //ini_set('display_errors', '0');
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
session_start();
$puser="";
if (isset($_SESSION['USERID'])) $puser=$_SESSION['USERID'];

if (empty($puser)) {
    echo "ANDA HARUS LOGIN ULANG....!!!";
    exit;
}

$pmyidcard=$_SESSION['IDCARD'];
$pmyjabatanid=$_SESSION['JABATANID'];
$pmynamlengkap=$_SESSION['NAMALENGKAP'];

$ppilihrpt=$_GET['ket'];
if ($ppilihrpt=="excel") {
    // Fungsi header dengan mengirimkan raw data excel
    header("Content-type: application/vnd-ms-excel");
    // Mendefinisikan nama file ekspor "hasil-export.xls"
    header("Content-Disposition: attachment; filename=SALES INSENTIF DM.xls");
}

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

$pviewdate=date("d/m/Y H:i:s");

$tgl01=$_POST['bulan'];
$pkaryawanid=$_POST['cb_karyawan'];
$pnamakaryawan="";

$pbln1 = date("Y-m-01", strtotime($tgl01));
$pbln2 = date("Y-m-t", strtotime($tgl01));
$pbulan = date("F Y", strtotime($tgl01));


$milliseconds = round(microtime(true) * 1000);
$now=date("mdYhis");
$tmp01 ="dbtemp.tmpdminstf01_".$puser."_$now$milliseconds";
$tmp02 ="dbtemp.tmpdminstf02_".$puser."_$now$milliseconds";

include("config/koneksimysqli_ms.php");

$query = "select nama as nama from ms.karyawan WHERE karyawanid='$pkaryawanid'";
$tampilk= mysqli_query($cnms, $query);
$rowk= mysqli_fetch_array($tampilk);
$pnamakaryawan=$rowk['nama'];

$filtercab="";
$query = "select distinct icabangid from sls.idm0 WHERE karyawanid='$pkaryawanid'";
$tampil= mysqli_query($cnms, $query);
while ($row= mysqli_fetch_array($tampil)) {
    $picabangid=$row['icabangid'];
    
    $filtercab .="'".$picabangid."',";
}
if (!empty($filtercab)) {
    $filtercab="(".substr($filtercab, 0, -1).")";
}else{
    $filtercab="('')";
}

//echo $filtercab;
$query = "select icabangid, divprodid, iprodid, SUM(qty_target) as qty_target, SUM(value_target) as value_target, "
        . " SUM(qty_sales) as qty_sales, SUM(value_sales) as value_sales from sls.sales "
        . " WHERE bulan between '$pbln1' AND '$pbln2' AND icabangid IN $filtercab ";
$query .= " AND iprodid NOT in (select distinct IFNULL(iprodid,'') FROM sls.othproduk)";
$query .= " and iprodid not in (select iprodid from ms.gpeth_sales WHERE groupp='NEW')";
$query .= " GROUP BY 1,2,3";
//echo $query;
$query = "CREATE TEMPORARY TABLE $tmp01 ($query)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

$query = "SELECT a.icabangid, c.nama as nama_cabang, a.divprodid, a.iprodid, b.nama as nama_produk, a.qty_target, a.value_target, "
        . " a.qty_sales, a.value_sales  "
        . " from $tmp01 as a JOIN sls.iproduk as b on a.iprodid=b.iprodid "
        . " JOIN sls.icabang as c on a.icabangid=c.icabangid";
$query = "CREATE TEMPORARY TABLE $tmp02 ($query)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

$query = "ALTER TABLE $tmp02 ADD COLUMN groupp VARCHAR(10)";
mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

$query = "UPDATE $tmp02 as a JOIN ms.gpeth_sales as b on a.iprodid=b.iprodid AND a.divprodid=b.divprodid SET a.groupp=b.groupp";
mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

$query = "UPDATE $tmp02 SET groupp='GROUP2' WHERE IFNULL(groupp,'')=''";
mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

?>


<HTML>
<HEAD>
    <title>Sales Insentif DM</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Mei 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <!--<link href="css/laporanbaru.css" rel="stylesheet">-->
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        
        <!-- Bootstrap -->
        <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    
        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">


        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">


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
                <?PHP
                
                if ($ppilihrpt=="excel") {
                    echo "<tr><td colspan=5 width='150px'><b>Pencapaian Insentif DM</b></td></tr>";
                    echo "<tr><td colspan=5 width='150px'><b>Bulan : $pbulan</b></td></tr>";
                    echo "<tr><td colspan=5 width='150px'><b>DM : $pnamakaryawan</b></td></tr>";
                    
                    echo "<tr><td colspan=5 width='150px'>view date : $pviewdate</td></tr>";
                }else{
                    echo "<tr><td width='150px'><b><h3>Pencapaian Insentif DM</h3></b></td></tr>";
                    echo "<tr><td width='150px'><b>Bulan : $pbulan</b></td></tr>";
                    echo "<tr><td width='150px'><b>DM : $pnamakaryawan</b></td></tr>";
                    
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

    <hr/>
    <div class="ijudul"><h2>Tanpa New dan Other Produk</h2></div>
    <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
        <thead>
            <tr>
                <th>Nama Cabang</th>
                <th>Group</th>
                <th>Sales</th>
                <th>Target</th>
                <th>Ach</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $query = "select icabangid, nama_cabang, sum(value_sales) as value_sales, sum(value_target) as value_target FROM "
                    . " $tmp02 GROUP BY 1,2";
            $query .=" ORDER BY 2,1";
            $tampil= mysqli_query($cnms, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $nicabangid=$row['icabangid'];
                $nicabangnm=$row['nama_cabang'];
                $nmgrpprod="ALLGROUP";
                
                $pvalsls=$row['value_sales'];
                $pvaltgt=$row['value_target'];
                
                $pach=0;
                if ((DOUBLE)$pvaltgt<>0) $pach=(DOUBLE)$pvalsls/(DOUBLE)$pvaltgt*100;
                
                $pvalsls=number_format($pvalsls,0,",",",");
                $pvaltgt=number_format($pvaltgt,0,",",",");
                $pach=ROUND($pach,2);
                
                echo "<tr>";
                echo "<td nowrap>$nicabangnm</td>";
                echo "<td nowrap>$nmgrpprod</td>";
                echo "<td nowrap align='right'>$pvalsls</td>";
                echo "<td nowrap align='right'>$pvaltgt</td>";
                echo "<td nowrap align='right'>$pach</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
    
    <br/>
    <table id='mydatatable2' class='table table-striped table-bordered' width="100%" border="1px solid black">
        <thead>
            <tr>
                <th>Divisi</th>
                <th>Sales</th>
                <th>Target</th>
                <th>Ach</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $query = "select divprodid, sum(value_sales) as value_sales, sum(value_target) as value_target FROM "
                    . " $tmp02 GROUP BY 1";
            $query .=" ORDER BY 1";
            $tampil= mysqli_query($cnms, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $niddivisi=$row['divprodid'];

                $pvalsls=$row['value_sales'];
                $pvaltgt=$row['value_target'];
                
                $pach=0;
                if ((DOUBLE)$pvaltgt<>0) $pach=(DOUBLE)$pvalsls/(DOUBLE)$pvaltgt*100;
                
                $pvalsls=number_format($pvalsls,0,",",",");
                $pvaltgt=number_format($pvaltgt,0,",",",");
                $pach=ROUND($pach,2);
                
                echo "<tr>";
                echo "<td nowrap>$niddivisi</td>";
                echo "<td nowrap align='right'>$pvalsls</td>";
                echo "<td nowrap align='right'>$pvaltgt</td>";
                echo "<td nowrap align='right'>$pach</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
    
    <br/>
    <table id='mydatatable3' class='table table-striped table-bordered' width="100%" border="1px solid black">
        <thead>
            <tr>
                <th>Divisi Produk</th>
                <th>Produk</th>
                <th>Qty Sales</th>
                <th>Value Sales</th>
                <th>Qty Target</th>
                <th>Value Target</th>
                <th>Ach</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $query = "select * FROM "
                    . " $tmp02 ";
            $query .=" ORDER BY divprodid, nama_produk";
            $tampil= mysqli_query($cnms, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $niddivisi=$row['divprodid'];
                $nnmproduk=$row['nama_produk'];

                $pqtysls=$row['qty_sales'];
                $pvalsls=$row['value_sales'];
                $pqtytgt=$row['qty_target'];
                $pvaltgt=$row['value_target'];
                
                $pach=0;
                if ((DOUBLE)$pqtytgt<>0) {
                    $pach=(DOUBLE)$pqtysls/(DOUBLE)$pqtytgt*100;
                }
                $pqtysls=number_format($pqtysls,0,",",",");
                $pvalsls=number_format($pvalsls,0,",",",");
                $pqtytgt=number_format($pqtytgt,0,",",",");
                $pvaltgt=number_format($pvaltgt,0,",",",");
                $pach=ROUND($pach,2);
                
                echo "<tr>";
                echo "<td nowrap>$niddivisi</td>";
                echo "<td nowrap>$nnmproduk</td>";
                echo "<td nowrap align='right'>$pqtysls</td>";
                echo "<td nowrap align='right'>$pvalsls</td>";
                echo "<td nowrap align='right'>$pqtytgt</td>";
                echo "<td nowrap align='right'>$pvaltgt</td>";
                echo "<td nowrap align='right'>$pach</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <br/><br/><br/><br/>
    
    
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

        </style>

        <style>
            #n_content {
                color:#000;
                font-family: "Arial";
                margin: 5px 20px 20px 20px;
                /*overflow-x:auto;*/
            }
            
            .h1judul {
              color: blue;
              font-family: verdana;
              font-size: 140%;
              font-weight: bold;
            }
            table.tbljudul {
                font-size : 15px;
            }
            table.tbljudul tr td {
                padding: 1px;
                font-family : "Arial, Verdana, sans-serif";
            }
            .tebal {
                 font-weight: bold;
            }
            .miring {
                 font-style: italic;
            }
            table.tbljudul tr.text2 {
                font-size : 13px;
            }
            .tjudul {
                font-family: Georgia, serif;
                font-size: 15px;
                margin-left:10px;
                margin-right:10px;
            }
            .tjudul td {
                padding: 4px;
            }
            .ijudul h2 {
                font-size: 16px;
                font-weight:bold;
            }
        </style>
    
        <style>
            
            .divnone {
                display: none;
            }
            #mydatatable1, #mydatatable2, #mydatatable3, #mydatatable4 {
                color:#000;
                font-family: "Arial";
            }
            #mydatatable1 th, #mydatatable2 th, #mydatatable3 th, #mydatatable4 th {
                font-size: 12px;
            }
            #mydatatable1 td, #mydatatable2 td, #mydatatable3 td, #mydatatable4 td { 
                font-size: 11px;
            }
        </style>
        
    <?PHP }else{ ?>
        <style>
            .h1judul {
              font-size: 140%;
              font-weight: bold;
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
            
            
            var table1 = $('#mydatatable1').DataTable({
                fixedHeader: true,
                "ordering": false,
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": -1,
                "order": [[ 0, "asc" ]],
                "columnDefs": [
                    { "visible": false },
                    { className: "text-right", "targets": [2,3,4] },//right
                    { className: "text-nowrap", "targets": [0,1,2,3,4] }//nowrap

                ],
                bFilter: false, bInfo: true, "bLengthChange": false, "bLengthChange": true,
                "bPaginate": false
            } );
            
            var table12 = $('#mydatatable2').DataTable({
                fixedHeader: true,
                "ordering": false,
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": -1,
                "order": [[ 0, "asc" ]],
                "columnDefs": [
                    { "visible": false },
                    { className: "text-right", "targets": [2,3] },//right
                    { className: "text-nowrap", "targets": [0,1,2,3] }//nowrap

                ],
                bFilter: false, bInfo: true, "bLengthChange": false, "bLengthChange": true,
                "bPaginate": false
            } );
            
            var table13 = $('#mydatatable3').DataTable({
                fixedHeader: true,
                "ordering": false,
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": -1,
                "order": [[ 0, "asc" ]],
                "columnDefs": [
                    { "visible": false },
                    { className: "text-right", "targets": [2,3,4,5,6] },//right
                    { className: "text-nowrap", "targets": [0,1,2,3,4,5,6] }//nowrap

                ],
                bFilter: false, bInfo: true, "bLengthChange": false, "bLengthChange": true,
                "bPaginate": false
            } );
            
            var table14 = $('#mydatatable4').DataTable({
                fixedHeader: true,
                "ordering": false,
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": -1,
                "order": [[ 0, "asc" ]],
                "columnDefs": [
                    { "visible": false },
                    { className: "text-right", "targets": [1,2,3] },//right
                    { className: "text-nowrap", "targets": [0,1,2,3] }//nowrap

                ],
                bFilter: false, bInfo: true, "bLengthChange": false, "bLengthChange": true,
                "bPaginate": false
            } );
            

        } );
    
    
    </script>
    
    
</HTML>
    
<?PHP
hapusdata:
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp02");
    mysqli_close($cnms);
?>