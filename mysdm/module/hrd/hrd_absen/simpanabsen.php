<?php
date_default_timezone_set('Asia/Jakarta');
ini_set("memory_limit","512M");
ini_set('max_execution_time', 0);

session_start();

$pcardidabsen="";
if (isset($_SESSION['IDCARD'])) $pcardidabsen=$_SESSION['IDCARD'];

if (empty($pcardidabsen)) {
    echo "Anda Harus Login Ulang... (GAGAL)"; exit;
}

$pmodule="";
$pact="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];
if (isset($_GET['act'])) $pact=$_GET['act'];

$pberhasil="GAGAL";

if ($pmodule=="hrdabsenmasuk" AND ($pact=="absenmasuk" || $pact=="absenpulang")) {
    
    //mendefinisikan folder
    define('UPLOAD_DIR', '../../../images/foto_absen/');

    $pkey        = $_POST['ukey'];
    $plangitut   = $_POST['ulatitude'];
    $plongitut   = $_POST['ulongitude'];
    $pimg        = $_POST['image'];

    $pnamaabse="";
    if ($pkey=="1") $pnamaabse="Absen Masuk";
    elseif ($pkey=="2") $pnamaabse="Absen Pulang";
    elseif ($pkey=="3") $pnamaabse="Absen Istirahat";
    elseif ($pkey=="4") $pnamaabse="Masuk Dari Istirahat";
    
    
    if (empty($plangitut) OR empty($plongitut)) {
        $pberhasil= "GAGAL... Lokasi Kosong";
        echo "$pberhasil"; exit;
    }
    
    if (empty($pkey)) {
        echo "$pberhasil"; exit;
    }
    
    
    $ptangga=date("d F Y");
    $pjam=date("H i s");
    
    $ptglabsen=date("Y-m-d");
    $pjamabsen=date("H:i");
    
    
    include "../../../config/koneksimysqli.php";
    include "../../../config/fungsi_sql.php";
    
    
    $pabsensiwfh=false;
    $query = "select * from hrd.sdm_lokasi";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    $sdmlat=$row['sdm_latitude'];
    $sdmlong=$row['sdm_longitude'];
    $sdmradius=$row['sdm_radius'];
    
    if (empty($sdmlat)) $sdmlat=0;
    if (empty($sdmlong)) $sdmlong=0;
    if (empty($sdmradius)) $sdmradius=0;
    
    
    $a_lat="";
    $a_long="";
    $a_radius="";
    
    $query = "select * from hrd.karyawan_absen WHERE karyawanid='$pcardidabsen'";
    $tampilwfh= mysqli_query($cnmy, $query);
    $ketemuwfh= mysqli_num_rows($tampilwfh);
    if ((INT)$ketemuwfh>0) {
        $row= mysqli_fetch_array($tampilwfh);
        $a_lat=$row['a_latitude'];
        $a_long=$row['a_longitude'];
        $a_radius=$row['a_radius'];
        $pabsensiwfh=true;
    }
    if (empty($a_lat)) $a_lat=0;
    if (empty($a_long)) $a_long=0;
    if (empty($a_radius)) $a_radius=0;
    
    $plangitut_rds="";
    $plongitut_rds="";
    $pradius_rds="";
    
    if ($pabsensiwfh==true) {
        $plangitut_rds=$a_lat;
        $plongitut_rds=$a_long;
        $pradius_rds=$a_radius;
    }else{
        $plangitut_rds=$sdmlat;
        $plongitut_rds=$sdmlong;
        $pradius_rds=$sdmradius;
    }
    
    if ( ((INT)$a_lat==0 || (INT)$a_long==0) AND ((INT)$sdmlat==0 || (INT)$sdmlong==0) ) {
        mysqli_close($cnmy);
        echo "GAGAL...\n"."Tidak ada pengaturan absen...";
        exit;
    }
    
    
    $pjarak_absen=getDistanceBetween($plangitut, $plongitut, $plangitut_rds, $plongitut_rds, $unit = 'Mi');
    $pjarak_absen_wfo=getDistanceBetween($plangitut, $plongitut, $sdmlat, $sdmlong, $unit = 'Mi');
    $pjarak_absen_wfh=getDistanceBetween($plangitut, $plongitut, $a_lat, $a_long, $unit = 'Mi');
    
    
    
    
    
    if ( ((DOUBLE)$pjarak_absen_wfo>(DOUBLE)$sdmradius) AND ((DOUBLE)$pjarak_absen_wfh>(DOUBLE)$a_radius) ) {
        mysqli_close($cnmy);
        echo "GAGAL...\n"."Lokasi ABSEN Tidak Sesuai...\n"."Jarak dari Kantor : ".$pjarak_absen_wfo." KM\n"."Jarak dari Rumah : ".$pjarak_absen_wfh." KM";
        exit;
    }
    
    $pjarakdarilokasi=$pjarak_absen_wfh;
    $plokasiabs="WFH";
    if ( (DOUBLE)$pjarak_absen_wfo<=(DOUBLE)$sdmradius ) {
        $plokasiabs="WFO";
        $pjarakdarilokasi=$pjarak_absen_wfo;
    }
    
    //mysqli_close($cnmy); echo "jarak : $pjarak_absen, radius sdm : $pjarak_absen_wfo, radius rumah : $pjarak_absen_wfh,  status : $plokasiabs"; exit;

    
    
    
    if ($pkey=="2" OR $pkey=="3" OR $pkey=="4") {
        $query = "select * from hrd.t_absen WHERE tanggal='$ptglabsen' AND kode_absen='1' AND karyawanid='$pcardidabsen'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ((INT)$ketemu==0) {
            mysqli_close($cnmy);
            echo "Tidak ada proses absen...\n"."Karena anda belum absen masuk...";
            exit;
        }
    }
    
    
    if ($pkey=="3" OR $pkey=="4") {
        $query = "select * from hrd.t_absen WHERE tanggal='$ptglabsen' AND kode_absen='2' AND karyawanid='$pcardidabsen'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ((INT)$ketemu>0) {
            mysqli_close($cnmy);
            echo "Tidak ada proses absen...\n"."Karena sudah absen pulang...";
            exit;
        }
    }
    
    if ($pkey=="2") {
        $query = "select * from hrd.t_absen WHERE tanggal='$ptglabsen' AND kode_absen='3' AND karyawanid='$pcardidabsen'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ((INT)$ketemu>0) {
            
            $query = "select * from hrd.t_absen WHERE tanggal='$ptglabsen' AND kode_absen='4' AND karyawanid='$pcardidabsen'";
            $tampil= mysqli_query($cnmy, $query);
            $ketemu= mysqli_num_rows($tampil);
            
            if ((INT)$ketemu==0) {
                mysqli_close($cnmy);
                echo "Tidak ada proses absen...\n"."Karena anda belum absen masuk dari istirahat...";
                exit;
            }
            
        }
    }
    
    if ($pkey=="4") {
        $query = "select * from hrd.t_absen WHERE tanggal='$ptglabsen' AND kode_absen='3' AND karyawanid='$pcardidabsen'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ((INT)$ketemu==0) {
            mysqli_close($cnmy);
            echo "Tidak ada proses absen...\n"."Karena anda belum absen istirahat...";
            exit;
        }
    }
    
    $query = "select * from hrd.t_absen WHERE tanggal='$ptglabsen' AND kode_absen='$pkey' AND karyawanid='$pcardidabsen'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        mysqli_close($cnmy);
        echo "Tidak ada proses absen...\n"."Karena anda sudah $pnamaabse..., tidak bisa diulang";
        exit;
    }
    
    
    $query = "INSERT INTO hrd.t_absen(kode_absen, karyawanid, tanggal, jam, l_latitude, l_longitude, l_status, l_radius, l_jarak)VALUES"
            . "('$pkey', '$pcardidabsen', '$ptglabsen', '$pjamabsen', '$plangitut', '$plongitut', '$plokasiabs', '$pradius_rds', '$pjarakdarilokasi')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    
    $pkodeid = mysqli_insert_id($cnmy);
    
    if (empty($pkodeid)) {
        mysqli_query($cnmy, "DELETE FROM hrd.t_absen WHERE karyawanid='$pcardidabsen' AND tanggal='$ptglabsen' AND kode_absen='$pkey' LIMIT 1");
        mysqli_close($cnmy);
        echo "GAGAL";
        exit; 
    }
    
    $pimgges        = str_replace('data:image/jpeg;base64,', '', $pimg);
    $pimgges        = str_replace(' ', '+', $pimgges);

    //resource gambar diubah dari encode
    $pdata       = base64_decode($pimgges);

    //menamai file, file dinamai secara random dengan unik
    $pfile       = uniqid() . '.png';

    //memindahkan file ke folder upload
    file_put_contents(UPLOAD_DIR.$pfile, $pdata);
    
    
    $query = "INSERT INTO dbimages2.img_absen(idabsen, kode_absen, tanggal, nama, gambar)VALUES"
            . "('$pkodeid', '$pkey', '$ptglabsen', '$pfile', '$pimg')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy);
    if (!empty($erropesan)) {
        mysqli_query($cnmy, "DELETE FROM hrd.t_absen WHERE karyawanid='$pcardidabsen' AND tanggal='$ptglabsen' AND kode_absen='$pkey' LIMIT 1");
        mysqli_close($cnmy);
        echo $erropesan; 
        exit; 
    }
    
    
    
    $pberhasil="Anda berhasil $pnamaabse, Tgl : $ptangga, Jam : $pjam";
    
    

    mysqli_close($cnmy);

    //$pberhasil="$pmodule | $pact = $pkey : $plangitut, $plongitut";
}
echo $pberhasil; exit;
?>

