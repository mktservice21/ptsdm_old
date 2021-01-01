<?PHP
    session_start();
    $erpttipe = $_POST['radio1'];
    $rptheader="SUMMARY";
    if ($erpttipe=="D") $rptheader="DETAIL";
    $ketprint=$_GET['ket'];
    if ($ketprint=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REPORT $rptheader TRANSAKSI BR.xls");
    }
    
    include("config/koneksimysqli_it.php");
    include("config/common.php");
    
?>

<html>
<head>
    <title>REPORT <?PHP echo $rptheader; ?> TRANSAKSI BR</title>
<?PHP if ($ketprint!="excel") { ?>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
    <link href="css/laporanbaru.css" rel="stylesheet">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
<?PHP } ?>
</head>

<body>
<?PHP
    $tglnow = date("d/m/Y");
    $tgl01 = $_POST['bulan1'];
    $tgl02 = $_POST['bulan2'];
    
    $periode1 = date("Y-m-d", strtotime($tgl01));
    $periode2 = date("Y-m-d", strtotime($tgl02));
    
    $pbln1 = date("Y-m", strtotime($tgl01));
    $pbln2 = date("Y-m", strtotime($tgl02));
    
    $ptglprint1 = date("d F Y", strtotime($tgl01));
    $ptglprint2 = date("d F Y", strtotime($tgl02));
    
    
    $edivisi = $_POST['divprodid'];
    $fdivisi = "";
    
    $milliseconds = round(microtime(true) * 1000);
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DRCOAGLD01_".$_SESSION['USERID']."_$now$milliseconds ";
    $tmp02 =" dbtemp.DRCOAGLD02_".$_SESSION['USERID']."_$now$milliseconds ";
    $tmp03 =" dbtemp.DRCOAGLD03_".$_SESSION['USERID']."_$now$milliseconds ";
    
    $droptable01 = "DROP TABLE $tmp01";
    $droptable02 = "DROP TABLE $tmp02";
    $droptable03 = "DROP TABLE $tmp03";
    

    $query = "CREATE TABLE $tmp01 (kodeinput VARCHAR(2), pengajuan varchar(100), divisi varchar(5), tgl date, icabangid varchar(10), bukti varchar(50), 
        idinput varchar(20), noid varchar(50), 
        noidnm varchar(150), noidsub varchar(50), noidsubnm varchar(150), 
        coa4 varchar(20), karyawanid varchar(10), iddokter varchar(10), nmdokter varchar(200), noslip varchar(50),
        keterangan varchar(500), nmrealisasi varchar(200), icabangid_o varchar(10), debit DECIMAL(30,2), kredit DECIMAL(30,2), saldo DECIMAL(30,2),
        jumlah DECIMAL(30,2), realisasi DECIMAL(30,2), cn DECIMAL(30,2)
        )";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    if ($edivisi != "OTC") {
        //DCC, DSS & NON
        $filtertgl = " AND DATE_FORMAT(a.tgl,'%Y-%m-%d') between '$periode1' AND '$periode2' ";
        if (!empty($edivisi) AND ($edivisi <> "*") AND ($edivisi <> "0")) $fdivisi = " AND a.divprodid='$edivisi' ";
        $sql = "select 'A' kodeinput, 'NON DCC DSS' pengajuan, a.divprodid, a.tgl, a.icabangid, a.brId, a.kode, a.COA4, a.karyawanId, 
            a.dokterId, b.nama namadokter, a.noslip,
            a.aktivitas1, a.realisasi1 nmrealisasi, a.jumlah, a.jumlah1, a.cn 
            FROM hrd.br0 a LEFT JOIN hrd.dokter b on a.dokterId=b.dokterId  
            WHERE a.brId not in (SELECT DISTINCT ifnull(brId,'') from hrd.br0_reject) $filtertgl $fdivisi";
        
        $query = "INSERT INTO $tmp01 (kodeinput, pengajuan, divisi, tgl, icabangid, idinput, noid, coa4, karyawanid, iddokter, nmdokter, noslip,
                keterangan, nmrealisasi, jumlah, realisasi, cn) $sql";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
            mysqli_query($cnit, $droptable01);
            echo $erropesan; exit;
        }
        mysqli_query($cnit, "UPDATE $tmp01 as a set a.coa4=(select b.COA4 from dbmaster.posting_coa_br b WHERE b.kodeid=a.noid AND b.divisi=a.divisi limit 1) WHERE a.kodeinput='A' AND ifnull(a.coa4,'')=''");
        mysqli_query($cnit, "UPDATE $tmp01 as a set a.divisi=(select b.divisi from dbmaster.posting_coa_br b WHERE b.kodeid=a.noid AND b.divisi=a.divisi limit 1) WHERE a.kodeinput='A' AND ifnull(a.divisi,'')=''");
        
        //$query="select dokterId, nama from hrd.dokter where dokterId in (select distinct iddokter from $tmp01 where ifnull(iddokter,'') not in ('', 'blank', '(blank)'))";
        //mysqli_query($cnit, "CREATE TABLE $tmp03 ($query)");
        //mysqli_query($cnit, "UPDATE $tmp01 as a set a.nmdokter=(select b.nama from $tmp03 b WHERE b.dokterId=a.iddokter limit 1) WHERE ifnull(a.iddokter,'')<>''");
        //mysqli_query($cnit, $droptable03);
        
        mysqli_query($cnit, "UPDATE $tmp01 as a set a.pengajuan=ifnull((select b.nama from hrd.br_kode b WHERE b.kodeid=a.noid and b.divprodid=a.divisi limit 1),'DCC DSS NON') WHERE a.kodeinput='A'");
    }
    
    if ($edivisi != "OTC") {
        //KLAIM ==> belum pakai divisi
        $filtertgl = " AND DATE_FORMAT(tgl,'%Y-%m-%d') between '$periode1' AND '$periode2' ";
        $fdivisi = "";
        $sql = "select 'B' kodeinput, 'KLAIM' pengajuan, DIVISI, tgl, distid, klaimId, COA4, karyawanid, noslip,
            aktivitas1, realisasi1 nmrealisasi, jumlah 
            FROM hrd.klaim WHERE klaimId not in (SELECT DISTINCT ifnull(klaimId,'') from hrd.klaim_reject) $filtertgl $fdivisi";
        
        $query = "INSERT INTO $tmp01 (kodeinput, pengajuan, divisi, tgl, icabangid, idinput, coa4, karyawanid, noslip, 
                keterangan, nmrealisasi, jumlah) $sql";
        
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
            mysqli_query($cnit, $droptable01);
            echo $erropesan; exit;
        }
        mysqli_query($cnit, "UPDATE $tmp01 set coa4=(select COA4 from dbmaster.posting_coa_br WHERE kodeid='700-02-07' limit 1) WHERE kodeinput='B' AND ifnull(coa4,'')=''");
        mysqli_query($cnit, "UPDATE $tmp01 set divisi=(select divisi from dbmaster.posting_coa_br WHERE kodeid='700-02-07' limit 1) WHERE kodeinput='B' AND ifnull(divisi,'')=''");
    }
    
    
    if ($edivisi != "OTC") {
        //KAS
        $filtertgl = " AND DATE_FORMAT(a.periode1,'%Y-%m-%d') between '$periode1' AND '$periode2' ";
        if (!empty($edivisi) AND ($edivisi <> "*") AND ($edivisi <> "0")) $fdivisi = " AND e.DIVISI2='$edivisi' ";
        $sql = "select 	'C' kodeinput, 'KAS' pengajuan, e.DIVISI2, a.periode1, a.kasId, a.kode, b.COA4, a.karyawanid, a.nobukti,
                a.aktivitas1, a.jumlah
                FROM hrd.kas a LEFT JOIN dbmaster.posting_coa_kas b on a.kode=b.kodeid
                LEFT JOIN dbmaster.coa_level4 c on b.COA4=c.COA4 LEFT JOIN dbmaster.coa_level3 d on c.COA3=d.COA3
                LEFT JOIN dbmaster.coa_level2 e on e.COA2=d.COA2 
                WHERE 1=1 $filtertgl $fdivisi";
        
        $query = "INSERT INTO $tmp01 (kodeinput, pengajuan, divisi, tgl, idinput, noid, coa4, karyawanid, bukti,
                keterangan, jumlah) $sql";
        
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
            mysqli_query($cnit, $droptable01);
            echo $erropesan; exit;
        }
        mysqli_query($cnit, "UPDATE $tmp01 set coa4=null WHERE kodeinput='C' AND coa4=''");
    }
    
    if (empty($edivisi) OR ($edivisi == "OTC")) {
        //BR OTC
        $filtertgl = " AND DATE_FORMAT(tglbr,'%Y-%m-%d') between '$periode1' AND '$periode2' ";
        $fdivisi = "";
        $sql = "select 'D' kodeinput, 'BROTC' pengajuan, 'OTC' divisi, tglbr, icabangid_o, brOtcId, subpost, kodeid, COA4, noslip,
            keterangan1, jumlah, realisasi 
            FROM hrd.br_otc WHERE brOtcId not in (SELECT DISTINCT ifnull(brOtcId,'') from hrd.br_otc_reject) $filtertgl";
        
        $query = "INSERT INTO $tmp01 (kodeinput, pengajuan, divisi, tgl, icabangid, idinput, noid, noidsub, coa4, noslip,
                keterangan, jumlah, realisasi) $sql";
        
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
            mysqli_query($cnit, $droptable01);
            echo $erropesan; exit;
        }
        mysqli_query($cnit, "UPDATE $tmp01 as a set a.coa4=(select b.COA4 from dbmaster.posting_coa b WHERE CONCAT(b.subpost, b.kodeid)=CONCAT(a.noid, a.noidsub) limit 1) WHERE a.kodeinput='D' AND ifnull(a.coa4,'')=''");
        mysqli_query($cnit, "UPDATE $tmp01 as a set a.divisi='OTC' WHERE a.kodeinput='D' AND ifnull(a.divisi,'')=''");
        
        mysqli_query($cnit, "UPDATE $tmp01 as a set a.pengajuan=ifnull((select b.nama from hrd.brkd_otc b WHERE b.kodeid=a.noidsub AND b.subpost=a.noid limit 1),'BR OTC') WHERE a.kodeinput='D'");
        
    }
    
    //Budget Biaya Rutin Gelondongan COA
    $filtertgl = " AND DATE_FORMAT(periode,'%Y-%m') between '$pbln1' AND '$pbln2' ";
    if (!empty($edivisi) AND ($edivisi <> "*") AND ($edivisi <> "0")) $fdivisi = " AND divisi='$edivisi' ";
    $sql = "select 'E' kodeinput, 'BRRUTINALL' pengajuan, divisi, periode, idbr, COA4, karyawanid, icabangid, keterangan, jumlah
        FROM dbmaster.t_br_bulan WHERE stsnonaktif <> 'Y' $filtertgl $fdivisi";
    
    $query = "INSERT INTO $tmp01 (kodeinput, pengajuan, divisi, tgl, idinput, coa4, karyawanid, icabangid, keterangan, jumlah) $sql";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
        mysqli_query($cnit, $droptable01);
        echo $erropesan; exit;
    }
    
    mysqli_query($cnit, "UPDATE $tmp01 as a set a.pengajuan='RUTIN' WHERE kodeinput='E' AND karyawanid='0000000143'");
    mysqli_query($cnit, "UPDATE $tmp01 as a set a.pengajuan='LUAR KOTA' WHERE kodeinput='E' AND karyawanid='0000000329'");
    
    
    //RUTIN
    $filtertgl = " AND ((DATE_FORMAT(b.periode1,'%Y-%m-%d') between '$periode1' AND '$periode2') OR (DATE_FORMAT(b.periode2,'%Y-%m-%d') between '$periode1' AND '$periode2')) ";
    if (!empty($edivisi) AND ($edivisi <> "*") AND ($edivisi <> "0")) $fdivisi = " AND b.divisi='$edivisi' ";
    $sql = "select 'F' kodeinput,  'RUTIN' pengajuan, b.divisi, b.bulan, b.icabangid, 
        a.idrutin, a.nobrid, a.coa, b.karyawanid, b.keterangan, sum(a.rptotal) rptotal 
        FROM dbmaster.t_brrutin1 a JOIN dbmaster.t_brrutin0 b on a.idrutin=b.idrutin 
        WHERE b.stsnonaktif <> 'Y' AND kode=1 AND ifnull(b.fin,'') <> '' $filtertgl $fdivisi 
        GROUP BY 1,2,3,4,5,6,7,8,9,10";
    
    $query = "INSERT INTO $tmp01 (kodeinput, pengajuan, divisi, tgl, icabangid, idinput, noid, coa4, karyawanid, keterangan, jumlah) $sql";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
        mysqli_query($cnit, $droptable01);
        echo $erropesan; exit;
    }
    
    //LUAR KOTA
    $filtertgl = " AND ((DATE_FORMAT(b.periode1,'%Y-%m-%d') between '$periode1' AND '$periode2') OR (DATE_FORMAT(b.periode2,'%Y-%m-%d') between '$periode1' AND '$periode2')) ";
    if (!empty($edivisi) AND ($edivisi <> "*") AND ($edivisi <> "0")) $fdivisi = " AND b.divisi='$edivisi' ";
    $sql = "select 'G' kodeinput,  'LUAR KOTA' pengajuan, b.divisi, b.bulan, b.icabangid, 
        a.idrutin, a.nobrid, a.coa, b.karyawanid, b.keterangan, sum(a.rptotal) rptotal 
        FROM dbmaster.t_brrutin1 a JOIN dbmaster.t_brrutin0 b on a.idrutin=b.idrutin 
        WHERE b.stsnonaktif <> 'Y' AND kode=2 AND ifnull(b.fin,'') <> '' $filtertgl $fdivisi 
        GROUP BY 1,2,3,4,5,6,7,8,9,10";
    
    $query = "INSERT INTO $tmp01 (kodeinput, pengajuan, divisi, tgl, icabangid, idinput, noid, coa4, karyawanid, keterangan, jumlah) $sql";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
        mysqli_query($cnit, $droptable01);
        echo $erropesan; exit;
    }
    
    //CA RUTIN
    $filtertgl = " AND DATE_FORMAT(b.periode,'%Y-%m') between '$pbln1' AND '$pbln2' ";
    if (!empty($edivisi) AND ($edivisi <> "*") AND ($edivisi <> "0")) $fdivisi = " AND b.divisi='$edivisi' ";
    $sql = "select 'H' kodeinput,  'CA RUTIN' pengajuan, b.divisi, b.periode, b.icabangid, 
        a.idca, a.nobrid, a.coa, b.karyawanid, b.keterangan, sum(a.rptotal) rptotal 
        FROM dbmaster.t_ca1 a JOIN dbmaster.t_ca0 b on a.idca=b.idca 
        WHERE b.stsnonaktif <> 'Y' AND jenis_ca='br' AND ifnull(b.fin,'') <> '' $filtertgl $fdivisi 
        GROUP BY 1,2,3,4,5,6,7,8,9,10";
    
    $query = "INSERT INTO $tmp01 (kodeinput, pengajuan, divisi, tgl, icabangid, idinput, noid, coa4, karyawanid, keterangan, jumlah) $sql";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
        mysqli_query($cnit, $droptable01);
        echo $erropesan; exit;
    }
    
    
    //CA LUAR KOTA
    $filtertgl = " AND DATE_FORMAT(b.periode,'%Y-%m') between '$pbln1' AND '$pbln2' ";
    if (!empty($edivisi) AND ($edivisi <> "*") AND ($edivisi <> "0")) $fdivisi = " AND b.divisi='$edivisi' ";
    $sql = "select 'I' kodeinput,  'CA LUAR KOTA' pengajuan, b.divisi, b.periode, b.icabangid, 
        a.idca, a.nobrid, a.coa, b.karyawanid, b.keterangan, sum(a.rptotal) rptotal 
        FROM dbmaster.t_ca1 a JOIN dbmaster.t_ca0 b on a.idca=b.idca 
        WHERE b.stsnonaktif <> 'Y' AND jenis_ca='lk' AND ifnull(b.fin,'') <> '' $filtertgl $fdivisi 
        GROUP BY 1,2,3,4,5,6,7,8,9,10";
    
    $query = "INSERT INTO $tmp01 (kodeinput, pengajuan, divisi, tgl, icabangid, idinput, noid, coa4, karyawanid, keterangan, jumlah) $sql";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
        mysqli_query($cnit, $droptable01);
        echo $erropesan; exit;
    }
    
    //service
    
    
    //sewa
    
    $query = "SELECT a.*, b.NAMA4, b.COA3, c.NAMA3, c.COA2, d.NAMA2, d.COA1, e.NAMA1, d.DIVISI2 DIVISICOA 
        from $tmp01 a 
        LEFT JOIN dbmaster.coa_level4 b on a.coa4=b.COA4
        LEFT JOIN dbmaster.coa_level3 c on b.coa3=c.COA3
        LEFT JOIN dbmaster.coa_level2 d on c.coa2=d.COA2
        LEFT JOIN dbmaster.coa_level1 e on d.coa1=e.COA1";
    
    $query = "CREATE TEMPORARY TABLE $tmp02 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
        mysqli_query($cnit, $droptable01);
        echo $erropesan; exit;
    }
    
    $query = "UPDATE $tmp02 set divisi='zzzz', DIVISICOA='zzzz', coa4='zzzz', coa3='zzzz', coa2='zzzz', coa1='zzzz' WHERE IFNULL(coa4,'')=''";
    mysqli_query($cnit, $query);
    
    $query = "UPDATE $tmp02 set kredit=ifnull(realisasi,0), debit=ifnull(jumlah,0)";
    mysqli_query($cnit, $query);
    
    $query = "UPDATE $tmp02 set saldo=debit-kredit";
    mysqli_query($cnit, $query);
    
    mysqli_query($cnit, $droptable01);
?>
    
    <center><h2><u>TRANSASKI BUDGET REQUEST</u></h2></center>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='50%'>
                <tr><td><b>Periode </b></td><td>:</td><td><?PHP echo "$ptglprint1 s/d. $ptglprint2"; ?></td></tr>
                <tr><td><b>View Date </b></td><td>:</td><td><?PHP echo "$tglnow"; ?></td></tr>
            </table>
        </div>
        <div id="isikanan">
            
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
    
    <?PHP if ($erpttipe=="D") { ?>
    
        <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                <th align="center" nowrap>Divisi</th>
                <th align="center" nowrap>Date</th>
                <th align="center" nowrap>Bukti</th>
                <th align="center" nowrap>KODE</th>
                <th align="center" nowrap>PERKIRAAN</th>
                <th align="center" nowrap>IDINPUT</th>
                <th align="center" nowrap>Dokter</th>
                <th align="center" nowrap>No. Slip</th>
                <th align="center" nowrap>Pengajuan</th>
                <th align="center" nowrap>Keterangan</th>
                <th align="center" nowrap>Realisasi</th>
                <th align="center" nowrap>Jumlah</th>
                <th align="center" nowrap>Realisasi</th>
                <th align="center" nowrap>Selisih</th>

            </thead>
            <tbody>
            <?PHP
                $totaldebit=0;
                $totalkredit=0;
                $totalsaldo=0;
                $query = "select distinct divisi from $tmp02 order by divisi";
                $tampil=mysqli_query($cnit, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pdivisi=$row['divisi'];
                    //echo "<tr>";
                    //echo "<td colspan=13>$pdivisi</td>";
                    //echo "</tr>";
                    $totperdivd=0;
                    $totperdivk=0;
                    $totperdivs=0;

                    $query2 = "select distinct divisi, coa4 from $tmp02 WHERE divisi='$pdivisi' order by divisi, coa4";
                    $tampil2=mysqli_query($cnit, $query2);
                    while ($row2= mysqli_fetch_array($tampil2)) {
                        $pdivisi=$row2['divisi'];
                        $pcoa4=$row2['coa4'];

                        $totpercoad=0;
                        $totpercoak=0;
                        $totpercoas=0;

                        $query3 = "select * from $tmp02 WHERE divisi='$pdivisi' AND coa4='$pcoa4' order by divisi, coa4, pengajuan, tgl";
                        $tampil3=mysqli_query($cnit, $query3);
                        while ($row3= mysqli_fetch_array($tampil3)) {
                            $pdivisi=$row3['divisi'];
                            $mdivisi=$pdivisi;
                            if ($pdivisi=="CAN") $mdivisi = "CANARY";
                            if ($row3['kodeinput']=="E" OR $row3['kodeinput']=="F" OR $row3['kodeinput']=="G" OR $row3['kodeinput']=="H" OR $row3['kodeinput']=="I")
                                $ptgl = date("F Y", strtotime($row3['tgl']));
                            else
                                $ptgl = date("d F Y", strtotime($row3['tgl']));
                            $pbukti=$row3['bukti'];
                            $pcoa4=$row3['coa4'];
                            $pnama4=$row3['NAMA4'];
                            $pidinput=$row3['idinput'];
                            $pdokterid=$row3['iddokter'];
                            $pdokternm=$row3['nmdokter'];
                            $pnoslip=$row3['noslip'];
                            $ppengajuan=$row3['pengajuan'];
                            $pketerangan=$row3['keterangan'];
                            $pnmrealisasi=$row3['nmrealisasi'];

                            $pdebit=number_format($row3['debit'],0,",",",");
                            $pcredit=number_format($row3['kredit'],0,",",",");

                            $psaldo=number_format($row3['saldo'],0,",",",");

                            $totpercoad=$totpercoad+$row3['debit'];
                            $totpercoak=$totpercoak+$row3['kredit'];
                            $totpercoas=$totpercoas+$row3['saldo'];


                            if ($row3['coa4']=="zzzz") {
                                $mdivisi="";
                                $pcoa4="";
                            }

                            if ($ketprint=="excel") {
                                $pidinput="'".$row3['idinput'];
                            }

                            echo "<tr>";
                            echo "<td nowrap>$mdivisi</td>";
                            echo "<td nowrap>$ptgl</td>";
                            echo "<td nowrap>$pbukti</td>";
                            echo "<td nowrap>$pcoa4</td>";
                            echo "<td nowrap>$pnama4</td>";
                            echo "<td nowrap>$pidinput</td>";
                            echo "<td >$pdokternm</td>";
                            echo "<td nowrap>$pnoslip</td>";
                            echo "<td >$ppengajuan</td>";
                            echo "<td>$pketerangan</td>";
                            echo "<td nowrap>$pnmrealisasi</td>";
                            echo "<td nowrap align='right'>$pdebit</td>";
                            echo "<td nowrap align='right'>$pcredit</td>";
                            echo "<td nowrap align='right'>$psaldo</td>";
                            echo "</tr>";
                        }
                        //total per COA
                        $totperdivd=$totperdivd+$totpercoad;
                        $totperdivk=$totperdivk+$totpercoak;
                        $totperdivs=$totperdivs+$totpercoas;

                        $totpercoad=number_format($totpercoad,0,",",",");
                        $totpercoak=number_format($totpercoak,0,",",",");
                        $totpercoas=number_format($totpercoas,0,",",",");

                        echo "<tr>";
                        echo "<td nowrap colspan=3></td>";
                        echo "<td nowrap><b>$pcoa4</b></td>";
                        echo "<td nowrap><b>$pnama4</b></td>";
                        echo "<td nowrap colspan=6></td>";
                        echo "<td nowrap align='right'><b>$totpercoad</b></td>";
                        echo "<td nowrap align='right'><b>$totpercoak</b></td>";
                        echo "<td nowrap align='right'><b>$totpercoas</b></td>";
                        echo "</tr>";

                        $totpercoad=0;
                        $totpercoak=0;
                        $totpercoas=0;

                    }
                    //total per DIVISI
                    $totaldebit=$totaldebit+$totperdivd;
                    $totalkredit=$totalkredit+$totperdivk;
                    $totalsaldo=$totalsaldo+$totperdivs;

                    $totperdivd=number_format($totperdivd,0,",",",");
                    $totperdivk=number_format($totperdivk,0,",",",");
                    $totperdivs=number_format($totperdivs,0,",",",");

                    if (!empty($mdivisi)) {

                        echo "<tr>";
                        echo "<td colspan=11>$mdivisi</td>";
                        echo "<td nowrap align='right'><b>$totperdivd</b></td>";
                        echo "<td nowrap align='right'><b>$totperdivk</b></td>";
                        echo "<td nowrap align='right'><b>$totperdivs</b></td>";
                        echo "</tr>";

                        echo "<tr><td colspan=15></td></tr>";

                    }

                    $totperdivd=0;
                    $totperdivk=0;
                    $totperdivs=0;
                }
                $totaldebit=number_format($totaldebit,0,",",",");
                $totalkredit=number_format($totalkredit,0,",",",");
                $totalsaldo=number_format($totalsaldo,0,",",",");

                echo "<tr><td colspan=14></td></tr>";

                echo "<tr>";
                echo "<td colspan=11><b>GRAND TOTAL</b></td>";
                echo "<td nowrap align='right'><b>$totaldebit</b></td>";
                echo "<td nowrap align='right'><b>$totalkredit</b></td>";
                echo "<td nowrap align='right'><b>$totalsaldo</b></td>";
                echo "</tr>";
            ?>
            </tbody>
        </table>
    
    <?PHP }else{ ?>
    
        <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                <th align="center" nowrap>Divisi</th>
                <th align="center" nowrap>KODE</th>
                <th align="center" nowrap>PERKIRAAN</th>
                <th align="center" nowrap>Jumlah</th>
                <th align="center" nowrap>Realisasi</th>
                <th align="center" nowrap>Selisih</th>

            </thead>
            <tbody>
            <?PHP
                $totaldebit=0;
                $totalkredit=0;
                $totalsaldo=0;
                $query = "select distinct divisi from $tmp02 order by divisi";
                $tampil=mysqli_query($cnit, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pdivisi=$row['divisi'];
                    $totperdivd=0;
                    $totperdivk=0;
                    $totperdivs=0;

                    $query3 = "select DIVISICOA divisi, COA1, NAMA1, COA2, NAMA2, COA3, NAMA3, coa4, NAMA4, 
                            sum(debit) debit, sum(kredit) kredit, sum(debit-kredit) saldo  
                            from $tmp02 WHERE DIVISICOA='$pdivisi' GROUP BY 1,2,3,4,5,6,7,8,9 order by divisi, coa4";
                    $tampil3=mysqli_query($cnit, $query3);
                    while ($row3= mysqli_fetch_array($tampil3)) {
                        $pdivisi=$row3['divisi'];
                        $mdivisi=$pdivisi;
                        if ($pdivisi=="CAN") $mdivisi = "CANARY";
                        
                        $pcoa4=$row3['coa4'];
                        $pnama4=$row3['NAMA4'];
                        
                        if ($row3['coa4']=="zzzz") {
                            $mdivisi="";
                            $pcoa4="";
                        }
                            
                        $pdebit=number_format($row3['debit'],0,",",",");
                        $pcredit=number_format($row3['kredit'],0,",",",");

                        $psaldo=number_format($row3['saldo'],0,",",",");
                        
                        $totperdivd=$totperdivd+$row3['debit'];
                        $totperdivk=$totperdivk+$row3['kredit'];
                        $totperdivs=$totperdivs+$row3['saldo'];
                    
                        echo "<tr>";
                        echo "<td nowrap>$mdivisi</td>";
                        echo "<td nowrap>$pcoa4</td>";
                        echo "<td nowrap>$pnama4</td>";
                        echo "<td nowrap align='right'>$pdebit</td>";
                        echo "<td nowrap align='right'>$pcredit</td>";
                        echo "<td nowrap align='right'>$psaldo</td>";
                        echo "</tr>";
                        
                    }
                    //total per DIVISI
                    $totaldebit=$totaldebit+$totperdivd;
                    $totalkredit=$totalkredit+$totperdivk;
                    $totalsaldo=$totalsaldo+$totperdivs;
                    
                    $totperdivd=number_format($totperdivd,0,",",",");
                    $totperdivk=number_format($totperdivk,0,",",",");
                    $totperdivs=number_format($totperdivs,0,",",",");
                    
                    if (!empty($mdivisi)) {

                        echo "<tr>";
                        echo "<td colspan=3><b>TOTAL $mdivisi</b></td>";
                        echo "<td nowrap align='right'><b>$totperdivd</b></td>";
                        echo "<td nowrap align='right'><b>$totperdivk</b></td>";
                        echo "<td nowrap align='right'><b>$totperdivs</b></td>";
                        echo "</tr>";

                        echo "<tr><td colspan=6></td></tr>";

                    }

                    $totperdivd=0;
                    $totperdivk=0;
                    $totperdivs=0;
                    
                }
                
                $totaldebit=number_format($totaldebit,0,",",",");
                $totalkredit=number_format($totalkredit,0,",",",");
                $totalsaldo=number_format($totalsaldo,0,",",",");

                echo "<tr><td colspan=6></td></tr>";

                echo "<tr>";
                echo "<td colspan=3><b>GRAND TOTAL</b></td>";
                echo "<td nowrap align='right'><b>$totaldebit</b></td>";
                echo "<td nowrap align='right'><b>$totalkredit</b></td>";
                echo "<td nowrap align='right'><b>$totalsaldo</b></td>";
                echo "</tr>";
                
            ?>
            </tbody>
        </table>
                
    <?PHP } ?>
    
    <?PHP
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
    ?>
</body>
</html>