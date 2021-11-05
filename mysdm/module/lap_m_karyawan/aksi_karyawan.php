<?php

    session_start();
    include "../../config/koneksimysqli_it.php";
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_combo.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    
    $dbname = "dbmaster";
    

    
if ($module=='datakaryawan' AND $act=='update')
{
    
    $pkaryawan = $_POST['e_id'];
    $pnama = $_POST['e_nama'];
    $pdivisi = $_POST['cb_divisi'];
    $pregion = $_POST['e_region'];
    $pjabatan = $_POST['e_jabatan'];
    $patasan = $_POST['e_atasan'];
    $pidcab = $_POST['e_idcab'];
    $pspv = $_POST['e_spv'];
    $pdm = $_POST['e_dm'];
    $psm = $_POST['e_sm'];
    $pgsm = $_POST['e_gsm'];
    $pbank = $_POST['e_bank'];
    $pnorek = $_POST['e_norek'];
    
    $prutinchc= "N";
    if (isset($_POST['chk_rutinchc'])) $prutinchc = $_POST['chk_rutinchc'];
    
    $pdivisi1 = $_POST['cb_divisi1'];
    $pdivisi2 = $_POST['cb_divisi2'];
    $pdivisi3 = $_POST['cb_divisi3'];
    if (empty($pdivisi1)) $pdivisi1=$pdivisi;
    
    $phanyaadmin = "";
    if (isset($_POST['chk_admin'])) $phanyaadmin = $_POST['chk_admin'];
    $paktif="Y";
    if (isset($_POST['chk_nonaktif'])) $paktif = "N";
    
    $pidcabang = ""; $pareaid = "";
    if (!empty($_POST['e_idarea'])) {
        $areacabaang = explode(",",$_POST['e_idarea']);
        $pidcabang = trim($areacabaang[0]);
        $pareaid = trim($areacabaang[1]);
        if (!empty($pareaid) AND empty($pidcabang)) {
            $pidcabang = getfieldcnit("select icabangid as lcfields from dbmaster.v_penempatan_all where areaId='$pareaid' AND karyawanId='$pkaryawan'");
        }
    }
    
    if (!empty($pkaryawan)) {
        /*
        mysqli_query($cnmy, "DELETE FROM $dbname.t_karyawan_posisi WHERE karyawanId='$pkaryawan'");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        $query = "insert into $dbname.t_karyawan_posisi (karyawanId, jabatanId, divisiId, iCabangId, areaId, atasanId, region, IDCAB, aktif, dm, sm, b_bank, b_norek, spv, gsm, divisi1, divisi2, divisi3, rutin_chc)values"
                . "('$pkaryawan', '$pjabatan', '$pdivisi', '$pidcabang', '$pareaid', '$patasan', '$pregion', '$pidcab', '$paktif', '$pdm', '$psm', '$pbank', '$pnorek', '$pspv', '$pgsm', '$pdivisi1', '$pdivisi2', '$pdivisi3', '$prutinchc')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        */
        mysqli_query($cnmy, "DELETE FROM $dbname.t_karyawanadmin WHERE karyawanId='$pkaryawan'");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        if (!empty($phanyaadmin)) {
            mysqli_query($cnmy, "INSERT INTO $dbname.t_karyawanadmin (karyawanId, nama)values('$pkaryawan', '$pnama')");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }
        
        
        $query = "select * from $dbname.t_karyawan_posisi WHERE karyawanId='$pkaryawan'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ((INT)$ketemu<=0) {
            $query = "insert into $dbname.t_karyawan_posisi (karyawanId, jabatanId, divisiId, iCabangId, areaId, atasanId, region, IDCAB, aktif, dm, sm, b_bank, b_norek, spv, gsm, divisi1, divisi2, divisi3, rutin_chc)values"
                    . "('$pkaryawan', '$pjabatan', '$pdivisi', '$pidcabang', '$pareaid', '$patasan', '$pregion', '$pidcab', '$paktif', '$pdm', '$psm', '$pbank', '$pnorek', '$pspv', '$pgsm', '$pdivisi1', '$pdivisi2', '$pdivisi3', '$prutinchc')";
        }else{
            $query = "UPDATE $dbname.t_karyawan_posisi SET jabatanId='$pjabatan', "
                    . " iCabangId='$pidcabang', areaId='$pareaid', "
                    . " atasanId='$patasan', "
                    . " aktif='$paktif', dm='$pdm', sm='$psm', b_bank='$pbank', b_norek='$pnorek', spv='$pspv', gsm='$pgsm', "
                    . " divisi1='$pdivisi1', divisi2='$pdivisi2' WHERE karyawanId='$pkaryawan' LIMIT 1";
        }
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        
        if (empty($patasan)) $patasan=$pspv;
        if (empty($patasan)) $patasan=$pdm;
        if (empty($patasan)) $patasan=$psm;
        if (empty($patasan)) $patasan=$pgsm;
        
        mysqli_query($cnmy, "UPDATE hrd.karyawan set AKTIF='$paktif', atasanId='$patasan', jabatanId='$pjabatan', iCabangId='$pidcabang', areaId='$pareaid', divisiId='$pdivisi' WHERE karyawanId='$pkaryawan' LIMIT 1");
        
        
        mysqli_query($cnit, "UPDATE hrd.karyawan set AKTIF='$paktif', atasanId='$patasan', jabatanId='$pjabatan', iCabangId='$pidcabang', areaId='$pareaid', divisiId='$pdivisi' WHERE karyawanId='$pkaryawan' LIMIT 1");
        
        //$datasavems=SaveDataMS("dbmaster", "t_karyawan_posisi");
        //$datasavems=SaveDataMS("dbmaster", "t_karyawanadmin");
        //$datasavems=SaveDataMS("hrd", "karyawan");
		
        mysqli_close($cnit);
        
        
        //mysqli_query($cnmy, "CALL dbmaster.hrd_proseskaryawan()");
        
        mysqli_close($cnmy);
		
		
        
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    }
}    
    
?>

