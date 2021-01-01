<?php

session_start();
include "../../config/koneksimysqli.php";
$cnmy=$cnmy;
$dbname = "dbmaster";

$module=$_POST['u_module'];
$act=$_POST['u_act'];
$idmenu=$_POST['u_idmenu'];
$idkaryawan=$_POST['u_karyawan'];
$tgl1=$_POST['u_tgl1'];
$tgl2=$_POST['u_tgl2'];
$sesid=$_SESSION['IDCARD'];

$patasan1 = $_POST['e_atasan'];
$patasan2 = $_POST['e_atasan2'];
$patasan3 = $_POST['e_atasan3'];
    
if ($module=='mktplanuc' AND $act=='input') {
    $no=1;
    while (strtotime($tgl1) <= strtotime($tgl2)) {
        $ketfld="txtket".$no;
        if (isset($_POST[$ketfld])) {
            $keterangan=$_POST[$ketfld];
            mysqli_query($cnmy, "delete from $dbname.t_planuc_mkt where karyawanid='$idkaryawan' and tgl='$tgl1'");
            
            if (!empty($keterangan)) {
                mysqli_query($cnmy, "insert into $dbname.t_planuc_mkt (karyawanid, tgl, keterangan, userid, atasan1, atasan2, atasan3) values"
                        . " ('$idkaryawan', '$tgl1', '$keterangan', '$sesid', '$patasan1', '$patasan2', '$patasan3')");
            }
        }
        $no++;
        $tgl1 = date ("Y-m-d", strtotime("+1 day", strtotime($tgl1)));
    }
}
header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complit');
?>
