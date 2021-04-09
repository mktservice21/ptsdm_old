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
$tmp01 =" dbtemp.tmprptmntpln01_".$puserid."_$now ";
$tmp02 =" dbtemp.tmprptmntpln02_".$puserid."_$now ";
$tmp03 =" dbtemp.tmprptmntpln03_".$puserid."_$now ";
$tmp04 =" dbtemp.tmprptmntpln04_".$puserid."_$now ";


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



$sql = "select a.idinput, a.karyawanid, a.jabatanid, a.tanggal, a.ketid, b.nama as nama_ket,
    b.pointMR, b.pointSpv, b.pointDM,
    c.jenis, c.dokterid, d.namalengkap, d.gelar, d.spesialis 
    FROM hrd.dkd_new0 as a JOIN hrd.ket as b on a.ketid=b.ketId 
    JOIN hrd.dkd_new1 as c on a.idinput=c.idinput
    LEFT JOIN dr.masterdokter as d on c.dokterid=d.id
    WHERE a.karyawanid='$pkryid'";
$sql .=" AND LEFT(a.tanggal,7)= '$pbulan'";
$query = "create TEMPORARY table $tmp01 ($sql)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "ALTER TABLE $tmp01 ADD COLUMN real_user VARCHAR(10)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "create TEMPORARY table $tmp04 (select * from $tmp01)"; 
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$sql = "select a.nourut as idinput, a.karyawanid, '' as jabatanid, a.tanggal, '' as ketid, '' as nama_ket,
    '0' as pointMR, '0' as pointSpv, '0' as pointDM,
    a.jenis, a.dokterid, d.namalengkap, d.gelar, d.spesialis 
    FROM hrd.dkd_new_real1 as a 
    LEFT JOIN dr.masterdokter as d on a.dokterid=d.id
    WHERE a.karyawanid='$pkryid'";
$sql .=" AND LEFT(a.tanggal,7)= '$pbulan'";
$query = "create TEMPORARY table $tmp03 ($sql)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "INSERT INTO $tmp01 (idinput, karyawanid, jabatanid, tanggal, ketid, nama_ket, "
        . " pointMR, pointSpv, pointDM, "
        . " jenis, dokterid, namalengkap, gelar, spesialis, real_user)"
        . " SELECT idinput, karyawanid, jabatanid, tanggal, ketid, nama_ket, "
        . " pointMR, pointSpv, pointDM, "
        . " jenis, dokterid, namalengkap, gelar, spesialis, karyawanid as real_user "
        . " FROM $tmp03 WHERE CONCAT(karyawanid,dokterid,tanggal) NOT IN "
        . " (select DISTINCT IFNULL(CONCAT(karyawanid,dokterid,tanggal),'') FROM $tmp04)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 as a JOIN $tmp03 as b on a.karyawanid=b.karyawanid AND a.dokterid=b.dokterid AND a.tanggal=b.tanggal SET "
        . " a.real_user=a.karyawanid, a.jenis=b.jenis WHERE IFNULL(a.jenis,'') NOT IN ('EC')";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }



$query = "select a.jabatanid as jabatanid, b.nama as nama_jabatan from $tmp01 as a 
    LEFT join hrd.jabatan as b on a.jabatanid=b.jabatanId ";
$tampilk=mysqli_query($cnmy, $query);
$rowk=mysqli_fetch_array($tampilk);
$pnamajabatan=$rowk['nama_jabatan'];
$pjabatanid=$rowk['jabatanid'];

$query = "UPDATE $tmp01 SET jenis='VR' WHERE IFNULL(jenis,'')='' AND IFNULL(real_user,'')<>''";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }




$sql = "select distinct dokterid, namalengkap, gelar, spesialis FROM $tmp01";
$query = "create TEMPORARY table $tmp02 ($sql)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$lcfieldtambah="";
for($ix=1; $ix<=(INT)$ptgl02;$ix++) {
    $pnmfield="t_".$ix;
    $lcfieldtambah .="ADD COLUMN ".$pnmfield." VARCHAR(5),";
}

if (!empty($lcfieldtambah)) {
    $lcfieldtambah .="ADD COLUMN jml VARCHAR(5)";
    
    $query = "Alter Table $tmp02 $lcfieldtambah"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
}

for($ix=1; $ix<=(INT)$ptgl02;$ix++) {
    $pnmfield="t_".$ix;
    $pntgl=$ix;
    if (strlen($pntgl)<=1) $pntgl="0".$ix;
    
    $query = "UPDATE $tmp02 as a JOIN (select distinct dokterid, jenis from $tmp01 WHERE DATE_FORMAT(tanggal,'%d')='$pntgl') as b on "
            . " IFNULL(a.dokterid,'')=IFNULL(b.dokterid,'') SET "
            . " a.".$pnmfield."=CASE WHEN IFNULL(b.jenis,'')='' THEN 'VS' ELSE b.jenis END";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
}
?>

<HTML>
<HEAD>
  <TITLE>Report Monthly Plan By Dokter</TITLE>
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
    echo "<b>Report Monthly Plan By Dokter</b><br/>";
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
    
    
    echo "<table id='tbltable' border='1' cellspacing='0' cellpadding='1'>";
        echo "<thead>";
            echo "<tr>";
                echo "<th align='center'><small>No</small></th>";
                echo "<th align='center'><small>Dokter</small></th>";
                for($ix=1; $ix<=(INT)$ptgl02;$ix++) {
                    $pntgl=$ix;
                    if (strlen($pntgl)<=1) $pntgl="0".$ix;

                    $phari = strtoupper(date('l', strtotime($nthn."-".$nbln."-".$pntgl)));
                         
                    $pcollibur="";
                    if ($phari=="SATURDAY") $pcollibur="style='background-color:#ff9999'";
                    elseif ($phari=="SUNDAY") $pcollibur="style='background-color:#ff3333'";
                            
                    echo "<th align='center' $pcollibur><small>$pntgl</small></th>";

                }
            echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
            $no=1;
            $query = "select * from $tmp02 order by namalengkap, dokterid";
            $tampil0=mysqli_query($cnmy, $query);
            while ($row0=mysqli_fetch_array($tampil0)) {
                $pdokterid=$row0['dokterid'];
                $pdokternm=$row0['namalengkap'];
                $pgelar=$row0['gelar'];
                $pspesialis=$row0['spesialis'];
                
                echo "<tr>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap>$pgelar $pdokternm $pspesialis</td>";
                for($ix=1; $ix<=(INT)$ptgl02;$ix++) {
                    $pntgl=$ix;
                    if (strlen($pntgl)<=1) $pntgl="0".$ix;
                    
                    $pfield="t_".$ix;
                    $pketjenis=$row0[$pfield];
                    
                    
                    $pbckcolor=$pcolor0;
                    if ($pketjenis=="VS") $pbckcolor=$pcolor1;
                    elseif ($pketjenis=="VR") $pbckcolor=$pcolor2;
                    elseif ($pketjenis=="JV") $pbckcolor=$pcolor3;
                    elseif ($pketjenis=="EC") $pbckcolor=$pcolor4;
                    else{ 
                        if (!empty($pketjenis)) {
                            $pbckcolor=$pcolor5;
                        }
                    }
                    echo "<td nowrap $pbckcolor>&nbsp;</td>";

                }
                echo "</tr>";
                
                $no++;
                
            }
            
        echo "</tbody>";
    echo "</table>";
    
    echo "<br/><br/><br/>";
    

    
                    
    echo "<table id='tblket' border='1' cellspacing='0' cellpadding='1'>";
        echo "<tr>";
            echo "<td colspan='2'>Keterangan : </td>";
        echo "</tr>";
        
        echo "<tr>";
            echo "<td $pcolor0>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
            echo "<td>tidak ada visit</td>";
        echo "</tr>";
        
        echo "<tr>";
            echo "<td $pcolor1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
            echo "<td>visit belum relisasi</td>";
        echo "</tr>";
        
        echo "<tr>";
            echo "<td $pcolor2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
            echo "<td>visit sudah relisasi</td>";
        echo "</tr>";
        
        echo "<tr>";
            echo "<td $pcolor3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
            echo "<td>join visit</td>";
        echo "</tr>";
        
        echo "<tr>";
            echo "<td $pcolor4>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
            echo "<td>extra call</td>";
        echo "</tr>";
        
        echo "<tr>";
            echo "<td $pcolor5>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
            echo "<td>others</td>";
        echo "</tr>";
        
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
    mysqli_close($cnmy);
?>