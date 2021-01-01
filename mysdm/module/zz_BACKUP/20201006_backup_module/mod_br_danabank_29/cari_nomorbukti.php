<?php

function caribuktinomor($zket, $zperiode){
    include "../../config/koneksimysqli.php";
    $zperiode = date('Y-m-d', strtotime($zperiode));
    
    $pilih_no="nobbm";
    if ($zket=="2") $pilih_no="nobbk";
    
    $pblnini = date('m', strtotime($zperiode));
    $pthnini = date('Y', strtotime($zperiode));
    $pthnini_bln = date('Ym', strtotime($zperiode));
    $tno="1501";
    $query = "SELECT $pilih_no as pnomor FROM dbmaster.t_setup_bukti WHERE bulantahun='$pthnini_bln' LIMIT 1";
    $showkan= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($showkan);
    if ($ketemu>0){
        $sh= mysqli_fetch_array($showkan);
        if (!empty($sh['pnomor'])) { $tno=(INT)$sh['pnomor']+1; }
        if ((double)$tno==1) $tno="1501";
    }
    
    mysqli_close($cnmy);
    
    return $tno;
}