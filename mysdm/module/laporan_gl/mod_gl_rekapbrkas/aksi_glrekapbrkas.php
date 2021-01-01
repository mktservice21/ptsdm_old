<?PHP
    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REKAP BUDGET REQUEST KAS KECIL.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>

<html>
<head>
    <title>Rekap Budget Request Kas Kecil</title>
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
        
        $query = "select a.kasId, a.periode1, a.kode, b.COA4, c.NAMA4, a.periode2, a.karyawanid, a.nama, d.nama nama_karyawan,
            a.aktivitas1, a.aktivitas2, a.jumlah 
            from hrd.kas a LEFT JOIN dbmaster.posting_coa_kas b on a.kode=b.kodeid 
            LEFT JOIN dbmaster.coa_level4 c on b.COA4=c.COA4
            LEFT JOIN hrd.karyawan d on a.karyawanid=d.karyawanId 
            WHERE 1=1 ";
        if ($pstsspd=="2") {
            $query .= " AND a.kasId IN (select IFNULL(bridinput,'') from $tmp01)";
        }else{
            
            $tgl01=$_POST['bulan1'];
            $periode1= date("Y-m-01", strtotime($tgl01));
            $periode2= date("Y-m-t", strtotime($tgl01));
        
            $filtertgl = "AND DATE_FORMAT(a.periode1,'%Y-%m-%d') BETWEEN '$periode1' AND '$periode2'";
            
            
            $query .=" $filtertgl ";
            
        }
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnit, $query);
        
        if ($pstsspd=="2") {
            $query = "SELECT a.*, b.divisipd, b.kodenama, b.tglpd, b.nomor, b.nodivisi, 
                b.nobbm, b.nobbk, b.urutan, b.amount, b.coa, b.coa_nama, b.jumlahpd,
                b.jenis_rpt 
                FROM $tmp02 a JOIN $tmp01 b on a.kasId=b.bridinput";
        }else{
            $query = "select *, CAST('' as CHAR(1)) as kodenama, CAST('' as CHAR(1)) as divisipd, CAST(NULL as DATE) as tglpd, CAST('' as CHAR(1)) as nomor, CAST('' as CHAR(1)) as nodivisi, 
                    CAST(NULL as CHAR(1)) as nobbm, CAST(NULL as CHAR(1)) as nobbk, CAST(NULL as DECIMAL(20,2)) as urutan, 
                    CAST(NULL as DECIMAL(20,2)) as amount, CAST('' as CHAR(1)) as coa, CAST('' as CHAR(1)) as coa_nama,
                    CAST(NULL as DECIMAL(20,2)) as jumlahpd, CAST('' as CHAR(1)) as jenis_rpt from $tmp02";
        }
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnit, $query);
        //echo "$query";exit;
        
        $ftgl=$_POST['bulan1'];
        $mbln= date("F Y", strtotime($ftgl));
            
        if ($pstsspd=="2") {
            $query = "select distinct tglpd, divisipd divisi, kodenama, nomor, nodivisi, coa, coa_nama, jumlahpd, jenis_rpt FROM $tmp03 order by tglpd, divisipd, nodivisi";
        }else{
            $query = "select distinct tglpd, '' divisi, kodenama, nomor, nodivisi, coa, coa_nama, jumlahpd, jenis_rpt FROM $tmp03 order by nodivisi";
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
                $nket="Laporan Kas Kecil periode $mbln";
                
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
                        $pjmldebit="";
                        if ($pstsspd=="2") {
                            $pjmldebit=$pjumlahpd;
                            $pjumlahpd=number_format($pjumlahpd,0,",",",");
                                           
                            
                            echo "<tr>";
                            echo "<td>$ptgltrans</td><td>$pbbk</td><td>$pcoapd</td><td>$pnmcoapd</td><td>Klaim</td>";
                            echo "<td>$pnospd</td><td>KK</td><td>$pnodivisi</td>";
                            echo "<td>Kas Kecil</td>";
                            echo "<td></td> <td></td>";
                            echo "<td nowrap align='right'>$pjumlahpd</td>";
                            echo "<td nowrap align='right'><b> </b></td>";
                            echo "</tr>";
                            
                            $pjmldebit=number_format($pjmldebit,0,",",",");
                        }
                        
                        
                        $pjmlkredit=number_format($pjmlkredit,0,",",",");
                        
                        
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
                            }elseif ($x==2) {
                                echo "<td><b>Petty Cash</b></td>";
                            }elseif ($x==3) {
                                echo "<td><b>Kas Bon terlampir</b></td>";
                            }elseif ($x==4) {
                                echo "<td><b>Petty Cash- belum tarik dari Bank Niaga No.</b></td>";
                            }elseif ($x==5) {
                                echo "<td><b>Petty Cash- belum tarik dari Bank Niaga No.</b></td>";
                            }elseif ($x==6) {
                                echo "<td><b>Saldo Akhir</b></td>";
                            }else{
                                echo "<td><b></b></td>";
                            }
                            echo "<td></td> <td></td>";
                            echo "<td nowrap align='right'><b>$pjmldebit</b></td>";
                            echo "<td nowrap align='right'><b>$pjmlkredit</b></td>";
                            echo "</tr>";
                            $pjmlkredit="";
                            $pjmldebit="";
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
    ?>
    
    
    <?PHP
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp03");
        
        mysqli_close($cnit);
    ?>
</body>
</html>