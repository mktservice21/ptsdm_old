<?php
    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=SUMMARY CF DANA JKT PER BULAN.xls");
    }
    
    $nmodule=$_GET['module'];
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;

    
    $pses_grpuser=$_SESSION['GROUP'];
    $pses_divisi=$_SESSION['DIVISI'];
    $pses_idcard=$_SESSION['IDCARD'];
    $pses_userid=$_SESSION['USERID'];
    
    $tgl01=$_POST['bulan1'];
    $periode1= date("Ym", strtotime($tgl01));
    $pilih_bulan_=date("F Y", strtotime($tgl01));
    
    $nkodeneksi="config/koneksimysqli.php";
    include("module/mod_br_danabank/query_saldobank.php");
    $tmp01=seleksi_query_bank($nkodeneksi, $tgl01);
    if ($tmp01==false) goto hapusdata;
    
    $pjmlsaldoawal=0;
    $query = "select saldoawal from $tmp01 where idinputbank='SAWAL'";
    $tampil = mysqli_query($cnmy, $query);
    $nsa= mysqli_fetch_array($tampil);
    $pjmlsaldoawal=$nsa['saldoawal'];
    
    $query = "DELETE FROM $tmp01 WHERE idinputbank='SAWAL'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 a LEFT JOIN dbmaster.t_kode_spd b on a.kodeid=b.kodeid and a.subkode=b.subkode "
            . " SET a.kodeid='2', a.subkode='32' WHERE IFNULL(b.nama,'')=''";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $now=date("mdYhis");
    $tmp02 =" dbtemp.RPTSUMCF02_".$pses_userid."_$now ";
    
    $query ="CREATE TEMPORARY TABLE $tmp02 (
        nourut INT(4) PRIMARY KEY NOT NULL AUTO_INCREMENT,
        noid VARCHAR(5),
        noid2 VARCHAR(1),
        ikode VARCHAR(5),
        nama VARCHAR(100),
        saldoawal VARCHAR(100),
        rincian DECIMAL(20,2),
        advance DECIMAL(20,2),
        kasbon DECIMAL(20,2),
        klaim DECIMAL(20,2),
        adj DECIMAL(20,2),
        jumlah DECIMAL(20,2)
        )";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "INSERT INTO $tmp02(noid, noid2, ikode, nama)VALUES"
            . "('I', '', '00001', 'Saldo Awal'),"
            . "('II', '', '00002', 'Transfer Surabaya (SPD)'),"
            . "('III', '', '00003', 'Pengeluaran'),"
            . "('', 'A', '00004', 'Advance'),"
            . "('', '', '00005', 'SPD'),"
            . "('', '', '00006', 'Retur Advance'),"
            . "('', 'B', '00007', 'Kasbon Surabaya'),"
            . "('', '', '00008', 'SPD'),"
            . "('', '', '00009', 'Retur Kasbon SBY'),"
            . "('', 'C', '00010', 'Klaim'),"
            . "('', '', '00011', 'PC 1,1M'),"
            . "('', '', '00012', 'Piutang BCA JKT'),"
            . "('', '', '00013', 'Retur Klaim'),"
            . "('', 'D', '00014', 'Adjustment'),"
            . "('IV', '', '00015', 'SALDO AKHIR')";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
    
    //SALDOAWAL
    $query="UPDATE $tmp02 SET saldoawal=$pjmlsaldoawal WHERE ikode='00001'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
    
    $query="UPDATE $tmp02 SET jumlah=saldoawal "
            . " WHERE ikode='00001'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //END SALDOAWAL
    
    //Permitaan dana SBY
    $query="UPDATE $tmp02 SET advance=(SELECT SUM(mintadana) as jml FROM $tmp01 WHERE stsinput='N' AND kodeid='1') "
            . " WHERE ikode='00002'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
    
    $query="UPDATE $tmp02 SET kasbon=(SELECT SUM(mintadana) as jml FROM $tmp01 WHERE stsinput='N' AND kodeid='6') "
            . " WHERE ikode='00002'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
    
    $query="UPDATE $tmp02 SET klaim=(SELECT SUM(mintadana) as jml FROM $tmp01 WHERE stsinput='N' AND kodeid='2') "
            . " WHERE ikode='00002'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
    
    $query="UPDATE $tmp02 SET adj=(SELECT SUM(mintadana) as jml FROM $tmp01 WHERE stsinput='N' AND kodeid='3') "
            . " WHERE ikode='00002'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
    
    $query="UPDATE $tmp02 SET jumlah=IFNULL(advance,0)+IFNULL(kasbon,0)+IFNULL(klaim,0)+IFNULL(adj,0)"
            . " WHERE ikode='00002'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
    
    $query = "DELETE FROM $tmp01 WHERE stsinput='N' AND kodeid IN (1,6,2,3)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
    //END Permitaan dana SBY
    
    
    
    //PENGELUARAN ADVANCE
    
    $query="UPDATE $tmp02 SET advance=(SELECT SUM(IFNULL(kredit,'')-IFNULL(debit,'')) as jml FROM $tmp01 WHERE stsinput IN ('K', 'T', 'D') AND kodeid='1') "
            . " WHERE ikode='00004'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query="UPDATE $tmp02 SET jumlah=advance "
            . " WHERE ikode='00004'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //SPD
    $query="UPDATE $tmp02 SET rincian=(SELECT SUM(kredit) as jml FROM $tmp01 WHERE stsinput IN ('K') AND kodeid='1') "
            . " WHERE ikode='00005'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
    
    //RETUR
    $query="UPDATE $tmp02 SET rincian=(SELECT SUM(kredit) as jml FROM $tmp01 WHERE stsinput IN ('T') AND kodeid='1') "
            . " WHERE ikode='00006'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; } 
    
    
    //END PENGELUARAN ADVANCE
    
    //PENGELUARAN KASBON
    $query="UPDATE $tmp02 SET kasbon=(SELECT SUM(IFNULL(kredit,'')-IFNULL(debit,'')) as jml FROM $tmp01 WHERE stsinput IN ('K', 'T', 'D') AND kodeid='6') "
            . " WHERE ikode='00007'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    //SPD
    $query="UPDATE $tmp02 SET rincian=(SELECT SUM(kredit) as jml FROM $tmp01 WHERE stsinput IN ('K') AND kodeid='6') "
            . " WHERE ikode='00008'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
    
    
    $query="UPDATE $tmp02 SET jumlah=kasbon "
            . " WHERE ikode='00007'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    //END PENGELUARAN KASBON
    
    //PENGELUARAN KLAIM
    $query="UPDATE $tmp02 SET klaim=(SELECT SUM(IFNULL(kredit,'')-IFNULL(debit,'')) as jml FROM $tmp01 WHERE stsinput IN ('K', 'T', 'D') AND kodeid='2') "
            . " WHERE ikode='00010'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query="UPDATE $tmp02 SET jumlah=klaim "
            . " WHERE ikode='00010'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //PC 1,1M
    $query="UPDATE $tmp02 SET rincian=(SELECT SUM(kredit) as jml FROM $tmp01 WHERE stsinput IN ('K', 'T') AND kodeid='2' AND IFNULL(nomor,'')<>'') "
            . " WHERE ikode='00011'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //PIUTANG BCA JKT
    $query="UPDATE $tmp02 SET rincian=(SELECT SUM(kredit) as jml FROM $tmp01 WHERE stsinput IN ('K', 'T') AND kodeid='2' AND IFNULL(nomor,'')='') "
            . " WHERE ikode='00012'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //END PENGELUARAN KLAIM
    
    //PENGELUARAN ADJUSTMENT
    $query="UPDATE $tmp02 SET adj=(SELECT SUM(kredit) as jml FROM $tmp01 WHERE stsinput IN ('K', 'T') AND kodeid='3') "
            . " WHERE ikode='00014'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query="UPDATE $tmp02 SET jumlah=adj "
            . " WHERE ikode='00014'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //END PENGELUARAN ADJUSTMENT
    
    
    //DEBIT MASUK KE RETUR
    $query="UPDATE $tmp02 SET rincian=IFNULL(rincian,0)-IFNULL((SELECT SUM(debit) as jml FROM $tmp01 WHERE stsinput IN ('D') AND kodeid='1'),0)"
            . " WHERE ikode='00006'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; } 

    $query="UPDATE $tmp02 SET rincian=IFNULL(rincian,0)-IFNULL((SELECT SUM(debit) as jml FROM $tmp01 WHERE stsinput IN ('D') AND kodeid='2'),0)"
            . " WHERE ikode='00013'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; } 

    $query="UPDATE $tmp02 SET rincian=IFNULL(rincian,0)-IFNULL((SELECT SUM(debit) as jml FROM $tmp01 WHERE stsinput IN ('D') AND kodeid='6'),0)"
            . " WHERE ikode='00009'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; } 
    
    //END DEBIT MASUK KE RETUR
    
    
    
    $nsbyadv=0;
    $nsbykasbon=0;
    $nsbyklaim=0;
    $nsbyadj=0;
    $nsbyjml=0;
    
    $query = "select * from $tmp02 where ikode='00002'";
    $tampil = mysqli_query($cnmy, $query);
    while ($rs= mysqli_fetch_array($tampil)) {
        $nsbyadv=$rs['advance'];
        $nsbykasbon=$rs['kasbon'];
        $nsbyklaim=$rs['klaim'];
        $nsbyadj=$rs['adj'];
        $nsbyjml=$rs['jumlah'];
    }
    
    $pjmladv=0;
    $query = "select advance from $tmp02 where ikode='00004'";
    $tampil = mysqli_query($cnmy, $query);
    $nadv= mysqli_fetch_array($tampil);
    $pjmladv=$nadv['advance'];
    
    $pjmlkasbon=0;
    $query = "select kasbon from $tmp02 where ikode='00007'";
    $tampil = mysqli_query($cnmy, $query);
    $nks= mysqli_fetch_array($tampil);
    $pjmlkasbon=$nks['kasbon'];
    
    $pjmlkklaim=0;
    $query = "select klaim from $tmp02 where ikode='00010'";
    $tampil = mysqli_query($cnmy, $query);
    $nkl= mysqli_fetch_array($tampil);
    $pjmlkklaim=$nkl['klaim'];
    
    
    $pjmlkadj=0;
    $query = "select adj from $tmp02 where ikode='00014'";
    $tampil = mysqli_query($cnmy, $query);
    $nadj= mysqli_fetch_array($tampil);
    $pjmlkadj=$nadj['adj'];
    
    
    $nsbyadv=(DOUBLE)$nsbyadv-(DOUBLE)$pjmladv;
    $nsbykasbon=(DOUBLE)$nsbykasbon-(DOUBLE)$pjmlkasbon;
    $nsbyklaim=(DOUBLE)$nsbyklaim-(DOUBLE)$pjmlkklaim;
    $nsbyadj=(DOUBLE)$nsbyadj-(DOUBLE)$pjmlkadj;
?>
<HTML>
<HEAD>
    <title>Summary CF Dana Jakarta Per Bulan</title>
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
    
</HEAD>
<BODY>
    
<div class='modal fade' id='myModal' role='dialog'></div>
<?PHP if ($ppilihrpt!="excel") { ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?PHP } ?>
    
<div id='n_content'>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <?PHP if ($ppilihrpt=="excel") {
                    echo "<tr><td colspan=5 width='150px'><b>Summary CF Dana Jakarta $pilih_bulan_</b></td></tr>";
                }else{
                    echo "<tr><td width='150px'><b>Summary CF Dana Jakarta $pilih_bulan_</b></td></tr>";
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
            <th align="center"></th>
            <th align="center"></th>
            <th align="center">Nama</th>
            <th align="center">Saldo Awal</th>
            <th align="center">Rincian</th>
            <th align="center">Advance</th>
            <th align="center">Kasbon Surabaya</th>
            <th align="center">Klaim</th>
            <th align="center">Adjustment</th>
            <th align="center">Jumlah</th>
            </tr>
        </thead>
        <tbody>
        <?PHP
            $query = "select * from $tmp02 order by nourut";
            $tampil=mysqli_query($cnmy, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $pnoid=$row['noid'];
                $pnoid2=$row['noid2'];
                $pnama=$row['nama'];
                $pnkodeid=$row['ikode'];
                
                $psaldoawal=$row['saldoawal'];
                $princian=$row['rincian'];
                $padvance=$row['advance'];
                $pkasbon=$row['kasbon'];
                $pklaim=$row['klaim'];
                $padj=$row['adj'];
                $pjumlah=$row['jumlah'];
                
                if ($pnkodeid=="00015") {
                    $psaldoawal=$pjmlsaldoawal;
                    $padvance=$nsbyadv;
                    $pkasbon=$nsbykasbon;
                    $pklaim=$nsbyklaim;
                    $padj=$nsbyadj;
                    
                    $pjumlah=(DOUBLE)$psaldoawal+(DOUBLE)$nsbyadv+(DOUBLE)$nsbykasbon+(DOUBLE)$nsbyklaim+(DOUBLE)$nsbyadj;
                }
                
                if (!empty($psaldoawal)) $psaldoawal=number_format($psaldoawal,0,",",",");
                if (!empty($princian)) $princian=number_format($princian,0,",",",");
                if (!empty($padvance)) $padvance=number_format($padvance,0,",",",");
                if (!empty($pkasbon)) $pkasbon=number_format($pkasbon,0,",",",");
                if (!empty($pklaim)) $pklaim=number_format($pklaim,0,",",",");
                if (!empty($padj)) $padj=number_format($padj,0,",",",");
                if (!empty($pjumlah)) $pjumlah=number_format($pjumlah,0,",",",");
                
                
                echo "<tr>";
                echo "<td nowrap>$pnoid</td>";
                echo "<td nowrap>$pnoid2</td>";
                echo "<td nowrap>$pnama</td>";
                echo "<td nowrap>$psaldoawal</td>";
                echo "<td nowrap>$princian</td>";
                echo "<td nowrap>$padvance</td>";
                echo "<td nowrap>$pkasbon</td>";
                echo "<td nowrap>$pklaim</td>";
                echo "<td nowrap>$padj</td>";
                echo "<td nowrap>$pjumlah</td>";
                echo "</tr>";
                
            }
        ?>
        </tbody>
    </table>
    
    
    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
    
    
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
            var table = $('#datatable2').DataTable({
                fixedHeader: true,
                "ordering": false,
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": -1,
                "order": [[ 0, "asc" ]],
                "columnDefs": [
                    { "visible": false },
                    { className: "text-right", "targets": [3,4,5,6,7,8,9] },//right
                    { className: "text-nowrap", "targets": [0,1,2,3,4,5,6,7,8,9] }//nowrap

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
    
    
</BODY>
</HTML>


<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_close($cnmy);
?>

