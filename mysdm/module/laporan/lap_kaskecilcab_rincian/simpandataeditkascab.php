<?php
date_default_timezone_set('Asia/Jakarta');
session_start();
include "../../../config/koneksimysqli.php";
$dbname = "dbmaster";

$puserid=$_SESSION['IDCARD'];
if (empty($puserid)) {
    echo "ANDA HARUS LOGIN ULANG...!!!";
    mysqli_close($cnmy);
    exit;
}

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

$berhasil="Tidak ada data yang disimpan";
if (($module=='laprinciankaskecilcab' OR $module=='laprinciankaskecilcabotc') AND $act=='input') {
    
    $pnourut=$_POST['unourut'];
    $pidkascab=$_POST['uidkascab'];
    $pidno=$_POST['uinoid'];
    $pcoakode=$_POST['ucoa'];
    
    //$berhasil = "$pnourut, $pidkascab, COA : $pcoakode";
    
    if (!empty($pnourut) AND !empty($pcoakode)) {
        
        $query = "UPDATE dbmaster.t_kaskecilcabang_d SET coa4='$pcoakode' WHERE nourut='$pnourut' AND idkascab='$pidkascab' LIMIT 1";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Update"; exit; }
        
        $berhasil="berhasil";
        
    }
    
    
}
mysqli_close($cnmy);
echo $berhasil;
?>