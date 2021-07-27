<?php

session_start();

    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","1G");
    ini_set('max_execution_time', 0);
    
$puserid="";
if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];


if (empty($puserid)) {
    echo "ANDA HARUS LOGIN ULANG...";
    exit;
}

$pidcard=$_SESSION['IDCARD'];
$pmodule=$_GET['module'];
$pact=$_GET['act'];
$pidmenu=$_GET['idmenu'];

include "../../config/koneksimysqli.php";
include "../../config/fungsi_sql.php";

if ($pmodule=='entrybrnon')
{
    
    if ($pact=="updatekryidcab") {
        
        $pkaryawanid=$_POST['e_kryid'];
        $pcabangid=$_POST['cb_cabangid'];
        
        if (!empty($pkaryawanid)) {
            
            foreach ($_POST['chkbox_br'] as $no_brid) {
                if (!empty($no_brid)) {
                    $pkryidpl=$_POST['txt_kryid'][$no_brid];
                    $pcabidpl=$_POST['txt_cabid'][$no_brid];
                    
                    //echo "$no_brid, $pkryidpl, $pcabidpl<br/>";
                    
                    $query = "DELETE FROM hrd.br0_ganti_karyawan WHERE brid='$no_brid' LIMIT 1";
                    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                    
                    $query = "INSERT INTO hrd.br0_ganti_karyawan (brid, karyawanid, icabangid, userid)VALUES "
                            . "('$no_brid', '$pkryidpl', '$pcabidpl', '$pidcard')";
                    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                    
                    $query = "UPDATE hrd.br0 SET karyawanid='$pkaryawanid', icabangid='$pcabangid' WHERE brid='$no_brid' LIMIT 1";
                    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                    
                }
            }
            
        }
        
        
        mysqli_close($cnmy);
        
        //echo "$pkaryawanid, $pcabangid";
        header('location:../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&act=complete');
        
    }
    
}
?>

