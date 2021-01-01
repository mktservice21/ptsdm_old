<?php
    date_default_timezone_set('Asia/Jakarta');
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/koneksimysqli_it.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    $cnmy=$cnmy;
    $dbname = "dbmaster";
    
    $puserid=$_SESSION['IDCARD'];
    
    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN...!!!";
        exit;
    }
    
    //echo $act; exit;
if ($module=="finprosesbrcab" AND $act=="input") {
    
    
    mysqli_query($cnmy, "DELETE FROM dbmaster.tmp_t_br_cab WHERE cardid='$puserid'");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $sql=  mysqli_query($cnit, "select max(brId) as NOURUT from dbmaster.t_setup");
    $ketemu=  mysqli_num_rows($sql);
    $awal=10; $urut=1; $kodenya=""; $periode=date('Ymd');
    if ($ketemu>0){
        $o=  mysqli_fetch_array($sql);
        $urut=(double)$o['NOURUT'];
    }
    $nharussave=false;
    $f_nobr="";
    foreach ($_POST['chkbox_br'] as $nobrinput) {
        if (!empty($nobrinput)) {
            $f_nobr .="'".$nobrinput."',";
            
            $urut++;
            $jml=  strlen($urut);
            $nawal=(double)$awal-(double)$jml;
            $kodenya=str_repeat("0", $nawal).$urut;
            
            /*
            $query ="INSERT INTO dbmaster.tmp_t_br_cab (bridinputcab, brid, tgl, kode, coa4, dokterid, aktivitas, 
                    ccyid, jumlah, divisi, icabangid, karyawanid, karyawanid2,
                    pajak, nama_pengusaha, noseri, tgl_fp, dpp, ppn, ppn_rp, pph_jns, 
                    pph, pph_rp, pembulatan, jasa_rp, materai_rp, jenis_dpp,
                    noseri_pph, tgl_fp_pph, dpp_pph, cardid, idcabang)
                    select bridinputcab, '$kodenya' as brid, tgl, kode, coa4, dokterid, aktivitas, 
                    ccyid, jumlah, divisi, icabangid, karyawanid, karyawanid2,
                    pajak, nama_pengusaha, noseri, tgl_fp, dpp, ppn, ppn_rp, pph_jns, 
                    pph, pph_rp, pembulatan, jasa_rp, materai_rp, jenis_dpp,
                    noseri_pph, tgl_fp_pph, dpp_pph, '$puserid' as cardid, idcabang 
                    from dbmaster.t_br_cab WHERE bridinputcab='$nobrinput'";
            */
            
            $query ="INSERT INTO dbmaster.tmp_t_br_cab (bridinputcab, brid, tgl, kode, coa4, dokterid, aktivitas, 
                    ccyid, divisi, icabangid, karyawanid, karyawanid2,
                    pajak, nama_pengusaha, noseri, tgl_fp, dpp, ppn, ppn_rp, pph_jns, 
                    pph, pph_rp, pembulatan, jasa_rp, materai_rp, jenis_dpp,
                    noseri_pph, tgl_fp_pph, dpp_pph, cardid, idcabang, jumlah)
                    select a.bridinputcab, '$kodenya' as brid, a.tgl, a.kode, a.coa4, a.dokterid, a.aktivitas, 
                    a.ccyid, a.divisi, a.icabangid, a.karyawanid, a.karyawanid2,
                    a.pajak, a.nama_pengusaha, a.noseri, a.tgl_fp, a.dpp, a.ppn, a.ppn_rp, a.pph_jns, 
                    a.pph, a.pph_rp, a.pembulatan, a.jasa_rp, a.materai_rp, a.jenis_dpp,
                    a.noseri_pph, a.tgl_fp_pph, a.dpp_pph, '$puserid' as cardid, a.idcabang, sum(b.rp) as jumlah 
                    from dbmaster.t_br_cab a JOIN dbmaster.t_br_cab1 b on a.bridinputcab=b.bridinputcab 
                    WHERE a.bridinputcab='$nobrinput' 
                    group by 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $nharussave=true;
        }
    }
    
    if (!empty($kodenya) AND !empty($f_nobr) AND $nharussave==true) {
        $f_nobr="(".substr($f_nobr, 0, -1).")";
        
        //mysqli_query($cnit, "UPDATE dbmaster.t_setup SET brId='$kodenya'");
        //$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "INSERT INTO hrd.br0 (brid, tgl, kode, coa4, dokterid, aktivitas1, 
            ccyid, jumlah, divprodid, icabangid, karyawanid, karyawanI2,
            pajak, nama_pengusaha, noseri, tgl_fp, dpp, ppn, ppn_rp, pph_jns, 
            pph, pph_rp, pembulatan, jasa_rp, materai_rp, jenis_dpp,
            noseri_pph, tgl_fp_pph, dpp_pph, idcabang)
            SELECT brid, CURRENT_DATE() as tgl, kode, coa4, dokterid, aktivitas, 
                ccyid, jumlah, divisi, icabangid, karyawanid, karyawanid2,
                pajak, nama_pengusaha, noseri, tgl_fp, dpp, ppn, ppn_rp, pph_jns, 
                pph, pph_rp, pembulatan, jasa_rp, materai_rp, jenis_dpp,
                noseri_pph, tgl_fp_pph, dpp_pph, idcabang 
                from dbmaster.tmp_t_br_cab WHERE cardid='$puserid' AND bridinputcab IN $f_nobr";
        //mysqli_query($cnit, $query);
        //$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query ="UPDATE dbmaster.t_br_cab a JOIN dbmaster.tmp_t_br_cab b on a.bridinputcab=b.bridinputcab SET "
                . " a.brid=b.brid WHERE IFNULL(a.brid,'')='' AND a.bridinputcab IN $f_nobr AND b.cardid='$puserid'";
        //mysqli_query($cnmy, $query);
        //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    }
    
    mysqli_query($cnmy, "DELETE FROM dbmaster.tmp_t_br_cab WHERE cardid='$puserid'");
    mysqli_close($cnmy);
    mysqli_close($cnit);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
    exit;
    hapusdata:
        mysqli_query($cnmy, "DELETE FROM dbmaster.tmp_t_br_cab WHERE cardid='$puserid'");
        mysqli_close($cnmy);
        mysqli_close($cnit);
        echo "GAGAL PROSES...";
        
}elseif ($module=="finprosesbrcab" AND $act=="unapprove") {
    
    $f_nobr="";
    $f_inputnobr="";
    foreach ($_POST['chkbox_br'] as $nobrinput) {
        if (!empty($nobrinput)) {
            $f_nobr .="'".$nobrinput."',";
            
            $pinputbrid=$_POST['txtinputbrid'][$nobrinput];
            $f_inputnobr .="'".$pinputbrid."',";
            
            //echo "$nobrinput, $pinputbrid<br/>";
        }
    }
    
    if (!empty($f_nobr) AND !empty($f_inputnobr)) {
        $f_nobr="(".substr($f_nobr, 0, -1).")";
        $f_inputnobr="(".substr($f_inputnobr, 0, -1).")";
        
        $query = "UPDATE dbmaster.t_br_cab SET brid='' WHERE bridinputcab IN $f_nobr AND brid IN $f_inputnobr";
        //mysqli_query($cnmy, $query);
        //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Update ID : $nobrinput"; exit; }
        
        $query = "UPDATE hrd.br0 SET batal='Y', jumlah=0, jumlah1=0 WHERE brId IN $f_inputnobr";
        //mysqli_query($cnit, $query);
        //$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "Error Update ID : $nobrinput"; exit; }
        
    }
    
    mysqli_close($cnmy);
    mysqli_close($cnit);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
    
}elseif ($module=="finprosesbrcab" AND $act=="reject") {
    $f_nobr="";
    $palasanket=$_GET['ukethapus'];
    
    $pnmreject=$_SESSION['NAMALENGKAP'];
    
    $hari_ini = date("d F Y h:i:s");
    $palasanket="User : ".$pnmreject."  ".$hari_ini.", ".$palasanket;
    
    foreach ($_POST['chkbox_br'] as $nobrinput) {
        if (!empty($nobrinput)) {
            $f_nobr .="'".$nobrinput."',";
            //echo "$nobrinput<br/>";
        }
    }
    
    if (!empty($f_nobr)) {
        $f_nobr="(".substr($f_nobr, 0, -1).")";
        
        $query = "UPDATE dbmaster.t_br_cab SET stsnonaktif='Y', alasan_batal='$palasanket' WHERE bridinputcab IN $f_nobr";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Update ID : $nobrinput"; exit; }
        
    }
    
    mysqli_close($cnmy);
    mysqli_close($cnit);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
    
}elseif ($module=="finprosesbrcab" AND $act=="validatedata") {
    $pgbr="$puserid";
    $f_nobr="";
    foreach ($_POST['chkbox_br'] as $nobrinput) {
        if (!empty($nobrinput)) {
            $f_nobr .="'".$nobrinput."',";
            //echo "$nobrinput<br/>";
        }
    }
    
    if (!empty($f_nobr)) {
        $f_nobr="(".substr($f_nobr, 0, -1).")";
        
        $query = "UPDATE dbmaster.t_br_cab SET validate_date=NOW(), validate='$puserid', validate_gbr='$pgbr' WHERE bridinputcab IN $f_nobr AND IFNULL(tglissued,'')='' AND IFNULL(tglbooking,'')<>''";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Update validate ID : $nobrinput"; exit; }
        
    }
    
    
    mysqli_close($cnmy);
    mysqli_close($cnit);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
    
}elseif ($module=="finprosesbrcab" AND $act=="removevalidate") {
    
    $pnobrinput=$_GET['id'];

    if (!empty($pnobrinput)) {

        $query = "UPDATE dbmaster.t_br_cab SET validate_date=NULL, validate=NULL, validate_gbr=NULL WHERE bridinputcab='$pnobrinput' AND IFNULL(tglissued,'')=''";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error HAPUS ISSUED ID : $pnobrinput"; exit; }

    }
        
    mysqli_close($cnmy);
    mysqli_close($cnit);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
    
}
?>

