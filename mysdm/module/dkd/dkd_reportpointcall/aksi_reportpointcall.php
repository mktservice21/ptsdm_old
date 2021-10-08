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
$tmp06 =" dbtemp.tmprptpoincal06_".$puserid."_$now ";
$tmp07 =" dbtemp.tmprptpoincal07_".$puserid."_$now ";
$tmp08 =" dbtemp.tmprptpoincal08_".$puserid."_$now ";


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
$pjabatanid=$rowk['jabatanid'];
$pnmjbtkarywanpl=$rowk['nama_jabatan'];


//PLAN
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

//REALISASI
$sql = "select a.idinput, a.karyawanid, a.jabatanid, a.tanggal, a.ketid, b.nama as nama_ket,
    b.pointMR, b.pointSpv, b.pointDM 
    FROM hrd.dkd_new_real0 as a LEFT JOIN hrd.ket as b on a.ketid=b.ketId 
    WHERE a.karyawanid='$pkryid'";
$sql .=" AND LEFT(a.tanggal,7)= '$pbulan'";
$query = "create TEMPORARY table $tmp03 ($sql)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$sql = "select a.idinput, a.karyawanid, a.jabatanid, a.tanggal, 
    a.jenis, a.dokterid, b.namalengkap, b.gelar, b.spesialis 
    FROM hrd.dkd_new_real1 as a 
    LEFT JOIN dr.masterdokter as b on a.dokterid=b.id
    WHERE a.karyawanid='$pkryid'";
$sql .=" AND LEFT(a.tanggal,7)= '$pbulan'";
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


$query = "INSERT INTO $tmp02 (idinput, karyawanid, jabatanid, tanggal, 
    jenis, dokterid, namalengkap, gelar, spesialis) SELECT"
        . " idinput, karyawanid, jabatanid, tanggal, "
        . " jenis, dokterid, namalengkap, gelar, spesialis FROM $tmp04 WHERE CONCAT(karyawanid, tanggal, dokterid) NOT IN "
        . " (SELECT DISTINCT IFNULL(CONCAT(karyawanid, tanggal, dokterid),'') FROM $tmp05 WHERE IFNULL(jenis,'') NOT IN ('JV'))";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "DROP TEMPORARY TABLE $tmp05";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }



$query = "ALTER TABLE $tmp01 ADD jpoint INT(4)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query_point="";
if ($pjabatanid=="10" OR $pjabatanid=="18") {
    $query_point = "UPDATE $tmp01 SET jpoint=pointSpv";
}elseif ($pjabatanid=="08") {
    $query_point = "UPDATE $tmp01 SET jpoint=pointDM";
}elseif ($pjabatanid=="15") {
    $query_point = "UPDATE $tmp01 SET jpoint=pointMR";
}

if (!empty($query_point)) {
    mysqli_query($cnmy, $query_point); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
}



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
    //jpoint as diganti jadi count(distinct ketid)
    $query = "UPDATE $tmp05 as a JOIN (select '1' as idket, jpoint as jml from $tmp01 WHERE DATE_FORMAT(tanggal,'%d')='$pntgl' GROUP BY 1) as b on "
            . " IFNULL(a.idket,'')=IFNULL(b.idket,'') SET "
            . " a.".$pnmfield."=b.jml WHERE a.idket='1'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
    
    $query = "UPDATE $tmp05 as a JOIN (select '2' as idket, count(distinct dokterid) as jml from $tmp02 "
            . " WHERE IFNULL(jenis,'') NOT IN ('JV') AND DATE_FORMAT(tanggal,'%d')='$pntgl' GROUP BY 1) as b on "
            . " IFNULL(a.idket,'')=IFNULL(b.idket,'') SET "
            . " a.".$pnmfield."=b.jml WHERE a.idket='2'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
}



//summary persentase
$sql = "select karyawanid, idinput, jabatanid, tanggal, ketid, nama_ket,
    pointMR, pointSpv, pointDM, jpoint  
    FROM $tmp01";
$query = "create TEMPORARY table $tmp06 ($sql)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "create TEMPORARY table $tmp07 (select * from $tmp06)"; 
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "INSERT INTO $tmp06 (karyawanid, idinput, jabatanid, tanggal)"
        . " SELECT DISTINCT karyawanid, idinput, jabatanid, tanggal FROM $tmp02 WHERE CONCAT(karyawanid, tanggal) NOT IN "
        . " (SELECT DISTINCT IFNULL(CONCAT(karyawanid, tanggal),'') FROM $tmp07)"; 
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//ADD COLUMN jpoint DECIMAL(20,2), 
$query = "ALTER TABLE $tmp06 ADD totakv INT(4), ADD totvisit INT(4), ADD totjv INT(4), ADD totec INT(4), ADD sudahreal VARCHAR(1)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

/*
if ($pjabatanid=="10" OR $pjabatanid=="18") {
    $query = "UPDATE $tmp06 SET jpoint=pointSpv";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
}elseif ($pjabatanid=="08") {
    $query = "UPDATE $tmp06 SET jpoint=pointDM";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
}elseif ($pjabatanid=="15") {
    $query = "UPDATE $tmp06 SET jpoint=pointMR";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
}
*/

$query = "UPDATE $tmp06 SET totakv=1";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp06 as a JOIN (select karyawanid, tanggal, count(distinct dokterid) as jml FROM 
    $tmp02 WHERE IFNULL(jenis,'') NOT IN ('JV') GROUP BY 1,2) as b on a.karyawanid=b.karyawanid AND a.tanggal=b.tanggal SET a.totvisit=b.jml";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp06 as a JOIN (select karyawanid, tanggal, count(distinct dokterid) as jml FROM 
    $tmp02 WHERE IFNULL(jenis,'') IN ('EC') GROUP BY 1,2) as b on a.karyawanid=b.karyawanid AND a.tanggal=b.tanggal SET a.totec=b.jml";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "UPDATE $tmp06 as a JOIN (select karyawanid, tanggal, count(distinct dokterid) as jml FROM 
    $tmp02 WHERE IFNULL(jenis,'') IN ('JV') GROUP BY 1,2) as b on a.karyawanid=b.karyawanid AND a.tanggal=b.tanggal SET a.totjv=b.jml";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
//END summary persentase

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
    
    $query = "SELECT jumlah FROM hrd.hrkrj WHERE left(periode1,7)='$pbulan'";
    $tampilk=mysqli_query($cnmy, $query);
    $rowk=mysqli_fetch_array($tampilk);
    $jml_hari_krj=$rowk['jumlah'];

    if ($pjabatanid=='08') {
        $jab = 4;
    } else {
        if (($pjabatanid=='10') or ($pjabatanid=='18')) {
            $jab = 6;
        } else {
            if ($pjabatanid=='15') {
                $jab = 10;
            }
        }
    }
    if (empty($jab)) $jab=0;
    if (empty($jml_hari_krj)) $jml_hari_krj=0;

    $jpoint = (DOUBLE)$jab * (DOUBLE)$jml_hari_krj;
    
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
                
                $pwarnapoint="";
                if ($pjabatanid=="15") {
                    if ((INT)$pketjenis<10) $pwarnapoint=" style='color:red; font-size:15px;' ";
                }elseif ($pjabatanid=="10" OR $pjabatanid=="18") {
                    if ((INT)$pketjenis<6) $pwarnapoint=" style='color:red; font-size:15px;' ";
                }elseif ($pjabatanid=="08") {
                    if ((INT)$pketjenis<4) $pwarnapoint=" style='color:red; font-size:15px;' ";
                }
                if (empty($pketjenis)) $pwarnapoint="";
                
                echo "<td nowrap $pwarnapoint>$pketjenis</td>";
                
                if (empty($pketjenis)) $pketjenis="0";
                $pjmlvisit=(INT)$pjmlvisit+(INT)$pketjenis;
            }
                
            echo "<td nowrap align='right'>&nbsp;</td>";
            echo "</tr>";
                
        echo "</tbody>";
    echo "</table>";

    echo "<br/><br/>";
    
    
    $totcall=0;
    $totpoint1=0;
    $totpoint2=0;
    
    //echo "<div><b>Persentasi</b></div>";
    /*
    echo "<table id='tbltable' border='1' cellspacing='0' cellpadding='1'>";
        echo "<tr>";
            $header_ = add_space('Tanggal',40);
            echo "<th align='left'><small>$header_</small></th>";
            $header_ = add_space('Keterangan',60);
            echo "<th align='left'><small>$header_</small></th>";
            $header_ = add_space('Call',40);
            echo "<th align='left'><small>$header_</small></th>";
            $header_ = add_space('Point',40);
            echo "<th align='left'><small>$header_</small></th>";
        echo "</tr>";
        */
        $no=1;
        $query = "select distinct idinput, tanggal, jpoint, totakv, totvisit, totjv, totec, sudahreal, nama_ket from $tmp06 order by tanggal";
        $tampil0=mysqli_query($cnmy, $query);
        while ($row0=mysqli_fetch_array($tampil0)) {
            $cidinput=$row0['idinput'];
            $ntgl=$row0['tanggal'];
            $ntotakv=$row0['totakv'];
            $ntotvisit=$row0['totvisit'];
            $ntotec=$row0['totec'];
            $ntotjv=$row0['totjv'];
            $nsudahreal=$row0['sudahreal'];
            $nnamaket=$row0['nama_ket'];
            $ntotpoint=$row0['jpoint'];

            $totcall = (DOUBLE)$totcall + (DOUBLE)$ntotvisit;
            if ((DOUBLE)$ntotpoint != 0) {
                if ((DOUBLE)$ntotpoint >= 0) {
                    $totpoint2 = (DOUBLE)$totpoint2 + (DOUBLE)$ntotpoint;
                }else{
                    $totpoint1 = (DOUBLE)$totpoint1 + abs((DOUBLE)$ntotpoint);
                }
            }

            $ntotpoint=number_format($ntotpoint,0,"","");
            $ntotvisit=number_format($ntotvisit,0,"","");

            if (empty($ntotakv)) $ntotakv=1;
            if (empty($ntotvisit)) $ntotvisit=0;
            if (empty($ntotec)) $ntotec=0;

            $ntanggal = date('l d F Y', strtotime($ntgl));

            $xhari = $hari_array[(INT)date('w', strtotime($ntgl))];
            $xtgl= date('d', strtotime($ntgl));
            $xbulan = $bulan_array[(INT)date('m', strtotime($ntgl))];
            $xthn= date('Y', strtotime($ntgl));

            $pkettotal="$ntotakv Activity, $ntotvisit Visit";
            if ((INT)$ntotec>0) {
                $pkettotal="$ntotakv Activity, $ntotvisit Visit, $ntotec Extra Call";
            }
            /*
            echo "<tr>";
            echo "<td nowrap>$xhari, $xtgl $xbulan $xthn</td>";
            echo "<td nowrap>$nnamaket</td>";
            echo "<td nowrap align='right'>$ntotvisit</td>";
            echo "<td nowrap align='right'>$ntotpoint</td>";
            echo "</tr>";
            */
            $no++;
        }

        if ((DOUBLE)$jpoint-(DOUBLE)$totpoint1==0) {
            $summary_=0;
         }else{
            $summary_ = (((DOUBLE)$totcall+(DOUBLE)$totpoint2) / ((DOUBLE)$jpoint-(DOUBLE)$totpoint1)) * 100;
         }
    /*
        echo "<tr style='font-weight:bold;'>";
        echo "<td nowrap></td>";
        echo "<td nowrap>Summary : </td>";
        echo "<td nowrap align='right'>".round($summary_,2)." %</td>";
        echo "<td nowrap align='right'></td>";
        echo "</tr>";

    echo "</table>";
    */
         
    echo "<table id='tbltable' border='1' cellspacing='0' cellpadding='1'>";
        echo "<tr style='font-weight:bold;'>";
        echo "<td nowrap>Summary : </td>";
        echo "<td nowrap align='right'>".round($summary_,2)." %</td>";
        echo "</tr>";
    echo "</table>";
    
    echo "<br/><br/>";
    
    echo "<div style='font-size:14px;'>";
    
            $pjab_15="10";
            $pjab_10="6";
            $pjab_08="4";
            
            echo "<b><u>Point Sesuai Jabatan</u></b><br/>";
            echo "MR : $pjab_15<br/>";
            echo "AM : $pjab_10<br/>";
            echo "DM : $pjab_08<br/><br/>";
            
            echo "HARI KERJA $pperiode : $jml_hari_krj<br/>";
            //echo "Rumus :  <br/>";
            echo "POINT BULAN (POINT SESUAI JABATAN * JUMLAH HARI KERJA) = $jpoint<br/>";
            echo "POINT AKTIVITAS MINUS (TOTAL POINT AKTIVITAS LEBIH KECIL 0) = $totpoint1<br/>";
            echo "POINT AKTIVITAS PLUS (TOTAL POINT AKTIVITAS LEBIH BESAR 0) = $totpoint2<br/>";
            echo "<b>(TOTAL CALL + POINT AKTIVITAS PLUS) / (POINT BULAN - POINT AKTIVITAS MINUS) * 100 = SUMMARY</b><br/>";
            //echo "<b>($totcall + $totpoint2) / ($jpoint - $totpoint1) * 100 "." = <br/>".round($summary_,2)."%</b><br/>";
            echo "<b>($totcall + $totpoint2) / ($jpoint - $totpoint1) * 100 "." = </b><br/>";
            
            $pwarnasumary=" style='color:green; font-size:150px;' ";
            $psumarynya=round($summary_,2);
            if ((DOUBLE)$psumarynya<80) {
                $pwarnasumary=" style='color:red; font-size:150px;' ";
            }
            
            echo "<center><span $pwarnasumary><b>$psumarynya %</b></span></center>";
    echo "</div>";
    
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
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp06");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp07");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp08");
    mysqli_close($cnmy);
?>