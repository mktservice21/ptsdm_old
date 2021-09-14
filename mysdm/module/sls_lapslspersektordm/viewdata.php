<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];
//$ptgl = str_replace('/', '-', $_POST['utgl']);
//$ptglpengajuan= date("Y-m-d", strtotime($ptgl));
//$pjumlah=str_replace(",","", $pjumlah);

if ($pmodule=="caricabangdm") {
    
    include "../../config/koneksimysqli.php";
    include "../../config/koneksimysqli_ms.php";
    
    $pmyidcard=$_SESSION['IDCARD'];
    $pmyjabatanid=$_SESSION['JABATANID'];
                
    $pidsm=$_POST['usm'];
    
    $filsm=$pidsm;
    if ($pmyjabatanid=="20" OR (DOUBLE)$pmyjabatanid==20) $filsm=$pmyidcard;
        
    

    
    $query = "select iCabangId, nama, aktif from sls.icabang where "
            . " icabangid IN (select distinct IFNULL(icabangid,'') from sls.idm0 WHERE karyawanid='$filsm') ";//aktif='Y' AND 
    $query .=" order by aktif DESC, nama";
    $tampil = mysqli_query($cnms, $query);
    $ketemu= mysqli_num_rows($tampil);
    
    if ((INT)$ketemu>1) echo "<option value=''>-- All --</option>";
    elseif ((INT)$ketemu<=0) echo "<option value='nonecabang' selected></option>";
    while ($rx= mysqli_fetch_array($tampil)) {
        $nidcab=$rx['iCabangId'];
        $nnmcab=$rx['nama'];
        $nnmaktif=$rx['aktif'];
        
        $namaaktif="Aktif";
        if ($nnmaktif!="Y") $namaaktif="Non Aktif";
                                                        
        if ($pidcabangpil==$nidcab)
            echo "<option value='$nidcab' selected>$nnmcab ($namaaktif)</option>";
        else {
            if ((INT)$ketemu==1) echo "<option value='$nidcab' selected>$nnmcab  ($namaaktif)</option>";
            else echo "<option value='$nidcab'>$nnmcab  ($namaaktif)</option>";
        }
    }
    
    
    mysqli_close($cnms);
    mysqli_close($cnmy);
}elseif ($pmodule=="xxx") {
}elseif ($pmodule=="xxx") {
}elseif ($pmodule=="xxx") {
}elseif ($pmodule=="xxx") {
    
}

?>