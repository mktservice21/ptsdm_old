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
    
    $query = "SELECT nomor, nodivisi, parentidbank FROM dbmaster.t_suratdana_bank WHERE idinputbank='$idinputbank'";
    $showkan= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($showkan);
    if ($ketemu>0){
        $nx= mysqli_fetch_array($showkan);
        $pparentid=$nx['parentidbank'];
    }
    
    $query = "UPDATE dbmaster.t_suratdana_bank SET stsnonaktif='Y', userid='$puserid' WHERE idinputbank='$idinputbank'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
                    $query = "SELECT sum(jumlah) jumlah FROM dbmaster.t_suratdana_bank WHERE stsnonaktif<>'Y' AND "
                            . " IFNULL(parentidbank,'')='$pparentid' ANd stsinput='N'";
                    $tampil= mysqli_query($cnmy, $query);
                    $ketemu= mysqli_num_rows($tampil);
                    if ($ketemu>0){
                        $xs= mysqli_fetch_array($tampil);
                        $pjumltot=$xs['jumlah'];
                        if (empty($pjumltot)) $pjumltot=0;
                        
                        $query = "UPDATE $dbname.t_suratdana_bank SET jumlah=$pjumltot WHERE stsnonaktif<>'Y' AND IFNULL(stsinput,'')='M' AND IFNULL(parentidbank,'')='$pparentid'";
                        mysqli_query($cnmy, $query);
                        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                    }
    $berhasil = "data berhasil dihapus...";
    
    mysqli_close($cnmy);
    echo $berhasil;
    
?>