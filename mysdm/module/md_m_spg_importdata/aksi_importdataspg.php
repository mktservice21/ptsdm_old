<?php
    date_default_timezone_set('Asia/Jakarta');
    session_start();
    $puserid=$_SESSION['IDCARD'];
    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG....!!!";
        exit;
    }
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
    $module=$_GET['module'];
    $idmenu=$_GET['idmenu'];
    $act=$_GET['act'];
    
    include "../../config/koneksimysqli.php";
    
    $date1=$_POST['u_tgl1'];
    $ptgl= date("Y-m-01", strtotime($date1));
    $pbulan= date("Ym", strtotime($date1));
    
    $fdata_spg="";
    foreach ($_POST['chkbox_br'] as $nobrinput) {
        if (!empty($nobrinput)) {
            $fdata_spg .= "'".$nobrinput."',";
        }
    }
    if (!empty($fdata_spg)) {
        $fdata_spg="(".substr($fdata_spg, 0, -1).")";
    }
    
if ($module=="importdataspg" AND $act=="hapus") {
    if (!empty($fdata_spg)) {
        
        $query = "DELETE FROM dbmaster.t_spg_data WHERE id_spg IN $fdata_spg AND DATE_FORMAT(periode,'%Y%m')='$pbulan'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
}elseif ($module=="importdataspg" AND $act=="input") {
    
    if (!empty($fdata_spg)) {
        
        $query ="SELECT id_spg, nama, tempatlahir, tgllahir, alamat1, alamat2, agama, telpon, handphone, jeniskelamin, "
                . " tglmasuk, tglkeluar, pendidikan, status, icabangid, areaid, alokid, jabatid, atasanid, pemilikrekening, "
                . " namabank, cabangbank, norekening, kota, propinsi, sys_now, user1, deleted, penempatan, kategory, aktif, "
                . " '$ptgl' as periode, '$puserid' as userproses "
                . " FROM MKT.spg WHERE id_spg IN $fdata_spg";
        
        $query ="INSERT INTO dbmaster.t_spg_data("
                . " id_spg, nama, tempatlahir, tgllahir, alamat1, alamat2, agama, telpon, handphone, jeniskelamin, "
                . " tglmasuk, tglkeluar, pendidikan, status, icabangid, areaid, alokid, jabatid, atasanid, pemilikrekening, "
                . " namabank, cabangbank, norekening, kota, propinsi, sys_now, user1, deleted, penempatan, kategory, aktif, "
                . " periode, userproses)"
                . " $query";
        
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        include "../../config/koneksimysqli_it.php";
        foreach ($_POST['chkbox_br'] as $nobrinput) {
            if (!empty($nobrinput)) {
                $ptgl=$_POST['dtp_keluaar'][$nobrinput];
                
                if (!empty($ptgl)) {
                    $ptglkeluar= date("Y-m-d", strtotime($ptgl));
                    $filtglkeluar=" tglkeluar='$ptglkeluar' ";
                }else{
                    $filtglkeluar=" tglkeluar=NULL ";
                }    
                    
                $query = "UPDATE MKT.spg SET $filtglkeluar WHERE id_spg='$nobrinput'";
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                
            }
        }
        mysqli_close($cnit);
    
    }
}
    
    mysqli_close($cnmy);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
?>

