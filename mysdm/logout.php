<?php
    session_start();
    $lccardid=$_SESSION['IDCARD'];
    include 'config/koneksimysqli.php';
    mysqli_query($cnmy, "UPDATE dbmaster.sdm_users_log SET AKTIF='N' WHERE karyawanId='$lccardid' AND SESSION_ID='$_SESSION[IDSESI]'");
    mysqli_query($cnmy, "UPDATE dbmaster.sdm_users SET ONLINE='N' WHERE (karyawanId='$lccardid')");
    mysqli_query($cnmy, "UPDATE dbmaster.sdm_users_log SET AKTIF='N' WHERE karyawanId='$lccardid' AND SESSION_ID='$_SESSION[IDSESI_VPS]'");
    //echo "<center>Anda telah sukses keluar sistem <b>[LOGOUT]<b>";
    //echo"<h3><a href='index.php'>Kembali Login</a></h3>";
    $ip      = $_SERVER['REMOTE_ADDR']; // Mendapatkan IP komputer user
    $tanggal = date("Ymd"); // Mendapatkan tanggal sekarang
    //mysql_query("UPDATE userniconlain SET online='N' WHERE ip='$ip' AND tanggal='$tanggal' and cardid='$_SESSION[idcard]'");
    session_destroy();
	
	mysqli_close($cnmy);
	
	
    header('location:../index.php?module=home');

?>