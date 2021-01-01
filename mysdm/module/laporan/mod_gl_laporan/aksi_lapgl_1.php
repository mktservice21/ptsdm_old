<?PHP
    ini_set('memory_limit', '-1');
    ini_set('max_execution_time', 0);
    session_start();
    $erpttipe = $_POST['radio1'];
    $rptheader="SUMMARY";
    if ($erpttipe=="D") $rptheader="DETAIL";
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REPORT $rptheader TRANSAKSI BR.xls");
    }
    
    include("config/koneksimysqli.php");
    include "config/fungsi_combo.php";
    include("config/common.php");
    $cnit=$cnmy;
?>


<html>
<head>
    <title>REPORT <?PHP echo $rptheader; ?> TRANSAKSI BR</title>
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
        $pdivisicardid=$_SESSION['DIVISI'];
        $padmkhusus="";
        if ($_SESSION['ADMINKHUSUS']=="Y"){
            $padmkhusus=$_SESSION['KHUSUSSEL'];
        }
        
        $pperiodeby=$_POST['cb_periode'];
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
        
        if (isset($_POST['chkbox_rpt1'])) $pbreth=$_POST['chkbox_rpt1'];
        if (isset($_POST['chkbox_rpt2'])) $pklaim=$_POST['chkbox_rpt2'];
        if (isset($_POST['chkbox_rpt3'])) $pkas=$_POST['chkbox_rpt3'];
        if (isset($_POST['chkbox_rpt4'])) $pbrotc=$_POST['chkbox_rpt4'];
        if (isset($_POST['chkbox_rpt5'])) $prutin=$_POST['chkbox_rpt5'];
        if (isset($_POST['chkbox_rpt6'])) $pblk=$_POST['chkbox_rpt6'];
        if (isset($_POST['chkbox_rpt7'])) $pca=$_POST['chkbox_rpt7'];
        
        
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
        
        /*
        $filterrealisasi=('');
        if (!empty($_POST['chkbox_real'])){
            $filterrealisasi=$_POST['chkbox_real'];
            $filterrealisasi=PilCekBoxAndEmpty($filterrealisasi);
        }
        */
        
        $pfilreal="";
        $filterrealisasi=$_POST['tags_real'];
        
        $pfilrealotc="";
        $filterrealisasiotc="";
        
        if (!empty($filterrealisasi)) {
            $pnmreal=explode(",", $filterrealisasi);
            $pjmlreal=count($pnmreal);
            for ($i=0;$i<(double)$pjmlreal;$i++) {
                //$pfilreal=$pfilreal."'".$pnmreal[$i]."',";
                
                $pfilreal=$pfilreal." a.realisasi1 LIKE '%".$pnmreal[$i]."%' ";
                if ((double)$i<(double)$pjmlreal - 1) {
                    $pfilreal=$pfilreal." OR ";
                }
                
                $pfilrealotc=$pfilrealotc." a.real1 LIKE '%".$pnmreal[$i]."%' ";
                if ((double)$i<(double)$pfilrealotc - 1) {
                    $pfilrealotc=$pfilrealotc." OR ";
                }
                
            }
            //$pfilreal="(".substr($pfilreal, 0, -1).")";
            
            $filterrealisasi="(".$pfilreal.")";
            $filterrealisasiotc="(".$pfilrealotc.")";
            
        }
        
        //echo "$padmkhusus, $pdivisicardid : $prpttype, $pdivisi, $filtercoa, $pbreth, $pklaim, $pkas, $pbrotc, $prutin, $pblk, $pca, $filterrealisasi, $filterrealisasiotc"; exit;
        
        
        $now=date("mdYhis");
        $tmp00 =" dbtemp.RPTGLACC00_".$_SESSION['USERID']."_$now ";
        $tmp01 =" dbtemp.RPTGLACC01_".$_SESSION['USERID']."_$now ";
        $tmp02 =" dbtemp.RPTGLACC02_".$_SESSION['USERID']."_$now ";
        
        $query = "CREATE TEMPORARY TABLE $tmp01 (kodeinput VARCHAR(2), idinput VARCHAR(20), divisi VARCHAR(5), 
                tgltrans date, bukti VARCHAR(50), coa VARCHAR(30), nama_coa VARCHAR(200), 
                dokter VARCHAR(200), noslip VARCHAR(50), pengajuan VARCHAR(200), keterangan VARCHAR(300), 
                nmrealisasi VARCHAR(100), debit DECIMAL(20,2), kredit DECIMAL(20,2), saldo DECIMAL(20,2), 
                dpp DECIMAL(20,2), ppn DECIMAL(20,2), pph DECIMAL(20,2), tglfp date, 
                tglfp_ppn date, seri_ppn VARCHAR(200), tglfp_pph date, seri_pph VARCHAR(200) )";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        if (!empty($pbreth)) {
            
            $npilihtgl="a.tgl";
            if ($pperiodeby=="2") $npilihtgl="a.tgltrans";
            
            $query = "select a.divprodid, a.brId, a.tgl, a.noslip, a.tgltrans, a.karyawanId, c.nama nama_karyawan, 
                a.dokterId, a.dokter, b.nama nama_dokter,
                a.aktivitas1, a.aktivitas2, a.realisasi1, a.jumlah, jumlah1, a.realisasi2, a.COA4, 
                a.dpp, a.ppn_rp, a.pph_rp, a.tgl_fp
                from hrd.br0 a 
                LEFT JOIN hrd.dokter b on a.dokterId=b.dokterId
                LEFT JOIN hrd.karyawan c on a.karyawanId=c.karyawanId 
                WHERE a.retur <> 'Y' and a.batal <>'Y' AND DATE_FORMAT($npilihtgl,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
            if (!empty($pdivisi)) $query .=" AND a.divprodid='$pdivisi' ";
            if (!empty($filtercoa)) $query .=" AND IFNULL(a.COA4,'') IN $filtercoa ";
            
            //if (!empty($filterrealisasi)) $query .=" AND IFNULL(a.realisasi1,'') IN $filterrealisasi ";
            if (!empty($filterrealisasi)) $query .=" AND $filterrealisasi ";
            
            if ($pdivisicardid=="OTC")
                $query .=" AND a.divprodid='$pdivisicardid' ";
            else{
                if (!empty($padmkhusus)) $query .=" AND a.divprodid in $padmkhusus ";
            }
            
            $query = "create TEMPORARY table $tmp02 ($query)";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            //$query = "UPDATE $tmp02 SET jumlah=jumlah1 WHERE IFNULL(jumlah1,0)<>0";
            //mysqli_query($cnit, $query);
            //$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            $query = "INSERT INTO $tmp01 (kodeinput, idinput, divisi, tgltrans, coa, dokter, noslip, pengajuan, keterangan, nmrealisasi, debit, kredit, dpp, ppn, pph, tglfp)"
                    . "SELECT 'A' kodeinput, brId, divprodid, tgltrans, COA4, nama_dokter, noslip, nama_karyawan, aktivitas1, realisasi1, jumlah, jumlah1, dpp, ppn_rp, pph_rp, tgl_fp FROM $tmp02";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        }
        
        
        if (!empty($pklaim)) {
            mysqli_query($cnit, "DROP TABLE $tmp02");
            
            $npilihtgl="a.tgl";
            if ($pperiodeby=="2") $npilihtgl="a.tgltrans";
            
            $query = "select a.DIVISI divprodid, a.tgltrans, a.distid, a.klaimId, a.COA4, a.karyawanid, b.nama nama_karyawan, a.noslip,
                a.aktivitas1, a.realisasi1 nmrealisasi, a.jumlah, 
                a.dpp, a.ppn_rp, a.pph_rp, a.tgl_fp 
                FROM hrd.klaim a LEFT JOIN hrd.karyawan b on a.karyawanid=b.karyawanId 
                WHERE a.klaimId not in (SELECT DISTINCT ifnull(klaimId,'') from hrd.klaim_reject) AND 
                DATE_FORMAT($npilihtgl,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
            if (!empty($pdivisi)) $query .=" AND a.DIVISI='$pdivisi' ";
            if (!empty($filtercoa)) $query .=" AND IFNULL(a.COA4,'') IN $filtercoa ";
            
            //if (!empty($filterrealisasi)) $query .=" AND IFNULL(a.realisasi1,'') IN $filterrealisasi ";
            if (!empty($filterrealisasi)) $query .=" AND $filterrealisasi ";
            
            if ($pdivisicardid=="OTC") $query .=" AND a.DIVISI='$pdivisicardid' ";
            else{
                if (!empty($padmkhusus)) $query .=" AND a.DIVISI in $padmkhusus ";
            }
            
            $query = "create TEMPORARY table $tmp02 ($query)";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "INSERT INTO $tmp01 (kodeinput, idinput, divisi, tgltrans, coa, noslip, pengajuan, keterangan, nmrealisasi, debit, dpp, ppn, pph, tglfp)"
                    . "SELECT 'E', klaimId, divprodid, tgltrans, COA4, noslip, nama_karyawan, aktivitas1, nmrealisasi, jumlah, dpp, ppn_rp, pph_rp, tgl_fp FROM $tmp02";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
            
        }
        
        if (!empty($pkas)) {
            mysqli_query($cnit, "DROP TABLE $tmp02");
            
            $query = "select e.DIVISI2 divprodid, a.periode1 tgltrans, a.kasId, a.kode, b.COA4, a.karyawanid, f.nama nama_karyawan, a.nobukti,
                    a.aktivitas1, a.jumlah
                    FROM hrd.kas a LEFT JOIN dbmaster.posting_coa_kas b on a.kode=b.kodeid
                    LEFT JOIN dbmaster.coa_level4 c on b.COA4=c.COA4 LEFT JOIN dbmaster.coa_level3 d on c.COA3=d.COA3
                    LEFT JOIN dbmaster.coa_level2 e on e.COA2=d.COA2
                    LEFT JOIN hrd.karyawan f on a.karyawanid=f.karyawanId 
                    WHERE DATE_FORMAT(a.periode1,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
            if (!empty($pdivisi)) $query .=" AND IFNULL(e.DIVISI2,'HO')='$pdivisi' ";
            if (!empty($filtercoa)) $query .=" AND IFNULL(b.COA4,'') IN $filtercoa ";
            
            if ($pdivisicardid=="OTC") $query .=" AND e.DIVISI2='$pdivisicardid' ";
            else{
                if (!empty($padmkhusus)) $query .=" AND e.DIVISI2 in $padmkhusus ";
            }
            
            $query = "create TEMPORARY table $tmp02 ($query)";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "INSERT INTO $tmp01 (kodeinput, idinput, divisi, tgltrans, coa, pengajuan, keterangan, debit)"
                    . "SELECT 'K', kasId, divprodid, tgltrans, COA4, nama_karyawan, aktivitas1, jumlah FROM $tmp02";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
            
        }
        
        if (!empty($pbrotc)) {
            mysqli_query($cnit, "DROP TABLE $tmp02");
            
            $npilihtgl="a.tglbr";
            if ($pperiodeby=="2") $npilihtgl="a.tgltrans";
            
            $query = "select 'OTC' divprodid, a.brOtcId, a.noslip, a.tgltrans,  
                a.keterangan1 aktivitas1, a.keterangan2 aktivitas2, a.real1 realisasi1, a.jumlah, realisasi jumlah1, a.COA4, 
                a.dpp, a.ppn_rp, a.pph_rp, a.tgl_fp 
                from hrd.br_otc a 
                WHERE a.brOtcId NOT IN (select DISTINCT IFNULL(brOtcId,'') FROM hrd.br_otc_reject) AND 
                DATE_FORMAT($npilihtgl, '%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
            if (!empty($filtercoa)) $query .=" AND IFNULL(a.COA4,'') IN $filtercoa ";
            
            //if (!empty($filterrealisasiotc)) $query .=" AND IFNULL(a.real1,'') IN $filterrealisasiotc ";
            if (!empty($filterrealisasiotc)) $query .=" AND $filterrealisasiotc ";
            else{
               if (!empty($padmkhusus)) $query .=" AND 'OTC' in $padmkhusus "; 
            }
            
            $query = "create TEMPORARY table $tmp02 ($query)";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            //$query = "UPDATE $tmp02 SET jumlah=jumlah1 WHERE IFNULL(jumlah1,0)<>0";
            //mysqli_query($cnit, $query);
            //$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "INSERT INTO $tmp01 (kodeinput, idinput, divisi, tgltrans, coa, noslip, keterangan, nmrealisasi, debit, kredit, dpp, ppn, pph, tglfp)"
                    . "SELECT 'D' kodeinput, brOtcId, divprodid, tgltrans, COA4, noslip, aktivitas1, realisasi1, jumlah, jumlah1, dpp, ppn_rp, pph_rp, tgl_fp FROM $tmp02";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
        }
        
        if (!empty($prutin)) {
            mysqli_query($cnit, "DROP TABLE $tmp02");
            
            $query = "select DATE_FORMAT(b.periode1,'%Y-%m-01') periode, b.divisi, b.bulan, b.icabangid, 
                a.idrutin, a.nobrid, a.coa, b.karyawanid, c.nama nama_karyawan, b.keterangan, sum(a.rptotal) rptotal 
                FROM dbmaster.t_brrutin1 a JOIN dbmaster.t_brrutin0 b on a.idrutin=b.idrutin 
                LEFT JOIN hrd.karyawan c on b.karyawanid=c.karyawanId 
                WHERE IFNULL(b.stsnonaktif,'') <> 'Y' AND b.kode=1 AND IFNULL(b.tgl_fin,'')<>'' 
                AND DATE_FORMAT(b.bulan,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
            if (!empty($pdivisi)) $query .=" AND b.divisi='$pdivisi' ";
            if (!empty($filtercoa)) $query .=" AND IFNULL(a.coa,'') IN $filtercoa ";
            
            if ($pdivisicardid=="OTC") $query .=" AND b.divisi='$pdivisicardid' ";
            else{
                if (!empty($padmkhusus)) $query .=" AND b.divisi in $padmkhusus ";
            }
            
            $query .= " GROUP BY 1,2,3,4,5,6,7,8,9,10";
            $query = "create TEMPORARY table $tmp02 ($query)";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            mysqli_query($cnit, "UPDATE $tmp02 a SET a.coa=IFNULL((select coa FROM dbmaster.posting_coa_rutin b WHERE a.nobrid=b.nobrid AND a.divisi=b.divisi),a.divisi)");
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $query = "INSERT INTO $tmp01 (kodeinput, idinput, divisi, tgltrans, coa, keterangan, pengajuan, debit)"
                    . "SELECT 'F' kodeinput, idrutin, divisi, periode, coa, keterangan, nama_karyawan, sum(rptotal) rptotal FROM $tmp02 GROUP BY 1,2,3,4,5,6";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        }
        
        if (!empty($pblk)) {
            mysqli_query($cnit, "DROP TABLE $tmp02");
            
            $query = "select DATE_FORMAT(b.periode1,'%Y-%m-01') periode, b.divisi, b.bulan, b.icabangid, 
                a.idrutin, a.nobrid, a.coa, b.karyawanid, c.nama nama_karyawan, b.keterangan, sum(a.rptotal) rptotal 
                FROM dbmaster.t_brrutin1 a JOIN dbmaster.t_brrutin0 b on a.idrutin=b.idrutin 
                LEFT JOIN hrd.karyawan c on b.karyawanid=c.karyawanId 
                WHERE IFNULL(b.stsnonaktif,'') <> 'Y' AND b.kode=2 AND IFNULL(b.tgl_fin,'')<>'' 
                AND DATE_FORMAT(b.bulan,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
            if (!empty($pdivisi)) $query .=" AND b.divisi='$pdivisi' ";
            if (!empty($filtercoa)) $query .=" AND IFNULL(a.coa,'') IN $filtercoa ";
            
            if ($pdivisicardid=="OTC") $query .=" AND b.divisi='$pdivisicardid' ";
            else{
                if (!empty($padmkhusus)) $query .=" AND b.divisi in $padmkhusus ";
            }
            
            $query .= " GROUP BY 1,2,3,4,5,6,7,8,9,10";
            $query = "create TEMPORARY table $tmp02 ($query)";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            mysqli_query($cnit, "UPDATE $tmp02 a SET a.coa=IFNULL((select coa FROM dbmaster.posting_coa_rutin b WHERE a.nobrid=b.nobrid AND a.divisi=b.divisi),a.divisi)");
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $query = "INSERT INTO $tmp01 (kodeinput, idinput, divisi, tgltrans, coa, keterangan, pengajuan, debit)"
                    . "SELECT 'I' kodeinput, idrutin, divisi, periode, coa, keterangan, nama_karyawan, sum(rptotal) rptotal FROM $tmp02 GROUP BY 1,2,3,4,5,6";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        }
        
        if (!empty($pca)) {
            mysqli_query($cnit, "DROP TABLE $tmp02");
            
        }
        
        
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.nama_coa=(SELECT NAMA4 FROM dbmaster.coa_level4 b WHERE a.coa=b.COA4 LIMIT 1)");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        if ($prpttype=="D") {
            $query = "SELECT * FROM $tmp01";
        }else{
            $query = "SELECT kodeinput, idinput, divisi, tgltrans, bukti, coa, nama_coa, '' dokter, '' noslip, '' pengajuan, '' keterangan, "
                    . " '' nmrealisasi, tglfp_ppn, seri_ppn, tglfp_pph, seri_pph, sum(debit) debit, "
                    . " sum(kredit) kredit, sum(saldo) saldo, sum(dpp) dpp, sum(ppn) ppn, sum(pph) pph FROM $tmp01 "
                    . " GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16";
            
            $query = "SELECT divisi, coa, nama_coa, sum(debit) debit, "
                    . " sum(kredit) kredit, sum(saldo) saldo, sum(dpp) dpp, sum(ppn) ppn, sum(pph) pph FROM $tmp01 "
                    . " GROUP BY 1,2,3";
        }
        $query = "create TEMPORARY table $tmp00 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        echo "<table class='tjudul' width='100%'>";
        echo "<tr> <td width='400px'>TRANSAKSI BUDGET REQUEST</td> </tr>";
        echo "<tr> <td width='200px'>$myperiode1 s/d. $myperiode2</td></tr>";
        echo "</table>";
        echo "<br/>&nbsp;";
        
        
    if ($prpttype=="D") {  
    ?>
        <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
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
                <th align="center" nowrap>Nama Realisasi</th>
                <th align="center" nowrap>Usulan</th>
                <th align="center" nowrap>Realisasi</th>
                <th align="center" nowrap>Selisih</th>
                <th align="center" nowrap>No</th>
                <th align="center" nowrap>DPP</th>
                <th align="center" nowrap>PPN</th>
                <th align="center" nowrap>PPH</th>
                <th align="center" nowrap>TGL FP PPN</th>
                <th align="center" nowrap>SERI FP PPN</th>
                <th align="center" nowrap>TGL FP PPH</th>
                <th align="center" nowrap>SERI FP PPH</th>
                <th align="center" nowrap>ID</th>

            </thead>
            <tbody>
                <?PHP
                $pcoanama="";
                
                $ptotcoadebit=0;
                $ptotcoacredit=0;
                $ptotcoadivisidebit=0;
                $ptotcoadivisicredit=0;
                $ptotaldebit=0;
                $ptotalcredit=0;
                
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
                $tampil=mysqli_query($cnit, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pdivisi=$row['divisi'];
                    $mdivisi=$pdivisi;
                    if ($pdivisi=="CAN") $mdivisi="CANARY";
                    
                    $ptotcoadivisidebit=0;
                    $ptotcoadivisicredit=0;
                    $ptotcoadivisidpp=0;
                    $ptotcoadivisippn=0;
                    $ptotcoadivisipph=0;
                    
                    $query2 = "select distinct divisi, coa from $tmp00 WHERE RTRIM(divisi)='$pdivisi' order by divisi, coa";
                    $tampil2=mysqli_query($cnit, $query2);
                    while ($row2= mysqli_fetch_array($tampil2)) {
                        $pcoa=$row2['coa'];
                        
                        $ptotcoadebit=0;
                        $ptotcoacredit=0;
                        $ptotcoadpp=0;
                        $ptotcoappn=0;
                        $ptotcoapph=0;
                        
                        $query3 = "select * from $tmp00 WHERE RTRIM(divisi)='$pdivisi' AND RTRIM(coa)='$pcoa' order by divisi, coa, pengajuan, tgltrans";
                        $tampil3=mysqli_query($cnit, $query3);
                        while ($row3= mysqli_fetch_array($tampil3)) {
                            
                            $ptgltrans="";
                            if (!empty($row3['tgltrans']) AND $row3['tgltrans']<>"0000-00-00")
                                $ptgltrans = date("d/m/Y", strtotime($row3['tgltrans']));
                            
                            $pidinput=$row3['idinput'];
                            $pbukti=$row3['bukti'];
                            $pcoa=$row3['coa'];
                            $pcoanama=$row3['nama_coa'];
                            $pidinput=$row3['idinput'];
                            $pdokternm=$row3['dokter'];
                            $pnoslip=$row3['noslip'];
                            $ppengajuan=$row3['pengajuan'];
                            $pketerangan=$row3['keterangan'];
                            $pnmrealisasi=$row3['nmrealisasi'];
                            
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
                            
                            $pdebit=$row3['debit'];
                            $ptotcoadebit=(double)$ptotcoadebit+(double)$pdebit;
                            
                            $pcredit=$row3['kredit'];
                            $ptotcoacredit=(double)$ptotcoacredit+(double)$pcredit;
                            
                            $psaldo=(double)$pdebit-(double)$pcredit;
                            
                            $pdebit=number_format($pdebit,0,",",",");
                            $pcredit=number_format($pcredit,0,",",",");
                            $psaldo=number_format($psaldo,0,",",",");
                            
                            echo "<tr>";
                            echo "<td nowrap>$mdivisi</td>";
                            echo "<td nowrap>$ptgltrans</td>";
                            echo "<td nowrap>$pbukti</td>";
                            echo "<td nowrap>$pcoa</td>";
                            echo "<td nowrap>$pcoanama</td>";

                            echo "<td >$pdokternm</td>";
                            echo "<td nowrap>$pnoslip</td>";
                            echo "<td >$ppengajuan</td>";
                            echo "<td>$pketerangan</td>";
                            echo "<td nowrap>$pnmrealisasi</td>";
                            echo "<td nowrap align='right'>$pdebit</td>";//$pdebit
                            echo "<td nowrap align='right'>$pcredit</td>";
                            echo "<td nowrap align='right'>$psaldo</td>";//$psaldo
                            echo "<td nowrap align='right'></td>";//no
                            
                            echo "<td nowrap align='right'>$pdpprp</td>";
                            echo "<td nowrap align='right'>$pppnrp</td>";
                            echo "<td nowrap align='right'>$ppphrp</td>";
                            echo "<td nowrap>$ptglfp</td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td>$pidinput</td>";
                            echo "</tr>";
                            
                            
                            
                        }
                        
                        echo "<tr>";
                        echo "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
                        echo "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "</tr>";
                        
                        $ptotcoadivisidebit=(double)$ptotcoadivisidebit+(double)$ptotcoadebit;
                        $ptotcoadivisicredit=(double)$ptotcoadivisicredit+(double)$ptotcoacredit;
                        
                        $ptotcoasaldo=(double)$ptotcoadebit-(double)$ptotcoacredit;
                        
                        $ptotcoadebit=number_format($ptotcoadebit,0,",",",");
                        $ptotcoacredit=number_format($ptotcoacredit,0,",",",");
                        $ptotcoasaldo=number_format($ptotcoasaldo,0,",",",");
                        
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
                        
                        echo "<td nowrap align='right'><b>$ptotcoadebit</b></td>";//$ptotcoadebit
                        echo "<td nowrap align='right'><b>$ptotcoacredit</b></td>";
                        echo "<td nowrap align='right'><b>$ptotcoasaldo</b></td>";//$ptotcoasaldo
                            
                        echo "<td></td>";
                        
                        echo "<td nowrap align='right'><b>$ptotcoadpp</b></td>";//dpp
                        echo "<td nowrap align='right'><b>$ptotcoappn</b></td>";//ppn
                        echo "<td nowrap align='right'><b>$ptotcoapph</b></td>";//pph
                        
                        echo "<td></td><td></td><td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "</tr>";
                        
                        echo "<tr>";
                        echo "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
                        echo "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "</tr>";
                        
                    }
                    
                    $ptotaldebit=(double)$ptotaldebit+(double)$ptotcoadivisidebit;
                    $ptotalcredit=(double)$ptotalcredit+(double)$ptotcoadivisicredit;
                    
                    $ptotcoadivisisaldo=(double)$ptotcoadivisidebit-(double)$ptotcoadivisicredit;
                    
                    $ptotcoadivisidebit=number_format($ptotcoadivisidebit,0,",",",");
                    $ptotcoadivisicredit=number_format($ptotcoadivisicredit,0,",",",");
                    $ptotcoadivisisaldo=number_format($ptotcoadivisisaldo,0,",",",");
                    
                    $ptotaldpp=(double)$ptotaldpp+(double)$ptotcoadivisidpp;
                    $ptotcoadivisidpp=number_format($ptotcoadivisidpp,0,",",",");
                    
                    $ptotalppn=(double)$ptotalppn+(double)$ptotcoadivisippn;
                    $ptotcoadivisippn=number_format($ptotcoadivisippn,0,",",",");
                    
                    $ptotalpph=(double)$ptotalpph+(double)$ptotcoadivisipph;
                    $ptotcoadivisipph=number_format($ptotcoadivisipph,0,",",",");

                    echo "<tr>";
                    echo "<td nowrap colspan=5 align='center'><b>TOTAL $mdivisi </b></td>";
                    echo "<td></td><td></td><td></td><td></td><td></td>";

                    echo "<td nowrap align='right'><b>$ptotcoadivisidebit</b></td>";//$ptotcoadivisidebit
                    echo "<td nowrap align='right'><b>$ptotcoadivisicredit</b></td>";
                    echo "<td nowrap align='right'><b>$ptotcoadivisisaldo</b></td>";//$ptotcoadivisisaldo

                    echo "<td></td>";

                    echo "<td nowrap align='right'><b>$ptotcoadivisidpp</b></td>";//dpp
                    echo "<td nowrap align='right'><b>$ptotcoadivisippn</b></td>";//ppn
                    echo "<td nowrap align='right'><b>$ptotcoadivisipph</b></td>";//pph

                    echo "<td></td><td></td><td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "</tr>";

                    echo "<tr>";
                    echo "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
                    echo "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "</tr>";
                        
                }
                
                $ptotalsaldo=(double)$ptotaldebit-(double)$ptotalcredit;
                
                $ptotaldebit=number_format($ptotaldebit,0,",",",");
                $ptotalcredit=number_format($ptotalcredit,0,",",",");
                $ptotalsaldo=number_format($ptotalsaldo,0,",",",");
                
                $ptotaldpp=number_format($ptotaldpp,0,",",",");
                $ptotalppn=number_format($ptotalppn,0,",",",");
                $ptotalpph=number_format($ptotalpph,0,",",",");

                echo "<tr>";
                echo "<td nowrap colspan=5 align='center'><b>GRAND TOTAL</b></td>";
                echo "<td></td><td></td><td></td><td></td><td></td>";

                echo "<td nowrap align='right'><b>$ptotaldebit</b></td>";//$ptotaldebit
                echo "<td nowrap align='right'><b>$ptotalcredit</b></td>";
                echo "<td nowrap align='right'><b>$ptotalsaldo</b></td>";//$ptotalsaldo

                echo "<td></td>";

                echo "<td nowrap align='right'><b>$ptotaldpp</b></td>";//dpp
                echo "<td nowrap align='right'><b>$ptotalppn</b></td>";//ppn
                echo "<td nowrap align='right'><b>$ptotalpph</b></td>";//pph

                echo "<td></td><td></td><td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "</tr>";

                echo "<tr>";
                echo "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
                echo "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "</tr>";
                ?>
            </tbody>
        </table>
    
    <?PHP
    }else{
    ?>
        <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
            <thead>
                <tr>
                <th align="center" nowrap>Divisi</th>
                <th align="center" nowrap>KODE</th>
                <th align="center" nowrap>PERKIRAAN</th>
                <th align="center" nowrap>Usulan</th>
                <th align="center" nowrap>Realisasi</th>
                <th align="center" nowrap>Selisih</th>
                <th align="center" nowrap>No</th>
                <th align="center" nowrap>DPP</th>
                <th align="center" nowrap>PPN</th>
                <th align="center" nowrap>PPH</th>

            </thead>
            <tbody>
                <?PHP
                $pcoanama="";
                
                $ptotcoadivisidebit=0;
                $ptotcoadivisicredit=0;
                $ptotaldebit=0;
                $ptotalcredit=0;
                
                $ptotcoadivisidpp=0;
                $ptotaldpp=0;
                $ptotcoadivisippn=0;
                $ptotalppn=0;
                $ptotcoadivisipph=0;
                $ptotalpph=0;
                
                $query = "select distinct divisi from $tmp00 order by divisi";
                $tampil=mysqli_query($cnit, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pdivisi=$row['divisi'];
                    $mdivisi=$pdivisi;
                    if ($pdivisi=="CAN") $mdivisi="CANARY";
                    
                    $ptotcoadivisidebit=0;
                    $ptotcoadivisicredit=0;
                    $ptotcoadivisidpp=0;
                    $ptotcoadivisippn=0;
                    $ptotcoadivisipph=0;
                    
                    $query2 = "select * from $tmp00 WHERE RTRIM(divisi)='$pdivisi' order by divisi, coa";
                    $tampil2=mysqli_query($cnit, $query2);
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

                        $pdebit=$row2['debit'];
                        $pcredit=$row2['kredit'];
                        
                        $ptotcoadivisidebit=(double)$ptotcoadivisidebit+(double)$pdebit;
                        $ptotcoadivisicredit=(double)$ptotcoadivisicredit+(double)$pcredit;
                        
                        $psaldo=(double)$pdebit-(double)$pcredit;
                        
                        $pdebit=number_format($pdebit,0,",",",");
                        $pcredit=number_format($pcredit,0,",",",");
                        $psaldo=number_format($psaldo,0,",",",");
                            
                        echo "<tr>";
                        echo "<td nowrap>$mdivisi</td>";
                        echo "<td nowrap>$pcoa</td>";
                        echo "<td nowrap>$pcoanama</td>";

                        echo "<td nowrap align='right'>$pdebit</td>";//$pdebit
                        echo "<td nowrap align='right'>$pcredit</td>";
                        echo "<td nowrap align='right'>$psaldo</td>";//$psaldo

                        echo "<td nowrap align='right'></td>";
                        
                        echo "<td nowrap align='right'>$pdpprp</td>";
                        echo "<td nowrap align='right'>$pppnrp</td>";
                        echo "<td nowrap align='right'>$ppphrp</td>";
                        echo "</tr>";
                    }
                    
                    $ptotaldebit=(double)$ptotaldebit+(double)$ptotcoadivisidebit;
                    $ptotalcredit=(double)$ptotalcredit+(double)$ptotcoadivisicredit;
                    
                    $ptotcoadivisisaldo=(double)$ptotcoadivisidebit-(double)$ptotcoadivisicredit;
                    
                    $ptotcoadivisidebit=number_format($ptotcoadivisidebit,0,",",",");
                    $ptotcoadivisicredit=number_format($ptotcoadivisicredit,0,",",",");
                    $ptotcoadivisisaldo=number_format($ptotcoadivisisaldo,0,",",",");
                    
                    $ptotaldpp=(double)$ptotaldpp+(double)$ptotcoadivisidpp;
                    $ptotcoadivisidpp=number_format($ptotcoadivisidpp,0,",",",");
                    
                    $ptotalppn=(double)$ptotalppn+(double)$ptotcoadivisippn;
                    $ptotcoadivisippn=number_format($ptotcoadivisippn,0,",",",");
                    
                    $ptotalpph=(double)$ptotalpph+(double)$ptotcoadivisipph;
                    $ptotcoadivisipph=number_format($ptotcoadivisipph,0,",",",");

                    echo "<tr>";
                    echo "<td nowrap colspan='3' align='center'><b>TOTAL $mdivisi </b></td>";
                        
                    echo "<td nowrap align='right'><b>$ptotcoadivisidebit</b></td>";//$ptotcoadivisidebit
                    echo "<td nowrap align='right'><b>$ptotcoadivisicredit</b></td>";
                    echo "<td nowrap align='right'><b>$ptotcoadivisisaldo</b></td>";//$ptotcoadivisisaldo

                    echo "<td></td>";

                    echo "<td nowrap align='right'><b>$ptotcoadivisidpp</b></td>";//dpp
                    echo "<td nowrap align='right'><b>$ptotcoadivisippn</b></td>";//ppn
                    echo "<td nowrap align='right'><b>$ptotcoadivisipph</b></td>";//$pph
                    
                    echo "</tr>";
                    
                }
                $ptotalsaldo=(double)$ptotaldebit-(double)$ptotalcredit;
                
                $ptotaldebit=number_format($ptotaldebit,0,",",",");
                $ptotalcredit=number_format($ptotalcredit,0,",",",");
                $ptotalsaldo=number_format($ptotalsaldo,0,",",",");
                $ptotaldpp=number_format($ptotaldpp,0,",",",");
                $ptotalppn=number_format($ptotalppn,0,",",",");
                $ptotalpph=number_format($ptotalpph,0,",",",");

                echo "<tr>";
                echo "<td nowrap colspan='3' align='center'><b>GRAND TOTAL </b></td>";

                echo "<td nowrap align='right'><b>$ptotaldebit</b></td>";//$ptotaldebit
                echo "<td nowrap align='right'><b>$ptotalcredit</b></td>";
                echo "<td nowrap align='right'><b>$ptotalsaldo</b></td>";//$ptotalsaldo

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
    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
    <?PHP
    hapusdata:
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp00");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
        
        mysqli_close($cnit);
    ?>
</body>

    <style>
        .tjudul {
            font-family: Georgia, serif;
            font-size: 15px;
            margin-left:10px;
            margin-right:10px;
        }
        .tjudul td {
            padding: 4px;
        }
        #datatable2 {
            font-family: Georgia, serif;
            margin-left:10px;
            margin-right:10px;
        }
        #datatable2 th, #datatable2 td {
            padding: 4px;
        }
        #datatable2 thead{
            background-color:#cccccc; 
            font-size: 12px;
        }
        #datatable2 tbody{
            font-size: 11px;
        }
    </style>
    
    
    
</html>
