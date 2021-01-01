<?PHP
    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=BIAYA KENDARAAN.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>

<html>
<head>
    <title>Laporan Biaya Kendaraan</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Apr 2019 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <link href="css/laporanbaru.css" rel="stylesheet">
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <?PHP } ?>
        
    <style> .str{ mso-number-format:\@; } </style>
    <style>
        table {
            text-align: left;
            position: relative;
            border-collapse: collapse;
            background-color:#FFFFFF;
        }

        th {
            background: white;
            position: sticky;
            top: 0;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        }

        .th2 {
            background: white;
            position: sticky;
            top: 23;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
            border-top: 1px solid #000;
        }
    </style>
</head>

<body>
    <?PHP
        include "config/koneksimysqli.php";
        include "config/fungsi_combo.php";
        $tgl01=$_POST['bulan1'];
        $periode1= date("Ym", strtotime($tgl01));
        $tgl02=$_POST['bulan2'];
        $periode2= date("Ym", strtotime($tgl02));
        
        $now=date("mdYhis");
        $tmp01 =" dbtemp.RPTREKOTCF01_".$_SESSION['USERID']."_$now ";
        $tmp02 =" dbtemp.RPTREKOTCF02_".$_SESSION['USERID']."_$now ";
        $tmp03 =" dbtemp.RPTREKOTCF03_".$_SESSION['USERID']."_$now ";
        $tmp04 =" dbtemp.RPTREKOTCF04_".$_SESSION['USERID']."_$now ";
        $tmp05 =" dbtemp.RPTREKOTCF05_".$_SESSION['USERID']."_$now ";
        $tmp06 =" dbtemp.RPTREKOTCF06_".$_SESSION['USERID']."_$now ";
        $tmp07 =" dbtemp.RPTREKOTCF07_".$_SESSION['USERID']."_$now ";
        $tmp10 =" dbtemp.RPTREKOTCF10_".$_SESSION['USERID']."_$now ";
        
        
                    /*
                    $query = "select karyawanid, tglawal, nopol from dbmaster.t_kendaraan_pemakai";
                    $query = "create TEMPORARY table $tmp07 ($query)";
                    mysqli_query($cnmy, $query);
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

                        $query = "ALTER table $tmp07 ADD COLUMN noidauto BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY";
                        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                        $query = "CREATE UNIQUE INDEX `unx1` ON $tmp07 (noidauto)";
                        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


                    $query = "select idrutin, karyawanid, bulan, nopol from dbmaster.t_brrutin0";    
                    $query = "create  table $tmp05 ($query)";
                    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

                        $query = "ALTER table $tmp05 ADD COLUMN noidauto BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY";
                        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                        $query = "CREATE UNIQUE INDEX `unx1` ON $tmp05 (noidauto)";
                        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

                        $query = "CREATE INDEX `norm1` ON $tmp05 (idrutin, karyawanid, bulan, nopol)";
                        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

                    $query = "select distinct karyawanid, DATE_FORMAT(tglawal,'%Y%m') bulan, nopol FROM $tmp07 order by 1,2";
                    $tampil=mysqli_query($cnmy, $query);
                    while ($nr= mysqli_fetch_array($tampil)) {
                        $pikryid=$nr['karyawanid'];
                        $pibln=$nr['bulan'];
                        $pinopol=$nr['nopol'];
                        if (!empty($pinopol)) {

                            $query = "UPDATE $tmp05 SET nopol='$pinopol' WHERE DATE_FORMAT(bulan,'%Y%m')>='$pibln' AND karyawanid='$pikryid'";
                            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                        }
                    }


                    goto hapusdata;
                     * 
                     */
        
        
        $query = "select distinct b.idinput, b.divisi, b.nodivisi, a.kodeinput, a.bridinput, b.pilih, b.kodeid, b.subkode from dbmaster.t_suratdana_br1 a "
                . " JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput WHERE "
                . " IFNULL(b.stsnonaktif,'')<>'Y' AND IFNULL(b.nodivisi,'')<>'' AND a.kodeinput IN ('F', 'I', 'N', 'M') ";
        $query = "create TEMPORARY table $tmp10 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
            $query = "CREATE INDEX `norm1` ON $tmp10 (idinput, divisi, nodivisi, kodeinput, bridinput)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
        $query = "select a.karyawanid, a.tglawal, a.nopol, b.merk, b.jenis, c.nama_jenis from dbmaster.t_kendaraan_pemakai a "
                . " LEFT JOIN dbmaster.t_kendaraan b on a.nopol=b.nopol "
                . " LEFT JOIN dbmaster.t_kendaraan_jenis c on b.jenis=c.jenis";
        $query = "create TEMPORARY table $tmp07 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "ALTER table $tmp07 ADD COLUMN noidauto BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            $query = "CREATE UNIQUE INDEX `unx1` ON $tmp07 (noidauto)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        
        
        $query = "select b.tgl_fin, b.nopol, b.kode, b.bulan, b.periode1, DATE_FORMAT(b.periode1,'%Y-%m-01') periode, "
                . " a.idrutin, b.divisi, b.divi, b.karyawanid, b.nama_karyawan krynone, "
                . " b.icabangid, b.areaid, b.icabangid_o, b.areaid_o, "
                . " a.coa, a.nobrid, a.rptotal, "
                . " IFNULL(a.notes,'') as ketdetail, IFNULL(b.keterangan,'') as keterangan, "
                . " a.deskripsi, DATE_FORMAT(a.tgl1,'%d/%m/%Y') as tgl1, DATE_FORMAT(a.tgl2,'%d/%m/%Y') as tgl2, a.qty, FORMAT(a.rp,0,'de_DE') as rp "
                . " from dbmaster.t_brrutin1 a "
                . " JOIN dbmaster.t_brrutin0 b on a.idrutin=b.idrutin WHERE "
                . " IFNULL(b.stsnonaktif,'') <> 'Y' AND IFNULL(a.rptotal,'')<>0 AND "
                . " a.nobrid IN ('01', '02', '03', '08', '', '09', '41', '21', '22', '23', '24') AND "
                . " DATE_FORMAT(b.bulan,'%Y%m') BETWEEN '$periode1' AND '$periode2' ";
        $query = "create TEMPORARY table $tmp05 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
            $query = "CREATE INDEX `norm1` ON $tmp05 (idrutin, nopol, divi, kode, divisi, icabangid, areaid, icabangid_o, areaid_o,  karyawanid, nobrid)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            
        $query = "DELETE FROM $tmp05 WHERE IFNULL(divi,'')<>'OTC' AND ( IFNULL(tgl_fin,'')='' OR IFNULL(tgl_fin,'0000-00-00')='0000-00-00' OR IFNULL(tgl_fin,'0000-00-00 00:00:00')='0000-00-00 00:00:00')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
            $query = "ALTER table $tmp05 ADD COLUMN jenis VARCHAR(50), ADD COLUMN nama_merk VARCHAR(100)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "select distinct karyawanid, DATE_FORMAT(tglawal,'%Y%m') bulan, nopol, nama_jenis, merk FROM $tmp07 order by 1,2";
        $tampil=mysqli_query($cnmy, $query);
        while ($nr= mysqli_fetch_array($tampil)) {
            $pikryid=$nr['karyawanid'];
            $pibln=$nr['bulan'];
            $pinopol=$nr['nopol'];
            $pidjenis=$nr['nama_jenis'];
            $pnmmerk=$nr['merk'];
            if (!empty($pinopol)) {
                
                $query = "UPDATE $tmp05 SET nopol='$pinopol', jenis='$pidjenis', nama_merk='$pnmmerk' WHERE DATE_FORMAT(bulan,'%Y%m')>='$pibln' AND karyawanid='$pikryid'";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            }
        }
        
        /*
            mysqli_query($cnmy, "UPDATE $tmp05 a SET a.nopol=IFNULL((SELECT b.nopol FROM dbmaster.t_kendaraan_pemakai b WHERE "
                    . " a.karyawanid=b.karyawanid AND IFNULL(b.stsnonaktif,'')<>'Y' AND "
                    . " DATE_FORMAT(b.tglawal,'%Y%m')<=DATE_FORMAT(a.bulan,'%Y%m') "
                    . " order by b.tglawal DESC LIMIT 1),'') WHERE IFNULL(nopol,'')=''");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        */
        
        $query = "UPDATE $tmp05 a JOIN dbmaster.posting_coa_rutin b on a.divisi=b.divisi AND a.nobrid=b.nobrid SET a.coa=b.COA4 WHERE IFNULL(a.divisi,'')<>''";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "select a.*, b.jabatanid, h.nama nama_jabatan, IFNULL(g.nama,'') nama_brid, b.nama nama_karyawan, c.nama nama_cabang, d.nama nama_area, "
                . " e.nama nmcabotc, f.nama nmareaotc, CAST('' as CHAR(50)) as nodivisi, "
                . " CAST('' as CHAR(50)) as kodeinput from $tmp05 a "
                . " LEFT JOIN hrd.karyawan b on a.karyawanid=b.karyawanid "
                . " LEFT JOIN MKT.icabang c on a.icabangid=c.iCabangId "
                . " LEFT JOIN MKT.iarea d on a.areaid=d.areaId AND a.icabangid=d.iCabangId "
                . " LEFT JOIN MKT.icabang_o e on a.icabangid_o=e.icabangid_o "
                . " LEFT JOIN MKT.iarea_o f on a.areaid_o=f.areaid_o AND a.icabangid_o=f.icabangid_o "
                . " LEFT JOIN dbmaster.t_brid g ON a.nobrid=g.nobrid "
                . " LEFT JOIN hrd.jabatan h on b.jabatanid=h.jabatanid";
        $query = "create TEMPORARY table $tmp06 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "ALTER table $tmp06 ADD COLUMN idinput BIGINT(20)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
            $query = "CREATE INDEX `norm1` ON $tmp06 (idrutin, divi, kode, divisi, icabangid, areaid, icabangid_o, areaid_o,  karyawanid)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        //OTHER
        $query = "UPDATE $tmp06 SET nama_karyawan=krynone, karyawanid=idrutin WHERE karyawanid IN ('0000002083', '0000002200')";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp06 SET icabangid=icabangid_o, areaid=areaid_o, nama_cabang=nmcabotc, nama_area=nmareaotc WHERE divisi='OTC'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp06 a JOIN (select distinct pilih, nodivisi, idinput, bridinput FROM $tmp10 WHERE kodeinput IN ('F', 'I', 'N', 'M')) b on a.idrutin=b.bridinput "
                . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput"; 
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query ="DELETE FROM $tmp06 WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(bulan,'%Y-%m')>='2020-01'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        // delete yang no polisi nya kosong
        mysqli_query($cnmy, "DELETE FROM $tmp06 WHERE IFNULL(nopol,'')=''");
        // HAPUS SELAIN BENSIN PARKIT TOL SERVICE
        mysqli_query($cnmy, "DELETE FROM $tmp06 WHERE nobrid NOT IN ('01', '02', '03', '08')");
        
        $query = "SELECT distinct karyawanid, nama_karyawan, nama_jabatan, IFNULL(nopol,'') as nopol, jenis, nama_merk FROM $tmp06";
        $query = "create TEMPORARY table $tmp03 ($query)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "ALTER table $tmp03 ADD COLUMN noidauto BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            $query = "CREATE UNIQUE INDEX `unx1` ON $tmp03 (noidauto)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "ALTER table $tmp03 ADD COLUMN icabangid VARCHAR(10), ADD COLUMN nama_cabang VARCHAR(100)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "ALTER TABLE $tmp03 ADD B1 DECIMAL(20,5), ADD B2 DECIMAL (20,2), ADD P1 DECIMAL(20,5), ADD P2 DECIMAL (20,2),"
                . " ADD T1 DECIMAL(20,5), ADD T2 DECIMAL (20,2), ADD S1 DECIMAL(20,5), ADD S2 DECIMAL (20,2),"
                . " ADD J1 DECIMAL(20,5), ADD J2 DECIMAL (20,2)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp03 (karyawanid, nopol, icabangid);";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        //cari cabang takut ada yang double
        $query = "UPDATE $tmp03 a JOIN $tmp06 b on a.karyawanid=b.karyawanid SET a.icabangid=b.icabangid, "
                . " a.nama_cabang=b.nama_cabang WHERE IFNULL(b.nama_cabang,'')<>''";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        // BENSIN
        mysqli_query($cnmy, "UPDATE $tmp03 a SET a.B2=(SELECT sum(b.rptotal) FROM $tmp06 b WHERE a.karyawanid=b.karyawanid AND IFNULL(a.nopol,'')=IFNULL(b.nopol,'') AND b.nobrid IN ('01'))");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        // PARKIR
        mysqli_query($cnmy, "UPDATE $tmp03 a SET a.P2=(SELECT sum(b.rptotal) FROM $tmp06 b WHERE a.karyawanid=b.karyawanid AND IFNULL(a.nopol,'')=IFNULL(b.nopol,'') AND b.nobrid IN ('03'))");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        // TOL
        mysqli_query($cnmy, "UPDATE $tmp03 a SET a.T2=(SELECT sum(b.rptotal) FROM $tmp06 b WHERE a.karyawanid=b.karyawanid AND IFNULL(a.nopol,'')=IFNULL(b.nopol,'') AND b.nobrid IN ('02'))");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        // SERVICE
        mysqli_query($cnmy, "UPDATE $tmp03 a SET a.S2=(SELECT sum(b.rptotal) FROM $tmp06 b WHERE a.karyawanid=b.karyawanid AND IFNULL(a.nopol,'')=IFNULL(b.nopol,'') AND b.nobrid IN ('08'))");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        // JUMLAH
        mysqli_query($cnmy, "UPDATE $tmp03 a SET a.J2=IFNULL(B2,0)+IFNULL(P2,0)+IFNULL(T2,0)+IFNULL(S2,0)");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        //goto hapusdata;
        

        /*
        goto hapusdata;
        
        
        $query = "select
            b.bulan, 
            b.karyawanid,
            b.icabangid,
            b.nopol,
            a.idrutin, a.nobrid, a.coa,
            sum(a.rptotal) rptotal
             FROM dbmaster.t_brrutin1 a 
            JOIN dbmaster.t_brrutin0 b on a.idrutin=b.idrutin
            WHERE IFNULL(b.stsnonaktif,'')<>'Y' AND a.nobrid IN ('01', '02', '03', '08', '', '09', '41', '21', '22', '23', '24') AND 
            IFNULL(b.tgl_fin,'')<>'' AND 
            DATE_FORMAT(bulan,'%Y%m') BETWEEN '$periode1' AND '$periode2'";
        $query .=" GROUP BY 1,2,3,4,5,6";
        //echo "$query";exit;
        $query = "create TEMPORARY table $tmp01 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.nopol=IFNULL((SELECT b.nopol FROM dbmaster.t_kendaraan_pemakai b WHERE "
                . " a.karyawanid=b.karyawanid AND IFNULL(b.stsnonaktif,'')<>'Y' AND "
                . " DATE_FORMAT(b.tglawal,'%Y%m')<=DATE_FORMAT(a.bulan,'%Y%m') "
                . " order by b.tglawal DESC LIMIT 1),'') WHERE IFNULL(nopol,'')=''");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
        
        // delete yang no polisi nya kosong
        mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE IFNULL(nopol,'')=''");
        // HAPUS SELAIN BENSIN PARKIT TOL SERVICE
        mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE nobrid NOT IN ('01', '02', '03', '08')");
        
        $query = "select a.*, d.nama nama_karyawan, b.nama namaid, c.NAMA4
               from $tmp01 a
               JOIN dbmaster.t_brid b on a.nobrid=b.nobrid
               LEFT JOIN dbmaster.coa_level4 c on a.coa=c.COA4
               JOIN hrd.karyawan d on a.karyawanid=d.karyawanId";
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "SELECT distinct karyawanid, nama_karyawan, CAST('' as CHAR(10)) icabangid, CAST('' as CHAR(200)) nama_cabang, nopol FROM $tmp02";
        $query = "create TEMPORARY table $tmp03 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "ALTER TABLE $tmp03 ADD B1 DECIMAL(20,5), ADD B2 DECIMAL (20,2), ADD P1 DECIMAL(20,5), ADD P2 DECIMAL (20,2),"
                . " ADD T1 DECIMAL(20,5), ADD T2 DECIMAL (20,2), ADD S1 DECIMAL(20,5), ADD S2 DECIMAL (20,2),"
                . " ADD J1 DECIMAL(20,5), ADD J2 DECIMAL (20,2)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        //cari cabang takut ada yang double
        mysqli_query($cnmy, "UPDATE $tmp03 a SET a.icabangid=(SELECT b.icabangid FROM $tmp01 b WHERE a.karyawanid=b.karyawanid LIMIT 1)");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        mysqli_query($cnmy, "UPDATE $tmp03 a JOIN MKT.icabang b ON a.icabangid=b.iCabangId SET a.nama_cabang=b.nama");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        // BENSIN
        mysqli_query($cnmy, "UPDATE $tmp03 a SET a.B2=(SELECT sum(b.rptotal) FROM $tmp01 b WHERE a.karyawanid=b.karyawanid AND a.nopol=b.nopol AND b.nobrid IN ('01'))");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        // PARKIR
        mysqli_query($cnmy, "UPDATE $tmp03 a SET a.P2=(SELECT sum(b.rptotal) FROM $tmp01 b WHERE a.karyawanid=b.karyawanid AND a.nopol=b.nopol AND b.nobrid IN ('03'))");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        // TOL
        mysqli_query($cnmy, "UPDATE $tmp03 a SET a.T2=(SELECT sum(b.rptotal) FROM $tmp01 b WHERE a.karyawanid=b.karyawanid AND a.nopol=b.nopol AND b.nobrid IN ('02'))");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        // SERVICE
        mysqli_query($cnmy, "UPDATE $tmp03 a SET a.S2=(SELECT sum(b.rptotal) FROM $tmp01 b WHERE a.karyawanid=b.karyawanid AND a.nopol=b.nopol AND b.nobrid IN ('08'))");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        // JUMLAH
        mysqli_query($cnmy, "UPDATE $tmp03 a SET a.J2=IFNULL(B2,0)+IFNULL(P2,0)+IFNULL(T2,0)+IFNULL(S2,0)");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        //mysqli_query($cnmy, "CREATE TEMPORARY TABLE $tmp04 SELECT * FROM $tmp03");
        
        
        */
        
        $query = "select IFNULL(SUM(B2),0) B2, IFNULL(SUM(P2),0) P2, IFNULL(SUM(T2),0) T2, IFNULL(SUM(S2),0) S2, IFNULL(SUM(J2),0) J2 FROM $tmp03";
        $tampil=mysqli_query($cnmy, $query);
        $ra= mysqli_fetch_array($tampil);
        $ptotbensin=$ra['B2'];
        $ptotparkir=$ra['P2'];
        $ptottol=$ra['T2'];
        $ptotservice=$ra['S2'];
        $ptotal=$ra['J2'];
        
        //echo "B : $ptotbensin, P : $ptotparkir, T : $ptottol, S : $ptotservice, J : $ptotal<br/>";
        
        if ((double)$ptotbensin>0) {
            mysqli_query($cnmy, "UPDATE $tmp03 a SET a.B1=IFNULL(B2,0)/".(double)$ptotbensin."*100");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        }
        
        if ((double)$ptotparkir>0) {
            mysqli_query($cnmy, "UPDATE $tmp03 a SET a.P1=IFNULL(P2,0)/".(double)$ptotparkir."*100");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        }
        
        if ((double)$ptottol>0) {
            mysqli_query($cnmy, "UPDATE $tmp03 a SET a.T1=IFNULL(T2,0)/".(double)$ptottol."*100");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        }
        
        if ((double)$ptotservice>0) {
            mysqli_query($cnmy, "UPDATE $tmp03 a SET a.S1=IFNULL(S2,0)/".(double)$ptotservice."*100");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        }
        
        if ((double)$ptotal>0) {
            mysqli_query($cnmy, "UPDATE $tmp03 a SET a.J1=IFNULL(J2,0)/".(double)$ptotal."*100");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        }
        
        
        echo "<table class='tjudul' width='100%'>";
        echo "<tr> <td width='300px'><b>BIAYA KENDARAAN</b></td> <td> $tgl01 s/d. $tgl02 </td> <td>&nbsp;</td> </tr>";
        //echo "<tr> <td width='200px'>&nbsp; </td> <td> &nbsp; </td> <td>&nbsp;</td> </tr>";
        echo "</table>";
        echo "<br/>&nbsp;";
                
    ?>
    
    <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
        <thead>
            <tr style='background-color:#cccccc; font-size: 13px;'>
            <th align="center">No.</th>
            <th align="center">ID</th>
            <th align="center">Nama</th>
            <th align="center">Jabatan</th>
            <th align="center">Cabang</th>
            <th align="center">No. POLISI</th>
            <th align="center">Jenis/Merk</th>
            <th align="center">%</th>
            <th align="center">BENSIN</th>
            <th align="center">%</th>
            <th align="center">PARKIR</th>
            <th align="center">%</th>
            <th align="center">TOL</th>
            <th align="center">%</th>
            <th align="center">SEVICE</th>
            <th align="center">%</th>
            <th align="center">JUMLAH</th>
            </tr>
        </thead>
        <tbody>
        <?PHP
        
            $no=1;
            $query = "select * FROM $tmp03 order by nama_karyawan, nama_cabang";
            $tampil=mysqli_query($cnmy, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $pidkaryawan = $row['karyawanid'];
                $pnmkaryawan = $row['nama_karyawan'];
                $pnmcabang = $row['nama_cabang'];
                $pnmjabatan = $row['nama_jabatan'];
                $pnopol = $row['nopol'];
                $pnmjenis = $row['jenis'];
                $pnmmerk = $row['nama_merk'];
                
                $pbensin = $row['B2'];
                $pparkir = $row['P2'];
                $ptol = $row['T2'];
                $pservice = $row['S2'];
                $pjumlah = $row['J2'];
                
                $ppersen1 = ROUND($row['B1'],2);
                $ppersen2 = ROUND($row['P1'],2);
                $ppersen3 = ROUND($row['T1'],2);
                $ppersen4 = ROUND($row['S1'],2);
                $ppersen5 = ROUND($row['J1'],2);
                
                if ($_SESSION['IDCARD']=="0000000143") {
                    $pbensin=number_format($pbensin,0,".",".");
                    $pparkir=number_format($pparkir,0,".",".");
                    $ptol=number_format($ptol,0,".",".");
                    $pservice=number_format($pservice,0,".",".");
                    $pjumlah=number_format($pjumlah,0,".",".");
                }else{
                    $pbensin=number_format($pbensin,0,",",",");
                    $pparkir=number_format($pparkir,0,",",",");
                    $ptol=number_format($ptol,0,",",",");
                    $pservice=number_format($pservice,0,",",",");
                    $pjumlah=number_format($pjumlah,0,",",",");
                }
                
                echo "<tr>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap class='str'>$pidkaryawan</td>";
                echo "<td nowrap>$pnmkaryawan</td>";
                echo "<td nowrap>$pnmjabatan</td>";
                echo "<td nowrap>$pnmcabang</td>";
                echo "<td nowrap>$pnopol</td>";
                echo "<td nowrap>$pnmjenis $pnmmerk</td>";
                echo "<td nowrap align='right'>$ppersen1</td>";
                echo "<td nowrap align='right'>$pbensin</td>";
                echo "<td nowrap align='right'>$ppersen2</td>";
                echo "<td nowrap align='right'>$pparkir</td>";
                echo "<td nowrap align='right'>$ppersen3</td>";
                echo "<td nowrap align='right'>$ptol</td>";
                echo "<td nowrap align='right'>$ppersen4</td>";
                echo "<td nowrap align='right'>$pservice</td>";
                echo "<td nowrap align='right'>$ppersen5</td>";
                echo "<td nowrap align='right'>$pjumlah</td>";
                echo "</tr>";


                $no++;
            }
            
            $ppersen1=0;
            $ppersen2=0;
            $ppersen3=0;
            $ppersen4=0;
            $ppersen5=0;
            if ((double)$ptotal>0) {
                $ppersen1=ROUND((double)$ptotbensin/(double)$ptotal*100,2);
                $ppersen2=ROUND((double)$ptotparkir/(double)$ptotal*100,2);
                $ppersen3=ROUND((double)$ptottol/(double)$ptotal*100,2);
                $ppersen4=ROUND((double)$ptotservice/(double)$ptotal*100,2);
                $ppersen5=ROUND((double)$ptotal/(double)$ptotal*100,2);
            }
            if ($_SESSION['IDCARD']=="0000000143") {
                $ptotbensin=number_format($ptotbensin,0,".",".");
                $ptotparkir=number_format($ptotparkir,0,".",".");
                $ptottol=number_format($ptottol,0,".",".");
                $ptotservice=number_format($ptotservice,0,".",".");
                $ptotal=number_format($ptotal,0,".",".");
            }else{
                $ptotbensin=number_format($ptotbensin,0,",",",");
                $ptotparkir=number_format($ptotparkir,0,",",",");
                $ptottol=number_format($ptottol,0,",",",");
                $ptotservice=number_format($ptotservice,0,",",",");
                $ptotal=number_format($ptotal,0,",",",");
            }
            
            echo "<tr>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            
            echo "<td nowrap align='right'><b>$ppersen1</b></td>";
            echo "<td nowrap align='right'><b>$ptotbensin</b></td>";
            echo "<td nowrap align='right'><b>$ppersen2</b></td>";
            echo "<td nowrap align='right'><b>$ptotparkir</b></td>";
            echo "<td nowrap align='right'><b>$ppersen3</b></td>";
            echo "<td nowrap align='right'><b>$ptottol</b></td>";
            echo "<td nowrap align='right'><b>$ppersen4</b></td>";
            echo "<td nowrap align='right'><b>$ptotservice</b></td>";
            echo "<td nowrap align='right'><b>$ppersen5</b></td>";
            echo "<td nowrap align='right'><b>$ptotal</b></td>";
            
            echo "</tr>";
         
        ?>
        </tbody>
    </table>
    
    <?PHP
    hapusdata:
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp05");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp06");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp07");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp10");
        
        mysqli_close($cnmy);
    ?>
</body>
</html>