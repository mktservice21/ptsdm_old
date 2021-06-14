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

//Sales
if ($pincfrom=="GSM" OR $pincfrom=="ALL") {
    $query = "select 'MR' as sts, karyawanid, sum(value_sales) as value_sales, sum(value_target) as value_target 
        from ms.sales_mr where bulan between '$pbln1' AND '$pbln2' ";
    $query .=" Group By 1,2";
    if (empty($pjabatan) OR $pjabatan=="MR") {
    }else{
        $query .=" AND karyawanid='NON NONE'";
    }
    $query = "CREATE TEMPORARY TABLE $tmp03 ($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

    if (empty($pjabatan) OR $pjabatan=="AM") {
        $query = "INSERT INTO $tmp03 (sts, karyawanid, value_sales, value_target)
            select 'AM' as sts, karyawanid, sum(value_sales) as value_sales, sum(value_target) as value_target 
            from ms.sales_am where bulan between '$pbln1' AND '$pbln2' Group By 1,2";
        mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    }

    if (empty($pjabatan) OR $pjabatan=="DM") {
        $query = "INSERT INTO $tmp03 (sts, karyawanid, value_sales, value_target)
            select 'DM' as sts, karyawanid, sum(value_sales) as value_sales, sum(value_target) as value_target 
            from ms.sales_dm where bulan between '$pbln1' AND '$pbln2' Group By 1,2";
        mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    }
}
//END Sales


$query = "select a.sts, a.karyawanid, b.nama as nama_karyawan, sum(a.sales) as sales, sum(a.`target`) as `target`, 
    sum(incentive) as incentive, CAST(0 as DECIMAL(20,2)) as ach FROM $tmp01 as a JOIN ms.karyawan as b on a.karyawanid=b.karyawanid";
$query .=" GROUP BY 1,2,3";
$query = "CREATE TEMPORARY TABLE $tmp02 ($query)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }


$tmp06 ="dbtemp.tmprptincrkp06_".$puser."_$now$milliseconds";

$query = "select b.divprodid, sum(incentive) as incentive "
        . " FROM $tmp01 as a JOIN ms.jenisincentivepm as b on a.jenis=b.jenis";
$query .=" GROUP BY 1";
$query = "CREATE TEMPORARY TABLE $tmp06 ($query)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }


//update sales
if ($pincfrom=="GSM" OR $pincfrom=="ALL") {
    $query = "UPDATE $tmp02 as a JOIN $tmp03 as b on a.karyawanid=b.karyawanid AND a.sts=b.sts SET 
        a.sales=b.value_sales, a.`target`=b.value_target";
    mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
}

$query = "UPDATE $tmp02 SET ach=CASE WHEN IFNULL(`target`,0)=0 THEN 0 ELSE IFNULL(`sales`,0)/IFNULL(`target`,0)*100 END";
mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

?>


<HTML>
<HEAD>
    <title>Rekap Incentive</title>
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

    echo "<br/>";
}

echo "<table id='dttable' border='1' cellspacing='0' cellpadding='1' width='100%'>";
    echo "<thead>";
    echo "<tr>";
        $header_ = add_space('Karyawan',40);
        echo "<th align='center'>No</th>";
        echo "<th align='center'>Karyawan</th>";
        echo "<th align='center'>Jabatan</th>";
        if ($pincfrom=="GSM" OR $pincfrom=="ALL") {
            echo "<th align='center'>Sales</th>";
            echo "<th align='center'>Target</th>";
            echo "<th align='center'>Ach</th>";
        }
        echo "<th align='center'>Incentive</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
        $no=1;
        $ptotalinc=0;
        $query = "select * from  $tmp02 order by nama_karyawan, karyawanid, sts";
        $tampil= mysqli_query($cnms, $query);
        while ($row= mysqli_fetch_array($tampil)) {
            $nkryid=$row['karyawanid'];
            $nkrynm=$row['nama_karyawan'];
            $njabatan=$row['sts'];

            $psales=$row['sales'];
            $ptarget=$row['target'];
            $pach=$row['ach'];
            $pincentive=$row['incentive'];

            $ptotalinc=(DOUBLE)$ptotalinc+(DOUBLE)$pincentive;

            if ($ppilihrpt!="excel") {
                $psales=number_format($psales,0,",",",");
                $ptarget=number_format($ptarget,0,",",",");
                $pincentive=number_format($pincentive,0,",",",");
            }


            echo "<tr>";
            echo "<td nowrap>$no</td>";
            echo "<td nowrap>$nkrynm</td>";
            echo "<td nowrap>$njabatan</td>";
            if ($pincfrom=="GSM" OR $pincfrom=="ALL") {
                echo "<td nowrap align='right'>$psales</td>";
                echo "<td nowrap align='right'>$ptarget</td>";
                echo "<td nowrap align='right'>$pach</td>";
            }
            echo "<td nowrap align='right'>$pincentive</td>";
            echo "</tr>";

            $no++;
        }

        if ($ppilihrpt!="excel") {
            $ptotalinc=number_format($ptotalinc,0,",",",");
        }
        
        if ($pincfrom=="GSM" OR $pincfrom=="ALL") {
            echo "<tr class='trtotal'>";
            echo "<td nowrap colspan='6' align='center'>GRAND TOTAL</td>";
            echo "<td nowrap align='right'>$ptotalinc</td>";
        }else{
            echo "<tr class='trtotal'>";
            echo "<td nowrap colspan='3' align='center'>GRAND TOTAL</td>";
            echo "<td nowrap align='right'>$ptotalinc</td>";
        }
        echo "</tr>";


    echo "</tbody>";


echo "</table>";
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
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp06");
    mysqli_close($cnms);
?>