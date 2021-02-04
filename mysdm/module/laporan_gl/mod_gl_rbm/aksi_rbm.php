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
    
    $bolehadasales=false;
    if ($pidgrouppil=="1" OR $pidgrouppil=="24" OR $pidgrouppil=="22" OR $pidgrouppil=="2" OR $pidgrouppil=="46") {
        $bolehadasales=true;        
    }
    
?>

<?PHP
    include "config/fungsi_combo.php";
    include("config/common.php");
    
    $ppildivisiid = $_POST['cb_divisip'];
    $periode = $_POST['bulan1'];
    
    $ptanggalprosesnya="";
    $query = "select tanggal_proses from dbmaster.t_proses_data_bm_date WHERE tahun='$periode'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ((DOUBLE)$ketemu>0) {
        $nt= mysqli_fetch_array($tampil);
        $ptanggalprosesnya=$nt['tanggal_proses'];
    }
    
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
        
        $query .=" AND IFNULL(ishare,'')<>'Y' "; //pilih salah satu divisi <> 'HO' atau IFNULL(ishare,'')<>'Y'
        //$query .=" AND IFNULL(divisi,'')<>'HO' ";  //pilih salah satu divisi <> 'HO' atau IFNULL(ishare,'')<>'Y'
        
        if (!empty($pdivisi)) $query .=" AND divisi='$pdivisi' ";
        if (!empty($filtercoa)) $query .=" AND IFNULL(coa_edit,'') IN $filtercoa ";
        
        if ($pidgrouppil=="8") {
            $ppilregion="B";
            if ($picardid=="0000000159") $ppilregion="T";
            $query .=" AND ( IFNULL(icabangid,'') IN (select distinct IFNULL(icabangid,'') FROM MKT.icabang WHERE region='$ppilregion' ) OR karyawanid='$picardid' ) ";
            $query .=" AND ( IFNULL(divisi,'') NOT IN ('OTC', 'CHC', 'HO') OR karyawanid='$picardid' )";
            $query .=" AND ( IFNULL(icabangid,'') NOT IN ('0000000001') OR karyawanid='$picardid' )";
        }elseif ($pidgrouppil=="30" AND !empty($ppilihpm)) {
            $query .=" AND IFNULL(divisi,'') ='$ppilihpm' ";
        }
        //echo $query; goto hapusdata;
        $query = "create TEMPORARY table $tmp01 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
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
        
        //$query = "DELETE FROM $tmp01 WHERE IFNULL(divisi,'')='OTC'";
        //mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        if (!empty($ppildivisiid)) {
            if ($ppildivisiid=="ETHICAL") {
                $query = "DELETE FROM $tmp01 WHERE IFNULL(divisi,'')='OTC'";
                mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            }else{
                $query = "DELETE FROM $tmp01 WHERE IFNULL(divisi,'')<>'$ppildivisiid'";
                mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            }
        }
        
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
    
    
    if ($ppildivisiid=="OTC") {
        
        $query = "select date_format(tgljual,'%Y-%m') bulan, 'OTC' as divprodid, sum(`value`) as rpsales from dbmaster.sales_otc_local WHERE YEAR(tgljual)='$periode' AND divprodid <>'OTHER' and icabangid <> 22 GROUP BY 1,2";
        $query = "create TEMPORARY table $tmp04 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "CREATE INDEX `norm1` ON $tmp04 (bulan, divprodid)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }elseif ($ppildivisiid=="ETHICAL") {
        
        $query = "select date_format(bulan,'%Y-%m') bulan, divprodid, sum(value_sales) as rpsales from dbmaster.sales_local WHERE YEAR(bulan)='$periode' ";
        
        if ($pidgrouppil=="8") {
            $ppilregion="B";
            if ($picardid=="0000000159") $ppilregion="T";
            $query .=" AND IFNULL(icabangid,'') IN (select distinct IFNULL(icabangid,'') FROM MKT.icabang WHERE region='$ppilregion' ) ";
        }elseif ($pidgrouppil=="30" AND !empty($ppilihpm)) {
            $query .=" AND IFNULL(divprodid,'') ='$ppilihpm' ";
        }
        
        $query .=" GROUP BY 1,2";
        
        $query = "create TEMPORARY table $tmp04 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "CREATE INDEX `norm1` ON $tmp04 (bulan, divprodid)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }else{
        
    
        $query = "select date_format(bulan,'%Y-%m') bulan, divprodid, sum(value_sales) as rpsales from dbmaster.sales_local WHERE YEAR(bulan)='$periode' GROUP BY 1,2";
        $query = "create TEMPORARY table $tmp04 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "CREATE INDEX `norm1` ON $tmp04 (bulan, divprodid)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        $query = "select date_format(tgljual,'%Y-%m') bulan, 'OTC' as divprodid, sum(`value`) as rpsales from dbmaster.sales_otc_local WHERE YEAR(tgljual)='$periode' AND divprodid <>'OTHER' and icabangid <> 22 GROUP BY 1,2";
        $query = "create TEMPORARY table $tmp05 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "INSERT INTO $tmp04 (bulan, divprodid, rpsales) SELECT bulan, divprodid, rpsales FROM $tmp05";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
    
    }
    
    
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
            <tr class='miring text2'><td>Proses Terakhir</td><td>:</td><td><?PHP echo "$ptanggalprosesnya"; ?></td></tr>
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
                if ($bolehadasales==true) {
                    
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
                    
                }
                
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