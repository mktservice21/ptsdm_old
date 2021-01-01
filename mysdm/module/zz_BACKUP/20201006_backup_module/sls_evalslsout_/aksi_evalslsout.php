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
    header("Content-Disposition: attachment; filename=EVALUSASI SALES OUTLET.xls");
}

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];
    
$tgl01=$_POST['tahun'];
$pmrpilih=$_POST['cb_mr'];
$pcboutlet=$_POST['cb_outlet'];


$pbln1=$tgl01;

$pperiode1=$tgl01;

$pviewdate=date("d/m/Y H:i:s");

$milliseconds = round(microtime(true) * 1000);
$now=date("mdYhis");
$tmp01 ="dbtemp.TEMPSLSMRD01_".$puser."_$now$milliseconds";
$tmp02 ="dbtemp.TEMPSLSMRD02_".$puser."_$now$milliseconds";
$tmp03 ="dbtemp.TEMPSLSMRD03_".$puser."_$now$milliseconds";

include("config/koneksimysqli_ms.php");

$query = "select icabangid, areaid, nama from sls.icust where CONCAT(IFNULL(icabangid,''),IFNULL(areaid,''),IFNULL(icustid,''))='$pcboutlet'";
$tampil1= mysqli_query($cnms, $query);
$rs1= mysqli_fetch_array($tampil1);
$pnamaoutlet=$rs1['nama'];
$pkdcabotl=$rs1['icabangid'];
$pkdareaotl=$rs1['areaid'];

$query = "select nama from ms.karyawan where karyawanid='$pmrpilih'";
$tampil= mysqli_query($cnms, $query);
$rs= mysqli_fetch_array($tampil);
$pnamakry=$rs['nama'];

$pnmareapilih="";
//$query = "select DISTINCT a.areaid, b.nama from sls.imr0 a JOIN sls.iarea b on a.areaid=b.areaid where a.karyawanid='$pmrpilih'";
$query = "select DISTINCT nama from sls.iarea WHERE icabangid='$pkdcabotl' AND areaid='$pkdareaotl'";
$tampil= mysqli_query($cnms, $query);
while ($nrow= mysqli_fetch_array($tampil)) {
    $pnmareapilih .= $nrow['nama'].", ";
}

if (!empty($pnmareapilih)) {
    //$pnmareapilih="(".substr($pnmareapilih, 0, -2).")";
    $pnmareapilih=substr($pnmareapilih, 0, -2);
}

$query = "select divprodid, iprodid, DATE_FORMAT(tgljual,'%m') bulan, sum(qty) as qty, sum(hna*qty) as tvalue 
    from sls.mr_sales2 WHERE year(tgljual)='$pperiode1' 
    and CONCAT(IFNULL(icabangid,''),IFNULL(areaid,''),IFNULL(icustid,''))='$pcboutlet' ";
if ($pmyjabatanid=="15") {
    $query .=" AND CONCAT(IFNULL(icabangid,''),IFNULL(areaid,'')) IN 
      (SELECT DISTINCT CONCAT(IFNULL(icabangid,''),IFNULL(areaid,'')) FROM sls.imr0 WHERE karyawanid='$pmrpilih') ";
}

$query .=" GROUP BY 1,2,3";
$query = "CREATE TEMPORARY TABLE $tmp01 ($query)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }


$query = "UPDATE $tmp01 SET divprodid=''";
//mysqli_query($cnms, $query);
//$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }


$query = "SELECT a.bulan, a.divprodid, a.iprodid, b.nama nmprod, a.qty, a.tvalue, "
        . " CAST(0 as DECIMAL(20,2)) as tavg, CAST(0 as DECIMAL(20,2)) as tnilai FROM $tmp01 a LEFT JOIN sls.iproduk b on "
        . " a.iprodid=b.iprodid";
$query = "CREATE TEMPORARY TABLE $tmp02 ($query)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }


$query = "UPDATE $tmp02 SET tavg=IFNULL(qty,0)/12*100";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

$query = "select distinct divprodid, iprodid, nmprod, "
        . " CAST(0 as DECIMAL(20,2)) as A, CAST(0 as DECIMAL(20,2)) B, CAST(0 as DECIMAL(20,2)) C, CAST(0 as DECIMAL(20,2)) D, "
        . " CAST(0 as DECIMAL(20,2)) E, CAST(0 as DECIMAL(20,2)) F, CAST(0 as DECIMAL(20,2)) G, CAST(0 as DECIMAL(20,2)) H, "
        . " CAST(0 as DECIMAL(20,2)) I, CAST(0 as DECIMAL(20,2)) J, CAST(0 as DECIMAL(20,2)) K, CAST(0 as DECIMAL(20,2)) L, "
        . " CAST(0 as DECIMAL(20,2)) as AA, CAST(0 as DECIMAL(20,2)) BA, CAST(0 as DECIMAL(20,2)) CA, CAST(0 as DECIMAL(20,2)) DA, "
        . " CAST(0 as DECIMAL(20,2)) EA, CAST(0 as DECIMAL(20,2)) FA, CAST(0 as DECIMAL(20,2)) GA, CAST(0 as DECIMAL(20,2)) HA, "
        . " CAST(0 as DECIMAL(20,2)) IA, CAST(0 as DECIMAL(20,2)) JA, CAST(0 as DECIMAL(20,2)) KA, CAST(0 as DECIMAL(20,2)) LA, "
        . " CAST(0 as DECIMAL(20,2)) totqty, CAST(0 as DECIMAL(20,2)) totvalue,"
        . " CAST(0 as DECIMAL(20,2)) tavg, CAST(0 as DECIMAL(20,2)) tnilai"
        . " FROM $tmp02";
$query = "CREATE TEMPORARY TABLE $tmp03 ($query)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

for ($ix=1;$ix<=12;$ix++) {
    $nx=$ix;
    if (strlen($ix)<=1) $nx="0".$ix;
    
    $field_p="A";
    if ((DOUBLE)$ix==1) $field_p="A";
    if ((DOUBLE)$ix==2) $field_p="B";
    if ((DOUBLE)$ix==3) $field_p="C";
    if ((DOUBLE)$ix==4) $field_p="D";
    if ((DOUBLE)$ix==5) $field_p="E";
    if ((DOUBLE)$ix==6) $field_p="F";
    if ((DOUBLE)$ix==7) $field_p="G";
    if ((DOUBLE)$ix==8) $field_p="H";
    if ((DOUBLE)$ix==9) $field_p="I";
    if ((DOUBLE)$ix==10) $field_p="J";
    if ((DOUBLE)$ix==11) $field_p="K";
    if ((DOUBLE)$ix==12) $field_p="L";
    
    $query = "UPDATE $tmp03 a JOIN "
            . " (SELECT divprodid, iprodid, SUM(qty) as qty, sum(tvalue) as tvalue FROM $tmp02 WHERE "
            . " bulan='$nx' GROUP BY 1,2) as b ON "
            . " a.divprodid=b.divprodid AND a.iprodid=b.iprodid SET "
            . " a.".$field_p."=IFNULL(b.qty,0), a.".$field_p."A=IFNULL(tvalue,0)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
}


$query = "UPDATE $tmp03 SET totqty=IFNULL(A,0)+IFNULL(B,0)+IFNULL(C,0)+IFNULL(D,0)+IFNULL(E,0)+IFNULL(F,0)+IFNULL(G,0)+IFNULL(H,0)+IFNULL(I,0)+IFNULL(J,0)+IFNULL(K,0)+IFNULL(L,0), "
        . " totvalue=IFNULL(AA,0)+IFNULL(BA,0)+IFNULL(CA,0)+IFNULL(DA,0)+IFNULL(EA,0)+IFNULL(FA,0)+IFNULL(GA,0)+IFNULL(HA,0)+IFNULL(IA,0)+IFNULL(JA,0)+IFNULL(KA,0)+IFNULL(LA,0)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }


$query = "UPDATE $tmp03 SET tavg=IFNULL(totqty,0)/12";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

?>


<HTML>
    
<HEAD>
    <title>Evaluasi Sales Outlet</title>
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
                    echo "<tr><td colspan=5 width='150px'><b>Evaluasi Sales Outlet</b></td></tr>";
                    echo "<tr><td colspan=5 width='150px'><b>Tahun : $pperiode1</b></td></tr>";
                    if ($pmyjabatanid=="15") {
                        echo "<tr><td colspan=5 width='150px'><b>MR : $pnamakry</b></td></tr>";
                    }
                    echo "<tr><td colspan=5 width='150px'><b>Outlet : $pnamaoutlet</b></td></tr>";
                    echo "<tr><td colspan=5 width='150px'><b>Area : $pnmareapilih</b></td></tr>";
                    
                    echo "<tr><td colspan=5 width='150px'>view date : $pviewdate</td></tr>";
                }else{
                    echo "<tr><td width='150px'><b><h3>Evaluasi Sales Outlet</h3></b></td></tr>";
                    echo "<tr><td width='150px'><b>Tahun : $pperiode1</b></td></tr>";
                    if ($pmyjabatanid=="15") {
                        echo "<tr><td width='150px'><b>MR : $pnamakry</b></td></tr>";
                    }
                    echo "<tr><td width='150px'><b>Outlet : $pnamaoutlet</b></td></tr>";
                    echo "<tr><td width='150px'><b>Area : $pnmareapilih</b></td></tr>";
                    
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
            <th align="center">Nama Produk</th>
            <th align="center">01</th>
            <th align="center">02</th>
            <th align="center">03</th>
            <th align="center">04</th>
            <th align="center">05</th>
            <th align="center">06</th>
            <th align="center">07</th>
            <th align="center">08</th>
            <th align="center">09</th>
            <th align="center">10</th>
            <th align="center">11</th>
            <th align="center">12</th>
            <th align="center">Total Qty</th>
            <th align="center">Total Nilai</th>
            <th align="center">Average Qty</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $itot_pntotqty=0;
            $itot_ptotjml1=0; $itot_ptotjml2=0; $itot_ptotjml3=0;  $itot_ptotjml4=0; $itot_ptotjml5=0; $itot_ptotjml6=0;
            $itot_ptotjml7=0; $itot_ptotjml8=0; $itot_ptotjml9=0;  $itot_ptotjml10=0; $itot_ptotjml11=0; $itot_ptotjml12=0;
            
            $itot_gpntotqty=0;
            $itot_gptotjml1=0; $itot_gptotjml2=0; $itot_gptotjml3=0;  $itot_gptotjml4=0; $itot_gptotjml5=0; $itot_gptotjml6=0;
            $itot_gptotjml7=0; $itot_gptotjml8=0; $itot_gptotjml9=0;  $itot_gptotjml10=0; $itot_gptotjml11=0; $itot_gptotjml12=0;
            
            
            $pntotqty=0;
            $ptotjml1=0; $ptotjml2=0; $ptotjml3=0;  $ptotjml4=0; $ptotjml5=0; $ptotjml6=0;
            $ptotjml7=0; $ptotjml8=0; $ptotjml9=0;  $ptotjml10=0; $ptotjml11=0; $ptotjml12=0;
            
            $gpntotqty=0;
            $gptotjml1=0; $gptotjml2=0; $gptotjml3=0;  $gptotjml4=0; $gptotjml5=0; $gptotjml6=0;
            $gptotjml7=0; $gptotjml8=0; $gptotjml9=0;  $gptotjml10=0; $gptotjml11=0; $gptotjml12=0;
            
            $no=0;
            $query = "select distinct IFNULL(divprodid,'') as divprodid from $tmp03 order by divprodid";
            $tampil1 = mysqli_query($cnms, $query);
            while ($row1= mysqli_fetch_array($tampil1)) {
                $pdivprodid=$row1['divprodid'];
                $no++;
                
                echo "<tr>";
                echo "<td nowrap align='center'><b>$pdivprodid</b></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "</tr>";
                
                $pntotqty=0;
                $ptotjml1=0; $ptotjml2=0; $ptotjml3=0;  $ptotjml4=0; $ptotjml5=0; $ptotjml6=0;
                $ptotjml7=0; $ptotjml8=0; $ptotjml9=0;  $ptotjml10=0; $ptotjml11=0; $ptotjml12=0;
                
                $gpntotqty=0;
                $gptotjml1=0; $gptotjml2=0; $gptotjml3=0;  $gptotjml4=0; $gptotjml5=0; $gptotjml6=0;
                $gptotjml7=0; $gptotjml8=0; $gptotjml9=0;  $gptotjml10=0; $gptotjml11=0; $gptotjml12=0;
            
            
                $query = "select * from $tmp03 WHERE IFNULL(divprodid,'')='$pdivprodid' order by divprodid, nmprod";
                $tampil2 = mysqli_query($cnms, $query);
                while ($row2= mysqli_fetch_array($tampil2)) {
                    $pprodid=$row2['iprodid'];
                    $pnmprod=$row2['nmprod'];
                    
                    $pjml1=$row2['A'];
                    $pjml2=$row2['B'];
                    $pjml3=$row2['C'];
                    $pjml4=$row2['D'];
                    $pjml5=$row2['E'];
                    $pjml6=$row2['F'];
                    $pjml7=$row2['G'];
                    $pjml8=$row2['H'];
                    $pjml9=$row2['I'];
                    $pjml10=$row2['J'];
                    $pjml11=$row2['K'];
                    $pjml12=$row2['L'];
                    
                    $pjml1A=$row2['AA'];
                    $pjml2A=$row2['BA'];
                    $pjml3A=$row2['CA'];
                    $pjml4A=$row2['DA'];
                    $pjml5A=$row2['EA'];
                    $pjml6A=$row2['FA'];
                    $pjml7A=$row2['GA'];
                    $pjml8A=$row2['HA'];
                    $pjml9A=$row2['IA'];
                    $pjml10A=$row2['JA'];
                    $pjml11A=$row2['KA'];
                    $pjml12A=$row2['LA'];
                    
                    $pjml1qty=$row2['totqty'];
                    $pjml1value=$row2['totvalue'];
                    
                    $pjmlaverg=ROUND($row2['tavg'],2);
                    
                    
                    //GRAND TOTAL
                        $itot_pntotqty=(DOUBLE)$itot_pntotqty+(DOUBLE)$pjml1qty;
                        $itot_gpntotqty=(DOUBLE)$itot_gpntotqty+(DOUBLE)$pjml1value;

                        $itot_ptotjml1=(DOUBLE)$itot_ptotjml1+(DOUBLE)$pjml1;
                        $itot_ptotjml2=(DOUBLE)$itot_ptotjml2+(DOUBLE)$pjml2;
                        $itot_ptotjml3=(DOUBLE)$itot_ptotjml3+(DOUBLE)$pjml3;
                        $itot_ptotjml4=(DOUBLE)$itot_ptotjml4+(DOUBLE)$pjml4;
                        $itot_ptotjml5=(DOUBLE)$itot_ptotjml5+(DOUBLE)$pjml5;
                        $itot_ptotjml6=(DOUBLE)$itot_ptotjml6+(DOUBLE)$pjml6;
                        $itot_ptotjml7=(DOUBLE)$itot_ptotjml7+(DOUBLE)$pjml7;
                        $itot_ptotjml8=(DOUBLE)$itot_ptotjml8+(DOUBLE)$pjml8;
                        $itot_ptotjml9=(DOUBLE)$itot_ptotjml9+(DOUBLE)$pjml9;
                        $itot_ptotjml10=(DOUBLE)$itot_ptotjml10+(DOUBLE)$pjml10;
                        $itot_ptotjml11=(DOUBLE)$itot_ptotjml11+(DOUBLE)$pjml11;
                        $itot_ptotjml12=(DOUBLE)$itot_ptotjml12+(DOUBLE)$pjml12;

                        $itot_gptotjml1=(DOUBLE)$itot_gptotjml1+(DOUBLE)$pjml1A;
                        $itot_gptotjml2=(DOUBLE)$itot_gptotjml2+(DOUBLE)$pjml2A;
                        $itot_gptotjml3=(DOUBLE)$itot_gptotjml3+(DOUBLE)$pjml3A;
                        $itot_gptotjml4=(DOUBLE)$itot_gptotjml4+(DOUBLE)$pjml4A;
                        $itot_gptotjml5=(DOUBLE)$itot_gptotjml5+(DOUBLE)$pjml5A;
                        $itot_gptotjml6=(DOUBLE)$itot_gptotjml6+(DOUBLE)$pjml6A;
                        $itot_gptotjml7=(DOUBLE)$itot_gptotjml7+(DOUBLE)$pjml7A;
                        $itot_gptotjml8=(DOUBLE)$itot_gptotjml8+(DOUBLE)$pjml8A;
                        $itot_gptotjml9=(DOUBLE)$itot_gptotjml9+(DOUBLE)$pjml9A;
                        $itot_gptotjml10=(DOUBLE)$itot_gptotjml10+(DOUBLE)$pjml10A;
                        $itot_gptotjml11=(DOUBLE)$itot_gptotjml11+(DOUBLE)$pjml11A;
                        $itot_gptotjml12=(DOUBLE)$itot_gptotjml12+(DOUBLE)$pjml12A;
                    //END GRAND TOTAL
                    
                    
                    $pntotqty=(DOUBLE)$pntotqty+(DOUBLE)$pjml1qty;
                    $gpntotqty=(DOUBLE)$gpntotqty+(DOUBLE)$pjml1value;
                    
                    $ptotjml1=(DOUBLE)$ptotjml1+(DOUBLE)$pjml1;
                    $ptotjml2=(DOUBLE)$ptotjml2+(DOUBLE)$pjml2;
                    $ptotjml3=(DOUBLE)$ptotjml3+(DOUBLE)$pjml3;
                    $ptotjml4=(DOUBLE)$ptotjml4+(DOUBLE)$pjml4;
                    $ptotjml5=(DOUBLE)$ptotjml5+(DOUBLE)$pjml5;
                    $ptotjml6=(DOUBLE)$ptotjml6+(DOUBLE)$pjml6;
                    $ptotjml7=(DOUBLE)$ptotjml7+(DOUBLE)$pjml7;
                    $ptotjml8=(DOUBLE)$ptotjml8+(DOUBLE)$pjml8;
                    $ptotjml9=(DOUBLE)$ptotjml9+(DOUBLE)$pjml9;
                    $ptotjml10=(DOUBLE)$ptotjml10+(DOUBLE)$pjml10;
                    $ptotjml11=(DOUBLE)$ptotjml11+(DOUBLE)$pjml11;
                    $ptotjml12=(DOUBLE)$ptotjml12+(DOUBLE)$pjml12;
                    
                    $gptotjml1=(DOUBLE)$gptotjml1+(DOUBLE)$pjml1A;
                    $gptotjml2=(DOUBLE)$gptotjml2+(DOUBLE)$pjml2A;
                    $gptotjml3=(DOUBLE)$gptotjml3+(DOUBLE)$pjml3A;
                    $gptotjml4=(DOUBLE)$gptotjml4+(DOUBLE)$pjml4A;
                    $gptotjml5=(DOUBLE)$gptotjml5+(DOUBLE)$pjml5A;
                    $gptotjml6=(DOUBLE)$gptotjml6+(DOUBLE)$pjml6A;
                    $gptotjml7=(DOUBLE)$gptotjml7+(DOUBLE)$pjml7A;
                    $gptotjml8=(DOUBLE)$gptotjml8+(DOUBLE)$pjml8A;
                    $gptotjml9=(DOUBLE)$gptotjml9+(DOUBLE)$pjml9A;
                    $gptotjml10=(DOUBLE)$gptotjml10+(DOUBLE)$pjml10A;
                    $gptotjml11=(DOUBLE)$gptotjml11+(DOUBLE)$pjml11A;
                    $gptotjml12=(DOUBLE)$gptotjml12+(DOUBLE)$pjml12A;
                    
                    $pjml1value=(double)$pjml1value/1000;
                    $pjml1qty=number_format($pjml1qty,0,",",",");
                    $pjml1value=number_format($pjml1value,0,",",",");
                    
                    
                    $pjml1=number_format($pjml1,0,",",",");
                    $pjml2=number_format($pjml2,0,",",",");
                    $pjml3=number_format($pjml3,0,",",",");
                    $pjml4=number_format($pjml4,0,",",",");
                    $pjml5=number_format($pjml5,0,",",",");
                    $pjml6=number_format($pjml6,0,",",",");
                    $pjml7=number_format($pjml7,0,",",",");
                    $pjml8=number_format($pjml8,0,",",",");
                    $pjml9=number_format($pjml9,0,",",",");
                    $pjml10=number_format($pjml10,0,",",",");
                    $pjml11=number_format($pjml11,0,",",",");
                    $pjml12=number_format($pjml12,0,",",",");

                    
                    if ($pjml1==0) $pjml1="";
                    if ($pjml2==0) $pjml2="";
                    if ($pjml3==0) $pjml3="";
                    if ($pjml4==0) $pjml4="";
                    if ($pjml5==0) $pjml5="";
                    if ($pjml6==0) $pjml6="";
                    if ($pjml7==0) $pjml7="";
                    if ($pjml8==0) $pjml8="";
                    if ($pjml9==0) $pjml9="";
                    if ($pjml10==0) $pjml10="";
                    if ($pjml11==0) $pjml11="";
                    if ($pjml12==0) $pjml12="";
                    
                    echo "<tr>";
                    echo "<td nowrap>$pnmprod</td>";
                    
                    echo "<td nowrap>$pjml1</td>";
                    echo "<td nowrap>$pjml2</td>";
                    echo "<td nowrap>$pjml3</td>";
                    echo "<td nowrap>$pjml4</td>";
                    echo "<td nowrap>$pjml5</td>";
                    echo "<td nowrap>$pjml6</td>";
                    echo "<td nowrap>$pjml7</td>";
                    echo "<td nowrap>$pjml8</td>";
                    echo "<td nowrap>$pjml9</td>";
                    echo "<td nowrap>$pjml10</td>";
                    echo "<td nowrap>$pjml11</td>";
                    echo "<td nowrap>$pjml12</td>";
                    
                    echo "<td nowrap>$pjml1qty</td>";
                    echo "<td nowrap>$pjml1value</td>";
                    echo "<td nowrap>$pjmlaverg</td>";
                    echo "</tr>";
                    
                }
                
                
                $pavgqty=(DOUBLE)$pntotqty/12;
                $pavgqty=ROUND($pavgqty,2);
                
                $gpavgqty=(DOUBLE)$gpntotqty/12/1000;
                //$gpavgqty=ROUND($gpavgqty,0);
                
                
                
                $pntotqty=number_format($pntotqty,0,",",",");
                $gpntotqty=number_format($gpntotqty,0,",",",");
                
                $gpavgqty=number_format($gpavgqty,0,",",",");
                
                
                //average
                $av_jml1=(DOUBLE)$ptotjml1/12;
                $av_jml2=(DOUBLE)$ptotjml2/12;
                $av_jml3=(DOUBLE)$ptotjml3/12;
                $av_jml4=(DOUBLE)$ptotjml4/12;
                $av_jml5=(DOUBLE)$ptotjml5/12;
                $av_jml6=(DOUBLE)$ptotjml6/12;
                $av_jml7=(DOUBLE)$ptotjml7/12;
                $av_jml8=(DOUBLE)$ptotjml8/12;
                $av_jml9=(DOUBLE)$ptotjml9/12;
                $av_jml10=(DOUBLE)$ptotjml10/12;
                $av_jml11=(DOUBLE)$ptotjml11/12;
                $av_jml12=(DOUBLE)$ptotjml12/12;
                
                $av_jml1=ROUND($av_jml1,2);
                $av_jml2=ROUND($av_jml2,2);
                $av_jml3=ROUND($av_jml3,2);
                $av_jml4=ROUND($av_jml4,2);
                $av_jml5=ROUND($av_jml5,2);
                $av_jml6=ROUND($av_jml6,2);
                $av_jml7=ROUND($av_jml7,2);
                $av_jml8=ROUND($av_jml8,2);
                $av_jml9=ROUND($av_jml9,2);
                $av_jml10=ROUND($av_jml10,2);
                $av_jml11=ROUND($av_jml11,2);
                $av_jml12=ROUND($av_jml12,2);
                
                //end average
                
                
                $ptotjml1=number_format($ptotjml1,0,",",",");
                $ptotjml2=number_format($ptotjml2,0,",",",");
                $ptotjml3=number_format($ptotjml3,0,",",",");
                $ptotjml4=number_format($ptotjml4,0,",",",");
                $ptotjml5=number_format($ptotjml5,0,",",",");
                $ptotjml6=number_format($ptotjml6,0,",",",");
                $ptotjml7=number_format($ptotjml7,0,",",",");
                $ptotjml8=number_format($ptotjml8,0,",",",");
                $ptotjml9=number_format($ptotjml9,0,",",",");
                $ptotjml10=number_format($ptotjml10,0,",",",");
                $ptotjml11=number_format($ptotjml11,0,",",",");
                $ptotjml12=number_format($ptotjml12,0,",",",");
                
                $gptotjml1=number_format($gptotjml1,0,",",",");
                $gptotjml2=number_format($gptotjml2,0,",",",");
                $gptotjml3=number_format($gptotjml3,0,",",",");
                $gptotjml4=number_format($gptotjml4,0,",",",");
                $gptotjml5=number_format($gptotjml5,0,",",",");
                $gptotjml6=number_format($gptotjml6,0,",",",");
                $gptotjml7=number_format($gptotjml7,0,",",",");
                $gptotjml8=number_format($gptotjml8,0,",",",");
                $gptotjml9=number_format($gptotjml9,0,",",",");
                $gptotjml10=number_format($gptotjml10,0,",",",");
                $gptotjml11=number_format($gptotjml11,0,",",",");
                $gptotjml12=number_format($gptotjml12,0,",",",");
                
                if ($ptotjml1==0) $ptotjml1="";
                if ($ptotjml2==0) $ptotjml2="";
                if ($ptotjml3==0) $ptotjml3="";
                if ($ptotjml4==0) $ptotjml4="";
                if ($ptotjml5==0) $ptotjml5="";
                if ($ptotjml6==0) $ptotjml6="";
                if ($ptotjml7==0) $ptotjml7="";
                if ($ptotjml8==0) $ptotjml8="";
                if ($ptotjml9==0) $ptotjml9="";
                if ($ptotjml10==0) $ptotjml10="";
                if ($ptotjml11==0) $ptotjml11="";
                if ($ptotjml12==0) $ptotjml12="";
                
                if ($gptotjml1==0) $gptotjml1="";
                if ($gptotjml2==0) $gptotjml2="";
                if ($gptotjml3==0) $gptotjml3="";
                if ($gptotjml4==0) $gptotjml4="";
                if ($gptotjml5==0) $gptotjml5="";
                if ($gptotjml6==0) $gptotjml6="";
                if ($gptotjml7==0) $gptotjml7="";
                if ($gptotjml8==0) $gptotjml8="";
                if ($gptotjml9==0) $gptotjml9="";
                if ($gptotjml10==0) $gptotjml10="";
                if ($gptotjml11==0) $gptotjml11="";
                if ($gptotjml12==0) $gptotjml12="";
                
                if ($av_jml1==0) $av_jml1="";
                if ($av_jml2==0) $av_jml2="";
                if ($av_jml3==0) $av_jml3="";
                if ($av_jml4==0) $av_jml4="";
                if ($av_jml5==0) $av_jml5="";
                if ($av_jml6==0) $av_jml6="";
                if ($av_jml7==0) $av_jml7="";
                if ($av_jml8==0) $av_jml8="";
                if ($av_jml9==0) $av_jml9="";
                if ($av_jml10==0) $av_jml10="";
                if ($av_jml11==0) $av_jml11="";
                if ($av_jml12==0) $av_jml12="";
                
                
                echo "<tr>";
                echo "<td nowrap align='left'><b>$pdivprodid Total Qty : </b></td>";//$pdivprodid

                echo "<td nowrap><b>$ptotjml1</b></td>";
                echo "<td nowrap><b>$ptotjml2</b></td>";
                echo "<td nowrap><b>$ptotjml3</b></td>";
                echo "<td nowrap><b>$ptotjml4</b></td>";
                echo "<td nowrap><b>$ptotjml5</b></td>";
                echo "<td nowrap><b>$ptotjml6</b></td>";
                echo "<td nowrap><b>$ptotjml7</b></td>";
                echo "<td nowrap><b>$ptotjml8</b></td>";
                echo "<td nowrap><b>$ptotjml9</b></td>";
                echo "<td nowrap><b>$ptotjml10</b></td>";
                echo "<td nowrap><b>$ptotjml11</b></td>";
                echo "<td nowrap><b>$ptotjml12</b></td>";

                echo "<td nowrap><b>$pntotqty</b></td>";
                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b>$pavgqty</b></td>";
                echo "</tr>";
                
                echo "<tr>";
                echo "<td nowrap align='left'><b>$pdivprodid Total Nilai : </b></td>";//$pdivprodid

                echo "<td nowrap><b>$gptotjml1</b></td>";
                echo "<td nowrap><b>$gptotjml2</b></td>";
                echo "<td nowrap><b>$gptotjml3</b></td>";
                echo "<td nowrap><b>$gptotjml4</b></td>";
                echo "<td nowrap><b>$gptotjml5</b></td>";
                echo "<td nowrap><b>$gptotjml6</b></td>";
                echo "<td nowrap><b>$gptotjml7</b></td>";
                echo "<td nowrap><b>$gptotjml8</b></td>";
                echo "<td nowrap><b>$gptotjml9</b></td>";
                echo "<td nowrap><b>$gptotjml10</b></td>";
                echo "<td nowrap><b>$gptotjml11</b></td>";
                echo "<td nowrap><b>$gptotjml12</b></td>";

                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b>$gpntotqty</b></td>";
                echo "<td nowrap><b>$gpavgqty</b></td>";
                echo "</tr>";
                
                echo "<tr>";
                echo "<td nowrap align='left'><b>$pdivprodid Average Qty : </b></td>";//$pdivprodid

                echo "<td nowrap><b>$av_jml1</b></td>";
                echo "<td nowrap><b>$av_jml2</b></td>";
                echo "<td nowrap><b>$av_jml3</b></td>";
                echo "<td nowrap><b>$av_jml4</b></td>";
                echo "<td nowrap><b>$av_jml5</b></td>";
                echo "<td nowrap><b>$av_jml6</b></td>";
                echo "<td nowrap><b>$av_jml7</b></td>";
                echo "<td nowrap><b>$av_jml8</b></td>";
                echo "<td nowrap><b>$av_jml9</b></td>";
                echo "<td nowrap><b>$av_jml10</b></td>";
                echo "<td nowrap><b>$av_jml11</b></td>";
                echo "<td nowrap><b>$av_jml12</b></td>";

                echo "<td nowrap><b>$pavgqty</b></td>";
                echo "<td nowrap><b>$gpavgqty</b></td>";
                echo "<td nowrap><b></b></td>";
                echo "</tr>";
                    
                
            }
            
            
            
            
            if ($no>1) {
                echo "<tr>";
                echo "<td nowrap align='left'><b>&nbsp;</b></td>";//$pdivprodid

                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b></b></td>";

                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b></b></td>";
                echo "</tr>";
                
                
                
                
                $itot_pavgqty=(DOUBLE)$itot_pntotqty/12;
                $itot_pavgqty=ROUND($itot_pavgqty,2);
                
                $itot_gpavgqty=(DOUBLE)$itot_gpntotqty/12/1000;
                //$itot_gpavgqty=ROUND($itot_gpavgqty,0);
                
                
                
                $itot_pntotqty=number_format($itot_pntotqty,0,",",",");
                $itot_gpntotqty=number_format($itot_gpntotqty,0,",",",");
                
                $itot_gpavgqty=number_format($itot_gpavgqty,0,",",",");
                
                
                //average
                $itot_av_jml1=(DOUBLE)$itot_ptotjml1/12;
                $itot_av_jml2=(DOUBLE)$itot_ptotjml2/12;
                $itot_av_jml3=(DOUBLE)$itot_ptotjml3/12;
                $itot_av_jml4=(DOUBLE)$itot_ptotjml4/12;
                $itot_av_jml5=(DOUBLE)$itot_ptotjml5/12;
                $itot_av_jml6=(DOUBLE)$itot_ptotjml6/12;
                $itot_av_jml7=(DOUBLE)$itot_ptotjml7/12;
                $itot_av_jml8=(DOUBLE)$itot_ptotjml8/12;
                $itot_av_jml9=(DOUBLE)$itot_ptotjml9/12;
                $itot_av_jml10=(DOUBLE)$itot_ptotjml10/12;
                $itot_av_jml11=(DOUBLE)$itot_ptotjml11/12;
                $itot_av_jml12=(DOUBLE)$itot_ptotjml12/12;
                
                $itot_av_jml1=ROUND($itot_av_jml1,2);
                $itot_av_jml2=ROUND($itot_av_jml2,2);
                $itot_av_jml3=ROUND($itot_av_jml3,2);
                $itot_av_jml4=ROUND($itot_av_jml4,2);
                $itot_av_jml5=ROUND($itot_av_jml5,2);
                $itot_av_jml6=ROUND($itot_av_jml6,2);
                $itot_av_jml7=ROUND($itot_av_jml7,2);
                $itot_av_jml8=ROUND($itot_av_jml8,2);
                $itot_av_jml9=ROUND($itot_av_jml9,2);
                $itot_av_jml10=ROUND($itot_av_jml10,2);
                $itot_av_jml11=ROUND($itot_av_jml11,2);
                $itot_av_jml12=ROUND($itot_av_jml12,2);
                
                //end average
                
                
                $itot_ptotjml1=number_format($itot_ptotjml1,0,",",",");
                $itot_ptotjml2=number_format($itot_ptotjml2,0,",",",");
                $itot_ptotjml3=number_format($itot_ptotjml3,0,",",",");
                $itot_ptotjml4=number_format($itot_ptotjml4,0,",",",");
                $itot_ptotjml5=number_format($itot_ptotjml5,0,",",",");
                $itot_ptotjml6=number_format($itot_ptotjml6,0,",",",");
                $itot_ptotjml7=number_format($itot_ptotjml7,0,",",",");
                $itot_ptotjml8=number_format($itot_ptotjml8,0,",",",");
                $itot_ptotjml9=number_format($itot_ptotjml9,0,",",",");
                $itot_ptotjml10=number_format($itot_ptotjml10,0,",",",");
                $itot_ptotjml11=number_format($itot_ptotjml11,0,",",",");
                $itot_ptotjml12=number_format($itot_ptotjml12,0,",",",");
                
                $itot_gptotjml1=number_format($itot_gptotjml1,0,",",",");
                $itot_gptotjml2=number_format($itot_gptotjml2,0,",",",");
                $itot_gptotjml3=number_format($itot_gptotjml3,0,",",",");
                $itot_gptotjml4=number_format($itot_gptotjml4,0,",",",");
                $itot_gptotjml5=number_format($itot_gptotjml5,0,",",",");
                $itot_gptotjml6=number_format($itot_gptotjml6,0,",",",");
                $itot_gptotjml7=number_format($itot_gptotjml7,0,",",",");
                $itot_gptotjml8=number_format($itot_gptotjml8,0,",",",");
                $itot_gptotjml9=number_format($itot_gptotjml9,0,",",",");
                $itot_gptotjml10=number_format($itot_gptotjml10,0,",",",");
                $itot_gptotjml11=number_format($itot_gptotjml11,0,",",",");
                $itot_gptotjml12=number_format($itot_gptotjml12,0,",",",");
                
                if ($itot_ptotjml1==0) $itot_ptotjml1="";
                if ($itot_ptotjml2==0) $itot_ptotjml2="";
                if ($itot_ptotjml3==0) $itot_ptotjml3="";
                if ($itot_ptotjml4==0) $itot_ptotjml4="";
                if ($itot_ptotjml5==0) $itot_ptotjml5="";
                if ($itot_ptotjml6==0) $itot_ptotjml6="";
                if ($itot_ptotjml7==0) $itot_ptotjml7="";
                if ($itot_ptotjml8==0) $itot_ptotjml8="";
                if ($itot_ptotjml9==0) $itot_ptotjml9="";
                if ($itot_ptotjml10==0) $itot_ptotjml10="";
                if ($itot_ptotjml11==0) $itot_ptotjml11="";
                if ($itot_ptotjml12==0) $itot_ptotjml12="";
                
                if ($itot_gptotjml1==0) $itot_gptotjml1="";
                if ($itot_gptotjml2==0) $itot_gptotjml2="";
                if ($itot_gptotjml3==0) $itot_gptotjml3="";
                if ($itot_gptotjml4==0) $itot_gptotjml4="";
                if ($itot_gptotjml5==0) $itot_gptotjml5="";
                if ($itot_gptotjml6==0) $itot_gptotjml6="";
                if ($itot_gptotjml7==0) $itot_gptotjml7="";
                if ($itot_gptotjml8==0) $itot_gptotjml8="";
                if ($itot_gptotjml9==0) $itot_gptotjml9="";
                if ($itot_gptotjml10==0) $itot_gptotjml10="";
                if ($itot_gptotjml11==0) $itot_gptotjml11="";
                if ($itot_gptotjml12==0) $itot_gptotjml12="";
                
                if ($itot_av_jml1==0) $itot_av_jml1="";
                if ($itot_av_jml2==0) $itot_av_jml2="";
                if ($itot_av_jml3==0) $itot_av_jml3="";
                if ($itot_av_jml4==0) $itot_av_jml4="";
                if ($itot_av_jml5==0) $itot_av_jml5="";
                if ($itot_av_jml6==0) $itot_av_jml6="";
                if ($itot_av_jml7==0) $itot_av_jml7="";
                if ($itot_av_jml8==0) $itot_av_jml8="";
                if ($itot_av_jml9==0) $itot_av_jml9="";
                if ($itot_av_jml10==0) $itot_av_jml10="";
                if ($itot_av_jml11==0) $itot_av_jml11="";
                if ($itot_av_jml12==0) $itot_av_jml12="";
                
                
                
                echo "<tr>";
                echo "<td nowrap align='left'><b>Total Qty : </b></td>";//$pdivprodid

                echo "<td nowrap><b>$itot_ptotjml1</b></td>";
                echo "<td nowrap><b>$itot_ptotjml2</b></td>";
                echo "<td nowrap><b>$itot_ptotjml3</b></td>";
                echo "<td nowrap><b>$itot_ptotjml4</b></td>";
                echo "<td nowrap><b>$itot_ptotjml5</b></td>";
                echo "<td nowrap><b>$itot_ptotjml6</b></td>";
                echo "<td nowrap><b>$itot_ptotjml7</b></td>";
                echo "<td nowrap><b>$itot_ptotjml8</b></td>";
                echo "<td nowrap><b>$itot_ptotjml9</b></td>";
                echo "<td nowrap><b>$itot_ptotjml10</b></td>";
                echo "<td nowrap><b>$itot_ptotjml11</b></td>";
                echo "<td nowrap><b>$itot_ptotjml12</b></td>";

                echo "<td nowrap><b>$itot_pntotqty</b></td>";
                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b>$itot_pavgqty</b></td>";
                echo "</tr>";
                
                
                echo "<tr>";
                echo "<td nowrap align='left'><b>Total Nilai : </b></td>";//$pdivprodid

                echo "<td nowrap><b>$itot_gptotjml1</b></td>";
                echo "<td nowrap><b>$itot_gptotjml2</b></td>";
                echo "<td nowrap><b>$itot_gptotjml3</b></td>";
                echo "<td nowrap><b>$itot_gptotjml4</b></td>";
                echo "<td nowrap><b>$itot_gptotjml5</b></td>";
                echo "<td nowrap><b>$itot_gptotjml6</b></td>";
                echo "<td nowrap><b>$itot_gptotjml7</b></td>";
                echo "<td nowrap><b>$itot_gptotjml8</b></td>";
                echo "<td nowrap><b>$itot_gptotjml9</b></td>";
                echo "<td nowrap><b>$itot_gptotjml10</b></td>";
                echo "<td nowrap><b>$itot_gptotjml11</b></td>";
                echo "<td nowrap><b>$itot_gptotjml12</b></td>";

                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b>$itot_gpntotqty</b></td>";
                echo "<td nowrap><b>$itot_gpavgqty</b></td>";
                echo "</tr>";
                
                
                
                echo "<tr>";
                echo "<td nowrap align='left'><b>Average Qty : </b></td>";//$pdivprodid

                echo "<td nowrap><b>$itot_av_jml1</b></td>";
                echo "<td nowrap><b>$itot_av_jml2</b></td>";
                echo "<td nowrap><b>$itot_av_jml3</b></td>";
                echo "<td nowrap><b>$itot_av_jml4</b></td>";
                echo "<td nowrap><b>$itot_av_jml5</b></td>";
                echo "<td nowrap><b>$itot_av_jml6</b></td>";
                echo "<td nowrap><b>$itot_av_jml7</b></td>";
                echo "<td nowrap><b>$itot_av_jml8</b></td>";
                echo "<td nowrap><b>$itot_av_jml9</b></td>";
                echo "<td nowrap><b>$itot_av_jml10</b></td>";
                echo "<td nowrap><b>$itot_av_jml11</b></td>";
                echo "<td nowrap><b>$itot_av_jml12</b></td>";

                echo "<td nowrap><b>$itot_pavgqty</b></td>";
                echo "<td nowrap><b>$itot_gpavgqty</b></td>";
                echo "<td nowrap><b></b></td>";
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
                        { className: "text-right", "targets": [1, 2, 3,4,5,6,7,8,9,10,11,12,13,14,15] },//right
                        { className: "text-nowrap", "targets": [0, 1, 2, 3,4,5,6,7,8,9,10,11,12,13,14,15] }//nowrap

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