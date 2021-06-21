<?php

    session_start();
    include "../../config/koneksimysqli.php";
    
    $module=$_GET['module'];
    $idmenu=$_GET['idmenu'];
    $act=$_GET['act'];
    $cnmy=$cnmy;
    $dbname = "dbmaster";
    
    $pses_grpuser=$_SESSION['GROUP'];
    $pses_divisi=$_SESSION['DIVISI'];
    $pses_idcard=$_SESSION['IDCARD'];
    
    
if ($module=="ttdspdfin") {
    $noidbr=$_POST['unobr'];
    if ($noidbr=="()") $noidbr = "";
    $stsspv=$_POST['ket'];
    $karyawanapv=$_POST['ukaryawan'];
    $noteapv = "tidak ada data yang diapprove";
    
    if (!empty($noidbr) AND !empty($karyawanapv)) {
        
        if ($act=="simpan_ttdallam") {
            //include "../../config/ttdkosong.php";
            //$ttdkosong = ttdimage ();
            $gbrapv=$_POST['uttd'];
            //if ($ttdkosong==$gbrapv) { echo "ttdkosong"; exit; }
            
            if ($pses_grpuser=="3" OR $pses_grpuser=="67" OR $pses_grpuser=="23" OR $pses_grpuser=="28" OR $pses_grpuser=="61" OR $pses_grpuser=="40") {
                mysqli_query($cnmy, "update $dbname.t_suratdana_br set apv1='$karyawanapv', tgl_apv1=NOW(), gbr_apv1='$gbrapv' WHERE idinput in $noidbr AND IFNULL(tgl_apv2,'')=''");
            }elseif ($pses_grpuser=="25") {//anne || AND $pses_grpuser=="26"
                mysqli_query($cnmy, "update $dbname.t_suratdana_br set apv1='$karyawanapv', tgl_apv1=NOW(), gbr_apv1='$gbrapv' WHERE karyawanid='$pses_idcard' AND idinput in $noidbr AND IFNULL(tgl_apv2,'')=''");
                mysqli_query($cnmy, "update $dbname.t_suratdana_br set apv2='$karyawanapv', tgl_apv2=NOW(), gbr_apv2='$gbrapv' WHERE idinput in $noidbr AND IFNULL(tgl_apv1,'')<>'' AND IFNULL(tgl_dir,'')=''");
            }elseif ($pses_grpuser=="26") {//saiful | BARU
                
                mysqli_query($cnmy, "update $dbname.t_suratdana_br set apv1='$karyawanapv', tgl_apv1=NOW(), gbr_apv1='$gbrapv' WHERE karyawanid='$pses_idcard' AND idinput in $noidbr AND "
                        . " IFNULL(tgl_apv2,'')='' AND CONCAT(kodeid, subkode) IN ('103', '221', '236')");
                
                mysqli_query($cnmy, "update $dbname.t_suratdana_br set apv2='$karyawanapv', tgl_apv2=NOW(), gbr_apv2='$gbrapv' WHERE "
                        . " idinput in $noidbr AND IFNULL(tgl_apv1,'')<>'' AND IFNULL(tgl_dir,'')='' AND "
                        . " CONCAT(kodeid, subkode) NOT IN ('103', '221', '236')");
            }elseif ($pses_grpuser=="38") {//pa asykur
                
                mysqli_query($cnmy, "update $dbname.t_suratdana_br set apv3='$karyawanapv', tgl_apv3=NOW(), gbr_apv3='$gbrapv' WHERE idinput in $noidbr AND IFNULL(tgl_apv2,'')<>'' AND IFNULL(tgl_dir,'')='' AND CONCAT(kodeid,subkode) IN ('102') ");
            }
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $noteapv = "data berhasil diapprove...";
            
        }elseif ($act=="unapprove") {
            
            if ($pses_grpuser=="3" OR $pses_grpuser=="67" OR $pses_grpuser=="23" OR $pses_grpuser=="28" OR $pses_grpuser=="61" OR $pses_grpuser=="40") {
                mysqli_query($cnmy, "update $dbname.t_suratdana_br set apv1=NULL, tgl_apv1=NULL, gbr_apv1=NULL WHERE idinput in $noidbr AND IFNULL(tgl_apv2,'')=''");
            }elseif ($pses_grpuser=="25") {//anne || AND $pses_grpuser=="26"
                mysqli_query($cnmy, "update $dbname.t_suratdana_br set apv2=NULL, tgl_apv2=NULL, gbr_apv2=NULL WHERE idinput in $noidbr AND IFNULL(tgl_apv1,'')<>'' AND IFNULL(tgl_dir,'')=''");
                mysqli_query($cnmy, "update $dbname.t_suratdana_br set apv1=NULL, tgl_apv1=NULL, gbr_apv1=NULL WHERE karyawanid='$pses_idcard' AND idinput in $noidbr AND IFNULL(tgl_apv2,'')=''");
            }elseif ($pses_grpuser=="26") {//saiful | BARU
                mysqli_query($cnmy, "update $dbname.t_suratdana_br set apv2=NULL, tgl_apv2=NULL, gbr_apv2=NULL WHERE idinput in $noidbr AND IFNULL(tgl_apv1,'')<>'' AND IFNULL(tgl_dir,'')='' AND CONCAT(kodeid, subkode) NOT IN ('103', '221', '236')");
                mysqli_query($cnmy, "update $dbname.t_suratdana_br set apv1=NULL, tgl_apv1=NULL, gbr_apv1=NULL WHERE karyawanid='$pses_idcard' AND idinput in $noidbr AND IFNULL(tgl_apv2,'')='' AND CONCAT(kodeid, subkode) IN ('103', '221', '236')");
            }elseif ($pses_grpuser=="38") {//pa asykur
                mysqli_query($cnmy, "update $dbname.t_suratdana_br set apv3=NULL, tgl_apv3=NULL, gbr_apv3=NULL WHERE idinput in $noidbr AND IFNULL(tgl_dir,'')='' AND CONCAT(kodeid,subkode) IN ('102') ");
                
            }
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $noteapv = "data berhasil diunapprove...";
            
        }elseif ($act=="reject") {
            $kethapus = $_POST['ketrejpen'];
            if ($kethapus=="null") $kethapus="";
            if (!empty($kethapus)) $kethapus =", Ket Reject : ".$kethapus;
            if (!empty($kethapus)) $kethapus = str_replace("'", " ", $kethapus);
            
            mysqli_query($cnmy, "update $dbname.t_suratdana_br set stsnonaktif='Y', "
                    . " keterangan=CONCAT(IFNULL(keterangan,''),'$kethapus',', $_SESSION[NAMALENGKAP], ', NOW()) WHERE idinput in $noidbr");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $noteapv = "data berhasil direject...";
        }
    }
    
    echo $noteapv;
    
}
    
?>

