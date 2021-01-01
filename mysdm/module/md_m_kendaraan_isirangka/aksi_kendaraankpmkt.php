<?php
    session_start();
    
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    $cnmy=$cnmy;
    $dbname = "dbmaster";
    
    $puserid=$_SESSION['IDCARD'];
    
if ($module=='isidatakendaraanmkt' AND $act=='input')
{
    $idlama=$_POST['e_idlama'];
    $kodenya=$_POST['e_id'];
    
    $pnorangka=$_POST['e_norangka'];
    $pnomesin=$_POST['e_nomesin'];
    $ptglst=$_POST['e_tglstnk'];
    
    $ptgltempostnk="";
    if (!empty($ptglst)) {
        $ptglst_ = str_replace('/', '-', $ptglst);
        $ptgltempostnk =  date("Y-m-d", strtotime($ptglst_));
    }
    
    
    if (!empty($idlama)) {
        
        mysqli_query($cnmy, "UPDATE $dbname.t_kendaraan SET tgltempostnk=NULL, norangka='$pnorangka', nomesin='$pnomesin', userid='$puserid' WHERE nopol='$idlama' LIMIT 1");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        if (!empty($ptgltempostnk)) {
            mysqli_query($cnmy, "UPDATE $dbname.t_kendaraan SET tgltempostnk='$ptgltempostnk' WHERE nopol='$idlama' LIMIT 1");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }
        
    }
    
}


header('location:../../media.php?module=home');
?>