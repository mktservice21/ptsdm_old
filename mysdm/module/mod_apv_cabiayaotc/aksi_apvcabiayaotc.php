<?php

    session_start();
    include "../../config/koneksimysqli.php";
    
    $kodeinput = " AND kode=3 ";
    $apvfinance = " AND ifnull(fin,'')='' ";
    
    $module=$_GET['module'];
    $idmenu=$_GET['idmenu'];
    $act=$_GET['act'];
    $cnmy=$cnmy;
    $dbname = "dbmaster";
    
    $pidapprove=$_SESSION['IDCARD'];
    
if ($module=="appmktcabiayaotc") {
    $noidbr=$_POST['unobr'];
    if ($noidbr=="()") $noidbr = "";
    $stsspv=$_POST['ket'];
    $karyawanapv=$_POST['ukaryawan'];
    $lvlposisi=$_POST['ulevel'];
    $noteapv = "tidak ada data yang diapprove";
    
    
    if (!empty($noidbr) AND !empty($karyawanapv) AND !empty($lvlposisi)) {
        
        if ($act=="simpan_ttdallam") {
            $gbrapv=$_POST['uttd'];
            
            if ($_SESSION['JABATANID']=="36") {//HOS
                
                mysqli_query($cnmy, "update $dbname.t_ca0 set tgl_atasan4=NOW(), gbr_atasan4='$gbrapv' WHERE idca in $noidbr");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                mysqli_query($cnmy, "update $dbname.t_brrutin0 set tgl_atasan4=NOW(), gbr_atasan4='$gbrapv' WHERE idrutin in $noidbr");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                $noteapv = "data berhasil diapprove...";
                
            }elseif ($_SESSION['JABATANID']=="10") {//AM
                
                mysqli_query($cnmy, "update $dbname.t_ca0 set tgl_atasan2=NOW(), gbr_atasan2='$gbrapv' WHERE idca in $noidbr");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                mysqli_query($cnmy, "update $dbname.t_brrutin0 set tgl_atasan2=NOW(), gbr_atasan2='$gbrapv' WHERE idrutin in $noidbr");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                mysqli_query($cnmy, "update $dbname.t_ca0 set tgl_atasan3=NOW() WHERE idca in $noidbr AND "
                        . " IFNULL(tgl_atasan3,'0000-00-00')='0000-00-00' AND "
                        . " IFNULL(atasan3,'')=''");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                mysqli_query($cnmy, "update $dbname.t_brrutin0 set tgl_atasan3=NOW() WHERE idrutin in $noidbr AND "
                        . " IFNULL(tgl_atasan3,'0000-00-00')='0000-00-00' AND "
                        . " IFNULL(atasan3,'')=''");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                
                $noteapv = "data berhasil diapprove...";
                
            }elseif ($_SESSION['JABATANID']=="20") {//DM
                
                mysqli_query($cnmy, "update $dbname.t_ca0 set tgl_atasan3=NOW(), gbr_atasan3='$gbrapv' WHERE idca in $noidbr AND IFNULL(tgl_atasan4,'')=''");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                mysqli_query($cnmy, "update $dbname.t_brrutin0 set tgl_atasan3=NOW(), gbr_atasan3='$gbrapv' WHERE idrutin in $noidbr AND IFNULL(tgl_atasan4,'')=''");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                
                $noteapv = "data berhasil diapprove...";
                
            }else{
                //23=Merchandiser ||| 18=Supervisor
                mysqli_query($cnmy, "update $dbname.t_ca0 set tgl_atasan1=NOW(), gbr_atasan1='$gbrapv' WHERE idca in $noidbr");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                mysqli_query($cnmy, "update $dbname.t_brrutin0 set tgl_atasan1=NOW(), gbr_atasan1='$gbrapv' WHERE idrutin in $noidbr");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                mysqli_query($cnmy, "update $dbname.t_ca0 set tgl_atasan2=NOW() WHERE idca in $noidbr AND "
                        . " IFNULL(tgl_atasan2,'0000-00-00')='0000-00-00' AND "
                        . " IFNULL(atasan2,'')='' AND IFNULL(tgl_atasan3,'')=''");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                mysqli_query($cnmy, "update $dbname.t_brrutin0 set tgl_atasan2=NOW() WHERE idrutin in $noidbr AND "
                        . " IFNULL(tgl_atasan2,'0000-00-00')='0000-00-00' AND "
                        . " IFNULL(atasan2,'')='' AND IFNULL(tgl_atasan3,'')=''");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                
                mysqli_query($cnmy, "update $dbname.t_ca0 set tgl_atasan3=NOW() WHERE idca in $noidbr AND "
                        . " IFNULL(tgl_atasan3,'0000-00-00')='0000-00-00' AND "
                        . " IFNULL(atasan3,'')='' AND IFNULL(tgl_atasan4,'')='' AND IFNULL(tgl_atasan2,'')<>''");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                mysqli_query($cnmy, "update $dbname.t_brrutin0 set tgl_atasan3=NOW() WHERE idrutin in $noidbr AND "
                        . " IFNULL(tgl_atasan3,'0000-00-00')='0000-00-00' AND "
                        . " IFNULL(atasan3,'')='' AND IFNULL(tgl_atasan4,'')='' AND IFNULL(tgl_atasan2,'')<>''");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                
                $noteapv = "data berhasil diapprove...";
            }
            
        }elseif ($act=="unapprove") {
            if ($_SESSION['JABATANID']=="36") {//HOS
                
                mysqli_query($cnmy, "update $dbname.t_ca0 set tgl_atasan4=NULL, gbr_atasan4=NULL WHERE idca in $noidbr AND IFNULL(tgl_fin,'')=''");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                mysqli_query($cnmy, "update $dbname.t_brrutin0 set tgl_atasan4=NULL, gbr_atasan4=NULL WHERE idrutin in $noidbr AND IFNULL(tgl_fin,'')=''");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                $noteapv = "data berhasil diunapprove...";
                
            }elseif ($_SESSION['JABATANID']=="10") {//AM
                
                mysqli_query($cnmy, "update $dbname.t_ca0 set tgl_atasan2=NULL, gbr_atasan2=NULL WHERE idca in $noidbr AND IFNULL(tgl_atasan4,'')=''");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                mysqli_query($cnmy, "update $dbname.t_brrutin0 set tgl_atasan2=NULL, gbr_atasan2=NULL WHERE idrutin in $noidbr AND IFNULL(tgl_atasan4,'')=''");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                mysqli_query($cnmy, "update $dbname.t_ca0 set tgl_atasan3=NULL WHERE idca in $noidbr AND "
                        . " IFNULL(atasan3,'')='' AND IFNULL(tgl_atasan4,'')=''");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                mysqli_query($cnmy, "update $dbname.t_brrutin0 set tgl_atasan3=NULL WHERE idrutin in $noidbr AND "
                        . " IFNULL(atasan3,'')='' AND IFNULL(tgl_atasan4,'')=''");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                
                $noteapv = "data berhasil diunapprove...";
                
            }elseif ($_SESSION['JABATANID']=="20") {//DM
                
                mysqli_query($cnmy, "update $dbname.t_ca0 set tgl_atasan3=NULL, gbr_atasan3=NULL WHERE idca in $noidbr AND IFNULL(tgl_atasan4,'')=''");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                mysqli_query($cnmy, "update $dbname.t_brrutin0 set tgl_atasan3=NULL, gbr_atasan3=NULL WHERE idrutin in $noidbr AND IFNULL(tgl_atasan4,'')=''");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                $noteapv = "data berhasil diunapprove...";
                
            }else{
                //23=Merchandiser ||| 18=Supervisor
                mysqli_query($cnmy, "update $dbname.t_ca0 set tgl_atasan1=NULL, gbr_atasan1=NULL WHERE idca in $noidbr");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                mysqli_query($cnmy, "update $dbname.t_brrutin0 set tgl_atasan1=NULL, gbr_atasan1=NULL WHERE idrutin in $noidbr");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                
                mysqli_query($cnmy, "update $dbname.t_ca0 set tgl_atasan3=NULL WHERE idca in $noidbr AND "
                        . " IFNULL(tgl_atasan3,'0000-00-00')='0000-00-00' AND "
                        . " IFNULL(atasan3,'')='' AND IFNULL(tgl_atasan4,'')=''");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                mysqli_query($cnmy, "update $dbname.t_brrutin0 set tgl_atasan3=NULL WHERE idrutin in $noidbr AND "
                        . " IFNULL(tgl_atasan3,'0000-00-00')='0000-00-00' AND "
                        . " IFNULL(atasan3,'')='' AND IFNULL(tgl_atasan4,'')=''");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                
                mysqli_query($cnmy, "update $dbname.t_ca0 set tgl_atasan2=NULL WHERE idca in $noidbr AND "
                        . " IFNULL(tgl_atasan2,'0000-00-00')='0000-00-00' AND "
                        . " IFNULL(atasan2,'')='' AND IFNULL(tgl_atasan3,'')=''");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                mysqli_query($cnmy, "update $dbname.t_brrutin0 set tgl_atasan2=NULL WHERE idrutin in $noidbr AND "
                        . " IFNULL(tgl_atasan2,'0000-00-00')='0000-00-00' AND "
                        . " IFNULL(atasan2,'')='' AND IFNULL(tgl_atasan3,'')=''");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                
                $noteapv = "data berhasil diunapprove...";
            }
        }elseif ($act=="reject") {
            
            $kethapus = $_POST['ketrejpen'];
            if ($kethapus=="null") $kethapus="";
            if (!empty($kethapus)) $kethapus =", Ket Reject : ".$kethapus;
            if (!empty($kethapus)) $kethapus = str_replace("'", " ", $kethapus);
                
            mysqli_query($cnmy, "update $dbname.t_ca0 set stsnonaktif='Y', userid='$pidapprove', keterangan=CONCAT(keterangan,'$kethapus',', $_SESSION[NAMALENGKAP], ', NOW()) WHERE idca in $noidbr AND IFNULL(tgl_fin,'')=''");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

            mysqli_query($cnmy, "update $dbname.t_brrutin0 set stsnonaktif='Y', userid='$pidapprove', keterangan=CONCAT(keterangan,'$kethapus',', $_SESSION[NAMALENGKAP], ', NOW()) WHERE idrutin in $noidbr AND IFNULL(tgl_fin,'')=''");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $noteapv = "data berhasil direject...";
            
        }
        
    }
    
    echo $noteapv;
    
}

?>

