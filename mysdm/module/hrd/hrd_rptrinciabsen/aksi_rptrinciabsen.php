<?PHP

date_default_timezone_set('Asia/Jakarta');
ini_set("memory_limit","512M");
ini_set('max_execution_time', 0);

session_start();
if (!isset($_SESSION['USERID'])) {
    echo "ANDA HARUS LOGIN ULANG....";
    exit;
}


$ppilihrpt="";
if (isset($_GET['ket'])) $ppilihrpt=$_GET['ket'];

if ($ppilihrpt=="excel") {
    // Fungsi header dengan mengirimkan raw data excel
    header("Content-type: application/vnd-ms-excel");
    // Mendefinisikan nama file ekspor "hasil-export.xls"
    header("Content-Disposition: attachment; filename=Report Detail Absensi.xls");
}

$pmodule=$_GET['module'];

include("config/koneksimysqli.php");
include("config/fungsi_sql.php");
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


$sql = "select id_status, karyawanid, kode_absen, tanggal, jam, keterangan, l_status FROM hrd.t_absen "
        . " WHERE 1=1 ";
if (!empty($pkryid)) $sql .=" AND karyawanid='$pkryid'";
$sql .=" AND LEFT(tanggal,7)= '$pbulan'";
$query = "create TEMPORARY table $tmp01 ($sql)";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "ALTER TABLE $tmp01 ADD COLUMN nama_karyawan VARCHAR(100), ADD COLUMN nama_absen VARCHAR(100), ADD COLUMN cuti_masal VARCHAR(1) DEFAULT 'N', "
        . " ADD COLUMN libur VARCHAR(1) DEFAULT 'N', ADD COLUMN jam_masuk_sdm VARCHAR(5) DEFAULT '', ADD COLUMN terlambat_sdm INT(4) DEFAULT '0', "
        . " ADD COLUMN ket_absen VARCHAR(100) DEFAULT ''";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 as a JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId SET a.nama_karyawan=b.nama";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 as a JOIN hrd.t_absen_kode as b on a.kode_absen=b.kode_absen SET a.nama_absen=b.nama_absen";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 SET id_status='HO1' WHERE IFNULL(id_status,'')=''";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "UPDATE $tmp01 as a JOIN hrd.t_absen_status as b on a.kode_absen=b.kode_absen AND a.id_status=b.id_status SET a.jam_masuk_sdm=b.jam, "
        . " a.terlambat_sdm=b.menit_terlambat";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "UPDATE $tmp01 SET jam='' WHERE IFNULL(jam,'')=''";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 SET jam_masuk_sdm='' WHERE IFNULL(jam_masuk_sdm,'')=''";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 SET terlambat_sdm=0 WHERE IFNULL(terlambat_sdm,'')=''";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 SET ket_absen='Tidak Absen' WHERE IFNULL(jam,'')=''";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 SET ket_absen="
        . " CASE WHEN jam='' THEN 'KOSONG' 
            ELSE
                CASE WHEN jam<=jam_masuk_sdm THEN 'TEPATWAKTU'
                ELSE
                    CASE WHEN RIGHT(jam,2)>terlambat_sdm THEN 'TERLAMBAT'
                    ELSE
			'TEPATWAKTU'
                    END
                END
            END";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }





//CUSTI MASAL

$query = "SELECT DISTINCT b.tanggal FROM hrd.t_cuti0 as a "
        . " JOIN hrd.t_cuti1 as b on a.idcuti=b.idcuti where a.id_jenis='00' "
        . " AND a.karyawanid IN ('ALL', 'ALLHO') AND IFNULL(a.stsnonaktif,'')<>'Y' "
        . " AND LEFT(b.tanggal,7)= '$pbulan'";
$query = "create TEMPORARY table $tmp03 ($query)";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        $query = "INSERT INTO $tmp03 (tanggal)values('2021-08-10')";
        //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        $query = "INSERT INTO $tmp03 (tanggal)values('2021-08-17')";
        //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "UPDATE $tmp01 as a JOIN $tmp03 as b on a.tanggal=b.tanggal SET a.cuti_masal='Y'";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//END CUSTI MASAL


//LIBUR dan JUMLAH HARI KERJA
$query = "CREATE TEMPORARY TABLE $tmp04 (tanggal DATE, libur VARCHAR(1) DEFAULT 'N', libur_cmasal VARCHAR(1) DEFAULT 'N')";
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
    
    $query = "UPDATE $tmp04 as a JOIN $tmp03 as b on a.tanggal=b.tanggal SET a.libur_cmasal='Y'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

}

$query = "UPDATE $tmp01 as a JOIN $tmp04 as b on a.tanggal=b.tanggal SET a.libur=b.libur";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//END LIBUR dan JUMLAH HARI KERJA

$query ="select distinct karyawanid, nama_karyawan FROM $tmp01";
$query = "create TEMPORARY table $tmp02 ($query)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "ALTER TABLE $tmp02 ADD COLUMN jmasuk INT(4), ADD COLUMN jterlambat INT(4), ADD COLUMN jistirahat INT(4), ADD COLUMN jmasuk_ist INT(4), "
        . " ADD COLUMN jpulang INT(4), ADD COLUMN jabsenlibur INT(4), ADD COLUMN jtidakabsen INT(4)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//absen masuk
$query = "UPDATE $tmp02 as a JOIN (SELECT karyawanid, COUNT(DISTINCT tanggal) as jumlah FROM $tmp01 WHERE kode_absen='1' GROUP BY 1) as b "
        . " on a.karyawanid=b.karyawanid SET a.jmasuk=b.jumlah";//AND IFNULL(cuti_masal,'')<>'Y' AND IFNULL(libur,'')<>'Y'
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//absen masuk hari libur dan cuti masal
$query = "UPDATE $tmp02 as a JOIN (SELECT karyawanid, COUNT(DISTINCT tanggal) as jumlah FROM $tmp01 WHERE kode_absen='1' AND (IFNULL(cuti_masal,'')='Y' OR IFNULL(libur,'')='Y') GROUP BY 1) as b "
        . " on a.karyawanid=b.karyawanid SET a.jabsenlibur=b.jumlah";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//absen pulang
$query = "UPDATE $tmp02 as a JOIN (SELECT karyawanid, COUNT(DISTINCT tanggal) as jumlah FROM $tmp01 WHERE kode_absen='2' GROUP BY 1) as b "
        . " on a.karyawanid=b.karyawanid SET a.jpulang=b.jumlah";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//absen istirahat
$query = "UPDATE $tmp02 as a JOIN (SELECT karyawanid, COUNT(DISTINCT tanggal) as jumlah FROM $tmp01 WHERE kode_absen='3' GROUP BY 1) as b "
        . " on a.karyawanid=b.karyawanid SET a.jistirahat=b.jumlah";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//absen masuk istirahat
$query = "UPDATE $tmp02 as a JOIN (SELECT karyawanid, COUNT(DISTINCT tanggal) as jumlah FROM $tmp01 WHERE kode_absen='4' GROUP BY 1) as b "
        . " on a.karyawanid=b.karyawanid SET a.jmasuk_ist=b.jumlah";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//absen terlambat 
$query = "UPDATE $tmp02 as a JOIN (SELECT karyawanid, COUNT(DISTINCT tanggal) as jumlah FROM $tmp01 WHERE kode_absen='1' AND IFNULL(ket_absen,'')='TERLAMBAT' GROUP BY 1) as b "
        . " on a.karyawanid=b.karyawanid SET a.jterlambat=b.jumlah";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "select tanggal FROM $tmp04 WHERE IFNULL(libur,'')<>'Y' AND IFNULL(libur_cmasal,'')<>'Y'";
$tampill=mysqli_query($cnmy, $query);
$pjmlharikerjasdm=mysqli_num_rows($tampill);

$query = "select tanggal FROM $tmp04 WHERE IFNULL(libur_cmasal,'')='Y'";
$tampilcm=mysqli_query($cnmy, $query);
$pjmlcutimasal=mysqli_num_rows($tampilcm);


if (empty($pjmlharikerjasdm)) $pjmlharikerjasdm=0;
if (empty($pjmlcutimasal)) $pjmlcutimasal=0;


//tidak absen
$query = "UPDATE $tmp02 SET jtidakabsen=".(DOUBLE)$pjmlharikerjasdm."-IFNULL(jmasuk,0)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }



//RINCIAN

$query ="drop TEMPORARY table if EXISTS $tmp03";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query ="select distinct b.karyawanid, b.nama_karyawan, a.tanggal, a.libur, a.libur_cmasal FROM $tmp04 as a, $tmp01 as b";
$query = "create TEMPORARY table $tmp03 ($query)";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "ALTER TABLE $tmp03 ADD COLUMN jam_masuk VARCHAR(5), ADD COLUMN jam_istirahat VARCHAR(5), ADD COLUMN jam_masuk_ist VARCHAR(5), "
        . " ADD COLUMN jam_pulang VARCHAR(5), ADD COLUMN ket_absen VARCHAR(100), ADD COLUMN keterangan VARCHAR(300), ADD COLUMN keterangan_p VARCHAR(300), ADD COLUMN l_status VARCHAR(100)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "UPDATE $tmp03 as a JOIN (select karyawanid, tanggal, jam, ket_absen FROM $tmp01 WHERE kode_absen='1') as b "
        . " on a.karyawanid=b.karyawanid AND a.tanggal=b.tanggal SET a.jam_masuk=b.jam, a.ket_absen=b.ket_absen";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//keterangan
$query = "UPDATE $tmp03 as a JOIN (select karyawanid, tanggal, keterangan, l_status FROM $tmp01 WHERE kode_absen='1') as b "
        . " on a.karyawanid=b.karyawanid AND a.tanggal=b.tanggal SET a.keterangan=b.keterangan, a.l_status=b.l_status";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "UPDATE $tmp03 as a JOIN (select karyawanid, tanggal, jam FROM $tmp01 WHERE kode_absen='2') as b "
        . " on a.karyawanid=b.karyawanid AND a.tanggal=b.tanggal SET a.jam_pulang=b.jam";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//keterangan
$query = "UPDATE $tmp03 as a JOIN (select karyawanid, tanggal, keterangan, l_status FROM $tmp01 WHERE kode_absen='2') as b "
        . " on a.karyawanid=b.karyawanid AND a.tanggal=b.tanggal SET a.keterangan_p=b.keterangan";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp03 as a JOIN (select karyawanid, tanggal, jam FROM $tmp01 WHERE kode_absen='3') as b "
        . " on a.karyawanid=b.karyawanid AND a.tanggal=b.tanggal SET a.jam_istirahat=b.jam";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp03 as a JOIN (select karyawanid, tanggal, jam FROM $tmp01 WHERE kode_absen='4') as b "
        . " on a.karyawanid=b.karyawanid AND a.tanggal=b.tanggal SET a.jam_masuk_ist=b.jam";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


//END RINCIAN



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
  <TITLE>Report Detail Absensi</TITLE>
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
    echo "<b>Report Detail Absensi</b><br/>";
    echo "<b>Periode : $pperiode</b><br/>";
    
    echo "<b>Hari Kerja : $pjmlharikerjasdm</b><br/>";
    if ((INT)$pjmlcutimasal>0) {
        echo "<b>Cuti Massal : $pjmlcutimasal</b><br/>";
    }
    
    if (!empty($pkryid)) {
        //echo "<b>Nama : $pnamakarywanpl - $pkryid</b><br/>";
        //echo "<b>Jabatan : $pnmjbtkarywanpl</b><br/>";
    }
    
    $printdate= date("d/m/Y H:i");
    echo "<i>view date : $printdate</i><br/>";
    
    echo "<hr/><br/>";
    
    echo "<table id='tbltable' border='1' cellspacing='0' cellpadding='1'>";
        echo "<thead>";
            echo "<tr>";
                echo "<th align='center' rowspan='2'><small>Tanggal</small></th>";
                echo "<th align='center' colspan='7'><small>Absen</small></th>";
                //echo "<th align='center' colspan='4'><small>Absen</small></th>";
                echo "<th align='center' rowspan='2'><small>Durasi</small></th>";
                echo "<th align='center' rowspan='2'><small>&nbsp;</small></th>";
            echo "</tr>";
            
            echo "<tr>";
                echo "<th align='center'><small>Masuk</small></th>";
                echo "<th align='center'><small>Telat</small></th>";
                echo "<th align='center'><small>Keterangan</small></th>";
                echo "<th align='center'><small>Istirahat</small></th>";
                echo "<th align='center'><small>Masuk Istirahat</small></th>";
                echo "<th align='center'><small>Pulang</small></th>";
                echo "<th align='center'><small>Keterangan</small></th>";
            echo "</tr>";
            
        echo "</thead>";
        
        echo "<tbody>";
        
            $no=1;//- INTERVAL '60' MINUTE
            $query = "select *, CASE WHEN IFNULL(jam_pulang,'')='' THEN '' ELSE LEFT(timediff(jam_pulang, jam_masuk),5) END as lamawaktu from $tmp03 order by nama_karyawan, karyawanid, tanggal";
            $tampil0=mysqli_query($cnmy, $query);
            while ($row0=mysqli_fetch_array($tampil0)) {
                $nkryid=$row0['karyawanid'];
                $nkrynm=$row0['nama_karyawan'];
                $ntgl=$row0['tanggal'];
                $nlibur=$row0['libur'];
                $ncutimasal=$row0['libur_cmasal'];
                $njammasuk=$row0['jam_masuk'];
                $njampulang=$row0['jam_pulang'];
                $njamistirahat=$row0['jam_istirahat'];
                $njammmst_ist=$row0['jam_masuk_ist'];
                $nlamawaktu=$row0['lamawaktu'];
                $nketerangan=$row0['keterangan'];
                $nketerangan_p=$row0['keterangan_p'];
                $nket_abs=$row0['ket_absen'];
                $nstatusabs=$row0['l_status'];
                
                
                $pselisih_jam="";
                $pselisih_ist="";
                $pselisih_telat="";
                $pliburannone="";
                if ($nlibur=="Y" OR $ncutimasal=="Y") $pliburannone="Y";
                //0 = hitung selisih masuk, pulang dan istirahat, 1 = hitung hanya masuk dan pulang, 2 = hitung hanya istirahat, 3 = telat
                //$pselisih_jam=CariSelisihJamMenit02("0", $pliburannone, $ntgl, $njammasuk, $njampulang, $njamistirahat, $njammmst_ist);
                //$pselisih_jam=CariSelisihJamMenit01("0", $pliburannone, $ntgl, $njammasuk, $njampulang);
                $pselisih_jam=CariSelisihJamMenit("1", $pliburannone, $ntgl, $njammasuk, $njampulang);
                $pselisih_ist=CariSelisihJamMenit("1", $pliburannone, $ntgl, $njamistirahat, $njammmst_ist);
                $pselisih_telat=CariSelisihJamMenit("3", $pliburannone, $ntgl, "08:00", $njammasuk);
                
                if ( (!empty($pselisih_jam) && $pselisih_jam<>"invalid") ) {
                    if ((INT)substr($pselisih_jam,0,2)>=8) {
                        //$pselisih_jam="08:00";
                    }
                }
                /*
                if ( (!empty($pselisih_jam) && $pselisih_jam<>"invalid") ) {
                    
                    if ( (INT)substr($pselisih_jam,0,2)>4 ) {
                        if ( (empty($pselisih_ist) && $pselisih_ist<>"invalid") ) $pselisih_ist="01:00";
                        
                        //$pselisih_jam="09:00";
                        //$pselisih_ist="01:00";
                        
                        $pjamnya_="0";
                        $pmenitya_="0";
                        
                        $pjamnya_=(INT)substr($pselisih_jam,0,2)-(INT)substr($pselisih_ist,0,2);
                        if ((INT)substr($pselisih_ist,3,2)<>0) {
                            $pmenitya_=(INT)substr($pselisih_jam,3,2)-(INT)substr($pselisih_ist,3,2);
                        }
                        
                        if ((INT)$pmenitya_<0) {
                            //$pmenitya_=-1*(INT)$pmenitya_;
                            //$pjamnya_=(INT)$pjamnya_-1;
                        }
                        
                        $pjamnya_ = str_pad($pjamnya_, 2, '0', STR_PAD_LEFT);
                        $pmenitya_ = str_pad($pmenitya_, 2, '0', STR_PAD_LEFT);
                        
                        $pselisih_jam =$pjamnya_.":".$pmenitya_;
                        
                        //$pselisih_jam .=" : ".$pselisih_ist;
                        //$pselisih_jam=CariSelisihJamMenit("1", $pliburannone, $ntgl, $pselisih_jam, $pselisih_ist);
                        
                        if ((INT)substr($pselisih_jam,0,2)>=8) {
                            $pselisih_jam="08:00";
                        }
                        
                    }else{
                        if ( (INT)substr($pselisih_jam,0,2)<0 ) {
                            $pselisih_jam="00:".substr($pselisih_jam,3,2);
                        }
                    }
                    
                }
                */
                
                
                $xhari = $hari_array[(INT)date('w', strtotime($ntgl))];
                $xtgl= date('d', strtotime($ntgl));
                $xbulan = $bulan_array[(INT)date('m', strtotime($ntgl))];
                $xthn= date('Y', strtotime($ntgl));
                
                $pharitanggal="$xhari, $xtgl $xbulan $xthn";
                
                $nketerangan_abs="";
                $pclasslibur="";
                $pclasslibur_rd="";
                if ($nlibur=="Y" OR $ncutimasal=="Y") {
                    $pclasslibur=" style='color:#880808;' ";
                    if ($pselisih_jam=="invalid") $pselisih_jam="";
                }else{
                    if ($nket_abs=="TERLAMBAT") {
                        $nketerangan_abs="terlambat";
                        $pclasslibur_rd=" style='color:red;' ";
                    }
                }
                
                $nketerangan_abs="";//dihilangkat statusnya
                if (!empty($nketerangan)) {
                    if (empty($nketerangan_abs)) $nketerangan_abs=$nketerangan;
                    else $nketerangan_abs=$nketerangan.", ".$nketerangan_abs;
                }
                
                echo "<tr $pclasslibur>";
                echo "<td nowrap>$pharitanggal</td>";
                echo "<td nowrap $pclasslibur_rd>$njammasuk</td>";
                echo "<td nowrap $pclasslibur_rd>$pselisih_telat</td>";
                echo "<td nowrap >$nketerangan</td>";
                echo "<td nowrap >$njamistirahat</td>";
                echo "<td nowrap >$njammmst_ist</td>";
                echo "<td nowrap >$njampulang</td>";
                echo "<td nowrap >$nketerangan_p</td>";
                echo "<td nowrap >$pselisih_jam</td>";
                echo "<td nowrap >$nstatusabs</td>";
                echo "</tr>";
                
                $no++;
            }
            
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
    mysqli_close($cnmy);
?>