<?PHP
date_default_timezone_set('Asia/Jakarta');
ini_set("memory_limit","512M");
ini_set('max_execution_time', 0);

session_start();
if (!isset($_SESSION['USERID'])) {
    echo "ANDA HARUS LOGIN ULANG....";
    exit;
}


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


$ppilformat=1;
$ppilihrpt="";
if (isset($_GET['ket'])) $ppilihrpt=$_GET['ket'];

if ($ppilihrpt=="excel") {
    $ppilformat=3;
    // Fungsi header dengan mengirimkan raw data excel
    header("Content-type: application/vnd-ms-excel");
    // Mendefinisikan nama file ekspor "hasil-export.xls"
    header("Content-Disposition: attachment; filename=Report Absensi By Finance.xls");
}

$pmodule=$_GET['module'];

include("config/koneksimysqli.php");
include("config/fungsi_sql.php");
include("config/common.php");
include "config/fungsi_ubahget_id.php";

$puserid=$_SESSION['USERID'];
$now=date("mdYhis");
$tmp00 =" dbtemp.tmprptabsfin00_".$puserid."_$now ";
$tmp01 =" dbtemp.tmprptabsfin01_".$puserid."_$now ";
$tmp02 =" dbtemp.tmprptabsfin02_".$puserid."_$now ";
$tmp03 =" dbtemp.tmprptabsfin03_".$puserid."_$now ";
$tmp04 =" dbtemp.tmprptabsfin04_".$puserid."_$now ";

$pjamkerja_wfo=0;
$pjamkerja_wfh=0;

$query ="select jam_kerja_wfo_y, jam_kerja_wfo_n from hrd.t_absen_jam_kerja WHERE IFNULL(id_status,'')='HO1'";
$tampilw=mysqli_query($cnmy, $query);
$roww=mysqli_fetch_array($tampilw);
$pjamkerja_wfo=$roww['jam_kerja_wfo_y'];
$pjamkerja_wfh=$roww['jam_kerja_wfo_n'];

if (empty($pjamkerja_wfo)) $pjamkerja_wfo=0;
if (empty($pjamkerja_wfh)) $pjamkerja_wfh=0;

$hari_ini = date("Y-m-d");
$pkryid_ = $_GET['i']; 
$pbln_ = $_GET['b'];

$pkryid=decodeString($pkryid_);
$pblnpilih=decodeString($pbln_);
//$pblnpilih="202109"; //hilangkan
if (empty($pkryid) OR empty($pblnpilih)) {
    goto hapusdata;
}

$pthnbln_=substr($pblnpilih,0,4)."-".substr($pblnpilih,4,2);
$ptanggal = date('Y-m-01', strtotime($pthnbln_));

$ptgl01 = "01";
$ptgl02 = date('t', strtotime($ptanggal));
$nbln = date('m', strtotime($ptanggal));
$nthn = date('Y', strtotime($ptanggal));
$pbulan = date('Y-m', strtotime($ptanggal));
$pperiode = date('F Y', strtotime($ptanggal));



$query = "select a.nama, a.jabatanId as jabatanid, b.nama as nama_jabatan, a.divisiId as divisiid from hrd.karyawan as a 
    LEFT join hrd.jabatan as b on a.jabatanId=b.jabatanId 
    where a.karyawanid='$pkryid'";
$tampilk=mysqli_query($cnmy, $query);
$rowk=mysqli_fetch_array($tampilk);
$pnamakarywanpl=$rowk['nama'];
$pnmjbtkarywanpl=$rowk['nama_jabatan'];
$pidjbtkarywanpl=$rowk['jabatanid'];
$piddivisipl=$rowk['divisiid'];

$prupiah_um=0;
//cari rp biaya rutin uang makan
$query = "select rupiah, rupiah_otc from dbmaster.t_brrutin_rp_jbt WHERE jabatanid='$pidjbtkarywanpl' AND nobrid='04'";
$tampilum=mysqli_query($cnmy, $query);
$rowu=mysqli_fetch_array($tampilum);
$pum_jbt=$rowu['rupiah'];
$pum_jbt_otc=$rowu['rupiah_otc'];

if (empty($pum_jbt)) $pum_jbt=0;
if (empty($pum_jbt_otc)) $pum_jbt_otc=0;

if ($piddivisipl=="OTC" AND (DOUBLE)$pum_jbt_otc>0) $pum_jbt=$pum_jbt_otc;


$query = "select rupiah from dbmaster.t_brrutin_rp_person WHERE karyawanid='$pkryid' AND nobrid='04'";
$tampilum2=mysqli_query($cnmy, $query);
$rowu2=mysqli_fetch_array($tampilum2);
$pum_person=$rowu2['rupiah'];

if (empty($pum_person)) $pum_person=0;

$prupiah_um=$pum_jbt;
if ((DOUBLE)$pum_person>0) $prupiah_um=$pum_person;

//echo "$pkryid, $ptanggal dan $pblnpilih ($pperiode) Rp. $prupiah_um";


//LIBUR dan JUMLAH HARI KERJA
$query = "CREATE TEMPORARY TABLE $tmp00 (tanggal DATE, libur VARCHAR(1) DEFAULT 'N', libur_cmasal VARCHAR(1) DEFAULT 'N')";
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
    
    $query = "INSERT INTO $tmp00 (tanggal, libur) VALUES ".implode(', ', $pinsert_data_detail);
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) {  goto hapusdata; }

}

//END LIBUR


//CUSTI MASAL

$query = "SELECT DISTINCT b.tanggal FROM hrd.t_cuti0 as a "
        . " JOIN hrd.t_cuti1 as b on a.idcuti=b.idcuti where a.id_jenis IN ('00', '12') "
        . " AND a.karyawanid IN ('ALL', 'ALLHO') AND IFNULL(a.stsnonaktif,'')<>'Y' "
        . " AND LEFT(b.tanggal,7)= '$pbulan'";
$query = "create TEMPORARY table $tmp01 ($query)";
mysqli_query($cnmy, $query); 
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        $query = "INSERT INTO $tmp01 (tanggal)values('2021-08-10')";
        //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        $query = "INSERT INTO $tmp01 (tanggal)values('2021-08-17')";
        //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//END CUSTI MASAL

        
//CUTI dan LIBUR
if ($psimpandata==true) {
    
    $query = "UPDATE $tmp00 as a JOIN $tmp01 as b on a.tanggal=b.tanggal SET a.libur_cmasal='Y'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

}else{
    $query = "INSERT INTO $tmp00 (tanggal, libur_cmasal) SELECT DISTINCT tanggal, 'Y' as libur_cmasal FROM $tmp01";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
}

$query="drop TEMPORARY table if EXISTS $tmp01";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) {  goto hapusdata; }




//JUMLAH LIBUR DAN HARI KERJA
$query = "select tanggal FROM $tmp00 WHERE IFNULL(libur,'')<>'Y' AND IFNULL(libur_cmasal,'')<>'Y'";
$tampill=mysqli_query($cnmy, $query);
$pjmlharikerjasdm=mysqli_num_rows($tampill);

$query = "select tanggal FROM $tmp00 WHERE IFNULL(libur_cmasal,'')='Y'";
$tampilcm=mysqli_query($cnmy, $query);
$pjmlcutimasal=mysqli_num_rows($tampilcm);


if (empty($pjmlharikerjasdm)) $pjmlharikerjasdm=0;
if (empty($pjmlcutimasal)) $pjmlcutimasal=0;

//END JUMLAH LIBUR DAN HARI KERJA



$query = "select idabsen, id_status, karyawanid, kode_absen, tanggal, jam, l_status, l_latitude, l_longitude, keterangan "
        . " FROM hrd.t_absen WHERE "
        . " karyawanid='$pkryid' AND LEFT(tanggal,7)= '$pbulan'";
$query = "create TEMPORARY table $tmp01 ($query)";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 SET id_status='HO1' WHERE IFNULL(id_status,'')=''";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 SET jam='' WHERE IFNULL(jam,'')=''";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "ALTER TABLE $tmp01 ADD COLUMN nama_karyawan VARCHAR(200), ADD COLUMN jam_masuk_sdm VARCHAR(5) DEFAULT '', "
        . " ADD COLUMN terlambat_sdm INT(4) DEFAULT '0', ADD COLUMN ket_absen VARCHAR(100) DEFAULT '',"
        . " ADD COLUMN libur_cmasal VARCHAR(1) DEFAULT 'N', ADD COLUMN libur VARCHAR(1) DEFAULT 'N'";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


//UPDATE YANG LIBUR TAPI ABSEN
$query = "UPDATE $tmp01 as a JOIN $tmp00 as b on a.tanggal=b.tanggal SET a.libur_cmasal=b.libur_cmasal, a.libur=b.libur";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 SET libur='Y' WHERE IFNULL(libur_cmasal,'')='Y'";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
//END UPDATE YANG LIBUR TAPI ABSEN


$query = "UPDATE $tmp01 as a JOIN (select * from hrd.t_absen_status) as b "
        . " on IFNULL(a.id_status,'')=IFNULL(b.id_status,'') AND a.kode_absen=b.kode_absen AND a.id_status=b.id_status SET "
        . " a.jam_masuk_sdm=CONCAT(LEFT(b.jam,3), LPAD(IFNULL(b.menit_terlambat,0), 2, '0') ), "
        . " a.terlambat_sdm=b.menit_terlambat";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 SET jam_masuk_sdm='' WHERE IFNULL(jam_masuk_sdm,'')=''";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 SET terlambat_sdm=0 WHERE IFNULL(terlambat_sdm,'')=''";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 SET ket_absen='Tidak Absen' WHERE IFNULL(jam,'')=''";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "UPDATE $tmp01 SET jam='09:00' WHERE kode_absen='1'";
//mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 SET ket_absen="
        . " CASE WHEN jam='' THEN 'KOSONG' 
            ELSE
                CASE WHEN jam<=jam_masuk_sdm THEN 'TEPATWAKTU'
                ELSE
                    CASE WHEN RIGHT(jam,2)>terlambat_sdm THEN 'TERLAMBAT'
                    ELSE
                        CASE WHEN LEFT(jam,2)=LEFT(jam_masuk_sdm,2) THEN 
                            CASE WHEN RIGHT(jam,2)>terlambat_sdm THEN 'TERLAMBAT'
                            ELSE
                                'TEPATWAKTU'
                            END
                        ELSE
                            CASE WHEN LEFT(jam,2)>=LEFT(jam_masuk_sdm,2) THEN 'TERLAMBAT'
                            ELSE
                                'TEPATWAKTU'
                            END
                        END
                    END
                END
            END";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }



//CEK JAM KERJA DAN JADWAL WFO / WFH

$query = "ALTER TABLE $tmp01 ADD COLUMN lantai INT(4), ADD COLUMN j_wfo VARCHAR(1), ADD COLUMN ex_jamkerja VARCHAR(1), ADD COLUMN jam_kerja_wfo_ex INT(4)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "UPDATE $tmp01 as a JOIN dbmaster.t_karyawan_posisi as b on a.karyawanid=b.karyawanId SET a.lantai=b.lantai";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$pfilterkaryawanex=" ('', 'ALL', 'all', 'All', 'ALLHO', '$pkryid') ";
$query ="select distinct a.karyawanid, a.tanggal, a.jam_kerja_wfo FROM hrd.t_absen_jam_kerja_ex as a JOIN "
        . " (select distinct id_status, tanggal FROM $tmp01) as b on a.tanggal=b.tanggal WHERE "
        . " IFNULL(a.karyawanid,'') IN $pfilterkaryawanex "
        . " AND a.id_status=b.id_status";
$query = "create TEMPORARY table $tmp02 ($query)";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


// PENGECUALIAN

$query = "SELECT DISTINCT a.karyawanid, b.tanggal, b.jam_kerja_wfo FROM $tmp01 as a, "
        . " (SELECT * FROM $tmp02 WHERE IFNULL(karyawanid,'') IN $pfilterkaryawanex) as b";
$query = "create TEMPORARY table $tmp03 ($query)";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "INSERT INTO $tmp03 (karyawanid, tanggal, jam_kerja_wfo) SELECT DISTINCT karyawanid, tanggal, jam_kerja_wfo "
        . " FROM $tmp02 WHERE IFNULL(karyawanid,'') NOT IN $pfilterkaryawanex";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "UPDATE $tmp01 as a JOIN $tmp03 as b on a.tanggal=b.tanggal AND IFNULL(a.karyawanid,'')=IFNULL(b.karyawanid,'') SET "
        . " a.ex_jamkerja='Y', a.jam_kerja_wfo_ex=b.jam_kerja_wfo";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

// END PENGECUALIAN

$query = "UPDATE $tmp01 as a JOIN hrd.t_absen_jadwal_wfo as b on a.tanggal=b.tanggal AND IFNULL(a.lantai,'')=IFNULL(b.lantai,'') SET "
        . " a.j_wfo='Y'";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//END CEK JAM KERJA DAN JADWAL WFO / WFH


//GAMBAR / FOTO
$query="drop TEMPORARY table if EXISTS $tmp02";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) {  goto hapusdata; }
$query="drop TEMPORARY table if EXISTS $tmp03";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) {  goto hapusdata; }

$query = "select a.idabsen, a.kode_absen, a.tanggal, a.nama FROM dbimages2.img_absen as a "
        . " JOIN $tmp01 as b on a.idabsen=b.idabsen AND a.kode_absen=b.kode_absen";
$query = "create TEMPORARY table $tmp02 ($query)";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "ALTER TABLE $tmp01 ADD COLUMN nama_gambar VARCHAR(200)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 as a JOIN $tmp02 as b on a.idabsen=b.idabsen AND a.tanggal=b.tanggal AND a.kode_absen=b.kode_absen SET "
        . " a.nama_gambar=b.nama";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


//END GAMBAR / FOTO
    


$query = "select DISTINCT karyawanid, nama_karyawan, lantai, j_wfo, ex_jamkerja, jam_kerja_wfo_ex, "
        . " tanggal, libur, libur_cmasal, jam as jam_masuk, jam_masuk_sdm, terlambat_sdm, ket_absen, "
        . " l_status, l_latitude, l_longitude, nama_gambar, keterangan "
        . " from $tmp01 WHERE kode_absen='1'";;
$query = "create TEMPORARY table $tmp03 ($query)";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "ALTER TABLE $tmp03 ADD COLUMN jam_pulang VARCHAR(5), ADD COLUMN keterangan_p VARCHAR(300), ADD COLUMN nama_gambar_p VARCHAR(300), "
        . " ADD COLUMN lamawaktu VARCHAR(5), "
        . " ADD COLUMN jam_istirahat VARCHAR(5), ADD COLUMN jam_masuk_ist VARCHAR(5)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp03 as a JOIN (select * from $tmp01 WHERE kode_absen='2') as b on a.karyawanid=b.karyawanid AND a.tanggal=b.tanggal SET "
        . " a.jam_pulang=b.jam, a.keterangan_p=b.keterangan, a.nama_gambar_p=b.nama_gambar";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp03 SET lamawaktu=CASE WHEN IFNULL(jam_pulang,'')='' THEN '' ELSE LEFT(timediff(jam_pulang, jam_masuk),5) END";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp03 as a JOIN (select * from $tmp01 WHERE kode_absen='3') as b on a.karyawanid=b.karyawanid AND a.tanggal=b.tanggal SET "
        . " a.jam_istirahat=b.jam";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp03 as a JOIN (select * from $tmp01 WHERE kode_absen='4') as b on a.karyawanid=b.karyawanid AND a.tanggal=b.tanggal SET "
        . " a.jam_masuk_ist=b.jam";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


//$query = "create table $tmp03 (select * from $tmp01)"; mysqli_query($cnmy, $query);
//echo "OK"; goto hapusdata;

?>


<HTML>
<HEAD>
  <TITLE>Report Absensi By Finance</TITLE>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2030 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <link rel="shortcut icon" href="images/icon.ico" />
    <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet"> 
    <style> .str{ mso-number-format:\@; } </style>
    <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="vendors/jquery/dist/jquery.min.js"></script>
</HEAD>


<BODY class="nav-md">
    <div class='modal fade' id='myModalImages' role='dialog' class='no-print'></div>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
    
    
    <div id="n_content">
        
        <div class="row">
            <?PHP
            echo "<b>Report Detail Absensi</b><br/>";
            echo "<b>Periode : $pperiode</b><br/>";

            echo "<b>Nama : $pnamakarywanpl - $pkryid</b><br/>";
            //echo "<b>Jabatan : $pnmjbtkarywanpl</b><br/>";

            echo "<b>Hari Kerja : $pjmlharikerjasdm</b><br/>";
            if ((INT)$pjmlcutimasal>0) {
                echo "<b>Cuti Massal : $pjmlcutimasal</b><br/>";
            }


            $printdate= date("d/m/Y H:i");
            echo "<i>view date : $printdate</i><br/>";

            echo "<hr/><br/>";

            ?>
            <div class='x_panel'>
                <div class='x_content'>


                    <div class='col-md-12 col-sm-12 col-xs-12'>

                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                Filter WFO / WFH
                            </label>
                            <div class='col-md-3 col-sm-3 col-xs-12'>
                                <select class='form-control input-sm' id='myInput' name='myInput' onchange="myFilterData()" data-live-search="true">
                                    <?PHP
                                        echo "<option value='' selected>--ALL--</option>";
                                        echo "<option value='WFO'>WFO</option>";
                                        echo "<option value='WFH'>WFH</option>";
                                    ?>
                                </select>
                            </div>
                        </div>


                    </div>

                </div>
            </div>
            <div class='clearfix'></div>
            <br/>
            <?PHP
            
            echo "<table id='tbltable' border='1' cellspacing='0' cellpadding='1'>";
                echo "<thead>";
                    echo "<tr>";
                        echo "<th align='center' rowspan='2'><small>Tanggal</small></th>";
                        echo "<th align='center' colspan='3'><small>Absen Masuk</small></th>";
                        echo "<th align='center' colspan='3'><small>Absen Pulang</small></th>";
                        echo "<th align='center' rowspan='2'><small>Durasi</small></th>";
                        echo "<th align='center' rowspan='2'><small>&nbsp;</small></th>";
                        echo "<th align='center' rowspan='2'><small>U.M</small></th>";
                        echo "<th align='center' rowspan='2' class='divnone'><small>&nbsp;</small></th>";
                    echo "</tr>";

                    echo "<tr>";
                        echo "<th align='center'><small>Foto</small></th>";
                        echo "<th align='center'><small>Jam</small></th>";
                        echo "<th align='center'><small>Keterangan</small></th>";
                        echo "<th align='center'><small>Foto</small></th>";
                        echo "<th align='center'><small>Jam</small></th>";
                        echo "<th align='center'><small>Keterangan</small></th>";
                    echo "</tr>";

                echo "</thead>";

                echo "<tbody>";

                    $pjmlwfo=0;
                    $pjmlwfo_ok=0;
                    $pjmlwfh=0;
                    $no=1;//- INTERVAL '60' MINUTE
                    $query = "select * from $tmp03 order by nama_karyawan, karyawanid, tanggal";
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
                        $ngambar_m=$row0['nama_gambar'];
                        $ngambar_p=$row0['nama_gambar_p'];
                        $nket_abs=$row0['ket_absen'];
                        $nstatusabs=$row0['l_status'];
                        $nterlambatsdm=$row0['terlambat_sdm'];

                        $njadwalwfo=$row0['j_wfo'];
                        $nexjamkerja=$row0['ex_jamkerja'];
                        $pex_jamkerja_wfo=$row0['jam_kerja_wfo_ex'];


                        $folderfoto="images/foto_absen/";


                        if (empty($nterlambatsdm)) $nterlambatsdm=0;
                        $nterlambatsdm_jm = "08:".str_pad($nterlambatsdm, 2, '0', STR_PAD_LEFT);

                        //$nstatusabs="WFO";$njampulang="13:30";

                        $pselisih_jam="";
                        $pselisih_ist="";
                        $pselisih_telat="";
                        $pliburannone="";
                        if ($nlibur=="Y" OR $ncutimasal=="Y") $pliburannone="Y";
                        //0 = hitung selisih masuk, pulang dan istirahat, 1 = hitung hanya masuk dan pulang, 2 = hitung hanya istirahat, 3 = telat
                        //$pselisih_jam=CariSelisihJamMenit02("0", $pliburannone, $ntgl, $njammasuk, $njampulang, $njamistirahat, $njammmst_ist);
                        //$pselisih_jam=CariSelisihJamMenit01("0", $pliburannone, $ntgl, $njammasuk, $njampulang);
                        $pselisih_jam=CariSelisihJamMenit("1", $pliburannone, $ntgl, $njammasuk, $njampulang, "");
                        $pselisih_ist=CariSelisihJamMenit("1", $pliburannone, $ntgl, $njamistirahat, $njammmst_ist, "");
                        $pselisih_telat=CariSelisihJamMenit("3", $pliburannone, $ntgl, $nterlambatsdm_jm, $njammasuk, $nterlambatsdm);

                        if ($hari_ini==$ntgl && $pselisih_jam=="invalid") $pselisih_jam="";

                        if ( (!empty($pselisih_jam) && $pselisih_jam<>"invalid" && empty($njampulang)) ) {
                            if ((INT)substr($pselisih_jam,0,2)>=8) {
                                //$pselisih_jam="08:00";
                            }
                        }


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

                        //$nstatusabs="WFO";
                        //$pselisih_jam="05:01";

                        $puangmakan="";
                        $pketjadwal_wfo="";
                        if ($nstatusabs=="WFO") {
                            if (!empty($pselisih_jam) && $pselisih_jam<>"invalid") {

                                if ($njadwalwfo=="Y") {
                                    if ($nexjamkerja=="Y") $pjamkerja=$pex_jamkerja_wfo;
                                    else $pjamkerja=$pjamkerja_wfo;
                                }else{
                                    $pjamkerja=$pjamkerja_wfh;
                                    $pketjadwal_wfo="bukan jadwal wfo";
                                }


                                if ((INT)substr($pselisih_jam,0,2)>=(INT)$pjamkerja) {
                                    $puangmakan="<a href=\"#/prediksi_uang_makan\"><i class=\"fa fa-money\"></i></a>";
                                    $pjmlwfo_ok++;
                                }

                            }
                            $pjmlwfo++;
                        }elseif ($nstatusabs=="WFH") {
                            $pjmlwfh++;
                        }


                        if (!empty($pketjadwal_wfo)) $pketjadwal_wfo="<b>".$nstatusabs."</b><br/>(".$pketjadwal_wfo.")";
                        else $pketjadwal_wfo="<b>".$nstatusabs."</b>";

                        echo "<tr $pclasslibur>";
                        echo "<td nowrap>$pharitanggal</td>";

                        if (!empty($ngambar_m)) {
                            $folderfotofileabs_m=$folderfoto."".$ngambar_m;
                            if (file_exists($folderfotofileabs_m)) {
                                echo "<td nowrap ><img src='$folderfotofileabs_m' class='zoomimg' width='50px' height='50px' data-toggle='modal' data-target='#myModalImages' onclick=\"ShowFormImages('$folderfotofileabs_m')\" /></td>";
                            }else{
                                echo "<td nowrap >&nbsp;</td>";
                            }
                        }else{
                            echo "<td nowrap >&nbsp;</td>";
                        }

                        echo "<td nowrap $pclasslibur_rd>$njammasuk</td>";
                        echo "<td  >$nketerangan</td>";

                        if (!empty($ngambar_p)) {
                            $folderfotofileabs_p=$folderfoto."".$ngambar_p;
                            if (file_exists($folderfotofileabs_p)) {
                                echo "<td nowrap ><img src='$folderfotofileabs_p' class='zoomimg' width='50px' height='50px' data-toggle='modal' data-target='#myModalImages' onclick=\"ShowFormImages('$folderfotofileabs_p')\" /></td>";
                            }else{
                                echo "<td nowrap >&nbsp;</td>";
                            }
                        }else{
                            echo "<td nowrap >&nbsp;</td>";
                        }

                        echo "<td nowrap >$njampulang</td>";
                        echo "<td  >$nketerangan_p</td>";
                        echo "<td nowrap align='center'><b>$pselisih_jam</b></td>";
                        echo "<td nowrap align='center'>&nbsp; $pketjadwal_wfo &nbsp;</td>";
                        echo "<td nowrap >&nbsp; <b>$puangmakan</b> &nbsp;</td>";
                        echo "<td nowrap align='center' class='divnone'>$nstatusabs</td>";
                        echo "</tr>";


                    }

                echo "</tbody>";

            echo "</table>";

            echo "<br/><hr/>";

            $ptotal_uangmakan=(DOUBLE)$pjmlwfo_ok * (DOUBLE)$prupiah_um;

            $prupiah_um=BuatFormatNumberRp($prupiah_um, $ppilformat);//1 OR 2 OR 3
            $ptotal_uangmakan=BuatFormatNumberRp($ptotal_uangmakan, $ppilformat);//1 OR 2 OR 3

            echo "Total WFO : $pjmlwfo<br/>";
            echo "Total WFH : $pjmlwfh<br/>";
            echo "Prediksi Uang Makan Bulan $pperiode : $pjmlwfo_ok * $prupiah_um = $ptotal_uangmakan<br/>";

            echo "<br/><br/><br/><br/><br/><br/>";


        ?>
        </div>
    </div>
    <!-- Bootstrap -->
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
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
            text-align: center;
        }
        tr td {
            font-size : 12px;
        }
        tr td {
            padding : 3px;
        }
        tr:hover {background-color:#f5f5f5;}
        thead tr:hover {background-color:#cccccc;}
        .divnone {
            display: none;
        }
    </style>
    
    
    <style>
        #n_content {
            color:#000;
            font-family: "Arial";
            margin: 20px;
            /*overflow-x:auto;*/
        }
        .zoomimg {
          transition: transform .2s; /* Animation */
          margin: 0 auto;
        }
        .zoomimg:hover {
            cursor: pointer;
        }
    </style>

    <script>

        function ShowFormImages(sKey) {
            $.ajax({
                type:"post",
                url:"module/hrd/hrd_rptabsenmasuk/form_images.php?module=showimagespoto",
                data:"ukey="+sKey,
                success:function(data){
                    $("#myModalImages").html(data);
                }
            });
        }

        function myFilterData() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("myInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("tbltable");
            tr = table.getElementsByTagName("tr");
            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[10];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }       
            }
        }
    </script>


</HTML>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp00");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp03");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp04");
    mysqli_close($cnmy);
?>