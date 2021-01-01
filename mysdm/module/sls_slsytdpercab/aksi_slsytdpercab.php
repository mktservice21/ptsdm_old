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
    header("Content-Disposition: attachment; filename=REPORT SALES YTD PER CABANG.xls");
}

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];
    
$tgl01=$_POST['bulan'];
$pcab=$_POST['cb_cabang'];
$pidregion=$_POST['cb_region'];

if ($_SESSION['IDCARD']=="0000000175") {
	if (empty($pidregion)) exit;
	if (empty($pcab)) exit;
}

$pptgl01 = date('Y-m-d', strtotime("-1 year", strtotime(date("Y-m-01", strtotime($tgl01)))));
$pptgl02 = date("Y-m-01", strtotime($tgl01));

//YTD tahun lalu
$pbln1 = date("Y01", strtotime($pptgl01));
$pbln2 = date("Ym", strtotime($pptgl01));

//YTD tahun sekrang
$pbln3 = date("Y01", strtotime($pptgl02));
$pbln4 = date("Ym", strtotime($pptgl02));

$ptahun1 = date("Y", strtotime($pptgl01));
$ptahun2 = date("Y", strtotime($pptgl02));

$pperiode1=date("F Y", strtotime($tgl01));

$pviewdate=date("d/m/Y H:i:s");

$milliseconds = round(microtime(true) * 1000);
$now=date("mdYhis");
$tmp01 ="dbtemp.TEMPSLSYTDCB01_".$puser."_$now$milliseconds";
$tmp02 ="dbtemp.TEMPSLSYTDCB02_".$puser."_$now$milliseconds";
$tmp03 ="dbtemp.TEMPSLSYTDCB03_".$puser."_$now$milliseconds";




include("config/koneksimysqli_ms.php");

$query = "select nama from sls.icabang where icabangid='$pcab'";
$tampil= mysqli_query($cnms, $query);
$rs= mysqli_fetch_array($tampil);
$pnamacabang=$rs['nama'];

$filter_cabang= " ";
if (!empty($pcab)) {
    $filter_cabang= " AND icabangid='$pcab' ";
}

if ($pmyjabatanid=="15") {
    $filter_cabang= " AND CONCAT(icabangid,areaid,divprodid) IN "
            . " (select DISTINCT CONCAT(icabangid,areaid,divprodid) FROM sls.imr0 WHERE karyawanid='$pmyidcard') ";
}elseif ($pmyjabatanid=="10" OR $pmyjabatanid=="18") {
    $filter_cabang= " AND CONCAT(icabangid,areaid,divprodid) IN "
            . " (select DISTINCT CONCAT(icabangid,areaid,divisiid) FROM sls.ispv0 WHERE karyawanid='$pmyidcard') ";
    if (!empty($pcab)) {
        $filter_cabang .=" AND icabangid='$pcab' ";
    }
}elseif ($pmyjabatanid=="08") {
    if (empty($pcab)) {
        $filter_cabang= " AND icabangid IN "
                . " (select DISTINCT icabangid FROM sls.idm0 WHERE karyawanid='$pmyidcard') ";
    }
}elseif ($pmyjabatanid=="20") {
    if (empty($pcab)) {
        $filter_cabang= " AND icabangid IN "
                . " (select DISTINCT icabangid FROM sls.ism0 WHERE karyawanid='$pmyidcard') ";
    }
}else{

}

$filter_region="";
if (!empty($pidregion)) $filter_region=" AND icabangid IN (select distinct icabangid from sls.icabang WHERE region='$pidregion')";

//echo "$pptgl01, $pptgl02<br/>$ptahun1 - $ptahun2 <br/>$pbln1 - $pbln2 <br/> $pbln3 - $pbln4 <br/>";


$query = "select * from sls.sales WHERE YEAR(bulan) BETWEEN '$ptahun1' AND '$ptahun2' "
        . " $filter_cabang $filter_region ";
$query = "CREATE TEMPORARY TABLE $tmp01 ($query)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

$query = "DELETE FROM $tmp01 WHERE IFNULL(qty_target,0)=0 AND IFNULL(value_target,0)=0 AND IFNULL(qty_sales,0)=0 AND IFNULL(value_sales,0)=0";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan DELETE KOSONG"; goto hapusdata; }

$query = "select DISTINCT a.divprodid, a.iprodid, b.nama prodnm, CAST(NULL as CHAR(50)) as kategori, "
        . " CAST(NULL as DECIMAL(30,2)) as hna "
        . " FROM $tmp01 a LEFT JOIN sls.iproduk b on a.iprodid=b.iprodid";
$query = "CREATE TEMPORARY TABLE $tmp02 ($query)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }


$query = "UPDATE $tmp02 a JOIN "
        . " (select distinct iprodid, kategori from sls.ytdprod WHERE DATE_FORMAT(bulan,'%Y%m')='$pbln4') b "
        . " on a.iprodid=b.iprodid "
        . " SET a.kategori=b.kategori";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

$query = "UPDATE $tmp02 SET kategori='' WHERE IFNULL(kategori,'')=''";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }


$query = "alter table $tmp02 "
        . " ADD mtqty DOUBLE(32,2), ADD mtvalue DOUBLE(32,2), "
        . " ADD msqty DOUBLE(32,2), ADD msvalue DOUBLE(32,2), ADD mach DOUBLE(32,2), "
        . " ADD mlstyearqty DOUBLE(32,2), ADD mlstyearvalue DOUBLE(32,2), ADD mgrowth DOUBLE(32,2), "
        . " ADD ytqty DOUBLE(32,2), ADD ytvalue DOUBLE(32,2), "
        . " ADD ysqty DOUBLE(32,2), ADD ysvalue DOUBLE(32,2), ADD yach DOUBLE(32,2), "
        . " ADD ylstyearqty DOUBLE(32,2), ADD ylstyearvalue DOUBLE(32,2), ADD ygrowth DOUBLE(32,2), "
        . " ADD year_tqty DOUBLE(32,2), ADD year_tvalue DOUBLE(32,2), "
        . " ADD year_sqty DOUBLE(32,2), ADD year_svalue DOUBLE(32,2), ADD year_ach DOUBLE(32,2)"
        . "";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }



//monthly
$query = "UPDATE $tmp02 a JOIN "
        . " (select iprodid, sum(qty_target) qty_target, sum(value_target) value_target, "
        . " sum(qty_sales) qty_sales, sum(value_sales) as value_sales FROM $tmp01 WHERE"
        . " DATE_FORMAT(bulan,'%Y%m')='$pbln4' GROUP BY 1) as b "
        . " on a.iprodid=b.iprodid "
        . " SET a.mtqty=b.qty_target, a.mtvalue=b.value_target, "
        . " a.msqty=b.qty_sales, a.msvalue=b.value_sales";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan UPDATE monthly"; goto hapusdata; }

//monthly lst year
$query = "UPDATE $tmp02 a JOIN "
        . " (select iprodid, sum(qty_sales) qty_sales, sum(value_sales) as value_sales FROM $tmp01 WHERE"
        . " DATE_FORMAT(bulan,'%Y%m')='$pbln2' GROUP BY 1) as b "
        . " on a.iprodid=b.iprodid "
        . " SET a.mlstyearqty=b.qty_sales, a.mlstyearvalue=b.value_sales";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan UPDATE monthly last year"; goto hapusdata; }


//ytd
$query = "UPDATE $tmp02 a JOIN "
        . " (select iprodid, sum(qty_target) qty_target, sum(value_target) value_target, "
        . " sum(qty_sales) qty_sales, sum(value_sales) as value_sales FROM $tmp01 WHERE"
        . " DATE_FORMAT(bulan,'%Y%m') between '$pbln3' AND '$pbln4' GROUP BY 1) as b "
        . " on a.iprodid=b.iprodid "
        . " SET a.ytqty=b.qty_target, a.ytvalue=b.value_target, "
        . " a.ysqty=b.qty_sales, a.ysvalue=b.value_sales";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan UPDATE YTD"; goto hapusdata; }



//ytd lst year
$query = "UPDATE $tmp02 a JOIN "
        . " (select iprodid, sum(qty_sales) qty_sales, sum(value_sales) as value_sales FROM $tmp01 WHERE"
        . " DATE_FORMAT(bulan,'%Y%m') between '$pbln1' AND '$pbln2' GROUP BY 1) as b "
        . " on a.iprodid=b.iprodid "
        . " SET a.ylstyearqty=b.qty_sales, a.ylstyearvalue=b.value_sales";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan UPDATE YTD last year"; goto hapusdata; }



//YEAR NOW
$query = "UPDATE $tmp02 a JOIN "
        . " (select iprodid, sum(qty_target) qty_target, sum(value_target) value_target, "
        . " sum(qty_sales) qty_sales, sum(value_sales) as value_sales FROM $tmp01 WHERE"
        . " YEAR(bulan)= '$ptahun2' GROUP BY 1) as b "
        . " on a.iprodid=b.iprodid "
        . " SET a.year_tqty=b.qty_target, a.year_tvalue=b.value_target, "
        . " a.year_sqty=b.qty_sales, a.year_svalue=b.value_sales";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan UPDATE YEAR NOW"; goto hapusdata; }


$query = "update $tmp02 as a set a.hna=(select b.hna_sales from $tmp01 as b WHERE a.iprodid=b.iprodid order by b.hna_sales desc LIMIT 1)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan UPDATE HNA"; goto hapusdata; }

$query = "update $tmp02 as a set mach=IFNULL(msqty,0)/IFNULL(mtqty,0)*100, yach=IFNULL(ysqty,0)/IFNULL(ytqty,0)*100, "
        . " year_ach=IFNULL(year_sqty,0)/IFNULL(year_tqty,0)*100, "
        . " mgrowth=(IFNULL(msqty,0)/IFNULL(mlstyearqty,0))*100-100, "
        . " ygrowth=(IFNULL(ysqty,0)/IFNULL(ylstyearqty,0))*100-100";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan UPDATE HNA"; goto hapusdata; }




?>

<HTML>
<HEAD>
    <title>Report Sales YTD Per Cabang</title>
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
                    echo "<tr><td colspan=5 width='150px'><b>Report Sales YTD Per Cabang</b></td></tr>";
                    echo "<tr><td colspan=5 width='150px'><b>Bulan : $pperiode1</b></td></tr>";
                    echo "<tr><td colspan=5 width='150px'><b>Cabang : $pnamacabang</b></td></tr>";
                    
                    if ($pmyjabatanid=="10" OR $pmyjabatanid=="18" OR $pmyjabatanid=="15" OR $pmyjabatanid=="08" OR $pmyjabatanid=="20") {
                        echo "<tr><td colspan=5 width='150px'><b>Karyawan : $pmynamlengkap</b></td></tr>";
                    }
                    
                    echo "<tr><td colspan=5 width='150px'>view date : $pviewdate</td></tr>";
                }else{
                    echo "<tr><td width='150px'><b><h3>Report Sales YTD Per Cabang</h3></b></td></tr>";
                    echo "<tr><td width='150px'><b>Bulan : $pperiode1</b></td></tr>";
                    echo "<tr><td width='150px'><b>Cabang : $pnamacabang</b></td></tr>";
                    
                    if ($pmyjabatanid=="10" OR $pmyjabatanid=="18" OR $pmyjabatanid=="15" OR $pmyjabatanid=="08" OR $pmyjabatanid=="20") {
                        echo "<tr><td width='150px'><b>Karyawan : $pmynamlengkap</b></td></tr>";
                    }
                    
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
    
    <?PHP
    
    ?>
    
        <table id='datatable2' class='table table-striped table-bordered' width="100%" border="1px solid black">
            <thead>
                <tr>
                    <th rowspan="2" align="center">Produk</th>
                    <th rowspan="2" align="center">HNA</th>
                    <th colspan="5" align="center">Monthly</th>
                    <th colspan="5" align="center">Year to Date</th>
                    <th colspan="2" align="center">Year</th>
                </tr>
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
                <?PHP
                
                $gtotmont_tgt=0; $gtotmont_sls=0; $gtotmont_last=0;
                $gtotytd_tgt=0; $gtotytd_sls=0; $gtotytd_last=0;
                $gtotyear_tgt=0; $gtotyear_sls=0;
                        
                $no=1;
                $query = "select distinct divprodid FROM $tmp02 order by divprodid";
                $tampil0= mysqli_query($cnms, $query);
                while ($row0= mysqli_fetch_array($tampil0)) {
                    $ndivprod=$row0['divprodid'];
                    
                    $npnamadivprod=$ndivprod;

                    echo "<tr>";
                    echo "<td nowrap colspan='14'><b>$npnamadivprod</b></td>";
                    if ($ppilihrpt!="excel") {
                        echo "<td nowrap class='divnone'></td>";
                        echo "<td nowrap class='divnone'></td>";
                        echo "<td nowrap class='divnone'></td>";
                        echo "<td nowrap class='divnone'></td>";
                        echo "<td nowrap class='divnone'></td>";
                        echo "<td nowrap class='divnone'></td>";
                        echo "<td nowrap class='divnone'></td>";
                        echo "<td nowrap class='divnone'></td>";
                        echo "<td nowrap class='divnone'></td>";
                        echo "<td nowrap class='divnone'></td>";
                        echo "<td nowrap class='divnone'></td>";
                        echo "<td nowrap class='divnone'></td>";
                        echo "<td nowrap class='divnone'></td>";
                    }
                    echo "</tr>";
                        
                    $no++;
                    
                    $dtotmont_tgt=0; $dtotmont_sls=0; $dtotmont_last=0;
                    $dtotytd_tgt=0; $dtotytd_sls=0; $dtotytd_last=0;
                    $dtotyear_tgt=0; $dtotyear_sls=0;
                    
                    $query = "select distinct divprodid, kategori FROM $tmp02 WHERE divprodid='$ndivprod' order by divprodid, kategori";
                    $tampil1= mysqli_query($cnms, $query);
                    while ($row1= mysqli_fetch_array($tampil1)) {
                        $nkategori=$row1['kategori'];
                        
                        $npnmkaegori=$nkategori;
                        if (empty($nkategori)) $npnmkaegori = "&nbsp;";
                        
                        echo "<tr>";
                        echo "<td nowrap align='center'><b>$npnmkaegori</b></td>";
                        if ($ppilihrpt!="excel") {
                            echo "<td nowrap ></td>";
                            echo "<td nowrap ></td>";
                            echo "<td nowrap ></td>";
                            echo "<td nowrap ></td>";
                            echo "<td nowrap ></td>";
                            echo "<td nowrap ></td>";
                            echo "<td nowrap ></td>";
                            echo "<td nowrap ></td>";
                            echo "<td nowrap ></td>";
                            echo "<td nowrap ></td>";
                            echo "<td nowrap ></td>";
                            echo "<td nowrap ></td>";
                            echo "<td nowrap ></td>";
                        }
                        echo "</tr>";
                        
                        
                        $totmont_tgt=0; $totmont_sls=0; $totmont_last=0;
                        $totytd_tgt=0; $totytd_sls=0; $totytd_last=0;
                        $totyear_tgt=0; $totyear_sls=0;
                        
                        
                        $query = "select * FROM $tmp02 WHERE divprodid='$ndivprod' AND kategori='$nkategori' order by prodnm, iprodid";
                        $tampil2= mysqli_query($cnms, $query);
                        while ($row2= mysqli_fetch_array($tampil2)) {

                            $npidprod=$row2['iprodid'];
                            $npnmprod=$row2['prodnm'];
                            
                            $nhna=$row2['hna'];
                            
                            //MONTHLY
                            $nmtgt_qty=$row2['mtqty'];
                            $nmtgt_val=$row2['mtvalue'];
                            
                            $nmsls_qty=$row2['msqty'];
                            $nmsls_val=$row2['msvalue'];
                            
                            $nm_ach=$row2['mach'];

                            $nmlstyear_qty=$row2['mlstyearqty'];
                            $nmlstyear_value=$row2['mlstyearvalue'];
                            
                            $nm_growth=$row2['mgrowth'];
                            

                            $totmont_tgt=(double)$totmont_tgt+(double)$nmtgt_val;
                            $totmont_sls=(double)$totmont_sls+(double)$nmsls_val;
                            $totmont_last=(double)$totmont_last+(double)$nmlstyear_value;
                            
                            
                            $dtotmont_tgt=(double)$dtotmont_tgt+(double)$nmtgt_val;
                            $dtotmont_sls=(double)$dtotmont_sls+(double)$nmsls_val;
                            $dtotmont_last=(double)$dtotmont_last+(double)$nmlstyear_value;

                            $gtotmont_tgt=(double)$gtotmont_tgt+(double)$nmtgt_val;
                            $gtotmont_sls=(double)$gtotmont_sls+(double)$nmsls_val;
                            $gtotmont_last=(double)$gtotmont_last+(double)$nmlstyear_value;
                            
                            $nhna=number_format($nhna,0,",",",");
                            
                            $nmtgt_qty=number_format($nmtgt_qty,0,",",",");
                            $nmtgt_val=number_format($nmtgt_val,0,",",",");
                            
                            $nmsls_qty=number_format($nmsls_qty,0,",",",");
                            $nmsls_val=number_format($nmsls_val,0,",",",");
                            
                            $nm_ach=ROUND($nm_ach,2);
                            
                            $nmlstyear_qty=number_format($nmlstyear_qty,0,",",",");
                            $nmlstyear_value=number_format($nmlstyear_value,0,",",",");
                            
                            $nm_growth=ROUND($nm_growth,2);
                            
                            
                            //YTD
                            $nytdtgt_qty=$row2['ytqty'];
                            $nytdtgt_val=$row2['ytvalue'];
                            
                            $nytdsls_qty=$row2['ysqty'];
                            $nytdsls_val=$row2['ysvalue'];
                            
                            $nytd_ach=$row2['yach'];

                            $nytdlstyear_qty=$row2['ylstyearqty'];
                            $nytdlstyear_value=$row2['ylstyearvalue'];
                            
                            $nytd_growth=$row2['ygrowth'];
                            
                            $totytd_tgt=(double)$totytd_tgt+(double)$nytdtgt_val;
                            $totytd_sls=(double)$totytd_sls+(double)$nytdsls_val;
                            $totytd_last=(double)$totytd_last+(double)$nytdlstyear_value;
                            
                            $dtotytd_tgt=(double)$dtotytd_tgt+(double)$nytdtgt_val;
                            $dtotytd_sls=(double)$dtotytd_sls+(double)$nytdsls_val;
                            $dtotytd_last=(double)$dtotytd_last+(double)$nytdlstyear_value;
                            
                            $gtotytd_tgt=(double)$gtotytd_tgt+(double)$nytdtgt_val;
                            $gtotytd_sls=(double)$gtotytd_sls+(double)$nytdsls_val;
                            $gtotytd_last=(double)$gtotytd_last+(double)$nytdlstyear_value;
                            
                            $nytdtgt_qty=number_format($nytdtgt_qty,0,",",",");
                            $nytdtgt_val=number_format($nytdtgt_val,0,",",",");
                            
                            $nytdsls_qty=number_format($nytdsls_qty,0,",",",");
                            $nytdsls_val=number_format($nytdsls_val,0,",",",");
                            
                            $nytd_ach=ROUND($nytd_ach,2);
                            
                            $nytdlstyear_qty=number_format($nytdlstyear_qty,0,",",",");
                            $nytdlstyear_value=number_format($nytdlstyear_value,0,",",",");
                            
                            $nytd_growth=ROUND($nytd_growth,2);
                            
                            
                            
                            //YEAR
                            $nyeart_qty=$row2['year_tqty'];
                            $nyeart_val=$row2['year_tvalue'];
                            
                            $nyears_qty=$row2['year_sqty'];
                            $nyears_val=$row2['year_svalue'];
                            
                            $nyear_ach=$row2['year_ach'];
                            
                            $totyear_tgt=(double)$totyear_tgt+(double)$nyeart_val;
                            $totyear_sls=(double)$totyear_sls+(double)$nyears_val;
                            
                            $dtotyear_tgt=(double)$dtotyear_tgt+(double)$nyeart_val;
                            $dtotyear_sls=(double)$dtotyear_sls+(double)$nyears_val;
                            
                            $gtotyear_tgt=(double)$gtotyear_tgt+(double)$nyeart_val;
                            $gtotyear_sls=(double)$gtotyear_sls+(double)$nyears_val;
                            
                            $nyeart_qty=number_format($nyeart_qty,0,",",",");
                            $nyeart_val=number_format($nyeart_val,0,",",",");
                            
                            $nyears_qty=number_format($nyears_qty,0,",",",");
                            $nyears_val=number_format($nyears_val,0,",",",");
                            
                            $nyear_ach=ROUND($nyear_ach,2);
                            
                            echo "<tr>";
                            echo "<td nowrap>$npnmprod</td>";
                            echo "<td nowrap>$nhna</td>";
                            echo "<td nowrap>$nmtgt_qty</td>";
                            echo "<td nowrap>$nmsls_qty</td>";
                            echo "<td nowrap>$nm_ach</td>";
                            echo "<td nowrap>$nmlstyear_qty</td>";
                            echo "<td nowrap>$nm_growth</td>";
                            echo "<td nowrap>$nytdtgt_qty</td>";
                            echo "<td nowrap>$nytdsls_qty</td>";
                            echo "<td nowrap>$nytd_ach</td>";
                            echo "<td nowrap>$nytdlstyear_qty</td>";
                            echo "<td nowrap>$nytd_growth</td>";
                            echo "<td nowrap>$nyeart_qty</td>";
                            echo "<td nowrap>$nyear_ach</td>";
                            echo "</tr>";

                        }
                        
                        if ((DOUBLE)$totmont_tgt==0) $totmont_ach=0;
                        else $totmont_ach=(DOUBLE)$totmont_sls/(DOUBLE)$totmont_tgt*100;
                        
                        if ((DOUBLE)$totmont_last==0) $totmont_growth=0;
                        else $totmont_growth=((DOUBLE)$totmont_sls/(DOUBLE)$totmont_last)*100-100;
                        
                        $totmont_tgt=number_format($totmont_tgt,0,",",",");
                        $totmont_sls=number_format($totmont_sls,0,",",",");
                        $totmont_ach=ROUND($totmont_ach,2);
                        $totmont_last=number_format($totmont_last,0,",",",");
                        $totmont_growth=ROUND($totmont_growth,2);
                        
                        
                        if ((DOUBLE)$totytd_tgt==0) $totytd_ach=0;
                        else $totytd_ach=(DOUBLE)$totytd_sls/(DOUBLE)$totytd_tgt*100;
                        
                        if ((DOUBLE)$totytd_last==0) $totytd_growth=0;
                        else $totytd_growth=((DOUBLE)$totytd_sls/(DOUBLE)$totytd_last)*100-100;
                        
                        $totytd_tgt=number_format($totytd_tgt,0,",",",");
                        $totytd_sls=number_format($totytd_sls,0,",",",");
                        $totytd_ach=ROUND($totytd_ach,2);
                        $totytd_last=number_format($totytd_last,0,",",",");
                        $totytd_growth=ROUND($totytd_growth,2);
                        
                        
                        if ((DOUBLE)$totyear_tgt==0) $totyear_ach=0;
                        else $totyear_ach=(DOUBLE)$totyear_sls/(DOUBLE)$totyear_tgt*100;
                        
                        $totyear_tgt=number_format($totyear_tgt,0,",",",");
                        $totyear_sls=number_format($totyear_sls,0,",",",");
                        $totyear_ach=ROUND($totyear_ach,2);
                        
                        echo "<tr>";
                        echo "<td nowrap align='center'><b>TOTAL $npnamadivprod $npnmkaegori : </b></td>";
                        echo "<td nowrap><b></b></td>";
                        echo "<td nowrap><b>$totmont_tgt</b></td>";
                        echo "<td nowrap><b>$totmont_sls</b></td>";
                        echo "<td nowrap><b>$totmont_ach</b></td>";
                        echo "<td nowrap><b>$totmont_last</b></td>";
                        echo "<td nowrap><b>$totmont_growth</b></td>";
                        echo "<td nowrap><b>$totytd_tgt</b></td>";
                        echo "<td nowrap><b>$totytd_sls</b></td>";
                        echo "<td nowrap><b>$totytd_ach</b></td>";
                        echo "<td nowrap><b>$totytd_last</b></td>";
                        echo "<td nowrap><b>$totytd_growth</b></td>";
                        echo "<td nowrap><b>$totyear_tgt</b></td>";
                        echo "<td nowrap><b>$totyear_ach</b></td>";
                        echo "</tr>";
                     
                    }
                    
                    
                        
                    if ((DOUBLE)$dtotmont_tgt==0) $dtotmont_ach=0;
                    else $dtotmont_ach=(DOUBLE)$dtotmont_sls/(DOUBLE)$dtotmont_tgt*100;

                    if ((DOUBLE)$dtotmont_last==0) $dtotmont_growth=0;
                    else $dtotmont_growth=((DOUBLE)$dtotmont_sls/(DOUBLE)$dtotmont_last)*100-100;

                    $dtotmont_tgt=number_format($dtotmont_tgt,0,",",",");
                    $dtotmont_sls=number_format($dtotmont_sls,0,",",",");
                    $dtotmont_ach=ROUND($dtotmont_ach,2);
                    $dtotmont_last=number_format($dtotmont_last,0,",",",");
                    $dtotmont_growth=ROUND($dtotmont_growth,2);


                    if ((DOUBLE)$dtotytd_tgt==0) $dtotytd_ach=0;
                    else $dtotytd_ach=(DOUBLE)$dtotytd_sls/(DOUBLE)$dtotytd_tgt*100;

                    if ((DOUBLE)$dtotytd_last==0) $dtotytd_growth=0;
                    else $dtotytd_growth=((DOUBLE)$dtotytd_sls/(DOUBLE)$dtotytd_last)*100-100;

                    $dtotytd_tgt=number_format($dtotytd_tgt,0,",",",");
                    $dtotytd_sls=number_format($dtotytd_sls,0,",",",");
                    $dtotytd_ach=ROUND($dtotytd_ach,2);
                    $dtotytd_last=number_format($dtotytd_last,0,",",",");
                    $dtotytd_growth=ROUND($dtotytd_growth,2);


                    if ((DOUBLE)$dtotyear_tgt==0) $dtotyear_ach=0;
                    else $dtotyear_ach=(DOUBLE)$dtotyear_sls/(DOUBLE)$dtotyear_tgt*100;

                    $dtotyear_tgt=number_format($dtotyear_tgt,0,",",",");
                    $dtotyear_sls=number_format($dtotyear_sls,0,",",",");
                    $dtotyear_ach=ROUND($dtotyear_ach,2);

                    echo "<tr>";
                    echo "<td nowrap><b>TOTAL $npnamadivprod : </b></td>";
                    echo "<td nowrap><b></b></td>";
                    echo "<td nowrap><b>$dtotmont_tgt</b></td>";
                    echo "<td nowrap><b>$dtotmont_sls</b></td>";
                    echo "<td nowrap><b>$dtotmont_ach</b></td>";
                    echo "<td nowrap><b>$dtotmont_last</b></td>";
                    echo "<td nowrap><b>$dtotmont_growth</b></td>";
                    echo "<td nowrap><b>$dtotytd_tgt</b></td>";
                    echo "<td nowrap><b>$dtotytd_sls</b></td>";
                    echo "<td nowrap><b>$dtotytd_ach</b></td>";
                    echo "<td nowrap><b>$dtotytd_last</b></td>";
                    echo "<td nowrap><b>$dtotytd_growth</b></td>";
                    echo "<td nowrap><b>$dtotyear_tgt</b></td>";
                    echo "<td nowrap><b>$dtotyear_ach</b></td>";
                    echo "</tr>";
                    
                }

                if ((DOUBLE)$gtotmont_tgt==0) $gtotmont_ach=0;
                else $gtotmont_ach=(DOUBLE)$gtotmont_sls/(DOUBLE)$gtotmont_tgt*100;

                if ((DOUBLE)$gtotmont_last==0) $gtotmont_growth=0;
                else $gtotmont_growth=((DOUBLE)$gtotmont_sls/(DOUBLE)$gtotmont_last)*100-100;

                $gtotmont_tgt=number_format($gtotmont_tgt,0,",",",");
                $gtotmont_sls=number_format($gtotmont_sls,0,",",",");
                $gtotmont_ach=ROUND($gtotmont_ach,2);
                $gtotmont_last=number_format($gtotmont_last,0,",",",");
                $gtotmont_growth=ROUND($gtotmont_growth,2);


                if ((DOUBLE)$gtotytd_tgt==0) $gtotytd_ach=0;
                else $gtotytd_ach=(DOUBLE)$gtotytd_sls/(DOUBLE)$gtotytd_tgt*100;

                if ((DOUBLE)$gtotytd_last==0) $gtotytd_growth=0;
                else $gtotytd_growth=((DOUBLE)$gtotytd_sls/(DOUBLE)$gtotytd_last)*100-100;

                $gtotytd_tgt=number_format($gtotytd_tgt,0,",",",");
                $gtotytd_sls=number_format($gtotytd_sls,0,",",",");
                $gtotytd_ach=ROUND($gtotytd_ach,2);
                $gtotytd_last=number_format($gtotytd_last,0,",",",");
                $gtotytd_growth=ROUND($gtotytd_growth,2);


                if ((DOUBLE)$gtotyear_tgt==0) $gtotyear_ach=0;
                else $gtotyear_ach=(DOUBLE)$gtotyear_sls/(DOUBLE)$gtotyear_tgt*100;

                $gtotyear_tgt=number_format($gtotyear_tgt,0,",",",");
                $gtotyear_sls=number_format($gtotyear_sls,0,",",",");
                $gtotyear_ach=ROUND($gtotyear_ach,2);

                echo "<tr>";
                echo "<td nowrap><b>GRAND TOTAL : </b></td>";
                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b>$gtotmont_tgt</b></td>";
                echo "<td nowrap><b>$gtotmont_sls</b></td>";
                echo "<td nowrap><b>$gtotmont_ach</b></td>";
                echo "<td nowrap><b>$gtotmont_last</b></td>";
                echo "<td nowrap><b>$gtotmont_growth</b></td>";
                echo "<td nowrap><b>$gtotytd_tgt</b></td>";
                echo "<td nowrap><b>$gtotytd_sls</b></td>";
                echo "<td nowrap><b>$gtotytd_ach</b></td>";
                echo "<td nowrap><b>$gtotytd_last</b></td>";
                echo "<td nowrap><b>$gtotytd_growth</b></td>";
                echo "<td nowrap><b>$gtotyear_tgt</b></td>";
                echo "<td nowrap><b>$gtotyear_ach</b></td>";
                echo "</tr>";

                
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
                    { className: "text-right", "targets": [1,2,3,4,5,6,7,8,9,10,11,12,13] },//right
                    { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,7,8,9,10,11,12,13] }//nowrap

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