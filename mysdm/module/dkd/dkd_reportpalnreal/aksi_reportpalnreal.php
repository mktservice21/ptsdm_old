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
$tmp01 =" dbtemp.tmprptplanreal01_".$puserid."_$now ";
$tmp02 =" dbtemp.tmprptplanreal02_".$puserid."_$now ";
$tmp03 =" dbtemp.tmprptplanreal03_".$puserid."_$now ";
$tmp04 =" dbtemp.tmprptplanreal04_".$puserid."_$now ";
$tmp05 =" dbtemp.tmprptplanreal05_".$puserid."_$now ";
$tmp06 =" dbtemp.tmprptplanreal06_".$puserid."_$now ";
$tmp07 =" dbtemp.tmprptplanreal07_".$puserid."_$now ";


$pkryid = $_POST['cb_karyawan']; 
$ptgl1 = $_POST['e_tanggal'];

$pbulan = date('Y-m', strtotime($ptgl1));
$ptgl1 = date('Y-m-d', strtotime($ptgl1));
$ptgl2 = date('Y-m-d', strtotime('+4 days', strtotime($ptgl1)));

$ftglfilter="";
if (isset($_POST['chktgl'])) {
    foreach ($_POST['chktgl'] as $pntgl) {
        if (!empty($pntgl)) {
            $ftglfilter .="'".$pntgl."',";
        }
    }
    if (!empty($ftglfilter)) $ftglfilter="(".substr($ftglfilter, 0, -1).")";
}else{
    $ftglfilter="('".$ptgl1."')";
}
    
$query = "select a.nama, a.jabatanId as jabatanid, b.nama as nama_jabatan from hrd.karyawan as a 
    LEFT join hrd.jabatan as b on a.jabatanId=b.jabatanId 
    where a.karyawanid='$pkryid'";
$tampilk=mysqli_query($cnmy, $query);
$rowk=mysqli_fetch_array($tampilk);
$pnamakarywanpl=$rowk['nama'];
$pnmjbtkarywanpl=$rowk['nama_jabatan'];


$sql = "select a.idinput, a.karyawanid, c.nama as namakaryawan, a.jabatanid, a.tanggal, a.tglinput, a.ketid, b.nama as nama_ket, a.compl, a.aktivitas
    FROM hrd.dkd_new0 as a LEFT JOIN hrd.ket as b on a.ketid=b.ketId 
    LEFT JOIN hrd.karyawan as c on a.karyawanid=c.karyawanId
    WHERE a.karyawanid='$pkryid' ";
if (!empty($ftglfilter)) {
    $sql .=" AND a.tanggal IN $ftglfilter ";
}else{
    $sql .=" AND a.tanggal BETWEEN '$ptgl1' AND '$ptgl2' ";
}
$query = "create TEMPORARY table $tmp01 ($sql)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$sql = "select c.nourut, c.idinput, c.karyawanid, e.nama as namakaryawan, e.jabatanid, c.tanggal, c.tglinput, 
    c.dokterid, d.namalengkap, d.gelar, d.spesialis, jenis, c.notes,
    c.atasan1, c.tgl_atasan1, c.atasan2, c.tgl_atasan2, c.atasan3, c.tgl_atasan3, c.atasan4, c.tgl_atasan4 
    FROM hrd.dkd_new1 as c JOIN dr.masterdokter as d on c.dokterid=d.id LEFT JOIN hrd.karyawan as e on c.karyawanid=e.karyawanId 
    WHERE c.karyawanid='$pkryid' ";
if (!empty($ftglfilter)) {
    $sql .=" AND c.tanggal IN $ftglfilter ";
}else{
    $sql .=" AND c.tanggal BETWEEN '$ptgl1' AND '$ptgl2' ";
}
$query = "create TEMPORARY table $tmp02 ($sql)"; 
mysqli_query($cnmy, $query);

$query = "UPDATE $tmp02 SET jenis='' WHERE jenis IN ('JV')"; 
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "ALTER TABLE $tmp02 ADD COLUMN saran varchar(300)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$sql = "select a.idinput, a.karyawanid, c.nama as namakaryawan, a.jabatanid, a.tanggal, a.tglinput, a.ketid, b.nama as nama_ket, a.compl, a.aktivitas
    FROM hrd.dkd_new_real0 as a LEFT JOIN hrd.ket as b on a.ketid=b.ketId 
    LEFT JOIN hrd.karyawan as c on a.karyawanid=c.karyawanId
    WHERE a.karyawanid='$pkryid' ";
if (!empty($ftglfilter)) {
    $sql .=" AND a.tanggal IN $ftglfilter ";
}else{
    $sql .=" AND a.tanggal BETWEEN '$ptgl1' AND '$ptgl2' ";
}
$query = "create TEMPORARY table $tmp03 ($sql)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$sql = "select a.nourut, a.karyawanid, c.nama as namakaryawan, a.jabatanid, a.tanggal, a.tglinput, 
    a.dokterid, d.namalengkap, d.gelar, d.spesialis, a.jenis, a.notes, a.saran, 
    a.atasan1, a.tgl_atasan1, a.atasan2, a.tgl_atasan2, a.atasan3, a.tgl_atasan3, a.atasan4, a.tgl_atasan4 
    FROM hrd.dkd_new_real1 as a JOIN dr.masterdokter as d on a.dokterid=d.id 
    LEFT JOIN hrd.karyawan as c on a.karyawanid=c.karyawanId
    WHERE a.karyawanid='$pkryid' ";
if (!empty($ftglfilter)) {
    $sql .=" AND a.tanggal IN $ftglfilter ";
}else{
    $sql .=" AND a.tanggal BETWEEN '$ptgl1' AND '$ptgl2' ";
}
$query = "create TEMPORARY table $tmp04 ($sql)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }



//cari jabatan yang diinput
$query = "select a.jabatanid, b.nama as nama_jabatan FROM ("
        . " select DISTINCT jabatanid FROM $tmp01 WHERE IFNULL(jabatanid,'')<>'' "
        . " UNION "
        . " select DISTINCT jabatanid FROM $tmp02 WHERE IFNULL(jabatanid,'')<>''"
        . " UNION "
        . " select DISTINCT jabatanid FROM $tmp03 WHERE IFNULL(jabatanid,'')<>''"
        . " UNION "
        . " select DISTINCT jabatanid FROM $tmp04 WHERE IFNULL(jabatanid,'')<>''"
        . " ) as a JOIN hrd.jabatan as b on a.jabatanid=b.jabatanId";
$tampilk=mysqli_query($cnmy, $query);
$rowk=mysqli_fetch_array($tampilk);
$pnamajabatan=$rowk['nama_jabatan'];
$pjabatanid=$rowk['jabatanid'];

if (empty($pnamajabatan)) $pnamajabatan=$pnmjbtkarywanpl;


$query = "create TEMPORARY table $tmp05 (select * from $tmp02)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "INSERT INTO $tmp02 (karyawanid, jabatanid, tanggal, 
    jenis, dokterid, namalengkap, gelar, spesialis, 
    atasan1, tgl_atasan1, atasan2, tgl_atasan2, atasan3, tgl_atasan3, atasan4, tgl_atasan4) SELECT"
        . " karyawanid, jabatanid, tanggal, "
        . " jenis, dokterid, namalengkap, gelar, spesialis, "
        . " atasan1, tgl_atasan1, atasan2, tgl_atasan2, atasan3, tgl_atasan3, atasan4, tgl_atasan4 "
        . " FROM $tmp04 WHERE CONCAT(karyawanid, tanggal, dokterid) NOT IN "
        . " (SELECT DISTINCT IFNULL(CONCAT(karyawanid, tanggal, dokterid),'') FROM $tmp05 WHERE IFNULL(jenis,'') NOT IN ('JV'))";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "DROP TEMPORARY TABLE $tmp05";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//$query = "CREATE TEMPORARY TABLE $tmp05 (ino INT(10) AUTO_INCREMENT PRIMARY KEY)";
//mysqli_query($cnmy, $query);
//$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "select distinct tanggal, karyawanid, namakaryawan from $tmp01 ORDER BY karyawanid, tanggal";
$query = "create TEMPORARY table $tmp05 ($query)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "INSERT INTO $tmp05 (tanggal, karyawanid, namakaryawan) "
        . " select distinct tanggal, karyawanid, namakaryawan FROM $tmp03 WHERE "
        . " CONCAT(tanggal,karyawanid) NOT IN (select IFNULL(CONCAT(tanggal,karyawanid),'') FROM $tmp01) ORDER BY karyawanid, tanggal"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "select distinct tanggal, karyawanid, namakaryawan from $tmp02 ORDER BY karyawanid, tanggal";
$query = "create TEMPORARY table $tmp06 ($query)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "INSERT INTO $tmp06 (tanggal, karyawanid, namakaryawan) "
        . " select distinct tanggal, karyawanid, namakaryawan FROM $tmp04 WHERE "
        . " CONCAT(tanggal,karyawanid) NOT IN (select IFNULL(CONCAT(tanggal,karyawanid),'') FROM $tmp02) ORDER BY karyawanid, tanggal"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "select distinct tanggal, karyawanid, namakaryawan, dokterid, namalengkap from $tmp02 ORDER BY karyawanid, tanggal";
$query = "create TEMPORARY table $tmp07 ($query)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "INSERT INTO $tmp07 (tanggal, karyawanid, namakaryawan, dokterid, namalengkap) "
        . " select distinct tanggal, karyawanid, namakaryawan, dokterid, namalengkap FROM $tmp04 WHERE "
        . " CONCAT(tanggal,karyawanid) NOT IN (select IFNULL(CONCAT(tanggal,karyawanid),'') FROM $tmp02) ORDER BY karyawanid, tanggal"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "ALTER TABLE $tmp07 ADD COLUMN tglinput timestamp";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp07 as a JOIN $tmp04 as b on a.dokterid=b.dokterid AND a.karyawanid=b.karyawanid AND "
        . " a.tanggal=b.tanggal SET a.tglinput=b.tglinput";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


    $bulan_array=array(1=> "Januari", "Februari", "Maret", "April", "Mei", 
        "Juni", "Juli", "Agustus", "September", 
        "Oktober", "November", "Desember");

    $hari_array = array(
        'Minggu',
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu'
    );




?>

<HTML>
<HEAD>
  <TITLE>Report Weekly Realisasi</TITLE>
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

    echo "<b>Report Weekly Realisasi</b><br/>";
    echo "<b>Nama : $pnamakarywanpl - $pkryid</b><br/>";
    echo "<b>Jabatan : $pnamajabatan</b><br/>";
    echo "<hr/><br/>";

    $totcall=0;
    $totpoint1=0;
    $totpoint2=0;
    
    echo "<br/><b>Activity</b><br/>";
    echo "<table id='tbltable' border='1' cellspacing='0' cellpadding='1'>";
        echo "<tr>";
        
            echo "<th align='left' rowspan='2'><small>Tanggal</small></th>";
            echo "<th align='cener' colspan='3'><small>Plan</small></th>";
            echo "<th align='cener' colspan='4'><small>Realisasi</small></th>";
            
        echo "</tr>";
        
        echo "<tr>";
        
            echo "<th align='left'><small>Keperluan</small></th>";
            echo "<th align='left'><small>Compl.</small></th>";
            echo "<th align='left'><small>Aktivitas</small></th>";
            
            echo "<th align='left'><small>Jam</small></th>";
            echo "<th align='left'><small>Keperluan</small></th>";
            echo "<th align='left'><small>Compl.</small></th>";
            echo "<th align='left'><small>Aktivitas</small></th>";
            
        echo "</tr>";
        
        $query = "select distinct tanggal, karyawanid from $tmp05 order by namakaryawan, karyawanid, tanggal";
        $tampil1=mysqli_query($cnmy, $query);
        while ($row1=mysqli_fetch_array($tampil1)) {
            $ftgl=$row1['tanggal'];
            $fkryid=$row1['karyawanid'];
            
            $query = "select * from $tmp01 WHERE tanggal='$ftgl' AND karyawanid='$fkryid' order by tanggal, nama_ket";
            $tampil0=mysqli_query($cnmy, $query);
            $ketemu0=mysqli_num_rows($tampil0);
            $pada=false;
            if ((INT)$ketemu0<=0) {
                
                $query = "select * from $tmp03 WHERE tanggal='$ftgl' AND karyawanid='$fkryid' order by tanggal, nama_ket";
                $tampil0=mysqli_query($cnmy, $query);
                $pada=true;
            }
                
            
                while ($row0=mysqli_fetch_array($tampil0)) {
                    $nketid=$row0['ketid'];
                    $ntgl=$row0['tanggal'];
                    $nnamaket=$row0['nama_ket'];
                    $ncompl=$row0['compl'];
                    $naktivitas=$row0['aktivitas'];
                    $ntglinput=$row0['tglinput'];
                    
                    $pjam="";
                    if (!empty($ntglinput)) $pjam = date('H:i', strtotime($ntglinput));
                    
                    $xhari = $hari_array[(INT)date('w', strtotime($ntgl))];
                    $xtgl= date('d', strtotime($ntgl));
                    $xbulan = $bulan_array[(INT)date('m', strtotime($ntgl))];
                    $xthn= date('Y', strtotime($ntgl));

                    
                    if ($pada==false) {
                        echo "<tr>";
                        echo "<td nowrap>$xhari, $xtgl $xbulan $xthn</td>";
                        echo "<td nowrap>$nnamaket</td>";
                        echo "<td >$ncompl</td>";
                        echo "<td >$naktivitas</td>";
                        
                        $query = "select * from $tmp03 WHERE tanggal='$ftgl' AND karyawanid='$fkryid' order by tanggal, nama_ket";
                        $tampil2=mysqli_query($cnmy, $query);
                        $ketemu2=mysqli_num_rows($tampil2);
                        if ((INT)$ketemu2<=0) {
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td ></td>";
                            echo "<td ></td>";
                            
                            echo "</tr>";
                        }else{
                            $ifirst=false;
                            while ($row2=mysqli_fetch_array($tampil2)) {
                                
                                $nketid=$row2['ketid'];
                                $nnamaket=$row2['nama_ket'];
                                $ncompl=$row2['compl'];
                                $naktivitas=$row2['aktivitas'];
                                $ntglinput=$row2['tglinput'];

                                $pjam="";
                                if (!empty($ntglinput)) $pjam = date('H:i', strtotime($ntglinput));
                                
                                $ntgl=$row2['tanggal'];
                                $xhari = $hari_array[(INT)date('w', strtotime($ntgl))];
                                $xtgl= date('d', strtotime($ntgl));
                                $xbulan = $bulan_array[(INT)date('m', strtotime($ntgl))];
                                $xthn= date('Y', strtotime($ntgl));
                    
                                if ($ifirst==true) {
                                    echo "<tr>";
                                    echo "<td nowrap>$xhari, $xtgl $xbulan $xthn</td>";
                                    echo "<td nowrap>&nbsp;</td>";
                                    echo "<td >&nbsp;</td>";
                                    echo "<td >&nbsp;</td>";
                                }
                                echo "<td nowrap>$pjam</td>";
                                echo "<td nowrap>$nnamaket</td>";
                                echo "<td >$ncompl</td>";
                                echo "<td >$naktivitas</td>";

                                echo "</tr>";
                                $ifirst=true;
                            }
                            
                        }
                        
                    }else{
                        echo "<tr>";
                        echo "<td nowrap>$xhari, $xtgl $xbulan $xthn</td>";
                        echo "<td nowrap>&nbsp;</td>";
                        echo "<td >&nbsp;</td>";
                        echo "<td >&nbsp;</td>";
                        
                        echo "<td nowrap>$pjam</td>";
                        echo "<td nowrap>$nnamaket</td>";
                        echo "<td >$ncompl</td>";
                        echo "<td >$naktivitas</td>";
                        
                        
                        echo "</tr>";
                    }
                    
                }
            
            
            
        }
        

    echo "</table>";

    
    echo "<br/>";
    echo "<b><u>Keterangan :</u> </b><br/>(-) warna merah menandakan kunjungan yang belum diapprove atasan.<br/>
        (-) warna hijau, sudah diapprove.<br/>
        (-) sebagai atasan, untuk approve silakan <b>lihat notes</b> lalu klik tombol <b>Approve</b> (Per masing-masing kunjungan)";
    echo "<br/>";

    echo "<br/><b>Visit</b><br/>";
    echo "<table id='tbltable' border='1' cellspacing='0' cellpadding='1'>";
        echo "<tr>";
        
            echo "<th align='left' rowspan='2'><small>Tanggal</small></th>";
            echo "<th align='cener' colspan='1'><small>Plan</small></th>";
            echo "<th align='cener' colspan='4'><small>Realisasi</small></th>";
            
        echo "</tr>";
        
        echo "<tr>";
        
            //echo "<th align='left'><small>Jenis</small></th>";
            echo "<th align='left'><small>User</small></th>";
            
            echo "<th align='left'><small>Jam</small></th>";
            //echo "<th align='left'><small>Jenis</small></th>";
            echo "<th align='left'><small>User</small></th>";
            echo "<th align='left'><small>&nbsp;</small></th>";
            echo "<th align='left'><small>&nbsp;</small></th>";
            
        echo "</tr>";
        
        
        $query = "select distinct tanggal, karyawanid from $tmp06 order by karyawanid, tanggal";
        $tampil1=mysqli_query($cnmy, $query);
        while ($row1=mysqli_fetch_array($tampil1)) {
            $ftgl=$row1['tanggal'];
            $fkryid=$row1['karyawanid'];
            
            $psudahtanggal=false;
            $query = "select distinct dokterid from $tmp07 WHERE tanggal='$ftgl' AND karyawanid='$fkryid' order by karyawanid, tanggal, tglinput, namalengkap, dokterid";
            $tampil01=mysqli_query($cnmy, $query);
            while ($row01=mysqli_fetch_array($tampil01)) {
                $pdokterid=$row01['dokterid'];
                
                $query = "select * from $tmp02 WHERE tanggal='$ftgl' AND karyawanid='$fkryid' AND dokterid='$pdokterid' order by tanggal, namalengkap";
                $tampil0=mysqli_query($cnmy, $query);
                $ketemu0=mysqli_num_rows($tampil0);
                $pada=false;
                if ((INT)$ketemu0<=0) {
                    
                    $query = "select * from $tmp04 WHERE tanggal='$ftgl' AND karyawanid='$fkryid' AND dokterid='$pdokterid' order by tanggal, namalengkap";
                    $tampil0=mysqli_query($cnmy, $query);
                    $pada=true;
                }

                
                while ($row0=mysqli_fetch_array($tampil0)) {
                    $cnourut=$row0['nourut'];
                    $ndoktid=$row0['dokterid'];
                    $nkaryawanid=$row0['karyawanid'];
                    $ntgl=$row0['tanggal'];
                    $nnamalengkap=$row0['namalengkap'];
                    $nnotes=$row0['notes'];
                    $nsaran=$row0['saran'];
                    $njenis=$row0['jenis'];
                    $ngelar=$row0['gelar'];
                    $nspesialis=$row0['spesialis'];
                    $ntglinput=$row0['tglinput'];


                    $pjam="";
                    if (!empty($ntglinput)) $pjam = date('H:i', strtotime($ntglinput));
                    if (!empty($ngelar))
                        $pnmdokt_=$nnamalengkap." (".$ngelar.") - ".$nspesialis." - ".$ndoktid;
                    else
                        $pnmdokt_=$nnamalengkap." - ".$nspesialis." - ".$ndoktid;
                    
                    $pnmjenis="";
                    if ($njenis=="JV") $pnmjenis="Join Visit";
                    elseif ($njenis=="EC") $pnmjenis="Extra Call";
                    else{
                        if (!empty($njenis)) {
                            $pnmjenis="Other";
                        }
                    }
                    
                    $xhari = $hari_array[(INT)date('w', strtotime($ntgl))];
                    $xtgl= date('d', strtotime($ntgl));
                    $xbulan = $bulan_array[(INT)date('m', strtotime($ntgl))];
                    $xthn= date('Y', strtotime($ntgl));
                    
                    $ppilihtgl="$xhari, $xtgl $xbulan $xthn";
                    if ($psudahtanggal==true) $ppilihtgl="";
                    
                    $pstsvisitreal="plan";
                    if ($pada==true) $pstsvisitreal="realisasi";
                    
                    $plihatnotes="<button type='button' class='btn btn-default btn-xs' data-toggle='modal' "
                            . " data-target='#myModal' onClick=\"LiatNotes('$pstsvisitreal', '$cnourut', '$nkaryawanid', '$ntgl', '$ndoktid')\">Lihat Notes</button>";
                    
                    $plihatkomen="<button type='button' class='btn btn-default btn-xs' data-toggle='modal' "
                            . " data-target='#myModal' onClick=\"LiatKomentar('$pstsvisitreal', '$cnourut', '$nkaryawanid', '$ntgl', '$ndoktid')\">Isi Komentar</button>";
                    
                    if ($pada==false) {
                        if ($njenis=="EC") {
                            $pnmjenis="";
                            $pnmdokt_="";
                        }
                        
                        if (!empty($pnmjenis)) {
                            $pnmdokt_="($pnmjenis) ".$pnmdokt_;
                        }
                        
                        echo "<tr>";
                        echo "<td nowrap>$ppilihtgl</td>";
                        //echo "<td >$pnmjenis</td>";
                        echo "<td nowrap>$pnmdokt_</td>";
                        //echo "<td >$nnotes</td>";
                        
                        $query = "select * from $tmp04 WHERE tanggal='$ftgl' AND karyawanid='$fkryid' AND dokterid='$pdokterid' order by tanggal, namalengkap";
                        $tampil2=mysqli_query($cnmy, $query);
                        $ketemu2=mysqli_num_rows($tampil2);
                        if ((INT)$ketemu2<=0) {
                            echo "<td nowrap></td>";
                            //echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td ></td>";
                            echo "<td >$plihatkomen</td>";
                            
                            echo "</tr>";
                        }else{
                            $ifirst=false;
                            while ($row2=mysqli_fetch_array($tampil2)) {
                                
                                $cnourut=$row2['nourut'];
                                $ndoktid=$row2['dokterid'];
                                $nkaryawanid=$row2['karyawanid'];
                                $ntgl=$row2['tanggal'];
                                $nnamalengkap=$row2['namalengkap'];
                                $nnotes=$row2['notes'];
                                $nsaran=$row2['saran'];
                                $njenis=$row2['jenis'];
                                $ngelar=$row2['gelar'];
                                $nspesialis=$row2['spesialis'];
                                $ntglinput=$row2['tglinput'];
                                
                                
                                
                                $njabatanid=$row2['jabatanid'];
                                $natasan1=$row2['atasan1'];
                                $natasan2=$row2['atasan2'];
                                $natasan3=$row2['atasan3'];
                                $natasan4=$row2['atasan4'];

                                $ntglatasan1=$row2['tgl_atasan1'];
                                $ntglatasan2=$row2['tgl_atasan2'];
                                $ntglatasan3=$row2['tgl_atasan3'];
                                $ntglatasan4=$row2['tgl_atasan4'];


                                if ($ntglatasan1=="0000-00-00" OR $ntglatasan1=="0000-00-00 00:00:00") $ntglatasan1="";
                                if ($ntglatasan2=="0000-00-00" OR $ntglatasan2=="0000-00-00 00:00:00") $ntglatasan2="";
                                if ($ntglatasan3=="0000-00-00" OR $ntglatasan3=="0000-00-00 00:00:00") $ntglatasan3="";
                                if ($ntglatasan4=="0000-00-00" OR $ntglatasan4=="0000-00-00 00:00:00") $ntglatasan4="";


                                if (!empty($ntglatasan1)) $ntglatasan1 = date('d F Y H:i:s', strtotime($ntglatasan1));
                                if (!empty($ntglatasan2)) $ntglatasan2 = date('d F Y H:i:s', strtotime($ntglatasan2));
                                if (!empty($ntglatasan3)) $ntglatasan3 = date('d F Y H:i:s', strtotime($ntglatasan3));
                                if (!empty($ntglatasan4)) $ntglatasan4 = date('d F Y H:i:s', strtotime($ntglatasan4));
                    
                                $pjam="";
                                if (!empty($ntglinput)) $pjam = date('H:i', strtotime($ntglinput));
                                
                                if (!empty($ngelar))
                                    $pnmdokt_=$nnamalengkap." (".$ngelar.") - ".$nspesialis." - ".$ndoktid;
                                else
                                    $pnmdokt_=$nnamalengkap." - ".$nspesialis." - ".$ndoktid;
                                
                                $pnmjenis="";
                                if ($njenis=="JV") $pnmjenis="Join Visit";
                                elseif ($njenis=="EC") $pnmjenis="Extra Call";
                                else{
                                    if (!empty($njenis)) {
                                        $pnmjenis="Other";
                                    }
                                }
                    
                                $ntgl=$row2['tanggal'];
                                $xhari = $hari_array[(INT)date('w', strtotime($ntgl))];
                                $xtgl= date('d', strtotime($ntgl));
                                $xbulan = $bulan_array[(INT)date('m', strtotime($ntgl))];
                                $xthn= date('Y', strtotime($ntgl));
                    
                                $ppilihtgl="$xhari, $xtgl $xbulan $xthn";
                                if ($psudahtanggal==true) $ppilihtgl="";
                                
                                $pstsvisitreal="realisasi";
                                
                                $pidget=encodeString($nkaryawanid);
                                
                                $plihatnotes="<button type='button' class='btn btn-default btn-xs' data-toggle='modal' "
                                        . " data-target='#myModal' onClick=\"LiatNotes('$pstsvisitreal', '$cnourut', '$nkaryawanid', '$ntgl', '$ndoktid')\">Lihat Notes</button>";
                                
                                $plihatkomen="<button type='button' class='btn btn-default btn-xs' data-toggle='modal' "
                                        . " data-target='#myModal' onClick=\"LiatKomentar('$pstsvisitreal', '$cnourut', '$nkaryawanid', '$ntgl', '$ndoktid')\">Isi Komentar</button>";
                                
                                
                                $pwarnaapv=" style='color:red;' ";
                                if ($njabatanid=="15") {//$ntglatasan1
                                    if (!empty($natasan1) AND !empty($ntglatasan1)) {
                                        $pwarnaapv=" style='color:green;' ";
                                    }else{
                                        if (!empty($ntglatasan2)) $pwarnaapv=" style='color:green;' ";
                                    }
                                }elseif ($njabatanid=="10" OR $njabatanid=="18") {
                                    if (!empty($natasan2) AND !empty($ntglatasan2)) {
                                        $pwarnaapv=" style='color:green;' ";
                                    }else{
                                        if (!empty($ntglatasan3)) $pwarnaapv=" style='color:green;' ";
                                    }
                                }elseif ($njabatanid=="08") {
                                    if (!empty($ntglatasan3)) $pwarnaapv=" style='color:green;' ";
                                }elseif ($njabatanid=="20") {
                                    if (!empty($ntglatasan4)) $pwarnaapv=" style='color:green;' ";
                                }elseif ($njabatanid=="05") {

                                }
                        
                                if ($ifirst==true) {
                                    echo "<tr>";
                                    echo "<td nowrap>$ppilihtgl</td>";
                                    echo "<td nowrap>&nbsp;</td>";
                                    echo "<td nowrap>&nbsp;</td>";
                                    //echo "<td >&nbsp;</td>";
                                }
                                if (!empty($pnmjenis)) {
                                    $pnmdokt_="($pnmjenis) ".$pnmdokt_;
                                }
                                echo "<td nowrap>$pjam</td>";
                                //echo "<td nowrap>$pnmjenis</td>";
                                echo "<td nowrap $pwarnaapv>$pnmdokt_</td>";
                                echo "<td >$plihatnotes</td>";
                                echo "<td >$plihatkomen</td>";

                                echo "</tr>";
                                $ifirst=true;
                            }
                            
                        }
                    }else{
                        echo "<tr>";
                        echo "<td nowrap>$ppilihtgl</td>";
                        echo "<td nowrap>&nbsp;</td>";
                        echo "<td nowrap>&nbsp;</td>";
                        //echo "<td >&nbsp;</td>";

                        if (!empty($pnmjenis)) {
                            $pnmdokt_="($pnmjenis) ".$pnmdokt_;
                        }
                                
                        echo "<td nowrap>$pjam</td>";
                        //echo "<td nowrap>$pnmjenis</td>";
                        echo "<td nowrap>$pnmdokt_</td>";
                        echo "<td >$plihatnotes</td>";
                        echo "<td >$plihatkomen</td>";
                    }

                    echo "</tr>";


                }
                
                $psudahtanggal=true;
            
            }
            
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