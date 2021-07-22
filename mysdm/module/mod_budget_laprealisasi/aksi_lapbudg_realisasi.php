<?PHP
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REALISASI BUDGET MARKETING.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>

<html>
<head>
    <title>REALISASI BUDGET MARKETING</title>
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
        
        $now=date("mdYhis");
        $tmp01 =" dbtemp.RPTREKBMA01_".$_SESSION['USERID']."_$now ";
        $tmp02 =" dbtemp.RPTREKBMA02_".$_SESSION['USERID']."_$now ";
        $tmp03 =" dbtemp.RPTREKBMA03_".$_SESSION['USERID']."_$now ";
        $tmp04 =" dbtemp.RPTREKBMA04_".$_SESSION['USERID']."_$now ";
        
        $tmp05 =" dbtemp.RPTREKBMA05_".$_SESSION['USERID']."_$now ";
        $tmp06 =" dbtemp.RPTREKBMA06_".$_SESSION['USERID']."_$now ";
        $tmp07 =" dbtemp.RPTREKBMA07_".$_SESSION['USERID']."_$now ";
        $tmp08 =" dbtemp.RPTREKBMA08_".$_SESSION['USERID']."_$now ";
        $tmp09 =" dbtemp.RPTREKBMA09_".$_SESSION['USERID']."_$now ";
        $tmp10 =" dbtemp.RPTREKBMA10_".$_SESSION['USERID']."_$now ";
        $tmp11 =" dbtemp.RPTREKBMA11_".$_SESSION['USERID']."_$now ";

        $tgl02=$_POST['tahun'];
        $tgl01=$_POST['tahun']-2;
        
        $nthn3=$_POST['tahun'];
        $nthn2=$_POST['tahun']-1;
        $nthn1=$_POST['tahun']-2;
        
        $espd = $_POST['radio1'];
        
        
        $query = "select distinct b.idinput, b.divisi, b.nodivisi, a.kodeinput, a.bridinput, b.pilih, b.kodeid, b.subkode from dbmaster.t_suratdana_br1 a "
                . " JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput WHERE "
                . " IFNULL(b.stsnonaktif,'')<>'Y' AND IFNULL(b.nodivisi,'')<>'' ";
        $query = "create TEMPORARY table $tmp10 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        $query = "CREATE INDEX `norm1` ON $tmp10 (idinput,divisi,nodivisi,kodeinput,bridinput, pilih)";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
        $query = "create TEMPORARY table $tmp01 (SELECT * FROM dbmaster.t_budget_realisasi_lap)"; 
        mysqli_query($cnit, $query);
        
        $query = "SELECT
            DATE_FORMAT(bulan,'%Y') tahun,
            a.divisi,
            a.kodeid,
            b.nama,
            SUM(a.jumlah) jumlah,
            SUM(a.ratio) ratio
            FROM
            dbmaster.t_budget_realisasi AS a
            JOIN dbmaster.t_budget_kode AS b ON a.kodeid = b.kodeid 
            WHERE DATE_FORMAT(bulan,'%Y') BETWEEN '$tgl01' AND '$tgl02'";
            $query .= "GROUP BY 1,2,3,4";
        
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnit, $query);
        
        


    //biaya rutin
    $query = "select CAST('' as CHAR(2)) as hapus_nodiv_kosong, b.tgl_fin, b.kode, b.bulan, b.periode1, DATE_FORMAT(b.periode1,'%Y-%m-01') periode, a.idrutin, b.divisi, b.divi, b.karyawanid, b.nama_karyawan, "
            . " b.icabangid, b.areaid, b.icabangid_o, b.areaid_o, "
            . " a.coa, a.nobrid, a.rptotal, "
            . " IFNULL(a.notes,'') as ketdetail, IFNULL(b.keterangan,'') as keterangan, "
            . " a.deskripsi, DATE_FORMAT(a.tgl1,'%d/%m/%Y') as tgl1, DATE_FORMAT(a.tgl2,'%d/%m/%Y') as tgl2, a.qty, FORMAT(a.rp,0,'de_DE') as rp "
            . " from dbmaster.t_brrutin1 a "
            . " JOIN dbmaster.t_brrutin0 b on a.idrutin=b.idrutin WHERE "
            . " IFNULL(b.stsnonaktif,'') <> 'Y' AND IFNULL(b.tgl_fin,'')<>'' AND b.divisi<>'OTC' AND "
            . " YEAR(b.bulan)>='2019' ";
    $query = "create TEMPORARY table $tmp05 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "ALTER table $tmp05 ADD COLUMN idinput BIGINT(20), ADD COLUMN nodivisi VARCHAR(50)";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "CREATE INDEX `norm1` ON $tmp05 (idrutin, divi, kode, divisi, icabangid, areaid, icabangid_o, areaid_o,  karyawanid, nobrid, idinput, nodivisi)";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        $query = "UPDATE $tmp05 a JOIN (select distinct pilih, nodivisi, idinput, bridinput FROM $tmp10 WHERE kodeinput IN ('F', 'I', 'N', 'M')) b on a.idrutin=b.bridinput "
                . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput"; 
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query ="DELETE FROM $tmp05 WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(bulan,'%Y-%m')>='2020-01'";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
    
    $query = "INSERT INTO $tmp02 (tahun, kodeid, divisi, jumlah)
        select YEAR(bulan) tahun, '01' kodeid, divisi, sum(rptotal) jumlah 
        from $tmp05 WHERE kode='1' GROUP BY 1,2,3";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
    
    //biaya luar kota
    $query = "INSERT INTO $tmp02 (tahun, kodeid, divisi, jumlah)
        select YEAR(bulan) tahun, '02' kodeid, divisi, sum(rptotal) jumlah 
        from $tmp05 WHERE kode='2' GROUP BY 1,2,3";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
    
    

    //INSENTIF
    $query = "SELECT CAST(null as DECIMAL(10,0)) as urutan, a.bulan, a.divisi, a.cabang icabangid, b.nama cabang, "
            . " a.jabatan, a.karyawanid, a.nama, a.region, a.jumlah, CAST('' as CHAR(50)) as nodivisi "
            . " FROM fe_ms.incentiveperdivisi a "
            . " LEFT JOIN mkt.icabang b on a.cabang=b.iCabangId WHERE IFNULL(a.jumlah,0)<>0 AND "
            . " DATE_FORMAT(a.bulan,'%Y') BETWEEN '$tgl01' AND '$tgl02' ";    
    $query = "create TEMPORARY table $tmp05 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "ALTER table $tmp05 ADD COLUMN idinput BIGINT(20)";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "CREATE INDEX `norm1` ON $tmp05 (urutan, bulan, divisi, icabangid, idinput)";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
    $query = "INSERT INTO $tmp02 (tahun, kodeid, divisi, jumlah)
        select YEAR(bulan) tahun, '03' kodeid, divisi, sum(jumlah) jumlah 
        from $tmp05 GROUP BY 1,2,3";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
    

    //BR ETHICAL
    
    $query = "select brId, noslip, icabangid, tgl, tgltrans, divprodid, COA4, kode, realisasi1, "
            . " jumlah, jumlah1, jumlah jumlah_asli, jumlah1 as jumlah1_asli, "
            . " aktivitas1, aktivitas2, dokterId, dokter, karyawanId, ccyId, tgltrm, lampiran, ca, "
            . " dpp, ppn_rp, pph_rp, tgl_fp, CAST('' as CHAR(20)) as nobukti "
            . " from hrd.br0 WHERE YEAR(tgltrans)>='2019' AND IFNULL(batal,'')<>'Y' AND IFNULL(retur,'')<>'Y' AND "
            . " brId NOT IN (select DISTINCT IFNULL(brId,'') FROM hrd.br0_reject) AND "
            . " DATE_FORMAT(tgltrans,'%Y') BETWEEN '$tgl01' AND '$tgl02' ";
    $query = "create TEMPORARY table $tmp05 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "CREATE INDEX `norm1` ON $tmp05 (brId,dokterId)";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //via SBY
    $query = "select a.bridinput brId, b.noslip, b.icabangid, b.tgl, a.tgltransfersby tgltrans, b.divprodid, "
            . " b.COA4, b.kode, b.realisasi1, a.jumlah jumlah, a.jumlah jumlah1, a.jumlah jumlah_asli, a.jumlah as jumlah1_asli, "
            . " b.aktivitas1, b.aktivitas2, b.dokterId, b.dokter, b.karyawanId, b.ccyId, b.tgltrm, b.lampiran, b.ca, "
            . " b.dpp, b.ppn_rp, b.pph_rp, b.tgl_fp, "
            . " a.nobukti "
            . " from dbmaster.t_br0_via_sby a JOIN hrd.br0 b on a.bridinput=b.brId "
            . " WHERE YEAR(a.tgltransfersby)>='2019' AND IFNULL(b.batal,'')<>'Y' AND IFNULL(b.retur,'')<>'Y' AND "
            . " a.bridinput NOT IN (select DISTINCT IFNULL(brId,'') FROM hrd.br0_reject) AND "
            . " DATE_FORMAT(a.tgltransfersby,'%Y') BETWEEN '$tgl01' AND '$tgl02' ";
    $query = "create TEMPORARY table $tmp06 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
    
    $query = "CREATE INDEX `norm1` ON $tmp06 (brId,dokterId)";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "DELETE FROM $tmp05 WHERE brId IN (select distinct IFNULL(brId,'') FROM $tmp06)";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
    $query = "INSERT INTO $tmp05 (brId, noslip, icabangid, tgl, tgltrans, divprodid, "
            . " COA4, kode, realisasi1, jumlah, jumlah1, jumlah_asli, jumlah1_asli, "
            . " aktivitas1, aktivitas2, dokterId, dokter, karyawanId, ccyId, tgltrm, lampiran, ca, "
            . " dpp, ppn_rp, pph_rp, tgl_fp, nobukti) "
            . " select brId, noslip, icabangid, tgl, tgltrans, divprodid, "
            . " COA4, kode, realisasi1, jumlah, jumlah1, jumlah_asli, jumlah1_asli, "
            . " aktivitas1, aktivitas2, dokterId, dokter, karyawanId, ccyId, tgltrm, lampiran, ca, "
            . " dpp, ppn_rp, pph_rp, tgl_fp, nobukti "
            . " from $tmp06 ";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    //END via SBY
            
    
    $query = "UPDATE $tmp05 SET jumlah=jumlah1 WHERE IFNULL(jumlah1,0)<>0";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "ALTER table $tmp05 ADD COLUMN idinput BIGINT(20), ADD COLUMN nodivisi VARCHAR(50), ADD COLUMN idinput1 BIGINT(20), ADD COLUMN nodivisi1 VARCHAR(50), ADD COLUMN idinput2 BIGINT(20), ADD COLUMN nodivisi2 VARCHAR(50), ADD COLUMN kodeid_pd INT(4), ADD COLUMN subkode_pd VARCHAR(5), ADD COLUMN pcm VARCHAR(1), ADD COLUMN kasbonsby VARCHAR(1), ADD COLUMN coa_pcm VARCHAR(50), ADD COLUMN nama_coa_pcm VARCHAR(100)";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp05 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp10 WHERE kodeinput IN ('A', 'B', 'C') AND divisi<>'OTC') b on a.brId=b.bridinput "
            . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput, a.nodivisi2=b.nodivisi, a.idinput2=b.idinput, a.kodeid_pd=b.kodeid, a.subkode_pd=b.subkode WHERE b.pilih='Y'"; 
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp05 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp10 WHERE kodeinput IN ('A', 'B', 'C') AND divisi<>'OTC') b on a.brId=b.bridinput "
            . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput, a.nodivisi1=b.nodivisi, a.idinput1=b.idinput, a.kodeid_pd=b.kodeid, a.subkode_pd=b.subkode WHERE b.pilih='N' AND IFNULL(a.nodivisi,'')=''"; 
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp05 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp10 WHERE kodeinput IN ('A', 'B', 'C') AND divisi<>'OTC') b on a.brId=b.bridinput "
            . " SET a.nodivisi1=b.nodivisi, a.idinput1=b.idinput, a.kodeid_pd=b.kodeid, a.subkode_pd=b.subkode WHERE b.pilih='N'"; 
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query ="DELETE FROM $tmp05 WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(tgl,'%Y-%m')>='2020-01'";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
    $filtallakunbreth=""; $filtdcc=""; $filtdss=""; $filtgimicprom=""; $filtiklan=""; $filtsimpo=""; $filtho="";
    $query = "select distinct kodeid, kode_akun from dbmaster.t_budget_kode_d order by kodeid";
    $tampil=mysqli_query($cnit, $query);
    while ($row= mysqli_fetch_array($tampil)) {
        $pkodeidp=$row['kodeid'];
        $pakunkode=$row['kode_akun'];
        
        $filtallakunbreth .="'".$pakunkode."',";
        
        if ($pkodeidp=="04") {
            $filtdss .="'".$pakunkode."',";
        }elseif ($pkodeidp=="05") {
            $filtdcc .="'".$pakunkode."',";
        }elseif ($pkodeidp=="06") {
            $filtgimicprom .="'".$pakunkode."',";
        }elseif ($pkodeidp=="07") {
            $filtiklan .="'".$pakunkode."',";
        }elseif ($pkodeidp=="08") {
            $filtsimpo .="'".$pakunkode."',";
        }elseif ($pkodeidp=="10") {
            $filtho .="'".$pakunkode."',";
        }
    }
    
    if (!empty($filtallakunbreth)) $filtallakunbreth="(".substr($filtallakunbreth, 0, -1).")";
    else $filtallakunbreth="('')";
    
    if (!empty($filtdcc)) $filtdcc="(".substr($filtdcc, 0, -1).")";
    else $filtdcc="('')";
    
    if (!empty($filtdss)) $filtdss="(".substr($filtdss, 0, -1).")";
    else $filtdss="('')";
    
    if (!empty($filtgimicprom)) $filtgimicprom="(".substr($filtgimicprom, 0, -1).")";
    else $filtgimicprom="('')";
    
    if (!empty($filtiklan)) $filtiklan="(".substr($filtiklan, 0, -1).")";
    else $filtiklan="('')";
    
    if (!empty($filtsimpo)) $filtsimpo="(".substr($filtsimpo, 0, -1).")";
    else $filtsimpo="('')";
    
    if (!empty($filtho)) $filtho="(".substr($filtho, 0, -1).")";
    else $filtho="('')";
    
    //echo "$filtdss<br/>$filtdcc<br/>$filtgimicprom<br/>$filtiklan<br/>$filtsimpo<br/>$filtho<br/>All : $filtallakunbreth<br/>";
    
    
    //DSS
    $query = "INSERT INTO $tmp02 (tahun, kodeid, divisi, jumlah) 
             select DATE_FORMAT(tgltrans,'%Y') tahun, '04' kodeid, divprodid, sum(jumlah) jumlah FROM 
             $tmp05 WHERE kode IN $filtdss 
             GROUP BY 1,2,3";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //DCC
    $query = "INSERT INTO $tmp02 (tahun, kodeid, divisi, jumlah) 
             select DATE_FORMAT(tgltrans,'%Y') tahun, '05' kodeid, divprodid, sum(jumlah) jumlah FROM 
             $tmp05 WHERE kode IN $filtdcc 
             GROUP BY 1,2,3";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    //Gimmic GIMIK GIMMIC PROMOSI
    $query = "INSERT INTO $tmp02 (tahun, kodeid, divisi, jumlah) 
             select DATE_FORMAT(tgltrans,'%Y') tahun, '06' kodeid, divprodid, sum(jumlah) jumlah FROM 
             $tmp05 WHERE kode IN $filtgimicprom  
             GROUP BY 1,2,3";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //IKLAN
    $query = "INSERT INTO $tmp02 (tahun, kodeid, divisi, jumlah) 
             select DATE_FORMAT(tgltrans,'%Y') tahun, '07' kodeid, divprodid, sum(jumlah) jumlah FROM 
             $tmp05 WHERE kode IN $filtiklan  
             GROUP BY 1,2,3";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
    
    //SIMPOSIUM
    $query = "INSERT INTO $tmp02 (tahun, kodeid, divisi, jumlah) 
             select DATE_FORMAT(tgltrans,'%Y') tahun, '08' kodeid, divprodid, sum(jumlah) jumlah FROM 
             $tmp05 WHERE kode IN $filtsimpo  
             GROUP BY 1,2,3";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
    
    //HO
    $query = "INSERT INTO $tmp02 (tahun, kodeid, divisi, jumlah) 
             select DATE_FORMAT(tgltrans,'%Y') tahun, '10' kodeid, divprodid, sum(jumlah) jumlah FROM 
             $tmp05 WHERE kode IN $filtho  
             GROUP BY 1,2,3";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
    
        //SELAIN AKUN YANG ADA MASUKAN KE HO
        $query = "INSERT INTO $tmp02 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT(tgltrans,'%Y') tahun, '10' kodeid, divprodid, sum(jumlah) jumlah FROM 
                 $tmp05 WHERE kode NOT IN $filtallakunbreth  AND YEAR(tgltrans)>='2019' 
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
    
    
    
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
    mysqli_query($cnit, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
    
    
    //KAS KECIL
    
    $query = "select CAST('C' as CHAR(1)) as nkode, e.DIVISI2 divprodid, a.periode1 tgltrans, a.kasId, a.kode, b.COA4, a.karyawanid, f.nama nama_karyawan, a.nama pengajuan, a.nobukti,
            a.aktivitas1, a.jumlah, CAST('' as CHAR(2)) as hapus_nodiv_kosong
            FROM hrd.kas a LEFT JOIN dbmaster.posting_coa_kas b on a.kode=b.kodeid
            LEFT JOIN dbmaster.coa_level4 c on b.COA4=c.COA4 LEFT JOIN dbmaster.coa_level3 d on c.COA3=d.COA3
            LEFT JOIN dbmaster.coa_level2 e on e.COA2=d.COA2
            LEFT JOIN hrd.karyawan f on a.karyawanid=f.karyawanId 
            WHERE YEAR(a.periode1)>='2019' AND DATE_FORMAT(a.periode1,'%Y') BETWEEN '$tgl01' AND '$tgl02' ";
    $query = "create TEMPORARY table $tmp05 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "CREATE INDEX `norm1` ON $tmp05 (kasId)";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp05 SET COA4='105-02' WHERE IFNULL(COA4,'')=''";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select CAST('D' as CHAR(1)) as nkode, e.DIVISI2 divisi, a.tgl, a.idkasbon, a.kode, b.COA4, a.karyawanid, f.nama nama_karyawan, a.nama pengajuan, '' as nobukti,
            a.keterangan, a.jumlah, CAST('' as CHAR(2)) as hapus_nodiv_kosong
            FROM dbmaster.t_kasbon a LEFT JOIN dbmaster.posting_coa_kas b on a.kode=b.kodeid
            LEFT JOIN dbmaster.coa_level4 c on b.COA4=c.COA4 LEFT JOIN dbmaster.coa_level3 d on c.COA3=d.COA3
            LEFT JOIN dbmaster.coa_level2 e on e.COA2=d.COA2
            LEFT JOIN hrd.karyawan f on a.karyawanid=f.karyawanId WHERE 
            IFNULL(a.stsnonaktif,'')<>'Y' AND DATE_FORMAT(a.tgl,'%Y') BETWEEN '$tgl01' AND '$tgl02' ";
    $query = "create TEMPORARY table $tmp06 ($query)";
    //mysqli_query($cnit, $query);
    //$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "CREATE INDEX `norm1` ON $tmp06 (idkasbon)";
    //mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp06 SET COA4='105-02' WHERE IFNULL(COA4,'')=''";
    //mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "INSERT INTO $tmp05 (nkode, divprodid, tgltrans, kasId, kode, COA4, karyawanid, nama_karyawan, pengajuan, nobukti, aktivitas1, jumlah)"
            . " select nkode, divisi, tgl, idkasbon, kode, COA4, karyawanid, nama_karyawan, pengajuan, nobukti, keterangan, jumlah from $tmp06";
    //mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
    $query = "ALTER table $tmp05 ADD COLUMN idinput BIGINT(20), ADD COLUMN nodivisi VARCHAR(50)";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp05 a JOIN (select distinct pilih, nodivisi, idinput, bridinput FROM $tmp10 WHERE kodeinput IN ('T', 'K')) b on a.kasId=b.bridinput "
            . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput"; 
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //$query ="UPDATE $tmp05 SET COA4='105-02' WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(tgltrans,'%Y-%m')>='2020-01'";
    $query ="DELETE FROM $tmp05 WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(tgltrans,'%Y-%m')>='2020-01'";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
    $query = "INSERT INTO $tmp02 (tahun, kodeid, divisi, jumlah) 
             select DATE_FORMAT(tgltrans,'%Y') tahun, '11' kodeid, 'HO' divisi, sum(jumlah) as jumlah 
             from $tmp05 
             group by 1,2,3";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
    mysqli_query($cnit, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
    
    
    //KLAIM DISKON CLAIM DISCOUNT
    
    $query = "select DIVISI divprodid, tgl, tgltrans, distid, klaimId, COA4, karyawanid, noslip, "
            . " aktivitas1, realisasi1 nmrealisasi, jumlah, dpp, ppn_rp, pph_rp, tgl_fp, pengajuan divpengajuan "
            . " FROM hrd.klaim WHERE IFNULL(pengajuan,'')<>'OTC' AND YEAR(tgltrans)>='2019' AND "
            . " klaimId not in (SELECT DISTINCT ifnull(klaimId,'') from hrd.klaim_reject) AND "
            . " DATE_FORMAT(tgltrans,'%Y') BETWEEN '$tgl01' AND '$tgl02' ";
    $query = "create TEMPORARY table $tmp05 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "CREATE INDEX `norm1` ON $tmp05 (klaimId)";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "ALTER table $tmp05 ADD COLUMN idinput BIGINT(20), ADD COLUMN nodivisi VARCHAR(50), ADD COLUMN icabangid VARCHAR(10), ADD COLUMN nama_cabang VARCHAR(100)";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp05 a JOIN (select distinct pilih, nodivisi, idinput, bridinput FROM $tmp10 WHERE kodeinput IN ('E') AND divisi='EAGLE') b on a.klaimId=b.bridinput "
            . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput WHERE b.pilih='Y'"; 
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp05 a JOIN (select distinct pilih, nodivisi, idinput, bridinput FROM $tmp10 WHERE kodeinput IN ('E') AND divisi='EAGLE') b on a.klaimId=b.bridinput "
            . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput WHERE b.pilih='N' AND IFNULL(a.nodivisi,'')=''"; 
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    $query ="DELETE FROM $tmp05 WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(tgl,'%Y-%m')>='2020-01'";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "INSERT INTO $tmp02 (tahun, kodeid, divisi, jumlah) 
             select DATE_FORMAT(tgltrans,'%Y') tahun, '09' kodeid, 'EAGLE' divisi, sum(jumlah) as jumlah 
             from $tmp05 
             group by 1,2,3";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
    mysqli_query($cnit, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
    
    
        
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah) FROM $tmp02 b WHERE a.kodeid=b.kodeid AND tahun='$nthn1')");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio1=(select sum(b.ratio) FROM $tmp02 b WHERE a.kodeid=b.kodeid AND tahun='$nthn1')");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah) FROM $tmp02 b WHERE a.kodeid=b.kodeid AND tahun='$nthn2')");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio2=(select sum(b.ratio) FROM $tmp02 b WHERE a.kodeid=b.kodeid AND tahun='$nthn2')");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah) FROM $tmp02 b WHERE a.kodeid=b.kodeid AND tahun='$nthn3')");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio3=(select sum(b.ratio) FROM $tmp02 b WHERE a.kodeid=b.kodeid AND tahun='$nthn3')");
        
        
        mysqli_query($cnit, "CREATE TEMPORARY TABLE $tmp03 (select * from $tmp01)");
        
        
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp03 b WHERE grp=1) WHERE nourut=5");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp03 b WHERE grp=1) WHERE nourut=5");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp03 b WHERE grp=1) WHERE nourut=5");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio1=(select sum(b.ratio1) FROM $tmp03 b WHERE grp=1) WHERE nourut=5");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio2=(select sum(b.ratio2) FROM $tmp03 b WHERE grp=1) WHERE nourut=5");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio3=(select sum(b.ratio3) FROM $tmp03 b WHERE grp=1) WHERE nourut=5");
        
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp03 b WHERE grp=2) WHERE nourut=10");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp03 b WHERE grp=2) WHERE nourut=10");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp03 b WHERE grp=2) WHERE nourut=10");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio1=(select sum(b.ratio1) FROM $tmp03 b WHERE grp=2) WHERE nourut=10");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio2=(select sum(b.ratio2) FROM $tmp03 b WHERE grp=2) WHERE nourut=10");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio3=(select sum(b.ratio3) FROM $tmp03 b WHERE grp=2) WHERE nourut=10");
        
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp03 b WHERE grp=3) WHERE nourut=14");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp03 b WHERE grp=3) WHERE nourut=14");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp03 b WHERE grp=3) WHERE nourut=14");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio1=(select sum(b.ratio1) FROM $tmp03 b WHERE grp=3) WHERE nourut=14");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio2=(select sum(b.ratio2) FROM $tmp03 b WHERE grp=3) WHERE nourut=14");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio3=(select sum(b.ratio3) FROM $tmp03 b WHERE grp=3) WHERE nourut=14");
        
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp03 b WHERE grp=4) WHERE nourut=20");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp03 b WHERE grp=4) WHERE nourut=20");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp03 b WHERE grp=4) WHERE nourut=20");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio1=(select sum(b.ratio1) FROM $tmp03 b WHERE grp=4) WHERE nourut=20");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio2=(select sum(b.ratio2) FROM $tmp03 b WHERE grp=4) WHERE nourut=20");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio3=(select sum(b.ratio3) FROM $tmp03 b WHERE grp=4) WHERE nourut=20");
        
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp03 b WHERE IFNULL(kodeid,'') <> '' and IFNULL(kodeid,'') not in ('01','02','03')) WHERE nourut=21");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp03 b WHERE IFNULL(kodeid,'') <> '' and IFNULL(kodeid,'') not in ('01','02','03')) WHERE nourut=21");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp03 b WHERE IFNULL(kodeid,'') <> '' and IFNULL(kodeid,'') not in ('01','02','03')) WHERE nourut=21");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio1=(select sum(b.ratio1) FROM $tmp03 b WHERE IFNULL(kodeid,'') <> '') WHERE nourut=21");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio2=(select sum(b.ratio2) FROM $tmp03 b WHERE IFNULL(kodeid,'') <> '') WHERE nourut=21");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio3=(select sum(b.ratio3) FROM $tmp03 b WHERE IFNULL(kodeid,'') <> '') WHERE nourut=21");
        
        
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp03");
        mysqli_query($cnit, "CREATE TEMPORARY TABLE $tmp03 (select * from $tmp01)");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp03 b WHERE nourut in (5,21)) WHERE nourut=22");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp03 b WHERE nourut in (5,21)) WHERE nourut=22");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp03 b WHERE nourut in (5,21)) WHERE nourut=22");
        
        
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp04");
        
        //SALES dari YTD sls
	$query = "select *from dbmaster.mr_sales2 where DATE_FORMAT(tgljual,'%Y')>='2019' AND DATE_FORMAT(tgljual,'%Y') BETWEEN '$tgl01' AND '$tgl02'";
        $query = "create TEMPORARY table $tmp04 ($query)";
        mysqli_query($cnit, $query);
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.hna*b.qty) FROM $tmp04 b WHERE DATE_FORMAT(tgljual,'%Y')='$nthn1') WHERE nourut=23");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.hna*b.qty) FROM $tmp04 b WHERE DATE_FORMAT(tgljual,'%Y')='$nthn2') WHERE nourut=23");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.hna*b.qty) FROM $tmp04 b WHERE DATE_FORMAT(tgljual,'%Y')='$nthn3') WHERE nourut=23");
        
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp03");
        mysqli_query($cnit, "CREATE TEMPORARY TABLE $tmp03 (select * from $tmp01)");
        
        $jsales1=0;
        $jsales2=0;
        $jsales3=0;
        $query = "select * FROM $tmp01 WHERE nourut=23";
        $tampil=mysqli_query($cnit, $query);
        while ($ro= mysqli_fetch_array($tampil)) {
            $jsales1=$ro['jumlah1'];
            $jsales2=$ro['jumlah2'];
            $jsales3=$ro['jumlah3'];
            if (empty($jsales1)) $jsales1=0;
            if (empty($jsales2)) $jsales2=0;
            if (empty($jsales3)) $jsales3=0;
        }
        if ((DOUBLE)$jsales1>0){
            mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio1=a.jumlah1/$jsales1*100");
        }
        if ((DOUBLE)$jsales2>0){
            mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio2=a.jumlah2/$jsales2*100");
        }
        if ((DOUBLE)$jsales3>0){
            mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio3=a.jumlah3/$jsales3*100");
        }
        
    ?>
    <style>
        .tjudul {
            font-family: Georgia, serif;
            font-size: 15px;
        }
        .tjudul td {
            padding: 4px;
        }
        #datatable2 {
            font-family: Georgia, serif;
        }
        #datatable2 th, #datatable2 td {
            padding: 4px;
        }
        #datatable2 thead{
            background-color:#cccccc; 
            font-size: 15px;
        }
        #datatable2 tbody{
            font-size: 14px;
        }
    </style>
    <?PHP
        echo "<table class='tjudul' width='100%'>";
        echo "<tr> <td width='300px'>REALISASI BUDGET MARKETING THN  $tgl02</td> <td> </td> <td></td> </tr>";
        echo "<tr> <td width='200px'>PT. SURYA DERMATO MEDICA LABORATORIES </td> <td> </td> <td></td> </tr>";
        echo "<tr> <td width='200px'>DIVISI ETHICAL</td> <td> </td> <td></td> </tr>";
        echo "</table>";
        echo "<br/>&nbsp;";
      
    
    ?>
    
    <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
        <thead>
            <tr>
            <th align="center" rowspan="2">NO</th>
            <th align="center" rowspan="2">KETERANGAN</th>
            <th align="center" colspan="2">REALISASI <?PHP echo "$nthn1"; ?></th>
            <th align="center" colspan="2">REALISASI <?PHP echo "$nthn2"; ?></th>
            <th align="center" colspan="2">REALISASI <?PHP echo "$nthn3"; ?></th>
            </tr>
            
            <tr>
            <th align="center">(EAGLE+PIGEON+PEACOCK)</th>
            <th align="center">COST <br/>RATIO</th>
            <th align="center">(EAGLE+PIGEON+PEACOCK)</th>
            <th align="center">COST <br/>RATIO</th>
            <th align="center">(EAGLE+PIGEON+PEACOCK)</th>
            <th align="center">COST <br/>RATIO</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $query = "select * FROM $tmp01 order by nourut";
            $tampil=mysqli_query($cnit, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $pno=$row['no'];
                $pjudul=$row['keterangan'];
                
                $pjumlah1=$row['jumlah1'];
                $pjumlah2=$row['jumlah2'];
                $pjumlah3=$row['jumlah3'];

                $pratio1=ROUND($row['ratio1'],2);
                $pratio2=ROUND($row['ratio2'],2);
                $pratio3=ROUND($row['ratio3'],2);

                


                $pjumlah1=number_format($pjumlah1,0,",",",");
                $pjumlah2=number_format($pjumlah2,0,",",",");
                $pjumlah3=number_format($pjumlah3,0,",",",");
                
                if ($pjumlah1==0) $pjumlah1="";
                if ($pjumlah2==0) $pjumlah2="";
                if ($pjumlah3==0) $pjumlah3="";
                
                if ($pratio1==0) $pratio1="";
                if ($pratio2==0) $pratio2="";
                if ($pratio3==0) $pratio3="";
                
                echo "<tr>";
                echo "<td nowrap>$pno</td>";
                echo "<td nowrap>$pjudul</td>";

                echo "<td nowrap align='right'>$pjumlah1</td>";
                echo "<td nowrap align='right'>$pratio1</td>";
                echo "<td nowrap align='right'>$pjumlah2</td>";
                echo "<td nowrap align='right'>$pratio2</td>";
                echo "<td nowrap align='right'>$pjumlah3</td>";
                echo "<td nowrap align='right'>$pratio3</td>";
                
                echo "</tr>";
                
                
            }
            
            ?>
        </tbody>
    </table>
    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
    <?PHP
    hapusdata:
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp03");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp04");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp06");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp07");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp08");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp09");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp10");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp11");
        
        mysqli_close($cnit);
    ?>
</body>
</html>