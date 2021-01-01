<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    
    session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
	
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=Laporan Budget DCC DSS By Region.xls");
    }
    
    include("config/koneksimysqli.php");
    include "config/fungsi_combo.php";
    include("config/common.php");
    
    
    $printdate= date("d/m/Y");
    
    
?>


<?PHP
    
    
    $tgl01 = $_POST['e_tgl1'];
    $tgl02 = $_POST['e_tgl2'];
    
    
    $pperiode1 = date("Y-m", strtotime($tgl01));
    $pperiode2 = date("Y-m", strtotime($tgl02));
    
    $myperiode1 = date("F Y", strtotime($tgl01));
    $myperiode2 = date("F Y", strtotime($tgl02));

    $ptahun= date("Y", strtotime($tgl01));
    
    $rptbypilih = $_POST['cb_by'];
    
    $filterkode=('');
    if (!empty($_POST['chkbox_kode'])){
        $filterkode=$_POST['chkbox_kode'];
        $filterkode=PilCekBoxAndEmpty($filterkode);
    }
    
    
    $picardid=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];

    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmprptrealbudgetreg01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmprptrealbudgetreg02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmprptrealbudgetreg03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmprptrealbudgetreg04_".$puserid."_$now ";
    $tmp05 =" dbtemp.tmprptrealbudgetreg05_".$puserid."_$now ";
    $tmp06 =" dbtemp.tmprptrealbudgetreg06_".$puserid."_$now ";
    $tmp10 =" dbtemp.tmpprosbmpil10_".$puserid."_$now ";
    $tmp11 =" dbtemp.tmpprosbmpil11_".$puserid."_$now ";
    $tmp12 =" dbtemp.tmpprosbmpil12_".$puserid."_$now ";
    
        
    if ($ptahun=="2019xxx") {
        
    }else{
        
        $query = "select brId, noslip, icabangid, tgl, tgltrans, divprodid, COA4, kode, realisasi1, "
                . " jumlah, jumlah1, jumlah jumlah_asli, jumlah1 as jumlah1_asli, "
                . " aktivitas1, aktivitas2, dokterId, dokter, karyawanId, ccyId, tgltrm, lampiran, ca, "
                . " dpp, ppn_rp, pph_rp, tgl_fp, CAST('' as CHAR(20)) as nobukti "
                . " from hrd.br0 WHERE IFNULL(batal,'')<>'Y' AND "
                . " brId NOT IN (select DISTINCT IFNULL(brId,'') FROM hrd.br0_reject) AND "
                . " DATE_FORMAT(tgltrans,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
        $query .=" AND IFNULL(kode,'') IN $filterkode ";

        $query = "create TEMPORARY table $tmp01 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "CREATE INDEX `norm1` ON $tmp01 (brId,dokterId)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        
        
            //via SBY
            $query = "select a.bridinput brId, b.noslip, b.icabangid, b.tgl, a.tgltransfersby tgltrans, b.divprodid, "
                    . " b.COA4, b.kode, b.realisasi1, a.jumlah jumlah, a.jumlah jumlah1, a.jumlah jumlah_asli, a.jumlah as jumlah1_asli, "
                    . " b.aktivitas1, b.aktivitas2, b.dokterId, b.dokter, b.karyawanId, b.ccyId, b.tgltrm, b.lampiran, b.ca, "
                    . " b.dpp, b.ppn_rp, b.pph_rp, b.tgl_fp, "
                    . " a.nobukti "
                    . " from dbmaster.t_br0_via_sby a JOIN hrd.br0 b on a.bridinput=b.brId "
                    . " WHERE IFNULL(b.batal,'')<>'Y' AND "
                    . " a.bridinput NOT IN (select DISTINCT IFNULL(brId,'') FROM hrd.br0_reject) AND "
                    . " DATE_FORMAT(a.tgltransfersby,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
            $query .=" AND IFNULL(kode,'') IN $filterkode ";
            
            $query = "create TEMPORARY table $tmp02 ($query)"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "CREATE INDEX `norm1` ON $tmp02 (brId,dokterId)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        
            
            $query = "DELETE FROM $tmp01 WHERE brId IN (select distinct IFNULL(brId,'') FROM $tmp02)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            $query = "INSERT INTO $tmp01 (brId, noslip, icabangid, tgl, tgltrans, divprodid, "
                    . " COA4, kode, realisasi1, jumlah, jumlah1, jumlah_asli, jumlah1_asli, "
                    . " aktivitas1, aktivitas2, dokterId, dokter, karyawanId, ccyId, tgltrm, lampiran, ca, "
                    . " dpp, ppn_rp, pph_rp, tgl_fp, nobukti) "
                    . " select brId, noslip, icabangid, tgl, tgltrans, divprodid, "
                    . " COA4, kode, realisasi1, jumlah, jumlah1, jumlah_asli, jumlah1_asli, "
                    . " aktivitas1, aktivitas2, dokterId, dokter, karyawanId, ccyId, tgltrm, lampiran, ca, "
                    . " dpp, ppn_rp, pph_rp, tgl_fp, nobukti "
                    . " from $tmp02 ";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            //END via SBY
            
        $query = "UPDATE $tmp01 SET jumlah=jumlah1 WHERE IFNULL(jumlah1,0)<>0";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
        
        $query = "select dokterId, nama from hrd.dokter WHERE dokterId IN (select distinct IFNULL(dokterId,'') from $tmp01)";
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp02 (dokterId)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        
            
            
        
        
        $query = "select a.*, d.nama nama_dokter, e.nama nama_karyawan, b.nama nama_cabang, c.nama nama_kode, f.NAMA4, b.region, "
                . " CAST('' as CHAR(50)) as nodivisi, CAST('' as CHAR(50)) as nodivisi1, CAST('' as CHAR(50)) as nodivisi2 "
                . " from $tmp01 a LEFT JOIN mkt.icabang b on a.icabangid=b.icabangid "
                . " LEFT JOIN hrd.br_kode c on a.kode=c.kodeid "
                . " LEFT JOIN $tmp02 d on a.dokterId=d.dokterId"
                . " LEFT JOIN hrd.karyawan e on a.karyawanId=e.karyawanId "
                . " LEFT JOIN dbmaster.coa_level4 f on a.COA4=f.COA4";
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "ALTER table $tmp03 ADD COLUMN idinput BIGINT(20), ADD COLUMN idinput1 BIGINT(20), ADD COLUMN idinput2 BIGINT(20), ADD COLUMN kodeid_pd INT(4), ADD COLUMN subkode_pd VARCHAR(5), ADD COLUMN pcm VARCHAR(1), ADD COLUMN kasbonsby VARCHAR(1), ADD COLUMN coa_pcm VARCHAR(50), ADD COLUMN nama_coa_pcm VARCHAR(100)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
            $query = "CREATE INDEX `norm1` ON $tmp03 (brId)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        
            
            
            
            
        $query ="select distinct idinput, bridinput, kodeinput from dbmaster.t_suratdana_br1 WHERE kodeinput IN ('A', 'B', 'C')";
        $query = "create TEMPORARY table $tmp11 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "CREATE INDEX `norm1` ON $tmp11 (bridinput,kodeinput)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "select idinput, divisi, nodivisi, pilih, kodeid, subkode "
                . " from dbmaster.t_suratdana_br WHERE idinput in (select distinct IFNULL(idinput,'') from $tmp11) "
                . " AND IFNULL(stsnonaktif,'')<>'Y'";
        $query = "create TEMPORARY table $tmp12 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "CREATE INDEX `norm1` ON $tmp12 (idinput,divisi,nodivisi, pilih)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "select distinct b.idinput, b.divisi, b.nodivisi, a.kodeinput, a.bridinput, b.pilih, b.kodeid, b.subkode from $tmp11 a "
                . " JOIN $tmp12 b on a.idinput=b.idinput";
        $query = "create TEMPORARY table $tmp10 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        /*
        
        $query = "select distinct b.idinput, b.divisi, b.nodivisi, a.kodeinput, a.bridinput, b.pilih, b.kodeid, b.subkode from dbmaster.t_suratdana_br1 a "
                . " JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput WHERE "
                . " IFNULL(b.stsnonaktif,'')<>'Y' AND IFNULL(b.nodivisi,'')<>'' AND a.kodeinput IN ('A', 'B', 'C') AND "
                . " a.bridinput IN (select distinct IFNULL(brid,'') from $tmp03)";
        $query = "create TEMPORARY table $tmp10 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    */
        
        $query = "CREATE INDEX `norm1` ON $tmp10 (idinput,divisi,nodivisi,kodeinput,bridinput, pilih)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp03 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp10 WHERE kodeinput IN ('A', 'B', 'C') AND divisi<>'OTC') b on a.brId=b.bridinput "
                . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput, a.nodivisi2=b.nodivisi, a.idinput2=b.idinput, a.kodeid_pd=b.kodeid, a.subkode_pd=b.subkode WHERE b.pilih='Y'"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        $query = "UPDATE $tmp03 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp10 WHERE kodeinput IN ('A', 'B', 'C') AND divisi<>'OTC') b on a.brId=b.bridinput "
                . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput, a.nodivisi1=b.nodivisi, a.idinput1=b.idinput, a.kodeid_pd=b.kodeid, a.subkode_pd=b.subkode WHERE b.pilih='N' AND IFNULL(a.nodivisi,'')=''"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
            $query = "UPDATE $tmp03 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp10 WHERE kodeinput IN ('A', 'B', 'C') AND divisi<>'OTC') b on a.brId=b.bridinput "
                    . " SET a.nodivisi1=b.nodivisi, a.idinput1=b.idinput, a.kodeid_pd=b.kodeid, a.subkode_pd=b.subkode WHERE b.pilih='N'"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            
            $query = "UPDATE $tmp03 SET pcm='Y' WHERE IFNULL(nodivisi1,'')<>'' AND IFNULL(nodivisi2,'')=''"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "UPDATE $tmp03 SET kasbonsby='Y' WHERE CONCAT(kodeid_pd,subkode_pd) IN ('680')"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            $query = "UPDATE $tmp03 SET pcm='' WHERE "
                    . " IFNULL(nodivisi2,'')='' AND ( (IFNULL(tgltrm,'0000-00-00')<>'0000-00-00' AND IFNULL(tgltrm,'')<>'') OR ( IFNULL(lampiran,'')='Y' AND IFNULL(ca,'')='N' AND IFNULL(tgltrm,'0000-00-00')='0000-00-00') )"
                    . " AND CONCAT(kodeid_pd,subkode_pd) NOT IN ('680')";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            //UPDATE PCM JADI U.M BIAYA UANG MUKA
            $query = "UPDATE $tmp03 SET coa_pcm='105-02', nama_coa_pcm='U.M. BIAYA' WHERE IFNULL(pcm,'')='Y'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        $query ="DELETE FROM $tmp03 WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(tgl,'%Y-%m')>='2020-01'";
        //$query ="UPDATE $tmp03 SET hapus_nodiv_kosong='Y' WHERE IFNULL(nodivisi,'')='' AND ( IFNULL(tgltrans,'')='' OR IFNULL(tgltrans,'0000-00-00')='0000-00-00' ) AND DATE_FORMAT(tgl,'%Y-%m')>='2020-01'";
        //$query ="UPDATE $tmp03 SET hapus_nodiv_kosong='Y' WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(tgl,'%Y-%m')>='2020-01'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }
        
    mysqli_query($cnmy, "update $tmp03 set region='N' WHERE IFNULL(region,'')=''");
    mysqli_query($cnmy, "update $tmp03 set icabangid='N', nama_cabang='NONE' WHERE IFNULL(icabangid,'')=''");
        

        
    //$tmp03 = "dbtemp.tmprptrealbudgetreg03_1854_06112020120205";
    
    if ($rptbypilih=="2") {
        $query = "select distinct region, divprodid, cast(NULL as DECIMAL(20,2)) as DCC, "
                . " cast(NULL as DECIMAL(20,2)) as DSS, cast(NULL as DECIMAL(20,2)) as VTOTAL from $tmp03 ";
        $query = "create TEMPORARY table $tmp04 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp04 a SET a.DCC=(select sum(b.jumlah) jumlah from $tmp03 b WHERE a.region=b.region AND a.divprodid=b.divprodid AND "
                . " b.nama_kode like '%DCC%' )"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp04 a SET a.DSS=(select sum(b.jumlah) jumlah from $tmp03 b WHERE a.region=b.region AND a.divprodid=b.divprodid AND "
                . " b.nama_kode like '%DSS%' )"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp04 SET VTOTAL=IFNULL(DCC,0)+IFNULL(DSS,0)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    }else{
        
        
        $query="select distinct divprodid, region, icabangid, nama_cabang, "
                . "CAST(null AS DECIMAL(20,2)) as DCC, CAST(null AS DECIMAL(20,2)) as DSS, CAST(null AS DECIMAL(20,2)) as TOTAL "
                . " from $tmp03";
        $query = "create TEMPORARY table $tmp04 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "CREATE INDEX `norm1` ON $tmp04 (divprodid,region,icabangid)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        


        $n_filed_add="";
        for($xi=1;$xi<=12;$xi++) {
            $n_filed_add .=" ADD COLUMN dcc_".$xi." DECIMAL(20,2),ADD COLUMN dss_".$xi." DECIMAL(20,2),";
        }
        $n_filed_add .=" ADD COLUMN vtotal_dcc DECIMAL(20,2), ADD COLUMN vtotal_dss DECIMAL(20,2)";

        $query = "ALTER TABLE $tmp04 $n_filed_add";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        for($xi=1;$xi<=12;$xi++) {
            $fbulan=$ptahun."-0".$xi;
            if ((double)$xi >=10) $fbulan=$ptahun."-".$xi;
            $n_filed_add = "dcc_".$xi;


            $sql="update $tmp04 set $n_filed_add=(select sum(jumlah) from $tmp03 where "
                    . " $tmp03.divprodid=$tmp04.divprodid and "
                    . " $tmp03.icabangid=$tmp04.icabangid and "
                    . " $tmp03.nama_kode like '%DCC%' AND DATE_FORMAT($tmp03.tgltrans,'%Y-%m')='$fbulan')";
            mysqli_query($cnmy, $sql);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


            $n_filed_add2 = "dss_".$xi;

            $sql="update $tmp04 set $n_filed_add2=(select sum(jumlah) from $tmp03 where "
                    . " $tmp03.divprodid=$tmp04.divprodid and "
                    . " $tmp03.icabangid=$tmp04.icabangid and "
                    . " $tmp03.nama_kode like '%DSS%' AND DATE_FORMAT($tmp03.tgltrans,'%Y-%m')='$fbulan')";
            mysqli_query($cnmy, $sql);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query="DELETE FROM $tmp03 WHERE DATE_FORMAT(tgltrans,'%Y-%m')='$fbulan'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        }
    
    
        $query = "UPDATE $tmp04 SET vtotal_dcc=IFNULL(dcc_1,0)+IFNULL(dcc_2,0)+IFNULL(dcc_3,0)+IFNULL(dcc_4,0)+IFNULL(dcc_5,0)+IFNULL(dcc_6,0)+IFNULL(dcc_7,0)+IFNULL(dcc_8,0)+IFNULL(dcc_9,0)+IFNULL(dcc_10,0)+IFNULL(dcc_11,0)+IFNULL(dcc_12,0),"
                . " vtotal_dss=IFNULL(dss_1,0)+IFNULL(dss_2,0)+IFNULL(dss_3,0)+IFNULL(dss_4,0)+IFNULL(dss_5,0)+IFNULL(dss_6,0)+IFNULL(dss_7,0)+IFNULL(dss_8,0)+IFNULL(dss_9,0)+IFNULL(dss_10,0)+IFNULL(dss_11,0)+IFNULL(dss_12,0)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query="UPDATE $tmp04 SET TOTAL=IFNULL(vtotal_dcc,0)+IFNULL(vtotal_dss,0), DCC=vtotal_dcc, DSS=vtotal_dss";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "select 
            region, sum(dcc) as dcc, sum(dss) dss, sum(total) total,
            sum(dcc_1) dcc_1, sum(dcc_2) dcc_2, sum(dcc_3) dcc_3, sum(dcc_4) dcc_4, sum(dcc_5) dcc_5, sum(dcc_6) dcc_6, 
            sum(dcc_7) dcc_7, sum(dcc_8) dcc_8, sum(dcc_9) dcc_9, sum(dcc_10) dcc_10, sum(dcc_11) dcc_11, sum(dcc_12) dcc_12,
            sum(dss_1) dss_1, sum(dss_2) dss_2, sum(dss_3) dss_3, sum(dss_4) dss_4, sum(dss_5) dss_5, sum(dss_6) dss_6, 
            sum(dss_7) dss_7, sum(dss_8) dss_8, sum(dss_9) dss_9, sum(dss_10) dss_10, sum(dss_11) dss_11, sum(dss_12) dss_12
            from $tmp04 group by 1";
        $query = "create TEMPORARY table $tmp05 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    }
    //goto hapusdata;
?>



<HTML>
<HEAD>
    <title>Laporan Budget DCC DSS By Region</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Mei 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <!--<link href="css/laporanbaru.css" rel="stylesheet">-->
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        
        <!-- Bootstrap -->
        <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    
        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">


        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">


        <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        
		
		
		
		<!-- jQuery -->
        <script src="vendors/jquery/dist/jquery.min.js"></script>
        

        
    <?PHP } ?>
    
</HEAD>
<BODY class="nav-md">
    
    <div class='modal fade' id='myModal' role='dialog'></div>
    
<?PHP if ($ppilihrpt!="excel") { ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?PHP } ?>
    
    
<div id='n_content'>
    
    <center><div class='h1judul'>Laporan Budget DCC DSS By Region</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <tr><td>Periode</td><td>:</td><td><?PHP echo "<b>$myperiode1 s/d. $myperiode2</b>"; ?></td></tr>
            <tr class='miring text2'><td>view date</td><td>:</td><td><?PHP echo "$printdate"; ?></td></tr>
        </table>
    </div>
    <div class="clearfix"></div>
    <hr/>
    
    <?PHP
    if ($rptbypilih=="2") {
    ?>
        <table id='mydatatable1' class='table table-striped table-bordered' width="50%" border="1px solid black">
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th nowrap>REGION</th>
                    <th nowrap>DCC</th>
                    <th nowrap>DSS</th>
                    <th nowrap>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                    $pgrdtotdcc=0;
                    $pgrdtotdss=0;
                    $pgrdtotrp=0;
                    
                    $psubtotdcc=0;
                    $psubtotdss=0;
                    $psubtotrp=0;
                    
                    $no=1;
                    $group1 = mysqli_query($cnmy, "select distinct region from $tmp04 order by region");
                    while ($g1=mysqli_fetch_array($group1)){
                        $region="Barat";
                        $pnreg=$g1['region'];
                        if ($pnreg=="T") $region="Timur";
                        if ($pnreg=="N") $region="None";
                        
                        echo "<tr>";
                        echo "<td>$no</td>";
                        echo "<td nowrap>$region</td>";
                        echo "<td nowrap align='right'></td>";
                        echo "<td nowrap align='right'></td>";
                        echo "<td nowrap align='right'></td>";
                        echo "</tr>";
                        
                        $no++;
                        
                        $psubtotdcc=0;
                        $psubtotdss=0;
                        $psubtotrp=0;
                        
                        $group2 = mysqli_query($cnmy, "select * from $tmp04 WHERE region='$pnreg' order by region, divprodid");
                        while ($g2=mysqli_fetch_array($group2)){
                            $pdivprod=$g2['divprodid'];
                            $pjmldcc=$g2['DCC'];
                            $pjmldss=$g2['DSS'];
                            $pjmltot=$g2['VTOTAL'];

                            if (empty($pjmldcc)) $pjmldcc=0;
                            if (empty($pjmldss)) $pjmldss=0;
                            if (empty($pjmltot)) $pjmltot=0;
                            
                            $psubtotdcc=(double)$psubtotdcc+(double)$pjmldcc;
                            $psubtotdss=(double)$psubtotdss+(double)$pjmldss;
                            $psubtotrp=(double)$psubtotrp+(double)$pjmltot;
                            
                            $pgrdtotdcc=(double)$pgrdtotdcc+(double)$pjmldcc;
                            $pgrdtotdss=(double)$pgrdtotdss+(double)$pjmldss;
                            $pgrdtotrp=(double)$pgrdtotrp+(double)$pjmltot;

                            $pjmldcc=number_format($pjmldcc,0,",",",");
                            $pjmldss=number_format($pjmldss,0,",",",");
                            $pjmltot=number_format($pjmltot,0,",",",");
                            
                            

                            echo "<tr>";
                            echo "<td></td>";
                            echo "<td nowrap>$pdivprod</td>";
                            echo "<td nowrap align='right'>$pjmldcc</td>";
                            echo "<td nowrap align='right'>$pjmldss</td>";
                            echo "<td nowrap align='right'>$pjmltot</td>";
                            echo "</tr>";
                        }
                        
                        $psubtotdcc=number_format($psubtotdcc,0,",",",");
                        $psubtotdss=number_format($psubtotdss,0,",",",");
                        $psubtotrp=number_format($psubtotrp,0,",",",");
                        
                        echo "<tr style='font-weight: bold;'>";
                        echo "<td></td>";
                        echo "<td nowrap>Total $region</td>";
                        echo "<td nowrap align='right'>$psubtotdcc</td>";
                        echo "<td nowrap align='right'>$psubtotdss</td>";
                        echo "<td nowrap align='right'>$psubtotrp</td>";
                        echo "</tr>";
                            
                        
                    }
                    
                    $pgrdtotdcc=number_format($pgrdtotdcc,0,",",",");
                    $pgrdtotdss=number_format($pgrdtotdss,0,",",",");
                    $pgrdtotrp=number_format($pgrdtotrp,0,",",",");
                    
                    echo "<tr style='font-weight: bold;'>";
                    echo "<td></td>";
                    echo "<td nowrap>Grand Total </td>";
                    echo "<td nowrap align='right'>$pgrdtotdcc</td>";
                    echo "<td nowrap align='right'>$pgrdtotdss</td>";
                    echo "<td nowrap align='right'>$pgrdtotrp</td>";
                    echo "</tr>";
                    
                ?>
            </tbody>
        </table>
    <?PHP
    }else{
    ?>
        <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th nowrap>Region</th>
                    <?PHP
                    for($xi=1;$xi<=12;$xi++) {
                        $fbulan=$ptahun."-0".$xi;
                        if ((double)$xi >=10) $fbulan=$ptahun."-".$xi;
                        $fbulan .="-01";
                        $nmbulan= date("F", strtotime($fbulan));
                        echo "<th>DCC $nmbulan</th>";
                        echo "<th>DSS $nmbulan</th>";
                    }
                    ?>
                    <th nowrap>Total DCC</th>
                    <th nowrap>Total DSS</th>
                    <th nowrap>Total</th>
                </tr>
                
                
            </thead>
            <tbody>
                <?PHP
                    $psub_dcc[1]=0;$psub_dcc[2]=0;$psub_dcc[3]=0;$psub_dcc[4]=0;$psub_dcc[5]=0;$psub_dcc[6]=0;
                    $psub_dcc[7]=0;$psub_dcc[8]=0;$psub_dcc[9]=0;$psub_dcc[10]=0;$psub_dcc[11]=0;$psub_dcc[12]=0;

                    $psub_dss[1]=0;$psub_dss[2]=0;$psub_dss[3]=0;$psub_dss[4]=0;$psub_dss[5]=0;$psub_dss[6]=0;
                    $psub_dss[7]=0;$psub_dss[8]=0;$psub_dss[9]=0;$psub_dss[10]=0;$psub_dss[11]=0;$psub_dss[12]=0;

                    $psub_totdcc=0;
                    $psub_totdss=0;
                    $psub_total=0;
                        
                    $no=1;
                    
                    $group1 = mysqli_query($cnmy, "select * from $tmp05 order by region");
                    while ($g2=mysqli_fetch_array($group1)){
                        $region="Barat";
                        $pnreg=$g2['region'];
                        if ($pnreg=="T") $region="Timur";
                        if ($pnreg=="N") $region="None";
                        
                        $jml1=$g2['dcc'];
                        $jml2=$g2['dss'];
                        $total=$g2['total'];

                        $psub_totdcc=(double)$psub_totdcc+(double)$jml1;
                        $psub_totdss=(double)$psub_totdss+(double)$jml2;
                        $psub_total=(double)$psub_total+(double)$total;

                        $jml1=number_format($jml1,0,",",",");
                        $jml2=number_format($jml2,0,",",",");
                        $total=number_format($total,0,",",",");

                        if ($jml1=="0") $jml1="";
                        if ($jml2=="0") $jml2="";
                        if ($total=="0") $total="";
                        
                        
                        echo "<tr>";
                        echo "<td>$no</td>";
                        echo "<td nowrap>$region</td>";
                        $nno_xi=1;
                        for($xi=1;$xi<=12;$xi++) {
                            $fbulan=$ptahun."-0".$xi;
                            if ((double)$xi >=10) $fbulan=$ptahun."-".$xi;
                            $pblndcc = "dcc_".$xi;
                            $pblndss = "dss_".$xi;

                            $pjmldcc=$g2[$pblndcc];
                            $pjmldss=$g2[$pblndss];
                            if (empty($pjmldcc)) $pjmldcc=0;
                            if (empty($pjmldss)) $pjmldss=0;

                            $psub_dcc[$nno_xi]=(double)$psub_dcc[$nno_xi]+(double)$pjmldcc;
                            $psub_dss[$nno_xi]=(double)$psub_dss[$nno_xi]+(double)$pjmldss;
                            $nno_xi++;

                            $pjmldcc=number_format($pjmldcc,0,",",",");
                            $pjmldss=number_format($pjmldss,0,",",",");

                            if ($pjmldcc=="0") $pjmldcc="";
                            if ($pjmldss=="0") $pjmldss="";

                            echo "<td align='right'>$pjmldcc</td>";
                            echo "<td align='right'>$pjmldss</td>";    
                        }
                        echo "<td align='right'>$jml1</td>";
                        echo "<td align='right'>$jml2</td>";
                        echo "<td align='right'>$total</td>";
                        echo "</tr>";
                        $no++;
                        
                        
                    }
                    
                    echo "<tr>";
                    echo "<td></td>";
                    echo "<td nowrap><b>Total : </b></td>";
                    for($xi=1;$xi<=12;$xi++) {
                        if (empty($psub_dcc[$xi])) $psub_dcc[$xi]=0;
                        if (empty($psub_dss[$xi])) $psub_dss[$xi]=0;


                        $psub_dcc[$xi]=number_format($psub_dcc[$xi],0,",",",");
                        $psub_dss[$xi]=number_format($psub_dss[$xi],0,",",",");

                        if ($psub_dcc[$xi]=="0") $psub_dcc[$xi]="";
                        if ($psub_dss[$xi]=="0") $psub_dss[$xi]="";

                        echo "<td align='right'><b>$psub_dcc[$xi]</b></td>";
                        echo "<td align='right'><b>$psub_dss[$xi]</b></td>";
                    }


                    $psub_totdcc=number_format($psub_totdcc,0,",",",");
                    $psub_totdss=number_format($psub_totdss,0,",",",");
                    $psub_total=number_format($psub_total,0,",",",");

                    if ($psub_totdcc=="0") $psub_totdcc="";
                    if ($psub_totdss=="0") $psub_totdss="";
                    if ($psub_total=="0") $psub_total="";

                    echo "<td align='right'><b>$psub_totdcc</b></td>";
                    echo "<td align='right'><b>$psub_totdss</b></td>";
                    echo "<td align='right'><b>$psub_total</b></td>";
                    echo "</tr>";
                ?>

            </tbody>
        </table>
    <?PHP    
    }
    ?>    
    <p/>&nbsp;<p/>&nbsp;<p/>&nbsp;
</div>
    
    
    <?PHP if ($ppilihrpt!="excel") { ?>

        
        <!-- Bootstrap -->
        <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    
        <!-- Datatables -->
		
        <script src="vendors/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
        <script src="vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
        <script src="vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
        <script src="vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
        <script src="vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
        <script src="vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
        <script src="vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
        <script src="vendors/jszip/dist/jszip.min.js"></script>
        <script src="vendors/pdfmake/build/pdfmake.min.js"></script>
        <script src="vendors/pdfmake/build/vfs_fonts.js"></script>

        
        
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

        <style>
            #n_content {
                color:#000;
                font-family: "Arial";
                margin: 5px 20px 20px 20px;
                /*overflow-x:auto;*/
            }
            
            .h1judul {
              color: blue;
              font-family: verdana;
              font-size: 140%;
              font-weight: bold;
            }
            table.tbljudul {
                font-size : 15px;
            }
            table.tbljudul tr td {
                padding: 1px;
                font-family : "Arial, Verdana, sans-serif";
            }
            .tebal {
                 font-weight: bold;
            }
            .miring {
                 font-style: italic;
            }
            table.tbljudul tr.text2 {
                font-size : 13px;
            }
            
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
                z-index:1;
            }

            .th2 {
                background: white;
                position: sticky;
                top: 23;
                box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
                border-top: 1px solid #000;
            }
        </style>
    
        <style>
            
            .divnone {
                display: none;
            }
            #mydatatable1, #mydatatable2 {
                color:#000;
                font-family: "Arial";
            }
            #mydatatable1 th, #mydatatable2 th {
                font-size: 12px;
            }
            #mydatatable1 td, #mydatatable2 td { 
                font-size: 11px;
            }
        </style>
        
    <?PHP }else{ ?>
        <style>
            .h1judul {
              font-size: 140%;
              font-weight: bold;
            }
        </style>
    <?PHP } ?>
        
        
</BODY>


    <script>
        // SCROLL
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
        // END SCROLL
    </script>
    
</HTML>




<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp05");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp06");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp10");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp11");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp12");
    mysqli_close($cnmy);
?>