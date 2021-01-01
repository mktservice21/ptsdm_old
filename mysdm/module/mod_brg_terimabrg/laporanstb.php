<?php

$ppilihrpt="";
if (isset($_GET['iprint'])) {
    $ppilihrpt=$_GET['iprint'];
}

if ($ppilihrpt=="print") {
    include "printstb.php";
}elseif ($ppilihrpt=="xxx") {
    
}

?>

