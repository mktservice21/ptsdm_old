<?php

session_start();
include "../../config/koneksimysqli_it.php";
$cnmy=$cnit;
$dbname="MKT";


if (isset($_GET['act'])) {
    if ($_GET['act']=="aktifkan") {
        
        $module=$_GET['module'];
        $act=$_GET['act'];
        $idmenu=$_GET['idmenu'];
        
        $query=mysqli_query($cnmy, "select * from $dbname.ispv0 WHERE icabangid='$_GET[idcab]' and divisiid='$_GET[divisi]' and "
                . " areaid='$_GET[idarea]' and karyawanid='$_GET[id]' and Date_format(tgl1,'%Y%m%d')='$_GET[tgl]'");
        $r=  mysqli_fetch_array($query);
        if ($r['aktif']=="Y") {
            mysqli_query($cnmy, "update $dbname.ispv0 set aktif='N' WHERE icabangid='$_GET[idcab]' and divisiid='$_GET[divisi]' and "
                    . " areaid='$_GET[idarea]' and karyawanid='$_GET[id]' and Date_format(tgl1,'%Y%m%d')='$_GET[tgl]'");
        }
        else {
            mysqli_query($cnmy, "update $dbname.ispv0 set aktif='Y' WHERE icabangid='$_GET[idcab]' and divisiid='$_GET[divisi]' and "
                    . " areaid='$_GET[idarea]' and karyawanid='$_GET[id]' and Date_format(tgl1,'%Y%m%d')='$_GET[tgl]'");
        }
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    }
}
$module=$_POST['u_module'];
$act=$_POST['u_act'];
$idmenu=$_POST['u_idmenu'];

// Hapus entry
if ($module=='penempatanspv' AND $act=='hapus')
{
    $query=mysqli_query($cnmy, "select * from $dbname.ispv0 WHERE icabangid='$_GET[idcab]' and divisiid='$_GET[divisi]' and "
            . " areaid='$_GET[idarea]' and karyawanid='$_GET[id]' and Date_format(tgl1,'%Y%m%d')='$_GET[tgl]'");
    $r=  mysqli_fetch_array($query);
    if ($r['aktif']=="Y") {
        mysqli_query($cnmy, "update $dbname.ispv0 set aktif='N' WHERE icabangid='$_GET[idcab]' and divisiid='$_GET[divisi]' and "
                . " areaid='$_GET[idarea]' and karyawanid='$_GET[id]' and Date_format(tgl1,'%Y%m%d')='$_GET[tgl]'");
    }
    else {
        mysqli_query($cnmy, "update $dbname.ispv0 set aktif='Y' WHERE icabangid='$_GET[idcab]' and divisiid='$_GET[divisi]' and "
                . " areaid='$_GET[idarea]' and karyawanid='$_GET[id]' and Date_format(tgl1,'%Y%m%d')='$_GET[tgl]'");
    }
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='penempatanspv'  AND $act=='input')
{
    $ptglinput= date("Y-m-d", strtotime($_POST['e_tglinput']));
    $query="insert into $dbname.ispv0 (icabangid, areaid, divisiid, karyawanid, tgl1, aktif)values"
            . "('$_POST[e_idcabang]', '$_POST[e_idarea]', '$_POST[cb_divisi]', '$_POST[e_idkaryawan]', '$ptglinput', '$_POST[rb_status]')";
    mysqli_query($cnmy, $query);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='penempatanspv'  AND $act=='update')
{
    $ptglinput= date("Y-m-d", strtotime($_POST['e_tglinput']));
    $ltglinput= date("Y-m-d", strtotime($_POST['l_tglinput']));
    
    $query="update $dbname.ispv0 set icabangid='$_POST[e_idcabang]', areaid='$_POST[e_idarea]', "
            . " divisiid='$_POST[cb_divisi]', karyawanid='$_POST[e_idkaryawan]', tgl1='$ptglinput', aktif='$_POST[rb_status]' where "
            . " icabangid='$_POST[l_idcabang]' and areaid='$_POST[l_idarea]' and divisiid='$_POST[l_iddivisi]' and "
            . " karyawanid='$_POST[l_idkaryawan]' and tgl1='$ltglinput' and aktif='$_POST[l_aktif]'";

    mysqli_query($cnmy, $query);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
?>
