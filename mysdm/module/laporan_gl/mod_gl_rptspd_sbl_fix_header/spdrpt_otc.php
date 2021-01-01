<?php
    if ($_GET['ket']=="bukan") {
        //echo "<a class='btn btn-success' href='eksekusi3.php?module=glreportspddetail&act=input&idmenu=$_GET[idmenu]&ket=excel&divisi=$_GET[divisi]&nodivisi=$_GET[nodivisi]' target='_blank'>EXCEL</a><br/>&nbsp;";
        
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

        $filter_pilih=" AND b.nodivisi='$spdnodivisi' ";
        if (!empty($spdidinput)) {
            $filter_pilih=" AND b.idinput='$spdidinput' ";
        }
        
    $query = "select b.kodeid, d.nama kodenama, b.subkode, d.subnama, b.divisi, 
        b.tgl, b.tglspd, b.nomor, b.nodivisi, b.jenis_rpt, b.jumlah jumlahpd, a.urutan, a.bridinput, a.amount, 
        a.nobbm, a.nobbk, b.coa4 coa, c.NAMA4 coa_nama from dbmaster.t_suratdana_br1 a 
        JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput 
        LEFT JOIN dbmaster.coa_level4 c on b.coa4=c.COA4 
        LEFT JOIN dbmaster.t_kode_spd d on b.kodeid=d.kodeid AND b.subkode=d.subkode WHERE 1=1 $filter_pilih ";//b.nodivisi='$spdnodivisi'
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
    
            $query = "select a.brOtcId, a.tgltrans, a.COA4, a.icabangid_o, a.noslip, a.kodeid, a.subpost,
               a.keterangan1, a.real1, a.jumlah, a.realisasi, 
               a.pajak, a.dpp, a.ppn, a.ppn_rp, a.pph_jns, a.pph, a.pph_rp, a.tgl_fp, a.noseri, a.pembulatan,
               a.lampiran, a.ca, a.via, a.tglbr, a.tglrpsby, a.tglreal, a.materai_rp, a.jasa_rp, a.jenis_dpp, a.batal as stsbatal, a.alasan_batal  
               from hrd.br_otc a WHERE 1=1 ";
            $query .= " AND a.brOtcId IN (select IFNULL(bridinput,'') from $tmp01)";
            $query = "create TEMPORARY table $tmp05 ($query)"; 
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
            $query = "UPDATE $tmp05 SET COA4='104-04' WHERE IFNULL(stsbatal,'')='Y'";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
            
    $query = "select a.brOtcId, a.tgltrans, a.COA4, b.NAMA4, a.icabangid_o, c.nama nama_cabang, a.noslip, a.kodeid, a.subpost,
       a.keterangan1, a.real1, a.jumlah, a.realisasi, 
       a.pajak, a.dpp, a.ppn, a.ppn_rp, a.pph_jns, a.pph, a.pph_rp, a.tgl_fp, a.noseri, a.pembulatan,
       a.lampiran, a.ca, a.via, a.tglbr, a.tglrpsby, a.tglreal, a.materai_rp, a.jasa_rp, a.jenis_dpp, a.stsbatal, a.alasan_batal  
       from $tmp05 a 
       LEFT JOIN dbmaster.coa_level4 b on a.COA4=b.COA4 
       LEFT JOIN mkt.icabang_o c on a.icabangid_o=c.icabangid_o WHERE 1=1 ";
    //$query .= " AND a.brOtcId IN (select IFNULL(bridinput,'') from $tmp01)";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnit, "alter table $tmp02 ADD nama_posting VARCHAR(200)");
    mysqli_query($cnit, "UPDATE $tmp02 a SET a.nama_posting =(SELECT b.nama FROM hrd.brkd_otc b WHERE a.kodeid=b.kodeid AND a.subpost=b.subpost) WHERE IFNULL(a.kodeid,'')<> '' AND IFNULL(a.subpost,'')<> ''");
    mysqli_query($cnit, "UPDATE $tmp02 a SET a.nama_posting =(SELECT b.nmsubpost FROM hrd.brkd_otc b WHERE a.subpost=b.subpost) WHERE IFNULL(a.kodeid,'')<> '' AND IFNULL(a.subpost,'')= '' AND IFNULL(a.nama_posting,'')= ''");

    mysqli_query($cnit, "UPDATE $tmp02 a SET a.nama_cabang =(SELECT b.nama FROM dbmaster.cabang_otc b WHERE a.icabangid_o=b.cabangid_ho) WHERE IFNULL(a.nama_cabang,'')=''");
    
    
    $query = "select a.*, b.urutan, b.nodivisi, b.amount, b.nobbm, b.nobbk,"
            . " CAST('' as CHAR(10)) as jns_pajak "
            . " from $tmp02 a JOIN $tmp01 b on a.brOtcId=b.bridinput";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
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
        
        
    $query = "select * from $tmp03 WHERE brOtcId=''LIMIT 1";
    $query = "create TEMPORARY table $tmp04 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "DELETE FROM $tmp04";
    mysqli_query($cnit, $query);   
        
    //DPP
    $query = "INSERT INTO $tmp04 (jns_pajak, brOtcId, tgltrans, tglbr, tglrpsby, tglreal, icabangid_o, nama_cabang, "
            . " noslip, kodeid, subpost, keterangan1, real1, realisasi, jumlah, COA4)"
            . " SELECT 'DPP' jns_pajak, brOtcId, tgltrans, tglbr, tglrpsby, tglreal, icabangid_o, nama_cabang, "
            . " noslip, kodeid, subpost, keterangan1, real1, realisasi, dpp, COA4 "
            . " from $tmp03  WHERE pajak='Y'";
    //mysqli_query($cnit, $query);
    //$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }   
    
    //PPN
    $query = "INSERT INTO $tmp04 (jns_pajak, brOtcId, tgltrans, tglbr, tglrpsby, tglreal, icabangid_o, nama_cabang, "
            . " noslip, kodeid, subpost, keterangan1, real1, realisasi, jumlah, COA4, nodivisi, nobbm, nobbk, amount)"
            . " SELECT 'PPN' jns_pajak, brOtcId, tgltrans, tglbr, tglrpsby, tglreal, icabangid_o, nama_cabang, "
            . " noslip, kodeid, subpost, keterangan1, real1, realisasi, ppn_rp, '106-04' COA4, nodivisi, nobbm, nobbk, ppn_rp "
            . " from $tmp03  WHERE pajak='Y' AND IFNULL(ppn_rp,0)<>0";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }  
    
    
    //PPH 23
    $query = "INSERT INTO $tmp04 (jns_pajak, brOtcId, tgltrans, tglbr, tglrpsby, tglreal, icabangid_o, nama_cabang, "
            . " noslip, kodeid, subpost, keterangan1, real1, realisasi, jumlah, COA4, nodivisi, nobbm, nobbk, amount)"
            . " SELECT 'PPH23' jns_pajak, brOtcId, tgltrans, tglbr, tglrpsby, tglreal, icabangid_o, nama_cabang, "
            . " noslip, kodeid, subpost, keterangan1, real1, realisasi, pph_rp, '205-08' COA4, nodivisi, nobbm, nobbk, pph_rp "
            . " from $tmp03  WHERE pajak='Y' AND IFNULL(pph_rp,0)<>0 AND pph_jns='pph23'";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    //PPH 21
    $query = "INSERT INTO $tmp04 (jns_pajak, brOtcId, tgltrans, tglbr, tglrpsby, tglreal, icabangid_o, nama_cabang, "
            . " noslip, kodeid, subpost, keterangan1, real1, realisasi, jumlah, COA4, nodivisi, nobbm, nobbk, amount)"
            . " SELECT 'PPH21' jns_pajak, brOtcId, tgltrans, tglbr, tglrpsby, tglreal, icabangid_o, nama_cabang, "
            . " noslip, kodeid, subpost, keterangan1, real1, realisasi, pph_rp, '205-02' COA4, nodivisi, nobbm, nobbk, pph_rp "
            . " from $tmp03  WHERE pajak='Y' AND IFNULL(pph_rp,0)<>0 AND pph_jns='pph21'";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    //PEMBULATAN
    $query = "INSERT INTO $tmp04 (jns_pajak, brOtcId, tgltrans, tglbr, tglrpsby, tglreal, icabangid_o, nama_cabang, "
            . " noslip, kodeid, subpost, keterangan1, real1, realisasi, jumlah, COA4, nodivisi, nobbm, nobbk, amount)"
            . " SELECT 'BULAT' jns_pajak, brOtcId, tgltrans, tglbr, tglrpsby, tglreal, icabangid_o, nama_cabang, "
            . " noslip, kodeid, subpost, keterangan1, real1, realisasi, pembulatan, '905-02' COA4, nodivisi, nobbm, nobbk, pembulatan "
            . " from $tmp03  WHERE pajak='Y' AND IFNULL(pembulatan,0)<>0";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    //MATERAI
    $query = "INSERT INTO $tmp04 (jns_pajak, brOtcId, tgltrans, tglbr, tglrpsby, tglreal, icabangid_o, nama_cabang, "
            . " noslip, kodeid, subpost, keterangan1, real1, realisasi, jumlah, COA4, nodivisi, nobbm, nobbk, amount)"
            . " SELECT 'MATERAI' jns_pajak, brOtcId, tgltrans, tglbr, tglrpsby, tglreal, icabangid_o, nama_cabang, "
            . " noslip, kodeid, subpost, keterangan1, real1, realisasi, materai_rp, '' COA4, nodivisi, nobbm, nobbk, materai_rp "
            . " from $tmp03  WHERE pajak='Y' AND IFNULL(materai_rp,0)<>0";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; } 
    
    
    $query="UPDATE $tmp04 SET COA4='754-07' WHERE jns_pajak='MATERAI'";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }  
    
    
        //cocokan COA dari tabel
        
        $query="UPDATE $tmp04 a SET a.COA4=IFNULL((select b.COA4 FROM dbmaster.coa_pajak b WHERE a.jns_pajak=b.jns_pajak),COA4) WHERE "
                . " a.jns_pajak IN ('PPN', 'PPH23', 'PPH21', 'BULAT')";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
        
        $query="UPDATE $tmp04 a SET a.COA4=IFNULL((select b.COA4 FROM dbmaster.coa_pajak b WHERE a.jns_pajak=b.jns_pajak AND 'OTC'=IFNULL(b.divisi,'')),a.COA4) WHERE "
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
        
        
        
        
    $query = "INSERT INTO $tmp03 (jns_pajak, brOtcId, tgltrans, tglbr, tglrpsby, tglreal, icabangid_o, nama_cabang, "
            . " noslip, kodeid, subpost, keterangan1, real1, realisasi, jumlah, COA4, NAMA4, nodivisi, nobbm, nobbk, amount, pajak)"
            . " SELECT jns_pajak, brOtcId, tgltrans, tglbr, tglrpsby, tglreal, icabangid_o, nama_cabang, "
            . " noslip, kodeid, subpost, keterangan1, real1, jumlah as realisasi, jumlah, COA4, NAMA4, nodivisi, nobbm, nobbk, jumlah as amount, pajak "
            . " from $tmp04";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; } 
    
    
    
        
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
    $nket="* Advance";
    if ($pjenisrpt=="K") $nket="* Klaim";

    $ptglpd = "";
    if (!empty($r['tgl']) AND $r['tgl']<>"0000-00-00")
        $ptglpd =date("d-M-Y", strtotime($r['tgl']));

    $query = "select * FROM $tmp03";
    $jmlrec=mysqli_num_rows(mysqli_query($cnit, $query));
    $plimit=30;
    $pjmlfor=ceil((double)$jmlrec / (double)$plimit);

    echo "<table class='tjudul' width='100%'>";
    echo "<tr> <td width='300px' colspan='4'>Rekap Budget Request (RBR) Team OTC </td> <td> : </td> <td>$pnodivisi</td> </tr>";
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
                    <th align="center">DAERAH</th>
                    <th align="center">No Slip</th>
                    <th align="center">Pengajuan</th>
                    <th align="center">Posting</th>
                    <th align="center">Keterangan</th>
                    <th align="center">Rralisasi</th>
                    <th align="center">No. Rekening</th>
                    <th align='center'>Debit</th>
                    <th align='center'>Kredit</th>
                    <th align='center'>Saldo</th>
                    <th align="center">No</th>
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
                $pjmldebit=0;
                $pjmlkredit=0;
                $pjmlsaldo=0;
                
                $ptotdpp=0;
                $ptotppn=0;
                $ptotpph=0;
                
                $query = "select * FROM $tmp03 WHERE nodivisi='$pnodivisi' order by noslip, nama_cabang, real1, keterangan1, nodivisi, brOtcId, jns_pajak, COA4 LIMIT $pjmlsudah, $plimit";
                $tampil2=mysqli_query($cnit, $query);
                while ($row= mysqli_fetch_array($tampil2)) {
                    
                    $pnobrid = $row['brOtcId'];
                    $ppajak = $row['pajak'];
                    $pjnsdpp = $row['jenis_dpp'];
                    
                    $ptgltrans = "";
                    if (!empty($row['tgltrans']) AND $row['tgltrans']<>"0000-00-00")
                        $ptgltrans =date("d-M-Y", strtotime($row['tgltrans']));

                    $pbbk = $row['nobbk'];
                    $pcoa = $row['COA4'];
                    $pnmcoa = $row['NAMA4'];
                    $pnmcabang = $row['nama_cabang'];
                    $pnoslip = $row['noslip'];

                    $pnmposting = $row['nama_posting'];
                    $paktivitas1 = $row['keterangan1'];
                    
                    
                    $prealisasi = $row['real1'];
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

                    
                    $pstsbatal="";
                    $palasanbatal="";
                    if (isset($row['stsbatal'])) $pstsbatal = $row['stsbatal'];
                    if (isset($row['alasan_batal'])) $palasanbatal = $row['alasan_batal'];
                    

                    $stl_batal="";
                    if ($pstsbatal=="Y") {
                        $stl_batal="style='color:red;'";
                        
                        if (!empty($palasanbatal)) {
                            $paktivitas1=$paktivitas1." - (".$palasanbatal.")";
                        }
                    }
                    
                    echo "<tr $stl_batal>";
                    echo "<td nowrap>$ptgltrans</td>";
                    echo "<td nowrap>$pbbk</td>";
                    echo "<td nowrap>$pcoa</td>";
                    echo "<td nowrap>$pnmcoa</td>";
                    echo "<td nowrap>$pnmcabang</td>";
                    echo "<td nowrap>$pnoslip</td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap>$pnmposting</td>";
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
                        
                        $query_ = "select * FROM $tmp04 WHERE brOtcId='$pnobrid' order by COA4";
                        $tampil_=mysqli_query($cnit, $query_);
                        while ($rw1= mysqli_fetch_array($tampil_)) {
                            $pcoa=$rw1['COA4'];
                            $pnmcoa=$rw1['NAMA4'];
                            $pbbk = $rw1['nobbk'];

                            $ptgltrans = "";
                            if (!empty($rw1['tgltrans']) AND $rw1['tgltrans']<>"0000-00-00")
                                $ptgltrans =date("d-M-Y", strtotime($rw1['tgltrans']));

                            
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
                echo "<td align='right'><b></b></td><td align='right'><b></b></td><td align='right'><b>$pjmlsaldo</b></td>";
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
                        echo "<td align='right'><b></b></td><td align='right'><b></b></td><td align='right'><b></b></td>";
                        echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                        echo "<td>&nbsp;</td>";
                        echo "</tr>";
                        
                        echo "<tr>";
                        echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                        echo "<td> <b>Grand Total</b> </td> <td>&nbsp;</td> <td>&nbsp;</td>";
                        echo "<td align='right'><b></b></td><td align='right'><b></b></td><td align='right'><b>$totalsemuanya</b></td>";
                        echo "<td>&nbsp;</td>";
                        echo "<td align='right'><b>$totalsemuanya_dpp</b></td> <td align='right'><b>$totalsemuanya_ppn</b></td> <td align='right'><b>$totalsemuanya_pph</b></td>";
                        echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                        echo "<td>&nbsp;</td>";
                        echo "</tr>";
                    }
                }
                $nnomorjml++;
            ?>
            </tbody>
        </table>
<?PHP
        echo "<br/>&nbsp;<br/>&nbsp;";
    }
hapusdata:
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp04");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");

    mysqli_close($cnit);
?>