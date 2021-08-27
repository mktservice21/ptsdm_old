<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    
    session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REALISASI BIAYA MARKETING VS BUDGET.xls");
    }
    
    include("config/koneksimysqli.php");
    
    $printdate= date("d/m/Y");
    
    $picardid=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];
    
    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmprlvsbgethmkt00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmprlvsbgethmkt01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmprlvsbgethmkt02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmprlvsbgethmkt03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmprlvsbgethmkt04_".$puserid."_$now ";
    $tmp05 =" dbtemp.tmprlvsbgethmkt05_".$puserid."_$now ";
    
    
    $tgl1=$_POST['tahun'];
    $pbulan=date("Y-m", strtotime($tgl1));
    $ptahun=date("Y", strtotime($tgl1));
    $pblnthn=date("F Y", strtotime($tgl1));
    $pbln=date("m", strtotime($tgl1));
    
    $tgl01 = $ptahun."-01";
    $tgl02 = $ptahun."-".$pbln;
    
    
    $query = "create TEMPORARY table $tmp00 (SELECT * FROM dbmaster.t_budget_realisasi_lap)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    mysqli_query($cnmy, "update $tmp00 set keterangan=keterangan2");
        
        
    
    //CA H //BM biaya marketing surabaya I & J //sewa kontrakan rumah U //service kendaraan V 
    //BR ETHICAL A //klaimdiscount B //KAS KASBON C & D //BROTC E //RUTIN LUAR KOTA F rutin G lk //insentif incentive K
    //X Kas Kecil Cabang
    
    $pfilterselpil = "('A','B','C','E','F','G','K','X','U','V','I','J')";//,'D'
    
    //echo "$tgl01 - $tgl02";
    
    $query ="SELECT noidauto, tahun, periode, hapus_nodiv_kosong, kodeinput, idkodeinput as idinput, nobrid_r, nobrid_n, idkodeinput, divisi, tglinput, tgltrans, "
        . " karyawanid, nama_karyawan, dokterid, dokter_nama, noslip, nmrealisasi, keterangan, dpp, ppn, pph, tglfp, "
        . " idinput_pd, nodivisi, debit, kredit, saldo, jumlah1, jumlah2, "
        . " divisi_edit as divisi_coa, coa_edit as coa, coa_nama_edit as nama_coa, coa_edit2 as coa2, "
        . " coa_nama_edit2 as nama_coa2, coa_edit3 as coa3, coa_nama_edit3 as nama_coa3, "
        . " tgl_trans_bank, nobukti, idinput_pd1, idinput_pd2, nodivisi1, nodivisi2, pengajuan, "
        . " divisi2, icabangid, nama_cabang, areaid, nama_area, kodeid_pd, subkode_pd, pcm, kasbonsby, coa_pcm, nama_coa_pcm, "
        . " tgltarikan, nkodeid, nkodeid_nama "
        . " FROM dbmaster.t_proses_data_bm WHERE IFNULL(hapus_nodiv_kosong,'') <>'Y' AND DATE_FORMAT(tgltarikan,'%Y-%m') BETWEEN '$tgl01' AND '$tgl02' AND "
        . " kodeinput IN $pfilterselpil ";
    $query .=" AND IFNULL(ishare,'')<>'Y' "; //pilih salah satu divisi <> 'HO' atau IFNULL(ishare,'')<>'Y'
    $query .=" AND IFNULL(divisi,'') NOT IN ('OTC', 'CHC', 'OT') ";

    
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    
    
    
    //BANK
    $query ="INSERT INTO $tmp01"
            . "(noidauto, tahun, periode, hapus_nodiv_kosong, kodeinput, idinput, nobrid_r, nobrid_n, idkodeinput, divisi, tglinput, tgltrans, "
        . " karyawanid, nama_karyawan, dokterid, dokter_nama, noslip, nmrealisasi, keterangan, dpp, ppn, pph, tglfp, "
        . " idinput_pd, nodivisi, debit, kredit, saldo, jumlah1, jumlah2, "
        . " divisi_coa, coa, nama_coa, coa2, "
        . " nama_coa2, coa3, nama_coa3, "
        . " tgl_trans_bank, nobukti, idinput_pd1, idinput_pd2, nodivisi1, nodivisi2, pengajuan, "
        . " divisi2, icabangid, nama_cabang, areaid, nama_area, kodeid_pd, subkode_pd, pcm, kasbonsby, coa_pcm, nama_coa_pcm, "
        . " tgltarikan, nkodeid, nkodeid_nama)"
            . "SELECT noidauto, tahun, periode, hapus_nodiv_kosong, kodeinput, idkodeinput as idinput, nobrid_r, nobrid_n, idkodeinput, divisi, tglinput, tgltrans, "
        . " karyawanid, nama_karyawan, dokterid, dokter_nama, noslip, nmrealisasi, keterangan, dpp, ppn, pph, tglfp, "
        . " idinput_pd, nodivisi, debit, kredit, saldo, jumlah1, jumlah2, "
        . " divisi_edit as divisi_coa, coa_edit as coa, coa_nama_edit as nama_coa, coa_edit2 as coa2, "
        . " coa_nama_edit2 as nama_coa2, coa_edit3 as coa3, coa_nama_edit3 as nama_coa3, "
        . " tgl_trans_bank, nobukti, idinput_pd1, idinput_pd2, nodivisi1, nodivisi2, pengajuan, "
        . " divisi2, icabangid, nama_cabang, areaid, nama_area, kodeid_pd, subkode_pd, pcm, kasbonsby, coa_pcm, nama_coa_pcm, "
        . " tgltarikan, nkodeid, nkodeid_nama "
        . " FROM dbmaster.t_proses_data_bm WHERE IFNULL(hapus_nodiv_kosong,'') <>'Y' AND DATE_FORMAT(tgltarikan,'%Y-%m') BETWEEN '$tgl01' AND '$tgl02' AND "
        . " kodeinput IN ('M') "
        . " AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN (select CONCAT(IFNULL(kodeid,''),IFNULL(subkode,'')) from dbmaster.t_kode_spd where IFNULL(igroup,'')='3') "
        . " and IFNULL(nkodeid_nama,'')='K'";
    $query .=" AND IFNULL(ishare,'')<>'Y' "; //pilih salah satu divisi <> 'HO' atau IFNULL(ishare,'')<>'Y'
    $query .=" AND IFNULL(divisi,'') NOT IN ('OTC', 'CHC', 'OT') ";
    
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    
    
    $query = "CREATE INDEX `norm1` ON $tmp01 (kodeinput,idinput,divisi,tgltrans,coa)";
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
        dbmaster.t_budget AS a
        WHERE tahun = '$ptahun' AND g_divisi='ETH'";

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
    
    
    
    $query = "SELECT kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01";
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
    
    
    

    $query = "UPDATE $tmp00 as a JOIN (SELECT kodeid, SUM(kredit) as kredit FROM $tmp03 GROUP BY 1) as b on IFNULL(a.kodeid,'')=IFNULL(b.kodeid,'') SET a.jumlah1=b.kredit"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
    
    $query = "UPDATE $tmp00 as a JOIN (SELECT kodeid, SUM(jumlah) as jumlah FROM $tmp02 GROUP BY 1) as b on IFNULL(a.kodeid,'')=IFNULL(b.kodeid,'') SET a.jumlah2=b.jumlah"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");

    $query = "SELECT * FROM $tmp00";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp00 as a JOIN (SELECT SUM(jumlah1) as jumlah1, SUM(jumlah2) as jumlah2 FROM $tmp04 WHERE IFNULL(grp,0)=1) as b SET a.jumlah1=b.jumlah1, a.jumlah2=b.jumlah2 WHERE nourut=5";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp00 as a JOIN (SELECT SUM(jumlah1) as jumlah1, SUM(jumlah2) as jumlah2 FROM $tmp04 WHERE IFNULL(grp,0)=2) as b SET a.jumlah1=b.jumlah1, a.jumlah2=b.jumlah2 WHERE nourut=10";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp00 as a JOIN (SELECT SUM(jumlah1) as jumlah1, SUM(jumlah2) as jumlah2 FROM $tmp04 WHERE IFNULL(grp,0)=3) as b SET a.jumlah1=b.jumlah1, a.jumlah2=b.jumlah2 WHERE nourut=14";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp00 as a JOIN (SELECT SUM(jumlah1) as jumlah1, SUM(jumlah2) as jumlah2 FROM $tmp04 WHERE IFNULL(grp,0)=4) as b SET a.jumlah1=b.jumlah1, a.jumlah2=b.jumlah2 WHERE nourut=20";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp00 as a JOIN (SELECT SUM(jumlah1) as jumlah1, SUM(jumlah2) as jumlah2 FROM $tmp04 WHERE IFNULL(kodeid,'') <> '' and IFNULL(kodeid,'') not in ('01','02','03')) as b SET a.jumlah1=b.jumlah1, a.jumlah2=b.jumlah2 WHERE nourut=21";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");

    $query = "SELECT * FROM $tmp00";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp00 as a JOIN (SELECT SUM(jumlah1) as jumlah1, SUM(jumlah2) as jumlah2 FROM $tmp04 WHERE nourut in (5,21)) as b SET a.jumlah1=b.jumlah1, a.jumlah2=b.jumlah2 WHERE nourut=22";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
    
    //SALES
    $query = "select divprodid, sum(value_sales) as value_sales, sum(value_target) as value_target from fe_ms.sales WHERE YEAR(bulan)='$ptahun' AND DATE_FORMAT(bulan,'%Y-%m') <= '$pbulan' GROUP BY 1";
    $query = "create TEMPORARY table $tmp04 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    mysqli_query($cnmy, "UPDATE $tmp00 a SET a.jumlah1=(select sum(b.value_sales) FROM $tmp04 b) WHERE nourut=23");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    mysqli_query($cnmy, "UPDATE $tmp00 a SET a.jumlah2=(select sum(b.value_target) FROM $tmp04 b) WHERE nourut=23");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
    $query = "UPDATE $tmp00 SET jumlah3=IFNULL(jumlah2,0)-IFNULL(jumlah1,0)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    $jsales1=0;
    $jsales2=0;
    $jsales3=0;
    $query = "select * FROM $tmp00 WHERE nourut=23";
    $tampil=mysqli_query($cnmy, $query);
    while ($ro= mysqli_fetch_array($tampil)) {
        $jsales1=$ro['jumlah1'];
        $jsales2=$ro['jumlah2'];
        $jsales3=$ro['jumlah3'];
        if (empty($jsales1)) $jsales1=0;
        if (empty($jsales2)) $jsales2=0;
        if (empty($jsales3)) $jsales3=0;
    }
    if ((DOUBLE)$jsales1>0){
        mysqli_query($cnmy, "UPDATE $tmp00 a SET a.ratio1=ifnull(a.jumlah1,0)/$jsales1*100");
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    if ((DOUBLE)$jsales2>0){
        mysqli_query($cnmy, "UPDATE $tmp00 a SET a.ratio2=ifnull(a.jumlah2,0)/$jsales2*100");
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    if ((DOUBLE)$jsales2>0){
        mysqli_query($cnmy, "UPDATE $tmp00 a SET a.ratio3=ifnull(a.jumlah3,0)/$jsales2*100");
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
        
        
    
    $query = "SELECT * FROM $tmp00";
    //$query = "create table $tmp05 ($query)"; 
    //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
?>


<HTML>
<HEAD>
    <title>REALISASI BIAYA MARKETING VS BUDGET ETHICAL</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Mei 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <!--<link href="css/laporanbaru.css" rel="stylesheet">-->
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        
		
		<!-- jQuery -->
        <script src="vendors/jquery/dist/jquery.min.js"></script>
        
        
    
        <style>
            .tjudul {
                font-family: "times new roman", Arial, Georgia, serif;
                margin-left:10px;
                margin-right:10px;
            }
            .tjudul td {
                padding: 4px;
                font-size: 15px;
            }
            #datatable2 {
                font-family: "times new roman", Arial, Georgia, serif;
                margin-left:10px;
                margin-right:10px;
                border-collapse: collapse;
            }
            #datatable2 th, #datatable2 td {
                padding: 10px;
            }
            #datatable2 thead{
                background-color:#cccccc; 
                font-size: 18px;
            }
            #datatable2 tbody{
                font-size: 16px;
            }
        </style>
        
    <?PHP } ?>
    
</HEAD>
<BODY class="nav-md">
    
<?PHP if ($ppilihrpt!="excel") { ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?PHP } ?>
    
    
<div id='n_content'>
    
    <?PHP
        $tglbulanbesar=strtoupper($pblnthn);
        echo "<table class='tjudul' width='100%'>";
        echo "<tr> <td width='400px' colspan='2'>REALISASI BIAYA MARKETING VS BUDGET s/d. $tglbulanbesar</td> </tr>";
        echo "<tr> <td width='200px' colspan='2'>PT. SURYA DERMATO MEDICA LABORATORIES </td></tr>";
        echo "<tr> <td width='200px' colspan='2'>DIVISI ETHICAL</td></tr>";
        echo "</table>";
        echo "<br/>&nbsp;";
        
    ?>
   
    <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
        <thead>
            <tr>
            <th align="center" rowspan="2">NO</th>
            <th align="center" rowspan="2">KETERANGAN</th>
            <th align="center" colspan="2"><?PHP echo "$ptahun"; ?></th>
            <th align="center" colspan="2"><?PHP echo "$ptahun"; ?></th>
            <th align="center" colspan="2"><?PHP echo "$ptahun"; ?></th>
            </tr>
            
            <tr>
            <th align="center">REALISASI BUDGET</th>
            <th align="center">COST <br/>RATIO</th>
            <th align="center">USULAN BUDGET</th>
            <th align="center">COST <br/>RATIO</th>
            <th align="center">SISA BUDGET</th>
            <th align="center">COST <br/>RATIO</th>
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
                
                $pjumlah1=$row['jumlah1'];
                $pjumlah2=$row['jumlah2'];
                $pjumlah3=$row['jumlah3'];

                $pratio1=ROUND($row['ratio1'],2);
                $pratio2=ROUND($row['ratio2'],2);
                $pratio3=ROUND($row['ratio3'],2);
                //$pratio3=$row['ratio3'];

                


                $pjumlah1=number_format($pjumlah1,0,",",",");
                $pjumlah2=number_format($pjumlah2,0,",",",");
                $pjumlah3=number_format($pjumlah3,0,",",",");
                
                if ($pjumlah1==0) $pjumlah1="";
                if ($pjumlah2==0) $pjumlah2="";
                if ($pjumlah3==0) $pjumlah3="";
                
                if ($pratio1==0) $pratio1="";
                if ($pratio2==0) $pratio2="";
                if ($pratio3==0) $pratio3="";
                
                echo "<tr>";
                echo "<td nowrap>$pno</td>";
                echo "<td nowrap>$pjudul</td>";
                if ((int)$pnourut==5 OR (int)$pnourut==10 OR (int)$pnourut==14 OR (int)$pnourut==20 OR (int)$pnourut==21 OR (int)$pnourut==22 OR (int)$pnourut==23) {
                    echo "<td nowrap align='right'><b>$pjumlah1</b></td>";
                    echo "<td nowrap align='right'><b>$pratio1</b></td>";
                    echo "<td nowrap align='right'><b>$pjumlah2</b></td>";
                    echo "<td nowrap align='right'><b>$pratio2</b></td>";
                    
                    echo "<td nowrap align='right'><b>$pjumlah3</b></td>";
                    echo "<td nowrap align='right'><b>$pratio3</b></td>";
                }else{
                    echo "<td nowrap align='right'>$pjumlah1</td>";
                    echo "<td nowrap align='right'>$pratio1</td>";
                    echo "<td nowrap align='right'>$pjumlah2</td>";
                    echo "<td nowrap align='right'>$pratio2</td>";
                    
                    echo "<td nowrap align='right'>$pjumlah3</td>";
                    echo "<td nowrap align='right'>$pratio3</td>";
                }
                echo "</tr>";
                
                if ((int)$pnourut==5 OR (int)$pnourut==10 OR (int)$pnourut==14 OR (int)$pnourut==20 OR (int)$pnourut==21 OR (int)$pnourut==23) {
                    echo "<tr>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "</tr>";
                }
                
            }
            
            ?>
        </tbody>
    </table>
    
    
    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;    
</div>
    
    
    <?PHP if ($ppilihrpt!="excel") { ?>

        
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