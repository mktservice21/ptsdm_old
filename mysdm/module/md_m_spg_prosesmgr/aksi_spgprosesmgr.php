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
    
    $query = "UPDATE dbmaster.t_spg_gaji_br0 set apv2='$apvid', apvtgl2=NOW(), apvgbr2='$gbrapv' WHERE "
            . " idbrspg IN $pnoid";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "ERROR...."; exit; }
    
    //mysqli_query($cnmy, "DELETE FROM dbimages.img_spg_gaji_br0 WHERE idbrspg IN $pnoid");
    //mysqli_query($cnmy, "INSERT INTO dbimages.img_spg_gaji_br0 (periode, idbrspg, apvgbr2) SELECT DISTINCT periode, idbrspg, '$gbrapv' FROM dbmaster.t_spg_gaji_br0 WHERE idbrspg IN $pnoid");
    
    
    
    $userid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DGJSPGPOTC01_".$userid."_$now ";
    $query = "SELECT distinct idbrspg, apvgbr2 FROM dbimages.img_spg_gaji_br0 WHERE idbrspg IN $pnoid";
    $query ="CREATE TEMPORARY TABLE $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    mysqli_query($cnmy, "INSERT INTO dbimages.img_spg_gaji_br0 (periode, idbrspg, apvgbr2) "
            . "SELECT DISTINCT periode, idbrspg, '$gbrapv' FROM dbmaster.t_spg_gaji_br0 WHERE idbrspg IN $pnoid"
            . "AND idbrspg NOT IN (select distinct IFNULL(idbrspg,'') from $tmp01)");
    
    mysqli_query($cnmy, "UPDATE dbimages.img_spg_gaji_br0 SET apvgbr2='$gbrapv' WHERE idbrspg IN $pnoid "
            . " AND idbrspg NOT IN (select distinct IFNULL(idbrspg,'') from $tmp01 WHERE IFNULL(apvgbr2,'')<>'') ");
    
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    
    $berhasil = "data berhasil diproses";
    
    echo $berhasil;
?>
