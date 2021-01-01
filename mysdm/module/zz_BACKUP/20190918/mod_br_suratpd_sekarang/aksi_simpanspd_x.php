<?php

    session_start();
    include "../../config/koneksimysqli.php";
    
    
    
    $fidbr="";
    foreach ($_POST['chk_jml1'] as $nobrinput) {
        if (!empty($nobrinput) AND $nobrinput <> "0") {
            $fidbr=$fidbr."'".$nobrinput."',";
        }
    }
    
    if (!empty($fidbr)) {
        $fidbr=substr($fidbr, 0, -1);
        $fidbr="(".$fidbr.")";        
        echo "$fidbr<br/>";
    }
    
    foreach ($_POST['chk_jml1'] as $no_brid) {
        if (!empty($no_brid)) {
            $no_urutbr = $_POST['cb_urut'][$no_brid];
            $var = $no_brid . ',' . $no_urutbr;
            echo $var . '<br />';
        }
    }

?>

