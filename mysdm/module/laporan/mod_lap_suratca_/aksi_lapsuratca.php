<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=LAPORAN SURAT CA.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>
<html>
<head>
    <title>LAPORAN SURAT CA</title>
<?PHP if ($_GET['ket']!="excel") { ?>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
    <link href="css/laporanbaru.css" rel="stylesheet">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
<?PHP } ?>
    
<style>
    #myBtn {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 30px;
        z-index: 99;
        font-size: 18px;
        border: none;
        outline: none;
        background-color: red;
        color: white;
        cursor: pointer;
        padding: 15px;
        border-radius: 4px;
        opacity: 0.5;
    }

    #myBtn:hover {
        background-color: #555;
    }
</style>

</head>

<body>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?php


    $cnit=$cnmy;
    $date1=$_POST['bulan1'];
    $tgl1= date("Y-m-01", strtotime($date1));
    $bulan= date("Ym", strtotime($date1));
    
    $_SESSION['CLSLKCA']=date("F Y", strtotime($date1));
    
    
    $tglnow = date("d/m/Y");
    $tgl01 = $_POST['bulan1'];
    $periode1 = date("Y-m", strtotime($tgl01));
    $per1 = date("F Y", strtotime($tgl01));
    $tgl_utang_pi_= date('M Y', strtotime('-1 month', strtotime($tgl01)));
    $pperiode_nccx= date('Y-m', strtotime('+1 month', strtotime($tgl01)));
    
    $stsreport = $_POST['sts_rpt'];
    
    
    $tglini = date("d F Y");
    $pbulan = date("F", strtotime($tgl01));
    $periodeygdipilih = date("Y-m-01", strtotime($tgl01));
    $bulanberikutnya = date('Y-m-d', strtotime("+1 months", strtotime($periodeygdipilih)));
    $pbulanberikutnya = date("F", strtotime($bulanberikutnya));
    
    
    include ("module/mod_br_closing_lkca/seleksi_data_lk_ca.php");
    
    
    $query ="select *, CAST('' as CHAR(10)) as atasan1, CAST('' as CHAR(10)) as atasan2, "
            . " CAST('' as CHAR(10)) as atasan3, CAST('' as CHAR(10)) as atasan4, CAST('' as CHAR(5)) as  jabatanid,"
            . " CAST('' as CHAR(10)) as  idatasan "
            . " from $tmp01";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    

    
    // cari atasan sesuai periode
    $query = "SELECT distinct karyawanid, atasan1, atasan2, atasan3, atasan4, jabatanid, '1' as idx from dbmaster.t_brrutin0 WHERE idrutin in (
            select DISTINCT IFNULL(idrutin,'') from $tmp03 WHERE IFNULL(idrutin,'')<>''
            )";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "INSERT INTO $tmp04 (karyawanid, atasan1, atasan2, atasan3, atasan4, jabatanid, idx)
        SELECT distinct karyawanid, atasan1, atasan2, atasan3, atasan4, jabatanid, '2' as idx from dbmaster.t_ca0 WHERE idca in (
            select DISTINCT IFNULL(idca1,'') from $tmp03 WHERE IFNULL(idca1,'')<>'' AND IFNULL(idrutin,'')=''  AND IFNULL(idca2,'')<>'' 
            )";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "INSERT INTO $tmp04 (karyawanid, atasan1, atasan2, atasan3, atasan4, jabatanid, idx)
        SELECT distinct karyawanid, atasan1, atasan2, atasan3, atasan4, jabatanid, '2' as idx from dbmaster.t_ca0 WHERE idca in (
            select DISTINCT IFNULL(idca1,'') from $tmp03 WHERE IFNULL(idca1,'')<>'' AND IFNULL(idrutin,'')=''  AND IFNULL(idca2,'')='' 
            )";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "INSERT INTO $tmp04 (karyawanid, atasan1, atasan2, atasan3, atasan4, jabatanid, idx)
        SELECT distinct karyawanid, atasan1, atasan2, atasan3, atasan4, jabatanid, '3' as idx from dbmaster.t_ca0 WHERE idca in (
            select DISTINCT IFNULL(idca2,'') from $tmp03 WHERE IFNULL(idca2,'')<>'' AND IFNULL(idrutin,'')=''  AND IFNULL(idca1,'')='' 
            )";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    $query = "UPDATE $tmp03 a JOIN $tmp04 b on a.karyawanid=b.karyawanid SET a.atasan1=b.atasan1, a.atasan2=b.atasan2, a.atasan3=b.atasan3, a.atasan4=b.atasan4, a.jabatanid=b.jabatanid";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $nox_1=3;
    $nox_2=4;
    for ($ix=1;$ix<=3;$ix++) {
        //echo "$nox_1 da $nox_2<br/>";
        
        $inmfield_1="atasan".$nox_1;
        $inmfield_2="atasan".$nox_2;
        
        $query = "UPDATE $tmp03 SET $inmfield_1=$inmfield_2 WHERE IFNULL($inmfield_1,'')=''";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $nox_1=$nox_1-1;
        $nox_2=$nox_2-1;
    }
    
    $query = "UPDATE $tmp03 SET idatasan=atasan1 WHERE jabatanid IN ('15')";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp03 SET idatasan=atasan2 WHERE jabatanid IN ('10', '18')";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp03 SET idatasan=atasan3 WHERE jabatanid IN ('08')";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp03 SET idatasan=karyawanid WHERE IFNULL(idatasan,'')=''";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //spv
    $query = "UPDATE $tmp03 SET idatasan=karyawanid WHERE jabatanid IN ('10', '18') AND karyawanid=atasan1";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //dm
    $query = "UPDATE $tmp03 SET idatasan=karyawanid WHERE jabatanid IN ('08') AND karyawanid=atasan2";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    
                    $query = "DROP TEMPORARY TABLE $tmp02";
                    mysqli_query($cnit, $query);
                    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                    
    $query = "SELECT DISTINCT a.karyawanid, a.nama_karyawan, a.saldo, a.ca1, a.ca2, jml_adj, a.idatasan, b.nama nama_atasan "
            . " from $tmp03 a LEFT JOIN hrd.karyawan b on a.idatasan=b.karyawanid";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //goto hapusdata;
    
/*    
    
    
    
    //update atasan 1 jadi atasan 2 untuk yang atasan1 nya kosong
            $query = "UPDATE $tmp03 SET atasan3=atasan4 WHERE jabatanid IN ('20') AND IFNULL(atasan3,'')=''";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "UPDATE $tmp03 SET atasan2=atasan3 WHERE jabatanid IN ('08') AND IFNULL(atasan2,'')=''";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "UPDATE $tmp03 SET atasan1=atasan2 WHERE jabatanid IN ('10', '18') AND IFNULL(atasan1,'')=''";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    //END update atasan 1 jadi atasan 2 untuk yang atasan1 nya kosong
    
            
            
    //cari atasan di tabel karyawan yang tidak ada transaksi
        $query = "select distinct atasan1 atasanada from $tmp03";
        $query = "create TEMPORARY table $tmp05 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "INSERT INTO $tmp05 select distinct atasan2 from $tmp03";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "INSERT INTO $tmp05 select distinct atasan3 from $tmp03";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "INSERT INTO $tmp05 select distinct atasan4 from $tmp03";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
        $query = "INSERT INTO $tmp03 (karyawanid, atasan1, atasan2, atasan3, atasan4, jabatanid)"
                . "select a.karyawanid, b.spv atasan1, b.dm atasan2, b.sm atasan3, b.gsm atasan4, a.jabatanid from 
                hrd.karyawan a LEFT JOIN dbmaster.t_karyawan_posisi b on a.karyawanId=b.karyawanId 
                WHERE a.karyawanid not in (select distinct IFNULL(karyawanId,'') from dbmaster.t_karyawanadmin) AND 
                a.karyawanid not in (select distinct IFNULL(karyawanId,'') from $tmp05) AND "
                . " a.jabatanId IN ('10', '18', '08', '20', '05', '04')";
        
        $query = "INSERT INTO $tmp03 (karyawanid, atasan1, atasan2, atasan3, atasan4, jabatanid)"
                . "select a.karyawanid, b.spv atasan1, b.dm atasan2, b.sm atasan3, b.gsm atasan4, a.jabatanid from 
                hrd.karyawan a LEFT JOIN dbmaster.t_karyawan_posisi b on a.karyawanId=b.karyawanId 
                WHERE a.karyawanid in (select distinct IFNULL(atasanada,'') from $tmp05) ";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    //end cari atasan di tabel karyawan yang tidak ada transaksi
            
            
            
    
                    $query = "DROP TEMPORARY TABLE $tmp04";
                    mysqli_query($cnit, $query);
                    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

                    $query = "DROP TEMPORARY TABLE $tmp02";
                    mysqli_query($cnit, $query);
                    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    
    //buat tabel per atasan
    $query = "select * from $tmp03 WHERE karyawanid='xyz'";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "DELETE FROM $tmp04"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    // END buat tabel per atasan
    
    
    //divisi, idrutin, karyawanid, nama_karyawan, credit, saldo, idca1, idca2, ca1, ca2, nourut, keterangan, sts, tgltrans, nobukti, idinput, jml_adj, atasan2, atasan3, atasan4, jabatanid
    
    // insert yang karyawan 19, 18 SPV
    $query = "INSERT INTO $tmp04 SELECT * FROM $tmp03 WHERE jabatanid IN ('10', '18')";//DM 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    // insert yang atasannya 19, 18 SPV
        $query = "select * from $tmp04";
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "INSERT INTO $tmp04 SELECT * FROM $tmp03 WHERE jabatanid NOT IN ('10', '18') AND "
            . " atasan1 IN (select distinct IFNULL(karyawanid,'') from $tmp02)";//DM 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    // END insert yang atasannya 19, 18 SPV
    
    
    // isi jabatan MR yang tidak ada atasan 1 nya
    
                    $query = "DROP TEMPORARY TABLE $tmp02";
                    mysqli_query($cnit, $query);
                    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
        $query = "select * from $tmp04";
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
            //update dulu atasan1 jadi atasan2
            $query = "UPDATE $tmp03 SET atasan1=atasan2 WHERE jabatanid IN ('15') AND "
                    . " karyawanid NOT IN (select distinct IFNULL(karyawanid,'') from $tmp02)";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "INSERT INTO $tmp04 SELECT * FROM $tmp03 WHERE jabatanid IN ('15') AND "
            . " karyawanid NOT IN (select distinct IFNULL(karyawanid,'') from $tmp02)";//DM 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    // END isi jabatan MR yang tidak ada atasan 1 nya
                    
          
    
    //DM
                    $query = "DROP TEMPORARY TABLE $tmp02";
                    mysqli_query($cnit, $query);
                    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                    
                    $query = "select * from $tmp04";
                    $query = "create TEMPORARY table $tmp02 ($query)"; 
                    mysqli_query($cnit, $query);
                    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
                    
                    $query = "INSERT INTO $tmp04 SELECT * FROM $tmp03 WHERE jabatanid IN ('08') AND "
                            . " karyawanid NOT IN (select distinct IFNULL(karyawanid,'') from $tmp02)";//DM 
                    mysqli_query($cnit, $query);
                    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                    
                    
    //end DM
    
                    
    //SM
                    $query = "DROP TEMPORARY TABLE $tmp02";
                    mysqli_query($cnit, $query);
                    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                    
                    
                    $query = "select * from $tmp04";
                    $query = "create TEMPORARY table $tmp02 ($query)"; 
                    mysqli_query($cnit, $query);
                    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                    
                    $query = "INSERT INTO $tmp04 SELECT * FROM $tmp03 WHERE jabatanid IN ('20') AND "
                            . " karyawanid NOT IN (select distinct IFNULL(karyawanid,'') from $tmp02)";//DM 
                    mysqli_query($cnit, $query);
                    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                    
    // END SM
    
                    
                    
    //sama sekali belum masuk
                    $query = "DROP TEMPORARY TABLE $tmp02";
                    mysqli_query($cnit, $query);
                    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                    
                    
                    $query = "select * from $tmp04";
                    $query = "create TEMPORARY table $tmp02 ($query)"; 
                    mysqli_query($cnit, $query);
                    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                    
                    $query = "INSERT INTO $tmp04 SELECT * FROM $tmp03 WHERE 1=1 AND "
                            . " karyawanid NOT IN (select distinct IFNULL(karyawanid,'') from $tmp02)";//DM 
                    mysqli_query($cnit, $query);
                    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                    
    // END sama sekali belum masuk
                    
                    
                    
                    
                    
                    
    //cek data belum masuk
                    $query = "select * from $tmp03 WHERE karyawanid not in (select distinct karyawanid from $tmp04)";
                    $query = "create  table $tmp02 ($query)"; 
                    ///mysqli_query($cnit, $query);
                    //$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    //END cek data belum masuk                
                    
                    
                    
                    
    //update atasan
    
                    $query = "DROP TEMPORARY TABLE $tmp02";
                    mysqli_query($cnit, $query);
                    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query = "SELECT DISTINCT a.karyawanid, a.nama_karyawan, a.saldo, a.ca1, a.ca2, jml_adj, IFNULL(a.atasan1,'') idatasan, b.nama nama_atasan "
            . " from $tmp04 a LEFT JOIN hrd.karyawan b on a.atasan1=b.karyawanid";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //END update atasan
    
    
    
                //hapus yang tidak ada transaksi anak buah
                    $query = "DROP TEMPORARY TABLE $tmp04";
                    mysqli_query($cnit, $query);
                    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                    
                    $query = "select * from $tmp02";
                    $query = "create TEMPORARY table $tmp04 ($query)"; 
                    mysqli_query($cnit, $query);
                    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                    
                    
                    $query = "DELETE FROM $tmp02 WHERE idatasan NOT IN (select distinct IFNULL(karyawanid,'') from $tmp04)"; 
                    mysqli_query($cnit, $query);
                    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
                //END hapus yang tidak ada transaksi anak buah
                    
                    
                    
*/                    
        
    $pnmatasan="";
    $pidatasan="";
    $gtotca1=0; $gtotjmlsaldo=0; $gtotca2=0; $gtotadj=0; $gtotselisih=0; $gtottrans=0;
    
    $rp_gtotca1=0; $rp_gtotjmlsaldo=0; $rp_gtotca2=0; $rp_gtotadj=0; $rp_gtotselisih=0; $rp_gtottrans=0;
    
    $no=1;
    
?>
    
    <?PHP
    $query = "select distinct idatasan, nama_atasan from $tmp02 order by nama_atasan, idatasan";
    $tampil= mysqli_query($cnit, $query);
    while ($row= mysqli_fetch_array($tampil)) {
        $pidatasan = $row['idatasan'];
        $pnmatasan = $row['nama_atasan'];
    ?>
        <table style="font-size:13px;">
            <tr><td nowrap colspan='2'><b>Kepada Yth :</b></td></tr>
            <tr><td nowrap colspan='2'><b><?PHP echo $pnmatasan; ?></b></td></tr>
            <tr><td nowrap colspan='2'><b>PT SDM-Jakarta</b></td></tr>
            <tr><td nowrap colspan='2'>&nbsp;</td></tr>
            <tr><td nowrap colspan='2'>Hal : Pengiriman Cash Advance</td></tr>
        </table>
        
        <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                    <th align="center" nowrap>No</th>
                    <th align="center" nowrap>Nama</th>
                    <th align="center" nowrap>CA <?PHP echo $per1; ?><br/>Yg Hrs Diprtggjwb kan</th>
                    <th align="center" nowrap>Biaya Luar Kota <?PHP echo $per1; ?></th>
                    <th align="center" nowrap>Saldo <?PHP echo $per1; ?></th>
                    <th align="center" nowrap>CA <?PHP echo $per2; ?><br/>Yg Diminta</th>
                    <th align="center" nowrap>CA Yg Dikirim</th>
                    <th align="center" nowrap>CA <?PHP echo $per2; ?><br/>Yg Hrs dipertggjwbkan</th>
                <th align="center" nowrap>Jumlah yg ditransfer ke rek <br/><?PHP echo $pnmatasan; ?></th>
                </tr>
            </thead>
            <tbody>
            <?PHP
                $gtotca1=0; $gtotjmlsaldo=0; $gtotca2=0; $gtotadj=0; $gtotselisih=0; $gtottrans=0;
                
                $no=1;
                $query = "select * from $tmp02 WHERE idatasan='$pidatasan' order by nama_karyawan, karyawanid";
                $tampil_= mysqli_query($cnit, $query);
                while ($row1= mysqli_fetch_array($tampil_)) {
                    $pidkaryawan = $row1['karyawanid'];
                    $pnmkaryawan = $row1['nama_karyawan'];
                    
                    $pjmlca1 = $row1['ca1'];
                    $pjmllk = $row1['saldo'];
                    $pjmlca2 = $row1['ca2'];
                    $pjumlahadj = $row1['jml_adj'];
                    
                    
                    $pselisih=(double)$pjmlca1-(double)$pjmllk;
                    
                    
                    $pjmltrans= ( (double)$pjmlca2-(double)$pselisih ) + (double)$pjumlahadj;
                    //if ((double)$pjmltrans<0) $pjmltrans=0;
                    if ($pselisih>0 AND (double)$pjmlca2==0) $pjmltrans=0;
                    elseif ((double)$pselisih>0 AND (double)$pjmlca2>0) $pjmltrans=(double)$pjmlca2 + (double)$pjumlahadj;
                    elseif ((double)$pselisih==0 AND (double)$pjmlca2>0) $pjmltrans=(double)$pjmlca2 + (double)$pjumlahadj;
                            
                    
                    $gtotca1=(double)$gtotca1+(double)$pjmlca1;
                    $gtotjmlsaldo=(double)$gtotjmlsaldo+(double)$pjmllk;
                    $gtotca2=(double)$gtotca2+(double)$pjmlca2;
                    $gtotadj=(double)$gtotadj+(double)$pjumlahadj;
                    $gtotselisih=(double)$gtotselisih+(double)$pselisih;
                    $gtottrans=(double)$gtottrans+(double)$pjmltrans;
                    
                    $rp_gtotca1=(double)$rp_gtotca1+(double)$pjmlca1;
                    $rp_gtotjmlsaldo=(double)$rp_gtotjmlsaldo+(double)$pjmllk;
                    $rp_gtotca2=(double)$rp_gtotca2+(double)$pjmlca2;
                    $rp_gtotadj=(double)$rp_gtotadj+(double)$pjumlahadj;
                    $rp_gtotselisih=(double)$rp_gtotselisih+(double)$pselisih;
                    $rp_gtottrans=(double)$rp_gtottrans+(double)$pjmltrans;

                            
                    $pjmlca1=number_format($pjmlca1,0,",",",");
                    $pjmllk=number_format($pjmllk,0,",",",");
                    $pjmlca2=number_format($pjmlca2,0,",",",");
                    $pjumlahadj=number_format($pjumlahadj,0,",",",");
                    $pselisih=number_format($pselisih,0,",",",");
                    $pjmltrans=number_format($pjmltrans,0,",",",");
                    
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pnmkaryawan</td>";
                    echo "<td nowrap align='right'>$pjmlca1</td>";
                    echo "<td nowrap align='right'>$pjmllk</td>";
                    echo "<td nowrap align='right'>$pselisih</td>";
                    echo "<td nowrap align='right'>$pjmlca2</td>";
                    echo "<td nowrap align='right'>$pjmltrans</td>";
                    
                    echo "<td nowrap align='right'>$pjmlca2</td>";
                    echo "<td nowrap align='right'>$pjmltrans</td>";
                    echo "</tr>";
                    
                    $no++;
                }
                
                
                $gtotca1=number_format($gtotca1,0,",",",");
                $gtotjmlsaldo=number_format($gtotjmlsaldo,0,",",",");
                $gtotca2=number_format($gtotca2,0,",",",");
                $gtotadj=number_format($gtotadj,0,",",",");
                $gtotselisih=number_format($gtotselisih,0,",",",");
                $gtottrans=number_format($gtottrans,0,",",",");
                    
                echo "<tr style='font-weight:bold;'>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap align='center'>TOTAL</td>";
                echo "<td nowrap align='right'>$gtotca1</td>";
                echo "<td nowrap align='right'>$gtotjmlsaldo</td>";
                echo "<td nowrap align='right'>$gtotselisih</td>";
                echo "<td nowrap align='right'>$gtotca2</td>";
                echo "<td nowrap align='right'>$gtottrans</td>";
                
                echo "<td nowrap align='right'>$gtotca2</td>";
                echo "<td nowrap align='right'>$gtottrans</td>";
                echo "</tr>";
            ?>
            </tbody>
        </table>
        <br/>&nbsp;
    <?PHP
    }
    
    
    ?>
    <table style="font-size:13px;">
        <tr><td nowrap colspan='2'><b>GRAND TOTAL</b></td></tr>
    </table>
    <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
        <thead>
            <tr style='background-color:#cccccc; font-size: 13px;'>
                <th align="center" nowrap>CA <?PHP echo $per1; ?><br/>Yg Hrs Diprtggjwb kan</th>
                <th align="center" nowrap>Biaya Luar Kota <?PHP echo $per1; ?></th>
                <th align="center" nowrap>Saldo <?PHP echo $per1; ?></th>
                <th align="center" nowrap>CA <?PHP echo $per2; ?><br/>Yg Diminta</th>
                <th align="center" nowrap>CA Yg Dikirim</th>
                <th align="center" nowrap>CA <?PHP echo $per2; ?><br/>Yg Hrs dipertggjwbkan</th>
            <th align="center" nowrap>Jumlah yg ditransfer ke rek</th>
            </tr>
        </thead>
        <tbody>
        <?PHP
            $rp_gtotca1=number_format($rp_gtotca1,0,",",",");
            $rp_gtotjmlsaldo=number_format($rp_gtotjmlsaldo,0,",",",");
            $rp_gtotca2=number_format($rp_gtotca2,0,",",",");
            $rp_gtotadj=number_format($rp_gtotadj,0,",",",");
            $rp_gtotselisih=number_format($rp_gtotselisih,0,",",",");
            $rp_gtottrans=number_format($rp_gtottrans,0,",",",");

            echo "<tr style='font-weight:bold;'>";
            echo "<td nowrap align='right'>$rp_gtotca1</td>";
            echo "<td nowrap align='right'>$rp_gtotjmlsaldo</td>";
            echo "<td nowrap align='right'>$rp_gtotselisih</td>";
            echo "<td nowrap align='right'>$rp_gtotca2</td>";
            echo "<td nowrap align='right'>$rp_gtottrans</td>";

            echo "<td nowrap align='right'>$rp_gtotca2</td>";
            echo "<td nowrap align='right'>$rp_gtottrans</td>";
            echo "</tr>";
        ?>
        </tbody>
    </table>
        
        
    <?PHP
    
    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
hapusdata:
    mysqli_query($cnit, "drop temporary table $tmp01");
    mysqli_query($cnit, "drop TEMPORARY table $tmp02");
    mysqli_query($cnit, "drop temporary table $tmp03");
    mysqli_query($cnit, "drop temporary table $tmp04");
    mysqli_query($cnit, "drop temporary table $tmp05");
    mysqli_query($cnit, "drop TEMPORARY table $tmp08");
    
    mysqli_close($cnit);
?>
    <script>
        // When the user scrolls down 20px from the top of the document, show the button
        window.onscroll = function() {scrollFunction()};
        function scrollFunction() {
          if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            document.getElementById("myBtn").style.display = "block";
          } else {
            document.getElementById("myBtn").style.display = "none";
          }
        }

        // When the user clicks on the button, scroll to the top of the document
        function topFunction() {
          document.body.scrollTop = 0;
          document.documentElement.scrollTop = 0;
        }
    </script>
</body>
</html>