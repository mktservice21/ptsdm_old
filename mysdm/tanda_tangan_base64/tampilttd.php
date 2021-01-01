<?php
    include "config/koneksimysqli_it.php";
    $cnmy=$cnit;
    if ($_GET['ket']=="am") 
        $ssql="select TTDAM as id, TTDAM_GBR as gambar from hrd.br0_ttd where BRID='$_GET[brid]' and RTrim(TTDAM)<>'' order by IDKU desc";
    else
        $ssql="select TTDSPV as id, TTDSPV_GBR as gambar from hrd.br0_ttd where BRID='$_GET[brid]' and RTrim(TTDSPV)<>'' order by IDKU desc";
    $tampil=mysqli_query($cnmy, $ssql);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0){
        $t= mysqli_fetch_array($tampil);
        $rf= "{\"lines"."\":[";
        $jmkar=strlen($t['id']);
        //$data1=substr("$t[TTD]",12,$jmkar); 
        $data1=substr("$t[id]",10,$jmkar); 
        $data2=substr("$data1",0, -2); 
        //echo "$data2";
        echo "<img src='images/tanda_tangan_base64/$t[gambar]' height='350px'>";
    }
?>

