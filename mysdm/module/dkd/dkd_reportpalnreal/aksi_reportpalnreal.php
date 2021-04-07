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

$query = "select a.nama, a.jabatanId as jabatanid, b.nama as nama_jabatan from hrd.karyawan as a 
    LEFT join hrd.jabatan as b on a.jabatanId=b.jabatanId 
    where a.karyawanid='$pkryid'";
$tampilk=mysqli_query($cnmy, $query);
$rowk=mysqli_fetch_array($tampilk);
$pnamakarywanpl=$rowk['nama'];

$sql = "select a.idinput, a.karyawanid, c.nama as namakaryawan, a.jabatanid, a.tanggal, a.tglinput, a.ketid, b.nama as nama_ket, a.compl, a.aktivitas
    FROM hrd.dkd_new0 as a LEFT JOIN hrd.ket as b on a.ketid=b.ketId 
    LEFT JOIN hrd.karyawan as c on a.karyawanid=c.karyawanId
    WHERE a.karyawanid='$pkryid' ";
$sql .=" AND a.tanggal BETWEEN '$ptgl1' AND '$ptgl2' ";
$query = "create TEMPORARY table $tmp01 ($sql)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$sql = "select a.idinput, a.karyawanid, a.namakaryawan, a.jabatanid, a.tanggal, a.tglinput, 
    c.dokterid, d.namalengkap, d.gelar, d.spesialis, c.jenis, c.notes, c.saran 
    FROM $tmp01 as a LEFT JOIN hrd.dkd_new1 as c on a.idinput=c.idinput JOIN dr.masterdokter as d on c.dokterid=d.id";
$query = "create TEMPORARY table $tmp02 ($sql)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$sql = "select a.idinput, a.karyawanid, c.nama as namakaryawan, a.tanggal, a.tglinput, a.ketid, b.nama as nama_ket, a.compl, a.aktivitas
    FROM hrd.dkd_new_real0 as a LEFT JOIN hrd.ket as b on a.ketid=b.ketId 
    LEFT JOIN hrd.karyawan as c on a.karyawanid=c.karyawanId
    WHERE a.karyawanid='$pkryid' ";
$sql .=" AND a.tanggal BETWEEN '$ptgl1' AND '$ptgl2' ";
$query = "create TEMPORARY table $tmp03 ($sql)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$sql = "select a.karyawanid, c.nama as namakaryawan, a.tanggal, a.tglinput, 
    a.dokterid, d.namalengkap, d.gelar, d.spesialis, a.jenis, a.notes, a.saran 
    FROM hrd.dkd_new_real1 as a JOIN dr.masterdokter as d on a.dokterid=d.id 
    LEFT JOIN hrd.karyawan as c on a.karyawanid=c.karyawanId
    WHERE a.karyawanid='$pkryid' ";
$sql .=" AND a.tanggal BETWEEN '$ptgl1' AND '$ptgl2' ";
$query = "create TEMPORARY table $tmp04 ($sql)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


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

$query = "INSERT INTO $tmp07 (tanggal, karyawanid, namakaryawan, dokterid) "
        . " select distinct tanggal, karyawanid, namakaryawan, dokterid FROM $tmp04 WHERE "
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
  <TITLE>Report Daily Plan & Realisasi</TITLE>
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

    echo "<b>Report Daily Plan & Realisasi</b><br/>";
    echo "<b>Nama : $pnamakarywanpl - $pkryid</b><br/>";
    //echo "<b>Jabatan : $pnamajabatan</b><br/>";
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

    echo "<br/><b>Visit</b><br/>";
    echo "<table id='tbltable' border='1' cellspacing='0' cellpadding='1'>";
        echo "<tr>";
        
            echo "<th align='left' rowspan='2'><small>Tanggal</small></th>";
            echo "<th align='cener' colspan='4'><small>Plan</small></th>";
            echo "<th align='cener' colspan='5'><small>Realisasi</small></th>";
            
        echo "</tr>";
        
        echo "<tr>";
        
            echo "<th align='left'><small>Jenis</small></th>";
            echo "<th align='left'><small>Dokter</small></th>";
            echo "<th align='left'><small>Notes</small></th>";
            echo "<th align='left'><small>Saran</small></th>";
            
            echo "<th align='left'><small>Jam</small></th>";
            echo "<th align='left'><small>Jenis</small></th>";
            echo "<th align='left'><small>Dokter</small></th>";
            echo "<th align='left'><small>Notes</small></th>";
            echo "<th align='left'><small>Saran</small></th>";
            
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
                    $ndoktid=$row0['dokterid'];
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
                    
                    $pnmdokt_=$nnamalengkap." (".$ngelar.") ".$nspesialis." - ".$ndoktid;
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
                    
                    if ($pada==false) {
                        echo "<tr>";
                        echo "<td nowrap>$ppilihtgl</td>";
                        echo "<td >$pnmjenis</td>";
                        echo "<td nowrap>$pnmdokt_</td>";
                        echo "<td >$nnotes</td>";
                        echo "<td >$nsaran</td>";
                        
                        $query = "select * from $tmp04 WHERE tanggal='$ftgl' AND karyawanid='$fkryid' AND dokterid='$pdokterid' order by tanggal, namalengkap";
                        $tampil2=mysqli_query($cnmy, $query);
                        $ketemu2=mysqli_num_rows($tampil2);
                        if ((INT)$ketemu2<=0) {
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td ></td>";
                            echo "<td ></td>";
                            
                            echo "</tr>";
                        }else{
                            $ifirst=false;
                            while ($row2=mysqli_fetch_array($tampil2)) {
                                
                                $ndoktid=$row2['dokterid'];
                                $ntgl=$row2['tanggal'];
                                $nnamalengkap=$row2['namalengkap'];
                                $nnotes=$row2['notes'];
                                $nsaran=$row2['saran'];
                                $njenis=$row2['jenis'];
                                $ngelar=$row2['gelar'];
                                $nspesialis=$row2['spesialis'];
                                $ntglinput=$row2['tglinput'];
                                
                                $pjam="";
                                if (!empty($ntglinput)) $pjam = date('H:i', strtotime($ntglinput));
                                
                                $pnmdokt_=$nnamalengkap." (".$ngelar.") ".$nspesialis." - ".$ndoktid;
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
                                
                                if ($ifirst==true) {
                                    echo "<tr>";
                                    echo "<td nowrap>$ppilihtgl</td>";
                                    echo "<td nowrap>&nbsp;</td>";
                                    echo "<td nowrap>&nbsp;</td>";
                                    echo "<td >&nbsp;</td>";
                                    echo "<td >&nbsp;</td>";
                                }
                                echo "<td nowrap>$pjam</td>";
                                echo "<td nowrap>$pnmjenis</td>";
                                echo "<td nowrap>$pnmdokt_</td>";
                                echo "<td >$nnotes</td>";
                                echo "<td >$nsaran</td>";

                                echo "</tr>";
                                $ifirst=true;
                            }
                            
                        }
                    }else{
                        echo "<tr>";
                        echo "<td nowrap>$ppilihtgl</td>";
                        echo "<td nowrap>&nbsp;</td>";
                        echo "<td nowrap>&nbsp;</td>";
                        echo "<td >&nbsp;</td>";
                        echo "<td >&nbsp;</td>";

                        echo "<td nowrap>$pjam</td>";
                        echo "<td nowrap>$pnmjenis</td>";
                        echo "<td nowrap>$pnmdokt_</td>";
                        echo "<td >$nnotes</td>";
                        echo "<td >$nsaran</td>";
                    }

                    echo "</tr>";


                }
                
                $psudahtanggal=true;
            
            }
            
        }
        
        
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