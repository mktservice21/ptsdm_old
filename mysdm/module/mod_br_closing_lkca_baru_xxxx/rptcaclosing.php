<?PHP
    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REKAP DATA CA LK.xls");
    }
    
    
    $nmodule=$_GET['module'];
    
    include "config/koneksimysqli.php";
    include "config/fungsi_sql.php";
    include "config/library.php";
    $cnit=$cnmy;
    $tglnow = date("d/m/Y");
    
    //harus ada diseleksi
        $pilih_koneksi="config/koneksimysqli.php";
        $ptgl_pillih = $_POST['bulan1'];
        $stsreport = $_POST['sts_rpt'];
        $pprosid_sts = $_POST['sts_sudahprosesid'];
        $scaperiode1 = $_POST['sts_periodeca1'];
        $scaperiode2 = $_POST['sts_periodeca2'];
        $iproses_simpandata=false;
        $u_filterkaryawan="";
    //END harus ada diseleksi
    //seleksi data
    include ("seleksi_data_lk_ca.php");
    
    $pjenispilih = "1";
    
    $pigroupid="";
    $ptgl_pil01= date("Y-m-01", strtotime($ptgl_pillih));
    $ptgl_pil02= date('Y-m-01', strtotime('+1 month', strtotime($ptgl_pillih)));
    
    if ($scaperiode2=="2") $ptgl_pil02=$ptgl_pil01;
    
    $ptgl_pil_sbl= date('Y-m-01', strtotime('-1 month', strtotime($ptgl_pillih)));
    
    $m_periode1 = date("Y-m", strtotime($ptgl_pil01));
    $m_periode2 = date("Y-m", strtotime($ptgl_pil02));
    $m_periode_sbl = date("Y-m", strtotime($ptgl_pil_sbl));
    
    $perBlnThn1 = date("F Y", strtotime($ptgl_pil01));
    $perBlnThn2 = date("F Y", strtotime($ptgl_pil02));
    
    
?>

<html>
<head>
    <title>REKAP CA LK</title>
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
<?PHP if ($ppilihrpt!="excel") { ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?PHP } ?>
    

    
    
    <div id='n_content'>
        
        <div id="kotakjudul">
            <div id="isikiri">
                <table class='tjudul' width='100%'>
                    <?PHP if ($ppilihrpt=="excel") {
                        echo "<tr><td colspan=5 width='150px'><b>Rekap CA LK</b></td></tr>";
                    }else{
                        echo "<tr><td width='150px'><b>Rekap CA LK</b></td></tr>";
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
                <th width="30px" align="center" nowrap>No.</th>
                <th align="center" nowrap>NAMA</th>
                <th align="center" nowrap>DIVISI</th>
                <th align="center" nowrap>No LK</th>
                <th align="center" nowrap>Credit</th>
                <th align="center" nowrap>Saldo REAL</th>
                <th align="center" nowrap>CA<br/><?PHP echo $perBlnThn1; ?></th>
                <th align="center" nowrap>Kurang/<br/>Lebih CA</th>
                <th align="center" nowrap>Selisih</th>
                <th align="center" >CA<br/><?PHP echo $perBlnThn2; ?></th>
                <th align="center" >AR / AP<br/><?PHP echo $m_periode_sbl; ?></th>
                <th align="center" >JUML TRSF</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                    $gtotjumlah=0; $gtotca1=0; $gtotca2=0; $gtotadj=0; $gtotselisih=0; $gtottrans=0; $gtotkurlebihca1=0;
                    $no=1;
                    $query = "select distinct "
                            . " IFNULL(divisi,'') as divisi, karyawanid, nama_karyawan, 
                                IFNULL(saldo,0) saldo, IFNULL(ca1,0) ca1, IFNULL(ca2,0) ca2, 
                                IFNULL(jml_adj,0) jml_adj, IFNULL(selisih,0) selisih, 
                                IFNULL(jmltrans,0) jmltrans, IFNULL(kuranglebihca1,0) kuranglebihca1  "
                            . " from $tmp01 order by divisi, nama_karyawan, karyawanid";
                    $tampil= mysqli_query($cnit, $query);
                    while ($row= mysqli_fetch_array($tampil)) {
                        $pdivisi=$row['divisi'];
                        $pkaryawanid=$row['karyawanid'];
                        $pnmkaryawan=$row['nama_karyawan'];
                        
                        $prprutin=$row['saldo'];
                        $pca1=$row['ca1'];
                        $pca2=$row['ca2'];
                        $pjumlahadj=$row['jml_adj'];
                        $pselisih=$row['selisih'];
                        $pjmltrans=$row['jmltrans'];
                        $pjmlkuranglebih_ca1=$row['kuranglebihca1'];

                        
                        //text name tidak bisa lebih banyak, aneh ya
                        
                        $gtotjumlah=(double)$gtotjumlah+(double)$prprutin;
                        $gtotca1=(double)$gtotca1+(double)$pca1;
                        $gtotca2=(double)$gtotca2+(double)$pca2;
                        $gtotadj=(double)$gtotadj+(double)$pjumlahadj;
                        $gtotselisih=(double)$gtotselisih+(double)$pselisih;
                        $gtottrans=(double)$gtottrans+(double)$pjmltrans;
                        $gtotkurlebihca1=(double)$gtotkurlebihca1+(double)$pjmlkuranglebih_ca1;
                        
                        $prprutin=number_format($prprutin,0,",",",");
                        $pca1=number_format($pca1,0,",",",");
                        $pca2=number_format($pca2,0,",",",");
                        $pjumlahadj=number_format($pjumlahadj,0,",",",");
                        $pselisih=number_format($pselisih,0,",",",");
                        $pjmltrans=number_format($pjmltrans,0,",",",");
                        $pjmlkuranglebih_ca1=number_format($pjmlkuranglebih_ca1,0,",",",");
                        
                        
                        if ($stsreport=="C" OR $stsreport=="S") $ceklisnya="";
                            
                        
                        /*
                        $belum=false;
                        $query = "select * from $tmp01 where karyawanid='$pkaryawanid' order by idrutin";
                        $result2 = mysqli_query($cnit, $query);
                        while ($row2= mysqli_fetch_array($result2)) {
                            
                            $pnolk=$row2['idrutin'];
                            $pidca1=$row2['idca1'];
                            $pidca2=$row2['idca2'];
                            $pketerangan=$row2['keterangan'];
                            $pjumlah=number_format($row2['credit'],0,",",",");
                            $gtotjumlah=$gtotjumlah+$row2['credit'];
                            
                            if ($belum==true) {
                                echo "<tr>";
                                echo "<td></td>";
                                echo "<td></td>";
                                echo "<td></td>";

                                echo "<td nowrap>$pnolk</td>";
                                echo "<td nowrap align='right'>$pjumlah</td>";
                                echo "<td nowrap align='right'></td>";
                                echo "<td nowrap align='right'></td>";
                                echo "<td nowrap align='right'></td>";
                                echo "<td nowrap align='right'></td>";
                                echo "<td nowrap align='right'></td>";
                                echo "<td nowrap align='right'></td>";
                                echo "<td nowrap align='right'></td>";
                                echo "</tr>";

                            }else{
                                if ((double)$pjmltrans < 0) $pjmltrans=0;
                                
                                echo "<tr>";
                                echo "<td>$ceklisnya</td>";
                                echo "<td>$pnmkaryawan</td>";
                                echo "<td>$pdivisi</td>";
                                
                                echo "<td nowrap>$pnolk</td>";//no id blk
                                echo "<td nowrap align='right'>$pjumlah</td>";//credit
                                echo "<td nowrap align='right'>$prprutin</td>";//$ptxt_saldoreal
                                echo "<td nowrap align='right'>$pca1</td>";//$ptxt_1_untukca
                                echo "<td nowrap align='right'>$pjmlkuranglebih_ca1</td>";//$txt_kuranglebih $ptxt_kuranglebih_sebelum
                                echo "<td nowrap align='right'>$pselisih</td>";//$txt_selisih
                                echo "<td nowrap align='right'>$pca2</td>";//$pca2
                                echo "<td nowrap align='right'>$pjumlahadj</td>";//$ptxt_jmladj
                                echo "<td nowrap align='right'>$pjmltrans</td>";//$ptxt_transjml
                                echo "</tr>";

                            }

                            $belum=true;
                            $no++;
                            
                        }
                        */
                        
                        
                        if ((double)$pjmltrans < 0) $pjmltrans=0;

                        echo "<tr>";
                        echo "<td>$no</td>";
                        echo "<td>$pnmkaryawan</td>";
                        echo "<td>$pdivisi</td>";

                        echo "<td nowrap></td>";//no id blk
                        echo "<td nowrap align='right'></td>";//credit
                        echo "<td nowrap align='right'>$prprutin</td>";//$ptxt_saldoreal
                        echo "<td nowrap align='right'>$pca1</td>";//$ptxt_1_untukca
                        echo "<td nowrap align='right'>$pjmlkuranglebih_ca1</td>";//$txt_kuranglebih $ptxt_kuranglebih_sebelum
                        echo "<td nowrap align='right'>$pselisih</td>";//$txt_selisih
                        echo "<td nowrap align='right'>$pca2</td>";//$pca2
                        echo "<td nowrap align='right'>$pjumlahadj</td>";//$ptxt_jmladj
                        echo "<td nowrap align='right'>$pjmltrans</td>";//$ptxt_transjml
                        echo "</tr>";
                        $no++;
                    }
                    
                    $gtotjumlah=number_format($gtotjumlah,0,",",",");
                    $gtotca1=number_format($gtotca1,0,",",",");
                    $gtotselisih=number_format($gtotselisih,0,",",",");
                    $gtotca2=number_format($gtotca2,0,",",",");
                    $gtotadj=number_format($gtotadj,0,",",",");
                    if ((double)$gtottrans < 0) $gtottrans=0;
                    $gtottrans=number_format($gtottrans,0,",",",");
                    $gtotkurlebihca1=number_format($gtotkurlebihca1,0,",",",");

                    echo "<tr>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";

                    echo "<td nowrap></td>";
                    echo "<td nowrap align='right'><b></b></td>";
                    echo "<td nowrap align='right'><b>$gtotjumlah</b></td>";
                    echo "<td nowrap align='right'><b>$gtotca1</b></td>";
                    echo "<td nowrap align='right'><b>$gtotkurlebihca1</b></td>";
                    echo "<td nowrap align='right'><b>$gtotselisih</b></td>";
                    echo "<td nowrap align='right'><b>$gtotca2</b></td>";
                    echo "<td nowrap align='right'><b>$gtotadj</b></td>";
                    echo "<td nowrap align='right'><b>$gtottrans</b></td>";
                    echo "</tr>";
                ?>
            </tbody>
        </table>
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
            var table = $('#datatable2, #datatable3').DataTable({
                fixedHeader: true,
                "ordering": false,
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": -1,
                "order": [[ 0, "asc" ]],
                "columnDefs": [
                    { "visible": false },
                    { className: "text-right", "targets": [5,6,7,8,9,10,11] },//right
                    { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,8,9,10,11] }//nowrap

                ],
                bFilter: true, bInfo: true, "bLengthChange": true, "bLengthChange": true,
                "bPaginate": true
            } );

        } );
    </script>
    
</body>
</html>


<?PHP
hapusdata:
    mysqli_query($cnit, "drop TEMPORARY table $tmp00");
    mysqli_query($cnit, "drop TEMPORARY table $tmp01");
    mysqli_query($cnit, "drop TEMPORARY table $tmp02");
    mysqli_query($cnit, "drop TEMPORARY table $tmp03");
    mysqli_query($cnit, "drop TEMPORARY table $tmp04");
    mysqli_query($cnit, "drop TEMPORARY table $tmp05");
?>