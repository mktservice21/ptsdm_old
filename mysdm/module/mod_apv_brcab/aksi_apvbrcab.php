<?php
    session_start();
    include "../../config/koneksimysqli.php";
    $pidcardapv = $_SESSION['IDCARD'];
    
    if (empty($pidcardapv)) {
        echo "ANDA HARUS LOGIN ULANG....!!!";
        mysqli_close($cnmy);
        exit;
    }
    
    
    
    $apvfinance = " AND ifnull(brid,'')='' AND ifnull(tglbooking,'')<>'' AND ifnull(tglbooking,'0000-00-00')<>'0000-00-00' ";
    
    $module=$_GET['module'];
    $idmenu=$_GET['idmenu'];
    $act=$_GET['act'];
    $cnmy=$cnmy;
    $dbname = "dbmaster";

if ($module=="apvbrdssdcccab") {
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
        $idatasan4 = "";
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
        }elseif ($lvlposisi=="FF5" OR $lvlposisi=="FF7") {
            $tglatasannya = "tgl_atasan4";
            $gbratasannya = "gbr_atasan4";
            $idatasan4 = " atasan4='$karyawanapv', ";
            if ($act=="unapprove") $idatasan4 = " atasan4=atasan4, ";
        }
        
        if (!empty($tglatasannya_bawah)) $tglatasannya_bawah = " AND ifnull($tglatasannya_bawah,'')<>'' "; //tanggal approve palingatas
        if (!empty($tglatasannya_atas)) $tglatasannya_atas = " AND ifnull($tglatasannya_atas,'')='' "; //tanggal approve palingatas
        
        
        $stsnonaktifnya = " AND stsnonaktif <> 'Y' ";
        
        if ($act=="simpan_ttdallam") {
            include "../../config/ttdkosong.php";
            $ttdkosong = ttdimage ();
            $gbrapv=$_POST['uttd'];
            
            $pav_ceked="";
            $pav_ceked=" AND DATE_FORMAT(tglex,'%Y%m%d%H%i') >= DATE_FORMAT(NOW(),'%Y%m%d%H%i') ";
            
            if ($ttdkosong==$gbrapv) { echo "ttdkosong"; exit; }
            
            if (!empty($tglatasannya)) {
                $img = $gbrapv;//base64_encode(serialize($gbrapv));
                
                mysqli_query($cnmy, "update $dbname.t_br_cab set $idatasan4 $tglatasannya=NOW(), $gbratasannya='$img' WHERE bridinputcab in $noidbr $tglatasannya_bawah $tglatasannya_atas $stsnonaktifnya $apvfinance $pav_ceked");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                $flter = "SELECT DISTINCT IFNULL(bridinputcab,'') FROM $dbname.t_br_cab WHERE bridinputcab in $noidbr $tglatasannya_bawah $tglatasannya_atas $stsnonaktifnya $apvfinance $pav_ceked";
                $query = "UPDATE dbimages.t_br_cab_ttd set $gbratasannya='$img' WHERE bridinputcab IN ($flter) AND bridinputcab IN $noidbr";
                mysqli_query($cnmy, $query);
                //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                if ($lvlposisi=="FF2") {
                    
                    mysqli_query($cnmy, "update $dbname.t_br_cab set tgl_atasan2=NOW() WHERE bridinputcab in $noidbr AND "
                            . " IFNULL(tgl_atasan3,'0000-00-00')='0000-00-00' AND "
                            . " IFNULL(atasan2,'')='' $pav_ceked");
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                    
                    mysqli_query($cnmy, "update $dbname.t_br_cab set tgl_atasan2=NOW() WHERE bridinputcab in $noidbr AND "
                            . " ifnull(tgl_atasan3,'0000-00-00')='0000-00-00' AND "
                            . " atasan2 in (select distinct ifnull(karyawanid,'') from dbmaster.t_karyawan_apv) $pav_ceked");
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                    
                }
                
                $noteapv = "data berhasil diapprove...";
                
            }

            

        }elseif ($act=="unapprove") {
            if (!empty($tglatasannya)) {
                mysqli_query($cnmy, "update $dbname.t_br_cab set $idatasan4 $tglatasannya=null, $gbratasannya=null WHERE bridinputcab in $noidbr $tglatasannya_bawah $tglatasannya_atas $stsnonaktifnya $apvfinance");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                $flter = "SELECT DISTINCT IFNULL(bridinputcab,'') FROM $dbname.t_br_cab WHERE bridinputcab in $noidbr $apvfinance";
                $query = "UPDATE dbimages.t_br_cab_ttd set $gbratasannya=null WHERE bridinputcab IN ($flter) AND bridinputcab IN $noidbr";
                mysqli_query($cnmy, $query);
                //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                $noteapv = "data berhasil diunapprove...";
            }
        }elseif ($act=="reject") {
            $kethapus = $_POST['ketrejpen'];
            if ($kethapus=="null") $kethapus="";
            
            $pnmreject=$_SESSION['NAMALENGKAP'];

            $hari_ini = date("d F Y h:i:s");
            $kethapus="User : ".$pnmreject."  ".$hari_ini.", ".$kethapus;
    
            mysqli_query($cnmy, "update $dbname.t_br_cab set stsnonaktif='Y', "
                    . " alasan_batal='$kethapus' WHERE bridinputcab in $noidbr $apvfinance");
            
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $noteapv = "data berhasil direject...";
        }
        
    }
    
    mysqli_close($cnmy);
    echo $noteapv;

}

?>

