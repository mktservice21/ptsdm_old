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

        
        $tgl1=$_POST['tahun'];
        $pbulan=date("Y-m", strtotime($tgl1));
        $ptahun=date("Y", strtotime($tgl1));
        $pblnthn=date("F Y", strtotime($tgl1));
        
        $nbulan=date("F", strtotime($tgl1));
        
        $espd = $_POST['radio1'];
        
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
        
        
        //BR OTC
        $periodeby_br="tglbr";
        if ($espd=="A") {
            $periodeby_br="tgltrans";
        }
        
	$query = "select * from hrd.br_otc
                 where DATE_FORMAT($periodeby_br,'%Y')='$ptahun' AND DATE_FORMAT($periodeby_br,'%Y-%m') <= '$pbulan' and batal <>'Y'";
        $query = "create TEMPORARY table $tmp04 ($query)";
        mysqli_query($cnit, $query);
        
        
        if ($espd=="A") {
            $query = "UPDATE $tmp04 SET jumlah=realisasi WHERE IFNULL(realisasi,0)<>0";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        }
        
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
        
        $mkode="09";
        // 09 IKLAN
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '$mkode' kodeid, 'OTC' divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE subpost IN (select distinct IFNULL(kode_akun,'') FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $mkode="10";
        // 10 EVENT
        $query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '$mkode' kodeid, 'OTC' divprodid, sum(jumlah) jumlah FROM 
                 $tmp04 WHERE subpost IN (select distinct IFNULL(kode_akun,'') FROM dbmaster.t_budget_kode_otc_d WHERE kodeid='$mkode')
                 GROUP BY 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
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
        
        $mkode="12";
        // 12 BONUS DPL/DPF DARI KLAIM DISCOUNT PRITA --- PENGAJUAN = OTC
	$query = "INSERT INTO $tmp03 (tahun, kodeid, divisi, jumlah) 
                 select DATE_FORMAT($periodeby_br,'%Y') tahun, '$mkode' kodeid, 'EAGLE' divisi, sum(jumlah) as jumlah 
                 from hrd.klaim where pengajuan='OTC' AND DATE_FORMAT($periodeby_br,'%Y')='$ptahun' AND DATE_FORMAT($periodeby_br,'%Y-%m') <= '$pbulan' 
                 group by 1,2,3";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
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
        
        mysqli_close($cnit);
    ?>
</body>
</html>