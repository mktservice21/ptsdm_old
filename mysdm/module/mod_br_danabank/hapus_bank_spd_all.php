<?php

    date_default_timezone_set('Asia/Jakarta');
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    $dbname = "dbmaster";
    
    $puserid=$_SESSION['IDCARD'];
    if (empty($puserid)) {
        echo "Anda harus LOGIN ULANG...!!!";
    }
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    $idinputbank=$_POST['uid'];
    $pparentid="";
    
    $berhasil = "Tidak ada data yang dihapus...";
    
    $query = "UPDATE dbmaster.t_suratdana_bank SET stsnonaktif='Y', userid='$puserid' WHERE parentidbank='$idinputbank' AND stsinput IN ('M', 'N')";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $berhasil = "data berhasil dihapus...";
    
    mysqli_close($cnmy);
    echo $berhasil;
    
?>