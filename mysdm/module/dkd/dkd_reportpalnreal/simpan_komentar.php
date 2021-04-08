<?php

session_start();
//data:"uidkry="+eidkry+"&utgl="+etgl+"&udoktid="+edoktid+"&uiduserinput="+eiduserinput+"&ukomen="+ekomen,

$pidstatus=$_POST['usts'];
$pidinput=$_POST['uidinput'];
$pkryid=$_POST['uidkry'];
$ptgl=$_POST['utgl'];
$pdoktid=$_POST['udoktid'];
$puserinput=$_POST['uiduserinput'];
$pjbtinput=$_POST['uidjbtinput'];
$pkomentar=$_POST['ukomen'];

$pinpt_user="";
$pinpt_jbt="";
if (isset($_SESSION['IDCARD'])) $pinpt_user=$_SESSION['IDCARD'];
if (isset($_SESSION['JABATANID'])) $pinpt_jbt=$_SESSION['JABATANID'];

if (empty($puserinput)) $puserinput=$pinpt_user;
if (empty($pjbtinput)) $pjbtinput=$pinpt_jbt;
    
if (empty($puserinput)) {
    echo "Anda harus login ulang...";
    exit;
}

$ptgl = date('Y-m-d', strtotime($ptgl));
if (!empty($pkomentar)) $pkomentar = str_replace("'", " ", $pkomentar);

$pberhasil="Tidak ada data yang disimpan";

include "../../../config/koneksimysqli.php";

if (!empty($pidinput) AND !empty($pkryid) AND !empty($ptgl) AND !empty($pdoktid)) {
    $query = "UPDATE hrd.dkd_new_real1 SET komentar='$pkomentar', komen_user='$puserinput', komen_date=NOW() WHERE "
            . " karyawanid='$pkryid' AND tanggal='$ptgl' and dokterid='$pdoktid' LIMIT 1";
    
    $query = "INSERT INTO hrd.dkd_new_real1_komen (sts, nourut, jabatanid, komen_user, komen_date, komentar)VALUES"
            . " ('$pidstatus', '$pidinput', '$pjbtinput', '$puserinput', NOW(), '$pkomentar')";
    mysqli_query($cnmy, $query); 
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    $pberhasil="berhasil input...";
}

mysqli_close($cnmy);
echo $pberhasil;
?>
