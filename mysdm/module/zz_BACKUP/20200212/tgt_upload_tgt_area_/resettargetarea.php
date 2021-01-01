<?php

    session_start();
    
    $userid=$_SESSION['IDCARD'];
    if (empty($userid)) {
        echo "Anda harus login ulang..."; exit;
    }
    
    //ini_set('memory_limit', '-1');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    include ("../../config/koneksimysqli_ms.php");
    
    $ptglpil=$_POST['uperiode1'];
    $pidcabpil=$_POST['uidcabang'];
    $pidareapil=$_POST['uareaid'];
    
    $puserid=$_SESSION['IDCARD'];
    
    $tgl_pertama=$ptglpil;
    $ptglpilihupload = date("Y-m-d", strtotime($ptglpil));
    $pperiode_ = date("Ym", strtotime($ptglpil));
    
    
    $berhasil="Tidak ada data yang diproses...";
    
    
    $filarea="";
    if (!empty($pidareapil)) $filarea=" AND areaid='$pidareapil' ";
    
    if (empty($pidcabpil)) { echo $berhasil; exit;}
    
    $query ="UPDATE tgt.targetarea SET qty=0, value=0, `user`='$puserid', sys_now=NOW() WHERE DATE_FORMAT(bulan,'%Y%m')='$pperiode_' AND icabangid='$pidcabpil' $filarea";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { mysqli_close($cnms); echo "Error SIMPAN DATA : $erropesan"; exit; }
    $berhasil="";
    
    echo $berhasil;
?>

<?PHP
hapusdata:
    
    
    mysqli_close($cnms);
?>