    <?PHP
    
    if ($_GET['ket']=="bukan") {
        
        if (!empty($spdidinput)) {
            
            echo "<table>";
            echo "<tr>";
                echo "<td>";
                    echo "<a class='btn button1' href='eksekusi3.php?module=glreportspddetail&act=input&idmenu=$_GET[idmenu]&ket=excel&divisi=$_GET[divisi]&nodivisi=$_GET[nodivisi]&idinspd=$spdidinput' target='_blank'>EXCEL</a>";
                echo "</td>";
                echo "<td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</td>";
                echo "<td>";
                if ($psts_posting==true) {
                    
                    if ($psudahpost==true) {
                        echo "<input type='button' class='btn button3' value='HAPUS POST' onClick=\"ProsesDataPosting('hapuspost', '$spdidinput', '$spdnodivisi')\">";
                    }else{
                        echo "<input type='button' class='btn button2' value='POST' onClick=\"ProsesDataPosting('posting', '$spdidinput', '$spdnodivisi')\">";
                    }
                    
                }
                echo "</td>";
            echo "</tr>";
            echo "</table>";
            echo "<br/>&nbsp;<br/>&nbsp;";
            
        }
        
    }
        $now=date("mdYhis");
        $tmp01 =" dbtemp.RPTREKPD01_".$_SESSION['USERID']."_$now ";
        $tmp02 =" dbtemp.RPTREKPD02_".$_SESSION['USERID']."_$now ";
        $tmp03 =" dbtemp.RPTREKPD03_".$_SESSION['USERID']."_$now ";
        $tmp04 =" dbtemp.RPTREKPD04_".$_SESSION['USERID']."_$now ";
        $tmp05 =" dbtemp.RPTREKPD05_".$_SESSION['USERID']."_$now ";
        $tmp06 =" dbtemp.RPTREKPD06_".$_SESSION['USERID']."_$now ";
        
        $filter_pilih=" AND b.nodivisi='$spdnodivisi' ";
        if (!empty($spdidinput)) {
            $filter_pilih=" AND b.idinput='$spdidinput' ";
        }
        
        $query = "select b.pilih, b.kodeid, d.nama kodenama, b.subkode, d.subnama, b.divisi, 
            b.tgl, b.tglspd, b.nomor, b.nodivisi, b.jenis_rpt, b.jumlah jumlahpd, a.urutan, a.bridinput, a.amount, 
            a.nobbm, a.nobbk, b.coa4 coa, c.NAMA4 coa_nama from dbmaster.t_suratdana_br1 a 
            JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput 
            LEFT JOIN dbmaster.coa_level4 c on b.coa4=c.COA4 
            LEFT JOIN dbmaster.t_kode_spd d on b.kodeid=d.kodeid AND b.subkode=d.subkode WHERE IFNULL(stsnonaktif,'')<>'Y' $filter_pilih ";
        $query = "create TEMPORARY table $tmp01 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        /*
        $query = "select tanggal, nodivisi, nobukti, idinput FROM dbmaster.t_suratdana_bank WHERE subkode NOT IN ('29') AND "
                . " IFNULL(stsnonaktif,'')<>'Y' AND stsinput='K' AND nodivisi='$spdnodivisi' ";
        $query = "create TEMPORARY table $tmp06 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        */
        
        
        $query = "select distinct jenis_rpt FROM $tmp01";
        $tampil=mysqli_query($cnit, $query);
        $r= mysqli_fetch_array($tampil);
        $pnjenisrpt=$r['jenis_rpt'];
        
        if ($pnjenisrpt=="D") {
            $query = "select '' lampiran, '' as kode, '' as ca, '' as via,
                a.DIVISI divprodid, a.klaimId brId, a.karyawanid karyawanId, c.nama nama_karyawan,
                a.distid dokterId, '' as dokter, b.nama nama_dokter, a.aktivitas1, '' aktivitas2, a.jumlah, a.tgl, a.tgltrans, 
                a.realisasi1, a.noslip, a.COA4, d.NAMA4, 0 jumlah1, 0 realisasi2, '' iCabangId, '' nama_cabang, 
                a.pajak, a.dpp, a.ppn, a.ppn_rp, a.pph_jns, a.pph, a.pph_rp, a.tgl_fp, a.noseri, a.pembulatan,
                CAST(0 as DECIMAL(20,2)) as jasa_rp, CAST(0 as DECIMAL(20,2)) as materai_rp, CAST(NULL as CHAR(5)) as jenis_dpp 
                from hrd.klaim a LEFT JOIN MKT.distrib0 b on a.distid=b.distid 
                LEFT JOIN hrd.karyawan c on a.karyawanid=c.karyawanId 
                LEFT JOIN dbmaster.coa_level4 d on a.COA4=d.COA4
                WHERE a.klaimId IN (select DISTINCT IFNULL(bridinput,'') bridinput FROM $tmp01) ";
            $query = "create TEMPORARY table $tmp02 ($query)"; 
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            $query = "select a.*, g.urutan, g.nodivisi, g.amount, g.nobbm, g.nobbk, 
                CAST('' as CHAR(10)) as jns_pajak, CAST('' as CHAR(1)) as stsbatal 
                from $tmp02 a 
                JOIN $tmp01 g on a.brId=g.bridinput";
            $query = "create TEMPORARY table $tmp03 ($query)"; 
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
        }else{
        
            $query = "select divprodid, brId, icabangid, tgl, noslip, tgltrans, tglrpsby, karyawanId, dokterId, dokter,
                aktivitas1, aktivitas2, realisasi1, jumlah, jumlah1, realisasi2, COA4, kode,
                lampiran, ca, via, pajak, dpp, ppn, ppn_rp, pph_jns, pph, pph_rp, tgl_fp, noseri, pembulatan,
                jasa_rp, materai_rp, jenis_dpp, CAST('' as CHAR(1)) as stsbatal
                from hrd.br0 WHERE 
                brId IN (select distinct IFNULL(bridinput,'') from $tmp01)";
            $query = "create TEMPORARY table $tmp02 ($query)"; 
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            
            
            //mencari yang batal
            $query = "select * from $tmp01 WHERE IFNULL(bridinput,'') NOT IN (select distinct brId from $tmp02)";
            $query = "create TEMPORARY table $tmp05 ($query)"; 
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            $query = "INSERT INTO $tmp02 (divprodid, brId, icabangid, tgl, noslip, tgltrans, tglrpsby, karyawanId, dokterId, dokter,
                aktivitas1, aktivitas2, realisasi1, jumlah, jumlah1, realisasi2, COA4, kode,
                lampiran, ca, via, pajak, dpp, ppn, ppn_rp, pph_jns, pph, pph_rp, tgl_fp, noseri, pembulatan,
                jasa_rp, materai_rp, jenis_dpp, stsbatal
                )
                select divprodid, brId, icabangid, tgl, noslip, tgltrans, tglrpsby, karyawanId, dokterId, dokter,
                aktivitas1, aktivitas2, realisasi1, jumlah, jumlah1, realisasi2, COA4, kode,
                lampiran, ca, via, pajak, dpp, ppn, ppn_rp, pph_jns, pph, pph_rp, tgl_fp, noseri, pembulatan,
                jasa_rp, materai_rp, jenis_dpp, 'Y' stsbatal 
                from dbmaster.backup_br0 WHERE 
                brId IN (select distinct IFNULL(bridinput,'') from $tmp05)";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            //update ke piutang bca jkt
            $query = "UPDATE $tmp02 SET COA4='104-04' WHERE IFNULL(stsbatal,'')='Y'";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            //END batal
        
            
            
            $query = "select a.divprodid, a.brId, a.tgl, a.noslip, a.tgltrans, a.tglrpsby, a.karyawanId, c.nama nama_karyawan, a.dokterId, a.dokter, b.nama nama_dokter,
                a.aktivitas1, a.aktivitas2, a.realisasi1, a.jumlah, jumlah1, a.realisasi2, a.COA4, d.NAMA4, a.kode, e.nama nama_kode,
                a.lampiran, a.ca, a.via, a.pajak, a.dpp, a.ppn, a.ppn_rp, a.pph_jns, a.pph, a.pph_rp, a.tgl_fp, a.noseri, a.pembulatan,
                a.jasa_rp, a.materai_rp, a.jenis_dpp,
                f.nama nama_cabang, g.urutan, g.nodivisi, g.amount, g.nobbm, g.nobbk,
                CAST('' as CHAR(10)) as jns_pajak, a.stsbatal 
                from $tmp02 a 
                LEFT JOIN hrd.dokter b on a.dokterId=b.dokterId
                LEFT JOIN hrd.karyawan c on a.karyawanId=c.karyawanId 
                LEFT JOIN dbmaster.coa_level4 d on a.COA4=d.COA4 
                LEFT JOIN hrd.br_kode e on a.kode=e.kodeid AND a.divprodid=e.divprodid 
                LEFT JOIN mkt.icabang f on a.icabangid=f.iCabangId 
                JOIN $tmp01 g on a.brId=g.bridinput";
            $query = "create TEMPORARY table $tmp03 ($query)"; 
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; }
            
        }
        
        
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
        
        
        //DPP
        $query = "INSERT INTO $tmp04 (jns_pajak, divprodid, brId, noslip, karyawanId, nama_karyawan, "
                . " dokterId, dokter, nama_dokter, aktivitas1, aktivitas2, tgl, tgltrans, realisasi1, jumlah, COA4, nodivisi, nobbm, nobbk, amount)"
                . " SELECT 'DPP' jns_pajak, divprodid, brId, noslip, karyawanId, nama_karyawan, "
                . " dokterId, dokter, nama_dokter, aktivitas1, aktivitas2, tgl, tgltrans, realisasi1, dpp, COA4, nodivisi, nobbm, nobbk, dpp "
                . " from $tmp03  WHERE pajak='Y'";
        //mysqli_query($cnit, $query);
        //$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }     
        
        
        //PPN
        $query = "INSERT INTO $tmp04 (jns_pajak, divprodid, brId, noslip, karyawanId, nama_karyawan, "
                . " dokterId, dokter, nama_dokter, aktivitas1, aktivitas2, tgl, tgltrans, realisasi1, jumlah, COA4, nodivisi, nobbm, nobbk, amount)"
                . " SELECT 'PPN' jns_pajak, divprodid, brId, noslip, karyawanId, nama_karyawan, "
                . " dokterId, dokter, nama_dokter, aktivitas1, aktivitas2, tgl, tgltrans, realisasi1, ppn_rp, '106-04' COA4, nodivisi, nobbm, nobbk, ppn_rp "
                . " from $tmp03  WHERE pajak='Y' AND IFNULL(ppn_rp,0)<>0";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }           
        
        //PPH 23
        $query = "INSERT INTO $tmp04 (jns_pajak, divprodid, brId, noslip, karyawanId, nama_karyawan, "
                . " dokterId, dokter, nama_dokter, aktivitas1, aktivitas2, tgl, tgltrans, realisasi1, jumlah, COA4, nodivisi, nobbm, nobbk, amount)"
                . " SELECT 'PPH23' jns_pajak, divprodid, brId, noslip, karyawanId, nama_karyawan, "
                . " dokterId, dokter, nama_dokter, aktivitas1, aktivitas2, tgl, tgltrans, realisasi1, pph_rp, '205-08' COA4, nodivisi, nobbm, nobbk, pph_rp "
                . " from $tmp03  WHERE pajak='Y' AND IFNULL(pph_rp,0)<>0 AND pph_jns='pph23'";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        
        
        //PPH 21
        $query = "INSERT INTO $tmp04 (jns_pajak, divprodid, brId, noslip, karyawanId, nama_karyawan, "
                . " dokterId, dokter, nama_dokter, aktivitas1, aktivitas2, tgl, tgltrans, realisasi1, jumlah, COA4, nodivisi, nobbm, nobbk, amount)"
                . " SELECT 'PPH21' jns_pajak, divprodid, brId, noslip, karyawanId, nama_karyawan, "
                . " dokterId, dokter, nama_dokter, aktivitas1, aktivitas2, tgl, tgltrans, realisasi1, pph_rp, '205-02' COA4, nodivisi, nobbm, nobbk, pph_rp "
                . " from $tmp03  WHERE pajak='Y' AND IFNULL(pph_rp,0)<>0 AND pph_jns='pph21'";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        
        
        //PEMBULATAN
        $query = "INSERT INTO $tmp04 (jns_pajak, divprodid, brId, noslip, karyawanId, nama_karyawan, "
                . " dokterId, dokter, nama_dokter, aktivitas1, aktivitas2, tgl, tgltrans, realisasi1, jumlah, COA4, nodivisi, nobbm, nobbk, amount)"
                . " SELECT 'BULAT' jns_pajak, divprodid, brId, noslip, karyawanId, nama_karyawan, "
                . " dokterId, dokter, nama_dokter, aktivitas1, aktivitas2, tgl, tgltrans, realisasi1, pembulatan, '905-02' COA4, nodivisi, nobbm, nobbk, pembulatan "
                . " from $tmp03  WHERE pajak='Y' AND IFNULL(pembulatan,0)<>0";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
        
        //MATERAI
        $query = "INSERT INTO $tmp04 (jns_pajak, divprodid, brId, noslip, karyawanId, nama_karyawan, "
                . " dokterId, dokter, nama_dokter, aktivitas1, aktivitas2, tgl, tgltrans, realisasi1, jumlah, COA4, nodivisi, nobbm, nobbk, amount)"
                . " SELECT 'MATERAI' jns_pajak, divprodid, brId, noslip, karyawanId, nama_karyawan, "
                . " dokterId, dokter, nama_dokter, aktivitas1, aktivitas2, tgl, tgltrans, realisasi1, materai_rp, '' COA4, nodivisi, nobbm, nobbk, materai_rp "
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
                . " dokterId, dokter, nama_dokter, aktivitas1, aktivitas2, tgl, tgltrans, realisasi1, jumlah, COA4, NAMA4, nodivisi, nobbm, nobbk, amount, pajak)"
            . " SELECT jns_pajak, divprodid, brId, noslip, karyawanId, nama_karyawan, "
                . " dokterId, dokter, nama_dokter, aktivitas1, aktivitas2, tgl, tgltrans, realisasi1, jumlah, COA4, NAMA4, nodivisi, nobbm, nobbk, jumlah as amount, pajak "
            . " from $tmp04";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; } 
    
    

    $query = "UPDATE $tmp03 a JOIN $tmp01 b on a.brId=b.bridinput SET a.urutan=b.urutan";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; } 

    /*
    $query = "UPDATE $tmp03 a JOIN $tmp06 b SET a.nobbk=b.nobukti";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; } 

    $query = "UPDATE $tmp03 a JOIN $tmp06 b SET a.tgltrans=b.tanggal WHERE a.jenis_rpt='K'";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; } 
    */
    
    
        $query = "select distinct tgl, divisi, kodenama, nomor, nodivisi, coa, coa_nama, jumlahpd, jenis_rpt FROM $tmp01 order by tgl, divisi, nodivisi";
        $tampil=mysqli_query($cnit, $query);
        $r= mysqli_fetch_array($tampil);
        $pkodenm=$r['kodenama'];
        $pnospd=$r['nomor'];
        $pnodivisi=$r['nodivisi'];
        $pcoapd=$r['coa'];
        $pnmcoapd=$r['coa_nama'];
        $pjumlahpd=$r['jumlahpd'];

        $pdivisipd=$r['divisi'];

        $ppengajuanpd=$pdivisipd;
        $ppengajuanpd2="BR $pdivisipd";

        $pjenisrpt=$r["jenis_rpt"];
        $nket="";
        if ($pdivisipd=="EAGLE") {
            $nket="**Cash Advance";
            if ($pjenisrpt=="D") $nket="**Mau Minta Uang";
        }else{
            $nket="* Advance";
            if ($pjenisrpt=="K") $nket="* Klaim";
        }

        $ptglpd = "";
        if (!empty($r['tgl']) AND $r['tgl']<>"0000-00-00")
            $ptglpd =date("d-M-Y", strtotime($r['tgl']));
        
        
        
        
        $query = "select * FROM $tmp03";
        $jmlrec=mysqli_num_rows(mysqli_query($cnit, $query));
        $plimit=30;
        $pjmlfor=ceil((double)$jmlrec / (double)$plimit);
        
        echo "<table class='tjudul' width='100%'>";
        echo "<tr> <td width='300px' colspan='4'>Rekap Budget Request (RBR) Team $pdivisipd </td> <td> : </td> <td>$pnodivisi</td> </tr>";
        echo "<tr> <td width='200px' colspan='4'>$nket </td> <td> : </td> <td>$ptglpd</td> </tr>";
        echo "</table>";
        
        echo "<br/>&nbsp;";
    
    $nnomorjml=1;
    $pjmlsudah=0;
    $totalsemuanya=0;
    $totalsemuanya_dpp=0;
    $totalsemuanya_ppn=0;
    $totalsemuanya_pph=0;
    for($ijml=1;$ijml<=$pjmlfor;$ijml++) {
    ?>
    
        <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
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
                
                $query = "select * FROM $tmp03 order by urutan, noslip, nodivisi, brId, jns_pajak, COA4 LIMIT $pjmlsudah, $plimit";
                $tampil2=mysqli_query($cnit, $query);
                while ($row= mysqli_fetch_array($tampil2)) {
                    
                    $pnobrid = $row['brId'];
                    $ppajak = $row['pajak'];
                    $pjnsdpp = $row['jenis_dpp'];
                    
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
                    echo "<td nowrap align='right'>$pdpp</td>";
                    echo "<td nowrap align='right'>$pppn</td>";
                    echo "<td nowrap align='right'>$ppph</td>";
                    echo "<td nowrap>$ptglfp</td>";
                    echo "<td nowrap>$pnoseri</td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap>$pnobrid</td>";
                    echo "</tr>";
                    
                    

                    /*
                    if ($ppajak=="Ys") {
                        
                        $query_ = "select * FROM $tmp04 WHERE brId='$pnobrid' order by COA4";
                        $tampil_=mysqli_query($cnit, $query_);
                        while ($rw1= mysqli_fetch_array($tampil_)) {
                            $pcoa=$rw1['COA4'];
                            $pnmcoa=$rw1['NAMA4'];
                            $pbbk = $rw1['nobbk'];

                            $ptgltrans = "";
                            if (!empty($rw1['tgltrans']) AND $rw1['tgltrans']<>"0000-00-00")
                                $ptgltrans =date("d-M-Y", strtotime($rw1['tgltrans']));

                            $pnmdokter = $rw1['nama_dokter'];
                            $pnoslip = $rw1['noslip'];
                            $ppengajuan = $rw1['nama_karyawan'];
                            $pcabang = $rw1['nama_cabang'];
                            if ($pcabang=="ETH - HO") $pcabang = "HO";
                            $paktivitas1 = $rw1['aktivitas1'];
                            
                            $pkredit = $rw1['jumlah'];

                            $pkredit=number_format($pkredit,2,".",",");


                            echo "<tr>";
                            echo "<td nowrap>$ptgltrans</td>";
                            echo "<td nowrap>$pbbk</td>";
                            echo "<td nowrap>$pcoa</td>";
                            echo "<td nowrap>$pnmcoa</td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap align='right'></td>";
                            echo "<td nowrap align='right'>$pkredit</td>";
                            echo "<td nowrap align='right'></td>";
                            echo "<td nowrap align='center'></td>";
                            echo "<td nowrap align='right'></td>";
                            echo "<td nowrap align='right'></td>";
                            echo "<td nowrap align='right'></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "</tr>";
                        }
                        
                        //ada 22 colum
                        echo "<tr>";
                        echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                        echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                        echo "</tr>";
                        
                    }
                    */
                    
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
                echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                echo "<td> <b>Total</b> </td> <td>&nbsp;</td> <td>&nbsp;</td>";
                echo "<td align='right'><b></b></td> <td align='right'><b></b></td> <td align='right'><b>$pjmlsaldo</b></td>";
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
                        echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                        echo "<td> <b></b> </td> <td>&nbsp;</td> <td>&nbsp;</td>";
                        echo "<td align='right'><b></b></td> <td align='right'><b></b></td> <td align='right'><b></b></td>";
                        echo "<td>&nbsp;</td>";
                        echo "<td align='right'><b></b></td> <td align='right'><b></b></td> <td align='right'><b></b></td>";
                        echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                        echo "<td>&nbsp;</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                        echo "<td> <b>Grand Total</b> </td> <td>&nbsp;</td> <td>&nbsp;</td>";
                        echo "<td align='right'><b></b></td> <td align='right'><b></b></td> <td align='right'><b>$totalsemuanya</b></td>";
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
    }
    
        //echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
    ?>
    
    <?PHP
    hapusdata:
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp03");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp04");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp06");
        
        mysqli_close($cnit);
    ?>