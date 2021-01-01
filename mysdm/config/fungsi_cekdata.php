<?php

function CekClosing($periode1, $periode2){
    $ssql = "Select * from t_closingbln where CONCAT(TAHUN, BULAN) between '$periode1' and '$periode2'";
    $sudahclose=mysql_num_rows(mysql_query($ssql));
    if ($sudahclose>0)
        return "True";
    else
        return "False";
}

function CekClosingTahun($periode1, $periode2){
    $ssql = "Select * from t_closingbln where TAHUN between '$periode1' and '$periode2'";
    $sudahclose=mysql_num_rows(mysql_query($ssql));
    if ($sudahclose>0)
        return "True";
    else
        return "False";
}
?>
