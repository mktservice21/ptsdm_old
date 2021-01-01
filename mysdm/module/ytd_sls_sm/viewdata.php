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
    $filregion=" AND jabatanid='20' ";
    
    $pfilter=false;
    if ($pmyidcard=="0000000158") {
        $filregion=" AND karyawanid in (select distinct IFNULL(id_sm,'') from ms.cbgytd where region ='B') ";
    }elseif ($pmyidcard=="0000000159"){
        $filregion=" AND karyawanid in (select distinct IFNULL(id_sm,'') from ms.cbgytd where region ='T') ";
    }else{
        $pfilter=true;
        if (!empty($pidregion)) $filregion=" AND karyawanid in (select distinct IFNULL(id_sm,'') from ms.cbgytd where region ='$pidregion') ";
    }
    
    $query = "select karyawanid, nama from ms.karyawan where 1=1 $filregion ";
    if ($pmygroupid=="1" OR $pmygroupid=="24" OR $pmygroupid=="50") {
    }else{
        if ($pfilter==true) {
            $query .=" AND karyawanid='$pmyidcard' ";
        }
    }
    $query .=" order by nama";
                                                    
    $tampil = mysqli_query($cnms, $query);
    while ($rx= mysqli_fetch_array($tampil)) {
        $nidcab=$rx['karyawanid'];
        $nnmcab=$rx['nama'];
        if ($pmyidcard==$nidcab)
            echo "<option value='$nidcab' selected>$nnmcab</option>";
        else
            echo "<option value='$nidcab'>$nnmcab</option>";
    }
    
    
    mysqli_close($cnms);
    mysqli_close($cnmy);
    
}elseif ($pmodule=="xxxx") {
    
    
}

?>

