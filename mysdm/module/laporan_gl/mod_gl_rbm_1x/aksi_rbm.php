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
        header("Content-Disposition: attachment; filename=REPORT REALISASI BIAYA MARKETING.xls");
    }
    
    include("config/koneksimysqli.php");
    $cnit=$cnmy;
    
    $printdate= date("d/m/Y");
    
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
    
?>

<?PHP
    include "config/fungsi_combo.php";
    include("config/common.php");
    
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
    
    
    $psewakontrak=""; $pserviceken="";
    if (isset($_POST['chkbox_rpt11'])) $psewakontrak=$_POST['chkbox_rpt11'];
    if (isset($_POST['chkbox_rpt12'])) $pserviceken=$_POST['chkbox_rpt12'];
    
    
    $pbelumprosesclose=false;
    if ($ptahuninput=="2019") {
        $pbelumprosesclose=true;
        
        $pfilterselpil="";
        //BR ETHICAL A
        if (!empty($pbreth)) $pfilterselpil .= "'A',";
        //klaimdiscount B
        if (!empty($pklaim)) $pfilterselpil .= "'B',";
        //KAS KASBON C & D
        if (!empty($pkas)) $pfilterselpil .= "'C','D',";
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
        if (!empty($ppilbank)) $pfilterselpil .= "'L','M','N','O','P',";
        
        //sewa kontrakan rumah
        if (!empty($psewakontrak)) $pfilterselpil .= "'U',";
        //service kendaraan
        if (!empty($pserviceken)) $pfilterselpil .= "'V',";
		
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
    
    
        $query = "select noidauto, kodeinput, idkodeinput as idinput, nobrid_r, nobrid_n, divisi, tgltrans, nobukti as bukti, "
                . " coa, nama_coa, dokter_nama as dokter, noslip, pengajuan, keterangan, "
                . " nmrealisasi, rincian as mintadana, debit, kredit, saldo, "
                . " dpp, ppn, pph, tglfp, nodivisi, divisi2, idinput_pd as idinputdiv "
                . " from dbmaster.t_proses_bm_act WHERE IFNULL(hapus_nodiv_kosong,'') <>'Y' AND DATE_FORMAT(tgltarikan,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' AND "
                . " kodeinput IN $pfilterselpil ";
        
        $query ="SELECT noidauto, tahun, periode, hapus_nodiv_kosong, kodeinput, idkodeinput as idinput, nobrid_r, nobrid_n, idkodeinput, divisi, tglinput, tgltrans, "
            . " karyawanid, nama_karyawan, dokterid, dokter_nama, noslip, nmrealisasi, keterangan, dpp, ppn, pph, tglfp, "
            . " idinput_pd, nodivisi, debit, kredit, saldo, jumlah1, jumlah2, "
            . " divisi_edit as divisi_coa, coa_edit as coa, coa_nama_edit as nama_coa, coa_edit2 as coa2, "
            . " coa_nama_edit2 as nama_coa2, coa_edit3 as coa3, coa_nama_edit3 as nama_coa3, "
            . " tgl_trans_bank, nobukti, idinput_pd1, idinput_pd2, nodivisi1, nodivisi2, pengajuan, "
            . " divisi2, icabangid, nama_cabang, areaid, nama_area, kodeid_pd, subkode_pd, pcm, kasbonsby, coa_pcm, nama_coa_pcm, "
            . " tgltarikan, nkodeid, nkodeid_nama "
            . " FROM dbmaster.t_proses_bm_act WHERE IFNULL(hapus_nodiv_kosong,'') <>'Y' AND DATE_FORMAT(tgltarikan,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' AND "
            . " kodeinput IN $pfilterselpil ";
        if (!empty($pdivisi)) $query .=" AND divisi='$pdivisi' ";
        if (!empty($filtercoa)) $query .=" AND IFNULL(coa_edit,'') IN $filtercoa ";
        $query = "create TEMPORARY table $tmp01 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
        $query = "CREATE INDEX `norm1` ON $tmp01 (kodeinput,idinput,divisi,tgltrans,coa)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; } 
        
    }else{
        
        $pberhasilquery=false;
        include "module/act_prosesbiayamkt/query_proses.php";
        if ($pberhasilquery==false) goto hapusdata;
        
        $query = "DELETE FROM $tmp01 WHERE IFNULL(hapus_nodiv_kosong,'') ='Y'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    
    
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
    
    
    // sales dbmaster.mr_sales2 YEAR(tgljual)='$periode'
    // sales dbmaster.sales YEAR(bulan)='$periode'
    //$query = "select date_format(tgljual,'%Y-%m') bulan, divprodid, sum(qty*hna) as rpsales from dbmaster.mr_sales2 WHERE YEAR(tgljual)='$periode' GROUP BY 1,2";
    $query = "select date_format(bulan,'%Y-%m') bulan, divprodid, sum(value_sales) as rpsales from dbmaster.sales WHERE YEAR(bulan)='$periode' GROUP BY 1,2";
    $query = "create TEMPORARY table $tmp04 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "CREATE INDEX `norm1` ON $tmp04 (bulan, divprodid)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select date_format(tgljual,'%Y-%m') bulan, 'OTC' as divprodid, sum(`value`) as rpsales from MKT.otc_etl WHERE YEAR(tgljual)='$periode' AND divprodid <>'OTHER' and icabangid <> 22 GROUP BY 1,2";
    $query = "create TEMPORARY table $tmp05 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "INSERT INTO $tmp04 (bulan, divprodid, rpsales) SELECT bulan, divprodid, rpsales FROM $tmp05";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
    
    
    
    $query = "select DISTINCT a.divisi DIVISI, b.COA1, c.NAMA1, a.coa2 COA2, a.nama_coa2 NAMA2, "
            . " a.coa3 COA3, a.nama_coa3 NAMA3, coa COA4, nama_coa NAMA4 "
            . " from $tmp02 a LEFT JOIN dbmaster.coa_level2 b on "
            . " a.coa2=b.COA2 LEFT JOIN dbmaster.coa_level1 c on "
            . " b.COA1=c.COA1";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $addcolumn="";
    for ($x=1;$x<=12;$x++) {
        $addcolumn .= " ADD B$x DECIMAL(20,2),ADD S$x DECIMAL(20,2),";
    }
    $addcolumn .= " ADD TOTAL DECIMAL(20,2), ADD STOTAL DECIMAL(20,2)";
    
    $query = "ALTER TABLE $tmp03 $addcolumn";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $urut=2;
    for ($x=1;$x<=12;$x++) {
        $jml=  strlen($x);
        $awal=$urut-$jml;
        $nbulan=$periode."-".str_repeat("0", $awal).$x;
        $nfield="B".$x;
        
        $query = "UPDATE $tmp03 a SET a.$nfield=(SELECT SUM(b.kredit) FROM $tmp02 b WHERE a.DIVISI=b.divisi AND a.COA4=b.coa AND DATE_FORMAT(b.tgltarikan, '%Y-%m')='$nbulan')";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $nfield="S".$x;
        $query = "UPDATE $tmp03 a SET a.$nfield=(SELECT SUM(b.rpsales) FROM $tmp04 b WHERE a.DIVISI=b.divprodid AND b.bulan='$nbulan')";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
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
    
    
    
//goto hapusdata;    
goto kesiniaja;



    
    $query = "select distinct b.idinput, b.divisi, b.nodivisi, a.kodeinput, a.bridinput, b.pilih, b.kodeid, b.subkode from dbmaster.t_suratdana_br1 a "
            . " JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput WHERE "
            . " IFNULL(b.stsnonaktif,'')<>'Y' AND IFNULL(b.nodivisi,'')<>''";
    $query = "create TEMPORARY table $tmp00 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        $query = "CREATE INDEX `norm1` ON $tmp00 (idinput,divisi,nodivisi,kodeinput,bridinput, kodeid, subkode)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    $query = "CREATE TEMPORARY TABLE $tmp01 (noidauto BIGINT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, 
            tgl date, tgltrans date, divprodid VARCHAR(10), COA4 VARCHAR(50), jumlah DECIMAL(20,2), jumlah1 DECIMAL(20,2), pcm VARCHAR(1), kasbonsby VARCHAR(1))";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    
    // sales dbmaster.mr_sales2 YEAR(tgljual)='$periode'
    // sales dbmaster.sales YEAR(bulan)='$periode'
    //$query = "select date_format(tgljual,'%Y-%m') bulan, divprodid, sum(qty*hna) as rpsales from dbmaster.mr_sales2 WHERE YEAR(tgljual)='$periode' GROUP BY 1,2";
    $query = "select date_format(bulan,'%Y-%m') bulan, divprodid, sum(value_sales) as rpsales from dbmaster.sales WHERE YEAR(bulan)='$periode' GROUP BY 1,2";
    $query = "create TEMPORARY table $tmp04 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "CREATE INDEX `norm1` ON $tmp04 (bulan, divprodid)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //BR
    if (!empty($pbreth)) {
        $query = "select brId, tgl, tgltrans, divprodid, COA4, jumlah, jumlah1, CAST('' as CHAR(50)) as nodivisi, "
                . " CAST('' as CHAR(50)) as nodivisi_p, CAST('' as CHAR(50)) as nodivisi_k, CAST('' as CHAR(1)) as pcm, "
                . " CAST('' as CHAR(1)) as kasbonsby, tgltrm, lampiran, ca "
                . " from hrd.br0 WHERE IFNULL(batal,'')<>'Y' AND "
                . " brId NOT IN (select DISTINCT IFNULL(brId,'') FROM hrd.br0_reject) AND "
                . " YEAR(tgltrans)='$periode' ";
        if (!empty($filtercoa)) $query .=" AND IFNULL(COA4,'') IN $filtercoa ";
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "ALTER TABLE $tmp02 ADD COLUMN kodeid INT(4), ADD COLUMN subkode VARCHAR(2)";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "CREATE INDEX `norm1` ON $tmp02 (brId, divprodid, COA4, kodeid, subkode)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
        
            //via SBY
            $query = "select a.bridinput brId, b.tgl, a.tgltransfersby tgltrans, b.divprodid, "
                    . " b.COA4, a.jumlah jumlah, a.jumlah jumlah1, "
                    . " b.tgltrm, b.lampiran, b.ca "
                    . " from dbmaster.t_br0_via_sby a JOIN hrd.br0 b on a.bridinput=b.brId "
                    . " WHERE IFNULL(b.batal,'')<>'Y' AND "
                    . " a.bridinput NOT IN (select DISTINCT IFNULL(brId,'') FROM hrd.br0_reject) AND "
                    . " YEAR(a.tgltransfersby)='$periode' ";
            if (!empty($filtercoa)) $query .=" AND IFNULL(b.COA4,'') IN $filtercoa ";
            $query = "create TEMPORARY table $tmp05 ($query)"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "CREATE INDEX `norm1` ON $tmp05 (brId,divprodid, COA4)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        
            
            $query = "DELETE FROM $tmp02 WHERE brId IN (select distinct IFNULL(brId,'') FROM $tmp05)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            $query = "INSERT INTO $tmp02 (brId, tgl, tgltrans, divprodid, "
                    . " COA4, jumlah, jumlah1, tgltrm, lampiran, ca) "
                    . " select brId, tgl, tgltrans, divprodid, "
                    . " COA4, jumlah, jumlah1, tgltrm, lampiran, ca "
                    . " from $tmp05 ";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            //END via SBY
        
            
        
        $query = "UPDATE $tmp02 SET jumlah=jumlah1 WHERE IFNULL(jumlah1,0)<>0";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "UPDATE $tmp02 a JOIN (select distinct kodeid, subkode, nodivisi, bridinput FROM $tmp00 WHERE kodeinput IN ('A', 'B', 'C') AND divisi<>'OTC') b on a.brId=b.bridinput "
                . " SET a.nodivisi=b.nodivisi, a.kodeid=b.kodeid, a.subkode=b.subkode"; 
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query = "UPDATE $tmp02 a JOIN (select distinct nodivisi, bridinput FROM $tmp00 WHERE pilih='N' AND kodeinput IN ('A', 'B', 'C') AND divisi<>'OTC') b on a.brId=b.bridinput "
                    . " SET a.nodivisi_p=b.nodivisi"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query = "UPDATE $tmp02 a JOIN (select distinct nodivisi, bridinput FROM $tmp00 WHERE pilih='Y' AND kodeinput IN ('A', 'B', 'C') AND divisi<>'OTC') b on a.brId=b.bridinput "
                    . " SET a.nodivisi_k=b.nodivisi"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "UPDATE $tmp02 SET pcm='Y' WHERE IFNULL(nodivisi_p,'')<>'' AND IFNULL(nodivisi_k,'')=''"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "UPDATE $tmp02 SET kasbonsby='Y' WHERE CONCAT(kodeid,subkode) IN ('680')"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp02 SET pcm='' WHERE "
                . " IFNULL(nodivisi_k,'')='' AND ( (IFNULL(tgltrm,'0000-00-00')<>'0000-00-00' AND IFNULL(tgltrm,'')<>'') OR ( IFNULL(lampiran,'')='Y' AND IFNULL(ca,'')='N' AND IFNULL(tgltrm,'0000-00-00')='0000-00-00') )"
                . " AND CONCAT(kodeid,subkode) NOT IN ('680')";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
            //UPDATE PCM JADI U.M BIAYA UANG MUKA
            $query = "UPDATE $tmp02 SET COA4='105-02' WHERE IFNULL(pcm,'')='Y'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        
        $query = "DELETE FROM $tmp02 WHERE IFNULL(nodivisi,'')='' AND YEAR(tgl)>='2020'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    
    
        $query = "INSERT INTO $tmp01 (tgl, tgltrans, divprodid, COA4, jumlah, jumlah1)"
                . "select tgl, tgltrans, divprodid, COA4, sum(jumlah) as jumlah, sum(jumlah1) as jumlah1 from $tmp02 GROUP BY 1,2,3,4";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }



        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
    }
    
    
    //KLAIM
    if (!empty($pklaim)) {
        $query = "select klaimId, tgl, tgltrans, IFNULL(DIVISI,'EAGLE') DIVISI, pengajuan as divpengajuan, COA4, jumlah, jumlah jumlah1, CAST('' as CHAR(50)) as nodivisi, "
                . " CAST('' as CHAR(50)) as nodivisi_p, CAST('' as CHAR(50)) as nodivisi_k, CAST('' as CHAR(1)) as pcm "
                . " from hrd.klaim WHERE "
                . " klaimId not in (SELECT DISTINCT ifnull(klaimId,'') from hrd.klaim_reject) AND "
                . " YEAR(tgltrans) = '$periode' ";
        if (!empty($filtercoa)) $query .=" AND IFNULL(COA4,'') IN $filtercoa ";
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "UPDATE $tmp02 a JOIN (select distinct nodivisi, bridinput FROM $tmp00 WHERE kodeinput IN ('E') AND divisi='EAGLE') b on a.klaimId=b.bridinput "
                . " SET a.nodivisi=b.nodivisi"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "DELETE FROM $tmp02 WHERE IFNULL(nodivisi,'')='' AND YEAR(tgl)>='2020'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        
            $query ="UPDATE $tmp02 SET divpengajuan='CAN' WHERE IFNULL(divpengajuan,'') NOT IN ('PIGEO', 'PEACO', 'EAGLE', 'OTC')";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query ="UPDATE $tmp02 SET DIVISI=divpengajuan WHERE IFNULL(divpengajuan,'')<>''";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query ="UPDATE $tmp02 SET COA4='701-03' WHERE IFNULL(DIVISI,'')='EAGLE'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            $query ="UPDATE $tmp02 SET COA4='702-03' WHERE IFNULL(DIVISI,'')='PIGEO'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            $query ="UPDATE $tmp02 SET COA4='703-03' WHERE IFNULL(DIVISI,'')='PEACO'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            $query ="UPDATE $tmp02 SET COA4='704-03' WHERE IFNULL(DIVISI,'')='OTC'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            $query ="UPDATE $tmp02 SET COA4='705-03' WHERE IFNULL(DIVISI,'')='CAN'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "INSERT INTO $tmp01 (tgl, tgltrans, divprodid, COA4, jumlah, jumlah1) "
                . " select tgl, tgltrans, DIVISI, COA4, SUM(jumlah) as jumlah, SUM(jumlah1) as jumlah1 FROM $tmp02 GROUP BY 1,2,3,4";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
    }
    
    
    //KAS KAS BON KASBON
        if (!empty($pkas)) {
            //kas uang muka COA
            $query_XXX = "select kasId, periode1 tgl, periode1 tgltrans, 'HO' as DIVISI, '105-02' as COA4, jumlah, jumlah jumlah1, CAST('' as CHAR(50)) as nodivisi "
                    . " FROM hrd.kas WHERE YEAR(periode1)= '$periode'";


        $query = "select a.kasId, a.periode1 tgl, a.periode1 tgltrans, e.DIVISI2 DIVISI, b.COA4, a.jumlah, a.jumlah jumlah1, CAST('' as CHAR(50)) as nodivisi "
                . " from hrd.kas as a LEFT JOIN dbmaster.posting_coa_kas b "
                . " ON a.kode=b.kodeid LEFT JOIN dbmaster.coa_level4 c "
                . " ON b.COA4=c.COA4 LEFT JOIN dbmaster.coa_level3 d "
                . " ON c.COA3=d.COA3 LEFT JOIN dbmaster.coa_level2 e ON d.COA2=e.COA2 WHERE "
                . " YEAR(a.periode1)='$periode'";
        //if (!empty($filtercoa)) $query .=" AND IFNULL(b.COA4,'') IN $filtercoa ";
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "UPDATE $tmp02 SET COA4='105-02' WHERE IFNULL(COA4,'')=''";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "UPDATE $tmp02 a JOIN (select distinct nodivisi, bridinput FROM $tmp00 WHERE kodeinput IN ('T', 'K')) b on a.kasId=b.bridinput "
                . " SET a.nodivisi=b.nodivisi"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "DELETE FROM $tmp02 WHERE IFNULL(nodivisi,'')='' AND YEAR(tgl)>='2020'";
        //mysqli_query($cnmy, $query);
        //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        $query ="UPDATE $tmp02 SET COA4='105-02' WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(tgltrans,'%Y-%m')>='2020-01'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
                $query ="DELETE FROM $tmp02 WHERE 1=1 ";
                if (!empty($filtercoa)) $query .=" AND IFNULL(COA4,'') NOT IN $filtercoa ";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        

        $query = "INSERT INTO $tmp01 (tgl, tgltrans, divprodid, COA4, jumlah, jumlah1) "
                . " select tgl, tgltrans, DIVISI, COA4, SUM(jumlah) as jumlah, SUM(jumlah1) as jumlah1 FROM $tmp02 GROUP BY 1,2,3,4";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");

        //KAS BON
        $queryxxx="select idkasbon, tgl, tgl as tgltrans, 'HO' as DIVISI, '105-02' as COA4, jumlah, jumlah as jumlah1, CAST('' as CHAR(50)) as nodivisi "
                . " FROM dbmaster.t_kasbon WHERE IFNULL(stsnonaktif,'')<>'Y' AND YEAR(tgl) = '$periode'";
        //if (!empty($filtercoa)) $query .=" AND '105-02' IN $filtercoa ";
        
        $query = "select e.DIVISI2 divisi, a.tgl, a.tgl tgltrans, a.idkasbon, a.kode, b.COA4, a.karyawanid, a.nama nama_karyawan, '' as nobukti,
                a.keterangan, a.jumlah, jumlah as jumlah1, CAST('' as CHAR(50)) as nodivisi 
                FROM dbmaster.t_kasbon a LEFT JOIN dbmaster.posting_coa_kas b on a.kode=b.kodeid
                LEFT JOIN dbmaster.coa_level4 c on b.COA4=c.COA4 LEFT JOIN dbmaster.coa_level3 d on c.COA3=d.COA3
                LEFT JOIN dbmaster.coa_level2 e on e.COA2=d.COA2
                LEFT JOIN hrd.karyawan f on a.karyawanid=f.karyawanId WHERE 
                IFNULL(stsnonaktif,'')<>'Y' AND YEAR(tgl) = '$periode' ";
        //if (!empty($filtercoa)) $query .=" AND b.COA4 IN $filtercoa ";
        
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "UPDATE $tmp02 a JOIN (select distinct nodivisi, bridinput FROM $tmp00 WHERE kodeinput IN ('T', 'K')) b on a.idkasbon=b.bridinput "
                . " SET a.nodivisi=b.nodivisi"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "DELETE FROM $tmp02 WHERE IFNULL(nodivisi,'')='' AND YEAR(tgl)>='2020'";
        //mysqli_query($cnmy, $query);
        //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        $query ="UPDATE $tmp02 SET COA4='105-02' WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(tgltrans,'%Y-%m')>='2020-01'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
                $query ="DELETE FROM $tmp02 WHERE 1=1 ";
                if (!empty($filtercoa)) $query .=" AND IFNULL(COA4,'') NOT IN $filtercoa ";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        

        $query = "INSERT INTO $tmp01 (tgl, tgltrans, divprodid, COA4, jumlah, jumlah1) "
                . " select tgl, tgltrans, DIVISI, COA4, SUM(jumlah) as jumlah, SUM(jumlah1) as jumlah1 FROM $tmp02 GROUP BY 1,2,3,4";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
    }
    
    
    
    //BR OTC
    if (!empty($pbrotc)) {
        $query = "select brOtcId, tglbr as tgl, tgltrans, 'OTC' DIVISI, COA4, jumlah, realisasi as jumlah1, CAST('' as CHAR(50)) as nodivisi, "
                . " CAST('' as CHAR(50)) as nodivisi_p, CAST('' as CHAR(50)) as nodivisi_k, CAST('' as CHAR(1)) as pcm,"
                . " CAST('' as CHAR(1)) as kasbonsby, lampiran, ca "
                . " from hrd.br_otc WHERE IFNULL(batal,'')<>'Y' AND "
                . " brOtcId NOT IN (select DISTINCT IFNULL(brOtcId,'') FROM hrd.br_otc_reject) AND "
                . " YEAR(tgltrans) ='$periode'";
        if (!empty($filtercoa)) $query .=" AND IFNULL(COA4,'') IN $filtercoa ";
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        
        $query = "ALTER TABLE $tmp02 ADD COLUMN kodeid INT(4), ADD COLUMN subkode VARCHAR(2)";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "CREATE INDEX `norm1` ON $tmp02 (brOtcId, COA4, kodeid, subkode)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp02 SET jumlah=jumlah1 WHERE IFNULL(jumlah1,0)<>0";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp02 a JOIN (select distinct nodivisi, bridinput, kodeid, subkode FROM $tmp00 WHERE kodeinput IN ('D') AND divisi='OTC') b on a.brOtcId=b.bridinput "
                . " SET a.nodivisi=b.nodivisi, a.kodeid=b.kodeid, a.subkode=b.subkode"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        
            $query = "UPDATE $tmp02 a JOIN (select distinct nodivisi, bridinput FROM $tmp00 WHERE pilih='N' AND kodeinput IN ('D') AND divisi='OTC') b on a.brOtcId=b.bridinput "
                    . " SET a.nodivisi_p=b.nodivisi"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query = "UPDATE $tmp02 a JOIN (select distinct nodivisi, bridinput FROM $tmp00 WHERE pilih='Y' AND kodeinput IN ('D') AND divisi='OTC') b on a.brOtcId=b.bridinput "
                    . " SET a.nodivisi_k=b.nodivisi"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "UPDATE $tmp02 SET pcm='Y' WHERE IFNULL(nodivisi_p,'')<>'' AND IFNULL(nodivisi_k,'')=''"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "UPDATE $tmp02 SET kasbonsby='Y' WHERE CONCAT(kodeid,subkode) IN ('680')"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp02 SET pcm='' WHERE "
                . " IFNULL(nodivisi_k,'')='' AND IFNULL(lampiran,'')='Y' AND IFNULL(ca,'')='N'"
                . " AND CONCAT(kodeid,subkode) NOT IN ('680')";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
            //UPDATE PCM JADI U.M BIAYA UANG MUKA
            $query = "UPDATE $tmp02 SET COA4='105-02' WHERE IFNULL(pcm,'')='Y'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
        $query = "DELETE FROM $tmp02 WHERE IFNULL(nodivisi,'')='' AND YEAR(tgl)>='2020'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }



        $query = "INSERT INTO $tmp01 (tgl, tgltrans, divprodid, COA4, jumlah, jumlah1) "
                . " select tgl, tgltrans, DIVISI, COA4, SUM(jumlah) as jumlah, SUM(jumlah1) as jumlah1 FROM $tmp02 GROUP BY 1,2,3,4";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
    }
    
    
    //BM biaya marketing surabaya
    if (!empty($pbmsby)) {
        $query = "SELECT ID, TANGGAL, NOBBM, NOBBK, DIVISI, COA4, COA4_K, DEBIT, KREDIT, SALDO, KETERANGAN, CAST('' as CHAR(50)) as NOBUKTI FROM dbmaster.t_bm_sby WHERE "
                . " IFNULL(STSNONAKTIF,'')<>'Y' AND YEAR(TANGGAL) = '$periode' ";
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
        
        
        

        //debit 
        $query = "INSERT INTO $tmp01 (tgl, tgltrans, divprodid, COA4, jumlah, jumlah1)"
                . "SELECT TANGGAL, TANGGAL, DIVISI, COA4, DEBIT, DEBIT FROM $tmp02 WHERE 1=1 ";
        if (!empty($filtercoa)) $query .=" AND IFNULL(COA4,'') IN $filtercoa ";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        //kredit
        $query = "INSERT INTO $tmp01 (tgl, tgltrans, divprodid, COA4, jumlah, jumlah1)"
                . "SELECT TANGGAL, TANGGAL, DIVISI, COA4_K, KREDIT, KREDIT FROM $tmp02 WHERE 1=1 ";
        if (!empty($filtercoa)) $query .=" AND IFNULL(COA4_K,'') IN $filtercoa ";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
    }
    
    //BANK
    if (!empty($ppilbank)) {
        $query = "select nourut, bulan, tanggal, nobukti, divisi, nodivisi, coa4, keterangan, debit, kredit "
                . " from dbmaster.t_bank_saldo_d "
                . " where IFNULL(idinputbank,'')<>'SAWAL' AND "
                . " YEAR(bulan) = '$periode' ";
        if (!empty($pdivisi)) $query .=" AND divisi='$pdivisi' ";
        if (!empty($filtercoa)) $query .=" AND IFNULL(coa4,'') IN $filtercoa ";
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp02 (nourut, bulan, tanggal, nobukti, divisi)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        $query = "UPDATE $tmp02 SET nodivisi='' where IFNULL(nodivisi,'')='0'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp02 SET divisi='CAN' WHERE keterangan LIKE '%outstanding%' AND IFNULL(divisi,'')=''";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        //debit
        $query = "INSERT INTO $tmp01 (tgl, tgltrans, divprodid, COA4, jumlah, jumlah1)"
                . "SELECT tanggal, tanggal, divisi, coa4, debit, debit FROM $tmp02 WHERE IFNULL(debit,0)<>0 ";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        //kredit
        $query = "INSERT INTO $tmp01 (tgl, tgltrans, divprodid, COA4, jumlah, jumlah1)"
                . "SELECT tanggal, tanggal, divisi, coa4, kredit, kredit FROM $tmp02 WHERE IFNULL(kredit,0)<>0 ";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
        
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
        
        $query = "SELECT CAST(null as DECIMAL(10,0)) as urutan, a.bulan, a.divisi, a.cabang icabangid, b.nama cabang, "
                . " a.jabatan, a.karyawanid, a.nama, a.region, a.jumlah, CAST('' as CHAR(50)) as nodivisi "
                . " FROM dbmaster.incentiveperdivisi a "
                . " LEFT JOIN mkt.icabang b on a.cabang=b.iCabangId WHERE IFNULL(a.jumlah,0)<>0 AND "
                . " YEAR(a.bulan) = '$periode' ";
        
        $query = "SELECT CAST(null as DECIMAL(10,0)) as urutan, a.bulan, a.divisi, a.cabang icabangid, "
                . " a.jabatan, a.karyawanid, a.nama, a.region, a.jumlah, CAST('' as CHAR(50)) as nodivisi "
                . " FROM dbmaster.incentiveperdivisi a "
                . " WHERE IFNULL(a.jumlah,0)<>0 AND "
                . " YEAR(a.bulan) = '$periode' ";
        
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
                . " and IFNULL(stsnonaktif,'')<>'' AND YEAR(tglf) BETWEEN '$periode' AND nodivisi like '%INC%'";
        $query = "create TEMPORARY table $tmp03 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp03 (idinput, nodivisi, nomor, bulan)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        $query = "UPDATE $tmp02 a JOIN $tmp03 b on DATE_FORMAT(a.bulan,'%Y-%m')=DATE_FORMAT(b.bulan,'%Y-%m') "
                . " SET a.nodivisi=b.nodivisi"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        


        $query = "INSERT INTO $tmp01 (tgl, tgltrans, divprodid, COA4, jumlah, jumlah1) "
                . " select bulan, bulan as tgltrans, divisi, coa, SUM(jumlah) as jumlah, SUM(jumlah) as jumlah1 FROM $tmp02 GROUP BY 1,2,3,4";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            
            
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
        
    }
    
    
    //RUTIN / LUAR KOTA LK
    if (!empty($prutin) OR !empty($pblk)) {
        
        
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
        
        
        
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
                . " YEAR(b.bulan) = '$periode' ";
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
        
        
        
        $query = "select *, CAST('' as CHAR(50)) as nodivisi from $tmp02";
        $query = "create TEMPORARY table $tmp03 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp03 a JOIN (select distinct nodivisi, bridinput FROM $tmp00 WHERE kodeinput IN ('F', 'I')) b on a.idrutin=b.bridinput "
                . " SET a.nodivisi=b.nodivisi"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query ="DELETE FROM $tmp03 WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(bulan,'%Y-%m')>='2020-01'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
        $query = "INSERT INTO $tmp01 (tgl, tgltrans, divprodid, COA4, jumlah, jumlah1)"
                . "SELECT periode, periode, divisi, coa, sum(rptotal) jumlah, sum(rptotal) jumlah1 FROM $tmp03 GROUP BY 1,2,3,4";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
        
    }
    
    /*
    if (!empty($prutin) OR !empty($pblk)) {
        
        //RUTIN
        $query = "select bulan, idrutin, divisi, divi, kode "
                . " from dbmaster.t_brrutin0 WHERE "
                . " IFNULL(stsnonaktif,'') <> 'Y' AND "
                . " YEAR(bulan)='$periode'";
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "select distinct idrutin, divisi from dbmaster.t_brrutin_ca_close WHERE idrutin IN (select distinct IFNULL(idrutin,'') from $tmp02)";
        $query = "create TEMPORARY table $tmp03 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "UPDATE $tmp02 a JOIN (select distinct idrutin, divisi from $tmp03) b on a.idrutin=b.idrutin SET a.divisi=b.divisi WHERE a.divisi<>'OTC' and a.kode=2";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");

        //, CAST(NULL as date) bulan, CAST('' as CHAR(50)) as divisi
        $query = "select idrutin, coa, nobrid, rptotal  "
                . " FROM dbmaster.t_brrutin1 WHERE "
                . " idrutin in (select distinct IFNULL(idrutin,'') FROM $tmp02)";
        $query = "create TEMPORARY table $tmp05 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "UPDATE $tmp05 a JOIN $tmp02 b on a.idrutin=b.idrutin SET a.bulan=b.bulan, a.divisi=b.divisi";
        //mysqli_query($cnmy, $query);
        //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        //echo "ada"; goto hapusdata;

        $query_XXX = "select nobrid, idrutin, bulan tgl, bulan tgltrans, divisi DIVISI, coa COA4, rptotal as jumlah, rptotal as jumlah1, CAST('' as CHAR(50)) as nodivisi FROM $tmp05";
        $query = "select a.nobrid, a.idrutin, b.bulan tgl, b.bulan tgltrans, b.divisi DIVISI, a.coa COA4, a.rptotal as jumlah, a.rptotal as jumlah1, CAST('' as CHAR(50)) as nodivisi "
                . " from $tmp05 a JOIN $tmp02 b on a.idrutin=b.idrutin";
        $query = "create TEMPORARY table $tmp03 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "select * from dbmaster.posting_coa_rutin";
        $query = "create TEMPORARY table $tmp06 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "UPDATE $tmp03 a JOIN $tmp06 b on a.DIVISI=b.divisi AND a.nobrid=b.nobrid SET a.COA4=b.COA4 WHERE IFNULL(a.DIVISI,'')<>''";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        $query = "UPDATE $tmp03 a JOIN (select distinct nodivisi, bridinput FROM $tmp00 WHERE kodeinput IN ('F', 'I')) b on a.idrutin=b.bridinput "
                . " SET a.nodivisi=b.nodivisi"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "DELETE FROM $tmp03 WHERE IFNULL(nodivisi,'')='' AND YEAR(tgl)>='2020'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }



        $query = "INSERT INTO $tmp01 (tgl, tgltrans, divprodid, COA4, jumlah, jumlah1) "
                . " select tgl, tgltrans, DIVISI, COA4, SUM(jumlah) as jumlah, SUM(jumlah1) as jumlah1 FROM $tmp03 GROUP BY 1,2,3,4";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    }
    */
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
    
    
    $query = "select DISTINCT d.DIVISI2, d.COA1, e.NAMA1, c.COA2, d.NAMA2, b.COA3, c.NAMA3, b.COA4, b.NAMA4
       from dbmaster.coa_level4 b 
       LEFT JOIN dbmaster.coa_level3 c ON c.COA3=b.COA3
       LEFT JOIN dbmaster.coa_level2 d ON c.COA2=d.COA2
       LEFT JOIN dbmaster.coa_level1 e ON e.COA1=d.COA1";
    $query = "create TEMPORARY table $tmp08 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
            $query = "CREATE INDEX `norm1` ON $tmp08 (DIVISI2, COA1, COA2, COA3, COA4)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
            
            
    $query = "select a.*, b.NAMA4, d.COA1, e.NAMA1, c.COA2, d.NAMA2, b.COA3, c.NAMA3, d.DIVISI2
       from $tmp01 a 
       LEFT JOIN dbmaster.coa_level4 b ON a.COA4=b.COA4
       LEFT JOIN dbmaster.coa_level3 c ON c.COA3=b.COA3
       LEFT JOIN dbmaster.coa_level2 d ON c.COA2=d.COA2
       LEFT JOIN dbmaster.coa_level1 e ON e.COA1=d.COA1";
    
    $query = "select a.*, b.NAMA4, b.COA1, b.NAMA1, b.COA2, b.NAMA2, b.COA3, b.NAMA3, b.DIVISI2 from $tmp01 a LEFT JOIN $tmp08 b ON a.COA4=b.COA4";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    
    $query = "UPDATE $tmp01 SET divprodid='AA' WHERE IFNULL(divprodid,'')=''";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 SET divprodid='AA' WHERE IFNULL(divprodid,'')=''";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    
    $query = "select DISTINCT divprodid DIVISI, COA1, NAMA1, COA2, NAMA2, COA3, NAMA3, COA4, NAMA4 from $tmp02";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    $addcolumn="";
    for ($x=1;$x<=12;$x++) {
        $addcolumn .= " ADD B$x DECIMAL(20,2),ADD S$x DECIMAL(20,2),";
    }
    $addcolumn .= " ADD TOTAL DECIMAL(20,2), ADD STOTAL DECIMAL(20,2)";
    
    $query = "ALTER TABLE $tmp03 $addcolumn";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $urut=2;
    for ($x=1;$x<=12;$x++) {
        $jml=  strlen($x);
        $awal=$urut-$jml;
        $nbulan=$periode."-".str_repeat("0", $awal).$x;
        $nfield="B".$x;
        
        $query = "UPDATE $tmp03 a SET a.$nfield=(SELECT SUM(b.jumlah) FROM $tmp01 b WHERE a.DIVISI=b.divprodid AND a.COA4=b.COA4 AND DATE_FORMAT(tgltrans, '%Y-%m')='$nbulan')";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $nfield="S".$x;
        $query = "UPDATE $tmp03 a SET a.$nfield=(SELECT SUM(b.rpsales) FROM $tmp04 b WHERE a.DIVISI=b.divprodid AND b.bulan='$nbulan')";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }
    
    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp00");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp07");
    
        
        //MERUBAH DIVISI JADI KOSONG UNTUK COA4 YANG TIDAK ADA DIVISINTA
        
        $query = "UPDATE $tmp02 SET divprodid='AA' WHERE COA4 IN (select IFNULL(COA4,'') FROM $tmp08 WHERE IFNULL(DIVISI2,'') IN ('', 'OTHER'))";
        //mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp03 SET DIVISI='AA' WHERE COA4 IN (select IFNULL(COA4,'') FROM $tmp08 WHERE IFNULL(DIVISI2,'') IN ('', 'OTHER'))";
        //mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        //END MERUBAH DIVISI JADI KOSONG UNTUK COA4 YANG TIDAK ADA DIVISINTA
        
    
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

    
kesiniaja:
?>

<HTML>
<HEAD>
    <title>REPORT REALISASI BIAYA MARKETING</title>
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
    
    <center><div class='h1judul'>REPORT REALISASI BIAYA MARKETING</div></center>
    
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

                    <th align="center" nowrap>1</th>
                    <th align="center" nowrap>JANUARI</th>
                    <th align="center" nowrap>2</th>
                    <th align="center" nowrap>FEBRUARI</th>
                    <th align="center" nowrap>3</th>
                    <th align="center" nowrap>MARET</th>
                    <th align="center" nowrap>4</th>
                    <th align="center" nowrap>APRIL</th>
                    <th align="center" nowrap>5</th>
                    <th align="center" nowrap>MEI</th>
                    <th align="center" nowrap>6</th>
                    <th align="center" nowrap>JUNI</th>
                    <th align="center" nowrap>7</th>
                    <th align="center" nowrap>JULI</th>
                    <th align="center" nowrap>8</th>
                    <th align="center" nowrap>AGUSTUS</th>
                    <th align="center" nowrap>9</th>
                    <th align="center" nowrap>SEPTEMBER</th>
                    <th align="center" nowrap>10</th>
                    <th align="center" nowrap>OKTOBER</th>
                    <th align="center" nowrap>11</th>
                    <th align="center" nowrap>NOVEMBER</th>
                    <th align="center" nowrap>12</th>
                    <th align="center" nowrap>DESEMBER</th>
                    <th align="center" nowrap>%</th>
                    <th align="center" nowrap>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                for ($x=1;$x<=12;$x++) {
                    $pgrandtotal[$x]=0;
                    $pgrandtotalsls[$x]=0;
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
                    if ($mdivisi=="AA") $mdivisi="NONE";
                    
                    for ($x=1;$x<=12;$x++) {
                        $ptotdivisi[$x]=0;
                        $ptotdivisisls[$x]=0;
                    }
                    
                    $query = "select distinct IFNULL(DIVISI,'') DIVISI, IFNULL(COA2,'') COA2, IFNULL(NAMA2,'') NAMA2 from $tmp03 WHERE DIVISI='$divisi' ORDER BY IFNULL(DIVISI,''), IFNULL(COA2,''), IFNULL(NAMA2,'')";
                    $tampil=mysqli_query($cnmy, $query);
                    while ($row= mysqli_fetch_array($tampil)) {
                        $pdivisi=$row['DIVISI'];
                        $pcoa2=$row['COA2'];
                        $pnmcoa2=$row['NAMA2'];

                        echo "<tr>";
                        echo "<td nowrap><b>$pcoa2</b></td>";
                        echo "<td nowrap colspan=27><b>$pnmcoa2</b></td>";
                        echo "</tr>";

                        for ($x=1;$x<=12;$x++) {
                            $psubtot[$x]=0;
                        }

                        $query = "select * from $tmp03 WHERE IFNULL(DIVISI,'')='$divisi' AND IFNULL(COA2,'')='$pcoa2' ORDER BY IFNULL(COA4,''), IFNULL(NAMA4,'')";
                        $tampil2=mysqli_query($cnmy, $query);
                        while ($row2= mysqli_fetch_array($tampil2)) {
                            $pcoa4=$row2['COA4'];
                            $pnmcoa4=$row2['NAMA4'];

                            $pers1="";
                            $pb1=$row2['B1'];

                            echo "<tr>";
                            echo "<td nowrap>$pcoa4</td>";
                            echo "<td nowrap>$pnmcoa4</td>";


                            //hitung dulu sales per jajar
                            $totsalestahunan=0;
                            for ($x=1;$x<=12;$x++) {
                                $snmcol="S".$x;
                                $pjml=$row2[$snmcol];
                                if (empty($pjml)) $pjml=0;
                                $totsalestahunan=(double)$totsalestahunan+(double)$pjml;
                            }
                            //END hitung dulu sales per jajar
                    
                            $ptotaltahund=0;
                            for ($x=1;$x<=12;$x++) {
                                $nmcol="B".$x;
                                $pjml=$row2[$nmcol];
                                if (empty($pjml)) $pjml=0;

                                $ptotaltahund=(double)$ptotaltahund+(double)$pjml;
                                $psubtot[$x]=(double)$psubtot[$x]+(double)$pjml;

                                //sales
                                $snmcol="S".$x;
                                $pjmlsls=$row2[$snmcol];
                                if (empty($pjmlsls)) $pjmlsls=0;
                                $ptotdivisisls[$x]=$pjmlsls;
                                
                                if ((double)$pjmlsls==0) {
                                    $npersen=0;
                                }else{
                                    $npersen=ROUND((double)$pjml/(double)$pjmlsls*100,2);
                                }
                                if ((double)$npersen==0) $npersen="";
                                    
                                $pjml=number_format($pjml,0,",",",");

                                echo "<td align='right' nowrap>$npersen</td>";
                                echo "<td align='right' nowrap>".$pjml."</td>";
                                
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
                        for ($x=1;$x<=12;$x++) {

                            $pjml=$psubtot[$x];
                            if (empty($pjml)) $pjml=0;
                            
                            $ptotaltahund=(double)$ptotaltahund+(double)$pjml;
                            $ptotdivisi[$x]=(double)$ptotdivisi[$x]+(double)$pjml;
                            
                            $pjmlsls=$ptotdivisisls[$x];
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
                        echo "<td nowrap colspan=28><b></b></td>";
                        echo "</tr>";

                    }
                    
                    //total per divisi
                    echo "<tr>";
                    echo "<td nowrap><b></b></td>";
                    echo "<td nowrap><b>BIAYA $mdivisi</b></td>";

                    $ztotbr=0;
                    $ztotsls=0;


                    $urut=2;
                    for ($x=1;$x<=12;$x++) {
                        $ztotalbr[$x]=0;
                        $ztotalsls[$x]=0;

                        $jml=  strlen($x);
                        $awal=$urut-$jml;
                        $zbulan=$periode."-".str_repeat("0", $awal).$x;


                        //cari total br
                        $query = "select IFNULL(sum(jumlah),0) jumlah From $tmp02 WHERE divisi='$divisi' AND DATE_FORMAT(tgltarikan,'%Y-%m')='$zbulan'";
                        $rowslb=mysqli_query($cnmy, $query);
                        $ketemubr= mysqli_num_rows($rowslb);
                        if ($ketemubr>0) {
                            $rslb= mysqli_fetch_array($rowslb);
                            $ztotalbr[$x]=$rslb['jumlah'];
                            $ztotbr=(double)$ztotbr+(double)$ztotalbr[$x];
                        }

                        //cari total sales
                        $query = "select IFNULL(sum(rpsales),0) rpsales From $tmp04 WHERE divprodid='$divisi' AND bulan='$zbulan'";
                        $rowsls=mysqli_query($cnmy, $query);
                        $ketemusls= mysqli_num_rows($rowsls);
                        if ($ketemusls>0) {
                            $rsls= mysqli_fetch_array($rowsls);
                            $ztotalsls[$x]=$rsls['rpsales'];
                            $ztotsls=(double)$ztotsls+(double)$ztotalsls[$x];
                        }


                        if ((double)$ztotalsls[$x]==0) {
                            $zpersen=0;
                        }else{
                            $zpersen=ROUND((double)$ztotalbr[$x]/(double)$ztotalsls[$x]*100,2);
                        }

                        $ztotalbr[$x]=number_format($ztotalbr[$x],0,",",",");
                        echo "<td align='right' nowrap><b>$zpersen</b></td>";
                        echo "<td align='right' nowrap><b>".$ztotalbr[$x]."</b></td>";

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
                    
                    if ($mdivisi!="HO" AND $mdivisi!="CANARY" AND $mdivisi!="CAN" AND $mdivisi!="OTHER" AND $mdivisi!="OTHERS") {
                    
                        echo "<tr>";
                        echo "<td nowrap><b></b></td>";
                        echo "<td nowrap><b>PENJUALAN S2 $mdivisi</b></td>";

                            for ($x=1;$x<=12;$x++) {

                                if ((double)$ztotsls==0) {
                                    $zpersen=0;
                                }else{
                                    $zpersen=ROUND((double)$ztotalsls[$x]/(double)$ztotsls*100,2);
                                }

                                $ztotalsls[$x]=number_format($ztotalsls[$x],0,",",",");
                                echo "<td align='right' nowrap><b>$zpersen</b></td>";
                                echo "<td align='right' nowrap><b>".$ztotalsls[$x]."</b></td>";
                            }

                        $ztotsls=number_format($ztotsls,0,",",",");
                        echo "<td align='right' nowrap><b>100</b></td>";
                        echo "<td align='right' nowrap><b>$ztotsls</b></td>";

                        echo "</tr>";
                        
                    }
                    
                                
                    echo "<tr>";
                    echo "<td nowrap colspan=28><b></b></td>";
                    echo "</tr>";
                    
                    
                    
                    
                }
                
                // grand total
                echo "<tr>";
                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b>TOTAL BIAYA MARKETING</b></td>";

                $ztotbr=0;
                $ztotsls=0;


                $urut=2;
                for ($x=1;$x<=12;$x++) {
                    $ztotalbr[$x]=0;
                    $ztotalsls[$x]=0;

                    $jml=  strlen($x);
                    $awal=$urut-$jml;
                    $zbulan=$periode."-".str_repeat("0", $awal).$x;


                    //cari total br
                    $query = "select IFNULL(sum(jumlah),0) jumlah From $tmp02 WHERE DATE_FORMAT(tgltarikan,'%Y-%m')='$zbulan'";
                    $rowslb=mysqli_query($cnmy, $query);
                    $ketemubr= mysqli_num_rows($rowslb);
                    if ($ketemubr>0) {
                        $rslb= mysqli_fetch_array($rowslb);
                        $ztotalbr[$x]=$rslb['jumlah'];
                        $ztotbr=(double)$ztotbr+(double)$ztotalbr[$x];
                    }

                    //cari total sales
                    $query = "select IFNULL(sum(rpsales),0) rpsales From $tmp04 WHERE bulan='$zbulan'";
                    $rowsls=mysqli_query($cnmy, $query);
                    $ketemusls= mysqli_num_rows($rowsls);
                    if ($ketemusls>0) {
                        $rsls= mysqli_fetch_array($rowsls);
                        $ztotalsls[$x]=$rsls['rpsales'];
                        $ztotsls=(double)$ztotsls+(double)$ztotalsls[$x];
                    }


                    if ((double)$ztotalsls[$x]==0) {
                        $zpersen=0;
                    }else{
                        $zpersen=ROUND((double)$ztotalbr[$x]/(double)$ztotalsls[$x]*100,2);
                    }

                    $ztotalbr[$x]=number_format($ztotalbr[$x],0,",",",");
                    echo "<td align='right' nowrap><b>$zpersen</b></td>";
                    echo "<td align='right' nowrap><b>".$ztotalbr[$x]."</b></td>";

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

                    for ($x=1;$x<=12;$x++) {

                        if ((double)$ztotsls==0) {
                            $zpersen=0;
                        }else{
                            $zpersen=ROUND((double)$ztotalsls[$x]/(double)$ztotsls*100,2);
                        }

                        $ztotalsls[$x]=number_format($ztotalsls[$x],0,",",",");
                        echo "<td align='right' nowrap><b>$zpersen</b></td>";
                        echo "<td align='right' nowrap><b>".$ztotalsls[$x]."</b></td>";
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

                    <th align="center" nowrap>1</th>
                    <th align="center" nowrap>JANUARI</th>
                    <th align="center" nowrap>2</th>
                    <th align="center" nowrap>FEBRUARI</th>
                    <th align="center" nowrap>3</th>
                    <th align="center" nowrap>MARET</th>
                    <th align="center" nowrap>4</th>
                    <th align="center" nowrap>APRIL</th>
                    <th align="center" nowrap>5</th>
                    <th align="center" nowrap>MEI</th>
                    <th align="center" nowrap>6</th>
                    <th align="center" nowrap>JUNI</th>
                    <th align="center" nowrap>7</th>
                    <th align="center" nowrap>JULI</th>
                    <th align="center" nowrap>8</th>
                    <th align="center" nowrap>AGUSTUS</th>
                    <th align="center" nowrap>9</th>
                    <th align="center" nowrap>SEPTEMBER</th>
                    <th align="center" nowrap>10</th>
                    <th align="center" nowrap>OKTOBER</th>
                    <th align="center" nowrap>11</th>
                    <th align="center" nowrap>NOVEMBER</th>
                    <th align="center" nowrap>12</th>
                    <th align="center" nowrap>DESEMBER</th>
                    <th align="center" nowrap>%</th>
                    <th align="center" nowrap>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                for ($x=1;$x<=12;$x++) {
                    $pgrandtotal[$x]=0;
                    $pgrandtotalsls[$x]=0;
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
                    if ($mdivisi=="AA") $mdivisi="NONE";
                    
                    for ($x=1;$x<=12;$x++) {
                        $ptotdivisi[$x]=0;
                        $ptotdivisisls[$x]=0;
                    }
                    
                    $query = "select distinct IFNULL(DIVISI,'') DIVISI, IFNULL(COA2,'') COA2, IFNULL(NAMA2,'') NAMA2 from $tmp06 WHERE DIVISI='$divisi' ORDER BY IFNULL(DIVISI,''), IFNULL(COA2,''), IFNULL(NAMA2,'')";
                    $tampil=mysqli_query($cnmy, $query);
                    while ($row= mysqli_fetch_array($tampil)) {
                        $pdivisi=$row['DIVISI'];
                        $pcoa2=$row['COA2'];
                        $pnmcoa2=$row['NAMA2'];

                        echo "<tr>";
                        echo "<td nowrap><b>$pcoa2</b></td>";
                        echo "<td nowrap colspan=27><b>$pnmcoa2</b></td>";
                        echo "</tr>";

                        for ($x=1;$x<=12;$x++) {
                            $psubtot[$x]=0;
                        }

                        $query = "select * from $tmp06 WHERE IFNULL(DIVISI,'')='$divisi' AND IFNULL(COA2,'')='$pcoa2' ORDER BY IFNULL(COA4,''), IFNULL(NAMA4,'')";
                        $tampil2=mysqli_query($cnmy, $query);
                        while ($row2= mysqli_fetch_array($tampil2)) {
                            $pcoa4=$row2['COA4'];
                            $pnmcoa4=$row2['NAMA4'];

                            $pers1="";
                            $pb1=$row2['B1'];

                            echo "<tr>";
                            echo "<td nowrap>$pcoa4</td>";
                            echo "<td nowrap>$pnmcoa4</td>";


                            //hitung dulu sales per jajar
                            $totsalestahunan=0;
                            for ($x=1;$x<=12;$x++) {
                                $snmcol="S".$x;
                                $pjml=$row2[$snmcol];
                                if (empty($pjml)) $pjml=0;
                                $totsalestahunan=(double)$totsalestahunan+(double)$pjml;
                            }
                            //END hitung dulu sales per jajar
                    
                            $ptotaltahund=0;
                            for ($x=1;$x<=12;$x++) {
                                $nmcol="B".$x;
                                $pjml=$row2[$nmcol];
                                if (empty($pjml)) $pjml=0;

                                $ptotaltahund=(double)$ptotaltahund+(double)$pjml;
                                $psubtot[$x]=(double)$psubtot[$x]+(double)$pjml;

                                //sales
                                $snmcol="S".$x;
                                $pjmlsls=$row2[$snmcol];
                                if (empty($pjmlsls)) $pjmlsls=0;
                                $ptotdivisisls[$x]=$pjmlsls;
                                
                                if ((double)$pjmlsls==0) {
                                    $npersen=0;
                                }else{
                                    $npersen=ROUND((double)$pjml/(double)$pjmlsls*100,2);
                                }
                                if ((double)$npersen==0) $npersen="";
                                    
                                $pjml=number_format($pjml,0,",",",");

                                echo "<td align='right' nowrap>$npersen</td>";
                                echo "<td align='right' nowrap>".$pjml."</td>";
                                
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
                        for ($x=1;$x<=12;$x++) {

                            $pjml=$psubtot[$x];
                            if (empty($pjml)) $pjml=0;
                            
                            $ptotaltahund=(double)$ptotaltahund+(double)$pjml;
                            $ptotdivisi[$x]=(double)$ptotdivisi[$x]+(double)$pjml;
                            
                            $pjmlsls=$ptotdivisisls[$x];
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
                        echo "<td nowrap colspan=28><b></b></td>";
                        echo "</tr>";

                    }
                    
                    //total per divisi
                    echo "<tr>";
                    echo "<td nowrap><b></b></td>";
                    echo "<td nowrap><b>BIAYA $mdivisi</b></td>";

                    $ztotbr=0;
                    $ztotsls=0;


                    $urut=2;
                    for ($x=1;$x<=12;$x++) {
                        $ztotalbr[$x]=0;
                        $ztotalsls[$x]=0;

                        $jml=  strlen($x);
                        $awal=$urut-$jml;
                        $zbulan=$periode."-".str_repeat("0", $awal).$x;


                        //cari total br
                        $query = "select IFNULL(sum(jumlah),0) jumlah From $tmp05 WHERE divisi='$divisi' AND DATE_FORMAT(tgltarikan,'%Y-%m')='$zbulan'";
                        $rowslb=mysqli_query($cnmy, $query);
                        $ketemubr= mysqli_num_rows($rowslb);
                        if ($ketemubr>0) {
                            $rslb= mysqli_fetch_array($rowslb);
                            $ztotalbr[$x]=$rslb['jumlah'];
                            $ztotbr=(double)$ztotbr+(double)$ztotalbr[$x];
                        }

                        
                        
                        //cari total sales
                        $query = "select IFNULL(sum(rpsales),0) rpsales From $tmp07 WHERE divprodid='$divisi' AND bulan='$zbulan'";
                        $rowsls=mysqli_query($cnmy, $query);
                        $ketemusls= mysqli_num_rows($rowsls);
                        if ($ketemusls>0) {
                            $rsls= mysqli_fetch_array($rowsls);
                            $ztotalsls[$x]=$rsls['rpsales'];
                            $ztotsls=(double)$ztotsls+(double)$ztotalsls[$x];
                        }


                        if ((double)$ztotalsls[$x]==0) {
                            $zpersen=0;
                        }else{
                            $zpersen=ROUND((double)$ztotalbr[$x]/(double)$ztotalsls[$x]*100,2);
                        }

                        $ztotalbr[$x]=number_format($ztotalbr[$x],0,",",",");
                        echo "<td align='right' nowrap><b>$zpersen</b></td>";
                        echo "<td align='right' nowrap><b>".$ztotalbr[$x]."</b></td>";

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
                    
                    
                /*
                    //sales
                    echo "<tr>";
                    echo "<td nowrap><b></b></td>";
                    echo "<td nowrap><b>PENJUALAN S2 $mdivisi</b></td>";

                        for ($x=1;$x<=12;$x++) {

                            if ((double)$ztotsls==0) {
                                $zpersen=0;
                            }else{
                                $zpersen=ROUND((double)$ztotalsls[$x]/(double)$ztotsls*100,2);
                            }

                            $ztotalsls[$x]=number_format($ztotalsls[$x],0,",",",");
                            echo "<td align='right' nowrap><b>$zpersen</b></td>";
                            echo "<td align='right' nowrap><b>".$ztotalsls[$x]."</b></td>";
                        }

                    $ztotsls=number_format($ztotsls,0,",",",");
                    echo "<td align='right' nowrap><b>100</b></td>";
                    echo "<td align='right' nowrap><b>$ztotsls</b></td>";

                    echo "</tr>";
                */
                                
                    echo "<tr>";
                    echo "<td nowrap colspan=28><b></b></td>";
                    echo "</tr>";
                    
                }
                
                // grand total
                echo "<tr>";
                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b>TOTAL BIAYA MARKETING</b></td>";

                $ztotbr=0;
                $ztotsls=0;


                $urut=2;
                for ($x=1;$x<=12;$x++) {
                    $ztotalbr[$x]=0;
                    $ztotalsls[$x]=0;

                    $jml=  strlen($x);
                    $awal=$urut-$jml;
                    $zbulan=$periode."-".str_repeat("0", $awal).$x;


                    //cari total br
                    $query = "select IFNULL(sum(jumlah),0) jumlah From $tmp05 WHERE DATE_FORMAT(tgltarikan,'%Y-%m')='$zbulan'";
                    $rowslb=mysqli_query($cnmy, $query);
                    $ketemubr= mysqli_num_rows($rowslb);
                    if ($ketemubr>0) {
                        $rslb= mysqli_fetch_array($rowslb);
                        $ztotalbr[$x]=$rslb['jumlah'];
                        $ztotbr=(double)$ztotbr+(double)$ztotalbr[$x];
                    }

                    //cari total sales
                    $query = "select IFNULL(sum(rpsales),0) rpsales From $tmp07 WHERE bulan='$zbulan'";
                    $rowsls=mysqli_query($cnmy, $query);
                    $ketemusls= mysqli_num_rows($rowsls);
                    if ($ketemusls>0) {
                        $rsls= mysqli_fetch_array($rowsls);
                        $ztotalsls[$x]=$rsls['rpsales'];
                        $ztotsls=(double)$ztotsls+(double)$ztotalsls[$x];
                    }


                    if ((double)$ztotalsls[$x]==0) {
                        $zpersen=0;
                    }else{
                        $zpersen=ROUND((double)$ztotalbr[$x]/(double)$ztotalsls[$x]*100,2);
                    }

                    $ztotalbr[$x]=number_format($ztotalbr[$x],0,",",",");
                    echo "<td align='right' nowrap><b>$zpersen</b></td>";
                    echo "<td align='right' nowrap><b>".$ztotalbr[$x]."</b></td>";

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
                
                
            /*    
                //sales
                echo "<tr>";
                echo "<td nowrap><b></b></td>";
                echo "<td nowrap><b>PENJUALAN S2 MARKETING</b></td>";

                    for ($x=1;$x<=12;$x++) {

                        if ((double)$ztotsls==0) {
                            $zpersen=0;
                        }else{
                            $zpersen=ROUND((double)$ztotalsls[$x]/(double)$ztotsls*100,2);
                        }

                        $ztotalsls[$x]=number_format($ztotalsls[$x],0,",",",");
                        echo "<td align='right' nowrap><b>$zpersen</b></td>";
                        echo "<td align='right' nowrap><b>".$ztotalsls[$x]."</b></td>";
                    }

                
                $ztotsls=number_format($ztotsls,0,",",",");
                echo "<td align='right' nowrap><b>100</b></td>";
                echo "<td align='right' nowrap><b>$ztotsls</b></td>";

                echo "</tr>";
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
    mysqli_close($cnmy);
?>