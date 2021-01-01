<?php
    include "../../config/koneksimysqli_it.php"; 
    
    $pket=$_POST['ket'];
    $ptglbr= date("Y-m-d", strtotime($_POST['tglbr']));
    $pno=(int)$_POST['tno'];
    mysqli_query($cnit, "DELETE FROM dbmaster.t_otc_norekapdanabr_b where tglbr='$ptglbr'");
    if ($pket=="simpan") {
        mysqli_query($cnit, "UPDATE dbmaster.t_otc_norekapdanabr SET tno='$pno'");
    }
    $noteinput="berhasil";
    echo $noteinput;
    
?>