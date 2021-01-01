<?php

session_start();
$puserid="";
$piket="";
if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
if (isset($_GET['iprint'])) $piket=$_GET['iprint'];

if (empty($puserid)) {
    echo "ANDA HARUS LOGIN ULANG....";
    exit;
}

$pibrinput=$_POST['e_id'];
$pketerangan=$_POST['txt_ket'];
if (!empty($pketerangan)) $pketerangan = str_replace("'", " ", $pketerangan);

if (empty($pibrinput)) {
    echo "ID yang akan diedit kosong....";
    exit;
}

$pidmodule=$_GET['module'];
$pidmenu="161";

include "config/koneksimysqli.php";

//echo "$piket : $pibrinput, $pketerangan";

$query_data="";
if ($piket=="nrutin") {
    $query_data="UPDATE dbmaster.t_brrutin0 SET keterangan='$pketerangan' WHERE idrutin='$pibrinput' AND kode='1' LIMIT 1";
}elseif ($piket=="nlk") {
    $query_data="UPDATE dbmaster.t_brrutin0 SET keterangan='$pketerangan' WHERE idrutin='$pibrinput' AND kode='2' LIMIT 1";
}elseif ($piket=="nca") {
    $query_data="UPDATE dbmaster.t_ca0 SET keterangan='$pketerangan' WHERE idca='$pibrinput' LIMIT 1";
}

if (!empty($query_data)) {
    mysqli_query($cnmy, $query_data);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
}

mysqli_close($cnmy);
echo "<HTML>";
echo "<HEAD><TITLE>SIMPAN EDIT DATA</TITLE><link rel='shortcut icon' href='images/icon.ico' /></HEAD>";
echo "berhasil, silakan close...!!!";
echo "</HTML>";
echo "<script>alert('berhasil disimpan...');</script>";
echo "<script>window.close();</script>";
//header('location:../../eksekusi3.php?module='.$pidmodule.'&idmenu='.$pidmenu.'&act=complt&brid='.$pibrinput.'&iprint='.$piket);
?>
