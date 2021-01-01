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
    
    $puserid=$_SESSION['IDCARD'];
    
    
    $tgl_pertama=$ptglpil;
    $ptglpilihupload = date("Y-m-d", strtotime($ptglpil));
    $pperiode_ = date("Y", strtotime($ptglpil));
    
    
    $berhasil="Tidak ada data yang diproses...";
    
    if (empty($pidcabpil)) { echo $berhasil; exit;}
    
    $query ="DELETE FROM tgt.targettahun WHERE DATE_FORMAT(bulan,'%Y')='$pperiode_' AND icabangid='$pidcabpil' AND YEAR(bulan)>'2019'";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { mysqli_close($cnms); echo "Error SIMPAN DATA : $erropesan"; exit; }
    $berhasil="Data berhasil dihapus...";
    
    echo $berhasil;
?>

<?PHP
hapusdata:
    
    
    mysqli_close($cnms);
?>

