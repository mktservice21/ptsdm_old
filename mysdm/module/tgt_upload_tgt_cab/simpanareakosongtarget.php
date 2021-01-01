<?php
    session_start();
    
    $userid=$_SESSION['IDCARD'];
    if (empty($userid)) {
        echo "Anda harus login ulang..."; exit;
    }
    
    
    include ("../../config/koneksimysqli_ms.php");
    
    
    $ptglpil=$_POST['uperiode1'];
    $tgl_pertama=$ptglpil;
    $ptglpilihupload = date("Y-m-d", strtotime($ptglpil));
    $pperiode_ = date("Ym", strtotime($ptglpil));
    
    $pidcabpil=$_POST['uidcabang'];
    $pidareapil=$_POST['uareaid'];
    
    
    $berhasil="tidak ada data yang diproses simpan...";
    
    $query ="DELETE FROM tgt.targetarea WHERE DATE_FORMAT(bulan,'%Y%m')='$pperiode_' AND icabangid='$pidcabpil' AND areaid='$pidareapil'";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "Error DELETE DATA : $erropesan"; mysqli_close($cnms); exit; }
    
    
    $query ="INSERT INTO tgt.targetarea (bulan, divprodid, iprodid, hna, qty, value, icabangid, areaid, `user`)"
            . " SELECT DISTINCT '$ptglpilihupload' as bulan, divprodid, iprodid, hna, 0 as qty, 0 as value, "
            . " '$pidcabpil' as icabangid, '$pidareapil' as areaid, '$_SESSION[IDCARD]' as `user` from tgt.targetcab WHERE DATE_FORMAT(bulan,'%Y%m')='$pperiode_' AND icabangid='$pidcabpil'";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "Error DELETE DATA : $erropesan"; mysqli_close($cnms); exit; }
    
    mysqli_close($cnms);
    $berhasil="data berhasil diproses simpan...";
    echo $berhasil;
    
?>