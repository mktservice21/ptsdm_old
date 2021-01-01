<?php

    $picardid=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];
    
    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmpprosbmpil00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmpprosbmpil01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpprosbmpil02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmpprosbmpil03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmpprosbmpil04_".$puserid."_$now ";
    $tmp05 =" dbtemp.tmpprosbmpil05_".$puserid."_$now ";
    $tmp06 =" dbtemp.tmpprosbmpil06_".$puserid."_$now ";
    $tmp07 =" dbtemp.tmpprosbmpil07_".$puserid."_$now ";
    $tmp08 =" dbtemp.tmpprosbmpil08_".$puserid."_$now ";
    $tmp09 =" dbtemp.tmpprosbmpil09_".$puserid."_$now ";
    $tmp10 =" dbtemp.tmpprosbmpil10_".$puserid."_$now ";
    $tmp11 =" dbtemp.tmpprosbmpil11_".$puserid."_$now ";
    
  
    $query = "select * from dbmaster.t_proses_bm_act WHERE tahun='XXXX' LIMIT 1";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "ALTER table $tmp01 MODIFY COLUMN noidauto BIGINT(50) NOT NULL AUTO_INCREMENT PRIMARY KEY";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
    
        $query = "CREATE INDEX `norm1` ON $tmp01 (kodeinput,divisi,coa,tglinput,tgltrans,karyawanid,dokterid,idinput_pd,nodivisi,nobukti, idinput_pd1, idinput_pd2,nodivisi1,nodivisi2)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    $query = "select distinct b.idinput, b.divisi, b.nodivisi, a.kodeinput, a.bridinput, b.pilih, b.kodeid, b.subkode from dbmaster.t_suratdana_br1 a "
            . " JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput WHERE "
            . " IFNULL(b.stsnonaktif,'')<>'Y' AND IFNULL(b.nodivisi,'')<>'' ";
    $query = "create TEMPORARY table $tmp10 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        $query = "CREATE INDEX `norm1` ON $tmp10 (idinput,divisi,nodivisi,kodeinput,bridinput, pilih)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        

    //BR ETHICAL A
    if (!empty($pbreth)) {
        $pfiltersel= " ('A') ";
        $pfilterdelete .="'A',";
		
		
        $query = "select brId, noslip, icabangid, tgl, tgltrans, divprodid, COA4, kode, realisasi1, "
                . " jumlah, jumlah1, jumlah jumlah_asli, jumlah1 as jumlah1_asli, "
                . " aktivitas1, aktivitas2, dokterId, dokter, karyawanId, ccyId, tgltrm, lampiran, ca, "
                . " dpp, ppn_rp, pph_rp, tgl_fp, CAST('' as CHAR(20)) as nobukti "
                . " from hrd.br0 WHERE IFNULL(batal,'')<>'Y' AND IFNULL(retur,'')<>'Y' AND "
                . " brId NOT IN (select DISTINCT IFNULL(brId,'') FROM hrd.br0_reject) AND "
                . " DATE_FORMAT(tgltrans,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
        if ($pdivisi=="PRS_") {   
        }else{
            if (!empty($pdivisi)) $query .=" AND divprodid='$pdivisi' ";
        }
        if ($filtercoa=="PRS_") {    
        }else{
            if (!empty($filtercoa)) $query .=" AND IFNULL(COA4,'') IN $filtercoa ";
        }
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp02 (brId,dokterId)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        
            
            
            //via SBY
            $query = "select a.bridinput brId, b.noslip, b.icabangid, b.tgl, a.tgltransfersby tgltrans, b.divprodid, "
                    . " b.COA4, b.kode, b.realisasi1, a.jumlah jumlah, a.jumlah jumlah1, a.jumlah jumlah_asli, a.jumlah as jumlah1_asli, "
                    . " b.aktivitas1, b.aktivitas2, b.dokterId, b.dokter, b.karyawanId, b.ccyId, b.tgltrm, b.lampiran, b.ca, "
                    . " b.dpp, b.ppn_rp, b.pph_rp, b.tgl_fp, "
                    . " a.nobukti "
                    . " from dbmaster.t_br0_via_sby a JOIN hrd.br0 b on a.bridinput=b.brId "
                    . " WHERE IFNULL(b.batal,'')<>'Y' AND IFNULL(b.retur,'')<>'Y' AND "
                    . " a.bridinput NOT IN (select DISTINCT IFNULL(brId,'') FROM hrd.br0_reject) AND "
                    . " DATE_FORMAT(a.tgltransfersby,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
            if ($pdivisi=="PRS_") {   
            }else{
                if (!empty($pdivisi)) $query .=" AND b.divprodid='$pdivisi' ";
            }
            if ($filtercoa=="PRS_") {    
            }else{
                if (!empty($filtercoa)) $query .=" AND IFNULL(b.COA4,'') IN $filtercoa ";
            }
            $query = "create TEMPORARY table $tmp05 ($query)"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "CREATE INDEX `norm1` ON $tmp05 (brId,dokterId)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        
            
            $query = "DELETE FROM $tmp02 WHERE brId IN (select distinct IFNULL(brId,'') FROM $tmp05)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            $query = "INSERT INTO $tmp02 (brId, noslip, icabangid, tgl, tgltrans, divprodid, "
                    . " COA4, kode, realisasi1, jumlah, jumlah1, jumlah_asli, jumlah1_asli, "
                    . " aktivitas1, aktivitas2, dokterId, dokter, karyawanId, ccyId, tgltrm, lampiran, ca, "
                    . " dpp, ppn_rp, pph_rp, tgl_fp, nobukti) "
                    . " select brId, noslip, icabangid, tgl, tgltrans, divprodid, "
                    . " COA4, kode, realisasi1, jumlah, jumlah1, jumlah_asli, jumlah1_asli, "
                    . " aktivitas1, aktivitas2, dokterId, dokter, karyawanId, ccyId, tgltrm, lampiran, ca, "
                    . " dpp, ppn_rp, pph_rp, tgl_fp, nobukti "
                    . " from $tmp05 ";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            //END via SBY
            
            
            
        $query = "UPDATE $tmp02 SET jumlah=jumlah1 WHERE IFNULL(jumlah1,0)<>0";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        $query = "UPDATE $tmp02 SET jumlah_asli=NULL, jumlah1_asli=NULL"; 
        //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "select dokterId, nama from hrd.dokter WHERE dokterId IN (select distinct IFNULL(dokterId,'') from $tmp02)";
        $query = "create TEMPORARY table $tmp04 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp04 (dokterId)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        
            
            
        
        $query = "select a.*, d.nama nama_dokter, e.nama nama_karyawan, b.nama nama_cabang, c.nama nama_kode, "
                . " CAST('' as CHAR(50)) as nodivisi, CAST('' as CHAR(50)) as nodivisi1, CAST('' as CHAR(50)) as nodivisi2, "
                . " CAST('' as CHAR(2)) as hapus_nodiv_kosong "
                . " from $tmp02 a LEFT JOIN mkt.icabang b on a.icabangid=b.icabangid "
                . " LEFT JOIN hrd.br_kode c on a.kode=c.kodeid "
                . " LEFT JOIN $tmp04 d on a.dokterId=d.dokterId"
                . " LEFT JOIN hrd.karyawan e on a.karyawanId=e.karyawanId";
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        
        $query = "ALTER table $tmp03 ADD COLUMN idinput BIGINT(20), ADD COLUMN idinput1 BIGINT(20), ADD COLUMN idinput2 BIGINT(20), ADD COLUMN kodeid_pd INT(4), ADD COLUMN subkode_pd VARCHAR(5), ADD COLUMN pcm VARCHAR(1), ADD COLUMN kasbonsby VARCHAR(1), ADD COLUMN coa_pcm VARCHAR(50), ADD COLUMN nama_coa_pcm VARCHAR(100)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
            $query = "CREATE INDEX `norm1` ON $tmp03 (brId)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        
            
        
        $query = "UPDATE $tmp03 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp10 WHERE kodeinput IN ('A', 'B', 'C') AND divisi<>'OTC') b on a.brId=b.bridinput "
                . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput, a.nodivisi2=b.nodivisi, a.idinput2=b.idinput, a.kodeid_pd=b.kodeid, a.subkode_pd=b.subkode WHERE b.pilih='Y'"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        $query = "UPDATE $tmp03 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp10 WHERE kodeinput IN ('A', 'B', 'C') AND divisi<>'OTC') b on a.brId=b.bridinput "
                . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput, a.nodivisi1=b.nodivisi, a.idinput1=b.idinput, a.kodeid_pd=b.kodeid, a.subkode_pd=b.subkode WHERE b.pilih='N' AND IFNULL(a.nodivisi,'')=''"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
            $query = "UPDATE $tmp03 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp10 WHERE kodeinput IN ('A', 'B', 'C') AND divisi<>'OTC') b on a.brId=b.bridinput "
                    . " SET a.nodivisi1=b.nodivisi, a.idinput1=b.idinput, a.kodeid_pd=b.kodeid, a.subkode_pd=b.subkode WHERE b.pilih='N'"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            
            $query = "UPDATE $tmp03 SET pcm='Y' WHERE IFNULL(nodivisi1,'')<>'' AND IFNULL(nodivisi2,'')=''"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "UPDATE $tmp03 SET kasbonsby='Y' WHERE CONCAT(kodeid_pd,subkode_pd) IN ('680')"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            $query = "UPDATE $tmp03 SET pcm='' WHERE "
                    . " IFNULL(nodivisi2,'')='' AND ( (IFNULL(tgltrm,'0000-00-00')<>'0000-00-00' AND IFNULL(tgltrm,'')<>'') OR ( IFNULL(lampiran,'')='Y' AND IFNULL(ca,'')='N' AND IFNULL(tgltrm,'0000-00-00')='0000-00-00') )"
                    . " AND CONCAT(kodeid_pd,subkode_pd) NOT IN ('680')";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            //UPDATE PCM JADI U.M BIAYA UANG MUKA
            $query = "UPDATE $tmp03 SET coa_pcm='105-02', nama_coa_pcm='U.M. BIAYA' WHERE IFNULL(pcm,'')='Y'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            
        //$query ="DELETE FROM $tmp03 WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(tgl,'%Y-%m')>='2020-01'";
        //$query ="UPDATE $tmp03 SET hapus_nodiv_kosong='Y' WHERE IFNULL(nodivisi,'')='' AND ( IFNULL(tgltrans,'')='' OR IFNULL(tgltrans,'0000-00-00')='0000-00-00' ) AND DATE_FORMAT(tgl,'%Y-%m')>='2020-01'";
        $query ="UPDATE $tmp03 SET hapus_nodiv_kosong='Y' WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(tgl,'%Y-%m')>='2020-01'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "INSERT INTO $tmp01 (hapus_nodiv_kosong, kodeinput, idkodeinput, divisi, coa, tglinput, tgltrans, icabangid, nama_cabang, karyawanid, nama_karyawan, dokterid, dokter_nama, noslip, nmrealisasi, pengajuan, keterangan, dpp, ppn, pph, tglfp, idinput_pd, nodivisi, kredit, jumlah1, jumlah2, idinput_pd1, idinput_pd2, nodivisi1, nodivisi2, kodeid_pd, subkode_pd, pcm, kasbonsby, coa_pcm, nama_coa_pcm, nobukti, tgltarikan)"
                . " SELECT hapus_nodiv_kosong, 'A' kodeinput, brId, divprodid, COA4, tgl, tgltrans, icabangid, nama_cabang, karyawanid, nama_karyawan, dokterid, nama_dokter, noslip, realisasi1, nama_karyawan, aktivitas1, dpp, ppn_rp, pph_rp, tgl_fp, idinput, nodivisi, jumlah, jumlah_asli, jumlah1_asli, idinput1, idinput2, nodivisi1, nodivisi2, kodeid_pd, subkode_pd, pcm, kasbonsby, coa_pcm, nama_coa_pcm, nobukti, tgltrans FROM $tmp03";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    }
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
    
    //klaimdiscount B
    if (!empty($pklaim)) {
        $pfiltersel= " ('A', 'B') ";
        $pfilterdelete .="'B',";
		
		
        $query = "select DIVISI divprodid, tgl, tgltrans, distid, klaimId, COA4, karyawanid, noslip, "
                . " aktivitas1, realisasi1 nmrealisasi, jumlah, dpp, ppn_rp, pph_rp, tgl_fp, pengajuan divpengajuan "
                . " FROM hrd.klaim WHERE "
                . " klaimId not in (SELECT DISTINCT ifnull(klaimId,'') from hrd.klaim_reject) AND "
                . " DATE_FORMAT(tgltrans,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
        if ($pdivisi=="PRS_") {   
        }else{
            if (!empty($pdivisi)) $query .=" AND DIVISI='$pdivisi' ";
        }
        if ($filtercoa=="PRS_") {    
        }else{
            if (!empty($filtercoa)) $query .=" AND IFNULL(COA4,'') IN $filtercoa ";
        }
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp02 (klaimId)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        
        
        $query = "select a.*, b.nama nama_karyawan, c.ikotaid, c.nama namadist, "
                . " CAST('' as CHAR(50)) as nodivisi, CAST('' as CHAR(2)) as hapus_nodiv_kosong "
                . " FROM $tmp02 a "
                . " LEFT JOIN hrd.karyawan b on a.karyawanid=b.karyawanId"
                . " LEFT JOIN MKT.distrib0 c on a.distid=c.distid";
        $query = "create TEMPORARY table $tmp03 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        $query = "ALTER table $tmp03 ADD COLUMN idinput BIGINT(20), ADD COLUMN icabangid VARCHAR(10), ADD COLUMN nama_cabang VARCHAR(100)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp03 (klaimId, ikotaid, icabangid, idinput)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "UPDATE $tmp03 a JOIN MKT.icabang b ON a.ikotaid=b.ikotaid SET a.icabangid=b.icabangid, a.nama_cabang=b.nama";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp03 a JOIN (select distinct pilih, nodivisi, idinput, bridinput FROM $tmp10 WHERE kodeinput IN ('E') AND divisi='EAGLE') b on a.klaimId=b.bridinput "
                . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput WHERE b.pilih='Y'"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        $query = "UPDATE $tmp03 a JOIN (select distinct pilih, nodivisi, idinput, bridinput FROM $tmp10 WHERE kodeinput IN ('E') AND divisi='EAGLE') b on a.klaimId=b.bridinput "
                . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput WHERE b.pilih='N' AND IFNULL(a.nodivisi,'')=''"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        //$query ="DELETE FROM $tmp03 WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(tgl,'%Y-%m')>='2020-01'";
        $query ="UPDATE $tmp03 SET hapus_nodiv_kosong='Y' WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(tgl,'%Y-%m')>='2020-01'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
		
            $query ="UPDATE $tmp03 SET divpengajuan='CAN' WHERE IFNULL(divpengajuan,'') NOT IN ('PIGEO', 'PEACO', 'EAGLE', 'OTC')";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query ="UPDATE $tmp03 SET divprodid=divpengajuan WHERE IFNULL(divpengajuan,'')<>''";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query ="UPDATE $tmp03 SET COA4='701-03' WHERE IFNULL(divprodid,'')='EAGLE'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            $query ="UPDATE $tmp03 SET COA4='702-03' WHERE IFNULL(divprodid,'')='PIGEO'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            $query ="UPDATE $tmp03 SET COA4='703-03' WHERE IFNULL(divprodid,'')='PEACO'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            $query ="UPDATE $tmp03 SET COA4='704-03' WHERE IFNULL(divprodid,'')='OTC'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            $query ="UPDATE $tmp03 SET COA4='705-03' WHERE IFNULL(divprodid,'')='CAN'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
			
        
        $query = "INSERT INTO $tmp01 (hapus_nodiv_kosong, kodeinput, idkodeinput, divisi, coa, tglinput, tgltrans, icabangid, nama_cabang, karyawanid, nama_karyawan, dokterid, dokter_nama, noslip, nmrealisasi, pengajuan, keterangan, dpp, ppn, pph, tglfp, idinput_pd, nodivisi, kredit, jumlah1, jumlah2, tgltarikan)"
                . " SELECT hapus_nodiv_kosong, 'B' kodeinput, klaimId, divprodid, COA4, tgl, tgltrans, icabangid, nama_cabang, karyawanid, nama_karyawan, distid, namadist, noslip, nmrealisasi, nama_karyawan, aktivitas1, dpp, ppn_rp, pph_rp, tgl_fp, idinput, nodivisi, jumlah, jumlah, jumlah, tgltrans FROM $tmp03";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
    

    //KAS KASBON C & D
    if (!empty($pkas)) {
        $pfiltersel= " ('A', 'B', 'C', 'D') ";
        $pfilterdelete .="'C','D',";
        
        $query = "select CAST('C' as CHAR(1)) as nkode, e.DIVISI2 divprodid, a.periode1 tgltrans, a.kasId, a.kode, b.COA4, a.karyawanid, f.nama nama_karyawan, a.nama pengajuan, a.nobukti,
                a.aktivitas1, a.jumlah, CAST('' as CHAR(2)) as hapus_nodiv_kosong
                FROM hrd.kas a LEFT JOIN dbmaster.posting_coa_kas b on a.kode=b.kodeid
                LEFT JOIN dbmaster.coa_level4 c on b.COA4=c.COA4 LEFT JOIN dbmaster.coa_level3 d on c.COA3=d.COA3
                LEFT JOIN dbmaster.coa_level2 e on e.COA2=d.COA2
                LEFT JOIN hrd.karyawan f on a.karyawanid=f.karyawanId 
                WHERE DATE_FORMAT(a.periode1,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
        if ($pdivisi=="PRS_") {   
        }else{
            if (!empty($pdivisi)) $query .=" AND 'HO'='$pdivisi' ";
        }
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp02 (kasId)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        $query = "UPDATE $tmp02 SET COA4='105-02' WHERE IFNULL(COA4,'')=''";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        $query = "select CAST('D' as CHAR(1)) as nkode, e.DIVISI2 divisi, a.tgl, a.idkasbon, a.kode, b.COA4, a.karyawanid, f.nama nama_karyawan, a.nama pengajuan, '' as nobukti,
                a.keterangan, a.jumlah, CAST('' as CHAR(2)) as hapus_nodiv_kosong
                FROM dbmaster.t_kasbon a LEFT JOIN dbmaster.posting_coa_kas b on a.kode=b.kodeid
                LEFT JOIN dbmaster.coa_level4 c on b.COA4=c.COA4 LEFT JOIN dbmaster.coa_level3 d on c.COA3=d.COA3
                LEFT JOIN dbmaster.coa_level2 e on e.COA2=d.COA2
                LEFT JOIN hrd.karyawan f on a.karyawanid=f.karyawanId WHERE 
                IFNULL(a.stsnonaktif,'')<>'Y' AND DATE_FORMAT(a.tgl,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
        if ($pdivisi=="PRS_") {   
        }else{
            if (!empty($pdivisi)) $query .=" AND 'HO'='$pdivisi' ";
        }
        $query = "create TEMPORARY table $tmp03 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp03 (idkasbon)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        $query = "UPDATE $tmp03 SET COA4='105-02' WHERE IFNULL(COA4,'')=''";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        $query = "INSERT INTO $tmp02 (nkode, divprodid, tgltrans, kasId, kode, COA4, karyawanid, nama_karyawan, pengajuan, nobukti, aktivitas1, jumlah)"
                . " select nkode, divisi, tgl, idkasbon, kode, COA4, karyawanid, nama_karyawan, pengajuan, nobukti, keterangan, jumlah from $tmp03";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "select a.*, CAST('' as CHAR(50)) as nodivisi "
                . " from $tmp02 a";
        $query = "create TEMPORARY table $tmp04 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "ALTER table $tmp04 ADD COLUMN idinput BIGINT(20)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp04 (kasId)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        $query = "UPDATE $tmp04 a JOIN (select distinct pilih, nodivisi, idinput, bridinput FROM $tmp10 WHERE kodeinput IN ('T', 'K')) b on a.kasId=b.bridinput "
                . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query ="UPDATE $tmp04 SET COA4='105-02' WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(tgltrans,'%Y-%m')>='2020-01'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            if ($filtercoa=="PRS_") {    
            }else{
                $query ="DELETE FROM $tmp04 WHERE 1=1 ";
                if (!empty($filtercoa)) $query .=" AND IFNULL(COA4,'') NOT IN $filtercoa ";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            }
        
        
        
        $query = "INSERT INTO $tmp01 (hapus_nodiv_kosong, kodeinput, idkodeinput, divisi, coa, tglinput, tgltrans, icabangid, nama_cabang, karyawanid, nama_karyawan, pengajuan, keterangan, idinput_pd, nodivisi, kredit, jumlah1, jumlah2, tgltarikan)"
                . " SELECT hapus_nodiv_kosong, nkode, kasId, divprodid, COA4, tgltrans, tgltrans, '0000000001' as icabangid, 'ETH - HO' as nama_cabang, karyawanid, nama_karyawan, pengajuan, aktivitas1, idinput, nodivisi, jumlah, jumlah, jumlah, tgltrans FROM $tmp04";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    }
    
    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
    
    
    //BROTC E
    if (!empty($pbrotc)) {
        $pfiltersel= " ('A', 'B', 'C', 'D', 'E') ";
        $pfilterdelete .="'E',";
        
        $query = "select brOtcId, noslip, icabangid_o, tglbr, tgltrans, COA4, kodeid, subpost, real1, "
                . " jumlah, realisasi, jumlah jumlah_asli, realisasi as realisasi_asli, "
                . " keterangan1, keterangan2, lampiran, ca, dpp, ppn_rp, pph_rp, tgl_fp "
                . " from hrd.br_otc WHERE IFNULL(batal,'')<>'Y' AND "
                . " brOtcId NOT IN (select DISTINCT IFNULL(brOtcId,'') FROM hrd.br_otc_reject) AND "
                . " DATE_FORMAT(tgltrans, '%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
        if ($filtercoa=="PRS_") {    
        }else{
            if (!empty($filtercoa)) $query .=" AND IFNULL(COA4,'') IN $filtercoa ";
        }
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp02 (brOtcId, icabangid_o)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        $query = "UPDATE $tmp02 SET jumlah=realisasi WHERE IFNULL(realisasi,0)<>0";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "select a.*, b.nama nama_cabang, c.nama nama_kode, "
                . " CAST('' as CHAR(50)) as nodivisi, CAST('' as CHAR(50)) as nodivisi1, CAST('' as CHAR(50)) as nodivisi2, CAST('' as CHAR(2)) as hapus_nodiv_kosong "
                . " from $tmp02 a LEFT JOIN mkt.icabang_o b on a.icabangid_o=b.icabangid_o "
                . " LEFT JOIN hrd.brkd_otc c on a.kodeid=c.kodeid and a.subpost=c.subpost";
        $query = "create TEMPORARY table $tmp03 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp03 SET nama_cabang=icabangid_o where IFNULL(nama_cabang,'')=''";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        $query = "ALTER table $tmp03 ADD COLUMN idinput BIGINT(20), ADD COLUMN idinput1 BIGINT(20), ADD COLUMN idinput2 BIGINT(20), ADD COLUMN kodeid_pd INT(4), ADD COLUMN subkode_pd VARCHAR(5), ADD COLUMN pcm VARCHAR(1), ADD COLUMN kasbonsby VARCHAR(1), ADD COLUMN coa_pcm VARCHAR(50), ADD COLUMN nama_coa_pcm VARCHAR(100)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp03 (brOtcId, icabangid_o)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
        $query = "UPDATE $tmp03 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp10 WHERE kodeinput IN ('D') AND divisi='OTC') b on a.brOtcId=b.bridinput "
                . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput, a.nodivisi2=b.nodivisi, a.idinput2=b.idinput, a.kodeid_pd=b.kodeid, a.subkode_pd=b.subkode WHERE b.pilih='Y'"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        $query = "UPDATE $tmp03 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp10 WHERE kodeinput IN ('D') AND divisi='OTC') b on a.brOtcId=b.bridinput "
                . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput, a.nodivisi1=b.nodivisi, a.idinput1=b.idinput, a.kodeid_pd=b.kodeid, a.subkode_pd=b.subkode WHERE b.pilih='N' AND IFNULL(a.nodivisi,'')=''"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
            $query = "UPDATE $tmp03 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp10 WHERE kodeinput IN ('D') AND divisi='OTC') b on a.brOtcId=b.bridinput "
                    . " SET a.nodivisi1=b.nodivisi, a.idinput1=b.idinput, a.kodeid_pd=b.kodeid, a.subkode_pd=b.subkode WHERE b.pilih='N'"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
            
            $query = "UPDATE $tmp03 SET pcm='Y' WHERE IFNULL(nodivisi1,'')<>'' AND IFNULL(nodivisi2,'')=''"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "UPDATE $tmp03 SET kasbonsby='Y' WHERE CONCAT(kodeid_pd,subkode_pd) IN ('680')"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            $query = "UPDATE $tmp03 SET pcm='' WHERE "
                    . " IFNULL(nodivisi2,'')='' AND IFNULL(lampiran,'')='Y' AND IFNULL(ca,'')='N'"
                    . " AND CONCAT(kodeid_pd,subkode_pd) NOT IN ('680')";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            //UPDATE PCM JADI U.M BIAYA UANG MUKA
            $query = "UPDATE $tmp03 SET coa_pcm='105-02', nama_coa_pcm='U.M. BIAYA' WHERE IFNULL(pcm,'')='Y'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            
        //$query ="DELETE FROM $tmp03 WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(tglbr,'%Y-%m')>='2020-01'";
        //$query ="UPDATE $tmp03 SET hapus_nodiv_kosong='Y' WHERE IFNULL(nodivisi,'')='' AND ( IFNULL(tgltrans,'')='' OR IFNULL(tgltrans,'0000-00-00')='0000-00-00' ) AND DATE_FORMAT(tglbr,'%Y-%m')>='2020-01'";
        $query ="UPDATE $tmp03 SET hapus_nodiv_kosong='Y' WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(tglbr,'%Y-%m')>='2020-01'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "INSERT INTO $tmp01 (hapus_nodiv_kosong, kodeinput, idkodeinput, divisi, coa, tglinput, tgltrans, icabangid, nama_cabang, noslip, nmrealisasi, keterangan, dpp, ppn, pph, tglfp, idinput_pd, nodivisi, kredit, jumlah1, jumlah2, idinput_pd1, idinput_pd2, nodivisi1, nodivisi2, kodeid_pd, subkode_pd, pcm, kasbonsby, coa_pcm, nama_coa_pcm, tgltarikan)"
                . " SELECT hapus_nodiv_kosong, 'E' kodeinput, brOtcId, 'OTC' as divprodid, COA4, tglbr, tgltrans, icabangid_o, nama_cabang, noslip, real1, keterangan1, dpp, ppn_rp, pph_rp, tgl_fp, idinput, nodivisi, jumlah, jumlah_asli, realisasi_asli, idinput1, idinput2, nodivisi1, nodivisi2, kodeid_pd, subkode_pd, pcm, kasbonsby, coa_pcm, nama_coa_pcm, tgltrans FROM $tmp03";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    }
    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
    
    
    //RUTIN LUAR KOTA F rutin G lk
    if (!empty($prutin) OR !empty($pblk)) {
        $pfiltersel= " ('A', 'B', 'C', 'D', 'E', 'F', 'G') ";
        
        $pfilkode=" AND kode IN ('1', '2') ";
        if (!empty($prutin) AND !empty($pblk)) {
            $pfilterdelete .="'F','G',";
        }else{
            if (!empty($prutin)) {
                $pfilkode=" AND kode IN ('1') ";
                $pfilterdelete .="'F',";
            }
            if (!empty($pblk)) {
                $pfilkode=" AND kode IN ('2') ";
                $pfilterdelete .="'G',";
            }
        }
        
        
        $query = "select CAST('' as CHAR(2)) as hapus_nodiv_kosong, b.tgl_fin, b.kode, b.bulan, b.periode1, DATE_FORMAT(b.periode1,'%Y-%m-01') periode, a.idrutin, b.divisi, b.divi, b.karyawanid, b.nama_karyawan, "
                . " b.icabangid, b.areaid, b.icabangid_o, b.areaid_o, "
                . " a.coa, a.nobrid, a.rptotal, "
                . " IFNULL(a.notes,'') as ketdetail, IFNULL(b.keterangan,'') as keterangan, "
                . " a.deskripsi, DATE_FORMAT(a.tgl1,'%d/%m/%Y') as tgl1, DATE_FORMAT(a.tgl2,'%d/%m/%Y') as tgl2, a.qty, FORMAT(a.rp,0,'de_DE') as rp "
                . " from dbmaster.t_brrutin1 a "
                . " JOIN dbmaster.t_brrutin0 b on a.idrutin=b.idrutin WHERE "
                . " IFNULL(b.stsnonaktif,'') <> 'Y' $pfilkode AND "
                . " DATE_FORMAT(b.bulan,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
        if ($pdivisi=="PRS_") {   
        }else{
            if (!empty($pdivisi)) $query .=" AND b.divisi='$pdivisi' ";
        }
        if ($filtercoa=="PRS_") {    
        }else{
            if (!empty($filtercoa)) $query .=" AND IFNULL(a.coa,'') IN $filtercoa ";
        }
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp02 (idrutin, divi, kode, divisi, icabangid, areaid, icabangid_o, areaid_o,  karyawanid, nobrid)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            
        $query = "DELETE FROM $tmp02 WHERE IFNULL(divi,'')<>'OTC' AND ( IFNULL(tgl_fin,'')='' OR IFNULL(tgl_fin,'0000-00-00')='0000-00-00' OR IFNULL(tgl_fin,'0000-00-00 00:00:00')='0000-00-00 00:00:00')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
            //hilangkan
        if (!empty($pblk)) {
            //hilangkan
            $query = "select distinct idrutin, divisi from dbmaster.t_brrutin_ca_close WHERE idrutin IN (select distinct IFNULL(idrutin,'') from $tmp02)";
            $query = "create TEMPORARY table $tmp03 ($query)";
            //mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
                //$query = "CREATE INDEX `norm1` ON $tmp03 (idrutin, divisi)";
                //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "UPDATE $tmp02 a JOIN $tmp03 b on a.idrutin=b.idrutin SET a.divisi=b.divisi WHERE a.kode='2' AND IFNULL(b.divisi,'')<>''";
            //mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            //mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
            
        }
        
        
        $query = "UPDATE $tmp02 a JOIN dbmaster.posting_coa_rutin b on a.divisi=b.divisi AND a.nobrid=b.nobrid SET a.coa=b.COA4 WHERE IFNULL(a.divisi,'')<>''";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        //hilangkan
        //$query = "DELETE FROM $tmp02 WHERE coa NOT IN $filtercoa";
        //mysqli_query($cnmy, $query);
        //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
        $query = "select a.*, IFNULL(g.nama,'') nama_brid, b.nama namakry, c.nama nama_cabang, d.nama nama_area, "
                . " e.nama nmcabotc, f.nama nmareaotc, CAST('' as CHAR(50)) as nodivisi, "
                . " CAST('' as CHAR(50)) as kodeinput from $tmp02 a "
                . " LEFT JOIN hrd.karyawan b on a.karyawanid=b.karyawanid "
                . " LEFT JOIN MKT.icabang c on a.icabangid=c.iCabangId "
                . " LEFT JOIN MKT.iarea d on a.areaid=d.areaId AND a.icabangid=d.iCabangId "
                . " LEFT JOIN MKT.icabang_o e on a.icabangid_o=e.icabangid_o "
                . " LEFT JOIN MKT.iarea_o f on a.areaid_o=f.areaid_o AND a.icabangid_o=f.icabangid_o "
                . " LEFT JOIN dbmaster.t_brid g ON a.nobrid=g.nobrid";
        $query = "create TEMPORARY table $tmp03 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "ALTER table $tmp03 ADD COLUMN idinput BIGINT(20)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
            $query = "CREATE INDEX `norm1` ON $tmp03 (idrutin, divi, kode, divisi, icabangid, areaid, icabangid_o, areaid_o,  karyawanid)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        //OTHER
        $query = "UPDATE $tmp03 SET namakry=nama_karyawan, karyawanid=idrutin WHERE karyawanid IN ('0000002083', '0000002200')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp03 SET icabangid=icabangid_o, areaid=areaid_o, nama_cabang=nmcabotc, nama_area=nmareaotc WHERE divisi='OTC'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp03 a JOIN (select distinct pilih, nodivisi, idinput, bridinput FROM $tmp10 WHERE kodeinput IN ('F', 'I', 'N', 'M')) b on a.idrutin=b.bridinput "
                . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        

            
        
        
        $query ="DELETE FROM $tmp03 WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(bulan,'%Y-%m')>='2020-01'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query ="UPDATE $tmp03 SET kodeinput='F' WHERE kode='1'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        $query ="UPDATE $tmp03 SET kodeinput='G' WHERE kode='2'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
            $query ="UPDATE $tmp03 SET qty=NULL, rp=NULL WHERE (IFNULL(qty,0)='0' OR IFNULL(qty,0)='0') AND nobrid IN ('04', '25')";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        
            $query ="UPDATE $tmp03 SET deskripsi=CONCAT(nama_brid, ' (',qty,'x',rp,')', '<br/>', ketdetail) WHERE nobrid IN ('04', '25')";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query ="UPDATE $tmp03 SET deskripsi=CONCAT(nama_brid, ' (',tgl1,'s/d.',tgl2,')', '<br/>', ketdetail) WHERE nobrid IN ('21')";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query ="UPDATE $tmp03 SET deskripsi=CONCAT(nama_brid, '<br/>', ketdetail) WHERE nobrid NOT IN ('04', '25', '21')";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
            $query ="UPDATE $tmp03 SET hapus_nodiv_kosong='Y' WHERE IFNULL(rptotal,0)=0";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
			
        $query = "INSERT INTO $tmp01 (hapus_nodiv_kosong, kodeinput, idkodeinput, divisi, tglinput, tgltrans, tgltarikan, coa, keterangan, icabangid, nama_cabang, areaid, nama_area, karyawanid, nama_karyawan, pengajuan, nmrealisasi, nodivisi, idinput_pd, nobrid_r, nobrid_n, kredit, jumlah1, jumlah2)"
                . "SELECT hapus_nodiv_kosong, kodeinput, idrutin, divisi, periode, periode, periode, coa, keterangan, icabangid, nama_cabang, areaid, nama_area, karyawanid, namakry, namakry pengajuan, deskripsi, nodivisi, idinput, nobrid, nama_brid, sum(rptotal) rptotal, sum(rptotal) jumlah1, sum(rptotal) jumlah2 FROM $tmp03 GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
    }
    
    //CA H
    //if (!empty($prutin) OR !empty($pblk)) {
    //    $pfiltersel= " ('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H') ";
    //    $pfilterdelete .="'H',";
        
    //}
    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
    
    
    //BM biaya marketing surabaya I & J
    if (!empty($pbmsby)) {
        $pfiltersel= " ('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J') ";
        $pfilterdelete .="'I','J',";
        
        $query = "SELECT ID, TANGGAL, NOBBM, NOBBK, DIVISI, COA4, COA4_K, DEBIT, KREDIT, SALDO, KETERANGAN, CAST('' as CHAR(50)) as NOBUKTI FROM dbmaster.t_bm_sby WHERE "
                . " IFNULL(STSNONAKTIF,'')<>'Y' AND DATE_FORMAT(TANGGAL,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
        if ($pdivisi=="PRS_") {   
        }else{
            if (!empty($pdivisi)) $query .=" AND DIVISI='$pdivisi' ";
        }
        if ($filtercoa=="PRS_") {    
        }else{
            if (!empty($filtercoa)) $query .=" AND ( IFNULL(COA4,'') IN $filtercoa OR IFNULL(COA4_K,'') IN $filtercoa ) ";
        }
        
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp02 (ID)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
        $query = "UPDATE $tmp02 SET NOBUKTI=NOBBM WHERE IFNULL(NOBBM,'')<>''";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp02 SET NOBUKTI=NOBBK WHERE IFNULL(NOBBK,'')<>''";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        //NOTE :  di GL ada di posisi DEBIT jadi di Balik DEBIT = KREDIT begitu juga KREDIT = DEBIT

        //debit diinpsert jadi kredit
        $query = "INSERT INTO $tmp01 (kodeinput, idkodeinput, divisi, tglinput, tgltrans, tgltarikan, coa, icabangid, nama_cabang, keterangan, nobukti, kredit, jumlah1, jumlah2)"
                . "SELECT 'I' kodeinput, ID, DIVISI, TANGGAL, TANGGAL, TANGGAL, COA4, '0000000001' as icabangid, 'ETH - HO' as nama_cabang, KETERANGAN, NOBUKTI, DEBIT, DEBIT, KREDIT FROM $tmp02 WHERE 1=1 ";
        if ($filtercoa=="PRS_") {    
        }else{
            if (!empty($filtercoa)) $query .=" AND IFNULL(COA4,'') IN $filtercoa ";
        }
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        //kredit diinpsert jadi debit
        $query = "INSERT INTO $tmp01 (kodeinput, idkodeinput, divisi, tglinput, tgltrans, tgltarikan, coa, icabangid, nama_cabang, keterangan, nobukti, debit, jumlah1, jumlah2)"
                . "SELECT 'J' kodeinput, ID, DIVISI, TANGGAL, TANGGAL, TANGGAL, COA4_K, '0000000001' as icabangid, 'ETH - HO' as nama_cabang, KETERANGAN, NOBUKTI, KREDIT, DEBIT, KREDIT FROM $tmp02 WHERE 1=1 ";
        if ($filtercoa=="PRS_") {    
        }else{
            if (!empty($filtercoa)) $query .=" AND IFNULL(COA4_K,'') IN $filtercoa ";
        }
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    }
    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
    
    $filterpilih=false;
    if ($filtercoa=="PRS_") {
        $filterpilih=true;
    }else{
        if (strpos($filtercoa, "701-05")==true) $filterpilih=true;//eagle
        if (strpos($filtercoa, "702-05")==true) $filterpilih=true;//pigeo
        if (strpos($filtercoa, "703-05")==true) $filterpilih=true;//peaco
        if (strpos($filtercoa, "704-05")==true) $filterpilih=true;//otc
        if (strpos($filtercoa, "705-05")==true) $filterpilih=true;//can
    }
    
    //insentif incentive K
    if (!empty($ppilinsen) AND $filterpilih==true) {
        $pfiltersel= " ('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K') ";
        $pfilterdelete .="'K',";
        
        $query = "SELECT CAST(null as DECIMAL(10,0)) as urutan, a.bulan, a.divisi, a.cabang icabangid, b.nama cabang, "
                . " a.jabatan, a.karyawanid, a.nama, a.region, a.jumlah, CAST('' as CHAR(50)) as nodivisi "
                . " FROM dbmaster.incentiveperdivisi a "
                . " LEFT JOIN mkt.icabang b on a.cabang=b.iCabangId WHERE IFNULL(a.jumlah,0)<>0 AND "
                . " DATE_FORMAT(a.bulan,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
        if ($pdivisi=="PRS_") {   
        }else{
            if (!empty($pdivisi)) $query .=" AND a.divisi='$pdivisi' ";
        }
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "ALTER table $tmp02 ADD COLUMN idinput BIGINT(20)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp02 (urutan, bulan, divisi, icabangid, idinput)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
        $query="UPDATE $tmp02 SET urutan=1 WHERE jabatan='MR'";
        mysqli_query($cnmy, $query);
        $query="UPDATE $tmp02 SET urutan=2 WHERE jabatan='AM'";
        mysqli_query($cnmy, $query);
        $query="UPDATE $tmp02 SET urutan=3 WHERE jabatan='DM'";
        mysqli_query($cnmy, $query);
        
        $query="Alter table $tmp02 ADD COLUMN coa CHAR(50), ADD COLUMN nama_coa CHAR(100)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query ="UPDATE $tmp02 SET coa='705-05' WHERE divisi='CAN'";//, nama_coa='P1-DIN-INSENTIVE CANARY'
        mysqli_query($cnmy, $query);

        $query ="UPDATE $tmp02 SET coa='701-05' WHERE divisi='EAGLE'";//, nama_coa='P1-DIN-INSENTIVE EAGLE'
        mysqli_query($cnmy, $query);

        $query ="UPDATE $tmp02 SET coa='702-05' WHERE divisi='PIGEO'";//, nama_coa='P2-DIN-INSENTIVE PIGEON'
        mysqli_query($cnmy, $query);

        $query ="UPDATE $tmp02 SET coa='703-05' WHERE divisi='PEACO'";//, nama_coa='P3-DIN-INSENTIF PEACOCK'
        mysqli_query($cnmy, $query);
        
            if ($filtercoa=="PRS_") {    
            }else{
                $query ="DELETE FROM $tmp02 WHERE 1=1 ";
                if (!empty($filtercoa)) $query .=" AND IFNULL(coa,'') NOT IN $filtercoa ";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            }
        
        $query = "select idinput, nodivisi, nomor, tglf as bulan "
                . " from dbmaster.t_suratdana_br where concat(kodeid,subkode)='104' "
                . " and IFNULL(stsnonaktif,'')<>'' AND DATE_FORMAT(tglf,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' AND nodivisi like '%INC%'";
        $query = "create TEMPORARY table $tmp03 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp03 (idinput, nodivisi, nomor, bulan)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        $query = "UPDATE $tmp02 a JOIN $tmp03 b on DATE_FORMAT(a.bulan,'%Y-%m')=DATE_FORMAT(b.bulan,'%Y-%m') "
                . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "INSERT INTO $tmp01 (kodeinput, idkodeinput, divisi, tglinput, tgltrans, tgltarikan, coa, icabangid, nama_cabang, karyawanid, nama_karyawan, pengajuan, idinput_pd, nodivisi, kredit, jumlah1, jumlah2, keterangan)"
                . "SELECT 'K' kodeinput, urutan, divisi, bulan, bulan, bulan, coa, icabangid, cabang, karyawanid, nama, nama, idinput, nodivisi, jumlah, jumlah, jumlah, CONCAT('Periode ', DATE_FORMAT(bulan,'%M %Y')) FROM $tmp02";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
    }
    
    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
    
    
    //BANK L M N O P
    if (!empty($ppilbank)) {
        $pfiltersel= " ('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P') ";
        $pfilterdelete .="'L','M','N','O','P',";
        
        $query = "select nourut, bulan, tanggal, nobukti, divisi, nodivisi, idinput, coa4, "
                . " keterangan, debit, kredit, mintadana, idinputbank, stsinput, jmlothselisih, stsinput as stsinputasli "
                . " from dbmaster.t_bank_saldo_d "
                . " where IFNULL(idinputbank,'')<>'SAWAL' AND "
                . " DATE_FORMAT(bulan,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
        //$query .=" AND coa4='105-02' AND nobukti='BBM1501/B/2020' ";//AND (divisi='CAN' OR stsinput='M')
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp02 (nourut, bulan, tanggal, nobukti, divisi)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
          
            
            
                $query = "create TEMPORARY table $tmp03 (select distinct nobukti, idinput, jmlothselisih from $tmp02 WHERE stsinput='M')";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                $query = "UPDATE $tmp02 a JOIN $tmp03 b on a.nobukti=b.nobukti AND a.idinput=b.idinput SET a.jmlothselisih=b.jmlothselisih WHERE stsinput='N'";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
                
                
                $query = "UPDATE $tmp02 SET mintadana=debit WHERE stsinputasli IN ('M')";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                $query = "UPDATE $tmp02 SET debit=mintadana WHERE stsinputasli IN ('N')";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                    $query = "UPDATE $tmp02 SET debit=0 WHERE stsinputasli IN ('M')";
                    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                    
                    $query = "UPDATE $tmp02 SET mintadana=0, stsinput='D' WHERE stsinputasli IN ('N')";
                    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
        
        
        //OUTSTANDING
            $query = "select *, REPLACE(idinputbank,'OT','') as iidasli from $tmp02 WHERE left(idinputbank,2)='OT'";
            $query = "create TEMPORARY table $tmp05 ($query)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "ALTER TABLE $tmp05 MODIFY COLUMN iidasli BIGINT(18)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "CREATE INDEX `norm1` ON $tmp05 (iidasli, idinputbank, divisi, nodivisi)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            
            $query = "select a.idinputbank, a.iidasli, a.stsinput, a.divisi, a.coa4, a.nodivisi, a.idinput, 
                a.nobukti, a.bulan bulanots, c.bulan, a.tanggal, b.karyawanid, 
                d.idrutin, d.idca1, d.idca2, d.credit, d.saldo, d.ca1, d.ca2, 
                d.jmltrans, d.selisih, a.debit, a.mintadana, a.jmlothselisih, a.keterangan, d.icabangid, d.areaid    
                from $tmp05 a 
                LEFT JOIN dbmaster.t_brrutin_outstanding b ON a.iidasli=b.idots
                LEFT JOIN dbmaster.t_brrutin_ca_close_head c on a.idinput=c.idinput
                LEFT JOIN dbmaster.t_brrutin_ca_close d on c.igroup=d.igroup and d.karyawanid=b.karyawanid";
            
            $query = "create TEMPORARY table $tmp06 ($query)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "select distinct a.idinputbank, a.tanggal, a.karyawanid, a.icabangid, a.areaid, a.stsinput, a.divisi, a.nobukti, 
                    a.nodivisi, a.idinput, a.bulan, a.idrutin, a.credit, b.notes keterangan, a.keterangan pengajuan, b.coa, b.rptotal, 
                    b.nobrid, c.nama namabrid from $tmp06 a JOIN dbmaster.t_brrutin1 b on a.idrutin=b.idrutin
                    LEFT JOIN dbmaster.t_brid c on b.nobrid=c.nobrid";
            $query = "create TEMPORARY table $tmp07 ($query)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            //, nama_coa varchar(100)
            $query = "nourutauto BIGINT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, kodeinput varchar(1), idinputbank varchar(50), divisi varchar(10), karyawanid varchar(10), "
                    . " nobukti varchar(50), nodivisi varchar(50), idinput BIGINT(20), coa varchar(20), "
                    . " bulan date, myid varchar(50), keterangan varchar(200), pengajuan varchar(100), icabangid varchar(10), areaid varchar(10), debit DECIMAL(20,2), kredit DECIMAL(20,2)";
            $query = "create TEMPORARY table $tmp08 ($query)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "CREATE INDEX `norm1` ON $tmp08 (nourutauto, divisi, karyawanid, nobukti, nodivisi, idinput)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            
            
            $query = "select distinct tanggal, nobukti, karyawanid from $tmp06 where stsinput='D' order by divisi, tanggal, nobukti";
            $tampil=mysqli_query($cnmy, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $pnobuk=$row['nobukti'];
                $pkryid=$row['karyawanid'];
                $pbln=$row['tanggal'];
                $pbln = date("Y-m", strtotime($pbln));
                
                //CA1 U.M BIAYA
                $query = "insert into $tmp08 (kodeinput, idinputbank, divisi, karyawanid, nobukti, nodivisi, idinput, coa, bulan, myid, pengajuan, icabangid, areaid, debit)"
                        . " SELECT distinct 'O', idinputbank, divisi, karyawanid, nobukti, nodivisi, idinput, '105-02' coa, bulan, idca1, keterangan, icabangid, areaid, ca1 "
                        . " FROM $tmp06 where karyawanid='$pkryid' AND stsinput='D' AND DATE_FORMAT(tanggal,'%Y-%m')='$pbln' "
                        . " AND nobukti='$pnobuk' AND IFNULL(idca1,'')<>''";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                //CA1 BCA
                $query = "insert into $tmp08 (kodeinput, idinputbank, divisi, karyawanid, nobukti, nodivisi, idinput, coa, bulan, myid, pengajuan, icabangid, areaid, kredit)"
                        . " SELECT distinct 'O', idinputbank, divisi, karyawanid, nobukti, nodivisi, idinput, '101-02-002' coa, bulan, idca1, keterangan, icabangid, areaid, ca1 "
                        . " FROM $tmp06 where karyawanid='$pkryid' AND stsinput='D' AND DATE_FORMAT(tanggal,'%Y-%m')='$pbln' "
                        . " AND nobukti='$pnobuk' AND IFNULL(idca1,'')<>''";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                
                //BL
                $query = "insert into $tmp08 (kodeinput, idinputbank, divisi, karyawanid, nobukti, nodivisi, idinput, coa, bulan, myid, keterangan, pengajuan, icabangid, areaid, debit) "
                        . " SELECT distinct 'O', idinputbank, divisi, karyawanid, nobukti, nodivisi, idinput, coa, bulan, idrutin, CONCAT(namabrid, ' ',keterangan) as keterangan, pengajuan, icabangid, areaid, rptotal "
                        . " FROM $tmp07 where karyawanid='$pkryid' AND stsinput='D' AND DATE_FORMAT(tanggal,'%Y-%m')='$pbln' "
                        . " AND nobukti='$pnobuk' AND IFNULL(idrutin,'')<>''"
                        . " order by divisi, tanggal, nobukti, idrutin";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                //BL SUM
                $query = "insert into $tmp08 (kodeinput, idinputbank, divisi, karyawanid, nobukti, nodivisi, idinput, coa, bulan, pengajuan, icabangid, areaid, kredit) "
                        . " SELECT 'O', idinputbank, divisi, karyawanid, nobukti, nodivisi, idinput, '105-02' as coa, bulan, pengajuan, icabangid, areaid, sum(rptotal) as rptotal "
                        . " FROM $tmp07 where karyawanid='$pkryid' AND stsinput='D' AND DATE_FORMAT(tanggal,'%Y-%m')='$pbln' "
                        . " AND nobukti='$pnobuk' AND IFNULL(idrutin,'')<>''"
                        . " GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                
                //CA2 U.M BIAYA
                $query = "insert into $tmp08 (kodeinput, idinputbank, divisi, karyawanid, nobukti, nodivisi, idinput, coa, bulan, myid, pengajuan, icabangid, areaid, debit)"
                        . " SELECT distinct 'O', idinputbank, divisi, karyawanid, nobukti, nodivisi, idinput, '105-02' coa, bulan, idca2, keterangan, icabangid, areaid, ca2 "
                        . " FROM $tmp06 where karyawanid='$pkryid' AND stsinput='D' AND DATE_FORMAT(tanggal,'%Y-%m')='$pbln' "
                        . " AND nobukti='$pnobuk' AND IFNULL(idca2,'')<>''";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                //CA2 BCA
                $query = "insert into $tmp08 (kodeinput, idinputbank, divisi, karyawanid, nobukti, nodivisi, idinput, coa, bulan, myid, pengajuan, icabangid, areaid, kredit)"
                        . " SELECT distinct 'O', idinputbank, divisi, karyawanid, nobukti, nodivisi, idinput, '101-02-002' coa, bulan, idca2, keterangan, icabangid, areaid, ca2 "
                        . " FROM $tmp06 where karyawanid='$pkryid' AND stsinput='D' AND DATE_FORMAT(tanggal,'%Y-%m')='$pbln' "
                        . " AND nobukti='$pnobuk' AND IFNULL(idca2,'')<>''";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                //OUTSTANDING TRF
                $query = "insert into $tmp08 (kodeinput, idinputbank, divisi, karyawanid, nobukti, nodivisi, idinput, coa, bulan, myid, pengajuan, icabangid, areaid, debit)"
                        . " SELECT distinct 'P', idinputbank, divisi, karyawanid, nobukti, nodivisi, idinput, '101-02-002' coa, tanggal, '' as idinputbank, keterangan, icabangid, areaid, debit "
                        . " FROM $tmp06 where karyawanid='$pkryid' AND stsinput='D' AND DATE_FORMAT(tanggal,'%Y-%m')='$pbln' "
                        . " AND nobukti='$pnobuk' AND IFNULL(idca2,'')<>''";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                //OUTSTANDING jika ada kelebihan
                $query = "insert into $tmp08 (kodeinput, idinputbank, divisi, karyawanid, nobukti, nodivisi, idinput, coa, bulan, myid, pengajuan, icabangid, areaid, debit)"
                        . " SELECT distinct 'P', idinputbank, divisi, karyawanid, nobukti, nodivisi, idinput, '905-02' coa, tanggal, '' as idinputbank, keterangan, icabangid, areaid, "
                        . " IFNULL(selisih,0)-IFNULL(debit,0) jmlkurleb "
                        . " FROM $tmp06 where karyawanid='$pkryid' AND stsinput='D' AND DATE_FORMAT(tanggal,'%Y-%m')='$pbln' "
                        . " AND IFNULL(debit,0)<>IFNULL(selisih,0) "
                        . " AND nobukti='$pnobuk' AND IFNULL(idca2,'')<>''";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                //OUTSTANDING TRF U.M BIAYA
                $query = "insert into $tmp08 (kodeinput, idinputbank, divisi, karyawanid, nobukti, nodivisi, idinput, coa, bulan, myid, pengajuan, icabangid, areaid, kredit)"
                        . " SELECT distinct 'P', idinputbank, divisi, karyawanid, nobukti, nodivisi, idinput, '105-02' coa, tanggal, '' as idinputbank, keterangan, icabangid, areaid, selisih "
                        . " FROM $tmp06 where karyawanid='$pkryid' AND stsinput='D' AND DATE_FORMAT(tanggal,'%Y-%m')='$pbln' "
                        . " AND nobukti='$pnobuk' AND IFNULL(idca2,'')<>''";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                
            }
            
                    //mysqli_query($cnmy, "delete from $tmp08 where karyawanid<>'0000002136'");
                    //mysqli_query($cnmy, "delete from $tmp02");
                    //mysqli_query($cnmy, "delete from $tmp05 where nobukti<>'BBM1505/B/2020'");
            
            
                    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp06");
                    $query = "select a.*, b.NAMA4 nama_coa, c.nama nama_cabang, d.nama nama_area from $tmp08 a LEFT JOIN dbmaster.coa_level4 b on a.coa=b.COA4 "
                            . " LEFT JOIN MKT.icabang c on a.icabangid=c.icabangid "
                            . " LEFT JOIN MKT.iarea d on a.areaid=d.areaid AND a.icabangid=d.icabangid";
                    $query = "create TEMPORARY table $tmp06 ($query)";
                    mysqli_query($cnmy, $query);
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                $query = "UPDATE $tmp08 a JOIN dbmaster.coa_level4 b on a.coa=b.COA4 "
                        . "SET a.nama_coa=b.NAMA4"; 
                //mysqli_query($cnmy, $query);
                //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


                //hapus outstanding bank
                $query = "delete from $tmp02 WHERE left(idinputbank,2)='OT' AND IFNULL(stsinput,'') IN ('D')";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

                $query = "INSERT INTO $tmp01 (kodeinput, idkodeinput, divisi, tglinput, tgltrans, tgltarikan, coa, keterangan, nobukti, debit, idinput, nodivisi)"
                        . "SELECT kodeinput, idinputbank, divisi, tanggal, tanggal, tanggal, coa4, keterangan, nobukti, debit, idinput_pd, nodivisi FROM $tmp05 WHERE "
                        . " IFNULL(debit,0)<>0 AND IFNULL(stsinput,'') IN ('D')";
                //mysqli_query($cnmy, $query);
                //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

                $query = "INSERT INTO $tmp01 (kodeinput, idkodeinput, divisi, tglinput, tgltrans, tgltarikan, coa, dokter_nama, pengajuan, keterangan, nobukti, debit, kredit, idinput_pd, nodivisi, nama_cabang, nama_area)"
                        . "SELECT kodeinput, idinputbank, divisi, bulan, bulan, bulan, coa, myid, pengajuan, keterangan, nobukti, debit, kredit, idinput, nodivisi, nama_cabang, nama_area FROM $tmp06";//$tmp08
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                            
        //END OUTSTANDING        
        
            
            
            
        $query = "UPDATE $tmp02 SET nodivisi='' where IFNULL(nodivisi,'')='0'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        $query = "UPDATE $tmp02 SET divisi='CAN' WHERE keterangan LIKE '%outstanding%' AND IFNULL(divisi,'')=''";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
                            
        //debit diinpsert jadi kredit
        $query = "INSERT INTO $tmp01 (kodeinput, idkodeinput, divisi, tglinput, tgltrans, tgltarikan, coa, keterangan, nobukti, debit, nodivisi, idinput_pd, icabangid, nama_cabang)"
                . "SELECT 'L' kodeinput, idinputbank, divisi, tanggal, tanggal, tanggal, coa4, keterangan, nobukti, debit, nodivisi, idinput, '0000000001' as icabangid, 'ETH - HO' as nama_cabang FROM $tmp02 WHERE IFNULL(debit,0)<>0 ";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        //kredit diinpsert jadi debit
        $query = "INSERT INTO $tmp01 (kodeinput, idkodeinput, divisi, tglinput, tgltrans, tgltarikan, coa, keterangan, nobukti, kredit, nodivisi, idinput_pd, icabangid, nama_cabang)"
                . "SELECT 'M' kodeinput, idinputbank, divisi, tanggal, tanggal, tanggal, coa4, keterangan, nobukti, kredit, nodivisi, idinput, '0000000001' as icabangid, 'ETH - HO' as nama_cabang FROM $tmp02 WHERE IFNULL(kredit,0)<>0 ";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "select * from $tmp02 WHERE left(idinputbank,2)='OT' AND IFNULL(stsinput,'')='M'";
        $query = "create TEMPORARY table $tmp03 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "select *, divisi as divisi2 from $tmp02 WHERE IFNULL(mintadana,0)<>0 AND left(idinputbank,2)='OT' AND IFNULL(stsinput,'')='N'";
        $query = "create TEMPORARY table $tmp04 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        
        $query = "UPDATE $tmp04 a JOIN $tmp03 b on a.nobukti=b.nobukti AND a.nodivisi=b.nodivisi and a.tanggal=b.tanggal SET a.divisi=b.divisi";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "INSERT INTO $tmp01 (kodeinput, idkodeinput, divisi, tglinput, tgltrans, tgltarikan, coa, keterangan, nobukti, rincian, nodivisi, divisi2, idinput_pd, icabangid, nama_cabang)"
                . "SELECT 'N' kodeinput, idinputbank, divisi, tanggal, tanggal, tanggal, coa4, keterangan, nobukti, mintadana, nodivisi, divisi2, idinput, '0000000001' as icabangid, 'ETH - HO' as nama_cabang FROM $tmp04 ";//WHERE IFNULL(mintadana,0)<>0 AND left(idinputbank,2)='OT' AND IFNULL(stsinput,'')='N'
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        

        
                $pdelfilkodeid="(".substr($pfilterdelete, 0, -1).")";
                if ($pdivisi=="PRS_") {
                }else{
                    if (!empty($pdivisi)) {
                        $query = "DELETE FROM $tmp01 WHERE divisi <> '$pdivisi' AND kodeinput IN $pdelfilkodeid";
                        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                    }
                }
                if ($filtercoa=="PRS_") {
                }else{
                    if (!empty($filtercoa)) {
                        $query = "DELETE FROM $tmp01 WHERE coa NOT IN $filtercoa AND kodeinput IN $pdelfilkodeid";
                        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                    }
                }
        
    }
    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp07");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp08");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp09");
    
    
    
    //SEWA KONTRAKAN RUMAH U
    if (!empty($psewakontrak)) {
        $pfiltersel= " ('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'U') ";
        $pfilterdelete .="'U',";
        
        
        $query = "select a.karyawanid, a.tglawal, a.nopol, b.merk, b.jenis, c.nama_jenis from dbmaster.t_kendaraan_pemakai a "
                . " LEFT JOIN dbmaster.t_kendaraan b on a.nopol=b.nopol "
                . " LEFT JOIN dbmaster.t_kendaraan_jenis c on b.jenis=c.jenis";
        $query = "create TEMPORARY table $tmp07 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        

            $query = "ALTER table $tmp07 ADD COLUMN noidauto BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            $query = "CREATE UNIQUE INDEX `unx1` ON $tmp07 (noidauto)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            $query = "select distinct idkodeinput from dbmaster.t_proses_bm_act where kodeinput='U'";
            $query = "create TEMPORARY table $tmp08 ($query)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "select idsewa, kode, nobrid, tgl, divisi, karyawanid, icabangid, areaid, "
                    . " icabangid_o, areaid_o, periode, tglmulai, tglakhir, "
                    . " jumlah, ppn, keterangan, COA4 "
                    . " from dbmaster.t_sewa WHERE IFNULL(stsnonaktif,'') <> 'Y' AND "
                    . " ( DATE_FORMAT(tglmulai,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' OR "
                    . " DATE_FORMAT(tglakhir,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' )";
            $query .= " AND idsewa NOT IN (select distinct IFNULL(idkodeinput,'') from $tmp08)";
            if ($pdivisi=="PRS_") {   
            }else{
                if (!empty($pdivisi)) $query .=" AND divisi='$pdivisi' ";
            }
            if ($filtercoa=="PRS_") {    
            }else{
                if (!empty($filtercoa)) $query .=" AND IFNULL(COA4,'') IN $filtercoa ";
            }
            $query = "create TEMPORARY table $tmp02 ($query)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        mysqli_query($cnmy, "UPDATE $tmp02 SET COA4='750-08' WHERE divisi='HO'"); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        mysqli_query($cnmy, "UPDATE $tmp02 SET COA4='751-08' WHERE divisi='EAGLE'"); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        mysqli_query($cnmy, "UPDATE $tmp02 SET COA4='752-08' WHERE divisi='PIGEO'"); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        mysqli_query($cnmy, "UPDATE $tmp02 SET COA4='753-08' WHERE divisi='PEACO'"); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        mysqli_query($cnmy, "UPDATE $tmp02 SET COA4='754-08' WHERE divisi='OTC'"); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        mysqli_query($cnmy, "UPDATE $tmp02 SET COA4='755-08' WHERE divisi='CAN'"); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        $query = "ALTER table $tmp02 ADD COLUMN nobuktibbk VARCHAR(20), ADD COLUMN jenis VARCHAR(50), ADD COLUMN nama_merk VARCHAR(100)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "select a.*, b.jabatanid, h.nama nama_jabatan, IFNULL(g.nama,'') nama_brid, b.nama nama_karyawan, c.nama nama_cabang, d.nama nama_area, "
                . " e.nama nmcabotc, f.nama nmareaotc, i.NAMA4, CAST('' as CHAR(2)) as hapus_nodiv_kosong from $tmp02 a "
                . " LEFT JOIN hrd.karyawan b on a.karyawanid=b.karyawanid "
                . " LEFT JOIN MKT.icabang c on a.icabangid=c.iCabangId "
                . " LEFT JOIN MKT.iarea d on a.areaid=d.areaId AND a.icabangid=d.iCabangId "
                . " LEFT JOIN MKT.icabang_o e on a.icabangid_o=e.icabangid_o "
                . " LEFT JOIN MKT.iarea_o f on a.areaid_o=f.areaid_o AND a.icabangid_o=f.icabangid_o "
                . " LEFT JOIN dbmaster.t_brid g ON a.nobrid=g.nobrid "
                . " LEFT JOIN hrd.jabatan h on b.jabatanid=h.jabatanid "
                . " LEFT JOIN dbmaster.coa_level4 i on a.COA4=i.COA4";
        $query = "create TEMPORARY table $tmp03 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp03 SET icabangid=icabangid_o, areaid=areaid_o, nama_cabang=nmcabotc, nama_area=nmareaotc WHERE divisi='OTC'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "ALTER table $tmp03 ADD COLUMN idinput BIGINT(20), ADD COLUMN nodivisi VARCHAR(20), ADD COLUMN tgltrans DATE";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        $query = "UPDATE $tmp03 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp10 where kodeinput IN ('S')) b on a.idsewa=b.bridinput "
                . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query ="UPDATE $tmp03 SET hapus_nodiv_kosong='Y' WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(tgl,'%Y-%m')>='2020-01'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "INSERT INTO $tmp01 (kodeinput, idkodeinput, divisi, tglinput, tgltrans, tgltarikan, coa, keterangan, kredit, jumlah1, nodivisi, idinput_pd, icabangid, nama_cabang, karyawanid, nama_karyawan)"
                . "SELECT 'U' kodeinput, idsewa, divisi, tgl, tglmulai, tglmulai, coa4, keterangan, jumlah, jumlah, nodivisi, idinput, icabangid, nama_cabang, karyawanid, nama_karyawan FROM $tmp03 ";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    
    }
    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp07");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp08");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp09");
    
    
    
    //SERVICE KENDARAAN V
    if (!empty($pserviceken)) {
        $pfiltersel= " ('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'U', 'V') ";
        $pfilterdelete .="'V',";
        
        
        $query = "select a.karyawanid, a.tglawal, a.nopol, b.merk, b.jenis, c.nama_jenis from dbmaster.t_kendaraan_pemakai a "
                . " LEFT JOIN dbmaster.t_kendaraan b on a.nopol=b.nopol "
                . " LEFT JOIN dbmaster.t_kendaraan_jenis c on b.jenis=c.jenis";
        $query = "create TEMPORARY table $tmp07 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query = "ALTER table $tmp07 ADD COLUMN noidauto BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            $query = "CREATE UNIQUE INDEX `unx1` ON $tmp07 (noidauto)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            $query = "select idservice, kode, nobrid, tgl, divisi, "
                    . " karyawanid, icabangid, areaid, icabangid_o, areaid_o, "
                    . " nopol, tglservice, km, jumlah, keterangan, COA4 "
                    . " from dbmaster.t_service_kendaraan WHERE IFNULL(stsnonaktif,'') <> 'Y' AND "
                    . " DATE_FORMAT(tglservice,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2'";
            if ($pdivisi=="PRS_") {   
            }else{
                if (!empty($pdivisi)) $query .=" AND divisi='$pdivisi' ";
            }
            if ($filtercoa=="PRS_") {    
            }else{
                if (!empty($filtercoa)) $query .=" AND IFNULL(COA4,'') IN $filtercoa ";
            }
            $query = "create TEMPORARY table $tmp02 ($query)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query = "ALTER table $tmp02 ADD COLUMN nobuktibbk VARCHAR(20), ADD COLUMN jenis VARCHAR(50), ADD COLUMN nama_merk VARCHAR(100)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


            $query = "select distinct karyawanid, DATE_FORMAT(tglawal,'%Y%m') bulan, nopol, nama_jenis, merk FROM $tmp07 order by 1,2";
            $tampil=mysqli_query($cnmy, $query);
            while ($nr= mysqli_fetch_array($tampil)) {
                $pikryid=$nr['karyawanid'];
                $pibln=$nr['bulan'];
                $pinopol=$nr['nopol'];
                $pidjenis=$nr['nama_jenis'];
                $pnmmerk=$nr['merk'];
                if (!empty($pinopol)) {

                    $query = "UPDATE $tmp02 SET nopol='$pinopol', jenis='$pidjenis', nama_merk='$pnmmerk' WHERE DATE_FORMAT(tglservice,'%Y%m')>='$pibln' AND karyawanid='$pikryid'";
                    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                }
            }
            
            
            $query = "select a.*, b.jabatanid, h.nama nama_jabatan, IFNULL(g.nama,'') nama_brid, b.nama nama_karyawan, c.nama nama_cabang, d.nama nama_area, "
                    . " e.nama nmcabotc, f.nama nmareaotc, i.NAMA4, CAST('' as CHAR(2)) as hapus_nodiv_kosong from $tmp02 a "
                    . " LEFT JOIN hrd.karyawan b on a.karyawanid=b.karyawanid "
                    . " LEFT JOIN MKT.icabang c on a.icabangid=c.iCabangId "
                    . " LEFT JOIN MKT.iarea d on a.areaid=d.areaId AND a.icabangid=d.iCabangId "
                    . " LEFT JOIN MKT.icabang_o e on a.icabangid_o=e.icabangid_o "
                    . " LEFT JOIN MKT.iarea_o f on a.areaid_o=f.areaid_o AND a.icabangid_o=f.icabangid_o "
                    . " LEFT JOIN dbmaster.t_brid g ON a.nobrid=g.nobrid "
                    . " LEFT JOIN hrd.jabatan h on b.jabatanid=h.jabatanid "
                    . " LEFT JOIN dbmaster.coa_level4 i on a.COA4=i.COA4";
            $query = "create TEMPORARY table $tmp03 ($query)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query = "UPDATE $tmp03 SET icabangid=icabangid_o, areaid=areaid_o, nama_cabang=nmcabotc, nama_area=nmareaotc WHERE divisi='OTC'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query = "ALTER table $tmp03 ADD COLUMN idinput BIGINT(20), ADD COLUMN nodivisi VARCHAR(20), ADD COLUMN tgltrans DATE";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


            $query = "UPDATE $tmp03 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp10 where kodeinput IN ('S', 'O')) b on a.idservice=b.bridinput "
                    . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query ="UPDATE $tmp03 SET hapus_nodiv_kosong='Y' WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(tglservice,'%Y-%m')>='2020-01'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        
            $query = "INSERT INTO $tmp01 (kodeinput, idkodeinput, divisi, tglinput, tgltrans, tgltarikan, coa, keterangan, kredit, jumlah1, nodivisi, idinput_pd, icabangid, nama_cabang, karyawanid, nama_karyawan)"
                    . "SELECT 'V' kodeinput, idservice, divisi, tgl, tglservice, tglservice, coa4, keterangan, jumlah, jumlah, nodivisi, idinput, icabangid, nama_cabang, karyawanid, nama_karyawan FROM $tmp03 ";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp07");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp08");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp09");
    
    
    $query = "select distinct tanggal, nobukti, idinput, nodivisi from dbmaster.t_suratdana_bank "
            . " WHERE IFNULL(stsnonaktif,'')<>'Y' and stsinput='K' and subkode not in ('29') "
            . " AND idinput IN (select distinct IFNULL(idinput,'') from $tmp10)";
    $query = "create TEMPORARY table $tmp11 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        $query = "CREATE INDEX `norm1` ON $tmp11 (tanggal, nobukti, idinput, nodivisi)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    $query = "UPDATE $tmp01 a JOIN $tmp11 b on a.idinput_pd=b.idinput SET a.nobukti=b.nobukti, a.tgl_trans_bank=b.tanggal WHERE "
            . " a.kodeinput NOT IN ('I', 'J', 'L', 'M', 'N', 'P', 'A', 'U', 'V')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
    $query = "UPDATE $tmp01 a JOIN $tmp11 b on a.idinput_pd=b.idinput SET a.nobukti=b.nobukti, a.tgl_trans_bank=b.tanggal WHERE "
            . " a.kodeinput IN ('A') AND IFNULL(a.nobukti,'')=''";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
    
    $query = "UPDATE $tmp01 a JOIN $tmp11 b on a.idinput_pd1=b.idinput SET a.nobukti=b.nobukti, a.tgl_trans_bank=b.tanggal WHERE IFNULL(a.nobukti,'')=''";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //update sesuai tgl transfer bank
    $query = "UPDATE $tmp01 SET tgltrans=tgl_trans_bank WHERE IFNULL(tgl_trans_bank,'')<>'' AND "
            . " IFNULL(tgl_trans_bank,'0000-00-00')<>'0000-00-00' AND "
            . " kodeinput NOT IN ('I', 'J', 'L', 'M', 'N', 'P')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select DISTINCT d.DIVISI2, d.COA1, e.NAMA1, c.COA2, d.NAMA2, b.COA3, c.NAMA3, b.COA4, b.NAMA4
       from dbmaster.coa_level4 b 
       LEFT JOIN dbmaster.coa_level3 c ON c.COA3=b.COA3
       LEFT JOIN dbmaster.coa_level2 d ON c.COA2=d.COA2
       LEFT JOIN dbmaster.coa_level1 e ON e.COA1=d.COA1";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        $query = "CREATE INDEX `norm1` ON $tmp02 (DIVISI2, COA1, COA2, COA3, COA4)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
    
    $query = "UPDATE $tmp01 a JOIN $tmp02 b on a.coa=b.COA4 SET a.nama_coa=b.NAMA4, "
            . " a.divisi_coa=b.DIVISI2, a.coa2=b.COA2, a.nama_coa2=b.NAMA2, a.coa3=b.COA3, a.nama_coa3=b.NAMA3";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
        $query = "UPDATE $tmp01 SET coa='' WHERE IFNULL(nama_coa,'')=''";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        
        $query = "UPDATE $tmp01 SET divisi='AA' WHERE IFNULL(divisi,'')=''";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
?>



<?PHP
$pberhasilquery=true;
goto leewatisaja;

hapusdata:
    $pberhasilquery=false;
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp05");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp06");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp07");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp08");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp09");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp10");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp11");
    mysqli_close($cnmy);
    
leewatisaja:
?>