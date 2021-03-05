<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
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
    header("Content-Disposition: attachment; filename=REPORT SALES DISTRIBUTOR YTD.xls");
}
    

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];
    
$tgl01=$_POST['bulan'];
$pdist=$_POST['distibutor'];
$pregion=$_POST['region'];
$prpttipe=$_POST['rb_rpttipe'];
    
$tgl02=date('F Y', strtotime('-1 year', strtotime($tgl01)));

$p01bln1=date("Y01", strtotime($tgl02));
$p01bln2=date("Ym", strtotime($tgl02));


$p02bln1=date("Y01", strtotime($tgl01));
$p02bln2=date("Ym", strtotime($tgl01));

$pyear1=date("Y", strtotime($tgl02));
$pyear2=date("Y", strtotime($tgl01));

$pbln01=date("F Y", strtotime($tgl02));
$pbln02=date("F Y", strtotime($tgl01));

$pilih_bulan_ = "Periode $tgl01";
//echo "$p01bln1 - $p01bln2, $p02bln1-$p02bln2, $pdist, $pregion, $prpttipe";

$pviewdate=date("d/m/Y H:i:s");

$milliseconds = round(microtime(true) * 1000);
$now=date("mdYhis");
$tmp01 ="dbtemp.TEMPSLSDISCAB01_".$puser."_$now$milliseconds";
$tmp02 ="dbtemp.TEMPSLSDISCAB02_".$puser."_$now$milliseconds";
$tmp03 ="dbtemp.TEMPSLSDISCAB03_".$puser."_$now$milliseconds";
    

include("config/koneksimysqli_ms.php");

$filter_dist="";
if (!empty($pdist)) $filter_dist=" AND distid='$pdist' ";

$filter_region = "";
if ($pregion!="A") {
    $filter_region = " AND idcbg IN (select distinct idcabang from ms.cbgytd WHERE region='$pregion') ";
}else{
    //$filter_region = " AND idcbg IN (select distinct idcabang from ms.cbgytd WHERE IFNULL(aktif,'')='Y' AND region IN ('B', 'T')) ";
}

$filter_prod="";
if ($prpttipe=="N") {
    $filter_prod=" AND iprodid NOT IN ('0000000004') ";
}


$query = "select *, CAST('' as CHAR(1)) as region from sls.ytd_dist WHERE ( (DATE_FORMAT(bulan,'%Y%m') BETWEEN '$p01bln1' AND '$p01bln2') OR "
        . " (DATE_FORMAT(bulan,'%Y%m') BETWEEN '$p02bln1' AND '$p02bln2') ) "
        . " $filter_prod $filter_region $filter_dist ";
$query = "CREATE TEMPORARY TABLE $tmp01 ($query)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }


$query = "UPDATE $tmp01 a JOIN ms.cbgytd b on a.idcbg=b.idcabang SET a.region=b.region";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }


$query = "UPDATE $tmp01 SET region='A' WHERE IFNULL(region,'')=''";
mysqli_query($cnms, $query);


$query = "SELECT DISTINCT a.region, a.distid, c.nama nmdist, "
        . " CAST(0 as DECIMAL(20,2)) as eagle1, CAST(0 as DECIMAL(20,2)) as eagle2, "
        . " CAST(0 as DECIMAL(20,2)) as peaco1, CAST(0 as DECIMAL(20,2)) as peaco2, "
        . " CAST(0 as DECIMAL(20,2)) as pigeo1, CAST(0 as DECIMAL(20,2)) as pigeo2, "
        . " CAST(0 as DECIMAL(20,2)) as total1, CAST(0 as DECIMAL(20,2)) as total2, "
        . " CAST(0 as DECIMAL(20,2)) as ntotal"
        . " FROM $tmp01 a LEFT JOIN sls.distrib0 c on a.distid=c.Distid";
$query = "CREATE TEMPORARY TABLE $tmp02 ($query)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }



//EAGLE
for ($ix=1;$ix<=2;$ix++) {
    $nthn=$pyear1;
    if ((INT)$ix==2) $nthn=$pyear2;
    
    $query = "UPDATE $tmp02 a JOIN (select region as rgn, distid as iddist, SUM(value) as nvalue FROM $tmp01 WHERE YEAR(bulan)='$nthn' AND divprodid='EAGLE' GROUP BY  1,2) b on "
            . " a.distid=b.iddist AND a.region=b.rgn SET "
            . " a.eagle".(INT)$ix."=b.nvalue";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
}

//PEACO
for ($ix=1;$ix<=2;$ix++) {
    $nthn=$pyear1;
    if ((INT)$ix==2) $nthn=$pyear2;
    
    $query = "UPDATE $tmp02 a JOIN (select region as rgn, distid as iddist, SUM(value) as nvalue FROM $tmp01 WHERE YEAR(bulan)='$nthn' AND divprodid='PEACO' GROUP BY  1,2) b on "
            . " a.distid=b.iddist AND a.region=b.rgn SET "
            . " a.peaco".(INT)$ix."=b.nvalue";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
}

//PIGEO
for ($ix=1;$ix<=2;$ix++) {
    $nthn=$pyear1;
    if ((INT)$ix==2) $nthn=$pyear2;
    
    $query = "UPDATE $tmp02 a JOIN (select region as rgn, distid as iddist, SUM(value) as nvalue FROM $tmp01 WHERE YEAR(bulan)='$nthn' AND divprodid='PIGEO' GROUP BY  1,2) b on "
            . " a.distid=b.iddist AND a.region=b.rgn SET "
            . " a.pigeo".(INT)$ix."=b.nvalue";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
}

$query = "UPDATE $tmp02 SET total1=IFNULL(eagle1,0)+IFNULL(peaco1,0)+IFNULL(pigeo1,0), "
        . " total2=IFNULL(eagle2,0)+IFNULL(peaco2,0)+IFNULL(pigeo2,0), "
        . " ntotal=IFNULL(eagle1,0)+IFNULL(peaco1,0)+IFNULL(pigeo1,0)+IFNULL(eagle2,0)+IFNULL(peaco2,0)+IFNULL(pigeo2,0)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
?>

<HTML>
<HEAD>
    <title>Report Sales Distributor YTD</title>
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
                    echo "<tr><td colspan=5 width='150px'><b>Report Sales Distributor YTD $pilih_bulan_</b></td></tr>";
                    echo "<tr><td colspan=5 width='150px'>view date : $pviewdate</td></tr>";
                }else{
                    echo "<tr><td width='150px'><b><h3>Report Sales Distributor YTD $pilih_bulan_</h3></b></td></tr>";
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
            <th align="center">NO</th>
            <th align="center">DISTRIBUTOR</th>
            <th align="center">EAGLE <?PHP echo $pyear1; ?></th>
            <th align="center">EAGLE <?PHP echo $pyear2; ?></th>
            <th align="center">PEACOK <?PHP echo $pyear1; ?></th>
            <th align="center">PEACOK <?PHP echo $pyear2; ?></th>
            <th align="center">PIGEON <?PHP echo $pyear1; ?></th>
            <th align="center">PIGEON <?PHP echo $pyear2; ?></th>
            <th align="center">TOTAL <?PHP echo $pyear1; ?></th>
            <th align="center">TOTAL <?PHP echo $pyear2; ?></th>
            <!--<th align="center">TOTAL</th>-->
            </tr>
        </thead>
        <tbody>
            <?PHP
            $tot_peagle1=0;
            $tot_peagle2=0;
            $tot_peaco1=0;
            $tot_peaco2=0;
            $tot_pigeo1=0;
            $tot_pigeo2=0;
            $tot_total1=0;
            $tot_total2=0;
            $tot_ntotal=0;
            
            $g_peagle1=0;
            $g_peagle2=0;
            $g_peaco1=0;
            $g_peaco2=0;
            $g_pigeo1=0;
            $g_pigeo2=0;
            $g_total1=0;
            $g_total2=0;
            $g_ntotal=0;
            
            $query = "select distinct region from $tmp02 order by region";
            $tampil= mysqli_query($cnms, $query);
            while ($row1= mysqli_fetch_array($tampil)) {
                $nregion=$row1['region'];
                $dnmregion="BARAT";
                if ($nregion=="T") $dnmregion="TIMUR";
                elseif ($nregion=="A") $dnmregion="NONE";
                
                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap colspan='9'><b>$dnmregion</b></td>";
                if ($ppilihrpt!="excel") {
                    echo "<td nowrap class='divnone'></td>";
                    echo "<td nowrap class='divnone'></td>";
                    echo "<td nowrap class='divnone'></td>";
                    echo "<td nowrap class='divnone'></td>";
                    echo "<td nowrap class='divnone'></td>";
                    echo "<td nowrap class='divnone'></td>";
                    echo "<td nowrap class='divnone'></td>";
                    echo "<td nowrap class='divnone'></td>";
                    //echo "<td nowrap class='divnone'></td>";
                }
                echo "</tr>";
                
                
                $tot_peagle1=0;
                $tot_peagle2=0;
                $tot_peaco1=0;
                $tot_peaco2=0;
                $tot_pigeo1=0;
                $tot_pigeo2=0;
                $tot_total1=0;
                $tot_total2=0;
                $tot_ntotal=0;
            
                $no=1;
                $query = "select * from $tmp02 WHERE region='$nregion' order by nmdist";
                $tampil2= mysqli_query($cnms, $query);
                while ($row2= mysqli_fetch_array($tampil2)) {
                    $pnmdist=$row2['nmdist'];
                    
                    $peagle1=$row2['eagle1'];
                    $peagle2=$row2['eagle2'];
                    $ppeaco1=$row2['peaco1'];
                    $ppeaco2=$row2['peaco2'];
                    $ppigeo1=$row2['pigeo1'];
                    $ppigeo2=$row2['pigeo2'];
                    
                    $ptotal1=$row2['total1'];
                    $ptotal2=$row2['total2'];
                    $pntotal2=$row2['ntotal'];
                    
                    
                    $tot_peagle1=(double)$tot_peagle1+(double)$peagle1;
                    $tot_peagle2=(double)$tot_peagle2+(double)$peagle2;
                    $tot_peaco1=(double)$tot_peaco1+(double)$ppeaco1;
                    $tot_peaco2=(double)$tot_peaco2+(double)$ppeaco2;
                    $tot_pigeo1=(double)$tot_pigeo1+(double)$ppigeo1;
                    $tot_pigeo2=(double)$tot_pigeo2+(double)$ppigeo2;
                    
                    $tot_total1=(double)$tot_total1+(double)$ptotal1;
                    $tot_total2=(double)$tot_total2+(double)$ptotal2;
                    
                    $tot_ntotal=(double)$tot_ntotal+(double)$pntotal2;
                    
                    $peagle1=number_format($peagle1,0,",",",");
                    $peagle2=number_format($peagle2,0,",",",");
                    $ppeaco1=number_format($ppeaco1,0,",",",");
                    $ppeaco2=number_format($ppeaco2,0,",",",");
                    $ppigeo1=number_format($ppigeo1,0,",",",");
                    $ppigeo2=number_format($ppigeo2,0,",",",");
                    
                    $ptotal1=number_format($ptotal1,0,",",",");
                    $ptotal2=number_format($ptotal2,0,",",",");
                    $pntotal2=number_format($pntotal2,0,",",",");
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pnmdist</td>";
                    
                    echo "<td nowrap>$peagle1</td>";
                    echo "<td nowrap>$peagle2</td>";
                    echo "<td nowrap>$ppeaco1</td>";
                    echo "<td nowrap>$ppeaco2</td>";
                    echo "<td nowrap>$ppigeo1</td>";
                    echo "<td nowrap>$ppigeo2</td>";
                    echo "<td nowrap><b>$ptotal1</b></td>";
                    echo "<td nowrap><b>$ptotal2</b></td>";
                    //echo "<td nowrap><b>$pntotal2</b></td>";
                    echo "</tr>";
                    
                    $no++;
                }
                
                $g_peagle1=(double)$g_peagle1+(double)$tot_peagle1;
                $g_peagle2=(double)$g_peagle2+(double)$tot_peagle2;
                $g_peaco1=(double)$g_peaco1+(double)$tot_peaco1;
                $g_peaco2=(double)$g_peaco2+(double)$tot_peaco2;
                $g_pigeo1=(double)$g_pigeo1+(double)$tot_pigeo1;
                $g_pigeo2=(double)$g_pigeo2+(double)$tot_pigeo2;

                $g_total1=(double)$g_total1+(double)$tot_total1;
                $g_total2=(double)$g_total2+(double)$tot_total2;

                $g_ntotal=(double)$g_ntotal+(double)$tot_ntotal;

                
                if ($pregion=="A") {
                    
                    $tot_peagle1=number_format($tot_peagle1,0,",",",");
                    $tot_peagle2=number_format($tot_peagle2,0,",",",");
                    $tot_peaco1=number_format($tot_peaco1,0,",",",");
                    $tot_peaco2=number_format($tot_peaco2,0,",",",");
                    $tot_pigeo1=number_format($tot_pigeo1,0,",",",");
                    $tot_pigeo2=number_format($tot_pigeo2,0,",",",");

                    $tot_total1=number_format($tot_total1,0,",",",");
                    $tot_total2=number_format($tot_total2,0,",",",");
                    $tot_ntotal=number_format($tot_ntotal,0,",",",");
                
                    echo "<tr>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap><b>TOTAL $dnmregion</b></td>";
                    
                    echo "<td nowrap><b>$tot_peagle1</b></td>";
                    echo "<td nowrap><b>$tot_peagle2</b></td>";
                    echo "<td nowrap><b>$tot_peaco1</b></td>";
                    echo "<td nowrap><b>$tot_peaco2</b></td>";
                    echo "<td nowrap><b>$tot_pigeo1</b></td>";
                    echo "<td nowrap><b>$tot_pigeo2</b></td>";
                    echo "<td nowrap><b>$tot_total1</b></td>";
                    echo "<td nowrap><b>$tot_total2</b></td>";
                    //echo "<td nowrap><b>$tot_ntotal</b></td>";
                    echo "</tr>";
                    
                    
                }
                
            }
            
            $g_peagle1=number_format($g_peagle1,0,",",",");
            $g_peagle2=number_format($g_peagle2,0,",",",");
            $g_peaco1=number_format($g_peaco1,0,",",",");
            $g_peaco2=number_format($g_peaco2,0,",",",");
            $g_pigeo1=number_format($g_pigeo1,0,",",",");
            $g_pigeo2=number_format($g_pigeo2,0,",",",");

            $g_total1=number_format($g_total1,0,",",",");
            $g_total2=number_format($g_total2,0,",",",");
            $g_ntotal=number_format($g_ntotal,0,",",",");

            echo "<tr>";
            echo "<td nowrap></td>";
            echo "<td nowrap><b>G R A N D  &nbsp; &nbsp; &nbsp;  T O T A L</b></td>";

            echo "<td nowrap><b>$g_peagle1</b></td>";
            echo "<td nowrap><b>$g_peagle2</b></td>";
            echo "<td nowrap><b>$g_peaco1</b></td>";
            echo "<td nowrap><b>$g_peaco2</b></td>";
            echo "<td nowrap><b>$g_pigeo1</b></td>";
            echo "<td nowrap><b>$g_pigeo2</b></td>";
            echo "<td nowrap><b>$g_total1</b></td>";
            echo "<td nowrap><b>$g_total2</b></td>";
            //echo "<td nowrap><b>$g_ntotal</b></td>";
            echo "</tr>";
            
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
                    { className: "text-right", "targets": [2,3,4,5,6,7,8,9] },//right
                    { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,8,9] }//nowrap

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

