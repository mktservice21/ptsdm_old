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
if (($module=='laprincianbrrutin' OR $module=='laprutinrinciotc') AND $act=='input') {
    
    $pnourut=$_POST['unourut'];
    $pidrtnbr=$_POST['uidbrrutin'];
    $pidno=$_POST['uinoid'];
    $pcoakode=$_POST['ucoa'];
    
    //$berhasil = "$pnourut, $pidrtnbr, COA : $pcoakode";
    
    if (!empty($pnourut) AND !empty($pcoakode)) {
        
        $query = "UPDATE dbmaster.t_brrutin1 SET coa='$pcoakode' WHERE nourut='$pnourut' AND idrutin='$pidrtnbr' LIMIT 1";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Update"; exit; }
        
        $berhasil="berhasil";
        
    }
    
    
}
mysqli_close($cnmy);
echo $berhasil;
?>