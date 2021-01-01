<?PHP
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REALISASI BIAYA MARKETING VS BUDGET.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>

<html>
<head>
    <title>REALISASI BIAYA MARKETING VS BUDGET</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Mei 2050 1:00:00 GMT">
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
        $tmp01 =" dbtemp.RPTREKBMABG01_".$_SESSION['USERID']."_$now ";
        $tmp02 =" dbtemp.RPTREKBMABG02_".$_SESSION['USERID']."_$now ";
        $tmp03 =" dbtemp.RPTREKBMABG03_".$_SESSION['USERID']."_$now ";
        $tmp04 =" dbtemp.RPTREKBMABG04_".$_SESSION['USERID']."_$now ";
        $tmp05 =" dbtemp.RPTREKBMABG05_".$_SESSION['USERID']."_$now ";
        $tmp06 =" dbtemp.RPTREKBMABG06_".$_SESSION['USERID']."_$now ";
        $tmp07 =" dbtemp.RPTREKBMABG07_".$_SESSION['USERID']."_$now ";
        $tmp08 =" dbtemp.RPTREKBMABG08_".$_SESSION['USERID']."_$now ";
        $tmp09 =" dbtemp.RPTREKBMABG09_".$_SESSION['USERID']."_$now ";
        $tmp10 =" dbtemp.RPTREKBMABG10_".$_SESSION['USERID']."_$now ";

        
        $tgl1=$_POST['tahun'];
        $pbulan=date("Y-m", strtotime($tgl1));
        $ptahun=date("Y", strtotime($tgl1));
        $pblnthn=date("F Y", strtotime($tgl1));
        
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
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        mysqli_query($cnit, "update $tmp01 set keterangan=keterangan2");
        
        $query = "SELECT
            a.tahun,
            a.g_divisi,
            a.kodeid,
            a.jumlah
            FROM
            dbmaster.t_budget AS a
            WHERE tahun = '$ptahun' AND g_divisi='ETH'";
        
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
        
    //biaya rutin luar kota
    $query = "select CAST('' as CHAR(2)) as hapus_nodiv_kosong, b.tgl_fin, b.kode, b.bulan, b.periode1, DATE_FORMAT(b.periode1,'%Y-%m-01') periode, a.idrutin, b.divisi, b.divi, b.karyawanid, b.nama_karyawan, "
            . " b.icabangid, b.areaid, b.icabangid_o, b.areaid_o, "
            . " a.coa, a.nobrid, a.rptotal, "
            . " IFNULL(a.notes,'') as ketdetail, IFNULL(b.keterangan,'') as keterangan, "
            . " a.deskripsi, DATE_FORMAT(a.tgl1,'%d/%m/%Y') as tgl1, DATE_FORMAT(a.tgl2,'%d/%m/%Y') as tgl2, a.qty, FORMAT(a.rp,0,'de_DE') as rp "
            . " from dbmaster.t_brrutin1 a "
            . " JOIN dbmaster.t_brrutin0 b on a.idrutin=b.idrutin WHERE "
            . " IFNULL(b.stsnonaktif,'') <> 'Y' AND IFNULL(b.tgl_fin,'')<>'' AND b.divisi<>'OTC' AND "
            . " YEAR(b.bulan)='$ptahun' AND DATE_FORMAT(b.bulan,'%Y-%m')<='$pbulan' ";
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
        
        
        $query = "select YEAR(bulan) tahun, '01' kodeid, divisi, sum(rptotal) jumlah 
            from $tmp05 WHERE kode=1 
            GROUP BY 1,2,3";
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        

        //biaya luar kota
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah)
            select YEAR(bulan) tahun, '02' kodeid, divisi, sum(rptotal) jumlah 
            from $tmp05 WHERE kode=2 
            GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        
        
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
    
    

    //INSENTIF
    $query = "SELECT CAST(null as DECIMAL(10,0)) as urutan, a.bulan, a.divisi, a.cabang icabangid, b.nama cabang, "
            . " a.jabatan, a.karyawanid, a.nama, a.region, a.jumlah, CAST('' as CHAR(50)) as nodivisi "
            . " FROM dbmaster.incentiveperdivisi a "
            . " LEFT JOIN mkt.icabang b on a.cabang=b.iCabangId WHERE IFNULL(a.jumlah,0)<>0 AND "
            . " YEAR(a.bulan)='$ptahun' AND DATE_FORMAT(a.bulan,'%Y-%m')<='$pbulan' ";    
    $query = "create TEMPORARY table $tmp05 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "ALTER table $tmp05 ADD COLUMN idinput BIGINT(20)";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "CREATE INDEX `norm1` ON $tmp05 (urutan, bulan, divisi, icabangid, idinput)";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
    $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah)
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
            . " YEAR(tgltrans)='$ptahun' AND DATE_FORMAT(tgltrans,'%Y-%m')<='$pbulan' ";
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
            . " YEAR(a.tgltransfersby)='$ptahun' AND DATE_FORMAT(a.tgltransfersby,'%Y-%m')<='$pbulan' ";
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
    $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
             select DATE_FORMAT(tgltrans,'%Y') tahun, '04' kodeid, divprodid, sum(jumlah) jumlah FROM 
             $tmp05 WHERE kode IN $filtdss 
             GROUP BY 1,2,3";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //DCC
    $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
             select DATE_FORMAT(tgltrans,'%Y') tahun, '05' kodeid, divprodid, sum(jumlah) jumlah FROM 
             $tmp05 WHERE kode IN $filtdcc 
             GROUP BY 1,2,3";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    //Gimmic GIMIK GIMMIC PROMOSI
    $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
             select DATE_FORMAT(tgltrans,'%Y') tahun, '06' kodeid, divprodid, sum(jumlah) jumlah FROM 
             $tmp05 WHERE kode IN $filtgimicprom  
             GROUP BY 1,2,3";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //IKLAN
    $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
             select DATE_FORMAT(tgltrans,'%Y') tahun, '07' kodeid, divprodid, sum(jumlah) jumlah FROM 
             $tmp05 WHERE kode IN $filtiklan  
             GROUP BY 1,2,3";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
    
    //SIMPOSIUM
    $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
             select DATE_FORMAT(tgltrans,'%Y') tahun, '08' kodeid, divprodid, sum(jumlah) jumlah FROM 
             $tmp05 WHERE kode IN $filtsimpo  
             GROUP BY 1,2,3";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
    
    //HO
    $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
             select DATE_FORMAT(tgltrans,'%Y') tahun, '10' kodeid, divprodid, sum(jumlah) jumlah FROM 
             $tmp05 WHERE kode IN $filtho  
             GROUP BY 1,2,3";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
    
        //SELAIN AKUN YANG ADA MASUKAN KE HO
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
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
            WHERE YEAR(a.periode1)='$ptahun' AND DATE_FORMAT(a.periode1,'%Y-%m')<='$pbulan'";
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
            IFNULL(a.stsnonaktif,'')<>'Y' AND YEAR(a.tgl)='$ptahun' AND DATE_FORMAT(a.tgl,'%Y-%m')<='$pbulan' ";
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
        
    
    $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
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
            . " YEAR(tgltrans)='$ptahun' AND DATE_FORMAT(tgltrans,'%Y-%m')<='$pbulan' ";
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

    $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
             select DATE_FORMAT(tgltrans,'%Y') tahun, '09' kodeid, 'EAGLE' divisi, sum(jumlah) as jumlah 
             from $tmp05 
             group by 1,2,3";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
    mysqli_query($cnit, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
    
    
    
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah) FROM $tmp03 b WHERE a.kodeid=b.kodeid)");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah) FROM $tmp02 b WHERE a.kodeid=b.kodeid)");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=IFNULL(jumlah2,0)-IFNULL(jumlah1,0)");
        
        mysqli_query($cnit, "CREATE TEMPORARY TABLE $tmp05 (select * from $tmp01)");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp05 b WHERE grp=1) WHERE nourut=5");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp05 b WHERE grp=1) WHERE nourut=5");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp05 b WHERE grp=1) WHERE nourut=5");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp05 b WHERE grp=2) WHERE nourut=10");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp05 b WHERE grp=2) WHERE nourut=10");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp05 b WHERE grp=2) WHERE nourut=10");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp05 b WHERE grp=3) WHERE nourut=14");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp05 b WHERE grp=3) WHERE nourut=14");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp05 b WHERE grp=3) WHERE nourut=14");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp05 b WHERE grp=4) WHERE nourut=20");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp05 b WHERE grp=4) WHERE nourut=20");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp05 b WHERE grp=4) WHERE nourut=20");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp05 b WHERE IFNULL(kodeid,'') <> '' and IFNULL(kodeid,'') not in ('01','02','03')) WHERE nourut=21");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp05 b WHERE IFNULL(kodeid,'') <> '' and IFNULL(kodeid,'') not in ('01','02','03')) WHERE nourut=21");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp05 b WHERE IFNULL(kodeid,'') <> '' and IFNULL(kodeid,'') not in ('01','02','03')) WHERE nourut=21");
        
        
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
        mysqli_query($cnit, "CREATE TEMPORARY TABLE $tmp05 (select * from $tmp01)");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp05 b WHERE nourut in (5,21)) WHERE nourut=22");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp05 b WHERE nourut in (5,21)) WHERE nourut=22");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp05 b WHERE nourut in (5,21)) WHERE nourut=22");
        
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
        
        //SALES
	$query = "select *from dbmaster.sales where DATE_FORMAT(bulan,'%Y')='$ptahun' AND DATE_FORMAT(bulan,'%Y-%m') <= '$pbulan'";
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.value_sales) FROM $tmp02 b) WHERE nourut=23");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.value_target) FROM $tmp02 b) WHERE nourut=23");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=IFNULL(jumlah2,0)-IFNULL(jumlah1,0) WHERE nourut=23");
        
        
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
            mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio1=ifnull(a.jumlah1,0)/$jsales1*100");
        }
        if ((DOUBLE)$jsales2>0){
            mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio2=ifnull(a.jumlah2,0)/$jsales2*100");
        }
        if ((DOUBLE)$jsales2>0){
            mysqli_query($cnit, "UPDATE $tmp01 a SET a.ratio3=ifnull(a.jumlah3,0)/$jsales2*100");
        }
        
    ?>
    
    <style>
        .tjudul {
            font-family: "times new roman", Arial, Georgia, serif;
            margin-left:10px;
            margin-right:10px;
        }
        .tjudul td {
            padding: 4px;
            font-size: 15px;
        }
        #datatable2 {
            font-family: "times new roman", Arial, Georgia, serif;
            margin-left:10px;
            margin-right:10px;
        }
        #datatable2 th, #datatable2 td {
            padding: 10px;
        }
        #datatable2 thead{
            background-color:#cccccc; 
            font-size: 18px;
        }
        #datatable2 tbody{
            font-size: 16px;
        }
    </style>
    
    <?PHP
        $tglbulanbesar=strtoupper($pblnthn);
        echo "<table class='tjudul' width='100%'>";
        echo "<tr> <td width='400px' colspan='2'>REALISASI BIAYA MARKETING VS BUDGET s/d. $tglbulanbesar</td> </tr>";
        echo "<tr> <td width='200px' colspan='2'>PT. SURYA DERMATO MEDICA LABORATORIES </td></tr>";
        echo "<tr> <td width='200px' colspan='2'>DIVISI ETHICAL</td></tr>";
        echo "</table>";
        echo "<br/>&nbsp;";
        
    ?>
    
    <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
        <thead>
            <tr>
            <th align="center" rowspan="2">NO</th>
            <th align="center" rowspan="2">KETERANGAN</th>
            <th align="center" colspan="2"><?PHP echo "$ptahun"; ?></th>
            <th align="center" colspan="2"><?PHP echo "$ptahun"; ?></th>
            <th align="center" colspan="2"><?PHP echo "$ptahun"; ?></th>
            </tr>
            
            <tr>
            <th align="center">REALISASI BUDGET</th>
            <th align="center">COST <br/>RATIO</th>
            <th align="center">USULAN BUDGET</th>
            <th align="center">COST <br/>RATIO</th>
            <th align="center">SISA BUDGET</th>
            <th align="center">COST <br/>RATIO</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $query = "select * FROM $tmp01 order by nourut";
            $tampil=mysqli_query($cnit, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $pnourut=$row['nourut'];
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
                if ((int)$pnourut==5 OR (int)$pnourut==10 OR (int)$pnourut==14 OR (int)$pnourut==20 OR (int)$pnourut==21 OR (int)$pnourut==22 OR (int)$pnourut==23) {
                    echo "<td nowrap align='right'><b>$pjumlah1</b></td>";
                    echo "<td nowrap align='right'><b>$pratio1</b></td>";
                    echo "<td nowrap align='right'><b>$pjumlah2</b></td>";
                    echo "<td nowrap align='right'><b>$pratio2</b></td>";
                    
                    echo "<td nowrap align='right'><b>$pjumlah3</b></td>";
                    echo "<td nowrap align='right'><b>$pratio3</b></td>";
                }else{
                    echo "<td nowrap align='right'>$pjumlah1</td>";
                    echo "<td nowrap align='right'>$pratio1</td>";
                    echo "<td nowrap align='right'>$pjumlah2</td>";
                    echo "<td nowrap align='right'>$pratio2</td>";
                    
                    echo "<td nowrap align='right'>$pjumlah3</td>";
                    echo "<td nowrap align='right'>$pratio3</td>";
                }
                echo "</tr>";
                
                if ((int)$pnourut==5 OR (int)$pnourut==10 OR (int)$pnourut==14 OR (int)$pnourut==20 OR (int)$pnourut==21 OR (int)$pnourut==23) {
                    echo "<tr>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "</tr>";
                }
                
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
        
        mysqli_close($cnit);
    ?>
</body>
</html>