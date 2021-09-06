<?php

session_start();
    $puserid="";
    if (!isset($_SESSION['USERID'])) {
        //echo "ANDA HARUS LOGIN ULANG....";
        //exit;
    }else{
        $puserid=$_SESSION['USERID'];
    }


$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];


                            $phidupkanprosesinsertkaryawan=true;

function plus1($pVar_,$pDigit_)
{
    if ($pVar_ == str_repeat('9',$pDigit_)) {
        $myVar_ = str_repeat('0',$pDigit_);
    } else {
        $myVar_ = intval($pVar_) + 1;
        $myVar_ = str_repeat('0',$pDigit_) . strval($myVar_);
        $myVar_ = substr($myVar_,0-$pDigit_);
    }
    return $myVar_;
}



if ($module=='mstisidatakaryawan' AND ($act=="updateatasan" OR $act=="updateatasanotc"))
{
    include "../../config/koneksimysqli.php";
    
    $pkodenya=$_POST['e_id'];
    
    if (empty($pkodenya)) {
        echo "ID KOSONG...";
        exit;
    }
    
    
    $pspvid=$_POST['e_spv'];
    $pdmid=$_POST['e_dm'];
    $psmid=$_POST['e_sm'];
    $pgsmid=$_POST['e_gsm'];
    
    $patasanidpilih="";
    if (!empty($pgsmid)) $patasanidpilih=$pgsmid;
    if (!empty($psmid)) $patasanidpilih=$psmid;
    if (!empty($pdmid)) $patasanidpilih=$pdmid;
    if (!empty($pspvid)) $patasanidpilih=$pspvid;


    $patasanidpilih2="";
    if (!empty($pgsmid)) $patasanidpilih2=$pgsmid;
    if (!empty($psmid)) $patasanidpilih2=$psmid;
    if (!empty($pdmid) AND !empty($pspvid)) $patasanidpilih2=$pdmid;
    if (empty($pspvid) AND empty($pdmid) AND !empty($psmid)) $patasanidpilih2=$pgsmid;
    
    
    if (!empty($pkodenya)) {
        $query = "UPDATE dbmaster.t_karyawan_posisi SET atasanId='$patasanidpilih', spv='$pspvid',  "
                . " dm='$pdmid', sm='$psmid', gsm='$pgsmid' WHERE karyawanid='$pkodenya' LIMIT 1";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
        $query_updatehrd1 = "UPDATE hrd.karyawan SET atasanId='$patasanidpilih', atasanId2='$patasanidpilih2' WHERE karyawanid='$pkodenya' LIMIT 1";
        mysqli_query($cnmy, $query_updatehrd1);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
    }
    
    
    if ($act=="updateatasanotc") {
        
        
        $pbank = $_POST['e_bank'];
        $pnorek = $_POST['e_norek'];
        $paktif="Y";
        if (isset($_POST['chk_nonaktif'])) $paktif = "N";
        
        
        $query = "UPDATE dbmaster.t_karyawan_posisi SET b_bank='$pbank', b_norek='$pnorek', aktif='$paktif' WHERE karyawanid='$pkodenya' LIMIT 1";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
        
        $query_updatehrd2 = "UPDATE hrd.karyawan SET aktif='$paktif' WHERE karyawanid='$pkodenya' LIMIT 1";
        mysqli_query($cnmy, $query_updatehrd2);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
    }
    
    //echo "$pkodenya, $pspvid, $pdmid, $psmid, $pgsmid";
    mysqli_close($cnmy);
    
    
    if ($phidupkanprosesinsertkaryawan==true) {
        include "../../config/koneksimysqli_it.php";
        
        mysqli_query($cnit, $query_updatehrd1);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }
        
        if ($act=="updateatasanotc") {
            mysqli_query($cnit, $query_updatehrd2);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }
        }
        
        mysqli_close($cnit);
    }
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=completeinup&id='.$pkodenya.'&nmun='.$idmenu);
    exit;
}

if ($module=='mstisidatakaryawan' AND $act=="updateaktifnon")
{
    $pkodenya=$_POST['e_id'];
    $pnama=$_POST['e_nama'];
    
    if (empty($pkodenya)) {
        echo "ID KOSONG...";
        exit;
    }
    
    $ptglkeluar=$_POST['e_tglkeluar'];
    if (!empty($ptglkeluar)) $ptglkeluar = str_replace('/', '-', $ptglkeluar);
    else $ptglkeluar="0000-00-00";
    
    if (!empty($ptglkeluar) AND $ptglkeluar<>"0000-00-00") $ptglkeluar= date("Y-m-d", strtotime($ptglkeluar));
    if ($ptglkeluar=="1970-01-01") $ptglkeluar="0000-00-00";
        
        
    
    $paktif="Y";
    if (isset($_POST['chk_nonaktif'])) $paktif = "N";
    if (!empty($ptglkeluar) AND $ptglkeluar<>"0000-00-00") {
        $paktif="N";
    }

    
    //untuk admin bayangan
    $phanyaadmin = "";
    if (isset($_POST['chk_admin'])) $phanyaadmin = $_POST['chk_admin'];
    
    
    
    //echo "$pkodenya : $ptglkeluar, aktif: $paktif, $phanyaadmin"; exit;
    
    
    include "../../config/koneksimysqli.php";
    
    
    $query_updatehrd1 = "UPDATE hrd.karyawan SET tglkeluar='$ptglkeluar', aktif='$paktif' WHERE karyawanid='$pkodenya' LIMIT 1";
    mysqli_query($cnmy, $query_updatehrd1);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    
    
    $query = "UPDATE dbmaster.t_karyawan_posisi SET aktif='$paktif' WHERE karyawanid='$pkodenya' LIMIT 1";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
        
    mysqli_query($cnmy, "DELETE FROM dbmaster.t_karyawanadmin WHERE karyawanId='$pkodenya' LIMIT 1");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    if (!empty($phanyaadmin)) {
        mysqli_query($cnmy, "INSERT INTO dbmaster.t_karyawanadmin (karyawanId, nama)values('$pkodenya', '$pnama')");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
        
        
    mysqli_close($cnmy);
    
    
    if ($phidupkanprosesinsertkaryawan==true) {
        include "../../config/koneksimysqli_it.php";
        
        $query_updatehrd1 = "UPDATE hrd.karyawan SET tglkeluar='$ptglkeluar', aktif='$paktif' WHERE karyawanid='$pkodenya' LIMIT 1";
        mysqli_query($cnit, $query_updatehrd1);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }
    
        mysqli_close($cnit);
    }
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=completeinup&id='.$pkodenya.'&nmun='.$idmenu);
    exit;
}


if ($module=='mstisidatakaryawan' AND $act=="updatejabatandivisarea")
{
    $pkodenya=$_POST['e_id'];
    
    if (empty($pkodenya)) {
        echo "ID KOSONG...";
        exit;
    }
    
    
    $pjabatanid=$_POST['cb_jabatan'];
    $pdivisiid1=$_POST['cb_divisi'];
    $pdivisiid2=$_POST['cb_divisi2'];
    $pcabangid=$_POST['cb_cabang'];
    $pareaid=$_POST['cb_area'];
    
    //echo "$pjabatanid, $pdivisiid1, $pdivisiid2, $pcabangid, $pareaid"; exit;
    
    
    include "../../config/koneksimysqli.php";
    
        $query_updatehrd1 = "UPDATE hrd.karyawan SET jabatanid='$pjabatanid', divisiid='$pdivisiid1', divisiid2='$pdivisiid2', "
                . " icabangid='$pcabangid', areaid='$pareaid' WHERE karyawanid='$pkodenya' LIMIT 1";
        mysqli_query($cnmy, $query_updatehrd1);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
        
        
        $query = "UPDATE dbmaster.t_karyawan_posisi SET jabatanid='$pjabatanid', "
                . " divisiid='$pdivisiid1', icabangid='$pcabangid', areaid='$pareaid' WHERE karyawanid='$pkodenya' LIMIT 1";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
    
    mysqli_close($cnmy);
    
    
    if ($phidupkanprosesinsertkaryawan==true) {
        include "../../config/koneksimysqli_it.php";
        
        mysqli_query($cnit, $query_updatehrd1);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }
        
        mysqli_close($cnit);
    }
    
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=completeinup&id='.$pkodenya.'&nmun='.$idmenu);
    exit;
    
    
}


if ($module=='mstisidatakaryawan' AND ($act=="input" OR $act=="update"))
{
    
    $pkodenya=$_POST['e_id'];
    $ppin=$_POST['e_pin'];
    $pnamakry=$_POST['e_nmkaryawan'];    
    $ptlahir=$_POST['e_tlahir'];

    $ptgllahir=$_POST['e_tgllahir'];
    if (!empty($ptgllahir)) $ptgllahir = str_replace('/', '-', $ptgllahir);
    else $ptgllahir="0000-00-00";
    
    $palamat1=$_POST['e_alamat1'];
    $palamat2=$_POST['e_alamat2'];
    $palamatkota=$_POST['e_kotaalamat'];
    $ptelprmh=$_POST['e_tlprumah'];
    $ptelphp=$_POST['e_tlphp'];
    $pjkel=$_POST['cb_jekel'];
    $pagamaid=$_POST['cb_agama'];
    
    $ptglmasuk=$_POST['e_tglmasuk'];
    if (!empty($ptglmasuk)) $ptglmasuk = str_replace('/', '-', $ptglmasuk);
    else $ptglmasuk="0000-00-00";
    
    $ptglkeluar=$_POST['e_tglkeluar'];
    if (!empty($ptglkeluar)) $ptglkeluar = str_replace('/', '-', $ptglkeluar);
    else $ptglkeluar="0000-00-00";
    
    
    $ppendidikanid=$_POST['cb_pendidikan'];
    $pjabatanid=$_POST['cb_jabatan'];
    $pstskaryawan=$_POST['cb_stskry'];
    $pdivisiid1=$_POST['cb_divisi'];
    $pdivisiid2=$_POST['cb_divisi2'];
    $pcabangid=$_POST['cb_cabang'];
    $pareaid=$_POST['cb_area'];
    
    $pbnknama=$_POST['e_nmbank'];
    $pbnkcab=$_POST['e_cabbank'];
    $pbnkkota=$_POST['e_nmkotabnk'];
    $pbnkprov=$_POST['e_provinsibnk'];
    $pbnknmrek=$_POST['e_nmrekbank'];
    $pbnknorek=$_POST['e_norekbank'];
    
    $ppasnama=$_POST['e_pasnama'];
    $ppastempat=$_POST['e_pastempat'];
    $ppaspekerjaan=$_POST['e_paskerja'];
    
    $ppastgllahir=$_POST['e_pastgllahir'];
    if (!empty($ppastgllahir)) $ppastgllahir = str_replace('/', '-', $ppastgllahir);
    else $ppastgllahir="0000-00-00";
    
    $panaknama1=$_POST['e_anaknm1'];
    $panakjkel1=$_POST['cb_ankjekel1'];
    $panaktempat1=$_POST['e_anaktempat1'];
    $panaktgllhr1=$_POST['e_anaktgllahir1'];
    if (!empty($panaktgllhr1)) $panaktgllhr1 = str_replace('/', '-', $panaktgllhr1);
    else $panaktgllhr1="0000-00-00";
    
    $panaknama2=$_POST['e_anaknm2'];
    $panakjkel2=$_POST['cb_ankjekel2'];
    $panaktempat2=$_POST['e_anaktempat2'];
    $panaktgllhr2=$_POST['e_anaktgllahir2'];
    if (!empty($panaktgllhr2)) $panaktgllhr2 = str_replace('/', '-', $panaktgllhr2);
    else $panaktgllhr2="0000-00-00";
    
    $panaknama3=$_POST['e_anaknm3'];
    $panakjkel3=$_POST['cb_ankjekel3'];
    $panaktempat3=$_POST['e_anaktempat3'];
    $panaktgllhr3=$_POST['e_anaktgllahir3'];
    if (!empty($panaktgllhr3)) $panaktgllhr3 = str_replace('/', '-', $panaktgllhr3);
    else $panaktgllhr3="0000-00-00";
    
    $panaknama4=$_POST['e_anaknm4'];
    $panakjkel4=$_POST['cb_ankjekel4'];
    $panaktempat4=$_POST['e_anaktempat4'];
    $panaktgllhr4=$_POST['e_anaktgllahir4'];
    if (!empty($panaktgllhr4)) $panaktgllhr4 = str_replace('/', '-', $panaktgllhr4);
    else $panaktgllhr4="0000-00-00";
    
    $panaknama5=$_POST['e_anaknm5'];
    $panakjkel5=$_POST['cb_ankjekel5'];
    $panaktempat5=$_POST['e_anaktempat5'];
    $panaktgllhr5=$_POST['e_anaktgllahir5'];
    if (!empty($panaktgllhr5)) $panaktgllhr5 = str_replace('/', '-', $panaktgllhr5);
    else $panaktgllhr5="0000-00-00";
    
    
    $pspvid=$_POST['e_spv'];
    $pdmid=$_POST['e_dm'];
    $psmid=$_POST['e_sm'];
    $pgsmid=$_POST['e_gsm'];
    
    $prutinchc= "N";
    if (isset($_POST['chk_rutinchc'])) $prutinchc = $_POST['chk_rutinchc'];
    
    $patasanidpilih="";
    if (!empty($pgsmid)) $patasanidpilih=$pgsmid;
    if (!empty($psmid)) $patasanidpilih=$psmid;
    if (!empty($pdmid)) $patasanidpilih=$pdmid;
    if (!empty($pspvid)) $patasanidpilih=$pspvid;


    $patasanidpilih2="";
    if (!empty($pgsmid)) $patasanidpilih2=$pgsmid;
    if (!empty($psmid)) $patasanidpilih2=$psmid;
    if (!empty($pdmid) AND !empty($pspvid)) $patasanidpilih2=$pdmid;
    if (empty($pspvid) AND empty($pdmid) AND !empty($psmid)) $patasanidpilih2=$pgsmid;

    if ($patasanidpilih==$patasanidpilih2) $patasanidpilih2="";
    
    
    if (!empty($pnamakry)) $pnamakry = str_replace("'", " ", $pnamakry);
    if (!empty($ptlahir)) $ptlahir = str_replace("'", " ", $ptlahir);
    if (!empty($palamat1)) $palamat1 = str_replace("'", " ", $palamat1);
    if (!empty($palamat2)) $palamat2 = str_replace("'", " ", $palamat2);
    if (!empty($palamatkota)) $palamatkota = str_replace("'", " ", $palamatkota);
    if (!empty($ptelprmh)) $ptelprmh = str_replace("'", " ", $ptelprmh);
    if (!empty($ptelphp)) $ptelphp = str_replace("'", " ", $ptelphp);
    if (!empty($pbnknama)) $pbnknama = str_replace("'", " ", $pbnknama);
    if (!empty($ppastempat)) $ppastempat = str_replace("'", " ", $ppastempat);
    if (!empty($ppaspekerjaan)) $ppaspekerjaan = str_replace("'", " ", $ppaspekerjaan);
    if (!empty($panaknama1)) $panaknama1 = str_replace("'", " ", $panaknama1);
    if (!empty($panaktempat1)) $panaktempat1 = str_replace("'", " ", $panaktempat1);
    if (!empty($panaknama2)) $panaknama2 = str_replace("'", " ", $panaknama2);
    if (!empty($panaktempat2)) $panaktempat2 = str_replace("'", " ", $panaktempat2);
    if (!empty($panaknama3)) $panaknama3 = str_replace("'", " ", $panaknama3);
    if (!empty($panaktempat3)) $panaktempat3 = str_replace("'", " ", $panaktempat3);
    if (!empty($panaknama4)) $panaknama4 = str_replace("'", " ", $panaknama4);
    if (!empty($panaktempat4)) $panaktempat4 = str_replace("'", " ", $panaktempat4);
    if (!empty($panaknama5)) $panaknama5 = str_replace("'", " ", $panaknama5);
    if (!empty($panaktempat5)) $panaktempat5 = str_replace("'", " ", $panaktempat5);
    
    if (!empty($ptgllahir) AND $ptgllahir<>"0000-00-00") $ptgllahir= date("Y-m-d", strtotime($ptgllahir));
    if (!empty($ptglmasuk) AND $ptglmasuk<>"0000-00-00") $ptglmasuk= date("Y-m-d", strtotime($ptglmasuk));
    if (!empty($ptglkeluar) AND $ptglkeluar<>"0000-00-00") $ptglkeluar= date("Y-m-d", strtotime($ptglkeluar));
    if (!empty($ppastgllahir) AND $ppastgllahir<>"0000-00-00") $ppastgllahir= date("Y-m-d", strtotime($ppastgllahir));
    if (!empty($panaktgllhr1) AND $panaktgllhr1<>"0000-00-00") $panaktgllhr1= date("Y-m-d", strtotime($panaktgllhr1));
    if (!empty($panaktgllhr2) AND $panaktgllhr2<>"0000-00-00") $panaktgllhr2= date("Y-m-d", strtotime($panaktgllhr2));
    if (!empty($panaktgllhr3) AND $panaktgllhr3<>"0000-00-00") $panaktgllhr3= date("Y-m-d", strtotime($panaktgllhr3));
    if (!empty($panaktgllhr4) AND $panaktgllhr4<>"0000-00-00") $panaktgllhr4= date("Y-m-d", strtotime($panaktgllhr4));
    if (!empty($panaktgllhr5) AND $panaktgllhr5<>"0000-00-00") $panaktgllhr5= date("Y-m-d", strtotime($panaktgllhr5));
    
    if ($ptgllahir=="1970-01-01") $ptgllahir="0000-00-00";
    if ($ptglmasuk=="1970-01-01") $ptglmasuk="0000-00-00";
    if ($ptglkeluar=="1970-01-01") $ptglkeluar="0000-00-00";
    if ($ppastgllahir=="1970-01-01") $ppastgllahir="0000-00-00";
    if ($panaktgllhr1=="1970-01-01") $panaktgllhr1="0000-00-00";
    if ($panaktgllhr2=="1970-01-01") $panaktgllhr2="0000-00-00";
    if ($panaktgllhr3=="1970-01-01") $panaktgllhr3="0000-00-00";
    if ($panaktgllhr4=="1970-01-01") $panaktgllhr4="0000-00-00";
    if ($panaktgllhr5=="1970-01-01") $panaktgllhr5="0000-00-00";
    
    
    
    
    
    
    include "../../config/koneksimysqli.php";
    include "../../config/koneksimysqli_it.php";
    
    
    if ($act == "input") {
        $pidurut = "0000000000";
        //$query = "select noKary from hrd.setup0";
        $query = "select max(karyawanid) as noKary from hrd.karyawan";
        $tampil= mysqli_query($cnit, $query);
        $nrow= mysqli_fetch_array($tampil);
        $pidurut=(INT)$nrow['noKary'];
        $pidurut = plus1($pidurut,10);
        if (!empty($pidurut)) $pkodenya=$pidurut;
    }
    
    /*
    echo "$pkodenya, $ppin, $pnamakry, $ptlahir, $ptgllahir, <br/>";
    echo "$palamat1, $palamat2, $palamatkota, $ptelprmh, $ptelphp, $pjkel, $pagamaid, $ptglmasuk, keluar : $ptglkeluar<br/>";
    echo "$ppendidikanid, $pjabatanid, $pstskaryawan, $pdivisiid1, $pdivisiid2, $pcabangid, $pareaid<br/>";
    echo "BANK : $pbnknama, $pbnkcab, $pbnkkota, $pbnkprov, $pbnknmrek, $pbnknorek<br/>";
    echo "Pasangan : $ppasnama, $ppastempat, $ppastgllahir, $ppaspekerjaan<br/>";
    echo "Atasan : $pspvid, $pdmid, $psmid, $pgsmid<br/>";
    
    echo "Anak1 : $panaknama1, $panakjkel1, $panaktempat1, $panaktgllhr1<br/>";
    echo "Anak2 : $panaknama2, $panakjkel2, $panaktempat2, $panaktgllhr2<br/>";
    echo "Anak3 : $panaknama3, $panakjkel3, $panaktempat3, $panaktgllhr3<br/>";
    echo "Anak4 : $panaknama4, $panakjkel4, $panaktempat4, $panaktgllhr4<br/>";
    echo "Anak5 : $panaknama5, $panakjkel5, $panaktempat5, $panaktgllhr5<br/>";
    */
    
    
    if ($act == "input" AND !empty($pkodenya) AND $pkodenya<>"0000000000") {
        
        $query = "INSERT INTO hrd.karyawan (karyawanid, pin, nama, jabatanid, skar, AKTIF, user1)VALUES('$pkodenya', '$ppin', '$pnamakry', '$pjabatanid', '$pstskaryawan', 'Y', '$puserid')";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }
        
        $query = "INSERT INTO dbmaster.t_karyawan_posisi (karyawanid, aktif, rutin_chc)VALUES('$pkodenya', 'Y', '$prutinchc')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
        
        //$query = "update hrd.setup0 set noKary='$pkodenya' LIMIT 1";
        //mysqli_query($cnit, $query);
        //$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }
        
    }
    
    
    if (!empty($pkodenya) AND $pkodenya<>"0000000000") {
        
        
        $query_updatehrd1 = "UPDATE hrd.karyawan SET pin='$ppin', nama='$pnamakry', skar='$pstskaryawan', jabatanid='$pjabatanid', tempat='$ptlahir', tgllahir='$ptgllahir', "
                . " alamat1='$palamat1', alamat2='$palamat2', kota='$palamatkota', telp='$ptelprmh', "
                . " hp='$ptelphp' WHERE karyawanid='$pkodenya' LIMIT 1";
        mysqli_query($cnit, $query_updatehrd1);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }


        $query_updatehrd2 = "UPDATE hrd.karyawan SET tglmasuk='$ptglmasuk', tglkeluar='$ptglkeluar', "
                . " eduid='$ppendidikanid', skar='$pstskaryawan', atasanId='$patasanidpilih', atasanId2='$patasanidpilih2' WHERE karyawanid='$pkodenya' LIMIT 1";
        mysqli_query($cnit, $query_updatehrd2);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }

        $query_updatehrd3 = "UPDATE hrd.karyawan SET divisiid='$pdivisiid1', divisiid2='$pdivisiid2', "
                . " icabangid='$pcabangid', areaid='$pareaid',"
                . " agamaid='$pagamaid', jkel='$pjkel' WHERE karyawanid='$pkodenya' LIMIT 1";
        mysqli_query($cnit, $query_updatehrd3);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }



        $query_updatehrd4 = "UPDATE hrd.karyawan SET pasangan='$ppasnama', pekerjaan='$ppaspekerjaan', "
                . " tempat2='$ppastempat', tgllahir2='$ppastgllahir' WHERE karyawanid='$pkodenya' LIMIT 1";
        mysqli_query($cnit, $query_updatehrd4);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }

        $query_updatehrd5 = "UPDATE hrd.karyawan SET b_norek='$pbnknorek', b_nama='$pbnknmrek', "
                . " b_bank='$pbnknama', b_cabang='$pbnkcab', b_kota='$pbnkkota',"
                . " b_prov='$pbnkprov' WHERE karyawanid='$pkodenya' LIMIT 1";
        mysqli_query($cnit, $query_updatehrd5);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }




        if (!empty($panaknama1)) {

            mysqli_query($cnit, "DELETE from hrd.anak WHERE karyawanid='$pkodenya'");
            
            if (!empty($panaknama1)) {
                $query = "INSERT INTO hrd.anak (karyawanid, nama, jkel, tempat, tgllahir)VALUES"
                        . " ('$pkodenya', '$panaknama1', '$panakjkel1', '$panaktempat1', '$panaktgllhr1')";
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }
            }
            
            if (!empty($panaknama2)) {
                $query = "INSERT INTO hrd.anak (karyawanid, nama, jkel, tempat, tgllahir)VALUES"
                        . " ('$pkodenya', '$panaknama2', '$panakjkel2', '$panaktempat2', '$panaktgllhr2')";
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }
            }
            
            if (!empty($panaknama3)) {
                $query = "INSERT INTO hrd.anak (karyawanid, nama, jkel, tempat, tgllahir)VALUES"
                        . " ('$pkodenya', '$panaknama3', '$panakjkel3', '$panaktempat3', '$panaktgllhr3')";
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }
            }
            
            if (!empty($panaknama4)) {
                $query = "INSERT INTO hrd.anak (karyawanid, nama, jkel, tempat, tgllahir)VALUES"
                        . " ('$pkodenya', '$panaknama4', '$panakjkel4', '$panaktempat4', '$panaktgllhr4')";
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }
            }
            
            if (!empty($panaknama5)) {
                $query = "INSERT INTO hrd.anak (karyawanid, nama, jkel, tempat, tgllahir)VALUES"
                        . " ('$pkodenya', '$panaknama5', '$panakjkel5', '$panaktempat5', '$panaktgllhr5')";
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }
            }
            
            
        }
    
    
    }
    
    mysqli_close($cnit);
    
    if ($phidupkanprosesinsertkaryawan==true) {
        include "../../config/koneksimysqli.php";
        if ($act == "input") {
            if (!empty($pkodenya) AND $pkodenya<>"0000000000") {
                
                //$query = "update hrd.setup0 set noKary='$pkodenya' LIMIT 1";
                //mysqli_query($cnmy, $query);
                //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

                //mysqli_query($cnmy, "call dbmaster.proses_insert_karyawan_dari_it('$pkodenya')");
        
            }
        }else{
            
            mysqli_query($cnmy, $query_updatehrd1);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            mysqli_query($cnmy, $query_updatehrd2);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            mysqli_query($cnmy, $query_updatehrd3);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            mysqli_query($cnmy, $query_updatehrd4);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            mysqli_query($cnmy, $query_updatehrd5);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            
        }
        
        $query = "UPDATE dbmaster.t_karyawan_posisi SET jabatanid='$pjabatanid', "
                        . " divisiid='$pdivisiid1', icabangid='$pcabangid', areaid='$pareaid', atasanid='$patasanidpilih', "
                        . " b_bank='$pbnknama', b_cabang='$pbnkcab', b_norek='$pbnknorek', "
                        . " spv='$pspvid', dm='$pdmid', sm='$psmid', gsm='$pgsmid', "
                        . " divisi1='$pdivisiid1', divisi2='$pdivisiid2', rutin_chc='$prutinchc' WHERE karyawanid='$pkodenya' LIMIT 1";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
                
        mysqli_close($cnmy);
    }
    
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=completeinup&id='.$pkodenya.'&nmun='.$idmenu);
    
    
}

?>
