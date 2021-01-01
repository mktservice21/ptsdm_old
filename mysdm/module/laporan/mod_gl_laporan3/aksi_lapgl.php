<?PHP
    session_start();
    $rptheader="DETAIL";
    $ketprint=$_GET['ket'];
    if ($ketprint=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REPORT $rptheader GENERAL LEDGER.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/fungsi_sql.php");
    include("config/common.php");
    $cnit=$cnmy;
?>

<html>
<head>
    <title>REPORT <?PHP echo $rptheader; ?>  GENERAL LEDGER</title>
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
    
    $periode1 = date("Y-m-d", strtotime($tgl01));
    
    $pbln1 = date("Y-m", strtotime($tgl01));
    
    $ptglprint1 = date("d F Y", strtotime($tgl01));
    
    
    $espd = $_POST['radio1'];
    $edivisi = $_POST['divprodid'];
    $fdivisi = "";
    
    $milliseconds = round(microtime(true) * 1000);
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DRCOAGLD01_".$_SESSION['USERID']."_$now$milliseconds ";
    $tmp02 =" dbtemp.DRCOAGLD02_".$_SESSION['USERID']."_$now$milliseconds ";
    $tmp03 =" dbtemp.DRCOAGLD03_".$_SESSION['USERID']."_$now$milliseconds ";
    $tmp04 =" dbtemp.DRCOAGLD04_".$_SESSION['USERID']."_$now$milliseconds ";
    $tmp05 =" dbtemp.DRCOAGLD05_".$_SESSION['USERID']."_$now$milliseconds ";
    $tmp06 =" dbtemp.DRCOAGLD06_".$_SESSION['USERID']."_$now$milliseconds ";
    
    
    $query = "CREATE TABLE $tmp01 (kodeinput VARCHAR(2), pengajuan varchar(100), divisi varchar(5), tgl date, icabangid varchar(10), bukti varchar(50), 
        idinputspd INT(20), idinput varchar(20), noid varchar(50), 
        noidnm varchar(150), noidsub varchar(50), noidsubnm varchar(150), 
        coa4 varchar(20), nama4 varchar(200), karyawanid varchar(10), iddokter varchar(10), nmdokter varchar(200), noslip varchar(50),
        keterangan varchar(500), nmrealisasi varchar(200), icabangid_o varchar(10), debit DECIMAL(30,2), kredit DECIMAL(30,2), saldo DECIMAL(30,2),
        jumlah DECIMAL(30,2), realisasi DECIMAL(30,2), cn DECIMAL(30,2)
        )";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "select a.idinput, a.divisi, a.kodeid, a.subkode, a.nomor,
        a.nodivisi, a.tgl, a.jumlah jmlpd, a.coa4, c.NAMA4 nama4,
        b.kodeinput, b.bridinput, b.urutan, b.amount
        from dbmaster.t_suratdana_br a
        left JOIN dbmaster.t_suratdana_br1 b ON a.idinput=b.idinput
        LEFT JOIN dbmaster.coa_level4 c on a.coa4=c.COA4 WHERE IFNULL(a.stsnonaktif,'')<>'Y' AND IFNULL(a.nomor,'')<>'' AND DATE_FORMAT(a.tgl,'%Y-%m')='$pbln1'";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "SELECT * FROM dbmaster.t_suratdana_br_d WHERE idinput IN (select distinct IFNULL(idinput,'') FROM $tmp02)";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    // BR
    $query = "select a.divprodid, a.brId, a.tgl, a.noslip, a.tgltrans, a.karyawanId, c.nama nama_karyawan, a.dokterId, a.dokter, b.nama nama_dokter,
        a.aktivitas1, a.aktivitas2, a.realisasi1, a.jumlah, jumlah1, a.realisasi2, COA4
        from hrd.br0 a 
        LEFT JOIN hrd.dokter b on a.dokterId=b.dokterId
        LEFT JOIN hrd.karyawan c on a.karyawanId=c.karyawanId 
        WHERE a.brid NOT IN (select DISTINCT IFNULL(brId,'') FROM hrd.br0_reject) AND  DATE_FORMAT(a.tgl,'%Y-%m') = '$pbln1' ";
    if ($espd=="S") {
        $query .= " AND a.brId IN (SELECT DISTINCT IFNULL(bridinput,'') FROM $tmp02 WHERE kodeinput IN ('A', 'B', 'C'))";
    }
    $query = "create TEMPORARY table $tmp04 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    //mysqli_query($cnit, "ALTER TABLE $tmp05 ADD idinput INT(20), ADD kodeinput CHAR(1), ADD urutan INT(4), ADD nomor VARCHAR(50), ADD nodivisi VARCHAR(50), ADD amount DECIMAL(20,2)");
    
    
    $query = "select a.*, b.idinput, b.kodeinput, b.urutan, b.amount FROM $tmp04 a LEFT JOIN "
            . " (select * from $tmp02 WHERE kodeinput IN ('A', 'B', 'C')) as b "
            . " ON a.brId=b.bridinput";
    $query = "create TEMPORARY table $tmp05 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    if ($espd=="S") {
        $query = "UPDATE $tmp05 SET jumlah=amount WHERE kodeinput IN ('A', 'B', 'C')";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    /*
    mysqli_query($cnit, "UPDATE $tmp05 a SET a.idinput=(SELECT idinput FROM $tmp02 b WHERE a.brId=b.bridinput LIMIT 1)");
    mysqli_query($cnit, "UPDATE $tmp05 a SET a.kodeinput=(SELECT kodeinput FROM $tmp02 b WHERE a.brId=b.bridinput LIMIT 1)");
    mysqli_query($cnit, "UPDATE $tmp05 a SET a.urutan=(SELECT urutan FROM $tmp02 b WHERE a.brId=b.bridinput LIMIT 1)");
    mysqli_query($cnit, "UPDATE $tmp05 a SET a.amount=(SELECT amount FROM $tmp02 b WHERE a.brId=b.bridinput LIMIT 1)");
    //
    mysqli_query($cnit, "UPDATE $tmp05 a SET a.nomor=(SELECT nomor FROM $tmp02 b WHERE a.idinput=b.idinput LIMIT 1)");
    mysqli_query($cnit, "UPDATE $tmp05 a SET a.nodivisi=(SELECT nodivisi FROM $tmp02 b WHERE a.idinput=b.idinput LIMIT 1)");
    */
    
    $query = "INSERT INTO $tmp01 (idinput, divisi, tgl, bukti, coa4, iddokter, nmdokter, noslip, pengajuan, keterangan, nmrealisasi, kredit, debit)"
            . "SELECT distinct brId, divprodid, tgl, '', COA4, dokterId, nama_dokter, noslip, '' pengajuan, aktivitas1, realisasi1, jumlah, amount FROM $tmp05";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp04");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
    
    
    
    //BR OTC
    $query = "select 'OTC' divprodid, a.brOtcId, a.tglbr, a.noslip, a.tgltrans, 
        a.keterangan1, a.keterangan2, a.real1 nmrealisasi, a.jumlah, realisasi, COA4
        from hrd.br_otc a 
        WHERE a.brOtcId NOT IN (select DISTINCT IFNULL(brOtcId,'') FROM hrd.br_otc_reject) AND  DATE_FORMAT(a.tglbr,'%Y-%m') = '$pbln1'";
    if ($espd=="S") {
        $query .= " AND a.brOtcId IN (SELECT DISTINCT IFNULL(bridinput,'') FROM $tmp02 WHERE kodeinput IN ('D'))";
    }
    $query = "create TEMPORARY table $tmp04 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "select a.*, b.idinput, b.kodeinput, b.urutan, b.amount FROM $tmp04 a LEFT JOIN "
            . " (select * from $tmp02 WHERE kodeinput IN ('D')) as b "
            . " ON a.brOtcId=b.bridinput";
    $query = "create TEMPORARY table $tmp05 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    if ($espd=="S") {
        $query = "UPDATE $tmp05 SET jumlah=amount WHERE kodeinput IN ('D')";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
    $query = "INSERT INTO $tmp01 (idinput, divisi, tgl, bukti, coa4, noslip, pengajuan, keterangan, nmrealisasi, kredit, debit)"
            . "SELECT distinct brOtcId, divprodid, tglbr, '', COA4, noslip, '' pengajuan, keterangan1, nmrealisasi, jumlah, amount FROM $tmp05";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp04");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
    
    
    //KLAIM DIS
    
    $query = "select a.DIVISI divprodid, a.tgl, a.distid, a.klaimId, a.COA4, a.karyawanid, b.nama nama_karyawan, a.noslip,
        a.aktivitas1, a.realisasi1 nmrealisasi, a.jumlah 
        FROM hrd.klaim a LEFT JOIN hrd.karyawan b on a.karyawanid=b.karyawanId 
        WHERE a.klaimId not in (SELECT DISTINCT ifnull(klaimId,'') from hrd.klaim_reject) AND DATE_FORMAT(a.tgl,'%Y-%m') = '$pbln1'";
    if ($espd=="S") {
        $query .= " AND a.klaimId IN (SELECT DISTINCT IFNULL(bridinput,'') FROM $tmp02 WHERE kodeinput IN ('E'))";
    }
    $query = "create TEMPORARY table $tmp04 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "select a.*, b.idinput, b.kodeinput, b.urutan, b.amount FROM $tmp04 a LEFT JOIN "
            . " (select * from $tmp02 WHERE kodeinput IN ('E')) as b "
            . " ON a.klaimId=b.bridinput";
    $query = "create TEMPORARY table $tmp05 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "INSERT INTO $tmp01 (idinput, divisi, tgl, bukti, coa4, noslip, pengajuan, keterangan, nmrealisasi, kredit, debit)"
            . "SELECT distinct klaimId, divprodid, tgl, '', COA4, noslip, '' pengajuan, aktivitas1, nmrealisasi, jumlah, amount FROM $tmp05";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp04");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
    
    
    //KAS
    $query = "select e.DIVISI2 divprodid, a.periode1, a.kasId, a.kode, b.COA4, a.karyawanid, f.nama nama_karyawan, a.nobukti,
            a.aktivitas1, a.jumlah
            FROM hrd.kas a LEFT JOIN dbmaster.posting_coa_kas b on a.kode=b.kodeid
            LEFT JOIN dbmaster.coa_level4 c on b.COA4=c.COA4 LEFT JOIN dbmaster.coa_level3 d on c.COA3=d.COA3
            LEFT JOIN dbmaster.coa_level2 e on e.COA2=d.COA2
            LEFT JOIN hrd.karyawan f on a.karyawanid=f.karyawanId 
            WHERE DATE_FORMAT(a.periode1,'%Y-%m')='$pbln1'";
    if ($espd=="S") {
        $query .= " AND a.kasId IN (SELECT DISTINCT IFNULL(bridinput,'') FROM $tmp02 WHERE kodeinput IN ('K'))";
    }
    $query = "create TEMPORARY table $tmp04 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "select a.*, b.idinput, b.kodeinput, b.urutan, b.amount FROM $tmp04 a LEFT JOIN "
            . " (select * from $tmp02 WHERE kodeinput IN ('K')) as b "
            . " ON a.kasId=b.bridinput";
    $query = "create TEMPORARY table $tmp05 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "INSERT INTO $tmp01 (idinput, divisi, tgl, bukti, coa4, pengajuan, keterangan, kredit, debit)"
            . "SELECT distinct kasId, divprodid, periode1, '', COA4, '' pengajuan, aktivitas1, jumlah, amount FROM $tmp05";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp04");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
    
    
    //RUTIN
    $filtertgl = " AND ((DATE_FORMAT(b.periode1,'%Y-%m') = '$pbln1') OR (DATE_FORMAT(b.periode2,'%Y-%m') = '$pbln1')) ";
    $query = "select DATE_FORMAT(b.periode1,'%Y-%m-01') periode, b.divisi, b.bulan, b.icabangid, 
        a.idrutin, a.nobrid, a.coa, b.karyawanid, c.nama nama_karyawan, b.keterangan, sum(a.rptotal) rptotal 
        FROM dbmaster.t_brrutin1 a JOIN dbmaster.t_brrutin0 b on a.idrutin=b.idrutin 
        LEFT JOIN hrd.karyawan c on b.karyawanid=c.karyawanId 
        WHERE b.stsnonaktif <> 'Y' AND kode=1 AND ifnull(b.fin,'') <> '' $filtertgl  
        ";
    if ($espd=="S") {
        $query .= " AND a.idrutin IN (SELECT DISTINCT IFNULL(bridinput,'') FROM $tmp02 WHERE kodeinput IN ('F'))";
    }
    $query .= " GROUP BY 1,2,3,4,5,6,7,8,9,10";
    $query = "create TEMPORARY table $tmp04 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    mysqli_query($cnit, "UPDATE $tmp04 a SET a.coa=IFNULL((select coa FROM dbmaster.posting_coa_rutin b WHERE a.nobrid=b.nobrid AND a.divisi=b.divisi),a.divisi)");
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "select a.*, b.idinput, b.kodeinput, b.urutan, b.amount FROM $tmp04 a LEFT JOIN "
            . " (select * from $tmp02 WHERE kodeinput IN ('F')) as b "
            . " ON a.idrutin=b.bridinput";
    $query = "create TEMPORARY table $tmp05 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    mysqli_query($cnit, "UPDATE $tmp05 SET amount=rptotal WHERE IFNULL(idinput,'')<>'' AND IFNULL(amount,0) > 0");
    
    $query = "INSERT INTO $tmp01 (kodeinput, idinputspd, noid, idinput, divisi, tgl, bukti, coa4, pengajuan, keterangan, kredit, debit)"
            . "SELECT distinct 'F' kodeinput, nobrid, idinput, idrutin, divisi, periode, '', coa, '' pengajuan, keterangan, rptotal, amount FROM $tmp05";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp04");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
    
    
    //LUAR KOTA BL LK
    //$filtertgl = " AND ((DATE_FORMAT(b.periode1,'%Y-%m') = '$pbln1') OR (DATE_FORMAT(b.periode2,'%Y-%m') = '$pbln1')) ";
    $filtertgl = " AND date_format(b.bulan,'%Y-%m') ='$pbln1' ";
    $query = "select DATE_FORMAT(b.bulan,'%Y-%m-01') periode, b.divisi, b.bulan, b.icabangid, 
        a.idrutin, a.nobrid, a.coa, b.karyawanid, c.nama nama_karyawan, b.keterangan, sum(a.rptotal) rptotal 
        FROM dbmaster.t_brrutin1 a JOIN dbmaster.t_brrutin0 b on a.idrutin=b.idrutin
        LEFT JOIN hrd.karyawan c on b.karyawanid=c.karyawanId 
        WHERE b.stsnonaktif <> 'Y' AND kode=2 AND ifnull(b.fin,'') <> '' $filtertgl 
        ";
    if ($espd=="S") {
        $query .= " AND a.idrutin IN (SELECT DISTINCT IFNULL(bridinput,'') FROM $tmp02 WHERE kodeinput IN ('I'))";
    }
    $query .= " GROUP BY 1,2,3,4,5,6,7,8,9,10";
    $query = "create TEMPORARY table $tmp04 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    mysqli_query($cnit, "UPDATE $tmp04 a SET a.coa=IFNULL((select coa FROM dbmaster.posting_coa_rutin b WHERE a.nobrid=b.nobrid AND a.divisi=b.divisi),a.divisi)");
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "select a.*, b.idinput, b.kodeinput, b.urutan, b.amount FROM $tmp04 a LEFT JOIN "
            . " (select * from $tmp02 WHERE kodeinput IN ('I')) as b "
            . " ON a.idrutin=b.bridinput";
    $query = "create TEMPORARY table $tmp05 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    mysqli_query($cnit, "UPDATE $tmp05 SET amount=rptotal WHERE IFNULL(idinput,'')<>'' AND IFNULL(amount,0) > 0");
    
    $query = "INSERT INTO $tmp01 (kodeinput, idinputspd, noid, idinput, divisi, tgl, bukti, coa4, pengajuan, keterangan, kredit, debit)"
            . "SELECT distinct 'I' kodeinput, idinput, nobrid, idrutin, divisi, periode, '', coa, '' pengajuan, keterangan, rptotal, amount FROM $tmp05";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp04");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
    
    
    mysqli_query($cnit, "UPDATE $tmp01 a SET a.nama4=(SELECT NAMA4 FROM dbmaster.coa_level4 b WHERE a.coa4=b.COA4 LIMIT 1)");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
    
    
    $coadebit = "101-02-002";
    $nmcoadebit = getfield("select NAMA4 as lcfields from dbmaster.coa_level4 WHERE COA4='$coadebit'");
    
?>
    
    <center><h2><u>GENERAL LEDGER</u></h2></center>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='50%'>
                <tr><td><b>Periode </b></td><td>:</td><td><?PHP echo "$ptglprint1"; ?></td></tr>
                <tr><td><b>View Date </b></td><td>:</td><td><?PHP echo "$tglnow"; ?></td></tr>
            </table>
        </div>
        <div id="isikanan">
            
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
    
        <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                <th align="center" nowrap>Divisi</th>
                <th align="center" nowrap>Date</th>
                <th align="center" nowrap>Bukti</th>
                <th align="center" nowrap>KODE</th>
                <th align="center" nowrap>PERKIRAAN</th>
                <th align="center" nowrap>Dokter</th>
                <th align="center" nowrap>No. Slip</th>
                <th align="center" nowrap>Pengajuan</th>
                <th align="center" nowrap>Keterangan</th>
                <th align="center" nowrap>Realisasi</th>
                <th align="center" nowrap>Debit</th>
                <th align="center" nowrap>Credit</th>
                <th align="center" nowrap>Saldo</th>

            </thead>
            <tbody>
                <?PHP
                $gtotdebit=0;
                $gtotcredit=0;
                $gtotsaldo=0;
                $mdivisi="";
                $pidinputspd="";
                
                $query = "select distinct divisi from $tmp01 order by divisi";
                $tampil=mysqli_query($cnit, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pdivisi=$row['divisi'];
                    
                    $divtotdebit=0;
                    $divtotcredit=0;
                    $divtotsaldo=0;
                    
                    $query2 = "select distinct divisi, coa4 from $tmp01 WHERE RTRIM(divisi)='$pdivisi' order by divisi, coa4";
                    $tampil2=mysqli_query($cnit, $query2);
                    while ($row2= mysqli_fetch_array($tampil2)) {
                        $pdivisi=$row2['divisi'];
                        $pcoa4=$row2['coa4'];

                        $coatotdebit=0;
                        $coatotcredit=0;
                        $coatotsaldo=0;
                    
                        $query3 = "select * from $tmp01 WHERE RTRIM(divisi)='$pdivisi' AND RTRIM(coa4)='$pcoa4' order by divisi, coa4, pengajuan, tgl";
                        $tampil3=mysqli_query($cnit, $query3);
                        while ($row3= mysqli_fetch_array($tampil3)) {
                            $pdivisi=$row3['divisi'];
                            $mdivisi=$pdivisi;
                            if ($pdivisi=="CAN") $mdivisi = "CANARY";

                            $ptgl = date("d F Y", strtotime($row3['tgl']));

                            $pkdinput=$row3['kodeinput'];
                            if (strpos($pidinputspd, $row3['idinputspd'])==0) {
                                $pidinputspd=$pidinputspd."'".$row3['idinputspd']."',";
                            }
                            
                            $pbukti=$row3['bukti'];
                            $pcoa4=$row3['coa4'];
                            $pnama4=$row3['nama4'];
                            $pidinput=$row3['idinput'];
                            $pdokterid=$row3['iddokter'];
                            $pdokternm=$row3['nmdokter'];
                            $pnoslip=$row3['noslip'];
                            $ppengajuan=$row3['pengajuan'];
                            $pketerangan=$row3['keterangan'];
                            $pnmrealisasi=$row3['nmrealisasi'];

                            $pdebit=$row3['debit'];
                            $pcredit=$row3['kredit'];
                            if (empty($pdebit)) $pdebit=0;
                            if (empty($pcredit)) $pcredit=0;
                            
                            $coatotdebit=$coatotdebit+$row3['debit'];
                            $coatotcredit=$coatotcredit+$row3['kredit'];

                            $pdebit=number_format($pdebit,0,",",",");
                            $pcredit=number_format($pcredit,0,",",",");


                            echo "<tr>";
                            echo "<td nowrap>$mdivisi</td>";
                            echo "<td nowrap>$ptgl</td>";
                            echo "<td nowrap>$pbukti</td>";
                            echo "<td nowrap>$pcoa4</td>";
                            echo "<td nowrap>$pnama4</td>";

                            echo "<td >$pdokternm</td>";
                            echo "<td nowrap>$pnoslip</td>";
                            echo "<td >$ppengajuan</td>";
                            echo "<td>$pketerangan</td>";
                            echo "<td nowrap>$pnmrealisasi</td>";
                            echo "<td nowrap align='right'></td>";//$pdebit
                            echo "<td nowrap align='right'>$pcredit</td>";
                            echo "<td nowrap align='right'></td>";//$psaldo
                            echo "</tr>";

                        }
                        
                        $divtotdebit=$divtotdebit+$coatotdebit;
                        $divtotcredit=$divtotcredit+$coatotcredit;
                    
                        $coatotdebit=number_format($coatotdebit,0,",",",");
                        $coatotcredit=number_format($coatotcredit,0,",",",");
                        $coatotsaldo=number_format($coatotsaldo,0,",",",");
                        
                        echo "<tr>";
                        echo "<td nowrap><b></b></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap><b>$pcoa4</b></td>";
                        echo "<td nowrap><b>$pnama4</b></td>";

                        echo "<td ></td>";
                        echo "<td nowrap></td>";
                        echo "<td ></td>";
                        echo "<td></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap align='right'><b></b></td>";//$pdebit
                        echo "<td nowrap align='right'><b>$coatotcredit</b></td>";
                        echo "<td nowrap align='right'><b></b></td>";//$psaldo
                        echo "</tr>";
                        

                    }
                    /*
                    if ($pkdinput=="F" OR $pkdinput=="I") {
                        $pidinputspd="(".substr($pidinputspd, 0, -1).")";
                        $jmlspd=0;
                        $query = "select SUM(jumlah) as jumlah from $tmp03 WHERE divisi='$pdivisi' AND idinput IN $pidinputspd";
                        $tampilrw=mysqli_query($cnit, $query);
                        $rw= mysqli_fetch_array($tampilrw);
                        if (!empty($rw['jumlah'])) $jmlspd=$rw['jumlah'];
                        $divtotdebit=$jmlspd;
                    }
                    $pidinputspd = "";
                    */
                    
                    $divtotsaldo=$divtotdebit-$divtotcredit;
                    
                    
                    $gtotdebit=$gtotdebit+$divtotdebit;
                    $gtotcredit=$gtotcredit+$divtotcredit;
                    
                    $divtotdebit=number_format($divtotdebit,0,",",",");
                    $divtotcredit=number_format($divtotcredit,0,",",",");
                    $divtotsaldo=number_format($divtotsaldo,0,",",",");
                    //$coadebit $nmcoadebit
                    
                    echo "<tr>";
                    echo "<td nowrap>$mdivisi</td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap>$coadebit</td>";
                    echo "<td nowrap>$nmcoadebit</td>";

                    echo "<td ></td>";
                    echo "<td nowrap></td>";
                    echo "<td ></td>";
                    echo "<td></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap align='right'><b>$divtotdebit</b></td>";//$pdebit
                    echo "<td nowrap align='right'><b></b></td>";
                    echo "<td nowrap align='right'><b></b></td>";//$psaldo
                    echo "</tr>";
                    
                    echo "<tr>";
                    echo "<td nowrap colspan=2><b>Total $mdivisi</b></td>";
                    //echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";

                    echo "<td ></td>";
                    echo "<td nowrap></td>";
                    echo "<td ></td>";
                    echo "<td></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap align='right'><b>$divtotdebit</b></td>";//$pdebit
                    echo "<td nowrap align='right'><b>$divtotcredit</b></td>";
                    echo "<td nowrap align='right'><b>$divtotsaldo</b></td>";//$psaldo
                    echo "</tr>";
                    echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                    echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                        
                }
                $gtotsaldo=$gtotdebit-$gtotcredit;
                
                $gtotdebit=number_format($gtotdebit,0,",",",");
                $gtotcredit=number_format($gtotcredit,0,",",",");
                $gtotsaldo=number_format($gtotsaldo,0,",",",");
                
                echo "<tr>";
                echo "<td nowrap colspan=2><b>Grand Total </b></td>";
                //echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";

                echo "<td ></td>";
                echo "<td nowrap></td>";
                echo "<td ></td>";
                echo "<td></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap align='right'><b>$gtotdebit</b></td>";//$pdebit
                echo "<td nowrap align='right'><b>$gtotcredit</b></td>";
                echo "<td nowrap align='right'><b>$gtotsaldo</b></td>";//$psaldo
                echo "</tr>";
                ?>
            </tbody>
        </table>
    <?PHP
        mysqli_query($cnit, "DROP TABLE $tmp01");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp03");
        //mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp04");
        //mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
        //mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp06");
        mysqli_close($cnit);
    ?>
</body>
</html>