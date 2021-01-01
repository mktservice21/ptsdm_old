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
if ($module=='dataspg' AND $act=='hapus')
{
    mysqli_query($cnmy, "update $dbname.t_spg set aktif='N' WHERE karyawanId='$_GET[id]'");
    header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='dataspg')
{
    $kodenya=$_POST['e_id'];
    $datemasuk = str_replace('/', '-', $_POST['e_tglmasuk']);
    $ptglmasuk= date("Y-m-d", strtotime($datemasuk));
    
    $ptgllahir="0000-00-00";
    if (!empty($_POST['e_tgllahir'])) {
        $datelahir = str_replace('/', '-', $_POST['e_tgllahir']);
        $ptgllahir= date("Y-m-d", strtotime($datelahir));
    }
    
    $pnama=$_POST['e_nama'];
    $ptlahir=$_POST['e_tlahir'];
    $pjekel=$_POST['cb_jekel'];
    $palamat=$_POST['e_alamat'];
    if (!empty($palamat)) $palamat = str_replace("'", " ", $palamat);
    $pkota=$_POST['e_kota'];
    $php=$_POST['e_hp'];
    $pjabatan=$_POST['cb_jabatan'];
    $pagama=$_POST['cb_agama'];
    $pdivisi=$_POST['cb_divisi'];
    $pcabang=$_POST['cb_cabang'];
    $parea=$_POST['cb_areasdm'];
    $pkdtoko=$_POST['cb_custsdm'];
    $ptoko=$_POST['e_toko'];
    
    
    $ptglkeluar="0000-00-00";
    if (!empty($_POST['e_tglkeluar'])) {
        $datekeluar = str_replace('/', '-', $_POST['e_tglkeluar']);
        $ptglkeluar= date("Y-m-d", strtotime($datekeluar));
    }
    
    if ($act=='input') {
        $sql=  mysqli_query($cnmy, "select IFNULL(MAX(karyawanId),0) as NOURUT from $dbname.t_spg");
        $ketemu=  mysqli_num_rows($sql);
        $awal=9; $urut=1; $kodenya=""; $periode=date('Ymd');
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            $urut=$o['NOURUT']+1;
            $jml=  strlen($urut);
            $awal=$awal-$jml;
            $kodenya="8".str_repeat("0", $awal).$urut;
        }else{
            $kodenya=$_POST['e_id'];
        }
    }
    echo $kodenya;
    exit;
    
    if ($act=="input") {
        $query = "INSERT INTO $dbname.t_spg (TGLINPUT, TANGGAL, DIVISI, COA4, JUMLAH, KETERANGAN, USERID)values"
                . "(CURRENT_DATE(), '$periode1', '$pdivisi', '$pcoa', '$pjumlah', '$pket', '$_SESSION[IDCARD]')";
    }else{
        $query = "UPDATE $dbname.t_spg SET divisi='$pdivisi', TANGGAL='$periode1', "
                . " COA4='$pcoa', KETERANGAN='$pket', jumlah='$pjumlah', userid='$_SESSION[IDCARD]' WHERE "
                . " ID='$kodenya'";
    }
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
  
    
?>
