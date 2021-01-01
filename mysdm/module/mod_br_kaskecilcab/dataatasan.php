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

$query ="SELECT a.karyawanid, b.nama nama_karyawan, a.spv, c.nama nama_spv, a.dm, d.nama nama_dm, a.sm, e.nama nama_sm, a.gsm, f.nama nama_gsm, a.jabatanId as jabatanid  
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

$pidjbtpl=$nrs['jabatanid'];
    

    $pcabangid=$_POST['ucab'];
    $query= "select DISTINCT a.karyawanid as karyawanid, b.nama as nama from MKT.idm0 a JOIN hrd.karyawan b on a.karyawanid=b.karyawanid "
            . " WHERE a.icabangid='$pcabangid' AND IFNULL(a.karyawanid,'')<>'' "
            . " AND (IFNULL(b.tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(b.tglkeluar,'')='')";
    $tampil= mysqli_query($cnmy, $query);
    $rowd= mysqli_fetch_array($tampil);
    $pnnkrydm=$rowd['karyawanid'];
    $pnnmkrydm=$rowd['nama'];
    if (!empty($pnnkrydm)) {
        $pkdspv=""; $pnamaspv="";
        $pkddm=$pnnkrydm;
        $pnamadm=$pnnmkrydm;
    }
    
    $query= "select DISTINCT a.karyawanid as karyawanid, b.nama as nama from MKT.ism0 a JOIN hrd.karyawan b on a.karyawanid=b.karyawanid "
            . " WHERE a.icabangid='$pcabangid' AND IFNULL(a.karyawanid,'')<>'' "
            . " AND (IFNULL(b.tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(b.tglkeluar,'')='')";
    $tampil= mysqli_query($cnmy, $query);
    $rowd2= mysqli_fetch_array($tampil);
    $pnnkrydm=$rowd2['karyawanid'];
    $pnnmkrydm=$rowd2['nama'];
    if (!empty($pnnkrydm)) {
        $pkdsm=$pnnkrydm;
        $pnamasm=$pnnmkrydm;
        $pkdgsm="";
        $pnamagsm="";
    }
    
    $query = "select a.gsm, b.nama as nama_gsm FROM dbmaster.t_karyawan_posisi a JOIN hrd.karyawan b on a.gsm=b.karyawanid WHERE a.karyawanid='$pkdsm'";
    $ptampil2= mysqli_query($cnmy, $query);
    $nrs2= mysqli_fetch_array($ptampil2);

    $pkdgsm=$nrs2['gsm'];
    $pnamagsm=$nrs2['nama_gsm'];
    
    if ($pcabangid=="0000000003" OR $pcabangid=="0000000005" OR $pcabangid=="0000000081") {
        $pkdspv="";
        $pnamaspv="";
        $pkddm="";
        $pnamadm="";
    }
    
    if ($pcabangid=="00000000114") {
        $pkdspv="";
        $pnamaspv="";
        $pkddm="";
        $pnamadm="";
        $pkdsm="";
        $pnamasm="";
    }
	
	
    if ($pidjbtpl=="08" || (DOUBLE)$pidjbtpl==8) {
        $pkdspv="";
        $pnamaspv="";
        $pkddm="";
        $pnamadm="";
    }
    
?>

<div class='form-group'>
    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SPV / AM <span class='required'></span></label>
    <div class='col-xs-3'>
        <input type='hidden' id='e_kdspv' name='e_kdspv' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkdspv; ?>'>
        <input type='text' id='e_namaspv' name='e_namaspv' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamaspv; ?>'>
    </div>
</div>

<div class='form-group'>
    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>DM <span class='required'></span></label>
    <div class='col-xs-3'>
        <input type='hidden' id='e_kddm' name='e_kddm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkddm; ?>'>
        <input type='text' id='e_namadm' name='e_namadm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamadm; ?>'>
    </div>
</div>

<div class='form-group'>
    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SM <span class='required'></span></label>
    <div class='col-xs-3'>
        <input type='hidden' id='e_kdsm' name='e_kdsm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkdsm; ?>'>
        <input type='text' id='e_namasm' name='e_namasm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamasm; ?>'>
    </div>
</div>

<div class='form-group'>
    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>GSM <span class='required'></span></label>
    <div class='col-xs-3'>
        <input type='hidden' id='e_kdgsm' name='e_kdgsm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkdgsm; ?>'>
        <input type='text' id='e_namagsm' name='e_namagsm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamagsm; ?>'>
    </div>
</div>