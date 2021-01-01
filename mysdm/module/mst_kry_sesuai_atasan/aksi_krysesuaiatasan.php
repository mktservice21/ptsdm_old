<?php
session_start();

$puserid=$_SESSION['USERID'];
if (empty($puserid)) {
    echo "ANDA HARUS LOGIN ULANG...";
    exit;
}

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];
    
if ($module=='mstsesuaidatakry' AND $act=='update')
{
    
    include "../../config/koneksimysqli.php";

    $pkaryawanid=$_POST['e_id'];
    $pidspv=$_POST['e_spv'];
    $piddm=$_POST['e_dm'];
    $pidsm=$_POST['e_sm'];
    $pidgsm=$_POST['e_gsm'];
    $piddivisi1=$_POST['cb_divisi1'];
    $piddivisi2=$_POST['cb_divisi2'];
    $piddivisi3=$_POST['cb_divisi3'];


    $patasanidpilih="";
    if (!empty($pidgsm)) $patasanidpilih=$pidgsm;
    if (!empty($pidsm)) $patasanidpilih=$pidsm;
    if (!empty($piddm)) $patasanidpilih=$piddm;
    if (!empty($pidspv)) $patasanidpilih=$pidspv;


    $patasanidpilih2="";
    if (!empty($pidgsm)) $patasanidpilih2=$pidgsm;
    if (!empty($pidsm)) $patasanidpilih2=$pidsm;
    if (!empty($piddm) AND !empty($pidspv)) $patasanidpilih2=$piddm;
    if (empty($pidspv) AND empty($piddm) AND !empty($pidsm)) $patasanidpilih2=$pidgsm;

    if ($patasanidpilih==$patasanidpilih2) $patasanidpilih2="";


    if (empty($piddivisi1) AND !empty($piddivisi2) AND empty($piddivisi3)) {
        $piddivisi1=$piddivisi2;
        $piddivisi2="";
    }

    if (empty($piddivisi1) AND !empty($piddivisi2) AND !empty($piddivisi3)) {
        $piddivisi1=$piddivisi2;
        $piddivisi2=$piddivisi3;
        $piddivisi3="";
    }

    if (empty($piddivisi1) AND empty($piddivisi2) AND !empty($piddivisi3)) {
        $piddivisi1=$piddivisi3;
        $piddivisi3="";
    }

    if ($piddivisi1==$piddivisi2) {
        $piddivisi2="";
    }

    if ($piddivisi1==$piddivisi3) {
        $piddivisi3="";
    }

    if ($piddivisi2==$piddivisi3) {
        $piddivisi3="";
    }


    $pdivisipilih="";
    if (!empty($piddivisi1) OR !empty($piddivisi2) OR !empty($piddivisi3)) {
        $pdivisipilih=$piddivisi1;
        if (!empty($piddivisi1) AND (!empty($piddivisi2) OR !empty($piddivisi3))) {
            $pdivisipilih="CAN";
        }
    }

    //echo "$pkaryawanid, atasan : $patasanidpilih - $patasanidpilih2 -->, $pidspv, $piddm, $pidsm, $pidgsm, $piddivisi1, $piddivisi2, $piddivisi3, $pdivisipilih<br/>";

	
    
    $query = "select * from dbmaster.t_karyawan_posisi where karyawanid='$pkaryawanid'";
    $tampilkan= mysqli_query($cnmy, $query);
    $ketemukan= mysqli_num_rows($tampilkan);
    if ($ketemukan==0) {
        $query ="select karyawanid, divisiid, icabangid, areaid, aktif, jabatanid from hrd.karyawan WHERE karyawanid='$pkaryawanid'";
        $ntampil= mysqli_query($cnmy, $query);
        $nrw= mysqli_fetch_array($ntampil);
        $pdiviiind=$nrw['divisiid'];
        $picabid=$nrw['icabangid'];
        $pareaid=$nrw['areaid'];
        $paktif=$nrw['aktif'];
		$pjbtid=$nrw['jabatanid'];
        //echo "$pdiviiind, $picabid, $pareaid, $paktif";
        
        $query = "INSERT INTO dbmaster.t_karyawan_posisi (karyawanid, divisiid, icabangid, areaid, aktif, jabatanid)values"
                . " ('$pkaryawanid', '$pdiviiind', '$picabid', '$pareaid', '$paktif', '$pjbtid')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
	
	
	
	
    $query_uptkaryawan = "UPDATE hrd.karyawan SET atasanId='$patasanidpilih', atasanId2='$patasanidpilih2', "
            . " divisiId='$piddivisi1', divisiId2='$piddivisi2' WHERE karyawanId='$pkaryawanid'";
    mysqli_query($cnmy, $query_uptkaryawan);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
    $query_uptposisi = "UPDATE dbmaster.t_karyawan_posisi SET atasanId='$patasanidpilih', spv='$pidspv', dm='$piddm', sm='$pidsm', "
            . " gsm='$pidgsm', divisi1='$piddivisi1', divisi2='$piddivisi2', "
            . " divisi3='$piddivisi3', divisiId='$pdivisipilih' WHERE karyawanId='$pkaryawanid'";
    mysqli_query($cnmy, $query_uptposisi);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    include "../../config/koneksimysqli_it.php";
	
    mysqli_query($cnit, $query_uptkaryawan);
    //$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    //mysqli_query($cnit, $query_uptposisi);
    //$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
    //echo "$query_uptkaryawan<br/>";
    //echo "$query_uptposisi<br/>";
    
    mysqli_close($cnit);
    mysqli_close($cnmy);
    
    //header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
	header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=editdata&id='.$pkaryawanid.'&nmun='.$idmenu);
}
?>

