<?php

session_start();
include "../../config/koneksimysqli.php";
//$cnmy=$cnit;
$dbname="MKT";

if (isset($_GET['act'])) {
    if ($_GET['act']=="aktifkan") {
        
        $module=$_GET['module'];
        $act=$_GET['act'];
        $idmenu=$_GET['idmenu'];
        
        $query=mysqli_query($cnmy, "select * from $dbname.idm0 WHERE icabangid='$_GET[idcab]' and "
                . " karyawanid='$_GET[id]' and Date_format(tgl1,'%Y%m%d')='$_GET[tgl]'");
        $r=  mysqli_fetch_array($query);
        if ($r['aktif']=="Y") {
            mysqli_query($cnmy, "update $dbname.idm0 set aktif='N' WHERE icabangid='$_GET[idcab]' and "
                    . " karyawanid='$_GET[id]' and Date_format(tgl1,'%Y%m%d')='$_GET[tgl]'");
        }
        else {
            mysqli_query($cnmy, "update $dbname.idm0 set aktif='Y' WHERE icabangid='$_GET[idcab]' and "
                    . " karyawanid='$_GET[id]' and Date_format(tgl1,'%Y%m%d')='$_GET[tgl]'");
        }
        include "../../config/koneksimysqli_ms.php";
        mysqli_query($cnms, "CALL sls.prosesisi_data_idm0()");
        mysqli_close($cnms);
        mysqli_close($cnmy);
        //$datasavems=SaveDataMS("MKT", "idm0");
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    }
}
$module=$_POST['u_module'];
$act=$_POST['u_act'];
$idmenu=$_POST['u_idmenu'];

// Hapus entry
if ($module=='penempatandm' AND $act=='hapus')
{
    $query=mysqli_query($cnmy, "select * from $dbname.idm0 WHERE icabangid='$_GET[idcab]' and "
            . " karyawanid='$_GET[id]' and Date_format(tgl1,'%Y%m%d')='$_GET[tgl]'");
    $r=  mysqli_fetch_array($query);
    if ($r['aktif']=="Y") {
        mysqli_query($cnmy, "update $dbname.idm0 set aktif='N' WHERE icabangid='$_GET[idcab]' and "
                . " karyawanid='$_GET[id]' and Date_format(tgl1,'%Y%m%d')='$_GET[tgl]'");
    }
    else {
        mysqli_query($cnmy, "update $dbname.idm0 set aktif='Y' WHERE icabangid='$_GET[idcab]' and "
                . " karyawanid='$_GET[id]' and Date_format(tgl1,'%Y%m%d')='$_GET[tgl]'");
    }
        include "../../config/koneksimysqli_ms.php";
        mysqli_query($cnms, "CALL sls.prosesisi_data_idm0()");
        mysqli_close($cnms);
        mysqli_close($cnmy);
    //$datasavems=SaveDataMS("MKT", "idm0");
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='penempatandm'  AND $act=='input')
{
    $ptglinput= date("Y-m-d", strtotime($_POST['e_tglinput']));
    $query="insert into $dbname.idm0 (icabangid, karyawanid, tgl1, aktif)values"
            . "('$_POST[e_idcabang]', '$_POST[e_idkaryawan]', '$ptglinput', '$_POST[rb_status]')";
    mysqli_query($cnmy, $query);
    
    $erropesan = mysqli_error($cnmy);
    if (!empty($erropesan))
        echo $erropesan;
    else{
        include "../../config/koneksimysqli_ms.php";
        mysqli_query($cnms, "CALL sls.prosesisi_data_idm0()");
        mysqli_close($cnms);
        mysqli_close($cnmy);
        //$datasavems=SaveDataMS("MKT", "idm0");
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    }
}
elseif ($module=='penempatandm'  AND $act=='update')
{
    $ptglinput= date("Y-m-d", strtotime($_POST['e_tglinput']));
    $ltglinput= date("Y-m-d", strtotime($_POST['l_tglinput']));
    
    $query="update $dbname.idm0 set icabangid='$_POST[e_idcabang]', "
            . " karyawanid='$_POST[e_idkaryawan]', tgl1='$ptglinput', aktif='$_POST[rb_status]' where "
            . " icabangid='$_POST[l_idcabang]' and "
            . " karyawanid='$_POST[l_idkaryawan]' and tgl1='$ltglinput' and aktif='$_POST[l_aktif]'";

    mysqli_query($cnmy, $query);
    
    
    $erropesan = mysqli_error($cnmy);
    if (!empty($erropesan))
        echo $erropesan;
    else{
        include "../../config/koneksimysqli_ms.php";
        mysqli_query($cnms, "CALL sls.prosesisi_data_idm0()");
        mysqli_close($cnms);
        mysqli_close($cnmy);
        //$datasavems=SaveDataMS("MKT", "idm0");
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    }
}

function SaveDataMS_DM($nmdb, $namatabel){
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
