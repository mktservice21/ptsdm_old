<?php

session_start();

    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];

    $erropesan="error";
    $pketeksekusi="";
    $phapusinduk="";
    
    
// Hapus 
if ($pmodule=='entrybrrutinho' OR $pmodule=='entrybrrutinhodivchc')
{
    
    if ($pact=='hapus') {
        
        $puserid="";
        $pnamalengkap="";
        if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
        if (isset($_SESSION['NAMALENGKAP'])) $pnamalengkap=$_SESSION['NAMALENGKAP'];

        if (empty($puserid)) {
            mysqli_close($cnmy);
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }
        
        $kodenya=$_GET['id'];
        $kethapus= $_GET['kethapus'];
        if ($kethapus=="null") $kethapus="";

        if (!empty($kethapus)) $kethapus = str_replace("'", " ", $kethapus);
            
            include "../../../config/koneksimysqli.php";
            
            //hapus data
            mysqli_query($cnmy, "UPDATE dbmaster.t_brrutin0 SET stsnonaktif='Y', keterangan=CONCAT(IFNULL(keterangan,''),'$kethapus',', $pnamalengkap, ', NOW()) WHERE idrutin='$kodenya' LIMIT 1");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $pketeksekusi="error hapus data"; mysqli_close($cnmy); goto errorsimpan; }

            //mysqli_query($cnmy, "DELETE FROM dbimages.img_brrutin1 WHERE idrutin='$kodenya' LIMIT 1");

        mysqli_close($cnmy);
        $pketeksekusi="berhasil hapus data ID $kodenya";
        header('location:../../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&nmun='.$pidmenu.'&act=complete&iderror=hapusok&keteks='.$pketeksekusi);
        exit;
    
    }elseif ($pact=='ttdupdate') {
        
        $kodenya=$_POST['e_id'];
        $pimgttd=$_POST['txtgambar'];

        if (!empty($kodenya)) {
            include "../../../config/koneksimysqli.php";
            
            mysqli_query($cnmy, "UPDATE dbmaster.t_brrutin0 SET gambar='$pimgttd' WHERE idrutin='$kodenya' LIMIT 1");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $pketeksekusi="error update tanda tangan"; mysqli_close($cnmy); goto errorsimpan; }

            mysqli_close($cnmy);
            $pketeksekusi="berhasil update tanda tangan ID $kodenya";
            header('location:../../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&nmun='.$pidmenu.'&act=complete&iderror=updatettd&keteks='.$pketeksekusi);
        }
        exit;
    
    }elseif ($pact=='uploaddok') {
        
        $kodenya=$_POST['e_id'];
        if (!empty($kodenya)) {
            include "../../../config/koneksimysqli.php";
            include "../../../config/fungsi_image.php";

            $gambarnya=$_POST['e_imgconv'];

            if (!empty($gambarnya)) {
                mysqli_query($cnmy, "insert into dbimages.img_brrutin1 (idrutin, gambar2) values ('$kodenya', '$gambarnya')");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $pketeksekusi="error input gambar dokumen"; mysqli_close($cnmy); goto errorsimpan; }
            }

            mysqli_close($cnmy);
            $pketeksekusi="berhasil upload dokumen ID $kodenya";
            header('location:../../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&nmun='.$pidmenu.'&act=complete&iderror=uploaddok&keteks='.$pketeksekusi);
        }
        exit;
    
    }elseif ($pact=='hapusgambar') {
        
        $kodenya=$_GET['id'];
        $idgam="";
        if (isset($_GET['idgam'])) $idgam=$_GET['idgam'];

        if (!empty($kodenya) AND !empty($idgam)) {
            include "../../../config/koneksimysqli.php";
            
            mysqli_query($cnmy, "delete from dbimages.img_brrutin1 WHERE nourut='$idgam' and idrutin='$kodenya' LIMIT 1");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $pketeksekusi="error hapus gambar dokumen "; mysqli_close($cnmy); goto errorsimpan; }

            mysqli_close($cnmy);
            $pketeksekusi="berhasil hapus dokumen ID $kodenya";
            header('location:../../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&nmun='.$pidmenu.'&act=complete&iderror=hapusdok&keteks='.$pketeksekusi);
        }
        exit;
    
    }elseif ($pact=='input' OR $pact=='update') {
        
        include "../../../config/koneksimysqli.php";
        include "../../../config/fungsi_sql.php";
        
        $puserid=$_POST['e_idinputuser'];
        $pcardid=$_POST['e_idcarduser'];

        if (empty($puserid)) {
            $puserid="";
            if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
            if (isset($_SESSION['IDCARD'])) $pcardid=$_SESSION['IDCARD'];

            if (empty($puserid)) {
                mysqli_close($cnmy);
                $pketeksekusi="ANDA HARUS LOGIN ULANG...";
                goto errorsimpan;
                exit;
            }
        }
        
        $ppengajuan_divisi="ETH";
        $pdivisiid=$_POST['e_divisiid'];
        if (empty($pdivisiid)) {
            mysqli_close($cnmy);
            $pketeksekusi="Gagal Simpan, divisi tidak ada...";
            goto errorsimpan;
            exit;
        }
        
        if ($pdivisiid=="OTC" OR $pdivisiid=="CHC" OR $pdivisiid=="OT") {
            $ppengajuan_divisi="HO";
        }
        
        $pidkaryawan=$_POST['e_idkaryawan'];
        if (empty($pidkaryawan)) {
            mysqli_close($cnmy);
            $pketeksekusi="Gagal Simpan, karyawan yang diinput kosong...";
            goto errorsimpan;
            exit;
        }
        
        $query = "select nama as lcfields from hrd.karyawan WHERE karyawanid='$pidkaryawan'";
        $tampilk= mysqli_query($cnmy, $query);
        $rowk= mysqli_fetch_array($tampilk);
        $pnamakaryawan=$rowk['lcfields'];
        
        $kodenya=$_POST['e_id'];
        $pjbtid=$_POST['e_jabatanid'];
        $pidcabang=$_POST['e_cabangid'];
        $pareaid=$_POST['e_areaid'];
        $pidnopol=$_POST['e_nopolid'];
        
        
        if (empty($pjbtid)) {
            
            $query = "select karyawanId as karyawanid, nama, iCabangId as icabangid, areaId as areaid, jabatanId as jabatanid, divisiId as divisiid "
                    . " from hrd.karyawan WHERE karyawanid='$pidkaryawan'";
            $tampilk= mysqli_query($cnmy, $query);
            $row= mysqli_fetch_array($tampilk);
            $pidcabang=$row['icabangid'];
            $pareaid=$row['areaid'];
            $pjbtid=$row['jabatanid'];
            $ndivisi_p=$row['divisiid'];
            if ($ndivisi_p=="HO" AND empty($pidcabang)) {
                $pidcabang="0000000001";//ETH HO
                $pareaid="0000000001";//ETH HO
            }
            $ndivisi_p="";// takut dipakai dibawah, makanya dihilangkan
            
        }
        
        if (empty($pareaid) AND $pidcabang=="0000000001") {
            $pareaid="0000000001";//ETH HO
        }
        
        if (empty($pidnopol)) {
            $query = "select nopol as lcfields from dbmaster.t_kendaraan_pemakai WHERE "
                    . " karyawanid='$pidkaryawan' AND IFNULL(stsnonaktif,'')<>'Y' order by tglawal desc LIMIT 1";
            $tampiln= mysqli_query($cnmy, $query);
            $rown= mysqli_fetch_array($tampiln);
            $pidnopol=$rown['lcfields'];
        }
        
        //echo "Kry : $pidkaryawan ($pnamakaryawan), Jbt : $pjbtid, Cab : $pidcabang, area : $pareaid, nopol : $pidnopol"; mysqli_close($cnmy); exit;
        
        
        
        
        $pbln_c=$_POST['e_bulan']; 
        $pkdperiode=$_POST['e_periode'];
        $ptgl_pl1=$_POST['e_periode01'];
        $ptgl_pl2=$_POST['e_periode02'];
        $pnotes=$_POST['e_ket'];
        $ptotalrp=$_POST['e_totalsemua'];
        
        
        $patasan=$_POST['e_atasan'];
        $patasan1="";//$_POST['e_atasan'];
        $patasan2="";//$_POST['e_atasan'];
        $patasan3="";//$_POST['e_atasan'];
        $patasan4=$_POST['e_atasan'];
    
        if (empty($pbln_c)) {
            mysqli_close($cnmy);
            $pketeksekusi="Gagal Simpan, Bulan kosong....";
            goto errorsimpan;
            exit;
        }
    
        if (empty($pkdperiode)) {
            mysqli_close($cnmy);
            $pketeksekusi="Gagal Simpan, Kode periode kosong....";
            goto errorsimpan;
            exit;
        }
        

        $pbln= date("Y-m-01", strtotime($pbln_c));
        $pcari_bln= date("Ym", strtotime($pbln_c));
        $pthnbln= date("ym", strtotime($pbln_c));//untuk id 2 digit
        $pthnbln_inp= date("yn", strtotime($pbln_c));//untuk id 1 digit
        
        $ptgl_pl1 = str_replace('/', '-', $ptgl_pl1);
        $ptgl_pl2 = str_replace('/', '-', $ptgl_pl2);
        $ptgl1= date("Y-m-d", strtotime($ptgl_pl1));
        $ptgl2= date("Y-m-d", strtotime($ptgl_pl2));

        $pbln_pl1=date("Ym", strtotime($ptgl_pl1));
        $pbln_pl2=date("Ym", strtotime($ptgl_pl2));
        
        
        if ( ($pcari_bln<>$pbln_pl1) OR ($pcari_bln<>$pbln_pl2) ) {
            mysqli_close($cnmy);
            $pketeksekusi="Gagal Simpan, Bulan dan Periode tidak sesuai....";
            goto errorsimpan;
            exit;
        }
    
        
        if (!empty($pnotes)) $pnotes = str_replace("'", " ", $pnotes);
        
        if (empty($ptotalrp)) $ptotalrp=0;
        $ptotalrp=str_replace(",","", $ptotalrp);
    
        
        $pwilayah="01";
        $pcabwil=  substr($pidcabang, 7,3);
        if ($pidcabang=="0000000001")
            $pwilayah="01";
        else{
            
            $query = "select region as lcfields from dbmaster.icabang where iCabangId='$pidcabang'";
            $tampilc= mysqli_query($cnmy, $query);
            $rowc= mysqli_fetch_array($tampilc);
            $reg=$rowc['lcfields'];
            
            if ($pdivisiid=="OTC") {
                if ($reg=="B")
                    $pwilayah="04";
                else
                    $pwilayah="05";
            }else{
                if ($reg=="B")
                    $pwilayah="02";
                else
                    $pwilayah="03";
            }
        }

        $pwilgabungan=$pwilayah."-".$pcabwil;
    
        
        $pbolehsimpan=false;
        foreach ($_POST['chk_kodeid'] as $no_brid) {
            $pdet_rptotal= $_POST['e_txttotalrp'][$no_brid];

            if (empty($pdet_rptotal)) $pdet_rptotal=0;
            $pdet_rptotal=str_replace(",","", $pdet_rptotal);

            if ((DOUBLE)$pdet_rptotal>0) {
                $pbolehsimpan=true;
                //echo "$pdet_rptotal<br/>";
            }
        }
        
        if ($pbolehsimpan == false) {
            mysqli_close($cnmy);
            $pketeksekusi="Gagal Simpan, Tidak ada Detail data yang disimpan....";
            goto errorsimpan;
            exit;
        }
    
        
        
        if ($pact=="input") {
            
            //id rutin dari urutan
            $sql=  mysqli_query($cnmy, "select MAX(RIGHT(idrutin,7)) as NOURUT from dbmaster.t_brrutin0");
            $ketemu=  mysqli_num_rows($sql);
            $awal=7; $urut=1; $kodenya=""; $periode=date('Ymd');
            if ($ketemu>0){
                $o=  mysqli_fetch_array($sql);
                $urut=$o['NOURUT']+1;
                $jml=  strlen($urut);
                $awal=$awal-$jml;
                $kodenya="BRT".str_repeat("0", $awal).$urut;
            }
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $pketeksekusi="error mendapatkan nourut"; mysqli_close($cnmy); goto errorsimpan; }
            
            
            
            
            //id rutin dari periode
            /*
            $sql=  mysqli_query($cnmy, "select rutin as NOURUT from dbmaster.t_setup_periode where thnbln='$pthnbln'");
            $ketemu=  mysqli_num_rows($sql);
            $awal=4; $nurut=1; $kodenya=""; $padaurut=false;
            if ($ketemu>0){
                $o=  mysqli_fetch_array($sql);
                if (empty($o['NOURUT'])) $o['NOURUT']=0;
                $nurut=$o['NOURUT']+1;
                $padaurut=true;
            }else{
                $nurut=1;
            }
            $jml=  strlen($nurut);
            $awal=$awal-$jml;
            $kodenya="BRT".$pthnbln_inp."".str_repeat("0", $awal).$nurut;

            if ($padaurut==false) {
                mysqli_query($cnmy, "INSERT INTO dbmaster.t_setup_periode (thnbln, rutin)VALUES('$pthnbln', '0')");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $pketeksekusi="error input nourut"; mysqli_close($cnmy); goto errorsimpan; }
            }

            mysqli_query($cnmy, "UPDATE dbmaster.t_setup_periode SET rutin=IFNULL(rutin,0)+1 WHERE thnbln='$pthnbln'");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $pketeksekusi="error update nourut"; mysqli_close($cnmy); goto errorsimpan; }
            */
            
            
        }
        
        
        if (empty($kodenya)) {
            mysqli_close($cnmy);
            $pketeksekusi="Gagal Simpan, Tidak bisa membuat ID RUTIN, ulangi lagi input....";
            goto errorsimpan;
            exit;
        }
        
        
        
        $pttotal=0;
        unset($pinsert_data_detail);//kosongkan array
        foreach ($_POST['chk_kodeid'] as $no_brid) {
            
            $pdet_coa= $_POST['e_txtcoa4'][$no_brid];
            
            $pdet_qty= $_POST['e_txtjmlrp'][$no_brid];
            $pdet_nilairp= $_POST['e_txtnilairp'][$no_brid];
            $pdet_rptotal= $_POST['e_txttotalrp'][$no_brid];
            
            $pdet_km= $_POST['e_txtkm'][$no_brid];
            $pdet_tkesuntuk= $_POST['cb_tkes'][$no_brid];
            $pdet_notes= $_POST['e_txtnotes'][$no_brid];
            
            $pdet_tgl01= $_POST['e_tglpilih01'][$no_brid];
            $pdet_tgl02= $_POST['e_tglpilih02'][$no_brid];
            
            
            
            if (empty($pdet_qty)) $pdet_qty=0;
            if (empty($pdet_nilairp)) $pdet_nilairp=0;
            if (empty($pdet_rptotal)) $pdet_rptotal=0;
            
            $pdet_qty=str_replace(",","", $pdet_qty);
            $pdet_nilairp=str_replace(",","", $pdet_nilairp);
            $pdet_rptotal=str_replace(",","", $pdet_rptotal);
            
            if (empty($pdet_km)) $pdet_km=0;
            $pdet_km=str_replace(",","", $pdet_km);
            
            if (!empty($pdet_notes)) $pdet_notes = str_replace("'", " ", $pdet_notes);
            
            if (empty($pdet_tgl01)) $pdet_tgl01="0000-00-00";
            if (empty($pdet_tgl02)) $pdet_tgl02="0000-00-00";
            
            if ((DOUBLE)$pdet_rptotal>0) {
                if ((DOUBLE)$pdet_qty==0) $pdet_qty=1;
                if ((DOUBLE)$pdet_nilairp==0) $pdet_nilairp=$pdet_rptotal;
                
                if ($pdet_tgl01<>"0000-00-00") $pdet_tgl01=date("Y-m-d", strtotime($pdet_tgl01));
                if ($pdet_tgl02<>"0000-00-00") $pdet_tgl02=date("Y-m-d", strtotime($pdet_tgl02));
                
                
                if (empty($pdet_coa)) {
                    $query = "select distinct COA4 as lcfields FROM dbmaster.posting_coa_rutin WHERE divisi='$pdivisiid' and nobrid='$no_brid'";
                    $tampilc= mysqli_query($cnmy, $query);
                    $rowc= mysqli_fetch_array($tampilc);
                    $pdet_coa=$rowc['lcfields'];
                    
                    //echo "coa p : $pdet_coa<br/>";
                }
                
                $pttotal=floatval($pttotal)+floatval($pdet_rptotal);
                
                //echo "COA : $pdet_coa, ";
                //echo "QTY : $pdet_qty, Nilai Rp. $pdet_nilairp, Total : $pdet_rptotal, ";
                //echo "KM : $pdet_km, Obat Untuk : $pdet_tkesuntuk, notes : $pdet_notes, ";
                //echo "TGL01 : $pdet_tgl01, TGL02 : $pdet_tgl02<br/>";
                ////idrutin, nobrid, qty, rp, rptotal, notes, tgl1, tgl2, km, obat_untuk, coa
                
                $pinsert_data_detail[] = "('$kodenya', '$no_brid', '$pdet_qty', '$pdet_nilairp', '$pdet_rptotal', '$pdet_notes', '$pdet_tgl01', '$pdet_tgl02', '$pdet_km', '$pdet_tkesuntuk', '$pdet_coa')";


            }
        }
        
        if ((DOUBLE)$ptotalrp<>(DOUBLE)$pttotal) $ptotalrp=$pttotal;
        
        
        //cek jika sudah ada inputan
        $query = "select idrutin from dbmaster.t_brrutin0 WHERE ( (periode1 between '$ptgl1' AND '$ptgl2') OR (periode2 between '$ptgl1' AND '$ptgl2') ) "
                . " AND "
                . " karyawanid='$pidkaryawan' AND IFNULL(stsnonaktif,'')<>'Y' AND idrutin<>'$kodenya'";//AND kodeperiode='$pkdperiode' 
        
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);

        if ($ketemu>0) {
            $row= mysqli_fetch_array($tampil);
            $nidrutin=$row['idrutin'];
            if (!empty($nidrutin)) {
                //echo "GAGAL.... Periode yang dipilih Tidak bisa tersimpan. karena sudah ada inputan, dengan ID : $nidrutin";
                //mysqli_close($cnmy); exit;
                
                mysqli_close($cnmy);
                $pketeksekusi="Gagal Simpan, Periode yang dipilih Tidak bisa tersimpan. karena sudah ada inputan, dengan ID : $nidrutin";
                goto errorsimpan;
                exit;
                
            }
        }
        
        
        
        //echo "ID : $kodenya, $pdivisiid, $pjbtid, KRY : $pidkaryawan, Bln : ($pcari_bln) $pbln ($pkdperiode : $ptgl1 - $ptgl2)<br/>$pnotes, Atasan : $patasan, Rp. $ptotalrp<br/>ID CAB : $pidcabang, ID WIL : $pwilgabungan, area : $pareaid, nopol : $pidnopol<br/>A 1 : $patasan1, A 2 : $patasan2, A 3 : $patasan3, A 4 : $patasan4";
        //echo "Total Jumlah : $ptotalrp, Total Rinci : $pttotal<br/>"; mysqli_close($cnmy); exit;
        
        //eksekusi 2
        
        if ($pact=="input") {
            
            $query="insert into dbmaster.t_brrutin0 (idrutin, karyawanid, icabangid, areaid, KODEWILAYAH, tgl, kode, "
                    . " bulan, kodeperiode, periode1, periode2, nama_karyawan, jabatanid, divisi, atasanid, atasan4, "
                    . " keterangan, nopol, userid, divi)values"
                    . "('$kodenya', '$pidkaryawan', '$pidcabang', '$pareaid', '$pwilgabungan', Current_Date(), 1, "
                    . " '$pbln', '$pkdperiode', '$ptgl1', '$ptgl2', '$pnamakaryawan', '$pjbtid', '$pdivisiid', '$patasan', '$patasan4', "
                    . " '$pnotes', '$pidnopol', '$pcardid', '$ppengajuan_divisi')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $pketeksekusi="error insert ke data rutin"; mysqli_close($cnmy); goto errorsimpan; }

            //simpan ke tabel images
            /*
            mysqli_query($cnmy, "DELETE FROM dbimages.t_brrutin0_ttd WHERE idrutin='$kodenya' LIMIT 1");
            $query = "INSERT INTO dbimages.t_brrutin0_ttd (idrutin)values('$kodenya')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $phapusinduk="Y"; $pketeksekusi="error insert ttd images"; goto errorsimpan; }
            */
        }
        
        
        
        $query = "UPDATE dbmaster.t_brrutin0 SET karyawanid='$pidkaryawan', "
                 . " icabangid='$pidcabang', "
                 . " areaid='$pareaid', "
                 . " bulan='$pbln', "
                 . " kodeperiode='$pkdperiode', "
                 . " periode1='$ptgl1', "
                 . " periode2='$ptgl2', "
                 . " nopol='$pidnopol', "
                 . " keterangan='$pnotes', "							 
                 . " jabatanid='$pjbtid', "
                 . " divisi='$pdivisiid', "
                 . " KODEWILAYAH='$pwilgabungan', "
                 . " atasanid='$patasan', "
                 . " idca='HO', "
                 . " userid='$pcardid', "
                . "  jumlah='$ptotalrp' WHERE "
                . " idrutin='$kodenya' LIMIT 1"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $phapusinduk="Y"; $pketeksekusi="error update data rutin"; goto errorsimpan; }
        
        
        
        if ($pdivisiid=="OTC" OR $pdivisiid=="CHC" OR $pdivisiid=="OT") {
            $query = "UPDATE dbmaster.t_brrutin0 SET icabangid_o='$pidcabang', areaid_o='$pareaid' WHERE idrutin='$kodenya' LIMIT 1"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $phapusinduk="Y"; $pketeksekusi="error update cabang chc data rutin"; goto errorsimpan; }
        }
        
        
        if ($pact=="input") {
            $pimgttd=$_POST['txtgambar'];
            $query = "UPDATE dbmaster.t_brrutin0 SET gambar='$pimgttd'  WHERE idrutin='$kodenya' LIMIT 1"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $phapusinduk="Y"; $pketeksekusi="error update tanda tangan 01"; goto errorsimpan; }
        }
        
        //update ke atasan
        $query = "UPDATE dbmaster.t_brrutin0 SET atasan1='', tgl_atasan1=NOW(), atasan2='', tgl_atasan2=NOW(), "
                . " atasan3='', tgl_atasan3=NOW(), atasan4='$patasan4' WHERE "
                . " idrutin='$kodenya' LIMIT 1";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $phapusinduk="Y"; $pketeksekusi="error update tanda tangan 02"; goto errorsimpan; }
        
        
        if ($pjbtid=="01" AND $pact=="input") {
            $query = "UPDATE dbmaster.t_brrutin0 SET atasan4='$pidkaryawan', tgl_atasan4=NULL WHERE idrutin='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $phapusinduk="Y"; $pketeksekusi="error update tanda tangan 03"; goto errorsimpan; }
        }
        
        if ($pidkaryawan=="0000001479X" AND $pact=="input") {
            
            //$query = "UPDATE dbmaster.t_brrutin0 SET atasan4='', tgl_atasan4=NOW() WHERE idrutin='$kodenya' LIMIT 1";
            //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $phapusinduk="Y"; $pketeksekusi="error update tanda tangan 04"; goto errorsimpan; }
            
        }
        
        
        //detail rincian
        $query = "DELETE from dbmaster.t_brrutin1 where idrutin='$kodenya'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $phapusinduk="Y"; $pketeksekusi="error hapus detail"; goto errorsimpan; }
        
        
        $query = "insert into dbmaster.t_brrutin1 (idrutin, nobrid, qty, rp, rptotal, notes, tgl1, tgl2, km, obat_untuk, coa) "
                . " VALUES ".implode(', ', $pinsert_data_detail);
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $phapusinduk="Y"; $pketeksekusi="error input detail"; goto errorsimpan; }
        
        
        
        
        
        mysqli_close($cnmy);
        
        
        $pketeksekusi=$kodenya;
        header('location:../../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&nmun='.$pidmenu.'&act=complete&iderror=berhasil&keteks='.$pketeksekusi);
        exit;
        
        
    }
    
}

errorsimpan:
    
    if ($phapusinduk=="Y" AND !empty($kodenya)) {
        mysqli_query($cnmy, "UPDATE dbmaster.t_brrutin0 set jumlah=0, stsnonaktif='Y' WHERE idrutin='$kodenya' AND karyawanid='$pidkaryawan' AND bulan='$pbln' LIMIT 1");
        mysqli_close($cnmy);
    }
    
    if (empty($pketeksekusi)) $pketeksekusi="error";
    //echo $pketeksekusi; exit;
    
    header('location:../../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&nmun='.$pidmenu.'&act=complete&iderror=error&keteks='.$pketeksekusi);
    exit;
    
?>

