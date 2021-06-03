<?php
session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    
    include "../../../config/koneksimysqli.php";
    
    $module=$_GET['module'];
    $idmenu=$_GET['idmenu'];
    $act=$_GET['act'];
    
    $pnamalogin=$_SESSION['NAMALENGKAP'];
    $pidgroup=$_SESSION['GROUP'];
    
if ($module=="ttdspdbyfin") {
    
    $noidbr=$_POST['unobr'];
    if ($noidbr=="()") $noidbr = "";
    $stsspv=$_POST['ket'];
    $karyawanapv=$_POST['ukaryawan'];
    
    if (empty($karyawanapv)) $karyawanapv=$_SESSION['IDCARD'];
    
    
    $pwewenang1=false;
    $query = "select karyawanid, subkode, divisi from dbmaster.t_kode_spd_wenang1 WHERE karyawanid='$karyawanapv'";
    $tampil_w1= mysqli_query($cnmy, $query);
    $ketemu_w1= mysqli_num_rows($tampil_w1);
    if ((INT)$ketemu_w1>0) {
        $pwewenang1=true;
    }
    
    $pwewenang2=false;
    $query = "select karyawanid, subkode, divisi from dbmaster.t_kode_spd_wenang2 WHERE karyawanid='$karyawanapv'";
    $tampil_w2= mysqli_query($cnmy, $query);
    $ketemu_w2= mysqli_num_rows($tampil_w2);
    if ((INT)$ketemu_w2>0) {
        $pwewenang2=true;
    }
    
    $pwewenang3=false;
    $query = "select karyawanid, subkode, divisi from dbmaster.t_kode_spd_wenang3 WHERE karyawanid='$karyawanapv'";
    $tampil_w3= mysqli_query($cnmy, $query);
    $ketemu_w3= mysqli_num_rows($tampil_w3);
    if ((INT)$ketemu_w3>0) {
        $pwewenang3=true;
    }
    
    
    $noteapv = "tidak ada data yang diapprove";
    //$noteapv="$noidbr, $karyawanapv";
    if (!empty($noidbr) AND !empty($karyawanapv)) {
        
        if ($act=="approvebyfinance") {
            $gbrapv=$_POST['uttd'];
            
            $padaygapprove=false;
            if ($pwewenang1 == true AND $pwewenang2 == true) {
                mysqli_query($cnmy, "update dbmaster.t_suratdana_br set apv1='$karyawanapv', tgl_apv1=NOW(), gbr_apv1='$gbrapv' WHERE karyawanid='$karyawanapv' AND idinput in $noidbr AND ( IFNULL(tgl_apv2,'')='' OR IFNULL(tgl_apv2,'0000-00-00 00:00:00')='0000-00-00 00:00:00' )");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                mysqli_query($cnmy, "update dbmaster.t_suratdana_br set apv2='$karyawanapv', tgl_apv2=NOW(), gbr_apv2='$gbrapv' WHERE idinput in $noidbr AND IFNULL(tgl_apv1,'')<>'' AND IFNULL(tgl_apv1,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' AND ( IFNULL(tgl_dir,'')='' OR IFNULL(tgl_dir,'0000-00-00 00:00:00')='0000-00-00 00:00:00' ) AND karyawanid<>'$karyawanapv' AND apv1<>'$karyawanapv'");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                $padaygapprove=true;
            }else{
                if ($pwewenang1 == true) {
                    mysqli_query($cnmy, "update dbmaster.t_suratdana_br set apv1='$karyawanapv', tgl_apv1=NOW(), gbr_apv1='$gbrapv' WHERE karyawanid='$karyawanapv' AND idinput in $noidbr AND ( IFNULL(tgl_apv2,'')='' OR IFNULL(tgl_apv2,'0000-00-00 00:00:00')='0000-00-00 00:00:00' )");
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                    
                    $padaygapprove=true;
                }elseif ($pwewenang2 == true) {
                    mysqli_query($cnmy, "update dbmaster.t_suratdana_br set apv2='$karyawanapv', tgl_apv2=NOW(), gbr_apv2='$gbrapv' WHERE idinput in $noidbr AND IFNULL(tgl_apv1,'')<>'' AND IFNULL(tgl_apv1,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' AND ( IFNULL(tgl_dir,'')='' OR IFNULL(tgl_dir,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' )");
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                    
                    $padaygapprove=true;
                }elseif ($pwewenang3 == true) {
                    mysqli_query($cnmy, "update dbmaster.t_suratdana_br set apv3='$karyawanapv', tgl_apv3=NOW(), gbr_apv3='$gbrapv' WHERE idinput in $noidbr AND IFNULL(tgl_apv2,'')<>'' AND IFNULL(tgl_apv2,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' AND ( IFNULL(tgl_dir,'')='' OR IFNULL(tgl_dir,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' )");
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                    
                    $padaygapprove=true;
                }
            }
            
            if ($padaygapprove==true) $noteapv = "data berhasil diapprove...";
        }elseif ($act=="approvedirekturebyfin") {
            $gbrapv=$_POST['uttd'];
            $pdirkaryawanapv="0000002403";//COO
            mysqli_query($cnmy, "update dbmaster.t_suratdana_br set dir='$pdirkaryawanapv', tgl_dir=NOW(), gbr_dir='$gbrapv' WHERE idinput in $noidbr AND IFNULL(tgl_apv2,'')<>'' AND IFNULL(tgl_apv2,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' AND ( IFNULL(tgl_dir2,'')='' OR IFNULL(tgl_dir2,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' )");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $noteapv = "data berhasil diapprove...";
        }elseif ($act=="unapprove") {
            
            $padaygapprove=false;
            if ($pwewenang1 == true AND $pwewenang2 == true) {
                mysqli_query($cnmy, "update dbmaster.t_suratdana_br set tgl_apv2=NULL, gbr_apv2=NULL WHERE idinput in $noidbr AND IFNULL(tgl_apv1,'')<>'' AND IFNULL(tgl_apv1,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' AND ( IFNULL(tgl_dir,'')='' OR IFNULL(tgl_dir,'0000-00-00 00:00:00')='0000-00-00 00:00:00' ) AND karyawanid<>'$karyawanapv' AND apv1<>'$karyawanapv'");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                mysqli_query($cnmy, "update dbmaster.t_suratdana_br set tgl_apv1=NULL, gbr_apv1=NULL WHERE karyawanid='$karyawanapv' AND idinput in $noidbr AND ( IFNULL(tgl_apv2,'')='' OR IFNULL(tgl_apv2,'0000-00-00 00:00:00')='0000-00-00 00:00:00' )");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                $padaygapprove=true;
            }else{
                if ($pwewenang1 == true) {
                    mysqli_query($cnmy, "update dbmaster.t_suratdana_br set tgl_apv1=NULL, gbr_apv1=NULL WHERE karyawanid='$karyawanapv' AND idinput in $noidbr AND ( IFNULL(tgl_apv2,'')='' OR IFNULL(tgl_apv2,'0000-00-00 00:00:00')='0000-00-00 00:00:00' )");
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                    
                    $padaygapprove=true;
                }elseif ($pwewenang2 == true) {
                    mysqli_query($cnmy, "update dbmaster.t_suratdana_br set tgl_apv2=NULL, gbr_apv2=NULL WHERE idinput in $noidbr AND IFNULL(tgl_apv1,'')<>'' AND IFNULL(tgl_apv1,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' AND ( IFNULL(tgl_dir,'')='' OR IFNULL(tgl_dir,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' )");
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                    
                    $padaygapprove=true;
                }elseif ($pwewenang3 == true) {
                    mysqli_query($cnmy, "update dbmaster.t_suratdana_br set tgl_apv3=NULL, gbr_apv3=NULL WHERE idinput in $noidbr AND IFNULL(tgl_apv2,'')<>'' AND IFNULL(tgl_apv2,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' AND ( IFNULL(tgl_dir,'')='' OR IFNULL(tgl_dir,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' )");
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                    
                    $padaygapprove=true;
                }
            }
            
            if ($padaygapprove==true) $noteapv = "data berhasil diunapprove...";
        }elseif ($act=="unapprovedirbyfin") {
            
            mysqli_query($cnmy, "update dbmaster.t_suratdana_br set dir=null, tgl_dir=null, gbr_dir=null WHERE idinput in $noidbr AND IFNULL(tgl_apv2,'')<>'' AND IFNULL(tgl_apv2,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' AND ( IFNULL(tgl_dir2,'')='' OR IFNULL(tgl_dir2,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' )");
            
            $noteapv = "data berhasil diunapprove...";
        }elseif ($act=="reject") {
            
            $noteapv = "data berhasil direject...";
        }
        
    }
    
}
    mysqli_close($cnmy);
    echo $noteapv; exit;
?>