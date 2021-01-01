<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];
//$ptgl = str_replace('/', '-', $_POST['utgl']);
//$ptglpengajuan= date("Y-m-d", strtotime($ptgl));
//$pjumlah=str_replace(",","", $pjumlah);

if ($pmodule=="caricabangsm") {
    
    include "../../config/koneksimysqli.php";
    include "../../config/koneksimysqli_ms.php";
    
    $pmyidcard=$_SESSION['IDCARD'];
    $pmyjabatanid=$_SESSION['JABATANID'];
                
    $pidsm=$_POST['usm'];
    
    $filsm=$pidsm;
    if ($pmyjabatanid=="20" OR (DOUBLE)$pmyjabatanid==20) $filsm=$pmyidcard;
        
    echo "<option value=''>-- All --</option>";

    
    $query = "select iCabangId, nama from sls.icabang where "
            . " aktif='Y' AND icabangid IN (select distinct IFNULL(icabangid,'') from sls.ism0 WHERE karyawanid='$filsm') ";
    $query .=" order by nama";
    $tampil = mysqli_query($cnms, $query);
    while ($rx= mysqli_fetch_array($tampil)) {
        $nidcab=$rx['iCabangId'];
        $nnmcab=$rx['nama'];
        if ($pidcabangpil==$nidcab)
            echo "<option value='$nidcab' selected>$nnmcab</option>";
        else
            echo "<option value='$nidcab'>$nnmcab</option>";
    }
    
    
    mysqli_close($cnms);
    mysqli_close($cnmy);
}elseif ($pmodule=="xxx") {
}elseif ($pmodule=="xxx") {
}elseif ($pmodule=="xxx") {
}elseif ($pmodule=="xxx") {
    
}

?>