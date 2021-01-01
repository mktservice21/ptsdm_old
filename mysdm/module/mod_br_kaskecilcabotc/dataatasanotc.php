<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];

    $pidkar=$_POST['ukry'];
    include "../../config/koneksimysqli.php";

$pkdspv="";
$pnamaspv="";
$pkddm="";
$pnamadm="";
$pkdsm="";
$pnamasm="";
$pkdgsm="";
$pnamagsm="";

$query ="SELECT a.karyawanid, b.nama nama_karyawan, a.spv, c.nama nama_spv, a.dm, d.nama nama_dm, a.sm, e.nama nama_sm, a.gsm, f.nama nama_gsm 
    FROM dbmaster.t_karyawan_posisi a 
    LEFT JOIN hrd.karyawan b on a.karyawanId=b.karyawanId 
    LEFT JOIN hrd.karyawan c on a.spv=c.karyawanId 
    LEFT JOIN hrd.karyawan d on a.dm=d.karyawanId 
    LEFT JOIN hrd.karyawan e on a.sm=e.karyawanId 
    LEFT JOIN hrd.karyawan f on a.gsm=f.karyawanId WHERE a.karyawanid='$pidkar'";
$ptampil= mysqli_query($cnmy, $query);
$nrs= mysqli_fetch_array($ptampil);
$pkdspv=$nrs['spv'];
$pnamaspv=$nrs['nama_spv'];
$pkddm=$nrs['dm'];
$pnamadm=$nrs['nama_dm'];
$pkdsm=$nrs['sm'];
$pnamasm=$nrs['nama_sm'];
$pkdgsm=$nrs['gsm'];
$pnamagsm=$nrs['nama_gsm'];

?>

<div hidden class='form-group'>
    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SPV / AM <span class='required'></span></label>
    <div class='col-xs-3'>
        <input type='hidden' id='e_kdspv' name='e_kdspv' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkdspv; ?>'>
        <input type='text' id='e_namaspv' name='e_namaspv' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamaspv; ?>'>
    </div>
</div>

<div hidden class='form-group'>
    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>DM <span class='required'></span></label>
    <div class='col-xs-3'>
        <input type='hidden' id='e_kddm' name='e_kddm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkddm; ?>'>
        <input type='text' id='e_namadm' name='e_namadm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamadm; ?>'>
    </div>
</div>

<div hidden class='form-group'>
    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SM <span class='required'></span></label>
    <div class='col-xs-3'>
        <input type='hidden' id='e_kdsm' name='e_kdsm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkdsm; ?>'>
        <input type='text' id='e_namasm' name='e_namasm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamasm; ?>'>
    </div>
</div>

<div class='form-group'>
    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>HOS <span class='required'></span></label>
    <div class='col-xs-3'>
        <input type='hidden' id='e_kdgsm' name='e_kdgsm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkdgsm; ?>'>
        <input type='text' id='e_namagsm' name='e_namagsm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamagsm; ?>'>
    </div>
</div>