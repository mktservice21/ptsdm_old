<?php
session_start();
include "../../config/koneksimysqli.php";

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];


// Hapus employee
if ($module=='employee' AND $act=='hapus'){

}

// Input modul
else{
    
    $cari=mysqli_query($cnmy, "select * from dbmaster.sdm_users WHERE karyawanId   = '$_POST[e_id]'");
    $ketemu=mysqli_num_rows($cari);
    if ($ketemu==0){
        mysqli_query($cnmy, "insert into dbmaster.sdm_users(karyawanId)values('$_POST[e_id]')");
    }
    
    $ssql="UPDATE dbmaster.sdm_users SET AKHUSUS='N', ID_GROUP = '$_POST[e_ugroup]', LEVEL='$_POST[rb_tipe]' WHERE karyawanId   = '$_POST[e_id]'";
    mysqli_query($cnmy, $ssql);

    if (!empty($_POST['e_user'])){
        $ssql="UPDATE dbmaster.sdm_users SET USERNAME = '$_POST[e_user]' WHERE karyawanId   = '$_POST[e_id]'";
        mysqli_query($cnmy, $ssql);
    }

    if (!empty($_POST['e_pass'])){
        include "../../config/library.php";
        include "../../config/encriptpassword.php";
        $pass=  encriptpasswordSSQl($_POST['e_pass'], $tgl_sekarang);
        $ssql="UPDATE dbmaster.sdm_users SET CREATEDPW=current_date(), PASSWORD = '$pass' WHERE karyawanId   = '$_POST[e_id]'";
        mysqli_query($cnmy, $ssql);
    }
    
     mysqli_query($cnmy, "delete from dbmaster.sdm_users_khusus WHERE karyawanId   = '$_POST[e_id]'");
    if ($_POST['rb_khusus']=="Y") {
        $ssql="UPDATE dbmaster.sdm_users SET AKHUSUS='Y' WHERE karyawanId   = '$_POST[e_id]'";
        mysqli_query($cnmy, $ssql);
        
        if (!empty($_POST['chkbox_divisiprod'])) {
            $tag = implode(',',$_POST['chkbox_divisiprod']);
            $arr_kata = explode(",",$tag);
            $jumlah_tag = substr_count($tag, ",") + 1;
            $u=0; $uTag="";
            for ($x=0; $x<=$jumlah_tag; $x++){
                if (!empty($arr_kata[$u])){
                    $uTag=trim($arr_kata[$u]);
                    $ssql="insert into dbmaster.sdm_users_khusus (karyawanId, DivProdId)values('$_POST[e_id]', '$uTag')";
                    mysqli_query($cnmy, $ssql);
                }
                $u++;
            }
        }
    }

    header('location:../../media.php?module='.$module.'&idmenu='.'&idmenu='.$idmenu.'&act=complt');

}
?>
