<?php
session_start();
if ($_GET['module']=="viewnomorspd") {
    include "../../config/koneksimysqli.php";

    $filterrbulan="";
    $ptgl=$_POST['utglspd'];
    if (!empty($ptgl)) {
        $ptglspd = date('Y-m', strtotime($ptgl));
        
        $filterrbulan=" AND ( DATE_FORMAT(tgl,'%Y-%m')='$ptglspd' OR DATE_FORMAT(tglspd,'%Y-%m')='$ptglspd' ) ";
    }
    
    
    echo "<option value='' selected>-- Pilihan --</option>";
    
    $query = "select nomor, SUM(jumlah) jumlah from dbmaster.t_suratdana_br WHERE IFNULL(stsnonaktif,'')<>'Y' "
            . "and IFNULL(pilih,'') = 'Y' AND IFNULL(nomor,'')<>'' $filterrbulan "
            . "GROUP BY 1 ORDER BY 1";
    
    $tampil= mysqli_query($cnmy, $query);
    
    while ($tr= mysqli_fetch_array($tampil)) {

        $pjmlspd=$tr['jumlah'];
        if (!empty($pjmlspd)) $pjmlspd=number_format($pjmlspd,0);
        $pnomorspd=$tr['nomor'];

        $pajsketjml = "$pnomorspd   &nbsp;&nbsp;&nbsp;&nbsp;    (Rp. $pjmlspd)";
        echo "<option value='$pnomorspd'>$pajsketjml</option>";
    }
    
}elseif ($_GET['module']=="viewnomorbrdivis") {
    include "../../config/koneksimysqli.php";
    
    $n_filterkaryawan="";
    $pses_grpuser=$_SESSION['GROUP'];
    $pses_divisi=$_SESSION['DIVISI'];
    $pses_idcard=$_SESSION['IDCARD'];
    if ($pses_grpuser=="1" OR $pses_grpuser=="24") {
    }else{
        if ($pses_divisi=="OTC") {
            $n_filterkaryawan=" AND divisi='OTC' ";
        }else{
            if ($pses_grpuser=="25") {
                
            }else{
                $n_filterkaryawan=" AND divisi<>'OTC' AND karyawanid='$pses_idcard' ";
            }
        }
    }
    
    
    $pnospd=$_POST['unomor'];
    
    $filterrbulan="";
    $tgl01=$_POST['utgl'];
    if (!empty($tgl01)) {
        $pbln= date("Y-m", strtotime($tgl01));
        
        $filterrbulan=" AND ( DATE_FORMAT(tgl,'%Y-%m')='$pbln' OR DATE_FORMAT(tglspd,'%Y-%m')='$pbln' ) ";
    }
    
    $filnospd="";
    if (!empty($pnospd)) $filnospd=" AND IFNULL(nomor,'')='$pnospd' ";
    $query = "select divisi, nodivisi, SUM(jumlah) jumlah from dbmaster.t_suratdana_br WHERE IFNULL(stsnonaktif,'')<>'Y' "
            . " AND IFNULL(nodivisi,'')<>'' $filnospd  $n_filterkaryawan "
            . " $filterrbulan"
            . "GROUP BY 1,2 ORDER BY 1,2";
    $tampil = mysqli_query($cnmy, $query);
    echo "<option value='' selected>-- Pilihan --</option>";
    
    while ($zs= mysqli_fetch_array($tampil)) {
        $pjumlah=$zs['jumlah'];
        if (!empty($pjumlah)) $pjumlah=number_format($pjumlah,0);
        $pnobrdiv=$zs['nodivisi'];
        $pdivisi=$zs['divisi'];
        if (empty($pdivisi)) $pdivisi= "ETHICAL";
        $pajsketjml = "$pnobrdiv   &nbsp;&nbsp;&nbsp;    (Rp. $pjumlah)";
        
        echo "<option value='$pnobrdiv'>$pajsketjml</option>";
    }
    
}elseif ($_GET['module']=="viewbrnoslip") {
    include "../../config/koneksimysqli.php";
    $pnodiv=$_POST['unodiv'];
    echo "<option value='' selected>-- Pilihan --</option>";
    
    $query = "select idinput from dbmaster.t_suratdana_br WHERE nodivisi='$pnodiv'";
    $tampil = mysqli_query($cnmy, $query);
    $z= mysqli_fetch_array($tampil);
    $nidinput=$z['idinput'];
    
    $query = "select brId, noslip, aktivitas1, jumlah From hrd.br0 where brId in 
            (select DISTINCT IFNULL(a.bridinput,'') bridinput from dbmaster.t_suratdana_br1 a WHERE a.idinput='$nidinput') order by noslip, brId";
    $tampil = mysqli_query($cnmy, $query);
    
    while ($zs= mysqli_fetch_array($tampil)) {
        $pbrid=$zs['brId'];
        $pnoslip=$zs['noslip'];
        $pket=$zs['aktivitas1'];
        
        $pjumlah=$zs['jumlah'];
        if (!empty($pjumlah)) $pjumlah=number_format($pjumlah,0);
        
        $pdataket = "$pbrid &nbsp; - &nbsp; $pnoslip   &nbsp;&nbsp;&nbsp;&nbsp;    (Rp. $pjumlah)   &nbsp;&nbsp;&nbsp;&nbsp; $pket";
        
        echo "<option value='$pbrid'>$pdataket</option>";
    }
    
}elseif ($_GET['module']=="viewnobuktispd") {
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_combo.php";
    
    $pnospd=$_POST['unospd'];
    $pnodivisi=$_POST['unodivisi'];
    
    $f_nodivisi="";
    if (!empty($pnodivisi)) {
        $f_nodivisi=" AND nodivisi='$pnodivisi' ";
    }
    
    $edit = mysqli_query($cnmy, "SELECT nobbm FROM dbmaster.t_suratdana_br WHERE IFNULL(stsnonaktif,'')<>'Y' AND IFNULL(nobbm,'')<>'' AND nomor='$pnospd' $f_nodivisi");
    $ketemu= mysqli_num_rows($edit);
    if ($ketemu>0) {
        $r    = mysqli_fetch_array($edit);
        $pnobukti=$r['nobbm'];
    }else{
        
        $tgl01 = str_replace('/', '-', $_POST['utgl']);

        $pblnini = date('m', strtotime($tgl01));
        $pthnini = date('Y', strtotime($tgl01));
        $pthnini_bln = date('Ym', strtotime($tgl01));
        $tno="1501";
        $query = "SELECT LTRIM(REPLACE(MAX(SUBSTRING_INDEX(nobbm, '/', 1)),'BBM','')) as nobbm FROM dbmaster.t_suratdana_br 
            WHERE IFNULL(stsnonaktif,'') <> 'Y' AND DATE_FORMAT(tglmasuk,'%Y%m')='$pthnini_bln'";
        $showkan= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($showkan);
        if ($ketemu>0){
            $sh= mysqli_fetch_array($showkan);
            if (!empty($sh['nobbm'])) { $tno=(INT)$sh['nobbm']+1; }
            if ((double)$tno==1) $tno="1501";
        }
        $mbulan=CariBulanHuruf($pblnini);
        $pnobukti = "BBM".$tno."/".$mbulan."/".$pthnini;
    
    }
    echo $pnobukti;
            
            
}elseif ($_GET['module']=="viewnobuktidivisi") {
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_combo.php";
    
    $pnospd=$_POST['unospd'];
    $pnodivisi=$_POST['unodivisi'];
        
    $tgl01 = str_replace('/', '-', $_POST['utgl']);

    $pblnini = date('m', strtotime($tgl01));
    $pthnini = date('Y', strtotime($tgl01));
    $pthnini_bln = date('Ym', strtotime($tgl01));
    
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
    
    
    echo $pnobukti;
    
}elseif ($_GET['module']=="cariprosesclosing") {
    include "../../config/koneksimysqli.php";
    $tgl01 = str_replace('/', '-', $_POST['utgl']);
    $tgl_msk = date('Ym', strtotime($tgl01));
    
    $caricls = mysqli_query($cnmy, "SELECT bulan FROM dbmaster.t_bank_saldo WHERE DATE_FORMAT(bulan,'%Y%m')='$tgl_msk'");
    $cl    = mysqli_fetch_array($caricls);
    $ntglclose="";
    if (!empty($cl['bulan'])) $ntglclose = date('d/m/Y', strtotime($cl['bulan']));
    
    mysqli_close($cnmy);

    echo $ntglclose;
    
}elseif ($_GET['module']=="viewjenisdebitkredit") {
    $pkodesub=$_POST['ukodesub'];
    $pd_spd_debker="D";
    
    if (!empty($pkodesub)) {
        $pd_spd_debker="K";
    }
    
    $pdebker_sel1="selected";
    $pdebker_sel2="";
    if ($pd_spd_debker=="K") {
        $pdebker_sel1="";
        $pdebker_sel2="selected";
    }
    echo "<option value='D' $pdebker_sel1>Debit</option>";
    echo "<option value='K' $pdebker_sel2>Kredit</option>";
    
}elseif ($_GET['module']=="viewcoapilihjenis") {
    include "../../config/koneksimysqli.php";
    $pkodesub=$_POST['ukodesub'];
    $pdebker=$_POST['udebker'];
    $pcoa="105-02";
    
    if (!empty($pkodesub)) {
        $nfiled=" ibank_coa_d as coa_jenis ";
        if ($pdebker=="K") $nfiled=" ibank_coa_k as coa_jenis ";
        $query = "select $nfiled FROM dbmaster.t_kode_spd WHERE subkode='$pkodesub'";
        $tampil_dk = mysqli_query($cnmy, $query);
        $dk= mysqli_fetch_array($tampil_dk);
        $pcoa=$dk['coa_jenis'];
    }
    
    $query = "select a.coa, b.NAMA4 FROM dbmaster.coa_dana_bank a JOIN "
            . " dbmaster.coa_level4 b on a.coa=b.COA4 order by a.coa";
    $tampil = mysqli_query($cnmy, $query);
    while ($z= mysqli_fetch_array($tampil)) {
        if ($z['coa']==$pcoa)
            echo "<option value='$z[coa]' selected>$z[coa] - $z[NAMA4]</option>";
        else
            echo "<option value='$z[coa]'>$z[coa] - $z[NAMA4]</option>";
    }
    
    mysqli_close($cnmy);
    
    
}elseif ($_GET['module']=="viewjenispilihsubjenis") {
    $pkodesub=$_POST['ukodesub'];
    $pjenis="5";
    if (!empty($pkodesub)) $pjenis="2";
    
    $pjns_sel1="selected";
    $pjns_sel2="";
    $pjns_sel5="";
    if ($pjenis=="2") $pjns_sel2="selected";
    if ($pjenis=="5") $pjns_sel5="selected";

    echo "<option value='1' $pjns_sel1>Advance</option>";
    echo "<option value='2' $pjns_sel2>Klaim</option>";
    echo "<option value='5' $pjns_sel5>Bank</option>";
                                                
}elseif ($_GET['module']=="xxxxxx") {
    
}else{
    
}
?>
