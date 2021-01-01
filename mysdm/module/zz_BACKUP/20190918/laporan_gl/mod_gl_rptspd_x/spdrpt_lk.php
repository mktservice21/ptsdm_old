<?php
    if ($_GET['ket']=="bukan") {
        echo "<a class='btn btn-success' href='eksekusi3.php?module=glreportspddetail&act=input&idmenu=$_GET[idmenu]&ket=excel&divisi=$_GET[divisi]&nodivisi=$_GET[nodivisi]' "
                . " target='_blank'>EXCEL</a><br/>&nbsp;";
    }
    
$now=date("mdYhis");
$tmp01 =" dbtemp.RPTREKPD01_".$_SESSION['USERID']."_$now ";
$tmp02 =" dbtemp.RPTREKPD02_".$_SESSION['USERID']."_$now ";
$tmp03 =" dbtemp.RPTREKPD03_".$_SESSION['USERID']."_$now ";
$tmp04 =" dbtemp.RPTREKPD04_".$_SESSION['USERID']."_$now ";
$tmp05 =" dbtemp.RPTREKPD05_".$_SESSION['USERID']."_$now ";
$tmp06 =" dbtemp.RPTREKPD06_".$_SESSION['USERID']."_$now ";

$mpilihdivisi="ETHICAL";
if ($spddivisi=="OTC") $mpilihdivisi="OTC";


$query = "select b.kodeid, d.nama kodenama, b.subkode, d.subnama, b.divisi, 
    b.tgl, b.tglspd, b.nomor, b.nodivisi, b.jenis_rpt, b.jumlah jumlahpd, a.urutan, a.bridinput, a.amount, 
    a.nobbm, a.nobbk, b.coa4 coa, c.NAMA4 coa_nama from dbmaster.t_suratdana_br1 a 
    JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput 
    LEFT JOIN dbmaster.coa_level4 c on b.coa4=c.COA4 
    LEFT JOIN dbmaster.t_kode_spd d on b.kodeid=d.kodeid AND b.subkode=d.subkode WHERE b.nodivisi='$spdnodivisi'";
$query = "create TEMPORARY table $tmp01 ($query)"; 
mysqli_query($cnit, $query);
$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$filperiode="";
if ($spdnodivisi=="010/LK/VI/19") {
    $filperiode=" AND DATE_FORMAT(bulan,'%Y%m')='201905' ";
}elseif ($spdnodivisi=="008/LK/IV/19") {
    $filperiode=" AND DATE_FORMAT(bulan,'%Y%m')='201904' ";
}

$ntabel1 ="dbmaster.t_brrutin_ca_close";
if ($spddivisi=="OTC") $ntabel1 ="dbmaster.t_brrutin_ca_close_otc";

$query = "select divisi, idrutin, karyawanid, idca1, idca2, credit, saldo, "
        . " ca1, selisih, ca2, jmltrans, nobukti, tgltrans from $ntabel1 WHERE "
        . " idrutin IN (select distinct IFNULL(bridinput,'') FROM $tmp01) $filperiode";
$query = "create TEMPORARY table $tmp02 ($query)"; 
mysqli_query($cnit, $query);
$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query="select * from dbmaster.t_brrutin1 where idrutin in (select distinct IFNULL(bridinput,'') FROM $tmp01)";
$query = "create TEMPORARY table $tmp03 ($query)"; 
mysqli_query($cnit, $query);
$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query="select * from dbmaster.t_brrutin0 where idrutin in (select distinct IFNULL(bridinput,'') FROM $tmp01)";
$query = "create TEMPORARY table $tmp04 ($query)"; 
mysqli_query($cnit, $query);
$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "SELECT a.idrutin, d.divisi, d.karyawanid, e.nama nama_kry, d.icabangid, 
    f.nama nama_cabang, d.areaid, g.nama nama_area, a.nobrid, b.nama namaid, 
    a.coa, c.NAMA4 nama_coa, a.tgl1, a.tgl2, a.qty, a.rp, a.rptotal 
    FROM $tmp03 a 
    JOIN dbmaster.t_brid b on a.nobrid=b.nobrid
    JOIN dbmaster.coa_level4 c on a.coa=c.COA4
    JOIN $tmp04 d on a.idrutin=d.idrutin
    JOIN hrd.karyawan e on d.karyawanid=e.karyawanId
    LEFT JOIN MKT.icabang f on d.icabangid=f.iCabangId
    LEFT JOIN MKT.iarea g on d.icabangid=g.iCabangId AND d.areaid=g.areaId ";
$query = "create TEMPORARY table $tmp05 ($query)"; 
mysqli_query($cnit, $query);
$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "SELECT a.divisi, a.karyawanid, a.nama_kry, a.icabangid, a.nama_cabang, a.nobrid, a.namaid, a.coa, a.nama_coa, "
        . " b.nobbm, b.nobbk, c.nobukti, c.tgltrans, sum(a.rptotal) as rptotal FROM "
        . " $tmp05 a JOIN $tmp01 b ON a.idrutin=b.bridinput "
        . " JOIN $tmp02 c ON a.idrutin=c.idrutin "
        . " GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13";
$query = "create TEMPORARY table $tmp06 ($query)"; 
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
            $ptglpd =date("F Y", strtotime($r['tgl']));
        
        $query = "select * FROM $tmp06";
        $jmlrec=mysqli_num_rows(mysqli_query($cnit, $query));
        $plimit=30;
        $pjmlfor=ceil((double)$jmlrec / (double)$plimit);
        
        echo "<table class='tjudul' width='100%'>";
        echo "<tr> <td width='300px' colspan='4'>Rekap Biaya Luar Kota Team $mpilihdivisi </td> <td> : </td> <td>$pnodivisi</td> </tr>";
        echo "<tr> <td width='200px' colspan='4'>$nket </td> <td> : </td> <td>$ptglpd</td> </tr>";
        echo "</table>";
        
        echo "<br/>&nbsp;";
        
    $nnomorjml=1;
    $pjmlsudah=0;
    $totalsemuanya=0;
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
                    <th align="center">DAERAH</th>
                    <th align="center">KETERANGAN</th>
                    <th align="center"></th>
                    <th align='center'>Debit</th>
                    <th align='center'>Kredit</th>
                    <th align='center'>Saldo</th>
                    <th align="center">No.</th>
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
                
                $query = "select * FROM $tmp06 order by nama_kry, coa LIMIT $pjmlsudah, $plimit";
                $tampil2=mysqli_query($cnit, $query);
                while ($row= mysqli_fetch_array($tampil2)) {
                    $ptgltrans = "";
                    if (!empty($row['tgltrans']) AND $row['tgltrans']<>"0000-00-00")
                        $ptgltrans =date("d-M-Y", strtotime($row['tgltrans']));

                    $pbbk = $row['nobukti'];
                    $pcoa = $row['coa'];
                    $pnmcoa = $row['nama_coa'];

                    $ppengajuan = $row['nama_kry'];
                    $pcabang = $row['nama_cabang'];
                    if ($pcabang=="ETH - HO") $pcabang = "HO";

                    $paktivitas1 = $row['namaid'];
                    $pnorek = "";



                    $pdebit = "";
                    $pkredit = $row['rptotal'];
                    $psaldo = "";

                    $njumlah=0;
                    $nrealisasi=0;

                    $pjmlkredit=$pjmlkredit+$pkredit;
                    $pkredit=number_format($pkredit,0,",",",");
                    
                    
                    echo "<tr>";
                    echo "<td nowrap>$ptgltrans</td>";
                    echo "<td nowrap>$pbbk</td>";
                    echo "<td nowrap>$pcoa</td>";
                    echo "<td nowrap>$pnmcoa</td>";
                    echo "<td nowrap>$ppengajuan</td>";
                    echo "<td nowrap>$pcabang</td>";
                    echo "<td nowrap>$paktivitas1</td>";
                    echo "<td nowrap>$pnorek</td>";
                    echo "<td nowrap align='right'>$pdebit</td>";
                    echo "<td nowrap align='right'>$pkredit</td>";
                    echo "<td nowrap align='right'>$psaldo</td>";
                    echo "<td nowrap align='center'>$no</td>";
                    echo "</tr>";

                    $no++;
                    
                    $pjmlsudah++;
                }
                
                $pjmlsaldo=$pjumlahpd-$pjmlkredit;

                $ngtotald=$ngtotald+$pjumlahpd;
                $ngtotalk=$ngtotalk+$pjmlkredit;
                
                $totalsemuanya=$totalsemuanya+$pjmlkredit;
                
                //$pjumlahpd=number_format($pjumlahpd,0,",",",");
                $pjmlkredit=number_format($pjmlkredit,0,",",",");
                $pjmlsaldo=number_format($pjmlsaldo,0,",",",");
                    
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
hapusdata:
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp04");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp06");

    mysqli_close($cnit);
?>
