<?php

session_start();

    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];

    $erropesan="error";
    $pketeksekusi="";
    $phapusinduk="";
    
    
// Hapus 
if ($pmodule=='hrdlokasirumah')
{
    
    if ($pact=='updatesmlokasi') {
        
        include "../../../config/koneksimysqli.php";
        include "../../../config/fungsi_sql.php";
        
        $puserid=$_POST['e_idinputuser'];
        $pcardid=$_POST['e_idcarduser'];

        if (empty($puserid)) {
            $puserid="";
            if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
            if (isset($_SESSION['IDCARD'])) $pcardid=$_SESSION['IDCARD'];

            if (empty($puserid)) {
                mysqli_close($cnmy);
                $pketeksekusi="ANDA HARUS LOGIN ULANG...";
                goto errorsimpan;
                exit;
            }
        }
        
        $pid=$_POST['e_id'];
        $pstsid=$_POST['e_idstatus'];
        $pradius=$_POST['e_radius'];
        if (empty($pradius)) $pradius=0;
        
        if (!empty($pid) AND !empty($pstsid)) {
            
            $query = "UPDATE hrd.sdm_lokasi SET sdm_radius='$pradius' WHERE id='$pid' AND id_status='$pstsid' LIMIT 1";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $pketeksekusi="error update ke data sdm lokasi"; mysqli_close($cnmy); goto errorsimpan; }
            
            $pketeksekusi="berhasil";
        }else{
            $pketeksekusi="error";
        } 
        
        mysqli_close($cnmy);
        
        
        
        header('location:../../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&nmun='.$pidmenu.'&act=complete&iderror='.$pketeksekusi.'&keteks='.$pketeksekusi);
        exit;
        
        
    }elseif($pact=="updatekrywfhlokasi") {
        
        
        include "../../../config/koneksimysqli.php";
        include "../../../config/fungsi_sql.php";
        
        $puserid=$_POST['e_idinputuser'];
        $pcardid=$_POST['e_idcarduser'];

        if (empty($puserid)) {
            $puserid="";
            if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
            if (isset($_SESSION['IDCARD'])) $pcardid=$_SESSION['IDCARD'];

            if (empty($puserid)) {
                mysqli_close($cnmy);
                $pketeksekusi="ANDA HARUS LOGIN ULANG...";
                goto errorsimpan;
                exit;
            }
        }
        
        $pid=$_POST['e_idkry'];
        $pstsid=$_POST['e_idstatus'];
        $paktifid=$_POST['e_aktif'];
        $pradius=$_POST['e_radius'];
        if (empty($pradius)) $pradius=0;
        
        
        if (!empty($pid) AND !empty($pstsid) AND !empty($paktifid)) {
            
            $query = "UPDATE hrd.karyawan_absen SET a_radius='$pradius', userid='$pcardid' WHERE karyawanid='$pid' AND id_status='$pstsid' AND IFNULL(aktif,'')='$paktifid' LIMIT 1";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $pketeksekusi="error update ke data lokasi wfh karyawan"; mysqli_close($cnmy); goto errorsimpan; }
            
            $pketeksekusi="berhasil";
        }else{
            $pketeksekusi="error";
        } 
        
        mysqli_close($cnmy);
        
        
        
        header('location:../../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&nmun='.$pidmenu.'&act=complete&iderror='.$pketeksekusi.'&keteks='.$pketeksekusi);
        exit;
        
        
    }elseif($pact=="updatekrysdmlokexp") {
        include "../../../config/koneksimysqli.php";
        include "../../../config/fungsi_sql.php";
        
        $puserid=$_POST['e_idinputuser'];
        $pcardid=$_POST['e_idcarduser'];

        if (empty($puserid)) {
            $puserid="";
            if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
            if (isset($_SESSION['IDCARD'])) $pcardid=$_SESSION['IDCARD'];

            if (empty($puserid)) {
                mysqli_close($cnmy);
                $pketeksekusi="ANDA HARUS LOGIN ULANG...";
                goto errorsimpan;
                exit;
            }
        }
        
        $pid=$_POST['e_idkry'];
        $pstsid=$_POST['e_idstatus'];
        $paktifid=$_POST['e_aktif'];
        $pradius=$_POST['e_radius'];
        if (empty($pradius)) $pradius=0;
        
        
        if (!empty($pid) AND !empty($pstsid)) {
            
            $query = "UPDATE hrd.sdm_lokasi_radius_ex SET sdm_radius='$pradius' WHERE karyawanid='$pid' AND id_status='$pstsid' LIMIT 1";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $pketeksekusi="error update ke data lokasi sdm exp karyawan"; mysqli_close($cnmy); goto errorsimpan; }
            
            $pketeksekusi="berhasil";
        }else{
            $pketeksekusi="error";
        } 
        
        mysqli_close($cnmy);
        
        
        
        header('location:../../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&nmun='.$pidmenu.'&act=complete&iderror='.$pketeksekusi.'&keteks='.$pketeksekusi);
        exit;
        
    }elseif($pact=="inputkrysdmlokasiexp") {
        
        include "../../../config/koneksimysqli.php";
        include "../../../config/fungsi_sql.php";
        
        $puserid=$_POST['e_idinputuser'];
        $pcardid=$_POST['e_idcarduser'];

        if (empty($puserid)) {
            $puserid="";
            if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
            if (isset($_SESSION['IDCARD'])) $pcardid=$_SESSION['IDCARD'];

            if (empty($puserid)) {
                mysqli_close($cnmy);
                $pketeksekusi="ANDA HARUS LOGIN ULANG...";
                goto errorsimpan;
                exit;
            }
        }
        
        $pid=$_POST['e_idkry'];
        $pstsid=$_POST['e_idstatus'];
        $pradius=$_POST['e_radius'];
        if (empty($pradius)) $pradius=0;
        
        
        if (!empty($pid) AND !empty($pstsid)) {
            
            $query = "INSERT INTO hrd.sdm_lokasi_radius_ex (karyawanid, sdm_radius, id_status)VALUES"
                    . "('$pid', '$pradius', '$pstsid')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $pketeksekusi="error insert ke data lokasi sdm exp karyawan"; mysqli_close($cnmy); goto errorsimpan; }
            
            $pketeksekusi="berhasil";
        }else{
            $pketeksekusi="error";
        } 
        
        mysqli_close($cnmy);
        
        
        
        header('location:../../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&nmun='.$pidmenu.'&act=complete&iderror='.$pketeksekusi.'&keteks='.$pketeksekusi);
        exit;
        
    }elseif($pact=="xxx") {
        
    }
    
    
}

errorsimpan:
    
    if (empty($pketeksekusi)) $pketeksekusi="error";
    //echo $pketeksekusi; exit;
    
    header('location:../../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&nmun='.$pidmenu.'&act=complete&iderror=error&keteks='.$pketeksekusi);
    exit;
?>