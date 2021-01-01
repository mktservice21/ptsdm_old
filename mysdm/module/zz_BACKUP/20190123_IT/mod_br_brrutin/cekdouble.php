<?php
    session_start();
    include "../../config/koneksimysqli_it.php";
    $cnmy=$cnit;
    $dbname = "dbmaster";
    $pidrutin = $_POST['uidrutin'];
    $pkaryawan = $_POST['ukar'];
    $pkode = $_POST['ukode'];
    $date1 =  date("Ym", strtotime($_POST['ubulan']));
    
    $nobrid="";
    $sql=  mysqli_query($cnmy, "select idrutin from $dbname.t_brrutin0 WHERE kode=1 AND stsnonaktif <> 'Y' AND karyawanid='$pkaryawan' AND "
            . " kodeperiode='$pkode' AND DATE_FORMAT(bulan,'%Y%m')='$date1'");
    $ketemu=  mysqli_num_rows($sql);
    if ($ketemu>0){
        $o=  mysqli_fetch_array($sql);
        $nobrid=$o['idrutin'];
    }
    echo "$nobrid";
?>

