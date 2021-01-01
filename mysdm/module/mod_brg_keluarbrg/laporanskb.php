<?php

$ppilihrpt="";
if (isset($_GET['iprint'])) {
    $ppilihrpt=$_GET['iprint'];
}

if ($ppilihrpt=="print") {
    include "printskb.php";
}elseif ($ppilihrpt=="xxx") {
    
}

?>

