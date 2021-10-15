<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];

if ($pmodule=="getdatapengajuan") {
    
    include "../../config/koneksimysqli.php";
    
    $pdepid=$_POST['udep'];
    
    $fpengajuan="";
    if (!empty($_SESSION['COAPOSDIP2'])) $fpengajuan = $_SESSION['COAPOSDIP2'];

    $ppilihpengajuan0="selected";
    $ppilihpengajuan1="";
    $ppilihpengajuan2="";
    $ppilihpengajuan3="";
    if ($fpengajuan=="ETH") $ppilihpengajuan1="selected";
    elseif ($fpengajuan=="HO") $ppilihpengajuan2="selected";
    elseif ($fpengajuan=="OTC") $ppilihpengajuan3="selected";
    
    if ($pdepid=="SLS01" OR $pdepid=="SLS02" OR $pdepid=="SLS03" OR $pdepid=="MKT") {
        echo "<option value='ETH' $ppilihpengajuan1>ETHICAL</option>";
        echo "<option value='OTC' $ppilihpengajuan3>CHC</option>";
    }else {
        if ($fpengajuan=="ETH" OR $fpengajuan=="OTC") {
            $ppilihpengajuan0="selected";
            $ppilihpengajuan1="";
            $ppilihpengajuan2="";
            $ppilihpengajuan3="";
        }
        
        echo "<option value='' $ppilihpengajuan0>--All--</option>";
        echo "<option value='ETH' $ppilihpengajuan1>ETHICAL</option>";
        echo "<option value='HO' $ppilihpengajuan2>HO</option>";
        echo "<option value='OTC' $ppilihpengajuan3>CHC</option>";
        
    }
    
    mysqli_close($cnmy);
    
    
}elseif ($pmodule=="xxx") {
}

?>