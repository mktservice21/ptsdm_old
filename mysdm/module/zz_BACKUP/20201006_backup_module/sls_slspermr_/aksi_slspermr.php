<?php
	ini_set('display_errors', '0');
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
session_start();
$puser=$_SESSION['IDCARD'];
$pmyidcard=$_SESSION['IDCARD'];
$pmyjabatanid=$_SESSION['JABATANID'];
$pmynamlengkap=$_SESSION['NAMALENGKAP'];

if (empty($puser)) {
    echo "ANDA HARUS LOGIN ULANG....!!!";
    exit;
}


$ppilihrpt=$_GET['ket'];
if ($ppilihrpt=="excel") {
    // Fungsi header dengan mengirimkan raw data excel
    header("Content-type: application/vnd-ms-excel");
    // Mendefinisikan nama file ekspor "hasil-export.xls"
    header("Content-Disposition: attachment; filename=REPORT SALES MR.xls");
}

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];
    
$tgl01=$_POST['bulan'];
$pmrpilih=$_POST['cb_mr'];


$pbln1=date("Ym", strtotime($tgl01));

$pperiode1=date("F Y", strtotime($tgl01));

$pviewdate=date("d/m/Y H:i:s");

$milliseconds = round(microtime(true) * 1000);
$now=date("mdYhis");
$tmp01 ="dbtemp.TEMPSLSMRD01_".$puser."_$now$milliseconds";
$tmp02 ="dbtemp.TEMPSLSMRD02_".$puser."_$now$milliseconds";

include("config/koneksimysqli_ms.php");

$query = "select nama from ms.karyawan where karyawanid='$pmrpilih'";
$tampil= mysqli_query($cnms, $query);
$rs= mysqli_fetch_array($tampil);
$pnamakry=$rs['nama'];



//30-3-2020 info bpk. yakub via WA ditambah fileter kategori hanya EXISTING, login mr, am, dm

$filterkategori="";
if ($pmyjabatanid=="15" OR $pmyjabatanid=="10" OR $pmyjabatanid=="18" OR $pmyjabatanid=="08") {
    $filterkategori=" AND IFNULL(kategoriproduk,'') = 'EXISTING' ";
}

$query = "SELECT divprodid, iprodid, SUM(qty_sales) as qty_sales, sum(qty_target) as qty_target,"
        . " sum(value_sales) as value_sales, sum(value_target) as value_target, CAST(0 as DECIMAL(20,2)) as tach "
        . " FROM sls.sales WHERE DATE_FORMAT(bulan,'%Y%m')='$pbln1' $filterkategori AND "
        . " CONCAT(IFNULL(icabangid,''),IFNULL(areaid,''),IFNULL(divprodid,'')) IN "
        . " (SELECT DISTINCT CONCAT(IFNULL(icabangid,''),IFNULL(areaid,''),IFNULL(divisiid,'')) FROM sls.imr0 WHERE karyawanid='$pmrpilih')";
$query .=" GROUP BY 1,2";
$query = "CREATE TEMPORARY TABLE $tmp01 ($query)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

$query = "SELECT a.divprodid, a.iprodid, b.nama nmprod, a.qty_sales, a.qty_target, a.value_sales, a.value_target, a.tach FROM $tmp01 a LEFT JOIN sls.iproduk b on "
        . " a.iprodid=b.iprodid";
$query = "CREATE TEMPORARY TABLE $tmp02 ($query)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

$query = "UPDATE $tmp02 SET tach=IFNULL(qty_sales,0)/IFNULL(qty_target,0)*100";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }


?>


<HTML>
    
<HEAD>
    <title>Report Sales MR</title>
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
                <?PHP
                
                if ($ppilihrpt=="excel") {
                    echo "<tr><td colspan=5 width='150px'><b>Report Sales MR</b></td></tr>";
                    echo "<tr><td colspan=5 width='150px'><b>Bulan : $pperiode1</b></td></tr>";
                    echo "<tr><td colspan=5 width='150px'><b>MR : $pnamakry</b></td></tr>";
                    
                    echo "<tr><td colspan=5 width='150px'>view date : $pviewdate</td></tr>";
                }else{
                    echo "<tr><td width='150px'><b><h3>Report Sales Per MR</h3></b></td></tr>";
                    echo "<tr><td width='150px'><b>Bulan : $pperiode1</b></td></tr>";
                    echo "<tr><td width='150px'><b>MR : $pnamakry</b></td></tr>";
                    
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
    
    <br/>&nbsp;
    
    
    <table id='datatable2' class='table table-striped table-bordered' width="100%" border="1px solid black">
        <thead>
            <tr>
            <th align="center">PRODUK</th>
            <th align="center">SALES</th>
            <th align="center">TARGET</th>
            <th align="center">ACH</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $pdivtotsls=0;
            $pdivtottgt=0;
            $pgtotsls=0;
            $pgtottgt=0;
            $no=0;
            $query = "select distinct IFNULL(divprodid,'') as divprodid from $tmp02 order by divprodid";
            $tampil1 = mysqli_query($cnms, $query);
            while ($row1= mysqli_fetch_array($tampil1)) {
                $pdivprodid=$row1['divprodid'];
                $no++;
                
                echo "<tr>";
                echo "<td nowrap><b>$pdivprodid</b></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "</tr>";
                
                $pdivtotsls=0;
                $pdivtottgt=0;
                
                $query = "select * from $tmp02 WHERE IFNULL(divprodid,'')='$pdivprodid' order by divprodid, nmprod";
                $tampil2 = mysqli_query($cnms, $query);
                while ($row2= mysqli_fetch_array($tampil2)) {
                    $pprodid=$row2['iprodid'];
                    $pnmprod=$row2['nmprod'];
                    $pqtysales=$row2['qty_sales'];
                    $pqtytarget=$row2['qty_target'];
                    $pvalsls=$row2['value_sales'];
                    $pvaltgt=$row2['value_target'];
                    $pach=ROUND($row2['tach'],2);
                    
                    $pdivtotsls=(DOUBLE)$pdivtotsls+(DOUBLE)$pvalsls;
                    $pdivtottgt=(DOUBLE)$pdivtottgt+(DOUBLE)$pvaltgt;
                    
                    $pgtotsls=(DOUBLE)$pgtotsls+(DOUBLE)$pvalsls;
                    $pgtottgt=(DOUBLE)$pgtottgt+(DOUBLE)$pvaltgt;
                    
                    $pvalsls=number_format($pvalsls,0,",",",");
                    $pvaltgt=number_format($pvaltgt,0,",",",");

                    
                    echo "<tr>";
                    echo "<td nowrap>$pnmprod</td>";
                    echo "<td nowrap>$pqtysales</td>";
                    echo "<td nowrap>$pqtytarget</td>";
                    echo "<td nowrap>$pach</td>";
                    echo "</tr>";
                    
                }
                $pntotach=0;
                if ((DOUBLE)$pdivtottgt<>0) $pntotach=(DOUBLE)$pdivtotsls/(DOUBLE)$pdivtottgt*100;
                $pntotach=ROUND($pntotach,2);
                $pdivtotsls=number_format($pdivtotsls,0,",",",");
                $pdivtottgt=number_format($pdivtottgt,0,",",",");
                
                echo "<tr>";
                echo "<td nowrap><b>TOTAL $pdivprodid : </b></td>";
                echo "<td nowrap><b>$pdivtotsls</b></td>";
                echo "<td nowrap><b>$pdivtottgt</b></td>";
                echo "<td nowrap><b>$pntotach</b></td>";
                echo "</tr>";
                
            }
            $pngrandach=0;
            if ((DOUBLE)$pgtottgt<>0) $pngrandach=(DOUBLE)$pgtotsls/(DOUBLE)$pgtottgt*100;
            $pngrandach=ROUND($pngrandach,2);
            $pgtotsls=number_format($pgtotsls,0,",",",");
            $pgtottgt=number_format($pgtottgt,0,",",",");
            if ($no>1) {
                echo "<tr>";
                echo "<td nowrap><b>GRAND TOTAL : </b></td>";
                echo "<td nowrap><b>$pgtotsls</b></td>";
                echo "<td nowrap><b>$pgtottgt</b></td>";
                echo "<td nowrap><b>$pngrandach</b></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
    
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
        
                <?PHP
                if ($_SESSION['MOBILE']=="Y") {
                ?>
                    <style>
                        .divnone {
                            display: none;
                        }
                        #datatable2, #datatable3 {
                            color:#000;
                            font-family: "Arial";
                            margin-right:30px;
                        }
                        #datatable2 th, #datatable3 th {
                            font-size: 20px;
                        }
                        #datatable2 td, #datatable3 td { 
                            font-size: 22px;
                        }
                    </style>
                <?PHP
                }else{
                ?>
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
                <?PHP
                }
                ?>
        
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
                font-size: 12px;
            }
            #datatable2 tbody, #datatable3 tbody{
                font-size: 11px;
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
                        { className: "text-right", "targets": [1,2,3] },//right
                        { className: "text-nowrap", "targets": [0, 1, 2, 3] }//nowrap

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
    mysqli_close($cnms);
?>