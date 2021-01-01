<?PHP
    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=Realisasi Dana.xls");
    }
    
    $nmodule=$_GET['module'];
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;

    
    $pses_grpuser=$_SESSION['GROUP'];
    $pses_divisi=$_SESSION['DIVISI'];
    $pses_idcard=$_SESSION['IDCARD'];

    if ($nmodule=="brdanabank") {
        $tgl01=$_POST['e_periode01'];
        $tgl02=$_POST['e_periode02'];
    }else{
        $tgl01=$_POST['bulan1'];
        $periode1= date("Ym", strtotime($tgl01));
    }

    $pilih_bulan_=date("F Y", strtotime($tgl01));
    
    
    $nkodeneksi="config/koneksimysqli.php";
    include("module/mod_br_danabank/query_saldobank.php");
    $tmp01=seleksi_query_bank($nkodeneksi, $tgl01);
    if ($tmp01==false) goto hapusdata;
    
    //saldo awal dari bulan sebelumnya
    $p_saldo_awal="0";
    $sql = "select saldoawal from $tmp01 WHERE idinputbank='SAWAL'";
    $tampil= mysqli_query($cnmy, $sql);
    $nt= mysqli_fetch_array($tampil);
    $p_saldo_awal=$nt['saldoawal'];
    if (empty($p_saldo_awal)) $p_saldo_awal=0;
    
    $query = "DELETE FROM $tmp01 WHERE idinputbank='SAWAL'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $now=date("mdYhis");
    $puserid=$_SESSION['USERID'];
    $tmp02 =" dbtemp.RCFRLDANA02_".$puserid."_$now ";
    
    $query="select distinct stsinput, nobukti, coa4, NAMA4, tanggal, nomor, jumlah, mintadana, debit, kredit, "
            . " CAST('' as CHAR(50)) as nodivisi, CAST('' as CHAR(5)) as divisi,"
            . " CAST('' as CHAR(5)) as kodeid, CAST('' as CHAR(5)) as subkode,"
            . " CAST('' as CHAR(100)) as nama_kode, CAST('' as CHAR(100)) as subnama,"
            . " CAST('' as CHAR(5)) as igroup "
            . " from $tmp01 "
            . " where IFNULL(nomor,'')<>'' and IFNULL(stsinput,'')='M' ORDER BY tanggal";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "INSERT INTO $tmp02 (stsinput, nobukti, coa4, NAMA4, tanggal, nomor, jumlah, mintadana, debit, kredit, nodivisi, divisi, "
            . " kodeid, subkode, subnama, igroup)"
            . " select distinct stsinput, nobukti, coa4, NAMA4, tanggal, nomor, jumlah, mintadana, debit, kredit, nodivisi, divisi,"
            . " kodeid, subkode, subnama, igroup "
            . " from $tmp01 where IFNULL(nomor,'')<>'' and IFNULL(stsinput,'')='N' ORDER BY tanggal"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "DELETE FROM $tmp02 WHERE nomor <>'076/UM-JKT/IX/19'";
    //mysqli_query($cnmy, $query);
    //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //goto testdata;
    //goto hapusdata;
?>
    


<html>
<head>
    <title>Realisasi Dana PT SDM – Jakarta</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Mei 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <link href="css/laporanbaru.css" rel="stylesheet">
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        
        <!-- Bootstrap -->
        <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    
        <!-- Datatables -->
        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">
    
        <!-- Datatables -->
        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

        <!-- Datatables -->
        <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <!-- jQuery -->
        <script src="vendors/jquery/dist/jquery.min.js"></script>
        
        
    <?PHP } ?>
    
</head>



<body class="nav-md">

<div class='modal fade' id='myModal' role='dialog'></div>

    
<?PHP if ($ppilihrpt!="excel") { ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?PHP } ?>
    
<div id='n_content'>

    
    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <?PHP if ($ppilihrpt=="excel") {
                    echo "<tr><td colspan=5 width='150px'><b>Realisasi Dana PT SDM – Jakarta $pilih_bulan_</b></td></tr>";
                }else{
                    echo "<tr><td width='150px'><b>Realisasi Dana PT SDM – Jakarta $pilih_bulan_</b></td></tr>";
                }
                ?>
            </table>
        </div>
        <div id="isikanan">
            
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
    
    
    <table id='datatable2' class='table table-striped table-bordered' width="100%" border="1px solid black">
        <thead>
            <tr>
            <th align="center">Date</th>
            <th align="center">Bukti</th>
            <th align="center">KODE</th>
            <th align="center">PERKIRAAN</th>
            <th align="center">Jenis</th>
            <th align="center">Surat Dana</th>
            <th align="center">Pengajuan</th>
            <th align="center">Keterangan</th>
            <th align="center">No. Divisi</th>
            <th align="center">Selisih</th>
            <th align="center">Minta Dana</th>
            <th align="center">Debit</th>
            <th align="center">Credit</th>
            <th align="center">Saldo</th>
            </tr>
        </thead>
        <tbody>
        <?PHP
            
            $p_saldo=number_format($p_saldo_awal,0,",",",");

            echo "<tr>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap>Saldo</td>";
            echo "<td nowrap></td>";
            echo "<td nowrap align='right'><b></b></td>";
            echo "<td nowrap align='right'><b></b></td>";
            echo "<td nowrap align='right'><b></b></td>";
            echo "<td nowrap align='right'><b></b></td>";
            echo "<td nowrap align='right'><b>$p_saldo</b></td>";
            echo "</tr>";
            
            $n_tot_saldo_debitspd=0;
            $no=1;
            $query = "select * FROM $tmp02 WHERE stsinput='M' order by tanggal";
            $tampil1=mysqli_query($cnmy, $query);
            while ($row1= mysqli_fetch_array($tampil1)) {
                $ptgltrans =date("d-M-Y", strtotime($row1['tanggal']));
                $pbukti = $row1['nobukti'];
                $pnospd = $row1['nomor'];
                $pcoa = $row1['coa4'];
                $pnmcoa = $row1['NAMA4'];
                
                $pnamakode = "Bank";
                $ppengajuan = "SBY";
                
                $pjml_md = $row1['mintadana'];
                $pdebit = $row1['debit'];
                $pkredit = $row1['kredit'];
                
                $n_tot_saldo_debitspd=$pdebit;
                
                $pdebit=number_format($pdebit,0,",",",");
                
                echo "<tr>";
                echo "<td nowrap>$ptgltrans</td>";
                echo "<td nowrap>$pbukti</td>";
                echo "<td nowrap>$pcoa</td>";
                echo "<td nowrap>$pnmcoa</td>";
                echo "<td nowrap>$pnamakode</td>";
                echo "<td nowrap>$pnospd</td>";
                echo "<td nowrap>$ppengajuan</td>";
                echo "<td nowrap></td>";//keterangan
                echo "<td nowrap></td>";
                echo "<td nowrap align='right'></td>";//slisih
                echo "<td nowrap align='right'></td>";
                echo "<td nowrap align='right'>$pdebit</td>";
                echo "<td nowrap align='right'></td>";
                echo "<td nowrap align='right'></td>";
                echo "</tr>";
                
                $n_tot_deb=0; $n_tot_ker=0; $n_tot_minta=0;
                
                $pmintadana_d=0;
                $query = "select * FROM $tmp02 WHERE nomor='$pnospd' AND stsinput='N' order by tanggal, nobukti, nomor, divisi, nodivisi, stsinput, kodeid";
                $tampil2=mysqli_query($cnmy, $query);
                while ($row2= mysqli_fetch_array($tampil2)) {
                    $ptgltrans =date("d-M-Y", strtotime($row2['tanggal']));
                    $pbukti = $row2['nobukti'];
                    $pnospd = $row2['nomor'];
                    $pnodivisi = $row2['nodivisi'];
                    $pcoa = $row2['coa4'];
                    $pnmcoa = $row2['NAMA4'];
                    $pdivisi = $row2['divisi'];
                    $pkodeid = $row2['kodeid'];
                    $psubkode = $row2['subkode'];
                    $pnamasub = $row2['subnama'];
                    $pigroup = $row2['igroup'];
                    
                    $n_nomordivisi=$pnodivisi;
                    if ($pigroup=="3" OR $pkodeid=="3") $n_nomordivisi="";
                    
                    $pketerangan="";
                    if ($pkodeid=="3") $pketerangan="Adjustment";
                    
                    $pnamakode = "Bank";
                    $ppengajuan = $pdivisi;
                    if (empty($pdivisi)) $ppengajuan="ETHICAL";
                    
                    $pjml_md = $row2['mintadana'];
                    $pdebit = $row2['debit'];
                    $pkredit = $row2['kredit'];
                    
                    $n_tot_minta=(double)$n_tot_minta+(double)$pjml_md;
                    
                    $pmintadana_d=$pjml_md;
                    $pjml_md=number_format($pjml_md,0,",",",");

                    echo "<tr>";
                    echo "<td nowrap>$ptgltrans</td>";
                    echo "<td nowrap>$pbukti</td>";
                    echo "<td nowrap>$pcoa</td>";
                    echo "<td nowrap>$pnmcoa</td>";
                    echo "<td nowrap>$pnamakode</td>";
                    echo "<td nowrap>$pnospd</td>";
                    echo "<td nowrap>$ppengajuan</td>";
                    echo "<td nowrap>$pketerangan</td>";//keterangan
                    echo "<td nowrap>$n_nomordivisi</td>";
                    echo "<td nowrap align='right'></td>";//slisih
                    echo "<td nowrap align='right'>$pjml_md</td>";
                    echo "<td nowrap align='right'></td>";
                    echo "<td nowrap align='right'></td>";
                    echo "<td nowrap align='right'></td>";
                    echo "</tr>";
                    
                    $nt_no=1;
                    //rincian debit kredit
                    $query = "select * FROM $tmp01 WHERE nomor='$pnospd' AND nodivisi='$pnodivisi' AND stsinput IN ('D','K', 'T') order by tanggal, nobukti, nomor, divisi, nodivisi, stsinput, kodeid, idinputbank";
                    $tampil=mysqli_query($cnmy, $query);
                    $ketemu= mysqli_num_rows($tampil);
                    if ($ketemu>0) {
                        
                        while ($row= mysqli_fetch_array($tampil)) {
                            
                            $ptgltrans =date("d-M-Y", strtotime($row['tanggal']));
                            $pbukti = $row['nobukti'];
                            $pnospd = $row['nomor'];
                            $pnodivisi = $row['nodivisi'];
                            $pcoa = $row['coa4'];
                            $pnmcoa = $row['NAMA4'];
                            $pdivisi = $row['divisi'];
                            $pkodeid = $row['kodeid'];
                            $psubkode = $row['subkode'];
                            $psubnamakode = $row['subnama'];
                            $pigroup = $row['igroup'];
                            $pinmgroup = $row['inama'];
                            $pstsinput = $row['stsinput'];
                            $pstatus = $row['sts']; 

                            $n_nomordivisi=$pnodivisi;
                            if ($pigroup=="3" OR $pkodeid=="3") $n_nomordivisi="";
                    
                            $pketerangan = $row['keterangan'];
                            if ($pkodeid=="3") {
                                $pketerangan="Adjustment";
                            }
                            
                            $pnamakode = "Bank";
                            //if ($pkodeid=="1") $pnamakode = "Advance";
                            //elseif ($pkodeid=="2") $pnamakode = "Klaim";
                            //elseif ($pkodeid=="3") $pnamakode = "Adjustment";
                            
                            if ($pigroup=="1" OR $pigroup=="2") $pnamakode = $pinmgroup;
                            elseif ($pigroup=="3") {
                                if ($ppilihrpt=="excel") $pnamakode = "Klaim - ".$pinmgroup;
                                else $pnamakode = "Klaim -<br/>".$pinmgroup;
                            }
                            
                            $ppengajuan = $pdivisi;
                            if (empty($pdivisi)) $ppengajuan="ETHICAL";

                            $pjml_md = $row['mintadana'];
                            $pdebit = $row['debit'];
                            $pkredit = $row['kredit'];
                            
                            $n_tot_deb=(double)$n_tot_deb+(double)$pdebit; 
                            $n_tot_ker=(double)$n_tot_ker+(double)$pkredit;
                            
                            $pmintadana_d=(double)$pmintadana_d+(double)$pdebit-(double)$pkredit;
                            $p_jml_saldo=$pmintadana_d;
                            
                            $pjml_md=number_format($pjml_md,0,",",",");
                            $pdebit=number_format($pdebit,0,",",",");
                            $pkredit=number_format($pkredit,0,",",",");
                            $p_jml_saldo=number_format($p_jml_saldo,0,",",",");
                            
                            if ((double)$p_jml_saldo==0) $p_jml_saldo="";
                            
                            if ($pstsinput=="D") {
                                $pjml_md="";
                                $pkredit="";
                            }elseif ($pstsinput=="K" OR $pstsinput=="T") {
                                $pjml_md="";
                                $pdebit="";
                            }else{
                                
                            }
                            
                            
                            $nk_rtr="retur";
                            if ($pstsinput=="T") {
                                $nk_rtr="transfer";
                            }

                            if (empty($pnospd) AND $pstatus=="1") {
                                $pnospd= "non surat";
                            }else{
                                if ($pstatus=="2") {
                                    if (!empty($pketerangan))
                                        $pketerangan="$nk_rtr, ".$pketerangan;
                                    else
                                        $pketerangan="$nk_rtr";
                                }
                            }

                            $pnobridinput = $row['brid'];
                            $pnoslip = $row["noslip"];
                            $paktivitasbr = $row["aktivitas1"];
                            $pnmuser = $row["nama_user"];
                            $pnmrealisasi = $row["realisasi"];
                            $pnmdokter = $row["customer"];



                            $nket_brinput="";
                            if (!empty($pnobridinput)) {
                                if (!empty($pnoslip)) $pnoslip="No Slip : $pnoslip";
                                if (empty($pnoslip)) $pnoslip="IDBR : $pnobridinput";

                                if (!empty($pnmdokter)) $pnmdokter=", Dok/Cust : $pnmdokter";
                                if (!empty($pnmrealisasi)) {
                                    $pnmrealisasi="<br/>Realisasi : $pnmrealisasi";
                                    if (!empty($paktivitasbr)) $pnmrealisasi .=", Ket : $paktivitasbr";
                                }else{
                                    if (!empty($paktivitasbr)) $pnmrealisasi .="<br/>Ket : $paktivitasbr";
                                }

                                $nket_brinput="$pnoslip $pnmdokter $pnmrealisasi";
                            }

                            if (!empty($pketerangan) AND !empty($nket_brinput)) {
                                $pketerangan .="<br/>".$nket_brinput;
                            }else{
                                if (!empty($nket_brinput)) $pketerangan =$nket_brinput;
                            }

                            if ($psubkode=="29") {
                                $pketerangan=$psubnamakode;
                            }else{
                                if (empty($pketerangan)) $pketerangan=$psubnamakode;
                            }
                    
                            echo "<tr>";
                            echo "<td nowrap>$ptgltrans</td>";
                            echo "<td nowrap>$pbukti</td>";
                            echo "<td nowrap>$pcoa</td>";
                            echo "<td nowrap>$pnmcoa</td>";
                            echo "<td nowrap>$pnamakode</td>";
                            echo "<td nowrap>$pnospd</td>";
                            echo "<td nowrap>$ppengajuan</td>";
                            echo "<td>$pketerangan</td>";//keterangan
                            echo "<td nowrap>$n_nomordivisi</td>";
                            echo "<td nowrap align='right'></td>";//slisih
                            echo "<td nowrap align='right'>$pjml_md</td>";
                            echo "<td nowrap align='right'>$pdebit</td>";
                            echo "<td nowrap align='right'>$pkredit</td>";
                            
                            if ((double)$nt_no==(double)$ketemu) echo "<td nowrap align='right'><b>$p_jml_saldo</b></td>";
                            else echo "<td nowrap align='right'><b></b></td>";
                            
                            echo "</tr>";
                            
                            $nt_no++;
                            
                        }
                        
                    }
                    

                }
                
                $n_tot_deb=(double)$n_tot_deb+(double)$n_tot_saldo_debitspd;
                $n_tot_saldo=(double)$n_tot_deb-(double)$n_tot_ker;
                
                $n_tot_minta=number_format($n_tot_minta,0,",",",");
                $n_tot_deb=number_format($n_tot_deb,0,",",",");
                $n_tot_ker=number_format($n_tot_ker,0,",",",");
                $n_tot_saldo=number_format($n_tot_saldo,0,",",",");
                
                
                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap align='right'><b></b></td>";
                echo "<td nowrap align='right'><b>$n_tot_minta</b></td>";
                echo "<td nowrap align='right'><b>$n_tot_deb</b></td>";
                echo "<td nowrap align='right'><b>$n_tot_ker</b></td>";
                echo "<td nowrap align='right'><b>$n_tot_saldo</b></td>";
                echo "</tr>";
                
                echo "<tr>";
                echo "<td colspan='14'><b>&nbsp;</b></td>";
                if ($ppilihrpt!="excel"){
                    echo "<td class='divnone'></td><td class='divnone'></td><td class='divnone'></td><td class='divnone'></td><td class='divnone'></td>";
                    echo "<td class='divnone'></td><td class='divnone'></td><td class='divnone'></td><td class='divnone'></td><td class='divnone'></td>";
                    echo "<td class='divnone'></td><td class='divnone'></td><td class='divnone'></td>";
                }
                echo "</tr>";
                
            
            }
            
        ?>
        </tbody>
    </table>
    <br/>&nbsp;<br/>&nbsp;
    
    <?PHP
    
    
    hapusdata:
        mysqli_query($cnmy, "DROP TABLE $tmp01");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
        
    testdata:
        mysqli_close($cnmy);
    ?>

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

            #n_content {
                color:#000;
                font-family: "Arial";
                margin: 20px;
                /*overflow-x:auto;*/
            }
        </style>

        <style>
            .divnone {
                display: none;
            }
            #datatable2, #datatable3 {
                color:#000;
                font-family: "Arial";
            }
            #datatable2 th, #datatable3 th {
                font-size: 12px;
            }
            #datatable2 td, #datatable3 td { 
                font-size: 11px;
            }
        </style>
        
    <?PHP }else{ ?>
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
            #datatable2, #datatable3 {
                font-family: Georgia, serif;
                margin-left:10px;
                margin-right:10px;
            }
            #datatable2 th, #datatable2 td, #datatable3 th, #datatable3 td {
                padding: 4px;
            }
            #datatable2 thead, #datatable3 thead{
                background-color:#cccccc; 
                font-size: 12px;
            }
            #datatable2 tbody, #datatable3 tbody{
                font-size: 11px;
            }
        </style>
    <?PHP } ?>
    
</body>

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
    
    
        $(document).ready(function() {
            var table = $('#datatable2, #datatable3').DataTable({
                fixedHeader: true,
                "ordering": false,
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": -1,
                "order": [[ 0, "asc" ]],
                "columnDefs": [
                    { "visible": false },
                    { className: "text-right", "targets": [9,10,11,12,13] },//right
                    { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,8,9,10,11,12,13] }//nowrap

                ],
                bFilter: true, bInfo: true, "bLengthChange": true, "bLengthChange": true,
                "bPaginate": true
            } );

        } );
    </script>

</html>