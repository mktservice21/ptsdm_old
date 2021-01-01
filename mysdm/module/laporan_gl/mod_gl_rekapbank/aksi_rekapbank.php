<?PHP
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
        header("Content-Disposition: attachment; filename=REKAP BANK.xls");
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
    
    
    $periodesdhcls= date("Ym", strtotime($tgl01));
    $mygambarup="";
    $namapengaju=$_SESSION['USERID'];
    $now=date("mdYhis");
    $psudah_closing=false;
    $query = "select bulan, gambar from dbmaster.t_bank_saldo WHERE DATE_FORMAT(bulan,'%Y%m')='$periodesdhcls'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $psudah_closing=true;
        
        
        if ($ppilihrpt!="excel") {
            $nx= mysqli_fetch_array($tampil);
            $ngambarup=$nx['gambar'];
            if (!empty($ngambarup)) {
                $data="data:".$ngambarup;
                $data=str_replace(' ','+',$data);
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);
                $mygambarup="img_".$now."BNK_.png";
                file_put_contents('images/tanda_tangan_base64/'.$mygambarup, $data);
            }
        }
        
        
    }
    

    //MARSIS
    if ($_SESSION['GROUP']=="28") {
        $p_saldo_awal=0;
        mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE LEFT(idinputbank,2) IN ('BN', 'SV')");
        mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE IFNULL(divisi,'') IN ('OTC')");
    }
    
    //SPV FIN OTC
    if ($_SESSION['GROUP']=="26") {
        $p_saldo_awal=0;
        mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE LEFT(idinputbank,2) IN ('BN', 'OT')");
        mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE IFNULL(divisi,'') NOT IN ('OTC')");
    }
	
	
	
    $puserid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp0x =" dbtemp.tmplapbnkpl0x_".$puserid."_$now ";
    
    $query = "select * from $tmp01 WHERE CONCAT(kodeid,subkode) ='221' and stsinput='K'";
    $query = "create TEMPORARY table $tmp0x ($query)";
    mysqli_query($cnmy, $query); 
    //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 a JOIN $tmp0x b on IFNULL(a.nobukti,'')=IFNULL(b.nobukti,'') SET a.parentidbank='LKTRF' WHERE CONCAT(a.kodeid,a.subkode) ='229' and a.stsinput='K' AND IFNULL(a.parentidbank,'')=''";
    mysqli_query($cnmy, $query); 
    //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp0x");
	
	
?>
    


<html>
<head>
    <title>Laporan Saldo BCA  PT SDM – Jakarta</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Mei 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <link href="css/laporanbaru.css" rel="stylesheet">
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        
        <!-- Bootstrap -->
        <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    
        <!--
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
        -->
		
		
		
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
                    echo "<tr><td colspan=5 width='150px'><b>Laporan Saldo BCA  PT SDM – Jakarta $pilih_bulan_</b></td></tr>";
                }else{
                    echo "<tr><td width='150px'><b>Laporan Saldo BCA  PT SDM – Jakarta $pilih_bulan_</b></td></tr>";
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
            
            
            $no=1;
            $ptotal=0;
            $ptotal_k=0;
            $c_sudah=false;
            //$query = "select distinct nomor, nodivisi FROM $tmp01 order by nomor, nodivisi";
            //$tampil1=mysqli_query($cnmy, $query);
            //while ($row1= mysqli_fetch_array($tampil1)) {
                //$pnospd1 = $row1['nomor'];
                //$pnodivisi1 = $row1['nodivisi'];
                //$c_sudah=false;
                //WHERE nomor='$pnospd1' AND nodivisi='$pnodivisi1' 
                $query = "select * FROM $tmp01 order by tanggal, nobukti, nomor, divisi, nodivisi, stsinput, kodeid, idinputbank";
                $tampil=mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    
                    $ptgltrans =date("d-M-Y", strtotime($row['tanggal']));
                    $pidinputbank = $row['idinputbank'];
                    $pbukti = $row['nobukti'];
                    
                    $pparentidipil = TRIM($row['parentidbank']);
                    $pidinputdari=substr($pidinputbank,0,2);

                    $pcoa = $row['coa4'];
                    $pnmcoa = $row['NAMA4'];
                    $pdivisi = $row['divisi'];
                    
                    $pstsinput = $row['stsinput'];
                    $pkodeid = $row['kodeid'];
                    $psubkode = $row["subkode"];
                    $psubnamakode = $row["subnama"];
                    
                    $pnamakode = "Bank";
                    if ($psubkode=="29") {
                        
                    }else{
                        if ($pkodeid=="1") $pnamakode = "Advance";
                        if ($pkodeid=="2") $pnamakode = "Klaim";
                    }
                    
                    if ($pkodeid!="5") {
                        //$pnamakode=$psubnamakode;
                    }
                    
                    if (empty($pdivisi) AND $pstsinput!="M") $pdivisi = "ETHICAL";
                    
                    
                    $pstatus = $row['sts'];
                    $pnospd = $row['nomor'];
                    
                    $pnket = $row['nket'];
                    $pnodivisi = $row['nodivisi'];
                    if ($pnket=="1") $pnodivisi="";
                    
                    $pketerangan = $row['keterangan'];
                    
                    $pjml_md = $row['mintadana'];
                    $pjumlah = $row['debit'];
                    $pjmlkredit = $row['kredit'];
                    
                    $ptotal_k=(double)$ptotal_k+(double)$pjmlkredit;
                    $ptotal=(double)$ptotal+(double)$pjumlah;
                    
                    $p_saldo_awal=(double)$p_saldo_awal+(double)$pjumlah-(double)$pjmlkredit;
                    
                    
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
                    
                    /*
                    $pjmlkredit = $row['kredit'];
                    if ($c_sudah==true){
                        $pjmlkredit="";
                    }else{
                        $ptotal_k=(double)$ptotal_k+(double)$pjmlkredit;
                        $pjmlkredit=number_format($pjmlkredit,0,",",",");
                    }
                    if ($pjmlkredit=="0") $pjmlkredit="";
                    */
                    
                    $pjml_md=number_format($pjml_md,0,",",",");
                    $pjumlah=number_format($pjumlah,0,",",",");
                    $pjmlkredit=number_format($pjmlkredit,0,",",",");
                    $p_saldo=number_format($p_saldo_awal,0,",",",");
                    
                    if ($pstsinput=="N") {
                        $p_saldo="";
                    }
                    
                    if ($pjml_md=="0") $pjml_md="";
                    if ($pjumlah=="0") $pjumlah="";
                    if ($pjmlkredit=="0") $pjmlkredit="";
                    
                    $psudahtransfer = $row["sudah_trans"];
                    $nadd_trans=$pnodivisi;
                    if ($ppilihrpt!="excel" AND ($pstsinput=="K" OR $pstsinput=="T") AND $psubkode!="29" AND $psudahtransfer!="Y") {
                        if (!empty($pnodivisi)) {
                            $nadd_trans="<button type='button' class='btn btn-default btn-xs' data-toggle='modal' data-target='#myModal' onClick=\"TambahDataInput('$pidinputbank')\">$pnodivisi</button>";
                        }else{
                            if ($ppilihrpt!="excel" AND ($psubkode=="25" OR $psubkode=="37") AND ($pstsinput=="K" OR $pstsinput=="T")) {
                                $nadd_trans="<button type='button' class='btn btn-default btn-xs' data-toggle='modal' data-target='#myModal' onClick=\"TambahDataInput('$pidinputbank')\">Transfer</button>";
                            }
                        }
                    }else{
                    }
					
                    if ($psudah_closing==true) $nadd_trans=$pnodivisi;
					
					
                    if ( ( ($pkodeid=="2" AND $psubkode=="21") OR $pparentidipil=="LKTRF" ) AND $pstsinput=="K") {
                        $pnospd="";
                    }
                    
                    if ($pidinputdari=="OT" OR $pidinputdari=="SV") {
                        $nadd_trans="";
                    }
					
					
                    echo "<tr>";
                    echo "<td nowrap>$ptgltrans</td>";
                    echo "<td nowrap>$pbukti</td>";
                    echo "<td nowrap>$pcoa</td>";
                    echo "<td nowrap>$pnmcoa</td>";
                    echo "<td nowrap>$pnamakode</td>";
                    echo "<td nowrap>$pnospd</td>";
                    echo "<td nowrap>$pdivisi</td>";
                    echo "<td>$pketerangan</td>";
                    echo "<td nowrap>$nadd_trans</td>";
                    echo "<td nowrap align='right'></td>";
                    echo "<td nowrap align='right'>$pjml_md</td>";
                    echo "<td nowrap align='right'>$pjumlah</td>";
                    echo "<td nowrap align='right'>$pjmlkredit</td>";
                    echo "<td nowrap align='right'>$p_saldo</td>";
                    echo "</tr>";

                    $c_sudah=true;
                    $no++;
                }            
            //}
            
                $ptotal=number_format($ptotal,0,",",",");
                $ptotal_k=number_format($ptotal_k,0,",",",");
                $p_saldo_awal=number_format($p_saldo_awal,0,",",",");
                
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
                echo "<td nowrap align='right'><b></b></td>";
                echo "<td nowrap align='right'><b>$ptotal</b></td>";
                echo "<td nowrap align='right'><b>$ptotal_k</b></td>";
                echo "<td nowrap align='right'><b>$p_saldo_awal</b></td>";
                echo "</tr>";
        ?>
        </tbody>
    </table>
    <br/>&nbsp;<br/>&nbsp;
    
    <?PHP
    if ($ppilihrpt!="excel") {
        if (!empty($mygambarup)) {
            echo "<img class='imgzoomx' src='images/tanda_tangan_base64/$mygambarup' height='500' width='500' class='img-thumnail'>";
            echo "<br/>&nbsp;<br/>&nbsp;";
        }
    }
    
    
    hapusdata:
        mysqli_query($cnmy, "DROP TABLE $tmp01");
        
        mysqli_close($cnmy);
    ?>

</div>
            


    <?PHP if ($ppilihrpt!="excel") { ?>

        
        <!-- Bootstrap -->
        <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    
        <!-- Datatables -->
		
		<!--
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
		-->
		
		
        <style>
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
        
        
        function TambahDataInput(eidbank){
            $.ajax({
                type:"post",
                url:"module/mod_br_danabank/tambah_trans_bank.php?module=viewisibankspdall",
                data:"uidbank="+eidbank,
                success:function(data){
                    $("#myModal").html(data);
                }
            });
        }
    
    
    
    </script>

</html>