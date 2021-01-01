<?php

session_start();
include "../../config/koneksimysqli.php";


$module=$_POST['u_module'];
$act=$_POST['u_act'];
$idmenu=$_POST['u_idmenu'];

if ($module=='datakaryawanlevel' AND $act=='hapus')
{
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='datakaryawanlevel')
{
    if ($act=='input'){
        
    }elseif ($act=='update'){
        include "../../config/fungsi_sql.php";
        mysqli_query($cnmy, "delete from dbmaster.karyawan_level WHERE karyawanId='$_POST[id]'");
        $lvljbt=  getfieldcnmy("select LEVELPOSISI as lcfields from dbmaster.jabatan_level where jabatanId='$_POST[cb_jabatan]'");
        $alvlemp=  (int)substr($lvljbt, 2,1);    
        mysqli_query($cnmy, "insert into dbmaster.karyawan_level (karyawanId, LEVELPOSISI) values ('$_POST[id]', '$lvljbt')");
        mysqli_query($cnmy, "update dbmaster.karyawan_level set LEVEL$alvlemp='$_POST[id]' where karyawanId='$_POST[id]'");
        
        //atasan
        $lvljbtatasan=  getfieldcnmy("select LEVELPOSISI as lcfields from dbmaster.v_karyawan where karyawanId='$_POST[cb_atasan]'");
        mysqli_query($cnmy, "update dbmaster.karyawan_level set ATASANID='$_POST[cb_atasan]', LVLATASAN='$lvljbtatasan' where karyawanId='$_POST[id]'");
        
        $alvl=  (int)substr($lvljbtatasan, 2,1);
        mysqli_query($cnmy, "update dbmaster.karyawan_level set LEVEL$alvl='$_POST[cb_atasan]' where karyawanId='$_POST[id]'");
        $alvl=(int)$alvl+1;
        $ii=""; $atasan=$_POST['cb_atasan'];
        $latasa="";$no=1;
        for ($x=$alvl; $x<=9; $x++){
            //if ($no==1) {
            $atasan=  getfieldcnmy("select atasanId as lcfields from dbmaster.v_karyawan where karyawanId='$atasan'");
            $latasa=  getfieldcnmy("select LEVELPOSISI as lcfields from dbmaster.v_karyawan where karyawanId='$atasan'");
            $latasa=  (int)substr($latasa, 2,1);
            mysqli_query($cnmy, "update dbmaster.karyawan_level set LEVEL$latasa='$atasan' where karyawanId='$_POST[id]' and LEVEL$latasa is null");
            //}
            $no++;
        }
    }
}
//echo "$atasan, $latasa";
header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
?>

