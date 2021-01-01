<?php
session_start();

    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
$puserid="";
if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

if (empty($puserid)) {
    echo "ANDA HARUS LOGIN ULANG...";
    exit;
}

$pidcard=$_SESSION['IDCARD'];
$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];


if ($module=='spdbpjs')
{
    
    $ptgl=$_POST['e_tanggal'];
    $ptglpengajuan= date("Y-m-d", strtotime($ptgl));
    
    $pbulan=$_POST['e_tglbulan'];
    $pblnpengajuan= date("Ym", strtotime($pbulan));
    
    $_SESSION['SBPJSINPBLN01']=$pbulan;
    $_SESSION['SBPJSINPTGLAJU']=$ptgl;
    $_SESSION['SBPJSINPTIPE']="";
    
    if (empty($pbulan)) exit;
    if (empty($ptglpengajuan)) exit;
    
    include "../../config/koneksimysqli.php";
    
    //echo "$pblnpengajuan : $ptglpengajuan<br/>";
    foreach ($_POST['chkbox_br'] as $pkaryawanid) {
        
        if (empty($pkaryawanid)) {
            continue;
        }
        
        $pjmlkls=$_POST['txt_kelas'][$pkaryawanid];
        
        $pjmlgp=$_POST['txt_ngp'][$pkaryawanid];
        $pjmlpt=$_POST['txt_npotpt'][$pkaryawanid];
        $pjmlkry=$_POST['txt_npotkry'][$pkaryawanid];
        $pjmltotal=$_POST['txt_ntotal'][$pkaryawanid];
        
        if (empty($pjmlgp)) $pjmlgp=0;
        if (empty($pjmlpt)) $pjmlpt=0;
        if (empty($pjmlkry)) $pjmlkry=0;
        if (empty($pjmltotal)) $pjmltotal=0;
        
        $pjmlgp=str_replace(",","", $pjmlgp);
        $pjmlpt=str_replace(",","", $pjmlpt);
        $pjmlkry=str_replace(",","", $pjmlkry);
        $pjmltotal=str_replace(",","", $pjmltotal);
        
        
        if ($act=="simpan" OR $act=="hapus") {
            $query = "DELETE FROM dbmaster.t_spd_bpjs WHERE periode='$pblnpengajuan' AND karyawanid='$pkaryawanid'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        }
        
        if ($act=="simpan") {
            
            $query = "INSERT INTO dbmaster.t_spd_bpjs (periode, karyawanid, tanggal, ngp, kelas, potongan_pt, potongan_kry, bayar, userid)VALUES"
                    . "('$pblnpengajuan', '$pkaryawanid', '$ptglpengajuan', '$pjmlgp', '$pjmlkls', '$pjmlpt', '$pjmlkry', '$pjmltotal', '$pidcard')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
        }
        
        //echo "$pkaryawanid - $pjmlkls : $pjmlgp, $pjmlpt, $pjmlkry, $pjmltotal<br/>";
    }
    
    
    mysqli_close($cnmy);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=sudahsimpan');
}

?>