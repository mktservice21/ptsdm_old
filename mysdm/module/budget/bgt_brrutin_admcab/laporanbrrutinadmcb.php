<?php
    $piprint="";
    if (isset($_GET['iprint'])) $piprint=$_GET['iprint'];
    
    if ($piprint=="print") {
        include "pritnbrrutinadmcb.php";
    }elseif ($piprint=="lihatgambar") {
        include "lihatgambaradmcb.php";
    }
?>

