<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];

if ($pmodule=="cekdatasudahada") {
    $bolehinput="boleh";
    
    
    echo $bolehinput;
    
}

?>