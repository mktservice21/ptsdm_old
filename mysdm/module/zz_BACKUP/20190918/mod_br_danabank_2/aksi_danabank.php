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
if ($module=='brdanabank' AND $act=='hapus')
{
    mysqli_query($cnmy, "update $dbname.t_suratdana_bank set stsnonaktif='Y', userid='$_SESSION[IDCARD]' WHERE idinputbank='$_GET[id]'");
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='brdanabank')
{
    $kodenya=$_POST['e_id'];
    
    $pnobukti="";// cari nomor bukti
    
    $ptgl01 = str_replace('/', '-', $_POST['e_tglmasuk']);
    $ptglmasuk= date("Y-m-d", strtotime($ptgl01));
    
    $pdarispd=$_POST['cb_darispd'];
    
    $ptglspd="";
    $ptgl02=$_POST['e_tglspd'];
    if (!empty($ptgl02))
        $ptglspd= date("Y-m-d", strtotime($ptgl02));
    
    $pnospd=$_POST['cb_nospd'];
    $pnodivisi=$_POST['cb_nodivisi'];
    
    $pnobrid=$_POST['e_idnobr'];
    $pnoslip=$_POST['e_noslip'];
    
    $pjenis=$_POST['cb_jenis'];//kodeid
    $psubkode="";
    $pcoa=$_POST['cb_coa'];
    $pdivisi=$_POST['cb_divisi'];//pengajuan
    
    $pstatus=$_POST['cb_sts'];
    
    $pjumlah=str_replace(",","", $_POST['e_jml']);
    if(empty($pjumlah)) $pjumlah=0;
    
    $pket=$_POST['e_ket'];
    if (!empty($pket)) $pket = str_replace("'", " ", $pket);
    
    $pidinputspd="";
    
    
    if ($act=="input") {
        $sql=  mysqli_query($cnmy, "select MAX(RIGHT(idinputbank,8)) as NOURUT from $dbname.t_suratdana_bank");
        $ketemu=  mysqli_num_rows($sql);
        $awal=8; $urut=1; $kodenya=""; $periode=date('Ymd');
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            if (empty($o['NOURUT'])) $o['NOURUT']=0;
            $urut=$o['NOURUT']+1;
            $jml=  strlen($urut);
            $awal=$awal-$jml;
            $kodenya="BN".str_repeat("0", $awal).$urut;
        }else{
            $kodenya="BN00000001";
        }
    }else{
        $kodenya=$_POST['e_id'];
    }
    
    
    if ($pdarispd=="T") {
        $ptglspd="0000-00-00";
        $pnospd="";
        $pnodivisi="";
        $pnobrid="";
        $pnoslip="";
        $pnobukti="";
    }else{
        //cari di suratpd berdasarkan no divisi
        
        $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_suratdana_br WHERE nodivisi='$pnodivisi'");
        $r    = mysqli_fetch_array($edit);
        $pjenis=$r['kodeid'];//kodeid
        $psubkode=$r['subkode'];//subkode
        $pidinputspd=$r['idinput'];
        //$pcoa=$r['coa4'];
        //$pcoa="000-0";//intransit jkt
        $pcoa="000";//intransit sby
        $pdivisi=$r['divisi'];//pengajuan
        $pnobukti=$r['nobbm'];
        
        if (empty($pnospd)) {//jika kosong maka cari nomor spd sesuai  no br / divisi
            $pnospd=$r['nomor'];
        }
        
    }
    
    //echo "$kodenya, dr spd : $pdarispd, $pnospd, $pnodivisi"; exit;
    
    if ($act=="input") {
        $query = "INSERT INTO $dbname.t_suratdana_bank (idinputbank, tanggal, coa4, kodeid, subkode, idinput, nomor, nodivisi, "
                . " nobukti, divisi, sts, jumlah, keterangan, brid, noslip, userid)values"
                . "('$kodenya', '$ptglmasuk', '$pcoa', '$pjenis', '$psubkode', '$pidinputspd', '$pnospd', '$pnodivisi', "
                . " '$pnobukti', '$pdivisi', '$pstatus', '$pjumlah', '$pket', '$pnobrid', '$pnoslip', '$_SESSION[IDCARD]')";
    }else{
        $query = "UPDATE $dbname.t_suratdana_bank SET tanggal='$ptglmasuk', "
                . " coa4='$pcoa', kodeid='$pjenis', subkode='$psubkode', idinput='$pidinputspd', nomor='$pnospd', nodivisi='$pnodivisi', "
                . " nobukti='$pnobukti', divisi='$pdivisi', sts='$pstatus', jumlah='$pjumlah', "
                . " keterangan='$pket', brid='$pnobrid', noslip='$pnoslip', userid='$_SESSION[IDCARD]' WHERE "
                . " idinputbank='$kodenya'";
    }
    
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
    if ($act=="input")
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=tambahbaru');
    else
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
  
    
?>
