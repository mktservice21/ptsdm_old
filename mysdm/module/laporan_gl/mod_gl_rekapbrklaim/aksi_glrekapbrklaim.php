<?PHP
    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REKAP BR KLAIM DISCOUNT.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>

<html>
<head>
    <title>Rekap Budget Request Klaim Discount</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Apr 2019 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <link href="css/laporanbaru.css" rel="stylesheet">
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <?PHP } ?>
</head>

<body>
    <?PHP
        include "config/koneksimysqli.php";
        include "config/fungsi_combo.php";
        $pstsspd=$_POST['e_stsspd'];
        
        $now=date("mdYhis");
        $tmp01 =" dbtemp.RPTREKOTCF01_".$_SESSION['USERID']."_$now ";
        $tmp02 =" dbtemp.RPTREKOTCF02_".$_SESSION['USERID']."_$now ";
        $tmp03 =" dbtemp.RPTREKOTCF03_".$_SESSION['USERID']."_$now ";
    
        $filternobr="";
        if ($pstsspd=="2") {
            $filternobr=('');
            if (!empty($_POST['chkbox_nodiv'])){
                $filternobr=$_POST['chkbox_nodiv'];
                $filternobr=PilCekBox($filternobr);
            }
            
            $query = "select a.*, b.divisi divisipd, b.nodivisi, b.nomor, b.tgl as tglpd, b.coa4 coa, c.NAMA4 coa_nama,
                b.jumlah jumlahpd, b.kodeid, d.nama kodenama, b.subkode, d.subnama, b.jenis_rpt  
                from dbmaster.t_suratdana_br1 a JOIN  dbmaster.t_suratdana_br b 
                ON a.idinput=b.idinput LEFT JOIN dbmaster.coa_level4 c on b.coa4=c.COA4 
                LEFT JOIN dbmaster.t_kode_spd d on b.kodeid=d.kodeid and b.subkode=d.subkode 
                WHERE a.idinput IN $filternobr";
            $query = "create TEMPORARY table $tmp01 ($query)"; 
            mysqli_query($cnit, $query);
        }
        
        $query = "select a.DIVISI divisi, a.klaimId, a.karyawanid, c.nama nama_karyawan,
            a.distid, b.nama nama_dist, a.aktivitas1, a.jumlah, a.tgl, a.tgltrans, 
            a.realisasi1, a.noslip, a.COA4, d.NAMA4,
            a.lampiran, a.pajak, a.dpp, a.ppn, a.ppn_rp, 
            a.pph_jns, a.pph, a.pph_rp, a.tgl_fp, a.noseri, a.pembulatan
            from hrd.klaim a LEFT JOIN MKT.distrib0 b on a.distid=b.distid 
            LEFT JOIN hrd.karyawan c on a.karyawanid=c.karyawanId 
            LEFT JOIN dbmaster.coa_level4 d on a.COA4=d.COA4 
            WHERE 1=1 ";
        if ($pstsspd=="2") {
            $query .= " AND a.klaimId IN (select IFNULL(bridinput,'') from $tmp01)";
        }else{
            
            $tgl01=$_POST['bulan1'];
            $periode1= date("Y-m-01", strtotime($tgl01));
            $periode2= date("Y-m-t", strtotime($tgl01));
            
            $pildivisi=$_POST['divprodid'];
            $fildivisi="  ";
            if (!empty($pildivisi)) $fildivisi=" AND a.DIVISI='$pildivisi'";
            
            $jenis=$_POST['e_jenis'];
            $filterlampiran = "";
            if (!empty($jenis)) $filterlampiran = " and case when ifnull(a.lampiran,'N')='' then 'N' else a.lampiran end ='$jenis' ";
        
            $ptipeperiode=$_POST['e_ststipe'];
            $ftglnya = " DATE_FORMAT(a.tgl,'%Y-%m-%d') ";
            if ($ptipeperiode=="T") $ftglnya = " DATE_FORMAT(a.tgltrans,'%Y-%m-%d') ";
            if ($ptipeperiode=="S") $ftglnya = " DATE_FORMAT(a.tglrpsby,'%Y-%m-%d') ";
            $filtertgl = "AND $ftglnya BETWEEN '$periode1' AND '$periode2'";
            
            
            $query .=" $filtertgl $filterlampiran $fildivisi";
            
        }
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnit, $query);
        
        if ($pstsspd=="2") {
            $query = "SELECT a.*, b.divisipd, b.kodenama, b.tglpd, b.nomor, b.nodivisi, 
                b.nobbm, b.nobbk, b.urutan, b.amount, b.coa, b.coa_nama, b.jumlahpd,
                b.jenis_rpt 
                FROM $tmp02 a JOIN $tmp01 b on a.klaimId=b.bridinput";
        }else{
            $query = "select *, CAST('' as CHAR(1)) as kodenama, CAST('' as CHAR(1)) as divisipd, CAST(NULL as DATE) as tglpd, CAST('' as CHAR(1)) as nomor, CAST('' as CHAR(1)) as nodivisi, 
                    CAST(NULL as CHAR(1)) as nobbm, CAST(NULL as CHAR(1)) as nobbk, CAST(NULL as DECIMAL(20,2)) as urutan, 
                    CAST(NULL as DECIMAL(20,2)) as amount, CAST('' as CHAR(1)) as coa, CAST('' as CHAR(1)) as coa_nama,
                    CAST(NULL as DECIMAL(20,2)) as jumlahpd, CAST('' as CHAR(1)) as jenis_rpt from $tmp02";
        }
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnit, $query);
        //echo "$query";exit;
        
        $ngtotald=0;
        $ngtotalk=0;
        $ngtotals=0;
        if ($pstsspd=="2") {
            $query = "select distinct tglpd, divisipd divisi, kodenama, nomor, nodivisi, coa, coa_nama, jumlahpd, jenis_rpt FROM $tmp03 order by divisipd, nodivisi, tglpd";
        }else{
            $query = "select distinct tglpd, divisi, kodenama, nomor, nodivisi, coa, coa_nama, jumlahpd, jenis_rpt FROM $tmp03 order by nodivisi";
        }
        $tampil=mysqli_query($cnit, $query);
        $ketemu=mysqli_num_rows($tampil);
        if ($ketemu>0) {
            while ($r= mysqli_fetch_array($tampil)) {
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
                if (!empty($r['tglpd']) AND $r['tglpd']<>"0000-00-00")
                    $ptglpd =date("d-M-Y", strtotime($r['tglpd']));
                    
                echo "<table class='tjudul' width='100%'>";
                echo "<tr> <td width='300px'>Rekap Budget Request (RBR) Team $pdivisipd </td> <td> : </td> <td>$pnodivisi</td> </tr>";
                echo "<tr> <td width='200px'>$nket </td> <td> : </td> <td>$ptglpd</td> </tr>";
                echo "</table>";
                echo "<br/>&nbsp;";
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
                        <?PHP
                        if ($pstsspd=="2") {
                            echo "<th align='center'>Debit</th>
                                <th align='center'>Kredit</th>
                                <th align='center'>Saldo</th>";
                        }else{
                            echo "<th align='center'>Jumlah</th>
                                <th align='center'>Realisasi</th>
                                <th align='center'>Selisih</th>";
                        }
                        ?>
                        
                        <th align="center">No.</th>
                        <th align="center">DPP</th>
                        <th align="center">PPN</th>
                        <th align="center">PPH</th>
                        <th align="center">TGL FP PPN</th>
                        <th align="center">SERI FP PPN</th>
                        <th align="center">TGL FP PPH</th>
                        <th align="center">SERI FP PPH</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?PHP
                        $no=1;
                        $pjmldebit=0;
                        $pjmlkredit=0;
                        $pjmlsaldo=0;
                        if ($pstsspd=="2") {
                            $query = "select * FROM $tmp03 WHERE nodivisi='$pnodivisi' order by nodivisi, COA4";
                        }else{
                            $query = "select * FROM $tmp03 WHERE divisi='$pdivisipd' order by nodivisi, COA4";
                        }
                        $tampil2=mysqli_query($cnit, $query);
                        while ($row= mysqli_fetch_array($tampil2)) {
                            $ptgltrans = "";
                            if (!empty($row['tgltrans']) AND $row['tgltrans']<>"0000-00-00")
                                $ptgltrans =date("d-M-Y", strtotime($row['tgltrans']));

                            $pbbk = $row['nobbk'];
                            $pcoa = $row['COA4'];
                            $pnmcoa = $row['NAMA4'];
                            $pnmdokter = $row['nama_dist'];
                            $pnoslip = $row['noslip'];

                            $ppengajuan = $row['nama_karyawan'];
                            $pcabang = "";
                            if ($pcabang=="ETH - HO") $pcabang = "HO";
                            
                            $paktivitas1 = $row['aktivitas1'];
                            $prealisasi = $row['realisasi1'];
                            $pnorek = "";



                            $pdpp = $row['dpp'];
                            $pppn = $row['ppn_rp'];
                            $ppph = $row['pph_rp'];
                            
                            $pdpp=number_format($pdpp,0,",",",");
                            $pppn=number_format($pppn,0,",",",");
                            $ppph=number_format($ppph,0,",",",");
                            
                            $ptglfp="";
                            if (!empty($row['tgl_fp']) AND $row['tgl_fp']<>"0000-00-00")
                                $ptglfp =date("d-M-Y", strtotime($row['tgl_fp']));
                            
                            $pnoseri = $row['noseri'];
                            
                            $pdebit = "";
                            $pkredit = $row['amount'];
                            $psaldo = "";
                            
                            $njumlah=0;
                            $nrealisasi=0;
                            if ($pstsspd=="2") {
                            }else{
                                $pdebit=$row['jumlah'];
                                $pkredit=0;//$row['jumlah1'];
                                
                                $psaldo=$pdebit+$pkredit;
                                
                                $pjmldebit=$pjmldebit+$pdebit;
                                $pdebit=number_format($pdebit,0,",",",");
                                
                                $psaldo=number_format($psaldo,0,",",",");
                                $psaldo=0;
                            }
                            
                            $pjmlkredit=$pjmlkredit+$pkredit;
                            $pkredit=number_format($pkredit,0,",",",");


                            echo "<tr>";
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
                            echo "</tr>";


                            $no++;
                        }
                        if ($pstsspd=="2") {
                            $pjmlsaldo=$pjumlahpd-$pjmlkredit;
                        }else{
                            $pjmlsaldo=$pjmldebit-$pjmlkredit;
                            $pjumlahpd=$pjmldebit;
                        }
                        
                        $ngtotald=$ngtotald+$pjumlahpd;
                        $ngtotalk=$ngtotalk+$pjmlkredit;
                        
                        $pjumlahpd=number_format($pjumlahpd,0,",",",");
                        $pjmlkredit=number_format($pjmlkredit,0,",",",");
                        $pjmlsaldo=number_format($pjmlsaldo,0,",",",");
                        $pjmlkredit=0;
                        $pjmlsaldo=0;
                        if ($pstsspd=="2") {
                            echo "<tr>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap>$pbbk</td>";
                            echo "<td nowrap>$pcoapd</td>";
                            echo "<td nowrap>$pnmcoapd</td>";
                            echo "<td nowrap>$pkodenm</td>";
                            echo "<td nowrap>$pnospd</td>";
                            echo "<td nowrap>$ppengajuanpd</td>";
                            echo "<td nowrap>$pnodivisi</td>";
                            echo "<td >$ppengajuanpd2</td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap align='right'>$pjumlahpd</td>";
                            echo "<td nowrap align='right'></td>";
                            echo "<td nowrap align='right'></td>";
                            echo "<td nowrap align='center'></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "</tr>";
                        }
                        
                        echo "<tr>";
                        echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                        echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                        echo "</tr>";
                        
                        echo "<tr>";
                        echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                        echo "<td> <b>Grand Total</b> </td> <td>&nbsp;</td> <td>&nbsp;</td>";
                        echo "<td align='right'><b>$pjumlahpd</b></td><td align='right'><b>$pjmlkredit</b></td><td align='right'><b>$pjmlsaldo</b></td>";
                        echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                        echo "</tr>";
                        
                        
                        ?>
                    </tbody>
                </table>
                <?PHP

                echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
            }
        }
        
        if ($pstsspd=="2") {

            $ngtotals=$ngtotald-$ngtotalk;
            $ngtotald=number_format($ngtotald,0,",",",");
            $ngtotalk=number_format($ngtotalk,0,",",",");
            $ngtotals=number_format($ngtotals,0,",",",");
            
            
            echo "<table id='datatable2' class='table table-striped table-bordered example_2' border='1px solid black'>";
            echo "<tr>";
            echo "<td nowrap colspan=3><b>Grand Total</b></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td><b>Debit</b></td><td><b>Kredit</b></td><td><b>Saldo</b></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td align='right'><b>$ngtotald</b></td> <td align='right'><b>$ngtotalk</b></td> <td align='right'><b>$ngtotals</b></td>";
            echo "</tr>";

            echo "</table>";
            echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
        }
    ?>
    
    
    <?PHP
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp03");
        
        mysqli_close($cnit);
    ?>
</body>
</html>