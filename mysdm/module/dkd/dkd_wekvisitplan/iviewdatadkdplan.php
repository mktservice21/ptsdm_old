<?php
$pviewid="";
if (isset($_GET['iprint'])) $pviewid=$_GET['iprint'];

if ($pviewid=="detail") {
    include "module/dkd/dkd_wekvisitplan/detailwekplan.php";
}elseif ($pviewid=="xxx") {
    
}
?>

