<?php
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
	
    session_start();
    include "../../config/koneksimysqli_it.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];

    $pacabangid=$_POST['e_cabang'];
    $pareaid=$_POST['e_area'];
    $pacabangidbaru=$_POST['e_cabangbaru'];
    $pareaidbaru=$_POST['e_areabaru'];
    
    //echo "LAMA : $pacabangid, $pareaid ... BARU :  $pacabangidbaru, $pareaidbaru"; exit;
    
    
    $query = "CALL MKT.cursor_icust_pindah('$pacabangid', '$pareaid', '$pacabangidbaru', '$pareaidbaru')";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
    $_SESSION['PIND_CABLAMA']=$pacabangid;
    $_SESSION['PIND_AREALAMA']=$pareaid;
    $_SESSION['PIND_CABBARU']=$pacabangidbaru;
    $_SESSION['PIND_AREABARU']=$pareaidbaru;
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act='.$act);
    //echo "berhasil...";
    
?>