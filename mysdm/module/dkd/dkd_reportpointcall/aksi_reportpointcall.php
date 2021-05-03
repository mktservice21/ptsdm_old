<?PHP

date_default_timezone_set('Asia/Jakarta');
ini_set("memory_limit","512M");
ini_set('max_execution_time', 0);

session_start();
if (!isset($_SESSION['USERID'])) {
    echo "ANDA HARUS LOGIN ULANG....";
    exit;
}

$pmodule=$_GET['module'];

include("config/koneksimysqli.php");
include("config/common.php");


$puserid=$_SESSION['USERID'];
$now=date("mdYhis");
$tmp01 =" dbtemp.tmprptpoincal01_".$puserid."_$now ";
$tmp02 =" dbtemp.tmprptpoincal02_".$puserid."_$now ";
$tmp03 =" dbtemp.tmprptpoincal03_".$puserid."_$now ";
$tmp04 =" dbtemp.tmprptpoincal04_".$puserid."_$now ";
$tmp05 =" dbtemp.tmprptpoincal05_".$puserid."_$now ";


$pkryid = $_POST['cb_karyawan']; 
$pbln = $_POST['e_bulan'];
$ptanggal = date('Y-m-01', strtotime($pbln));

$ptgl01 = "01";
$ptgl02 = date('t', strtotime($ptanggal));
$nbln = date('m', strtotime($ptanggal));
$nthn = date('Y', strtotime($ptanggal));
$pbulan = date('Y-m', strtotime($ptanggal));
$pperiode = date('F Y', strtotime($ptanggal));


$query = "select a.nama, a.jabatanId as jabatanid, b.nama as nama_jabatan from hrd.karyawan as a 
    LEFT join hrd.jabatan as b on a.jabatanId=b.jabatanId 
    where a.karyawanid='$pkryid'";
$tampilk=mysqli_query($cnmy, $query);
$rowk=mysqli_fetch_array($tampilk);
$pnamakarywanpl=$rowk['nama'];
$pnamajabatan=$rowk['nama_jabatan'];



$sql = "select a.idinput, a.karyawanid, a.jabatanid, a.tanggal, a.ketid, b.nama as nama_ket,
    b.pointMR, b.pointSpv, b.pointDM 
    FROM hrd.dkd_new0 as a LEFT JOIN hrd.ket as b on a.ketid=b.ketId 
    WHERE a.karyawanid='$pkryid'";
$sql .=" AND LEFT(a.tanggal,7)= '$pbulan'";
$query = "create TEMPORARY table $tmp01 ($sql)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$sql = "select a.idinput, a.karyawanid, a.jabatanid, a.tanggal, 
    a.jenis, a.dokterid, b.namalengkap, b.gelar, b.spesialis 
    FROM hrd.dkd_new1 as a 
    LEFT JOIN dr.masterdokter as b on a.dokterid=b.id
    WHERE a.karyawanid='$pkryid'";
$sql .=" AND LEFT(a.tanggal,7)= '$pbulan'";
$query = "create TEMPORARY table $tmp02 ($sql)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$sql = "select a.idinput, a.karyawanid, a.tanggal, a.ketid, b.nama as nama_ket,
    b.pointMR, b.pointSpv, b.pointDM 
    FROM hrd.dkd_new_real0 as a LEFT JOIN hrd.ket as b on a.ketid=b.ketId 
    WHERE a.karyawanid='$pkryid'";
$sql .=" AND LEFT(a.tanggal,7)= '$pbulan'";
$query = "create TEMPORARY table $tmp03 ($sql)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$sql = "select a.idinput, a.karyawanid,a.tanggal, 
    a.jenis, a.dokterid, b.namalengkap, b.gelar, b.spesialis 
    FROM hrd.dkd_new_real1 as a 
    LEFT JOIN dr.masterdokter as b on a.dokterid=b.id
    WHERE a.karyawanid='$pkryid'";
$sql .=" AND LEFT(a.tanggal,7)= '$pbulan'";
$query = "create TEMPORARY table $tmp04 ($sql)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }



//$sql = "select distinct dokterid, namalengkap, gelar, spesialis FROM $tmp01";
$sql = "idket INT(5), nama_ket varchar(100)";
$query = "create TEMPORARY table $tmp05 ($sql)"; 
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

unset($pinsert_data);//kosongkan array
$pinsert_data[] = "('1', 'Aktivitas')";
$pinsert_data[] = "('2', 'Call')";

$query = "INSERT INTO $tmp05 (idket, nama_ket) values ".implode(', ', $pinsert_data);
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$lcfieldtambah="";
for($ix=1; $ix<=(INT)$ptgl02;$ix++) {
    $pnmfield="t_".$ix;
    $lcfieldtambah .="ADD COLUMN ".$pnmfield." VARCHAR(5),";
}

if (!empty($lcfieldtambah)) {
    $lcfieldtambah .="ADD COLUMN jml VARCHAR(5)";
    
    $query = "Alter Table $tmp05 $lcfieldtambah"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
}

for($ix=1; $ix<=(INT)$ptgl02;$ix++) {
    $pnmfield="t_".$ix;
    $pntgl=$ix;
    if (strlen($pntgl)<=1) $pntgl="0".$ix;
    
    $query = "UPDATE $tmp05 as a JOIN (select '1' as idket, count(distinct ketid) as jml from $tmp01 WHERE DATE_FORMAT(tanggal,'%d')='$pntgl' GROUP BY 1) as b on "
            . " IFNULL(a.idket,'')=IFNULL(b.idket,'') SET "
            . " a.".$pnmfield."=b.jml WHERE a.idket='1'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
    
    $query = "UPDATE $tmp05 as a JOIN (select '2' as idket, count(distinct dokterid) as jml from $tmp02 "
            . " WHERE IFNULL(jenis,'') NOT IN ('JV') AND DATE_FORMAT(tanggal,'%d')='$pntgl' GROUP BY 1) as b on "
            . " IFNULL(a.idket,'')=IFNULL(b.idket,'') SET "
            . " a.".$pnmfield."=b.jml WHERE a.idket='2'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
}

?>

<HTML>
<HEAD>
  <TITLE>Monthly Report Point & Call</TITLE>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2030 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <link rel="shortcut icon" href="images/icon.ico" />
    <style> .str{ mso-number-format:\@; } </style>
</HEAD>
<script>
</script>

<BODY onload="initVar()">
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
    <?PHP
    echo "<b>Monthly Report Point & Call</b><br/>";
    echo "<b>Periode : $pperiode</b><br/>";
    echo "<b>Nama : $pnamakarywanpl - $pkryid</b><br/>";
    echo "<b>Jabatan : $pnamajabatan</b><br/>";
    echo "<hr/><br/>";
    
    $pcolor0="";//tidak ada visit
    $pcolor1="style='background-color:#ccff33'";//ada visit belum realisasi (VS)
    $pcolor2="style='background-color:#009900'";//ada visit sudah realisasi (VR)
    $pcolor3="style='background-color:#ff9900'";//join visit (JV)
    $pcolor4="style='background-color:#009999'";//extra call (EC)
    $pcolor5="style='background-color:#000066'";
    
    for($ix=1; $ix<=(INT)$ptgl02;$ix++) {
        $pgrtotal[$ix]=0;
    }
    
    echo "<table id='tbltable' border='1' cellspacing='0' cellpadding='1'>";
        echo "<thead>";
            echo "<tr>";
                echo "<th align='center'><small>No</small></th>";
                echo "<th align='center'><small>Keterangan</small></th>";
                for($ix=1; $ix<=(INT)$ptgl02;$ix++) {
                    $pntgl=$ix;
                    if (strlen($pntgl)<=1) $pntgl="0".$ix;

                    $phari = strtoupper(date('l', strtotime($nthn."-".$nbln."-".$pntgl)));
                         
                    $pcollibur="";
                    if ($phari=="SATURDAY") $pcollibur="style='background-color:#ff9999'";
                    elseif ($phari=="SUNDAY") $pcollibur="style='background-color:#ff3333'";
                            
                    echo "<th align='center' $pcollibur><small>$pntgl</small></th>";

                }
                echo "<th align='center'><small>Jumlah</small></th>";
            echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
            $no=1;
            $query = "select * from $tmp05 order by nama_ket, idket";
            $tampil0=mysqli_query($cnmy, $query);
            while ($row0=mysqli_fetch_array($tampil0)) {
                $pketid=$row0['idket'];
                $pketnm=$row0['nama_ket'];
                
                echo "<tr>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap>$pketnm</td>";
                
                $pjmlvisit=0;
                for($ix=1; $ix<=(INT)$ptgl02;$ix++) {
                    $pntgl=$ix;
                    if (strlen($pntgl)<=1) $pntgl="0".$ix;
                    
                    $pfield="t_".$ix;
                    $pketjenis=$row0[$pfield];
                    
                    echo "<td nowrap >$pketjenis</td>";
                    
                    if (empty($pketjenis)) $pketjenis="0";
                    $pjmlvisit=(INT)$pjmlvisit+(INT)$pketjenis;
                    
                    $pgrtotal[$ix]=(INT)$pgrtotal[$ix]+(INT)$pketjenis;
                }
                echo "<td nowrap align='right'>$pjmlvisit</td>";
                echo "</tr>";
                
                $no++;
                
            }
            
            
            echo "<tr style='font-weight:bold;'>";
            echo "<td nowrap>&nbsp;</td>";
            echo "<td nowrap>Total</td>";
            
            $pjmlvisit=0;
            for($ix=1; $ix<=(INT)$ptgl02;$ix++) {
                $pntgl=$ix;
                $pketjenis=$pgrtotal[$ix];
                if ((INT)$pketjenis==0) $pketjenis="";
                
                echo "<td nowrap >$pketjenis</td>";
                
                if (empty($pketjenis)) $pketjenis="0";
                $pjmlvisit=(INT)$pjmlvisit+(INT)$pketjenis;
            }
                
            echo "<td nowrap align='right'>$pjmlvisit</td>";
            echo "</tr>";
                
        echo "</tbody>";
    echo "</table>";

    
    echo "<br/><br/><br/><br/><br/>";
    ?>
</BODY>

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
    
    <style>
        #tbltable {
            border-collapse: collapse;
        }
        th {
            font-size : 16px;
            padding:5px;
            background-color: #ccccff;
        }
        tr td {
            font-size : 12px;
        }
        tr td {
            padding : 3px;
        }
        tr:hover {background-color:#f5f5f5;}
        thead tr:hover {background-color:#cccccc;}
    </style>
    
</HTML>
<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp03");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp04");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp05");
    mysqli_close($cnmy);
?>