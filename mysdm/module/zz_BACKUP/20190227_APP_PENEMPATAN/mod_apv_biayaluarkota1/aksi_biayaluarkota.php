<?php
    session_start();
    include "../../config/koneksimysqli.php";
    
    $kodeinput = " AND kode=2 "; //membedakan biaya luar kota dan rutin
    $apvfinance = " AND ifnull(fin,'')='' ";
    
    $module=$_GET['module'];
    $idmenu=$_GET['idmenu'];
    $act=$_GET['act'];
    $cnmy=$cnmy;
    $dbname = "dbmaster";

if ($module=="appmktbiayaluar") {
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
            $idatasan4 = " atasan4='$_SESSION[IDCARD]', ";
            if ($act=="unapprove") $idatasan4 = " atasan4=null, ";
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
                
                mysqli_query($cnmy, "update $dbname.t_brrutin0 set $idatasan4 $tglatasannya=NOW(), $gbratasannya='$img' WHERE idrutin in $noidbr $tglatasannya_bawah $tglatasannya_atas $stsnonaktifnya $apvfinance $kodeinput");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                $flter = "SELECT DISTINCT IFNULL(idrutin,'') FROM $dbname.t_brrutin0 WHERE idrutin in $noidbr $tglatasannya_bawah $tglatasannya_atas $stsnonaktifnya $apvfinance $kodeinput";
                $query = "UPDATE dbimages.t_brrutin0_ttd set $gbratasannya='$img' WHERE idrutin IN ($flter) AND idrutin IN $noidbr";
                mysqli_query($cnmy, $query);
                //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                if ($lvlposisi=="FF2") {
                    mysqli_query($cnmy, "update $dbname.t_brrutin0 set tgl_atasan2=NOW() WHERE idrutin in $noidbr AND "
                            . " ifnull(tgl_atasan3,'0000-00-00')='0000-00-00' AND "
                            . " atasan2 in (select distinct ifnull(karyawanid,'') from dbmaster.t_karyawan_apv)");
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                }
                
                $noteapv = "data berhasil diapprove...";
            }

            

        }elseif ($act=="unapprove") {
            if (!empty($tglatasannya)) {
                mysqli_query($cnmy, "update $dbname.t_brrutin0 set $idatasan4 $tglatasannya=null, $gbratasannya=null WHERE idrutin in $noidbr $tglatasannya_bawah $tglatasannya_atas $stsnonaktifnya $apvfinance $kodeinput");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                $flter = "SELECT DISTINCT IFNULL(idrutin,'') FROM $dbname.t_brrutin0 WHERE idrutin in $noidbr $tglatasannya_bawah $tglatasannya_atas $stsnonaktifnya $apvfinance $kodeinput";
                $query = "UPDATE dbimages.t_brrutin0_ttd set $gbratasannya=null WHERE idrutin IN ($flter) AND idrutin IN $noidbr";
                mysqli_query($cnmy, $query);
                //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                $noteapv = "data berhasil diunapprove...";
            }
        }elseif ($act=="reject") {
            $kethapus = $_POST['ketrejpen'];
            if ($kethapus=="null") $kethapus="";
            if (!empty($kethapus)) $kethapus =", Ket Reject : ".$kethapus;
            if (!empty($kethapus)) $kethapus = str_replace("'", " ", $kethapus);
            
            mysqli_query($cnmy, "update $dbname.t_brrutin0 set stsnonaktif='Y', userid='$karyawanapv', "
                    . " keterangan=CONCAT(keterangan,'$kethapus',', $_SESSION[NAMALENGKAP], ', NOW()) WHERE idrutin in $noidbr $apvfinance $kodeinput");
            
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            $noteapv = "data berhasil direject...";
        }
        
    }
    
    echo $noteapv;

}

?>

