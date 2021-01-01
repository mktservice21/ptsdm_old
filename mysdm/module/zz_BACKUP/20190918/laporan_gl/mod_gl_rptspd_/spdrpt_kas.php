<?php

    $now=date("mdYhis");
    $tmp01 =" dbtemp.RPTREKPD01_".$_SESSION['USERID']."_$now ";
    $tmp02 =" dbtemp.RPTREKPD02_".$_SESSION['USERID']."_$now ";
    $tmp03 =" dbtemp.RPTREKPD03_".$_SESSION['USERID']."_$now ";
    
    
    
    $p_rp_pettycash_ho="30000000";
    $p_rp_pettycash_cor="5000000";

    $p_rp_pettycash=$p_rp_pettycash_ho;
    
    if ($spddivisi=="KASCOR") $p_rp_pettycash=$p_rp_pettycash_cor;
    
    $query = "select b.kodeid, d.nama kodenama, b.subkode, d.subnama, b.divisi, b.divisi divisipd,  
        b.tgl tglpd, b.tglspd, b.nomor, b.nodivisi, b.jenis_rpt, b.jumlah jumlahpd, a.urutan, a.bridinput, a.amount, 
        a.nobbm, a.nobbk, b.coa4 coa, c.NAMA4 coa_nama from dbmaster.t_suratdana_br1 a 
        JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput 
        LEFT JOIN dbmaster.coa_level4 c on b.coa4=c.COA4 
        LEFT JOIN dbmaster.t_kode_spd d on b.kodeid=d.kodeid AND b.subkode=d.subkode WHERE b.nodivisi='$spdnodivisi'";
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
        b.jenis_rpt 
        FROM $tmp02 a JOIN $tmp01 b on a.kasId=b.bridinput";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "select distinct tglpd, divisipd divisi, kodenama, nomor, nodivisi, coa, coa_nama, jumlahpd, jenis_rpt FROM $tmp03 order by tglpd, divisipd, nodivisi";
    $tampil=mysqli_query($cnit, $query);
    $ketemu=mysqli_num_rows($tampil);
    
    $sudahkas="";
    if ($ketemu>0) {

        while ($r= mysqli_fetch_array($tampil)) {
            
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

            echo "<table class='tjudul' width='100%'>";
            echo "<tr> <td width='300px'>No. </td> <td> : </td> <td>$pnodivisi</td> </tr>";
            echo "<tr> <td width='200px'>Hal. </td> <td> : </td> <td>$nket</td> </tr>";
            echo "</table>";
            echo "<br/>&nbsp;";
            
            
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
                            echo "<td nowrap>$ptgltrans</td>";
                            echo "<td nowrap>$pbbk</td>";
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


                            $no++;
                        }
                        
                        
                            
                        
                            $psldakhir=(double)$p_rp_pettycash-(double)$pjmlkredit-(double)$pjmlkasbon;
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
                            
                        for ($x = 1; $x <= 6; $x++) {
                            echo "<tr>";
                            echo "<td></td><td></td><td></td><td></td><td></td>";
                            echo "<td></td><td></td><td></td>";
                            if ($x==1) {
                                echo "<td><b>TOTAL</b></td>";
                                echo "<td><b></b></td>";
                                echo "<td><b></b></td>";
                                echo "<td><b></b></td>";
                                echo "<td nowrap align='right'><b>$pjmlkredit</b></td>";
                            }elseif ($x==2) {
                                echo "<td><b>Petty Cash</b></td>";
                                echo "<td><b></b></td>";
                                echo "<td><b></b></td>";
                                echo "<td><b></b></td>";
                                echo "<td nowrap align='right'><b>$p_rp_pettycash</b></td>";
                            }elseif ($x==3) {
                                echo "<td><b>Kas Bon terlampir</b></td>";
                                echo "<td><b></b></td>";
                                echo "<td><b></b></td>";
                                echo "<td><b></b></td>";
                                echo "<td nowrap align='right'><b>$pjmlkasbon</b></td>";
                            }elseif ($x==4) {
                                echo "<td><b>Petty Cash- belum tarik dari Bank Niaga No.</b></td>";
                                echo "<td><b></b></td>";
                                echo "<td><b></b></td>";
                                echo "<td><b></b></td>";
                                echo "<td nowrap align='right'><b></b></td>";
                            }elseif ($x==5) {
                                echo "<td><b>Petty Cash- belum tarik dari Bank Niaga No.</b></td>";
                                echo "<td><b></b></td>";
                                echo "<td><b></b></td>";
                                echo "<td><b></b></td>";
                                echo "<td nowrap align='right'><b></b></td>";
                            }elseif ($x==6) {
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
                        ?>
                    </tbody>
                </table>
                <?PHP

                echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
            
        }

    }
    
    
hapusdata:
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp03");

    mysqli_close($cnit);
?>
