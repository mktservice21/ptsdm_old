<?php

session_start();
include "../../config/koneksimysqli_it.php";
$cnmy=$cnit;
$dbname="MKT";

if (isset($_GET['act'])) {
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    if ($_GET['act']=="hapusdata") {
        
        $query=mysqli_query($cnmy, "select * from $dbname.ispv0 WHERE icabangid='$_GET[idcab]' and divisiid='$_GET[divisi]' and "
                . " areaid='$_GET[idarea]' and karyawanid='$_GET[id]' and Date_format(tgl1,'%Y%m%d')='$_GET[tgl]'");
        $ketemu=  mysqli_num_rows($query);
        if ($ketemu>0) {
            $r=  mysqli_fetch_array($query);
            $sql= "insert into dbmaster.backup_ispv0(icabangid, areaid, divisiid, karyawanid, thnbln, tgl1, tgl2, aktif, user1, icabangid_2017, areaid_2017, srid, MODIFUN)"
                    . " select icabangid, areaid, divisiid, karyawanid, thnbln, tgl1, tgl2, 'Y', user1, icabangid_2017, areaid_2017, srid, '$_SESSION[USERID]' from $dbname.ispv0 WHERE "
                    . " icabangid='$_GET[idcab]' and divisiid='$_GET[divisi]' and "
                    . " areaid='$_GET[idarea]' and karyawanid='$_GET[id]' and Date_format(tgl1,'%Y%m%d')='$_GET[tgl]'";
            mysqli_query($cnmy, $sql);
            
            
            
            mysqli_query($cnmy, "update $dbname.ispv0 set aktif='N' WHERE icabangid='$_GET[idcab]' and divisiid='$_GET[divisi]' and "
                    . " areaid='$_GET[idarea]' and karyawanid='$_GET[id]' and Date_format(tgl1,'%Y%m%d')='$_GET[tgl]'");
            
            mysqli_query($cnmy, "DELETE from $dbname.ispv0 WHERE aktif='N' AND icabangid='$_GET[idcab]' and divisiid='$_GET[divisi]' and "
                . " areaid='$_GET[idarea]' and karyawanid='$_GET[id]' and Date_format(tgl1,'%Y%m%d')='$_GET[tgl]'");
            
        }else{
            exit;
        }
        $datasavems=SaveDataMS("MKT", "ispv0");
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
        
    }elseif ($_GET['act']=="aktifkan") {
        

        
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
        $datasavems=SaveDataMS("MKT", "ispv0");
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
    $datasavems=SaveDataMS("MKT", "ispv0");
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='penempatanspv'  AND $act=='input')
{
    $ptglinput= date("Y-m-d", strtotime($_POST['e_tglinput']));
    $query="insert into $dbname.ispv0 (icabangid, areaid, divisiid, karyawanid, tgl1, aktif)values"
            . "('$_POST[e_idcabang]', '$_POST[e_idarea]', '$_POST[cb_divisi]', '$_POST[e_idkaryawan]', '$ptglinput', '$_POST[rb_status]')";
    mysqli_query($cnmy, $query);
    
    
    $erropesan = mysqli_error($cnmy);
    if (!empty($erropesan))
        echo $erropesan;
    else{
        $datasavems=SaveDataMS("MKT", "ispv0");
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    }
}
elseif ($module=='penempatanspv'  AND $act=='update')
{
    $ptglinput= date("Y-m-d", strtotime($_POST['e_tglinput']));
    if (empty($_POST['l_tglinput']))
        $ltglinput="0000-00-00";
    else
        $ltglinput= date("Y-m-d", strtotime($_POST['l_tglinput']));
    
    
    
        $sql= "insert into dbmaster.backup_ispv0(icabangid, areaid, divisiid, karyawanid, thnbln, tgl1, tgl2, aktif, user1, icabangid_2017, areaid_2017, srid, MODIFUN)"
                . " select icabangid, areaid, divisiid, karyawanid, thnbln, tgl1, tgl2, 'Y', user1, icabangid_2017, areaid_2017, srid, '$_SESSION[USERID]' from $dbname.ispv0 WHERE "
                . " icabangid='$_POST[l_idcabang]' and areaid='$_POST[l_idarea]' and divisiid='$_POST[l_iddivisi]' and "
                . " karyawanid='$_POST[l_idkaryawan]' and ifnull(tgl1,'0000-00-00')='$ltglinput' and aktif='$_POST[l_aktif]'";
        mysqli_query($cnmy, $sql);
    
    $query="update $dbname.ispv0 set icabangid='$_POST[e_idcabang]', areaid='$_POST[e_idarea]', "
            . " divisiid='$_POST[cb_divisi]', karyawanid='$_POST[e_idkaryawan]', tgl1='$ptglinput', aktif='$_POST[rb_status]' where "
            . " icabangid='$_POST[l_idcabang]' and areaid='$_POST[l_idarea]' and divisiid='$_POST[l_iddivisi]' and "
            . " karyawanid='$_POST[l_idkaryawan]' and ifnull(tgl1,'0000-00-00')='$ltglinput' and aktif='$_POST[l_aktif]'";
    

    mysqli_query($cnmy, $query);
    
    $erropesan = mysqli_error($cnmy);
    if (!empty($erropesan))
        echo $erropesan;
    else{
        $datasavems=SaveDataMS("MKT", "ispv0");
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    }
}

function SaveDataMS($nmdb, $namatabel){
    include "../../config/koneksimysqli.php";
    $berhasil="";
    
    $query = "CREATE TABLE dbtemp.$namatabel (select * from it_$nmdb.$namatabel)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $berhasil="gagal"; }


    $query = "CREATE TABLE IF NOT EXISTS $nmdb.$namatabel (select * from it_$nmdb.$namatabel limit 1)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $berhasil="gagal"; }

    $query = "delete from $nmdb.$namatabel";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $berhasil="gagal"; }

    $query = "INSERT INTO $nmdb.$namatabel select * from dbtemp.$namatabel";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $berhasil="gagal"; }

    $query = "DROP TABLE dbtemp.$namatabel";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan."<br/>drop table dbtemp ".$namatabel; $berhasil="gagal"; }
    
    return $berhasil;
}
?>
