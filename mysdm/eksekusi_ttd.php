<?PHP session_start(); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php
        if ($_GET['module']=='brapproveam'){
            echo "<title>brapproveam</title>";
        }elseif ($_GET['module']=='brvalidasi'){
            echo "<title>brvalidasi</title>";
        }else{
            echo "<title>PT Surya Dermato Medica</title>";
        }
    ?>
    <link rel="shortcut icon" href="images/icon.ico" />
    
    
  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">

        <!-- page content -->

        <?PHP
            if ($_GET['module']=="brapproveam") {
                include "tanda_tangan_base64/tampilttd.php";
            }elseif ($_GET['module']=="brvalidasi") {
                include "module/mod_br_validasi/infodoter.php";
            }else{
                include "tanda_tangan_base64/tanda_tangan.php";
            }
             
        ?>
        
        <!-- /page content -->

        
        <!-- footer content -->
        <footer>
          <div class="pull-right">
            
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>

  

  </body>
</html>