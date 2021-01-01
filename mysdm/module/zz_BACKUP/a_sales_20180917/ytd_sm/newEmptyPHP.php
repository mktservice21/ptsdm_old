<?php

$rekeningbnk=  str_replace("_", "", $_POST['e_norekrel']);
$arr_kata = explode("-",$rekeningbnk);
if (empty($arr_kata[1])) $rekeningbnk=$arr_kata[0];
if (empty($rekeningbnk)) $rekeningbnk=str_replace("_", "", $_POST['e_norekrel']);

$arr_kata2 = explode("-",$rekeningbnk);
if (isset($arr_kata2[2])) {
    if (empty($arr_kata2[2])) $rekeningbnk=$arr_kata2[0]."-".$arr_kata2[1];
}
$prekreal1=$rekeningbnk;


?>
