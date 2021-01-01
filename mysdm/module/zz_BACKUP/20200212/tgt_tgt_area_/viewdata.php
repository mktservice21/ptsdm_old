<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="cariareacabang") {
    $ptglpil=$_POST['uperiode1'];
    $pidcabang=$_POST['ucabid'];
    
    $pperiode_ = date("Ym", strtotime($ptglpil));
    
    $pidareapil="";
    if (!empty($_SESSION['TGTUPDAREAPIL'])) $pidareapil=$_SESSION['TGTUPDAREAPIL'];
    
    include "../../config/koneksimysqli_ms.php";
    
    
    $query ="select DISTINCT icabangid, areaid from tgt.targetarea WHERE icabangid='$pidcabang' AND DATE_FORMAT(bulan,'%Y%m')='$pperiode_'";
    $tampil_= mysqli_query($cnms, $query);
    $ketemu= mysqli_num_rows($tampil_);
    if ($ketemu==0) {
        echo "<option value=''>-- Pilih --</option>";
    }else{
        
        $piarean="";
        while ($nr= mysqli_fetch_array($tampil_)) {
            $mmpidarea=$nr['areaid'];
            $piarean .="'".$mmpidarea."',";
        }
        if (!empty($piarean)) {
            $piarean .="'xxxcxxx'";
            $piarean=" AND areaId IN (".$piarean.") ";
        }
    
        include "../../config/koneksimysqli.php";

        echo "<option value=''>-- All --</option>";
        $query = "select iCabangId, areaId, Nama from MKT.iarea where aktif='Y' AND iCabangId='$pidcabang' $piarean order by Nama";
        $tampil = mysqli_query($cnmy, $query);
        while ($rx= mysqli_fetch_array($tampil)) {
            $nidarea=$rx['areaId'];
            $nnmarea=$rx['Nama'];
            if ($pidareapil==$nidarea)
                echo "<option value='$nidarea' selected>$nnmarea</option>";
            else
                echo "<option value='$nidarea'>$nnmarea</option>";
        }
    
    }
    
    mysqli_close($cnms);
    mysqli_close($cnmy);
}

?>