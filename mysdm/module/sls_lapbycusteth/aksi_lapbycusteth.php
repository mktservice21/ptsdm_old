<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
session_start();
$puser=$_SESSION['IDCARD'];

if (empty($puser)) {
    echo "ANDA HARUS LOGIN ULANG....!!!";
    exit;
}

$ppilihrpt=$_GET['ket'];
if ($ppilihrpt=="excel") {
    // Fungsi header dengan mengirimkan raw data excel
    header("Content-type: application/vnd-ms-excel");
    // Mendefinisikan nama file ekspor "hasil-export.xls"
    header("Content-Disposition: attachment; filename=Laporan Sales Per Customer Ethical.xls");
}
    

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

$tgl01=$_POST['e_periode01'];
$tgl02=$_POST['e_periode02'];


$pregion=$_POST['region'];
$pkdcabang=$_POST['cb_cabang'];
$pkdarea=$_POST['cb_area'];
$pkddivisi=$_POST['cb_divisi'];
$pkdproduk=$_POST['cb_produk'];
$pkdsektor=$_POST['cb_sektor'];
$pkddist=$_POST['distibutor'];
$pcabkddist=$_POST['cb_cabdist'];
    

$pptgl01=date("Y-m-d", strtotime($tgl01));
$pptgl02=date("Y-m-d", strtotime($tgl02));


$pviewdate=date("d/m/Y H:i:s");

$milliseconds = round(microtime(true) * 1000);
$now=date("mdYhis");
$tmp01 ="dbtemp.tempslsbucusteth01_".$puser."_$now$milliseconds";
$tmp02 ="dbtemp.tempslsbucusteth02_".$puser."_$now$milliseconds";
$tmp03 ="dbtemp.tempslsbucusteth03_".$puser."_$now$milliseconds";
    

include("config/koneksimysqli_ms.php");

$filter_regcab = "";
if (empty($pkdcabang)) {
    if ($pregion!="A") {
        $filter_regcab = " AND icabangid IN (select distinct IFNULL(icabangid,'') from sls.icabang WHERE region='$pregion') ";
    }
}else{
    $filter_regcab = " AND icabangid='$pkdcabang' ";
}

$filterarea="";
if (!empty($pkdarea)) {
    $filterarea=" And areaid='$pkdarea' ";
}

$filterdivisi="";
if (!empty($pkddivisi)) {
    $filterdivisi=" And divprodid='$pkddivisi' ";
}

$filterproduk="";
if (!empty($pkdproduk)) {
    $filterproduk=" And iprodid='$pkdproduk' ";
}

$filterdisti="";
if (!empty($pkddist)) {
    $filterdisti=" And distid='$pkddist' ";
}



$query = "select * from sls.mr_sales2 WHERE tgljual BETWEEN '$pptgl01' AND '$pptgl02' $filter_regcab $filterarea $filterdivisi $filterproduk $filterdisti";
$query = "CREATE TEMPORARY TABLE $tmp01 ($query)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }




$query = "SELECT a.fakturid, b.region, a.icabangid, b.nama nama_cabang, a.areaid, c.nama nama_area, a.icustid, d.nama nama_customer, "
        . " d.iSektorId, e.nama nama_sektor, a.divprodid, i.eprodid, a.iprodid, f.nama nama_produk, "
        . " a.distid, g.nama nama_dist, a.tgljual, "
        . " a.hna, sum(a.qty) as qty, sum(IFNULL(a.hna,0)*IFNULL(a.qty,0)) as tvalue "
        . " FROM $tmp01 a "
        . " LEFT JOIN sls.icabang b on a.icabangid=b.icabangid "
        . " LEFT JOIN sls.iarea c on a.icabangid=c.icabangid and b.icabangid=c.icabangid and a.areaid=c.areaid "
        . " LEFT JOIN sls.icust d on a.icabangid=d.iCabangId and a.icustid=d.iCustId "
        . " LEFT JOIN MKT.isektor e on d.iSektorId=e.iSektorId "
        . " LEFT JOIN sls.iproduk f on a.iprodid=f.iprodid "
        . " LEFT JOIN sls.distrib0 g on a.distid=g.Distid "
        . " LEFT JOIN sls.eproduk i on f.iprodid=i.iprodid AND a.distid=i.DistId WHERE 1=1";
if (!empty($pkdsektor)) {
    $query .=" AND d.iSektorId='$pkdsektor' ";
}
$query .=" GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18 ";
$query = "CREATE TEMPORARY TABLE $tmp02 ($query)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

?>

<HTML>
<HEAD>
    <title>Laporan Sales Per Customer Ethical</title>
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
                    echo "<tr><td colspan=5 width='150px'><b>Laporan Sales Per Customer Ethical</b></td></tr>";
                    echo "<tr><td colspan=5 width='150px'>view date : $pviewdate</td></tr>";
                }else{
                    echo "<tr><td width='150px'><b><h3>Laporan Sales Per Customer Ethical</h3></b></td></tr>";
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
    
    
    <table id='datatable2' class='table table-striped table-bordered' width="100%" border="1px solid black">
        <thead>
            <tr>
            <th align="center">No</th>
            <th align="center">No Faktur</th>
            <th align="center">Region</th>
            <th align="center">Cabang SDM</th>
            <th align="center">Area SDM</th>
            <th align="center">Nama Outlet</th>
            <th align="center">Sektor</th>
            <th align="center">Line</th>
            <th align="center">ID Produk</th>
            <th align="center">Produk</th>
            <th align="center">Distributor</th>
            <!--<th align="center">Cabang</th>-->
            <th align="center">Tanggal</th>
            <th align="center">HNA</th>
            <th align="center">Qty.</th>
            <th align="center">Value</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $no=1;
            $query = "select * from $tmp02 order by nama_cabang, nama_area, nama_customer, divprodid, nama_produk, fakturid";
            $tampil= mysqli_query($cnms, $query);
            while ($row1= mysqli_fetch_array($tampil)) {
                $nregion=$row1['region'];
                $pnofaktur=$row1['fakturid'];
                $picabangid=$row1['icabangid'];
                $pnmcabangsdm=$row1['nama_cabang'];
                $pnmarea=$row1['nama_area'];
                $pnmcustomer=$row1['nama_customer'];
                $pnmsektor=$row1['nama_sektor'];
                $pdivprodid=$row1['divprodid'];
                $piprodid=$row1['iprodid'];
                $pnmproduk=$row1['nama_produk'];
                $pnmdist=$row1['nama_dist'];
                $ptgljual=$row1['tgljual'];
                $phna=$row1['hna'];
                $pqty=$row1['qty'];
                $ptvalue=$row1['tvalue'];
				
				
                $peproduk=$row1['eprodid'];
				
                $piprodid=$peproduk;
                
                $dnmregion="BARAT";
                if ($nregion=="T") $dnmregion="TIMUR";
                elseif ($nregion=="A") $dnmregion="NONE";
                
                $phna=number_format($phna,0,",",",");
                $pqty=number_format($pqty,0,",",",");
                $ptvalue=number_format($ptvalue,0,",",",");
                
                echo "<tr>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap>$pnofaktur</td>";
                echo "<td nowrap>$dnmregion</td>";
                echo "<td nowrap>$pnmcabangsdm</td>";
                echo "<td nowrap>$pnmarea</td>";
                echo "<td nowrap>$pnmcustomer</td>";
                echo "<td nowrap>$pnmsektor</td>";
                echo "<td nowrap>$pdivprodid</td>";
                echo "<td nowrap>$piprodid</td>";
                echo "<td nowrap>$pnmproduk</td>";
                echo "<td nowrap>$pnmdist</td>";
                echo "<td nowrap>$ptgljual</td>";
                echo "<td nowrap>$phna</td>";
                echo "<td nowrap>$pqty</td>";
                echo "<td nowrap>$ptvalue</td>";
                echo "</tr>";
                
                $no++;
            }
            ?>
        </tbody>
    </table>
    
    
    
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
                font-size: 12px;
            }
            #datatable2 td, #datatable3 td { 
                font-size: 11px;
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
                    { className: "text-right", "targets": [12,13,14] },//right
                    { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,8,9,10,11,12,13,14] }//nowrap

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

