<?php
$pviewid="";
if (isset($_GET['iprint'])) $pviewid=$_GET['iprint'];

if ($pviewid=="detail") {
    include "module/dkd/dkd_wekvisitplanreal/detailwekplanreal.php";
}elseif ($pviewid=="xxx") {
    
}
?>

