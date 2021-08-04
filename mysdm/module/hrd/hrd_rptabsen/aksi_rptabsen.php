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
$tmp01 =" dbtemp.tmprptabsen01_".$puserid."_$now ";
$tmp02 =" dbtemp.tmprptabsen02_".$puserid."_$now ";
$tmp03 =" dbtemp.tmprptabsen03_".$puserid."_$now ";
$tmp04 =" dbtemp.tmprptabsen04_".$puserid."_$now ";


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
$pnmjbtkarywanpl=$rowk['nama_jabatan'];


$sql = "select karyawanid, kode_absen, tanggal, jam FROM hrd.t_absen "
        . " WHERE 1=1 ";
if (!empty($pkryid)) $sql .=" AND karyawanid='$pkryid'";
$sql .=" AND LEFT(tanggal,7)= '$pbulan'";
$query = "create TEMPORARY table $tmp01 ($sql)";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "ALTER TABLE $tmp01 ADD COLUMN nama_karyawan VARCHAR(100), ADD COLUMN nama_absen VARCHAR(100), ADD COLUMN cuti_masal VARCHAR(1) DEFAULT 'N', "
        . " ADD COLUMN libur VARCHAR(1) DEFAULT 'N'";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 as a JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId SET a.nama_karyawan=b.nama";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 as a JOIN hrd.t_absen_kode as b on a.kode_absen=b.kode_absen SET a.nama_absen=b.nama_absen";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


//CUSTI MASAL

$query = "SELECT DISTINCT b.tanggal FROM hrd.t_cuti0 as a "
        . " JOIN hrd.t_cuti1 as b on a.idcuti=b.idcuti where a.id_jenis='00' "
        . " AND a.karyawanid IN ('ALL', 'ALLHO') AND IFNULL(a.stsnonaktif,'')<>'Y' "
        . " AND LEFT(b.tanggal,7)= '$pbulan'";
$query = "create TEMPORARY table $tmp03 ($query)";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "UPDATE $tmp01 as a JOIN $tmp03 as b on a.tanggal=b.tanggal SET a.cuti_masal='Y'";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//END CUSTI MASAL


//LIBUR
$query = "CREATE TEMPORARY TABLE $tmp04 (tanggal DATE, libur VARCHAR(1) DEFAULT 'N')";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

unset($pinsert_data_detail);//kosongkan array
$psimpandata=false;
for($ix=1; $ix<=(INT)$ptgl02;$ix++) {
    $pntgl=$ix;
    if (strlen($pntgl)<=1) $pntgl="0".$ix;

    $phari = strtoupper(date('l', strtotime($nthn."-".$nbln."-".$pntgl)));
    
    $npltanggal=$pbulan."-".$pntgl;
    
    $pcollibur="";
    $plibur="N";
    if ($phari=="SATURDAY") { $plibur="Y"; $pcollibur="style='background-color:#ff9999'"; }
    elseif ($phari=="SUNDAY") { $plibur="Y";$pcollibur="style='background-color:#ff3333'"; }
    
    $pinsert_data_detail[] = "('$npltanggal', '$plibur')";
    
    $psimpandata=true;
    //echo "$pntgl : $phari dan $npltanggal<br/>";

}
if ($psimpandata==true) {
    $query = "INSERT INTO $tmp04 (tanggal, libur) VALUES ".implode(', ', $pinsert_data_detail);
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) {  goto hapusdata; }
}

$query = "UPDATE $tmp01 as a JOIN $tmp04 as b on a.tanggal=b.tanggal SET a.libur=b.libur";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//END LIBUR

$query ="select distinct karyawanid, nama_karyawan FROM $tmp01";
$query = "create TEMPORARY table $tmp02 ($query)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "ALTER TABLE $tmp02 ADD COLUMN jmasuk INT(4), ADD COLUMN jterlambat INT(4), ADD COLUMN jistirahat INT(4), ADD COLUMN jmasuk_ist INT(4), "
        . " ADD COLUMN jpulang INT(4), ADD COLUMN jabsenlibur INT(4)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//absen masuk
$query = "UPDATE $tmp02 as a JOIN (SELECT karyawanid, COUNT(DISTINCT tanggal) as jumlah FROM $tmp01 WHERE kode_absen='1' AND IFNULL(cuti_masal,'')<>'Y' AND IFNULL(libur,'')<>'Y' GROUP BY 1) as b "
        . " on a.karyawanid=b.karyawanid SET a.jmasuk=b.jumlah";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//absen masuk hari libur dan cuti masal
$query = "UPDATE $tmp02 as a JOIN (SELECT karyawanid, COUNT(DISTINCT tanggal) as jumlah FROM $tmp01 WHERE kode_absen='1' AND (IFNULL(cuti_masal,'')='Y' OR IFNULL(libur,'')='Y') GROUP BY 1) as b "
        . " on a.karyawanid=b.karyawanid SET a.jabsenlibur=b.jumlah";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//absen pulang
$query = "UPDATE $tmp02 as a JOIN (SELECT karyawanid, COUNT(DISTINCT tanggal) as jumlah FROM $tmp01 WHERE kode_absen='2' AND IFNULL(cuti_masal,'')<>'Y' AND IFNULL(libur,'')<>'Y' GROUP BY 1) as b "
        . " on a.karyawanid=b.karyawanid SET a.jpulang=b.jumlah";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//absen istirahat
$query = "UPDATE $tmp02 as a JOIN (SELECT karyawanid, COUNT(DISTINCT tanggal) as jumlah FROM $tmp01 WHERE kode_absen='3' AND IFNULL(cuti_masal,'')<>'Y' AND IFNULL(libur,'')<>'Y' GROUP BY 1) as b "
        . " on a.karyawanid=b.karyawanid SET a.jistirahat=b.jumlah";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//absen masuk istirahat
$query = "UPDATE $tmp02 as a JOIN (SELECT karyawanid, COUNT(DISTINCT tanggal) as jumlah FROM $tmp01 WHERE kode_absen='4' AND IFNULL(cuti_masal,'')<>'Y' AND IFNULL(libur,'')<>'Y' GROUP BY 1) as b "
        . " on a.karyawanid=b.karyawanid SET a.jmasuk_ist=b.jumlah";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


?>

<HTML>
<HEAD>
  <TITLE>Report Absensi</TITLE>
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
    echo "<b>Report Absensi</b><br/>";
    echo "<b>Periode : $pperiode</b><br/>";
    if (!empty($pkryid)) {
        echo "<b>Nama : $pnamakarywanpl - $pkryid</b><br/>";
        echo "<b>Jabatan : $pnmjbtkarywanpl</b><br/>";
    }
    echo "<hr/><br/>";
    
    echo "<table id='tbltable' border='1' cellspacing='0' cellpadding='1'>";
        echo "<thead>";
            echo "<tr>";
                echo "<th align='center' rowspan='2'><small>No</small></th>";
                echo "<th align='center' rowspan='2'><small>Karyawan</small></th>";
                echo "<th align='center' colspan='7'><small>Jumlah Absen</small></th>";
            echo "</tr>";
            
            echo "<tr>";
                echo "<th align='center'><small>Masuk</small></th>";
                echo "<th align='center'><small>Terlambat</small></th>";
                echo "<th align='center'><small>Istirahat</small></th>";
                echo "<th align='center'><small>Masuk Istirahat</small></th>";
                echo "<th align='center'><small>Pulang</small></th>";
                echo "<th align='center'><small>Tidak Masuk</small></th>";
                echo "<th align='center'><small>Masuk Hari Libur</small></th>";
            echo "</tr>";
            
        echo "</thead>";
        
        echo "<tbody>";
        
            $no=1;
            $query = "select * from $tmp02 order by nama_karyawan, karyawanid";
            $tampil0=mysqli_query($cnmy, $query);
            while ($row0=mysqli_fetch_array($tampil0)) {
                $nkryid=$row0['karyawanid'];
                $nkrynm=$row0['nama_karyawan'];
                $njmlmasuk=$row0['jmasuk'];
                $njmlterlambat=$row0['jterlambat'];
                $njmlistirahat=$row0['jistirahat'];
                $njmlmskist=$row0['jmasuk_ist'];
                $njmlpulang=$row0['jpulang'];
                $njmllibur=$row0['jabsenlibur'];
                $njmltdkabsen="";
                
                $nnkryid=(INT)$nkryid;
                
                echo "<tr>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap>$nkrynm - $nnkryid</td>";
                echo "<td nowrap align='right'>$njmlmasuk</td>";
                echo "<td nowrap align='right'>$njmlterlambat</td>";
                echo "<td nowrap align='right'>$njmlistirahat</td>";
                echo "<td nowrap align='right'>$njmlmskist</td>";
                echo "<td nowrap align='right'>$njmlpulang</td>";
                echo "<td nowrap align='right'>$njmltdkabsen</td>";
                echo "<td nowrap align='right'>$njmllibur</td>";
                echo "</tr>";
                
                $no++;
            }
            
        echo "</tbody>";
        
    echo "</table>";
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