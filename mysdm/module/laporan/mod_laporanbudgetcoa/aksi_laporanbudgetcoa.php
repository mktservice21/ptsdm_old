<?PHP
    session_start();
    $ketprint=$_GET['ket'];
    if ($ketprint=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=LAPORAN BUDGET REQUEST BY COA.xls");
    }
    
    include("config/koneksimysqli_it.php");
    include("config/common.php");
    
?>
<html>
<head>
    <title>LAPORAN BUDGET REQUEST BY COA</title>
<?PHP if ($ketprint!="excel") { ?>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
    <link href="css/laporanbaru.css" rel="stylesheet">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
<?PHP } ?>
</head>

<body>
<?php
    $tglnow = date("d/m/Y");
    $tgl01 = $_POST['bulan1'];
    $tahunpilih = date("Y", strtotime($tgl01));
    $periode1 = date("Y", strtotime($tgl01));
    
    $per1 = date("Y", strtotime($tgl01));
    
    $edivisi = $_POST['divprodid'];
    
    $fdivisi = "";
    
    $milliseconds = round(microtime(true) * 1000);
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DBRCOA01_".$_SESSION['USERID']."_$now$milliseconds ";
    $tmp02 =" dbtemp.DBRCOA02_".$_SESSION['USERID']."_$now$milliseconds ";
    
    $query = "CREATE TABLE $tmp01 (kodeinput VARCHAR(1), bulan VARCHAR(6), noid VARCHAR(100), kodesdm VARCHAR(100), subkodesdm VARCHAR(100), coa4 VARCHAR(100), cabangid VARCHAR(100), 
        karyawanid VARCHAR(100), divisi VARCHAR(100), jumlah DECIMAL(30,2), realisasi DECIMAL(30,2), cn DECIMAL(30,2))";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $droptable01 = "DROP TABLE $tmp01";
    $droptable02 = "DROP TABLE $tmp02";
    
    if ($edivisi != "OTC") {
        //DCC, DSS & NON
        $filtertgl = " AND DATE_FORMAT(tgl,'%Y')='$periode1' ";
        if (!empty($edivisi) AND ($edivisi <> "*") AND ($edivisi <> "0")) $fdivisi = " AND divprodid='$edivisi' ";
        $sql = "select 'A' kodeinput, DATE_FORMAT(tgl,'%Y%m') bulan, brId, kode, COA4, icabangid, karyawanId, divprodid, jumlah, jumlah1, cn
            FROM hrd.br0 WHERE brId not in (SELECT DISTINCT ifnull(brId,'') from hrd.br0_reject) $filtertgl $fdivisi";

        $query = "INSERT INTO $tmp01 (kodeinput, bulan, noid, kodesdm, coa4, cabangid, karyawanid, divisi, jumlah, realisasi, cn) $sql";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
            mysqli_query($cnit, $droptable01);
            echo $erropesan; exit;
        }
        mysqli_query($cnit, "UPDATE $tmp01 as a set a.coa4=(select b.COA4 from dbmaster.posting_coa_br b WHERE b.kodeid=a.kodesdm AND b.divisi=a.divisi limit 1) WHERE a.kodeinput='A' AND ifnull(a.coa4,'')=''");
        mysqli_query($cnit, "UPDATE $tmp01 as a set a.divisi=(select b.divisi from dbmaster.posting_coa_br b WHERE b.kodeid=a.kodesdm AND b.divisi=a.divisi limit 1) WHERE a.kodeinput='A' AND ifnull(a.divisi,'')=''");
    }
    
    if ($edivisi != "OTC") {
        //KLAIM ==> belum pakai divisi
        $filtertgl = " AND DATE_FORMAT(tgl,'%Y')='$periode1' ";
        $fdivisi = "";
        $sql = "select 'B' kodeinput, DATE_FORMAT(tgl,'%Y%m') bulan, klaimId, COA4, distid, karyawanid, DIVISI, jumlah
            FROM hrd.klaim WHERE klaimId not in (SELECT DISTINCT ifnull(klaimId,'') from hrd.klaim_reject) $filtertgl $fdivisi";

        $query = "INSERT INTO $tmp01 (kodeinput, bulan, noid, coa4, cabangid, karyawanid, divisi, jumlah) $sql";
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
        $filtertgl = " AND DATE_FORMAT(a.periode1,'%Y')='$periode1' ";
        if (!empty($edivisi) AND ($edivisi <> "*") AND ($edivisi <> "0")) $fdivisi = " AND e.DIVISI2='$edivisi' ";
        $sql = "select 	'C' kodeinput, DATE_FORMAT(a.periode1,'%Y%m') bulan, a.kasId, a.kode, b.COA4, a.karyawanid, e.DIVISI2, a.jumlah
                FROM hrd.kas a LEFT JOIN dbmaster.posting_coa_kas b on a.kode=b.kodeid
                LEFT JOIN dbmaster.coa_level4 c on b.COA4=c.COA4 LEFT JOIN dbmaster.coa_level3 d on c.COA3=d.COA3
                LEFT JOIN dbmaster.coa_level2 e on e.COA2=d.COA2 
                WHERE 1=1 $filtertgl $fdivisi";

        $query = "INSERT INTO $tmp01 (kodeinput, bulan, noid, kodesdm, coa4, karyawanid, divisi, jumlah) $sql";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
            mysqli_query($cnit, $droptable01);
            echo $erropesan; exit;
        }
        mysqli_query($cnit, "UPDATE $tmp01 set coa4=null WHERE kodeinput='C' AND coa4=''");
    }
    
    
    if (empty($edivisi) OR ($edivisi == "OTC")) {
        //BR OTC
        $filtertgl = " AND DATE_FORMAT(tglbr,'%Y')='$periode1' ";
        $fdivisi = "";
        $sql = "select 'D' kodeinput, DATE_FORMAT(tglbr,'%Y%m') bulan, brOtcId, subpost, kodeid, COA4, icabangid_o, 'OTC' divisi, jumlah, realisasi 
            FROM hrd.br_otc WHERE brOtcId not in (SELECT DISTINCT ifnull(brOtcId,'') from hrd.br_otc_reject) $filtertgl";
        $query = "INSERT INTO $tmp01 (kodeinput, bulan, noid, kodesdm, subkodesdm, coa4, cabangid, divisi, jumlah, realisasi) $sql";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
            mysqli_query($cnit, $droptable01);
            echo $erropesan; exit;
        }
        mysqli_query($cnit, "UPDATE $tmp01 as a set a.coa4=(select b.COA4 from dbmaster.posting_coa b WHERE CONCAT(b.subpost, b.kodeid)=CONCAT(a.kodesdm, a.subkodesdm) limit 1) WHERE a.kodeinput='D' AND ifnull(a.coa4,'')=''");
        mysqli_query($cnit, "UPDATE $tmp01 as a set a.divisi='OTC' WHERE a.kodeinput='D' AND ifnull(a.divisi,'')=''");
        
    }
    
    //Budget Biaya Rutin Gelondongan COA
    $filtertgl = " AND DATE_FORMAT(periode,'%Y')='$periode1' ";
    if (!empty($edivisi) AND ($edivisi <> "*") AND ($edivisi <> "0")) $fdivisi = " AND divisi='$edivisi' ";
    $sql = "select 'E' kodeinput, DATE_FORMAT(periode,'%Y%m') bulan, idbr, COA4, karyawanid, divisi, icabangid, jumlah
        FROM dbmaster.t_br_bulan WHERE stsnonaktif <> 'Y' $filtertgl $fdivisi";
    
    $query = "INSERT INTO $tmp01 (kodeinput, bulan, noid, coa4, karyawanid, divisi, cabangid, jumlah) $sql";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
        mysqli_query($cnit, $droptable01);
        echo $erropesan; exit;
    }
    
    //CA
    $filtertgl = " AND DATE_FORMAT(b.periode,'%Y')='$periode1' ";
    if (!empty($edivisi) AND ($edivisi <> "*") AND ($edivisi <> "0")) $fdivisi = " AND b.divisi='$edivisi' ";
    $sql = "select 'F' kodeinput, DATE_FORMAT(b.periode,'%Y%m') bulan, a.idca, a.nobrid, a.coa, b.divisi, b.karyawanid, b.icabangid, sum(a.rptotal) rptotal
        FROM dbmaster.t_ca1 a JOIN dbmaster.t_ca0 b on a.idca=b.idca 
        WHERE b.stsnonaktif <> 'Y' AND ifnull(b.fin,'') <> '' $filtertgl $fdivisi 
        GROUP BY 1,2,3,4,5,6,7,8";
    
    $query = "INSERT INTO $tmp01 (kodeinput, bulan, noid, kodesdm, coa4, divisi, karyawanid, cabangid, jumlah) $sql";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
        mysqli_query($cnit, $droptable01);
        echo $erropesan; exit;
    }
    
    //RUTIN
    $filtertgl = " AND DATE_FORMAT(b.bulan,'%Y')='$periode1' ";
    if (!empty($edivisi) AND ($edivisi <> "*") AND ($edivisi <> "0")) $fdivisi = " AND b.divisi='$edivisi' ";
    $sql = "select 'G' kodeinput, DATE_FORMAT(b.bulan,'%Y%m') bulan, a.idrutin, b.kode, a.nobrid, a.coa, b.divisi, b.karyawanid, b.icabangid, sum(a.rptotal) rptotal
        FROM dbmaster.t_brrutin1 a JOIN dbmaster.t_brrutin0 b on a.idrutin=b.idrutin 
        WHERE b.stsnonaktif <> 'Y' AND ifnull(b.fin,'') <> '' $filtertgl $fdivisi 
        GROUP BY 1,2,3,4,5,6,7,8, 9";
    
    $query = "INSERT INTO $tmp01 (kodeinput, bulan, noid, subkodesdm, kodesdm, coa4, divisi, karyawanid, cabangid, jumlah) $sql";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
        mysqli_query($cnit, $droptable01);
        echo $erropesan; exit;
    }
    
    
    //service
    $filtertgl = " AND DATE_FORMAT(tglservice,'%Y')='$periode1' ";
    if (!empty($edivisi) AND ($edivisi <> "*") AND ($edivisi <> "0")) $fdivisi = " AND divisi='$edivisi' ";
    $sql = "select 'H' kodeinput, DATE_FORMAT(tglservice,'%Y%m') bulan, idservice, kode, nobrid, COA4, divisi, karyawanid, icabangid, sum(jumlah) jumlah 
        from dbmaster.t_service_kendaraan WHERE stsnonaktif <> 'Y'  $filtertgl $fdivisi 
        group by 1,2,3,4,5,6,7,8,9";
    $query = "INSERT INTO $tmp01 (kodeinput, bulan, noid, subkodesdm, kodesdm, coa4, divisi, karyawanid, cabangid, jumlah) $sql";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
        mysqli_query($cnit, $droptable01);
        echo $erropesan; exit;
    }
    
    
    //sewa
    $filtertgl = " AND DATE_FORMAT(a.tgl,'%Y')='$periode1' ";
    if (!empty($edivisi) AND ($edivisi <> "*") AND ($edivisi <> "0")) $fdivisi = " AND b.divisi='$edivisi' ";
    $sql = "select 'I' kodeinput, DATE_FORMAT(a.tgl,'%Y%m') bulan, b.idsewa, b.kode, b.nobrid, b.COA4, b.divisi, b.karyawanid, b.icabangid, sum(a.rp) rp 
        from dbmaster.t_sewa1 a JOIN dbmaster.t_sewa b on a.idsewa=b.idsewa 
        WHERE b.stsnonaktif <> 'Y'  $filtertgl $fdivisi 
        group by 1,2,3,4,5,6,7,8,9";
    $query = "INSERT INTO $tmp01 (kodeinput, bulan, noid, subkodesdm, kodesdm, coa4, divisi, karyawanid, cabangid, jumlah) $sql";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
        mysqli_query($cnit, $droptable01);
        echo $erropesan; exit;
    }
    
    $sql = "select a.kodeinput, a.bulan, a.cabangid, b.nama nama_cabang, a.divisi, 
        ifnull(e.DIVISI2, 'ZZZZZ') divisicoa, ifnull(f.COA1, 'Z') coa1, ifnull(f.NAMA1, 'Z') nama1,
        ifnull(e.COA2, 'ZZ') coa2, ifnull(e.NAMA2, 'ZZ') nama2, 
        d.LVL4 lvl4, ifnull(d.COA3, 'ZZZ') coa3, ifnull(d.NAMA3, 'ZZZ') nama3, 
        ifnull(a.coa4, 'ZZZZ') coa4, ifnull(c.NAMA4, 'ZZZZ') nama4, 
        sum(a.jumlah) jumlah, 
        sum(a.realisasi) realisasi, sum(a.cn) cn from $tmp01 a 
        LEFT JOIN MKT.icabang b on a.cabangid=b.iCabangId 
        LEFT JOIN dbmaster.coa_level4 c on a.coa4=c.COA4 
        LEFT JOIN dbmaster.coa_level3 d on c.COA3=d.COA3 
        LEFT JOIN dbmaster.coa_level2 e on d.COA2=e.COA2 
        LEFT JOIN dbmaster.coa_level1 f on e.COA1=f.COA1 
        GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14";
    $query = "CREATE TABLE $tmp02 ($sql)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_query($cnit, $droptable01); echo $erropesan; exit; }
    //hapus temporary tabel 1
    mysqli_query($cnit, $droptable01);
    
    //update divisi asli yang kosong dari COA
    mysqli_query($cnit, "UPDATE $tmp02 set divisi=divisicoa where ifnull(divisi,'')=''");
    //update divisi COA yang kosong dari divisi asli
    mysqli_query($cnit, "UPDATE $tmp02 set divisicoa=divisi where ifnull(divisicoa,'')=''");
    
    //update cabang otc
    $query = "UPDATE $tmp02 a set a.nama_cabang=
        (select b.nama from MKT.icabang_o b where a.cabangid=b.icabangid_o) WHERE a.divisi='OTC'";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_query($cnit, $droptable02); echo $erropesan; exit; }
    
    //update cabang otc kosong
    mysqli_query($cnit, "UPDATE $tmp02 set nama_cabang=cabangid where ifnull(nama_cabang,'')=''");
    
    
    if (!empty($edivisi) AND ($edivisi <> "*") AND ($edivisi <> "0")) $fdivisi = " AND e.DIVISI2='$edivisi' ";
    $tambahfield="";
    for ($x=1;$x<=12;$x++) {
        $tambahfield .="CAST(0 AS DECIMAL(30,2)) as jumlah".$x.",CAST(0 AS DECIMAL(30,2)) as realisasi".$x.",CAST(0 AS DECIMAL(30,2)) as cn".$x.",";
    }
    $tambahfield .="CAST(0 AS DECIMAL(30,2)) as total1, CAST(0 AS DECIMAL(30,2)) as total2, CAST(0 AS DECIMAL(30,2)) as total3";
    $sql = "select DISTINCT e.DIVISI2 divisicoa, f.COA1 coa1, f.NAMA1 nama1,
        e.COA2 coa2, e.NAMA2 nama2, 
        d.LVL4 lvl4, d.COA3 coa3, d.NAMA3 nama3, 
        c.coa4, c.NAMA4 nama4, $tambahfield from 
        dbmaster.coa_level4 c 
        LEFT JOIN dbmaster.coa_level3 d on c.COA3=d.COA3
        LEFT JOIN dbmaster.coa_level2 e on d.COA2=e.COA2
        LEFT JOIN dbmaster.coa_level1 f on e.COA1=f.COA1 WHERE 1=1 $fdivisi ";
    $query = "CREATE TABLE $tmp01 ($sql)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_query($cnit, $droptable02); echo $erropesan; exit; }
    
    mysqli_query($cnit, "UPDATE $tmp01 set divisicoa='ZZZZZZ' WHERE ifnull(divisicoa,'OTHER')='OTHER'");
    mysqli_query($cnit, "UPDATE $tmp02 set divisicoa='ZZZZZZ' WHERE ifnull(divisicoa,'OTHER')='OTHER'");
    
    $query = "INSERT INTO $tmp01 (divisicoa, coa1, coa2, coa3, coa4, nama1, nama2, nama3, nama4, lvl4)
           SELECT DISTINCT divisicoa, coa1, coa2, coa3, coa4, nama1, nama2, nama3, nama4, 'N' from $tmp02 WHERE CONCAT(divisicoa, coa1, coa2, coa3, coa4) not in 
           (SELECT DISTINCT CONCAT(divisicoa, coa1, coa2, coa3, coa4) from $tmp01) ";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_query($cnit, $droptable01); mysqli_query($cnit, $droptable02); echo $erropesan; exit; }
    
    $ibln="01";
    for ($x=1;$x<=12;$x++) {
        $ibln = substr('00'.$x,-2);
        $ithnbln=$tahunpilih."".$ibln;
        $jumlah = "jumlah".$x;
        $realisasi = "realisasi".$x;
        $cn = "cn".$x;
        //jumlah
        $query = "UPDATE $tmp01 a SET a.$jumlah=ifnull((select sum(jumlah) from $tmp02 b WHERE a.coa4=b.coa4 and bulan='$ithnbln'),0)";
        mysqli_query($cnit, $query);
        //realisasi
        $query = "UPDATE $tmp01 a SET a.$realisasi=ifnull((select sum(realisasi) from $tmp02 b WHERE a.coa4=b.coa4 and bulan='$ithnbln'),0)";
        mysqli_query($cnit, $query);
        //cn
        $query = "UPDATE $tmp01 a SET a.$cn=ifnull((select sum(cn) from $tmp02 b WHERE a.coa4=b.coa4 and bulan='$ithnbln'),0)";
        mysqli_query($cnit, $query);
    }
    
    
    //echo "$tmp01</br>$tmp02"; exit;
?>
    <center><h2><u>LAPORAN BUDGET REQUEST BY COA</u></h2></center>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='50%'>
                <tr><td><b>Periode </b></td><td>:</td><td><?PHP echo "$per1"; ?></td></tr>
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
            <th align="center" nowrap>COA</th>
            <th align="center" nowrap>Nama COA</th>
            <th align="center">Januari (Rp.)</th>
            <th align="center">Februari (Rp.)</th>
            <th align="center">Maret (Rp.)</th>
            <th align="center">April (Rp.)</th>
            <th align="center">Mei (Rp.)</th>
            <th align="center">Juni (Rp.)</th>
            <th align="center">Juli (Rp.)</th>
            <th align="center">Agustus (Rp.)</th>
            <th align="center">September (Rp.)</th>
            <th align="center">Oktober (Rp.)</th>
            <th align="center">November (Rp.)</th>
            <th align="center">Desember (Rp.)</th>
        </thead>
        <tbody>
        <?PHP
        $filedpilih="";
        for ($x=1;$x<=12;$x++) {
            $filedpilih .="SUM(jumlah".$x.") as jumlah".$x.",SUM(realisasi".$x.") as realisasi".$x.",SUM(cn".$x.") as cn".$x.",";
        }
        $filedpilih .="SUM(total1) as total1, SUM(total2) as total2, SUM(total3) as total3";


        $query = "SELECT distinct divisicoa, coa1, nama1 FROM $tmp01 ORDER BY divisicoa, coa1";
        $result1 = mysqli_query($cnit, $query);
        $records1 = mysqli_num_rows($result1);
        $row1 = mysqli_fetch_array($result1);
        if ($records1) {
            $reco1 = 1;
            while ($reco1 <= $records1) {
                $divisi = $row1['divisicoa'];
                $nmdivisi = $row1['divisicoa'];
                if ($divisi=="ZZZZZ") $nmdivisi = "OTHER";
                
                if ($ketprint=="excel") {
                    echo "<tr><td><b>$nmdivisi</b></td><td></td>";
                    for ($x=1;$x<=12;$x++) {
                        echo "<td align='right'></td>";
                    }
                    echo "</tr>";
                }else
                    echo "<tr><td colspan=14><b>$nmdivisi</b></td></tr>";

                while (($reco1 <= $records1) AND $row1['divisicoa']==$divisi) {

                    $coa1 = $row1['coa1'];
                    $nmcoa1 = $row1['coa1'];
                    $nama1 = $row1['nama1'];
                    if ($coa1=="Z") { $nmcoa1 = "NONE"; $nama1 = "NONE"; }
                    
                    if ($coa1!="Z") {
                        echo "<tr>";
                        echo "<td nowrap><b>$nmcoa1</b></td>";
                        echo "<td nowrap><b>$nama1</b></td>";
                        for ($x=1;$x<=12;$x++) {
                            echo "<td align='right'></td>";
                        }
                        echo "</tr>";
                    }
                    
                    $row1 = mysqli_fetch_array($result1);
                    $reco1++;
                    
                    //COA2
                    $query = "SELECT divisicoa, coa1, coa2, nama2, $filedpilih  
                        FROM $tmp01 WHERE divisicoa='$divisi' AND coa1='$coa1' GROUP BY 1,2,3,4 ORDER BY divisicoa, coa1, coa2";
                    $result2 = mysqli_query($cnit, $query);
                    $records2 = mysqli_num_rows($result2);
                    $row2 = mysqli_fetch_array($result2);

                    if ($records2) {
                        $reco2 = 1;
                        while ($reco2 <= $records2) {
                            $coa2 = $row2['coa2'];
                            $nmcoa2 = $row2['coa2'];
                            $nama2 = $row2['nama2'];
                            if ($coa2=="ZZ") { $nmcoa2 = "NONE"; $nama2 = "NONE"; }
                            
                            $jumlah=0;

                            echo "<tr style='background-color: #f5f5f5;'>";
                            echo "<td nowrap><b>$nmcoa2</b></td>";
                            echo "<td nowrap><b>$nama2</b></td>";
                            for ($x=1;$x<=12;$x++) {
                                $jumlah=0;
                                $rownya="jumlah".$x;
                                if (isset($row2[$rownya])) $jumlah=number_format($row2[$rownya],0,",",",");
                                echo "<td align='right'>$jumlah</td>";
                            }
                            echo "</tr>";

                            $row2 = mysqli_fetch_array($result2);
                            $reco2++;
                            
                            if ($coa2!="ZZ") {
                                //COA3
                                $query = "SELECT lvl4, divisicoa, coa1, coa2, coa3, nama3, $filedpilih  
                                    FROM $tmp01 WHERE divisicoa='$divisi' AND coa1='$coa1' AND coa2='$coa2' GROUP BY 1,2,3,4,5,6 ORDER BY divisicoa, coa1, coa2, coa3";
                                $result3 = mysqli_query($cnit, $query);
                                $records3 = mysqli_num_rows($result3);
                                $row3 = mysqli_fetch_array($result3);

                                if ($records3) {
                                    $reco3 = 1;
                                    while ($reco3 <= $records3) {
                                        $lvl4 = $row3['lvl4'];
                                        $coa3 = $row3['coa3'];
                                        $nmcoa3 = $row3['coa3'];
                                        $nama3 = $row3['nama3'];
                                        if ($coa3=="ZZZ") { $nmcoa3 = "NONE"; $nama3 = "NONE"; }

                                        $jumlah=0;

                                        if ($lvl4=="XY") { //if ($lvl4!="Y") {
                                            echo "<tr style='background-color: #f5f5f5;'>";
                                            echo "<td nowrap><b>$nmcoa3</b></td>";
                                            echo "<td nowrap><b>$nama3</b></td>";
                                            for ($x=1;$x<=12;$x++) {
                                                $jumlah=0;
                                                $rownya="jumlah".$x;
                                                if (isset($row3[$rownya])) $jumlah=number_format($row3[$rownya],0,",",",");
                                                echo "<td align='right'>$jumlah</td>";
                                            }
                                            echo "</tr>";
                                        }

                                        $row3 = mysqli_fetch_array($result3);
                                        $reco3++;

                                        //COA4
                                        $query = "SELECT divisicoa, coa1, coa2, coa3, coa4, nama4, $filedpilih  
                                            FROM $tmp01 WHERE divisicoa='$divisi' AND coa1='$coa1' AND coa2='$coa2' AND coa3='$coa3' GROUP BY 1,2,3,4,6 ORDER BY divisicoa, coa1, coa2, coa3, coa4";
                                        $result4 = mysqli_query($cnit, $query);
                                        $records4 = mysqli_num_rows($result4);
                                        $row4 = mysqli_fetch_array($result4);

                                        if ($records4) {
                                            $reco4 = 1;
                                            while ($reco4 <= $records4) {
                                                $coa4 = $row4['coa4'];
                                                $nmcoa4 = $row4['coa4'];
                                                $nama4 = $row4['nama4'];
                                                if ($coa4=="ZZZZ") { $nmcoa4 = "NONE"; $nama4 = "NONE"; }

                                                $jumlah=0;

                                                echo "<tr style='background-color: #f5f5f5;'>";
                                                echo "<td nowrap>$nmcoa4</td>";
                                                echo "<td nowrap>$nama4</td>";
                                                for ($x=1;$x<=12;$x++) {
                                                    $jumlah=0;
                                                    $rownya="jumlah".$x;
                                                    if (isset($row4[$rownya])) $jumlah=number_format($row4[$rownya],0,",",",");
                                                    echo "<td align='right'>$jumlah</td>";
                                                }
                                                echo "</tr>";

                                                $row4 = mysqli_fetch_array($result4);
                                                $reco4++;

                                            }
                                        }



                                    }
                                }// level 3
                                
                            }

                        }
                        
                        if ($ketprint!="excel") {
                            echo "<tr>";
                            echo "<td colspan=14></td>";
                            echo "</tr>";
                        }

                    }//level 2
                    
                    
                }

            }
            
            //TOTAL
            $query = "SELECT coa1, nama1, $filedpilih  FROM $tmp01 GROUP BY 1,2 ORDER BY coa1, nama1";
            $result = mysqli_query($cnit, $query);
            $records = mysqli_num_rows($result);
            $row = mysqli_fetch_array($result);

            if ($records) {
                                        
                $reco = 1;
                while ($reco <= $records) {
                    $coa1 = $row['coa1'];
                    $nmcoa1 = $row['coa1'];
                    $nama1 = $row['nama1'];
                    if ($coa1=="Z") { $nmcoa1 = "NONE"; $nama1 = "NONE"; }

                    $jumlah=0;

                    echo "<tr style='background-color: #f5f5f5;'>";
                    echo "<td nowrap colspan=2>TOTAL $nama1</td>";
                    for ($x=1;$x<=12;$x++) {
                        $jumlah=0;
                        $rownya="jumlah".$x;
                        if (isset($row[$rownya])) $jumlah=number_format($row[$rownya],0,",",",");
                        echo "<td align='right'>$jumlah</td>";
                    }
                    echo "</tr>";

                    $row = mysqli_fetch_array($result);
                    $reco++;

                }
                                       
            }
            
                        
            if ($ketprint!="excel") {
                echo "<tr>";
                echo "<td colspan=14></td>";
                echo "</tr>";
            }
            //GRAND TOTAL
            $query = "SELECT CAST(0 as DECIMAL(30,2)) gtot, $filedpilih  FROM $tmp01";
            $result = mysqli_query($cnit, $query);
            $records = mysqli_num_rows($result);
            $row = mysqli_fetch_array($result);

            if ($records) {
                                        
                $reco = 1;
                while ($reco <= $records) {

                    $jumlah=0;

                    echo "<tr style='background-color: #f5f5f5;'>";
                    echo "<td nowrap colspan=2><b>GRAND TOTAL </b></td>";
                    for ($x=1;$x<=12;$x++) {
                        $jumlah=0;
                        $rownya="jumlah".$x;
                        if (isset($row[$rownya])) $jumlah=number_format($row[$rownya],0,",",",");
                        echo "<td align='right'><b>$jumlah</b></td>";
                    }
                    echo "</tr>";

                    $row = mysqli_fetch_array($result);
                    $reco++;

                }
                                       
            }
            
            
        }
        ?>
        </tbody>
    </table>
    <br/>&nbsp;<br/>&nbsp;
    <?PHP
        mysqli_query($cnit, $droptable01);
        mysqli_query($cnit, $droptable02);
    ?>
</body>
</html>
