<?php
    session_start();
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    $mylink="../ptsdm/mysdm/";
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
        if ($_GET['module']=='salesytddaerah'){
            echo "<title>salesytddaerah</title>";
        }elseif ($_GET['module']=='salesytdregion'){
            echo "<title>salesytdregion</title>";
        }elseif ($_GET['module']=='salesytdsm'){
            echo "<title>salesytdsm</title>";
        }elseif ($_GET['module']=='salesytddivisipm'){
            echo "<title>salesytdpm</title>";
        }else{
            echo "<title>PT Surya Dermato Medica</title>";
        }
    ?>
    
    <link rel="shortcut icon" href="<?PHP echo "$mylink"; ?>images/icon.ico" />
    
    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css" rel="stylesheet">

    
    <!-- Font Awesome -->
    <link href="<?PHP echo "$mylink"; ?>vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?PHP echo "$mylink"; ?>build/css/custom.css" rel="stylesheet">
    
    
    
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
                <!--row-->
                <div class="row">
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <div class='x_panel'>
                            <?php
                            if ($_GET['module']=='salesytddaerah'){
                                include 'module/ytd_sls_daerah/aksi_sls_ytd_daerah.php';
                            }elseif ($_GET['module']=='salesytdregion'){
                                include 'module/ytd_sls_region/aksi_ytd_sls_region.php';
                            }elseif ($_GET['module']=='salesytdsm'){
                                include 'module/ytd_sls_sm/aksi_ytd_sls_sm.php';
                            }elseif ($_GET['module']=='salesytddivisipm'){
                                include 'module/ytd_sls_pm/aksi_ytd_sls_pm.php';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <!--end row-->
        </div>
    </div>
      
    
  </body>
</html>