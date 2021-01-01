<?php
    session_start();
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php
        $lnow=date("Ymd_his");
        if ($_GET['module']=='salesytd'){
            echo "<title>Sales YTD $lnow</title>";
        }elseif ($_GET['module']=='lapbrdcc'){
            echo "<title>Laporan BR $lnow</title>";
        }elseif ($_GET['module']=='coadata'){
            echo "<title>Data COA $lnow</title>";
        }elseif ($_GET['module']=='lapbrklaim'){
            echo "<title>Laporan Klaim Diskon $lnow</title>";
        }elseif ($_GET['module']=='lapbrrealisasi'){
            echo "<title>Laporan Realisasi BR $lnow</title>";
        }elseif ($_GET['module']=='lapbrrealisasidaerah'){
            echo "<title>Laporan Realisasi BR Daerah $lnow</title>";
        }elseif ($_GET['module']=='lapbrrealisasidaerahbulan'){
            echo "<title>Laporan Realisasi BR Daerah Per Bulan $lnow</title>";
        }elseif ($_GET['module']=='lapbrrealisasicabang'){
            echo "<title>Laporan Realisasi BR Cabang $lnow</title>";
        }elseif ($_GET['module']=='lapbrytd'){
            echo "<title>Laporan YTD BR $lnow</title>";
        }elseif ($_GET['module']=='datakaryawan'){
            echo "<title>Data Karyawan $lnow</title>";
        }elseif ($_GET['module']=='lapbrkeuangan'){
            echo "<title>Laporan Keuangan $lnow</title>";
        }elseif ($_GET['module']=='lapbrotc'){
            echo "<title>Laporan Budget Request OTC $lnow</title>";
        }elseif ($_GET['module']=='lapbrtransotc'){
            echo "<title>Laporan BR Transfer OTC $lnow</title>";
        }elseif ($_GET['module']=='lapbrrekapotc'){
            echo "<title>Laporan Rekap Transfer BR OTC $lnow</title>";
        }elseif ($_GET['module']=='lapbrrekapotcall'){
            echo "<title>Laporan Rekap Budget Request OTC $lnow</title>";
        }elseif ($_GET['module']=='lapbrotcbulanan'){
            echo "<title>Laporan Bulanan Budget Request OTC $lnow</title>";
        }elseif ($_GET['module']=='asdsad'){
        }elseif ($_GET['module']=='asdsad'){
        }elseif ($_GET['module']=='asdsad'){

        }else{
            echo "<title>PT Surya Dermato Medica</title>";
        }
    ?>
    
    
    <link rel="shortcut icon" href="images/icon.ico" />
    <!--
    <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <link href="build/css/custom.css" rel="stylesheet">

    <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">


    
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <script src="js/inputmask.js"></script>
    -->
    
    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css" rel="stylesheet">

    
    
    <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="build/css/custom.css" rel="stylesheet">
    
    
    
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
    
  </head>

<body class="nav-md">
    <div class="containerx body">
        <div class="">
            <div class="main_container">
                <!--row-->
                <div class="row">
                    <div class="right_col" role="main">
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                <div class='x_panel'>
                                    <?php
                                        include "config/fungsi_rupiah.php";
                                        if ($_GET['module']=='salesytd'){
                                            include 'module/mod_sls_salesytd/aksi_salesytd.php';
                                        }elseif ($_GET['module']=='lapbrdcc'){
                                            include 'module/lap_br_dcc/aksi_lapbrdcc.php';
                                        }elseif ($_GET['module']=='coadata'){
                                            include 'module/mod_coa_coadata/lihatdatacoa.php';
                                        }elseif ($_GET['module']=='lapbrklaim'){
                                            include 'module/lap_br_klaim/aksi_lapbrklaim.php';
                                        }elseif ($_GET['module']=='lapbrrealisasi'){
                                            include 'module/lap_br_realisasi/aksi_lapbrrealisasi.php';
                                        }elseif ($_GET['module']=='lapbrrealisasidaerah'){
                                            include 'module/lap_br_realisasidaerah/aksi_lapbrrealisasidaerah.php';
                                        }elseif ($_GET['module']=='lapbrrealisasidaerahbulan'){
                                            include 'module/lap_br_realisasidaerahbln/aksi_lapbrrealisasidaerahbln.php';
                                        }elseif ($_GET['module']=='lapbrrealisasicabang'){
                                            include 'module/lap_br_realisasidaerahcab/aksi_lapbrrealisasidaerahcab.php';
                                        }elseif ($_GET['module']=='lapbrytd'){
                                            include 'module/lap_br_ytd/aksi_brytd.php';
                                        }elseif ($_GET['module']=='datakaryawan'){
                                            include 'module/lap_m_karyawan/lihatdatakaryawan.php';
                                        }elseif ($_GET['module']=='lapbrkeuangan'){
                                            include 'module/lap_br_keuangan/aksi_keuangan.php';
                                        }elseif ($_GET['module']=='lapbrotc'){
                                            include 'module/lap_br_otc/aksi_brotc.php';
                                        }elseif ($_GET['module']=='lapbrtransotc'){
                                            include 'module/lap_br_otctrans/aksi_brotctrans.php';
                                        }elseif ($_GET['module']=='lapbrrekapotc'){
                                            include 'module/lap_br_otcrekap/aksi_brotcrekap.php';
                                        }elseif ($_GET['module']=='lapbrrekapotcall'){
                                            include 'module/lap_br_otcrekapall/aksi_brotc.php';
                                        }elseif ($_GET['module']=='lapbrotcbulanan'){
                                            include 'module/lap_br_otcbulan/aksi_brotcbulan.php';
                                        }elseif ($_GET['module']=='asdsad'){
                                            
                                        }else{

                                        }
                                    ?>
                                    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
                                </div><!--end xpanel-->
                            </div>
                    </div>
                </div>
                <!--end row-->
            </div>
        </div>
    </div>
    <!--  
    <script type='text/javascript' src='datetime/js/jquery-ui.min.js'></script>
    
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    
    <script src="vendors/fastclick/lib/fastclick.js"></script>
    
    <script src="vendors/nprogress/nprogress.js"></script>
    
    <script src="vendors/iCheck/icheck.min.js"></script>
    
    <script src="vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>
    
    <script src="vendors/jQuery-Smart-Wizard/js/jquery.smartWizard.js"></script>
    
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

    
    <script src="vendors/moment/min/moment.min.js"></script>
    <script src="vendors/bootstrap-daterangepicker/daterangepicker.js"></script>
    
    <script src="vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
    
    <script src="build/js/custom.min.js"></script>
    -->
  </body>
</html>