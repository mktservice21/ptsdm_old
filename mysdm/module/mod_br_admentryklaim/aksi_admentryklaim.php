<?php

session_start();

    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    $dbname = "hrd";
    $dbname2 = "dbmaster";
    
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    
// Hapus 
if ($module=='bgtadmentrybrklaim' AND $act=='hapus')
{
    
    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        mysqli_close($cnmy);
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }
    
    $kodenya=$_GET['id'];
    $kethapus= $_GET['kethapus'];
    if ($kethapus=="null") $kethapus="";
    
        $sql = "insert into $dbname2.backup_klaim 
               SELECT * FROM $dbname.klaim WHERE klaimId='$kodenya' LIMIT 1";
        mysqli_query($cnmy, $sql);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    
        $sql = "insert into $dbname.klaim_reject(klaimId, KET, IDREJECT, TGLREJECT)values"
                . "('$kodenya', '$kethapus', '$_SESSION[IDCARD]', NOW())";
        mysqli_query($cnmy, $sql);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
        
        //delete
        mysqli_query($cnmy, "DELETE FROM $dbname.klaim WHERE klaimId='$kodenya' LIMIT 1");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
    mysqli_close($cnmy);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='bgtadmentrybrklaim' AND $act=='updateperiode')
{
    
    $puserid=$_POST['e_idinputuser'];
    $pcardid=$_POST['e_idcarduser'];
    
    if (empty($puserid)) {
        $puserid="";
        if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

        if (empty($puserid)) {
            //mysqli_close($cnmy);
            //echo "ANDA HARUS LOGIN ULANG...";
            //exit;
        }
    }
    $kodenya=$_POST['e_id'];
    $pbln=$_POST['e_bulan'];
    $pperiod1=$_POST['e_periode1'];
    $pperiod2=$_POST['e_periode2'];
    
    $pbulan="0000-00-00";
    $pperiode1="0000-00-00";
    $pperiode2="0000-00-00";
    
    if (!empty($pbln)) $pbulan= date("Y-m-01", strtotime($pbln));
    if (!empty($pperiod1)) $pperiode1= date("Y-m-d", strtotime($pperiod1));
    if (!empty($pperiod2)) $pperiode2= date("Y-m-d", strtotime($pperiod2));
    
    echo "ID : $kodenya ... Bulan : $pbulan, periode $pperiode1 - $pperiode2";
    
    if (!empty($kodenya)) {
        $query = "update $dbname.klaim set bulan='$pbulan', periode1='$pperiode1', periode2='$pperiode2' WHERE klaimId='$kodenya' LIMIT 1";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    }
    
    mysqli_close($cnmy);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    exit;
}
elseif ($module=='bgtadmentrybrklaim')
{
    $puserid=$_POST['e_idinputuser'];
    $pcardid=$_POST['e_idcarduser'];
    
    if (empty($puserid)) {
        $puserid="";
        if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

        if (empty($puserid)) {
            mysqli_close($cnmy);
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }
    }
    
    
    
    $kodenya=$_POST['e_id'];
    $pdate = $_POST['e_tglinput'];
    $ptgltras="0000-00-00";
    $pbln=$_POST['e_bulan'];
    $pperiod1=$_POST['e_periode1'];
    $pperiod2=$_POST['e_periode2'];
    $pkaryawan=$_POST['e_idkaryawan'];
    $pdist=$_POST['e_iddist'];
    $pdivpengajuan=$_POST['cb_divpengajuan'];
    $pregion=$_POST['cb_region'];
    $paktivitas1=$_POST['e_aktivitas'];
    $paktivitas2=$_POST['e_aktivitas2'];
    $pnppn=$_POST['e_jmlppn'];
    $pnpph=$_POST['e_jmlpph'];
    
    $pdpptot_rl=$_POST['e_txttotreal'];
    $pnppnrp_rl=$_POST['e_txtppnreal'];
    $pnpphrp_rl=$_POST['e_txtpphreal'];
    $pbulat_rl=$_POST['e_txtbulatreal'];
    $pgtotal_rl=$_POST['e_txtgrdreal'];
    $ptotkuranglebih=$_POST['e_jmlkuranglebih'];
    $pketkurleb=$_POST['e_ketkuranglebih'];
    
    $pjnspajak="Y";
    
    
    $pdate01 = str_replace('/', '-', $pdate);
    $ptglinput= date("Y-m-d", strtotime($pdate01));
    $pbulan= date("Y-m-01", strtotime($pbln));
    $pperiode1= date("Y-m-d", strtotime($pperiod1));
    $pperiode2= date("Y-m-d", strtotime($pperiod2));
    
    $pcoa = "755-31";
    if ($pdivpengajuan=="EAGLE") $pcoa = "751-31";
    elseif ($pdivpengajuan=="PIGEO") $pcoa = "752-31";
    elseif ($pdivpengajuan=="PEACO") $pcoa = "753-31";
    elseif ($pdivpengajuan=="OTC") $pcoa = "754-31";
    elseif ($pdivpengajuan=="CAN") $pcoa = "755-31";
    
    $pdivisi="CAN";
    if ($pdivpengajuan=="EAGLE") $pdivisi = "EAGLE";
    elseif ($pdivpengajuan=="PIGEO") $pdivisi = "PIGEO";
    elseif ($pdivpengajuan=="PEACO") $pdivisi = "PEACO";
    elseif ($pdivpengajuan=="OTC") $pdivisi = "OTC";
    
    if (!empty($paktivitas1)) $paktivitas1 = str_replace("'", " ", $paktivitas1);
    if (!empty($paktivitas2)) $paktivitas2 = str_replace("'", " ", $paktivitas2);
    if (!empty($pketkurleb)) $pketkurleb = str_replace("'", " ", $pketkurleb);
    
    if (empty($pnppn)) $pnppn=0;
    if (empty($pnpph)) $pnpph=0;
    
    if (empty($pdpptot_rl)) $pdpptot_rl=0;
    if (empty($pnppnrp_rl)) $pnppnrp_rl=0;
    if (empty($pnpphrp_rl)) $pnpphrp_rl=0;
    if (empty($pbulat_rl)) $pbulat_rl=0;
    if (empty($pgtotal_rl)) $pgtotal_rl=0;
    if (empty($ptotkuranglebih)) $ptotkuranglebih=0;
    
    $pnppn=str_replace(",","", $pnppn);
    $pnpph=str_replace(",","", $pnpph);
    
    $pdpptot_rl=str_replace(",","", $pdpptot_rl);
    $pnppnrp_rl=str_replace(",","", $pnppnrp_rl);
    $pnpphrp_rl=str_replace(",","", $pnpphrp_rl);
    $pbulat_rl=str_replace(",","", $pbulat_rl);
    $pgtotal_rl=str_replace(",","", $pgtotal_rl);
    $ptotkuranglebih=str_replace(",","", $ptotkuranglebih);
    
    if ((DOUBLE)$pnppn==0 AND (DOUBLE)$pnpph==0) $pjnspajak="N";
    
    $pblnsusulan=$_POST['e_bulansusulan'];
    if (!empty($pblnsusulan)) $pblnsusulan= date("Y-m-01", strtotime($pblnsusulan));
    else $pblnsusulan="0000-00-00";
    
    $pnmrealisasi=$_POST['e_realisasi'];
    if (!empty($pnmrealisasi)) $pnmrealisasi = str_replace("'", " ", $pnmrealisasi);
    
    $pnmpengusaha=$_POST['e_kenapajak'];
    $pnoseri=$_POST['e_noserifp'];
    $ptglfp="0000-00-00";
    if (!empty($_POST['e_tglpajak'])) {
        $datepjk=$_POST['e_tglpajak'];
        $datepajak = str_replace('/', '-', $datepjk);
        $ptglfp= date("Y-m-d", strtotime($datepajak));
    }
    
    
    $pidcabang=  getfieldcnmy("select distinct iCabangId as lcfields from dbmaster.karyawan where karyawanId='$pkaryawan'");
    if (empty($pidcabang)) $pidcabang="0000000001";
    //selain OTC
    $pwilayah="01";
    $pcabwil=  substr($pidcabang, 7,3);
    if ($pidcabang=="0000000001")
        $pwilayah="01";
    else{
        $reg=  getfieldcnmy("select distinct region as lcfields from dbmaster.icabang where iCabangId='$pidcabang'");
        if ($reg=="B")
            $pwilayah="02";
        else
            $pwilayah="03";
    }
    
    $pwilgabungan=$pwilayah."-".$pcabwil;
    
    
    $pbolehsimpan=false;
    foreach ($_POST['chk_kodeid'] as $no_brid) {
        $pdet_rpklaim= $_POST['e_txtrpklaim'][$no_brid];
        $pdet_rpreal= $_POST['e_txtrpreal'][$no_brid];
        $pdet_rptolakan= $_POST['e_txtrptolak'][$no_brid];
        $pdet_notes= $_POST['e_txtnotes'][$no_brid];
        $pdet_nmcabang= $_POST['e_txtnmcab'][$no_brid];
        
        if (empty($pdet_rpklaim)) $pdet_rpklaim=0;
        if (empty($pdet_rpreal)) $pdet_rpreal=0;
        if (empty($pdet_rptolakan)) $pdet_rptolakan=0;
        
        if (!empty($pdet_notes)) $pdet_notes = str_replace("'", " ", $pdet_notes);
        
        $pdet_rpsusul= $_POST['e_txtrpsusul'][$no_brid];
        $pdet_notesssl= $_POST['e_txtsusul'][$no_brid];
        
        if (empty($pdet_rpsusul)) $pdet_rpsusul=0;
        if (!empty($pdet_notesssl)) $pdet_notesssl = str_replace("'", " ", $pdet_notesssl);
        
        
        
        if ((DOUBLE)$ptotkuranglebih==0 AND (DOUBLE)$pdet_rpklaim==0 AND (DOUBLE)$pdet_rpreal==0 AND (DOUBLE)$pdet_rptolakan==0 AND empty($pdet_notes) AND (DOUBLE)$pdet_rpsusul==0) {
        }else{
            $pbolehsimpan=true;
        }
    }
    
    
    
    if ($pbolehsimpan==true) {
        if ($act=='input') {
            
            $sql=  mysqli_query($cnmy, "select max(RIGHT(klaimId,9)) as NOURUT from dbmaster.t_setup");
            $ketemu=  mysqli_num_rows($sql);
            $awal=9; $urut=1; $kodenya=""; $periode=date('Ymd');
            if ($ketemu>0){
                $o=  mysqli_fetch_array($sql);
                $urut=$o['NOURUT']+1;
                $jml=  strlen($urut);
                $awal=$awal-$jml;
                $kodenya="D".str_repeat("0", $awal).$urut;
            }

            mysqli_query($cnmy, "UPDATE dbmaster.t_setup SET klaimId='$kodenya'");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

            $sql=  mysqli_query($cnmy, "select klaimId from $dbname.klaim where klaimId='$kodenya'");
            $ketemu=  mysqli_num_rows($sql);
            if ($ketemu>0){
                echo "Kode : $kodenya, sudah ada";
                mysqli_close($cnmy);
                exit;
            }
            
        }
        
        if (empty($kodenya)){
            echo "ID kosong, ulang lagi....";
            mysqli_close($cnmy);
            exit;
        }
    }
    
    $padaqueryinsert=false;
    unset($pinsert_data_detail);//kosongkan array
    foreach ($_POST['chk_kodeid'] as $no_brid) {
        $pdet_rpklaim= $_POST['e_txtrpklaim'][$no_brid];
        $pdet_rpreal= $_POST['e_txtrpreal'][$no_brid];
        $pdet_rptolakan= $_POST['e_txtrptolak'][$no_brid];
        $pdet_notes= $_POST['e_txtnotes'][$no_brid];
        $pdet_nmcabang= $_POST['e_txtnmcab'][$no_brid];
        
        if (empty($pdet_rpklaim)) $pdet_rpklaim=0;
        if (empty($pdet_rpreal)) $pdet_rpreal=0;
        if (empty($pdet_rptolakan)) $pdet_rptolakan=0;
        
        if (!empty($pdet_notes)) $pdet_notes = str_replace("'", " ", $pdet_notes);
        
        
        $pperiodesusulan="0000-00-00";
        if (!empty($pblnsusulan) AND $pblnsusulan<>"0000-00-00") $pperiodesusulan=$pblnsusulan;
        
        $pdet_rpsusul= $_POST['e_txtrpsusul'][$no_brid];
        $pdet_notesssl= $_POST['e_txtsusul'][$no_brid];
        
        if (empty($pdet_rpsusul)) $pdet_rpsusul=0;
        if (!empty($pdet_notesssl)) $pdet_notesssl = str_replace("'", " ", $pdet_notesssl);
        else {
            if (!empty($pketkurleb) AND (DOUBLE)$pdet_rpsusul<>0) {
                $pdet_notesssl=$pketkurleb;
            }
        }
        
        if ((DOUBLE)$pdet_rpsusul==0) {
            $pperiodesusulan="0000-00-00";
            //$pdet_notesssl="";
        }
        
        
        if ((DOUBLE)$pdet_rpklaim==0 AND (DOUBLE)$pdet_rpreal==0 AND (DOUBLE)$pdet_rptolakan==0 AND empty($pdet_notes) AND (DOUBLE)$pdet_rpsusul==0) {
        }else{
            
            
            $pdet_rpklaim=str_replace(",","", $pdet_rpklaim);
            $pdet_rpreal=str_replace(",","", $pdet_rpreal);
            $pdet_rptolakan=str_replace(",","", $pdet_rptolakan);
            
            $pdet_rpsusul=str_replace(",","", $pdet_rpsusul);
            
            $pbolehsimpan=true;
            $padaqueryinsert=true;
            
            $pinsert_data_detail[] = "('$kodenya', '$no_brid', '$pdet_nmcabang', '$pdet_rpklaim', '$pdet_rpreal', '$pdet_rptolakan', '$pdet_notes', '$pdet_rpsusul', '$pperiodesusulan', '$pdet_notesssl')";
            
            //echo "$pdet_nmcabang : $pdet_rpklaim - $pdet_rpreal - $pdet_rptolakan - $pdet_notes<br/>";
        }
        
    }
    
    
    if ($pbolehsimpan==true) {
        
        if ($act=='input') {

            $query = "INSERT INTO dbttd.klaim_ttd(klaimId)VALUES('$kodenya')";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }


            $query="insert into $dbname.klaim (klaimId, tgl, karyawanid, distid, pengajuan, "
                    . " aktivitas1, aktivitas2, jumlah, realisasi1, DIVISI, COA4, KODEWILAYAH, user1, "
                    . " region, bulan, periode1, periode2, jmlkuranglebih, ketkuranglebih, blnkuranglebih)values"
                    . "('$kodenya', '$ptglinput', '$pkaryawan', '$pdist', '$pdivpengajuan', "
                    . " '$paktivitas1', '$paktivitas2', '$pgtotal_rl', '$pnmrealisasi', '$pdivisi', '$pcoa', '$pwilgabungan', '$puserid', "
                    . " '$pregion', '$pbulan', '$pperiode1', '$pperiode2', '$ptotkuranglebih', '$pketkurleb', '$pblnsusulan')";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        }
        
        if ($act=='input') {
            $pimgttd=$_POST['txtgambar'];
            $query = "update dbttd.klaim_ttd set gambar='$pimgttd' WHERE klaimId='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan." EDIT TTD ID $kodenya: "; mysqli_close($cnmy); exit; }
        }
        
        $query_delete_detail="DELETE FROM $dbname.klaim_d WHERE klaimId='$kodenya'";
        mysqli_query($cnmy, $query_delete_detail); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
        if ($padaqueryinsert==true) {
            $query_detail="INSERT INTO $dbname.klaim_d (klaimId, idcab, nm_cab, jumlah1, jumlah2, jumlah3, notes, jumlahsusulan, periodesusulan, notes_susulan) VALUES ".implode(', ', $pinsert_data_detail);
            mysqli_query($cnmy, $query_detail); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan." EDIT ID $kodenya: "; mysqli_close($cnmy); exit; }
        }
        
        $query = "update $dbname.klaim set tgl='$ptglinput',
                 aktivitas1='$paktivitas1',
                 aktivitas2='$paktivitas2',
                 jumlah='$pgtotal_rl',
                 jmlkuranglebih='$ptotkuranglebih',
                 ketkuranglebih='$pketkurleb',
                 realisasi1='$pnmrealisasi',
                 karyawanid='$pkaryawan',
                 tgltrans='$ptgltras',
                 user1='$puserid',
                 distid='$pdist', "
                . " DIVISI='$pdivisi', COA4='$pcoa', pengajuan='$pdivpengajuan', "
                . " region='$pregion', bulan='$pbulan', periode1='$pperiode1', periode2='$pperiode2', "
                . " KODEWILAYAH='$pwilgabungan', "
                . " blnkuranglebih='$pblnsusulan' WHERE klaimId='$kodenya' LIMIT 1";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    
        
        $pjnspph="pph23";
        $prpdpp=0;
        if ($pjnspajak!="Y") {
            $pnmpengusaha="";
            $pnoseri="";
            $ptglfp="0000-00-00";
            $prpdpp=0;
            $pnppn=0;
            $pnppnrp_rl=0;
            $pjnspph="";
            $pnpph=0;
            $pnpphrp_rl=0;
            $pbulat_rl=0;
        }


        $query = "update $dbname.klaim set pajak='$pjnspajak', nama_pengusaha='$pnmpengusaha', noseri='$pnoseri',"
                . " tgl_fp='$ptglfp', dpp='$pdpptot_rl', ppn='$pnppn', ppn_rp='$pnppnrp_rl', "
                . " pph_jns='$pjnspph', pph='$pnpph', pph_rp='$pnpphrp_rl', pembulatan='$pbulat_rl' WHERE klaimId='$kodenya' LIMIT 1";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
        
        
        mysqli_close($cnmy);
        
    //if ($act=='input') header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=tambahbaru');
    //else 
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
    
        exit;
        
    }
    
    mysqli_close($cnmy);
    /*
    echo "$puserid - $pcardid, $kodenya, $ptglinput, $pbulan ($pperiode1 - $pperiode2) "
            . " $pkaryawan, $pdist, $pdivpengajuan ($pcoa - $pdivisi)<br/>"
            . " $paktivitas1 & $paktivitas2, Reg : $pregion <br/>"
            . "Pajak : $pjnspajak<br/>PPn : $pnppn%, PPN RP. : $pnppnrp_rl<br/>"
            . "PPh : $pnpph%, PPH RP. : $pnpphrp_rl<br/>"
            . "Pembulatan : $pbulat_rl<br/>"
            . "Grand Total : $pgtotal_rl<br/>tgltrans : $ptgltras<br/>kurleb : $ptotkuranglebih<br/>"
            . "Pengusaha : $pnmpengusaha, NoSeri : $pnoseri, Tgl FP : $ptglfp<br/>Real : $pnmrealisasi, WIL : $pwilgabungan"; mysqli_close($cnmy); exit;
     * 
     */
     
     
}
    
?>