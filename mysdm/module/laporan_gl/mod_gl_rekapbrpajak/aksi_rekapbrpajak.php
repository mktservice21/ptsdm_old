<?PHP
    session_start();
    $pidgroup_user=$_SESSION['GROUP'];
    $pdivprodid=$_POST['divprodid'];
    $npilihdivisi=$pdivprodid;
    if (empty($pdivprodid)) $npilihdivisi="ALL";
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REKAP PAJAK BR $npilihdivisi.xls");
    }
    
    $hariini=date("Y-m-d");
    $ptglview = date("d F Y", strtotime($hariini));
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
    
    $now=date("mdYhis");
    $puserid=$_SESSION['USERID'];
    $tmp00 =" dbtemp.RPTREKOTOTSOTC00_".$puserid."_$now ";
    $tmp01 =" dbtemp.RPTREKOTOTSOTC01_".$puserid."_$now ";
    $tmp02 =" dbtemp.RPTREKOTOTSOTC02_".$puserid."_$now ";
    $tmp03 =" dbtemp.RPTREKOTOTSOTC03_".$puserid."_$now ";
    $tmp04 =" dbtemp.RPTREKOTOTSOTC04_".$puserid."_$now ";
    
    
    $ptgl1=$_POST['e_periode01'];
    $ptgl2=$_POST['e_periode02'];
    $ppajak_=$_POST['cb_pajak'];
    
    $f_pajak="";
    if ($ppajak_=="Y") $f_pajak=" AND IFNULL(pajak,'')='Y' ";
    
    $pperiode1=date("Ym", strtotime($ptgl1));
    $pperiode2=date("Ym", strtotime($ptgl2));
    
    $f_divisi="";
    if (!empty($pdivprodid)) $f_divisi=" AND divprodid='$pdivprodid' ";
    
    if ($pdivprodid=="OTC") {
        $query = "select CAST('OTC' as CHAR(5)) as divprodid, brOtcId brId, icabangid_o as icabangid, 
            tglbr tgl, noslip, tgltrans, tglrpsby, CAST('' as CHAR(10)) as karyawanId, 
            CAST('' as CHAR(10)) as dokterId, CAST('' as CHAR(10)) as dokter,
            keterangan1 aktivitas1, keterangan2 as aktivitas2, real1 realisasi1, jumlah, realisasi as jumlah1, 
            CAST(0 as DECIMAL(20,2)) as realisasi2, COA4, kodeid as kode,
            lampiran, ca, via, pajak, dpp, ppn, ppn_rp, pph_jns, pph, pph_rp, tgl_fp, noseri, pembulatan,
            jasa_rp, materai_rp, jenis_dpp, batal as stsbatal, CAST('BROTC' as CHAR(5)) as pengajuan, nama_pengusaha, subpost
            from hrd.br_otc WHERE 1=1 $f_pajak AND DATE_FORMAT(tglbr,'%Y%m') between '$pperiode1' AND '$pperiode2'";
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "select b.pilih, b.kodeid, d.nama kodenama, b.subkode, d.subnama, b.divisi, 
            b.tgl, b.tglspd, b.nomor, b.nodivisi, b.jenis_rpt, b.jumlah jumlahpd, a.urutan, a.bridinput, a.amount, 
            a.nobbm, a.nobbk, b.coa4 coa, c.NAMA4 coa_nama from dbmaster.t_suratdana_br1 a 
            JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput 
            LEFT JOIN dbmaster.coa_level4 c on b.coa4=c.COA4 
            LEFT JOIN dbmaster.t_kode_spd d on b.kodeid=d.kodeid AND b.subkode=d.subkode WHERE IFNULL(stsnonaktif,'')<>'Y' AND 
            a.bridinput IN (select distinct IFNULL(brId,'') FROM $tmp02) AND a.kodeinput IN ('D')";
        $query = "create TEMPORARY table $tmp01 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "DELETE FROM $tmp02 WHERE brId NOT IN (select distinct IFNULL(bridinput,'') FROM $tmp01)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "ALTER table $tmp02 ADD nama_dokter CHAR(100), ADD nama_karyawan CHAR(100), ADD nama_kode CHAR(150)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; }
        
        
        $query = "select a.divprodid, a.brId, a.tgl, a.noslip, a.tgltrans, a.tglrpsby, a.karyawanId, a.nama_karyawan, a.dokterId, a.dokter, a.nama_dokter,
            a.aktivitas1, a.aktivitas2, a.realisasi1, a.jumlah, jumlah1, a.realisasi2, a.COA4, d.NAMA4, a.kode, a.nama_kode,
            a.lampiran, a.ca, a.via, a.pajak, a.dpp, a.ppn, a.ppn_rp, a.pph_jns, a.pph, a.pph_rp, a.tgl_fp, a.noseri, a.pembulatan,
            a.jasa_rp, a.materai_rp, a.jenis_dpp,
            f.nama nama_cabang, g.urutan, g.nodivisi, g.amount, g.nobbm, g.nobbk,
            CAST('' as CHAR(10)) as jns_pajak, a.stsbatal, nama_pengusaha, a.subpost, a.icabangid   
            from $tmp02 a 
            LEFT JOIN dbmaster.coa_level4 d on a.COA4=d.COA4 
            LEFT JOIN mkt.icabang_o f on a.icabangid=f.iCabangId_o 
            LEFT JOIN (SELECT * FROM $tmp01 WHERE jenis_rpt<>'B') as g on a.brId=g.bridinput";
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; }
    
    
        mysqli_query($cnit, "UPDATE $tmp03 a SET a.nama_kode =(SELECT b.nama FROM hrd.brkd_otc b WHERE a.kode=b.kodeid AND a.subpost=b.subpost) WHERE IFNULL(a.kode,'')<> '' AND IFNULL(a.subpost,'')<> ''");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; }
        mysqli_query($cnit, "UPDATE $tmp03 a SET a.nama_kode =(SELECT b.nmsubpost FROM hrd.brkd_otc b WHERE a.subpost=b.subpost) WHERE IFNULL(a.kode,'')<> '' AND IFNULL(a.subpost,'')= '' AND IFNULL(a.nama_kode,'')= ''");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; }
        mysqli_query($cnit, "UPDATE $tmp03 a SET a.nama_cabang =(SELECT b.nama FROM dbmaster.cabang_otc b WHERE a.icabangid=b.cabangid_ho) WHERE IFNULL(a.nama_cabang,'')=''");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; }
        
        mysqli_query($cnit, "UPDATE $tmp03 a SET a.nama_karyawan ='OTC'");
        
    }elseif ($pdivprodid=="KD") {
        
        $query = "select pengajuan as divprodid, klaimId brId, CAST('' as CHAR(10)) as icabangid, 
            tgl, noslip, tgltrans, CAST(NULL as date) as tglrpsby, karyawanId, distid as dokterId, CAST('' as CHAR(50)) as dokter,
            aktivitas1, CAST('' as CHAR(10)) as aktivitas2, realisasi1, jumlah, CAST(0 as DECIMAL(20,2)) as jumlah1, 
            CAST(0 as DECIMAL(20,2)) as realisasi2, COA4, CAST('' as CHAR(10)) as kode,
            CAST('' as CHAR(1)) as lampiran, CAST('' as CHAR(1)) as ca, CAST('' as CHAR(1)) as via, 
            pajak, dpp, ppn, ppn_rp, pph_jns, pph, pph_rp, tgl_fp, noseri, pembulatan,
            CAST(0 as DECIMAL(20,2)) as jasa_rp, CAST(0 as DECIMAL(20,2)) as materai_rp, 
            CAST(NULL as CHAR(5)) as jenis_dpp, CAST('' as CHAR(1)) as stsbatal, CAST('KD' as CHAR(5)) as pengajuan, nama_pengusaha
            from hrd.klaim WHERE 1=1 $f_pajak AND DATE_FORMAT(tgl,'%Y%m') between '$pperiode1' AND '$pperiode2'";
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "select b.pilih, b.kodeid, d.nama kodenama, b.subkode, d.subnama, b.divisi, 
            b.tgl, b.tglspd, b.nomor, b.nodivisi, b.jenis_rpt, b.jumlah jumlahpd, a.urutan, a.bridinput, a.amount, 
            a.nobbm, a.nobbk, b.coa4 coa, c.NAMA4 coa_nama from dbmaster.t_suratdana_br1 a 
            JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput 
            LEFT JOIN dbmaster.coa_level4 c on b.coa4=c.COA4 
            LEFT JOIN dbmaster.t_kode_spd d on b.kodeid=d.kodeid AND b.subkode=d.subkode WHERE IFNULL(stsnonaktif,'')<>'Y' AND 
            a.bridinput IN (select distinct IFNULL(brId,'') FROM $tmp02) AND a.kodeinput IN ('E')";
        $query = "create TEMPORARY table $tmp01 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "DELETE FROM $tmp02 WHERE brId NOT IN (select distinct IFNULL(bridinput,'') FROM $tmp01)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "ALTER table $tmp02 ADD nama_kode CHAR(150), ADD nama_cabang CHAR(150)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; }
        
        $query = "select a.divprodid, a.brId, a.tgl, a.noslip, a.tgltrans, a.tglrpsby, a.karyawanId, c.nama nama_karyawan, a.dokterId, a.dokter, b.nama nama_dokter,
            a.aktivitas1, a.aktivitas2, a.realisasi1, a.jumlah, jumlah1, a.realisasi2, a.COA4, d.NAMA4, a.kode, a.nama_kode,
            a.lampiran, a.ca, a.via, a.pajak, a.dpp, a.ppn, a.ppn_rp, a.pph_jns, a.pph, a.pph_rp, a.tgl_fp, a.noseri, a.pembulatan,
            a.jasa_rp, a.materai_rp, a.jenis_dpp,
            a.nama_cabang, g.urutan, g.nodivisi, g.amount, g.nobbm, g.nobbk,
            CAST('' as CHAR(10)) as jns_pajak, a.stsbatal, nama_pengusaha 
            from $tmp02 a 
            LEFT JOIN MKT.distrib0 b on a.dokterId=b.distid
            LEFT JOIN hrd.karyawan c on a.karyawanId=c.karyawanId 
            LEFT JOIN dbmaster.coa_level4 d on a.COA4=d.COA4 
            LEFT JOIN (SELECT * FROM $tmp01 WHERE jenis_rpt<>'B') as g on a.brId=g.bridinput";
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; }
        
        
        
        
    }else{
        
        $query = "select divprodid, brId, icabangid, tgl, noslip, tgltrans, tglrpsby, karyawanId, dokterId, dokter,
            aktivitas1, aktivitas2, realisasi1, jumlah, jumlah1, realisasi2, COA4, kode,
            lampiran, ca, via, pajak, dpp, ppn, ppn_rp, pph_jns, pph, pph_rp, tgl_fp, noseri, pembulatan,
            jasa_rp, materai_rp, jenis_dpp, CAST('' as CHAR(1)) as stsbatal, CAST('BRETH' as CHAR(5)) as pengajuan, nama_pengusaha 
            from hrd.br0 WHERE 1=1 $f_pajak $f_divisi AND DATE_FORMAT(tgl,'%Y%m') between '$pperiode1' AND '$pperiode2'";
        $query = "create TEMPORARY table $tmp02 ($query)";// $tmp01
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "select b.pilih, b.kodeid, d.nama kodenama, b.subkode, d.subnama, b.divisi, 
            b.tgl, b.tglspd, b.nomor, b.nodivisi, b.jenis_rpt, b.jumlah jumlahpd, a.urutan, a.bridinput, a.amount, 
            a.nobbm, a.nobbk, b.coa4 coa, c.NAMA4 coa_nama from dbmaster.t_suratdana_br1 a 
            JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput 
            LEFT JOIN dbmaster.coa_level4 c on b.coa4=c.COA4 
            LEFT JOIN dbmaster.t_kode_spd d on b.kodeid=d.kodeid AND b.subkode=d.subkode WHERE IFNULL(stsnonaktif,'')<>'Y' AND 
            a.bridinput IN (select distinct IFNULL(brId,'') FROM $tmp02) AND a.kodeinput IN ('A', 'B', 'C')";
        $query = "create TEMPORARY table $tmp01 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "DELETE FROM $tmp02 WHERE brId NOT IN (select distinct IFNULL(bridinput,'') FROM $tmp01)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            //goto hapusdata;

        $query = "select a.divprodid, a.brId, a.tgl, a.noslip, a.tgltrans, a.tglrpsby, a.karyawanId, c.nama nama_karyawan, a.dokterId, a.dokter, b.nama nama_dokter,
            a.aktivitas1, a.aktivitas2, a.realisasi1, a.jumlah, jumlah1, a.realisasi2, a.COA4, d.NAMA4, a.kode, e.nama nama_kode,
            a.lampiran, a.ca, a.via, a.pajak, a.dpp, a.ppn, a.ppn_rp, a.pph_jns, a.pph, a.pph_rp, a.tgl_fp, a.noseri, a.pembulatan,
            a.jasa_rp, a.materai_rp, a.jenis_dpp,
            f.nama nama_cabang, g.urutan, g.nodivisi, g.amount, g.nobbm, g.nobbk,
            CAST('' as CHAR(10)) as jns_pajak, a.stsbatal, nama_pengusaha 
            from $tmp02 a 
            LEFT JOIN hrd.dokter b on a.dokterId=b.dokterId
            LEFT JOIN hrd.karyawan c on a.karyawanId=c.karyawanId 
            LEFT JOIN dbmaster.coa_level4 d on a.COA4=d.COA4 
            LEFT JOIN hrd.br_kode e on a.kode=e.kodeid AND a.divprodid=e.divprodid 
            LEFT JOIN mkt.icabang f on a.icabangid=f.iCabangId 
            LEFT JOIN (SELECT * FROM $tmp01 WHERE jenis_rpt<>'B') as g on a.brId=g.bridinput";
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; }
        
        
    }
    
/*
    $query = "INSERT INTO $tmp01 
        select pengajuan as divprodid, klaimId brId, CAST('' as CHAR(10)) as icabangid, 
        tgl, noslip, tgltrans, CAST(NULL as date) as tglrpsby, karyawanId, distid as dokterId, CAST('' as CHAR(50)) as dokter,
        aktivitas1, CAST('' as CHAR(10)) as aktivitas2, realisasi1, jumlah, CAST(0 as DECIMAL(20,2)) as jumlah1, 
        CAST(0 as DECIMAL(20,2)) as realisasi2, COA4, CAST('' as CHAR(10)) as kode,
        CAST('' as CHAR(1)) as lampiran, CAST('' as CHAR(1)) as ca, CAST('' as CHAR(1)) as via, 
        pajak, dpp, ppn, ppn_rp, pph_jns, pph, pph_rp, tgl_fp, noseri, pembulatan,
        CAST(0 as DECIMAL(20,2)) as jasa_rp, CAST(0 as DECIMAL(20,2)) as materai_rp, 
        CAST(NULL as CHAR(5)) as jenis_dpp, CAST('' as CHAR(1)) as stsbatal, CAST('KD' as CHAR(5)) as pengajuan, nama_pengusaha
        from hrd.klaim WHERE 1=1 $f_pajak AND DATE_FORMAT(tgl,'%Y%m') between '$pperiode1' AND '$pperiode2'";
    //$query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "INSERT INTO $tmp01 
        select CAST('OTC' as CHAR(5)) as divprodid, brOtcId brId, icabangid_o as icabangid, 
        tglbr tgl, noslip, tgltrans, tglrpsby, CAST('' as CHAR(10)) as karyawanId, 
        CAST('' as CHAR(10)) as dokterId, CAST('' as CHAR(10)) as dokter,
        keterangan1 aktivitas1, keterangan2 as aktivitas2, real1 realisasi1, jumlah, realisasi as jumlah1, 
        CAST(0 as DECIMAL(20,2)) as realisasi2, COA4, kodeid as kode,
        lampiran, ca, via, pajak, dpp, ppn, ppn_rp, pph_jns, pph, pph_rp, tgl_fp, noseri, pembulatan,
        jasa_rp, materai_rp, jenis_dpp, batal as stsbatal, CAST('BROTC' as CHAR(5)) as pengajuan, nama_pengusaha
        from hrd.br_otc WHERE 1=1 $f_pajak AND DATE_FORMAT(tglbr,'%Y%m') between '$pperiode1' AND '$pperiode2'";
    //$query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    goto hapusdata;
*/
    
    

    
        
        
    
    $query = "UPDATE $tmp03 a JOIN (SELECT * FROM $tmp01 WHERE jenis_rpt='B') g on a.brId=g.bridinput"
            . " SET a.urutan=g.urutan, a.nodivisi=g.nodivisi, a.amount=g.amount, a.nobbm=g.nobbm, a.nobbk=g.nobbk WHERE "
            . " IFNULL(a.nodivisi,'')=''";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; }
            
    
    //no bbm
    $query="UPDATE $tmp03 a SET a.nobbm=(select b.nobukti FROM dbmaster.t_suratdana_bank b WHERE IFNULL(b.stsnonaktif,'') <>'Y' AND "
            . " IFNULL(b.stsinput,'')='M' AND a.nodivisi=b.nodivisi LIMIT 1)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; } 

    //no bbk
    $query="UPDATE $tmp03 a SET a.nobbk=(select b.nobukti FROM dbmaster.t_suratdana_bank b WHERE IFNULL(b.stsnonaktif,'') <>'Y' AND "
            . " IFNULL(b.stsinput,'')='K' AND a.nodivisi=b.nodivisi LIMIT 1)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    $query = "select * from $tmp03 WHERE karyawanId='' AND brId='' AND divprodid='' LIMIT 1";
    $query = "create TEMPORARY table $tmp04 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "DELETE FROM $tmp04";
    mysqli_query($cnit, $query);
        
        
    
    //PPN
    $query = "INSERT INTO $tmp04 (jns_pajak, divprodid, brId, noslip, karyawanId, nama_karyawan, "
            . " dokterId, dokter, nama_dokter, aktivitas1, aktivitas2, tgl, tgltrans, realisasi1, jumlah, COA4, nodivisi, nobbm, nobbk, amount, nama_pengusaha, nama_cabang)"
            . " SELECT 'PPN' jns_pajak, divprodid, brId, noslip, karyawanId, nama_karyawan, "
            . " dokterId, dokter, nama_dokter, aktivitas1, aktivitas2, tgl, tgltrans, realisasi1, ppn_rp, '106-04' COA4, nodivisi, nobbm, nobbk, ppn_rp, nama_pengusaha, nama_cabang "
            . " from $tmp03  WHERE pajak='Y' AND IFNULL(ppn_rp,0)<>0";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }           

    //PPH 23
    $query = "INSERT INTO $tmp04 (jns_pajak, divprodid, brId, noslip, karyawanId, nama_karyawan, "
            . " dokterId, dokter, nama_dokter, aktivitas1, aktivitas2, tgl, tgltrans, realisasi1, jumlah, COA4, nodivisi, nobbm, nobbk, amount, nama_pengusaha, nama_cabang)"
            . " SELECT 'PPH23' jns_pajak, divprodid, brId, noslip, karyawanId, nama_karyawan, "
            . " dokterId, dokter, nama_dokter, aktivitas1, aktivitas2, tgl, tgltrans, realisasi1, pph_rp, '205-08' COA4, nodivisi, nobbm, nobbk, pph_rp, nama_pengusaha, nama_cabang "
            . " from $tmp03  WHERE pajak='Y' AND IFNULL(pph_rp,0)<>0 AND pph_jns='pph23'";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        

    //PPH 21
    $query = "INSERT INTO $tmp04 (jns_pajak, divprodid, brId, noslip, karyawanId, nama_karyawan, "
            . " dokterId, dokter, nama_dokter, aktivitas1, aktivitas2, tgl, tgltrans, realisasi1, jumlah, COA4, nodivisi, nobbm, nobbk, amount, nama_pengusaha, nama_cabang)"
            . " SELECT 'PPH21' jns_pajak, divprodid, brId, noslip, karyawanId, nama_karyawan, "
            . " dokterId, dokter, nama_dokter, aktivitas1, aktivitas2, tgl, tgltrans, realisasi1, pph_rp, '205-02' COA4, nodivisi, nobbm, nobbk, pph_rp, nama_pengusaha, nama_cabang "
            . " from $tmp03  WHERE pajak='Y' AND IFNULL(pph_rp,0)<>0 AND pph_jns='pph21'";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        

    //PEMBULATAN
    $query = "INSERT INTO $tmp04 (jns_pajak, divprodid, brId, noslip, karyawanId, nama_karyawan, "
            . " dokterId, dokter, nama_dokter, aktivitas1, aktivitas2, tgl, tgltrans, realisasi1, jumlah, COA4, nodivisi, nobbm, nobbk, amount, nama_pengusaha, nama_cabang)"
            . " SELECT 'BULAT' jns_pajak, divprodid, brId, noslip, karyawanId, nama_karyawan, "
            . " dokterId, dokter, nama_dokter, aktivitas1, aktivitas2, tgl, tgltrans, realisasi1, pembulatan, '905-02' COA4, nodivisi, nobbm, nobbk, pembulatan, nama_pengusaha, nama_cabang "
            . " from $tmp03  WHERE pajak='Y' AND IFNULL(pembulatan,0)<>0";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    

    //MATERAI
    $query = "INSERT INTO $tmp04 (jns_pajak, divprodid, brId, noslip, karyawanId, nama_karyawan, "
            . " dokterId, dokter, nama_dokter, aktivitas1, aktivitas2, tgl, tgltrans, realisasi1, jumlah, COA4, nodivisi, nobbm, nobbk, amount, nama_pengusaha, nama_cabang)"
            . " SELECT 'MATERAI' jns_pajak, divprodid, brId, noslip, karyawanId, nama_karyawan, "
            . " dokterId, dokter, nama_dokter, aktivitas1, aktivitas2, tgl, tgltrans, realisasi1, materai_rp, '' COA4, nodivisi, nobbm, nobbk, materai_rp, nama_pengusaha, nama_cabang "
            . " from $tmp03  WHERE pajak='Y' AND IFNULL(materai_rp,0)<>0";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    


    $query="UPDATE $tmp04 SET COA4='750-07' WHERE jns_pajak='MATERAI' AND divprodid='HO'";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    

    $query="UPDATE $tmp04 SET COA4='751-07' WHERE jns_pajak='MATERAI' AND divprodid='EAGLE'";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    

    $query="UPDATE $tmp04 SET COA4='752-07' WHERE jns_pajak='MATERAI' AND divprodid='PIGEO'";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    

    $query="UPDATE $tmp04 SET COA4='753-07' WHERE jns_pajak='MATERAI' AND divprodid='PEACO'";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    

    $query="UPDATE $tmp04 SET COA4='754-07' WHERE jns_pajak='MATERAI' AND divprodid='OTC'";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    

    $query="UPDATE $tmp04 SET COA4='755-07' WHERE jns_pajak='MATERAI' AND divprodid='CAN'";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }  
        
        

    //cocokan COA dari tabel

    $query="UPDATE $tmp04 a SET a.COA4=IFNULL((select b.COA4 FROM dbmaster.coa_pajak b WHERE a.jns_pajak=b.jns_pajak),COA4) WHERE "
            . " a.jns_pajak IN ('PPN', 'PPH23', 'PPH21', 'BULAT')";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    

    $query="UPDATE $tmp04 a SET a.COA4=IFNULL((select b.COA4 FROM dbmaster.coa_pajak b WHERE a.jns_pajak=b.jns_pajak AND IFNULL(a.divprodid,'')=IFNULL(b.divisi,'')),a.COA4) WHERE "
            . " a.jns_pajak IN ('MATERAI')";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    

    //update nama coa
    $query="UPDATE $tmp04 a SET a.NAMA4=(select b.NAMA4 FROM dbmaster.coa_level4 b WHERE a.COA4=b.COA4)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    

    //pph dijadikan negatif
    $query="UPDATE $tmp04 SET jumlah=0-IFNULL(jumlah,0) WHERE jns_pajak IN ('PPH23', 'PPH21') AND IFNULL(jumlah,0) > 0";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    


    $query="UPDATE $tmp04 SET pajak='T'";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
    $query = "INSERT INTO $tmp03 (jns_pajak, divprodid, brId, noslip, karyawanId, nama_karyawan, "
                . " dokterId, dokter, nama_dokter, aktivitas1, aktivitas2, tgl, tgltrans, realisasi1, jumlah, COA4, NAMA4, nodivisi, nobbm, nobbk, amount, pajak, nama_pengusaha, nama_cabang)"
            . " SELECT jns_pajak, divprodid, brId, noslip, karyawanId, nama_karyawan, "
                . " dokterId, dokter, nama_dokter, aktivitas1, aktivitas2, tgl, tgltrans, realisasi1, jumlah, COA4, NAMA4, nodivisi, nobbm, nobbk, jumlah as amount, pajak, nama_pengusaha, nama_cabang "
            . " from $tmp04";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; } 
    
    
    $query = "UPDATE $tmp03 a JOIN $tmp01 b on a.brId=b.bridinput SET a.urutan=b.urutan";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; } 
    
    
    
    $query = "select * FROM $tmp03";
    $jmlrec=mysqli_num_rows(mysqli_query($cnit, $query));
    $plimit=30;
    $pjmlfor=ceil((double)$jmlrec / (double)$plimit);
    
    
?>
<html>
<head>
    <?PHP 
        echo "<title>REKAP PAJAK BR $npilihdivisi</title>";
     
        if ($_GET['ket']!="excel") {
            echo "<meta http-equiv='Expires' content='Mon, 01 Jan 2019 1:00:00 GMT'>";
            echo "<meta http-equiv='Pragma' content='no-cache'>";
            echo "<link rel='shortcut icon' href='images/icon.ico' />";
            echo "<link href='css/laporanbaru.css' rel='stylesheet'>";
            
            header("Cache-Control: no-cache, must-revalidate");
        }
        
    ?>
</head>

<body>
    
    <center><h2><u></u></h2></center>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td><h2>REKAP PAJAK BR <?PHP echo "$pdivprodid"; ?></h2></td><td><h2><?PHP echo ""; ?></h2></td></tr>
                <tr><td></td><td><?PHP echo ""; ?></td></tr>
                <tr><td>View Date : <i><?PHP echo "$ptglview"; ?></i></td><td><?PHP echo ""; ?></td></tr>
            </table>
        </div>
        <div id="isikanan">
            
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
    
    <?PHP
    
    $nnomorjml=1;
    $pjmlsudah=0;
    $totalsemuanya=0;
    $totalsemuanya_dpp=0;
    $totalsemuanya_ppn=0;
    $totalsemuanya_pph=0;
    //for($ijml=1;$ijml<=$pjmlfor;$ijml++) {
    ?>
    
        <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                    <th align="center">No BR/Divisi</th>
                    <th align="center">Date</th>
                    <th align="center">Bukti</th>
                    <th align="center">KODE</th>
                    <th align="center">PERKIRAAN</th>
                    <th align="center">DOKTER/SUPPLIER/ CUST</th>
                    <th align="center">NO. SLIP</th>
                    <th align="center">PENGAJUAN</th>
                    <th align="center">DAERAH</th>
                    <th align="center">KETERANGAN</th>
                    <th align="center">REALISASI</th>
                    <th align="center"></th>
                    <th align='center'>Debit</th>
                    <th align='center'>Kredit</th>
                    <th align='center'>Saldo</th>
                    <th align="center">No.</th>
                    <th align="center">PKP</th>
                    <th align="center">DPP</th>
                    <th align="center">PPN</th>
                    <th align="center">PPH</th>
                    <th align="center">TGL FP PPN</th>
                    <th align="center">SERI FP PPN</th>
                    <th align="center">TGL FP PPH</th>
                    <th align="center">SERI FP PPH</th>
                    <th align="center">ID</th>
                </tr>
            </thead>
            <tbody>
            <?PHP
                $no=1;
                $pbbk="";
                
                $ngtotald=0;
                $ngtotalk=0;
                $ngtotals=0;
                
                $pjmldebit=0;
                $pjmlkredit=0;
                $pjmlsaldo=0;
                
                $ptotdpp=0;
                $ptotppn=0;
                $ptotpph=0;
                
                $query = "select * FROM $tmp03 order by nodivisi, urutan, noslip, brId, jns_pajak, COA4";// LIMIT $pjmlsudah, $plimit
                $tampil2=mysqli_query($cnit, $query);
                while ($row= mysqli_fetch_array($tampil2)) {
                    
                    $pnobrid = $row['brId'];
                    $pnodivisi_p = $row['nodivisi'];
                    $ppajak = $row['pajak'];
                    $pjnsdpp = $row['jenis_dpp'];
                    $pnamapengusaha = $row['nama_pengusaha'];
                    
                    $pstsbatal="";
                    if (isset($row['stsbatal'])) $pstsbatal = $row['stsbatal'];
                    
                    $ptgltrans = "";
                    if (!empty($row['tgltrans']) AND $row['tgltrans']<>"0000-00-00")
                        $ptgltrans =date("d-M-Y", strtotime($row['tgltrans']));

                    $pbbk = $row['nobbk'];
                    $pcoa = $row['COA4'];
                    $pnmcoa = $row['NAMA4'];
                    $pnmdokter = $row['nama_dokter'];
                    $pnoslip = $row['noslip'];

                    $ppengajuan = $row['nama_karyawan'];
                    $pcabang = $row['nama_cabang'];
                    if ($pcabang=="ETH - HO") $pcabang = "HO";

                    $paktivitas1 = $row['aktivitas1'];
                    $prealisasi = $row['realisasi1'];
                    $pnorek = "";

                    $ptglfp="";
                    if (!empty($row['tgl_fp']) AND $row['tgl_fp']<>"0000-00-00")
                        $ptglfp =date("d-M-Y", strtotime($row['tgl_fp']));

                    $pnoseri = $row['noseri'];
                    
                    $pdpp = $row['dpp'];
                    $pppn = $row['ppn_rp'];
                    $ppph = $row['pph_rp'];
                    
                    $pdebit="";
                    $pkredit="";
                    $psaldo="";
                    
                    $pjasa_rp = $row['jasa_rp'];
                    $psaldo = $row['amount'];
                    $pkredit = $row['amount'];
                    
                    if ($ppajak=="T") {
                    }else{
                        if ($ppajak=="Y" AND (double)$pdpp<>0) $pkredit=$pdpp;
                        if ($ppajak=="Y" AND (double)$pdpp<>0 AND !empty($pjnsdpp)) $pkredit=(double)$pjasa_rp;//+(double)$pdpp
                    }
                    $psaldo=$pkredit;
                    
                    //$pjmldebit=(double)$pjmldebit+(double)$pdebit;
                    $pjmlkredit=(double)$pjmlkredit+(double)$pkredit;
                    $pjmlsaldo=(double)$pjmlsaldo+(double)$psaldo;
                
                    $ptotdpp=(double)$ptotdpp+(double)$pdpp;
                    $ptotppn=(double)$ptotppn+(double)$pppn;
                    $ptotpph=(double)$ptotpph+(double)$ppph;
                    
                    $pdpp=number_format($pdpp,2,".",",");
                    $pppn=number_format($pppn,2,".",",");
                    $ppph=number_format($ppph,2,".",",");
                    
                    $pkredit=number_format($pkredit,2,".",",");
                    $psaldo=number_format($psaldo,2,".",",");
                    
                    
                    $stl_batal="";
                    if ($pstsbatal=="Y") $stl_batal="style='color:red;'";
                    
                    echo "<tr $stl_batal>";
                    echo "<td nowrap>$pnodivisi_p</td>";
                    echo "<td nowrap>$ptgltrans</td>";
                    echo "<td nowrap>$pbbk</td>";
                    echo "<td nowrap>$pcoa</td>";
                    echo "<td nowrap>$pnmcoa</td>";
                    echo "<td nowrap>$pnmdokter</td>";
                    echo "<td nowrap>$pnoslip</td>";
                    echo "<td nowrap>$ppengajuan</td>";
                    echo "<td nowrap>$pcabang</td>";
                    echo "<td nowrap>$paktivitas1</td>";
                    echo "<td nowrap>$prealisasi</td>";
                    echo "<td nowrap>$pnorek</td>";
                    echo "<td nowrap align='right'>$pdebit</td>";
                    echo "<td nowrap align='right'>$pkredit</td>";
                    echo "<td nowrap align='right'>$psaldo</td>";
                    echo "<td nowrap align='center'>$no</td>";
                    echo "<td nowrap>$pnamapengusaha</td>";
                    echo "<td nowrap align='right'>$pdpp</td>";
                    echo "<td nowrap align='right'>$pppn</td>";
                    echo "<td nowrap align='right'>$ppph</td>";
                    echo "<td nowrap>$ptglfp</td>";
                    echo "<td nowrap>$pnoseri</td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap>$pnobrid</td>";
                    echo "</tr>";
                    
                    $no++;
                    
                    $pjmlsudah++;
                }
                
                // saldo
                $totalsemuanya=(double)$totalsemuanya+(double)$pjmlsaldo;
                
                $totalsemuanya_dpp=(double)$totalsemuanya_dpp+(double)$ptotdpp;
                $totalsemuanya_ppn=(double)$totalsemuanya_ppn+(double)$ptotppn;
                $totalsemuanya_pph=(double)$totalsemuanya_pph+(double)$ptotpph;

    
                //$pjumlahpd=number_format($pjumlahpd,0,",",",");
                $pjmlkredit=number_format($pjmlkredit,2,".",",");
                $pjmlsaldo=number_format($pjmlsaldo,2,".",",");
                
                
                $ptotdpp=number_format($ptotdpp,2,".",",");
                $ptotppn=number_format($ptotppn,2,".",",");
                $ptotpph=number_format($ptotpph,2,".",",");
                    
                echo "<tr>";
                echo "<td>&nbsp;</td>";
                echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                echo "<td> <b>Total</b> </td> <td>&nbsp;</td> <td>&nbsp;</td>";
                echo "<td align='right'><b></b></td> <td align='right'><b></b></td> <td align='right'><b>$pjmlsaldo</b></td>";
                echo "<td>&nbsp;</td>";
                echo "<td>&nbsp;</td>";
                echo "<td align='right'><b>$ptotdpp</b></td> <td align='right'><b>$ptotppn</b></td> <td align='right'><b>$ptotpph</b></td>";
                echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                echo "<td>&nbsp;</td>";
                echo "</tr>";
                
                
                if ($pjmlfor==$nnomorjml) {
                    if ((double)$pjmlfor>1) {
                        $totalsemuanya=number_format($totalsemuanya,2,".",",");

                        $totalsemuanya_dpp=number_format($totalsemuanya_dpp,2,".",",");
                        $totalsemuanya_ppn=number_format($totalsemuanya_ppn,2,".",",");
                        $totalsemuanya_pph=number_format($totalsemuanya_pph,2,".",",");

                        echo "<tr>";
                        echo "<td>&nbsp;</td>";
                        echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                        echo "<td> <b></b> </td> <td>&nbsp;</td> <td>&nbsp;</td>";
                        echo "<td align='right'><b></b></td> <td align='right'><b></b></td> <td align='right'><b></b></td>";
                        echo "<td>&nbsp;</td>";
                        echo "<td>&nbsp;</td>";
                        echo "<td align='right'><b></b></td> <td align='right'><b></b></td> <td align='right'><b></b></td>";
                        echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                        echo "<td>&nbsp;</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td>&nbsp;</td>";
                        echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                        echo "<td> <b>Grand Total</b> </td> <td>&nbsp;</td> <td>&nbsp;</td>";
                        echo "<td align='right'><b></b></td> <td align='right'><b></b></td> <td align='right'><b>$totalsemuanya</b></td>";
                        echo "<td>&nbsp;</td>";
                        echo "<td>&nbsp;</td>";
                        echo "<td align='right'><b>$totalsemuanya_dpp</b></td> <td align='right'><b>$totalsemuanya_ppn</b></td> <td align='right'><b>$totalsemuanya_pph</b></td>";
                        echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                        echo "<td>&nbsp;</td>";
                        echo "</tr>";
                    
                    }
                
                }
                 
                $nnomorjml++
                
            ?>
            </tbody>
        </table>
    <?PHP
        echo "<br/>&nbsp;<br/>&nbsp;";
    //}
    ?>
    
    
</body>

</html>

<?PHP
hapusdata:
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp04");

    mysqli_close($cnmy);
?>