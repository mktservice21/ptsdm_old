<?php
    session_start();
    include "../../config/koneksimysqli.php";
    $kodeinput = " AND kode=4 ";
    $apvfinance = " AND ifnull(fin,'')='' ";
    
    $module=$_GET['module'];
    $idmenu=$_GET['idmenu'];
    $act=$_GET['act'];
    $dbname = "dbmaster";

if ($module=="appmktsewa") {
    $noidbr=$_POST['unobr'];
    if ($noidbr=="()") $noidbr = "";
    $stsspv=$_POST['ket'];
    $karyawanapv=$_POST['ukaryawan'];
    $lvlposisi=$_POST['ulevel'];
    $noteapv = "tidak ada data yang diapprove";
    
    if (!empty($noidbr) AND !empty($karyawanapv) AND !empty($lvlposisi)) {
        
        $tglatasannya = "";
        $tglatasannya_atas = "";
        $tglatasannya_bawah = "";
        $gbratasannya = "";
        if ($lvlposisi=="FF2") {
            $tglatasannya = "tgl_atasan1";
            $gbratasannya = "gbr_atasan1";
                    $tglatasannya_atas = "tgl_atasan2";
        }elseif ($lvlposisi=="FF3") {
            $tglatasannya = "tgl_atasan2";
            $gbratasannya = "gbr_atasan2";
                    $tglatasannya_bawah = "tgl_atasan1";
                    $tglatasannya_atas = "tgl_atasan3";
        }elseif ($lvlposisi=="FF4") {
            $tglatasannya = "tgl_atasan3";
            $gbratasannya = "gbr_atasan3";
                    $tglatasannya_bawah = "tgl_atasan2";
                    $tglatasannya_atas = "tgl_atasan4";
        }
        
        if (!empty($tglatasannya_bawah)) $tglatasannya_bawah = " AND ifnull($tglatasannya_bawah,'')<>'' "; //tanggal approve palingatas
        if (!empty($tglatasannya_atas)) $tglatasannya_atas = " AND ifnull($tglatasannya_atas,'')='' "; //tanggal approve palingatas
        
        
        $stsnonaktifnya = " AND stsnonaktif <> 'Y' ";
        
        if ($act=="simpan_ttdallam") {
            include "../../config/ttdkosong.php";
            $ttdkosong = ttdimage ();
            $gbrapv=$_POST['uttd'];
            
            if ($ttdkosong==$gbrapv) { echo "ttdkosong"; exit; }
            
            if (!empty($tglatasannya)) {
                $img = $gbrapv;//base64_encode(serialize($gbrapv));
                
                mysqli_query($cnmy, "update $dbname.t_sewa set $tglatasannya=NOW(), $gbratasannya='$img' WHERE idsewa in $noidbr $tglatasannya_bawah $tglatasannya_atas $stsnonaktifnya $apvfinance");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                $noteapv = "data berhasil diapprove...";
            }

            

        }elseif ($act=="unapprove") {
            if (!empty($tglatasannya)) {
                mysqli_query($cnmy, "update $dbname.t_sewa set $tglatasannya=null, $gbratasannya=null WHERE idsewa in $noidbr $tglatasannya_bawah $tglatasannya_atas $stsnonaktifnya $apvfinance");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                $noteapv = "data berhasil diunapprove...";
            }
        }elseif ($act=="reject") {
            $kethapus = $_POST['ketrejpen'];
            if ($kethapus=="null") $kethapus="";
            if (!empty($kethapus)) $kethapus =", Ket Reject : ".$kethapus;
            
            mysqli_query($cnmy, "update $dbname.t_sewa set stsnonaktif='Y', userid='$karyawanapv', "
                    . " keterangan=CONCAT(keterangan,'$kethapus',', $_SESSION[IDCARD], ', NOW()) WHERE idsewa in $noidbr $apvfinance");
            
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            $noteapv = "data berhasil direject...";
        }
        
    }
    
    echo $noteapv;

}

?>

