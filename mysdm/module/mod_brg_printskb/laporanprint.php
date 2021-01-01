<?php

$ppilihrpt="";
if (isset($_GET['act'])) {
    $ppilihrpt=$_GET['act'];
}

if ($ppilihrpt=="sjb") {
    include "printsjb.php";
}elseif ($ppilihrpt=="hapusberhasil") {
    include "printinfo.php";
}elseif ($ppilihrpt=="xxx") {
    
}

?>
