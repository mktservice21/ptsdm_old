<?php
$pviewid="";
if (isset($_GET['iprint'])) $pviewid=$_GET['iprint'];

if ($pviewid=="print") {
    include "module/mod_br_kaskecilcab/printdata.php";
}elseif ($pviewid=="editdatafin") {
    include "module/mod_br_kaskecilcab/editdatafin.php";
}
?>

