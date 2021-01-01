<?php

    session_start();
    include "../../config/koneksimysqli.php";
    $dbname = "dbmaster";
    
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    
    $ptgl01=$_POST['utgl'];
    $ptglproses= date("Y-m-01", strtotime($ptgl01));
    $periode1= date("Ym", strtotime($ptgl01));
        
    $berhasil="Tidak ada data yang disimpan";
    
    if ($module=="brdanabank" AND $act=="hapus") {
        $query = "DELETE from dbmaster.t_bank_saldo WHERE DATE_FORMAT(bulan,'%Y%m')='$periode1'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $berhasil="data berhasil dihapus...";
    }elseif ($module=="brdanabank" AND $act=="simpan") {
        
        $psaldoakhir=$_POST['usaldoakhir'];
        $psaldoakhir=str_replace(",","", $psaldoakhir);
        
        $query = "DELETE from dbmaster.t_bank_saldo WHERE DATE_FORMAT(bulan,'%Y%m')='$periode1'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query = "INSERT INTO dbmaster.t_bank_saldo (bulan, jumlah, userid)VALUES"
                . "('$ptglproses', '$psaldoakhir', '$_SESSION[IDCARD]')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        //$berhasil="$act, $periode1 : $ptglproses, $psaldoakhir, data berhasil disimpan";
        $berhasil="data berhasil disimpan";
    }
    
    mysqli_close($cnmy);
    echo $berhasil;

?>