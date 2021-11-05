<?php

    function BuatFormatNum($prp, $ppilih) {
        if (empty($prp)) $prp=0;
        
        $numrp=$prp;
        if ($ppilih=="1") $numrp=number_format($prp,0,",",",");
        elseif ($ppilih=="2") $numrp=number_format($prp,0,".",".");
            
        return $numrp;
    }
    
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","1G");
    ini_set('max_execution_time', 0);
    
    //
    session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
	
    $pidgrouppil=$_SESSION['GROUP'];
    $picardid=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];
    $pidsession=$_SESSION['IDSESI'];
    
    $ppilformat="1";

    
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        $ppilformat="3";
        
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REPORT REALISASI BIAYA MARKETING.xls");
    }
    
    if (($picardid=="0000000143" OR $picardid=="0000000329") AND $ppilihrpt=="excel") {
        $ppilformat="2";
    }
    
    include("config/koneksimysqli.php");
    
    $printdate= date("d/m/Y");
    
    
    include "config/fungsi_combo.php";
    include("config/common.php");
    
    $ppildivisiid = $_POST['cb_divisip'];
    $periode = $_POST['bulan1'];
    $prptpilihtype = $_POST['cb_rpttype'];
    
    
    $pcoapilih="";
    $filtercoa="";
    if (isset($_POST['cb_coa'])) $pcoapilih=$_POST['cb_coa'];
    if (!empty($pcoapilih)) {
        foreach ($pcoapilih as $pval_coa)
        {
            $filtercoa .="'".$pval_coa."',";

        }
    }
    
    if (!empty($filtercoa)) {
        $filtercoa="(".substr($filtercoa, 0, -1).")";
    }
    
    
    $pnamadivisi="";
    if ($prptpilihtype=="ETH") {
        $pnamadivisi="Ethical";
    }elseif ($prptpilihtype=="OTC") {
        $pnamadivisi="CHC";
    }else{
        $pnamadivisi=$ppildivisiid;
        if ($ppildivisiid=="EAGLE") $pnamadivisi="EAGLE";
        elseif ($ppildivisiid=="CAN") $pnamadivisi="CANARY";
        elseif ($ppildivisiid=="PEACO") $pnamadivisi="PEACOCK";
        elseif ($ppildivisiid=="PIGEO") $pnamadivisi="PIGEON";
        elseif ($ppildivisiid=="HO") $pnamadivisi="HO";
        elseif ($ppildivisiid=="OTC") $pnamadivisi="CHC";
        elseif ($ppildivisiid=="ETH") $pnamadivisi="Ethical";
    }
    
    $ptipereport="";
    if ($prptpilihtype=="COA") $ptipereport="COA";
    elseif ($prptpilihtype=="DIV") $ptipereport="Divisi";
    elseif ($prptpilihtype=="BMB") $ptipereport="Sub Posting Transaksi";
    
    
    $ptanggalprosesnya="";
    $query = "select tanggal_proses from dbmaster.t_proses_data_bm_date WHERE tahun='$periode'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ((DOUBLE)$ketemu>0) {
        $nt= mysqli_fetch_array($tampil);
        $ptanggalprosesnya=$nt['tanggal_proses'];
    }
    
    
    $tgl01 = $periode."-01-01";
    $tgl02 = $periode."-12-31";
    
    $pperiode1 = date("Y-m", strtotime($tgl01));
    $pperiode2 = date("Y-m", strtotime($tgl02));
    
    $myperiode1 = date("F Y", strtotime($tgl01));
    $myperiode2 = date("F Y", strtotime($tgl02));

    $ptahuninput = $periode;
    $pbulaninput = date("Y-m-01", strtotime($tgl01));
    
    $pfiltersel=" ('') ";
    $pfilterdelete="";
    
    $pdivisi="";
    
    
    $pbreth="";
    $pklaim="";
    $pkas="";
    $pbrotc="";
    $prutin="";
    $pblk="";
    $pca="";
    $pbmsby="";
    $ppilbank="";
    $ppilinsen="";
    
    
        
        
    if (isset($_POST['chkbox_rpt1'])) $pbreth=$_POST['chkbox_rpt1'];
    if (isset($_POST['chkbox_rpt2'])) $pklaim=$_POST['chkbox_rpt2'];
    if (isset($_POST['chkbox_rpt3'])) $pkas=$_POST['chkbox_rpt3'];
    if (isset($_POST['chkbox_rpt4'])) $pbrotc=$_POST['chkbox_rpt4'];
    if (isset($_POST['chkbox_rpt5'])) $prutin=$_POST['chkbox_rpt5'];
    if (isset($_POST['chkbox_rpt6'])) $pblk=$_POST['chkbox_rpt6'];
    if (isset($_POST['chkbox_rpt7'])) $pca=$_POST['chkbox_rpt7'];
    if (isset($_POST['chkbox_rpt8'])) $pbmsby=$_POST['chkbox_rpt8'];
    if (isset($_POST['chkbox_rpt9'])) $ppilbank=$_POST['chkbox_rpt9'];
    if (isset($_POST['chkbox_rpt10'])) $ppilinsen=$_POST['chkbox_rpt10'];
    
    $psewakontrak=""; $pserviceken=""; $pkaskecilcabang="";
    if (isset($_POST['chkbox_rpt11'])) $psewakontrak=$_POST['chkbox_rpt11'];
    if (isset($_POST['chkbox_rpt12'])) $pserviceken=$_POST['chkbox_rpt12'];
    if (isset($_POST['chkbox_rpt15'])) $pkaskecilcabang=$_POST['chkbox_rpt15'];
    $pkasbonnyasaja="";
    if (isset($_POST['chkbox_rpt16'])) $pkasbonnyasaja=$_POST['chkbox_rpt16'];
    
    
    
    $pbelumprosesclose=false;
    //if ($ptahuninput=="2019") {
    $pbelumprosesclose=true;

    $pfilterselpil="";
    //BR ETHICAL A
    if (!empty($pbreth)) $pfilterselpil .= "'A',";
    //klaimdiscount B
    if (!empty($pklaim)) $pfilterselpil .= "'B',";
    //KAS KECIL C
    if (!empty($pkas)) $pfilterselpil .= "'C',";
    //KASBON D
    if (!empty($pkasbonnyasaja)) $pfilterselpil .= "'D',";
    //BROTC E
    if (!empty($pbrotc)) $pfilterselpil .= "'E',";
    //RUTIN LUAR KOTA F rutin G lk
    if (!empty($prutin)) $pfilterselpil .= "'F',";
    if (!empty($pblk)) $pfilterselpil .= "'G',";

    //CA H
    //if (!empty($prutin) OR !empty($pblk)) $pfilterselpil .= "'H',";

    //BM biaya marketing surabaya I & J
    if (!empty($pbmsby)) $pfilterselpil .= "'I','J',";
    //insentif incentive K
    if (!empty($ppilinsen)) $pfilterselpil .= "'K',";
    //BANK L M N O P
    //if (!empty($ppilbank)) $pfilterselpil .= "'L','M','N','O','P',";


    //sewa kontrakan rumah
    if (!empty($psewakontrak)) $pfilterselpil .= "'U',";
    //service kendaraan
    if (!empty($pserviceken)) $pfilterselpil .= "'V',";

    //kas kecil cabang
    if (!empty($pkaskecilcabang)) $pfilterselpil .= "'X',";


    if (!empty($pfilterselpil)) {
        $pfilterselpil="(".substr($pfilterselpil, 0, -1).")";
    }else{
        $pfilterselpil="('xaxaXX')";
    }



    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmpprosbmpil00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmpprosbmpil01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpprosbmpil02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmpprosbmpil03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmpprosbmpil04_".$puserid."_$now ";
    $tmp05 =" dbtemp.tmpprosbmpil05_".$puserid."_$now ";

    
    
    if ($prptpilihtype=="RAW") {
        $query = "DELETE FROM dbproses.tmp_rawdata_expenses WHERE DATE_FORMAT(tgl_now, '%Y-%m-%d')<CURRENT_DATE()";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "DELETE FROM dbproses.tmp_rawdata_expenses WHERE DATE_FORMAT(tgl_now, '%Y-%m-%d')=CURRENT_DATE() AND userid_login='$picardid' AND session_id='$pidsession'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "ALTER TABLE dbproses.tmp_rawdata_expenses AUTO_INCREMENT=1";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    
    
    
                    $query ="SELECT noidauto, tahun, periode, hapus_nodiv_kosong, kodeinput, idkodeinput as idinput, nobrid_r, nobrid_n, idkodeinput, divisi, tglinput, tgltrans, "
                        . " karyawanid, nama_karyawan, dokterid, dokter_nama, noslip, nmrealisasi, keterangan, dpp, ppn, pph, tglfp, "
                        . " idinput_pd, nodivisi, debit, kredit, saldo, jumlah1, jumlah2, "
                        . " divisi_edit as divisi_coa, coa_edit as coa, coa_nama_edit as nama_coa, coa_edit2 as coa2, "
                        . " coa_nama_edit2 as nama_coa2, coa_edit3 as coa3, coa_nama_edit3 as nama_coa3, "
                        . " tgl_trans_bank, nobukti, idinput_pd1, idinput_pd2, nodivisi1, nodivisi2, pengajuan, "
                        . " divisi2, icabangid, nama_cabang, areaid, nama_area, kodeid_pd, subkode_pd, pcm, kasbonsby, coa_pcm, nama_coa_pcm, "
                        . " tgltarikan, nkodeid, nkodeid_nama, nsubkode, nsubkode_nama "
                        . " FROM dbmaster.t_proses_data_bm WHERE IFNULL(hapus_nodiv_kosong,'') <>'Y' AND DATE_FORMAT(tgltarikan,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' AND "
                        . " kodeinput IN $pfilterselpil ";
    
    
    $query = "SELECT tgltarikan, kodeinput, idkodeinput, idkodeinput as idinput, divisi, tgltrans, coa_edit as coa, coa_pcm, icabangid, "
            . " nkodeid, nkodeid_nama, nsubkode, nsubkode_nama, kredit "
            . " FROM dbmaster.t_proses_data_bm WHERE IFNULL(hapus_nodiv_kosong,'') <>'Y' AND DATE_FORMAT(tgltarikan,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' AND "
            . " kodeinput IN $pfilterselpil ";
    
    $query .=" AND IFNULL(ishare,'')<>'Y' ";
    if ($ppildivisiid=="ETH") {
        $query .=" AND divisi NOT IN ('OTC', 'CHC') ";
    }else{
        if (!empty($ppildivisiid)) $query .=" AND divisi='$ppildivisiid' ";
    }
    if (!empty($filtercoa)) $query .=" AND IFNULL(coa_edit,'') IN $filtercoa ";
    
    //echo $query; goto hapusdata;
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


    if (!empty($ppilbank)) {

                            $query ="INSERT INTO $tmp01 "
                                . "(noidauto, tahun, periode, hapus_nodiv_kosong, kodeinput, idinput, nobrid_r, nobrid_n, idkodeinput, divisi, tglinput, tgltrans, "
                                . " karyawanid, nama_karyawan, dokterid, dokter_nama, noslip, nmrealisasi, keterangan, dpp, ppn, pph, tglfp, "
                                . " idinput_pd, nodivisi, debit, kredit, saldo, jumlah1, jumlah2, "
                                . " divisi_coa, coa, nama_coa, coa2, "
                                . " nama_coa2, coa3, nama_coa3, "
                                . " tgl_trans_bank, nobukti, idinput_pd1, idinput_pd2, nodivisi1, nodivisi2, pengajuan, "
                                . " divisi2, icabangid, nama_cabang, areaid, nama_area, kodeid_pd, subkode_pd, pcm, kasbonsby, coa_pcm, nama_coa_pcm, "
                                . " tgltarikan, nkodeid, nkodeid_nama, nsubkode, nsubkode_nama)"
                                . "SELECT noidauto, tahun, periode, hapus_nodiv_kosong, kodeinput, idkodeinput as idinput, nobrid_r, nobrid_n, idkodeinput, divisi, tglinput, tgltrans, "
                                . " karyawanid, nama_karyawan, dokterid, dokter_nama, noslip, nmrealisasi, keterangan, dpp, ppn, pph, tglfp, "
                                . " idinput_pd, nodivisi, debit, kredit, saldo, jumlah1, jumlah2, "
                                . " divisi_edit as divisi_coa, coa_edit as coa, coa_nama_edit as nama_coa, coa_edit2 as coa2, "
                                . " coa_nama_edit2 as nama_coa2, coa_edit3 as coa3, coa_nama_edit3 as nama_coa3, "
                                . " tgl_trans_bank, nobukti, idinput_pd1, idinput_pd2, nodivisi1, nodivisi2, pengajuan, "
                                . " divisi2, icabangid, nama_cabang, areaid, nama_area, kodeid_pd, subkode_pd, pcm, kasbonsby, coa_pcm, nama_coa_pcm, "
                                . " tgltarikan, nkodeid, nkodeid_nama, nsubkode, nsubkode_nama "
                                . " FROM dbmaster.t_proses_data_bm WHERE IFNULL(hapus_nodiv_kosong,'') <>'Y' AND DATE_FORMAT(tgltarikan,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' AND "
                                . " kodeinput IN ('M') "
                                . " AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN (select CONCAT(IFNULL(kodeid,''),IFNULL(subkode,'')) from dbmaster.t_kode_spd where IFNULL(igroup,'')='3' AND IFNULL(ibank,'')<>'N') "
                                . " and IFNULL(nkodeid_nama,'')='K'";
                    
        
                    
        $query = "INSERT INTO $tmp01 (tgltarikan, kodeinput, idkodeinput, idinput, divisi, tgltrans, coa, coa_pcm, icabangid, "
                . " nkodeid, nkodeid_nama, nsubkode, nsubkode_nama, kredit)"
                . " SELECT tgltarikan, kodeinput, idkodeinput, idkodeinput as idinput, divisi, tgltrans, coa_edit as coa, coa_pcm, icabangid, "
                . " nkodeid, nkodeid_nama, nsubkode, nsubkode_nama, kredit "
                . " FROM dbmaster.t_proses_data_bm WHERE IFNULL(hapus_nodiv_kosong,'') <>'Y' AND DATE_FORMAT(tgltarikan,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' AND "
                . " kodeinput IN ('M') "
                . " AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN (select CONCAT(IFNULL(kodeid,''),IFNULL(subkode,'')) from dbmaster.t_kode_spd where IFNULL(igroup,'')='3' AND IFNULL(ibank,'')<>'N') "
                . " and IFNULL(nkodeid_nama,'')='K'";
    
        if ($ppildivisiid=="ETH") {
            $query .=" AND divisi NOT IN ('OTC', 'CHC') ";
        }else{
            if (!empty($ppildivisiid)) $query .=" AND divisi='$ppildivisiid' ";
        }
        if (!empty($filtercoa)) $query .=" AND IFNULL(coa_edit,'') IN $filtercoa ";

        $query .=" AND IFNULL(CONCAT(idkodeinput,kodeinput),'') NOT IN ('BN00001849M', 'BN00001856M', 'BN00001857M') ";
        $query .=" AND IFNULL(ishare,'')<>'Y' ";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


    }
    
    $query = "ALTER TABLE $tmp01 ADD COLUMN nama_coa VARCHAR(300), "
            . " ADD COLUMN coa3 VARCHAR(100), ADD COLUMN nama_coa3 VARCHAR(300), "
            . " ADD COLUMN coa2 VARCHAR(100), ADD COLUMN nama_coa2 VARCHAR(300), "
            . " ADD COLUMN coa1 VARCHAR(100), ADD COLUMN nama_coa1 VARCHAR(300), "
            . " ADD COLUMN nama_cabang VARCHAR(300)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    $query = "CREATE INDEX `norm1` ON $tmp01 (kodeinput,idinput,divisi,tgltrans,coa)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    $query = "UPDATE $tmp01 as a JOIN dbmaster.coa_level4 as b on a.coa=b.COA4 SET a.nama_coa=b.NAMA4, a.coa3=b.COA3";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    $query = "UPDATE $tmp01 as a JOIN dbmaster.coa_level3 as b on a.coa3=b.COA3 SET a.nama_coa3=b.NAMA3, a.coa2=b.COA2";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    $query = "UPDATE $tmp01 as a JOIN dbmaster.coa_level2 as b on a.coa2=b.COA2 SET a.nama_coa2=b.NAMA2, a.coa1=b.COA1";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    $query = "UPDATE $tmp01 as a JOIN dbmaster.coa_level1 as b on a.coa1=b.COA1 SET a.nama_coa1=b.NAMA1";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        
    $query = "UPDATE $tmp01 SET coa=coa_pcm WHERE IFNULL(coa_pcm,'') ='105-02'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 a JOIN dbmaster.coa_level4 b on a.coa=b.COA4 SET a.nama_coa=b.NAMA4, a.coa3=b.COA3 WHERE IFNULL(a.coa_pcm,'') ='105-02'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 a JOIN dbmaster.coa_level3 b on a.coa3=b.COA3 SET a.nama_coa3=b.NAMA3, a.coa2=b.COA2 WHERE IFNULL(a.coa_pcm,'') ='105-02'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 a JOIN dbmaster.coa_level2 b on a.coa2=b.COA2 SET a.nama_coa2=b.NAMA2 WHERE IFNULL(a.coa_pcm,'') ='105-02'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp01 SET divisi='OTHER' WHERE IFNULL(divisi,'') IN ('', 'AA', 'OTHERS')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp01 SET icabangid='HO', nama_cabang='HO' WHERE IFNULL(divisi,'')='OTC' AND icabangid='0000000001' AND kodeinput in ('L','M','N','O','P')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
        if ($prptpilihtype=="RAW") {
            
            $query = "INSERT INTO dbproses.tmp_rawdata_expenses"
                    . " (session_id, userid_login, "
                    . " kodeinput, idkodeinput, tanggal, "
                    . " divisi, coa1, nama_coa1, coa2, nama_coa2, coa3, nama_coa3, coa4, nama_coa4, "
                    . " jumlah) "
                    . " SELECT '$pidsession' as session_id, '$picardid' as userid_login, "
                    . " kodeinput, idkodeinput, tgltarikan, "
                    . " divisi, coa1, nama_coa1, coa2, nama_coa2, coa3, nama_coa3, coa, nama_coa, "
                    . " kredit FROM $tmp01";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            //$query = "create table $tmp04 (select * from $tmp00)";
            //mysqli_query($cnmy, $query);
            //echo "$tmp04";
            
            goto hapusdata;
            
        }elseif ($prptpilihtype=="BMB" AND $ppildivisiid<>"OTC") {
            
            
            //hapus yang PCM
            //$query = "DELETE FROM $tmp01 WHERE IFNULL(coa_pcm,'') ='105-02'";
            $query = "DELETE FROM $tmp01 WHERE IFNULL(coa2,'') ='105'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            //echo $tmp01; goto hapusdata;
            
            $query = "SELECT
                a.tahun,
                a.g_divisi,
                a.kodeid,
                a.jumlah
                FROM
                dbmaster.t_budget AS a
                WHERE tahun = '$ptahuninput' AND g_divisi='ETH'";

            $query = "create TEMPORARY table $tmp02 ($query)"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            
            
            $filtallakunbreth=""; $filtdcc=""; $filtdss=""; $filtgimicprom=""; $filtiklan=""; $filtsimpo=""; $filtho="";
            $query = "select distinct kodeid, kode_akun from dbmaster.t_budget_kode_d order by kodeid";
            $tampil=mysqli_query($cnmy, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $pkodeidp=$row['kodeid'];
                $pakunkode=$row['kode_akun'];

                $filtallakunbreth .="'".$pakunkode."',";

                if ($pkodeidp=="04") {
                    $filtdss .="'".$pakunkode."',";
                }elseif ($pkodeidp=="05") {
                    $filtdcc .="'".$pakunkode."',";
                }elseif ($pkodeidp=="06") {
                    $filtgimicprom .="'".$pakunkode."',";
                }elseif ($pkodeidp=="07") {
                    $filtiklan .="'".$pakunkode."',";
                }elseif ($pkodeidp=="08") {
                    $filtsimpo .="'".$pakunkode."',";
                }elseif ($pkodeidp=="10") {
                    $filtho .="'".$pakunkode."',";
                }
            }
            
            
            if (!empty($filtallakunbreth)) $filtallakunbreth="(".substr($filtallakunbreth, 0, -1).")";
            else $filtallakunbreth="('')";

            if (!empty($filtdcc)) $filtdcc="(".substr($filtdcc, 0, -1).")";
            else $filtdcc="('')";

            if (!empty($filtdss)) $filtdss="(".substr($filtdss, 0, -1).")";
            else $filtdss="('')";

            if (!empty($filtgimicprom)) $filtgimicprom="(".substr($filtgimicprom, 0, -1).")";
            else $filtgimicprom="('')";

            if (!empty($filtiklan)) $filtiklan="(".substr($filtiklan, 0, -1).")";
            else $filtiklan="('')";

            if (!empty($filtsimpo)) $filtsimpo="(".substr($filtsimpo, 0, -1).")";
            else $filtsimpo="('')";

            if (!empty($filtho)) $filtho="(".substr($filtho, 0, -1).")";
            else $filtho="('')";

            //echo "$filtdss<br/>$filtdcc<br/>$filtgimicprom<br/>$filtiklan<br/>$filtsimpo<br/>$filtho<br/>All : $filtallakunbreth<br/>";
            
            
            $query = "SELECT tgltarikan as bulan, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01";
            $query = "create TEMPORARY table $tmp03 ($query)"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            
            $query = "Alter table $tmp03 ADD COLUMN kodeid VARCHAR(5)"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            //BIAYA RUTIN
            $query = "UPDATE $tmp03 SET kodeid='01' WHERE kodeinput IN ('F','U','V')"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            //LUAR KOTA
            $query = "UPDATE $tmp03 SET kodeid='02' WHERE kodeinput='G'"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            //INSENTIF
            $query = "UPDATE $tmp03 SET kodeid='03' WHERE kodeinput='K'"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            //DARI BM SBY insentif masuk INSENTIF
                $query = "UPDATE $tmp03 SET kodeid='03' WHERE kodeinput IN ('I','J') AND coa IN ('700-05', '701-05', '702-05', '703-05', '704-05')"; 
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            //DARI BM SBY gaji masuk .... ke HO
                $query = "UPDATE $tmp03 SET kodeid='10' WHERE kodeinput IN ('I','J') AND coa NOT IN ('700-05', '701-05', '702-05', '703-05', '704-05')"; 
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            //DARI BANK masuk .... ke HO
                $query = "UPDATE $tmp03 SET kodeid='10' WHERE kodeinput IN ('M')"; 
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            //DSS
            $query = "UPDATE $tmp03 SET kodeid='04' WHERE kodeinput='A' AND IFNULL(nkodeid,'') IN $filtdss"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            //DCC
            $query = "UPDATE $tmp03 SET kodeid='05' WHERE kodeinput='A' AND IFNULL(nkodeid,'') IN $filtdcc"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            //Gimmic, Brosur, Program FF
            $query = "UPDATE $tmp03 SET kodeid='06' WHERE kodeinput='A' AND IFNULL(nkodeid,'') IN $filtgimicprom"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            //Iklan
            $query = "UPDATE $tmp03 SET kodeid='07' WHERE kodeinput='A' AND IFNULL(nkodeid,'') IN $filtiklan"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            //SIMPOSIUM & EXHIBITION
            $query = "UPDATE $tmp03 SET kodeid='08' WHERE kodeinput='A' AND IFNULL(nkodeid,'') IN $filtsimpo"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            //CLAIM DISCOUNT
            $query = "UPDATE $tmp03 SET kodeid='09' WHERE kodeinput='B' AND IFNULL(divisi,'') NOT IN ('OTC', 'CHC', 'OT')"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            //HO (10)
            $query = "UPDATE $tmp03 SET kodeid='10' WHERE kodeinput='A' AND IFNULL(nkodeid,'') IN $filtho"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            //KAS KECIL
            $query = "UPDATE $tmp03 SET kodeid='11' WHERE kodeinput IN ('C','D','X')"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            //UPDATE KE HO kodeidnya kosong nkodeid
            $query = "UPDATE $tmp03 SET kodeid='10' WHERE kodeinput IN ('A') AND IFNULL(nkodeid,'')=''"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            
            
            $query = "create TEMPORARY table $tmp00 (SELECT * FROM dbmaster.t_budget_realisasi_lap WHERE nourut NOT IN ('23'))"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            mysqli_query($cnmy, "update $tmp00 set keterangan=keterangan2");
            
            
            $addcolumn="";
            for ($x=1;$x<=12;$x++) {
                $addcolumn .= " ADD A$x DECIMAL(20,2),ADD B$x DECIMAL(20,2),ADD C$x DECIMAL(20,2),";
                //$addcolumn .= " ADD AA$x DECIMAL(20,2),ADD BB$x DECIMAL(20,2),ADD CC$x DECIMAL(20,2),";
            }
            $addcolumn .= " ADD ATOTAL DECIMAL(20,2), ADD BTOTAL DECIMAL(20,2), ADD CTOTAL DECIMAL(20,2)";
            //$addcolumn .= ", ADD AATOTAL DECIMAL(20,2), ADD BBTOTAL DECIMAL(20,2), ADD CCTOTAL DECIMAL(20,2)";

            $query = "ALTER TABLE $tmp00 $addcolumn";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
            
            $urut=2;
            for ($x=1;$x<=12;$x++) {
                $jml=  strlen($x);
                $awal=$urut-$jml;
                $nbulan=$periode."-".str_repeat("0", $awal).$x;
                $nfield="A".$x;
                $nfieldR="B".$x;

                $query = "UPDATE $tmp00 as a JOIN (SELECT kodeid, SUM(kredit) as kredit FROM $tmp03 WHERE DATE_FORMAT(bulan, '%Y-%m')='$nbulan' GROUP BY 1) as b "
                        . " on IFNULL(a.kodeid,'')=IFNULL(b.kodeid,'') SET a.$nfield=b.kredit"; 
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                $query = "UPDATE $tmp00 as a JOIN (SELECT kodeid, SUM(kredit) as kredit FROM $tmp03 GROUP BY 1) as b "
                        . " on IFNULL(a.kodeid,'')=IFNULL(b.kodeid,'') SET a.$nfieldR=ROUND(IFNULL($nfield,0)/IFNULL(b.kredit,0)*100,2)"; 
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
            }
            
            
            mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");

            $query = "SELECT * FROM $tmp00";
            $query = "create TEMPORARY table $tmp04 ($query)"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
            
            $urut=2;
            for ($x=1;$x<=12;$x++) {
                $jml=  strlen($x);
                $awal=$urut-$jml;
                $nbulan=$periode."-".str_repeat("0", $awal).$x;
                $nfield="A".$x;
                $nfieldR="B".$x;
                $nfield_="AA".$x;
                $nfieldR_="BB".$x;
                
                $query = "UPDATE $tmp00 as a JOIN (SELECT SUM($nfield) as jumlah1, SUM($nfieldR) as jumlah2 FROM $tmp04 WHERE IFNULL(grp,0)=1) as b "
                        . " SET a.$nfield=b.jumlah1, a.$nfieldR=b.jumlah2 WHERE nourut=5";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                $query = "UPDATE $tmp00 as a JOIN (SELECT SUM($nfield) as jumlah1, SUM($nfieldR) as jumlah2 FROM $tmp04 WHERE IFNULL(grp,0)=2) as b "
                        . " SET a.$nfield=b.jumlah1, a.$nfieldR=b.jumlah2 WHERE nourut=10";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                $query = "UPDATE $tmp00 as a JOIN (SELECT SUM($nfield) as jumlah1, SUM($nfieldR) as jumlah2 FROM $tmp04 WHERE IFNULL(grp,0)=3) as b "
                        . " SET a.$nfield=b.jumlah1, a.$nfieldR=b.jumlah2 WHERE nourut=14";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                $query = "UPDATE $tmp00 as a JOIN (SELECT SUM($nfield) as jumlah1, SUM($nfieldR) as jumlah2 FROM $tmp04 WHERE IFNULL(grp,0)=4) as b "
                        . " SET a.$nfield=b.jumlah1, a.$nfieldR=b.jumlah2 WHERE nourut=20";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                $query = "UPDATE $tmp00 as a JOIN (SELECT SUM($nfield) as jumlah1, SUM($nfieldR) as jumlah2 FROM $tmp04 WHERE IFNULL(kodeid,'') <> '' and IFNULL(kodeid,'') not in ('01','02','03') ) as b "
                        . " SET a.$nfield=b.jumlah1, a.$nfieldR=b.jumlah2 WHERE nourut=21";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                
            }
            
            
            mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");

            $query = "SELECT * FROM $tmp00";
            $query = "create TEMPORARY table $tmp04 ($query)"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
            $urut=2;
            for ($x=1;$x<=12;$x++) {
                $jml=  strlen($x);
                $awal=$urut-$jml;
                $nbulan=$periode."-".str_repeat("0", $awal).$x;
                $nfield="A".$x;
                $nfieldR="B".$x;
                $nfield_="AA".$x;
                $nfieldR_="BB".$x;
                
                $query = "UPDATE $tmp00 as a JOIN (SELECT SUM($nfield) as jumlah1, SUM($nfieldR) as jumlah2 FROM $tmp04 WHERE nourut in (5,21) ) as b "
                        . " SET a.$nfield=b.jumlah1, a.$nfieldR=b.jumlah2 WHERE nourut=22";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                
            }
    
    
            
            mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
            
            
        }elseif ($prptpilihtype=="BMB" AND $ppildivisiid=="OTC") {
            
            //hapus yang PCM
            //$query = "DELETE FROM $tmp01 WHERE IFNULL(coa_pcm,'') ='105-02'";
            $query = "DELETE FROM $tmp01 WHERE IFNULL(coa2,'') ='105'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            $query = "SELECT
                a.tahun,
                a.g_divisi,
                a.kodeid,
                a.jumlah
                FROM
                dbmaster.t_budget_otc AS a
                WHERE tahun = '$ptahuninput' AND g_divisi='OTC'";
            $query = "create TEMPORARY table $tmp02 ($query)"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        
    
            $filtgajispg=""; $filtinsentif=""; $filtsponsor=""; $filtsewadisp=""; $filtentertain=""; $filtpromat=""; $filtiklan=""; $filtevent="";
            $filtrafaksi=""; $filtklaimdisc=""; $filtpromotcost=""; $filtlistingfe=""; $filtfrontline=""; $filthoteltiket=""; $filtinventaris="";
            $filtrpetykes="";
            $query = "select distinct kodeid, kode_akun, kode_akun_sub from dbmaster.t_budget_kode_otc_d order by kodeid";
            $tampil=mysqli_query($cnmy, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $pkodeidp=$row['kodeid'];
                $pakunkode=$row['kode_akun'];
                $pakunkodesub=$row['kode_akun_sub'];

                $pkodeandsub=$pakunkode."".$pakunkodesub;

                if ($pkodeidp=="03") {
                    $filtgajispg .="'".$pkodeandsub."',";
                }elseif ($pkodeidp=="04") {
                    $filtinsentif .="'".$pkodeandsub."',";
                }elseif ($pkodeidp=="05") {
                    $filtsponsor .="'".$pkodeandsub."',";
                }elseif ($pkodeidp=="06") {
                    $filtsewadisp .="'".$pkodeandsub."',";
                }elseif ($pkodeidp=="07") {
                    $filtentertain .="'".$pkodeandsub."',";
                }elseif ($pkodeidp=="08") {
                    $filtpromat .="'".$pakunkode."',";
                }elseif ($pkodeidp=="09") {
                    $filtiklan .="'".$pakunkode."',";
                }elseif ($pkodeidp=="10") {
                    $filtevent .="'".$pakunkode."',";
                }elseif ($pkodeidp=="11") {
                    $filtrafaksi .="'".$pkodeandsub."',";
                }elseif ($pkodeidp=="17") {
                    $filtklaimdisc .="'".$pkodeandsub."',";
                }elseif ($pkodeidp=="18") {
                    $filtpromotcost .="'".$pkodeandsub."',";
                }elseif ($pkodeidp=="13") {
                    $filtlistingfe .="'".$pkodeandsub."',";
                }elseif ($pkodeidp=="14") {
                    $filtfrontline .="'".$pkodeandsub."',";
                }elseif ($pkodeidp=="15") {
                    $filthoteltiket .="'".$pkodeandsub."',";
                }elseif ($pkodeidp=="16") {
                    $filtinventaris .="'".$pkodeandsub."',";
                }elseif ($pkodeidp=="19") {
                    $filtrpetykes .="'".$pkodeandsub."',";
                }

            }

            if (!empty($filtgajispg)) $filtgajispg="(".substr($filtgajispg, 0, -1).")";
            else $filtgajispg="('')";

            if (!empty($filtinsentif)) $filtinsentif="(".substr($filtinsentif, 0, -1).")";
            else $filtinsentif="('')";

            if (!empty($filtsponsor)) $filtsponsor="(".substr($filtsponsor, 0, -1).")";
            else $filtsponsor="('')";

            if (!empty($filtsewadisp)) $filtsewadisp="(".substr($filtsewadisp, 0, -1).")";
            else $filtsewadisp="('')";

            if (!empty($filtentertain)) $filtentertain="(".substr($filtentertain, 0, -1).")";
            else $filtentertain="('')";

            if (!empty($filtpromat)) $filtpromat="(".substr($filtpromat, 0, -1).")";
            else $filtpromat="('')";

            if (!empty($filtiklan)) $filtiklan="(".substr($filtiklan, 0, -1).")";
            else $filtiklan="('')";

            if (!empty($filtevent)) $filtevent="(".substr($filtevent, 0, -1).")";
            else $filtevent="('')";

            if (!empty($filtrafaksi)) $filtrafaksi="(".substr($filtrafaksi, 0, -1).")";
            else $filtrafaksi="('')";

            if (!empty($filtklaimdisc)) $filtklaimdisc="(".substr($filtklaimdisc, 0, -1).")";
            else $filtklaimdisc="('')";

            if (!empty($filtpromotcost)) $filtpromotcost="(".substr($filtpromotcost, 0, -1).")";
            else $filtpromotcost="('')";

            if (!empty($filtlistingfe)) $filtlistingfe="(".substr($filtlistingfe, 0, -1).")";
            else $filtlistingfe="('')";

            if (!empty($filtfrontline)) $filtfrontline="(".substr($filtfrontline, 0, -1).")";
            else $filtfrontline="('')";

            if (!empty($filthoteltiket)) $filthoteltiket="(".substr($filthoteltiket, 0, -1).")";
            else $filtfrontline="('')";

            if (!empty($filtinventaris)) $filtinventaris="(".substr($filtinventaris, 0, -1).")";
            else $filtinventaris="('')";
            
            if (!empty($filtrpetykes)) $filtrpetykes="(".substr($filtrpetykes, 0, -1).")";
            else $filtrpetykes="('')";


            //echo "$filtgajispg<br/>$filtinsentif</br>$filtsponsor<br/>$filtsewadisp<br/>$filtentertain<br/>$filtpromat<br/>$filtiklan<br/>$filtevent<br/>$filtrafaksi<br/>$filtklaimdisc</br/>$filtpromotcost<br/>$filtlistingfe<br/>$filtfrontline<br/>$filthoteltiket<br/>$filtinventaris<br/>";
            
            
            
            //GAJI SPG 03
            $query = "SELECT tgltarikan as bulan, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, CAST('03' as CHAR(5)) as kodeid, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
                    . " CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtgajispg";
            $query = "create TEMPORARY table $tmp03 ($query)"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtgajispg");
            if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            //insentif 04
            $query = "INSERT INTO $tmp03 (bulan, kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                    . "SELECT tgltarikan as bulan, '04' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
                    . " CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtinsentif";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtinsentif");
            if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            //sponsor 05
            $query = "INSERT INTO $tmp03 (bulan, kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                    . "SELECT tgltarikan as bulan, '05' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
                    . " CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtsponsor";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtsponsor");
            if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            //sewa display 06
            $query = "INSERT INTO $tmp03 (bulan, kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                    . "SELECT tgltarikan as bulan, '06' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
                    . " CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtsewadisp";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtsewadisp");
            if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            //entertain 07
            $query = "INSERT INTO $tmp03 (bulan, kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                    . "SELECT tgltarikan as bulan, '07' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
                    . " CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtentertain";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtentertain");
            if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            //promat 08
            $query = "INSERT INTO $tmp03 (bulan, kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                    . "SELECT tgltarikan as bulan, '08' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
                    . " IFNULL(nkodeid,'') IN $filtpromat";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND IFNULL(nkodeid,'') IN $filtpromat");
            if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            //iklan 09
            $query = "INSERT INTO $tmp03 (bulan, kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                    . "SELECT tgltarikan as bulan, '09' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
                    . " IFNULL(nkodeid,'') IN $filtiklan";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND IFNULL(nkodeid,'') IN $filtiklan");
            if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            //event 10
            $query = "INSERT INTO $tmp03 (bulan, kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                    . "SELECT tgltarikan as bulan, '10' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
                    . " IFNULL(nkodeid,'') IN $filtevent";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND IFNULL(nkodeid,'') IN $filtevent");
            if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


            //rafaksi 11
            $query = "INSERT INTO $tmp03 (bulan, kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                    . "SELECT tgltarikan as bulan, '11' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
                    . " CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtrafaksi";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtrafaksi");
            if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


            //klaim discount 17
            $query = "INSERT INTO $tmp03 (bulan, kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                    . "SELECT tgltarikan as bulan, '17' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
                    . " CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtklaimdisc";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtklaimdisc");
            if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

                //klaim discount 17 KHUSUS
                $query = "INSERT INTO $tmp03 (bulan, kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                        . "SELECT tgltarikan as bulan, '17' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
                        . " IFNULL(nkodeid,'')='12' AND IFNULL(nsubkode,'')='' ";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

                mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND IFNULL(nkodeid,'')='12' AND IFNULL(nsubkode,'')=''");
                if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


            //PROMOTION COST 18 
            $query = "INSERT INTO $tmp03 (bulan, kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                    . "SELECT tgltarikan as bulan, '18' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
                    . " CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtpromotcost";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtpromotcost");
            if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            //Listing fee 13
            $query = "INSERT INTO $tmp03 (bulan, kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                    . "SELECT tgltarikan as bulan, '13' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
                    . " CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtlistingfe";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtlistingfe");
            if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            //front liner 14
            $query = "INSERT INTO $tmp03 (bulan, kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                    . "SELECT tgltarikan as bulan, '14' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
                    . " CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtfrontline";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtfrontline");
            if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            //tiket dan hotel 15
            $query = "INSERT INTO $tmp03 (bulan, kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                    . "SELECT tgltarikan as bulan, '15' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
                    . " CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filthoteltiket";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filthoteltiket");
            if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            //inventaris 16
            $query = "INSERT INTO $tmp03 (bulan, kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                    . "SELECT tgltarikan as bulan, '16' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
                    . " CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtinventaris";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtinventaris");
            if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


            //biaya rutin 01
            $query = "INSERT INTO $tmp03 (bulan, kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                    . "SELECT tgltarikan as bulan, '01' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('F','U','V')";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            //luar kota 02
            $query = "INSERT INTO $tmp03 (bulan, kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                    . "SELECT tgltarikan as bulan, '02' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('G')";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            //DARI BM SBY INSENTIF masuk INSENTIF 04
                $query = "INSERT INTO $tmp03 (bulan, kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                        . "SELECT tgltarikan as bulan, '04' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('I','J') AND "
                        . " coa IN ('704-05')";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }




            //DARI BM SBY GAJI masuk .... HO dulu 20
                $query = "INSERT INTO $tmp03 (bulan, kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                        . "SELECT tgltarikan as bulan, '20' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('I','J') AND "
                        . " coa NOT IN ('700-05', '701-05', '702-05', '703-05', '704-05')";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


            //DARI BANK masuk .... HO dulu 20
                $query = "INSERT INTO $tmp03 (bulan, kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                        . "SELECT tgltarikan as bulan, '20' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('M')";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


            //INVENTARIS
                $query = "INSERT INTO $tmp03 (bulan, kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                        . "SELECT tgltarikan as bulan, '16' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') "
                        . " AND nkodeid in ('07') AND IFNULL(nsubkode,'')=''";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

                mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND nkodeid in ('07') AND IFNULL(nsubkode,'')=''");


                
            //pety cash 19 kas kecil
            $query = "INSERT INTO $tmp03 (bulan, kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                    . "SELECT tgltarikan as bulan, '19' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
                    . " CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtrpetykes";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtrpetykes");
            if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            //BR OTC yang belum masuk .... HO dulu 20
                $query = "INSERT INTO $tmp03 (bulan, kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                        . "SELECT tgltarikan as bulan, '20' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E')";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

                mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E')");
                if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }




            // 12 BONUS DPL/DPF DARI KLAIM DISCOUNT PRITA AHMAD AHMED--- PENGAJUAN = OTC
            $query = "INSERT INTO $tmp03 (bulan, kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                    . "SELECT tgltarikan as bulan, '12' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('B')";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }



            // KAS KECIL CABANG MASUK KE .....? 19 KAS KECIL
            $query = "INSERT INTO $tmp03 (bulan, kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                    . "SELECT tgltarikan as bulan, '19' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('X')";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


            
            
            $query = "create TEMPORARY table $tmp00 (SELECT * FROM dbmaster.t_budget_realisasi_lap_otc)"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            mysqli_query($cnmy, "update $tmp00 set keterangan=keterangan2");
    
            $addcolumn="";
            for ($x=1;$x<=12;$x++) {
                $addcolumn .= " ADD A$x DECIMAL(20,2),ADD B$x DECIMAL(20,2),ADD C$x DECIMAL(20,2),";
                //$addcolumn .= " ADD AA$x DECIMAL(20,2),ADD BB$x DECIMAL(20,2),ADD CC$x DECIMAL(20,2),";
            }
            $addcolumn .= " ADD ATOTAL DECIMAL(20,2), ADD BTOTAL DECIMAL(20,2), ADD CTOTAL DECIMAL(20,2)";
            //$addcolumn .= ", ADD AATOTAL DECIMAL(20,2), ADD BBTOTAL DECIMAL(20,2), ADD CCTOTAL DECIMAL(20,2)";

            $query = "ALTER TABLE $tmp00 $addcolumn";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            $urut=2;
            for ($x=1;$x<=12;$x++) {
                $jml=  strlen($x);
                $awal=$urut-$jml;
                $nbulan=$periode."-".str_repeat("0", $awal).$x;
                $nfield="A".$x;
                $nfieldR="B".$x;

                $query = "UPDATE $tmp00 as a JOIN (SELECT kodeid, SUM(kredit) as kredit FROM $tmp03 WHERE DATE_FORMAT(bulan, '%Y-%m')='$nbulan' GROUP BY 1) as b "
                        . " on IFNULL(a.kodeid,'')=IFNULL(b.kodeid,'') SET a.$nfield=b.kredit"; 
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                $query = "UPDATE $tmp00 as a JOIN (SELECT kodeid, SUM(kredit) as kredit FROM $tmp03 GROUP BY 1) as b "
                        . " on IFNULL(a.kodeid,'')=IFNULL(b.kodeid,'') SET a.$nfieldR=ROUND(IFNULL($nfield,0)/IFNULL(b.kredit,0)*100,2)"; 
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
            }
            
            
            mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");

            $query = "SELECT * FROM $tmp00";
            $query = "create TEMPORARY table $tmp04 ($query)"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
            
            $urut=2;
            for ($x=1;$x<=12;$x++) {
                $jml=  strlen($x);
                $awal=$urut-$jml;
                $nbulan=$periode."-".str_repeat("0", $awal).$x;
                $nfield="A".$x;
                $nfieldR="B".$x;
                $nfield_="AA".$x;
                $nfieldR_="BB".$x;
                
                $query = "UPDATE $tmp00 as a JOIN (SELECT SUM($nfield) as jumlah1, SUM($nfieldR) as jumlah2 FROM $tmp04 WHERE IFNULL(grp,0)=1) as b "
                        . " SET a.$nfield=b.jumlah1, a.$nfieldR=b.jumlah2 WHERE nourut=7";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                $query = "UPDATE $tmp00 as a JOIN (SELECT SUM($nfield) as jumlah1, SUM($nfieldR) as jumlah2 FROM $tmp04 WHERE IFNULL(grp,0)=2) as b "
                        . " SET a.$nfield=b.jumlah1, a.$nfieldR=b.jumlah2 WHERE nourut=14";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                $query = "UPDATE $tmp00 as a JOIN (SELECT SUM($nfield) as jumlah1, SUM($nfieldR) as jumlah2 FROM $tmp04 WHERE IFNULL(grp,0)=3) as b "
                        . " SET a.$nfield=b.jumlah1, a.$nfieldR=b.jumlah2 WHERE nourut=19";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                $query = "UPDATE $tmp00 as a JOIN (SELECT SUM($nfield) as jumlah1, SUM($nfieldR) as jumlah2 FROM $tmp04 WHERE IFNULL(grp,0)=5) as b "
                        . " SET a.$nfield=b.jumlah1, a.$nfieldR=b.jumlah2 WHERE nourut=28";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                $query = "UPDATE $tmp00 as a JOIN (SELECT SUM($nfield) as jumlah1, SUM($nfieldR) as jumlah2 FROM $tmp04 WHERE IFNULL(grp,0)=6) as b "
                        . " SET a.$nfield=b.jumlah1, a.$nfieldR=b.jumlah2 WHERE nourut=36";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                
                
            }
            
            mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");

            $query = "SELECT * FROM $tmp00";
            $query = "create TEMPORARY table $tmp04 ($query)"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
            $urut=2;
            for ($x=1;$x<=12;$x++) {
                $jml=  strlen($x);
                $awal=$urut-$jml;
                $nbulan=$periode."-".str_repeat("0", $awal).$x;
                $nfield="A".$x;
                $nfieldR="B".$x;
                $nfield_="AA".$x;
                $nfieldR_="BB".$x;
                
                $query = "UPDATE $tmp00 as a JOIN (SELECT SUM($nfield) as jumlah1, SUM($nfieldR) as jumlah2 FROM $tmp04 WHERE grp in (2,3,4,5,6) ) as b "
                        . " SET a.$nfield=b.jumlah1, a.$nfieldR=b.jumlah2 WHERE nourut=37";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                
            }
            
            
            mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");

            $query = "SELECT * FROM $tmp00";
            $query = "create TEMPORARY table $tmp04 ($query)"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
            $urut=2;
            for ($x=1;$x<=12;$x++) {
                $jml=  strlen($x);
                $awal=$urut-$jml;
                $nbulan=$periode."-".str_repeat("0", $awal).$x;
                $nfield="A".$x;
                $nfieldR="B".$x;
                $nfield_="AA".$x;
                $nfieldR_="BB".$x;
                
                $query = "UPDATE $tmp00 as a JOIN (SELECT SUM($nfield) as jumlah1, SUM($nfieldR) as jumlah2 FROM $tmp04 WHERE nourut in (7,37) ) as b "
                        . " SET a.$nfield=b.jumlah1, a.$nfieldR=b.jumlah2 WHERE nourut=39";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                
            }
    
    
            mysqli_query($cnmy, "DELETE FROM $tmp00 WHERE nourut IN ('40', '41', '42', '43')");
            
            
            mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
            
            
            //$query = "create table $tmp04 (select * from $tmp00)";
            //mysqli_query($cnmy, $query);
            //echo "$tmp04";
            
            //goto hapusdata;
            
            
        }elseif ($prptpilihtype=="DIV") {
            
            $query = "select * from $tmp01 WHERE IFNULL(COA2,'')='105'";
            $query = "create TEMPORARY table $tmp02 ($query)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "DELETE from $tmp01 WHERE COA2='105'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "select kodeinput, divisi, nkodeid, nkodeid_nama, CONCAT(DATE_FORMAT(tgltarikan,'%Y-%m'),'-01') as bulan, sum(kredit) as jumlah from $tmp01 WHERE kodeinput='A' GROUP BY 1,2,3,4,5";
            $query = "create TEMPORARY table $tmp04 ($query)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "UPDATE $tmp04 as a JOIN hrd.br_kode as b on a.nkodeid=b.kodeid SET a.nkodeid_nama=b.nama WHERE a.kodeinput='A'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "INSERT INTO $tmp04 (kodeinput, divisi, nkodeid, nkodeid_nama, bulan, jumlah)"
                    . "select kodeinput, divisi, nsubkode, nsubkode_nama, CONCAT(DATE_FORMAT(tgltarikan,'%Y-%m'),'-01') as bulan, sum(kredit) as jumlah from $tmp01 WHERE kodeinput='E' GROUP BY 1,2,3,4,5";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "UPDATE $tmp04 as a JOIN hrd.brkd_otc as b on a.nkodeid=b.kodeid SET a.nkodeid_nama=b.nama WHERE a.kodeinput='E'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "UPDATE $tmp04 SET nkodeid_nama='BIAYA LAIN2' WHERE kodeinput='E' AND IFNULL(nkodeid,'')=''";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "DELETE from $tmp01 WHERE kodeinput IN ('A', 'E')";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            $query = "INSERT INTO $tmp04 (kodeinput, divisi, nkodeid, nkodeid_nama, bulan, jumlah)"
                    . "select kodeinput, divisi, kodeinput as nsubkode, 'Klaim Discount' as nsubkode_nama, CONCAT(DATE_FORMAT(tgltarikan,'%Y-%m'),'-01') as bulan, sum(kredit) as jumlah from $tmp01 WHERE kodeinput='B' GROUP BY 1,2,3,4,5";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "INSERT INTO $tmp04 (kodeinput, divisi, nkodeid, nkodeid_nama, bulan, jumlah)"
                    . "select kodeinput, divisi, kodeinput as nsubkode, 'Kas Kecil' as nsubkode_nama, CONCAT(DATE_FORMAT(tgltarikan,'%Y-%m'),'-01') as bulan, sum(kredit) as jumlah from $tmp01 WHERE kodeinput='C' GROUP BY 1,2,3,4,5";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "INSERT INTO $tmp04 (kodeinput, divisi, nkodeid, nkodeid_nama, bulan, jumlah)"
                    . "select kodeinput, divisi, kodeinput as nsubkode, 'Kas Bon' as nsubkode_nama, CONCAT(DATE_FORMAT(tgltarikan,'%Y-%m'),'-01') as bulan, sum(kredit) as jumlah from $tmp01 WHERE kodeinput='D' GROUP BY 1,2,3,4,5";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "INSERT INTO $tmp04 (kodeinput, divisi, nkodeid, nkodeid_nama, bulan, jumlah)"
                    . "select kodeinput, divisi, kodeinput as nsubkode, 'Biaya Rutin' as nsubkode_nama, CONCAT(DATE_FORMAT(tgltarikan,'%Y-%m'),'-01') as bulan, sum(kredit) as jumlah from $tmp01 WHERE kodeinput='F' GROUP BY 1,2,3,4,5";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "INSERT INTO $tmp04 (kodeinput, divisi, nkodeid, nkodeid_nama, bulan, jumlah)"
                    . "select kodeinput, divisi, kodeinput as nsubkode, 'Luar Kota' as nsubkode_nama, CONCAT(DATE_FORMAT(tgltarikan,'%Y-%m'),'-01') as bulan, sum(kredit) as jumlah from $tmp01 WHERE kodeinput='G' GROUP BY 1,2,3,4,5";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "INSERT INTO $tmp04 (kodeinput, divisi, nkodeid, nkodeid_nama, bulan, jumlah)"
                    . "select kodeinput, divisi, kodeinput as nsubkode, 'Biaya Marketing Surabaya' as nsubkode_nama, CONCAT(DATE_FORMAT(tgltarikan,'%Y-%m'),'-01') as bulan, sum(kredit) as jumlah from $tmp01 WHERE kodeinput IN ('I', 'J') AND IFNULL(kredit,0)<>0 GROUP BY 1,2,3,4,5";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            $query = "INSERT INTO $tmp04 (kodeinput, divisi, nkodeid, nkodeid_nama, bulan, jumlah)"
                    . "select kodeinput, divisi, kodeinput as nsubkode, 'Insentif' as nsubkode_nama, CONCAT(DATE_FORMAT(tgltarikan,'%Y-%m'),'-01') as bulan, sum(kredit) as jumlah from $tmp01 WHERE kodeinput='K' GROUP BY 1,2,3,4,5";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "INSERT INTO $tmp04 (kodeinput, divisi, nkodeid, nkodeid_nama, bulan, jumlah)"
                    . "select kodeinput, divisi, kodeinput as nsubkode, 'Sewa Kontrakan Rumah' as nsubkode_nama, CONCAT(DATE_FORMAT(tgltarikan,'%Y-%m'),'-01') as bulan, sum(kredit) as jumlah from $tmp01 WHERE kodeinput='U' GROUP BY 1,2,3,4,5";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "INSERT INTO $tmp04 (kodeinput, divisi, nkodeid, nkodeid_nama, bulan, jumlah)"
                    . "select kodeinput, divisi, kodeinput as nsubkode, 'Service Kendaraan' as nsubkode_nama, CONCAT(DATE_FORMAT(tgltarikan,'%Y-%m'),'-01') as bulan, sum(kredit) as jumlah from $tmp01 WHERE kodeinput='V' GROUP BY 1,2,3,4,5";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "INSERT INTO $tmp04 (kodeinput, divisi, nkodeid, nkodeid_nama, bulan, jumlah)"
                    . "select kodeinput, divisi, kodeinput as nsubkode, 'Kas Kecil Cabang' as nsubkode_nama, CONCAT(DATE_FORMAT(tgltarikan,'%Y-%m'),'-01') as bulan, sum(kredit) as jumlah from $tmp01 WHERE kodeinput='X' GROUP BY 1,2,3,4,5";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            //BANK
            $query = "INSERT INTO $tmp04 (kodeinput, divisi, nkodeid, nkodeid_nama, bulan, jumlah)"
                    . "select kodeinput, divisi, kodeinput as nsubkode, 'Bank' as nsubkode_nama, CONCAT(DATE_FORMAT(tgltarikan,'%Y-%m'),'-01') as bulan, sum(kredit) as jumlah from $tmp01 WHERE kodeinput='M' GROUP BY 1,2,3,4,5";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            //UANG MUKA UMB
            $query = "INSERT INTO $tmp04 (kodeinput, divisi, nkodeid, nkodeid_nama, bulan, jumlah)"
                    . "select kodeinput, divisi, 'UMB' as nsubkode, 'UANG MUKA' as nsubkode_nama, CONCAT(DATE_FORMAT(tgltarikan,'%Y-%m'),'-01') as bulan, sum(kredit) as jumlah from $tmp02 GROUP BY 1,2,3,4,5";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            $query = "UPDATE $tmp04 SET divisi='ZZZ' WHERE IFNULL(divisi,'') IN ('', 'OTHER', 'OTHERS')";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "select DISTINCT divisi, nkodeid, nkodeid_nama from $tmp04";
            $query = "create TEMPORARY table $tmp03 ($query)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            $addcolumn="";
            for ($x=1;$x<=12;$x++) {
                $addcolumn .= " ADD B$x DECIMAL(20,2),ADD S$x DECIMAL(20,2),ADD R$x DECIMAL(20,2),";
            }
            $addcolumn .= " ADD TOTAL DECIMAL(20,2), ADD STOTAL DECIMAL(20,2), ADD RTOTAL DECIMAL(20,2)";

            $query = "ALTER TABLE $tmp03 $addcolumn";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $urut=2;
            for ($x=1;$x<=12;$x++) {
                $jml=  strlen($x);
                $awal=$urut-$jml;
                $nbulan=$periode."-".str_repeat("0", $awal).$x;
                $nfield="B".$x;
                $nfieldR="R".$x;

                //$query = "UPDATE $tmp03 a SET a.$nfield=(SELECT SUM(b.jumlah) FROM $tmp04 b WHERE a.divisi=b.divisi AND a.nkodeid=b.nkodeid AND DATE_FORMAT(b.bulan, '%Y-%m')='$nbulan')";
                //$query = "UPDATE $tmp03 a SET a.$nfieldR=ROUND(IFNULL($nfield,0)/IFNULL((SELECT SUM(b.jumlah) FROM $tmp04 b WHERE a.divisi=b.divisi AND a.nkodeid=b.nkodeid),0)*100,2)";
                
                $query = "UPDATE $tmp03 a JOIN (SELECT divisi, nkodeid, SUM(jumlah) as jumlah FROM $tmp04 WHERE DATE_FORMAT(bulan, '%Y-%m')='$nbulan' GROUP BY 1,2) as b "
                        . " on a.divisi=b.divisi AND a.nkodeid=b.nkodeid SET a.$nfield=b.jumlah";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                $query = "UPDATE $tmp03 a JOIN (SELECT divisi, nkodeid, SUM(jumlah) as jumlah FROM $tmp04 GROUP BY 1,2) as b "
                        . " on a.divisi=b.divisi AND a.nkodeid=b.nkodeid SET a.$nfieldR=ROUND(IFNULL($nfield,0)/IFNULL(b.jumlah,0)*100,2)";
                mysqli_query($cnmy, $query); //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


            }
            
            $query = "UPDATE $tmp03 a JOIN (SELECT divisi, nkodeid, SUM(jumlah) as jumlah FROM $tmp04 GROUP BY 1,2) as b "
                    . " on a.divisi=b.divisi AND a.nkodeid=b.nkodeid SET a.TOTAL=b.jumlah";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "ALTER TABLE $tmp03 ADD COLUMN ikode VARCHAR(1)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "UPDATE $tmp03 SET ikode='A' WHERE IFNULL(nkodeid,'') <>'UMB'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            $query = "UPDATE $tmp03 SET ikode='Z' WHERE IFNULL(nkodeid,'') ='UMB'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
            
        }else{
        
            $query = "select *, kredit as jumlah from $tmp01";
            $query = "create TEMPORARY table $tmp02 ($query)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query = "select DISTINCT a.divisi DIVISI, b.COA1, c.NAMA1, a.coa2 COA2, a.nama_coa2 NAMA2, "
                    . " a.coa3 COA3, a.nama_coa3 NAMA3, coa COA4, nama_coa NAMA4 "
                    . " from $tmp02 a LEFT JOIN dbmaster.coa_level2 b on "
                    . " a.coa2=b.COA2 LEFT JOIN dbmaster.coa_level1 c on "
                    . " b.COA1=c.COA1";
            $query = "create TEMPORARY table $tmp03 ($query)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


            $addcolumn="";
            for ($x=1;$x<=12;$x++) {
                $addcolumn .= " ADD B$x DECIMAL(20,2),ADD S$x DECIMAL(20,2),ADD R$x DECIMAL(20,2),";
            }
            $addcolumn .= " ADD TOTAL DECIMAL(20,2), ADD STOTAL DECIMAL(20,2), ADD RTOTAL DECIMAL(20,2)";

            $query = "ALTER TABLE $tmp03 $addcolumn";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


            $urut=2;
            for ($x=1;$x<=12;$x++) {
                $jml=  strlen($x);
                $awal=$urut-$jml;
                $nbulan=$periode."-".str_repeat("0", $awal).$x;
                $nfield="B".$x;
                $nfieldR="R".$x;

                $query = "UPDATE $tmp03 a SET a.$nfield=(SELECT SUM(b.kredit) FROM $tmp02 b WHERE a.DIVISI=b.divisi AND a.COA4=b.coa AND DATE_FORMAT(b.tgltarikan, '%Y-%m')='$nbulan')";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

                $query = "UPDATE $tmp03 a SET a.$nfieldR=ROUND(IFNULL($nfield,0)/IFNULL((SELECT SUM(b.kredit) FROM $tmp02 b WHERE a.DIVISI=b.divisi AND a.COA4=b.coa),0)*100,2)";
                mysqli_query($cnmy, $query); //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


            }
            //goto hapusdata;

                $query = "UPDATE $tmp03 set DIVISI='ZZZ' WHERE IFNULL(DIVISI,'') IN ('', 'OTHER', 'OTHERS')";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

                $query = "ALTER table $tmp03 ADD COLUMN DIVISI_IN_COA VARCHAR(50)";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                $query = "UPDATE $tmp03 set DIVISI_IN_COA=DIVISI";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query = "select * from $tmp02 WHERE COA2='105'";
            $query = "create TEMPORARY table $tmp04 ($query)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


            $query = "select * from $tmp03 WHERE COA2='105'";
            $query = "create TEMPORARY table $tmp05 ($query)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


            $query = "DELETE from $tmp02 WHERE COA2='105'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query = "DELETE from $tmp03 WHERE COA2='105'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        }
    
?>

<HTML>
<HEAD>
    <title>REPORT REALISASI BIAYA MARKETING</title>
    
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Mei 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <!--<link href="css/laporanbaru.css" rel="stylesheet">-->
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        
        <!-- Bootstrap -->
        <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    
        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">


        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">


        <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        
		
        <!-- jQuery -->
        <script src="vendors/jquery/dist/jquery.min.js"></script>
        
        
    <?PHP } ?>
        
</HEAD>

<BODY>

<?PHP if ($ppilihrpt!="excel") { ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?PHP } ?>
    
    
<div id='n_content'>

    <center><div class='h1judul'>REPORT REALISASI BIAYA MARKETING</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <tr><td>Tahun</td><td>:</td><td><?PHP echo "<b>$periode</b>"; ?></td></tr>
            <tr class='miring text2'><td>Proses Terakhir</td><td>:</td><td><?PHP echo "$ptanggalprosesnya"; ?></td></tr>
            <?PHP
            
            if (!empty($pnamadivisi)) {
                echo "<tr><td>Divisi</td><td>:</td><td>$pnamadivisi</td></tr>";
            }
            if (!empty($ptipereport)) {
                echo "<tr><td>Report Type By</td><td>:</td><td>$ptipereport</td></tr>";
            }
            ?>
            <tr class='miring text2'><td>view date</td><td>:</td><td><?PHP echo "$printdate"; ?></td></tr>
        </table>
    </div>
    <div class="clearfix"></div>
    <hr/>
    
    <?PHP
    if ($prptpilihtype=="BMB") {
    ?>
    
    <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
        <thead>
            <tr>
            <th align="center" rowspan="1">NO</th>
            <th align="center" rowspan="1">KETERANGAN</th>
            
            <th align="center" nowrap>JANUARI</th>
            <th align="center" nowrap>FEBRUARI</th>
            <th align="center" nowrap>MARET</th>
            <th align="center" nowrap>APRIL</th>
            <th align="center" nowrap>MEI</th>
            <th align="center" nowrap>JUNI</th>
            <th align="center" nowrap>JULI</th>
            <th align="center" nowrap>AGUSTUS</th>
            <th align="center" nowrap>SEPTEMBER</th>
            <th align="center" nowrap>OKTOBER</th>
            <th align="center" nowrap>NOVEMBER</th>
            <th align="center" nowrap>DESEMBER</th>
            <th align="center" nowrap>TOTAL</th>
                    
            </tr>
        </thead>
        <tbody>
            <?PHP
            $query = "select * FROM $tmp00 order by nourut";
            $tampil=mysqli_query($cnmy, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $pnourut=$row['nourut'];
                $pno=$row['no'];
                $pjudul=$row['keterangan'];
                
                echo "<tr>";
                echo "<td nowrap>$pno</td>";
                echo "<td nowrap>$pjudul</td>";

                $ptotaltahund=0;
                for ($x=1;$x<=12;$x++) {
                    $nmcol="A".$x;
                    $pjml=$row[$nmcol];
                    if (empty($pjml)) $pjml=0;;

                    $ptotaltahund=(double)$ptotaltahund+(double)$pjml;
                    $pjml=BuatFormatNum($pjml, $ppilformat);

                    echo "<td nowrap align='right'>$pjml</td>";
                }

                $ptotaltahund=BuatFormatNum($ptotaltahund, $ppilformat);
                
                echo "<td align='right' nowrap align='right'><b>$ptotaltahund</b></td>";

                echo "</tr>";
                
            }
            ?>
        </tbody>
    </table>
    
    <?PHP
        if ($prptpilihtype=="BMB" AND $ppildivisiid<>"OTC") {
            echo "<br/><br/><br/>";
            
            
            $jfilterkodenotin="";
            
            echo "<br/>Mapping Budget Request Ethical</br>";
            echo "<table  id='mydatatable1' class='table table-striped table-bordered' width='100%' border='1px solid black'>";
            
            echo "<thead>";
            echo "<tr>";
                echo "<th nowrap>Keterangan</th>";
                echo "<th nowrap>Subposting</th>";
            echo "</tr>";
            echo "</thead>";
            
            echo "<tbody>";
            
            $query = "select a.kodeid, b.keterangan, a.kode_akun, c.nama from dbmaster.t_budget_kode_d as a "
                    . " JOIN dbmaster.t_budget_realisasi_lap as b on a.kodeid=b.kodeid "
                    . " join hrd.br_kode as c on a.kode_akun=c.kodeid "
                    . " order by a.kodeid, a.kode_akun";
            $tampil=mysqli_query($cnmy, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $jkodeid=$row['kodeid'];
                $jketerangan=$row['keterangan'];
                $jidkode=$row['kode_akun'];
                $jnmkode=$row['nama'];
                
                $jfilterkodenotin .="'".$jidkode."',";
                
                echo "<tr>";
                echo "<td nowrap>$jkodeid - $jketerangan</td>";
                echo "<td nowrap>$jidkode - $jnmkode</td>";
                echo "</tr>";
                
            }
            
            if (!empty($jfilterkodenotin)) {
                $jfilterkodenotin="(".substr($jfilterkodenotin, 0, -1).")";
                
                $query = "select kodeid, nama from hrd.br_kode where IFNULL(br,'')='Y' AND kodeid NOT IN $jfilterkodenotin "
                        . " ORDER BY kodeid, nama";
                $tampil2=mysqli_query($cnmy, $query);
                while ($row2= mysqli_fetch_array($tampil2)) {
                    $jketerangan="Yang Belum Masuk Mapping, Masuk ke HO (1. HEAD OFFICE)";
                    $jidkode=$row2['kodeid'];
                    $jnmkode=$row2['nama'];
                    
                    echo "<tr>";
                    echo "<td nowrap>$jketerangan</td>";
                    echo "<td nowrap>$jidkode - $jnmkode</td>";
                    echo "</tr>";
                    
                }
                
            }
            
            
            echo "</tbody>";
            echo "</table>";
            
            
        }elseif ($prptpilihtype=="BMB" AND $ppildivisiid=="OTC") {
            echo "<br/><br/><br/>";
            
            
            $jfilterkodenotin="";
            
            echo "<br/>Mapping Budget Request CHC</br>";
            echo "<table  id='mydatatable1' class='table table-striped table-bordered' width='100%' border='1px solid black'>";
            
            echo "<thead>";
            echo "<tr>";
                echo "<th nowrap>Keterangan</th>";
                echo "<th nowrap>Posting</th>";
                echo "<th nowrap>Subposting</th>";
            echo "</tr>";
            echo "</thead>";
            
            echo "<tbody>";
            
            $query = "select a.kodeid, b.keterangan, a.kode_akun, c.nmsubpost, a.kode_akun_sub, d.nama "
                    . " from dbmaster.t_budget_kode_otc_d as a join dbmaster.t_budget_realisasi_lap_otc as b "
                    . " on a.kodeid=b.kodeid join (select distinct subpost, nmsubpost from hrd.brkd_otc) as c "
                    . " on a.kode_akun=c.subpost left join hrd.brkd_otc as d on a.kode_akun_sub=d.kodeid "
                . " order by a.kodeid";
            $tampil=mysqli_query($cnmy, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $jkodeid=$row['kodeid'];
                $jketerangan=$row['keterangan'];
                $jsubpostkd=$row['kode_akun'];
                $jsubpostnm=$row['nmsubpost'];
                $jidkode=$row['kode_akun_sub'];
                $jnmkode=$row['nama'];
                
                $pkodeandsub=$jsubpostkd."".$jidkode;
                
                if ($jsubpostkd=="01" OR $jsubpostkd=="02" OR $jsubpostkd=="08") {
                }else{
                    $jfilterkodenotin .="'".$pkodeandsub."',";
                }
                
                echo "<tr>";
                echo "<td nowrap>$jkodeid - $jketerangan</td>";
                echo "<td nowrap>$jsubpostkd - $jsubpostnm</td>";
                echo "<td nowrap>$jidkode - $jnmkode</td>";
                echo "</tr>";
                
            }
            
            if (!empty($jfilterkodenotin)) {
                $jfilterkodenotin="(".substr($jfilterkodenotin, 0, -1).")";
                
                $query = "select subpost, nmsubpost, kodeid, nama from hrd.brkd_otc where ifnull(aktif,'')='Y' and subpost not in ('08', '01', '02') "
                        . " AND concat(subpost, kodeid) NOT IN $jfilterkodenotin "
                        . " ORDER BY nmsubpost, nama";
                $tampil2=mysqli_query($cnmy, $query);
                while ($row2= mysqli_fetch_array($tampil2)) {
                    $jketerangan="Yang Belum Masuk Mapping, Masuk ke HO (V. HO)";
                    $jsubpostkd=$row2['subpost'];
                    $jsubpostnm=$row2['nmsubpost'];
                    $jidkode=$row2['kodeid'];
                    $jnmkode=$row2['nama'];
                    
                    echo "<tr>";
                    echo "<td nowrap>$jketerangan</td>";
                    echo "<td nowrap>$jsubpostkd - $jsubpostnm</td>";
                    echo "<td nowrap>$jidkode - $jnmkode</td>";
                    echo "</tr>";
                    
                }
            }
            
            
            echo "</tbody>";
            echo "</table>";
            
            
            
        }
        
    ?>
    
    
    <?PHP
    }elseif ($prptpilihtype=="DIV") {
    ?>
        <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                    <th align="center" nowrap>Divisi</th>
                    <th align="center" nowrap>Akun</th>

                    <th align="center" nowrap>1</th>
                    <th align="center" nowrap>JANUARI</th>
                    <th align="center" nowrap>2</th>
                    <th align="center" nowrap>FEBRUARI</th>
                    <th align="center" nowrap>3</th>
                    <th align="center" nowrap>MARET</th>
                    <th align="center" nowrap>4</th>
                    <th align="center" nowrap>APRIL</th>
                    <th align="center" nowrap>5</th>
                    <th align="center" nowrap>MEI</th>
                    <th align="center" nowrap>6</th>
                    <th align="center" nowrap>JUNI</th>
                    <th align="center" nowrap>7</th>
                    <th align="center" nowrap>JULI</th>
                    <th align="center" nowrap>8</th>
                    <th align="center" nowrap>AGUSTUS</th>
                    <th align="center" nowrap>9</th>
                    <th align="center" nowrap>SEPTEMBER</th>
                    <th align="center" nowrap>10</th>
                    <th align="center" nowrap>OKTOBER</th>
                    <th align="center" nowrap>11</th>
                    <th align="center" nowrap>NOVEMBER</th>
                    <th align="center" nowrap>12</th>
                    <th align="center" nowrap>DESEMBER</th>
                    <th align="center" nowrap>%</th>
                    <th align="center" nowrap>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                    for ($x=1;$x<=12;$x++) {
                        $pgrandtotal[$x]=0;
                        $pgrandtotalsls[$x]=0;
                    }

                    for ($x=1;$x<=12;$x++) {
                        $ptotdivisi[$x]=0;
                        $ptotdivisisls[$x]=0;
                    }

                    for ($x=1;$x<=12;$x++) {
                        $psubtot[$x]=0;
                        $psubtotsales[$x]=0;
                    }
                        
                    $query = "select distinct ikode from $tmp03 ORDER BY ikode";
                    $tampil1=mysqli_query($cnmy, $query);
                    while ($row1= mysqli_fetch_array($tampil1)) {
                        $pikode=$row1['ikode'];
                        $pnamabiaya ="BIAYA MARKETING";
                        if ($pikode=="Z") $pnamabiaya ="UANG MUKA";
                        echo "<tr>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap><b>$pnamabiaya</b></td>";

                        for ($x=1;$x<=12;$x++) {
                            echo "<td nowrap align='right'></td>";
                            echo "<td nowrap align='right'></td>";
                        }


                        echo "<td align='right' nowrap><b></b></td>";
                        echo "<td align='right' nowrap><b></b></td>";

                        echo "</tr>";
                        
                        
                        for ($x=1;$x<=12;$x++) {
                            $pgrandtotal[$x]=0;
                            $pgrandtotalsls[$x]=0;
                        }

                        for ($x=1;$x<=12;$x++) {
                            $ptotdivisi[$x]=0;
                            $ptotdivisisls[$x]=0;
                        }

                        for ($x=1;$x<=12;$x++) {
                            $psubtot[$x]=0;
                            $psubtotsales[$x]=0;
                        }
                        
                        $query = "select distinct divisi as DIVISI from $tmp03 WHERE ikode='$pikode' ORDER BY divisi";
                        $tampil0=mysqli_query($cnmy, $query);
                        while ($row0= mysqli_fetch_array($tampil0)) {

                            $pdivisi=$row0['DIVISI'];

                            $nmdivisi=$pdivisi;
                            if ($pdivisi=="CAN") $nmdivisi="CANARY";
                            if ($pdivisi=="PIGEO") $nmdivisi="PIGEON";
                            if ($pdivisi=="PEACO") $nmdivisi="PEACOCK";
                            if ($pdivisi=="AA") $nmdivisi="OTHER";
                            if ($pdivisi=="ZZZ") $nmdivisi="OTHER";

                            for ($x=1;$x<=12;$x++) {
                                $psubtot[$x]=0;
                                $psubtotsales[$x]=0;
                            }


                            $query = "select * from $tmp03 WHERE ikode='$pikode' AND divisi='$pdivisi' order by nkodeid_nama, nkodeid";
                            $tampil2=mysqli_query($cnmy, $query);
                            while ($row2= mysqli_fetch_array($tampil2)) {
                                $pnkodeid=$row2['nkodeid'];
                                $pnkodenm=$row2['nkodeid_nama'];

                                echo "<tr>";
                                echo "<td nowrap>$nmdivisi</td>";
                                echo "<td nowrap>$pnkodenm</td>";

                                $ptotaltahund=0;
                                for ($x=1;$x<=12;$x++) {
                                    $nmcol="B".$x;
                                    $pjml=$row2[$nmcol];
                                    if (empty($pjml)) $pjml=0;

                                    $nmcolR="R".$x;
                                    $pjmlR=$row2[$nmcolR];
                                    if (empty($pjmlR)) $pjmlR=0;

                                    $ptotaltahund=(double)$ptotaltahund+(double)$pjml;
                                    $psubtot[$x]=(double)$psubtot[$x]+(double)$pjml;
                                    $ptotdivisi[$x]=(double)$ptotdivisi[$x]+(double)$pjml;
                                    $pgrandtotal[$x]=(double)$pgrandtotal[$x]+(double)$pjml;


                                    $pjml=BuatFormatNum($pjml, $ppilformat);

                                    echo "<td nowrap align='right'>$pjmlR</td>";
                                    echo "<td nowrap align='right'>$pjml</td>";
                                }

                                $ptotaltahund=BuatFormatNum($ptotaltahund, $ppilformat);

                                echo "<td align='right' nowrap align='right'><b></b></td>";
                                echo "<td align='right' nowrap align='right'><b>$ptotaltahund</b></td>";

                                echo "</tr>";

                            }


                            //sub total
                            echo "<tr>";
                            echo "<td nowrap><b>Total </b></td>";
                            echo "<td nowrap><b>$nmdivisi</b></td>";

                            $ptotpersubcoa=0;
                            for ($sx=1;$sx<=12;$sx++) {
                                $pjmlsb=$psubtot[$sx];
                                if (empty($pjmlsb)) $pjmlsb=0;
                                $ptotpersubcoa=(DOUBLE)$ptotpersubcoa+(DOUBLE)$pjmlsb;
                            }

                            $ptotaltahund=0;
                            for ($x=1;$x<=12;$x++) {
                                $pjml=$psubtot[$x];
                                if (empty($pjml)) $pjml=0;

                                $prjumlah=0;
                                if ((DOUBLE)$ptotpersubcoa>0) {
                                    $prjumlah=ROUND((DOUBLE)$pjml/(DOUBLE)$ptotpersubcoa*100,2);
                                }

                                $ptotaltahund=(double)$ptotaltahund+(double)$pjml;
                                $pjml=BuatFormatNum($pjml, $ppilformat);

                                echo "<td nowrap align='right'><b>$prjumlah</b></td>";
                                echo "<td nowrap align='right'><b>$pjml</b></td>";
                            }

                            $ptotaltahund=BuatFormatNum($ptotaltahund, $ppilformat);

                            echo "<td align='right' nowrap><b></b></td>";
                            echo "<td align='right' nowrap><b>$ptotaltahund</b></td>";

                            echo "</tr>";

                            if ($ppilihrpt!="excel") {
                                echo "<tr>";
                                echo "<td nowrap colspan=28><b></b></td>";
                                echo "</tr>";
                            }


                        }

                        //total 
                        echo "<tr>";
                        echo "<td nowrap><b></b></td>";
                        echo "<td nowrap><b>TOTAL $pnamabiaya</b></td>";


                        $ptotpersubcoa=0;
                        for ($sx=1;$sx<=12;$sx++) {
                            $pjmlsb=$ptotdivisi[$sx];
                            if (empty($pjmlsb)) $pjmlsb=0;
                            $ptotpersubcoa=(DOUBLE)$ptotpersubcoa+(DOUBLE)$pjmlsb;
                        }

                        $ptotaltahund=0;
                        for ($x=1;$x<=12;$x++) {
                            $pjml=$ptotdivisi[$x];
                            if (empty($pjml)) $pjml=0;

                            $prjumlah=0;
                            if ((DOUBLE)$ptotpersubcoa>0) {
                                $prjumlah=ROUND((DOUBLE)$pjml/(DOUBLE)$ptotpersubcoa*100,2);
                            }

                            $ptotaltahund=(double)$ptotaltahund+(double)$pjml;
                            $pjml=BuatFormatNum($pjml, $ppilformat);

                            echo "<td nowrap align='right'><b>$prjumlah</b></td>";
                            echo "<td nowrap align='right'><b>$pjml</b></td>";
                        }

                        $ptotaltahund=BuatFormatNum($ptotaltahund, $ppilformat);

                        echo "<td align='right' nowrap><b></b></td>";
                        echo "<td align='right' nowrap><b>$ptotaltahund</b></td>";

                        echo "</tr>";

                        if ($ppilihrpt!="excel") {
                            echo "<tr>";
                            echo "<td nowrap colspan=28><b></b></td>";
                            echo "</tr>";
                        }
                        
                    }
                ?>
            </tbody>
        </table>
    <?PHP
    }else{
    ?>
    
        <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                    <th align="center" nowrap>Kode</th>
                    <th align="center" nowrap>Nama Perkiraan</th>

                    <th align="center" nowrap>1</th>
                    <th align="center" nowrap>JANUARI</th>
                    <th align="center" nowrap>2</th>
                    <th align="center" nowrap>FEBRUARI</th>
                    <th align="center" nowrap>3</th>
                    <th align="center" nowrap>MARET</th>
                    <th align="center" nowrap>4</th>
                    <th align="center" nowrap>APRIL</th>
                    <th align="center" nowrap>5</th>
                    <th align="center" nowrap>MEI</th>
                    <th align="center" nowrap>6</th>
                    <th align="center" nowrap>JUNI</th>
                    <th align="center" nowrap>7</th>
                    <th align="center" nowrap>JULI</th>
                    <th align="center" nowrap>8</th>
                    <th align="center" nowrap>AGUSTUS</th>
                    <th align="center" nowrap>9</th>
                    <th align="center" nowrap>SEPTEMBER</th>
                    <th align="center" nowrap>10</th>
                    <th align="center" nowrap>OKTOBER</th>
                    <th align="center" nowrap>11</th>
                    <th align="center" nowrap>NOVEMBER</th>
                    <th align="center" nowrap>12</th>
                    <th align="center" nowrap>DESEMBER</th>
                    <th align="center" nowrap>%</th>
                    <th align="center" nowrap>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                    for ($x=1;$x<=12;$x++) {
                        $pgrandtotal[$x]=0;
                        $pgrandtotalsls[$x]=0;
                    }
                    $query = "select distinct DIVISI_IN_COA as DIVISI from $tmp03 ORDER BY DIVISI_IN_COA";
                    $tampil0=mysqli_query($cnmy, $query);
                    while ($row0= mysqli_fetch_array($tampil0)) {

                        $pdivisi=$row0['DIVISI'];

                        $nmdivisi=$pdivisi;
                        if ($pdivisi=="CAN") $nmdivisi="CANARY";
                        if ($pdivisi=="PIGEO") $nmdivisi="PIGEON";
                        if ($pdivisi=="PEACO") $nmdivisi="PEACOCK";
                        if ($pdivisi=="AA") $nmdivisi="OTHER";
                        if ($pdivisi=="ZZZ") $nmdivisi="OTHER";


                        for ($x=1;$x<=12;$x++) {
                            $ptotdivisi[$x]=0;
                            $ptotdivisisls[$x]=0;
                        }

                        $query = "select distinct IFNULL(DIVISI_IN_COA,'') as DIVISI, IFNULL(COA2,'') COA2, IFNULL(NAMA2,'') NAMA2 from $tmp03 "
                                . " WHERE DIVISI_IN_COA='$pdivisi' ORDER BY IFNULL(DIVISI_IN_COA,''), IFNULL(COA2,''), IFNULL(NAMA2,'')";
                        $tampil=mysqli_query($cnmy, $query);
                        while ($row= mysqli_fetch_array($tampil)) {

                            $pcoa2=$row['COA2'];
                            $pnmcoa2=$row['NAMA2'];


                            echo "<tr>";
                            echo "<td nowrap><b>$pcoa2</b></td>";
                            echo "<td nowrap colspan=27><b>$pnmcoa2</b></td>";
                            echo "</tr>";


                            for ($x=1;$x<=12;$x++) {
                                $psubtot[$x]=0;
                                $psubtotsales[$x]=0;
                            }

                            $query = "select * from $tmp03 WHERE IFNULL(DIVISI_IN_COA,'')='$pdivisi' AND IFNULL(COA2,'')='$pcoa2' "
                                    . " ORDER BY IFNULL(COA4,''), IFNULL(NAMA4,'')";
                            $tampil2=mysqli_query($cnmy, $query);
                            while ($row2= mysqli_fetch_array($tampil2)) {
                                $pcoa4=$row2['COA4'];
                                $pnmcoa4=$row2['NAMA4'];

                                echo "<tr>";
                                echo "<td nowrap>$pcoa4</td>";
                                echo "<td nowrap>$pnmcoa4</td>";

                                $ptotaltahund=0;
                                for ($x=1;$x<=12;$x++) {
                                    $nmcol="B".$x;
                                    $pjml=$row2[$nmcol];
                                    if (empty($pjml)) $pjml=0;

                                    $nmcolR="R".$x;
                                    $pjmlR=$row2[$nmcolR];
                                    if (empty($pjmlR)) $pjmlR=0;

                                    $ptotaltahund=(double)$ptotaltahund+(double)$pjml;
                                    $psubtot[$x]=(double)$psubtot[$x]+(double)$pjml;
                                    $ptotdivisi[$x]=(double)$ptotdivisi[$x]+(double)$pjml;
                                    $pgrandtotal[$x]=(double)$pgrandtotal[$x]+(double)$pjml;


                                    $pjml=BuatFormatNum($pjml, $ppilformat);

                                    echo "<td nowrap align='right'>$pjmlR</td>";
                                    echo "<td nowrap align='right'>$pjml</td>";
                                }

                                $ptotaltahund=BuatFormatNum($ptotaltahund, $ppilformat);

                                echo "<td align='right' nowrap align='right'><b></b></td>";
                                echo "<td align='right' nowrap align='right'><b>$ptotaltahund</b></td>";

                                echo "</tr>";


                            }

                            //sub total
                            echo "<tr>";
                            echo "<td nowrap><b>$pcoa2</b></td>";
                            echo "<td nowrap><b>$pnmcoa2</b></td>";

                            $ptotpersubcoa=0;
                            for ($sx=1;$sx<=12;$sx++) {
                                $pjmlsb=$psubtot[$sx];
                                if (empty($pjmlsb)) $pjmlsb=0;
                                $ptotpersubcoa=(DOUBLE)$ptotpersubcoa+(DOUBLE)$pjmlsb;
                            }

                            $ptotaltahund=0;
                            for ($x=1;$x<=12;$x++) {
                                $pjml=$psubtot[$x];
                                if (empty($pjml)) $pjml=0;

                                $prjumlah=0;
                                if ((DOUBLE)$ptotpersubcoa>0) {
                                    $prjumlah=ROUND((DOUBLE)$pjml/(DOUBLE)$ptotpersubcoa*100,2);
                                }

                                $ptotaltahund=(double)$ptotaltahund+(double)$pjml;
                                $pjml=BuatFormatNum($pjml, $ppilformat);

                                echo "<td nowrap align='right'><b>$prjumlah</b></td>";
                                echo "<td nowrap align='right'><b>$pjml</b></td>";
                            }

                            $ptotaltahund=BuatFormatNum($ptotaltahund, $ppilformat);

                            echo "<td align='right' nowrap><b></b></td>";
                            echo "<td align='right' nowrap><b>$ptotaltahund</b></td>";

                            echo "</tr>";

                            if ($ppilihrpt!="excel") {
                                echo "<tr>";
                                echo "<td nowrap colspan=28><b></b></td>";
                                echo "</tr>";
                            }


                        }


                        //total per divisi
                        echo "<tr>";
                        echo "<td nowrap><b></b></td>";
                        echo "<td nowrap><b>BIAYA $nmdivisi</b></td>";


                        $ptotpersubcoa=0;
                        for ($sx=1;$sx<=12;$sx++) {
                            $pjmlsb=$ptotdivisi[$sx];
                            if (empty($pjmlsb)) $pjmlsb=0;
                            $ptotpersubcoa=(DOUBLE)$ptotpersubcoa+(DOUBLE)$pjmlsb;
                        }

                        $ptotaltahund=0;
                        for ($x=1;$x<=12;$x++) {
                            $pjml=$ptotdivisi[$x];
                            if (empty($pjml)) $pjml=0;

                            $prjumlah=0;
                            if ((DOUBLE)$ptotpersubcoa>0) {
                                $prjumlah=ROUND((DOUBLE)$pjml/(DOUBLE)$ptotpersubcoa*100,2);
                            }

                            $ptotaltahund=(double)$ptotaltahund+(double)$pjml;
                            $pjml=BuatFormatNum($pjml, $ppilformat);

                            echo "<td nowrap align='right'><b>$prjumlah</b></td>";
                            echo "<td nowrap align='right'><b>$pjml</b></td>";
                        }

                        $ptotaltahund=BuatFormatNum($ptotaltahund, $ppilformat);

                        echo "<td align='right' nowrap><b></b></td>";
                        echo "<td align='right' nowrap><b>$ptotaltahund</b></td>";

                        echo "</tr>";

                        if ($ppilihrpt!="excel") {
                            echo "<tr>";
                            echo "<td nowrap colspan=28><b></b></td>";
                            echo "</tr>";
                        }


                    }

                    //total biaya marketing
                    echo "<tr>";
                    echo "<td nowrap><b></b></td>";
                    echo "<td nowrap><b>TOTAL BIAYA MARKETING</b></td>";

                    $ptotpersubcoa=0;
                    for ($sx=1;$sx<=12;$sx++) {
                        $pjmlsb=$pgrandtotal[$sx];
                        if (empty($pjmlsb)) $pjmlsb=0;
                        $ptotpersubcoa=(DOUBLE)$ptotpersubcoa+(DOUBLE)$pjmlsb;
                    }

                    $ptotaltahund=0;
                    for ($x=1;$x<=12;$x++) {
                        $pjml=$pgrandtotal[$x];
                        if (empty($pjml)) $pjml=0;

                        $prjumlah=0;
                        if ((DOUBLE)$ptotpersubcoa>0) {
                            $prjumlah=ROUND((DOUBLE)$pjml/(DOUBLE)$ptotpersubcoa*100,2);
                        }

                        $ptotaltahund=(double)$ptotaltahund+(double)$pjml;
                        $pjml=BuatFormatNum($pjml, $ppilformat);

                        echo "<td nowrap align='right'><b>$prjumlah</b></td>";
                        echo "<td nowrap align='right'><b>$pjml<b></td>";
                    }

                    $ptotaltahund=BuatFormatNum($ptotaltahund, $ppilformat);

                    echo "<td align='right' nowrap><b></b></td>";
                    echo "<td align='right' nowrap><b>$ptotaltahund</b></td>";

                    echo "</tr>";

                    if ($ppilihrpt!="excel") {
                        echo "<tr>";
                        echo "<td nowrap colspan=28><b></b></td>";
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>


        <?PHP
        $query = "select * from $tmp05";
        $tampiln=mysqli_query($cnmy, $query);
        $ketemuan= mysqli_fetch_array($tampiln);
        if ($ketemuan>0) {
        ?>
            <br/><hr/> <div class="clearfix"></div>

            <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
                <thead>
                    <tr style='background-color:#cccccc; font-size: 13px;'>
                        <th align="center" nowrap>Kode</th>
                        <th align="center" nowrap>Nama Perkiraan</th>

                        <th align="center" nowrap>1</th>
                        <th align="center" nowrap>JANUARI</th>
                        <th align="center" nowrap>2</th>
                        <th align="center" nowrap>FEBRUARI</th>
                        <th align="center" nowrap>3</th>
                        <th align="center" nowrap>MARET</th>
                        <th align="center" nowrap>4</th>
                        <th align="center" nowrap>APRIL</th>
                        <th align="center" nowrap>5</th>
                        <th align="center" nowrap>MEI</th>
                        <th align="center" nowrap>6</th>
                        <th align="center" nowrap>JUNI</th>
                        <th align="center" nowrap>7</th>
                        <th align="center" nowrap>JULI</th>
                        <th align="center" nowrap>8</th>
                        <th align="center" nowrap>AGUSTUS</th>
                        <th align="center" nowrap>9</th>
                        <th align="center" nowrap>SEPTEMBER</th>
                        <th align="center" nowrap>10</th>
                        <th align="center" nowrap>OKTOBER</th>
                        <th align="center" nowrap>11</th>
                        <th align="center" nowrap>NOVEMBER</th>
                        <th align="center" nowrap>12</th>
                        <th align="center" nowrap>DESEMBER</th>
                        <th align="center" nowrap>%</th>
                        <th align="center" nowrap>TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?PHP
                        for ($x=1;$x<=12;$x++) {
                            $pgrandtotal[$x]=0;
                            $pgrandtotalsls[$x]=0;
                        }
                        $query = "select distinct DIVISI from $tmp05 ORDER BY DIVISI";
                        $tampil0=mysqli_query($cnmy, $query);
                        while ($row0= mysqli_fetch_array($tampil0)) {

                            $pdivisi=$row0['DIVISI'];

                            $nmdivisi=$pdivisi;
                            if ($pdivisi=="CAN") $nmdivisi="CANARY";
                            if ($pdivisi=="PIGEO") $nmdivisi="PIGEON";
                            if ($pdivisi=="PEACO") $nmdivisi="PEACOCK";
                            if ($pdivisi=="AA") $nmdivisi="OTHER";
                            if ($pdivisi=="ZZZ") $nmdivisi="OTHER";


                            for ($x=1;$x<=12;$x++) {
                                $ptotdivisi[$x]=0;
                                $ptotdivisisls[$x]=0;
                            }

                            $query = "select distinct IFNULL(DIVISI,'') DIVISI, IFNULL(COA2,'') COA2, IFNULL(NAMA2,'') NAMA2 from $tmp05 "
                                    . " WHERE DIVISI='$pdivisi' ORDER BY IFNULL(DIVISI,''), IFNULL(COA2,''), IFNULL(NAMA2,'')";
                            $tampil=mysqli_query($cnmy, $query);
                            while ($row= mysqli_fetch_array($tampil)) {

                                $pcoa2=$row['COA2'];
                                $pnmcoa2=$row['NAMA2'];


                                echo "<tr>";
                                echo "<td nowrap><b>$pcoa2</b></td>";
                                echo "<td nowrap colspan=27><b>$pnmcoa2</b></td>";
                                echo "</tr>";


                                for ($x=1;$x<=12;$x++) {
                                    $psubtot[$x]=0;
                                    $psubtotsales[$x]=0;
                                }

                                $query = "select * from $tmp05 WHERE IFNULL(DIVISI,'')='$pdivisi' AND IFNULL(COA2,'')='$pcoa2' "
                                        . " ORDER BY IFNULL(COA4,''), IFNULL(NAMA4,'')";
                                $tampil2=mysqli_query($cnmy, $query);
                                while ($row2= mysqli_fetch_array($tampil2)) {
                                    $pcoa4=$row2['COA4'];
                                    $pnmcoa4=$row2['NAMA4'];

                                    echo "<tr>";
                                    echo "<td nowrap>$pcoa4</td>";
                                    echo "<td nowrap>$pnmcoa4</td>";

                                    $ptotaltahund=0;
                                    for ($x=1;$x<=12;$x++) {
                                        $nmcol="B".$x;
                                        $pjml=$row2[$nmcol];
                                        if (empty($pjml)) $pjml=0;

                                        $nmcolR="R".$x;
                                        $pjmlR=$row2[$nmcolR];
                                        if (empty($pjmlR)) $pjmlR=0;

                                        $ptotaltahund=(double)$ptotaltahund+(double)$pjml;
                                        $psubtot[$x]=(double)$psubtot[$x]+(double)$pjml;
                                        $ptotdivisi[$x]=(double)$ptotdivisi[$x]+(double)$pjml;
                                        $pgrandtotal[$x]=(double)$pgrandtotal[$x]+(double)$pjml;


                                        $pjml=BuatFormatNum($pjml, $ppilformat);

                                        echo "<td nowrap align='right'>$pjmlR</td>";
                                        echo "<td nowrap align='right'>$pjml</td>";
                                    }

                                    $ptotaltahund=BuatFormatNum($ptotaltahund, $ppilformat);

                                    echo "<td align='right' nowrap align='right'><b></b></td>";
                                    echo "<td align='right' nowrap align='right'><b>$ptotaltahund</b></td>";

                                    echo "</tr>";


                                }

                                //sub total
                                echo "<tr>";
                                echo "<td nowrap><b>$pcoa2</b></td>";
                                echo "<td nowrap><b>$pnmcoa2</b></td>";

                                $ptotpersubcoa=0;
                                for ($sx=1;$sx<=12;$sx++) {
                                    $pjmlsb=$psubtot[$sx];
                                    if (empty($pjmlsb)) $pjmlsb=0;
                                    $ptotpersubcoa=(DOUBLE)$ptotpersubcoa+(DOUBLE)$pjmlsb;
                                }

                                $ptotaltahund=0;
                                for ($x=1;$x<=12;$x++) {
                                    $pjml=$psubtot[$x];
                                    if (empty($pjml)) $pjml=0;

                                    $prjumlah=0;
                                    if ((DOUBLE)$ptotpersubcoa>0) {
                                        $prjumlah=ROUND((DOUBLE)$pjml/(DOUBLE)$ptotpersubcoa*100,2);
                                    }

                                    $ptotaltahund=(double)$ptotaltahund+(double)$pjml;
                                    $pjml=BuatFormatNum($pjml, $ppilformat);

                                    echo "<td nowrap align='right'><b>$prjumlah</b></td>";
                                    echo "<td nowrap align='right'><b>$pjml</b></td>";
                                }

                                $ptotaltahund=BuatFormatNum($ptotaltahund, $ppilformat);

                                echo "<td align='right' nowrap><b></b></td>";
                                echo "<td align='right' nowrap><b>$ptotaltahund</b></td>";

                                echo "</tr>";

                                if ($ppilihrpt!="excel") {
                                    echo "<tr>";
                                    echo "<td nowrap colspan=28><b></b></td>";
                                    echo "</tr>";
                                }


                            }


                            //total per divisi
                            echo "<tr>";
                            echo "<td nowrap><b></b></td>";
                            echo "<td nowrap><b>BIAYA $nmdivisi</b></td>";

                            $ptotpersubcoa=0;
                            for ($sx=1;$sx<=12;$sx++) {
                                $pjmlsb=$ptotdivisi[$sx];
                                if (empty($pjmlsb)) $pjmlsb=0;
                                $ptotpersubcoa=(DOUBLE)$ptotpersubcoa+(DOUBLE)$pjmlsb;
                            }

                            $ptotaltahund=0;
                            for ($x=1;$x<=12;$x++) {
                                $pjml=$ptotdivisi[$x];
                                if (empty($pjml)) $pjml=0;

                                $prjumlah=0;
                                if ((DOUBLE)$ptotpersubcoa>0) {
                                    $prjumlah=ROUND((DOUBLE)$pjml/(DOUBLE)$ptotpersubcoa*100,2);
                                }

                                $ptotaltahund=(double)$ptotaltahund+(double)$pjml;
                                $pjml=BuatFormatNum($pjml, $ppilformat);

                                echo "<td nowrap align='right'><b>$prjumlah</b></td>";
                                echo "<td nowrap align='right'><b>$pjml</b></td>";
                            }

                            $ptotaltahund=BuatFormatNum($ptotaltahund, $ppilformat);

                            echo "<td align='right' nowrap><b></b></td>";
                            echo "<td align='right' nowrap><b>$ptotaltahund</b></td>";

                            echo "</tr>";

                            if ($ppilihrpt!="excel") {
                                echo "<tr>";
                                echo "<td nowrap colspan=28><b></b></td>";
                                echo "</tr>";
                            }


                        }

                        //total biaya marketing
                        echo "<tr>";
                        echo "<td nowrap><b></b></td>";
                        echo "<td nowrap><b>TOTAL BIAYA MARKETING</b></td>";

                        $ptotpersubcoa=0;
                        for ($sx=1;$sx<=12;$sx++) {
                            $pjmlsb=$pgrandtotal[$sx];
                            if (empty($pjmlsb)) $pjmlsb=0;
                            $ptotpersubcoa=(DOUBLE)$ptotpersubcoa+(DOUBLE)$pjmlsb;
                        }

                        $ptotaltahund=0;
                        for ($x=1;$x<=12;$x++) {
                            $pjml=$pgrandtotal[$x];
                            if (empty($pjml)) $pjml=0;

                            $prjumlah=0;
                            if ((DOUBLE)$ptotpersubcoa>0) {
                                $prjumlah=ROUND((DOUBLE)$pjml/(DOUBLE)$ptotpersubcoa*100,2);
                            }

                            $ptotaltahund=(double)$ptotaltahund+(double)$pjml;
                            $pjml=BuatFormatNum($pjml, $ppilformat);

                            echo "<td nowrap align='right'><b>$prjumlah</b></td>";
                            echo "<td nowrap align='right'><b>$pjml<b></td>";
                        }

                        $ptotaltahund=BuatFormatNum($ptotaltahund, $ppilformat);

                        echo "<td align='right' nowrap><b></b></td>";
                        echo "<td align='right' nowrap><b>$ptotaltahund</b></td>";

                        echo "</tr>";

                        if ($ppilihrpt!="excel") {
                            echo "<tr>";
                            echo "<td nowrap colspan=28><b></b></td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>


        <?PHP
        }
        ?>

    <?PHP
    }
    ?>
        
        
        
    
</div>
<br/>&nbsp;<br/>&nbsp;<br/>&nbsp; 

<?PHP if ($ppilihrpt!="excel") { ?>


    <!-- Bootstrap -->
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Datatables -->

    <script src="vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
    <script src="vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
    <script src="vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
    <script src="vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
    <script src="vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
    <script src="vendors/jszip/dist/jszip.min.js"></script>
    <script src="vendors/pdfmake/build/pdfmake.min.js"></script>
    <script src="vendors/pdfmake/build/vfs_fonts.js"></script>



    <style>
        #myBtn {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 30px;
            z-index: 99;
            font-size: 18px;
            border: none;
            outline: none;
            background-color: red;
            color: white;
            cursor: pointer;
            padding: 15px;
            border-radius: 4px;
            opacity: 0.5;
        }

        #myBtn:hover {
            background-color: #555;
        }

    </style>

    <style>
        #n_content {
            color:#000;
            font-family: "Arial";
            margin: 5px 20px 20px 20px;
            /*overflow-x:auto;*/
        }

        .h1judul {
          color: blue;
          font-family: verdana;
          font-size: 140%;
          font-weight: bold;
        }
        table.tbljudul {
            font-size : 15px;
        }
        table.tbljudul tr td {
            padding: 1px;
            font-family : "Arial, Verdana, sans-serif";
        }
        .tebal {
             font-weight: bold;
        }
        .miring {
             font-style: italic;
        }
        table.tbljudul tr.text2 {
            font-size : 13px;
        }

        table {
            text-align: left;
            position: relative;
            border-collapse: collapse;
            background-color:#FFFFFF;
        }

        th {
            background: white;
            position: sticky;
            top: 0;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
            z-index:1;
        }

        .th2 {
            background: white;
            position: sticky;
            top: 23;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
            border-top: 1px solid #000;
        }
    </style>

    <style>

        .divnone {
            display: none;
        }
        #mydatatable1, #mydatatable2 {
            color:#000;
            font-family: "Arial";
        }
        #mydatatable1 th, #mydatatable2 th {
            font-size: 12px;
        }
        #mydatatable1 td, #mydatatable2 td { 
            font-size: 11px;
        }
    </style>

<?PHP }else{ ?>
    <style>
        .h1judul {
          font-size: 140%;
          font-weight: bold;
        }
    </style>
<?PHP } ?>
    
</BODY>

    <script>
        // SCROLL
        // When the user scrolls down 20px from the top of the document, show the button
        window.onscroll = function() {scrollFunction()};
        function scrollFunction() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                document.getElementById("myBtn").style.display = "block";
            } else {
                document.getElementById("myBtn").style.display = "none";
            }
        }

        // When the user clicks on the button, scroll to the top of the document
        function topFunction() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }
        // END SCROLL
    </script>
    
</HTML>

<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp05");
    mysqli_close($cnmy);
?>