<?PHP

$query = "select 'MR' as sts, karyawanid, jenis, sales, `target`, ach, incentive 
from ms.incentive_mr where bulan between '$pbln1' AND '$pbln2' ";
if ($pincfrom=="GSM") $query .= " AND IFNULL(jenis2,'')='GSM' ";
elseif ($pincfrom=="PM") $query .= " AND IFNULL(jenis2,'')='PM' ";
if (empty($pjabatan) OR $pjabatan=="MR") {
}else{
$query .=" AND karyawanid='NON NONE'";
}
$query = "CREATE TEMPORARY TABLE $tmp01 ($query)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

if (empty($pjabatan) OR $pjabatan=="AM") {
$query = "INSERT INTO $tmp01 (sts, karyawanid, jenis, sales, `target`, ach, incentive)
    select 'AM' as sts, karyawanid, jenis, sales, `target`, ach, incentive 
    from ms.incentive_am where bulan between '$pbln1' AND '$pbln2' ";
if ($pincfrom=="GSM") $query .= " AND IFNULL(jenis2,'')='GSM' ";
elseif ($pincfrom=="PM") $query .= " AND IFNULL(jenis2,'')='PM' ";
mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
}

if (empty($pjabatan) OR $pjabatan=="DM") {
$query = "INSERT INTO $tmp01 (sts, karyawanid, jenis, sales, `target`, ach, incentive)
    select 'DM' as sts, karyawanid, jenis, sales, `target`, ach, incentive 
    from ms.incentive_dm where bulan between '$pbln1' AND '$pbln2' ";
if ($pincfrom=="GSM") $query .= " AND IFNULL(jenis2,'')='GSM' ";
elseif ($pincfrom=="PM") $query .= " AND IFNULL(jenis2,'')='PM' ";
mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
}

$query = "DELETE FROM $tmp01 WHERE IFNULL(incentive,0)=0";
mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }


$query = "select a.*, b.nama as nama_karyawan from $tmp01 as a JOIN ms.karyawan as b on a.karyawanid=b.karyawanid";
$query = "CREATE TEMPORARY TABLE $tmp02 ($query)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }


$tmp04 ="dbtemp.tmprptincrkp04_".$puser."_$now$milliseconds";
$tmp05 ="dbtemp.tmprptincrkp05_".$puser."_$now$milliseconds";

//pivot
if ($preportpl=="P") {


    //AM
    $pfiltersts="('AM')";

    $query = "select distinct karyawanid, nama_karyawan FROM $tmp02 WHERE sts IN $pfiltersts";
    $query = "CREATE TEMPORARY TABLE $tmp03 ($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

    $arridjenisam[]="";
    unset($arridjenisam);
    $addcolumn="";
    $columntotal="";
    $query = "select distinct jenis from $tmp02 WHERE sts IN $pfiltersts Order by jenis";
    $tampil=mysqli_query($cnms, $query);
    $ketemuam=mysqli_num_rows($tampil);
    if ((INT)$ketemuam>0) {

        while ($row=mysqli_fetch_array($tampil)) {
            $pnmjenis=$row['jenis'];
            $arridjenisam[]=$pnmjenis;

            $columntotal .="IFNULL(`$pnmjenis`,0)+";
            $addcolumn .= " ADD COLUMN `$pnmjenis` DECIMAL(20,2),";
        }
        $addcolumn .= " ADD TOTAL DECIMAL(20,2)";

        $query = "ALTER TABLE $tmp03 $addcolumn";
        mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }


        for($ix=0;$ix<count($arridjenisam);$ix++) {
            $pnmjenis=$arridjenisam[$ix];

            $query = "UPDATE $tmp03 as a JOIN (select karyawanid, sum(incentive) as incentive FROM 
                $tmp01 WHERE jenis='$pnmjenis' AND sts IN $pfiltersts GROUP BY 1) as b
                on a.karyawanid=b.karyawanid SET a.`$pnmjenis`=b.incentive";
            //echo "$query<br/>";
            mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
        }

        if (!empty($columntotal)) {
            $columntotal=substr($columntotal, 0, -1);
            $query = "UPDATE $tmp03 SET TOTAL=$columntotal";
            mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
        }

    }
    // END AM

    //DM
    $pfiltersts="('DM')";

    $query = "select distinct karyawanid, nama_karyawan FROM $tmp02 WHERE sts IN $pfiltersts";
    $query = "CREATE TEMPORARY TABLE $tmp04 ($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

    $arridjenisdm[]="";
    unset($arridjenisdm);
    $addcolumn="";
    $columntotal="";
    $query = "select distinct jenis from $tmp02 WHERE sts IN $pfiltersts Order by jenis";
    $tampil=mysqli_query($cnms, $query);
    $ketemudm=mysqli_num_rows($tampil);
    if ((INT)$ketemudm>0) {

        while ($row=mysqli_fetch_array($tampil)) {
            $pnmjenis=$row['jenis'];
            $arridjenisdm[]=$pnmjenis;

            $columntotal .="IFNULL(`$pnmjenis`,0)+";
            $addcolumn .= " ADD COLUMN `$pnmjenis` DECIMAL(20,2),";
        }
        $addcolumn .= " ADD TOTAL DECIMAL(20,2)";

        $query = "ALTER TABLE $tmp04 $addcolumn";
        mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }


        for($ix=0;$ix<count($arridjenisdm);$ix++) {
            $pnmjenis=$arridjenisdm[$ix];

            $query = "UPDATE $tmp04 as a JOIN (select karyawanid, sum(incentive) as incentive FROM 
                $tmp01 WHERE jenis='$pnmjenis' AND sts IN $pfiltersts GROUP BY 1) as b
                on a.karyawanid=b.karyawanid SET a.`$pnmjenis`=b.incentive";
            //echo "$query<br/>";
            mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
        }

        if (!empty($columntotal)) {
            $columntotal=substr($columntotal, 0, -1);
            $query = "UPDATE $tmp04 SET TOTAL=$columntotal";
            mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
        }

    }

    // END DM

    //MR
    $pfiltersts="('MR')";

    $query = "select distinct karyawanid, nama_karyawan FROM $tmp02 WHERE sts IN $pfiltersts";
    $query = "CREATE TEMPORARY TABLE $tmp05 ($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

    $arridjenismr[]="";
    unset($arridjenismr);
    $addcolumn="";
    $columntotal="";
    $query = "select distinct jenis from $tmp02 WHERE sts IN $pfiltersts Order by jenis";
    $tampil=mysqli_query($cnms, $query);
    $ketemumr=mysqli_num_rows($tampil);
    if ((INT)$ketemumr>0) {

        while ($row=mysqli_fetch_array($tampil)) {
            $pnmjenis=$row['jenis'];
            $arridjenismr[]=$pnmjenis;

            $columntotal .="IFNULL(`$pnmjenis`,0)+";
            $addcolumn .= " ADD COLUMN `$pnmjenis` DECIMAL(20,2),";
        }
        $addcolumn .= " ADD TOTAL DECIMAL(20,2)";

        $query = "ALTER TABLE $tmp05 $addcolumn";
        mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }


        for($ix=0;$ix<count($arridjenismr);$ix++) {
            $pnmjenis=$arridjenismr[$ix];

            $query = "UPDATE $tmp05 as a JOIN (select karyawanid, sum(incentive) as incentive FROM 
                $tmp01 WHERE jenis='$pnmjenis' AND sts IN $pfiltersts GROUP BY 1) as b
                on a.karyawanid=b.karyawanid SET a.`$pnmjenis`=b.incentive";
            //echo "$query<br/>";
            mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
        }

        if (!empty($columntotal)) {
            $columntotal=substr($columntotal, 0, -1);
            $query = "UPDATE $tmp05 SET TOTAL=$columntotal";
            mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
        }

    }
    // END MR


}


$tmp06 ="dbtemp.tmprptincrkp06_".$puser."_$now$milliseconds";

$query = "select b.divprodid, sum(incentive) as incentive "
        . " FROM $tmp01 as a JOIN ms.jenisincentivepm as b on a.jenis=b.jenis";
$query .=" GROUP BY 1";
$query = "CREATE TEMPORARY TABLE $tmp06 ($query)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }


?>

<HTML>
<HEAD>
    <title>Rekap Incentive Detail</title>
    <meta http-equiv="Expires" content="Mon, 01 Mei 2050 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
    <!--<link href="css/laporanbaru.css" rel="stylesheet">-->
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>

    <style>
    @page 
    {
        /*size: auto;   /* auto is the current printer page size */
        /*margin: 0mm;  /* this affects the margin in the printer settings */
        margin-left: 7mm;  /* this affects the margin in the printer settings */
        margin-right: 7mm;  /* this affects the margin in the printer settings */
        margin-top: 5mm;  /* this affects the margin in the printer settings */
        margin-bottom: 5mm;  /* this affects the margin in the printer settings */
        size: portrait;
    }
    @media print {
        .ibuton {
            display:none;
        }
        
        .page-break { display: block; page-break-before: always; }
    }
    </style>

</HEAD>

<BODY>

<?PHP if ($ppilihrpt!="excel") { ?>

    <span class='ibuton'>
        <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
    </span>

<?PHP } ?>

<div id='n_content'>


<?PHP
//pivot
if ($preportpl=="P") {

    if ($pincfrom=="PM") {
        echo "<center><b>Incentive From PM - $pbulan</b></center><br>";
        echo "<div class='page-break'>";
            echo "<table id='dttable' border='1' cellspacing='0' cellpadding='1' width='50%'>";
                echo "<thead>";
                echo "<tr>";
                    echo "<th align='center'>No</th>";
                    echo "<th align='center'>Divisi</th>";
                    echo "<th align='center'>Incentive</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";

                $no_n=1;
                $ptotalinc_n=0;
                $query_n = "select * from  $tmp06 order by divprodid";
                $tampil_n= mysqli_query($cnms, $query_n);
                while ($nro= mysqli_fetch_array($tampil_n)) {
                    $ndivpord=$nro['divprodid'];
                    $nincentive=$nro['incentive'];


                    $nnamadiv=$ndivpord;
                    if ($ndivpord=="CAN") $nnamadiv="CANARY";
                    if ($ndivpord=="PEACO") $nnamadiv="PEACOCK";
                    if ($ndivpord=="PIGEO") $nnamadiv="PIGEON";

                    $ptotalinc_n=(DOUBLE)$ptotalinc_n+(DOUBLE)$nincentive;

                    if ($ppilihrpt!="excel") {
                        $nincentive=number_format($nincentive,0,",",",");
                    }

                    echo "<tr>";
                    echo "<td nowrap>$no_n</td>";
                    echo "<td nowrap>$nnamadiv</td>";
                    echo "<td nowrap align='right'>$nincentive</td>";
                    echo "</tr>";

                    $no_n++;

                }

                if ($ppilihrpt!="excel") {
                    $ptotalinc_n=number_format($ptotalinc_n,0,",",",");
                }

                echo "<tr style='font-weight:bold;'>";
                echo "<td nowrap></td>";
                echo "<td nowrap>TOTAL </td>";
                echo "<td nowrap align='right'>$ptotalinc_n</td>";
                echo "</tr>";

                echo "</tbody>";

            echo "</table>";

            echo "<br/><br/>";
        echo "</div>";
    }
            
    //AM
    echo "<div class='page-break'>";

        echo "<center><b>Incentive AM - $pbulan</b></center><br>";
        echo "<center>Incentive From $pincfrom</center><br>";
        

        if ((INT)$ketemuam>0) {
            
            $pgrandtotinc=0;
            for($ix=0;$ix<count($arridjenisam);$ix++) {
                $ptotperjenis[$ix]=0;
            }

            echo "<table id='dttable' border='1' cellspacing='0' cellpadding='1'>";
                echo "<thead>";
                echo "<tr>";
                    echo "<th align='center'>Karyawan</th>";
                    for($ix=0;$ix<count($arridjenisam);$ix++) {
                        $pnmjenis=$arridjenisam[$ix];
                        echo "<th align='center'>$pnmjenis</th>";
                    }
                    echo "<th align='center'>Total</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";

                    $query = "select * from $tmp03 order by nama_karyawan, karyawanid";
                    $tampil1=mysqli_query($cnms, $query);
                    while ($row1=mysqli_fetch_array($tampil1)) {
                        $pkaryawanid=$row1['karyawanid'];
                        $pkaryawannm=$row1['nama_karyawan'];
                        $ptotalkry=$row1['TOTAL'];

                        $pgrandtotinc=(DOUBLE)$pgrandtotinc+(DOUBLE)$ptotalkry;

                        if ($ppilihrpt!="excel") {
                            $ptotalkry=number_format($ptotalkry,0,",",",");
                        }
                        

                        echo "<tr>";
                        echo "<td nowrap>$pkaryawannm</td>";
                        for($ix=0;$ix<count($arridjenisam);$ix++) {
                            $pincfld=$row1[$arridjenisam[$ix]];

                            $ptotperjenis[$ix]=(DOUBLE)$ptotperjenis[$ix]+(DOUBLE)$pincfld;
                            
                            if ($ppilihrpt!="excel") {
                                $pincfld=number_format($pincfld,0,",",",");
                            }

                            echo "<td nowrap align='right'>$pincfld</td>";
                        }
                        echo "<td nowrap align='right'><b>$ptotalkry</b></td>";
                        echo "</tr>";

                    }
                    
                    //grand total
                    if ($ppilihrpt!="excel") {
                        $pgrandtotinc=number_format($pgrandtotinc,0,",",",");
                    }

                    echo "<tr class='trtotal'>";
                    echo "<td nowrap>Grand Total</td>";
                    for($ix=0;$ix<count($arridjenisam);$ix++) {
                        $pincfld=$ptotperjenis[$ix];

                        if ($ppilihrpt!="excel") {
                            $pincfld=number_format($pincfld,0,",",",");
                        }

                        echo "<td nowrap align='right'>$pincfld</td>";
                    }
                    echo "<td nowrap align='right'><b>$pgrandtotinc</b></td>";
                    echo "</tr>";


                echo "</tbody>";
            echo "</table>";
            echo "<br/>";

        }

    echo "</div>";

    //DM
    echo "<div class='page-break'>";

        echo "<center><b>Incentive DM - $pbulan</b></center><br>";
        echo "<center>Incentive From $pincfrom</center><br>";

        if ((INT)$ketemudm>0) {

            $pgrandtotinc=0;
            for($ix=0;$ix<count($arridjenisdm);$ix++) {
                $ptotperjenis[$ix]=0;
            }

            echo "<table id='dttable' border='1' cellspacing='0' cellpadding='1'>";
                echo "<thead>";
                echo "<tr>";
                    echo "<th align='center'>Karyawan</th>";
                    for($ix=0;$ix<count($arridjenisdm);$ix++) {
                        $pnmjenis=$arridjenisdm[$ix];
                        echo "<th align='center'>$pnmjenis</th>";
                    }
                    echo "<th align='center'>Total</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";

                    $query = "select * from $tmp04 order by nama_karyawan, karyawanid";
                    $tampil1=mysqli_query($cnms, $query);
                    while ($row1=mysqli_fetch_array($tampil1)) {
                        $pkaryawanid=$row1['karyawanid'];
                        $pkaryawannm=$row1['nama_karyawan'];
                        $ptotalkry=$row1['TOTAL'];

                        $pgrandtotinc=(DOUBLE)$pgrandtotinc+(DOUBLE)$ptotalkry;

                        if ($ppilihrpt!="excel") {
                            $ptotalkry=number_format($ptotalkry,0,",",",");
                        }
                        

                        echo "<tr>";
                        echo "<td nowrap>$pkaryawannm</td>";
                        for($ix=0;$ix<count($arridjenisdm);$ix++) {
                            $pincfld=$row1[$arridjenisdm[$ix]];

                            $ptotperjenis[$ix]=(DOUBLE)$ptotperjenis[$ix]+(DOUBLE)$pincfld;

                            if ($ppilihrpt!="excel") {
                                $pincfld=number_format($pincfld,0,",",",");
                            }

                            echo "<td nowrap align='right'>$pincfld</td>";
                        }
                        echo "<td nowrap align='right'><b>$ptotalkry</b></td>";
                        echo "</tr>";

                    }
                    
                    //grand total
                    if ($ppilihrpt!="excel") {
                        $pgrandtotinc=number_format($pgrandtotinc,0,",",",");
                    }

                    echo "<tr class='trtotal'>";
                    echo "<td nowrap>Grand Total</td>";
                    for($ix=0;$ix<count($arridjenisdm);$ix++) {
                        $pincfld=$ptotperjenis[$ix];

                        if ($ppilihrpt!="excel") {
                            $pincfld=number_format($pincfld,0,",",",");
                        }

                        echo "<td nowrap align='right'>$pincfld</td>";
                    }
                    echo "<td nowrap align='right'><b>$pgrandtotinc</b></td>";
                    echo "</tr>";


                echo "</tbody>";
            echo "</table>";
            echo "<br/>";

        }

    echo "</div>";


    //MR
    echo "<div class='page-break'>";

        echo "<center><b>Incentive MR - $pbulan</b></center><br>";
        echo "<center>Incentive From $pincfrom</center><br>";

        if ((INT)$ketemumr>0) {

            $pgrandtotinc=0;
            for($ix=0;$ix<count($arridjenismr);$ix++) {
                $ptotperjenis[$ix]=0;
            }

            echo "<table id='dttable' border='1' cellspacing='0' cellpadding='1'>";
                echo "<thead>";
                echo "<tr>";
                    echo "<th align='center'>Karyawan</th>";
                    for($ix=0;$ix<count($arridjenismr);$ix++) {
                        $pnmjenis=$arridjenismr[$ix];
                        echo "<th align='center'>$pnmjenis</th>";
                    }
                    echo "<th align='center'>Total</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";

                    $query = "select * from $tmp05 order by nama_karyawan, karyawanid";
                    $tampil1=mysqli_query($cnms, $query);
                    while ($row1=mysqli_fetch_array($tampil1)) {
                        $pkaryawanid=$row1['karyawanid'];
                        $pkaryawannm=$row1['nama_karyawan'];
                        $ptotalkry=$row1['TOTAL'];

                        $pgrandtotinc=(DOUBLE)$pgrandtotinc+(DOUBLE)$ptotalkry;

                        if ($ppilihrpt!="excel") {
                            $ptotalkry=number_format($ptotalkry,0,",",",");
                        }
                        

                        echo "<tr>";
                        echo "<td nowrap>$pkaryawannm</td>";
                        for($ix=0;$ix<count($arridjenismr);$ix++) {
                            $pincfld=$row1[$arridjenismr[$ix]];

                            $ptotperjenis[$ix]=(DOUBLE)$ptotperjenis[$ix]+(DOUBLE)$pincfld;

                            if ($ppilihrpt!="excel") {
                                $pincfld=number_format($pincfld,0,",",",");
                            }

                            echo "<td nowrap align='right'>$pincfld</td>";
                        }
                        echo "<td nowrap align='right'><b>$ptotalkry</b></td>";
                        echo "</tr>";

                    }
                    
                    //grand total
                    if ($ppilihrpt!="excel") {
                        $pgrandtotinc=number_format($pgrandtotinc,0,",",",");
                    }

                    echo "<tr class='trtotal'>";
                    echo "<td nowrap>Grand Total</td>";
                    for($ix=0;$ix<count($arridjenismr);$ix++) {
                        $pincfld=$ptotperjenis[$ix];

                        if ($ppilihrpt!="excel") {
                            $pincfld=number_format($pincfld,0,",",",");
                        }

                        echo "<td nowrap align='right'>$pincfld</td>";
                    }
                    echo "<td nowrap align='right'><b>$pgrandtotinc</b></td>";
                    echo "</tr>";


                echo "</tbody>";
            echo "</table>";
            echo "<br/>";

        }


    echo "</div>";


}else{

    echo "<center><b>Incentive All (DM,AM,MR) - $pbulan</b></center><br>";
    echo "<center>Incentive From $pincfrom</center><br>";
    if (!empty($pjabatan)) {
        echo "<center>Jabatan : $pjabatan</center><br>";
    }

    
    if ($pincfrom=="PM") {
        
        echo "<table id='dttable' border='1' cellspacing='0' cellpadding='1' width='50%'>";
            echo "<thead>";
            echo "<tr>";
                echo "<th align='center'>No</th>";
                echo "<th align='center'>Divisi</th>";
                echo "<th align='center'>Incentive</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            $no_n=1;
            $ptotalinc_n=0;
            $query_n = "select * from  $tmp06 order by divprodid";
            $tampil_n= mysqli_query($cnms, $query_n);
            while ($nro= mysqli_fetch_array($tampil_n)) {
                $ndivpord=$nro['divprodid'];
                $nincentive=$nro['incentive'];


                $nnamadiv=$ndivpord;
                if ($ndivpord=="CAN") $nnamadiv="CANARY";
                if ($ndivpord=="PEACO") $nnamadiv="PEACOCK";
                if ($ndivpord=="PIGEO") $nnamadiv="PIGEON";

                $ptotalinc_n=(DOUBLE)$ptotalinc_n+(DOUBLE)$nincentive;

                if ($ppilihrpt!="excel") {
                    $nincentive=number_format($nincentive,0,",",",");
                }

                echo "<tr>";
                echo "<td nowrap>$no_n</td>";
                echo "<td nowrap>$nnamadiv</td>";
                echo "<td nowrap align='right'>$nincentive</td>";
                echo "</tr>";

                $no_n++;

            }

            if ($ppilihrpt!="excel") {
                $ptotalinc_n=number_format($ptotalinc_n,0,",",",");
            }

            echo "<tr style='font-weight:bold;'>";
            echo "<td nowrap></td>";
            echo "<td nowrap>TOTAL </td>";
            echo "<td nowrap align='right'>$ptotalinc_n</td>";
            echo "</tr>";

            echo "</tbody>";

        echo "</table>";

        echo "<br/><br/>";
        
    }
    
    $pgrandtotinc=0;
    echo "<table id='dttable' border='1' cellspacing='0' cellpadding='1'>";
        echo "<thead>";
        echo "<tr>";
            $header_ = add_space('Karyawan',40);
            echo "<th align='center'>Karyawan</th>";
            echo "<th align='center'>Incentive</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        $query = "select distinct sts from $tmp02 Order by sts";
        $tampil0=mysqli_query($cnms, $query);
        while ($row0=mysqli_fetch_array($tampil0)) {
            $njabatan=$row0['sts'];

            $no=1;
            $ptotincjbt=0;
            $query = "select distinct karyawanid, nama_karyawan from $tmp02 WHERE sts='$njabatan' Order by sts, nama_karyawan, karyawanid";
            $tampil1=mysqli_query($cnms, $query);
            while ($row1=mysqli_fetch_array($tampil1)) {
                $pkaryawanid=$row1['karyawanid'];
                $pkaryawannm=$row1['nama_karyawan'];


                echo "<tr>";
                echo "<td nowrap>$pkaryawannm</td>";
                echo "<td nowrap align='right'></td>";
                echo "</tr>";

                $ptotalincjenis=0;

                $query = "select * from $tmp02 WHERE karyawanid='$pkaryawanid' AND sts='$njabatan' Order by sts, nama_karyawan, karyawanid, jenis";
                $tampil2=mysqli_query($cnms, $query);
                while ($row2=mysqli_fetch_array($tampil2)) {
                    $pjenisid=$row2['jenis'];

                    $pnmjenis=$pjenisid;

                    $pincentive=$row2['incentive'];

                    $ptotalincjenis=(DOUBLE)$ptotalincjenis+(DOUBLE)$pincentive;
                    $ptotincjbt=(DOUBLE)$ptotincjbt+(DOUBLE)$pincentive;
                    $pgrandtotinc=(DOUBLE)$pgrandtotinc+(DOUBLE)$pincentive;

                    if ($ppilihrpt!="excel") {
                        $pincentive=number_format($pincentive,0,",",",");
                    }

                    echo "<tr>";
                    echo "<td nowrap class='tdijenis'>$pnmjenis</td>";
                    echo "<td nowrap align='right'>$pincentive</td>";
                    echo "</tr>";

                }


                $no++;
            }

            if ($ppilihrpt!="excel") {
                $ptotincjbt=number_format($ptotincjbt,0,",",",");
            }

            echo "<tr class='trtotal'>";
            echo "<td nowrap class='tdijenis'>Total $njabatan</td>";
            echo "<td nowrap align='right'>$ptotincjbt</td>";
            echo "</tr>";

        }

        if (empty($pjabatan)) {
            echo "<tr class='trtotal'>";
            echo "<td nowrap class='tdijenis'>&nbsp;</td>";
            echo "<td nowrap align='right'>&nbsp;</td>";
            echo "</tr>";

            if ($ppilihrpt!="excel") {
                $pgrandtotinc=number_format($pgrandtotinc,0,",",",");
            }

            echo "<tr class='trtotal'>";
            echo "<td nowrap class='tdijenis'>Grand Total </td>";
            echo "<td nowrap align='right'>$pgrandtotinc</td>";
            echo "</tr>";
        }

        echo "</tbody>";
    echo "</table>";

}

echo "<br/><br/>";

echo "<div><b>Approve</b></div>";

echo "<table id='dttable' border='1' cellspacing='0' cellpadding='1'>";
echo "<tr><th>Nama</th><th>Status Approve</th><th>Tgl. Approve</th></tr>";

$query = "select a.karyawanid, b.nama, a.status as sts, date_format(a.sys_time,'%d/%m/%Y %H:%i:%s') as sys_time 
    from ms.approve_insentif as a JOIN ms.karyawan as b on a.karyawanid=b.karyawanid 
    WHERE LEFT(bulan,7)='$pfbln' ";
if (!empty($pincfrom) AND $pincfrom<>"ALL") $query .=" AND a.sts_apv='$pincfrom' ";
$query .=" order by a.sys_time";

$tampil=mysqli_query($cnms, $query);
while ($row=mysqli_fetch_array($tampil)) {
    $pnmapv=$row['nama'];
    $psts=$row['sts'];
    $ptglapv=$row['sys_time'];

    echo "<tr>";
    echo "<td nowrap>$pnmapv</td>";
    echo "<td nowrap>$psts</td>";
    echo "<td nowrap>$ptglapv</td>";
    echo "</tr>";

}

echo "</table>";
echo "<br/><br/>";

echo "<div>Menyetujui,</div>";
echo "<br/>&nbsp;";
echo "<br/>&nbsp;";
echo "<br/>&nbsp;";
echo "<div><b><u>Evi K. Santoso</u></b></div>";
echo "<div>Chief Operation Officer</div>";

echo "<br/><br/>";
echo "<br/><br/>";
echo "<br/><br/>";

?>



</div>

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
    #dttable, #dttable th, #dttable td {
        border: 1px solid black;
        border-collapse: collapse;
        font-size:13px;
    }
    #dttable th, #dttable td {
        padding: 3px;
    }
    #dttable .trtotal {
        font-weight: bold;
    }
    #dttable .tdijenis {
        padding-left : 25px;
    }
    </style>
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
    </script>


</HTML>

<?PHP
hapusdata:
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp04");
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp05");
    mysqli_close($cnms);
?>