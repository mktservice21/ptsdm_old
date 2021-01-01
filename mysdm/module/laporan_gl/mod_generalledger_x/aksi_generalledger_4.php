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
    
    
    
    
    $pfilterselpil="";
    //BR ETHICAL A
    if (!empty($pbreth)) {
        $pfilterselpil .= "'A',";
    }
    
    //klaimdiscount B
    if (!empty($pklaim)) {
        $pfilterselpil .= "'B',";
    }
    
    //KAS KASBON C & D
    if (!empty($pkas)) {
        $pfilterselpil .= "'C','D',";
    }
    
    //BROTC E
    if (!empty($pbrotc)) {
        $pfilterselpil .= "'E',";
    }
    
    //RUTIN LUAR KOTA F rutin G lk
    if (!empty($prutin) OR !empty($pblk)) {
        $pfilterselpil .= "'F','G',";
    }
    
    //CA H
    //if (!empty($prutin) OR !empty($pblk)) {
    //    $pfilterselpil .= "'H',";
        
    //}
    
    //BM biaya marketing surabaya I & J
    if (!empty($pbmsby)) {
        $pfilterselpil .= "'I','J',";
    }
    
    //insentif incentive K
    if (!empty($ppilinsen)) {
        $pfilterselpil .= "'K',";
    }
    
    //BANK L M N O P
    if (!empty($ppilbank)) {
        $pfilterselpil .= "'L','M','N','O','P',";
    }
    
    
    if (!empty($pfilterselpil)) {
        $pfilterselpil="(".substr($pfilterselpil, 0, -1).")";
    }else{
        $pfilterselpil="('xaxaXX')";
    }
    
    $query = "select noidauto, kodeinput, idkodeinput as idinput, divisi, tgltrans, nobukti as bukti, "
            . " coa, nama_coa, dokter_nama as dokter, noslip, pengajuan, keterangan, "
            . " nmrealisasi, rincian as mintadana, debit, kredit, saldo, "
            . " dpp, ppn, pph, tglfp, nodivisi, divisi2, idinput_pd as idinputdiv "
            . " from dbmaster.t_proses_bm_act WHERE IFNULL(hapus_nodiv_kosong,'') <>'Y' AND DATE_FORMAT(tgltrans,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' AND "
            . " kodeinput IN $pfilterselpil ";
    if (!empty($pdivisi)) $query .=" AND divisi='$pdivisi' ";
    if (!empty($filtercoa)) $query .=" AND IFNULL(coa,'') IN $filtercoa ";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
        $query = "CREATE INDEX `norm1` ON $tmp01 (kodeinput,idinput,divisi,tgltrans,coa)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        
    
        
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