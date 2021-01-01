<?php

if ($_GET['module']=="hitungtotal"){
    include "../../config/koneksimysqli.php";
    $pnoid=$_POST['unoidbr'];
    $totalinput=0;
    if (!empty($pnoid)) {
        $query="SELECT SUM(jumlah) as jumlah from hrd.br0 where brId in $pnoid";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $tr= mysqli_fetch_array($tampil);
            if (!empty($tr['jumlah'])) $totalinput=$tr['jumlah'];
        }
    }
    echo $totalinput;
}elseif ($_GET['module']=="caridatasudahsimpan"){
    
}elseif ($_GET['module']=="xxx"){
}
?>