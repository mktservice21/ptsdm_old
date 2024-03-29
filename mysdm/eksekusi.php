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
        if ($_GET['module']=='salesytd'){
            echo "<title>Sales YTD</title>";
        }elseif ($_GET['module']=='lapbrdcc'){
            echo "<title>Laporan BR</title>";
        }elseif ($_GET['module']=='coadata'){
            echo "<title>Data COA</title>";
        }elseif ($_GET['module']=='coalevel4'){
            echo "<title>Data COA Level 4</title>";
        }elseif ($_GET['module']=='asdsad'){

        }else{
            echo "<title>PT Surya Dermato Medica</title>";
        }
    ?>
    
    <link rel="shortcut icon" href="images/icon.ico" />
    <!-- Bootstrap -->
    <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="build/css/custom.css" rel="stylesheet">


    <!-- Datatables -->
    <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">


    
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>


    <!-- Custom Theme Scripts
    <script src="build/js/custom.min.js"></script>-->

    <!-- jQuery -->
    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <!--input mask -->
    <script src="js/inputmask.js"></script>
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
                                        }elseif ($_GET['module']=='coalevel4'){
                                            include 'module/mod_coa_coa4/lihatdatacoa4.php';
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
      
    <script type='text/javascript' src='datetime/js/jquery-ui.min.js'></script>
    <!-- Bootstrap -->
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="vendors/nprogress/nprogress.js"></script>
    <!-- iCheck -->
    <script src="vendors/iCheck/icheck.min.js"></script>
    <!-- jquery.inputmask -->
    <script src="vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>
    <!-- jQuery Smart Wizard -->
    <script src="vendors/jQuery-Smart-Wizard/js/jquery.smartWizard.js"></script>
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

    <!-- bootstrap-daterangepicker -->
    <script src="vendors/moment/min/moment.min.js"></script>
    <script src="vendors/bootstrap-daterangepicker/daterangepicker.js"></script>
    <!-- bootstrap-datetimepicker -->
    <script src="vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="build/js/custom.min.js"></script>
    
  </body>
</html>