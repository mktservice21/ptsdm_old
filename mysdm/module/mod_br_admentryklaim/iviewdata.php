<?php
$pviewid="";
if (isset($_GET['iprint'])) $pviewid=$_GET['iprint'];

if ($pviewid=="print") {
    include "module/mod_br_admentryklaim/printdata.php";
}elseif ($pviewid=="allprev") {
    include "module/mod_br_admentryklaim/printdataall.php";
}
?>

