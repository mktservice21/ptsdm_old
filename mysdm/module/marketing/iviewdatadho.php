<?php
$pviewid="";
if (isset($_GET['iprint'])) $pviewid=$_GET['iprint'];
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];

if ($pmodule=="mktformcutiho") {
    if ($pviewid=="detail") {
        include "module/marketing/mkt_formcutiho/detailformcutiho.php";
    }    
}else{
    if ($pviewid=="detail") {
        
    }
}
?>

