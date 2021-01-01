<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    $ppilihrpt="";
    include("config/koneksimysqli.php");
    $printdate= date("d/m/Y");
?>


<?PHP

    $ptahun = $_POST['e_tgl1'];
    $padaygdisimpan=false;
    
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
    $psewakontrak=""; 
    $pserviceken="";
    $ppilihsaleseth="";
    $ppilihsalesotc="";
    
    
    $ppilishadbgt="";
    
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
    if (isset($_POST['chkbox_rpt11'])) $psewakontrak=$_POST['chkbox_rpt11'];
    if (isset($_POST['chkbox_rpt12'])) $pserviceken=$_POST['chkbox_rpt12'];
    if (isset($_POST['chkbox_rpt13'])) $ppilihsaleseth=$_POST['chkbox_rpt13'];
    if (isset($_POST['chkbox_rpt14'])) $ppilihsalesotc=$_POST['chkbox_rpt14'];
    
    
    if (isset($_POST['chkbox_rpt100'])) $ppilishadbgt=$_POST['chkbox_rpt100'];
    
    
    $pbreth_nmpros="";
    $pklaim_nmpros="";
    $pkas_nmpros="";
    $pbrotc_nmpros="";
    $prutin_nmpros="";
    $pblk_nmpros="";
    $pca_nmpros="";
    $pbmsby_nmpros="";
    $ppilbank_nmpros="";
    $ppilinsen_nmpros="";    
    $psewakontrak_nmpros=""; 
    $pserviceken_nmpros="";
    $ppilihsaleseth_nmpros="";
    $ppilihsalesotc_nmpros="";
    
    $ppilishadbgt_nmpros="";
    
    
    //BR ETHICAL A
    if (!empty($pbreth)) {
        $query ="CALL dbmaster.proses_q_br0('$ptahun')";//
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto queryfunction; }
        
        $pbreth_nmpros="Proses Budget Request (BR) Ethical";
        $padaygdisimpan=true;
    }
    
    //klaimdiscount B
    if (!empty($pklaim)) {
        $query ="CALL dbmaster.proses_q_klaim('$ptahun')";//
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto queryfunction; }
        
        $pklaim_nmpros="Proses Klaim Discount Distributor";
        $padaygdisimpan=true;
    }
    
    //KAS KASBON C & D
    if (!empty($pkas)) {
        $query ="CALL dbmaster.proses_q_kas_kasbon('$ptahun')";//
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto queryfunction; }
        
        $pkas_nmpros="Proses Kas Kecil dan Kas Bon";
        $padaygdisimpan=true;
    }
    
    //BROTC E
    if (!empty($pbrotc)) {
        $query ="CALL dbmaster.proses_q_br_otc('$ptahun')";//
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto queryfunction; }
        
        $pbrotc_nmpros="Proses BR OTC";
        $padaygdisimpan=true;
    }
    
    //rutin
    if (!empty($prutin)) {
        $query ="CALL dbmaster.proses_q_rutin_eth('$ptahun')";//
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto queryfunction; }
        
        $query ="CALL dbmaster.proses_q_rutin_otc('$ptahun')";//
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto queryfunction; }
        
        $prutin_nmpros="Proses Biaya Rutin";
        $padaygdisimpan=true;
    }
    
    //LUAR KOTA
    if (!empty($pblk)) {
        $query ="CALL dbmaster.proses_q_lk_eth('$ptahun')";//
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto queryfunction; }
        
        $query ="CALL dbmaster.proses_q_lk_otc('$ptahun')";//
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto queryfunction; }
        
        $pblk_nmpros="Proses Biaya Luar Kota";
        $padaygdisimpan=true;
    }
    
    //BM biaya marketing surabaya I & J
    if (!empty($pbmsby)) {
        $query ="CALL dbmaster.proses_q_biaya_sby('$ptahun')";//
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto queryfunction; }
        
        $pbmsby_nmpros="Proses Biaya Marketing By Surabaya";
        $padaygdisimpan=true;
    }
    
    //insentif incentive K
    if (!empty($ppilinsen)) {
        $query ="CALL dbmaster.proses_q_insentif_eth('$ptahun')";//
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto queryfunction; }
        
        $ppilinsen_nmpros="Proses Insentif Ethical";
        $padaygdisimpan=true;
    }
    
    //BANK L M N O P
    if (!empty($ppilbank)) {
        $query ="CALL dbmaster.proses_q_bank('$ptahun')";//
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto queryfunction; }
        
        $ppilbank_nmpros="Proses Bank";
        $padaygdisimpan=true;
    }
    
    //SEWA KONTRAKAN RUMAH U
    if (!empty($psewakontrak)) {
        $query ="CALL dbmaster.proses_q_sewakontrakan('$ptahun')";//
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto queryfunction; }
        
        $psewakontrak_nmpros="Proses Sewa Kontrakan Ruman";
        $padaygdisimpan=true;
    }
    
    //SERVICE KENDARAAN V
    if (!empty($pserviceken)) {
        $query ="CALL dbmaster.proses_q_servicekendaraan('$ptahun')";//
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto queryfunction; }
        
        $pserviceken_nmpros="Proses Service Kendaraan";
        $padaygdisimpan=true;
    }
    
    
    //SALES ETHICAL
    if (!empty($ppilihsaleseth)) {
        $query ="CALL dbmaster.proses_q_sales_sls_new('$ptahun')";//
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto queryfunction; }
        
        $ppilihsaleseth_nmpros="Proses Sales Ethical";
        $padaygdisimpan=true;
    }
    
    //SALES OTC
    if (!empty($ppilihsalesotc)) {
        $query ="CALL dbmaster.proses_q_otc_etl_mkt_sales_it('$ptahun')";//
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto queryfunction; }
        
        $ppilihsalesotc_nmpros="Proses Sales OTC";
        $padaygdisimpan=true;
    }
    
    
    
    
    
    //SHARED BUDGET HO KE DIVISI EAGLE PEACOK PIGEON
    if (!empty($ppilishadbgt)) {
        $query ="CALL dbmaster.proses_q_ho_budget_shared('$ptahun')";//
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto queryfunction; }
        
        $ppilishadbgt_nmpros="Proses Shared Budget HO to Divisi Marketing";
        $padaygdisimpan=true;
    }
    
    
?>
<HTML>
<HEAD>
    <title>Proses Data Biaya Marketing</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Mei 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <!--<link href="css/laporanbaru.css" rel="stylesheet">-->
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        
        
    <?PHP } ?>
</HEAD>
<BODY>
    <?PHP
        
    if ($padaygdisimpan==true) {
        echo "TAHUN $ptahun BERHASIL...<br/>&nbsp;<br/>";
        echo "$pbreth_nmpros<br/>";
        echo "$pklaim_nmpros<br/>";
        echo "$pkas_nmpros<br/>";
        echo "$pbrotc_nmpros<br/>";
        echo "$prutin_nmpros<br/>";
        echo "$pblk_nmpros<br/>";
        echo "$pbmsby_nmpros<br/>";
        echo "$ppilbank_nmpros<br/>";
        echo "$ppilinsen_nmpros<br/>";
        echo "$psewakontrak_nmpros<br/>";
        echo "$pserviceken_nmpros<br/>";
        echo "$ppilihsaleseth_nmpros<br/>";
        echo "$ppilihsalesotc_nmpros<br/>";
        
        
        echo "<br/><span style='color:blue'><b>$ppilishadbgt_nmpros</b></span><br/>";
        echo "<br/>&nbsp;<br/>&nbsp;<br/><span style='color:red'><b>Silakan Tutup Halaman Ini...</b></span><br/>";
    }else{
        echo "TIDAK ADA DATA YANG DIPROSES...";
    }
    ?>
</BODY>
</HTML>
<?PHP
goto queryfunction;

exit;
?>


<?PHP

    $prpttype="PRS_";
    $pdivisi="PRS_";
    $filtercoa="PRS_";
    
    $tgl01 = $_POST['e_tgl1'];
    
    $pperiode1 = date("Y-01", strtotime($tgl01));
    $pperiode2 = date("Y-m", strtotime($tgl01));
    
    $myperiode1 = "January ".date("Y", strtotime($tgl01));
    $myperiode2 = date("F Y", strtotime($tgl01));
    
    $ptahuninput = date("Y", strtotime($tgl01));
    $pbulaninput = date("Y-m-01", strtotime($tgl01));
    
    $pfiltersel=" ('') ";
    $pfilterdelete="";
    
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
    
    
    $pberhasilquery=false;
    include "query_proses.php";
    if ($pberhasilquery==false) goto hapusdata;
    
    if (!empty($pfilterdelete)) {
        $pfilterdelete="(".substr($pfilterdelete, 0, -1).")";
    }else{
        $pfilterdelete="('xaxaXX')";
    }
    
    
    
    $query ="DELETE FROM dbmaster.t_proses_bm_act WHERE tahun='$ptahuninput' AND kodeinput IN $pfilterdelete";//
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query ="ALTER TABLE dbmaster.t_proses_bm_act AUTO_INCREMENT = 1";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp01 SET tgltarikan=tglinput WHERE IFNULL(tgltarikan,'')='' OR IFNULL(tgltarikan,'0000-00-00')='0000-00-00'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query ="INSERT INTO dbmaster.t_proses_bm_act (tahun, periode, hapus_nodiv_kosong, kodeinput, idkodeinput, divisi, coa, nama_coa, tglinput, tgltrans, "
            . " karyawanid, nama_karyawan, dokterid, dokter_nama, noslip, nmrealisasi, keterangan, dpp, ppn, pph, tglfp, "
            . " idinput_pd, nodivisi, debit, kredit, saldo, jumlah1, jumlah2, "
            . " divisi_coa, coa2, nama_coa2, coa3, nama_coa3, "
            . " tgl_trans_bank, nobukti, idinput_pd1, idinput_pd2, nodivisi1, nodivisi2, pengajuan, "
            . " divisi2, icabangid, nama_cabang, areaid, nama_area, kodeid_pd, subkode_pd, pcm, kasbonsby, coa_pcm, nama_coa_pcm, "
            . " tgltarikan, nobrid_r, nobrid_n, "
            . " coa_edit, coa_nama_edit, coa_edit2, coa_nama_edit2, coa_edit3, coa_nama_edit3, divisi_edit)"
            . " SELECT '$ptahuninput' as tahun, '$pbulaninput' as periode, hapus_nodiv_kosong, kodeinput, idkodeinput, divisi, coa, nama_coa, tglinput, tgltrans, "
            . " karyawanid, nama_karyawan, dokterid, dokter_nama, noslip, nmrealisasi, keterangan, dpp, ppn, pph, tglfp, "
            . " idinput_pd, nodivisi, debit, kredit, saldo, jumlah1, jumlah2, "
            . " divisi_coa, coa2, nama_coa2, coa3, nama_coa3, "
            . " tgl_trans_bank, nobukti, idinput_pd1, idinput_pd2, nodivisi1, nodivisi2, pengajuan, "
            . " divisi2, icabangid, nama_cabang, areaid, nama_area, kodeid_pd, subkode_pd, pcm, kasbonsby, coa_pcm, nama_coa_pcm, "
            . " tgltarikan, nobrid_r, nobrid_n, "
            . " coa, nama_coa, coa2, nama_coa2, coa3, nama_coa3, divisi_coa "
            . " FROM $tmp01";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    
    $query = "SELECT * FROM $tmp01";
    $query = "create TEMPORARY table $tmp00 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
?>

<HTML>
<HEAD>
    <title>Proses Data Biaya Marketing</title>
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

<?PHP
echo "berhasil....<br/>"; goto hapusdata;
?>

<BODY class="nav-md">
<div id='n_content'>
    
    <center><div class='h1judul'>Proses Data Biaya Marketing</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <tr><td>Periode</td><td>:</td><td><?PHP echo "<b>$myperiode1 s/d. $myperiode2</b>"; ?></td></tr>
            <tr class='miring text2'><td>view date</td><td>:</td><td><?PHP echo "$printdate"; ?></td></tr>
        </table>
    </div>
    <div class="clearfix"></div>
    <hr/>
    
    
    
        <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
            <thead>
                <tr>
                <th align="center" nowrap>Divisi</th>
                <th align="center" nowrap>Date</th>
                <th align="center" nowrap>Bukti</th>
                <th align="center" nowrap>KODE</th>
                <th align="center" nowrap>PERKIRAAN</th>
                <th align="center" nowrap>Customer</th>
                <th align="center" nowrap>No. Slip</th>
                <th align="center" nowrap>Pengajuan</th>
                <th align="center" nowrap>Keterangan</th>
                <th align="center" nowrap>Realisasi/ Notes</th>
                <th align="center" nowrap>Cabang</th>
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
                        
                        $query3 = "select * from $tmp00 WHERE RTRIM(divisi)='$pdivisi' AND RTRIM(coa)='$pcoa' order by divisi, coa, tgltrans, nobukti, pengajuan";
                        $tampil3=mysqli_query($cnmy, $query3);
                        while ($row3= mysqli_fetch_array($tampil3)) {
                            $ptgltrans = date("d/m/Y", strtotime($row3['tgltrans']));
                            
                            $pbrid=$row3['idkodeinput'];
                            $pbukti=$row3['nobukti'];
                            $pcoa=$row3['coa'];
                            $pcoanama=$row3['nama_coa'];
                            $pidinput=$row3['idkodeinput'];
                            $pdokternm=$row3['dokter_nama'];
                            $pnoslip=$row3['noslip'];
                            $ppengajuan=$row3['pengajuan'];
                            $pketerangan=$row3['keterangan'];
                            $pnmrealisasi=$row3['nmrealisasi'];
                            $pnodivisi=$row3['nodivisi'];
                            $pnmcab=$row3['nama_cabang'];
                            
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
                            if (!empty($pdivost) AND $pkdinput=="N") {
                                $pdanaminta=$row3['rincian'];
                                $pdanaminta=number_format($pdanaminta,0,",",",");
                            
                                $idivisi="";
                                $ptgltrans="";
                                $pbukti="";
                                $pcoa="";
                                $pcoanama="";
                                $ppengajuan=$pdivost;
                            }
                            
                            
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
                                echo "<td >$pnmrealisasi</td>";
                                echo "<td >$pnmcab</td>";
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
                        
                        echo "<tr>";
                        echo "<td></td>";
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
                        echo "<td></td>";
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
                        echo "<td></td>";
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
                    echo "<td></td>";
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
                    echo "<td></td>";
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
                echo "<td></td>";
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
                echo "<td></td>";
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
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp09");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp10");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp11");
    mysqli_close($cnmy);
?>

<?PHP
queryfunction:
?>