<?php

    session_start();
    include "../../config/koneksimysqli.php";
    $cnmy=$cnmy;
    $dbname = "dbmaster";
    
    if (empty($_SESSION['IDCARD'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    $picabangid=$_POST['ucabang'];
    $ptgl01=$_POST['utgl'];
    $pperiode= date("Ym", strtotime($ptgl01));
    $ptgl= date("Y-m-d", strtotime($ptgl01));
    
    $berhasil="tidak ada data yang divalidasi";
    if ($act=='input') {
        mysqli_query($cnmy, "DELETE FROM dbmaster.t_spg_validate WHERE icabangid='$picabangid' AND DATE_FORMAT(bulan,'%Y%m')='$pperiode'");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Simpan"; exit; }
        
        $query ="INSERT INTO dbmaster.t_spg_validate (icabangid, bulan, userid)VALUES"
                . "('$picabangid', '$ptgl', '$_SESSION[IDCARD]')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Simpan"; exit; }
        
        $berhasil="";
    }

    mysqli_close($cnmy);
    
    echo $berhasil;
?>

