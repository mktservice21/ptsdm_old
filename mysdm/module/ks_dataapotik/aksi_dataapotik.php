<?php

session_start();

    date_default_timezone_set('Asia/Jakarta');
    //ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    include "../../config/koneksimysqli.php";
    include "../../config/common.php";
    
    $cnit=$cnmy;
    
// Hapus / non aktifkan
if ($module=='ksdataapotik' AND $act=='hapus')
{
    
}
elseif ($module=='ksdataapotik' AND ($act=='input' OR $act=='update'))
{
    
    $puserid=$_POST['e_idinputuser'];
    $pcardid=$_POST['e_idcarduser'];
    
    if (empty($puserid)) {
        $puserid="";
        if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
        if (isset($_SESSION['IDCARD'])) $pcardid=$_SESSION['IDCARD'];

        if (empty($puserid)) {
            mysqli_close($cnit);
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }
    }
    
    $kodenya=$_POST['e_id'];
    $pkryid=$_POST['cb_karyawan'];
    $pnamaid=$_POST['e_nmapotik'];
    $palamatid1=$_POST['e_alamat1'];
    $palamatid2=$_POST['e_alamat2'];
    $pkota=$_POST['e_kota'];
    
    
    if (!empty($pnamaid)) $pnamaid = str_replace("'", " ", $pnamaid);
    if (!empty($palamatid1)) $palamatid1 = str_replace("'", " ", $palamatid1);
    if (!empty($palamatid2)) $palamatid2 = str_replace("'", " ", $palamatid2);
    if (!empty($pkota)) $pkota = str_replace("'", " ", $pkota);
    
    
    
    
    
    if ($act=="input") {
        $query = "select max(aptid) as aptid
                  from hrd.mr_apt
                  where srid='".$pkryid."'";
        $result = mysqli_query($cnit, $query);
        $row = mysqli_fetch_array($result);
        $num_results = mysqli_num_rows($result);
        $kodenya = plus1($row['aptid'],10);
    }
    
    
    if (empty($kodenya)){
        echo "ID kosong, ulang lagi....";
        mysqli_close($cnit);
        exit;
    }
    
    //echo "$kodenya, $pkryid, $pnamaid, $palamatid1 - $palamatid2, $pkota<br/>";
    
    if ($act=="input") {
        
        $query = "insert into hrd.mr_apt SET srid='".$pkryid."',
                  aptid='".$kodenya."',
                  nama='".$pnamaid."',
                  alamat1='".$palamatid1."',
                  alamat2='".$palamatid2."',
                  kota='".$pkota."',
                  user1='".$puserid."',
                  aktif='Y'";
        $result = mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        
    }else{
        $query = "UPDATE hrd.mr_apt SET srid='".$pkryid."',
                  nama='".$pnamaid."',
                  alamat1='".$palamatid1."',
                  alamat2='".$palamatid2."',
                  kota='".$pkota."',
                  user1='".$puserid."' WHERE idapotik='".$kodenya."' LIMIT 1";
        $result = mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
    mysqli_close($cnit);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
    exit;
            
            
}