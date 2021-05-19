<?php
$pmodule="";
$pact="";
if (isset($_GET['act'])) $pact=$_GET['act'];

if ($pact=="viewrptklaimdist") {
    include "laporanklaimdisc.php";
}elseif ($pact=="viewrptbradveth" OR $pact=="viewrptbrpcmeth" OR $pact=="viewrptbrklaimeth" OR $pact=="viewrptbrkboneth" OR $pact=="viewrptbradvvsbyeth") {
    include "laporanbrethical.php";
}

?>
