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
        header("Content-Disposition: attachment; filename=Laporan General Ledger.xls");
    }
    
    include("config/koneksimysqli.php");
    
    $printdate= date("d/m/Y");
    
    $picardid=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];
    
    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmplapglpil00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmplapglpil01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmplapglpil02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmplapglpil03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmplapglpil04_".$puserid."_$now ";
    $tmp05 =" dbtemp.tmplapglpil05_".$puserid."_$now ";
    $tmp06 =" dbtemp.tmplapglpil06_".$puserid."_$now ";
    $tmp07 =" dbtemp.tmplapglpil07_".$puserid."_$now ";
    $tmp08 =" dbtemp.tmplapglpil08_".$puserid."_$now ";
    $tmp10 =" dbtemp.tmplapglpil10_".$puserid."_$now ";
    
?>

<?PHP
    include "config/fungsi_combo.php";
    include("config/common.php");
    
    $prpttype=$_POST['radio1'];
    $pdivisi=$_POST['divprodid'];

    $filtercoa=('');
    if (!empty($_POST['chkbox_coa'])){
        $filtercoa=$_POST['chkbox_coa'];
        $filtercoa=PilCekBoxAndEmpty($filtercoa);
    }

    $tgl01 = $_POST['e_tgl1'];
    $tgl02 = $_POST['e_tgl2'];

    $pperiode1 = date("Y-m", strtotime($tgl01));
    $pperiode2 = date("Y-m", strtotime($tgl02));
        
    $myperiode1 = date("F Y", strtotime($tgl01));
    $myperiode2 = date("F Y", strtotime($tgl02));

    $pbreth="";
    $pklaim="";
    $pkas="";
    $pbrotc="";
    $prutin="";
    $pblk="";
    $pca="";
    $pbmsby="";
    $ppilbank="";
    $ppilinsen="";
        
        
    if (isset($_POST['chkbox_rpt1'])) $pbreth=$_POST['chkbox_rpt1'];
    if (isset($_POST['chkbox_rpt2'])) $pklaim=$_POST['chkbox_rpt2'];
    if (isset($_POST['chkbox_rpt3'])) $pkas=$_POST['chkbox_rpt3'];
    if (isset($_POST['chkbox_rpt4'])) $pbrotc=$_POST['chkbox_rpt4'];
    if (isset($_POST['chkbox_rpt5'])) $prutin=$_POST['chkbox_rpt5'];
    if (isset($_POST['chkbox_rpt6'])) $pblk=$_POST['chkbox_rpt6'];
    if (isset($_POST['chkbox_rpt7'])) $pca=$_POST['chkbox_rpt7'];
    if (isset($_POST['chkbox_rpt8'])) $pbmsby=$_POST['chkbox_rpt8'];
    if (isset($_POST['chkbox_rpt9'])) $ppilbank=$_POST['chkbox_rpt9'];
    if (isset($_POST['chkbox_rpt10'])) $ppilinsen=$_POST['chkbox_rpt10'];
        
        
    if ($pdivisi=="OTC") {
        $pbreth="";
        $pklaim="";
        $pkas="";
    }else{
        if (!empty($pdivisi)) {
            $pbrotc="";
            if ($pdivisi!="HO") {
                $pkas="";
            }

            if ($pdivisi!="EAGLE") {
                $pklaim="";
            }
        }
    }
    
    
    $query = "CREATE TEMPORARY TABLE $tmp01 (noidauto BIGINT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, kodeinput VARCHAR(2), idinput VARCHAR(20), divisi VARCHAR(5), 
            tgltrans date, bukti VARCHAR(50), coa VARCHAR(30), nama_coa VARCHAR(200), 
            dokter VARCHAR(200), noslip VARCHAR(50), pengajuan VARCHAR(200), keterangan VARCHAR(300), 
            nmrealisasi VARCHAR(100), mintadana DECIMAL(20,2), debit DECIMAL(20,2), kredit DECIMAL(20,2), saldo DECIMAL(20,2), 
            dpp DECIMAL(20,2), ppn DECIMAL(20,2), pph DECIMAL(20,2), tglfp date, 
            tglfp_ppn date, seri_ppn VARCHAR(200), tglfp_pph date, seri_pph VARCHAR(200), nodivisi VARCHAR(100), divisi2 VARCHAR(5) )";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "ALTER table $tmp01 ADD COLUMN idinputdiv BIGINT(20)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
    
    
    $query = "select noidauto, kodeinput, idkodeinput as idinput, divisi, tgltrans, nobukti as bukti, "
            . " coa, nama_coa, dokter_nama as dokter, noslip, pengajuan, keterangan, "
            . " nmrealisasi, rincian as mintadana, debit, kredit, "
            . " dpp, ppn, pph, tglfp, nodivisi, divisi2, divisi2, idinput_pd "
            . " from dbmaster.t_proses_bm_act WHERE DATE_FORMAT(tgltrans,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
    if (!empty($pdivisi)) $query .=" AND divisi='$pdivisi' ";
    if (!empty($filtercoa)) $query .=" AND IFNULL(coa,'') IN $filtercoa ";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
        $query = "CREATE INDEX `norm1` ON $tmp01 (kodeinput,idinput,divisi,tgltrans,coa)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        
    
    $query = "select distinct b.idinput, b.divisi, b.nodivisi, a.kodeinput, a.bridinput, b.pilih from dbmaster.t_suratdana_br1 a "
            . " JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput WHERE "
            . " IFNULL(b.stsnonaktif,'')<>'Y' AND IFNULL(b.nodivisi,'')<>'' ";
    $query = "create TEMPORARY table $tmp10 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        $query = "CREATE INDEX `norm1` ON $tmp10 (idinput,divisi,nodivisi,kodeinput,bridinput, pilih)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
     
    
    if (!empty($pbreth)) {
        
        $query = "select brId, noslip, icabangid, tgl, tgltrans, divprodid, COA4, kode, realisasi1, "
                . " jumlah, jumlah1, jumlah jumlah_asli, jumlah1 as jumlah1_asli, "
                . " aktivitas1, aktivitas2, dokterId, dokter, karyawanId, ccyId, tgltrm, lampiran, ca, "
                . " dpp, ppn_rp, pph_rp, tgl_fp, CAST('' as CHAR(20)) as nobukti "
                . " from hrd.br0 WHERE IFNULL(batal,'')<>'Y' AND "
                . " brId NOT IN (select DISTINCT IFNULL(brId,'') FROM hrd.br0_reject) AND "
                . " DATE_FORMAT(tgltrans,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
        if (!empty($pdivisi)) $query .=" AND divprodid='$pdivisi' ";
        if (!empty($filtercoa)) $query .=" AND IFNULL(COA4,'') IN $filtercoa ";
        
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp02 (brId,dokterId)";
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
                    . " DATE_FORMAT(a.tgltransfersby,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2'";
            if (!empty($pdivisi)) $query .=" AND b.divprodid='$pdivisi' ";
            if (!empty($filtercoa)) $query .=" AND IFNULL(b.COA4,'') IN $filtercoa ";
            $query = "create TEMPORARY table $tmp05 ($query)"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "CREATE INDEX `norm1` ON $tmp05 (brId,dokterId)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        
            
            $query = "DELETE FROM $tmp02 WHERE brId IN (select distinct IFNULL(brId,'') FROM $tmp05)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            $query = "INSERT INTO $tmp02 (brId, noslip, icabangid, tgl, tgltrans, divprodid, "
                    . " COA4, kode, realisasi1, jumlah, jumlah1, jumlah_asli, jumlah1_asli, "
                    . " aktivitas1, aktivitas2, dokterId, dokter, karyawanId, ccyId, tgltrm, lampiran, ca, "
                    . " dpp, ppn_rp, pph_rp, tgl_fp, nobukti) "
                    . " select brId, noslip, icabangid, tgl, tgltrans, divprodid, "
                    . " COA4, kode, realisasi1, jumlah, jumlah1, jumlah_asli, jumlah1_asli, "
                    . " aktivitas1, aktivitas2, dokterId, dokter, karyawanId, ccyId, tgltrm, lampiran, ca, "
                    . " dpp, ppn_rp, pph_rp, tgl_fp, nobukti "
                    . " from $tmp05 ";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            //END via SBY
            
            
            
        $query = "UPDATE $tmp02 SET jumlah=jumlah1 WHERE IFNULL(jumlah1,0)<>0";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        $query = "UPDATE $tmp02 SET jumlah_asli=NULL, jumlah1_asli=NULL"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "select dokterId, nama from hrd.dokter WHERE dokterId IN (select distinct IFNULL(dokterId,'') from $tmp02)";
        $query = "create TEMPORARY table $tmp04 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp04 (dokterId)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        
            
            
        
        $query = "select a.*, d.nama nama_dokter, e.nama nama_karyawan, b.nama nama_cabang, c.nama nama_kode, "
                . " CAST('' as CHAR(50)) as nodivisi "
                . " from $tmp02 a LEFT JOIN mkt.icabang b on a.icabangid=b.icabangid "
                . " LEFT JOIN hrd.br_kode c on a.kode=c.kodeid "
                . " LEFT JOIN $tmp04 d on a.dokterId=d.dokterId"
                . " LEFT JOIN hrd.karyawan e on a.karyawanId=e.karyawanId";
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        
        $query = "ALTER table $tmp03 ADD COLUMN idinput BIGINT(20)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp03 a JOIN (select distinct pilih, nodivisi, idinput, bridinput FROM $tmp10 WHERE kodeinput IN ('A', 'B', 'C') AND divisi<>'OTC') b on a.brId=b.bridinput "
                . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput WHERE b.pilih='Y'"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        $query = "UPDATE $tmp03 a JOIN (select distinct pilih, nodivisi, idinput, bridinput FROM $tmp10 WHERE kodeinput IN ('A', 'B', 'C') AND divisi<>'OTC') b on a.brId=b.bridinput "
                . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput WHERE b.pilih='N' AND IFNULL(a.nodivisi,'')=''"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        
        
        $query ="DELETE FROM $tmp03 WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(tgl,'%Y-%m')>='2020-01'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "INSERT INTO $tmp01 (kodeinput, idinput, divisi, tgltrans, coa, dokter, noslip, pengajuan, keterangan, nmrealisasi, kredit, dpp, ppn, pph, tglfp, nodivisi, idinputdiv, bukti)"
                . " SELECT 'A' kodeinput, brId, divprodid, tgltrans, COA4, nama_dokter, noslip, nama_karyawan, aktivitas1, realisasi1, jumlah, dpp, ppn_rp, pph_rp, tgl_fp, nodivisi, idinput, nobukti FROM $tmp03";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
    
    }
        
    
    if (!empty($pklaim)) {
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
        
        $query = "select DIVISI divprodid, tgl, tgltrans, distid, klaimId, COA4, karyawanid, noslip, "
                . " aktivitas1, realisasi1 nmrealisasi, jumlah, dpp, ppn_rp, pph_rp, tgl_fp, pengajuan divpengajuan "
                . " FROM hrd.klaim WHERE "
                . " klaimId not in (SELECT DISTINCT ifnull(klaimId,'') from hrd.klaim_reject) AND "
                . " DATE_FORMAT(tgltrans,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
        if (!empty($pdivisi)) $query .=" AND DIVISI='$pdivisi' ";
        if (!empty($filtercoa)) $query .=" AND IFNULL(COA4,'') IN $filtercoa ";
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp02 (klaimId)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        
        
        $query = "select a.*, b.nama nama_karyawan, "
                . " CAST('' as CHAR(50)) as nodivisi "
                . " FROM $tmp02 a "
                . " LEFT JOIN hrd.karyawan b on a.karyawanid=b.karyawanId";
        $query = "create TEMPORARY table $tmp03 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        $query = "ALTER table $tmp03 ADD COLUMN idinput BIGINT(20)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp03 (klaimId)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp03 a JOIN (select distinct pilih, nodivisi, idinput, bridinput FROM $tmp10 WHERE kodeinput IN ('E') AND divisi='EAGLE') b on a.klaimId=b.bridinput "
                . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput WHERE b.pilih='Y'"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        $query = "UPDATE $tmp03 a JOIN (select distinct pilih, nodivisi, idinput, bridinput FROM $tmp10 WHERE kodeinput IN ('E') AND divisi='EAGLE') b on a.klaimId=b.bridinput "
                . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput WHERE b.pilih='N' AND IFNULL(a.nodivisi,'')=''"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query ="DELETE FROM $tmp03 WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(tgl,'%Y-%m')>='2020-01'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        $query = "INSERT INTO $tmp01 (kodeinput, idinput, divisi, tgltrans, coa, noslip, pengajuan, keterangan, nmrealisasi, kredit, dpp, ppn, pph, tglfp, nodivisi, idinputdiv)"
                . "SELECT 'E', klaimId, divprodid, tgltrans, COA4, noslip, nama_karyawan, aktivitas1, nmrealisasi, jumlah, dpp, ppn_rp, pph_rp, tgl_fp, nodivisi, idinput FROM $tmp03";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
    }



    
    if (!empty($pkas)) {
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
        
        $query = "select e.DIVISI2 divprodid, a.periode1 tgltrans, a.kasId, a.kode, b.COA4, a.karyawanid, f.nama nama_karyawan, a.nobukti,
                a.aktivitas1, a.jumlah
                FROM hrd.kas a LEFT JOIN dbmaster.posting_coa_kas b on a.kode=b.kodeid
                LEFT JOIN dbmaster.coa_level4 c on b.COA4=c.COA4 LEFT JOIN dbmaster.coa_level3 d on c.COA3=d.COA3
                LEFT JOIN dbmaster.coa_level2 e on e.COA2=d.COA2
                LEFT JOIN hrd.karyawan f on a.karyawanid=f.karyawanId 
                WHERE DATE_FORMAT(a.periode1,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
        if (!empty($pdivisi)) $query .=" AND 'HO'='$pdivisi' ";
        //if (!empty($filtercoa)) $query .=" AND IFNULL(b.COA4,'') IN $filtercoa ";
        
        
        
        $queryXXX = "select 'HO' as divprodid, a.periode1 tgltrans, a.kasId, a.kode, '105-02' as COA4, a.karyawanid, f.nama nama_karyawan, a.nobukti,
                a.aktivitas1, a.jumlah
                FROM hrd.kas a LEFT JOIN dbmaster.posting_coa_kas b on a.kode=b.kodeid
                LEFT JOIN dbmaster.coa_level4 c on '105-02'=c.COA4 LEFT JOIN hrd.karyawan f on a.karyawanid=f.karyawanId 
                WHERE DATE_FORMAT(a.periode1,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
        //if (!empty($pdivisi)) $query .=" AND 'HO'='$pdivisi' ";
        //if (!empty($filtercoa)) $query .=" AND '105-02' IN $filtercoa ";
        
        
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp02 (kasId)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
        $query = "select e.DIVISI2 divisi, a.tgl, a.idkasbon, a.kode, b.COA4, a.karyawanid, a.nama nama_karyawan, '' as nobukti,
                a.keterangan, a.jumlah
                FROM dbmaster.t_kasbon a LEFT JOIN dbmaster.posting_coa_kas b on a.kode=b.kodeid
                LEFT JOIN dbmaster.coa_level4 c on b.COA4=c.COA4 LEFT JOIN dbmaster.coa_level3 d on c.COA3=d.COA3
                LEFT JOIN dbmaster.coa_level2 e on e.COA2=d.COA2
                LEFT JOIN hrd.karyawan f on a.karyawanid=f.karyawanId WHERE 
                IFNULL(a.stsnonaktif,'')<>'Y' AND DATE_FORMAT(a.tgl,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
        if (!empty($pdivisi)) $query .=" AND 'HO'='$pdivisi' ";
        //if (!empty($filtercoa)) $query .=" AND b.COA4 IN $filtercoa ";
        
        $queryxxx="select 'HO' as divisi, a.tgl, a.idkasbon, '' as kode, '105-02' as COA4, a.karyawanid, nama as nama_karyawan, '' as nobukti,
                a.keterangan, a.jumlah
                FROM dbmaster.t_kasbon a LEFT JOIN dbmaster.coa_level4 c on '105-02'=c.COA4 
                WHERE IFNULL(a.stsnonaktif,'')<>'Y' AND DATE_FORMAT(a.tgl,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2'";
        //if (!empty($pdivisi)) $query .=" AND 'HO'='$pdivisi' ";
        //if (!empty($filtercoa)) $query .=" AND '105-02' IN $filtercoa ";
        
        $query = "create TEMPORARY table $tmp05 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp05 (idkasbon)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
                        //$query = "DELETE FROM  $tmp02";
                        //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
        $query = "INSERT INTO $tmp02 (divprodid, tgltrans, kasId, kode, COA4, karyawanid, nama_karyawan, nobukti, aktivitas1, jumlah)"
                . " select divisi, tgl, idkasbon, kode, COA4, karyawanid, nama_karyawan, nobukti, keterangan, jumlah from $tmp05";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        
        
        
        $query = "select a.*, CAST('' as CHAR(50)) as nodivisi "
                . " from $tmp02 a";
        $query = "create TEMPORARY table $tmp03 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "ALTER table $tmp03 ADD COLUMN idinput BIGINT(20)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp03 (kasId)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        
        $query = "UPDATE $tmp03 a JOIN (select distinct pilih, nodivisi, idinput, bridinput FROM $tmp10 WHERE kodeinput IN ('T', 'K')) b on a.kasId=b.bridinput "
                . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        
        $query ="DELETE FROM $tmp03 WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(tgltrans,'%Y-%m')>='2020-01'";
        //mysqli_query($cnmy, $query);
        //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query ="UPDATE $tmp03 SET COA4='105-02' WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(tgltrans,'%Y-%m')>='2020-01'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
                $query ="DELETE FROM $tmp03 WHERE 1=1 ";
                if (!empty($filtercoa)) $query .=" AND IFNULL(COA4,'') NOT IN $filtercoa ";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
        
        
        $query = "INSERT INTO $tmp01 (kodeinput, idinput, divisi, tgltrans, coa, pengajuan, keterangan, kredit, nodivisi, idinputdiv)"
                . "SELECT 'K', kasId, divprodid, tgltrans, COA4, nama_karyawan, aktivitas1, jumlah, nodivisi, idinput FROM $tmp03";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
        
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
        
    }
    
    


    if (!empty($pbrotc)) {
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
        
        $query = "select brOtcId, noslip, icabangid_o, tglbr, tgltrans, COA4, kodeid, subpost, real1, "
                . " jumlah, realisasi, jumlah jumlah_asli, realisasi as realisasi_asli, "
                . " keterangan1, keterangan2, lampiran, ca, dpp, ppn_rp, pph_rp, tgl_fp "
                . " from hrd.br_otc WHERE IFNULL(batal,'')<>'Y' AND "
                . " brOtcId NOT IN (select DISTINCT IFNULL(brOtcId,'') FROM hrd.br_otc_reject) AND "
                . " DATE_FORMAT(tgltrans, '%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
        if (!empty($filtercoa)) $query .=" AND IFNULL(COA4,'') IN $filtercoa ";
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp02 (brOtcId, icabangid_o)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        
        $query = "UPDATE $tmp02 SET jumlah=realisasi WHERE IFNULL(realisasi,0)<>0";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        $query = "UPDATE $tmp02 SET jumlah_asli=NULL, realisasi_asli=NULL"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "select a.*, b.nama nama_cabang, c.nama nama_kode, "
                . " CAST('' as CHAR(50)) as nodivisi "
                . " from $tmp02 a LEFT JOIN mkt.icabang_o b on a.icabangid_o=b.icabangid_o "
                . " LEFT JOIN hrd.brkd_otc c on a.kodeid=c.kodeid and a.subpost=c.subpost";
        $query = "create TEMPORARY table $tmp03 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        $query = "ALTER table $tmp03 ADD COLUMN idinput BIGINT(20)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp03 (brOtcId, icabangid_o)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
       
        
        
        $query = "UPDATE $tmp03 a JOIN (select distinct pilih, nodivisi, idinput, bridinput FROM $tmp10 WHERE kodeinput IN ('D') AND divisi='OTC') b on a.brOtcId=b.bridinput "
                . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput WHERE b.pilih='Y'"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        $query = "UPDATE $tmp03 a JOIN (select distinct pilih, nodivisi, idinput, bridinput FROM $tmp10 WHERE kodeinput IN ('D') AND divisi='OTC') b on a.brOtcId=b.bridinput "
                . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput WHERE b.pilih='N' AND IFNULL(a.nodivisi,'')=''"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
        $query ="DELETE FROM $tmp03 WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(tglbr,'%Y-%m')>='2020-01'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "INSERT INTO $tmp01 (kodeinput, idinput, divisi, tgltrans, coa, noslip, keterangan, nmrealisasi, kredit, dpp, ppn, pph, tglfp, nodivisi, idinputdiv)"
                . "SELECT 'D' kodeinput, brOtcId, 'OTC' as divprodid, tgltrans, COA4, noslip, keterangan1, real1, jumlah, dpp, ppn_rp, pph_rp, tgl_fp, nodivisi, idinput FROM $tmp03";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");    
        
    }
    
    
    //BM biaya marketing surabaya
    if (!empty($pbmsby)) {

        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp06");

        $query = "SELECT ID, TANGGAL, NOBBM, NOBBK, DIVISI, COA4, COA4_K, DEBIT, KREDIT, SALDO, KETERANGAN, CAST('' as CHAR(50)) as NOBUKTI FROM dbmaster.t_bm_sby WHERE "
                . " IFNULL(STSNONAKTIF,'')<>'Y' AND DATE_FORMAT(TANGGAL,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
        if (!empty($pdivisi)) $query .=" AND DIVISI='$pdivisi' ";
        if (!empty($filtercoa)) $query .=" AND ( IFNULL(COA4,'') IN $filtercoa OR IFNULL(COA4_K,'') IN $filtercoa ) ";
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp02 (ID)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
        $query = "UPDATE $tmp02 SET NOBUKTI=NOBBM WHERE IFNULL(NOBBM,'')<>''";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp02 SET NOBUKTI=NOBBK WHERE IFNULL(NOBBK,'')<>''";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        //NOTE :  di GL ada di posisi DEBIT jadi di Balik DEBIT = KREDIT begitu juga KREDIT = DEBIT

        //debit diinpsert jadi kredit
        $query = "INSERT INTO $tmp01 (kodeinput, idinput, divisi, tgltrans, coa, keterangan, bukti, kredit)"
                . "SELECT 'M' kodeinput, ID, DIVISI, TANGGAL, COA4, KETERANGAN, NOBUKTI, DEBIT FROM $tmp02 WHERE 1=1 ";
        if (!empty($filtercoa)) $query .=" AND IFNULL(COA4,'') IN $filtercoa ";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        //kredit diinpsert jadi debit
        $query = "INSERT INTO $tmp01 (kodeinput, idinput, divisi, tgltrans, coa, keterangan, bukti, debit)"
                . "SELECT 'N' kodeinput, ID, DIVISI, TANGGAL, COA4_K, KETERANGAN, NOBUKTI, KREDIT FROM $tmp02 WHERE 1=1 ";
        if (!empty($filtercoa)) $query .=" AND IFNULL(COA4_K,'') IN $filtercoa ";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    }
    
    //BANK
    if (!empty($ppilbank)) {
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
        
        $query = "select nourut, bulan, tanggal, nobukti, divisi, nodivisi, idinput, coa4, "
                . " keterangan, debit, kredit, mintadana, idinputbank, stsinput, jmlothselisih "
                . " from dbmaster.t_bank_saldo_d "
                . " where IFNULL(idinputbank,'')<>'SAWAL' AND "
                . " DATE_FORMAT(bulan,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
        if (!empty($pdivisi)) $query .=" AND divisi='$pdivisi' ";
        if (!empty($filtercoa)) $query .=" AND IFNULL(coa4,'') IN $filtercoa ";
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp02 (nourut, bulan, tanggal, nobukti, divisi)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
           
            $query = "select *, REPLACE(idinputbank,'OT','') as iidasli from $tmp02 WHERE left(idinputbank,2)='OT'";
            $query = "create TEMPORARY table $tmp05 ($query)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "ALTER TABLE $tmp05 MODIFY COLUMN iidasli BIGINT(18)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "CREATE INDEX `norm1` ON $tmp05 (iidasli, idinputbank, divisi, nodivisi)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "select a.idinputbank, a.iidasli, a.stsinput, a.divisi, a.coa4, a.nodivisi, a.idinput, 
                a.nobukti, a.bulan bulanots, c.bulan, a.tanggal, b.karyawanid, 
                d.idrutin, d.idca1, d.idca2, d.credit, d.saldo, d.ca1, d.ca2, 
                d.jmltrans, d.selisih, a.debit, a.mintadana, a.jmlothselisih, a.keterangan   
                from $tmp05 a 
                LEFT JOIN dbmaster.t_brrutin_outstanding b ON a.iidasli=b.idots
                LEFT JOIN dbmaster.t_brrutin_ca_close_head c on a.idinput=c.idinput
                LEFT JOIN dbmaster.t_brrutin_ca_close d on c.igroup=d.igroup and d.karyawanid=b.karyawanid";
            
            $query = "create TEMPORARY table $tmp06 ($query)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "select distinct a.tanggal, a.karyawanid, a.stsinput, a.divisi, a.nobukti, 
                    a.nodivisi, a.idinput, a.bulan, a.idrutin, a.credit, b.notes keterangan, a.keterangan pengajuan, b.coa, b.rptotal, 
                    b.nobrid, c.nama namabrid from $tmp06 a JOIN dbmaster.t_brrutin1 b on a.idrutin=b.idrutin
                    LEFT JOIN dbmaster.t_brid c on b.nobrid=c.nobrid";
            $query = "create TEMPORARY table $tmp07 ($query)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            $query = "nourutauto BIGINT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, divisi varchar(10), karyawanid varchar(10), "
                    . " nobukti varchar(50), nodivisi varchar(50), idinput BIGINT(20), coa varchar(20), nama_coa varchar(100), "
                    . " bulan date, myid varchar(50), keterangan varchar(200), pengajuan varchar(100), debit DECIMAL(20,2), kredit DECIMAL(20,2)";
            $query = "create TEMPORARY table $tmp08 ($query)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "CREATE INDEX `norm1` ON $tmp08 (nourutauto, divisi, karyawanid, nobukti, nodivisi, idinput)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "select distinct tanggal, nobukti, karyawanid from $tmp06 where stsinput='D' order by divisi, tanggal, nobukti";
            $tampil=mysqli_query($cnmy, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $pnobuk=$row['nobukti'];
                $pkryid=$row['karyawanid'];
                $pbln=$row['tanggal'];
                $pbln = date("Y-m", strtotime($pbln));
                
                //CA1 U.M BIAYA
                $query = "insert into $tmp08 (divisi, karyawanid, nobukti, nodivisi, idinput, coa, bulan, myid, pengajuan, debit)"
                        . " SELECT distinct divisi, karyawanid, nobukti, nodivisi, idinput, '105-02' coa, bulan, idca1, keterangan, ca1 "
                        . " FROM $tmp06 where karyawanid='$pkryid' AND stsinput='D' AND DATE_FORMAT(tanggal,'%Y-%m')='$pbln' "
                        . " AND nobukti='$pnobuk' AND IFNULL(idca1,'')<>''";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                //CA1 BCA
                $query = "insert into $tmp08 (divisi, karyawanid, nobukti, nodivisi, idinput, coa, bulan, myid, pengajuan, kredit)"
                        . " SELECT distinct divisi, karyawanid, nobukti, nodivisi, idinput, '101-02-002' coa, bulan, idca1, keterangan, ca1 "
                        . " FROM $tmp06 where karyawanid='$pkryid' AND stsinput='D' AND DATE_FORMAT(tanggal,'%Y-%m')='$pbln' "
                        . " AND nobukti='$pnobuk' AND IFNULL(idca1,'')<>''";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                
                //BL
                $query = "insert into $tmp08 (divisi, karyawanid, nobukti, nodivisi, idinput, coa, bulan, myid, keterangan, pengajuan, debit) "
                        . " SELECT distinct divisi, karyawanid, nobukti, nodivisi, idinput, coa, bulan, idrutin, CONCAT(namabrid, ' ',keterangan) as keterangan, pengajuan, rptotal "
                        . " FROM $tmp07 where karyawanid='$pkryid' AND stsinput='D' AND DATE_FORMAT(tanggal,'%Y-%m')='$pbln' "
                        . " AND nobukti='$pnobuk' AND IFNULL(idrutin,'')<>''"
                        . " order by divisi, tanggal, nobukti, idrutin";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                //BL SUM
                $query = "insert into $tmp08 (divisi, karyawanid, nobukti, nodivisi, idinput, coa, bulan, pengajuan, kredit) "
                        . " SELECT divisi, karyawanid, nobukti, nodivisi, idinput, '105-02' as coa, bulan, pengajuan, sum(rptotal) as rptotal "
                        . " FROM $tmp07 where karyawanid='$pkryid' AND stsinput='D' AND DATE_FORMAT(tanggal,'%Y-%m')='$pbln' "
                        . " AND nobukti='$pnobuk' AND IFNULL(idrutin,'')<>''"
                        . " GROUP BY 1,2,3,4,5,6";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                
                //CA2 U.M BIAYA
                $query = "insert into $tmp08 (divisi, karyawanid, nobukti, nodivisi, idinput, coa, bulan, myid, pengajuan, debit)"
                        . " SELECT distinct divisi, karyawanid, nobukti, nodivisi, idinput, '105-02' coa, bulan, idca2, keterangan, ca2 "
                        . " FROM $tmp06 where karyawanid='$pkryid' AND stsinput='D' AND DATE_FORMAT(tanggal,'%Y-%m')='$pbln' "
                        . " AND nobukti='$pnobuk' AND IFNULL(idca2,'')<>''";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                //CA2 BCA
                $query = "insert into $tmp08 (divisi, karyawanid, nobukti, nodivisi, idinput, coa, bulan, myid, pengajuan, kredit)"
                        . " SELECT distinct divisi, karyawanid, nobukti, nodivisi, idinput, '101-02-002' coa, bulan, idca2, keterangan, ca2 "
                        . " FROM $tmp06 where karyawanid='$pkryid' AND stsinput='D' AND DATE_FORMAT(tanggal,'%Y-%m')='$pbln' "
                        . " AND nobukti='$pnobuk' AND IFNULL(idca2,'')<>''";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                //OUTSTANDING TRF
                $query = "insert into $tmp08 (divisi, karyawanid, nobukti, nodivisi, idinput, coa, bulan, myid, pengajuan, debit)"
                        . " SELECT distinct divisi, karyawanid, nobukti, nodivisi, idinput, '101-02-002' coa, bulan, '' as idinputbank, keterangan, debit "
                        . " FROM $tmp06 where karyawanid='$pkryid' AND stsinput='D' AND DATE_FORMAT(tanggal,'%Y-%m')='$pbln' "
                        . " AND nobukti='$pnobuk' AND IFNULL(idca2,'')<>''";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                //OUTSTANDING TRF U.M BIAYA
                $query = "insert into $tmp08 (divisi, karyawanid, nobukti, nodivisi, idinput, coa, bulan, myid, pengajuan, kredit)"
                        . " SELECT distinct divisi, karyawanid, nobukti, nodivisi, idinput, '105-02' coa, bulan, '' as idinputbank, keterangan, debit "
                        . " FROM $tmp06 where karyawanid='$pkryid' AND stsinput='D' AND DATE_FORMAT(tanggal,'%Y-%m')='$pbln' "
                        . " AND nobukti='$pnobuk' AND IFNULL(idca2,'')<>''";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                
            }
            
                                    //mysqli_query($cnmy, "delete from $tmp08 where karyawanid<>'0000002136'");
                                    //mysqli_query($cnmy, "delete from $tmp02");
                                    //mysqli_query($cnmy, "delete from $tmp05 where nobukti<>'BBM1505/B/2020'");
            
                                    
                                    
                            $query = "UPDATE $tmp08 a JOIN dbmaster.coa_level4 b on a.coa=b.COA4 "
                                    . "SET a.nama_coa=b.NAMA4"; 
                            mysqli_query($cnmy, $query);
                            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                            
        /* 
            
            $query = "select a.idinputbank, a.iidasli, c.periode_ca2, a.divisi, a.coa4, e.nodivisi, d.idinput, a.nobukti, a.bulan bulanots, c.bulan, a.tanggal, b.karyawanid, 
                c.idrutin, c.saldo, c.ca1, c.ca2, 
                c.selisih, c.jmltrans 
                from $tmp05 a 
                LEFT JOIN dbmaster.t_brrutin_outstanding b ON a.iidasli=b.idots 
                LEFT JOIN dbmaster.t_brrutin_ca_close c on 
                b.karyawanid=c.karyawanid AND DATE_FORMAT(b.bulan,'%Y%m')=DATE_FORMAT(c.bulan,'%Y%m') 

                LEFT JOIN dbmaster.t_brrutin_ca_close_head d on c.igroup=d.igroup 
                LEFT JOIN dbmaster.t_suratdana_br e on d.idinput=e.idinput";
            $query = "create TEMPORARY table $tmp06 ($query)";//AND date_format(a.bulan - INTERVAL '1' MONTH,'%Y%m')=DATE_FORMAT(c.bulan,'%Y%m')
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "CREATE INDEX `norm1` ON $tmp06 (iidasli, divisi, nodivisi, idinput, bulan)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            $query = "UPDATE $tmp02 a JOIN (select distinct bulan, idinputbank, nodivisi FROM $tmp06 WHERE IFNULL(nodivisi,'')<>'' and periode_ca2<>'1') b on "
                    . " DATE_FORMAT(a.bulan - INTERVAL '1' MONTH,'%Y%m')=DATE_FORMAT(b.bulan,'%Y%m') SET a.nodivisi=b.nodivisi WHERE "
                    . " left(a.idinputbank,2)='OT'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        */    
            
                            //hapus outstanding bank
                            $query = "delete from $tmp02 WHERE left(idinputbank,2)='OT' AND IFNULL(stsinput,'') IN ('D')";
                            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
                            $query = "INSERT INTO $tmp01 (kodeinput, idinput, divisi, tgltrans, coa, keterangan, bukti, debit, nodivisi)"
                                    . "SELECT 'Z' kodeinput, nourut, divisi, tanggal, coa4, keterangan, nobukti, debit, nodivisi FROM $tmp05 WHERE "
                                    . " IFNULL(debit,0)<>0 AND IFNULL(stsinput,'') IN ('D')";
                            mysqli_query($cnmy, $query);
                            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                            
                            
                            
                            
        $query = "UPDATE $tmp02 SET nodivisi='' where IFNULL(nodivisi,'')='0'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        $query = "UPDATE $tmp02 SET divisi='CAN' WHERE keterangan LIKE '%outstanding%' AND IFNULL(divisi,'')=''";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        //debit diinpsert jadi kredit
        $query = "INSERT INTO $tmp01 (kodeinput, idinput, divisi, tgltrans, coa, keterangan, bukti, debit, nodivisi)"
                . "SELECT 'O' kodeinput, nourut, divisi, tanggal, coa4, keterangan, nobukti, debit, nodivisi FROM $tmp02 WHERE IFNULL(debit,0)<>0 ";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        //kredit diinpsert jadi debit
        $query = "INSERT INTO $tmp01 (kodeinput, idinput, divisi, tgltrans, coa, keterangan, bukti, kredit, nodivisi)"
                . "SELECT 'O' kodeinput, nourut, divisi, tanggal, coa4, keterangan, nobukti, kredit, nodivisi FROM $tmp02 WHERE IFNULL(kredit,0)<>0 ";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "select * from $tmp02 WHERE left(idinputbank,2)='OT' AND IFNULL(stsinput,'')='M'";
        $query = "create TEMPORARY table $tmp03 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "select *, divisi as divisi2 from $tmp02 WHERE IFNULL(mintadana,0)<>0 AND left(idinputbank,2)='OT' AND IFNULL(stsinput,'')='N'";
        $query = "create TEMPORARY table $tmp04 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        
        $query = "UPDATE $tmp04 a JOIN $tmp03 b on a.nobukti=b.nobukti AND a.nodivisi=b.nodivisi and a.tanggal=b.tanggal SET a.divisi=b.divisi";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
        if ($prpttype=="D") {
            $query = "INSERT INTO $tmp01 (kodeinput, idinput, divisi, tgltrans, coa, keterangan, bukti, mintadana, nodivisi, divisi2)"
                    . "SELECT 'O' kodeinput, nourut, divisi, tanggal, coa4, keterangan, nobukti, mintadana, nodivisi, divisi2 FROM $tmp04 ";//WHERE IFNULL(mintadana,0)<>0 AND left(idinputbank,2)='OT' AND IFNULL(stsinput,'')='N'
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        }
        
        
            
    }
    
    
    
    //insentif incentive
    
        $filterpilih=false;
        
        
        if (strpos($filtercoa, "701-05")==true) $filterpilih=true;//eagle
        if (strpos($filtercoa, "702-05")==true) $filterpilih=true;//pigeo
        if (strpos($filtercoa, "703-05")==true) $filterpilih=true;//peaco
        if (strpos($filtercoa, "704-05")==true) $filterpilih=true;//otc
        if (strpos($filtercoa, "705-05")==true) $filterpilih=true;//can
        
    if (!empty($ppilinsen) AND $filterpilih==true) {
        
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
        
        
        
        
        $query = "SELECT CAST(null as DECIMAL(10,0)) as urutan, a.bulan, a.divisi, a.cabang icabangid, b.nama cabang, "
                . " a.jabatan, a.karyawanid, a.nama, a.region, a.jumlah, CAST('' as CHAR(50)) as nodivisi "
                . " FROM dbmaster.incentiveperdivisi a "
                . " LEFT JOIN mkt.icabang b on a.cabang=b.iCabangId WHERE IFNULL(a.jumlah,0)<>0 AND "
                . " DATE_FORMAT(a.bulan,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2'";
        if (!empty($pdivisi)) $query .=" AND a.divisi='$pdivisi' ";
        //if (!empty($filtercoa)) $query .=" AND IFNULL(coa4,'') IN $filtercoa ";
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp02 (urutan, bulan, divisi, icabangid)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
        $query="UPDATE $tmp02 SET urutan=1 WHERE jabatan='MR'";
        mysqli_query($cnmy, $query);
        $query="UPDATE $tmp02 SET urutan=2 WHERE jabatan='AM'";
        mysqli_query($cnmy, $query);
        $query="UPDATE $tmp02 SET urutan=3 WHERE jabatan='DM'";
        mysqli_query($cnmy, $query);
        
        $query="Alter table $tmp02 ADD COLUMN coa CHAR(50), ADD COLUMN nama_coa CHAR(100)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query ="UPDATE $tmp02 SET coa='705-05' WHERE divisi='CAN'";//, nama_coa='P1-DIN-INSENTIVE CANARY'
        mysqli_query($cnmy, $query);

        $query ="UPDATE $tmp02 SET coa='701-05' WHERE divisi='EAGLE'";//, nama_coa='P1-DIN-INSENTIVE EAGLE'
        mysqli_query($cnmy, $query);

        $query ="UPDATE $tmp02 SET coa='702-05' WHERE divisi='PIGEO'";//, nama_coa='P2-DIN-INSENTIVE PIGEON'
        mysqli_query($cnmy, $query);

        $query ="UPDATE $tmp02 SET coa='703-05' WHERE divisi='PEACO'";//, nama_coa='P3-DIN-INSENTIF PEACOCK'
        mysqli_query($cnmy, $query);
        
        $query = "select idinput, nodivisi, nomor, tglf as bulan "
                . " from dbmaster.t_suratdana_br where concat(kodeid,subkode)='104' "
                . " and IFNULL(stsnonaktif,'')<>'' AND DATE_FORMAT(tglf,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' AND nodivisi like '%INC%'";
        $query = "create TEMPORARY table $tmp03 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp03 (idinput, nodivisi, nomor, bulan)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        $query = "UPDATE $tmp02 a JOIN $tmp03 b on DATE_FORMAT(a.bulan,'%Y-%m')=DATE_FORMAT(b.bulan,'%Y-%m') "
                . " SET a.nodivisi=b.nodivisi"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "INSERT INTO $tmp01 (kodeinput, idinput, divisi, tgltrans, coa, pengajuan, nodivisi, kredit)"
                . "SELECT 'P' kodeinput, urutan, divisi, bulan, coa, nama, nodivisi, jumlah FROM $tmp02";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
        
        
    }
    
    
    
    if (!empty($prutin) OR !empty($pblk)) {
        
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
        
        
        
        $pfilkode=" AND kode IN ('1', '2') ";
        if (!empty($prutin) OR !empty($pblk)) {
        }else{
            if (!empty($prutin)) {
                $pfilkode=" AND kode IN ('1') ";
            }
            if (!empty($pblk)) {
                $pfilkode=" AND kode IN (2') ";
            }
        }
        
        $query = "select b.tgl_fin, b.kode, b.bulan, b.periode1, DATE_FORMAT(b.periode1,'%Y-%m-01') periode, a.idrutin, b.divisi, b.divi, b.karyawanid, b.nama_karyawan, "
                . " b.icabangid, b.areaid, b.icabangid_o, b.areaid_o, "
                . " a.coa, a.nobrid, b.keterangan, a.rptotal "
                . " from dbmaster.t_brrutin1 a "
                . " JOIN dbmaster.t_brrutin0 b on a.idrutin=b.idrutin WHERE "
                . " IFNULL(b.stsnonaktif,'') <> 'Y' $pfilkode AND "
                . " DATE_FORMAT(b.bulan,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
        if (!empty($pdivisi)) $query .=" AND divisi='$pdivisi' ";
        //hilangkan
        if (!empty($filtercoa)) $query .=" AND IFNULL(a.coa,'') IN $filtercoa ";
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp02 (idrutin, divi, kode, divisi, icabangid, areaid, icabangid_o, areaid_o,  karyawanid)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            
        $query = "DELETE FROM $tmp02 WHERE IFNULL(divi,'')<>'OTC' AND ( IFNULL(tgl_fin,'')='' OR IFNULL(tgl_fin,'0000-00-00')='0000-00-00' OR IFNULL(tgl_fin,'0000-00-00 00:00:00')='0000-00-00 00:00:00')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
            //hilangkan
        if (!empty($pblk)) {
            //hilangkan
            $query = "select distinct idrutin, divisi from dbmaster.t_brrutin_ca_close WHERE idrutin IN (select distinct IFNULL(idrutin,'') from $tmp02)";
            $query = "create TEMPORARY table $tmp03 ($query)";
            //mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
                //$query = "CREATE INDEX `norm1` ON $tmp03 (idrutin, divisi)";
                //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "UPDATE $tmp02 a JOIN $tmp03 b on a.idrutin=b.idrutin SET a.divisi=b.divisi WHERE a.kode='2' AND IFNULL(b.divisi,'')<>''";
            //mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            //mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
            
        }
        
        
        $query = "UPDATE $tmp02 a JOIN dbmaster.posting_coa_rutin b on a.divisi=b.divisi AND a.nobrid=b.nobrid SET a.coa=b.COA4 WHERE IFNULL(a.divisi,'')<>''";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        //hilangkan
        $query = "DELETE FROM $tmp02 WHERE coa NOT IN $filtercoa";
        //mysqli_query($cnmy, $query);
        //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
        $query = "select a.*, b.nama namakry, c.nama nama_cabang, d.nama nama_area, "
                . " e.nama nmcabotc, f.nama nmareaotc, CAST('' as CHAR(50)) as nodivisi from $tmp02 a "
                . " LEFT JOIN hrd.karyawan b on a.karyawanid=b.karyawanid "
                . " LEFT JOIN MKT.icabang c on a.icabangid=c.iCabangId "
                . " LEFT JOIN MKT.iarea d on a.areaid=d.areaId AND a.icabangid=d.iCabangId "
                . " LEFT JOIN MKT.icabang_o e on a.icabangid_o=e.icabangid_o "
                . " LEFT JOIN MKT.iarea_o f on a.areaid_o=f.areaid_o AND a.icabangid_o=f.icabangid_o";
        $query = "create TEMPORARY table $tmp03 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "ALTER table $tmp03 ADD COLUMN idinput BIGINT(20)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
            $query = "CREATE INDEX `norm1` ON $tmp03 (idrutin, divi, kode, divisi, icabangid, areaid, icabangid_o, areaid_o,  karyawanid)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        //OTHER
        $query = "UPDATE $tmp03 SET namakry=nama_karyawan, karyawanid=idrutin WHERE karyawanid IN ('0000002083', '0000002200')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp03 SET icabangid=icabangid_o, areaid=areaid_o, nama_cabang=nmcabotc, nama_area=nmareaotc WHERE divisi='OTC'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp03 a JOIN (select distinct pilih, nodivisi, idinput, bridinput FROM $tmp10 WHERE kodeinput IN ('F', 'I')) b on a.idrutin=b.bridinput "
                . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query ="DELETE FROM $tmp03 WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(bulan,'%Y-%m')>='2020-01'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
        $query = "INSERT INTO $tmp01 (kodeinput, idinput, divisi, tgltrans, coa, keterangan, pengajuan, nodivisi, idinputdiv, kredit)"
                . "SELECT 'I' kodeinput, idrutin, divisi, periode, coa, keterangan, nama_karyawan, nodivisi, idinput, sum(rptotal) rptotal FROM $tmp03 GROUP BY 1,2,3,4,5,6,7,8,9";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            
    }
    
    //goto hapusdata;
    
    /*
    
    if (!empty($prutin) OR !empty($pblk)) {
        
        $pfilkode=" AND kode IN ('1', '2') ";
        if (!empty($prutin) OR !empty($pblk)) {
        }else{
            if (!empty($prutin)) {
                $pfilkode=" AND kode IN ('1') ";
            }
            if (!empty($pblk)) {
                $pfilkode=" AND kode IN (2') ";
            }
        }
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
        
        $query = "select kode, divi, idrutin, divisi, bulan, periode1, icabangid, areaid, icabangid_o, areaid_o,  karyawanid, "
                . " keterangan, jumlah, tgl_fin, nama_karyawan "
                . " FROM dbmaster.t_brrutin0 WHERE "
                . " IFNULL(stsnonaktif,'') <> 'Y' $pfilkode AND "
                . " DATE_FORMAT(bulan,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
        if (!empty($pdivisi)) $query .=" AND divisi='$pdivisi' ";
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp02 (idrutin, divi, kode, divisi, icabangid, areaid, icabangid_o, areaid_o,  karyawanid)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
        //delete yang tgl_fin nya kosong selain divi='OTC' 
        $query = "DELETE FROM $tmp02 WHERE IFNULL(divi,'')<>'OTC' AND ( IFNULL(tgl_fin,'')='' OR IFNULL(tgl_fin,'0000-00-00')='0000-00-00' OR IFNULL(tgl_fin,'0000-00-00 00:00:00')='0000-00-00 00:00:00')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "select a.*, b.nama namakry, c.nama nama_cabang, d.nama nama_area, "
                . " e.nama nmcabotc, f.nama nmareaotc, CAST('' as CHAR(50)) as nodivisi from $tmp02 a "
                . " LEFT JOIN hrd.karyawan b on a.karyawanid=b.karyawanid "
                . " LEFT JOIN MKT.icabang c on a.icabangid=c.iCabangId "
                . " LEFT JOIN MKT.iarea d on a.areaid=d.areaId AND a.icabangid=d.iCabangId "
                . " LEFT JOIN MKT.icabang_o e on a.icabangid_o=e.icabangid_o "
                . " LEFT JOIN MKT.iarea_o f on a.areaid_o=f.areaid_o AND a.icabangid_o=f.icabangid_o";
        $query = "create TEMPORARY table $tmp03 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp03 (idrutin, divi, kode, divisi, icabangid, areaid, icabangid_o, areaid_o,  karyawanid)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        //OTHER
        $query = "UPDATE $tmp03 SET namakry=nama_karyawan, karyawanid=idrutin WHERE karyawanid IN ('0000002083', '0000002200')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp03 SET icabangid=icabangid_o, areaid=areaid_o, nama_cabang=nmcabotc, nama_area=nmareaotc WHERE divisi='OTC'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
        
        $query = "select *, CAST('' as CHAR(10)) as divisi, CAST('' as CHAR(1)) as kode from dbmaster.t_brrutin1 WHERE idrutin IN (select distinct IFNULL(idrutin,'') from $tmp03)";
        $query = "create TEMPORARY table $tmp04 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query = "CREATE INDEX `norm1` ON $tmp04 (idrutin, kode, divisi)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        $query = "UPDATE $tmp04 a JOIN $tmp03 b on a.idrutin=b.idrutin SET a.kode=b.kode, a.divisi=b.divisi";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        if (!empty($prutin)) {
            $query = "UPDATE $tmp04 a JOIN $tmp03 b on a.idrutin=b.idrutin SET a.divisi=b.divisi WHERE a.kode='1'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        }
        
        
        if (!empty($pblk)) {
            $query = "select distinct idrutin, divisi from dbmaster.t_brrutin_ca_close WHERE idrutin IN (select distinct IFNULL(idrutin,'') from $tmp03)";
            $query = "create TEMPORARY table $tmp05 ($query)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
                $query = "CREATE INDEX `norm1` ON $tmp05 (idrutin, divisi)";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "UPDATE $tmp04 a JOIN $tmp05 b on a.idrutin=b.idrutin SET a.divisi=b.divisi WHERE a.kode='2' AND IFNULL(b.divisi,'')<>''";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
            
        }
        
        
        $query = "UPDATE $tmp04 a JOIN dbmaster.posting_coa_rutin b on a.divisi=b.divisi AND a.nobrid=b.nobrid SET a.coa=b.COA4 WHERE IFNULL(a.divisi,'')<>''";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
        $query = "ALTER table $tmp03 ADD COLUMN idinput BIGINT(20)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
        $query = "UPDATE $tmp03 a JOIN (select distinct pilih, nodivisi, idinput, bridinput FROM $tmp10 WHERE kodeinput IN ('F', 'I')) b on a.idrutin=b.bridinput "
                . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query ="DELETE FROM $tmp03 WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(bulan,'%Y-%m')>='2020-01'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
        if (!empty($prutin)) {
            $query = "select DATE_FORMAT(b.periode1,'%Y-%m-01') periode, b.divisi, b.bulan, b.icabangid, 
                a.idrutin, a.nobrid, d.nama namaid, a.coa, b.karyawanid, b.namakry nama_karyawan, 
                b.keterangan, a.alasanedit_fin, nodivisi, idinput, sum(a.rptotal) rptotal 
                FROM $tmp04 a JOIN $tmp03 b on a.idrutin=b.idrutin 
                LEFT JOIN dbmaster.t_brid d on a.nobrid=d.nobrid WHERE b.kode='1' ";
            if (!empty($filtercoa)) $query .=" AND IFNULL(a.coa,'') IN $filtercoa ";
            $query .= " GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14";
            $query = "create TEMPORARY table $tmp02 ($query)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
                $query = "CREATE INDEX `norm1` ON $tmp02 (idrutin, divisi, icabangid, nobrid, coa, karyawanid)";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
            $query = "INSERT INTO $tmp01 (kodeinput, idinput, divisi, tgltrans, coa, keterangan, pengajuan, nodivisi, idinputdiv, kredit)"
                    . "SELECT 'F' kodeinput, idrutin, divisi, periode, coa, keterangan, nama_karyawan, nodivisi, idinput, sum(rptotal) rptotal FROM $tmp02 GROUP BY 1,2,3,4,5,6,7,8,9";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        }
        
        if (!empty($pblk)) {
            $query = "select DATE_FORMAT(b.periode1,'%Y-%m-01') periode, b.divisi, b.bulan, b.icabangid, 
                a.idrutin, a.nobrid, d.nama namaid, a.coa, b.karyawanid, b.namakry nama_karyawan, 
                b.keterangan, a.alasanedit_fin, nodivisi, idinput, sum(a.rptotal) rptotal 
                FROM $tmp04 a JOIN $tmp03 b on a.idrutin=b.idrutin 
                LEFT JOIN dbmaster.t_brid d on a.nobrid=d.nobrid WHERE b.kode='2' ";
            if (!empty($filtercoa)) $query .=" AND IFNULL(a.coa,'') IN $filtercoa ";
            $query .= " GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14";
            $query = "create TEMPORARY table $tmp05 ($query)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
                $query = "CREATE INDEX `norm1` ON $tmp05 (idrutin, divisi, icabangid, nobrid, coa, karyawanid)";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
            $query = "INSERT INTO $tmp01 (kodeinput, idinput, divisi, tgltrans, coa, keterangan, pengajuan, nodivisi, idinputdiv, kredit)"
                    . "SELECT 'I' kodeinput, idrutin, divisi, periode, coa, keterangan, nama_karyawan, nodivisi, idinput, sum(rptotal) rptotal FROM $tmp05 GROUP BY 1,2,3,4,5,6,7,8,9";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
        }
        
        
        
        
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
        
        
        
        
    }
    
    */
    
    
    
    
        
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
    
    $query = "select idinput, nodivisi, nobukti FROM dbmaster.t_suratdana_bank WHERE IFNULL(stsnonaktif,'')<>'Y' AND "
            . " stsinput='K' AND idinput IN (select IFNULL(idinputdiv,'') FROM $tmp01)";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 a JOIN $tmp02 b on a.idinputdiv=b.idinput "
            . "SET a.bukti=b.nobukti"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 a JOIN dbmaster.coa_level4 b on a.coa=b.COA4 "
            . "SET a.nama_coa=b.NAMA4"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    if ($prpttype=="D") {
        $query = "SELECT * FROM $tmp01";
    }else{
        $query = "SELECT divisi, coa, nama_coa, sum(debit) debit, "
                . " sum(kredit) kredit, sum(saldo) saldo, sum(dpp) dpp, sum(ppn) ppn, sum(pph) pph FROM $tmp01 "
                . " GROUP BY 1,2,3";
    }
    $query = "create TEMPORARY table $tmp00 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp00 SET divisi='AA' WHERE IFNULL(divisi,'')=''";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
?>

<HTML>
<HEAD>
    <title>Laporan General Ledger</title>
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
    
<?PHP if ($ppilihrpt!="excel") { ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?PHP } ?>
    
    
<div id='n_content'>
    
    <center><div class='h1judul'>General Ledger</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <tr><td>Periode</td><td>:</td><td><?PHP echo "<b>$myperiode1 s/d. $myperiode2</b>"; ?></td></tr>
            <tr class='miring text2'><td>view date</td><td>:</td><td><?PHP echo "$printdate"; ?></td></tr>
        </table>
    </div>
    <div class="clearfix"></div>
    <hr/>
    
    
    <?PHP
    if ($prpttype=="D") {
    ?>
    
        <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
            <thead>
                <tr>
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
                <th align="center" nowrap>Rincian</th>
                <th align="center" nowrap>Debit</th>
                <th align="center" nowrap>Credit</th>
                <th align="center" nowrap>Saldo</th>
                <th align="center" nowrap>No</th>
                <th align="center" nowrap>DPP</th>
                <th align="center" nowrap>PPN</th>
                <th align="center" nowrap>PPH</th>
                <th align="center" nowrap>TGL FP PPN</th>
                <th align="center" nowrap>SERI FP PPN</th>
                <th align="center" nowrap>TGL FP PPH</th>
                <th align="center" nowrap>SERI FP PPH</th>
                <th align="center" nowrap>No Divisi</th>
                <th align="center" nowrap>ID</th>

            </thead>
            <tbody>
                <?PHP
                $pcoanama="";
                
                $ptotcoacredit=0;
                $ptotcoadivisicredit=0;
                $ptotalcredit=0;
                
                $ptotcoadebit=0;
                $ptotcoadivisidebit=0;
                $ptotaldebit=0;
                
                
                $ptotcoadpp=0;
                $ptotcoadivisidpp=0;
                $ptotaldpp=0;
                
                $ptotcoappn=0;
                $ptotcoadivisippn=0;
                $ptotalppn=0;
                
                $ptotcoapph=0;
                $ptotcoadivisipph=0;
                $ptotalpph=0;
                
                $query = "select distinct divisi from $tmp00 order by divisi";
                $tampil=mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pdivisi=$row['divisi'];
                    $mdivisi=$pdivisi;
                    if ($pdivisi=="CAN") $mdivisi="CANARY";
                    
                    if ($mdivisi=="AA") $mdivisi="NONE";
                    
                    $ptotcoadivisicredit=0;
                    
                    $ptotcoadivisidebit=0;
                    
                    $ptotcoadivisidpp=0;
                    $ptotcoadivisippn=0;
                    $ptotcoadivisipph=0;
                    
                    $query2 = "select distinct divisi, coa from $tmp00 WHERE RTRIM(divisi)='$pdivisi' order by divisi, coa";
                    $tampil2=mysqli_query($cnmy, $query2);
                    while ($row2= mysqli_fetch_array($tampil2)) {
                        $pcoa=$row2['coa'];
                        
                        $ptotcoacredit=0;
                        $ptotcoadebit=0;
                        $ptotcoadpp=0;
                        $ptotcoappn=0;
                        $ptotcoapph=0;
                        
                        $query3 = "select * from $tmp00 WHERE RTRIM(divisi)='$pdivisi' AND RTRIM(coa)='$pcoa' order by divisi, coa, tgltrans, bukti, pengajuan";
                        $tampil3=mysqli_query($cnmy, $query3);
                        while ($row3= mysqli_fetch_array($tampil3)) {
                            $ptgltrans = date("d/m/Y", strtotime($row3['tgltrans']));
                            
                            $pbrid=$row3['idinput'];
                            $pbukti=$row3['bukti'];
                            $pcoa=$row3['coa'];
                            $pcoanama=$row3['nama_coa'];
                            $pidinput=$row3['idinput'];
                            $pdokternm=$row3['dokter'];
                            $pnoslip=$row3['noslip'];
                            $ppengajuan=$row3['pengajuan'];
                            $pketerangan=$row3['keterangan'];
                            $pnmrealisasi=$row3['nmrealisasi'];
                            $pnodivisi=$row3['nodivisi'];
                            
                            //dpp, ppn, pph, tglfp
                            $pdpprp=$row3['dpp'];
                            $pppnrp=$row3['ppn'];
                            $ppphrp=$row3['pph'];
                            $ptglfp="";
                            if (!empty($row3['tglfp']) AND $row3['tglfp']<>"0000-00-00") $ptglfp = date("d/m/Y", strtotime($row3['tglfp']));
                            
                            $ptotcoadpp=(double)$ptotcoadpp+(double)$pdpprp;
                            $ptotcoappn=(double)$ptotcoappn+(double)$pppnrp;
                            $ptotcoapph=(double)$ptotcoapph+(double)$ppphrp;
                            
                            $pdpprp=number_format($pdpprp,0,",",",");
                            $pppnrp=number_format($pppnrp,0,",",",");
                            $ppphrp=number_format($ppphrp,0,",",",");
                            
                            $pcredit=$row3['kredit'];
                            $ptotcoacredit=(double)$ptotcoacredit+(double)$pcredit;
                            $pcredit=number_format($pcredit,0,",",",");
                            
                            $pdebit=$row3['debit'];
                            $ptotcoadebit=(double)$ptotcoadebit+(double)$pdebit;
                            $pdebit=number_format($pdebit,0,",",",");
                            
                            
                            
                            $idivisi=$mdivisi;
                            
                            $pkdinput=$row3['kodeinput'];
                            $pdivost=$row3['divisi2'];
                            $pdanaminta="";
                            if (!empty($pdivost) AND $pkdinput=="O") {
                                $pdanaminta=$row3['mintadana'];
                                $pdanaminta=number_format($pdanaminta,0,",",",");
                            
                                $idivisi="";
                                $ptgltrans="";
                                $pbukti="";
                                $pcoa="";
                                $pcoanama="";
                                $ppengajuan=$pdivost;
                            }
                            
                            
                            if ($pkdinput=="Z") {
                                $ptglfil_ = date("Y-m", strtotime($row3['tgltrans']));
                                $query = "select * from $tmp08 WHERE nobukti='$pbukti'";
                                $ntampil= mysqli_query($cnmy, $query);
                                while($nro= mysqli_fetch_array($ntampil)) {
                                    $ptglfil_ = date("Y-m", strtotime($nro['bulan']));
                                    
                                    $idivisi=$nro['divisi'];
                                    $pketerangan=$nro['keterangan'];
                                    $ppengajuan=$nro['pengajuan'];
                                    $pnodivisi=$nro['nodivisi'];
                                    $pcoa=$nro['coa'];
                                    $pcoanama=$nro['nama_coa'];
                                    $pdokternm=$nro['myid'];
                                    
                                    $pcredit=$nro['kredit'];
                                    $ptotcoacredit=(double)$ptotcoacredit+(double)$pcredit;
                                    $pcredit=number_format($pcredit,0,",",",");

                                    $pdebit=$nro['debit'];
                                    $ptotcoadebit=(double)$ptotcoadebit+(double)$pdebit;
                                    $pdebit=number_format($pdebit,0,",",",");
                                    
                                    
                                    
                                    echo "<tr>";
                                    echo "<td nowrap>$idivisi</td>";
                                    echo "<td nowrap>$ptgltrans</td>";
                                    echo "<td nowrap>$pbukti</td>";
                                    echo "<td nowrap>$pcoa</td>";
                                    echo "<td nowrap>$pcoanama</td>";

                                    echo "<td >$pdokternm</td>";
                                    echo "<td nowrap>$pnoslip</td>";
                                    echo "<td >$ppengajuan</td>";
                                    echo "<td>$pketerangan</td>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap align='right'></td>";
                                    echo "<td nowrap align='right'>$pdebit</td>";
                                    echo "<td nowrap align='right'>$pcredit</td>";
                                    echo "<td nowrap align='right'></td>";//$psaldo
                                    echo "<td nowrap align='right'></td>";//no

                                    echo "<td nowrap align='right'></td>";
                                    echo "<td nowrap align='right'></td>";
                                    echo "<td nowrap align='right'></td>";
                                    echo "<td nowrap></td>";
                                    echo "<td></td>";
                                    echo "<td></td>";
                                    echo "<td></td>";
                                    echo "<td nowrap>$pnodivisi</td>";
                                    echo "<td nowrap></td>";
                                    echo "</tr>";
                                }
                                //continue;
                            }else{
                            
                                echo "<tr>";
                                echo "<td nowrap>$idivisi</td>";
                                echo "<td nowrap>$ptgltrans</td>";
                                echo "<td nowrap>$pbukti</td>";
                                echo "<td nowrap>$pcoa</td>";
                                echo "<td nowrap>$pcoanama</td>";

                                echo "<td >$pdokternm</td>";
                                echo "<td nowrap>$pnoslip</td>";
                                echo "<td >$ppengajuan</td>";
                                echo "<td>$pketerangan</td>";
                                echo "<td nowrap>$pnmrealisasi</td>";
                                echo "<td nowrap align='right'>$pdanaminta</td>";
                                echo "<td nowrap align='right'>$pcredit</td>";//$pdebit   NOTE :  di GL ada di posisi DEBIT jadi di Balik
                                echo "<td nowrap align='right'>$pdebit</td>";
                                echo "<td nowrap align='right'></td>";//$psaldo
                                echo "<td nowrap align='right'></td>";//no

                                echo "<td nowrap align='right'>$pdpprp</td>";
                                echo "<td nowrap align='right'>$pppnrp</td>";
                                echo "<td nowrap align='right'>$ppphrp</td>";
                                echo "<td nowrap>$ptglfp</td>";
                                echo "<td></td>";
                                echo "<td></td>";
                                echo "<td></td>";
                                echo "<td nowrap>$pnodivisi</td>";
                                echo "<td nowrap>$pbrid</td>";
                                echo "</tr>";
                                
                            }
                            
                            
                        }
                        
                        echo "<tr>";
                        echo "<td></td>";//mintadana
                        echo "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
                        echo "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "</tr>";
                        
                        $ptotcoadivisicredit=(double)$ptotcoadivisicredit+(double)$ptotcoacredit;
                        $ptotcoacredit=number_format($ptotcoacredit,0,",",",");
                        
                        $ptotcoadivisidebit=(double)$ptotcoadivisidebit+(double)$ptotcoadebit;
                        $ptotcoadebit=number_format($ptotcoadebit,0,",",",");
                        
                        
                        
                        $ptotcoadivisidpp=(double)$ptotcoadivisidpp+(double)$ptotcoadpp;
                        $ptotcoadpp=number_format($ptotcoadpp,0,",",",");
                        
                        $ptotcoadivisippn=(double)$ptotcoadivisippn+(double)$ptotcoappn;
                        $ptotcoappn=number_format($ptotcoappn,0,",",",");
                        
                        $ptotcoadivisipph=(double)$ptotcoadivisipph+(double)$ptotcoapph;
                        $ptotcoapph=number_format($ptotcoapph,0,",",",");
                        
                        echo "<tr>";
                        echo "<td></td><td></td><td></td>";
                        echo "<td nowrap><b>$pcoa</b></td> <td nowrap><b>$pcoanama</b></td>";
                        echo "<td></td><td></td><td></td><td></td><td></td>";
                        echo "<td></td>";//mintadana
                        echo "<td nowrap align='right'><b>$ptotcoacredit</b></td>";//$pdebit    NOTE :  di GL ada di posisi DEBIT jadi di Balik
                        echo "<td nowrap align='right'><b>$ptotcoadebit</b></td>";
                        echo "<td nowrap align='right'><b></b></td>";//$psaldo
                            
                        echo "<td></td>";
                        
                        echo "<td nowrap align='right'><b>$ptotcoadpp</b></td>";//dpp
                        echo "<td nowrap align='right'><b>$ptotcoappn</b></td>";//ppn
                        echo "<td nowrap align='right'><b>$ptotcoapph</b></td>";//pph
                        
                        echo "<td></td><td></td><td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "</tr>";
                        
                        echo "<tr>";
                        echo "<td></td>";//mintadana
                        echo "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
                        echo "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "</tr>";
                        
                    }
                    
                    $ptotalcredit=(double)$ptotalcredit+(double)$ptotcoadivisicredit;
                    $ptotcoadivisicredit=number_format($ptotcoadivisicredit,0,",",",");
                    
                    $ptotaldebit=(double)$ptotaldebit+(double)$ptotcoadivisidebit;
                    $ptotcoadivisidebit=number_format($ptotcoadivisidebit,0,",",",");
                    
                    $ptotaldpp=(double)$ptotaldpp+(double)$ptotcoadivisidpp;
                    $ptotcoadivisidpp=number_format($ptotcoadivisidpp,0,",",",");
                    
                    $ptotalppn=(double)$ptotalppn+(double)$ptotcoadivisippn;
                    $ptotcoadivisippn=number_format($ptotcoadivisippn,0,",",",");
                    
                    $ptotalpph=(double)$ptotalpph+(double)$ptotcoadivisipph;
                    $ptotcoadivisipph=number_format($ptotcoadivisipph,0,",",",");

                    echo "<tr>";
                    echo "<td nowrap colspan=5 align='center'><b>TOTAL $mdivisi </b></td>";
                    echo "<td></td><td></td><td></td><td></td><td></td>";
                    echo "<td></td>";//mintadana
                    echo "<td nowrap align='right'><b>$ptotcoadivisicredit</b></td>";//$pdebit    NOTE :  di GL ada di posisi DEBIT jadi di Balik
                    echo "<td nowrap align='right'><b>$ptotcoadivisidebit</b></td>";
                    echo "<td nowrap align='right'><b></b></td>";//$psaldo

                    echo "<td></td>";

                    echo "<td nowrap align='right'><b>$ptotcoadivisidpp</b></td>";//dpp
                    echo "<td nowrap align='right'><b>$ptotcoadivisippn</b></td>";//ppn
                    echo "<td nowrap align='right'><b>$ptotcoadivisipph</b></td>";//pph

                    echo "<td></td><td></td><td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "</tr>";

                    echo "<tr>";
                    echo "<td></td>";//mintadana
                    echo "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
                    echo "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "</tr>";
                        
                }
                
                $ptotalcredit=number_format($ptotalcredit,0,",",",");
                $ptotaldebit=number_format($ptotaldebit,0,",",",");
                $ptotaldpp=number_format($ptotaldpp,0,",",",");
                $ptotalppn=number_format($ptotalppn,0,",",",");
                $ptotalpph=number_format($ptotalpph,0,",",",");

                echo "<tr>";
                echo "<td nowrap colspan=5 align='center'><b>GRAND TOTAL</b></td>";
                echo "<td></td><td></td><td></td><td></td><td></td>";
                echo "<td></td>";//mintadana
                echo "<td nowrap align='right'><b>$ptotalcredit</b></td>";//$pdebit    NOTE :  di GL ada di posisi DEBIT jadi di Balik
                echo "<td nowrap align='right'><b>$ptotaldebit</b></td>";
                echo "<td nowrap align='right'><b></b></td>";//$psaldo

                echo "<td></td>";

                echo "<td nowrap align='right'><b>$ptotaldpp</b></td>";//dpp
                echo "<td nowrap align='right'><b>$ptotalppn</b></td>";//ppn
                echo "<td nowrap align='right'><b>$ptotalpph</b></td>";//pph

                echo "<td></td><td></td><td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "</tr>";

                echo "<tr>";
                echo "<td></td>";//mintadana
                echo "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
                echo "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
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
                <th align="center" nowrap>Divisi</th>
                <th align="center" nowrap>KODE</th>
                <th align="center" nowrap>PERKIRAAN</th>
                <th align="center" nowrap>Debit</th>
                <th align="center" nowrap>Credit</th>
                <th align="center" nowrap>Saldo</th>
                <th align="center" nowrap>No</th>
                <th align="center" nowrap>DPP</th>
                <th align="center" nowrap>PPN</th>
                <th align="center" nowrap>PPH</th>

            </thead>
            <tbody>
                <?PHP
                $pcoanama="";
                
                $ptotcoadivisicredit=0;
                $ptotalcredit=0;
                
                $ptotcoadivisidebit=0;
                $ptotaldebit=0;
                
                $ptotcoadivisidpp=0;
                $ptotaldpp=0;
                $ptotcoadivisippn=0;
                $ptotalppn=0;
                $ptotcoadivisipph=0;
                $ptotalpph=0;
                
                $query = "select distinct divisi from $tmp00 order by divisi";
                $tampil=mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pdivisi=$row['divisi'];
                    $mdivisi=$pdivisi;
                    if ($pdivisi=="CAN") $mdivisi="CANARY";
                    
                    if ($mdivisi=="AA") $mdivisi="NONE";
                    
                    $ptotcoadivisicredit=0;
                    $ptotcoadivisidebit=0;
                    $ptotcoadivisidpp=0;
                    $ptotcoadivisippn=0;
                    $ptotcoadivisipph=0;
                    
                    $query2 = "select * from $tmp00 WHERE RTRIM(divisi)='$pdivisi' order by divisi, coa";
                    $tampil2=mysqli_query($cnmy, $query2);
                    while ($row2= mysqli_fetch_array($tampil2)) {
                        $pcoa=$row2['coa'];
                        $pcoanama=$row2['nama_coa'];
                        
                        $pdpprp=$row2['dpp'];
                        $pppnrp=$row2['ppn'];
                        $ppphrp=$row2['pph'];
                        
                        $ptotcoadivisidpp=(double)$ptotcoadivisidpp+(double)$pdpprp;
                        $ptotcoadivisippn=(double)$ptotcoadivisippn+(double)$pppnrp;
                        $ptotcoadivisipph=(double)$ptotcoadivisipph+(double)$ppphrp;
                        
                        $pdpprp=number_format($pdpprp,0,",",",");
                        $pppnrp=number_format($pppnrp,0,",",",");
                        $ppphrp=number_format($ppphrp,0,",",",");

                        $pcredit=$row2['kredit'];
                        $ptotcoadivisicredit=(double)$ptotcoadivisicredit+(double)$pcredit;
                        $pcredit=number_format($pcredit,0,",",",");

                        $pdebit=$row2['debit'];
                        $ptotcoadivisidebit=(double)$ptotcoadivisidebit+(double)$pdebit;
                        $pdebit=number_format($pdebit,0,",",",");
                            
                        echo "<tr>";
                        echo "<td nowrap>$mdivisi</td>";
                        echo "<td nowrap>$pcoa</td>";
                        echo "<td nowrap>$pcoanama</td>";

                        echo "<td nowrap align='right'>$pcredit</td>";//$pdebit    NOTE :  di GL ada di posisi DEBIT jadi di Balik
                        echo "<td nowrap align='right'>$pdebit</td>";
                        echo "<td nowrap align='right'></td>";//$psaldo

                        echo "<td nowrap align='right'></td>";
                        
                        echo "<td nowrap align='right'>$pdpprp</td>";
                        echo "<td nowrap align='right'>$pppnrp</td>";
                        echo "<td nowrap align='right'>$ppphrp</td>";
                        echo "</tr>";
                    }
                    
                    $ptotalcredit=(double)$ptotalcredit+(double)$ptotcoadivisicredit;
                    $ptotcoadivisicredit=number_format($ptotcoadivisicredit,0,",",",");
                    
                    $ptotaldebit=(double)$ptotaldebit+(double)$ptotcoadivisidebit;
                    $ptotcoadivisidebit=number_format($ptotcoadivisidebit,0,",",",");
                    
                    $ptotaldpp=(double)$ptotaldpp+(double)$ptotcoadivisidpp;
                    $ptotcoadivisidpp=number_format($ptotcoadivisidpp,0,",",",");
                    
                    $ptotalppn=(double)$ptotalppn+(double)$ptotcoadivisippn;
                    $ptotcoadivisippn=number_format($ptotcoadivisippn,0,",",",");
                    
                    $ptotalpph=(double)$ptotalpph+(double)$ptotcoadivisipph;
                    $ptotcoadivisipph=number_format($ptotcoadivisipph,0,",",",");

                    echo "<tr>";
                    echo "<td nowrap colspan='3' align='center'><b>TOTAL $mdivisi </b></td>";
                        
                    echo "<td nowrap align='right'><b>$ptotcoadivisicredit</b></td>";//$pdebit    NOTE :  di GL ada di posisi DEBIT jadi di Balik
                    echo "<td nowrap align='right'><b>$ptotcoadivisidebit</b></td>";
                    echo "<td nowrap align='right'><b></b></td>";//$psaldo

                    echo "<td></td>";

                    echo "<td nowrap align='right'><b>$ptotcoadivisidpp</b></td>";//dpp
                    echo "<td nowrap align='right'><b>$ptotcoadivisippn</b></td>";//ppn
                    echo "<td nowrap align='right'><b>$ptotcoadivisipph</b></td>";//$pph
                    
                    echo "</tr>";
                    
                }
                $ptotalcredit=number_format($ptotalcredit,0,",",",");
                $ptotaldebit=number_format($ptotaldebit,0,",",",");
                $ptotaldpp=number_format($ptotaldpp,0,",",",");
                $ptotalppn=number_format($ptotalppn,0,",",",");
                $ptotalpph=number_format($ptotalpph,0,",",",");

                echo "<tr>";
                echo "<td nowrap colspan='3' align='center'><b>GRAND TOTAL </b></td>";

                echo "<td nowrap align='right'><b>$ptotalcredit</b></td>";//$pdebit    NOTE :  di GL ada di posisi DEBIT jadi di Balik
                echo "<td nowrap align='right'><b>$ptotaldebit</b></td>";
                echo "<td nowrap align='right'><b></b></td>";//$psaldo

                echo "<td></td>";

                echo "<td nowrap align='right'><b>$ptotaldpp</b></td>";//dpp
                echo "<td nowrap align='right'><b>$ptotalppn</b></td>";//ppn
                echo "<td nowrap align='right'><b>$ptotalpph</b></td>";//$pph

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
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp05");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp06");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp07");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp08");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp10");
    mysqli_close($cnmy);
?>