<?PHP
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REALISASI BIAYA MARKETING VS BUDGET OTC.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>

<html>
<head>
    <title>REALISASI BIAYA MARKETING VS BUDGET OTC</title>
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
        
        $nbulan=date("F", strtotime($tgl1));
        
        $espd = $_POST['radio1'];
        
        
        $query = "select distinct b.idinput, b.divisi, b.nodivisi, a.kodeinput, a.bridinput, b.pilih, b.kodeid, b.subkode from dbmaster.t_suratdana_br1 a "
                . " JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput WHERE "
                . " IFNULL(b.stsnonaktif,'')<>'Y' AND IFNULL(b.nodivisi,'')<>'' AND a.kodeinput IN ('D', 'E', 'F', 'I', 'N', 'M') ";
        $query = "create TEMPORARY table $tmp10 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        $query = "CREATE INDEX `norm1` ON $tmp10 (idinput,divisi,nodivisi,kodeinput,bridinput, pilih)";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
        $query = "create TEMPORARY table $tmp01 (SELECT * FROM dbmaster.t_budget_realisasi_lap_otc)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        mysqli_query($cnit, "update $tmp01 set keterangan=keterangan2");
        
        $query = "SELECT
            a.tahun,
            a.g_divisi,
            a.kodeid,
            a.jumlah
            FROM
            dbmaster.t_budget_otc AS a
            WHERE tahun = '$ptahun' AND g_divisi='OTC'";
        
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        /*
        if ((double)$ptahun<2019) {
            
            //biaya rutin
            $query = "select YEAR(periode) tahun, '01' kodeid, divisi, sum(jumlah) jumlah 
                from dbmaster.t_br_bulan WHERE divisi='OTC' AND IFNULL(stsnonaktif,'')<>'Y' AND userid='0000000143'
                and YEAR(periode)='$ptahun'
                GROUP BY 1,2,3";
            $query = "create TEMPORARY table $tmp03 ($query)"; 
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            //AND IFNULL(b.tgl_fin,'')<>''
            $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah)
                select YEAR(b.bulan) tahun, '01' kodeid, b.divisi, sum(a.rptotal) jumlah 
                from dbmaster.t_brrutin1 a JOIN dbmaster.t_brrutin0 b ON a.idrutin=b.idrutin
                WHERE IFNULL(b.stsnonaktif,'')<>'Y' AND kode=1 AND b.divisi='OTC' 
                and YEAR(b.bulan)='$ptahun' AND DATE_FORMAT(b.bulan,'%Y-%m')<='$pbulan'
                GROUP BY 1,2,3";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            //biaya luar kota
            $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah)
                select YEAR(periode) tahun, '02' kodeid, divisi, sum(jumlah) jumlah 
                from dbmaster.t_br_bulan WHERE divisi='OTC' AND IFNULL(stsnonaktif,'')<>'Y' AND userid='0000000329'
                and YEAR(periode)='$ptahun'
                GROUP BY 1,2,3";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            //AND IFNULL(b.tgl_fin,'')<>''
            $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah)
                select YEAR(b.bulan) tahun, '02' kodeid, b.divisi, sum(a.rptotal) jumlah 
                from dbmaster.t_brrutin1 a JOIN dbmaster.t_brrutin0 b ON a.idrutin=b.idrutin
                WHERE IFNULL(b.stsnonaktif,'')<>'Y' AND kode=2 AND b.divisi='OTC' 
                and YEAR(b.bulan) ='$ptahun' AND DATE_FORMAT(b.bulan,'%Y-%m')<='$pbulan' 
                GROUP BY 1,2,3";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        }else{
            //AND IFNULL(b.tgl_fin,'')<>''
            //biaya rutin
            $query = "select YEAR(b.bulan) tahun, '01' kodeid, b.divisi, sum(a.rptotal) jumlah 
                from dbmaster.t_brrutin1 a JOIN dbmaster.t_brrutin0 b ON a.idrutin=b.idrutin
                WHERE IFNULL(b.stsnonaktif,'')<>'Y' AND kode=1 AND b.divisi='OTC' 
                and YEAR(b.bulan)='$ptahun' AND DATE_FORMAT(b.bulan,'%Y-%m')<='$pbulan'
                GROUP BY 1,2,3";
            $query = "create TEMPORARY table $tmp03 ($query)"; 
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            //AND IFNULL(b.tgl_fin,'')<>'' 
            //biaya luar kota
            $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah)
                select YEAR(b.bulan) tahun, '02' kodeid, b.divisi, sum(a.rptotal) jumlah 
                from dbmaster.t_brrutin1 a JOIN dbmaster.t_brrutin0 b ON a.idrutin=b.idrutin
                WHERE IFNULL(b.stsnonaktif,'')<>'Y' AND kode=2 AND b.divisi='OTC' 
                and YEAR(b.bulan) ='$ptahun' AND DATE_FORMAT(b.bulan,'%Y-%m')<='$pbulan' 
                GROUP BY 1,2,3";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        }
        
        
        
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah)"
                . "select YEAR(tglf) tahun, '03' kodeid, '' divisi, sum(jumlah) jumlah from dbmaster.t_suratdana_br where "
                . " kodeid=1 and subkode='04' and YEAR(tglf)='$ptahun' AND DATE_FORMAT(tglf,'%Y-%m')<='$pbulan' "
                . " AND IFNULL(stsnonaktif,'')<>'Y' "
                . " GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        */
        
        
        
        
    //biaya rutin luar kota
    $query = "select CAST('' as CHAR(2)) as hapus_nodiv_kosong, b.tgl_fin, b.kode, b.bulan, b.periode1, DATE_FORMAT(b.periode1,'%Y-%m-01') periode, a.idrutin, b.divisi, b.divi, b.karyawanid, b.nama_karyawan, "
            . " b.icabangid, b.areaid, b.icabangid_o, b.areaid_o, "
            . " a.coa, a.nobrid, a.rptotal, "
            . " IFNULL(a.notes,'') as ketdetail, IFNULL(b.keterangan,'') as keterangan, "
            . " a.deskripsi, DATE_FORMAT(a.tgl1,'%d/%m/%Y') as tgl1, DATE_FORMAT(a.tgl2,'%d/%m/%Y') as tgl2, a.qty, FORMAT(a.rp,0,'de_DE') as rp "
            . " from dbmaster.t_brrutin1 a "
            . " JOIN dbmaster.t_brrutin0 b on a.idrutin=b.idrutin WHERE "
            . " IFNULL(b.stsnonaktif,'') <> 'Y' AND b.divisi='OTC' AND "
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
        
        
    
    //BR OTC
    $query = "select brOtcId, noslip, icabangid_o, tglbr, tgltrans, COA4, kodeid, subpost, real1, "
            . " jumlah, realisasi, jumlah jumlah_asli, realisasi as realisasi_asli, "
            . " keterangan1, keterangan2, lampiran, ca, dpp, ppn_rp, pph_rp, tgl_fp "
            . " from hrd.br_otc WHERE IFNULL(batal,'')<>'Y' AND "
            . " brOtcId NOT IN (select DISTINCT IFNULL(brOtcId,'') FROM hrd.br_otc_reject) AND "
            . " YEAR(tgltrans)='$ptahun' AND DATE_FORMAT(tgltrans,'%Y-%m')<='$pbulan'";
    $query = "create TEMPORARY table $tmp05 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "CREATE INDEX `norm1` ON $tmp05 (brOtcId, icabangid_o)";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp05 SET jumlah=realisasi WHERE IFNULL(realisasi,0)<>0";
    //$query = "UPDATE $tmp05 SET realisasi=jumlah WHERE IFNULL(realisasi,0)=0";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "ALTER table $tmp05 ADD COLUMN idinput BIGINT(20), ADD COLUMN nodivisi VARCHAR(50), ADD COLUMN idinput1 BIGINT(20), ADD COLUMN nodivisi1 VARCHAR(50), ADD COLUMN idinput2 BIGINT(20), ADD COLUMN nodivisi2 VARCHAR(50), ADD COLUMN kodeid_pd INT(4), ADD COLUMN subkode_pd VARCHAR(5), ADD COLUMN pcm VARCHAR(1), ADD COLUMN kasbonsby VARCHAR(1), ADD COLUMN coa_pcm VARCHAR(50), ADD COLUMN nama_coa_pcm VARCHAR(100)";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    
    
    $query = "UPDATE $tmp05 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp10 WHERE kodeinput IN ('D') AND divisi='OTC') b on a.brOtcId=b.bridinput "
            . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput, a.nodivisi2=b.nodivisi, a.idinput2=b.idinput, a.kodeid_pd=b.kodeid, a.subkode_pd=b.subkode WHERE b.pilih='Y'"; 
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp05 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp10 WHERE kodeinput IN ('D') AND divisi='OTC') b on a.brOtcId=b.bridinput "
            . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput, a.nodivisi1=b.nodivisi, a.idinput1=b.idinput, a.kodeid_pd=b.kodeid, a.subkode_pd=b.subkode WHERE b.pilih='N' AND IFNULL(a.nodivisi,'')=''"; 
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp05 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp10 WHERE kodeinput IN ('D') AND divisi='OTC') b on a.brOtcId=b.bridinput "
            . " SET a.nodivisi1=b.nodivisi, a.idinput1=b.idinput, a.kodeid_pd=b.kodeid, a.subkode_pd=b.subkode WHERE b.pilih='N'"; 
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
    
    $query ="DELETE FROM $tmp05 WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(tglbr,'%Y-%m')>='2020-01'";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
        
    
    
    
        
        
        $periodeby_br="tglbr";
        if ($espd=="A") {
            $periodeby_br="tgltrans";
        }
        
        /*
	$query = "select * from hrd.br_otc
                 where DATE_FORMAT($periodeby_br,'%Y')='$ptahun' AND DATE_FORMAT($periodeby_br,'%Y-%m') <= '$pbulan' and batal <>'Y'";
        $query = "create TEMPORARY table $tmp04 ($query)";
        mysqli_query($cnit, $query);
          
         
        if ($espd=="A") {
            $query = "UPDATE $tmp04 SET realisasi=jumlah WHERE IFNULL(realisasi,0)=0";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        }
         
        */
        
	$query = "select * from $tmp05";
        $query = "create TEMPORARY table $tmp04 ($query)";
        mysqli_query($cnit, $query);
        
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
        
        
        $mkode="03";
        // 03 GAJI SPG
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '$mkode' kodeid, 'OTC' divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE CONCAT(subpost,kodeid) IN (select distinct CONCAT(IFNULL(kode_akun,''),IFNULL(kode_akun_sub,'')) FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnit, "DELETE FROM $tmp04 WHERE CONCAT(subpost,kodeid) IN (select distinct CONCAT(IFNULL(kode_akun,''),IFNULL(kode_akun_sub,'')) FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')");
        
        $mkode="04";
        // 04 INSENTIF
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '$mkode' kodeid, 'OTC' divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE CONCAT(subpost,kodeid) IN (select distinct CONCAT(IFNULL(kode_akun,''),IFNULL(kode_akun_sub,'')) FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnit, "DELETE FROM $tmp04 WHERE CONCAT(subpost,kodeid) IN (select distinct CONCAT(IFNULL(kode_akun,''),IFNULL(kode_akun_sub,'')) FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')");
        
        $mkode="05";
        // 05 SPONSORSIF
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '$mkode' kodeid, 'OTC' divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE CONCAT(subpost,kodeid) IN (select distinct CONCAT(IFNULL(kode_akun,''),IFNULL(kode_akun_sub,'')) FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnit, "DELETE FROM $tmp04 WHERE CONCAT(subpost,kodeid) IN (select distinct CONCAT(IFNULL(kode_akun,''),IFNULL(kode_akun_sub,'')) FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')");
        
        $mkode="06";
        // 06 SEWA DISPLAY
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '$mkode' kodeid, 'OTC' divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE CONCAT(subpost,kodeid) IN (select distinct CONCAT(IFNULL(kode_akun,''),IFNULL(kode_akun_sub,'')) FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnit, "DELETE FROM $tmp04 WHERE CONCAT(subpost,kodeid) IN (select distinct CONCAT(IFNULL(kode_akun,''),IFNULL(kode_akun_sub,'')) FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')");
        
        $mkode="07";
        // 07 ENTERTAIN
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '$mkode' kodeid, 'OTC' divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE CONCAT(subpost,kodeid) IN (select distinct CONCAT(IFNULL(kode_akun,''),IFNULL(kode_akun_sub,'')) FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnit, "DELETE FROM $tmp04 WHERE CONCAT(subpost,kodeid) IN (select distinct CONCAT(IFNULL(kode_akun,''),IFNULL(kode_akun_sub,'')) FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')");
        
        $mkode="08";
        // 08 PROMO MATERIAL 
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '$mkode' kodeid, 'OTC' divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE subpost IN (select distinct IFNULL(kode_akun,'') FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
                        mysqli_query($cnit, "DELETE FROM $tmp04 WHERE CONCAT(subpost) IN (select distinct CONCAT(IFNULL(kode_akun,'')) FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')");


        $mkode="09";
        // 09 IKLAN
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '$mkode' kodeid, 'OTC' divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE subpost IN (select distinct IFNULL(kode_akun,'') FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
                        mysqli_query($cnit, "DELETE FROM $tmp04 WHERE CONCAT(subpost) IN (select distinct CONCAT(IFNULL(kode_akun,'')) FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')");
                        
                        
        $mkode="10";
        // 10 EVENT
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '$mkode' kodeid, 'OTC' divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE subpost IN (select distinct IFNULL(kode_akun,'') FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
                        mysqli_query($cnit, "DELETE FROM $tmp04 WHERE CONCAT(subpost) IN (select distinct CONCAT(IFNULL(kode_akun,'')) FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')");
                        
                        
        $mkode="11";
        // 11 RAFAKSI
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '$mkode' kodeid, 'OTC' divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE CONCAT(subpost,kodeid) IN (select distinct CONCAT(IFNULL(kode_akun,''),IFNULL(kode_akun_sub,'')) FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnit, "DELETE FROM $tmp04 WHERE CONCAT(subpost,kodeid) IN (select distinct CONCAT(IFNULL(kode_akun,''),IFNULL(kode_akun_sub,'')) FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')");
        
        $mkode="17";
        // 17 KLAIM DISCOUNT
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '$mkode' kodeid, 'OTC' divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE CONCAT(subpost,kodeid) IN (select distinct CONCAT(IFNULL(kode_akun,''),IFNULL(kode_akun_sub,'')) FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        mysqli_query($cnit, "DELETE FROM $tmp04 WHERE CONCAT(subpost,kodeid) IN (select distinct CONCAT(IFNULL(kode_akun,''),IFNULL(kode_akun_sub,'')) FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')");
        // 17 KLAIM DISCOUNT KHUSUS
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '$mkode' kodeid, 'OTC' divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE subpost='12' AND IFNULL(kodeid,'')='' 
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        
        mysqli_query($cnit, "DELETE FROM $tmp04 WHERE subpost='12' AND IFNULL(kodeid,'')=''");
        
        $mkode="18";
        // 18 PROMOTION COST
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '$mkode' kodeid, 'OTC' divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE CONCAT(subpost,kodeid) IN (select distinct CONCAT(IFNULL(kode_akun,''),IFNULL(kode_akun_sub,'')) FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnit, "DELETE FROM $tmp04 WHERE CONCAT(subpost,kodeid) IN (select distinct CONCAT(IFNULL(kode_akun,''),IFNULL(kode_akun_sub,'')) FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')");
        
        $mkode="13";
        // 13 LISTING FEE
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '$mkode' kodeid, 'OTC' divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE CONCAT(subpost,kodeid) IN (select distinct CONCAT(IFNULL(kode_akun,''),IFNULL(kode_akun_sub,'')) FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnit, "DELETE FROM $tmp04 WHERE CONCAT(subpost,kodeid) IN (select distinct CONCAT(IFNULL(kode_akun,''),IFNULL(kode_akun_sub,'')) FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')");
        
        $mkode="14";
        // 14 FRONTLINER
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '$mkode' kodeid, 'OTC' divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE CONCAT(subpost,kodeid) IN (select distinct CONCAT(IFNULL(kode_akun,''),IFNULL(kode_akun_sub,'')) FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnit, "DELETE FROM $tmp04 WHERE CONCAT(subpost,kodeid) IN (select distinct CONCAT(IFNULL(kode_akun,''),IFNULL(kode_akun_sub,'')) FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')");
        
        $mkode="15";
        // 15 HOTEL & TIKET
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '$mkode' kodeid, 'OTC' divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE CONCAT(subpost,kodeid) IN (select distinct CONCAT(IFNULL(kode_akun,''),IFNULL(kode_akun_sub,'')) FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnit, "DELETE FROM $tmp04 WHERE CONCAT(subpost,kodeid) IN (select distinct CONCAT(IFNULL(kode_akun,''),IFNULL(kode_akun_sub,'')) FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')");
        
        $mkode="16";
        // 16 INVENTARIS
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '$mkode' kodeid, 'OTC' divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE CONCAT(subpost,kodeid) IN (select distinct CONCAT(IFNULL(kode_akun,''),IFNULL(kode_akun_sub,'')) FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnit, "DELETE FROM $tmp04 WHERE CONCAT(subpost,kodeid) IN (select distinct CONCAT(IFNULL(kode_akun,''),IFNULL(kode_akun_sub,'')) FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')");
        
        
        
        
        //dari kalim diskon claim discount
        
    $query = "select 'OTC' divprodid, tgl, tgltrans, distid, klaimId, COA4, karyawanid, noslip, "
            . " aktivitas1, realisasi1 nmrealisasi, jumlah, dpp, ppn_rp, pph_rp, tgl_fp, pengajuan divpengajuan "
            . " FROM hrd.klaim WHERE IFNULL(pengajuan,'')='OTC' AND YEAR(tgltrans)>='2019' AND "
            . " klaimId not in (SELECT DISTINCT ifnull(klaimId,'') from hrd.klaim_reject) AND "
            . " YEAR(tgltrans)='$ptahun' AND DATE_FORMAT(tgltrans,'%Y-%m')<='$pbulan' ";
    $query = "create TEMPORARY table $tmp05 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "CREATE INDEX `norm1` ON $tmp05 (klaimId)";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "ALTER table $tmp05 ADD COLUMN idinput BIGINT(20), ADD COLUMN nodivisi VARCHAR(50), ADD COLUMN icabangid VARCHAR(10), ADD COLUMN nama_cabang VARCHAR(100)";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp05 a JOIN (select distinct pilih, nodivisi, idinput, bridinput FROM $tmp10 WHERE kodeinput IN ('E')) b on a.klaimId=b.bridinput "
            . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput WHERE b.pilih='Y'"; 
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp05 a JOIN (select distinct pilih, nodivisi, idinput, bridinput FROM $tmp10 WHERE kodeinput IN ('E')) b on a.klaimId=b.bridinput "
            . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput WHERE b.pilih='N' AND IFNULL(a.nodivisi,'')=''"; 
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    $query ="DELETE FROM $tmp05 WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(tgl,'%Y-%m')>='2020-01'";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
        
        $mkode="12";
        // 12 BONUS DPL/DPF DARI KLAIM DISCOUNT PRITA --- PENGAJUAN = OTC
	$query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '$mkode' kodeid, 'OTC' divisi, sum(jumlah) as jumlah 
                 from $tmp05 
                 group by 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
        
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah) FROM $tmp03 b WHERE a.kodeid=b.kodeid)");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah) FROM $tmp02 b WHERE a.kodeid=b.kodeid)");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=IFNULL(jumlah2,0)-IFNULL(jumlah1,0)");
        
        
        mysqli_query($cnit, "CREATE TEMPORARY TABLE $tmp05 (select * from $tmp01)");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp05 b WHERE grp=1) WHERE nourut=6");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp05 b WHERE grp=1) WHERE nourut=6");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp05 b WHERE grp=1) WHERE nourut=6");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp05 b WHERE grp=2) WHERE nourut=14");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp05 b WHERE grp=2) WHERE nourut=14");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp05 b WHERE grp=2) WHERE nourut=14");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp05 b WHERE grp=3) WHERE nourut=19");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp05 b WHERE grp=3) WHERE nourut=19");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp05 b WHERE grp=3) WHERE nourut=19");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp05 b WHERE grp=5) WHERE nourut=28");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp05 b WHERE grp=5) WHERE nourut=28");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp05 b WHERE grp=5) WHERE nourut=28");
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp05 b WHERE grp=6) WHERE nourut=35");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp05 b WHERE grp=6) WHERE nourut=35");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp05 b WHERE grp=6) WHERE nourut=35");
        
        
        
        
        //
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
        mysqli_query($cnit, "CREATE TEMPORARY TABLE $tmp05 (select * from $tmp01)");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp05 b WHERE grp in (2,3,4,5,6)) WHERE nourut=37");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp05 b WHERE grp in (2,3,4,5,6)) WHERE nourut=37");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp05 b WHERE grp in (2,3,4,5,6)) WHERE nourut=37");
        
        //
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
        mysqli_query($cnit, "CREATE TEMPORARY TABLE $tmp05 (select * from $tmp01)");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah1=(select sum(b.jumlah1) FROM $tmp05 b WHERE nourut in (6,37)) WHERE nourut=39");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah2=(select sum(b.jumlah2) FROM $tmp05 b WHERE nourut in (6,37)) WHERE nourut=39");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.jumlah3=(select sum(b.jumlah3) FROM $tmp05 b WHERE nourut in (6,37)) WHERE nourut=39");
        
        
        
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
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
        echo "<tr> <td width='400px' colspan='7'>REALISASI BIAYA MARKETING VS BUDGET s/d. $tglbulanbesar</td> </tr>";
        echo "<tr> <td width='200px' colspan='7'>PT. SURYA DERMATO MEDICA LABORATORIES </td></tr>";
        echo "<tr> <td width='200px' colspan='7'>DIVISI OTC</td></tr>";
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
                
                if ((int)$pnourut==6 OR (int)$pnourut==14 OR (int)$pnourut==19 OR (int)$pnourut==28 OR (int)$pnourut==35 OR (int)$pnourut==37 OR (int)$pnourut==39 OR (int)$pnourut==43) {
                    echo "<td nowrap><b>$pjudul</b></td>";
                    echo "<td nowrap align='right'><b>$pjumlah1</b></td>";
                    echo "<td nowrap align='right'><b>$pratio1</b></td>";
                    echo "<td nowrap align='right'><b>$pjumlah2</b></td>";
                    echo "<td nowrap align='right'><b>$pratio2</b></td>";
                    
                    echo "<td nowrap align='right'><b>$pjumlah3</b></td>";
                    echo "<td nowrap align='right'><b>$pratio3</b></td>";
                }else{
                    echo "<td nowrap>$pjudul</td>";
                    echo "<td nowrap align='right'>$pjumlah1</td>";
                    echo "<td nowrap align='right'>$pratio1</td>";
                    echo "<td nowrap align='right'>$pjumlah2</td>";
                    echo "<td nowrap align='right'>$pratio2</td>";
                    
                    echo "<td nowrap align='right'>$pjumlah3</td>";
                    echo "<td nowrap align='right'>$pratio3</td>";
                }
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
        
        mysqli_close($cnit);
    ?>
</body>
</html>