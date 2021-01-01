<?php
function CekNamaDist($pdist) {
    $pname_foder_dist="";
    if ((double)$pdist==2) $pname_foder_dist="SPP";
    if ((double)$pdist==3) $pname_foder_dist="AMS";
    if ((double)$pdist==5) $pname_foder_dist="PV";
    if ((double)$pdist==6) $pname_foder_dist="CPM";
    if ((double)$pdist==10) $pname_foder_dist="SST";
    if ((double)$pdist==11) $pname_foder_dist="CP";
    if ((double)$pdist==16) $pname_foder_dist="MAS";
    if ((double)$pdist==23) $pname_foder_dist="DUM";
    if ((double)$pdist==30) $pname_foder_dist="CPP";
    if ((double)$pdist==31) $pname_foder_dist="SKS";
    
    if ((double)$pdist==21) $pname_foder_dist="AKF";
    if ((double)$pdist==28) $pname_foder_dist="BKS";
    if ((double)$pdist==15) $pname_foder_dist="EP";
    if ((double)$pdist==18) $pname_foder_dist="GMP";
    if ((double)$pdist==25) $pname_foder_dist="MPS";
    if ((double)$pdist==33) $pname_foder_dist="BCM";
    
    return $pname_foder_dist;
    
}
?>
