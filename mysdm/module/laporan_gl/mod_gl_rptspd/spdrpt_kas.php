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
    $tmp05 =" dbtemp.RPTREKPD05_".$_SESSION['USERID']."_$now ";
    $tmp06 =" dbtemp.RPTREKPD06_".$_SESSION['USERID']."_$now ";
    
    
    $pnobukti="";
    $ntransbnk="";
    
    $p_rp_pettycash_ho="30000000";
    $p_rp_pettycash_cor="5000000";

    $p_rp_pettycash=$p_rp_pettycash_ho;
    
    if ($spddivisi=="KASCOR") $p_rp_pettycash=$p_rp_pettycash_cor;
    
        $filter_pilih=" AND b.nodivisi='$spdnodivisi' ";
        if (!empty($spdidinput)) {
            $filter_pilih=" AND b.idinput='$spdidinput' ";
        }
        
        
    $query = "select b.kodeid, d.nama kodenama, b.subkode, d.subnama, b.divisi, b.divisi divisipd,  
        b.tgl tglpd, b.tglspd, b.nomor, b.nodivisi, b.jenis_rpt, b.jumlah jumlahpd, jumlah2 jml_kasbon, a.urutan, a.bridinput, a.amount, 
        a.nobbm, a.nobbk, b.coa4 coa, c.NAMA4 coa_nama from dbmaster.t_suratdana_br1 a 
        JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput 
        LEFT JOIN dbmaster.coa_level4 c on b.coa4=c.COA4 
        LEFT JOIN dbmaster.t_kode_spd d on b.kodeid=d.kodeid AND b.subkode=d.subkode WHERE 1=1 $filter_pilih ";//b.nodivisi='$spdnodivisi'
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    
    $query = "select a.kasId, a.periode1, a.kode, b.COA4, c.NAMA4, a.periode2, a.karyawanid, a.nama, d.nama nama_karyawan,
        a.aktivitas1, a.aktivitas2, a.jumlah 
        from hrd.kas a LEFT JOIN dbmaster.posting_coa_kas b on a.kode=b.kodeid 
        LEFT JOIN dbmaster.coa_level4 c on b.COA4=c.COA4
        LEFT JOIN hrd.karyawan d on a.karyawanid=d.karyawanId 
        WHERE 1=1 ";
    $query .= " AND a.kasId IN (select IFNULL(bridinput,'') from $tmp01)";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "SELECT a.*, b.divisipd, b.kodenama, b.tglpd, b.nomor, b.nodivisi, 
        b.nobbm, b.nobbk, b.urutan, b.amount, b.coa, b.coa_nama, b.jumlahpd,
        b.jenis_rpt, b.jml_kasbon, b.kodeid, b.subkode, CAST(0 as DECIMAL(20,2)) as kuranglebihrp, 
                CAST('' as CHAR(100)) as ketkurleb, CAST('' as CHAR(1)) as npilih   
        FROM $tmp02 a JOIN $tmp01 b on a.kasId=b.bridinput";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    
        $padakuranglebihrp=false;
        
            $query = "select * from hrd.kas_kuranglebih where idinput='$spdidinput'";
            $query = "create TEMPORARY table $tmp05 ($query)"; 
            mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "UPDATE $tmp03 a JOIN (select idinput, kasid, ket, sum(kuranglebihrp) as kuranglebihrp FROM $tmp05 GROUP BY 1,2,3) b on "
                    . " a.kasid=b.kasid SET a.kuranglebihrp=b.kuranglebihrp, a.ketkurleb=b.ket";
            mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            $query = "select sum(kuranglebihrp) as kuranglebihrp FROM $tmp03";
            $tampiljkur= mysqli_query($cnit, $query);
            $nrs= mysqli_fetch_array($tampiljkur);
            $ptotalkuranglbhrp=$nrs['kuranglebihrp'];
            if (empty($ptotalkuranglbhrp)) $ptotalkuranglbhrp=0;
            
            if ((DOUBLE)$ptotalkuranglbhrp<>0) $padakuranglebihrp=true;
            
            if ($padakuranglebihrp==true) {
                $query = "select a.kasid, b.periode1, b.kode, b.coa4, b.nama4, b.periode2, b.karyawanid, b.nama, "
                        . " b.nama_karyawan, b.aktivitas1, b.aktivitas2, '0' as jumlah, b.tglf, b.tglt, b.divisipd, "
                        . " b.kodenama, b.tglpd, b.nomor, b.nodivisi, b.nobbm, b.nobbk, b.urutan, a.kuranglebihrp as amount, b.coa, b.coa_nama, "
                        . " b.jumlahpd, b.jenis_rpt, b.kodeid, b.subkode, b.jml_kasbon, a.kuranglebihrp, '2' as npilih from "
                        . " $tmp05 a LEFT JOIN $tmp03 b on a.kasid=b.kasid WHERE "
                        . " IFNULL(a.kuranglebihrp,0)<>0";
                //$query = "create TEMPORARY table $tmp06 ($query)"; 
                //mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                
                $query = "INSERT INTO $tmp03 (kasid, periode1, kode, coa4, nama4, periode2, karyawanid, nama, "
                        . " nama_karyawan, aktivitas1, aktivitas2, jumlah, tglf, tglt, divisipd, "
                        . " kodenama, tglpd, nomor, nodivisi, nobbm, nobbk, urutan, amount, coa, coa_nama, "
                        . " jumlahpd, jenis_rpt, kodeid, subkode, jml_kasbon, kuranglebihrp, npilih)"
                        . " SELECT kasid, periode1, kode, coa4, nama4, periode2, karyawanid, nama, "
                        . " nama_karyawan, aktivitas1, aktivitas2, jumlah, tglf, tglt, divisipd, "
                        . " kodenama, tglpd, nomor, nodivisi, nobbm, nobbk, urutan, amount, coa, coa_nama, "
                        . " jumlahpd, jenis_rpt, kodeid, subkode, jml_kasbon, kuranglebihrp, npilih FROM $tmp06"; 
                //mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            }
    
    
    
    $query = "select distinct kodeid, subkode, tglpd, divisipd divisi, kodenama, nomor, nodivisi, coa, coa_nama, jumlahpd, jenis_rpt, jml_kasbon FROM $tmp03 order by tglpd, divisipd, nodivisi";
    $tampil=mysqli_query($cnit, $query);
    $ketemu=mysqli_num_rows($tampil);
    
    $sudahkas="";
    if ($ketemu>0) {

        while ($r= mysqli_fetch_array($tampil)) {
            
            $pkode_id=$r['kodeid'];
            $psubkode_id=$r['subkode'];
                
            $mthan =date("Y", strtotime($r['tglpd']));
            $pjmlkasbon=0;
            if (empty($sudahkas) AND $spddivisi=="KAS") {
                $query = "select sum(jumlah) jmlkasbon from dbmaster.t_kasbon where IFNULL(stsnonaktif,'')<>'Y' AND YEAR(tgl)='$mthan'";
                $tampilb=mysqli_query($cnit, $query);
                $ketemub=mysqli_num_rows($tampilb);
                if ($ketemub>0) {
                    $ks= mysqli_fetch_array($tampilb);
                    $pjmlkasbon=$ks['jmlkasbon'];
                    if (empty($pjmlkasbon)) $pjmlkasbon=0;
                    
                    $sudahkas="sudah";
                }
                
                $pjmlkasbon=$r['jml_kasbon'];
                
                $p_rp_pettycash=$p_rp_pettycash_ho;
                if ($pkode_id=="2" AND $psubkode_id=="23") {
                    $p_rp_pettycash=$p_rp_pettycash_cor;
                    $pjmlkasbon=0;
                }
                
            }
            
            
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
            $nket="Laporan Kas Kecil";

            $ptglpd = "";
            if (!empty($r['tglpd']) AND $r['tglpd']<>"0000-00-00")
                $ptglpd =date("d-M-Y", strtotime($r['tglpd']));

            
            $query_="select nobukti, tanggal from dbmaster.t_suratdana_bank WHERE nodivisi='$pnodivisi' AND stsinput='K' and stsnonaktif<>'Y'";
            $tampilbukti= mysqli_query($cnmy, $query_);
            $nb= mysqli_fetch_array($tampilbukti);
            $pnobukti=$nb['nobukti'];
            $ptgltransbank=$nb['tanggal'];
            $ntransbnk="";
            if (!empty($ptgltransbank) AND $ptgltransbank<>"0000-00-00")
                $ntransbnk =date("d-M-Y", strtotime($ptgltransbank));
            
            echo "<table class='tjudul' width='100%'>";
            echo "<tr> <td width='300px'>No. </td> <td> : </td> <td>$pnodivisi</td> </tr>";
            echo "<tr> <td width='200px'>Hal. </td> <td> : </td> <td>$nket</td> </tr>";
            echo "</table>";
            echo "<br/>&nbsp;";
            
        }
        
        $psubkas=0;
        $psubkasbon=0;
        $psublimitpcm=0;
        
                ?>
                <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
                    <thead>
                        <tr style='background-color:#cccccc; font-size: 13px;'>
                        <th align="center">TGL.</th>
                        <th align="center">Bukti</th>
                        <th align="center" colspan="3">COA</th>
                        <th align="center">DATE TRC</th>
                        <th align="center">Pengajuan</th>
                        <th align="center">Jenis</th>
                        <th align="center" colspan="3">DESCRIPTION</th>
                        <th align="center">Debit</th>
                        <th align="center">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?PHP
                        $no=1;
                        $nurut=1;
                        $pjmldebit=0;
                        $pjmlkredit=0;
                        $pjmlsaldo=0;
                        $query = "select * FROM $tmp03 WHERE nodivisi='$pnodivisi' order by nodivisi, COA4";
                        $tampil2=mysqli_query($cnit, $query);
                        while ($row= mysqli_fetch_array($tampil2)) {
                            $ptgltrans = "";
                            if (!empty($row['periode1']) AND $row['periode1']<>"0000-00-00")
                                $ptgltrans =date("d-M-Y", strtotime($row['periode1']));
                            
                            $ptgltrc = "";
                            if (!empty($row['periode2']) AND $row['periode2']<>"0000-00-00")
                                $ptgltrc =date("d-M-Y", strtotime($row['periode2']));

                            $pbbk = $row['nobbk'];
                            $pcoa = $row['COA4'];
                            $pnmcoa = $row['NAMA4'];

                            $pnama = $row['nama'];
                            $ppengajuan = $row['nama_karyawan'];
                            
                            $paktivitas1 = $row['aktivitas1'];
                            
                            $pkredit=$row['jumlah'];
                            
                            $pjmlkredit=$pjmlkredit+$pkredit;
                            $pkredit=number_format($pkredit,0,",",",");
                            


                            echo "<tr>";
                            echo "<td nowrap>$ntransbnk</td>";
                            echo "<td nowrap>$pnobukti</td>";
                            echo "<td nowrap>$pcoa</td>";
                            echo "<td nowrap>$pnmcoa</td>";
                            echo "<td nowrap></td>";
                            
                            echo "<td nowrap></td>";
                            
                            echo "<td nowrap>$pnama</td>";
                            echo "<td nowrap></td>";
                            
                            echo "<td nowrap>$paktivitas1</td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            
                            echo "<td nowrap align='right'></td>";
                            echo "<td nowrap align='right'>$pkredit</td>";
                            echo "</tr>";
                            
                            if ((double)$nurut==30) {
                                $nurut=1;
                                echo "<tr>";
                                echo "<td colspan=13></td>";
                                echo "</tr>";
                                echo "
                                <thead>
                                    <tr style='background-color:#cccccc; font-size: 13px;'>
                                    <th align='center'>TGL.</th>
                                    <th align='center'>Bukti</th>
                                    <th align='center' colspan='3'>COA</th>
                                    <th align='center'>DATE TRC</th>
                                    <th align='center'>Pengajuan</th>
                                    <th align='center'>Jenis</th>
                                    <th align='center' colspan='3'>DESCRIPTION</th>
                                    <th align='center'>Debit</th>
                                    <th align='center'>Kredit</th>
                                    </tr>
                                </thead>
                                ";
                            }

                            $no++;
                            $nurut++;
                        }
                        
                        
                            
                            $psubkas=$pjmlkredit;
                            $psubkasbon=$pjmlkasbon;
                            $psublimitpcm=$p_rp_pettycash;
                        
                        
                            $psldakhir=(double)$p_rp_pettycash-((double)$pjmlkredit+(double)$pjmlkasbon);
                            $pjmlkredit=number_format($pjmlkredit,0,",",",");
                            $p_rp_pettycash=number_format($p_rp_pettycash,0,",",",");
                            $pjmlkasbon=number_format($pjmlkasbon,0,",",",");
                            $psldakhir=number_format($psldakhir,0,",",",");
                        
                            
                            $pjmldebit=$pjumlahpd;
                            $pjumlahpd=number_format($pjumlahpd,0,",",",");
                            $pjmldebit=number_format($pjmldebit,0,",",",");
                        
                        
                        
                        
                        
                        echo "<tr>";
                        echo "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td><b></b></td>";
                        echo "<td></td> <td></td><td nowrap align='right'></td><td nowrap align='right'><b></b></td>";
                        echo "</tr>";
                            
                        for ($x = 1; $x <= 7; $x++) {
                            echo "<tr>";
                            echo "<td></td><td></td><td></td><td></td><td></td>";
                            echo "<td></td><td></td><td></td>";
                            if ($x==1) {
                                echo "<td><b>Jumlah</b></td>";
                                echo "<td><b></b></td>";
                                echo "<td><b></b></td>";
                                echo "<td><b></b></td>";
                                echo "<td nowrap align='right'><b>$pjmlkredit</b></td>";
                            }elseif ($x==2) {
                                echo "<td><b>Kas Bon terlampir</b></td>";
                                echo "<td><b></b></td>";
                                echo "<td><b></b></td>";
                                echo "<td><b></b></td>";
                                echo "<td nowrap align='right'><b>$pjmlkasbon</b></td>";
                            }elseif ($x==3) {
                                
                                $ptotalkas_kasbon=(double)$psubkas+(double)$psubkasbon;
                                $ptotalkas_kasbon=number_format($ptotalkas_kasbon,0,",",",");
                                echo "<td><b>TOTAL</b></td>";
                                echo "<td><b></b></td>";
                                echo "<td><b></b></td>";
                                echo "<td><b></b></td>";
                                echo "<td nowrap align='right'><b>$ptotalkas_kasbon</b></td>";
                            }elseif ($x==4) {
                                echo "<td><b>Limit Petty Cash</b></td>";
                                echo "<td><b></b></td>";
                                echo "<td><b></b></td>";
                                echo "<td><b></b></td>";
                                echo "<td nowrap align='right'><b>$p_rp_pettycash</b></td>";
                            }elseif ($x==5) {
                                echo "<td><b>Petty Cash- belum tarik dari Bank Niaga No.</b></td>";
                                echo "<td><b></b></td>";
                                echo "<td><b></b></td>";
                                echo "<td><b></b></td>";
                                echo "<td nowrap align='right'><b></b></td>";
                            }elseif ($x==6) {
                                echo "<td><b>Petty Cash- belum tarik dari Bank Niaga No.</b></td>";
                                echo "<td><b></b></td>";
                                echo "<td><b></b></td>";
                                echo "<td><b></b></td>";
                                echo "<td nowrap align='right'><b></b></td>";
                            }elseif ($x==7) {
                                echo "<td><b>Saldo Akhir</b></td>";
                                echo "<td><b></b></td>";
                                echo "<td><b></b></td>";
                                echo "<td><b></b></td>";
                                echo "<td nowrap align='right'><b>$psldakhir</b></td>";
                            }else{
                                echo "<td><b></b></td>";
                                echo "<td><b></b></td>";
                                echo "<td><b></b></td>";
                                echo "<td><b></b></td>";
                                echo "<td nowrap align='right'><b></b></td>";
                            }
                            
                            echo "</tr>";
                            
                        }


                            
                        /*
                        echo "<tr>";
                        echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                        echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                        echo "</tr>";
                        */
                        
                        
                        if ($padakuranglebihrp==true) {
                            
                            echo "<tr>";
                            echo "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td><b></b></td>";
                            echo "<td></td> <td></td><td nowrap align='right'></td><td nowrap align='right'><b></b></td>";
                            echo "</tr>";
                            
                            echo "<tr>";
                            echo "<td></td><td><b>Adjustment SPD<b></td><td></td><td></td><td></td><td></td><td></td><td></td><td><b></b></td>";
                            echo "<td></td> <td></td><td nowrap align='right'></td><td nowrap align='right'><b></b></td>";
                            echo "</tr>";
                            
                            $no=1;
                            $pjmlkredit=0;
                            
                            $query = "select * FROM $tmp03 WHERE IFNULL(kuranglebihrp,0)<>0 AND nodivisi='$pnodivisi' order by nodivisi, COA4";
                            $tampilk=mysqli_query($cnit, $query);
                            while ($rok= mysqli_fetch_array($tampilk)) {
                                $ptgltrans = "";
                                if (!empty($rok['periode1']) AND $rok['periode1']<>"0000-00-00")
                                    $ptgltrans =date("d-M-Y", strtotime($rok['periode1']));

                                $ptgltrc = "";
                                if (!empty($rok['periode2']) AND $rok['periode2']<>"0000-00-00")
                                    $ptgltrc =date("d-M-Y", strtotime($rok['periode2']));

                                $pbbk = $rok['nobbk'];
                                $pcoa = $rok['COA4'];
                                $pnmcoa = $rok['NAMA4'];

                                $pnama = $rok['nama'];
                                $ppengajuan = $rok['nama_karyawan'];

                                $paktivitas1 = $rok['aktivitas1'];

                                $pkredit=$rok['kuranglebihrp'];

                                $pjmlkredit=$pjmlkredit+$pkredit;
                                $pkredit=number_format($pkredit,0,",",",");
                                
                                
                                echo "<tr style='color:red;'>";
                                echo "<td nowrap>$ntransbnk</td>";
                                echo "<td nowrap>$pnobukti</td>";
                                echo "<td nowrap>$pcoa</td>";
                                echo "<td nowrap>$pnmcoa</td>";
                                echo "<td nowrap></td>";

                                echo "<td nowrap></td>";

                                echo "<td nowrap>$pnama</td>";
                                echo "<td nowrap></td>";

                                echo "<td nowrap>$paktivitas1</td>";
                                echo "<td nowrap></td>";
                                echo "<td nowrap></td>";

                                echo "<td nowrap align='right'></td>";
                                echo "<td nowrap align='right'>$pkredit</td>";
                                echo "</tr>";
                                
                                
                            }
                            
                            echo "<tr>";
                            echo "<td><b>Total Setelah</b></td><td><b>Adjustment SPD</b></td><td></td><td></td><td></td><td></td><td></td><td></td><td><b></b></td>";
                            echo "<td></td> <td></td><td nowrap align='right'></td><td nowrap align='right'><b></b></td>";
                            echo "</tr>";
                            
                            $pjmlkredit=(double)$psubkas+(double)$pjmlkredit;
                            $psubkas=$pjmlkredit;
                            $psldakhir=(double)$psublimitpcm-((double)$pjmlkredit+(double)$psubkasbon);
                            
                            $pjmlkredit=number_format($pjmlkredit,0,",",",");
                            $pjmlkasbon=number_format($psubkasbon,0,",",",");
                            $psldakhir=number_format($psldakhir,0,",",",");
                            
                            for ($x = 1; $x <= 4; $x++) {
                                echo "<tr>";
                                echo "<td></td><td></td><td></td><td></td><td></td>";
                                echo "<td></td><td></td><td></td>";
                                if ($x==1) {
                                    echo "<td><b>Jumlah</b></td>";
                                    echo "<td><b></b></td>";
                                    echo "<td><b></b></td>";
                                    echo "<td><b></b></td>";
                                    echo "<td nowrap align='right'><b>$pjmlkredit</b></td>";
                                }elseif ($x==2) {
                                    echo "<td><b>Kas Bon terlampir</b></td>";
                                    echo "<td><b></b></td>";
                                    echo "<td><b></b></td>";
                                    echo "<td><b></b></td>";
                                    echo "<td nowrap align='right'><b>$pjmlkasbon</b></td>";
                                }elseif ($x==3) {

                                    $ptotalkas_kasbon=(double)$psubkas+(double)$psubkasbon;
                                    $ptotalkas_kasbon=number_format($ptotalkas_kasbon,0,",",",");
                                    echo "<td><b>TOTAL</b></td>";
                                    echo "<td><b></b></td>";
                                    echo "<td><b></b></td>";
                                    echo "<td><b></b></td>";
                                    echo "<td nowrap align='right'><b>$ptotalkas_kasbon</b></td>";
                                }elseif ($x==4) {
                                    echo "<td><b>Saldo Akhir</b></td>";
                                    echo "<td><b></b></td>";
                                    echo "<td><b></b></td>";
                                    echo "<td><b></b></td>";
                                    echo "<td nowrap align='right'><b>$psldakhir</b></td>";
                                }else{
                                    echo "<td><b></b></td>";
                                    echo "<td><b></b></td>";
                                    echo "<td><b></b></td>";
                                    echo "<td><b></b></td>";
                                    echo "<td nowrap align='right'><b></b></td>";
                                }

                                echo "</tr>";

                            }
                            
                        }
                        
                        
                        
                        ?>
                    </tbody>
                </table>
                <?PHP

                echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
            

    }
    
    
hapusdata:
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp06");

    mysqli_close($cnit);
?>
