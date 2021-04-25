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
include "config/fungsi_ubahget_id.php";


$puserid=$_SESSION['USERID'];
$now=date("mdYhis");
$tmp01 =" dbtemp.tmprptcutikry01_".$puserid."_$now ";
$tmp02 =" dbtemp.tmprptcutikry02_".$puserid."_$now ";
$tmp03 =" dbtemp.tmprptcutikry03_".$puserid."_$now ";
$tmp04 =" dbtemp.tmprptcutikry04_".$puserid."_$now ";
$tmp05 =" dbtemp.tmprptcutikry05_".$puserid."_$now ";
$tmp06 =" dbtemp.tmprptcutikry06_".$puserid."_$now ";
$tmp07 =" dbtemp.tmprptcutikry07_".$puserid."_$now ";


$pkryid = $_POST['cb_karyawan'];  
$ptahun = $_POST['e_tahun'];
$ptahunsebelum=(INT)$ptahun-1;
$pnamajabatan="";

$nsjenisid="";
if (isset($_POST['chkbox_jenis'])) $nsjenisid = $_POST['chkbox_jenis'];

$fjenisid="";
if (!empty($nsjenisid)) {
    foreach ($nsjenisid as $npidjns) {
        //if (!empty($pbrandid)) {
            $fjenisid .="'".$npidjns."',";
        //}
    }
    if (!empty($fjenisid)) $fjenisid=" (".substr($fjenisid, 0, -1).") ";
}


//masa kerja
$pthnsistem = date("Y");
$pmasakerja=date("Y-m-d");
if ($ptahun!=$pthnsistem) {
    $pmasakerja=$ptahun."-12-31";
}


$query = "select a.nama, a.jabatanId as jabatanid, b.nama as nama_jabatan from hrd.karyawan as a 
    LEFT join hrd.jabatan as b on a.jabatanId=b.jabatanId 
    where a.karyawanid='$pkryid'";
$tampilk=mysqli_query($cnmy, $query);
$rowk=mysqli_fetch_array($tampilk);
$pnamakarywanpl=$rowk['nama'];
$pnamajabatan=$rowk['nama_jabatan'];

/*
$sql = "select a.*, b.potong_cuti from hrd.karyawan_cuti_close as a LEFT JOIN hrd.jenis_cuti as b "
        . " on a.id_jenis=b.id_jenis WHERE a.tahun='$ptahunsebelum' ";
if (!empty($pkryid)) $sql .=" AND a.karyawanid='$pkryid' ";

$query = "create TEMPORARY table $tmp01 ($sql)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
*/


$query = "select a.karyawanId as karyawanid, a.nama as nama_karyawan, a.tglmasuk, a.tglkeluar, a.skar, "
        . " a.jabatanId as jabatanid, b.nama as nama_jabatan, a.divisiId as divisiid "
        . " FROM hrd.karyawan as a LEFT JOIN hrd.jabatan as b on a.jabatanId=b.jabatanId ";
if (!empty($pkryid)) $query .=" AND a.karyawanid='$pkryid' ";
else{
    $query .= " AND (IFNULL(a.tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(a.tglkeluar,'')='') ";
    $query .=" AND LEFT(a.nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S')  "
        . " and LEFT(a.nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
        . " and LEFT(a.nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
        . " AND LEFT(a.nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P') "
        . " AND LEFT(a.nama,6) NOT IN ('SBYTO-', 'MR SBY') ";
}
$query = "create TEMPORARY table $tmp01 ($query)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "select DISTINCT a.*, c.potong_cuti, c.nama_jenis FROM hrd.t_cuti0 as a "
        . " LEFT JOIN hrd.t_cuti1 as b on a.idcuti=b.idcuti "
        . " LEFT JOIN hrd.jenis_cuti as c on a.id_jenis=c.id_jenis "
        . " WHERE IFNULL(a.stsnonaktif,'')<>'Y' ";
//if (!empty($fjenisid)) $query .=" AND a.id_jenis IN $fjenisid ";
$query .=" AND ( (YEAR(b.tanggal) = '$ptahun') "
        . " OR (YEAR(a.bulan1) = '$ptahun') OR (YEAR(a.bulan2) = '$ptahun') "
        . " )";
if (!empty($pkryid)) $query .=" AND ( a.karyawanid='$pkryid' OR a.karyawanid IN ('ALL', 'ALLETH', 'ALLHO', 'ALLCHC') ) ";
$query = "create TEMPORARY table $tmp02 ($query)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "select b.karyawanid, b.id_jenis, b.keperluan, b.potong_cuti, a.* "
        . " from hrd.t_cuti1 as a JOIN $tmp02 as b on a.idcuti=b.idcuti ";
$query = "create TEMPORARY table $tmp03 ($query)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


//ada di tabel cuti tapi tidak ada di tabel tarikan karyawan, karena sudah nonaktif
$query = "create TEMPORARY table $tmp04 (select distinct karyawanid from $tmp01)"; 
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "INSERT INTO $tmp01 (karyawanid, nama_karyawan, tglmasuk, tglkeluar, skar, jabatanid, nama_jabatan, divisiid)"
        . " SELECT distinct a.karyawanId as karyawanid, b.nama, b.tglmasuk, b.tglkeluar, b.skar, "
        . " b.jabatanId as jabatanid, c.nama as nama_jabatan, b.divisiId as divisiid "
        . " FROM $tmp02 as a JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId "
        . " LEFT JOIN hrd.jabatan as c on b.jabatanId=c.jabatanId "
        . " WHERE a.karyawanid NOT IN (select distinct karyawanid from $tmp04)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp04");
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
// END cek data karyawan

//CUTI MASSAL
$query = "select distinct a.karyawanid, a.keperluan, a.id_jenis, b.tanggal, a.nama_jenis from $tmp02 as a "
        . " JOIN $tmp03 as b on a.idcuti=b.idcuti WHERE a.karyawanid IN ('ALL', 'ALLETH', 'ALLHO', 'ALLCHC')";
$query = "create TEMPORARY table $tmp04 ($query)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "DELETE $tmp02, $tmp03 FROM $tmp02 JOIN $tmp03 on $tmp02.idcuti=$tmp03.idcuti WHERE $tmp02.karyawanid IN ('ALL', 'ALLETH', 'ALLHO', 'ALLCHC')";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
//END CUTI MASSAL


$query = "select a.*, b.nama_karyawan, b.tglmasuk, b.tglkeluar, b.tglkeluar as tglmasakerja, b.nama_jabatan, b.divisiid "
        . " from $tmp02 as a "
        . " LEFT JOIN $tmp01 as b on a.karyawanid=b.karyawanid ";
$query = "create TEMPORARY table $tmp05 ($query)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "SELECT karyawanid, divisiid, jabatanid, tglmasuk, tglkeluar, "
        . " IFNULL(TIMESTAMPDIFF(YEAR, tglmasuk, '$pmasakerja'),0) AS jml_thn, "
        . " IFNULL(TIMESTAMPDIFF(MONTH, tglmasuk, '$pmasakerja'),0) AS jml_bln "
        . " FROM $tmp05";
$query = "create TEMPORARY table $tmp06 ($query)";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "update $tmp06 set tglkeluar='0000-00-00' WHERE YEAR(tglkeluar)>'$ptahun'";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "alter table $tmp06 add column jml_tambah INT (4)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "select distinct karyawanid, divisiid, jabatanid, tglmasuk, tglkeluar, jml_thn, jml_bln, id_jenis, nama_jenis, potong_cuti FROM $tmp06, hrd.jenis_cuti"; 
$query = "create TEMPORARY table $tmp07 ($query)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "alter table $tmp07 add column jumlah INT(4), add column jml_cuti INT (4), add column sisa_cuti INT (4)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp07 SET jumlah='12' WHERE id_jenis='01' AND IFNULL(jml_thn,0)>=1";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp07 SET jumlah=jml_bln WHERE id_jenis='01' AND IFNULL(jml_thn,0)=0 AND IFNULL(jml_bln,0)>1 AND IFNULL(jml_bln,0)<=12";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query ="SELECT id_jenis, dari, sampai, ifnull(free_cuti,0) as free_cuti FROM hrd.jenis_cuti_free_tambahan WHERE 1=1 "
        . " order by id_jenis, dari, sampai";
$tampilk=mysqli_query($cnmy, $query);
while ($rowk= mysqli_fetch_array($tampilk)) {
    $lidjenis=$rowk['id_jenis'];
    $ldari=$rowk['dari'];
    $lsampai=$rowk['sampai'];
    $lfreecuti=$rowk['free_cuti'];
    
    if (empty($lfreecuti)) $lfreecuti=0;
    
    if ($lidjenis=="01") {
        $query = "UPDATE $tmp07 SET jumlah=12+'$lfreecuti' WHERE "
                . " ifnull(jml_thn,0)>='$ldari' AND ifnull(jml_thn,0)<='$lsampai' AND id_jenis='$lidjenis'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }else{
        if ((INT)$ldari==0 AND (INT)$lsampai==0) {
            $query ="UPDATE $tmp07 SET jumlah='$lfreecuti' WHERE  id_jenis='$lidjenis'";
        }else{
            $query ="UPDATE $tmp07 SET jumlah='$lfreecuti' WHERE "
                    . " ifnull(jml_thn,0)>='$ldari' AND ifnull(jml_thn,0)<='$lsampai' AND id_jenis='$lidjenis'";
        }
        if ($lidjenis=="08") {
            //echo "$query<br/>";
        }
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
}


$query = "alter table $tmp02 add column jml_cuti INT(4)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp02 as a JOIN (select idcuti, count(distinct tanggal) as jml_cuti FROM $tmp03 GROUP BY 1) as b "
        . " on a.idcuti=b.idcuti SET a.jml_cuti=b.jml_cuti";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp02 SET jml_cuti=1 WHERE id_jenis in ('02')";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp07 as a JOIN $tmp02 as b on a.karyawanid=b.karyawanid AND a.id_jenis=b.id_jenis SET a.jml_cuti=b.jml_cuti";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//cuti melahirkan
$query = "UPDATE $tmp07 SET jumlah=1 WHERE id_jenis in ('02') AND IFNULL(jml_cuti,0)>0";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//cuti massal , 'ALLHO', 'ALLCHC' untuk marketing dulu
$query = "UPDATE $tmp07 as a JOIN 
	(SELECT id_jenis, count(DISTINCT tanggal) as jml_cuti FROM $tmp04 WHERE karyawanid IN ('ALL', 'ALLETH') 
	GROUP BY 1) as b on a.id_jenis=b.id_jenis SET a.jml_cuti=b.jml_cuti WHERE a.divisiid NOT IN ('HO', 'OTC', 'CHC')";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp07 as a JOIN 
	(SELECT id_jenis, count(DISTINCT tanggal) as jml_cuti FROM $tmp04 WHERE karyawanid IN ('ALL', 'ALLHO') 
	GROUP BY 1) as b on a.id_jenis=b.id_jenis SET a.jml_cuti=b.jml_cuti WHERE a.divisiid IN ('HO')";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp07 as a JOIN 
	(SELECT id_jenis, count(DISTINCT tanggal) as jml_cuti FROM $tmp04 WHERE karyawanid IN ('ALL', 'ALLCHC') 
	GROUP BY 1) as b on a.id_jenis=b.id_jenis SET a.jml_cuti=b.jml_cuti WHERE a.divisiid IN ('OTC', 'CHC')";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//jabatan mkt
$query = "UPDATE $tmp07 as a JOIN 
	(SELECT id_jenis, count(DISTINCT tanggal) as jml_cuti FROM $tmp04 WHERE karyawanid IN ('ALL', 'ALLETH') 
	GROUP BY 1) as b on a.id_jenis=b.id_jenis SET a.jml_cuti=b.jml_cuti WHERE a.jabatanid IN ('15', '10', '18', '20', '05', '38')";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

// END CUTI MASSAL

$query = "UPDATE $tmp07 SET sisa_cuti=IFNULL(jumlah,0)-IFNULL(jml_cuti,0)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }




$query = "alter table $tmp05 add column jml_thn INT(4), add column jml_bln INT(4), add column jmlcutithn INT(4), add column jmlcutifree INT (4)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp05 as a JOIN $tmp07 as b on a.karyawanid=b.karyawanid SET a.jml_thn=b.jml_thn, a.jml_bln=b.jml_bln";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


if (!empty($fjenisid)) {
    $query = "DELETE FROM $tmp05 WHERE id_jenis NOT IN $fjenisid";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
}

?>

<HTML>
<HEAD>
  <TITLE>Report Data Cuti</TITLE>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2030 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <link rel="shortcut icon" href="images/icon.ico" />
    <style> .str{ mso-number-format:\@; } </style>
    
    
    <!-- Bootstrap -->
    <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    
    <!-- Custom Theme Style -->
    <link href="build/css/custom.min.css" rel="stylesheet">
</HEAD>
<script>
</script>

<BODY onload="initVar()" style="margin-left:10px; color:#000; background-color:#fff;">
    
    <div class='modal fade' id='myModal' role='dialog'></div>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

    <?PHP

    echo "<b>Report Data Cuti/Izin/Up Country Ethical</b><br/>";
    //echo "<b>Nama : $pnamakarywanpl - $pkryid</b><br/>";
    //echo "<b>Jabatan : $pnamajabatan</b><br/>";
    echo "<b>Tahun : $ptahun</b><br/>";
    echo "<hr/>";

    $totcall=0;
    $totpoint1=0;
    $totpoint2=0;
    
    
    echo "<table id='tbltable' border='1' cellspacing='0' cellpadding='1'>";
        
        echo "<tr>";
            
            echo "<th align='left'><small>No</small></th>";
            echo "<th align='left'><small>Nama Karyawan</small></th>";
            echo "<th align='left'><small>Jabatan</small></th>";
            echo "<th align='left'><small>Tgl. Masuk</small></th>";
            echo "<th align='left'><small>Masa Kerja</small></th>";
            echo "<th align='left'><small>Jenis Cuti</small></th>";
            echo "<th align='left'><small>Tanggal</small></th>";
            echo "<th align='left'><small>Keperluan</small></th>";
            
            echo "<th align='left'><small>Dapat Cuti</small></th>";
            echo "<th align='left'><small>Jumlah Cuti</small></th>";
            echo "<th align='left'><small>Sisa Cuti</small></th>";
            
        echo "</tr>";
        
        $no=1;
        $query = "select distinct karyawanid, nama_karyawan, divisiid, jabatanid, nama_jabatan, tglmasuk, jml_thn, jml_bln FROM $tmp05 ORDER BY nama_karyawan";
        $tampil0=mysqli_query($cnmy, $query);
        while ($row0= mysqli_fetch_array($tampil0)) {
            $pidkaryawan=$row0['karyawanid'];
            $pnmkaryawan=$row0['nama_karyawan'];
            $pnmjabatan=$row0['nama_jabatan'];
            $ptglmasuk=$row0['tglmasuk'];
            $ndivisipilih=$row0['divisiid'];
            
            $pmskrjathn=$row0['jml_thn'];
            $pmskrjabln=$row0['jml_bln'];
            
            $pmasakerja="0";
            if ((INT)$pmskrjathn>0) $pmasakerja=$pmskrjathn." tahun";
            else{
                if ((INT)$pmskrjabln>0) $pmasakerja=$pmskrjabln." bulan";
            }
    
            if ($ptglmasuk=="0000-00-00") $ptglmasuk="";
            if (!empty($ptglmasuk)) $ptglmasuk=date("d/m/Y", strtotime($ptglmasuk));
            
            $nidkry=(INT)$pidkaryawan;
            
            echo "<tr class='fbreak'>";
            echo "<td nowrap>$no</td>";
            echo "<td nowrap>$pnmkaryawan ($nidkry)</td>";
            echo "<td nowrap>$pnmjabatan</td>";
            echo "<td nowrap>$ptglmasuk</td>";
            echo "<td nowrap>$pmasakerja</td>";
            
            $plewat0=false;
            $query = "select distinct id_jenis, nama_jenis FROM $tmp05 WHERE karyawanid='$pidkaryawan' ORDER BY nama_jenis";
            $tampil=mysqli_query($cnmy, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $pidjenis_=$row['id_jenis'];
                $pnmjenis_=$row['nama_jenis'];
                
                if ($plewat0==false) {
                    echo "<td nowrap>$pnmjenis_</td>";
                }else{
                    echo "<tr>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>$pnmjenis_</td>";
                }
                $plewat0=true;
                    
                $plewat1=false;
                $query = "select distinct idcuti, id_jenis, nama_jenis, keperluan, bulan1, bulan2 FROM $tmp05 WHERE karyawanid='$pidkaryawan' AND id_jenis='$pidjenis_' ORDER BY nama_jenis";
                $tampil1=mysqli_query($cnmy, $query);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $pidcuti=$row1['idcuti'];
                    $pidjenis=$row1['id_jenis'];
                    $pnmjenis=$row1['nama_jenis'];
                    $pkeperluan=$row1['keperluan'];
                    $pbln1=$row1['bulan1'];
                    $pbln2=$row1['bulan2'];

                    $pbln1= date("d F Y", strtotime($pbln1));
                    $pbln2= date("d F Y", strtotime($pbln2));

                    
                    if ($plewat1==false) {
                        
                    }else{
                        echo "<tr>";
                        echo "<td nowrap>&nbsp;</td>";
                        echo "<td nowrap>&nbsp;</td>";
                        echo "<td nowrap>&nbsp;</td>";
                        echo "<td nowrap>&nbsp;</td>";
                        echo "<td nowrap>&nbsp;</td>";
                        echo "<td nowrap>&nbsp;</td>";
                    }
                    $plewat1=true;

                    $plewat2=false;
                    $query = "select * FROM $tmp03 WHERE karyawanid='$pidkaryawan' AND id_jenis='$pidjenis' AND idcuti='$pidcuti' ORDER BY tanggal";
                    $tampil2=mysqli_query($cnmy, $query);
                    $ketemu2= mysqli_num_rows($tampil2);
                    if ((INT)$ketemu2==0) {
                        echo "<td nowrap>$pbln1 s/d. $pbln2</td>";
                        echo "<td >$pkeperluan</td>";
                        
                        echo "<td nowrap>&nbsp;</td>";
                        echo "<td nowrap>&nbsp;</td>";
                        echo "<td nowrap>&nbsp;</td>";
                        
                        echo "</tr>";
                    }else{


                        while ($row2= mysqli_fetch_array($tampil2)) {
                            $ntgl=$row2['tanggal'];
                            $ntgl= date("d-m-Y", strtotime($ntgl));

                            $pkeperluan=$row2['keperluan'];

                            if ($plewat2==false) {
                                echo "<td nowrap>$ntgl</td>";
                                echo "<td >$pkeperluan</td>";
                                
                                echo "<td nowrap>&nbsp;</td>";
                                echo "<td nowrap>&nbsp;</td>";
                                echo "<td nowrap>&nbsp;</td>";
                                
                                echo "</tr>";
                            }else{
                                echo "<tr>";
                                echo "<td nowrap>&nbsp;</td>";
                                echo "<td nowrap>&nbsp;</td>";
                                echo "<td nowrap>&nbsp;</td>";
                                echo "<td nowrap>&nbsp;</td>";
                                echo "<td nowrap>&nbsp;</td>";
                                echo "<td nowrap>&nbsp;</td>";
                                echo "<td nowrap>$ntgl</td>";
                                echo "<td >$pkeperluan</td>";
                                
                                echo "<td nowrap>&nbsp;</td>";
                                echo "<td nowrap>&nbsp;</td>";
                                echo "<td nowrap>&nbsp;</td>";
                                
                                echo "</tr>";
                            }
                            $plewat2=true;
                        }

                    }

                }
            
            }
             
            
            $pjmlcutiskr=0;
            $pjmlcutifree=0;
            
            if (empty($pjmlcutiskr)) $pjmlcutiskr=0;
            if (empty($pjmlcutifree)) $pjmlcutifree=0;
            
            
            
            
            //Cuti Massal , 'ALLHO', 'ALLCHC'
            if (strpos($fjenisid, "00")) {
                
                $query ="select distinct tanggal, keperluan FROM $tmp04 WHERE id_jenis in ('00') ";
                if ($ndivisipilih=="HO") {
                    $query .=" AND karyawanid IN ('ALL', 'ALLHO') ";
                }elseif ($ndivisipilih=="OTC" OR $ndivisipilih=="CHC") {
                    $query .=" AND karyawanid IN ('ALL', 'ALLCHC') ";
                }else{
                    $query .=" AND karyawanid IN ('ALL', 'ALLETH') ";
                }
                $query .=" order by tanggal";
                $tampil_m=mysqli_query($cnmy, $query);
                $ketemu_m= mysqli_num_rows($tampil_m);
                if ((INT)$ketemu_m>0) {
                    echo "<tr>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td >Cuti Massal</td>";

                    $ilewat_m=false;
                    while ($row_m= mysqli_fetch_array($tampil_m)) {

                        $ntgl=$row_m['tanggal'];
                        $ntgl= date("d-m-Y", strtotime($ntgl));

                        $pkeperluan=$row_m['keperluan'];

                        if ($ilewat_m==false) {
                            echo "<td nowrap>$ntgl</td>";
                            echo "<td >$pkeperluan</td>";

                            echo "<td nowrap>&nbsp;</td>";
                            echo "<td nowrap>&nbsp;</td>";
                            echo "<td nowrap>&nbsp;</td>";

                            echo "</tr>";

                        }else{
                            echo "<tr>";
                            echo "<td nowrap>&nbsp;</td>";
                            echo "<td nowrap>&nbsp;</td>";
                            echo "<td nowrap>&nbsp;</td>";
                            echo "<td nowrap>&nbsp;</td>";
                            echo "<td nowrap>&nbsp;</td>";
                            echo "<td ></td>";
                            echo "<td nowrap>$ntgl</td>";
                            echo "<td >$pkeperluan</td>";

                            echo "<td nowrap>&nbsp;</td>";
                            echo "<td nowrap>&nbsp;</td>";
                            echo "<td nowrap>&nbsp;</td>";

                            echo "</tr>";
                        }
                        $ilewat_m=true;

                    }
                }
            
            }
            
            
            /*
            //sisa cuti tahun sebelumnya
            echo "<tr style='font-weight:bold;'>";
            echo "<td nowrap>&nbsp;</td>";
            echo "<td nowrap>&nbsp;</td>";
            echo "<td nowrap>&nbsp;</td>";
            echo "<td nowrap>&nbsp;</td>";
            echo "<td nowrap>&nbsp;</td>";
            echo "<td ></td>";
            echo "<td nowrap>&nbsp;</td>";
            echo "<td align='right'>Sisa Cuti Tahun $ptahunsebelum s/d. Maret $ptahun</td>";

            echo "<td nowrap align='right'>&nbsp;</td>";
            echo "<td nowrap align='right'>&nbsp;</td>";
            echo "<td nowrap align='right'>$pjmlsisacuti</td>";

            echo "</tr>";
            */
            
            
            $pjmlsisacuti=0;
            $query = "select * from $tmp07 WHERE IFNULL(potong_cuti,'')='Y' AND karyawanid='$pidkaryawan' order by id_jenis";
            $tampil_k=mysqli_query($cnmy, $query);
            $ketemu_k= mysqli_num_rows($tampil_k);
            if ((INT)$ketemu_k>0) {
                while ($row_k= mysqli_fetch_array($tampil_k)) {
                    $pidjenis=$row_k['id_jenis'];
                    $pnmjenis=$row_k['nama_jenis'];
                    
                    $pjmldptcuti=$row_k['jumlah'];
                    $pjmlcuti=$row_k['jml_cuti'];
                    $psisacuti=$row_k['sisa_cuti'];
                    
                    if ($pidjenis!="01" AND (INT)$pjmlcuti==0) continue;
                    
                    if ($pidjenis=="01") {
                        $pjmlsisacuti=(DOUBLE)$pjmlsisacuti+$psisacuti;
                    }else{
                        $pjmlsisacuti=(DOUBLE)$pjmlsisacuti+(DOUBLE)$psisacuti;
                    }
                    
                    if (strpos($fjenisid, $pidjenis)) {
                        echo "<tr style='font-weight:bold;'>";
                        echo "<td nowrap>&nbsp;</td>";
                        echo "<td nowrap>&nbsp;</td>";
                        echo "<td nowrap>&nbsp;</td>";
                        echo "<td nowrap>&nbsp;</td>";
                        echo "<td nowrap>&nbsp;</td>";
                        echo "<td >&nbsp;</td>";
                        echo "<td nowrap>&nbsp;</td>";
                        echo "<td align='right'>$pnmjenis</td>";

                        echo "<td nowrap align='right'>$pjmldptcuti</td>";
                        echo "<td nowrap align='right'>$pjmlcuti</td>";
                        echo "<td nowrap align='right'>$psisacuti</td>";

                        echo "</tr>";
                    }
                        
                }
                //sisa cuti
                echo "<tr style='font-weight:bold;'>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td ></td>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td align='right'>Sisa Cuti Tahun $ptahun</td>";

                echo "<td nowrap align='right'>&nbsp;</td>";
                echo "<td nowrap align='right'>&nbsp;</td>";
                echo "<td nowrap align='right'>$pjmlsisacuti</td>";

                echo "</tr>";
            }
            
            
            $no++;
            
        }

    echo "</table>";

    echo "<br/><br/><br/><br/><br/>";
    
    ?>

</BODY>

    <!-- jQuery -->
    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="build/js/custom.min.js"></script>

   
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
        .fbreak {
            background-color:#f5f5f5;
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
    
    <script>
        function LiatNotes(ests, enourut, eidkry, etgl, edoktid){
            $.ajax({
                type:"post",
                url:"module/dkd/dkd_reportpalnreal/lihatnotes.php?module=viewnotes",
                data:"usts="+ests+"&unourut="+enourut+"&uidkry="+eidkry+"&utgl="+etgl+"&udoktid="+edoktid,
                success:function(data){
                    $("#myModal").html(data);
                }
            });
        }
        function LiatKomentar(ests, enourut, eidkry, etgl, edoktid){
            $.ajax({
                type:"post",
                url:"module/dkd/dkd_reportpalnreal/lihatkomentar.php?module=viewnotes",
                data:"usts="+ests+"&unourut="+enourut+"&uidkry="+eidkry+"&utgl="+etgl+"&udoktid="+edoktid,
                success:function(data){
                    $("#myModal").html(data);
                }
            });
        }
    </script>

</HTML>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp03");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp04");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp05");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp06");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp07");
    mysqli_close($cnmy);
?>