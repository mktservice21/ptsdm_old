<?php

    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    //$cnmy=$cnit;
    $dbname = "dbmaster";
// Hapus 
if ($module=='entrybrkasbon' AND $act=='hapus')
{
    mysqli_query($cnmy, "update $dbname.t_kasbon set stsnonaktif='Y', userid='$_SESSION[IDCARD]' WHERE idkasbon='$_GET[id]'");
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='entrybrkasbon' AND $act=='statusselesai')
{
    mysqli_query($cnmy, "update $dbname.t_kasbon set iselesai='Y', userid='$_SESSION[IDCARD]' WHERE idkasbon='$_GET[id]'");
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='entrybrkasbon')
{
    $kodenya=$_POST['e_id'];
    $pdivisi=$_POST['cb_divisi'];
    $pkaryawanid=$_POST['cb_karyawan'];
    $pnamauntuk=$_POST['e_nama'];
    $pkodeid=$_POST['cb_kdoepil'];
    $pcoapilih=$_POST['cb_coa'];
    
    $pket=$_POST['e_ket'];
    if (!empty($pket)) $pket = str_replace("'", " ", $pket);
    
    $ptgl = str_replace('/', '-', $_POST['e_tglberlaku']);
    $periode1= date("Y-m-d", strtotime($ptgl));
    
    $pjumlah=str_replace(",","", $_POST['e_jml']);
    if(empty($pjumlah)) $pjumlah=0;
    
    

    
    if ($act=="input") {
        $sql=  mysqli_query($cnmy, "select MAX(RIGHT(idkasbon,8)) as NOURUT from $dbname.t_kasbon");
        $ketemu=  mysqli_num_rows($sql);
        $awal=8; $urut=1; $kodenya=""; $periode=date('Ymd');
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            if (empty($o['NOURUT'])) $o['NOURUT']=0;
            $urut=$o['NOURUT']+1;
            $jml=  strlen($urut);
            $awal=$awal-$jml;
            $kodenya="KB".str_repeat("0", $awal).$urut;
        }else{
            $kodenya="KB00000001";
        }
    }else{
        $kodenya=$_POST['e_id'];
    }
    
    
    if ($act=="input") {
        $query = "INSERT INTO $dbname.t_kasbon (idkasbon, tgl, divisi, karyawanid, nama, jumlah, keterangan, coa4, userid)values"
                . "('$kodenya', '$periode1', '$pdivisi', '$pkaryawanid', '$pnamauntuk', '$pjumlah', '$pket', '$pcoapilih', '$_SESSION[IDCARD]')";
    }else{
        $query = "UPDATE $dbname.t_kasbon SET divisi='$pdivisi', tgl='$periode1', "
                . " karyawanid='$pkaryawanid', nama='$pnamauntuk', keterangan='$pket', jumlah='$pjumlah', coa4='$pcoapilih', userid='$_SESSION[IDCARD]' WHERE "
                . " idkasbon='$kodenya'";
    }
    
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $query = "UPDATE $dbname.t_kasbon SET kode='$pkodeid' WHERE idkasbon='$kodenya' LIMIT 1";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    if ($act=="input")
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=tambahbaru');
    else
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
  
    
?>
