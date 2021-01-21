<?php

session_start();

    date_default_timezone_set('Asia/Jakarta');
    //ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    include "../../config/koneksimysqli_ms.php";
    include "../../config/common.php";
    
// Hapus / non aktifkan
if ($module=='mapcustomersdm' AND $act=='hapus')
{
    
}
elseif ($module=='mapcustomersdm' AND ($act=='input' OR $act=='update'))
{
    
    $puserid=$_POST['e_idinputuser'];
    $pcardid=$_POST['e_idcarduser'];
    
    if (empty($puserid)) {
        $puserid="";
        if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
        if (isset($_SESSION['IDCARD'])) $pcardid=$_SESSION['IDCARD'];

        if (empty($puserid)) {
            mysqli_close($cnms);
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }
    }
    
    $kodenya=$_POST['e_id'];
    $pidcab=$_POST['cb_cabangid'];
    $pidarea=$_POST['cb_areaid'];
    $pisektorid=$_POST['cb_sektorid'];
    $pnamaid=$_POST['e_nama'];
    $palamatid1=$_POST['e_alamat1'];
    $palamatid2=$_POST['e_alamat2'];
    $pkota=$_POST['e_kota'];
    $pkodepos=$_POST['e_kdpos'];
    $ptelp=$_POST['e_telp'];
    $pfax=$_POST['e_fax'];
    $pkontak=$_POST['e_kontakperson'];
    
    
    if (!empty($pnamaid)) $pnamaid = str_replace("'", " ", $pnamaid);
    if (!empty($palamatid1)) $palamatid1 = str_replace("'", " ", $palamatid1);
    if (!empty($palamatid2)) $palamatid2 = str_replace("'", " ", $palamatid2);
    if (!empty($pkota)) $pkota = str_replace("'", " ", $pkota);
    if (!empty($pkodepos)) $pkodepos = str_replace("'", " ", $pkodepos);
    if (!empty($ptelp)) $ptelp = str_replace("'", " ", $ptelp);
    if (!empty($pfax)) $pfax = str_replace("'", " ", $pfax);
    if (!empty($pkontak)) $pkontak = str_replace("'", " ", $pkontak);
    
    $pgrp="";
    if ($pisektorid == '01' or $pisektorid == '06') {
        $pgrp = '1';
    } else {
        if ($pisektorid == '02') {
            $pgrp = '2';
        } else {
            $pgrp = '3';
        }
    }
    
    
    
    if ($act=="input") {
        $query = "select max(icustid) as icustid
                  from MKT.icust
                  where icabangid='".$pidcab."' AND areaid='".$pidarea."'";
        $result = mysqli_query($cnms, $query);
        $row = mysqli_fetch_array($result);
        $num_results = mysqli_num_rows($result);
        $kodenya = plus1($row['icustid'],10);
    }
    
    
    if (empty($kodenya)){
        echo "ID kosong, ulang lagi....";
        mysqli_close($cnms);
        exit;
    }
    
    //echo "$kodenya, $pkodepos, $pnamaid, $palamatid1 - $palamatid2, $pkota, $pgrp<br/>";
    
    if ($act=="input") {
        $query = "insert into MKT.icust SET icabangid='".$pidcab."',
                  areaid='".$pidarea."',
                  isektorid='".$pisektorid."',
                  icustid='".$kodenya."',
                  nama='".$pnamaid."',
                  alamat1='".$palamatid1."',
                  alamat2='".$palamatid2."',
                  kota='".$pkota."',
                  kodepos='".$pkota."',
                  telp='".$ptelp."',
                  fax='".$pfax."',
                  contact='".$pkontak."',
                  grp='".$pgrp."',
                  user1='".$pcardid."'";
        $result = mysqli_query($cnms, $query);
        $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; exit; }
        
    }else{
        $query = "UPDATE MKT.icust SET nama='".$pnamaid."',
                  alamat1='".$palamatid1."',
                  alamat2='".$palamatid2."',
                  kota='".$pkota."',
                  kodepos='".$pkota."',
                  telp='".$ptelp."',
                  fax='".$pfax."',
                  contact='".$pkontak."',
                  grp='".$pgrp."',
                  user1='".$pcardid."' WHERE icabangid='".$pidcab."' AND areaid='".$pidarea."' AND icustid='".$kodenya."' LIMIT 1";
        $result = mysqli_query($cnms, $query);
        $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
    mysqli_close($cnms);
    //header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
    exit;
            
            
}