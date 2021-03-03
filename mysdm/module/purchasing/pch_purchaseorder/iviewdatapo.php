<?php
$pviewid="";
if (isset($_GET['iprint'])) $pviewid=$_GET['iprint'];

if ($pviewid=="print") {
    include "module/purchasing/pch_purchaseorder/printdatapo.php";
}elseif ($pviewid=="xxx") {
    
}
?>

