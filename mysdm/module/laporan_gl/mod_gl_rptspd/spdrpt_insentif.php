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
    
$pviewdata=date("d/m/Y");
$now=date("mdYhis");
$tmp01 =" dbtemp.RPTREKPDIN01_".$_SESSION['USERID']."_$now ";
$tmp02 =" dbtemp.RPTREKPDIN02_".$_SESSION['USERID']."_$now ";
$tmp03 =" dbtemp.RPTREKPDIN03_".$_SESSION['USERID']."_$now ";

$mpilihdivisi="ETHICAL";
        

        $filter_pilih=" AND nodivisi='$spdnodivisi' ";
        if (!empty($spdidinput)) {
            $filter_pilih=" AND idinput='$spdidinput' ";
        }
        
    $query = "SELECT idinput, nodivisi, tgl, jumlah, tglf, tglspd from dbmaster.t_suratdana_br WHERE stsnonaktif<>'Y' $filter_pilih ";
    $tampil= mysqli_query($cnit, $query);
    $nx= mysqli_fetch_array($tampil);
    $tglinc=$nx['tglf'];
    $tglaju=$nx['tgl'];
    $tglspd=$nx['tglspd'];
    $periode1= date("Ym", strtotime($tglinc));
    $pbulan= date("F Y", strtotime($tglinc));

    $ntglajukan=$tglaju;
    if (!empty($tglspd) AND $tglspd<>"0000-00-00") $ntglajukan=$tglspd;
    $ntglajukan= date("d F Y", strtotime($ntglajukan));


        

    echo "<table class='tjudul' width='100%'>";
    echo "<tr> <td width='300px' colspan='4'>Rekap Insentif $mpilihdivisi </td> <td> : </td> <td>$spdnodivisi</td> </tr>";
    echo "<tr> <td width='200px' colspan='4'>Periode </td> <td> : </td> <td>$pbulan</td> </tr>";
    echo "<tr> <td width='200px' colspan='4'>Pengajuan </td> <td> : </td> <td>$ntglajukan</td> </tr>";
    echo "<tr> <td width='200px' colspan='4'>View Data </td> <td> : </td> <td>$pviewdata</td> </tr>";
    echo "</table>";
    
    echo "<br/>&nbsp;";
    
    $query = "SELECT CAST(null as DECIMAL(10,0)) as urutan, a.bulan, a.divisi, a.cabang icabangid, b.nama cabang, "
            . " a.jabatan, a.karyawanid, a.nama, a.region, a.jumlah FROM dbmaster.incentiveperdivisi a "
            . " LEFT JOIN mkt.icabang b on a.cabang=b.iCabangId WHERE DATE_FORMAT(a.bulan,'%Y%m')='$periode1'";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    $query="DELETE FROM $tmp01 WHERE IFNULL(jumlah,0)=0";
    mysqli_query($cnmy, $query);
        
    $query="UPDATE $tmp01 SET urutan=1 WHERE jabatan='MR'";
    mysqli_query($cnmy, $query);
    $query="UPDATE $tmp01 SET urutan=2 WHERE jabatan='AM'";
    mysqli_query($cnmy, $query);
    $query="UPDATE $tmp01 SET urutan=3 WHERE jabatan='DM'";
    mysqli_query($cnmy, $query);
                
    $query="Alter table $tmp01 ADD COLUMN coa CHAR(50), ADD COLUMN nama_coa CHAR(100)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query ="UPDATE $tmp01 SET coa='705-05' WHERE divisi='CAN'";//, nama_coa='P1-DIN-INSENTIVE CANARY'
    mysqli_query($cnmy, $query);
    
    $query ="UPDATE $tmp01 SET coa='701-05' WHERE divisi='EAGLE'";//, nama_coa='P1-DIN-INSENTIVE EAGLE'
    mysqli_query($cnmy, $query);
    
    $query ="UPDATE $tmp01 SET coa='702-05' WHERE divisi='PIGEO'";//, nama_coa='P2-DIN-INSENTIVE PIGEON'
    mysqli_query($cnmy, $query);
    
    $query ="UPDATE $tmp01 SET coa='703-05' WHERE divisi='PEACO'";//, nama_coa='P3-DIN-INSENTIF PEACOCK'
    mysqli_query($cnmy, $query);
    
    $query ="UPDATE $tmp01 a SET a.nama_coa=(select NAMA4 FROM dbmaster.coa_level4 b WHERE a.coa=b.COA4) WHERE IFNULL(divisi,'')<>''";
    mysqli_query($cnmy, $query);
    
    
    
    $query_="select nobukti, tanggal from dbmaster.t_suratdana_bank WHERE nodivisi='$spdnodivisi' AND stsinput='K' and stsnonaktif<>'Y'";
    $tampilbukti= mysqli_query($cnmy, $query_);
    $nb= mysqli_fetch_array($tampilbukti);
    $pnobukti=$nb['nobukti'];
    $ptgltransbank=$nb['tanggal'];
    $ntransbnk="";
    if (!empty($ptgltransbank) AND $ptgltransbank<>"0000-00-00")
        $ntransbnk =date("d-M-Y", strtotime($ptgltransbank));
            
            
            
    $query = "select * FROM $tmp01";
    $jmlrec=mysqli_num_rows(mysqli_query($cnit, $query));
    $plimit=30;
    $pjmlfor=ceil((double)$jmlrec / (double)$plimit);
    
    
    $nnomorjml=1;
    $pjmlsudah=0;
    $totalsemuanya=0;

?>

<?PHP
for($ijml=1;$ijml<=$pjmlfor;$ijml++) {
?>
    <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
        <thead>
            <tr style='background-color:#cccccc; font-size: 13px;'>
                <th align="center">Date</th>
                <th align="center">Bukti</th>
                <th align="center">KODE</th>
                <th align="center">PERKIRAAN</th>
                <th align="center">PENGAJUAN</th>
                <th align="center">JABATAN</th>
                <th align="center">CABANG</th>
                <th align="center">REGION</th>
                <th align='center'>Debit</th>
                <th align='center'>Kredit</th>
                <th align='center'>Saldo</th>
                <th align="center">No.</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
                $no=1;
                $pjmlkredit=0;
                
                $query = "select * FROM $tmp01 order by divisi, nama, karyawanid, coa LIMIT $pjmlsudah, $plimit";
                $tampil2=mysqli_query($cnit, $query);
                while ($row= mysqli_fetch_array($tampil2)) {
                    
                    $pcoa = $row['coa'];
                    $pnmcoa = $row['nama_coa'];
                    
                    $ppengajuan = $row['nama'];
                    $pcabang = $row['cabang'];
                    if ($pcabang=="ETH - HO") $pcabang = "HO";
                    
                    $pjabatan = $row['jabatan'];
                    $pregion = $row['region'];
                    $pkredit = $row['jumlah'];
                    
                    $pdebit="";
                    $psaldo="";
                    
                    $pjmlkredit=$pjmlkredit+$pkredit;
                    $pkredit=number_format($pkredit,0,",",",");
                    
                    echo "<tr>";
                    echo "<td nowrap>$ntransbnk</td>";
                    echo "<td nowrap>$pnobukti</td>";
                    echo "<td nowrap>$pcoa</td>";
                    echo "<td nowrap>$pnmcoa</td>";
                    echo "<td nowrap>$ppengajuan</td>";
                    echo "<td nowrap>$pjabatan</td>";
                    echo "<td nowrap>$pcabang</td>";
                    echo "<td nowrap>$pregion</td>";
                    echo "<td nowrap align='right'>$pdebit</td>";
                    echo "<td nowrap align='right'>$pkredit</td>";
                    echo "<td nowrap align='right'>$psaldo</td>";
                    echo "<td nowrap align='center'>$no</td>";
                    echo "</tr>";
                    
                    
                    $no++;
                    
                    $pjmlsudah++;
                    
                }
                
                $totalsemuanya=$totalsemuanya+$pjmlkredit;
                $pjmlkredit=number_format($pjmlkredit,0,",",",");
                
                echo "<tr>";
                echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                echo "<td> <b>Total</b> </td> <td>&nbsp;</td> <td>&nbsp;</td>";
                echo "<td align='right'><b></b></td> <td align='right'><b>$pjmlkredit</b></td> <td align='right'><b></b></td>";
                echo "<td>&nbsp;</td>";
                echo "</tr>";
                
                if ($pjmlfor==$nnomorjml) {
                    
                    $totalsemuanya=number_format($totalsemuanya,0,",",",");
                    
                    echo "<tr>";
                    echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                    echo "<td> <b></b> </td> <td>&nbsp;</td> <td>&nbsp;</td>";
                    echo "<td align='right'><b></b></td> <td align='right'><b></b></td> <td align='right'><b></b></td>";
                    echo "<td>&nbsp;</td>";
                    echo "</tr>";
                    
                    echo "<tr>";
                    echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                    echo "<td> <b>Grand Total</b> </td> <td>&nbsp;</td> <td>&nbsp;</td>";
                    echo "<td align='right'><b></b></td> <td align='right'><b>$totalsemuanya</b></td> <td align='right'><b></b></td>";
                    echo "<td>&nbsp;</td>";
                    echo "</tr>";
                
                }
                $nnomorjml++
                
            ?>
        </tbody>
    </table>
<?PHP
    echo "<br/>&nbsp;<br/>&nbsp;";
}

?>

<?PHP
hapusdata:
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp03");
    mysqli_close($cnit);
?>