<?php

    session_start();
    include "../../config/koneksimysqli.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    $pkert=$_POST['ket'];
    $pidnomor=$_POST['unobr'];

    $pketreject=$_POST['ketrejpen'];
    //$berhasil="$pkert, $pidnomor, $pketreject";
    $userid=$_SESSION['IDCARD'];
    if (empty($userid)) {
        echo "data tidak ada yang diproses, silakan login ulang..."; exit;
    }
    $berhasil = "Tidak ada data yang diproses...";
    if (!empty($pidnomor)) {
        if ($pkert=="simpan") {
            
            $ptgl=$_POST['utgl'];
            $ptglspd= date("Y-m-d", strtotime($ptgl));
            $pnospd=$_POST['unospd'];
            
            $query = "UPDATE dbmaster.t_suratdana_br SET tglspd='$ptglspd', nomor='$pnospd', userproses='$userid', tgl_proses=NOW() WHERE IFNULL(nomor,'')='' AND idinput IN $pidnomor";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
            $berhasil = "Data berhasil diproses...";
        }if ($pkert=="hapus") {
            $query = "UPDATE dbmaster.t_suratdana_br SET tglspd='', nomor='', userproses=NUll, tgl_proses=NULL WHERE idinput IN $pidnomor";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

            $berhasil = "Data berhasil dihapus...";
        }
    }
    

    echo $berhasil;
?>

