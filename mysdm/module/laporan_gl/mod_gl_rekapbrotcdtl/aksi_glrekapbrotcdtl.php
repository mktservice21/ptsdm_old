<?PHP
    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REKAP DETAIL BUDGET REQUEST OTC.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>

<html>
<head>
    <title>Rekap Detail Budget Request Team OTC</title>
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
        $tmp04 =" dbtemp.RPTREKOTCF04_".$_SESSION['USERID']."_$now ";
        $tmp05 =" dbtemp.RPTREKOTCF05_".$_SESSION['USERID']."_$now ";
    
        $filternobr="";
        if ($pstsspd=="2") {
            $filternobr=('');
            if (!empty($_POST['chkbox_nodiv'])){
                $filternobr=$_POST['chkbox_nodiv'];
                $filternobr=PilCekBox($filternobr);
            }
            
            $query = "select a.*, b.nodivisi, b.nomor, b.tgl as tglpd, b.coa4 coa, c.NAMA4 coa_nama,
                b.jumlah jumlahpd, b.kodeid, d.nama kodenama, b.subkode, d.subnama 
                from dbmaster.t_suratdana_br1 a JOIN  dbmaster.t_suratdana_br b 
                ON a.idinput=b.idinput LEFT JOIN dbmaster.coa_level4 c on b.coa4=c.COA4 
                LEFT JOIN dbmaster.t_kode_spd d on b.kodeid=d.kodeid and b.subkode=d.subkode 
                WHERE a.idinput IN $filternobr";
            $query = "create TEMPORARY table $tmp01 ($query)"; 
            mysqli_query($cnit, $query);
        }
        
        $query = "select a.brOtcId, a.tgltrans, a.COA4, b.NAMA4, a.icabangid_o, c.nama nama_cabang, a.noslip, a.kodeid, a.subpost,
           a.keterangan1, a.real1, a.jumlah, a.realisasi, 
           a.pajak, a.dpp, a.ppn, a.ppn_rp, a.pph_jns, a.pph, a.pph_rp, a.tgl_fp, a.noseri, a.pembulatan,
           a.lampiran, a.ca, a.via, a.tglbr, a.tglrpsby, a.tglreal 
           from hrd.br_otc a 
           LEFT JOIN dbmaster.coa_level4 b on a.COA4=b.COA4 
           LEFT JOIN mkt.icabang_o c on a.icabangid_o=c.icabangid_o WHERE 1=1 ";
        if ($pstsspd=="2") {
            $query .= " AND a.brOtcId IN (select IFNULL(bridinput,'') from $tmp01)";
        }else{
            
            $tgl01=$_POST['bulan1'];
            $periode1= date("Y-m-01", strtotime($tgl01));
            $periode2= date("Y-m-t", strtotime($tgl01));
            
            $jenis=$_POST['e_jenis'];
            $filterlampiran = "";
            if (!empty($jenis)) $filterlampiran = " and case when ifnull(a.lampiran,'N')='' then 'N' else a.lampiran end ='$jenis' ";
        
            $ptipeperiode=$_POST['e_ststipe'];
            $ftglnya = " DATE_FORMAT(a.tglbr,'%Y-%m-%d') ";
            if ($ptipeperiode=="T") $ftglnya = " DATE_FORMAT(a.tgltrans,'%Y-%m-%d') ";
            if ($ptipeperiode=="S") $ftglnya = " DATE_FORMAT(a.tglrpsby,'%Y-%m-%d') ";
            if ($ptipeperiode=="R") $ftglnya = " DATE_FORMAT(a.tglreal,'%Y-%m-%d') ";
            $filtertgl = "AND $ftglnya BETWEEN '$periode1' AND '$periode2'";
            
            
            $query .=" $filtertgl $filterlampiran";
            
        }
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnit, $query);
        
        mysqli_query($cnit, "alter table $tmp02 ADD nama_posting VARCHAR(200)");
        mysqli_query($cnit, "UPDATE $tmp02 a SET a.nama_posting =(SELECT b.nama FROM hrd.brkd_otc b WHERE a.kodeid=b.kodeid AND a.subpost=b.subpost) WHERE IFNULL(a.kodeid,'')<> '' AND IFNULL(a.subpost,'')<> ''");
        mysqli_query($cnit, "UPDATE $tmp02 a SET a.nama_posting =(SELECT b.nmsubpost FROM hrd.brkd_otc b WHERE a.subpost=b.subpost) WHERE IFNULL(a.kodeid,'')<> '' AND IFNULL(a.subpost,'')= '' AND IFNULL(a.nama_posting,'')= ''");
        
        mysqli_query($cnit, "UPDATE $tmp02 a SET a.nama_cabang =(SELECT b.nama FROM dbmaster.cabang_otc b WHERE a.icabangid_o=b.cabangid_ho) WHERE IFNULL(a.nama_cabang,'')=''");
        
        if ($pstsspd=="2") {
            $query = "SELECT a.*, b.kodenama, b.tglpd, b.nomor, b.nodivisi, b.nobbm, b.nobbk, b.urutan, b.amount, b.coa, b.coa_nama, b.jumlahpd 
                    FROM $tmp02 a JOIN $tmp01 b on a.brOtcId=b.bridinput";
        }else{
            $query = "select *, CAST('' as CHAR(1)) as kodenama, CAST(NULL as DATE) as tglpd, CAST('' as CHAR(1)) as nomor, CAST('' as CHAR(1)) as nodivisi, 
                    CAST(NULL as CHAR(1)) as nobbm, CAST(NULL as CHAR(1)) as nobbk, CAST(NULL as DECIMAL(20,2)) as urutan, 
                    CAST(NULL as DECIMAL(20,2)) as amount, CAST('' as CHAR(1)) as coa, CAST('' as CHAR(1)) as coa_nama,
                    CAST(NULL as DECIMAL(20,2)) as jumlahpd from $tmp02";
        }
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnit, $query);
        //echo "$query";
        
        
        $query = "CREATE TEMPORARY TABLE $tmp04 select DISTINCT brOtcId, 'OTC' divisi, COA4, ppn as jmlpers, ppn_rp jumlahrp from $tmp03 WHERE pajak='Y'";
        mysqli_query($cnit, $query);
        
        $query = "delete from $tmp04";
        mysqli_query($cnit, $query);
        
        $query = "INSERT INTO $tmp04 select brOtcId, 'OTC' divprodid, '106-04' COA4, ppn, ppn_rp FROM $tmp03 WHERE pajak='Y'";
        mysqli_query($cnit, $query);
        
        $query = "INSERT INTO $tmp04 select brOtcId, 'OTC' divprodid, '205-08' COA4, pph, pph_rp FROM $tmp03 WHERE pajak='Y' AND pph_jns='pph23'";
        mysqli_query($cnit, $query);
        
        $query = "INSERT INTO $tmp04 select brOtcId, 'OTC' divprodid, '205-02' COA4, pph, pph_rp FROM $tmp03 WHERE pajak='Y' AND pph_jns='pph21';";
        mysqli_query($cnit, $query);
        
        $query = "INSERT INTO $tmp04 (brOtcId, divisi, COA4, jumlahrp) select brOtcId, 'OTC' divprodid, '905-02' COA4, pembulatan FROM $tmp03 WHERE IFNULL(pembulatan,0)<>0;";
        mysqli_query($cnit, $query);
        
        
        //$query = "INSERT INTO $tmp04 (brOtcId, divisi, COA4, jumlahrp) select brOtcId, divprodid, '754-07' COA4, materai_rp FROM $tmp03 WHERE IFNULL(materai_rp,0)<>0";
        //mysqli_query($cnit, $query);
        
        $query = "delete from $tmp04 where IFNULL(jumlahrp,0)=0;";
        mysqli_query($cnit, $query);
        
        $query = "CREATE TEMPORARY TABLE $tmp05
                select a.*, b.real1, b.dpp, b.ppn_rp, b.pph_rp, b.tgltrans, b.nobbk, b.nobbm, c.NAMA4 from $tmp04 a JOIN $tmp03 b on a.brOtcId=b.brOtcId JOIN dbmaster.coa_level4 c on a.COA4=c.COA4";
        mysqli_query($cnit, $query);
        
        
        
        
        
        $query = "select '' kodenama, '' nomor, '' nodivisi, '' coa, '' coa_nama, 0 jumlahpd FROM $tmp03 LIMIT 1";
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

                $ppengajuan="OTC";
                $ppengajuan2="BR OTC";

                echo "<table class='tjudul' width='100%'>";
                echo "<tr> <td width='300px'>Rekap Detail Budget Request (RBR) Team OTC </td> <td> </td> <td></td> </tr>";
                echo "<tr> <td width='200px'>&nbsp; </td> <td> </td> <td></td> </tr>";
                echo "</table>";
                echo "<br/>&nbsp;";
                ?>
                <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
                    <thead>
                        <tr style='background-color:#cccccc; font-size: 13px;'>
                        <th align="center">Date</th>
                        <th align="center">Bukti</th>
                        <th align="center">Kode</th>
                        <th align="center">Perkiraan</th>
                        <th align="center">Realisasi</th>
                        <th align="center">JUMLAH SPD</th>
                        <th align="center">Debit</th>
                        <th align="center">Credit</th>
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
                        $ptotdpp=0;$ptotppn=0;$ptotpph=0;
                        $ptotjmldebit=0;
                        $ptotjmlkredit=0;
                        $ptotjmlspd=0;
                        $query = "select * FROM $tmp03 order by nodivisi, COA4";
                        $tampil2=mysqli_query($cnit, $query);
                        while ($row= mysqli_fetch_array($tampil2)) {
                            
                            $pnoidbr = $row['brOtcId'];
                            $ppajak = $row['pajak'];
                            
                            $ptgltrans = "";
                            if (!empty($row['tgltrans']) AND $row['tgltrans']<>"0000-00-00")
                                $ptgltrans =date("d-M-Y", strtotime($row['tgltrans']));

                            $pbbk = $row['nobbk'];
                            $pcoa = $row['COA4'];
                            $pnmcoa = $row['NAMA4'];
                            $paktivitas1 = $row['keterangan1'];
                            $prealisasi = $row['real1'];
                            $pnorek = "";


                            $pdpp = $row['dpp'];
                            $pppn = $row['ppn_rp'];
                            $ppph = $row['pph_rp'];
                            
                            $ptotdpp=$ptotdpp+$pdpp; $ptotppn=$ptotppn+$pppn; $ptotpph=$ptotpph+$ppph;
                            
                            $pdpp = cariangkadesimal("1", $pdpp);
                            $pppn = cariangkadesimal("1", $pppn);
                            $ppph = cariangkadesimal("1", $ppph);
                            
                            //$pdpp=number_format($pdpp,0,",",",");
                            //$pppn=number_format($pppn,0,",",",");
                            //$ppph=number_format($ppph,0,",",",");
                            
                            $ptglfp="";
                            if (!empty($row['tgl_fp']) AND $row['tgl_fp']<>"0000-00-00")
                                $ptglfp =date("d-M-Y", strtotime($row['tgl_fp']));
                            
                            $pnoseri = $row['noseri'];
                            
                            
                            $pjmlspd=$row['jumlah'];
                            $ptotjmlspd=$ptotjmlspd+$pjmlspd;
                            $pjmlspd=number_format($pjmlspd,0,",",",");
                            
                            $pdebit="";
                            $pkredit="";
                            
                            if ($ppajak=="Y") {
                                $pkredit=$row['dpp'];
                                
                                $ptotjmlkredit=$ptotjmlkredit+$pkredit;
                                
                                $pkredit=number_format($pkredit,0,",",",");
                            }


                            echo "<tr>";
                            echo "<td nowrap>$ptgltrans</td>";
                            echo "<td nowrap>$pbbk</td>";
                            echo "<td nowrap>$pcoa</td>";
                            echo "<td nowrap>$pnmcoa</td>";
                            echo "<td nowrap>$prealisasi</td>";
                            echo "<td nowrap align='right'>$pjmlspd</td>";
                            echo "<td nowrap align='right'>$pdebit</td>";
                            echo "<td nowrap align='right'>$pkredit</td>";
                            echo "<td nowrap align='center'>$no</td>";
                            echo "<td nowrap align='right'>$pdpp</td>";
                            echo "<td nowrap align='right'>$pppn</td>";
                            echo "<td nowrap align='right'>$ppph</td>";
                            echo "<td nowrap>$ptglfp</td>";
                            echo "<td nowrap>$pnoseri</td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "</tr>";
                            


                            if ($ppajak=="Y") {
                                $query = "select * FROM $tmp05 WHERE brOtcId='$pnoidbr' order by COA4";
                                $tampilp=mysqli_query($cnit, $query);
                                while ($rwp= mysqli_fetch_array($tampilp)) {
                                    
                                    $ptgltrans = "";
                                    if (!empty($rwp['tgltrans']) AND $rwp['tgltrans']<>"0000-00-00")
                                        $ptgltrans =date("d-M-Y", strtotime($rwp['tgltrans']));

                                    $pbbk = $rwp['nobbk'];
                                    $pcoa = $rwp['COA4'];
                                    $pnmcoa = $rwp['NAMA4'];
                                    
                                    $ppjkcredit=$rwp['jumlahrp'];
                                    if (empty($ppjkcredit)) $ppjkcredit=0;
                                    
                                    $blakang = cariangkadesimal("2", $ppjkcredit);
                                    
                                    if ($pcoa=="205-02" OR $pcoa=="205-08") {
                                        $ppjkcredit=0-(double)$ppjkcredit;
                                    }
                                    $ptotjmlkredit=$ptotjmlkredit+$ppjkcredit;
                                    
                                    //if ($pcoa=="205-02" OR $pcoa=="205-08" OR $pcoa=="106-04") {
                                    if ((double)$blakang<>0) {
                                        $ppjkcredit=number_format($ppjkcredit,2,".",",");
                                    }else{
                                        $ppjkcredit=number_format($ppjkcredit,0,",",",");
                                    }
                                    
                                    echo "<tr>";
                                    echo "<td nowrap>$ptgltrans</td>";
                                    echo "<td nowrap>$pbbk</td>";
                                    echo "<td nowrap>$pcoa</td>";
                                    echo "<td nowrap>$pnmcoa</td>";
                                    echo "<td nowrap></td>";

                                    echo "<td nowrap align='right'></td>";
                                    echo "<td nowrap align='right'></td>";
                                    echo "<td nowrap align='right'>$ppjkcredit</td>";
                                    echo "<td nowrap align='center'>$no</td>";
                                    echo "<td nowrap align='right'></td>";
                                    echo "<td nowrap align='right'></td>";
                                    echo "<td nowrap align='right'></td>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap></td>";
                                    echo "</tr>";
                                    
                                    
                                }
                                echo "<tr>";
                                echo "<td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td>";
                                echo "<td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td>";
                                echo "</tr>";
                            }


                            $no++;
                        }
                        
                        $ptotjmldebit="";
                        $ptotjmlspd=number_format($ptotjmlspd,0,",",",");
                        $ptotjmlkredit=number_format($ptotjmlkredit,0,",",",");
                        //$ptotjmldebit=number_format($ptotjmldebit,0,",",",");
                        
                        $ptotdpp = cariangkadesimal("1", $ptotdpp);
                        $ptotppn = cariangkadesimal("1", $ptotppn);
                        $ptotpph = cariangkadesimal("1", $ptotpph);
                        
                        //$ptotdpp=number_format($ptotdpp,0,",",",");
                        //$ptotppn=number_format($ptotppn,0,",",",");
                        //$ptotpph=number_format($ptotpph,0,",",",");
                        
                        echo "<tr>";
                        echo "<td></td> <td></td> <td></td> <td align='center'><b>TOTAL</b></td> <td></td> ";
                        echo "<td align='right'><b>$ptotjmlspd</b></td> <td align='right'><b> </b></td> <td align='right'><b>$ptotjmlkredit</b></td>";
                        echo "<td></td>";
                        echo "<td align='right'><b>$ptotdpp</b></td> <td align='right'><b>$ptotppn</b></td> <td align='right'><b>$ptotpph</b></td>";
                        echo "<td></td> <td></td> <td></td> <td></td>";
                        echo "</tr>";
                        
                        ?>
                    </tbody>
                </table>
                <?PHP

                echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
            }
        }
    ?>
    
    
    <?PHP
        function cariangkadesimal($nformat, $nilai){
            if (empty($nilai)) $nilai=0;
            $titik = strpos($nilai,".");
            $blakang = substr($nilai,($titik+1));
            if (empty($blakang)) $blakang=0;
            
            if ((double)$blakang<>0) {
                $pnilai=number_format($nilai,2,".",",");
            }else{
                $pnilai=number_format($nilai,0,",",",");
            }
            if ($nformat=="1") {
                Return $pnilai;
            }else{
                Return $blakang;
            }
        }
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp03");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp04");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
        
        mysqli_close($cnit);
    ?>
</body>
</html>