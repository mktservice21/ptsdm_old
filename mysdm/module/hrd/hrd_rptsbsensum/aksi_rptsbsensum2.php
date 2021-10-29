<?php
include "config/fungsi_ubahget_id.php";
$bulan_pilih=encodeString($bulan_pilih);


$pjamkerja_wfo=0;
$pjamkerja_wfh=0;

$query ="select jam_kerja_wfo_y, jam_kerja_wfo_n from hrd.t_absen_jam_kerja WHERE IFNULL(id_status,'')='HO1'";
$tampilw=mysqli_query($cnmy, $query);
$roww=mysqli_fetch_array($tampilw);
$pjamkerja_wfo=$roww['jam_kerja_wfo_y'];
$pjamkerja_wfh=$roww['jam_kerja_wfo_n'];


if (empty($pjamkerja_wfo)) $pjamkerja_wfo=0;
if (empty($pjamkerja_wfh)) $pjamkerja_wfh=0;


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
        . " FROM hrd.t_absen WHERE LEFT(tanggal,7)= '$pbulan'";
if (!empty($pkryid)) $query .=" AND karyawanid='$pkryid' ";
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
        . " (select distinct karyawanid, id_status, tanggal FROM $tmp01) as b on a.tanggal=b.tanggal AND a.karyawanid=b.karyawanid WHERE "
        . " 1=1 "
        . " AND a.id_status=b.id_status";//IFNULL(a.karyawanid,'') IN $pfilterkaryawanex
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

$query="drop TEMPORARY table if EXISTS $tmp01";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) {  goto hapusdata; }
$query="drop TEMPORARY table if EXISTS $tmp02";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) {  goto hapusdata; }

$query = "UPDATE $tmp03 as a JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId SET a.nama_karyawan=b.nama";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "select * from $tmp03";
$query = "create TEMPORARY table $tmp01 ($query)";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "ALTER TABLE $tmp01 ADD COLUMN jmlwfh DECIMAL(20,0), ADD COLUMN jmlwfo DECIMAL(20,0), ADD COLUMN jmlwfo_ok DECIMAL(20,0), ADD COLUMN jmlwfo_inv DECIMAL(20,0)";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$pjmlwfo=0;
$pjmlwfo_ok=0;
$pjmlwfh=0;
$no=1;//- INTERVAL '60' MINUTE
$query = "select * from $tmp03 order by nama_karyawan, karyawanid, tanggal";
$tampil0=mysqli_query($cnmy, $query);
while ($row0=mysqli_fetch_array($tampil0)) {
    $nkryid=$row0['karyawanid'];
    $ntgl=$row0['tanggal'];
    $njammasuk=$row0['jam_masuk'];
    $njampulang=$row0['jam_pulang'];
    $njamistirahat=$row0['jam_istirahat'];
    $njammmst_ist=$row0['jam_masuk_ist'];
    
    $njadwalwfo=$row0['j_wfo'];
    $nexjamkerja=$row0['ex_jamkerja'];
    $pex_jamkerja_wfo=$row0['jam_kerja_wfo_ex'];
                        
    $nlibur=$row0['libur'];
    $ncutimasal=$row0['libur_cmasal'];
    $nstatusabs=$row0['l_status'];
    $nterlambatsdm=$row0['terlambat_sdm'];
    $nket_abs=$row0['ket_absen'];
    
    if (empty($nterlambatsdm)) $nterlambatsdm=0;
    $nterlambatsdm_jm = "08:".str_pad($nterlambatsdm, 2, '0', STR_PAD_LEFT);
    
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
    
    $nketerangan_abs="";
    $pclasslibur="";
    $pclasslibur_rd="";
    if ($nlibur=="Y" OR $ncutimasal=="Y") {
        if ($pselisih_jam=="invalid") $pselisih_jam="";
    }else{
        if ($nket_abs=="TERLAMBAT") {
            $nketerangan_abs="terlambat";
            $pclasslibur_rd=" style='color:red;' ";
        }
    }
    
    $pjamkerja="";
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
                $pjmlwfo_ok++;
            }

        }
        $pjmlwfo++;
    }elseif ($nstatusabs=="WFH") {
        $pjmlwfh++;
    }
    
    if (empty($pjmlwfh)) $pjmlwfh=0;
    if (empty($pjmlwfo)) $pjmlwfo=0;
    if (empty($pjmlwfo_ok)) $pjmlwfo_ok=0;
    
    $query = "UPDATE $tmp01 SET jmlwfh='$pjmlwfh', jmlwfo='$pjmlwfo', jmlwfo_ok='$pjmlwfo_ok' WHERE karyawanid='$nkryid' AND tanggal='$ntgl'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $pjmlwfo=0;
    $pjmlwfo_ok=0;
    $pjmlwfh=0;          
    
}

$query = "UPDATE $tmp01 SET jmlwfo_inv=IFNULL(jmlwfo,0)-IFNULL(jmlwfo_ok,0)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 SET jmlwfo_inv='0' WHERE IFNULL(jmlwfo_inv,0)<0";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "select karyawanid, nama_karyawan, sum(jmlwfh) as jmlwfh, sum(jmlwfo) as jmlwfo, sum(jmlwfo_ok) as jmlwfo_ok, "
        . " sum(jmlwfo_inv) as jmlwfo_inv from $tmp01 "
        . " GROUP BY 1,2";
$query = "create TEMPORARY table $tmp02 ($query)";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


?>

    <HTML>
    <HEAD>
      <TITLE>Report Summary Absensi Valid/Invalid</TITLE>
        <meta http-equiv="Expires" content="Mon, 01 Jan 2030 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        <link rel="shortcut icon" href="images/icon.ico" />
        <style> .str{ mso-number-format:\@; } </style>
        <style>
        @media print
        {
            .ilink {
                text-decoration: none;
                color: #000;
            }
        }
        </style>
    </HEAD>
    <script>
    </script>

    <BODY onload="initVar()">
        <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

        <?PHP
        echo "<b>Report Summary Absensi Valid / Invalid</b><br/>";
        echo "<b>Periode : $pperiode</b><br/>";

        echo "<b>Hari Kerja : $pjmlharikerjasdm</b><br/>";
        if ((INT)$pjmlcutimasal>0) {
            echo "<b>Cuti Massal : $pjmlcutimasal</b><br/>";
        }

        if (!empty($pkryid)) {
            //echo "<b>Nama : $pnamakarywanpl - $pkryid</b><br/>";
            //echo "<b>Jabatan : $pnmjbtkarywanpl</b><br/>";
        }

        $printdate= date("d/m/Y");
        echo "<i>view date : $printdate</i><br/>";
        
        echo "<hr/><br/>";
        
        
        echo "<table id='tbltable' border='1' cellspacing='0' cellpadding='1'>";
            echo "<thead>";
                echo "<tr>";
                    echo "<th align='center'><small>No</small></th>";
                    echo "<th align='center'><small>Nama Karyawan</small></th>";
                    echo "<th align='center'><small>Absen WFH</small></th>";
                    echo "<th align='center'><small>Absen WFO Valid</small></th>";
                    echo "<th align='center'><small style='color:red'>Absen WFO Invalid</small></th>";
                echo "</tr>";
            echo "</thead>";

            echo "<tbody>";
                $no=1;
                $query = "select * from $tmp02 order by nama_karyawan, karyawanid";
                $tampil0=mysqli_query($cnmy, $query);
                while ($row0=mysqli_fetch_array($tampil0)) {
                    $nkryid=$row0['karyawanid'];
                    $nkrynm=$row0['nama_karyawan'];
                    $njmlwfh=$row0['jmlwfh'];
                    $njmlwfo=$row0['jmlwfo'];
                    $njmlwfo_ok=$row0['jmlwfo_ok'];
                    $njmlwfo_inv=$row0['jmlwfo_inv'];
                    
                    
                    $pidkry=(INT)$nkryid;
                    
                    $pkaryidcode=encodeString($nkryid);
                    $pviewdataabsen = "<a class='btn btn-warning btn-xs ilink' href='eksekusi3.php?module=showdataabsensi&i=$pkaryidcode&b=$bulan_pilih' target='_blank'>$nkrynm</a>";
                        
                    
                    $pnamakaryawan_pl=$nkrynm;
                    if ($ppilihrpt=="excel") {
                    }else{
                        $pnamakaryawan_pl=$pviewdataabsen;
                    }
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pnamakaryawan_pl ($pidkry)</td>";
                    echo "<td nowrap align='right'>$njmlwfh</td>";
                    echo "<td nowrap align='right'>$njmlwfo_ok</td>";
                    echo "<td nowrap align='right'>$njmlwfo_inv</td>";
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
        mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp00");
        mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp01");
        mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp02");
        mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp03");
        mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp04");
        mysqli_close($cnmy);
?>