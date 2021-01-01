<?php

session_start();
include "../../config/koneksimysqli.php";

$module=$_POST['u_module'];
$act=$_POST['u_act'];
$idmenu=$_POST['u_idmenu'];


// Hapus entry
if ($module=='entrybrnoncabang' AND $act=='hapus')
{
    mysqli_query($cnmy, "update dbbudget.t_br set NONAKTIF='Y' WHERE NOID='$_GET[id]'");
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='entrybrnoncabang')
{
    
    
    if ($act=='input'){
        
        $sql=  mysqli_query($cnmy, "select DATE_FORMAT(CURRENT_DATE(),'%Y%m%d') AS TGLNYA, CONCAT(TAHUN) as PERIODE, NOID as NOURUT from dbmaster.sdm_counter where CONCAT(TAHUN)=DATE_FORMAT(CURRENT_DATE(),'%Y')");
        $ketemu=  mysqli_num_rows($sql);
        $awal=8; $urut=1; $kodenya=""; $periode=date('Ymd');
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            if (!empty($o['NOURUT'])) {
                $periode=$o['TGLNYA'];
                $urut=$o['NOURUT']+1;
                $jml=  strlen($urut);
                $awal=$awal-$jml;
                $kodenya=str_repeat("0", $awal).$urut;
            }else{
                mysqli_query($cnmy, "insert into dbmaster.sdm_counter(TAHUN, BULAN)values(DATE_FORMAT(CURRENT_DATE(),'%Y'), DATE_FORMAT(CURRENT_DATE(),'%m'))");
                $kodenya=str_repeat("0", (int)$awal-1)."1";
            }

            mysqli_query($cnmy, "update dbmaster.sdm_counter set NOID=ifnull(NOID,0)+1 where CONCAT(TAHUN,BULAN)=DATE_FORMAT(CURRENT_DATE(),'%Y%m')");
            $kodenya="ID-".$periode."-".$kodenya;
        
        }else{
            $kodenya=$_POST['e_nobr'];
        }
    }else{
        $kodenya=$_POST['e_nobr'];
    }
    
    if (empty($kodenya)){
        echo "ID kosong, ulang lagi....";
        exit;
    }
    
    $ptglinput= date("Y-m-d", strtotime($_POST['e_tglinput']));
    $ptglperlu= date("Y-m-d", strtotime($_POST['e_tglperlu']));
    $pidcabang=$_POST['e_idcabang'];
    $pdivprodid=$_POST['cb_divisi'];
    $pkaryawan=$_POST['e_idkaryawan'];
    $pidcabang=$_POST['e_idcabang'];
    $pdivprodid=$_POST['cb_divisi'];
    $paktivitas1=$_POST['e_aktivitas'];
    $prpnya=str_replace(",","", $_POST['e_jmlusulan']);
    
    
    if ($act=='input') {
    $sql="insert into dbbudget.t_br (NOID, TGL, TGL_PERLU, divprodid, KARYAWANID, ICABANGID, JUMLAH, AKTIVITAS1, MODIFUN)VALUES"
            . "('$kodenya', '$ptglinput', '$ptglperlu', '$pdivprodid', '$pkaryawan', '$pidcabang', '$prpnya', '$paktivitas1', '$_SESSION[IDCARD]')";
    }else{
        $sql="update dbbudget.t_br set TGL='$ptglinput', TGL_PERLU='$ptglperlu', divprodid='$pdivprodid', JUMLAH='$prpnya', AKTIVITAS1='$paktivitas1', ICABANGID='$pidcabang', MODIFUN='$_SESSION[IDCARD]' where"
                . " NOID='$kodenya'";
    }
    
    mysqli_query($cnmy, $sql);
    
    $savedetail="false";
    $sql="delete from dbbudget.t_br_d where NOID='$kodenya'";
    mysqli_query($cnmy, $sql);
    
    $akun=$_POST['m_akun'];
    $nominal=$_POST['m_nominal'];
    $catatan=$_POST['m_catatan'];
    for ($k=0;$k<=count($akun);$k++) {
        if (!empty($akun[$k])){
            $rpnya=0;
            if (!empty($nominal[$k])) {
                $rpnya=str_replace(",","", $nominal[$k]);
            }
            $sql="insert into dbbudget.t_br_d (NOID, kode, RP, AKTIVITAS2)VALUES"
                    . "('$kodenya','$akun[$k]', '$rpnya', '$catatan[$k]')";
            mysqli_query($cnmy, $sql);
            $savedetail="true";
        }
    }
    
    if ($savedetail=="false") {
        $rpnya=str_replace(",","", $_POST['e_nominal']);
        $sql="insert into dbbudget.t_br_d (NOID, kode, RP, AKTIVITAS2)VALUES"
                . "('$kodenya','$_POST[e_akun]', '$rpnya', '$_POST[e_aktivitas2]')";
        mysqli_query($cnmy, $sql);
        
        mysqli_query($cnmy, "update dbbudget.t_br set JUMLAH='$rpnya' where NOID='$kodenya'");
    }
    
    $sql="delete from dbbudget.t_br_u where NOID='$kodenya'";
    mysqli_query($cnmy, $sql);
    $tampil = mysqli_query($cnmy, "SELECT NOBUD, NAMA_BUD, FORMAT(RP,2,'de_DE') as RP FROM dbbudget.br_uc_budget order by NOBUD");
    while ($uc=mysqli_fetch_array($tampil)){
        $jhari=0; $jtot=0; $jket="";
        if (isset($_POST['e_hr'.$uc['NOBUD']])) $jhari=$_POST['e_hr'.$uc['NOBUD']];
        if (isset($_POST['e_hr'.$uc['NOBUD']])) $jtot=$_POST['e_rphr'.$uc['NOBUD']];
        $jtot=str_replace(",","", $jtot);
        if (isset($_POST['e_hr'.$uc['NOBUD']])) $jket=$_POST['e_note'.$uc['NOBUD']];
        
        $sql="insert into dbbudget.t_br_u (NOID, NOBUD, JML, TOTAL, KET)VALUES"
                . "('$kodenya','$uc[NOBUD]', '$jhari', '$jtot', '$jket')";
        mysqli_query($cnmy, $sql);
    }
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
?>
