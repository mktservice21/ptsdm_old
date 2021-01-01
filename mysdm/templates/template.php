<?PHP
	date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    $modnya = "";
    if (isset($_GET['module']))
        $modnya = $_GET['module'];
    
    $lactnya = "";
    if (isset($_GET['act']))
        $lactnya = $_GET['act'];
    
    $bolehlewat="true";
	
    if ($modnya=="home" OR empty($modnya)) $bolehlewat="false";
    if ( ($modnya=="entrybrcash" OR $modnya=="entrybrluarkota" OR $modnya=="entrybrrutin") AND $lactnya=="uploaddok") $bolehlewat="false";
	
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    
    <title>Marketing Service</title>
    <link rel="shortcut icon" href="images/icon.ico" />
    
    <?PHP if ($modnya=="home" OR $modnya=="") { ?> 
    <link href="css/AdminLTE.css" rel="stylesheet" type="text/css" />
    <?PHP } ?>
	
<?PHP if ($bolehlewat=="true") { ?> 
    <link href="css/konten.css" rel="stylesheet">
    <!-- style date time picker -->
    <link href="datetime/css/jquery-ui-1.8.4.custom.css" rel="stylesheet" type="text/css" />
<?PHP } ?> 
    
    <!-- Bootstrap -->
    <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="vendors/nprogress/nprogress.css" rel="stylesheet">
    
<?PHP if ($bolehlewat=="true") { ?> 
    <!-- bootstrap-daterangepicker -->
    <script src="vendors/moment/min/moment.min.js"></script>
    <script src="vendors/bootstrap-daterangepicker/daterangepicker.js"></script>
    <!-- bootstrap-datetimepicker -->
    <script src="vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
    <!-- Ion.RangeSlider -->
    <script src="vendors/ion.rangeSlider/js/ion.rangeSlider.min.js"></script>
    <!-- iCheck -->
    <link href="vendors/iCheck/skins/flat/green.css" rel="stylesheet">
    <!-- bootstrap-wysiwyg -->
    <link href="vendors/google-code-prettify/bin/prettify.min.css" rel="stylesheet">
    <!-- Select2 -->
    <link href="vendors/select2/dist/css/select2.min.css" rel="stylesheet">
    <!-- Switchery -->
    <link href="vendors/switchery/dist/switchery.min.css" rel="stylesheet">
    <!-- starrr -->
    <link href="vendors/starrr/dist/starrr.css" rel="stylesheet">
    <!-- bootstrap-daterangepicker -->
    <link href="vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

<?PHP } ?> 
    
    <!-- Custom Theme Style -->
    <link href="build/css/customz.css" rel="stylesheet">
    
<?PHP if ($bolehlewat=="true") { ?> 
    <script src="js/hanyaangka.js"></script>
    <!-- Datatables -->
    <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

    
    
        <!-- bootstrap-daterangepicker -->
        <link href="vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
        <!-- bootstrap-datetimepicker -->
        <link href="vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
        <!-- Ion.RangeSlider -->
        <link href="vendors/normalize-css/normalize.css" rel="stylesheet">
        <link href="vendors/ion.rangeSlider/css/ion.rangeSlider.css" rel="stylesheet">
        <link href="vendors/ion.rangeSlider/css/ion.rangeSlider.skinFlat.css" rel="stylesheet">
        <!-- Bootstrap Colorpicker -->
        <link href="vendors/mjolnic-bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css" rel="stylesheet">

        <link href="vendors/cropper/dist/cropper.min.css" rel="stylesheet">
        
<?PHP } ?> 
        
    <!-- Custom Theme Style -->
    <link href="build/css/custom.minz.css" rel="stylesheet">

<?PHP if ($bolehlewat=="true") { ?> 
    <!-- jQuery
    <script src="vendors/jquery/dist/jquery.min.js"></script>-->
    <!-- Bootstrap
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>-->

    <!-- FastClick
    <script src="vendors/fastclick/lib/fastclick.js"></script>-->
    <!-- NProgress
    <script src="vendors/nprogress/nprogress.js"></script>-->
    <!-- bootstrap-progressbar -->
    <script src="vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <!-- iCheck -->
    <script src="vendors/iCheck/icheck.min.js"></script>
    <!-- bootstrap-wysiwyg -->
    <script src="vendors/bootstrap-wysiwyg/js/bootstrap-wysiwyg.min.js"></script>
    <script src="vendors/jquery.hotkeys/jquery.hotkeys.js"></script>
    <script src="vendors/google-code-prettify/src/prettify.js"></script>
    <!-- jQuery Tags Input -->
    <script src="vendors/jquery.tagsinput/src/jquery.tagsinput.js"></script>
    <!-- Switchery -->
    <script src="vendors/switchery/dist/switchery.min.js"></script>
    <!-- Select2 -->
    <script src="vendors/select2/dist/js/select2.full.min.js"></script>
    <!-- Parsley -->
    <script src="vendors/parsleyjs/dist/parsley.min.js"></script>
    <!-- Autosize -->
    <script src="vendors/autosize/dist/autosize.min.js"></script>
    <!-- jQuery autocomplete -->
    <script src="vendors/devbridge-autocomplete/dist/jquery.autocomplete.min.js"></script>
    <!-- starrr -->
    <script src="vendors/starrr/dist/starrr.js"></script>
    <!-- Ion.RangeSlider -->
    <script src="vendors/ion.rangeSlider/js/ion.rangeSlider.min.js"></script>
    <!-- Bootstrap Colorpicker -->
    <script src="vendors/mjolnic-bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>
    <!-- jquery.inputmask -->
    <script src="vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>
    <!-- jQuery Knob -->
    <script src="vendors/jquery-knob/dist/jquery.knob.min.js"></script>
    <!-- Cropper -->
    <script src="vendors/cropper/dist/cropper.min.js"></script>

    
    <!-- Datatables -->
    <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">


    <!--
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
	-->
    <script src="dttable152/buttons/dataTables.buttons.min.js"></script>
    <script src="dttable152/buttons/buttons.flash.min.js"></script>
    <script src="dttable152/ajax/jszip.min.js"></script>
	
<?PHP } ?> 
    
    <!-- Custom Theme Scripts
    <script src="build/js/custom.min.js"></script>-->

    <!-- jQuery -->
    <script src="vendors/jquery/dist/jquery.min.js"></script>
    
    
    
<?PHP if ($bolehlewat=="true") { ?> 
    <!--input mask -->
    <script src="js/inputmask.js"></script>
<?PHP } ?> 
    

    
    </head>

    
    
    <body class="nav-md">
        
        <div class="container body">
            <div class="main_container">
                <div class="col-md-3 left_col">
                <div class="left_col scroll-view">
                    <div class="navbar nav_title" style="border: 0;">
                        <a href="?module=home" class="site_title"><i class="fa fa-line-chart"></i> <span>MS</span></a>
                    </div>

                    <div class="clearfix"></div>

                    <!-- menu profile quick info -->
                    <div class="profile clearfix">
                        <div class="profile_pic">
                            <img src="img/users/foto_f2.jpg" alt="..." class="img-circle profile_img">
                        </div>
                        <div class="profile_info">
                            <!--<span>Selamat Datang,</span>-->
                            <h2><?PHP echo $_SESSION['NAMALENGKAP']; ?></h2>
                        </div><div class="clearfix"></div>
                        <span style="color:#ffffff; padding-left:15px; font-size: 11.5px;"><u>
						<?PHP 
							if ($_SESSION['IDCARD']=="0000002403") {
							}else{
								echo $_SESSION['JABATANNM']; 
							}
						?>
						</u></span>
                    </div>
                    <!-- /menu profile quick info -->

                    <br />

                    <!-- sidebar menu -->
                    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                        <div class="menu_section">
                            <?PHP
                                include "menu.php";
                            ?>
                        </div>
                    </div>
                    <!-- /sidebar menu -->
                </div>
            </div>

            <!-- top navigation -->
            <div class="top_nav">
                <div class="nav_menu">
                    <nav>
                        <div class="nav toggle">
                            <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                        </div>

                        <ul class="nav navbar-nav navbar-right">
                            <li class="">
                                <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <img src="img/users/foto_f2.jpg" alt=""><?PHP echo $_SESSION['NAMALENGKAP']; ?>
                                    <span class=" fa fa-angle-down"></span>
                                </a>
                                
                                <ul class="dropdown-menu dropdown-usermenu pull-right">
                                    <!--
                                    <li><a href="javascript:;"> Profile</a></li>
                                    <li>
                                        <a href="javascript:;">
                                            <span class="badge bg-red pull-right">50%</span>
                                            <span>Settings</span>
                                        </a>
                                    </li>
                                    <li><a href="javascript:;">Help</a></li>
                                    -->
                                    <li><a href="logout.php"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                                </ul>
                                
                            </li>
							
                            <?PHP
                                if ($_SESSION['DIVISI']!="OTC") {
                                    include "dataproses.php";
                                }
                            ?>
							
                        </ul>
                    </nav>
                </div>
            </div>
            <!-- /top navigation -->


            <!-- page content -->
                <?PHP 
                    include "konten.php";
                    //include "wize.php"; 
                ?>
            <!-- /page content -->

            <!-- footer content -->
                <footer>
                    <div class="pull-right">
                        <a href="http://#"> MS</a>
                    </div>
                    <div class="clearfix"></div>
                </footer>
                <!-- /footer content -->
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
        
<?PHP if ($bolehlewat=="true") { ?> 
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

<?PHP } ?> 
        
		
    <?PHP if ($modnya=="lapgl") { ?>
        <!-- jQuery Tags Input -->
        <script src="vendors/jquery.tagsinput/src/jquery.tagsinput.js"></script>
    <?PHP } ?>
	
	
        <!-- bootstrap-daterangepicker -->
        <script src="vendors/moment/min/moment.min.js"></script>
        <script src="vendors/bootstrap-daterangepicker/daterangepicker.js"></script>
        <!-- bootstrap-datetimepicker -->
        <script src="vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
        <!-- Custom Theme Scripts -->
        <script src="build/js/custom.min.js"></script>

<?PHP if ($bolehlewat=="true") { ?> 
        <!-- Initialize datetimepicker -->
        <script>
            $('#myDatepicker').datetimepicker();

            $('#myDatepicker2').datetimepicker({
                format: 'DD.MM.YYYY'
            });

            $('#myDatepicker3').datetimepicker({
                format: 'hh:mm A'
            });

            $('#myDatepicker4').datetimepicker({
                ignoreReadonly: true,
                allowInputToggle: true,
                format: 'DD MMMM YYYY'
            });

            $('#tgl01, #tgl02, #tgl03, #tgl04, #tgl05, #tgl06, #tgl07, #tgl08, #tgl09, #tgl10').datetimepicker({
                ignoreReadonly: true,
                allowInputToggle: true,
                format: 'DD MMMM YYYY'
            });
            
            $('#thn01, #thn02').datetimepicker({
                ignoreReadonly: true,
                allowInputToggle: true,
                format: 'YYYY'
            });
            
            $('#mytgl01, #mytgl02').datetimepicker({
                ignoreReadonly: true,
                allowInputToggle: true,
                format: 'DD/MM/YYYY'
            });
            
            $('#e_catgl11, #e_catgl12, #e_catgl13, #e_catgl14, #e_catgl15, #e_catgl16, #e_catgl17, #e_catgl18, #e_catgl19, #e_catgl21, #e_catgl22, #e_catgl23, #e_catgl24, #e_catgl25, #e_catgl26, #e_catgl27, #e_catgl28, #e_catgl29').datetimepicker({
                ignoreReadonly: true,
                allowInputToggle: true,
                format: 'DD/MM/YYYY'
            });
            
            $('#thnbln01, #thnbln02').datetimepicker({
                ignoreReadonly: true,
                allowInputToggle: true,
                format: 'MM/YYYY'
            });
            
            $('#cbln01, #cbln02').datetimepicker({
                ignoreReadonly: true,
                allowInputToggle: true,
                format: 'MMMM YYYY'
            });

            $('#datetimepicker6').datetimepicker();

            $('#datetimepicker7').datetimepicker({
                useCurrent: false
            });

            $("#datetimepicker6").on("dp.change", function(e) {
                $('#datetimepicker7').data("DateTimePicker").minDate(e.date);
            });

            $("#datetimepicker7").on("dp.change", function(e) {
                $('#datetimepicker6').data("DateTimePicker").maxDate(e.date);
            });
        </script>
        
<?PHP } ?> 
        
        <!--
        <script type="text/javascript">
            $(function() {
                $('#tgl01, #tgl02, #tgl03, #tgl04').datepicker({
                    numberOfMonths: 1, /* bisa dua month*/
                    firstDay: 1,
                    dateFormat: 'dd MM yy' /*,  bisa DD dd-mm-yy*/
                    /*minDate: '-2'  bisa min bisa plus -1 0 2*/
                    /*maxDate: '+1d', /* bisa +2Y*/
                });
            });
        </script>
        -->
        <style>
            .right_col {
                color:#000;
            }
        </style>

  

    </body>
</html>
<!--
<link href="css/src/selectstyle.css" rel="stylesheet" type="text/css">
<script src="css/src/selectstyle.js"></script>
<script>
    jQuery(document).ready(function($) {
        $('#e_idcabang').selectstyle({
            onchange : function(val){}
        });
    });
</script>
-->