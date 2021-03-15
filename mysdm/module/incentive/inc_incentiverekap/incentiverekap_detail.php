<?PHP

$query = "select 'MR' as sts, karyawanid, jenis, sales, `target`, ach, incentive 
from ms.incentive_mr where bulan between '$pbln1' AND '$pbln2' ";
if ($pincfrom=="GSM") $query .= " AND IFNULL(jenis2,'')='GSM' ";
elseif ($pincfrom=="PM") $query .= " AND IFNULL(jenis2,'') Not In ('GSM', '') ";
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
elseif ($pincfrom=="PM") $query .= " AND IFNULL(jenis2,'') Not In ('GSM', '') ";
mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
}

if (empty($pjabatan) OR $pjabatan=="DM") {
$query = "INSERT INTO $tmp01 (sts, karyawanid, jenis, sales, `target`, ach, incentive)
    select 'DM' as sts, karyawanid, jenis, sales, `target`, ach, incentive 
    from ms.incentive_dm where bulan between '$pbln1' AND '$pbln2' ";
if ($pincfrom=="GSM") $query .= " AND IFNULL(jenis2,'')='GSM' ";
elseif ($pincfrom=="PM") $query .= " AND IFNULL(jenis2,'') Not In ('GSM', '') ";
mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
}

$query = "DELETE FROM $tmp01 WHERE IFNULL(incentive,0)=0";
mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }


$query = "select a.*, b.nama as nama_karyawan from $tmp01 as a JOIN ms.karyawan as b on a.karyawanid=b.karyawanid";
$query = "CREATE TEMPORARY TABLE $tmp02 ($query)";
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


                $pincentive=number_format($pincentive,0,",",",");

                echo "<tr>";
                echo "<td nowrap class='tdijenis'>$pnmjenis</td>";
                echo "<td nowrap align='right'>$pincentive</td>";
                echo "</tr>";

            }


            $no++;
        }

        $ptotincjbt=number_format($ptotincjbt,0,",",",");

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

        $pgrandtotinc=number_format($pgrandtotinc,0,",",",");

        echo "<tr class='trtotal'>";
        echo "<td nowrap class='tdijenis'>Grand Total </td>";
        echo "<td nowrap align='right'>$pgrandtotinc</td>";
        echo "</tr>";
    }

    echo "</tbody>";
echo "</table>";

echo "<br/><br/>";

echo "<div><b>Approve</b></div>";

echo "<table id='dttable' border='1' cellspacing='0' cellpadding='1'>";
echo "<tr><th>Nama</th><th>Status Approve</th><th>Tgl. Approve</th></tr>";
$query = "select a.karyawanid, b.nama, a.status as sts, date_format(a.sys_time,'%d/%m/%Y %H:%i:%s') as sys_time 
    from ms.approve_insentif as a JOIN ms.karyawan as b on a.karyawanid=b.karyawanid 
    WHERE LEFT(bulan,7)='$pfbln' order by a.sys_time";
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
    mysqli_close($cnms);
?>