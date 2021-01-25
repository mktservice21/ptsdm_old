<?php
    /*
     FILE LAMA mrosls.php
     */

    //ini_set('display_errors', '0');
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    //ini_set('max_execution_time', 0);
    
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
    header("Content-Disposition: attachment; filename=REPORT SALES PER OUTLET.xls");
}

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];
    
$tgl01=$_POST['e_bulan01'];
$tgl02=$_POST['e_bulan02'];
$pcab=$_POST['cb_cabang'];
$parea=$_POST['cb_area'];
$pdivisi=$_POST['cb_divisi'];
$pprod=$_POST['cb_prod'];
$prpttipe=$_POST['rb_rpttipe'];

$ptxtcab=$_POST['txt_cabang'];
$ptxtcabarea=$_POST['txt_cabarea'];

$pbln1=date("Y-m-d", strtotime($tgl01));
$pbln2=date("Y-m-d", strtotime($tgl02));


$pperiode1=date("d F Y", strtotime($tgl01));
$pperiode2=date("d F Y", strtotime($tgl02));


$pprodoth = "";
if (isset($_POST['chkboth'])) $pprodoth=$_POST['chkboth'];


$pviewdate=date("d/m/Y H:i:s");

$milliseconds = round(microtime(true) * 1000);
$now=date("mdYhis");
$tmp01 ="dbtemp.TEMPSLSOTL01_".$puser."_$now$milliseconds";
$tmp02 ="dbtemp.TEMPSLSOTL02_".$puser."_$now$milliseconds";
$tmp03 ="dbtemp.TEMPSLSOTL03_".$puser."_$now$milliseconds";


include("config/koneksimysqli_ms.php");

$query = "select nama from sls.icabang where icabangid='$pcab'";
$tampil= mysqli_query($cnms, $query);
$rs= mysqli_fetch_array($tampil);
$pnamacabang=$rs['nama'];

$pnamaarea="";
if (!empty($parea)) {
    $query = "select nama from sls.iarea where icabangid='$pcab' AND areaid='$parea'";
    $tampil= mysqli_query($cnms, $query);
    $rs= mysqli_fetch_array($tampil);
    $pnamaarea=$rs['nama'];
}

$filterarea="";
if (!empty($parea)) $filterarea=" AND areaid='$parea' ";

$filterdivisi="";
if (!empty($pdivisi)) $filterdivisi=" AND divprodid='$pdivisi' ";

$filterproduk="";
if (!empty($pprod)) $filterproduk=" AND iprodid='$pprod' ";

              
if (empty($pdivisi)) {
    $query_cab="";
    if ($pmyjabatanid=="15") {
        $query_cab = "select distinct divisiid FROM sls.imr0 WHERE karyawanid='$pmyidcard'";
    }elseif ($pmyjabatanid=="10" OR $pmyjabatanid=="18") {
        $query_cab = "select distinct divisiid FROM sls.ispv0 WHERE karyawanid='$pmyidcard'";
    }
    
    if (!empty($query_cab)) {
        $piddivi_="";
        $filiddivisipil="";
        $tampil= mysqli_query($cnms, $query_cab);
        while ($rs= mysqli_fetch_array($tampil)) {
            $piddivi_=$rs['divisiid'];
            if (strpos($filiddivisipil, $piddivi_)==false) $filiddivisipil .="'".$piddivi_."',";
            
        }
        
        if (!empty($filiddivisipil)) {
            $filiddivisipil="(".substr($filiddivisipil, 0, -1).")";
            $filterdivisi=" AND divprodid IN $filiddivisipil ";
        }
        
    }
}

if (empty($parea)) {
    if ($pmyjabatanid=="15") {
        $filterarea .=" AND CONCAT(IFNULL(icabangid,''),IFNULL(areaid,'')) IN 
          (SELECT DISTINCT CONCAT(IFNULL(icabangid,''),IFNULL(areaid,'')) FROM sls.imr0 WHERE karyawanid='$pmyidcard') ";
    }elseif ($pmyjabatanid=="10" OR $pmyjabatanid=="18") {
        
        //$filterarea .=" AND CONCAT(IFNULL(icabangid,''),IFNULL(areaid,'')) IN (SELECT DISTINCT CONCAT(IFNULL(icabangid,''),IFNULL(areaid,'')) FROM sls.ispv0 WHERE karyawanid='$pmyidcard') ";

        $ppilihbedaarea=false;
        $pfiltercabarea="";
        if ($pmyjabatanid=="10" OR $pmyjabatanid=="18") {
            if (!empty($ptxtcabarea)) $pfiltercabarea=" (".substr($ptxtcabarea, 0, -1).")";
            if (strpos($ptxtcab, $pcab)==true) {
                $ppilihbedaarea=true;
            }
        }
        if ($ppilihbedaarea==false) {
            $filterarea .=" AND CONCAT(IFNULL(icabangid,''),IFNULL(areaid,'')) IN (SELECT DISTINCT CONCAT(IFNULL(icabangid,''),IFNULL(areaid,'')) FROM sls.iarea WHERE icabangid='$pcab' AND IFNULL(aktif,'')<>'Y') ";
        }else{
            if (!empty($pfiltercabarea)) $filterarea = " AND CONCAT(IFNULL(icabangid,''),IFNULL(areaid,'')) IN $pfiltercabarea ";
        }
        
        
    }
}

$query = "select * from sls.mr_sales2 WHERE tgljual BETWEEN '$pbln1' AND '$pbln2' "
        . " AND icabangid='$pcab' $filterarea $filterdivisi $filterproduk ";
if ($pprodoth=="Y") {
}else{
    $query .= " AND iprodid NOT IN (select IFNULL(iprodid,'') iprodid from sls.othproduk WHERE divprodid='PEACO')";
}
//echo $query;
$query = "CREATE TEMPORARY TABLE $tmp01 ($query)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

$query = "select a.*, IFNULL(b.nama,'') as custnm, b.isektorid, c.nama as prodnm from $tmp01 a "
        . " LEFT JOIN sls.icust b on a.icabangid=b.icabangid AND a.icustid=b.icustid and a.areaid=b.areaid "
        . " LEFT JOIN sls.iproduk c on a.iprodid=c.iprodid";
$query = "CREATE TEMPORARY TABLE $tmp02 ($query)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

$query = "UPDATE $tmp02 SET isektorid='' WHERE IFNULL(isektorid,'')=''";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

$query = "select tgljual, fakturid, initial, ecabangid, isektorid, icabangid, areaid, icustid, custnm, iprodid, prodnm, sum(qty) qty, sum(hna*qty) as tvalue  
        from $tmp02 
        group by 1,2,3,4,5,6,7,8,9,10,11";
$query = "CREATE TEMPORARY TABLE $tmp03 ($query)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

?>

<HTML>
<HEAD>
    <title>Report Sales Per Outlet</title>
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
                    echo "<tr><td colspan=5 width='150px'><b>Report Sales Per Outlet</b></td></tr>";
                    echo "<tr><td colspan=5 width='150px'><b>Periode : $pperiode1 s/d. $pperiode2</b></td></tr>";
                    echo "<tr><td colspan=5 width='150px'><b>Cabang : $pnamacabang</b></td></tr>";
                    if (!empty($pnamaarea)) {
                        echo "<tr><td colspan=5 width='150px'><b>Area : $pnamaarea</b></td></tr>";
                    }
                    
                    if ($pmyjabatanid=="10" OR $pmyjabatanid=="18" OR $pmyjabatanid=="15") {
                        echo "<tr><td colspan=5 width='150px'><b>Karyawan : $pmynamlengkap</b></td></tr>";
                    }
                    if ($pprodoth=="Y") {
                        echo "<tr><td colspan=5 width='150px'>Include Produk Other Peacock</td></tr>";
                    }else{
                        echo "<tr><td colspan=5 width='150px'>Tanpa Produk Other Peacock</td></tr>";
                    }
                    echo "<tr><td colspan=5 width='150px'>view date : $pviewdate</td></tr>";
                }else{
                    echo "<tr><td width='150px'><b><h3>Report Sales Per Outlet</h3></b></td></tr>";
                    echo "<tr><td width='150px'><b>Periode : $pperiode1 s/d. $pperiode2</b></td></tr>";
                    echo "<tr><td width='150px'><b>Cabang : $pnamacabang</b></td></tr>";
                    if (!empty($pnamaarea)) {
                        echo "<tr><td width='150px'><b>Area : $pnamaarea</b></td></tr>";
                    }
                    
                    if ($pmyjabatanid=="10" OR $pmyjabatanid=="18" OR $pmyjabatanid=="15") {
                        echo "<tr><td width='150px'><b>Karyawan : $pmynamlengkap</b></td></tr>";
                    }
                    if ($pprodoth=="Y") {
                        echo "<tr><td width='150px'>Include Produk Other Peacock</td></tr>";
                    }else{
                        echo "<tr><td width='150px'>Tanpa Produk Other Peacock</td></tr>";
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
    if ($prpttipe=="D") {
    ?>
    
        <table id='datatable2' class='table table-striped table-bordered' width="100%" border="1px solid black">
            <thead>
                <tr>
                <th align="center">No</th>
                <th align="center">Nama Outlet / Produk</th>
                <th align="center">Distributor</th>
                <th align="center">Cabang</th>
                <th align="center">No. Faktur</th>
                <th align="center">Tanggal</th>
                <th align="center">Qty</th>
                <th align="center">Value</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $ptotqty=0;
                $ptotvalue=0;
                $no=1;
                $query = "select distinct isektorid, icabangid as icabangid, areaid as areaid, icustid, custnm FROM $tmp03 order by custnm";
                $tampil1= mysqli_query($cnms, $query);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $npidcust=$row1['icustid'];
                    $npnmcust=$row1['custnm'];
                    $npisektor=$row1['isektorid'];
					
					$npidcabid=$row1['icabangid'];
					$npidareaid=$row1['areaid'];
					
					$npidcab=(INT)$row1['icabangid'];
					$npidarea=(INT)$row1['areaid'];
					$npidcustpl=(INT)$row1['icustid'];
					
					
                    if ($npnmcust=="") $npnmcust = "(nama outlet belum ada)";
                    
                    $allowed_ = 1;
                    if ($pmyjabatanid==10 or $pmyjabatanid==18 or $pmyjabatanid==15) {
                        //mr spv koordinator
                        if ($npisektor==03) {
                            $allowed_ = 0;
                        }
                    }
                    if (!$allowed_) {
                        $npnmcust = "(nama outlet belum ada)";
                    }

                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap><b>$npnmcust</b> ($npidcab - $npidarea - <b>$npidcustpl</b>)</td>";
                    
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "</tr>";
                        
                    $no++;
                    $pnsubtotval=0;
                    $pnsubtotqty=0;
                    
                    $query = "select * FROM $tmp03 WHERE icabangid='$npidcabid' AND areaid='$npidareaid' AND icustid='$npidcust' AND isektorid='$npisektor' order by custnm, prodnm, initial, ecabangid";
                    $tampil2= mysqli_query($cnms, $query);
                    while ($row2= mysqli_fetch_array($tampil2)) {

                        $npidprod=$row2['iprodid'];
                        $npnmprod=$row2['prodnm'];
                        $npdistid=$row2['initial'];
                        $npecab=$row2['ecabangid'];
                        $npfakturid=$row2['fakturid'];
                        $nptgljual=$row2['tgljual'];
                        $npqty=$row2['qty'];
                        $ntvalue=$row2['tvalue'];

                        $pnsubtotval=(double)$pnsubtotval+(double)$ntvalue;
                        $pnsubtotqty=(double)$pnsubtotqty+(double)$npqty;
                        
                        $ptotqty=(double)$ptotqty+(double)$npqty;
                        $ptotvalue=(double)$ptotvalue+(double)$ntvalue;

                        $npqty=number_format($npqty,0,",",",");
                        $ntvalue=number_format($ntvalue,0,",",",");


                        echo "<tr>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap>$npnmprod</td>";
                        echo "<td nowrap>$npdistid</td>";
                        echo "<td nowrap>$npecab</td>";
                        echo "<td nowrap>$npfakturid</td>";
                        echo "<td nowrap>$nptgljual</td>";
                        echo "<td nowrap>$npqty</td>";
                        echo "<td nowrap>$ntvalue</td>";
                        echo "</tr>";

                    }
                    
                    $pnsubtotqty=number_format($pnsubtotqty,0,",",",");
                    $pnsubtotval=number_format($pnsubtotval,0,",",",");
                    
                    echo "<tr>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap><b>Total $npnmcust : </b></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap><b>$pnsubtotqty</b></td>";
                    echo "<td nowrap><b>$pnsubtotval</b></td>";
                    echo "</tr>";

                }

                $ptotqty=number_format($ptotqty,0,",",",");
                $ptotvalue=number_format($ptotvalue,0,",",",");

                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap><b>TOTAL : </b></td>";
                echo "<td nowrap><b>$ptotqty</b></td>";
                echo "<td nowrap><b>$ptotvalue</b></td>";
                echo "</tr>";

                ?>
            </tbody>
        </table>
    
    <?PHP
    }else{
    ?>
    
        <table id='datatable2' class='table table-striped table-bordered' width="100%" border="1px solid black">
            <thead>
                <tr>
                <th align="center">No</th>
                <th align="center">Nama Outlet / Produk</th>
                <th align="center">Qty</th>
                <th align="center">Value</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $ptotqty=0;
                $ptotvalue=0;
                $no=1;
                $query = "select distinct isektorid, icabangid as icabangid, areaid as areaid, icustid, custnm FROM $tmp03 order by custnm";
                $tampil1= mysqli_query($cnms, $query);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $npidcust=$row1['icustid'];
                    $npnmcust=$row1['custnm'];
                    $npisektor=$row1['isektorid'];
					
					$npidcabid=$row1['icabangid'];
					$npidareaid=$row1['areaid'];
					
					$npidcab=(INT)$row1['icabangid'];
					$npidarea=(INT)$row1['areaid'];
					$npidcustpl=(INT)$row1['icustid'];
					
                    /*
                    if ($npnmcust=="") $npnmcust = "(nama outlet belum ada)";

                    $allowed_ = 1;
                    if ($pmyjabatanid==10 or $pmyjabatanid==18 or $pmyjabatanid==15) {
                        //mr spv koordinator
                        if ($npisektor==03) {
                            $allowed_ = 0;
                        }
                    }
                    if (!$allowed_) {
                        $npnmcust = "(nama outlet belum ada)";
                    }
                    */
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap><b>$npnmcust</b> ($npidcab - $npidarea - <b>$npidcustpl</b>)</td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "</tr>";
                    $no++;
                    
                    $pnsubtotqty=0;
                    $pnsubtotval=0;
                    
                    $query = "select icustid, custnm, iprodid, prodnm, sum(qty) qty, sum(tvalue) tvalue "
                            . " FROM $tmp03 WHERE icabangid='$npidcabid' AND areaid='$npidareaid' AND icustid='$npidcust' AND isektorid='$npisektor' GROUP BY 1,2,3,4 order by custnm, prodnm";
                    $tampil2= mysqli_query($cnms, $query);
                    while ($row2= mysqli_fetch_array($tampil2)) {

                        $npidprod=$row2['iprodid'];
                        $npnmprod=$row2['prodnm'];
                        $npqty=$row2['qty'];
                        $npvalue=$row2['tvalue'];

                        $pnsubtotval=(double)$pnsubtotval+(double)$npvalue;
                        $pnsubtotqty=(double)$pnsubtotqty+(double)$npqty;
                        
                        $ptotqty=(double)$ptotqty+(double)$npqty;
                        $ptotvalue=(double)$ptotvalue+(double)$npvalue;

                        
                        $npqty=number_format($npqty,0,",",",");
                        $npvalue=number_format($npvalue,0,",",",");


                        echo "<tr>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap>$npnmprod</td>";
                        echo "<td nowrap>$npqty</td>";
                        echo "<td nowrap>$npvalue</td>";
                        echo "</tr>";

                    }
                    
                    $pnsubtotval=number_format($pnsubtotval,0,",",",");
                    $pnsubtotqty=number_format($pnsubtotqty,0,",",",");
                    echo "<tr>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap><b>Total $npnmcust : </b></td>";
                    echo "<td nowrap><b>$pnsubtotqty</b></td>";
                    echo "<td nowrap><b>$pnsubtotval</b></td>";
                    echo "</tr>";

                }

                $ptotqty=number_format($ptotqty,0,",",",");
                $ptotvalue=number_format($ptotvalue,0,",",",");

                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap><b>TOTAL : </b></td>";
                echo "<td nowrap><b>$ptotqty</b></td>";
                echo "<td nowrap><b>$ptotvalue</b></td>";
                echo "</tr>";

                ?>
            </tbody>
        </table>
    
    <?PHP
    }
    ?>
    
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
                        <?PHP if ($prpttipe=="D") { ?>
                            { className: "text-right", "targets": [6,7] },//right
                            { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,7] }//nowrap
                        <?PHP }else{ ?>
                            { className: "text-right", "targets": [2,3] },//right
                            { className: "text-nowrap", "targets": [0, 1, 2, 3] }//nowrap
                        <?PHP } ?>

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