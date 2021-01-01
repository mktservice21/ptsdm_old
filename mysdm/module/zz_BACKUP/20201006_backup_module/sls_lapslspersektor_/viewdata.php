<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];
//$ptgl = str_replace('/', '-', $_POST['utgl']);
//$ptglpengajuan= date("Y-m-d", strtotime($ptgl));
//$pjumlah=str_replace(",","", $pjumlah);

if ($pmodule=="caridataam") {
    
    $pmyidcard=$_SESSION['IDCARD'];
    $pmyjabatanid=$_SESSION['JABATANID'];
    
    $hanayakaryawan=false;
    $nnidkaryn="";
    if ($pmyjabatanid=="10" OR $pmyjabatanid=="18") {
        $nnidkaryn=$pmyidcard;
        $hanayakaryawan=true;
    }
    $pidcab=$_POST['uidcab'];
    
    include "../../config/koneksimysqli.php";
    
    echo "<option value=''>--Pilih--</option>";
    
    $query = "select DISTINCT a.karyawanid, b.nama nama_karyawan from MKT.ispv0 a "
            . " JOIN hrd.karyawan b on a.karyawanid=b.karyawanid WHERE a.icabangid='$pidcab' ";
    if ($hanayakaryawan == true) {
        $query .=" AND a.karyawanid='$nnidkaryn' ";
    }
    /*
    $query .=" AND LEFT(b.nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.')  "
            . " and LEFT(b.nama,7) NOT IN ('NN DM - ')  "
            . " and LEFT(b.nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-') "
            . " AND LEFT(b.nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR') ";
     * 
     */
    $query .=" order by b.nama";
                                                    
                                                    
    $tampil= mysqli_query($cnmy, $query);
    while ($Xt=mysqli_fetch_array($tampil)){
        $pidkary=$Xt['karyawanid'];
        $pnmkary=$Xt['nama_karyawan'];
        if ($pidkary==$nnidkaryn) 
            echo "<option value='$pidkary' selected>$pnmkary</option>";
        else
            echo "<option value='$pidkary'>$pnmkary</option>";
        
    }
    
    mysqli_close($cnmy);
    
    
}elseif ($pmodule=="xxx") {
}elseif ($pmodule=="xxx") {
}elseif ($pmodule=="xxx") {
}elseif ($pmodule=="xxx") {
    
}

?>