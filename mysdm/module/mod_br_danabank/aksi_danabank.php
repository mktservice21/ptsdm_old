<?php

    session_start();
	
$puserid="";
if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

if (empty($puserid)) {
    echo "ANDA HARUS LOGIN ULANG...";
    exit;
}
	
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
    
    mysqli_query($cnmy, "update $dbname.t_suratdana_bank set parentidbank='' WHERE parentidbank='$_GET[id]' AND IFNULL(parentidbank,'')<>'' AND stsinput='D'");
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='brdanabank' AND $act=='transferulang')
{
    
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
        
    $pnoslipbaru=$_GET['utxt'];
    $pidbank=$_GET['id'];
    if (!empty($kodenya) AND !empty($pidbank)) {
        $userid=$_SESSION['IDCARD'];
        $now=date("mdYhis");
        $tmp01 =" dbtemp.TSDSETHZR01_".$userid."_$now ";
        
        $query = "select * from $dbname.t_suratdana_bank WHERE idinputbank='$pidbank'";
        $query = "create TEMPORARY table $tmp01 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query = "select divisi from $tmp01";
        $tampil=  mysqli_query($cnmy, $query);
        $nr= mysqli_fetch_array($tampil);
        $ndivisi=$nr['divisi'];
        
        
        //no slip
        $nnoslip="";
        $nnobrid="";
        
        if ($ndivisi=="OTC"){
            $query = "select noslip, brOtcId brId from hrd.br_otc where brOtcId =(select IFNULL(brid,'') from $dbname.t_suratdana_bank WHERE idinputbank='$pidbank')";
        }else{
            $query = "select noslip, brId from hrd.br0 where brId =(select IFNULL(brid,'') from $dbname.t_suratdana_bank WHERE idinputbank='$pidbank')";
        }
        $tampil=  mysqli_query($cnmy, $query);
        $nr= mysqli_fetch_array($tampil);
        $nnobrid=$nr['brId'];
        
        if (empty($pnoslipbaru)) {
            $nnoslip=$nr['noslip'];
        }else{
            $nnoslip=$pnoslipbaru;
            if (!empty($nnoslip)) {
                //include "../../config/koneksimysqli_it.php";
                
                if ($ndivisi=="OTC"){
                    $query = "UPDATE hrd.br_otc SET noslip='$nnoslip' WHERE brOtcId='$nnobrid'";
                }else{
                    $query = "UPDATE hrd.br0 SET noslip='$nnoslip' WHERE brId='$nnobrid'";
                }
                mysqli_query($cnmy, $query);
            }
        }
        
        //no bbk
        $pnobukti="";
        $query = "select nobukti from $dbname.t_suratdana_bank WHERE idinputbank='$pidbank'";
        $tampil=  mysqli_query($cnmy, $query);
        $nr= mysqli_fetch_array($tampil);
        $pnobukti=$nr['nobukti'];
        
        if (empty($pnobukti)) {
            //nobbk
            include "../../config/fungsi_combo.php";

            $hari_ini = date("Y-m-d");
            $tgl1 = date('d/m/Y', strtotime($hari_ini));
            $pblnini = date('m', strtotime($hari_ini));
            $pthnini = date('Y', strtotime($hari_ini));
            $pthnini_bln = date('Ym', strtotime($hari_ini));
            $tno="1501";
            $query = "SELECT LTRIM(REPLACE(MAX(SUBSTRING_INDEX(nobukti, '/', 1)),'BBK','')) as nobbk FROM dbmaster.t_suratdana_bank 
                WHERE IFNULL(stsnonaktif,'') <> 'Y' AND DATE_FORMAT(tanggal,'%Y%m')='$pthnini_bln' AND IFNULL(stsinput,'')='K'";
            $showkan= mysqli_query($cnmy, $query);
            $ketemu= mysqli_num_rows($showkan);
            if ($ketemu>0){
                $sh= mysqli_fetch_array($showkan);
                if (!empty($sh['nobbk'])) { $tno=(INT)$sh['nobbk']+1; }
                if ((double)$tno==1) $tno="1501";
            }
            $mbulan=CariBulanHuruf($pblnini);
            $pnobukti = "BBK".$tno."/".$mbulan."/".$pthnini;
        }
        $pnobukti="";
        $query = "UPDATE $tmp01 SET idinputbank='$kodenya', stsinput='T', parentidbank='$pidbank', nobukti='$pnobukti', noslip='$nnoslip', coa4='000-0', keterangan='Transfer Ulang', tanggal=CURRENT_DATE(), sys_now=NOW()"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        
        $query = "INSERT INTO $dbname.t_suratdana_bank "
                . "SELECT * FROM $tmp01"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        
        $query = "UPDATE $dbname.t_suratdana_bank SET parentidbank='$kodenya' WHERE idinputbank='$pidbank'"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        
        mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
        mysqli_close($cnmy);
        
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    }
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
    //$pnodivisi=$_POST['cb_nodivisi'];
	$pnodivisi=$_POST['cb_nodivisipil'];
    
    
    $pdivpilih_d="";
    $edit = mysqli_query($cnmy, "SELECT divisi FROM dbmaster.t_suratdana_br WHERE nodivisi='$pnodivisi' AND stsnonaktif<>'Y'");
    $rd    = mysqli_fetch_array($edit);
    $pdivpilih_d = $rd['divisi'];
    
    
    $pnobrid=$_POST['e_idnobr'];
    $pnoslip=$_POST['e_noslip'];
    
    $pnmrealisasi="";
    $paktivitas1="";
    $piddokter="";
    $pnmdokter="";
    
    if (!empty($pnobrid)) {
        if ($pdivpilih_d=="OTC") {
            
            $query = "select brOtcId, real1 as realisasi1, noslip, keterangan1 as aktivitas1 from hrd.br_otc WHERE brOtcId='$pnobrid'";
            $tampil=  mysqli_query($cnmy, $query);
            $ketemu= mysqli_num_rows($tampil);
            if ($ketemu>0) {
                $nr= mysqli_fetch_array($tampil);
                
                $pnmrealisasi=$nr['realisasi1'];
                $paktivitas1=$nr['aktivitas1'];
            }
            
        }else{
            $query = "select brId, realisasi1, noslip, dokterId, dokter, aktivitas1 from hrd.br0 WHERE brId='$pnobrid'";
            $tampil=  mysqli_query($cnmy, $query);
            $ketemu= mysqli_num_rows($tampil);
            if ($ketemu>0) {
                $nr= mysqli_fetch_array($tampil);

                $pnmrealisasi=$nr['realisasi1'];
                $paktivitas1=$nr['aktivitas1'];
                $piddokter=$nr['dokterId'];
                $pnmdokter=$nr['dokter'];
                if (!empty($piddokter)) {

                    //include "../../config/koneksimysqli_it.php";

                    $query = "select nama from hrd.dokter WHERE dokterId='$piddokter'";
                    $tampil_d=  mysqli_query($cnmy, $query);
                    $nd= mysqli_fetch_array($tampil_d);

                    if (!empty($nd['nama'])) $pnmdokter=$nd['nama'];

                }

            }
        }
    }
    
    
    
    //$pjenis=$_POST['cb_jenis'];//kodeid
	$pjenis="2";
    $psubkode=$_POST['cb_kodesub'];//sub kodeid
	if (empty($psubkode)) $pjenis="5";
    
    $pcoa=$_POST['cb_coa'];
    $pdivisi=$_POST['cb_divisi'];//pengajuan
    
    $pstatus=$_POST['cb_sts'];
    
    $stsinput_kode=$_POST['cb_debitkredit'];
    
    
    
    
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
        
        if (empty($psubkode)) {// jenis sub
            $pjenis=$r['kodeid'];//kodeid
            $psubkode=$r['subkode'];//subkode
			
            if (empty($pnodivisi)) $psubkode="";
			
            //$pcoa=$r['coa4'];
            //$pcoa="000-0";//intransit jkt
            $pcoa="000";//intransit sby
        }
        
        $pidinputspd=$r['idinput'];
        if (empty($pnospd)) {//jika kosong maka cari nomor spd sesuai  no br / divisi
            $pnospd=$r['nomor'];
        }
        $pdivisi=$r['divisi'];//pengajuan
        if ($stsinput_kode=="D") $pnobukti=$r['nobbm'];
        
    }
    
    //echo "$kodenya, dr spd : $pdarispd, $pnospd, $pnodivisi"; exit;
    
    if ($act=="input") {
        $query = "INSERT INTO $dbname.t_suratdana_bank (idinputbank, tanggal, coa4, kodeid, subkode, idinput, nomor, nodivisi, "
                . " nobukti, divisi, sts, jumlah, keterangan, brid, noslip, userid, stsinput)values"
                . "('$kodenya', '$ptglmasuk', '$pcoa', '$pjenis', '$psubkode', '$pidinputspd', '$pnospd', '$pnodivisi', "
                . " '$pnobukti', '$pdivisi', '$pstatus', '$pjumlah', '$pket', '$pnobrid', '$pnoslip', '$_SESSION[IDCARD]', '$stsinput_kode')";
    }else{
        $query = "UPDATE $dbname.t_suratdana_bank SET tanggal='$ptglmasuk', "
                . " coa4='$pcoa', kodeid='$pjenis', subkode='$psubkode', idinput='$pidinputspd', nomor='$pnospd', nodivisi='$pnodivisi', "
                . " nobukti='$pnobukti', divisi='$pdivisi', sts='$pstatus', jumlah='$pjumlah', "
                . " keterangan='$pket', brid='$pnobrid', noslip='$pnoslip', userid='$_SESSION[IDCARD]', stsinput='$stsinput_kode' WHERE "
                . " idinputbank='$kodenya'";
    }
    
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
    $query = "UPDATE $dbname.t_suratdana_bank SET realisasi='$pnmrealisasi', "
            . " customer='$pnmdokter', aktivitas1='$paktivitas1' WHERE "
            . " idinputbank='$kodenya'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
    if ($stsinput_kode=="D" OR $stsinput_kode=="K") {
        
        $pnobukti="";
        $p_no="1";
        $p_buknin="BBM";
        $p_fieldno="nobbm";
        if ($stsinput_kode=="K") {
            $p_no="2";
            $p_buknin="BBK";
            $p_fieldno="nobbk";
        }
        
        include "cari_nomorbukti.php";
        include "../../config/fungsi_combo.php";
        $ppilih_nobukti=caribuktinomor($p_no, $ptglmasuk);// 1=bbm, 2=bbm
        
        $pbukti_periode=date('Ym', strtotime($ptglmasuk));;
        $pblnini = date('m', strtotime($ptglmasuk));
        $pthnini = date('Y', strtotime($ptglmasuk));
        $mbulan=CariBulanHuruf($pblnini);
        $ppilih_blnthn="/".$mbulan."/".$pthnini;
        $pnobukti = $p_buknin.$ppilih_nobukti."/".$mbulan."/".$pthnini;
        
        
        $query = "SELECT * FROM dbmaster.t_setup_bukti WHERE bulantahun='$pbukti_periode'";
        $showkan= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($showkan);
        if ($ketemu==0){
            mysqli_query($cnmy, "INSERT INTO dbmaster.t_setup_bukti (bulantahun, $p_fieldno)VALUES('$pbukti_periode', '$ppilih_nobukti')");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }else{
            mysqli_query($cnmy, "UPDATE dbmaster.t_setup_bukti SET $p_fieldno='$ppilih_nobukti' WHERE bulantahun='$pbukti_periode'");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }
        
        $query = "UPDATE $dbname.t_suratdana_bank SET nobukti='$pnobukti' WHERE idinputbank='$kodenya'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
    
    mysqli_close($cnmy);
    
    if ($act=="input")
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=tambahbaru');
    else
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
  
    
?>
