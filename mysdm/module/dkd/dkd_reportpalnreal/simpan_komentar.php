<?php

session_start();
//data:"uidkry="+eidkry+"&utgl="+etgl+"&udoktid="+edoktid+"&uiduserinput="+eiduserinput+"&ukomen="+ekomen,

$pkryid=$_POST['uidkry'];
$ptgl=$_POST['utgl'];
$pdoktid=$_POST['udoktid'];
$puserinput=$_POST['uiduserinput'];
$pkomentar=$_POST['ukomen'];


if (empty($puserinput)) {
    echo "Anda harus login ulang...";
    exit;
}

$ptgl = date('Y-m-d', strtotime($ptgl));
if (!empty($pkomentar)) $pkomentar = str_replace("'", " ", $pkomentar);

$pberhasil="Tidak ada data yang disimpan";

include "../../../config/koneksimysqli.php";

if (!empty($pkryid) AND !empty($ptgl) AND !empty($pdoktid)) {
    $query = "UPDATE hrd.dkd_new_real1 SET komentar='$pkomentar', komen_user='$puserinput', komen_date=NOW() WHERE "
            . " karyawanid='$pkryid' AND tanggal='$ptgl' and dokterid='$pdoktid' LIMIT 1";
    mysqli_query($cnmy, $query); 
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    $pberhasil="berhasil input...";
}

mysqli_close($cnmy);
echo $pberhasil;
?>
