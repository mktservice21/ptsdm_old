<?php
    //ini_set('display_errors', '0');
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

$pmyidcard=$_SESSION['IDCARD'];
$pmyjabatanid=$_SESSION['JABATANID'];
$pmynamlengkap=$_SESSION['NAMALENGKAP'];
$pidgroup=$_SESSION['GROUP'];

$ppilihrpt=$_GET['ket'];
if ($ppilihrpt=="excel") {
    // Fungsi header dengan mengirimkan raw data excel
    header("Content-type: application/vnd-ms-excel");
    // Mendefinisikan nama file ekspor "hasil-export.xls"
    header("Content-Disposition: attachment; filename=LAPORAN INCENTIVE.xls");
}

$pmodule=$_GET['module'];
$pact=$_GET['act'];
$pidmenu=$_GET['idmenu'];
$aksi="module/incentive/inc_incentivelap/simpan_approveinc";

$pviewdate=date("d/m/Y H:i:s");

$tgl01=$_POST['bulan'];
$pregion=$_POST['cb_region'];
$pnamaregion="All";
if ($pregion=="B") $pnamaregion="Barat";
elseif ($pregion=="T") $pnamaregion="Timur";

$pplblnapv = date("Y-m-01", strtotime($tgl01));
$pplbulan = date("Y-m", strtotime($tgl01));
$pbln1 = date("Y-m-01", strtotime($tgl01));
$pbln2 = date("Y-m-t", strtotime($tgl01));
$pbulan = date("F Y", strtotime($tgl01));


$milliseconds = round(microtime(true) * 1000);
$now=date("mdYhis");
$tmp01 ="dbtemp.tmprptinclp01_".$puser."_$now$milliseconds";
$tmp02 ="dbtemp.tmprptinclp02_".$puser."_$now$milliseconds";
$tmp03 ="dbtemp.tmprptinclp03_".$puser."_$now$milliseconds";

include("config/koneksimysqli_ms.php");

$query = "select karyawanid, `status` as sts, sys_time from ms.approve_insentif WHERE karyawanid='$pmyidcard' AND LEFT(bulan,7)='$pplbulan'";
$tampilp=mysqli_query($cnms, $query);
$rowp=mysqli_fetch_array($tampilp);
$pstsapv=$rowp['sts'];
$ptglapv=$rowp['sys_time'];
if ($pstsapv=="approve") {
}else{
    $pstsapv="";
    $ptglapv="";
}


$query = "CREATE TEMPORARY TABLE $tmp01 (icabangid VARCHAR(10), karyawanid VARCHAR(10), sts VARCHAR(5))";
mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

// MR
$query = "INSERT INTO $tmp01 (icabangid, karyawanid, sts)
    select distinct icabangid, mr, 'MR' as sts 
    from ms.penempatan_marketing where bulan Between '$pbln1' AND '$pbln2' 
    and ifnull(mr,'')<>'' ";
if ($pidgroup=="1" OR $pidgroup=="24") {
}else{
    if ($pmyjabatanid=="15") $query .= " AND mr='$pmyidcard' ";
    elseif ($pmyjabatanid=="10" OR $pmyjabatanid=="18") $query .= " AND am='$pmyidcard' ";
    elseif ($pmyjabatanid=="08") $query .= " AND dm='$pmyidcard' ";
    elseif ($pmyjabatanid=="20") $query .= " AND sm='$pmyidcard' ";
    else {
        $query .= " AND gsm='$pmyidcard' ";
    }
}
if (!empty($pregion)) $query .= " AND region='$pregion' ";
mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

// AM / SPV
$query = "INSERT INTO $tmp01 (icabangid, karyawanid, sts)
    select distinct icabangid, am, 'AM' as sts 
    from ms.penempatan_marketing where bulan Between '$pbln1' AND '$pbln2' 
    and ifnull(am,'')<>'' ";
if ($pidgroup=="1" OR $pidgroup=="24") {
}else{
    if ($pmyjabatanid=="15") $query .= " AND mr='$pmyidcard' ";
    elseif ($pmyjabatanid=="10" OR $pmyjabatanid=="18") $query .= " AND am='$pmyidcard' ";
    elseif ($pmyjabatanid=="08") $query .= " AND dm='$pmyidcard' ";
    elseif ($pmyjabatanid=="20") $query .= " AND sm='$pmyidcard' ";
    else {
        $query .= " AND gsm='$pmyidcard' ";
    }
}
if (!empty($pregion)) $query .= " AND region='$pregion' ";
mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

// DM
$query = "INSERT INTO $tmp01 (icabangid, karyawanid, sts)
    select distinct icabangid, dm, 'DM' as sts 
    from ms.penempatan_marketing where bulan Between '$pbln1' AND '$pbln2' 
    and ifnull(dm,'')<>'' ";
if ($pidgroup=="1" OR $pidgroup=="24") {
}else{
    if ($pmyjabatanid=="15") $query .= " AND mr='$pmyidcard' ";
    elseif ($pmyjabatanid=="10" OR $pmyjabatanid=="18") $query .= " AND am='$pmyidcard' ";
    elseif ($pmyjabatanid=="08") $query .= " AND dm='$pmyidcard' ";
    elseif ($pmyjabatanid=="20") $query .= " AND sm='$pmyidcard' ";
    else {
        $query .= " AND gsm='$pmyidcard' ";
    }
}
if (!empty($pregion)) $query .= " AND region='$pregion' ";
mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }


$query = "DELETE FROM $tmp01 WHERE ifnull(karyawanid,'') IN ('', '000', '0')";
mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }


//$query = "UPDATE $tmp01 as a JOIN ms.karyawan as b on a.karyawanid=b.karyawanid SET a.tglmasuk=b.tglmasuk";
//mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

$query = "select 'MR' as sts, karyawanid, jenis, sales, `target`, ach, incentive 
    from ms.incentive_mr where bulan between '$pbln1' AND '$pbln2' AND
    karyawanid IN (select distinct IFNULL(karyawanid,'') FROM $tmp01 WHERE sts='MR')";
$query = "CREATE TEMPORARY TABLE $tmp02 ($query)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

$query = "INSERT INTO $tmp02 (sts, karyawanid, jenis, sales, `target`, ach, incentive)
    select 'AM' as sts, karyawanid, jenis, sales, `target`, ach, incentive 
    from ms.incentive_am where bulan between '$pbln1' AND '$pbln2' AND
    karyawanid IN (select distinct IFNULL(karyawanid,'') FROM $tmp01 WHERE sts='AM')";
mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

$query = "INSERT INTO $tmp02 (sts, karyawanid, jenis, sales, `target`, ach, incentive)
    select 'DM' as sts, karyawanid, jenis, sales, `target`, ach, incentive 
    from ms.incentive_dm where bulan between '$pbln1' AND '$pbln2' AND
    karyawanid IN (select distinct IFNULL(karyawanid,'') FROM $tmp01 WHERE sts='DM')";
mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

$query = "ALTER TABLE $tmp02 ADD COLUMN icabangid VARCHAR(10)";
mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
$query = "UPDATE $tmp02 as a JOIN $tmp01 as b on a.karyawanid=b.karyawanid AND a.sts=b.sts SET a.icabangid=b.icabangid";
mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }


$query = "select a.*, b.nama as nama_karyawan, b.tglmasuk, c.nama as nama_cabang, c.region   
    FROM $tmp02 as a LEFT JOIN ms.karyawan as b on a.karyawanid=b.karyawanid 
    LEFT JOIN sls.icabang as c on a.icabangid=c.icabangid";
$query = "CREATE TEMPORARY TABLE $tmp03 ($query)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

$query = "DROP TEMPORARY TABLE $tmp02";
mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

//cari data sales
$query = "select b.region, a.icabangid, b.nama as nama_cabang, sum(a.value_sales) as sales, CAST(0 as DECIMAL(20,2)) as incentive  
    from sls.sales as a JOIN sls.icabang as b on a.icabangid=b.icabangid 
    WHERE a.bulan between '$pbln1' AND '$pbln2' ";
if (!empty($pregion)) {
    $query .= " AND b.region='$pregion' ";
}
$query .= " GROUP BY 1,2,3";
$query = "CREATE TEMPORARY TABLE $tmp02 ($query)";
mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

$query = "UPDATE $tmp02 as a JOIN 
    (select icabangid, sum(incentive) as incentive from $tmp03 GROUP BY 1) as b 
    on a.icabangid=b.icabangid SET a.incentive=b.incentive";
mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }


$query = "DELETE $tmp02 FROM $tmp02 JOIN $tmp03 on $tmp02.icabangid=$tmp03.icabangid WHERE IFNULL($tmp03.icabangid,'')=''";
mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

?>

<HTML>
<HEAD>
    <title>Laporan Incentive</title>
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
                    echo "<tr><td colspan=5 width='150px'><b>Laporan Incentive</b></td></tr>";
                    echo "<tr><td colspan=5 width='150px'><b>Bulan : $pbulan</b></td></tr>";
                    echo "<tr><td colspan=5 width='150px'><b>Region : $pnamaregion</b></td></tr>";
                    
                    echo "<tr><td colspan=5 width='150px'>view date : $pviewdate</td></tr>";
                    if ($pstsapv=="approve") {
                        echo "<tr><td colspan=5 width='150px'>Status : sudah approved</td></tr>";
                    }else{
                        echo "<tr><td colspan=5 width='150px'>Status : belum approve</td></tr>";
                    }
                }else{
                    echo "<tr><td width='150px'><b><h3>Laporan Incentive</h3></b></td></tr>";
                    echo "<tr><td width='150px'><b>Bulan : $pbulan</b></td></tr>";
                    echo "<tr><td width='150px'><b>Region : $pnamaregion</b></td></tr>";
                    
                    echo "<tr><td width='150px'><i>view date : $pviewdate</i></td></tr>";
                    echo "<tr><td width='150px'>Notes : Untuk approve silakan scroll ke paling bawah...</td></tr>";
                    if ($pstsapv=="approve") {
                        echo "<tr><td width='150px'><i>Status : sudah approved</i></td></tr>";
                    }else{
                        echo "<tr><td width='150px'><i>Status : belum approve</i></td></tr>";
                    }
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
    <div class="ijudul"><h2>Incentive Per Cabang</h2></div>
    <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
        <thead class="header" id="myHeader">
            <tr>
                <th>&nbsp;</th>
                <th>Cabang Id</th>
                <th>Cabang</th>
                <th>Sales</th>
                <th>Incentive</th>
                <th>Ratio</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $pidno=1;
            $ptotslsregion=0;
            $ptotincregion=0;
            $query = "select distinct region from $tmp03 Order by region";
            $tampil0= mysqli_query($cnms, $query);
            while ($row0= mysqli_fetch_array($tampil0)) {
                $nregion=$row0['region'];
                $nnmregion="Barat";
                if ($nregion=="T") $nnmregion="Timur";

                echo "<tr>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td nowrap><b>$nnmregion</b></td>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td nowrap align='right'>&nbsp;</td>";
                echo "<td nowrap align='right'>&nbsp;</td>";
                echo "<td nowrap align='right'>&nbsp;</td>";
                echo "</tr>";

                $ptotslscab=0;
                $ptotinccab=0;

                $query = "select icabangid, nama_cabang, SUM(sales) as sales, sum(incentive) as incentive FROM $tmp02 WHERE region='$nregion' ";
                $query .=" GROUP BY 1,2";
                $query .=" ORDER BY region, nama_cabang";
                $tampil= mysqli_query($cnms, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $nicabangid=$row['icabangid'];
                    $nicabangnm=$row['nama_cabang'];
                    $nsales=$row['sales'];
                    $nincentive=$row['incentive'];

                    $ptotslsregion=(DOUBLE)$ptotslsregion+(DOUBLE)$nsales;
                    $ptotincregion=(DOUBLE)$ptotincregion+(DOUBLE)$nincentive;

                    $ptotslscab=(DOUBLE)$ptotslscab+(DOUBLE)$nsales;
                    $ptotinccab=(DOUBLE)$ptotinccab+(DOUBLE)$nincentive;

                    $nratio=0;
                    if ($nsales<>"0") {
                        $nratio=ROUND($nincentive/$nsales*100,2);
                    }
                    
                    $nsales=number_format($nsales,0,",",",");
                    $nincentive=number_format($nincentive,0,",",",");

                    $pnamefield=$nicabangid;
                    $pnamebtnfld="btn".$nicabangid;
                    $pbtnshow = "<input type='button' id='$pnamebtnfld' name='$pnamebtnfld' class='btn btn-success btn-xs' value=' + ' onClick=\"showhideRow('$pnamefield')\">";


                    echo "<tr>";
                    echo "<td nowrap>$pbtnshow</td>";
                    echo "<td nowrap>$nicabangid</td>";
                    echo "<td nowrap>$nicabangnm</td>";
                    echo "<td nowrap align='right'>$nsales</td>";
                    echo "<td nowrap align='right'>$nincentive</td>";
                    echo "<td nowrap align='right'>$nratio</td>";
                    echo "</tr>";

                    
                    echo "<tr id='$pnamefield' style='display:none;'>";
                    echo "<td colspan='6'>";

                        echo "<table id='mydatatable2' class='table table-striped table-bordered tbl2' width='100%' border='1px solid black'>";
                        echo "<tr>";
                        echo "<th>No</th>";
                        echo "<th>Karyawan</th>";
                        echo "<th>Jabatan</th>";
                        echo "<th>Tgl Masuk</th>";
                        echo "<th>Incentive</th>";
                        echo "</tr>";

                        $precno=1;
                        $ptotsaleskry=0;
                        $ptotinckry=0;

                        $query = "select karyawanid, nama_karyawan, sts, tglmasuk, sum(incentive) as incentive FROM $tmp03 WHERE region='$nregion' AND icabangid='$nicabangid' ";
                        $query .=" GROUP BY 1,2,3,4";
                        $query .=" ORDER BY sts, nama_karyawan";
                        $tampil2 = mysqli_query($cnms, $query);
                        while ($row2= mysqli_fetch_array($tampil2)) {
                            $pidkaryawan=$row2['karyawanid'];
                            $pnmkaryawan=$row2['nama_karyawan'];
                            $pjabatan=$row2['sts'];
                            $ptglmasuk=$row2['tglmasuk'];
                            $pincentive=$row2['incentive'];

                            $ptotinckry=(DOUBLE)$ptotinckry+(DOUBLE)$pincentive;

                            $pincentive=number_format($pincentive,0,",",",");
                            

                            echo "<tr>";
                            echo "<td nowrap>$precno</td>";
                            echo "<td nowrap>$pnmkaryawan</td>";
                            echo "<td nowrap>$pjabatan</td>";
                            echo "<td nowrap>$ptglmasuk</td>";
                            echo "<td nowrap align='right'>$pincentive</td>";
                            echo "</tr>";

                            $precno++;
                            
                        }

                        $ptotinckry=number_format($ptotinckry,0,",",",");
                        
                        echo "<tr style='font-weight:bold;'>";
                        echo "<td nowrap>&nbsp;</td>";
                        echo "<td nowrap>Total : </td>";
                        echo "<td nowrap>&nbsp;</td>";
                        echo "<td nowrap>&nbsp;</td>";
                        echo "<td nowrap align='right'>$ptotinckry</td>";
                        echo "</tr>";

                        echo "</table>";

                    echo "</td>";
                    echo "</tr>";

                    $pidno++;
                }

                $nratio=0;
                if ($ptotslscab<>"0") {
                    $nratio=ROUND($ptotinccab/$ptotslscab*100,2);
                }

                $ptotslscab=number_format($ptotslscab,0,",",",");
                $ptotinccab=number_format($ptotinccab,0,",",",");

                echo "<tr style='font-weight:bold;'>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td nowrap>Tota $nnmregion : </td>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td nowrap align='right'>$ptotslscab</td>";
                echo "<td nowrap align='right'>$ptotinccab</td>";
                echo "<td nowrap align='right'>$nratio</td>";
                echo "</tr>";



            }

            if (empty($pregion)) {
                $nratio=0;
                if ($ptotslsregion<>"0") {
                    $nratio=ROUND($ptotincregion/$ptotslsregion*100,2);
                }

                $ptotslsregion=number_format($ptotslsregion,0,",",",");
                $ptotincregion=number_format($ptotincregion,0,",",",");

                echo "<tr style='font-weight:bold;'>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td nowrap>Grand Total : </td>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td nowrap align='right'>$ptotslsregion</td>";
                echo "<td nowrap align='right'>$ptotincregion</td>";
                echo "<td nowrap align='right'>$nratio</td>";
                echo "</tr>";
            }

            ?>
        </tbody>
    </table>
    

    <?PHP
    $notbl=3;
    $no=1;
    $query = "select distinct sts FROM $tmp03 order by sts";
    $tampil0= mysqli_query($cnms, $query);
    while ($row0= mysqli_fetch_array($tampil0)) {
        $njabatan=$row0['sts'];

        $pnamatabel="mydatatable".$notbl;
    ?>

        <hr/>
        <div class="ijudul"><h2>Incentive <?PHP echo $njabatan; ?></h2></div>
        <table id='<?PHP echo $pnamatabel; ?>' class='table table-striped table-bordered' width="100%" border="1px solid black">
            <thead class="header" id="myHeader">
                <tr>
                    <th>No</th>
                    <th>Karyawan Id</th>
                    <th>Nama</th>
                    <th>Incentive</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $ptotslskry=0;
                $ptotinckry=0;
                $no=1;
                $query = "select karyawanid, nama_karyawan, SUM(sales) as sales, sum(incentive) as incentive FROM $tmp03 WHERE sts='$njabatan' ";
                $query .=" GROUP BY 1,2";
                $query .=" ORDER BY nama_karyawan, karyawanid";
                $tampil= mysqli_query($cnms, $query);
                while ($row= mysqli_fetch_array($tampil)) {

                    $nkryid=$row['karyawanid'];
                    $nkrynm=$row['nama_karyawan'];
                    $nsales=$row['sales'];
                    $nincentive=$row['incentive'];

                    $ptotslskry=(DOUBLE)$ptotslskry+(DOUBLE)$nsales;
                    $ptotinckry=(DOUBLE)$ptotinckry+(DOUBLE)$nincentive;

                    $nratio=0;
                    if ($nsales<>"0") {
                        $nratio=ROUND($nincentive/$nsales*100,2);
                    }
                    
                    $nsales=number_format($nsales,0,",",",");
                    $nincentive=number_format($nincentive,0,",",",");


                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$nkryid</td>";
                    echo "<td nowrap>$nkrynm</td>";
                    //echo "<td nowrap align='right'>$nsales</td>";
                    echo "<td nowrap align='right'>$nincentive</td>";
                    //echo "<td nowrap align='right'>$nratio</td>";
                    echo "</tr>";

                    $no++;

                }

                $nratio=0;
                if ($ptotslskry<>"0") {
                    $nratio=ROUND($ptotinckry/$ptotslskry*100,2);
                }

                $ptotslskry=number_format($ptotslskry,0,",",",");
                $ptotinckry=number_format($ptotinckry,0,",",",");

                echo "<tr style='font-weight:bold;'>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td nowrap>Total $njabatan : </td>";
                echo "<td nowrap>&nbsp;</td>";
                //echo "<td nowrap align='right'>$ptotslskry</td>";
                echo "<td nowrap align='right'>$ptotinckry</td>";
                //echo "<td nowrap align='right'>$nratio</td>";
                echo "</tr>";

                ?>
            </tbody>
        </table>

    <?PHP
        $notbl++;
    }
    ?>


    <hr/>
    <div class="ijudul"><h2>Incentive Jabatan</h2></div>
    <table id='mydatatable6' class='table table-striped table-bordered' width="100%" border="1px solid black">
        <thead class="header" id="myHeader">
            <tr>
                <th>No</th>
                <th>Jabatan</th>
                <th>Incentive</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $no=1;
            $ptotslssts=0;
            $ptotincsts=0;
            $query = "select sts, sum(incentive) as incentive from $tmp03 ";
            $query .=" GROUP BY 1";
            $query .=" ORDER BY 1";
            $tampil0= mysqli_query($cnms, $query);
            while ($row0= mysqli_fetch_array($tampil0)) {
                $njabatan=$row0['sts'];

                $nincentive=$row0['incentive'];
                $ptotincsts=(DOUBLE)$ptotincsts+(DOUBLE)$nincentive;

                $nincentive=number_format($nincentive,0,",",",");


                echo "<tr>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap>$njabatan</td>";
                //echo "<td nowrap align='right'>$nsales</td>";
                echo "<td nowrap align='right'>$nincentive</td>";
                //echo "<td nowrap align='right'>$nratio</td>";
                echo "</tr>";

                $no++;
            }

            $ptotincsts=number_format($ptotincsts,0,",",",");

            echo "<tr style='font-weight:bold;'>";
            echo "<td nowrap>&nbsp;</td>";
            echo "<td nowrap>Total : </td>";
            //echo "<td nowrap align='right'>$ptotslskry</td>";
            echo "<td nowrap align='right'>$ptotincsts</td>";
            //echo "<td nowrap align='right'>$nratio</td>";
            echo "</tr>";
            ?>
        </tbody>
    </table>

    
    <div class='col-md-12 col-sm-12 col-xs-12'>
        <div class='x_panel'>
            <?PHP
            ?>
            <form name='form_data1' id='form_data1' method='POST' 
                action="<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu"; ?>" 
                enctype='multipart/form-data' target="_blank">

                <table id='mydatatable_' class='table-bordered' border="1px solid black">
                    <tr>
                        <td nowrap>Bulan </td>
                        <td nowrap> : </td>
                        <td><input type='text' id='e_bulan' name='e_bulan' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl01; ?>' placeholder='dd mmm yyyy' Readonly></td>
                    </tr>

                    <tr>
                        <td nowrap>Region </td>
                        <td nowrap> : </td>
                        <td>
                            <input type='hidden' id='e_region' name='e_region' required='required' class='form-control' value='<?PHP echo $pregion; ?>' Readonly>
                            <input type='text' id='e_nmregion' name='e_nmregion' required='required' class='form-control' value='<?PHP echo $pnamaregion; ?>' Readonly>
                        </td>
                    </tr>
                    <tr>
                        
                            <?PHP
                            if ($pstsapv=="approve") {
                                echo "<td>approved</td><td>&nbsp;</td>";
                                echo "<td>$ptglapv</td>";
                            }else{
                                echo "<td>&nbsp;</td><td>&nbsp;</td>";
                                echo "<td>
                                    <input type='button' id='btnsimpan' name='btnsimpan' class='btn btn-warning' value='Approve' onClick=\"SimpanApproveInc('$pplblnapv', '$pmyidcard', '$pregion')\">
                                    <label id='lblstatus'></label>
                                    </td>";
                            }
                            ?>
                        
                    </tr>

                </table>
                
            </form>

        </div>
    </div>

    <br/><br/><br/><br/>
    <br/><br/><br/><br/>
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
            #mydatatable1, #mydatatable2, #mydatatable3, #mydatatable4, #mydatatable5, #mydatatable6, #mydatatable_ {
                color:#000;
                font-family: "Arial";
            }
            #mydatatable1 th, #mydatatable2 th, #mydatatable3 th, #mydatatable4 th, #mydatatable5 th, #mydatatable6 th {
                font-size: 12px;
            }
            #mydatatable1 td, #mydatatable2 td, #mydatatable3 td, #mydatatable4 td, #mydatatable5 td, #mydatatable6 td, #mydatatable_ td { 
                font-size: 14px;
            }


            th {
                background: white;
                position: sticky;
                top: 0;
                box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
                z-index:1;
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
    
        function SimpanApproveInc(itgl, ikaryawan, iregion) {
            if (itgl=="") {
                alert("tidak ada data yang akan diapprove...");
                return false;
            }
            var pText_="Apakah akan melakukan approve...?";
            ok_ = 1;
            if (ok_) {
                var r=confirm(pText_)
                if (r==true) {
                    
                    $.ajax({
                        type:"post",
                        url:"module/incentive/inc_incentivelap/simpanapproveinc.php?module=simpandataapprove",
                        data:"ubulan="+itgl+"&ukaryawan="+ikaryawan+"&uregion="+iregion,
                        beforeSend: function () {
                            //$('select[name=cb_karyawan]').attr('disabled',true)
                            //$('select[name=cb_karyawan]').empty();
                        },
                        success:function(data){
                            if (data.trim()=="berhasil") {
                                alert("berhasil approve...");
                                btnsimpan.style.display = "none";
                                document.getElementById('lblstatus').innerHTML = 'berhasil approve';
                            }else{
                                alert(data);
                                btnsimpan.style.display = "block";
                                document.getElementById('lblstatus').innerHTML = '';
                            }
                        },
                        complete: function () {
                            //$('select[name=cb_karyawan]').attr('disabled',false)
                            //$('select[name=cb_karyawan] option[value="loading"]').text('');
                        },
                        error: function () {
                            //$('select[name=cb_karyawan]').attr('disabled',false)
                            alert('Something wrong. Try Again!')            
                        }
                    });

                }
            } else {
                //document.write("You pressed Cancel!")
                return 0;
            }

        }

        var table13 = $('#mydatatable3, #mydatatable4, #mydatatable5').DataTable({
            fixedHeader: true,
            "ordering": false,
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": -1,
            "order": [[ 0, "asc" ]],
            "columnDefs": [
                { "visible": false },
                { className: "text-right", "targets": [3] },//right
                { className: "text-nowrap", "targets": [0,1,2,3] }//nowrap

            ],
            bFilter: true, bInfo: true, "bLengthChange": false, "bLengthChange": true,
            "bPaginate": false
        } );


        $(document).ready(function() {
            

        } );


        function showhideRow(rowId) {
            if (document.getElementById(rowId).style.display=="none") {
                document.getElementById(rowId).style.display = "";
                document.getElementById('btn'+rowId).value="  -  ";
            }else{
                document.getElementById(rowId).style.display = "none";
                document.getElementById('btn'+rowId).value=" + ";
            }
        }    

        function SortTabel(icol) {
            var table, rows, switching, i, x, y, shouldSwitch;
            table = document.getElementById("mydatatable1");
            switching = true;
            /*Make a loop that will continue until
            no switching has been done:*/
            while (switching) {
                //start by saying: no switching is done:
                switching = false;
                rows = table.rows;
                /*Loop through all table rows (except the
                first, which contains table headers):*/
                for (i = 1; i < (rows.length - 1); i++) {
                    //start by saying there should be no switching:
                    shouldSwitch = false;
                    /*Get the two elements you want to compare,
                    one from current row and one from the next:*/
                    x = rows[i].getElementsByTagName("TD")[icol];
                    y = rows[i + 1].getElementsByTagName("TD")[icol];
                    //check if the two rows should switch place:
                    if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                      //if so, mark as a switch and break the loop:
                      shouldSwitch = true;
                      break;
                    }
                }
                
                if (shouldSwitch) {
                    /*If a switch has been marked, make the switch
                    and mark that a switch has been done:*/
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                }
                
            }
        }
    
    </script>
</HTML>

<?PHP
hapusdata:
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp03");
    mysqli_close($cnms);
?>