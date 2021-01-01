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
        if ($_GET['module']=='entrybrnon'){
            echo "<title>entrybrnon</title>";
        }elseif ($_GET['module']=='entrybrnoncabang'){
            echo "<title>entrybrnoncabang</title>";
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
            if ($_GET['module']=="entrybrnon"){
                include "module/mod_br_entrynon/print.php";
            }elseif($_GET['module']=="entrybrnoncabang"){
                include "module/mod_br_entrynoncab/lihatdatauc.php";
            }elseif($_GET['module']=="entrybrotc"){
                include "module/mod_br_entryotc/print.php";
            }else{
                
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

