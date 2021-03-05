<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="caricabangregion") {
    include "../../config/koneksimysqli.php";
    include "../../config/koneksimysqli_ms.php";
    
    $pmyidcard=$_SESSION['IDCARD'];
    $pmyjabatanid=$_SESSION['JABATANID'];
    $pmygroupid=$_SESSION['GROUP'];
    $pidcabangpil="";
    
    $pidregion=$_POST['uregion'];
    $filregion="";
    if (!empty($pidregion)) $filregion=" AND region='$pidregion' ";
    
    $query = "SELECT distinct idcabang, nama from ms.cbgytd WHERE "
            . " aktif='Y' $filregion ";
    if ($pmygroupid=="1" OR $pmygroupid=="24") {
    }else{
        if ($pmyjabatanid=="08") $query .=" AND id_dm='$pmyidcard' ";
        if ($pmyjabatanid=="20") $query .=" AND id_sm='$pmyidcard' ";
        if ($pmyjabatanid=="38") $query .=" AND id_admin='$pmyidcard' ";
        
        if ($pmyidcard=="0000000158") $query .=" AND region='B' ";
        if ($pmyidcard=="0000000159") $query .=" AND region='T' ";
            
    }
                                                    
    $query .=" order by nama";
                                                    
    $tampil = mysqli_query($cnms, $query);
    while ($rx= mysqli_fetch_array($tampil)) {
        $nidcab=$rx['idcabang'];
        $nnmcab=$rx['nama'];
        if ($pidcabangpil==$nidcab)
            echo "<option value='$nidcab' selected>$nnmcab</option>";
        else
            echo "<option value='$nidcab'>$nnmcab</option>";
    }
    
    
    mysqli_close($cnms);
    mysqli_close($cnmy);
    
}elseif ($pmodule=="xxxx") {
    
    
}

?>

