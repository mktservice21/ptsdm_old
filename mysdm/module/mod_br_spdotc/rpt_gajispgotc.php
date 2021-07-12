<?php
include("config/koneksimysqli.php");

$periodeajukan="";
$pidinput=$_GET['ispd'];
$query = "select * from dbmaster.t_suratdana_br WHERE idinput='$pidinput'";
$tampil= mysqli_query($cnmy, $query);
$ketemu= mysqli_num_rows($tampil);
if ($ketemu>0) {
    $ra= mysqli_fetch_array($tampil);
    $periodeajukan=$ra['tgl'];
    $periodeajukan= date("Ymd", strtotime($periodeajukan));
}
mysqli_close($cnmy);

if (!empty($periodeajukan)) {
    if ((double)$periodeajukan>='20210624') {
        include "rpt_gajispgotc_bpjs.php";
    }else{
        include "rpt_gajispgotc_non.php";
    }
}


?>
