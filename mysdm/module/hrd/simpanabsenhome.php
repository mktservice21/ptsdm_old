<?PHP
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
if (isset($_GET['act'])) $pact=$_GET['act'];

$pberhasil="GAGAL....";
if ($pact=="simpandataabsen") {
    
    $pcardidabsen="";
    if (isset($_SESSION['IDCARD'])) $pcardidabsen=$_SESSION['IDCARD'];
    if (empty($pcardidabsen)) {
        echo "Anda Harus Login Ulang... (GAGAL)"; exit;
    }
    
    $pkey=$_POST['ukey'];
    $plangitut=$_POST['ulat'];
    $plongitut=$_POST['ulong'];
    
    $pnamaabse="";
    if ($pkey=="1") $pnamaabse="Absen Masuk";
    elseif ($pkey=="2") $pnamaabse="Absen Keluar";
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
    
    //$pberhasil="$pcardidabsen : $plangitut & $plongitut";
    
    include "../../config/koneksimysqli.php";
    
    if ($pkey=="2" OR $pkey=="3" OR $pkey=="4") {
        $query = "select * from hrd.t_absen WHERE tanggal='$ptglabsen' AND kode_absen='1' AND karyawanid='$pcardidabsen'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ((INT)$ketemu==0) {
            mysqli_close($cnmy);
            echo "Tidak ada proses absen... Karena anda belum absen masuk...";
            exit;
        }
    }
    
    
    if ($pkey=="3" OR $pkey=="4") {
        $query = "select * from hrd.t_absen WHERE tanggal='$ptglabsen' AND kode_absen='2' AND karyawanid='$pcardidabsen'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ((INT)$ketemu>0) {
            mysqli_close($cnmy);
            echo "Tidak ada proses absen... Karena sudah absen keluar...";
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
                echo "Tidak ada proses absen... Karena anda belum absen masuk dari istirahat...";
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
            echo "Tidak ada proses absen... Karena anda belum absen istirahat...";
            exit;
        }
    }
    
    $query = "select * from hrd.t_absen WHERE tanggal='$ptglabsen' AND kode_absen='$pkey' AND karyawanid='$pcardidabsen'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        mysqli_close($cnmy);
        echo "Tidak ada proses absen... Karena anda sudah $pnamaabse..., tidak bisa diulang";
        exit;
    }
    
    $query = "INSERT INTO hrd.t_absen(kode_absen, karyawanid, tanggal, jam, l_latitude, l_longitude)VALUES"
            . "('$pkey', '$pcardidabsen', '$ptglabsen', '$pjamabsen', '$plangitut', '$plongitut')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    
    $pberhasil="Anda berhasil $pnamaabse, Tgl : $ptangga, Jam : $pjam";
    
    mysqli_close($cnmy);
    
}
echo $pberhasil; exit;
?>