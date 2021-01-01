<?php
$pviewid="";
if (isset($_GET['iprint'])) $pviewid=$_GET['iprint'];

if ($pviewid=="print") {
    include "module/mod_br_kaskecilcabotc/printdataotc.php";
}
?>

