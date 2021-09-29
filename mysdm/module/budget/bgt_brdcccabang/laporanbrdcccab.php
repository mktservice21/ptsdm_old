<?php
    $piprint="";
    if (isset($_GET['iprint'])) $piprint=$_GET['iprint'];
    
    if ($piprint=="print") {
        include "pritnbrdcccab.php";
    }elseif ($piprint=="lihatgambar") {
        
    }
?>

