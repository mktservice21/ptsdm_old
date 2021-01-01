<?php
    session_start();
    
    $berhasil="Tidak ada data yang diproses...";
    include "../../config/koneksimysqli.php";
    $apvid=$_SESSION['IDCARD'];
    
    if (empty($apvid)) {
        echo "Anda harus login ulang....";
        exit;
    }
    
    $pnoid=$_POST['unobr'];
    $gbrapv=$_POST['uttd'];
    
    $query = "UPDATE dbmaster.t_spg_gaji_br0 set apv3='$apvid', apvtgl3=NOW(), apvgbr3='$gbrapv' WHERE "
            . " idbrspg IN $pnoid";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "ERROR...."; exit; }
    
    mysqli_query($cnmy, "DELETE FROM dbimages.img_spg_gaji_br0 WHERE idbrspg IN $pnoid");
    
    mysqli_query($cnmy, "INSERT INTO dbimages.img_spg_gaji_br0 (periode, idbrspg, apvgbr3) SELECT DISTINCT periode, idbrspg, '$gbrapv' FROM dbmaster.t_spg_gaji_br0 WHERE idbrspg IN $pnoid");
    //mysqli_query($cnmy, "INSERT INTO dbimages.img_spg_gaji_br0 (periode, apvgbr3) SELECT DISTINCT periode, '$gbrapv' FROM dbmaster.t_spg_gaji_br0 WHERE idbrspg IN $pnoid");
    
    $berhasil = "data berhasil diproses";
    
    echo $berhasil;
?>
