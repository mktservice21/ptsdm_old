<?php

session_start();

    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
$puserid="";
$pidcard="";
$pidgroup="";
if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
if (isset($_SESSION['IDCARD'])) $pidcard=$_SESSION['IDCARD'];
if (isset($_SESSION['GROUP'])) $pidgroup=$_SESSION['GROUP'];


$pmodule=$_GET['module'];
$pact=$_GET['act'];
$pidmenu=$_GET['idmenu'];
$erropesan="error";
$pketeksekusi="";

if (empty($puserid)) {
    
    echo "<script src=\"../../../vendors/jquery/dist/jquery.min.js\"></script>";
    //echo "<script>alert('Anda harus login ulang');</script>";
    echo "<script>alert('Anda harus login ulang'); window.location = '../../../../'</script>";
    exit;
}

if ($pmodule=="tolsresetpass" AND ($pact=="updatepass" OR $pact=="updatepassawal")) {
    include "../../../config/koneksimysqli.php";
    include "../../../config/fungsi_ubahget_id.php";
    
    
    $ppin1=$_POST['txt_pin'];
    $ppin2=$_POST['txt_pin2'];
    
    if ($ppin1<>$ppin2) {
        $erropesan="pin1 dan pin2 tidak sama";
        goto errorsimpan;
    }
    
    $pkryid=$_POST['txt_idkaryawan'];
    $pidnoget=encodeString($pkryid);
    
    $query = "select karyawanId as karyawanid, jabatanId as jabatanid, atasanId as atasanid, atasanId2 as atasanid2, "
            . " divisiId as divisi, iCabangId as icabangid, areaId as areaid from hrd.karyawan "
            . " WHERE karyawanId='$pkryid'";
    $tampil= mysqli_query($cnmy, $query);
    $row=mysqli_fetch_array($tampil);
    $pjabatanid=$row['jabatanid'];
    $patasanid=$row['atasanid'];
    $patasanid2=$row['atasanid2'];
    $pdivisiid=$row['divisi'];
    $pcabangid=$row['icabangid'];
    $pareaid=$row['areaid'];
    
    if (empty($patasanid)) $patasanid=$patasanid2;
    
    
    $query = "select karyawanId as karyawanid FROM dbmaster.t_karyawan_posisi WHERE karyawanId='$pkryid'";
    $tampil2= mysqli_query($cnmy, $query);
    $ketemu2=mysqli_num_rows($tampil2);

    if ((INT)$ketemu2<=0) {
        
        //echo "belum ada<br/>";
        $query = "INSERT INTO dbmaster.t_karyawan_posisi (karyawanId, jabatanId, divisiId, iCabangId, areaId, atasanId)VALUES"
                . " ('$pkryid', '$pjabatanid', '$patasanid', '$pcabangid', '$pareaid', '$patasanid')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) {  goto errorsimpan; }
        
    }
    
    $query = "UPDATE dbmaster.t_karyawan_posisi SET pin_pass='$ppin1', tgl_pass=NOW(), slogin='Y' WHERE karyawanId='$pkryid' LIMIT 1";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) {  goto errorsimpan; }
    
    //$_SESSION['SUDAHUPDATEPASS']="Y";//ditutup, biar setelah ubah password login ulang.
    
    //echo "pin new : $ppin1, jbt : $pjabatanid, ats : $patasanid, ats2 : $patasanid2, div : $pdivisiid, cab : $pcabangid, area : $pareaid<br/>"; exit;
    
    mysqli_close($cnmy);
    
    $pketeksekusi="berhasil";
    if ($pact=="updatepassawal") {
        //header('location:../../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&nmun='.$pidmenu.'&act=editdata&iderror=berhasil&keteks='.$pketeksekusi.'&id='.$pidnoget.'&sloginawal=awal');
        
        echo "<script src=\"../../../vendors/jquery/dist/jquery.min.js\"></script>";
        //echo "<script>alert('Anda harus login ulang');</script>";
        echo "<script>alert('Berhasil Ubah Password. Anda harus login ulang'); window.location = '../../../../'</script>";
        exit;
        
    }else{
        header('location:../../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&nmun='.$pidmenu.'&act=berhasil&iderror=berhasil&keteks='.$pketeksekusi);
    }
    exit;
}elseif ($pmodule=="karyawanpassworubah" AND $pact=="lokupdatekrypass") {
    include "../../../config/koneksimysqli.php";
    
    $ppin1=$_POST['txt_pin'];
    $ppin2=$_POST['txt_pin2'];
    
    if ($ppin1<>$ppin2) {
        $erropesan="pin1 dan pin2 tidak sama";
        goto errorsimpan;
    }
    
    $pkryid=$pidcard;
    if (!empty($pkryid)) {
        
        
        $query = "select karyawanId as karyawanid, jabatanId as jabatanid, atasanId as atasanid, atasanId2 as atasanid2, "
                . " divisiId as divisi, iCabangId as icabangid, areaId as areaid from hrd.karyawan "
                . " WHERE karyawanId='$pkryid'";
        $tampil= mysqli_query($cnmy, $query);
        $row=mysqli_fetch_array($tampil);
        $pjabatanid=$row['jabatanid'];
        $patasanid=$row['atasanid'];
        $patasanid2=$row['atasanid2'];
        $pdivisiid=$row['divisi'];
        $pcabangid=$row['icabangid'];
        $pareaid=$row['areaid'];

        if (empty($patasanid)) $patasanid=$patasanid2;


        $query = "select karyawanId as karyawanid FROM dbmaster.t_karyawan_posisi WHERE karyawanId='$pkryid'";
        $tampil2= mysqli_query($cnmy, $query);
        $ketemu2=mysqli_num_rows($tampil2);
        
        
        if ((INT)$ketemu2<=0) {

            //echo "belum ada<br/>";
            $query = "INSERT INTO dbmaster.t_karyawan_posisi (karyawanId, jabatanId, divisiId, iCabangId, areaId, atasanId)VALUES"
                    . " ('$pkryid', '$pjabatanid', '$patasanid', '$pcabangid', '$pareaid', '$patasanid')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) {  goto errorsimpan; }

        }

        $query = "UPDATE dbmaster.t_karyawan_posisi SET pin_pass='$ppin1', tgl_pass=NOW(), slogin='Y' WHERE karyawanId='$pkryid' LIMIT 1";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) {  goto errorsimpan; }

        //echo "pin new : $ppin1, jbt : $pjabatanid, ats : $patasanid, ats2 : $patasanid2, div : $pdivisiid, cab : $pcabangid, area : $pareaid<br/>"; exit;

        mysqli_close($cnmy);
        
        $pketeksekusi="berhasil";
        header('location:../../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&nmun='.$pidmenu.'&act=berhasil&iderror=berhasil&keteks='.$pketeksekusi);
        
        
    }
    
    exit;
}


errorsimpan:
    $pketeksekusi=$erropesan;
    if (empty($pketeksekusi)) $pketeksekusi="error";
    
    //echo $pketeksekusi; exit;
    
    mysqli_close($cnmy);
    if ($pact=="updatepassawal") {
        header('location:../../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&nmun='.$pidmenu.'&act=editdata&iderror=error&keteks='.$pketeksekusi.'&id='.$pidnoget.'&sloginawal=awal');
    }else{
        header('location:../../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&nmun='.$pidmenu.'&act=error&iderror=error&keteks='.$pketeksekusi);
    }
    exit;
            
            
?>




