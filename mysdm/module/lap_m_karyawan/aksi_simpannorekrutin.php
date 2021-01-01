<?php

session_start();

    date_default_timezone_set('Asia/Jakarta');
    //ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    include "../../config/koneksimysqli.php";
    include "../../config/common.php";
    
// Hapus / non aktifkan
if ($module=='datakaryawan' AND $act=='hapus')
{
    
}
elseif ($module=='datakaryawan' AND ($act=='input' OR $act=='update' OR $act=='norekupdate'))
{
    
    $puserid=$_POST['e_idinputuser'];
    $pcardid=$_POST['e_idcarduser'];
    
    if (empty($puserid)) {
        $puserid="";
        if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
        if (isset($_SESSION['IDCARD'])) $pcardid=$_SESSION['IDCARD'];

        if (empty($puserid)) {
            mysqli_close($cnmy);
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }
    }
    
    
    $pkaryawanid=$_POST['e_idkaryawan'];
    $pbank_nama=$_POST['e_banknm'];
    $pbank_norek=$_POST['e_banknorek'];
    $pbank_atsnm=$_POST['e_bankan'];
    $pbank_nmcab=$_POST['e_bankcb'];
    
    if (!empty($pbank_nama)) $pbank_nama = str_replace("'", " ", $pbank_nama);
    if (!empty($pbank_norek)) $pbank_norek = str_replace("'", " ", $pbank_norek);
    if (!empty($pbank_atsnm)) $pbank_atsnm = str_replace("'", " ", $pbank_atsnm);
    if (!empty($pbank_nmcab)) $pbank_nmcab = str_replace("'", " ", $pbank_nmcab);
    
    
    $query = "DELETE from dbmaster.t_karyawan_bank_rutin WHERE karyawanid='$pkaryawanid' LIMIT 1";
    $result = mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "INSERT INTO dbmaster.t_karyawan_bank_rutin (karyawanid, nmbank, atasnama_b, cabang_b, norek_b, userid)VALUES"
            . "('$pkaryawanid', '$pbank_nama', '$pbank_atsnm', '$pbank_nmcab', '$pbank_norek', '$pcardid')";
    $result = mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    mysqli_close($cnmy);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    
    
}

?>