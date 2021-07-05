<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","1G");
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
        header("Content-Disposition: attachment; filename=REPORT REALISASI BIAYA MARKETING BY CABANG.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/fungsi_sql.php");
    $cnit=$cnmy;
    
    $printdate= date("d/m/Y");
    
    $fjbtid=$_SESSION['JABATANID'];
    $pidgrouppil=$_SESSION['GROUP'];
    $picardid=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];
    
    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmplapglreak00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmplapglreak01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmplapglreak02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmplapglreak03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmplapglreak04_".$puserid."_$now ";
    $tmp05 =" dbtemp.tmplapglreak05_".$puserid."_$now ";
    $tmp06 =" dbtemp.tmplapglreak06_".$puserid."_$now ";
    $tmp07 =" dbtemp.tmplapglreak07_".$puserid."_$now ";
    $tmp08 =" dbtemp.tmplapglreak08_".$puserid."_$now ";
    $tmp09 =" dbtemp.tmplapglreak09_".$puserid."_$now ";
    $tmp10 =" dbtemp.tmplapglreak10_".$puserid."_$now ";
    $tmp11 =" dbtemp.tmplapglreak11_".$puserid."_$now ";
    $tmp12 =" dbtemp.tmplapglreak12_".$puserid."_$now ";
    $tmp13 =" dbtemp.tmplapglreak13_".$puserid."_$now ";
    
?>

<?PHP
    include "config/fungsi_combo.php";
    include("config/common.php");
    
    $ppildivisiid = $_POST['cb_divisip'];
    $periode = $_POST['bulan1'];
    
    
    
    $tgl01 = $periode."-01-01";
    $tgl02 = $periode."-12-31";
    
    $pperiode1 = date("Y-m", strtotime($tgl01));
    $pperiode2 = date("Y-m", strtotime($tgl02));
    
    $myperiode1 = date("F Y", strtotime($tgl01));
    $myperiode2 = date("F Y", strtotime($tgl02));

    $ptahuninput = $periode;
    $pbulaninput = date("Y-m-01", strtotime($tgl01));
    
    $pfiltersel=" ('') ";
    $pfilterdelete="";
    
    $pdivisi="";
    
    //echo "$tgl01<br/>$tgl02<br/>$pperiode1<br/>$pperiode2</br>$myperiode1<br/>$myperiode2</br>$ptahuninput<br/>$pbulaninput"; goto hapusdata;
    
    $ppilihpm="";
    if ($fjbtid=="06" OR $fjbtid=="22") {
        $ppilihpm=getfield("select divprodid as lcfields from ms.penempatan_pm WHERE karyawanid='$picardid'");
    }
    
    
    $filtercoa=('');
    if (!empty($_POST['chkbox_coa'])){
        $filtercoa=$_POST['chkbox_coa'];
        $filtercoa=PilCekBoxAndEmpty($filtercoa);
    }
    
    
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
    
    
    $psewakontrak=""; $pserviceken=""; $pkaskecilcabang="";
    if (isset($_POST['chkbox_rpt11'])) $psewakontrak=$_POST['chkbox_rpt11'];
    if (isset($_POST['chkbox_rpt12'])) $pserviceken=$_POST['chkbox_rpt12'];
    if (isset($_POST['chkbox_rpt15'])) $pkaskecilcabang=$_POST['chkbox_rpt15'];
    
    
    $pbelumprosesclose=false;
    //if ($ptahuninput=="2019") {
        $pbelumprosesclose=true;
        
        $pfilterselpil="";
        //BR ETHICAL A
        if (!empty($pbreth)) $pfilterselpil .= "'A',";
        //klaimdiscount B
        if (!empty($pklaim)) $pfilterselpil .= "'B',";
        //KAS KASBON C & D
        if (!empty($pkas)) $pfilterselpil .= "'C',";//'D',
        //BROTC E
        if (!empty($pbrotc)) $pfilterselpil .= "'E',";
        //RUTIN LUAR KOTA F rutin G lk
        if (!empty($prutin)) $pfilterselpil .= "'F',";
        if (!empty($pblk)) $pfilterselpil .= "'G',";

        //CA H
        //if (!empty($prutin) OR !empty($pblk)) $pfilterselpil .= "'H',";

        //BM biaya marketing surabaya I & J
        if (!empty($pbmsby)) $pfilterselpil .= "'I','J',";
        //insentif incentive K
        if (!empty($ppilinsen)) $pfilterselpil .= "'K',";
        
        //BANK L M N O P
        //if (!empty($ppilbank)) $pfilterselpil .= "'L','M','N','O','P',";
        
        
        
        //sewa kontrakan rumah
        if (!empty($psewakontrak)) $pfilterselpil .= "'U',";
        //service kendaraan
        if (!empty($pserviceken)) $pfilterselpil .= "'V',";
        
        //kas kecil cabang
        if (!empty($pkaskecilcabang)) $pfilterselpil .= "'X',";
        
        
        if (!empty($pfilterselpil)) {
            $pfilterselpil="(".substr($pfilterselpil, 0, -1).")";
        }else{
            $pfilterselpil="('xaxaXX')";
        }
        
        
        $picardid=$_SESSION['IDCARD'];
        $puserid=$_SESSION['USERID'];

        $now=date("mdYhis");
        $tmp00 =" dbtemp.tmpprosbmpil00_".$puserid."_$now ";
        $tmp01 =" dbtemp.tmpprosbmpil01_".$puserid."_$now ";
        $tmp02 =" dbtemp.tmpprosbmpil02_".$puserid."_$now ";
        $tmp03 =" dbtemp.tmpprosbmpil03_".$puserid."_$now ";
        $tmp04 =" dbtemp.tmpprosbmpil04_".$puserid."_$now ";
        $tmp05 =" dbtemp.tmpprosbmpil05_".$puserid."_$now ";
        $tmp06 =" dbtemp.tmpprosbmpil06_".$puserid."_$now ";
        $tmp07 =" dbtemp.tmpprosbmpil07_".$puserid."_$now ";
        $tmp08 =" dbtemp.tmpprosbmpil08_".$puserid."_$now ";
        $tmp09 =" dbtemp.tmpprosbmpil09_".$puserid."_$now ";
        $tmp10 =" dbtemp.tmpprosbmpil10_".$puserid."_$now ";
        $tmp11 =" dbtemp.tmpprosbmpil11_".$puserid."_$now ";
    
    
        
        $query ="SELECT noidauto, tahun, periode, hapus_nodiv_kosong, kodeinput, idkodeinput as idinput, nobrid_r, nobrid_n, idkodeinput, divisi, tglinput, tgltrans, "
            . " karyawanid, nama_karyawan, dokterid, dokter_nama, noslip, nmrealisasi, keterangan, dpp, ppn, pph, tglfp, "
            . " idinput_pd, nodivisi, debit, kredit, saldo, jumlah1, jumlah2, "
            . " divisi_edit as divisi_coa, coa_edit as coa, coa_nama_edit as nama_coa, coa_edit2 as coa2, "
            . " coa_nama_edit2 as nama_coa2, coa_edit3 as coa3, coa_nama_edit3 as nama_coa3, "
            . " tgl_trans_bank, nobukti, idinput_pd1, idinput_pd2, nodivisi1, nodivisi2, pengajuan, "
            . " divisi2, icabangid, nama_cabang, areaid, nama_area, kodeid_pd, subkode_pd, pcm, kasbonsby, coa_pcm, nama_coa_pcm, "
            . " tgltarikan, nkodeid, nkodeid_nama "
            . " FROM dbmaster.t_proses_data_bm WHERE IFNULL(hapus_nodiv_kosong,'') <>'Y' AND DATE_FORMAT(tgltarikan,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' AND "
            . " kodeinput IN $pfilterselpil ";
        if (!empty($pdivisi)) $query .=" AND divisi='$pdivisi' ";
        if (!empty($filtercoa)) $query .=" AND IFNULL(coa_edit,'') IN $filtercoa ";
        
        $query .=" AND IFNULL(ishare,'')<>'Y' "; //pilih salah satu divisi <> 'HO' atau IFNULL(ishare,'')<>'Y'
        //$query .=" AND IFNULL(divisi,'')<>'HO' ";  //pilih salah satu divisi <> 'HO' atau IFNULL(ishare,'')<>'Y'
        
        if ($pidgrouppil=="8") {
            $ppilregion="B";
            if ($picardid=="0000000159") $ppilregion="T";
            $query .=" AND ( IFNULL(icabangid,'') IN (select distinct IFNULL(icabangid,'') FROM MKT.icabang WHERE region='$ppilregion' ) OR karyawanid='$picardid' ) ";
            $query .=" AND ( IFNULL(divisi,'') NOT IN ('OTC', 'CHC', 'HO') OR karyawanid='$picardid' )";
            $query .=" AND ( IFNULL(icabangid,'') NOT IN ('0000000001') OR karyawanid='$picardid' )";
        }elseif ($pidgrouppil=="30" AND !empty($ppilihpm)) {
            $query .=" AND IFNULL(divisi,'') ='$ppilihpm' ";
        }
        
        $query = "create TEMPORARY table $tmp01 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp01 SET icabangid='ZKLAIMDISC', nama_cabang='ZKLAIMDISC' WHERE kodeinput='B'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        
        if (!empty($ppilbank)) {
            
            $query ="INSERT INTO $tmp01 "
                . "(noidauto, tahun, periode, hapus_nodiv_kosong, kodeinput, idinput, nobrid_r, nobrid_n, idkodeinput, divisi, tglinput, tgltrans, "
                . " karyawanid, nama_karyawan, dokterid, dokter_nama, noslip, nmrealisasi, keterangan, dpp, ppn, pph, tglfp, "
                . " idinput_pd, nodivisi, debit, kredit, saldo, jumlah1, jumlah2, "
                . " divisi_coa, coa, nama_coa, coa2, "
                . " nama_coa2, coa3, nama_coa3, "
                . " tgl_trans_bank, nobukti, idinput_pd1, idinput_pd2, nodivisi1, nodivisi2, pengajuan, "
                . " divisi2, icabangid, nama_cabang, areaid, nama_area, kodeid_pd, subkode_pd, pcm, kasbonsby, coa_pcm, nama_coa_pcm, "
                . " tgltarikan, nkodeid, nkodeid_nama)"
                . "SELECT noidauto, tahun, periode, hapus_nodiv_kosong, kodeinput, idkodeinput as idinput, nobrid_r, nobrid_n, idkodeinput, divisi, tglinput, tgltrans, "
                . " karyawanid, nama_karyawan, dokterid, dokter_nama, noslip, nmrealisasi, keterangan, dpp, ppn, pph, tglfp, "
                . " idinput_pd, nodivisi, debit, kredit, saldo, jumlah1, jumlah2, "
                . " divisi_edit as divisi_coa, coa_edit as coa, coa_nama_edit as nama_coa, coa_edit2 as coa2, "
                . " coa_nama_edit2 as nama_coa2, coa_edit3 as coa3, coa_nama_edit3 as nama_coa3, "
                . " tgl_trans_bank, nobukti, idinput_pd1, idinput_pd2, nodivisi1, nodivisi2, pengajuan, "
                . " divisi2, icabangid, nama_cabang, areaid, nama_area, kodeid_pd, subkode_pd, pcm, kasbonsby, coa_pcm, nama_coa_pcm, "
                . " tgltarikan, nkodeid, nkodeid_nama "
                . " FROM dbmaster.t_proses_data_bm WHERE IFNULL(hapus_nodiv_kosong,'') <>'Y' AND DATE_FORMAT(tgltarikan,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' AND "
                . " kodeinput IN ('M') "
                . " AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN (select CONCAT(IFNULL(kodeid,''),IFNULL(subkode,'')) from dbmaster.t_kode_spd where IFNULL(igroup,'')='3' AND IFNULL(ibank,'')<>'N') "
                . " and IFNULL(nkodeid_nama,'')='K'";
            if (!empty($pdivisi)) $query .=" AND divisi='$pdivisi' ";
            if (!empty($filtercoa)) $query .=" AND IFNULL(coa_edit,'') IN $filtercoa ";

            $query .=" AND IFNULL(ishare,'')<>'Y' "; //pilih salah satu divisi <> 'HO' atau IFNULL(ishare,'')<>'Y'
            //$query .=" AND IFNULL(divisi,'')<>'HO' ";  //pilih salah satu divisi <> 'HO' atau IFNULL(ishare,'')<>'Y'
            
            if ($pidgrouppil=="8") {
                $ppilregion="B";
                if ($picardid=="0000000159") $ppilregion="T";
                $query .=" AND ( IFNULL(icabangid,'') IN (select distinct IFNULL(icabangid,'') FROM MKT.icabang WHERE region='$ppilregion' ) OR karyawanid='$picardid' ) ";
                $query .=" AND ( IFNULL(divisi,'') NOT IN ('OTC', 'CHC', 'HO') OR karyawanid='$picardid' )";
                $query .=" AND ( IFNULL(icabangid,'') NOT IN ('0000000001') OR karyawanid='$picardid' )";
            }elseif ($pidgrouppil=="30" AND !empty($ppilihpm)) {
                $query .=" AND IFNULL(divisi,'') ='$ppilihpm' ";
            }
        
        
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        }
        
        
    
        $query = "CREATE INDEX `norm1` ON $tmp01 (kodeinput,idinput,divisi,tgltrans,coa)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; } 
        
        /*
    }else{
        
        $pberhasilquery=false;
        include "module/act_prosesbiayamkt/query_proses.php";
        if ($pberhasilquery==false) goto hapusdata;
        
        $query = "DELETE FROM $tmp01 WHERE IFNULL(hapus_nodiv_kosong,'') ='Y'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
         * 
         */
    
    
    $query = "UPDATE $tmp01 SET coa=coa_pcm WHERE IFNULL(coa_pcm,'') ='105-02'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 a JOIN dbmaster.coa_level4 b on a.coa=b.COA4 SET a.nama_coa=b.NAMA4, a.coa3=b.COA3 WHERE IFNULL(a.coa_pcm,'') ='105-02'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 a JOIN dbmaster.coa_level3 b on a.coa3=b.COA3 SET a.nama_coa3=b.NAMA3, a.coa2=b.COA2 WHERE IFNULL(a.coa_pcm,'') ='105-02'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 a JOIN dbmaster.coa_level2 b on a.coa2=b.COA2 SET a.nama_coa2=b.NAMA2 WHERE IFNULL(a.coa_pcm,'') ='105-02'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
    
    
    $query = "UPDATE $tmp01 SET divisi='OTHER' WHERE IFNULL(divisi,'') IN ('', 'AA', 'OTHERS')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //select divisi OTC ETHICAL
    if ($ppildivisiid=="OTC") {
        $query = "DELETE FROM $tmp01 WHERE IFNULL(divisi,'')<>'OTC'";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp01 SET icabangid='HO', nama_cabang='HO' WHERE IFNULL(divisi,'')='OTC' AND icabangid='0000000001' AND kodeinput in ('L','M','N','O','P')";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    }else{
        $query = "DELETE FROM $tmp01 WHERE IFNULL(divisi,'')='OTC'";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    
    
    $query = "select *, kredit as jumlah from $tmp01";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp07");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp08");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp09");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp10");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp11");
    
    $npppperi=$periode."12";
    
    if ($ppildivisiid=="OTC") { //mkt.otc_etl
        //$query = "select icabangid, date_format(tgljual,'%Y-%m') bulan, 'OTC' as divprodid, sum(`value`) as rpsales from dbmaster.sales_otc_local WHERE YEAR(tgljual)='$periode' AND divprodid <>'OTHER' and icabangid <> 22 GROUP BY 1,2,3";
        $query = "select * from dbmaster.sales_otc_local WHERE YEAR(tgljual)='$periode' AND divprodid <>'OTHER' and icabangid <> 22";
        $query = "create TEMPORARY table $tmp01 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "select a.icabangid, a.tgljual, a.iprodid, c.GRP_FKIDEN, d.GRP_NAMESS, a.`value` 
                from $tmp01 a 
                left JOIN MKT.iproduk b on a.iprodid=b.iprodid 
                left join MKT.T_OTC_GRPPRD_DETAIL c on b.iprodid=c.GRP_IDPROD
                left join MKT.T_OTC_GRPPRD d on c.GRP_FKIDEN = d.GRP_IDENTS";
        $query = "create TEMPORARY table $tmp03 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "select * from $tmp03 WHERE IFNULL(GRP_FKIDEN,'') IN ('1', '6', '4', '5', '2', '3', '7', '10')";
        $query = "create TEMPORARY table $tmp12 ($query)";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "select * from $tmp03";
        $query = "create TEMPORARY table $tmp13 ($query)";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp13 SET icabangid='ZKLAIMDISC'";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
        
        
        // 6 = MELANOX DECORATIVE 1 = MELANOX PREMIUM
        $query = "UPDATE $tmp12 SET icabangid='PM_MELANOX' WHERE IFNULL(GRP_FKIDEN,'') IN ('1', '6')";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        //  4 	 PARASOL (FOCUS) 5 	 PARASOL EXIST 
        $query = "UPDATE $tmp12 SET icabangid='PM_PARASOL' WHERE IFNULL(GRP_FKIDEN,'') IN ('4', '5')";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        // 2 	 CARMED LOTION
        $query = "UPDATE $tmp12 SET icabangid='PM_CARMED' WHERE IFNULL(GRP_FKIDEN,'') IN ('2')";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        //  3 	 LANORE MAKE UP 	7 	 LANORE SKIN CARE 
        $query = "UPDATE $tmp12 SET icabangid='PM_LANORE' WHERE IFNULL(GRP_FKIDEN,'') IN ('3', '7')";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        //  10 	 ACNEMED
        $query = "UPDATE $tmp12 SET icabangid='PM_ACNEMED' WHERE IFNULL(GRP_FKIDEN,'') IN ('10')";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "INSERT INTO $tmp03 select * from $tmp12";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "INSERT INTO $tmp03 select * from $tmp13";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
        //$query = "select icabangid, date_format(tgljual,'%Y-%m') bulan, 'OTC' as divprodid, sum(`value`) as rpsales from dbmaster.sales_otc_local WHERE YEAR(tgljual)='$periode' AND divprodid <>'OTHER' and icabangid <> 22 GROUP BY 1,2,3";
        $query = "select icabangid, date_format(tgljual,'%Y-%m') bulan, 'OTC' as divprodid, sum(`value`) as rpsales from $tmp03 GROUP BY 1,2,3";
        $query = "create TEMPORARY table $tmp04 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp01");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
        
    }else{
        
        $query = "select icabangid, date_format(bulan,'%Y-%m') bulan, divprodid, sum(value_sales) as rpsales from dbmaster.sales_local WHERE YEAR(bulan)='$periode' ";// DATE_FORMAT(bulan,'%Y%m')='$npppperi'
        if ($pidgrouppil=="8") {
            $ppilregion="B";
            if ($picardid=="0000000159") $ppilregion="T";
            $query .=" AND IFNULL(icabangid,'') IN (select distinct IFNULL(icabangid,'') FROM MKT.icabang WHERE region='$ppilregion' ) ";
        }elseif ($pidgrouppil=="30" AND !empty($ppilihpm)) {
            $query .=" AND IFNULL(divprodid,'') ='$ppilihpm' ";
        }
        
        $query .=" GROUP BY 1,2,3 ";
        $query = "create TEMPORARY table $tmp04 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "select * from $tmp04";
        $query = "create TEMPORARY table $tmp13 ($query)";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp13 SET icabangid='ZKLAIMDISC'";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "INSERT INTO $tmp04 select * from $tmp13";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp13");
        
    }
    
    $query = "CREATE INDEX `norm1` ON $tmp04 (bulan, divprodid)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    $query = "select DISTINCT a.divisi DIVISI, b.COA1, c.NAMA1, a.coa2 COA2, a.nama_coa2 NAMA2, "
            . " a.coa3 COA3, a.nama_coa3 NAMA3, coa COA4, nama_coa NAMA4 "
            . " from $tmp02 a LEFT JOIN dbmaster.coa_level2 b on "
            . " a.coa2=b.COA2 LEFT JOIN dbmaster.coa_level1 c on "
            . " b.COA1=c.COA1";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select * from (select distinct icabangid from $tmp02 UNION select distinct icabangid from $tmp04) as xxx ";
    $query = "create TEMPORARY table $tmp10 ($query)";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "ALTER table $tmp10 ADD COLUMN nama_cabang VARCHAR(100)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    if ($ppildivisiid=="OTC") {
        $query = "UPDATE $tmp10 a LEFT JOIN MKT.icabang_o b on a.icabangid=b.icabangid_o SET a.nama_cabang=b.nama";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp10 SET nama_cabang=icabangid WHERE IFNULL(nama_cabang,'')=''";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        //$query = "insert into $tmp10 (icabangid, nama_cabang)values('ETHHO', 'ETH - HO')";
        //$query = "UPDATE $tmp10 SET nama_cabang='ETH - HO' WHERE IFNULL(icabangid,'')='ETHHO'";
        //$query = "UPDATE $tmp10 SET nama_cabang='HO', icabangid='HO' WHERE IFNULL(icabangid,'')='ETHHO'";
        //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    }else{
        $query = "UPDATE $tmp10 a LEFT JOIN MKT.icabang b on a.icabangid=b.icabangid SET a.nama_cabang=b.nama";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp10 SET nama_cabang='ZKLAIMDISC' WHERE icabangid='ZKLAIMDISC'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    
    
    $query = "insert into $tmp10 (icabangid, nama_cabang)values('0000000000', 'zz')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "DELETE FROM $tmp10 where IFNULL(icabangid,'')=''";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $arridcab[]="";
    $arrnmcab[]="";
    $query = "select distinct icabangid, nama_cabang from $tmp10 order by nama_cabang, icabangid";
    $tampilk= mysqli_query($cnit, $query);
    while ($zr= mysqli_fetch_array($tampilk)) {
        $zidcab=$zr['icabangid'];
        $znmcab=$zr['nama_cabang'];
        
        $arridcab[]=$zidcab;
        $arrnmcab[]=$znmcab;
    }
    
    $addcolumn="";
    for($ix=1;$ix<count($arridcab);$ix++) {
        $zidcab=$arridcab[$ix];
        $znmcab=$arrnmcab[$ix];
        
        $nmfield1="B".$zidcab;
        $nmfield2="S".$zidcab;
        
        $addcolumn .= " ADD COLUMN $nmfield1 DECIMAL(20,2), ADD COLUMN $nmfield2 DECIMAL(20,2),";
        
    }
    //$addcolumn .= " ADD COLUMN B0000000000 DECIMAL(20,2), ADD COLUMN S0000000000 DECIMAL(20,2), ADD COLUMN TOTAL DECIMAL(20,2), ADD COLUMN STOTAL DECIMAL(20,2)";
    $addcolumn .= " ADD COLUMN TOTAL DECIMAL(20,2), ADD COLUMN STOTAL DECIMAL(20,2)";
    
    $query = "ALTER TABLE $tmp03 $addcolumn";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    for($ix=1;$ix<count($arridcab);$ix++) {
        $zidcab=$arridcab[$ix];
        $znmcab=$arrnmcab[$ix];
        
        $nmfield1="a.B".$zidcab;
        $nmfield2="a.S".$zidcab;
        
        $filcabid=$zidcab;
        if (empty($zidcab) OR $zidcab=="0000000000") $filcabid="";
        
        $query = "UPDATE $tmp03 a JOIN (select divisi, coa, sum(kredit) as kredit from $tmp02 WHERE IFNULL(icabangid,'')='$filcabid' GROUP BY 1,2) b on "
                . " a.divisi=b.divisi AND a.COA4=b.coa SET $nmfield1=b.kredit";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp03 a JOIN (select divprodid, sum(rpsales) rpsales from $tmp04 WHERE IFNULL(icabangid,'')='$filcabid' GROUP BY 1) b on "
                . " a.divisi=b.divprodid SET $nmfield2=b.rpsales";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    
    
    
    $query = "select * from $tmp02 WHERE COA2='105'";
    $query = "create TEMPORARY table $tmp05 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select * from $tmp03 WHERE COA2='105'";
    $query = "create TEMPORARY table $tmp06 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select * from $tmp04 WHERE divprodid='xxx_001' LIMIT 1";
    $query = "create TEMPORARY table $tmp07 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    $query = "DELETE from $tmp02 WHERE COA2='105'";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "DELETE from $tmp03 WHERE COA2='105'";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "DELETE from $tmp07";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //$query = "DELETE from $tmp04 WHERE icabangid IN ('PM_ACNEMED', 'PM_CARMED', 'PM_LANORE', 'PM_MELANOX', 'PM_PARASOL', 'ZKLAIMDISC')";
    //mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
                                            
    $parraycabang=array('PM_ACNEMED', 'PM_CARMED', 'PM_LANORE', 'PM_MELANOX', 'PM_PARASOL', 'ZKLAIMDISC');
?>

<HTML>
<HEAD>
    <title>REPORT REALISASI BIAYA MARKETING BY CABANG</title>
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
    
    <center><div class='h1judul'>REPORT REALISASI BIAYA MARKETING BY CABANG</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <tr><td>Tahun</td><td>:</td><td><?PHP echo "<b>$periode</b>"; ?></td></tr>
            <tr class='miring text2'><td>view date</td><td>:</td><td><?PHP echo "$printdate"; ?></td></tr>
        </table>
    </div>
    <div class="clearfix"></div>
    <hr/>
   
    
    
        <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                    <th align="center" nowrap>Kode</th>
                    <th align="center" nowrap>Nama Perkiraan</th>

                    <?PHP
                    $jmlcolspan=0;
                    for($ix=1;$ix<count($arridcab);$ix++) {
                        $zidcab=$arridcab[$ix];
                        $znmcab=$arrnmcab[$ix];
                        
                        if ($znmcab=="zz") $znmcab="OTHERS";
                        elseif ($znmcab=="ZKLAIMDISC") $znmcab="KLAIM DISCOUNT";
                        
                        echo "<th align='center' nowrap>%</th>";
                        echo "<th align='center' nowrap>$znmcab</th>";
                        
                        $jmlcolspan++; $jmlcolspan++;
                    }
                    $jmlcolspan=(double)$jmlcolspan+3;
                    
                    ?>
                    
                    <th align="center" nowrap>%</th>
                    <th align="center" nowrap>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                
             <?PHP
                for($ix=1;$ix<count($arridcab);$ix++) {
                    $pgrandtotal[$ix]=0;
                    $pgrandtotalsls[$ix]=0;
                }
                $query = "select distinct DIVISI from $tmp03 ORDER BY DIVISI";
                $tampil0=mysqli_query($cnmy, $query);
                while ($row0= mysqli_fetch_array($tampil0)) {
                    $divisi=$row0['DIVISI'];
                    $nmdivisi=$row0['DIVISI'];
                    if ($nmdivisi=="CAN") $nmdivisi="CANARY";
                    if ($nmdivisi=="PIGEO") $nmdivisi="PIGEON";
                    if ($nmdivisi=="PEACO") $nmdivisi="PEACOCK";
                    
                    $mdivisi=$nmdivisi;
                    if ($mdivisi=="AA") $mdivisi="OTHERS";
                    
                    for($ix=1;$ix<count($arridcab);$ix++) {
                        $ptotdivisi[$ix]=0;
                        $ptotdivisisls[$ix]=0;
                    }
                    
                    $query = "select distinct IFNULL(DIVISI,'') DIVISI, IFNULL(COA2,'') COA2, IFNULL(NAMA2,'') NAMA2 from $tmp03 WHERE DIVISI='$divisi' ORDER BY IFNULL(DIVISI,''), IFNULL(COA2,''), IFNULL(NAMA2,'')";
                    $tampil=mysqli_query($cnmy, $query);
                    while ($row= mysqli_fetch_array($tampil)) {
                        
                        $pdivisi=$row['DIVISI'];
                        $pcoa2=$row['COA2'];
                        $pnmcoa2=$row['NAMA2'];
                        
                        echo "<tr>";
                        echo "<td nowrap><b>$pcoa2</b></td>";
                        echo "<td nowrap colspan='$jmlcolspan'><b>$pnmcoa2</b></td>";
                        
                        echo "</tr>";
                        
                        for($ix=1;$ix<count($arridcab);$ix++) {
                            $psubtot[$ix]=0;
                        }
                    
                        $query = "select * from $tmp03 WHERE IFNULL(DIVISI,'')='$divisi' AND IFNULL(COA2,'')='$pcoa2' ORDER BY IFNULL(COA4,''), IFNULL(NAMA4,'')";
                        $tampil2=mysqli_query($cnmy, $query);
                        while ($row2= mysqli_fetch_array($tampil2)) {
                            
                            $pcoa4=$row2['COA4'];
                            $pnmcoa4=$row2['NAMA4'];
                            
                            echo "<tr>";
                            
                            echo "<td nowrap>$pcoa4</td>";
                            echo "<td nowrap>$pnmcoa4</td>";
                            
                            //hitung dulu sales per jajar
                            $totsalestahunan=0;
                            for($ix=1;$ix<count($arridcab);$ix++) {
                                $zidcab=$arridcab[$ix];
                                $znmcab=$arrnmcab[$ix];
                                
                                $snmcol="S".$zidcab;
                                $pjml=$row2[$snmcol];
                                if (empty($pjml)) $pjml=0;
                                $totsalestahunan=(double)$totsalestahunan+(double)$pjml;
                            }
                            //END hitung dulu sales per jajar
                            
                            $ptotaltahund=0;
                            
                            for($ix=1;$ix<count($arridcab);$ix++) {
                                $zidcab=$arridcab[$ix];
                                $znmcab=$arrnmcab[$ix];
                                
                                $nmcol="B".$zidcab;
                                $pjml=$row2[$nmcol];
                                if (empty($pjml)) $pjml=0;
                                
                                $ptotaltahund=(double)$ptotaltahund+(double)$pjml;
                                $psubtot[$ix]=(double)$psubtot[$ix]+(double)$pjml;
                                
                                //sales
                                $snmcol="S".$zidcab;
                                $pjmlsls=$row2[$snmcol];
                                if (empty($pjmlsls)) $pjmlsls=0;
                                $ptotdivisisls[$ix]=$pjmlsls;
                                
                                
                                
                                if ((double)$pjmlsls==0) {
                                    $npersen=0;
                                }else{
                                    $npersen=ROUND((double)$pjml/(double)$pjmlsls*100,2);
                                }
                                if ((double)$npersen==0) $npersen="";
                                    
                                $pjml=number_format($pjml,0,",",",");
                                
                                echo "<td align='right' nowrap>$npersen</td>";
                                echo "<td align='right' nowrap>$pjml</td>";
                                
                                
                            }
                            
                            if ((double)$totsalestahunan==0) {
                                $inpersen=0;
                            }else{
                                $inpersen=ROUND((double)$ptotaltahund/(double)$totsalestahunan*100,2);
                            }
                            if ((double)$inpersen==0) $inpersen="";
                            
                            $ptotaltahund=number_format($ptotaltahund,0,",",",");
                            
                            echo "<td align='right' nowrap>$inpersen</td>";
                            echo "<td align='right' nowrap>$ptotaltahund</td>";

                            echo "</tr>";
                            
                            
                        }
                        
                        //sub total
                        echo "<tr>";
                        echo "<td nowrap><b>$pcoa2</b></td>";
                        echo "<td nowrap><b>$pnmcoa2</b></td>";
                        
                        
                        $ptotaltahund=0;
                        for($ix=1;$ix<count($arridcab);$ix++) {

                            $pjml=$psubtot[$ix];
                            if (empty($pjml)) $pjml=0;
                            
                            $ptotaltahund=(double)$ptotaltahund+(double)$pjml;
                            $ptotdivisi[$ix]=(double)$ptotdivisi[$ix]+(double)$pjml;
                            
                            $pjmlsls=$ptotdivisisls[$ix];
                            if ((double)$pjmlsls==0) {
                                $npersen=0;
                            }else{
                                $npersen=ROUND((double)$pjml/(double)$pjmlsls*100,2);
                            }
                            if ((double)$npersen==0) $npersen="";

                            
                            
                            $pjml=number_format($pjml,0,",",",");
                            
                            
                            echo "<td align='right' nowrap><b>$npersen</b></td>";
                            echo "<td align='right' nowrap><b>".$pjml."</b></td>";

                        }
                        
                        if ((double)$totsalestahunan==0) {
                            $inpersen=0;
                        }else{
                            $inpersen=ROUND((double)$ptotaltahund/(double)$totsalestahunan*100,2);
                        }
                        if ((double)$inpersen==0) $inpersen="";
                    
                        $ptotaltahund=number_format($ptotaltahund,0,",",",");
                        echo "<td align='right' nowrap><b>$inpersen</b></td>";
                        echo "<td align='right' nowrap><b>$ptotaltahund</b></td>";

                        echo "</tr>";
                        
                        echo "<tr>";
                        echo "<td nowrap colspan='$jmlcolspan'><b>&nbsp;</b></td>";
                        echo "</tr>";
                        
                        
                        
                    }
                    
                    //total per divisi
                    
                    if ($ppildivisiid!="OTC") {
                        echo "<tr>";
                        echo "<td nowrap><b></b></td>";
                        echo "<td nowrap><b>BIAYA $mdivisi</b></td>";
                    }
                    $ztotbr=0;
                    $ztotsls=0;


                    $urut=2;
                    for($ix=1;$ix<count($arridcab);$ix++) {
                        $zidcab=$arridcab[$ix];
                        $znmcab=$arrnmcab[$ix];
                                
                        $ztotalbr[$ix]=0;
                        $ztotalsls[$ix]=0;

                        $jml=  strlen($ix);
                        $awal=$urut-$jml;
                        //$zbulan=$periode."-".str_repeat("0", $awal).$ix;

                        $filcabid=$zidcab;
                        if (empty($zidcab) OR $zidcab=="0000000000") $filcabid="";
                        //cari total br
                        $query = "select IFNULL(sum(jumlah),0) jumlah From $tmp02 WHERE divisi='$divisi' AND IFNULL(icabangid,'')='$filcabid'";
                        $rowslb=mysqli_query($cnmy, $query);
                        $ketemubr= mysqli_num_rows($rowslb);
                        if ($ketemubr>0) {
                            $rslb= mysqli_fetch_array($rowslb);
                            $ztotalbr[$ix]=$rslb['jumlah'];
                            $ztotbr=(double)$ztotbr+(double)$ztotalbr[$ix];
                        }

                        //cari total sales
                        $query = "select IFNULL(sum(rpsales),0) rpsales From $tmp04 WHERE divprodid='$divisi' AND IFNULL(icabangid,'')='$filcabid'";
                        $rowsls=mysqli_query($cnmy, $query);
                        $ketemusls= mysqli_num_rows($rowsls);
                        if ($ketemusls>0) {
                            $rsls= mysqli_fetch_array($rowsls);
                            $ztotalsls[$ix]=$rsls['rpsales'];
                            if ($zidcab=="ZKLAIMDISC" AND $ppildivisiid!="OTC") {
                            }else{
                                $ztotsls=(double)$ztotsls+(double)$ztotalsls[$ix];
                            }
                        }


                        if ((double)$ztotalsls[$ix]==0) {
                            $zpersen=0;
                        }else{
                            $zpersen=ROUND((double)$ztotalbr[$ix]/(double)$ztotalsls[$ix]*100,2);
                        }

                        $ztotalbr[$ix]=number_format($ztotalbr[$ix],0,",",",");
                        if ($ppildivisiid!="OTC") {
                            echo "<td align='right' nowrap><b>$zpersen</b></td>";
                            echo "<td align='right' nowrap><b>".$ztotalbr[$ix]."</b></td>";
                        }

                    }

                    if ((double)$ztotsls==0) {
                        $zpersen=0;
                    }else{
                        $zpersen=ROUND((double)$ztotbr/(double)$ztotsls*100,2);
                    }
                    $ztotbr=number_format($ztotbr,0,",",",");
                    
                    if ($ppildivisiid!="OTC") {
                        echo "<td align='right' nowrap><b>$zpersen</b></td>";
                        echo "<td align='right' nowrap><b>$ztotbr</b></td>";

                        echo "</tr>";
                    }
                    
                    
                    //sales
                    if ($ppildivisiid!="OTC") {
                        
                        if ($mdivisi!="HO" AND $mdivisi!="CANARY" AND $mdivisi!="CAN" AND $mdivisi!="OTHER" AND $mdivisi!="OTHERS") {

                            echo "<tr>";
                            echo "<td nowrap><b></b></td>";
                            echo "<td nowrap><b>PENJUALAN S2 $mdivisi</b></td>";

                                for($ix=1;$ix<count($arridcab);$ix++) {

                                    if ((double)$ztotsls==0) {
                                        $zpersen=0;
                                    }else{
                                        $zpersen=ROUND((double)$ztotalsls[$ix]/(double)$ztotsls*100,2);
                                    }

                                    $ztotalsls[$ix]=number_format($ztotalsls[$ix],0,",",",");
                                    echo "<td align='right' nowrap><b>$zpersen</b></td>";
                                    echo "<td align='right' nowrap><b>".$ztotalsls[$ix]."</b></td>";
                                }

                            $ztotsls=number_format($ztotsls,0,",",",");
                            echo "<td align='right' nowrap><b>100</b></td>";
                            echo "<td align='right' nowrap><b>$ztotsls</b></td>";

                            echo "</tr>";

                        }
                        
                        echo "<tr>";
                        echo "<td nowrap colspan='$jmlcolspan'><b>&nbsp;</b></td>";
                        echo "</tr>";
                        
                    }
                    
                    
                    
                }
                
                // grand total
                echo "<tr>";
                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b>TOTAL BIAYA MARKETING</b></td>";

                $ztotbr=0;
                $ztotsls=0;


                $urut=2;
                for($ix=1;$ix<count($arridcab);$ix++) {
                    $zidcab=$arridcab[$ix];
                    $znmcab=$arrnmcab[$ix];
                        
                    $ztotalbr[$ix]=0;
                    $ztotalsls[$ix]=0;

                    $jml=  strlen($ix);
                    $awal=$urut-$jml;
                    //$zbulan=$periode."-".str_repeat("0", $awal).$ix;

                    $filcabid=$zidcab;
                    if (empty($zidcab) OR $zidcab=="0000000000") $filcabid="";
                    
                    //cari total br
                    $query = "select IFNULL(sum(jumlah),0) jumlah From $tmp02 WHERE IFNULL(icabangid,'')='$filcabid'";
                    $rowslb=mysqli_query($cnmy, $query);
                    $ketemubr= mysqli_num_rows($rowslb);
                    if ($ketemubr>0) {
                        $rslb= mysqli_fetch_array($rowslb);
                        $ztotalbr[$ix]=$rslb['jumlah'];
                        $ztotbr=(double)$ztotbr+(double)$ztotalbr[$ix];
                    }

                    //cari total sales
                    $query = "select IFNULL(sum(rpsales),0) rpsales From $tmp04 WHERE IFNULL(icabangid,'')='$filcabid'";
                    $rowsls=mysqli_query($cnmy, $query);
                    $ketemusls= mysqli_num_rows($rowsls);
                    if ($ketemusls>0) {
                        $rsls= mysqli_fetch_array($rowsls);
                        $ztotalsls[$ix]=$rsls['rpsales'];
                        
                        if (in_array($zidcab, $parraycabang)) {
                            
                        }else{
                            $ztotsls=(double)$ztotsls+(double)$ztotalsls[$ix];
                        }
                        
                        
                    }


                    if ((double)$ztotalsls[$ix]==0) {
                        $zpersen=0;
                    }else{
                        $zpersen=ROUND((double)$ztotalbr[$ix]/(double)$ztotalsls[$ix]*100,2);
                    }

                    $ztotalbr[$ix]=number_format($ztotalbr[$ix],0,",",",");
                    echo "<td align='right' nowrap><b>$zpersen</b></td>";
                    echo "<td align='right' nowrap><b>".$ztotalbr[$ix]."</b></td>";

                }

                if ((double)$ztotsls==0) {
                    $zpersen=0;
                }else{
                    $zpersen=ROUND((double)$ztotbr/(double)$ztotsls*100,2);
                }
                $ztotbr=number_format($ztotbr,0,",",",");
                echo "<td align='right' nowrap><b>$zpersen</b></td>";
                echo "<td align='right' nowrap><b>$ztotbr</b></td>";

                echo "</tr>";

                //sales
                echo "<tr>";
                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b>PENJUALAN S2 MARKETING</b></td>";

                    for($ix=1;$ix<count($arridcab);$ix++) {

                        if ((double)$ztotsls==0) {
                            $zpersen=0;
                        }else{
                            $zpersen=ROUND((double)$ztotalsls[$ix]/(double)$ztotsls*100,2);
                        }

                        $ztotalsls[$ix]=number_format($ztotalsls[$ix],0,",",",");
                        echo "<td align='right' nowrap><b>$zpersen</b></td>";
                        echo "<td align='right' nowrap><b>".$ztotalsls[$ix]."</b></td>";
                    }

                
                $ztotsls=number_format($ztotsls,0,",",",");
                echo "<td align='right' nowrap><b>100</b></td>";
                echo "<td align='right' nowrap><b>$ztotsls</b></td>";

                echo "</tr>";
             ?>
                
            </tbody>
        </table>
        <br/>&nbsp;
        <hr/>
        <div class="clearfix"></div>

        
        
        <?PHP
        $query = "select * from $tmp06";
        $tampiln=mysqli_query($cnit, $query);
        $ketemuan= mysqli_fetch_array($tampiln);
        if ($ketemuan>0) {
        ?>
        
        <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                    <th align="center" nowrap>Kode</th>
                    <th align="center" nowrap>Nama Perkiraan</th>

                    <?PHP
                    $jmlcolspan=0;
                    for($ix=1;$ix<count($arridcab);$ix++) {
                        $zidcab=$arridcab[$ix];
                        $znmcab=$arrnmcab[$ix];
                        
                        if ($znmcab=="zz") $znmcab="OTHERS";
                        elseif ($znmcab=="ZKLAIMDISC") $znmcab="KLAIM DISCOUNT";
                        
                        echo "<th align='center' nowrap>%</th>";
                        echo "<th align='center' nowrap>$znmcab</th>";
                        
                        $jmlcolspan++; $jmlcolspan++;
                    }
                    $jmlcolspan=(double)$jmlcolspan+3;
                    
                    ?>
                    
                    <th align="center" nowrap>%</th>
                    <th align="center" nowrap>TOTAL</th>
                </tr>
            </thead>
            <tbody>
             <?PHP
                for($ix=1;$ix<count($arridcab);$ix++) {
                    $pgrandtotal[$ix]=0;
                    $pgrandtotalsls[$ix]=0;
                }
                $query = "select distinct DIVISI from $tmp06 ORDER BY DIVISI";
                $tampil0=mysqli_query($cnmy, $query);
                while ($row0= mysqli_fetch_array($tampil0)) {
                    $divisi=$row0['DIVISI'];
                    $nmdivisi=$row0['DIVISI'];
                    if ($nmdivisi=="CAN") $nmdivisi="CANARY";
                    if ($nmdivisi=="PIGEO") $nmdivisi="PIGEON";
                    if ($nmdivisi=="PEACO") $nmdivisi="PEACOCK";
                    
                    $mdivisi=$nmdivisi;
                    if ($mdivisi=="AA") $mdivisi="OTHERS";
                    
                    for($ix=1;$ix<count($arridcab);$ix++) {
                        $ptotdivisi[$ix]=0;
                        $ptotdivisisls[$ix]=0;
                    }
                    
                    $query = "select distinct IFNULL(DIVISI,'') DIVISI, IFNULL(COA2,'') COA2, IFNULL(NAMA2,'') NAMA2 from $tmp06 WHERE DIVISI='$divisi' ORDER BY IFNULL(DIVISI,''), IFNULL(COA2,''), IFNULL(NAMA2,'')";
                    $tampil=mysqli_query($cnmy, $query);
                    while ($row= mysqli_fetch_array($tampil)) {
                        
                        $pdivisi=$row['DIVISI'];
                        $pcoa2=$row['COA2'];
                        $pnmcoa2=$row['NAMA2'];
                        
                        echo "<tr>";
                        echo "<td nowrap><b>$pcoa2</b></td>";
                        echo "<td nowrap colspan='$jmlcolspan'><b>$pnmcoa2</b></td>";
                        
                        echo "</tr>";
                        
                        for($ix=1;$ix<count($arridcab);$ix++) {
                            $psubtot[$ix]=0;
                        }
                    
                        $query = "select * from $tmp06 WHERE IFNULL(DIVISI,'')='$divisi' AND IFNULL(COA2,'')='$pcoa2' ORDER BY IFNULL(COA4,''), IFNULL(NAMA4,'')";
                        $tampil2=mysqli_query($cnmy, $query);
                        while ($row2= mysqli_fetch_array($tampil2)) {
                            
                            $pcoa4=$row2['COA4'];
                            $pnmcoa4=$row2['NAMA4'];
                            
                            echo "<tr>";
                            
                            echo "<td nowrap>$pcoa4</td>";
                            echo "<td nowrap>$pnmcoa4</td>";
                            
                            //hitung dulu sales per jajar
                            $totsalestahunan=0;
                            for($ix=1;$ix<count($arridcab);$ix++) {
                                $zidcab=$arridcab[$ix];
                                $znmcab=$arrnmcab[$ix];
                                
                                $snmcol="S".$zidcab;
                                $pjml=$row2[$snmcol];
                                if (empty($pjml)) $pjml=0;
                                $totsalestahunan=(double)$totsalestahunan+(double)$pjml;
                            }
                            //END hitung dulu sales per jajar
                            
                            $ptotaltahund=0;
                            
                            for($ix=1;$ix<count($arridcab);$ix++) {
                                $zidcab=$arridcab[$ix];
                                $znmcab=$arrnmcab[$ix];
                                
                                $nmcol="B".$zidcab;
                                $pjml=$row2[$nmcol];
                                if (empty($pjml)) $pjml=0;
                                
                                $ptotaltahund=(double)$ptotaltahund+(double)$pjml;
                                $psubtot[$ix]=(double)$psubtot[$ix]+(double)$pjml;
                                
                                //sales
                                $snmcol="S".$zidcab;
                                $pjmlsls=$row2[$snmcol];
                                if (empty($pjmlsls)) $pjmlsls=0;
                                $ptotdivisisls[$ix]=$pjmlsls;
                                
                                
                                
                                if ((double)$pjmlsls==0) {
                                    $npersen=0;
                                }else{
                                    $npersen=ROUND((double)$pjml/(double)$pjmlsls*100,2);
                                }
                                if ((double)$npersen==0) $npersen="";
                                    
                                $pjml=number_format($pjml,0,",",",");
                                
                                echo "<td align='right' nowrap>$npersen</td>";
                                echo "<td align='right' nowrap>$pjml</td>";
                                
                                
                            }
                            
                            if ((double)$totsalestahunan==0) {
                                $inpersen=0;
                            }else{
                                $inpersen=ROUND((double)$ptotaltahund/(double)$totsalestahunan*100,2);
                            }
                            if ((double)$inpersen==0) $inpersen="";
                            
                            $ptotaltahund=number_format($ptotaltahund,0,",",",");
                            
                            echo "<td align='right' nowrap>$inpersen</td>";
                            echo "<td align='right' nowrap>$ptotaltahund</td>";

                            echo "</tr>";
                            
                            
                        }
                        
                        //sub total
                        echo "<tr>";
                        echo "<td nowrap><b>$pcoa2</b></td>";
                        echo "<td nowrap><b>$pnmcoa2</b></td>";
                        
                        
                        $ptotaltahund=0;
                        for($ix=1;$ix<count($arridcab);$ix++) {

                            $pjml=$psubtot[$ix];
                            if (empty($pjml)) $pjml=0;
                            
                            $ptotaltahund=(double)$ptotaltahund+(double)$pjml;
                            $ptotdivisi[$ix]=(double)$ptotdivisi[$ix]+(double)$pjml;
                            
                            $pjmlsls=$ptotdivisisls[$ix];
                            if ((double)$pjmlsls==0) {
                                $npersen=0;
                            }else{
                                $npersen=ROUND((double)$pjml/(double)$pjmlsls*100,2);
                            }
                            if ((double)$npersen==0) $npersen="";

                            
                            
                            $pjml=number_format($pjml,0,",",",");
                            
                            
                            echo "<td align='right' nowrap><b>$npersen</b></td>";
                            echo "<td align='right' nowrap><b>".$pjml."</b></td>";

                        }
                        
                        if ((double)$totsalestahunan==0) {
                            $inpersen=0;
                        }else{
                            $inpersen=ROUND((double)$ptotaltahund/(double)$totsalestahunan*100,2);
                        }
                        if ((double)$inpersen==0) $inpersen="";
                    
                        $ptotaltahund=number_format($ptotaltahund,0,",",",");
                        echo "<td align='right' nowrap><b>$inpersen</b></td>";
                        echo "<td align='right' nowrap><b>$ptotaltahund</b></td>";

                        echo "</tr>";
                        
                        echo "<tr>";
                        echo "<td nowrap colspan='$jmlcolspan'><b>&nbsp;</b></td>";
                        echo "</tr>";
                        
                        
                        
                    }
                    
                    //total per divisi
                    
                    if ($ppildivisiid!="OTC") {
                        echo "<tr>";
                        echo "<td nowrap><b></b></td>";
                        echo "<td nowrap><b>BIAYA $mdivisi</b></td>";
                    }
                    $ztotbr=0;
                    $ztotsls=0;


                    $urut=2;
                    for($ix=1;$ix<count($arridcab);$ix++) {
                        $zidcab=$arridcab[$ix];
                        $znmcab=$arrnmcab[$ix];
                                
                        $ztotalbr[$ix]=0;
                        $ztotalsls[$ix]=0;

                        $jml=  strlen($ix);
                        $awal=$urut-$jml;
                        //$zbulan=$periode."-".str_repeat("0", $awal).$ix;

                        $filcabid=$zidcab;
                        if (empty($zidcab) OR $zidcab=="0000000000") $filcabid="";
                    
                        //cari total br
                        $query = "select IFNULL(sum(jumlah),0) jumlah From $tmp05 WHERE divisi='$divisi' AND IFNULL(icabangid,'')='$filcabid'";
                        $rowslb=mysqli_query($cnmy, $query);
                        $ketemubr= mysqli_num_rows($rowslb);
                        if ($ketemubr>0) {
                            $rslb= mysqli_fetch_array($rowslb);
                            $ztotalbr[$ix]=$rslb['jumlah'];
                            $ztotbr=(double)$ztotbr+(double)$ztotalbr[$ix];
                        }

                        //cari total sales
                        $query = "select IFNULL(sum(rpsales),0) rpsales From $tmp07 WHERE divprodid='$divisi' AND IFNULL(icabangid,'')='$filcabid'";
                        $rowsls=mysqli_query($cnmy, $query);
                        $ketemusls= mysqli_num_rows($rowsls);
                        if ($ketemusls>0) {
                            $rsls= mysqli_fetch_array($rowsls);
                            $ztotalsls[$ix]=$rsls['rpsales'];
                            $ztotsls=(double)$ztotsls+(double)$ztotalsls[$ix];
                        }


                        if ((double)$ztotalsls[$ix]==0) {
                            $zpersen=0;
                        }else{
                            $zpersen=ROUND((double)$ztotalbr[$ix]/(double)$ztotalsls[$ix]*100,2);
                        }

                        $ztotalbr[$ix]=number_format($ztotalbr[$ix],0,",",",");
                        
                        if ($ppildivisiid!="OTC") {
                            echo "<td align='right' nowrap><b>$zpersen</b></td>";
                            echo "<td align='right' nowrap><b>".$ztotalbr[$ix]."</b></td>";
                        }

                    }

                    if ((double)$ztotsls==0) {
                        $zpersen=0;
                    }else{
                        $zpersen=ROUND((double)$ztotbr/(double)$ztotsls*100,2);
                    }
                    $ztotbr=number_format($ztotbr,0,",",",");
                    
                    if ($ppildivisiid!="OTC") {
                        echo "<td align='right' nowrap><b>$zpersen</b></td>";
                        echo "<td align='right' nowrap><b>$ztotbr</b></td>";

                        echo "</tr>";
                    }
                    
                    
                    //sales
                    /*
                    if ($ppildivisiid!="OTC") {
                        if ($mdivisi!="HO" AND $mdivisi!="CANARY" AND $mdivisi!="CAN" AND $mdivisi!="OTHER" AND $mdivisi!="OTHERS") {

                            echo "<tr>";
                            echo "<td nowrap><b></b></td>";
                            echo "<td nowrap><b>PENJUALAN S2 $mdivisi</b></td>";

                                for($ix=1;$ix<count($arridcab);$ix++) {

                                    if ((double)$ztotsls==0) {
                                        $zpersen=0;
                                    }else{
                                        $zpersen=ROUND((double)$ztotalsls[$ix]/(double)$ztotsls*100,2);
                                    }

                                    $ztotalsls[$ix]=number_format($ztotalsls[$ix],0,",",",");
                                    echo "<td align='right' nowrap><b>$zpersen</b></td>";
                                    echo "<td align='right' nowrap><b>".$ztotalsls[$ix]."</b></td>";
                                }

                            $ztotsls=number_format($ztotsls,0,",",",");
                            echo "<td align='right' nowrap><b>100</b></td>";
                            echo "<td align='right' nowrap><b>$ztotsls</b></td>";

                            echo "</tr>";

                        }
                    }
                    */
                    if ($ppildivisiid!="OTC") {
                        echo "<tr>";
                        echo "<td nowrap colspan='$jmlcolspan'><b>&nbsp;</b></td>";
                        echo "</tr>";
                    }
                    
                    
                }
                
                // grand total
                echo "<tr>";
                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b>TOTAL BIAYA MARKETING</b></td>";

                $ztotbr=0;
                $ztotsls=0;


                $urut=2;
                for($ix=1;$ix<count($arridcab);$ix++) {
                    $zidcab=$arridcab[$ix];
                    $znmcab=$arrnmcab[$ix];
                        
                    $ztotalbr[$ix]=0;
                    $ztotalsls[$ix]=0;

                    $jml=  strlen($ix);
                    $awal=$urut-$jml;
                    //$zbulan=$periode."-".str_repeat("0", $awal).$ix;

                    $filcabid=$zidcab;
                    if (empty($zidcab) OR $zidcab=="0000000000") $filcabid="";
                        
                    //cari total br
                    $query = "select IFNULL(sum(jumlah),0) jumlah From $tmp05 WHERE IFNULL(icabangid,'')='$filcabid'";
                    $rowslb=mysqli_query($cnmy, $query);
                    $ketemubr= mysqli_num_rows($rowslb);
                    if ($ketemubr>0) {
                        $rslb= mysqli_fetch_array($rowslb);
                        $ztotalbr[$ix]=$rslb['jumlah'];
                        $ztotbr=(double)$ztotbr+(double)$ztotalbr[$ix];
                    }

                    //cari total sales
                    $query = "select IFNULL(sum(rpsales),0) rpsales From $tmp07 WHERE IFNULL(icabangid,'')='$filcabid'";
                    $rowsls=mysqli_query($cnmy, $query);
                    $ketemusls= mysqli_num_rows($rowsls);
                    if ($ketemusls>0) {
                        $rsls= mysqli_fetch_array($rowsls);
                        $ztotalsls[$ix]=$rsls['rpsales'];
                        $ztotsls=(double)$ztotsls+(double)$ztotalsls[$ix];
                    }


                    if ((double)$ztotalsls[$ix]==0) {
                        $zpersen=0;
                    }else{
                        $zpersen=ROUND((double)$ztotalbr[$ix]/(double)$ztotalsls[$ix]*100,2);
                    }

                    $ztotalbr[$ix]=number_format($ztotalbr[$ix],0,",",",");
                    echo "<td align='right' nowrap><b>$zpersen</b></td>";
                    echo "<td align='right' nowrap><b>".$ztotalbr[$ix]."</b></td>";

                }

                if ((double)$ztotsls==0) {
                    $zpersen=0;
                }else{
                    $zpersen=ROUND((double)$ztotbr/(double)$ztotsls*100,2);
                }
                $ztotbr=number_format($ztotbr,0,",",",");
                echo "<td align='right' nowrap><b>$zpersen</b></td>";
                echo "<td align='right' nowrap><b>$ztotbr</b></td>";

                echo "</tr>";

                //sales
                /*
                
                echo "<tr>";
                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b>PENJUALAN S2 MARKETING</b></td>";

                    for($ix=1;$ix<count($arridcab);$ix++) {

                        if ((double)$ztotsls==0) {
                            $zpersen=0;
                        }else{
                            $zpersen=ROUND((double)$ztotalsls[$ix]/(double)$ztotsls*100,2);
                        }

                        $ztotalsls[$ix]=number_format($ztotalsls[$ix],0,",",",");
                        echo "<td align='right' nowrap><b>$zpersen</b></td>";
                        echo "<td align='right' nowrap><b>".$ztotalsls[$ix]."</b></td>";
                    }

                
                $ztotsls=number_format($ztotsls,0,",",",");
                echo "<td align='right' nowrap><b>100</b></td>";
                echo "<td align='right' nowrap><b>$ztotsls</b></td>";

                echo "</tr>";
                 * 
                 */
             ?>
            </tbody>
        </table>
        
        
        <?PHP
        }
        ?>
        
        <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;    
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
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp09");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp10");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp11");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp12");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp13");
    mysqli_close($cnmy);
?>