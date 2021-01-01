<?php
session_start();
$puserid=$_SESSION['IDCARD'];
if (empty($puserid)) {
    echo "ANDA HARUS LOGIN ULANG...!!!";
    exit;
}


$pprintjenis="";
if (isset($_GET['iprint'])) {
    $pprintjenis=$_GET['iprint'];
}

if ($pprintjenis=="lihatgambar") {
    include "lihatgambar.php";
}
?>

