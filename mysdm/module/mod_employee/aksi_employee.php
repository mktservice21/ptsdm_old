<?php
session_start();
include "../../config/koneksimysqli.php";
include "../../config/library.php";

$module=$_GET['module'];
$act=$_GET['act'];
$xmodp=$_GET['xmodp'];


// Hapus employee
if ($module=='employee' AND $act=='hapus'){
    
    if (strtoupper($_GET['aktif'])=="NONAKTIF")
        mysqli_query($cnmy, "update t_employee set STSNONAKTIF=1 WHERE CARDID='$_GET[id]'");
    else
        mysqli_query($cnmy, "update t_employee set STSNONAKTIF=null WHERE CARDID='$_GET[id]'");

    header('location:../../media.php?module='.$module.'&idmenu='.'&xmodp='.$xmodp.'&act=complt');
}

// Input modul
else{
    $tgllahir= date("Y-m-d", strtotime($_POST['e_born']));
    $tglmarit= date("Y-m-d", strtotime($_POST['e_dom']));

    
    if ($module=='employee' AND $act=='input'){
        //echo "$tgllahir, $tglmarit, $_POST[e_id], $_POST[e_bcity]";exit;
        // Input data employee
        $ssql="INSERT INTO t_employee(CARDID,EMPLOYEE)VALUES('$_POST[e_id]','$_POST[e_name]')";
        mysqli_query($cnmy, $ssql);

    }
    // Update modul
    elseif ($module=='employee' AND $act=='update'){

    }

    $ssql="UPDATE t_employee SET EMPLOYEE = '$_POST[e_name]',
                            NIK = '$_POST[e_nik]', T_LAHIR = '$_POST[e_bcity]',
                            KTP = '$_POST[e_ktp]', NPWP = '$_POST[e_npwp]', JEKEL = '$_POST[e_genre]',
                            BLOOD = '$_POST[e_bold]', KDRELIGION = '$_POST[e_religi]', KDMARITAL = '$_POST[e_marital]',
                            TELP = '$_POST[e_tlp]', HP = '$_POST[e_celp]',
                            ALAMAT = '$_POST[e_addr]', EMAIL = '$_POST[e_email]',
                            KDJBT = '$_POST[e_jabatan]', KDDIVISI = '$_POST[e_divisi]'
                            , TGL_LAHIR = null, DATE_MARITAL = null
                           WHERE CARDID   = '$_POST[e_id]'";
    mysqli_query($cnmy, $ssql);

    if (!empty ($_POST['e_born'])){
        mysqli_query($cnmy, "UPDATE t_employee SET TGL_LAHIR = '$tgllahir' WHERE CARDID   = '$_POST[e_id]'");
    }

    if (!empty ($_POST['e_dom'])){
        mysqli_query($cnmy, "UPDATE t_employee SET DATE_MARITAL = '$tglmarit' WHERE CARDID   = '$_POST[e_id]'");
    }

    if (empty($_POST['e_tanpa']) AND !empty($_POST['e_email'])){
        $adauser=mysqli_num_rows(mysqli_query($cnmy, "select IDCARD from t_users where IDCARD='$_POST[e_id]'"));
        if ($adauser==0){
            $adapakaiemail=mysqli_num_rows(mysqli_query($cnmy, "select IDCARD from t_users where EMAIL='$_POST[e_email]' AND IDCARD<>'$_POST[e_id]'"));
            if ($adapakaiemail==0){
                include "../../config/encriptpassword.php";
                $tanggal=$tgl_sekarang;
                $pass=  encriptpasswordSSQl("123456", $tanggal);
                $grpuser="0";

                mysqli_query($cnmy, "INSERT INTO t_users(STATUS_USER, IDCARD, EMAIL, USERNAME,
                         CREATEDPW, PASSWORD, ID_GROUP, ADMIN, AWAL, BLOKIR)
                        VALUES('PETUGAS', '$_POST[e_id]', '$_POST[e_email]', '$_POST[e_email]',
                        '$tgl_sekarang', '$pass', '$grpuser', 'N', 'Y', 'Y')");
            }
        }
    }

    header('location:../../media.php?module='.$module.'&idmenu='.'&xmodp='.$xmodp.'&act=complt');

}
?>
