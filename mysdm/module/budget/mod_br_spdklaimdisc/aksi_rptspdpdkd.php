<?php
$pmodule="";
$pact="";
if (isset($_GET['act'])) $pact=$_GET['act'];

if ($pact=="viewbrklaim") {
    include "laporanklaimdisc.php";
}

?>
