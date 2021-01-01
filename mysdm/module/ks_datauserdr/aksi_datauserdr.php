<?php

session_start();

    date_default_timezone_set('Asia/Jakarta');
    //ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    include "../../config/koneksimysqli_it.php";
    include "../../config/koneksimysqli.php";
    include "../../config/common.php";
    
    //$cnit=$cnmy;
    
// Hapus / non aktifkan
if ($module=='ksdatauser' AND $act=='hapus')
{
    
}
elseif ($module=='ksdatauser' AND ($act=='input' OR $act=='update' OR $act=='updatecn'))
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
    $pnamaid=$_POST['e_nmuserdr'];
    $psplid=$_POST['cb_spesialis'];
    $pbagianrs=$_POST['e_bagian'];
    $palamatid1=$_POST['e_alamat1'];
    $palamatid2=$_POST['e_alamat2'];
    $pkota=$_POST['e_kota'];
    $ptelp1=$_POST['e_telp1'];
    $ptelp2=$_POST['e_telp2'];
    $php=$_POST['e_hp'];
    $pcn=$_POST['e_cn'];
    $ptglcn=$_POST['e_tglcn'];
    $psaldorp=$_POST['e_saldorp'];
    
    
    if (!empty($pnamaid)) $pnamaid = str_replace("'", " ", $pnamaid);
    if (!empty($pbagianrs)) $pbagianrs = str_replace("'", " ", $pbagianrs);
    if (!empty($palamatid1)) $palamatid1 = str_replace("'", " ", $palamatid1);
    if (!empty($palamatid2)) $palamatid2 = str_replace("'", " ", $palamatid2);
    if (!empty($pkota)) $pkota = str_replace("'", " ", $pkota);
    if (!empty($ptelp1)) $ptelp1 = str_replace("'", " ", $ptelp1);
    if (!empty($ptelp2)) $ptelp2 = str_replace("'", " ", $ptelp2);
    if (!empty($php)) $php = str_replace("'", " ", $php);
    
    
    if (!empty($ptglcn)) {
        $ptglcn = str_replace('/', '-',$ptglcn);
        $ptglcn= date("Y-m-d", strtotime($ptglcn));
    }else $ptglcn = "0000-00-00";
    
    $pcn=str_replace(",","", $pcn);
    if(empty($pcn)) $pcn=0;
    
    
    $psaldorp=str_replace(",","", $psaldorp);
    if(empty($psaldorp)) $psaldorp=0;
    
    
    if ($act=="input") {
        $query = "select max(dokterId) as dokterid from hrd.dokter";
        $result = mysqli_query($cnit, $query);
        $row = mysqli_fetch_array($result);
        $num_results = mysqli_num_rows($result);
        $kodenya = plus1($row['dokterid'],10);
    }
    
    
    if (empty($kodenya)){
        echo "ID kosong, ulang lagi....";
        mysqli_close($cnit);
        exit;
    }
    
    //echo "$kodenya, $pkryid, $pnamaid, spesial : $psplid, bag : $pbagianrs, Almt : $palamatid1 - $palamatid2, $pkota, telp : $ptelp1 - $ptelp2, hp : $php, CN : $pcn ($ptglcn), Saldo : $psaldorp<br/>"; exit;
    
    if ($act=="input") {
        
        $query = "INSERT INTO hrd.dokter (dokterid,nama,spid,bagian,alamat1,alamat2,kota,telp,telp2,hp,user1,aktif)
            VALUES('".$kodenya."','".$pnamaid."','".$psplid."','".$pbagianrs."','".$palamatid1."','".$palamatid2."','".$pkota."','".$ptelp1."','".$ptelp2."','".$php."','".$puserid."','Y')";
        $result = mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        
    
    }else{
        $query = "UPDATE hrd.dokter SET nama='".$pnamaid."',
                  spid='".$psplid."',
                  bagian='".$pbagianrs."',
                  alamat1='".$palamatid1."',
                  alamat2='".$palamatid2."',
                  kota='".$pkota."',
                  telp='".$ptelp1."',
                  telp2='".$ptelp2."',
                  hp='".$php."',
                  user1='".$puserid."' WHERE dokterid='".$kodenya."' LIMIT 1";
        $result = mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
    }
     
    
    
    if ($act=="input" OR $act=="updatecn") {

        $query = "DELETE FROM hrd.mr_dokt WHERE karyawanid='$pkryid' AND dokterid='$kodenya' LIMIT 1";
        $result = mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }


        $query1 = "INSERT INTO hrd.mr_dokt (karyawanid,dokterid,cn,aktif,tgl,awal)
            VALUES ('".$pkryid."','".$kodenya."','".$pcn."','Y','".$ptglcn."','".$psaldorp."')";
        $result = mysqli_query($cnit, $query1);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query = "DELETE FROM hrd.mr_dokt_a WHERE karyawanid='$pkryid' AND dokterid='$kodenya' LIMIT 1";
        $result = mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }

        $query2 = "INSERT INTO hrd.mr_dokt_a (karyawanid,dokterid,cn,aktif,tgl,awal)
            VALUES ('".$pkryid."','".$kodenya."','".$pcn."','Y','".$ptglcn."','".$psaldorp."')";
        $result = mysqli_query($cnit, $query2);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }


    }

    $result = mysqli_query($cnmy, "CALL dbmaster.proses_data_karyawan_hrd_dokter()");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }    
    
    mysqli_close($cnmy);
    mysqli_close($cnit);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
    exit;
            
            
}