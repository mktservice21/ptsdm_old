<?PHP
session_start();
$aksi="";
$pidcard="";
if (isset($_SESSION['IDCARD'])) $pidcard=$_SESSION['IDCARD'];

$papvby=$_POST['uapvby'];
$pkryidapv=$_POST['ukryapv'];
$pidinput=$_POST['unourut'];
$pkryid=$_POST['ukryid'];
$ptgl=$_POST['utgl'];
$pudoktid=$_POST['udoktid'];

$pmodule=$_GET['module'];

if (empty($pkryidapv)) $pkryidapv=$pidcard;

$tgl_pertama = date('d F Y', strtotime($ptgl));
$itgl = date('Y-m-d', strtotime($ptgl));

include "../../../config/koneksimysqli.php";

$berhasil="Tidak ada data yang diapprove";
if ($pmodule=="simpanapv") {
    
    if (!empty($pkryidapv) AND !empty($pidinput)) {
        
        if ($papvby=="SPV") {
            $query = "UPDATE hrd.dkd_new_real1 SET tgl_atasan1=NOW() WHERE nourut='$pidinput' AND atasan1='$pkryidapv' AND "
                    . " tanggal='$itgl' and karyawanid='$pkryid' and dokterid='$pudoktid' AND "
                    . " IFNULL(tgl_atasan1,'') IN ('', '0000-00-00', '0000-00-00 00:00:00') AND "
                    . " IFNULL(tgl_atasan2,'') IN ('', '0000-00-00', '0000-00-00 00:00:00') LIMIT 1";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            //hilangkan dulu
            $query = "UPDATE hrd.dkd_new_real1 SET tgl_atasan2=NOW() WHERE nourut='$pidinput' AND atasan1='$pkryidapv' AND "
                    . " tanggal='$itgl' and karyawanid='$pkryid' and dokterid='$pudoktid' AND "
                    . " IFNULL(tgl_atasan1,'') NOT IN ('', '0000-00-00', '0000-00-00 00:00:00') AND "
                    . " IFNULL(tgl_atasan2,'') IN ('', '0000-00-00', '0000-00-00 00:00:00') AND IFNULL(atasan2,'')='' AND "
                    . " IFNULL(tgl_atasan3,'') IN ('', '0000-00-00', '0000-00-00 00:00:00') LIMIT 1";
            //mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

            $berhasil="berhasil approve";
            
            
        }elseif ($papvby=="DM") {
            $query = "UPDATE hrd.dkd_new_real1 SET tgl_atasan2=NOW() WHERE nourut='$pidinput' AND atasan2='$pkryidapv' AND "
                    . " tanggal='$itgl' and karyawanid='$pkryid' and dokterid='$pudoktid' AND "
                    . " IFNULL(tgl_atasan1,'') NOT IN ('', '0000-00-00', '0000-00-00 00:00:00') AND "
                    . " IFNULL(tgl_atasan2,'') IN ('', '0000-00-00', '0000-00-00 00:00:00') AND "
                    . " IFNULL(tgl_atasan3,'') IN ('', '0000-00-00', '0000-00-00 00:00:00') LIMIT 1";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            
            $query = "UPDATE hrd.dkd_new_real1 SET tgl_atasan2=NOW() WHERE nourut='$pidinput' AND atasan2='$pkryidapv' AND "
                    . " tanggal='$itgl' and karyawanid='$pkryid' and dokterid='$pudoktid' AND "
                    . " ( IFNULL(atasan1,'')='' AND IFNULL(tgl_atasan1,'') IN ('', '0000-00-00', '0000-00-00 00:00:00') ) AND "
                    . " IFNULL(tgl_atasan2,'') IN ('', '0000-00-00', '0000-00-00 00:00:00') AND "
                    . " IFNULL(tgl_atasan3,'') IN ('', '0000-00-00', '0000-00-00 00:00:00') LIMIT 1";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

            $berhasil="berhasil approve";
            
        }elseif ($papvby=="SM") {
            $query = "UPDATE hrd.dkd_new_real1 SET tgl_atasan3=NOW() WHERE nourut='$pidinput' AND atasan3='$pkryidapv' AND "
                    . " tanggal='$itgl' and karyawanid='$pkryid' and dokterid='$pudoktid' AND "
                    . " IFNULL(tgl_atasan2,'') NOT IN ('', '0000-00-00', '0000-00-00 00:00:00') AND "
                    . " IFNULL(tgl_atasan3,'') IN ('', '0000-00-00', '0000-00-00 00:00:00') AND "
                    . " IFNULL(tgl_atasan4,'') IN ('', '0000-00-00', '0000-00-00 00:00:00') LIMIT 1";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

            $berhasil="berhasil approve";
            
        }elseif ($papvby=="GSM") {
            $query = "UPDATE hrd.dkd_new_real1 SET tgl_atasan4=NOW() WHERE nourut='$pidinput' AND atasan4='$pkryidapv' AND "
                    . " tanggal='$itgl' and karyawanid='$pkryid' and dokterid='$pudoktid' AND "
                    . " IFNULL(tgl_atasan3,'') NOT IN ('', '0000-00-00', '0000-00-00 00:00:00') AND "
                    . " IFNULL(tgl_atasan4,'') IN ('', '0000-00-00', '0000-00-00 00:00:00') LIMIT 1";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

            $berhasil="berhasil approve";
            
        }
    }
    
    mysqli_close($cnmy);
    echo $berhasil; exit;
}elseif ($pmodule=="caridataapv") {
    
    $sql = "select a.karyawanid, c.nama as namakaryawan, a.tanggal, a.tglinput, 
        a.dokterid, d.namalengkap, d.gelar, d.spesialis, a.jenis, a.notes, a.saran, a.user_tandatangan, a.user_foto,
        atasan1, tgl_atasan1, atasan2, tgl_atasan2, atasan3, tgl_atasan3, atasan4, tgl_atasan4,
        d.nama as nama_spv, e.nama as nama_dm, f.nama as nama_sm, g.nama as nama_gsm 
        FROM hrd.dkd_new_real1 as a JOIN dr.masterdokter as d on a.dokterid=d.id 
        LEFT JOIN hrd.karyawan as c on a.karyawanid=c.karyawanId 
        LEFT JOIN hrd.karyawan as d on a.atasan1=d.karyawanid 
        LEFT JOIN hrd.karyawan as e on a.atasan2=e.karyawanid 
        LEFT JOIN hrd.karyawan as f on a.atasan3=f.karyawanid 
        LEFT JOIN hrd.karyawan as g on a.atasan4=g.karyawanid 
        WHERE a.karyawanid='$pkryid' AND a.tanggal='$itgl' AND a.dokterid='$pudoktid' ";
    $tampil=mysqli_query($cnmy, $sql);
    $row= mysqli_fetch_array($tampil);
    
    $pnmatasan1= $row['nama_spv'];
    $patasan1= $row['atasan1'];
    $ptglatasan1= $row['tgl_atasan1'];
    $pnmatasan2= $row['nama_dm'];
    $patasan2= $row['atasan2'];
    $ptglatasan2= $row['tgl_atasan2'];
    $pnmatasan3= $row['nama_sm'];
    $patasan3= $row['atasan3'];
    $ptglatasan3= $row['tgl_atasan3'];
    $pnmatasan4= $row['nama_gsm'];
    $patasan4= $row['atasan4'];
    $ptglatasan4= $row['tgl_atasan4'];

    if ($ptglatasan1=="0000-00-00" OR $ptglatasan1=="0000-00-00 00:00:00") $ptglatasan1="";
    if ($ptglatasan2=="0000-00-00" OR $ptglatasan2=="0000-00-00 00:00:00") $ptglatasan2="";
    if ($ptglatasan3=="0000-00-00" OR $ptglatasan3=="0000-00-00 00:00:00") $ptglatasan3="";
    if ($ptglatasan4=="0000-00-00" OR $ptglatasan4=="0000-00-00 00:00:00") $ptglatasan4="";
    
    
    if (!empty($ptglatasan1)) $ptglatasan1 = date('d F Y H:i:s', strtotime($ptglatasan1));
    if (!empty($ptglatasan2)) $ptglatasan2 = date('d F Y H:i:s', strtotime($ptglatasan2));
    if (!empty($ptglatasan3)) $ptglatasan3 = date('d F Y H:i:s', strtotime($ptglatasan3));
    if (!empty($ptglatasan4)) $ptglatasan4 = date('d F Y H:i:s', strtotime($ptglatasan4));
    
    if (!empty($ptglatasan1) AND !empty($patasan1)) {
        echo "<b>Sudah Approve SPV/AM : </b>$pnmatasan1<br/>Tgl : $ptglatasan1<br/><br/>";
    }
    if (!empty($ptglatasan2) AND !empty($patasan2)) {
        echo "<b>Sudah Approve DM : </b>$pnmatasan2<br/>Tgl : $ptglatasan2<br/><br/>";
    }
    if (!empty($ptglatasan3) AND !empty($patasan3)) {
        echo "<b>Sudah Approve SM : </b>$pnmatasan3<br/>Tgl : $ptglatasan3<br/><br/>";
    }
    if (!empty($ptglatasan4) AND !empty($patasan4)) {
        echo "<b>Sudah Approve GSM : </b>$pnmatasan4<br/>Tgl : $ptglatasan4<br/><br/>";
    }
    
    mysqli_close($cnmy);
    exit;
    
}
mysqli_close($cnmy);

?>